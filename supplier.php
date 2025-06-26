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
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>সরবরাহকারী ড্যাশবোর্ড – SmartKrishi</title>

  <!-- Font Awesome (icons) -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

  <!-- === Styles (inline for demo; move to style.css in production) === -->
  <style>
  /* ---------- Global Reset ---------- */
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

  body{font-family:'Segoe UI','Georgia',serif;background:#f5f7f4;color:#333}

  /* ---------- Sidebar ---------- */
  .sidebar{
    position:fixed;top:0;left:0;height:100%;width:250px;
    background:linear-gradient(to bottom,#4caf50,#2e7d32);
    box-shadow:3px 0 10px rgba(0,0,0,.1);padding-top:80px;z-index:10}
  .sidebar-menu{list-style:none;padding:0}
  .sidebar-menu a{
    display:block;padding:15px 25px;color:#fff;font-size:16px;font-weight:500;
    text-decoration:none;transition:.3s;border-left:4px solid transparent}
  .sidebar-menu a:hover,
  .sidebar-menu a.active{
    background:rgba(255,255,255,.1);padding-left:35px;border-left:4px solid #81c784}
  .logout-btn{border-top:1px solid rgba(255,255,255,.2);margin-top:30px;font-weight:bold}
  .logout-btn:hover{background:#c62828;padding-left:35px}

  /* ---------- Header ---------- */
  header{
    background:linear-gradient(to right,#81c784,#388e3c);
    color:#fff;padding:30px 50px;text-align:center;
    border-bottom:4px solid #2e7d32;margin-left:250px;
    box-shadow:0 4px 12px rgba(0,0,0,.2)}
  header h1{font-size:2rem;font-weight:700;letter-spacing:1px;text-shadow:1px 1px 4px rgba(0,0,0,.3)}

  /* ---------- Layout ---------- */
  .container{max-width:1200px;margin-left:250px;padding:40px}

  .form-container,.table-container{
    background:#fff;border-radius:12px;padding:40px 30px;
    box-shadow:0 6px 20px rgba(0,0,0,.08);margin-bottom:40px;transition:.3s}
  .form-container:hover,.table-container:hover{transform:translateY(-6px);box-shadow:0 12px 35px rgba(0,0,0,.15)}
  h2{font-size:26px;color:#2e7d32;text-align:center;margin-bottom:30px;text-transform:uppercase;letter-spacing:1px}

  /* ---------- Forms ---------- */
  form label{display:block;margin:15px 0 6px;font-weight:600;font-size:15px}
  form input[type="text"],
  form input[type="number"],
  form select{
    width:100%;padding:12px;border:2px solid #ccc;border-radius:8px;
    font-size:16px;background:#f0f0f0;margin-bottom:25px;transition:border .3s}
  form input:focus,form select:focus{border-color:#4caf50;background:#e8f5e9;outline:0}
  .btn-primary{
    background:#43a047;color:#fff;border:none;padding:14px 28px;font-size:16px;
    border-radius:8px;cursor:pointer;transition:.3s}
  .btn-primary:hover{background:#2e7d32;transform:translateY(-2px) scale(1.03)}
  .btn-danger{
    background:#ff7043;color:#fff;border:none;padding:10px 16px;font-size:14px;
    border-radius:6px;cursor:pointer;transition:.3s}
  .btn-danger:hover{background:#e64a19;transform:scale(1.05)}

  /* ---------- Table ---------- */
  table{width:100%;border-collapse:collapse;background:#fafafa;margin-top:20px}
  thead th{
    background:#66bb6a;color:#fff;padding:16px;font-size:16px;text-align:left;border-bottom:2px solid #388e3c}
  tbody td{padding:14px 16px;border-bottom:1px solid #ddd;font-size:15px;background:#fff}
  tbody tr:hover td{background:#f1f8e9}

  /* inline-img utility */
  .thumb{max-width:120px;border-radius:6px;transition:.3s}
  .thumb:hover{transform:scale(1.1)}

  /* Flex row for edit/delete buttons */
  .inline-actions{display:flex;gap:8px;flex-wrap:wrap}

  /* Messages */
  .success,.error{text-align:center;font-weight:600;font-size:16px;margin:10px 0;animation:fadeIn .5s}
  .success{color:#2e7d32}.error{color:#d32f2f}
  @keyframes fadeIn{0%{opacity:0;transform:translateY(-10px)}100%{opacity:1;transform:translateY(0)}}

  /* ---------- Responsive ---------- */
  @media (max-width:768px){
    .sidebar{width:200px}
    .container,header{margin-left:200px;padding:20px}
    header h1{font-size:1.5rem}
  }
  </style>
</head>

<body>

  <!-- ===== Sidebar ===== -->
  <aside class="sidebar">
    <ul class="sidebar-menu">
      <li><a href="supplier.php"               class="<?= $current==='supplier.php'             ? 'active':'' ?>">ড্যাশবোর্ড</a></li>
      <li><a href="supplier/add_equipment.php" class="<?= $current==='add_equipment.php'        ? 'active':'' ?>">ভাড়ার জন্য নতুন সরঞ্জাম</a></li>
      <li><a href="supplier/supplier_orders.php" class="<?= $current==='supplier_orders.php'    ? 'active':'' ?>">অর্ডার ম্যানেজমেন্ট</a></li>
      <li><a href="supplier/add_new_supply.php"  class="<?= $current==='add_new_supply.php'     ? 'active':'' ?>">নতুন সরবরাহ যোগ করুন</a></li>
      <li><a href="supplier/my_supplies.php"     class="<?= $current==='my_supplies.php'        ? 'active':'' ?>">আমার সরবরাহ</a></li>
      <li><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> লগআউট</a></li>
    </ul>
  </aside>

  <!-- ===== Header ===== -->
  <header>
    <h1>সরবরাহকারী ড্যাশবোর্ড – স্মার্টকৃষি</h1>
  </header>

  <!-- ===== Main ===== -->
  <main class="container">


    <!-- Most Expensive Supply -->
    <section class="table-container">
      <h2>সবচেয়ে ব্যয়বহুল সরবরাহ</h2>
      <?php if ($most_expensive): ?>
        <p><strong>সরবরাহের নাম:</strong> <?= htmlspecialchars($most_expensive['supply_name']) ?></p>
        <p><strong>দাম:</strong> <?= htmlspecialchars($most_expensive['price']) ?></p>
      <?php else: ?>
        <p>কোনও সরবরাহ পাওয়া যায়নি</p>
      <?php endif; ?>
    </section>

    <!-- Supplies Table -->
    <section class="table-container">
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
              <td><?= htmlspecialchars($supply['supply_id']) ?></td>
              <td><?= htmlspecialchars($supply['supply_name']) ?></td>
              <td><?= htmlspecialchars($supply['quantity']) ?></td>
              <td><?= htmlspecialchars($supply['quantity_type']) ?></td>
              <td><?= htmlspecialchars($supply['price']) ?></td>
              <td>
                <?php if (!empty($supply['image'])): ?>
                  <img src="<?= htmlspecialchars($supply['image']) ?>"
                       alt="ছবিঃ <?= htmlspecialchars($supply['supply_name']) ?>"
                       class="thumb">
                <?php else: ?>No Image<?php endif; ?>
              </td>
              <td>
                <div class="inline-actions">
                  <!-- Edit -->
                  <form method="post" action="supplier.php" enctype="multipart/form-data">
                    <input type="hidden" name="supply_id" value="<?= $supply['supply_id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= $supply['image'] ?>">

                    <input type="text"     name="supply_name"   value="<?= htmlspecialchars($supply['supply_name']) ?>" required>
                    <input type="number"   name="quantity"      value="<?= htmlspecialchars($supply['quantity']) ?>" required>
                    <select name="quantity_type" required>
                      <option value="Per-Kg"    <?= $supply['quantity_type']==='Per-Kg'   ? 'selected':'' ?>>Per-Kg</option>
                      <option value="Per-Piece" <?= $supply['quantity_type']==='Per-Piece'? 'selected':'' ?>>Per-Piece</option>
                    </select>
                    <input type="text"     name="price"         value="<?= htmlspecialchars($supply['price']) ?>" required>
                    <input type="file"     name="supply_image"  accept="image/*">
                    <button type="submit" name="edit_supply" class="btn-primary">সংরক্ষণ</button>
                  </form>

                  <!-- Delete -->
                  <form method="post" action="supplier.php"
                        onsubmit="return confirm('আপনি কি নিশ্চিত?');">
                    <input type="hidden" name="supply_id" value="<?= $supply['supply_id'] ?>">
                    <button type="submit" name="delete_supply" class="btn-danger">মুছে ফেলুন</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>

  </main>
</body>
</html>


