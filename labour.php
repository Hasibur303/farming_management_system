<?php
session_start();
include 'database.php';

// Dummy user ID for demo; replace with $_SESSION['user_id'] in production
if (!isset($_SESSION['user_id'])) {
    // Redirect or handle unauthorized access
    header("Location: login.php");
    exit();
}
$labour_id = $_SESSION['user_id'];

// Language handling
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'bn';
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['bn', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'];
$text = [
    'bn' => [
        'title' => 'শ্রমিকের ড্যাশবোর্ড',
        'welcome' => 'স্বাগতম, শ্রমিক!',
        'update_profile' => 'আপনার প্রোফাইল হালনাগাদ করুন',
        'upload_image' => 'প্রোফাইল ছবি আপলোড করুন:',
        'daily_salary' => 'প্রতিদিনের বেতন (টাকায়):',
        'bio' => 'আপনার সম্পর্কে সংক্ষিপ্ত বিবরণ:',
        'save_profile' => 'প্রোফাইল সংরক্ষণ করুন',
        'job_list' => 'কৃষকদের চাহিদা',
        'farmer_name' => 'কৃষকের নাম',
        'requirement' => 'চাহিদা',
        'location' => 'অবস্থান',
        'no_jobs' => 'এই মুহূর্তে কোনো চাহিদা নেই।',
        'logout' => 'লগ আউট',
        'dashboard' => 'ড্যাশবোর্ড',
        'profile' => 'প্রোফাইল',
        'jobs' => 'কাজের তালিকা',
        'appliedjobs' => 'আবেদনকৃত চাকরির তালিকা',
        'messages' => 'বার্তা',
        'notifications' => 'নোটিফিকেশন',
        'settings' => 'সেটিংস',
        'totaljobpost'=> 'মোট চাকরির পদ',
        'jobsyouapplied'=> 'আবেদনকৃত চাকরি',
        'acceptedjobs'=> 'গৃহীত চাকরি',
        'processingjobs'=> 'চাকরি প্রক্রিয়াধীন',

    ],
    'en' => [
        'title' => 'Labour Dashboard',
        'welcome' => 'Welcome, Labourer!',
        'update_profile' => 'Update Your Profile',
        'upload_image' => 'Upload Profile Image:',
        'daily_salary' => 'Daily Salary (in BDT):',
        'bio' => 'Short Description About You:',
        'save_profile' => 'Save Profile',
        'job_list' => 'Farmer Requirements',
        'farmer_name' => 'Farmer Name',
        'appliedjobs' => 'Applied Job List',
        'requirement' => 'Requirement',
        'location' => 'Location',
        'no_jobs' => 'No job requirements at the moment.',
        'logout' => 'Logout',
        'dashboard' => 'Dashboard',
        'profile' => 'Profile',
        'jobs' => 'Job List',
        'messages' => 'Messages',
        'notifications' => 'Notifications',
        'settings' => 'Settings',
        'totaljobpost'=> 'Total Job Post',
        'jobsyouapplied'=> 'Jobs You Applied',
        'acceptedjobs'=> 'Accepted Jobs',
        'processingjobs'=> 'Processing Jobs',
    ]
];
$current_text = $text[$lang];

// Job metrics queries
$totalJobs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM labour_jobpost"))['total'];
$appliedJobs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM job_applications WHERE labour_id = $labour_id"))['total'];
$acceptedJobs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM job_applications WHERE labour_id = $labour_id AND status = 'Accepted'"))['total'];
$processingJobs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM job_applications WHERE labour_id = $labour_id AND status = 'Pending'"))['total'];
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $current_text['title'] ?> | SmartKirshi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            background: #f0f4f8;
        }
        .sidebar {
            width: 70px;
            background: linear-gradient(180deg, #2E7D32, #1B5E20);
            color: white;
            height: 100vh;
            padding: 20px 10px;
            position: fixed;
            transition: width 0.3s;
            overflow: hidden;
        }
        .sidebar:hover { width: 250px; }
        .sidebar h2 {
            font-size: 24px; margin-bottom: 30px; white-space: nowrap; opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar:hover h2 { opacity: 1; }
        .sidebar a {
            display: flex; align-items: center; padding: 10px;
            color: white; text-decoration: none; border-radius: 8px;
            margin-bottom: 10px; white-space: nowrap;
            background-color: rgba(255,255,255,0.1);
            transition: background 0.2s;
        }
        .sidebar a:hover { background-color: rgba(255,255,255,0.3); }
        .sidebar a span {
            margin-left: 10px; display: none; transition: opacity 0.3s;
        }
        .sidebar:hover a span { display: inline; opacity: 1; }
        .logout-sidebar { color: #ffdddd; background-color: #c62828; }
        .main {
            margin-left: 270px; width: calc(100% - 270px); padding: 20px 40px;
        }
        .top-bar {
            display: flex; justify-content: space-between; align-items: center;
        }
        .logout-button {
            background-color: #d32f2f; border: none; padding: 10px 18px;
            color: white; border-radius: 6px; cursor: pointer; font-weight: bold;
        }
        .logout-button:hover { background-color: #b71c1c; }
        .language-btn { background-color: #388E3C; margin-left: 10px; }
        h2 { color: #2E7D32; }
        .dashboard-metrics {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 40px;
        }

        .metric-card {
            background: #1e1e2f;
            border-radius: 20px;
            padding: 20px;
            width: 220px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            position: relative;
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-8px);
        }

        /* Circular icon box */
        .metric-card .circle {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            animation: pulseGlow 2.5s ease-in-out infinite;
        }

        /* Circle colors for each card */
        .metric-card:nth-child(1) .circle {
            background-color: #e91e63; /* Pink */
        }

        .metric-card:nth-child(2) .circle {
            background-color: #3f51b5; /* Indigo */
        }

        .metric-card:nth-child(3) .circle {
            background-color: #4caf50; /* Green */
        }

        .metric-card:nth-child(4) .circle {
            background-color: #ff9800; /* Orange */
        }

        /* Glow animation */
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 10px rgba(255,255,255,0.2);
            }
            50% {
                box-shadow: 0 0 20px rgba(255,255,255,0.4);
            }
        }

        .metric-card .icon {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .metric-card h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .metric-card p {
            margin-top: 10px;
            font-size: 16px;
            font-weight: 600;
            color: #ccc;
            letter-spacing: 0.5px;
        }

    </style>
</head>
<body>

<div class="sidebar">
    <h2>SmartKirshi</h2>
    <a href="labour.php">🏠 <span><?= $current_text['dashboard'] ?></span></a>
    <a href="L_profile.php">🧑‍🌾 <span><?= $current_text['profile'] ?></span></a>
    <a href="L_job.php">📋 <span><?= $current_text['jobs'] ?></span></a>
    <a href="messages.php">💬 <span><?= $current_text['messages'] ?></span></a>
    <a href="L_apply_job_list.php">📋 <span><?= $current_text['appliedjobs'] ?></span></a>
    <a href="notifications.php">🔔 <span><?= $current_text['notifications'] ?></span></a>
    <a href="settings.php">⚙️ <span><?= $current_text['settings'] ?></span></a>
    <a class="logout-sidebar" href="logout.php">🚪 <span><?= $current_text['logout'] ?></span></a>
</div>

<div class="main">
    <div class="top-bar">
        <h1><?= $current_text['title'] ?></h1>
        <div>
            <a href="?lang=bn"><button class="logout-button language-btn">🇧🇩 Bn</button></a>
            <a href="?lang=en"><button class="logout-button language-btn">🇬🇧 En</button></a>
            <a href="logout.php"><button class="logout-button">🚪 <?= $current_text['logout'] ?></button></a>
        </div>
    </div>

    <div class="dashboard-metrics">
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-briefcase icon"></i>
                <h3><?= $totalJobs ?></h3>
            </div>
            <?= $current_text['totaljobpost'] ?>
        </div>
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-paper-plane icon"></i>
                <h3><?= $appliedJobs ?></h3>
            </div>
            <?= $current_text['jobsyouapplied'] ?>
        </div>
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-check-circle icon"></i>
                <h3><?= $acceptedJobs ?></h3>
            </div>
            <?= $current_text['acceptedjobs'] ?>
        </div>
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-spinner icon"></i>
                <h3><?= $processingJobs ?></h3>
            </div>
            <?= $current_text['processingjobs'] ?>
        </div>
    </div>

</div>

</body>
</html>
