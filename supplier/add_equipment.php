<?php
session_start();
include('../database.php');



// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $rate = $_POST['rental_rate_per_day'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $imagePath = '';

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . time() . "_" . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO equipment (supplier_id, name, type, rental_rate_per_day, quantity_available, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdiss", $supplier_id, $name, $type, $rate, $quantity, $description, $imagePath);

    if ($stmt->execute()) {
        $success = "সরঞ্জাম সফলভাবে যোগ হয়েছে!";
    } else {
        $error = "ত্রুটি ঘটেছে!";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>সরঞ্জাম যোগ করুন</title>
    <style>
        body {
            font-family: 'Noto Sans Bengali', sans-serif;
            background-color: #eef2f3;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            color: #2e7d32;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input, textarea, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #43a047;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #388e3c;
        }

        .message {
            text-align: center;
            font-weight: bold;
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>ভাড়ার জন্য নতুন সরঞ্জাম যুক্ত করুন</h2>

        <?php if (isset($success)): ?>
            <p class="message"><?= $success ?></p>
        <?php elseif (isset($error)): ?>
            <p class="message error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>সরঞ্জামের নাম:</label>
            <input type="text" name="name" required>

            <label>সরঞ্জামের ধরণ:</label>
            <input type="text" name="type" required>

            <label>প্রতি দিনের ভাড়া (৳):</label>
            <input type="number" step="0.01" name="rental_rate_per_day" required>

            <label>মোট সংখ্যা:</label>
            <input type="number" name="quantity" required>

            <label>বর্ণনা:</label>
            <textarea name="description" rows="4"></textarea>

            <label>ছবি আপলোড করুন:</label>
            <input type="file" name="image" accept="image/*">

            <input type="submit" value="সরঞ্জাম যোগ করুন">
        </form>
    </div>
</body>
</html>
