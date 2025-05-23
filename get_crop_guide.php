<?php
// Database credentials
$host = "localhost";
$user = "your_db_username";       // তোমার ডাটাবেজ ইউজারনেম
$password = "your_db_password";   // তোমার ডাটাবেজ পাসওয়ার্ড
$dbname = "your_database_name";   // তোমার ডাটাবেজ নাম

// Enable error reporting for debugging (production-এ এডিট করো)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Connect to the database
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "ডাটাবেইজ সংযোগ ব্যর্থ: " . $conn->connect_error]);
    exit;
}

// Get the crop name from POST request and sanitize it
$cropName = isset($_POST['crop_name']) ? trim($_POST['crop_name']) : '';

if (empty($cropName)) {
    echo json_encode(["success" => false, "error" => "ফসলের নাম পাওয়া যায়নি।"]);
    exit;
}

// Prepare and execute query securely
$stmt = $conn->prepare("SELECT info, roadmap FROM crop_guidelines WHERE crop_name = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "কোয়েরি প্রস্তুত করতে সমস্যা হয়েছে।"]);
    exit;
}

$stmt->bind_param("s", $cropName);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => "কোয়েরি সম্পাদনে ত্রুটি হয়েছে।"]);
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "info" => $row['info'],
        "roadmap" => $row['roadmap']
    ]);
} else {
    echo json_encode(["success" => false, "error" => "এই ফসলের তথ্য পাওয়া যায়নি।"]);
}

$stmt->close();
$conn->close();
