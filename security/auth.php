<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function ensure_login_attempts_table(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS login_attempts (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            identifier_hash CHAR(64) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            successful TINYINT(1) NOT NULL DEFAULT 0,
            attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_login_attempt_lookup (identifier_hash, ip_address, attempted_at),
            INDEX idx_login_attempt_cleanup (attempted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function login_identifier_hash(string $identifier): string
{
    return hash('sha256', strtolower(trim($identifier)));
}

function login_is_rate_limited(PDO $pdo, string $identifier): bool
{
    $maximum = max(3, (int) env('LOGIN_MAX_ATTEMPTS', '5'));
    $window = max(5, (int) env('LOGIN_WINDOW_MINUTES', '15'));
    $cutoff = gmdate('Y-m-d H:i:s', time() - ($window * 60));
    $statement = $pdo->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE identifier_hash = ? AND ip_address = ? AND successful = 0 AND attempted_at >= ?'
    );
    $statement->execute([login_identifier_hash($identifier), security_client_ip(), $cutoff]);
    return (int) $statement->fetchColumn() >= $maximum;
}

function record_login_attempt(PDO $pdo, string $identifier, bool $successful): void
{
    $statement = $pdo->prepare(
        'INSERT INTO login_attempts (identifier_hash, ip_address, successful) VALUES (?, ?, ?)'
    );
    $statement->execute([login_identifier_hash($identifier), security_client_ip(), $successful ? 1 : 0]);

    if (random_int(1, 100) === 1) {
        $pdo->exec('DELETE FROM login_attempts WHERE attempted_at < (UTC_TIMESTAMP() - INTERVAL 30 DAY)');
    }
}

function preferred_password_algorithm(): string|int|null
{
    return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
}

