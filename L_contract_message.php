<?php
session_start();
include 'database.php';

$user_id = $_SESSION['user_id'];

$labour_id = $_SESSION['user_id'];
$sql = "SELECT * FROM contract WHERE labour_id = ? ORDER BY post_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $labour_id);
$stmt->execute();
$result = $stmt->get_result();

// Language setup
if (!isset($_SESSION['lang'])) $_SESSION['lang'] = 'bn';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['bn', 'en'])) $_SESSION['lang'] = $_GET['lang'];
$lang = $_SESSION['lang'];
$text = [
    'bn' => [
        'title' => 'কৃষক-শ্রমিক চুক্তির অনুরোধ',
        'name' => 'নাম',
        'age' => 'বয়স',
        'salary' => 'প্রতিদিনের বেতন',
        'desc' => 'বিবরণ',
        'experience' => 'কাজের অভিজ্ঞতা',
        'location' => 'অবস্থান',
        'upload' => 'ছবি আপলোড করুন',
        'save' => 'সংরক্ষণ করুন',
        'dashboard' => 'ড্যাশবোর্ড',
        'profile' => 'প্রোফাইল',
        'jobs' => 'কাজের তালিকা',
        'messages' => 'বার্তা',
        'notifications' => 'নোটিফিকেশন',
        'settings' => 'সেটিংস',
        'district' => 'জেলা',
        'logout' => 'লগ আউট',
    ],
    'en' => [
        'title' => 'Farmer-Labourer Contract Request',
        'name' => 'Name',
        'age' => 'Age',
        'salary' => 'Daily Salary',
        'desc' => 'Description',
        'experience' => 'Job Experience',
        'location' => 'Location',
        'upload' => 'Upload Photo',
        'save' => 'Save',
        'dashboard' => 'Dashboard',
        'profile' => 'Profile',
        'jobs' => 'Job List',
        'messages' => 'Messages',
        'notifications' => 'Notifications',
        'settings' => 'Settings',
        'district' => 'District',
        'logout' => 'Logout',
    ]
];
$current_text = $text[$lang];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['contract_id'])) {
        $contract_id = $_POST['contract_id'];
        $status = '';

        if (isset($_POST['accept'])) {
            $status = 'Accepted';
        } elseif (isset($_POST['reject'])) {
            $status = 'Rejected';
        }

        if ($status !== '') {
            $update = $conn->prepare("UPDATE contract SET status = ? WHERE id = ?");
            $update->bind_param("si", $status, $contract_id);
            if ($update->execute()) {
                echo "<script>alert('চুক্তির অবস্থা আপডেট হয়েছে: $status'); window.location.href='L_contract_message.php';</script>";
            } else {
                echo "<script>alert('স্ট্যাটাস আপডেট করতে ব্যর্থ।');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $current_text['title'] ?></title>
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
        .sidebar h2 { font-size: 24px; margin-bottom: 30px; white-space: nowrap; opacity: 0; transition: opacity 0.3s; }
        .sidebar:hover h2 { opacity: 1; }
        .sidebar a { display: flex; align-items: center; padding: 10px; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 10px; white-space: nowrap; background-color: rgba(255,255,255,0.1); transition: background 0.2s; }
        .sidebar a:hover { background-color: rgba(255,255,255,0.3); }
        .sidebar a span { margin-left: 10px; display: none; transition: opacity 0.3s; }
        .sidebar:hover a span { display: inline; opacity: 1; }
        .logout-sidebar { color: #ffdddd; background-color: #c62828; }
        .main { margin-left: 270px; width: calc(100% - 270px); padding: 20px 40px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        .logout-button { background-color: #d32f2f; border: none; padding: 10px 18px; color: white; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .logout-button:hover { background-color: #b71c1c; }
        .language-btn { background-color: #388E3C; margin-left: 10px; }
        .card { background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 6px; color: white; font-weight: bold; cursor: pointer; margin-right: 10px; }
        .btn-success { background-color: #4CAF50; }
        .btn-danger { background-color: #f44336; }
        .btn-primary { background-color: #2196F3; }
        img { max-width: 100%; height: auto; border-radius: 8px; margin-top: 10px; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>SmartKirshi</h2>
    <a href="labour.php">🏠 <span><?= $current_text['dashboard'] ?></span></a>
    <a href="L_profile.php">🧑‍🌾 <span><?= $current_text['profile'] ?></span></a>
    <a href="L_job.php">📋 <span><?= $current_text['jobs'] ?></span></a>
    <a href="L_contract_message.php">💬 <span><?= $current_text['messages'] ?></span></a>
    <a href="notifications.php">🔔 <span><?= $current_text['notifications'] ?></span></a>
    <a href="settings.php">⚙ <span><?= $current_text['settings'] ?></span></a>
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

    <?php while ($row = $result->fetch_assoc()): ?>
        <form method="post" action="L_contract_message.php">
            <input type="hidden" name="contract_id" value="<?= $row['id'] ?>">
            <div class="card" id="contract_<?= $row['id'] ?>">
                <h3>কৃষকের নাম: <?= htmlspecialchars($row['name']) ?></h3>
                <p><strong>জেলা:</strong> <?= htmlspecialchars($row['district']) ?></p>
                <p><strong>শুরু তারিখ:</strong> <?= htmlspecialchars($row['start_date']) ?></p>
                <p><strong>বিবরণ:</strong> <?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <p><strong>টাকা:</strong> ৳<?= number_format($row['amount'], 2) ?></p>
                <p><strong>ঠিকানা:</strong> <?= htmlspecialchars($row['address']) ?></p>
                <p><strong>অবস্থা:</strong> <?= $row['status'] ?></p>
                <?php if (!empty($row['photo'])): ?>
                    <p><strong>ছবি:</strong><br><img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="Contract Image"></p>
                <?php endif; ?>
                <?php if ($row['status'] === 'Pending'): ?>
                    <button type="submit" name="accept" class="btn btn-success">গ্রহণ করুন</button>
                    <button type="submit" name="reject" class="btn btn-danger">প্রত্যাখ্যান করুন</button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary" onclick="printContract('contract_<?= $row['id'] ?>')">প্রিন্ট করুন</button>
            </div>
        </form>
    <?php endwhile; ?>
</div>
<script>
function printContract(id) {
    const printContents = document.getElementById(id).innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>
</body>
</html>