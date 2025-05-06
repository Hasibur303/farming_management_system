<?php
session_start();
include 'database.php';

try {
    // Check authentication
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
        header("Location: login.php");
        exit();
    }

  // Fetch available products - this should be at the start of your try block
  $productStmt = $conn->prepare("
  SELECT fc.*, fc.farmer_id,p.image as product_image
  FROM farmer_crops fc
  LEFT JOIN products p ON fc.product_id = p.id
  WHERE fc.quantity > 0
");

if (!$productStmt->execute()) {
    throw new Exception("Error fetching products: " . $conn->error);
}

$products = $productStmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $userId = $_SESSION['user_id'];

    try {
        // Fetch cart items grouped by farmer
        $stmt = $conn->prepare("
            SELECT c.*, fc.name as crop_name, fc.farmer_id, fc.price,
                   u.name as customer_name
            FROM cart c
            JOIN farmer_crops fc ON c.product_id = fc.product_id
            JOIN users u ON c.user_id = u.user_id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $cartItems = $stmt->get_result();

        if ($cartItems->num_rows > 0) {
            $conn->begin_transaction();
            try {
                $orderQuery = $conn->prepare("
                    INSERT INTO orders (
                        farmer_id,
                        customer_id,
                        product_id,
                        customer_name,
                        crop_name,
                        quantity,
                        total_amount,
                        status,
                        order_date
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
                ");

                $orders = [];
                while ($item = $cartItems->fetch_assoc()) {
                    $farmerId = $item['farmer_id'];
                    $orders[$farmerId][] = $item;
                }

                foreach ($orders as $farmerId => $items) {
                    foreach ($items as $item) {
                        $totalAmount = $item['price'] * $item['quantity'];
                        $orderQuery->bind_param(
                            "iiissid",
                            $farmerId,
                            $userId,
                            $item['product_id'],
                            $item['customer_name'],
                            $item['crop_name'],
                            $item['quantity'],
                            $totalAmount
                        );
                        $orderQuery->execute();
                    }
                }

                // Clear cart
                $clearCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                $clearCart->bind_param("i", $userId);
                $clearCart->execute();

                $conn->commit();
                $_SESSION['message'] = "Orders placed successfully!";
              
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['error'] = "Error placing orders: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Your cart is empty.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error processing orders: " . $e->getMessage();
    }
}


  // Handle removing items from cart
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product removed from cart successfully!";
    } else {
        $_SESSION['error'] = "Error removing product from cart.";
    }
}



    // Handle adding items to cart
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user_id'];
        $farmer_id= $_POST['farmer_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;




        // Check if product already exists in cart
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND farmer_id=?");
        $stmt->bind_param("iii", $userId, $productId,$farmer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity if product exists
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $userId, $productId);
        } else {
            // Insert new cart item

            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id,farmer_id, quantity) VALUES (?, ?,?, ?)");
            $stmt->bind_param("iiii", $userId, $productId,$farmer_id, $quantity);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Product added to cart successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
    exit();
        } else {
            $_SESSION['error'] = "Error adding product to cart.";
        }
    }


    // Fetch cart items
    $cartStmt = $conn->prepare("
        SELECT c.*, fc.name, fc.price, fc.quantity_type, fc.image,fc.farmer_id
        FROM cart c
        JOIN farmer_crops fc ON c.product_id = fc.product_id AND c.farmer_id=fc.farmer_id
        WHERE c.user_id = ?
    ");
    $cartStmt->bind_param("i", $_SESSION['user_id']);
    $cartStmt->execute();
    $cartItems = $cartStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Calculate cart total
    $cartTotal = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $cartItems));

} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "An error occurred while processing your request.";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];
    $farmer_id= $_POST['farmer_id'];
    $action = $_POST['update_quantity'];

    if ($action === 'increase') {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ? AND farmer_id=?");
    } elseif ($action === 'decrease') {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ? AND farmer_id=?");
    }

    $stmt->bind_param("iii", $userId, $productId,$farmer_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Cart updated successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['error'] = "Error updating cart.";
    }

   
}

// Fetch search term from request
$searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Fetch available products with search filter
$productStmt = $conn->prepare("
    SELECT fc.*, fc.farmer_id, p.image as product_image
    FROM farmer_crops fc
    LEFT JOIN products p ON fc.product_id = p.id
    WHERE fc.quantity > 0 AND fc.name LIKE ?
");
$productStmt->bind_param("s", $searchTerm);

if (!$productStmt->execute()) {
    throw new Exception("Error fetching products: " . $conn->error);
}

$products = $productStmt->get_result();



// Fetch the customer's past orders including product images
$orderHistoryStmt = $conn->prepare("
    SELECT
        o.order_id,
        o.crop_name,
        o.quantity,
        o.total_amount,
        o.status,
        o.order_date,
        fc.price,
        fc.quantity_type,
        fc.image
    FROM orders o
    LEFT JOIN farmer_crops fc ON o.product_id = fc.product_id
    WHERE o.customer_id = ?
    ORDER BY o.order_date DESC
");
$orderHistoryStmt->bind_param("i", $_SESSION['user_id']);
$orderHistoryStmt->execute();
$orderHistory = $orderHistoryStmt->get_result();

// Fetch top-selling products
$query = "
    SELECT
        products.id AS product_id,
        products.name AS product_name,
        products.price AS product_price,
        products.image AS product_image,
        SUM(orders.quantity) AS total_sold
    FROM
        orders
    JOIN
        products ON orders.product_id = products.id
    GROUP BY
        products.id
    ORDER BY
        total_sold DESC
    LIMIT 10
";

$result = $conn->query($query);

// Initialize an array to store the results
$topSellingProducts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topSellingProducts[] = $row;
    }
}








?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.5">
    <title>Customer Dashboard - SmartKrishi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="./css/customer.css">

<style>

.payment-options {
    display: flex;
    gap: 20px;
    margin: 20px 0;
    flex-wrap: wrap;
    justify-content: center;
}

.payment-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    text-align: center;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 15px;
    width: 100px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    transition: all 0.3s ease-in-out;
}

.payment-option:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border: 2px solid transparent;
    border-radius: 8px;
    padding: 5px;
    background-color: #f9f9f9;
    transition: border-color 0.3s, background-color 0.3s;
}

.payment-option input[type="radio"]:checked + img {
    border-color: #2e7d32;
    background-color: #e0f2f1;
}

.payment-option span {
    margin-top: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #2e3d2f;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background: #f5f5f5;
    margin: auto;
    padding: 30px;
    border: none;
    width: 400px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    animation: fadeIn 0.3s ease-in-out;
}

.modal .close {
    color: #c62828;
    float: right;
    font-size: 26px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.modal .close:hover {
    color: #a00000;
}

.payment-methods {
    margin: 25px 0 10px;
    font-size: 18px;
    font-weight: 600;
    color: #2e3d2f;
    text-align: center;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

/* Cart Sidebar */
.cart-sidebar {
    position: fixed;
    right: -420px;
    top: 0;
    width: 400px;
    height: 100%;
    background-color: #f0f4c3;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
    padding: 25px 20px;
    overflow-y: auto;
    z-index: 1000;
    transition: right 0.4s ease-in-out;
    font-family: 'Segoe UI', sans-serif;
    color: #2e3d2f;
}

.cart-sidebar.open {
    right: 0;
}

.cart-sidebar h2 {
    font-size: 22px;
    color: #2e7d32;
    margin-bottom: 10px;
}

.cart-sidebar .close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 26px;
    color: #2e7d32;
    cursor: pointer;
    transition: color 0.3s;
}
.cart-sidebar .close:hover {
    color: #1b5e20;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    background-color: #ffffff;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(34, 139, 34, 0.08);
}

.cart-item h4 {
    margin: 0;
    font-size: 17px;
    color: #2e7d32;
}

.cart-item p {
    margin: 5px 0;
    color: #4e944f;
    font-size: 14px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 8px 0;
}

.quantity-controls button {
    padding: 4px 10px;
    font-size: 16px;
    background-color: #dcedc8;
    color: #2e7d32;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}
.quantity-controls button:hover {
    background-color: #c5e1a5;
}

.cart-item span {
    font-size: 16px;
    font-weight: 500;
    color: #2e3d2f;
}

.remove-btn {
    background-color: transparent;
    border: 1px solid #c62828;
    color: #c62828;
    padding: 5px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}
.remove-btn:hover {
    background-color: #ffebee;
}

.cart-total {
    font-size: 18px;
    font-weight: bold;
    color: #2e7d32;
    margin: 15px 0;
    text-align: right;
    border-top: 1px solid #c8e6c9;
    padding-top: 10px;
}

.place-order-form {
    text-align: center;
    margin-top: 10px;
}

.btn-primary {
    background-color: #8bc34a;
    color: white;
    padding: 10px 25px;
    border: none;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn-primary:hover {
    background-color: #689f38;
}


</style>
</head>
   
<body>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Language Toggle -->
    <div class="language-toggle">
        <button id="btn-bn" class="btn btn-secondary" onclick="changeLanguage('bn')">BN</button>
        <button id="btn-en" class="btn btn-secondary" onclick="changeLanguage('en')">EN</button>
    </div>

    <ul>
        <li><a href="customer.php" class="nav-link"><i class="fas fa-home"></i> <span class="text-dashboard">ড্যাশবোর্ড</span></a></li>
        <li><a href="C_market.php" class="nav-link"><i class="fas fa-store"></i> <span class="text-market">বাজার</span></a></li>
        <li><a href="C_review.php" class="nav-link"><i class="fas fa-star"></i> <span class="text-review">পর্যালোচনা</span></a></li>
        <li><a href="C_top_selling_products.php" class="nav-link"><i class="fas fa-chart-line"></i> <span class="text-top-selling">সর্বাধিক বিক্রিত</span></a></li>
        <li><a href="C_order_history.php" class="nav-link"><i class="fas fa-history"></i> <span class="text-order-history">অর্ডার ইতিহাস</span></a></li>
        <li><a href="C_purchase_history.php" class="nav-link"><i class="fas fa-shopping-cart"></i> <span class="text-purchase-history">ক্রয়ের ইতিহাস</span></a></li>
        <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span class="text-logout">লগআউট</span></a></li>
    </ul>
</div>

<header>
    <h1 class="text-header">গ্রাহক ড্যাশবোর্ড - স্মার্টকৃষি</h1>
</header>





<!-- Cart Icon -->
<div class="cart-icon" onclick="toggleCart()">
    কার্ট <span class="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span>
</div>
<!-- Search Bar -->
<div class="search-bar">
    <form method="GET" action="C_market.php">
        <input
            type="text"
            name="search"
            placeholder="ফসল বা পণ্য অনুসন্ধান করুন..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            class="search-input"
        >
        <button type="submit" class="search-button text-search-btn">অনুসন্ধান করুন</button>
    </form>
</div>


<!-- Update the cart section with confirmation -->
<div class="cart-sidebar" id="cartSidebar">
    <h1>শপিং কার্ট</h1>
    <span class="close" onclick="toggleCart()">&times;</span>
    <div id="cartItems">
        <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
    <div>
        <h4><?= htmlspecialchars($item['name']) ?></h4>
        <p>Price: TK. <?= htmlspecialchars($item['price']) ?></p>
        <div class="quantity-controls">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                <input type="hidden" name="farmer_id" value="<?= $item['farmer_id'] ?>">
                <input type="hidden" name="update_quantity" value="decrease">
                <button type="submit" class="btn-decrement">-</button>
            </form>
            <span><?= htmlspecialchars($item['quantity']) ?></span>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                <input type="hidden" name="update_quantity" value="increase">
                <input type="hidden" name="farmer_id" value="<?= $item['farmer_id'] ?>">
                <button type="submit" class="btn-increment">+</button>
            </form>
        </div>
        <p>Total: TK. <?= htmlspecialchars($item['price'] * $item['quantity']) ?></p>
    </div>
    <form method="POST">
        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
        <button type="submit" name="remove_from_cart" class="remove-btn">অপসারণ</button>
    </form>
</div>

            <?php endforeach; ?>
            <div class="cart-total">
                Total: TK. <?= htmlspecialchars($cartTotal) ?>
            </div>

            <!-- Place Order Button -->
            <form method="POST" class="place-order-form" onsubmit="return confirmOrder()">
                <button type="submit" name="place_order" class="btn-primary">অর্ডার দিন</button>
            </form>

        <?php else: ?>
            <p>তোমার কার্ট খালি।</p>
        <?php endif; ?>


        <!-- Payment Method Selection -->
        <div class="payment-methods">
            <h3>পেমেন্ট পদ্ধতি নির্বাচন করুন:</h3>
            <div class="payment-options">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="bkash" required>
                                    <img src="bkash.jpeg" alt="bKash">
                                    <span>bKash</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="nagad">
                                    <img src="nagad.jpeg" alt="Nagad">
                                    <span>Nagad</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="rocket">
                                    <img src="rocket.jpg" alt="Rocket">
                                    <span>Rocket</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cod">
                                    <img src="cod.jpg" alt="Cash on Delivery">
                                    <span>COD</span>
                                </label>
        </div>
         </div>

        <!-- Order Button -->
        <button onclick="handleOrderClick()" class="btn-primary">অর্ডার দিন</button>

        <!-- Payment Modal -->
        <div id="paymentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closePaymentModal()">&times;</span>
                <h3>পেমেন্ট তথ্য দিন</h3>
                <form method="POST">
                    <label>একাউন্ট নম্বর:</label><br>
                    <input type="text" name="account_number" required><br><br>

                    <label>পরিমাণ (টাকা):</label><br>
                    <input type="number" name="amount" required><br><br>

                    <label>পিন নম্বর:</label><br>
                    <input type="password" name="pin" required><br><br>

                    <button type="submit" name="confirm_payment" class="btn-primary">নিশ্চিত করুন</button>
                </form>
            </div>
        </div>

    </div>
</div>



<!-- Add message display -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['message']) ?>
        <?php unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>



<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="productDetails">
            <!-- Product details will be inserted here -->
        </div>
    </div>
</div>



<h2 class="market-title text-market-title">বাজারের পণ্য</h2>




<div class="product-grid">
    <?php if ($products->num_rows > 0): ?>
        <?php while ($row = $products->fetch_assoc()): ?>
            <div class="product-card" onclick="showProductDetails(<?= htmlspecialchars(json_encode($row)) ?>)">
                <div class="card">
                    <?php
                    $display_image = !empty($row['image']) ? $row['image'] : $row['product_image'];
                    ?>
                    <img src="<?= htmlspecialchars($display_image); ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($row['name']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($row['name']); ?></h3>
                        <p class="card-text">Price: TK. <?= htmlspecialchars($row['price']); ?> / <?= htmlspecialchars($row['quantity_type']); ?></p>
                        <p class="card-text">Available: <?= htmlspecialchars($row['quantity']); ?> <?= htmlspecialchars($row['quantity_type']); ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center;">এই মুহূর্তে কোনও পণ্য উপলব্ধ নেই।</p>
    <?php endif; ?>
</div>



<script>

// Store the default language setting in the session or local storage (default Bangla)
if (!localStorage.getItem('language')) {
    localStorage.setItem('language', 'bn'); // Default to Bangla
}

function changeLanguage(language) {
    localStorage.setItem('language', language);
    location.reload(); // Refresh the page to reflect the change
}

// Load the language when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const language = localStorage.getItem('language');
    if (language === 'en') {
        // Change all text to English
        document.body.classList.add('en');
    } else {
        // Change all text to Bangla
        document.body.classList.remove('en');
    }
});


function toggleCart() {
    document.getElementById('cartSidebar').classList.toggle('active');
}

function confirmOrder() {
    return confirm('আপনি কি নিশ্চিত যে আপনি এই অর্ডারটি দিতে চান?');
}

function showProductDetails(product) {
    const modal = document.getElementById('productModal');
    const details = document.getElementById('productDetails');

    details.innerHTML = `
        <h2>${product.name}</h2>
        <img src="${product.image || product.product_image}" alt="${product.name}" style="max-width: 200px;">
        <p>দাম :  ${product.price} টাকা</p>
        <form method="POST">
            <input type="hidden" name="product_id" value="${product.product_id}">
            <input type="hidden" name="farmer_id" value="${product.farmer_id}">
            <div class="form-group">
                <label>পরিমাণ:</label>
                <input type="number" name="quantity" min="1" value="1" required class="form-control">
            </div>
            <button type="submit" name="add_to_cart" class="btn-primary">কার্ট এ যোগ করুন</button>
        </form>
    `;

    modal.style.display = "block";
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        closeModal();
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});

function handleOrderClick() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!selectedMethod) {
        alert("দয়া করে একটি পেমেন্ট পদ্ধতি নির্বাচন করুন।");
        return;
    }

    let url = "";

    switch (selectedMethod.value) {
        case "bkash":
            url = "bkash.php"; // 🟣 Real Bkash sandbox redirection
            break;
        case "nagad":
            url = "nagad.php"; // 🟠 Mock Nagad page
            break;
        case "rocket":
            url = "rocket.php"; // 🔵 Mock Rocket page
            break;
        default:
            document.querySelector('form.place-order-form').submit();
            return;
    }

    // Redirect to payment page
    window.location.href = url;
}

function closePaymentModal() {
    document.getElementById("paymentModal").style.display = "none";
}
</script>

<!-- JavaScript Language Switcher -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const lang = localStorage.getItem("language") || "bn";
    setLanguage(lang);
});

function changeLanguage(lang) {
    localStorage.setItem("language", lang);
    setLanguage(lang);
}

function setLanguage(lang) {
    const textMap = {
        bn: {
            "text-dashboard": "ড্যাশবোর্ড",
            "text-market": "বাজার",
            "text-review": "পর্যালোচনা",
            "text-top-selling": "সর্বাধিক বিক্রিত",
            "text-order-history": "অর্ডার ইতিহাস",
            "text-purchase-history": "ক্রয়ের ইতিহাস",
            "text-logout": "লগআউট",
            "text-header": "গ্রাহক ড্যাশবোর্ড - স্মার্টকৃষি",
            "text-cart-label": "কার্ট",
            "text-market-title": "বাজারের পণ্য",
            "text-search-btn": "অনুসন্ধান করুন"
        },
        en: {
            "text-dashboard": "Dashboard",
            "text-market": "Market",
            "text-review": "Review",
            "text-top-selling": "Top Selling",
            "text-order-history": "Order History",
            "text-purchase-history": "Purchase History",
            "text-logout": "Logout",
            "text-header": "Customer Dashboard - SmartKirshi",
            "text-cart-label": "Cart",
            "text-market-title": "Market Products",
            "text-search-btn": "Search"

        }
    };

    Object.keys(textMap[lang]).forEach(cls => {
        const el = document.querySelector(`.${cls}`);
        if (el) el.innerText = textMap[lang][cls];
    });
}
</script>







</body>
</html>
