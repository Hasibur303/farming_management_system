<?php
// Start session and connect DB
session_start();
include('database.php');

// Fetch available equipment
$sql = "SELECT * FROM equipment WHERE quantity_available > 0";
$result = $conn->query($sql);

// Handle rental form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rent_now'])) {
    $farmer_id = $_SESSION['user_id']; // Assuming farmer is logged in
    $equipment_id = $_POST['equipment_id'];
    $quantity = $_POST['quantity'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Get rental rate
    $stmt = $conn->prepare("SELECT rental_rate_per_day FROM equipment WHERE equipment_id = ?");
    $stmt->bind_param("i", $equipment_id);
    $stmt->execute();
    $stmt->bind_result($rate);
    $stmt->fetch();
    $stmt->close();

    $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
    $total_cost = $rate * $quantity * ($days > 0 ? $days : 1);

    // Insert rental
    $insert = $conn->prepare("INSERT INTO equipment_rentals (farmer_id, equipment_id, quantity, rental_start_date, rental_end_date, total_cost) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iiissd", $farmer_id, $equipment_id, $quantity, $start_date, $end_date, $total_cost);

    if ($insert->execute()) {
        echo "<script>alert('ভাড়া সফলভাবে সম্পন্ন হয়েছে');</script>";
    } else {
        echo "<script>alert('ভাড়া ব্যর্থ হয়েছে');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>সরঞ্জাম ভাড়া</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Noto Sans Bengali', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .sidebar i {
            margin-right: 10px;
        }

        /* Content area styles */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #4caf50;
            color: white;
        }

        td img {
            width: 250px;
            height: auto;
            border-radius: 1px;
        }

        input[type="number"], input[type="date"] {
            padding: 6px;
            width: 90px;
        }

        input[type="submit"] {
            padding: 8px 14px;
            background-color: #388e3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #2e7d32;
        }

        .rent-form {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
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

    <!-- Content Area -->
    <div class="content">
        <div class="container">
            <h2>ভাড়ার জন্য উপলব্ধ সরঞ্জাম</h2>
            <table>
                <thead>
                    <tr>
                        <th>ছবি</th>
                        <th>সরঞ্জামের নাম</th>
                        <th>ধরণ</th>
                        <th>প্রাপ্যতা</th>
                        <th>ভাড়া (প্রতি দিন)</th>
                        <th>ভাড়া ফর্ম</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                   <img src="<?= htmlspecialchars($row['image']); ?>" width="300" height="250" />
                                <?php else: ?>
                                    ছবি নেই
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= htmlspecialchars($row['quantity_available']) ?></td>
                            <td>৳<?= htmlspecialchars($row['rental_rate_per_day']) ?></td>
                            <td>
                                <form method="POST" class="rent-form">
                                    <input type="hidden" name="equipment_id" value="<?= $row['equipment_id'] ?>">
                                    <input type="number" name="quantity" min="1" max="<?= $row['quantity_available'] ?>" required placeholder="সংখ্যা">
                                    <input type="date" name="start_date" required>
                                    <input type="date" name="end_date" required>
                                    <input type="submit" name="rent_now" value="ভাড়া নিন">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
