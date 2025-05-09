<?php
session_start();
include 'database.php';



if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];



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
        'title' => 'আবেদনকৃত চাকরির তালিকা',
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
        'messages' => 'বার্তা',
        'notifications' => 'নোটিফিকেশন',
        'settings' => 'সেটিংস',
    ],
    'en' => [
        'title' => 'Applied Job List',
        'welcome' => 'Welcome, Labourer!',
        'update_profile' => 'Update Your Profile',
        'upload_image' => 'Upload Profile Image:',
        'daily_salary' => 'Daily Salary (in BDT):',
        'bio' => 'Short Description About You:',
        'save_profile' => 'Save Profile',
        'job_list' => 'Farmer Requirements',
        'farmer_name' => 'Farmer Name',
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
    ]
];
$current_text = $text[$lang];

$labour_id = $_SESSION['user_id'] ?? 1;

$sql = "SELECT a.*, j.caption, j.farmer_name, j.post_date
        FROM job_applications a
        JOIN labour_jobpost j ON a.job_id = j.id
        WHERE a.labour_id = ?
        ORDER BY a.apply_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $labour_id);
$stmt->execute();
$result = $stmt->get_result();


?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $current_text['title'] ?> | SmartKirshi</title>
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

        .sidebar:hover {
            width: 250px;
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 30px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar:hover h2 {
            opacity: 1;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 10px;
            white-space: nowrap;
            background-color: rgba(255,255,255,0.1);
            transition: background 0.2s;
        }

        .sidebar a:hover {
            background-color: rgba(255,255,255,0.3);
        }

        .sidebar a span {
            margin-left: 10px;
            display: none;
            transition: opacity 0.3s;
        }

        .sidebar:hover a span {
            display: inline;
            opacity: 1;
        }

        .logout-sidebar {
            color: #ffdddd;
            background-color: #c62828;
        }

        .main {
            margin-left: 270px;
            width: calc(100% - 270px);
            padding: 20px 40px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logout-button {
            background-color: #d32f2f;
            border: none;
            padding: 10px 18px;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-button:hover {
            background-color: #b71c1c;
        }
        .language-btn {
            background-color: #388E3C;
            margin-left: 10px;
        }
        .container {
            margin-top: 30px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #2E7D32;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="file"], textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #388E3C;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #2e7030;
        }
                /* Job Post Styling */
                .job-container {
                    background-color: white;
                    border-radius: 12px;
                    padding: 20px;
                    margin-bottom: 25px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    max-width: 700px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .job-container img {
                    max-width: 100%;
                    border-radius: 10px;
                    margin-bottom: 15px;
                }

                .job-meta {
                    font-size: 16px;
                    color: #555;
                    margin-bottom: 10px;
                }

                .job-caption {
                    font-size: 18px;
                    margin-bottom: 15px;
                    color: #222;
                }

                .apply-button {
                    background-color: #28a745;
                    color: white;
                    padding: 10px 16px;
                    text-decoration: none;
                    border-radius: 8px;
                    display: inline-block;
                    margin-top: 10px;
                    font-size: 18px;
                }
                .apply-button:hover {
                    background-color: #218838;
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
    <a href="notifications.php">🔔 <span><?= $current_text['notifications'] ?></span></a>
    <a href="settings.php">⚙️ <span><?= $current_text['settings'] ?></span></a>
    <a class="logout-sidebar" href="logout.php">🚪 <span><?= $current_text['logout'] ?></span></a>
</div>

<div class="main">
    <div class="top-bar">
        <h2><?= $current_text['title'] ?></h2>
        <div>
            <a href="?lang=bn"><button class="logout-button language-btn">🇧🇩 Bn</button></a>
            <a href="?lang=en"><button class="logout-button language-btn">🇬🇧 En</button></a>
            <a href="logout.php"><button class="logout-button">🚪 <?= $current_text['logout'] ?></button></a>
        </div>
    </div>



<h3 style="text-align:center; font-size:24px; margin-bottom:25px;">
        <?= $current_text['job_list'] ?>
    </h3>



<h2>আপনার আবেদনকৃত চাকরি</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Farmer</th>
        <th>Caption</th>
        <th>Post Date</th>
        <th>Status</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['farmer_name']) ?></td>
            <td><?= htmlspecialchars($row['caption']) ?></td>
            <td><?= $row['post_date'] ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>


</body>
</html>
