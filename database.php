<?php
require_once __DIR__ . '/environment.php';

$server = env('DB_HOST', 'localhost');
$port = (int) env('DB_PORT', '3306');
$user = env('DB_USER', 'root');
$pass = env('DB_PASSWORD', '');
$name = env('DB_NAME', 'farming_management');

$conn = mysqli_connect($server, $user, $pass, $name, $port);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Database connection failed. Check your environment configuration.');
}

mysqli_set_charset($conn, 'utf8mb4');
