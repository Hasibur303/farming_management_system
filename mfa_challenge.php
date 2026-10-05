<?php
declare(strict_types=1);

require_once __DIR__ . '/security/bootstrap.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security/mfa.php';

ensure_mfa_tables($pdo);
$pending = mfa_pending_user();
if ($pending === null) {
    header('Location: login.php');
    exit;
}

$error = '';
$userId = (int) $pending['user_id'];
$rateLimitSeconds = mfa_rate_limit_remaining_seconds($pdo, $userId);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($rateLimitSeconds > 0) {
        security_log('authentication.mfa_rate_limited', ['pending_user_id' => $userId]);
        http_response_code(429);
        $error = 'Too many incorrect codes. Verification is temporarily locked.';
    } else {
        $code = trim((string) ($_POST['code'] ?? ''));
        try {
            $valid = mfa_verify_user_code($pdo, $userId, $code);
        } catch (RuntimeException $exception) {
            security_log('authentication.mfa_configuration_error', ['message' => $exception->getMessage()], 'error');
            $valid = false;
        }
        mfa_record_attempt($pdo, $userId, $valid);
        if ($valid) {
            security_log('authentication.mfa_succeeded', ['pending_user_id' => $userId], 'info');
            mfa_complete_login($pending);
        }
        security_log('authentication.mfa_failed', ['pending_user_id' => $userId]);
        usleep(random_int(150000, 350000));
        $rateLimitSeconds = mfa_rate_limit_remaining_seconds($pdo, $userId);
        if ($rateLimitSeconds > 0) {
            http_response_code(429);
            security_log('authentication.mfa_rate_limited', ['pending_user_id' => $userId]);
            $error = 'Too many incorrect codes. Verification is temporarily locked.';
        } else {
            $error = 'The authenticator or recovery code is invalid.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Two-factor verification · SmartKrishi</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:#e8f5e9;font-family:Arial,sans-serif;color:#17351f}.card{width:min(92%,430px);background:#fff;padding:32px;border-radius:18px;box-shadow:0 18px 45px #1b5e2030}h1{margin-top:0;color:#237a39;font-size:1.65rem}.muted{color:#587060;line-height:1.5}.error{background:#ffebee;color:#a51c30;padding:12px;border-radius:8px;margin:16px 0}.lockout{margin:16px 0;padding:18px;border:1px solid #ef9a9a;border-radius:10px;background:#fff5f5;text-align:center}.countdown{display:block;margin-top:8px;color:#a51c30;font-size:2rem;font-weight:800;font-variant-numeric:tabular-nums}label{display:block;font-weight:700;margin:20px 0 8px}input{width:100%;padding:13px;border:1px solid #9eb8a4;border-radius:8px;font-size:1.1rem;letter-spacing:.08em}button{width:100%;margin-top:18px;padding:13px;border:0;border-radius:8px;background:#2e7d32;color:#fff;font-weight:700;cursor:pointer}a{color:#2e7d32}.cancel{text-align:center;margin-top:18px}
    </style>
</head>
<body><main class="card">
    <h1>Two-factor verification</h1>
    <p class="muted">Open your authenticator app and enter its current six-digit code. You may also use one unused recovery code.</p>
    <?php if ($error !== ''): ?><div class="error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <?php if ($rateLimitSeconds > 0): ?>
        <div class="lockout" role="status" aria-live="polite">
            Try again when the countdown finishes.
            <strong class="countdown" id="mfa-countdown" data-remaining="<?= (int) $rateLimitSeconds ?>">--:--</strong>
            <span class="muted">This page will refresh automatically.</span>
        </div>
    <?php else: ?>
    <form method="post">
        <label for="code">Authenticator or recovery code</label>
        <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="9" required autofocus>
        <button type="submit">Verify and sign in</button>
    </form>
    <?php endif; ?>
    <p class="cancel"><a href="logout.php">Cancel sign-in</a></p>
</main>
<?php if ($rateLimitSeconds > 0): ?>
<script>
(() => {
    const display = document.getElementById('mfa-countdown');
    let remaining = Number.parseInt(display.dataset.remaining, 10) || 0;
    const render = () => {
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        if (remaining <= 0) {
            window.setTimeout(() => window.location.reload(), 1000);
            return;
        }
        remaining -= 1;
        window.setTimeout(render, 1000);
    };
    render();
})();
</script>
<?php endif; ?>
</body></html>
