<?php
$server = "localhost";
$user = "root";
$pass = "";
$name = "farming_management";

$conn = mysqli_connect($server, $user, $pass, $name);

if (!$conn) {
    die("ডাটাবেজে সংযোগ ব্যর্থ: " . mysqli_connect_error());
}
?>
