<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agrologist_id'])) {
    $farmer_id = $_SESSION['user_id'];
    $agrologist_id = $_POST['agrologist_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO bookings (farmer_id, agrologist_id, message, status, request_date)
            VALUES ('$farmer_id', '$agrologist_id', '$message', 'Pending', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: agrologist_list.php?success=1");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
