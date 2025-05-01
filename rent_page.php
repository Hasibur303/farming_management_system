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
    <style>
        body {
            font-family: 'Noto Sans Bengali', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
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
            width: 80px;
            height: auto;
            border-radius: 6px;
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
                                <img src="<?= htmlspecialchars($row['image']); ?>" alt="ছবি">
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
</body>
</html>
