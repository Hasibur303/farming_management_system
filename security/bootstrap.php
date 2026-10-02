<?php
declare(strict_types=1);

if (defined('SMARTKRISHI_SECURITY_BOOTSTRAPPED')) {
    return;
}
define('SMARTKRISHI_SECURITY_BOOTSTRAPPED', true);

require_once dirname(__DIR__) . '/environment.php';

function security_is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('smartkrishi_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => security_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self' https: data:; script-src 'self' https: 'unsafe-inline'; style-src 'self' https: 'unsafe-inline'; img-src 'self' https: data: blob:; font-src 'self' https: data:; connect-src 'self' https:; object-src 'none'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'");
}

if (!isset($_SESSION['_created_at'])) {
    $_SESSION['_created_at'] = time();
}
if (time() - (int) ($_SESSION['_created_at'] ?? 0) > 1800) {
    session_regenerate_id(true);
    $_SESSION['_created_at'] = time();
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

function security_client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 0, 45);
}

function security_log(string $event, array $context = [], string $level = 'warning'): void
{
    $logDir = dirname(__DIR__) . '/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0750, true);
    }

    $record = [
        'timestamp' => gmdate('c'),
        'application' => 'smartkrishi',
        'level' => $level,
        'event' => preg_replace('/[^a-z0-9_.-]/i', '_', $event),
        'user_id' => isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null,
        'role' => isset($_SESSION['role']) ? substr((string) $_SESSION['role'], 0, 40) : null,
        'ip' => security_client_ip(),
        'method' => substr((string) ($_SERVER['REQUEST_METHOD'] ?? 'CLI'), 0, 10),
        'path' => substr((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), 0, 500),
        'context' => $context,
    ];

    $json = json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($json !== false) {
        @file_put_contents($logDir . '/security.jsonl', $json . PHP_EOL, FILE_APPEND | LOCK_EX);
        error_log('[SmartKrishi Security] ' . $json);
    }
}

function csrf_validate_request(): void
{
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
        return;
    }

    $provided = (string) ($_POST['_csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
    $expected = (string) ($_SESSION['_csrf_token'] ?? '');
    if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
        security_log('csrf.violation', ['reason' => 'missing_or_invalid_token']);
        http_response_code(403);
        exit('Invalid or expired security token. Return to the previous page and try again.');
    }
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        security_log('authorization.login_required');
        header('Location: ' . (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') || str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/farmer/') || str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/supplier/') ? '../login.php' : 'login.php'));
        exit;
    }
}

function require_role(string|array $roles): void
{
    require_login();
    $allowed = array_map('strtolower', (array) $roles);
    $actual = strtolower((string) ($_SESSION['role'] ?? ''));
    if (!in_array($actual, $allowed, true)) {
        security_log('authorization.role_denied', ['required_roles' => $allowed, 'actual_role' => $actual]);
        http_response_code(403);
        exit('You are not authorized to access this page.');
    }
}

function enforce_route_policy(): void
{
    $documentRoot = realpath(dirname(__DIR__));
    $script = realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
    if ($documentRoot === false || $script === false || !str_starts_with(strtolower($script), strtolower($documentRoot))) {
        return;
    }

    $route = str_replace('\\', '/', ltrim(substr($script, strlen($documentRoot)), '/\\'));
    $prefixPolicies = [
        'admin/' => ['Admin'],
        'analytics/' => ['Admin'],
        'farmer/' => ['Farmer'],
        'supplier/' => ['Supplier'],
    ];
    foreach ($prefixPolicies as $prefix => $roles) {
        if (str_starts_with($route, $prefix)) {
            require_role($roles);
            return;
        }
    }

    $routePolicies = [
        'admin.php' => ['Admin'],
        'analytics_report.php' => ['Admin'],
        'process_request.php' => ['Admin'],
        'farmer.php' => ['Farmer'],
        'crop_management.php' => ['Farmer'],
        'addNewProduct.php' => ['Farmer'],
        'Agrologist_List.php' => ['Farmer'],
        'A_book_agrologist.php' => ['Farmer'],
        'A_previous_response.php' => ['Farmer'],
        'F_Agribot.php' => ['Farmer'],
        'F_Agrologist_Request.php' => ['Farmer'],
        'F_article.php' => ['Farmer'],
        'F_chatbot.php' => ['Farmer'],
        'F_Doctor.php' => ['Farmer'],
        'F_insects.php' => ['Farmer'],
        'F_labour_list.php' => ['Farmer'],
        'F_Smart_Crop_Doctor.php' => ['Farmer'],
        'farmer_applications.php' => ['Farmer'],
        'help_post.php' => ['Farmer'],
        'labour_jobs.php' => ['Farmer'],
        'buy.php' => ['Farmer'],
        'rent_page.php' => ['Farmer'],
        'agrologist.php' => ['Agrologist'],
        'A_agro_article.php' => ['Agrologist'],
        'A_comment.php' => ['Agrologist'],
        'A_profile.php' => ['Agrologist'],
        'A_view_agrologist.php' => ['Agrologist'],
        'comment.php' => ['Agrologist'],
        'farmer_request.php' => ['Agrologist'],
        'customer.php' => ['Customer'],
        'C_market.php' => ['Customer'],
        'C_order_history.php' => ['Customer'],
        'C_purchase_history.php' => ['Customer'],
        'C_review.php' => ['Customer'],
        'C_top_selling_products.php' => ['Customer'],
        'process_order.php' => ['Customer'],
        'order_confirmation.php' => ['Customer'],
        'bkash.php' => ['Customer'],
        'bkash_payment.php' => ['Customer'],
        'nagad.php' => ['Customer'],
        'rocket.php' => ['Customer'],
        'labour.php' => ['Labour'],
        'L_apply_job.php' => ['Labour'],
        'L_apply_job_list.php' => ['Labour'],
        'L_contract_message.php' => ['Labour'],
        'L_job.php' => ['Labour'],
        'L_profile.php' => ['Labour'],
        'L_supplier_product.php' => ['Labour'],
        'supplier.php' => ['Supplier'],
        'supplier_interection.php' => ['Farmer'],
        'manage_suppliers.php' => ['Admin'],
    ];

    if (isset($routePolicies[$route])) {
        require_role($routePolicies[$route]);
    }
}

function security_inject_csrf_fields(string $html): string
{
    if (stripos($html, '<form') === false) {
        return $html;
    }

    return (string) preg_replace_callback(
        '/<form\b([^>]*)>/i',
        static function (array $match): string {
            $attributes = $match[1];
            if (!preg_match('/\bmethod\s*=\s*(["\']?)post\1/i', $attributes)) {
                return $match[0];
            }
            return $match[0] . csrf_field();
        },
        $html
    );
}

csrf_token();
enforce_route_policy();
csrf_validate_request();

if (PHP_SAPI !== 'cli') {
    ob_start('security_inject_csrf_fields');
}

