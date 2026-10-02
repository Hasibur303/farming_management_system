<?php
require_once __DIR__ . '/security/bootstrap.php';
include 'database.php';

security_log('authentication.logout', [], 'info');
session_unset();
session_destroy();
setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/', 'secure' => security_is_https(), 'httponly' => true, 'samesite' => 'Lax']);
header("Location: login.php");
exit();
