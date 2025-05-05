<?php
session_start();
include 'database.php';

// Dummy user ID for demo purposes; in production, use $_SESSION['user_id']
$user_id = 1;

$success = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $salary = $_POST['daily_salary'];
    $bio = $_POST['bio'];

    // Handle profile image upload
    $target_dir = "uploads/";
    $profile_image = '';
    if (!empty($_FILES["profile_image"]["name"])) {
        $profile_image = basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $profile_image;
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
    }

    $sql = "UPDATE users SET daily_salary = ?, bio = ?, profile_image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $salary, $bio, $profile_image, $user_id);

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . $stmt->error;
    }
}

// Fetch job requirements posted by farmers
//$jobs_sql = "SELECT name, requirement_detail, location FROM farmer_jobs ORDER BY created_at DESC";
//$jobs_result = $conn->query($jobs_sql);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>শ্রমিক প্রোফাইল | SmartKirshi</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f8;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #2E7D32;
        }
        .section {
            margin-top: 40px;
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
            transition: background 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #2e7030;
        }
        .job-card {
            background: #f1f1f1;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #4CAF50;
            border-radius: 8px;
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .profile-image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #4CAF50;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>স্বাগতম, শ্রমিক!</h2>

        <!-- Profile Update Section -->
        <div class="section">
            <h3>আপনার প্রোফাইল হালনাগাদ করুন</h3>
            <?php if ($success) echo "<p class='success'>{$success}</p>"; ?>
            <?php if ($error) echo "<p class='error'>{$error}</p>"; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>প্রোফাইল ছবি আপলোড করুন:</label>
                    <input type="file" name="profile_image" accept="image/*">
                </div>
                <div class="form-group">
                    <label>প্রতিদিনের বেতন (টাকায়):</label>
                    <input type="text" name="daily_salary" required>
                </div>
                <div class="form-group">
                    <label>আপনার সম্পর্কে সংক্ষিপ্ত বিবরণ:</label>
                    <textarea name="bio" rows="4" placeholder="আমি একজন অভিজ্ঞ কৃষি শ্রমিক..."></textarea>
                </div>
                <input type="submit" value="প্রোফাইল সংরক্ষণ করুন">
            </form>
        </div>

        <!-- Job Requirements Section -->
        <div class="section">
            <h3>কৃষকদের চাহিদা</h3>
            <?php if ($jobs_result->num_rows > 0): ?>
                <?php while ($job = $jobs_result->fetch_assoc()): ?>
                    <div class="job-card">
                        <strong>কৃষকের নাম:</strong> <?= htmlspecialchars($job['name']) ?><br>
                        <strong>চাহিদা:</strong> <?= htmlspecialchars($job['requirement_detail']) ?><br>
                        <strong>অবস্থান:</strong> <?= htmlspecialchars($job['location']) ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>এই মুহূর্তে কোনো চাহিদা নেই।</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
