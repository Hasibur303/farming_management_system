<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: agrologist.php");
    exit();
}

$post_id = intval($_POST['post_id']);
$agrologist_id = $_SESSION['user_id'];
$comment = mysqli_real_escape_string($conn, $_POST['comment']);

mysqli_query($conn, "
    INSERT INTO help_comments (post_id, user_id, comment)
    VALUES ($post_id, $agrologist_id, '$comment')
");

header("Location: agrologist.php");
exit();
?>
