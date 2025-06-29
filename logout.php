<?php
session_start();
include 'database.php';

if (isset($_SESSION['user_id'])) {
    $labour_id = $_SESSION['user_id'];

//     // Correct column name: user_id
//     //$query = "UPDATE labour SET last_login = NOW() WHERE user_id = $labour_id";
//     if (!mysqli_query($conn, $query)) {
//         die("Error updating last_login: " . mysqli_error($conn));
//     }
}

session_unset();
session_destroy();
header("Location: login.php");
exit();
