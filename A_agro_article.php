<?php
session_start();
include 'database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Sample agrologist ID
$agrologist_id = $_SESSION['user_id'];

// Handle submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = '';

    if ($_FILES['image']['name']) {
        $target_dir = "uploads/articles/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("INSERT INTO agro_articles (agrologist_id, title, content, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $agrologist_id, $title, $content, $image);
    $stmt->execute();
}

// Fetch own articles
$own_articles = $conn->query("SELECT * FROM agro_articles WHERE agrologist_id = $agrologist_id ORDER BY created_at DESC");

// Fetch others' articles
$others_articles = $conn->query("SELECT a.*, ag.full_name FROM agro_articles a
JOIN agrologists ag ON a.agrologist_id = ag.user_id
WHERE a.agrologist_id != $agrologist_id ORDER BY a.created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agrologists' Article | SmartKirshi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            color: #000;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            width: 70px;
            background: linear-gradient(180deg, #0f2027, #203a43, #2c5364); /* dark navy to blue gradient */
            color: #fff;
            height: 100vh;
            transition: width 0.3s ease;
            overflow-x: hidden;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 80;
        }

        .sidebar:hover {
            width: 220px;
        }

        .sidebar .logo {
            text-align: center;
            padding: 20px 10px;
            background: linear-gradient(to right, #1c1c2d, #24243e); /* matching dark tone */
        }

        .sidebar .logo img {
            width: 50px;
            transition: transform 0.3s;
        }

        .sidebar:hover .logo img {
            transform: rotate(360deg);
        }

        .sidebar ul {
            list-style: none;
            padding: 150px 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .sidebar ul li a:hover {
            color: #fff;
            text-decoration: none;
        }


        .sidebar ul li i {
            margin-right: 15px;
            min-width: 35px;
            text-align: center;
        }

        /* Topbar */
        .topbar {
            padding: 15px 30px;
            background: linear-gradient(to right, #1f1f2e, #2a2a40);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Logout Button */
        .logout-btn {
            color: #fff;
            background-color: #e74c3c;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        /* Cards */
        .dashboard-metrics {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }

        .card-box {
            background: linear-gradient(145deg, #1f2b37, #293544); /* subtle blue/grey glassy */
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 213, 255, 0.2);
            width: 30%;
            color: #ffffff;
        }

        .card-box h2 {
            color: #00d1b2;
        }

        /* Requests */
        .request-box {
            background: linear-gradient(to right, #1f2b37, #263445);
            margin: 15px;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 0 8px rgba(0, 255, 255, 0.1);
            color: #ffffff;
        }

        /* Reply Button */
        .btn-reply {
            background-color: #3498db;
            border: none;
            padding: 6px 12px;
            color: #fff;
            border-radius: 5px;
        }

        .btn-reply:hover {
            background-color: #2980b9;
        }

        /* Main Content */
        .main-content {
            margin-left: 80px;
            padding: 30px;
            transition: margin-left 0.3s;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 220px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-metrics {
                flex-direction: column;
                align-items: center;
            }

            .card-box {
                width: 90%;
                margin-bottom: 15px;
            }

            .sidebar {
                width: 60px;
            }

            .sidebar:hover {
                width: 180px;
            }

            .main-content {
                margin-left: 60px;
            }

            .sidebar:hover ~ .main-content {
                margin-left: 180px;
            }
        }

        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
                .form-section { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ddd; }
                .article-card { margin-bottom: 20px; border: 1px solid #e2e2e2; border-radius: 8px; padding: 20px; background: #fff; }
                .article-card img { max-height: 200px; object-fit: cover; margin-top: 10px; border-radius: 6px; }
                h4.section-title { border-left: 5px solid #0d6efd; padding-left: 10px; margin-top: 40px; }

    </style>
    <!-- Font Awesome for icons -->
      <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</head>
<body>

<div class="sidebar">
    <ul>
        <li><a href="agrologist.php"><i class="fas fa-dashboard"></i><span class="d-none d-md-inline"> Dashboard</span></a></li>
        <li><a href="A_profile.php"><i class="fas fa-user-md"></i><span class="d-none d-md-inline"> Profile</span></a></li>
        <li><a href="A_agro_article.php"><i class="fas fa-pen"></i><span class="d-none d-md-inline">Articals</span></a></li>
        <li><a href="farmer_request.php"><i class="fas fa-seedling"></i><span class="d-none d-md-inline"> Farmer Requests</span></a></li>
        <li><a href="login.php"><i class="fas fa-sign-out-alt"></i><span class="d-none d-md-inline"> Logout</span></a></li>
    </ul>
</div>

<div class="main-content">
    <div class="topbar">
        <h2>Agrologists' Article</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>


    <div class="container my-5">
        <h2 class="text-center mb-4">✍️ এগ্রো-আর্টিকেল ও পরামর্শ</h2>

        <!-- Article Form -->
        <div class="form-section">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">আর্টিকেলের শিরোনাম</label>
                    <input type="text" name="title" class="form-control" required placeholder="যেমন: ধানে ব্লাস্ট রোগ প্রতিরোধের উপায়">
                </div>
                <div class="mb-3">
                    <label class="form-label">বিস্তারিত আর্টিকেল</label>
                    <textarea name="content" rows="6" class="form-control" required placeholder="এখানে আপনার পরামর্শ, নির্দেশনা, অভিজ্ঞতা লিখুন..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">ছবি আপলোড (ঐচ্ছিক)</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <button type="submit" class="btn btn-success w-100">✅ আর্টিকেল জমা দিন</button>
            </form>
        </div>

        <!-- Own Articles -->
        <h4 class="section-title">📄 আপনার আর্টিকেলসমূহ</h4>
        <?php while($row = $own_articles->fetch_assoc()): ?>
            <div class="article-card">
                <h5><?= htmlspecialchars($row['title']) ?></h5>
                <small class="text-muted">তারিখ: <?= date('d M Y', strtotime($row['created_at'])) ?></small>
                <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <?php if ($row['image']): ?>
                    <img src="<?= $row['image'] ?>" class="img-fluid">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <!-- Others' Articles -->
        <h4 class="section-title">🌐 অন্যান্য এগ্রোলজিস্টদের আর্টিকেল</h4>
        <?php while($row = $others_articles->fetch_assoc()): ?>
            <div class="article-card">
                <h5><?= htmlspecialchars($row['title']) ?></h5>
                <small class="text-muted">লেখক: <?= htmlspecialchars($row['full_name']) ?> | তারিখ: <?= date('d M Y', strtotime($row['created_at'])) ?></small>
                <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <?php if ($row['image']): ?>
                    <img src="<?= $row['image'] ?>" class="img-fluid">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>


</body>
</html>
