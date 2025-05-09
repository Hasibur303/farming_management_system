<?php
session_start();
include('database.php');

// Dummy farmer ID for demonstration
$farmer_id = 1;

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
    <style>
        body {
            font-family: 'Noto Sans Bengali', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .job-container {
            background: #fff;
            padding: 20px;
            margin-bottom: 30px;
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
        .labour-box form {
            display: inline-block;
            margin-right: 10px;
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
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #1B5E20;
            transform: translateY(-2px);
        }

    </style>
</head>
<body>
    <h2>আপনার চাকরির জন্য আবেদনকারীদের তালিকা</h2>

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

    <a href="labour_jobs.php" class="back-button">
        ⬅ ফিরে যান
    </a>

</body>
</html>
