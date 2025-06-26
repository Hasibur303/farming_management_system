<?php
session_start();
include('../database.php');

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
        /* Reset & Base */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
          font-family: 'Segoe UI', 'Noto Sans Bengali', sans-serif;
          background-color: #f0f4f1;
          color: #333;
        }

        /* Sidebar */
        .sidebar {
          position: fixed;
          top: 0;
          left: 0;
          height: 100%;
          width: 250px;
          background: linear-gradient(to bottom, #4caf50, #2e7d32);
          box-shadow: 3px 0 10px rgba(0,0,0,0.1);
          padding-top: 80px;
          z-index: 1000;
        }

        .sidebar-menu {
          list-style: none;
          padding: 0;
        }

        .sidebar-menu li a {
          display: block;
          padding: 15px 25px;
          color: #fff;
          font-size: 16px;
          font-weight: 500;
          text-decoration: none;
          transition: 0.3s;
          border-left: 4px solid transparent;
        }

        .sidebar-menu a.active,
        .sidebar-menu a:hover {
          background: rgba(255,255,255,0.1);
          padding-left: 35px;
          border-left: 4px solid #81c784;
        }

        .logout-btn {
          border-top: 1px solid rgba(255,255,255,0.2);
          margin-top: 30px;
          font-weight: bold;
        }

        .logout-btn:hover {
          background: #c62828;
          padding-left: 35px;
        }

        /* Header */
        header {
          background: linear-gradient(to right, #81c784, #388e3c);
          color: #fff;
          padding: 30px 50px;
          margin-left: 250px;
          box-shadow: 0 4px 12px rgba(0,0,0,0.2);
          border-bottom: 4px solid #2e7d32;
          text-align: center;
        }

        header h1 {
          font-size: 2rem;
          font-weight: 700;
          letter-spacing: 1px;
          text-shadow: 1px 1px 4px rgba(0,0,0,.3);
        }

        /* Main Content */
        .container {
          margin-left: 250px;
          padding: 40px;
        }

        h2 {
          font-size: 24px;
          margin-bottom: 20px;
          color: #2e7d32;
          text-align: center;
          text-transform: uppercase;
        }

        .table-container {
          background: #fff;
          border-radius: 12px;
          padding: 30px;
          margin-bottom: 40px;
          box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        table {
          width: 100%;
          border-collapse: collapse;
        }

        table thead {
          background: #4caf50;
          color: white;
        }

        table th, table td {
          padding: 14px;
          text-align: left;
          border-bottom: 1px solid #ddd;
        }

        table tbody td {
          background: #fafafa;
          vertical-align: top;
        }

        table td img {
          max-width: 120px;
          border-radius: 8px;
        }

        table td form {
          display: flex;
          flex-direction: column;
          gap: 6px;
          margin-top: 10px;
        }

        table td label {
          font-weight: 600;
          font-size: 13px;
        }

        table td input[type="text"],
        table td input[type="number"],
        table td select,
        table td input[type="file"] {
          padding: 6px 8px;
          border: 1px solid #ccc;
          border-radius: 6px;
          background: #f0f0f0;
          font-size: 14px;
        }

        table td input[type="submit"] {
          background: #388e3c;
          color: white;
          border: none;
          padding: 8px 16px;
          border-radius: 6px;
          cursor: pointer;
          font-size: 14px;
          transition: 0.3s;
        }

        table td input[type="submit"]:hover {
          background: #2e7d32;
        }

        table td form[action*="delete"] input[type="submit"] {
          background: #d32f2f;
        }

        table td form[action*="delete"] input[type="submit"]:hover {
          background: #b71c1c;
        }

        @media(max-width: 768px) {
          .sidebar { width: 200px; }
          .container, header { margin-left: 200px; padding: 20px; }
          table td form { flex-direction: column; }
        }
      </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <ul class="sidebar-menu">
      <li><a href="../supplier.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'supplier.php') ? 'class="active"' : ''; ?>>ড্যাশবোর্ড</a></li>
            <li><a href="supplier/add_equipment.php" class="<?= $current==='add_equipment.php'        ? 'active':'' ?>">ভাড়ার জন্য নতুন সরঞ্জাম</a></li>
      <li><a href="supplier/supplier_orders.php" class="<?= $current==='supplier_orders.php'    ? 'active':'' ?>">অর্ডার ম্যানেজমেন্ট</a></li>
      <li><a href="add_new_supply.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'add_new_supply.php') ? 'class="active"' : ''; ?>>নতুন সরবরাহ যোগ করুন</a></li>
      <li><a href="my_supplies.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'my_supplies.php') ? 'class="active"' : ''; ?>>আমার সরবরাহ</a></li>
      <li><a href="logout.php" class="logout-btn">লগআউট</a></li>
    </ul>
  </div>

  <!-- Header -->
  <header>
    <h1>আমার সরবরাহ - স্মার্টকৃষি</h1>
  </header>

  <!-- Main Content -->
  <div class="container">

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
                  <img src="<?= htmlspecialchars($supply['image']); ?>" alt="Supply Image">
                <?php else: ?>
                  No Image
                <?php endif; ?>
              </td>
              <td>
                <!-- Edit Form -->
                <form method="POST" action="supplier.php" enctype="multipart/form-data">
                  <input type="hidden" name="supply_id" value="<?= $supply['supply_id']; ?>">
                  <input type="hidden" name="existing_image" value="<?= $supply['image']; ?>">

                  <label>নাম:
                    <input type="text" name="supply_name" value="<?= htmlspecialchars($supply['supply_name']); ?>" required>
                  </label>

                  <label>পরিমাণ:
                    <input type="number" name="quantity" value="<?= htmlspecialchars($supply['quantity']); ?>" required>
                  </label>

                  <label>পরিমাণের ধরণ:
                    <select name="quantity_type" required>
                      <option value="Per-Kg" <?= $supply['quantity_type'] === 'Per-Kg' ? 'selected' : ''; ?>>Per-Kg</option>
                      <option value="Per-Piece" <?= $supply['quantity_type'] === 'Per-Piece' ? 'selected' : ''; ?>>Per-Piece</option>
                    </select>
                  </label>

                  <label>দাম:
                    <input type="text" name="price" value="<?= htmlspecialchars($supply['price']); ?>" required>
                  </label>

                  <label>ছবি:
                    <input type="file" name="supply_image" accept="image/*">
                  </label>

                  <input type="submit" name="edit_supply" value="সংরক্ষণ করুন">
                </form>

                <!-- Delete Form -->
                <form method="POST" action="supplier.php" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে মুছে ফেলতে চান?');">
                  <input type="hidden" name="supply_id" value="<?= $supply['supply_id']; ?>">
                  <input type="submit" name="delete_supply" value="মুছে ফেলুন">
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


