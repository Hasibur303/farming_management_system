<?php
declare(strict_types=1);

require_once __DIR__ . '/security/bootstrap.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security/mfa.php';

ensure_mfa_tables($pdo);
$pending = mfa_pending_user();
$authenticatedId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($pending === null && $authenticatedId < 1) {
    header('Location: login.php');
    exit;
}

$userId = $pending !== null ? (int) $pending['user_id'] : $authenticatedId;
$statement = $pdo->prepare('SELECT user_id, name, email, password, role FROM users WHERE user_id = ?');
$statement->execute([$userId]);
$user = $statement->fetch();
if (!$user) {
    security_log('authentication.mfa_setup_invalid_user');
    header('Location: logout.php');
    exit;
}

if (!isset($_SESSION['_mfa_setup_secret'])) {
    $_SESSION['_mfa_setup_secret'] = mfa_generate_secret();
}
$secret = (string) $_SESSION['_mfa_setup_secret'];
$account = (string) ($user['email'] ?: $user['name']);
$otpauth = mfa_otpauth_uri($secret, $account);
$error = '';
$recoveryCodes = $_SESSION['_mfa_recovery_display'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finish'])) {
    if (!is_array($_SESSION['_mfa_recovery_display'] ?? null) || !mfa_is_enabled($pdo, $userId)) {
        security_log('authentication.mfa_setup_bypass_blocked', ['target_user_id' => $userId]);
        http_response_code(403);
        exit('MFA enrollment must be completed before sign-in.');
    }
    unset($_SESSION['_mfa_recovery_display']);
    if ($pending !== null) {
        mfa_complete_login($pending);
    }
    header('Location: security_settings.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['finish'])) {
    if (mfa_is_rate_limited($pdo, $userId)) {
        http_response_code(429);
        security_log('authentication.mfa_setup_rate_limited', ['target_user_id' => $userId]);
        $error = 'Too many incorrect codes. Please wait 10 minutes.';
    } elseif ($pending === null && !password_verify((string) ($_POST['password'] ?? ''), (string) $user['password'])) {
        mfa_record_attempt($pdo, $userId, false);
        security_log('authentication.mfa_setup_password_failed', ['target_user_id' => $userId]);
        $error = 'Your current password is incorrect.';
    } elseif (($matchedCounter = mfa_matching_totp_counter($secret, (string) ($_POST['code'] ?? ''))) === null) {
        mfa_record_attempt($pdo, $userId, false);
        security_log('authentication.mfa_setup_code_failed', ['target_user_id' => $userId]);
        $error = 'The authenticator code is invalid. Check your device time and try again.';
    } else {
        $codes = mfa_generate_recovery_codes();
        try {
            mfa_enable($pdo, $userId, $secret, $codes, $matchedCounter);
            mfa_record_attempt($pdo, $userId, true);
            unset($_SESSION['_mfa_setup_secret']);
            $_SESSION['_mfa_recovery_display'] = $codes;
            security_log('authentication.mfa_enabled', ['target_user_id' => $userId, 'role' => $user['role']], 'info');
            header('Location: mfa_setup.php?complete=1');
            exit;
        } catch (RuntimeException $exception) {
            security_log('authentication.mfa_configuration_error', ['message' => $exception->getMessage()], 'error');
            $error = 'MFA encryption is not configured correctly. Contact the system administrator.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set up MFA · SmartKrishi</title>
    <style>
        *{box-sizing:border-box}body{margin:0;padding:30px 0;min-height:100vh;background:#e8f5e9;font-family:Arial,sans-serif;color:#17351f}.card{width:min(92%,620px);margin:auto;background:#fff;padding:32px;border-radius:18px;box-shadow:0 18px 45px #1b5e2030}h1,h2{color:#237a39}.steps{line-height:1.6}.qr{display:flex;justify-content:center;margin:22px 0}.secret,.codes{font-family:Consolas,monospace;background:#f1f8f2;padding:14px;border-radius:8px;word-break:break-all}.codes{display:grid;grid-template-columns:1fr 1fr;gap:8px}.warning{background:#fff8e1;padding:13px;border-left:4px solid #f9a825}.error{background:#ffebee;color:#a51c30;padding:12px;border-radius:8px}label{display:block;font-weight:700;margin:18px 0 7px}input{width:100%;padding:12px;border:1px solid #9eb8a4;border-radius:8px;font-size:1rem}button{width:100%;margin-top:18px;padding:13px;border:0;border-radius:8px;background:#2e7d32;color:#fff;font-weight:700;cursor:pointer}.muted{color:#587060}a{color:#2e7d32}
    </style>
</head>
<body><main class="card">
<?php if (is_array($recoveryCodes)): ?>
    <h1>MFA is enabled</h1>
    <p class="warning"><strong>Save these recovery codes now.</strong> Each code works only once. They will not be shown again.</p>
    <div class="codes"><?php foreach ($recoveryCodes as $recoveryCode): ?><span><?= e($recoveryCode) ?></span><?php endforeach; ?></div>
    <form method="post"><input type="hidden" name="finish" value="1"><button type="submit">I saved the codes — continue</button></form>
<?php else: ?>
    <h1>Set up authenticator MFA</h1>
    <?php if ($pending !== null && $user['role'] === 'Admin'): ?><p class="warning">MFA is mandatory for Administrator accounts.</p><?php endif; ?>
    <ol class="steps"><li>Install or open an authenticator app.</li><li>Scan this QR code or enter the setup key.</li><li>Enter the generated six-digit code below.</li></ol>
    <div id="qrcode" class="qr" aria-label="Authenticator QR code"></div>
    <p class="muted">Manual setup key:</p><div class="secret"><?= e($secret) ?></div>
    <?php if ($error !== ''): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <?php if ($pending === null): ?><label for="password">Current password</label><input id="password" name="password" type="password" autocomplete="current-password" required><?php endif; ?>
        <label for="code">Six-digit authenticator code</label>
        <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
        <button type="submit">Verify and enable MFA</button>
    </form>
    <?php if ($pending === null): ?><p><a href="security_settings.php">Cancel</a></p><?php endif; ?>
    <script src="vendor/qrcodejs/qrcode.min.js"></script>
    <script>new QRCode(document.getElementById('qrcode'),{text:<?= json_encode($otpauth, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,width:220,height:220,correctLevel:QRCode.CorrectLevel.M});</script>
<?php endif; ?>
</main></body></html>
