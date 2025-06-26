<?php
/* ==============================================================
   add_equipment.php – SmartKrishi Supplier Portal
   ============================================================== */
session_start();
include('../database.php');

/* ---------- Handle form submission ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_SESSION['user_id'];
    $name        = $_POST['name']            ?? '';
    $type        = $_POST['type']            ?? '';
    $rate        = $_POST['rental_rate_per_day'] ?? 0;
    $quantity    = $_POST['quantity']        ?? 0;
    $description = $_POST['description']     ?? '';
    $imagePath   = '';

    /* ---- Image Upload ---- */
    if (!empty($_FILES['image']['name'])) {
        $imageName  = basename($_FILES['image']['name']);
        $folderName = "uploads/";
        $targetDir  = "../" . $folderName;              // Physical path
        $fileName   = time() . "_" . $imageName;
        $targetFile = $targetDir . $fileName;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $folderName . $fileName;       // Save relative path
        } else {
            $error = "ফাইল আপলোড করতে সমস্যা হয়েছে!";
        }
    }

    /* ---- Insert into DB ---- */
    if (empty($error)) {
        $stmt = $conn->prepare(
            "INSERT INTO equipment
             (supplier_id, name, type, rental_rate_per_day, quantity_available, description, image)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issdiss",
            $supplier_id, $name, $type, $rate, $quantity, $description, $imagePath
        );
        if ($stmt->execute()) {
            $success = "সরঞ্জাম সফলভাবে যোগ হয়েছে!";
        } else {
            $error = "ত্রুটি ঘটেছে!";
        }
    }
}

/* ---------- Active-link helper ---------- */
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ভাড়ার জন্য সরঞ্জাম যোগ করুন – SmartKrishi</title>

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>

    <!-- ============ Shared Styles (inline for demo) ============ -->
    <style>
    /* ---- Reset & Base ---- */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI','Noto Sans Bengali',sans-serif;background:#f5f7f4;color:#333}

    /* ---- Sidebar ---- */
    .sidebar{position:fixed;top:0;left:0;height:100%;width:250px;
             background:linear-gradient(to bottom,#4caf50,#2e7d32);
             box-shadow:3px 0 10px rgba(0,0,0,.1);padding-top:80px;z-index:10}
    .sidebar-menu{list-style:none;padding:0}
    .sidebar-menu a{display:block;padding:15px 25px;color:#fff;font-size:16px;font-weight:500;
                    text-decoration:none;transition:.3s;border-left:4px solid transparent}
    .sidebar-menu a:hover,.sidebar-menu a.active{
        background:rgba(255,255,255,.1);padding-left:35px;border-left:4px solid #81c784}
    .logout-btn{border-top:1px solid rgba(255,255,255,.2);margin-top:30px;font-weight:bold}
    .logout-btn:hover{background:#c62828;padding-left:35px}

    /* ---- Header ---- */
    header{background:linear-gradient(to right,#81c784,#388e3c);color:#fff;padding:30px 50px;
           text-align:center;border-bottom:4px solid #2e7d32;margin-left:250px;
           box-shadow:0 4px 12px rgba(0,0,0,.2)}
    header h1{font-size:2rem;font-weight:700;letter-spacing:1px;text-shadow:1px 1px 4px rgba(0,0,0,.3)}

    /* ---- Layout ---- */
    .container{max-width:800px;margin-left:250px;padding:40px}

    .box{background:#fff;border-radius:12px;padding:40px 30px;box-shadow:0 6px 20px rgba(0,0,0,.08)}
    .box:hover{transform:translateY(-6px);box-shadow:0 12px 35px rgba(0,0,0,.15);transition:.3s}

    h2{font-size:26px;color:#2e7d32;text-align:center;margin-bottom:30px;text-transform:uppercase}

    /* ---- Form ---- */
    form{display:flex;flex-direction:column;gap:18px}
    label{font-weight:600}
    input,textarea,select{
        padding:12px;border:2px solid #ccc;border-radius:8px;font-size:16px;background:#f0f0f0}
    input:focus,textarea:focus,select:focus{border-color:#4caf50;background:#e8f5e9;outline:0}
    .btn-primary{background:#43a047;color:#fff;border:none;padding:14px;font-size:16px;border-radius:8px;
                 cursor:pointer;transition:.3s}
    .btn-primary:hover{background:#2e7d32}

    /* ---- Messages ---- */
    .msg{margin-top:-10px;text-align:center;font-weight:600;font-size:16px;animation:fadeIn .5s}
    .success{color:#2e7d32}.error{color:#d32f2f}
    @keyframes fadeIn{0%{opacity:0;transform:translateY(-10px)}100%{opacity:1;transform:translateY(0)}}

    /* ---- Responsive ---- */
    @media (max-width:768px){
      .sidebar{width:200px}
      .container,header{margin-left:200px;padding:25px}
      header h1{font-size:1.6rem}
    }
    </style>
</head>
<body>

<!-- ===== Sidebar ===== -->
<aside class="sidebar">
  <ul class="sidebar-menu">
    <li><a href="../supplier.php"              class="<?= $current==='supplier.php'              ? 'active':'' ?>">ড্যাশবোর্ড</a></li>
    <li><a href="add_equipment.php"           class="<?= $current==='add_equipment.php'          ? 'active':'' ?>">ভাড়ার জন্য সরঞ্জাম</a></li>
    <li><a href="supplier_orders.php"         class="<?= $current==='supplier_orders.php'        ? 'active':'' ?>">অর্ডার ম্যানেজমেন্ট</a></li>
    <li><a href="add_new_supply.php"          class="<?= $current==='add_new_supply.php'         ? 'active':'' ?>">নতুন সরবরাহ</a></li>
    <li><a href="my_supplies.php"             class="<?= $current==='my_supplies.php'            ? 'active':'' ?>">আমার সরবরাহ</a></li>
    <li><a href="../logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> লগআউট</a></li>
  </ul>
</aside>

<!-- ===== Header ===== -->
<header><h1>ভাড়ার জন্য সরঞ্জাম যোগ করুন</h1></header>

<!-- ===== Main ===== -->
<main class="container">
  <section class="box">
    <h2>নতুন সরঞ্জাম যুক্ত করুন</h2>

    <?php if (isset($success)): ?>
      <p class="msg success"><?= htmlspecialchars($success) ?></p>
    <?php elseif (isset($error)): ?>
      <p class="msg error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <label for="name">সরঞ্জামের নাম:</label>
      <input id="name" type="text" name="name" required>

      <label for="type">সরঞ্জামের ধরণ:</label>
      <input id="type" type="text" name="type" required>

      <label for="rate">প্রতি দিনের ভাড়া (৳):</label>
      <input id="rate" type="number" step="0.01" name="rental_rate_per_day" required>

      <label for="quantity">মোট সংখ্যা:</label>
      <input id="quantity" type="number" name="quantity" required>

      <label for="description">বর্ণনা:</label>
      <textarea id="description" name="description" rows="4"></textarea>

      <label for="image">ছবি আপলোড করুন:</label>
      <input id="image" type="file" name="image" accept="image/*">

      <button type="submit" class="btn-primary">সরঞ্জাম যোগ করুন</button>
    </form>
  </section>
</main>
</body>
</html>
