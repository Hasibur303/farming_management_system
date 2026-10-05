<?php
declare(strict_types=1);

require_once __DIR__ . '/security/bootstrap.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security/mfa.php';

require_login();
ensure_mfa_tables($pdo);
$userId = (int) $_SESSION['user_id'];
$statement = $pdo->prepare('SELECT password, role FROM users WHERE user_id = ?');
$statement->execute([$userId]);
$user = $statement->fetch();
if (!$user) {
    header('Location: logout.php');
    exit;
}

$error = '';
$success = '';
$enabled = mfa_is_enabled($pdo, $userId);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_mfa'])) {
    if ($user['role'] === 'Admin') {
        security_log('authentication.mfa_disable_denied', ['reason' => 'admin_required']);
        http_response_code(403);
        $error = 'Administrator MFA cannot be disabled.';
    } elseif (!password_verify((string) ($_POST['password'] ?? ''), (string) $user['password'])) {
        security_log('authentication.mfa_disable_failed', ['reason' => 'password']);
        $error = 'Your current password is incorrect.';
    } elseif (mfa_is_rate_limited($pdo, $userId)) {
        http_response_code(429);
        $error = 'Too many incorrect codes. Please wait 10 minutes.';
    } else {
        $valid = mfa_verify_user_code($pdo, $userId, (string) ($_POST['code'] ?? ''));
        mfa_record_attempt($pdo, $userId, $valid);
        if (!$valid) {
            security_log('authentication.mfa_disable_failed', ['reason' => 'code']);
            $error = 'The authenticator or recovery code is invalid.';
        } else {
            $delete = $pdo->prepare('DELETE FROM user_mfa WHERE user_id = ?');
            $delete->execute([$userId]);
            $enabled = false;
            security_log('authentication.mfa_disabled', [], 'notice');
            $success = 'MFA has been disabled.';
        }
    }
}

$destinations = ['Admin' => 'admin/admin.php', 'Farmer' => 'farmer.php', 'Customer' => 'customer.php', 'Supplier' => 'supplier.php', 'Labour' => 'labour.php', 'Agrologist' => 'agrologist.php'];
$returnUrl = $destinations[$user['role']] ?? 'dashboard.php';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Account security · SmartKrishi</title>
<style>*{box-sizing:border-box}body{margin:0;min-height:100vh;padding:35px 0;background:#e8f5e9;font-family:Arial,sans-serif;color:#17351f}.card{width:min(92%,600px);margin:auto;background:#fff;padding:32px;border-radius:18px;box-shadow:0 18px 45px #1b5e2030}h1{color:#237a39}.status{padding:14px;border-radius:8px;background:#f1f8f2;border-left:4px solid #2e7d32}.error{background:#ffebee;color:#a51c30;padding:12px;border-radius:8px}.success{background:#e8f5e9;color:#1b5e20;padding:12px;border-radius:8px}label{display:block;font-weight:700;margin:16px 0 7px}input{width:100%;padding:12px;border:1px solid #9eb8a4;border-radius:8px}button,.button{display:inline-block;margin-top:18px;padding:12px 18px;border:0;border-radius:8px;background:#2e7d32;color:#fff;text-decoration:none;font-weight:700;cursor:pointer}.danger{background:#b3261e}.back{display:block;margin-top:24px;color:#2e7d32}</style></head>
<body><main class="card"><h1>Account security</h1>
<?php if ($error !== ''): ?><div class="error"><?= e($error) ?></div><?php endif; ?><?php if ($success !== ''): ?><div class="success"><?= e($success) ?></div><?php endif; ?>
<p class="status"><strong>Authenticator MFA:</strong> <?= $enabled ? 'Enabled' : 'Not enabled' ?><?= $user['role'] === 'Admin' ? ' (required for Administrators)' : '' ?></p>
<?php if (!$enabled): ?><p>Protect your account with a changing code from an authenticator app.</p><a class="button" href="mfa_setup.php">Set up MFA</a>
<?php elseif ($user['role'] !== 'Admin'): ?><h2>Disable MFA</h2><p>This reduces account security. Confirm both factors to continue.</p><form method="post"><input type="hidden" name="disable_mfa" value="1"><label for="password">Current password</label><input id="password" name="password" type="password" required><label for="code">Authenticator or recovery code</label><input id="code" name="code" type="text" required><button class="danger" type="submit">Disable MFA</button></form>
<?php else: ?><p>Administrator MFA cannot be disabled from the application.</p><?php endif; ?>
<a class="back" href="<?= e($returnUrl) ?>">← Return to dashboard</a></main></body></html>
