<?php
require_once __DIR__ . '/security/bootstrap.php';
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agrologist_id'])) {
    $agrologist_id = (int) ($_POST['agrologist_id'] ?? 0);
    $farmer_id = (int) $_SESSION['user_id'];
    $appointment_type = trim((string) ($_POST['appointment_type'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));


    // Insert into bookings or appointment_requests table
    $statement = $pdo->prepare('INSERT INTO bookings (farmer_id, agrologist_id, appointment_mode, message, status)
                               VALUES (?, ?, ?, ?, ?)');
    if ($statement->execute([$farmer_id, $agrologist_id, $appointment_type, $message, 'pending'])) {
        header("Location: agrologist_list.php?success=1");
    } else {
        http_response_code(500);
        echo 'Unable to create the booking.';
    }
}
?>
