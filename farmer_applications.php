<?php
session_start();
include('database.php');

// Dummy farmer ID for demonstration
if (!isset($_SESSION['user_id'])) {
    // Redirect or handle unauthorized access
    header("Location: login.php");
    exit();
}
$farmer_id = $_SESSION['user_id'];

// Handle Accept/Reject POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['status'])) {
    $application_id = intval($_POST['application_id']);
    $status = $_POST['status'] === 'Accepted' ? 'Accepted' : 'Rejected';

    // Update application status
    $stmt = $conn->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $application_id);
    $stmt->execute();

    // Fetch labour_id to notify
    $stmt = $conn->prepare("SELECT labour_id FROM job_applications WHERE id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $labour_id = $result->fetch_assoc()['labour_id'];

    // Insert notification
    $message = $status === 'Accepted' ? "আপনার আবেদনটি গৃহীত হয়েছে।" : "আপনার আবেদনটি বাতিল করা হয়েছে।";
    $notif_stmt = $conn->prepare("INSERT INTO notification (receiver_id, message, status, timestamp) VALUES (?, ?, 'unread', NOW())");
    $notif_stmt->bind_param("is", $labour_id, $message);
    $notif_stmt->execute();

    header("Location: farmer_applications.php");
    exit();
}

// Fetch all job posts by this farmer
$job_posts = $conn->query("SELECT * FROM labour_jobpost WHERE farmer_ID = $farmer_id ORDER BY post_date DESC");
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>আবেদনসমূহ পরিচালনা</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f7f8fa;
            color: #333;
            padding-top: 70px;
            padding-left: 60px;
        }

        header {
            background: linear-gradient(to right, #2E7D32, #66BB6A);
            color: white;
            padding: 1rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #2E7D32;
        }

        header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #fff;
            flex-grow: 1;
            text-align: center;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info span {
            font-size: 1rem;
            margin-right: 15px;
            color: #fff;
        }

        .user-info a {
            background-color: #d32f2f;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .user-info a:hover {
            background-color: #c62828;
            transform: translateY(-3px);
        }

        .sidebar {
            width: 60px;
            background-color: #1f2937;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 10px;
            transition: width 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 999;
        }

        .sidebar:hover {
            width: 250px;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
        }

        .sidebar a {
            color: #b0bec5;
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            margin-bottom: 10px;
            font-weight: 500;
            transition: background 0.3s, padding-left 0.3s ease;
            justify-content: center;
        }

        .sidebar a:hover {
            background-color: #3b4a59;
            color: #ffffff;
            padding-left: 20px;
            transform: translateX(5px);
        }

        .sidebar a .icon {
            width: 30px;
            text-align: center;
            margin-right: 10px;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover .icon {
            color: #4CAF50;
            transform: translateX(5px);
        }

        .sidebar a .text {
            display: none;
            font-size: 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar:hover a .text {
            display: block;
            opacity: 1;
        }

        .job-container {
            background: #fff;
            padding: 20px;
            margin: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .labour-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
            background: #f9f9f9;
        }

        .btn {
            padding: 6px 12px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-accept {
            background-color: #4CAF50;
            color: white;
        }

        .btn-reject {
            background-color: #f44336;
            color: white;
        }

        .back-button {
            display: inline-block;
            background-color: #2E7D32;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin: 20px 30px;
        }

        .back-button:hover {
            background-color: #1B5E20;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 1rem;
            }

            .user-info {
                margin-top: 10px;
            }

            body {
                padding-left: 60px;
                padding-top: 110px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>কৃষক ড্যাশবোর্ড</h1>
    <div class="user-info">
        <span>স্বাগতম, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="sidebar">
    <h2>ন্যাভিগেশন</h2>
    <a href="farmer.php"><i class="fas fa-wallet icon"></i><span class="text">ড্যাশবোর্ড</span></a>
    <a href="F_Smart_Crop_Doctor.php"><i class="fas fa-stethoscope icon"></i><span class="text">স্মার্ট ফসল ডাক্তার</span></a>
    <a href="Agrologist_List.php"><i class="fas fa-tree icon"></i><span class="text">কৃষি-বিশেষজ্ঞদের সেবা</span></a>
    <a href="F_article.php"><i class="fas fa-pen icon"></i><span class="text">প্রবন্ধ পড়ুন</span></a>
    <a href="crop_management.php"><i class="fas fa-seedling icon"></i><span class="text">ফসল ব্যবস্থাপনা</span></a>
    <a href="Buy.php"><i class="fas fa-shopping-cart icon"></i><span class="text">সরবরাহ থেকে কিনুন</span></a>
    <a href="F_labour_list.php"><i class="fas fa-list icon"></i><span class="text">শ্রমিক তালিকা</span></a>
    <a href="labour_jobs.php"><i class="fas fa-briefcase icon"></i><span class="text">চাকরি পোস্ট</span></a>
    <a href="farmer_applications.php"><i class="fas fa-briefcase icon"></i><span class="text">আবেদনসমূহ</span></a>
    <a href="rent_page.php"><i class="fas fa-tools icon"></i><span class="text">ভাড়ার পরিষেবা</span></a>
    <a href="addNewProduct.php"><i class="fas fa-plus-circle icon"></i><span class="text">নতুন পণ্য</span></a>
    <a href="farmer/order_management.php"><i class="fas fa-clipboard-list icon"></i><span class="text">অর্ডার ম্যানেজ</span></a>
    <a href="farmer/inventory_management.php"><i class="fas fa-boxes icon"></i><span class="text">ইনভেন্টরি</span></a>
    <a href="farmer/financial_overview.php"><i class="fas fa-wallet icon"></i><span class="text">আর্থিক সারসংক্ষেপ</span></a>
    <a href="analytics_report.php"><i class="fas fa-chart-bar icon"></i><span class="text">প্রতিবেদন</span></a>
</div>

<h2 style="margin: 20px 30px;">আপনার চাকরির জন্য আবেদনকারীদের তালিকা</h2>

<?php while ($job = $job_posts->fetch_assoc()): ?>
    <div class="job-container">
        <h3>পোস্ট: <?= htmlspecialchars($job['caption']) ?> 🕒 <?= $job['post_date'] ?></h3>
        <?php
        $job_id = $job['id'];
        $stmt = $conn->prepare("SELECT ja.*, l.name, l.photo, l.age, l.salary_per_day, l.description, l.job_experience, l.location
                                FROM job_applications ja
                                JOIN labour l ON ja.labour_id = l.user_id
                                WHERE ja.job_id = ?");
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $applications = $stmt->get_result();
        ?>
        <?php if ($applications->num_rows > 0): ?>
            <?php while ($app = $applications->fetch_assoc()): ?>
                <div class="labour-box">
                    <h3><?= htmlspecialchars($app['name']) ?></h3>
                    <?php if (!empty($app['photo'])): ?>
                        <img src="<?= htmlspecialchars($app['photo']) ?>" alt="Labour Photo" width="120" style="border-radius: 10px; margin-bottom: 10px;">
                    <?php endif; ?>
                    <p><strong>অবস্থান:</strong> <?= htmlspecialchars($app['location']) ?></p>
                    <p><strong>দৈনিক মজুরি:</strong> <?= htmlspecialchars($app['salary_per_day']) ?> টাকা</p>
                    <p><strong>অভিজ্ঞতা:</strong> <?= htmlspecialchars($app['job_experience']) ?></p>
                    <p><strong>বিবরণ:</strong><br> <?= nl2br(htmlspecialchars($app['description'])) ?></p>
                    <p>🟠 <strong>স্ট্যাটাস:</strong> <?= htmlspecialchars($app['status']) ?></p>
                    <?php if ($app['status'] === 'Pending'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                            <input type="hidden" name="status" value="Accepted">
                            <button class="btn btn-accept" type="submit">✔️ গ্রহণ করুন</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                            <input type="hidden" name="status" value="Rejected">
                            <button class="btn btn-reject" type="submit">❌ প্রত্যাখ্যান করুন</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>এই পোস্টে এখনো কেউ আবেদন করেনি।</p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<a href="farmer.php" class="back-button">⬅ ফিরে যান</a>

</body>
</html>
