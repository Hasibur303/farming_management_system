<?php
session_start();
include 'database.php'; // Include the database connection file

// Check if the user is logged in and has the role of 'Supplier'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Supplier') {
    header("Location: login.php"); // Redirect to login page if not authenticated
    exit();
}

// Initialize messages
$error = '';
$success_message = '';

// Initialize filter variables
$price_min = $_GET['price_min'] ?? 0;
$price_max = $_GET['price_max'] ?? 900000;
$quantity_type = $_GET['quantity_type'] ?? '';

// Handle adding new supply
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supply'])) {
    $supply_name = $_POST['supply_name'];
    $quantity = $_POST['quantity'];
    $quantity_type = $_POST['quantity_type'];
    $price = $_POST['price'];
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['supply_image']) && $_FILES['supply_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
        }
        $target_file = $target_dir . basename($_FILES["supply_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            // Move the file to the target directory
            if (move_uploaded_file($_FILES["supply_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                $error = "Failed to upload the image.";
            }
        }
    }

    if (empty($error)) {
        try {
            $sql = "INSERT INTO supplies (supplier_id, supply_name, quantity, quantity_type, price, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isidss", $_SESSION['user_id'], $supply_name, $quantity, $quantity_type, $price, $image_path);
            if ($stmt->execute()) {
                $success_message = "Supply added successfully!";
            } else {
                $error = "Failed to add supply.";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle supply deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_supply'])) {
    $supply_id = $_POST['supply_id'];

    try {
        $sql = "DELETE FROM supplies WHERE supply_id = ? AND supplier_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $supply_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $success_message = "Supply deleted successfully!";
        } else {
            $error = "Failed to delete supply.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle supply editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_supply'])) {
    $supply_id = $_POST['supply_id'];
    $supply_name = $_POST['supply_name'];
    $quantity = $_POST['quantity'];
    $quantity_type = $_POST['quantity_type'];
    $price = $_POST['price'];
    $existing_image = $_POST['existing_image'];

    $image_path = $existing_image;

    // Handle image upload
    if (isset($_FILES['supply_image']) && $_FILES['supply_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["supply_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types) && move_uploaded_file($_FILES["supply_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Update database record
    try {
        $sql = "UPDATE supplies SET supply_name = ?, quantity = ?, quantity_type = ?, price = ?, image = ? WHERE supply_id = ? AND supplier_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisisii", $supply_name, $quantity, $quantity_type, $price, $image_path, $supply_id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success_message = "Supply updated successfully!";
        } else {
            $error = "Failed to update supply.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch supplies for the logged-in supplier with filtering
$supplier_id = $_SESSION['user_id'];
try {
    $sql = "
    SELECT *
    FROM supplies
    WHERE supplier_id = ?
    AND price BETWEEN ? AND ?
    AND (quantity_type = ? OR ? = '')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $supplier_id, $price_min, $price_max, $quantity_type, $quantity_type);
    $stmt->execute();
    $supplies_result = $stmt->get_result();

    // Subquery to fetch the most expensive supply
    $sql_most_expensive = "
    SELECT supply_name, price
    FROM supplies
    WHERE supplier_id = ?
    AND price = (SELECT MAX(price) FROM supplies WHERE supplier_id = ?)";
    $stmt_expensive = $conn->prepare($sql_most_expensive);
    $stmt_expensive->bind_param("ii", $supplier_id, $supplier_id);
    $stmt_expensive->execute();
    $expensive_result = $stmt_expensive->get_result();
    $most_expensive = $expensive_result->fetch_assoc();
} catch (Exception $e) {
    $error = "Error fetching supplies: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard - AgriBuzz</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            color: #333;
        }

        .container {
            width: 85%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 40px;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #8bc34a, #4caf50);
            color: white;
            padding: 30px 50px;
            text-align: center;
            border-bottom: 5px solid #388e3c;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            margin: 0;
            font-size: 2em;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3);
        }

        header a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            margin-left: 25px;
            font-weight: 600;
            text-transform: uppercase;
            transition: color 0.3s ease;
        }

        header a:hover {
            color: #d4af37;
        }

        /* Containers */
        .form-container, .table-container {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin: 40px 0;
            padding: 40px 30px;
            transition: 0.4s ease;
        }

        .form-container:hover, .table-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        h2 {
            margin: 0 0 30px;
            font-size: 28px;
            color: #4caf50;
            text-align: center;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Form Elements */
        form label {
            display: block;
            margin: 18px 0 6px;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.5px;
        }

        form input[type="text"],
        form input[type="number"],
        form select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 25px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background: #f0f0f0;
            font-size: 16px;
            transition: 0.3s ease;
        }

        form input:focus, form select:focus {
            border-color: #4caf50;
            background: #e8f5e9;
            outline: none;
        }

        form input[type="submit"] {
            background: #4caf50;
            color: white;
            border: none;
            padding: 14px 25px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        form input[type="submit"]:hover {
            background: #388e3c;
            transform: translateY(-2px) scale(1.03);
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fafafa;
            margin-top: 20px;
        }

        table thead th {
            background: #8bc34a;
            color: #fff;
            padding: 16px;
            font-size: 17px;
            text-align: left;
            border-bottom: 3px solid #388e3c;
        }

        table tbody td {
            padding: 14px 16px;
            font-size: 15px;
            border-bottom: 1px solid #ddd;
            background: #fdfdfd;
            transition: background 0.3s ease;
        }

        table tbody td:hover {
            background: #e8f5e9;
        }

        table td img {
            max-width: 100px;
            border-radius: 8px;
            transition: 0.3s;
        }

        table td img:hover {
            transform: scale(1.1);
        }

        table td input[type="submit"] {
            background: #ff5722;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        table td input[type="submit"]:hover {
            background: #d32f2f;
            transform: scale(1.05);
        }

        /* Messages */
        .success, .error {
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            animation: bounceIn 0.6s ease-out;
        }

        .success { color: #4caf50; }
        .error { color: #f44336; }

        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            60% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); }
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #388e3c;
            padding-top: 80px;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
        }

        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 15px 25px;
            color: white;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover {
            background: #2e7d32;
            padding-left: 35px;
        }

        .sidebar-menu a.active {
            background: #2e7d32;
            border-left: 5px solid #81c784;
        }

        .sidebar-menu .logout-btn {
            color: white;
            padding: 15px 25px;
            display: block;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .sidebar-menu .logout-btn:hover {
            background: #d32f2f;
            padding-left: 35px;
        }

        /* Layout Adjustments */
        .container {
            margin-left: 250px;
        }

        header {
            margin-left: 250px;
            padding: 30px;
        }
    </style>

</head>
<body>
<div class="sidebar">
    <ul class="sidebar-menu">
        <li><a href="supplier.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'supplier.php') ? 'class="active"' : ''; ?>>ড্যাশবোর্ড</a></li>
        <li><a href="supplier/add_equipment.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'add_equipment.php') ? 'class="active"' : ''; ?>>ভাড়ার জন্য নতুন সরঞ্জাম যুক্ত করুন</a></li>
        <li><a href="supplier/supplier_orders.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'supplier_orders.php') ? 'class="active"' : ''; ?>>অর্ডার ম্যানেজমেন্ট</a></li>
        <li><a href="supplier/add_new_supply.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'add_new_supply.php') ? 'class="active"' : ''; ?>>নতুন সরবরাহ যোগ করুন</a></li>
        <li><a href="supplier/my_supplies.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'my_supplies.php') ? 'class="active"' : ''; ?>>আমার সরবরাহ</a></li>


        <li><a href="logout.php" class="logout-btn">লগআউট</a></li>
    </ul>
</div>


    <header>
        <h1>সরবরাহকারী ড্যাশবোর্ড - স্মার্টকৃষি</h1>

    </header>

    <div class="container">
        <!-- Filter Form -->
        <div class="form-container">
            <h2>ফিল্টার সরবরাহ</h2>
            <form method="GET" action="supplier.php">
                <label for="price_min">সর্বনিম্ন মূল্য:</label>
                <input type="number" name="price_min" value="<?= htmlspecialchars($price_min); ?>">

                <label for="price_max">সর্বোচ্চ মূল্য:</label>
                <input type="number" name="price_max" value="<?= htmlspecialchars($price_max); ?>">

                <label for="quantity_type">পরিমাণের ধরণ:</label>
                <select name="quantity_type">
                    <option value="">সব</option>
                    <option value="Per-Kg" <?= $quantity_type === 'Per-Kg' ? 'selected' : ''; ?>>প্রতি কেজি</option>
                    <option value="Per-Piece" <?= $quantity_type === 'Per-Piece' ? 'selected' : ''; ?>>প্রতি-পিস</option>
                </select>

                <input type="submit" value="Filter">
            </form>
        </div>

        <!-- Add New Supply Form -->
        <div class="form-container">
            <h2>নতুন সরবরাহ যোগ করুন</h2>
            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success"><?= htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <form method="POST" action="supplier.php" enctype="multipart/form-data">
                <label for="supply_name">সরবরাহের নাম:</label>
                <input type="text" name="supply_name" required>

                <label for="quantity">পরিমাণ:</label>
                <input type="number" name="quantity" required>

                <label for="quantity_type">পরিমাণের ধরণ:</label>
                <select name="quantity_type" required>
                    <option value="Per-Piece" selected>প্রতি-পিস</option>
                    <option value="Per-Kg">প্রতি কেজি</option>
                </select>


                <label for="price">দাম:</label>
                <input type="text" name="price" required>

                <label for="supply_image">ছবি:</label>
                <input type="file" name="supply_image" accept="image/*">

                <input type="submit" name="add_supply" value="Add Supply">
            </form>
        </div>

        <!-- Most Expensive Supply -->
        <div class="table-container">
            <h2>সবচেয়ে ব্যয়বহুল সরবরাহ</h2>
            <?php if ($most_expensive): ?>
                <p><strong>সরবরাহের নাম:</strong> <?= htmlspecialchars($most_expensive['supply_name']); ?></p>
                <p><strong>দাম:</strong> <?= htmlspecialchars($most_expensive['price']); ?></p>
            <?php else: ?>
                <p>কোনও সরবরাহ পাওয়া যায়নি</p>
            <?php endif; ?>
        </div>

        <!-- Supplies Table -->
        <div class="table-container">
            <h2>তোমার সরবরাহ</h2>
            <table>
                <thead>
                    <tr>
                        <th>সরবরাহ আইডি</th>
                        <th>সরবরাহের নাম</th>
                        <th>পরিমাণ</th>
                        <th>পরিমাণের ধরণ</th>
                        <th>দাম</th>
                        <th>ছবি</th>
                        <th>প্রক্রিয়া</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($supply = $supplies_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($supply['supply_id']); ?></td>
                            <td><?= htmlspecialchars($supply['supply_name']); ?></td>
                            <td><?= htmlspecialchars($supply['quantity']); ?></td>
                            <td><?= htmlspecialchars($supply['quantity_type']); ?></td>
                            <td><?= htmlspecialchars($supply['price']); ?></td>
                            <td>
                                <?php if (!empty($supply['image'])): ?>
                                    <img src="<?= htmlspecialchars($supply['image']); ?>" alt="Supply Image" style="max-width: 300px;">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                               <!-- Edit Form -->
                               <form method="POST" action="supplier.php" enctype="multipart/form-data" style="display: inline-block;">
                                   <input type="hidden" name="supply_id" value="<?= $supply['supply_id']; ?>">
                                   <input type="hidden" name="existing_image" value="<?= $supply['image']; ?>">

                                   <!-- Supply Name -->
                                   <label>নাম:
                                       <input type="text" name="supply_name" value="<?= htmlspecialchars($supply['supply_name']); ?>" required>
                                   </label>

                                   <!-- Quantity -->
                                   <label>পরিমাণ:
                                       <input type="number" name="quantity" value="<?= htmlspecialchars($supply['quantity']); ?>" required>
                                   </label>

                                   <!-- Quantity Type -->
                                   <label>পরিমাণের ধরণ:
                                       <select name="quantity_type" required>
                                           <option value="Per-Kg" <?= $supply['quantity_type'] === 'Per-Kg' ? 'selected' : ''; ?>>Per-Kg</option>
                                           <option value="Per-Piece" <?= $supply['quantity_type'] === 'Per-Piece' ? 'selected' : ''; ?>>Per-Piece</option>
                                       </select>
                                   </label>

                                   <!-- Price -->
                                   <label>দাম:
                                       <input type="text" name="price" value="<?= htmlspecialchars($supply['price']); ?>" required>
                                   </label>

                                   <!-- Image Upload -->
                                   <label>ছবি:
                                       <input type="file" name="supply_image" accept="image/*">
                                   </label>

                                   <!-- Save Button -->
                                   <input type="submit" name="edit_supply" value="সংরক্ষণ করুন">
                               </form>

                                <!-- Delete Form -->
                                <form method="POST" action="supplier.php" style="display: inline-block;">
                                    <input type="hidden" name="supply_id" value="<?= $supply['supply_id']; ?>">
                                    <input type="submit" name="delete_supply" value="মুছে ফেলুন" onclick="return confirm('Are you sure?')">
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