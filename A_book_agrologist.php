<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agrologist_id'])) {
    $agrologist_id = $_POST['agrologist_id'];
    $farmer_id = $_SESSION['user_id'];
    $appointment_type = $_POST['appointment_type'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);


    // Insert into bookings or appointment_requests table
    $query = "INSERT INTO bookings (farmer_id, agrologist_id, appointment_mode, message, status)
              VALUES ('$farmer_id', '$agrologist_id', '$appointment_type', '$message', 'pending')";
    mysqli_query($conn, $query);


    if (mysqli_query($conn, $query)) {
        header("Location: agrologist_list.php?success=1");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
