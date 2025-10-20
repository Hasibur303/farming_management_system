<?php
session_start();
include 'database.php';

// Ensure farmer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch agrologist ID from query string
if (!isset($_GET['id'])) {
    echo "Agrologist not found.";
    exit();
}


$agrologist_id = $_GET['id'];

$sql = "SELECT a.*, u.full_name
        FROM agrologists a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agrologist_id);
$stmt->execute();
$result = $stmt->get_result();
$agrologist = $result->fetch_assoc();

if (!$agrologist) {
    echo "Agrologist not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agrologist Profile | SmartKrishi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }
        .profile-card {
            background: linear-gradient(to right, #1f2b37, #263445);
            color: white;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,255,255,0.2);
        }
        .profile-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #00d1b2;
            margin-bottom: 20px;
        }
        .profile-header {
            text-align: center;
        }
        .profile-header h2 {
            color: #00d1b2;
        }
        .profile-info {
            margin-top: 20px;
        }
        .info-item {
            margin-bottom: 12px;
            font-size: 1.1rem;
        }
        .info-label {
            font-weight: bold;
            color: #00d1b2;
        }
    </style>
</head>
<body>

<div class="profile-card">
    <div class="profile-header">
        <img src="uploadimages/<?php echo htmlspecialchars($agrologist['photo']); ?>" alt="Profile Photo" class="profile-photo">
        <h2><?php echo htmlspecialchars($agrologist['full_name']); ?></h2>
        <p>Certified Agrologist</p>
    </div>
    <div class="profile-info">
        <div class="info-item"><span class="info-label">District:</span> <?php echo htmlspecialchars($agrologist['district']); ?></div>
        <div class="info-item"><span class="info-label">Farming Sector:</span> <?php echo htmlspecialchars($agrologist['farming_sector']); ?></div>
        <div class="info-item"><span class="info-label">Experience:</span> <?php echo htmlspecialchars($agrologist['experience']); ?> years</div>
        <div class="info-item"><span class="info-label">Address:</span> <?php echo htmlspecialchars($agrologist['address']); ?></div>
    </div>
</div>

</body>
</html>
