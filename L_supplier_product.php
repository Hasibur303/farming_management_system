<?php
session_start();
include 'database.php';


$user_id = $_SESSION['user_id'];

// Language setup
if (!isset($_SESSION['lang'])) $_SESSION['lang'] = 'bn';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['bn', 'en'])) $_SESSION['lang'] = $_GET['lang'];
$lang = $_SESSION['lang'];
$text = [
    'bn' => [
        'title' => 'সরবরাহকারীর কাছ থেকে কিনুন',
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
        'buyfromsupplier'=> 'সরবরাহকারীর কাছ থেকে কিনুন',

        'cart' => 'কার্ট',
         'addtocart' => 'কার্টে যুক্ত করুন',
         'remove' => 'মুছে ফেলুন',
         'pay' => 'পেমেন্ট পদ্ধতি',
         'confirm' => 'নিশ্চিত করুন',
    ],
    'en' => [
        'title' => 'Buy From Suppliers',
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
        'buyfromsupplier'=> 'Buy From Supplier',
        'cart' => 'Cart',
                'addtocart' => 'Add to Cart',
                'remove' => 'Remove',
                'pay' => 'Payment Method',
                'confirm' => 'Confirm',

    ]
];
$current_text = $text[$lang];

$success = '';
$error = '';

// Initialize cart
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $id = $_POST['supply_id'];
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 1;
    } else {
        $_SESSION['cart'][$id]++;
    }
}

// Remove from cart
if (isset($_POST['remove'])) {
    unset($_SESSION['cart'][$_POST['remove']]);
}

// Fetch supplies
$supplies = $conn->query("SELECT * FROM supplies ORDER BY supply_id DESC");
$cart_items = [];

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $cart_sql = "SELECT * FROM supplies WHERE supply_id IN ($ids)";
    $cart_items_result = $conn->query($cart_sql);
    while ($item = $cart_items_result->fetch_assoc()) {
        $item['quantity'] = $_SESSION['cart'][$item['supply_id']];
        $cart_items[] = $item;
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

           .sidebar:hover {
               width: 250px;
           }

           .sidebar h2 {
               font-size: 24px;
               margin-bottom: 30px;
               white-space: nowrap;
               opacity: 0;
               transition: opacity 0.3s;
           }

           .sidebar:hover h2 {
               opacity: 1;
           }

           .sidebar a {
               display: flex;
               align-items: center;
               padding: 10px;
               color: white;
               text-decoration: none;
               border-radius: 8px;
               margin-bottom: 10px;
               white-space: nowrap;
               background-color: rgba(255,255,255,0.1);
               transition: background 0.2s;
           }

           .sidebar a:hover {
               background-color: rgba(255,255,255,0.3);
           }

           .sidebar a span {
               margin-left: 10px;
               display: none;
               transition: opacity 0.3s;
           }

           .sidebar:hover a span {
               display: inline;
               opacity: 1;
           }

           .logout-sidebar {
               color: #ffdddd;
               background-color: #c62828;
           }

           .main {
               margin-left: 270px;
               width: calc(100% - 270px);
               padding: 20px 40px;
           }
           .top-bar {
               display: flex;
               justify-content: space-between;
               align-items: center;
           }
           .logout-button {
               background-color: #d32f2f;
               border: none;
               padding: 10px 18px;
               color: white;
               border-radius: 6px;
               cursor: pointer;
               font-weight: bold;
           }
           .logout-button:hover {
               background-color: #b71c1c;
           }
           .language-btn {
               background-color: #388E3C;
               margin-left: 10px;
           }
           .container {
               margin-top: 30px;
               background: white;
               padding: 30px;
               border-radius: 12px;
               box-shadow: 0 8px 20px rgba(0,0,0,0.1);
           }
           h2, h3 {
               color: #2E7D32;
           }

body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: #f4f6f9;
        }
        .topbar {
            background: #2E7D32;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-icon {
            font-size: 20px;
            background: green;
            color: #ffffff;
            padding: 1px;
            border-radius: 10%;
            cursor: pointer;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.05);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: #fefefe;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .product-card img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-card h4 {
            margin: 15px 0 5px;
            color: #2E7D32;
        }
        .product-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #444;
        }
        .product-card form {
            margin-top: 10px;
        }
        .product-card button {
            background-color: #388E3C;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
        }
        .cart-modal {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 400px;
            background: white;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            border-radius: 10px;
            padding: 20px;
            display: none;
            z-index: 999;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .cart-item span {
            font-size: 14px;
        }
        .remove-btn {
            background: #e53935;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
        }
        select, .confirm-btn {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            font-weight: bold;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .confirm-btn {
            background: #2E7D32;
            color: white;
            border: none;
            cursor: pointer;
        }

       </style>
</head>
<body>

<div class="sidebar">
    <h2>SmartKirshi</h2>
    <a href="labour.php">🏠 <span><?= $current_text['dashboard'] ?></span></a>
    <a href="L_profile.php">🧑‍🌾 <span><?= $current_text['profile'] ?></span></a>
    <a href="L_job.php">📋 <span><?= $current_text['jobs'] ?></span></a>
    <a href="L_supplier_product.php">📋 <span><?= $current_text['buyfromsupplier'] ?></span></a>
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
        <div class="cart-icon" onclick="toggleCart()">
                                    🛒 <?= $current_text['cart'] ?> (<?= count($_SESSION['cart']) ?>)
                                </div>

    </div>


    <div class="container">
        <div class="product-grid">
            <?php while ($row = $supplies->fetch_assoc()) { ?>
                <div class="product-card">
                    <img src="<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['supply_name']) ?>">
                    <h4><?= htmlspecialchars($row['supply_name']) ?></h4>
                    <p>৳ <?= $row['price'] ?> / <?= $row['quantity_type'] ?></p>
                    <p>Available: <?= $row['quantity'] ?></p>
                    <form method="post">
                        <input type="hidden" name="supply_id" value="<?= $row['supply_id'] ?>">
                        <button type="submit" name="add_to_cart"><?= $current_text['addtocart'] ?></button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <h3><?= $current_text['cart'] ?></h3>
        <?php foreach ($cart_items as $item) { ?>
            <div class="cart-item">
                <span><?= $item['supply_name'] ?> × <?= $item['quantity'] ?></span>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="remove" value="<?= $item['supply_id'] ?>">
                    <button class="remove-btn"><?= $current_text['remove'] ?></button>
                </form>
            </div>
        <?php } ?>

        <?php if (!empty($cart_items)) { ?>
            <select>
                <option><?= $current_text['pay'] ?>: Cash on Delivery</option>
                <option><?= $current_text['pay'] ?>: bKash</option>
                <option><?= $current_text['pay'] ?>: Nagad</option>
            </select>
            <button class="confirm-btn"><?= $current_text['confirm'] ?></button>
        <?php } else { ?>
            <p>No items in cart.</p>
        <?php } ?>
    </div>

    <script>
        function toggleCart() {
            const modal = document.getElementById('cartModal');
            modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
        }
    </script>

</body>
</html>