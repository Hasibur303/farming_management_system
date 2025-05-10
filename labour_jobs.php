<?php
session_start();
include('database.php');

// Dummy farmer session values (replace with actual session later)
$farmer_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$stmt->bind_result($farmer_name);
$stmt->fetch();
$stmt->close();




$message = '';

// Handle new job post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'];
    $photo = '';

    if (!empty($_FILES['photo']['name'])) {
        $photo = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    $stmt = $conn->prepare("INSERT INTO labour_jobpost (farmer_id, farmer_name, photo, caption) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $farmer_id, $farmer_name, $photo, $caption);

    if ($stmt->execute()) {
        $message = "✅ পোস্ট সফলভাবে প্রকাশিত হয়েছে!";
    } else {
        $message = "❌ সমস্যা হয়েছে: " . $stmt->error;
    }
}

// Fetch job posts
$posts = $conn->query("SELECT * FROM labour_jobpost ORDER BY post_date DESC");

// Fetch pending application count for notifications
$notif_stmt = $conn->prepare("
    SELECT COUNT(*) as pending_count
    FROM job_applications ja
    JOIN labour_jobpost jp ON ja.job_id = jp.id
    WHERE jp.farmer_ID = ? AND ja.status = 'Pending'
");
$notif_stmt->bind_param("i", $farmer_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result()->fetch_assoc();
$pending_count = $notif_result['pending_count'];
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>শ্রমিকের চাকরির পোস্ট</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Noto Sans Bengali', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #2E7D32;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #388E3C;
        }

        .main {
            margin-left: 270px;
            padding: 30px;
            width: 100%;
        }

        h2 {
            color: #2E7D32;
            margin-bottom: 20px;
        }

        .post-form {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        input[type="submit"] {
            margin-top: 15px;
            padding: 12px 20px;
            background-color: #388E3C;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2E7030;
        }

        .post {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #4CAF50;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .post img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .message {
            font-weight: bold;
            color: green;
            margin-bottom: 10px;
        }

        .notification {
            background-color: #FFEB3B;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }

        .notification a {
            color: #2E7D32;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <h2>ন্যাভিগেশন</h2>
        <a href="farmer.php">
                    <i class="fas fa-wallet icon"></i>
                    <span class="text">ড্যাশবোর্ড</span>
                </a>
        <a href="crop_management.php">
            <i class="fas fa-seedling icon"></i>
            <span class="text">ফসল/পণ্য ব্যবস্থাপনা</span>
        </a>
        <a href="Buy.php">
            <i class="fas fa-shopping-cart icon"></i>
            <span class="text">সরবরাহকারীদের কাছ থেকে কিনুন</span>
        </a>
        <a href="labour_jobs.php">
            <i class="fas fa-briefcase icon"></i>
            <span class="text">শ্রমিকের চাকরির পোস্ট</span>
        </a>

        <a href="rent_page.php">
            <i class="fas fa-shopping-cart icon"></i>
            <span class="text">ভাড়ার পরিষেবা</span>
        </a>
        <a href="addNewProduct.php">
            <i class="fas fa-plus-circle icon"></i>
            <span class="text">নতুন পণ্য যোগ করুন</span>
        </a>
        <a href="farmer/order_management.php">
            <i class="fas fa-clipboard-list icon"></i>
            <span class="text">অর্ডার ম্যানেজমেন্ট</span>
        </a>
        <a href="farmer/inventory_management.php">
            <i class="fas fa-boxes icon"></i>
            <span class="text">ইনভেন্টরি ম্যানেজমেন্ট</span>
        </a>
        <a href="farmer/financial_overview.php">
            <i class="fas fa-wallet icon"></i>
            <span class="text">আর্থিক সারসংক্ষেপ</span>
        </a>
        <a href="analytics_report.php">
            <i class="fas fa-chart-bar icon"></i>
            <span class="text">বিশ্লেষণ এবং প্রতিবেদন</span>
        </a>
    </div>

<div class="main">
    <h2>শ্রমিকের চাকরির পোস্ট</h2>

    <?php if ($pending_count > 0): ?>
    <div class="notification">
        🔔 আপনার কাছে <strong><?= $pending_count ?></strong>টি নতুন শ্রমিক আবেদন রয়েছে।
        <a href="farmer_applications.php">এখানে দেখুন</a>
    </div>
    <?php endif; ?>

    <div class="post-form">
        <?php if ($message) echo "<div class='message'>$message</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="caption">ক্যাপশন (বর্ণনা)*:</label>
            <textarea name="caption" id="caption" required rows="3"></textarea>

            <label for="photo">ছবি (ঐচ্ছিক):</label>
            <input type="file" name="photo" accept="image/*">

            <input type="submit" value="পোস্ট করুন">
        </form>
    </div>

    <?php while ($row = $posts->fetch_assoc()): ?>
        <div class="post">
            <p><strong><?= htmlspecialchars($row['farmer_name']) ?></strong> 🕒 <?= $row['post_date'] ?></p>
            <?php if (!empty($row['photo'])): ?>
                <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Post Image">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($row['caption'])) ?></p>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
