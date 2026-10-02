<?php
declare(strict_types=1);

require_once __DIR__ . '/security/bootstrap.php';
require_once __DIR__ . '/security/database.php';

$server = env('DB_HOST', 'localhost');
$port = (int) env('DB_PORT', '3306');
$user = env('DB_USER', 'root');
$pass = env('DB_PASSWORD', '');
$name = env('DB_NAME', 'farming_management');

try {
    $pdo = new PDO(
        "mysql:host={$server};port={$port};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]
    );
    $conn = new DatabaseConnection($pdo);
} catch (PDOException $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed. Check your environment configuration.');
}
