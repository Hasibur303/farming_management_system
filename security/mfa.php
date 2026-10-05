<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function ensure_mfa_tables(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS user_mfa (
            user_id INT NOT NULL PRIMARY KEY,
            secret_encrypted TEXT NOT NULL,
            recovery_codes_json TEXT NOT NULL,
            last_used_counter BIGINT NULL,
            enabled_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_mfa_enabled_at (enabled_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
    $pdo->exec('ALTER TABLE user_mfa ADD COLUMN IF NOT EXISTS last_used_counter BIGINT NULL AFTER recovery_codes_json');
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS mfa_attempts (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            successful TINYINT(1) NOT NULL DEFAULT 0,
            attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_mfa_attempt_lookup (user_id, ip_address, attempted_at),
            INDEX idx_mfa_attempt_cleanup (attempted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function mfa_encryption_key(): string
{
    $key = base64_decode((string) env('MFA_ENCRYPTION_KEY', ''), true);
    if ($key === false || strlen($key) !== 32) {
        throw new RuntimeException('MFA encryption is not configured. Set a random 32-byte base64 MFA_ENCRYPTION_KEY.');
    }
    return $key;
}

function mfa_encrypt_secret(string $secret): string
{
    $nonce = random_bytes(12);
    $tag = '';
    $ciphertext = openssl_encrypt($secret, 'aes-256-gcm', mfa_encryption_key(), OPENSSL_RAW_DATA, $nonce, $tag, 'smartkrishi-mfa-v1');
    if ($ciphertext === false) {
        throw new RuntimeException('Unable to protect the MFA secret.');
    }
    return base64_encode($nonce . $tag . $ciphertext);
}

function mfa_decrypt_secret(string $protected): string
{
    $payload = base64_decode($protected, true);
    if ($payload === false || strlen($payload) < 29) {
        throw new RuntimeException('The saved MFA secret is invalid.');
    }
    $secret = openssl_decrypt(
        substr($payload, 28),
        'aes-256-gcm',
        mfa_encryption_key(),
        OPENSSL_RAW_DATA,
        substr($payload, 0, 12),
        substr($payload, 12, 16),
        'smartkrishi-mfa-v1'
    );
    if ($secret === false) {
        throw new RuntimeException('Unable to unlock the MFA secret.');
    }
    return $secret;
}

function mfa_base32_encode(string $bytes): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $bits = '';
    foreach (str_split($bytes) as $byte) {
        $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
    }
    $encoded = '';
    foreach (str_split($bits, 5) as $chunk) {
        $encoded .= $alphabet[bindec(str_pad($chunk, 5, '0'))];
    }
    return $encoded;
}

function mfa_base32_decode(string $encoded): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $encoded = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $encoded) ?? '');
    $bits = '';
    foreach (str_split($encoded) as $character) {
        $position = strpos($alphabet, $character);
        if ($position === false) {
            throw new InvalidArgumentException('Invalid Base32 secret.');
        }
        $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
    }
    $decoded = '';
    foreach (str_split($bits, 8) as $chunk) {
        if (strlen($chunk) === 8) {
            $decoded .= chr(bindec($chunk));
        }
    }
    return $decoded;
}

function mfa_generate_secret(): string
{
    return mfa_base32_encode(random_bytes(20));
}

function mfa_totp_at(string $secret, int $counter, int $digits = 6): string
{
    $binaryCounter = pack('N2', intdiv($counter, 0x100000000), $counter % 0x100000000);
    $hash = hash_hmac('sha1', $binaryCounter, mfa_base32_decode($secret), true);
    $offset = ord($hash[19]) & 0x0f;
    $value = unpack('N', substr($hash, $offset, 4))[1] & 0x7fffffff;
    return str_pad((string) ($value % (10 ** $digits)), $digits, '0', STR_PAD_LEFT);
}

function mfa_verify_totp(string $secret, string $code, ?int $time = null): bool
{
    return mfa_matching_totp_counter($secret, $code, $time) !== null;
}

function mfa_matching_totp_counter(string $secret, string $code, ?int $time = null): ?int
{
    $code = preg_replace('/\D/', '', $code) ?? '';
    if (strlen($code) !== 6) {
        return null;
    }
    $counter = intdiv($time ?? time(), 30);
    for ($window = -1; $window <= 1; $window++) {
        $candidate = $counter + $window;
        if (hash_equals(mfa_totp_at($secret, $candidate), $code)) {
            return $candidate;
        }
    }
    return null;
}

function mfa_is_enabled(PDO $pdo, int $userId): bool
{
    $statement = $pdo->prepare('SELECT 1 FROM user_mfa WHERE user_id = ?');
    $statement->execute([$userId]);
    return (bool) $statement->fetchColumn();
}

function mfa_record_attempt(PDO $pdo, int $userId, bool $successful): void
{
    $statement = $pdo->prepare('INSERT INTO mfa_attempts (user_id, ip_address, successful) VALUES (?, ?, ?)');
    $statement->execute([$userId, security_client_ip(), $successful ? 1 : 0]);
    if (random_int(1, 100) === 1) {
        $pdo->exec('DELETE FROM mfa_attempts WHERE attempted_at < (UTC_TIMESTAMP() - INTERVAL 30 DAY)');
    }
}

function mfa_is_rate_limited(PDO $pdo, int $userId): bool
{
    return mfa_rate_limit_remaining_seconds($pdo, $userId) > 0;
}

function mfa_rate_limit_remaining_seconds(PDO $pdo, int $userId, ?int $now = null): int
{
    $maximum = max(3, (int) env('MFA_MAX_ATTEMPTS', '5'));
    $window = max(5, (int) env('MFA_WINDOW_MINUTES', '10'));
    $currentTime = $now ?? time();
    $windowSeconds = $window * 60;
    $statement = $pdo->prepare(
        'SELECT UNIX_TIMESTAMP(attempted_at) FROM mfa_attempts
         WHERE user_id = ? AND ip_address = ? AND successful = 0
           AND attempted_at >= (CURRENT_TIMESTAMP - INTERVAL ' . $window . ' MINUTE)'
        . ' ORDER BY attempted_at DESC LIMIT ' . $maximum
    );
    $statement->execute([$userId, security_client_ip()]);
    $attempts = $statement->fetchAll(PDO::FETCH_COLUMN);
    if (count($attempts) < $maximum) {
        return 0;
    }

    // Let MySQL convert its own TIMESTAMP value to Unix time. This avoids a
    // server/PHP timezone mismatch adding several hours to the countdown.
    $thresholdAttempt = filter_var($attempts[$maximum - 1], FILTER_VALIDATE_INT);
    if ($thresholdAttempt === false) {
        return $windowSeconds;
    }

    return max(0, ((int) $thresholdAttempt + $windowSeconds) - $currentTime);
}

function mfa_generate_recovery_codes(int $count = 8): array
{
    $codes = [];
    for ($i = 0; $i < $count; $i++) {
        $raw = strtoupper(bin2hex(random_bytes(4)));
        $codes[] = substr($raw, 0, 4) . '-' . substr($raw, 4, 4);
    }
    return $codes;
}

function mfa_enable(PDO $pdo, int $userId, string $secret, array $recoveryCodes, ?int $initialCounter = null): void
{
    $hashes = array_map(static fn (string $code): string => password_hash(strtoupper($code), PASSWORD_DEFAULT), $recoveryCodes);
    $statement = $pdo->prepare(
        'INSERT INTO user_mfa (user_id, secret_encrypted, recovery_codes_json, last_used_counter)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE secret_encrypted = VALUES(secret_encrypted),
             recovery_codes_json = VALUES(recovery_codes_json), last_used_counter = NULL,
             enabled_at = CURRENT_TIMESTAMP'
    );
    $statement->execute([$userId, mfa_encrypt_secret($secret), json_encode($hashes, JSON_THROW_ON_ERROR), $initialCounter]);
}

function mfa_verify_user_code(PDO $pdo, int $userId, string $code): bool
{
    $statement = $pdo->prepare('SELECT secret_encrypted, recovery_codes_json, last_used_counter FROM user_mfa WHERE user_id = ?');
    $statement->execute([$userId]);
    $record = $statement->fetch();
    if (!$record) {
        return false;
    }
    $counter = mfa_matching_totp_counter(mfa_decrypt_secret((string) $record['secret_encrypted']), $code);
    if ($counter !== null) {
        $consume = $pdo->prepare(
            'UPDATE user_mfa SET last_used_counter = ?
             WHERE user_id = ? AND (last_used_counter IS NULL OR last_used_counter < ?)'
        );
        $consume->execute([$counter, $userId, $counter]);
        return $consume->rowCount() === 1;
    }

    $normalized = strtoupper(trim($code));
    $hashes = json_decode((string) $record['recovery_codes_json'], true);
    if (!is_array($hashes)) {
        return false;
    }
    foreach ($hashes as $index => $hash) {
        if (is_string($hash) && password_verify($normalized, $hash)) {
            unset($hashes[$index]);
            $update = $pdo->prepare('UPDATE user_mfa SET recovery_codes_json = ? WHERE user_id = ?');
            $update->execute([json_encode(array_values($hashes), JSON_THROW_ON_ERROR), $userId]);
            security_log('authentication.mfa_recovery_used', ['remaining_codes' => count($hashes)], 'notice');
            return true;
        }
    }
    return false;
}

function mfa_store_pending_user(array $user): void
{
    unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role']);
    $_SESSION['_mfa_pending'] = [
        'user_id' => (int) $user['user_id'],
        'username' => (string) $user['name'],
        'role' => (string) $user['role'],
        'created_at' => time(),
    ];
}

function mfa_pending_user(): ?array
{
    $pending = $_SESSION['_mfa_pending'] ?? null;
    $windowSeconds = max(5, (int) env('MFA_WINDOW_MINUTES', '10')) * 60;
    $pendingLifetime = max(900, $windowSeconds + 300);
    if (!is_array($pending) || time() - (int) ($pending['created_at'] ?? 0) > $pendingLifetime) {
        unset($_SESSION['_mfa_pending'], $_SESSION['_mfa_setup_secret']);
        return null;
    }
    return $pending;
}

function mfa_complete_login(array $user): never
{
    session_regenerate_id(true);
    unset($_SESSION['_mfa_pending'], $_SESSION['_mfa_setup_secret']);
    $_SESSION['user_id'] = (int) $user['user_id'];
    $_SESSION['username'] = (string) $user['username'];
    $_SESSION['role'] = (string) $user['role'];
    $_SESSION['_created_at'] = time();
    $_SESSION['_last_activity'] = time();
    $_SESSION['_mfa_verified_at'] = time();
    security_log('authentication.login_succeeded', ['mfa' => true], 'info');

    $destinations = [
        'Admin' => 'admin/admin.php', 'Farmer' => 'farmer.php', 'Customer' => 'customer.php',
        'Investor' => 'investor.php', 'Supplier' => 'supplier.php', 'Labour' => 'labour.php',
        'Agrologist' => 'agrologist.php',
    ];
    header('Location: ' . ($destinations[$user['role']] ?? 'dashboard.php'));
    exit;
}

function mfa_otpauth_uri(string $secret, string $account): string
{
    $issuer = 'SmartKrishi';
    $label = rawurlencode($issuer . ':' . $account);
    return 'otpauth://totp/' . $label . '?secret=' . rawurlencode($secret)
        . '&issuer=' . rawurlencode($issuer) . '&algorithm=SHA1&digits=6&period=30';
}
