
<?php
session_start();
include 'database.php';
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'bn';
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['bn', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'];
// Text translations
$text = [
    'bn' => [
        'title' => 'বাজার',
        'search_placeholder' => 'পণ্য অনুসন্ধান করুন...',
        'search_button' => 'অনুসন্ধান',
        'cart' => 'কার্ট',
        'products' => 'পণ্য সমূহ',
        'price' => 'দাম',
        'quantity' => 'পরিমাণ',
        'add_to_cart' => 'কার্টে যোগ করুন',
        'no_products' => 'কোন পণ্য পাওয়া যায়নি',
        'welcome' => 'আপনাকে স্বাগতম'
    ],
    'en' => [
        'title' => 'Market',
        'search_placeholder' => 'Search products...',
        'search_button' => 'Search',
        'cart' => 'Cart',
        'products' => 'Products',
        'price' => 'Price',
        'quantity' => 'Quantity',
        'add_to_cart' => 'Add to Cart',
        'no_products' => 'No products found',
        'welcome' => 'Welcome'
    ]
];
$current = $text[$lang];
try {
    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Handle adding to cart
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user_id'];
        $farmer_id = $_POST['farmer_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        // Check if product exists in cart
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND farmer_id=?");
        $stmt->bind_param("iii", $userId, $productId, $farmer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $userId, $productId);
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, farmer_id, quantity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $userId, $productId, $farmer_id, $quantity);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Product added to cart!";
        } else {
            $_SESSION['error'] = "Error adding to cart";
        }
        header("Location: C_market.php");
        exit();
    }

    // Handle removing from cart
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user_id'];

        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $userId, $productId);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Product removed from cart!";
        } else {
            $_SESSION['error'] = "Error removing from cart";
        }
        header("Location: C_market.php");
        exit();
    }

    // Handle quantity updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user_id'];
        $farmer_id = $_POST['farmer_id'];
        $action = $_POST['update_quantity'];

        if ($action === 'increase') {
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ? AND farmer_id=?");
        } else {
            $stmt = $conn->prepare("UPDATE cart SET quantity = GREATEST(1, quantity - 1) WHERE user_id = ? AND product_id = ? AND farmer_id=?");
        }

        $stmt->bind_param("iii", $userId, $productId, $farmer_id);
        $stmt->execute();
        header("Location: C_market.php");
        exit();
    }

    // Fetch products with search
    $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
    $productStmt = $conn->prepare("
        SELECT fc.*, p.image as product_image
        FROM farmer_crops fc
        LEFT JOIN products p ON fc.product_id = p.id
        WHERE fc.quantity > 0 AND fc.name LIKE ?
    ");
    $productStmt->bind_param("s", $searchTerm);
    $productStmt->execute();
    $products = $productStmt->get_result();

    // Fetch cart items
    $cartStmt = $conn->prepare("
        SELECT c.*, fc.name, fc.price, fc.quantity_type, fc.image, fc.farmer_id
        FROM cart c
        JOIN farmer_crops fc ON c.product_id = fc.product_id AND c.farmer_id=fc.farmer_id
        WHERE c.user_id = ?
    ");
    $cartStmt->bind_param("i", $_SESSION['user_id']);
    $cartStmt->execute();
    $cartItems = $cartStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $cartTotal = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $cartItems));

} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}
// [Previous PHP code remains the same until the closing PHP tag]
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $current['title'] ?> - SmartKrishi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f9fc;
            color: #333;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 70px;
            height: 100vh;
            background: linear-gradient(to bottom, #3e4e60, #4b5c6b);
            border-right: 1px solid #dddddd;
            transition: width 0.3s ease;
            overflow-x: hidden;
            z-index: 1000;
        }

        .sidebar:hover {
            width: 220px;
        }

        .sidebar ul {
            padding: 70px 0 20px 0;
            list-style: none;
            margin: 0;
        }

        .sidebar .nav-link {
            color: #ffffff;
            font-size: 16px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav-link i {
            font-size: 24px;
            font-weight: bold;
            margin-right: 30px;
            min-width: 24px;
        }

        .sidebar .nav-link:hover {
            background-color: #e9f5ee;
            color: #28a745;
        }

        /* Language Switcher */
        .language-switcher {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
        }

        .language-btn {
            border: none;
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 4px;
            cursor: pointer;
            background-color: #d20103;
            color: white;
        }

        .language-btn.active {
            background-color: #28a745;
        }

        /* Main Content */
        .main-content {
            margin-left: 70px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 220px;
        }

        /* Cart Sidebar */
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background-color: white;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1001;
            padding: 20px;
            overflow-y: auto;
        }

        .cart-sidebar.active {
            right: 0;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        /* Cart Icon */
        .cart-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #e2136e;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .cart-count {
            background-color: white;
            color: #e2136e;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Payment Methods */
        .payment-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 15px 0;
        }

        .payment-option {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            width: 80px;
        }

        .payment-option.selected {
            border-color: #28a745;
            background-color: #f0fff0;
        }

        .payment-option img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
            }
            .sidebar:hover {
                width: 220px;
            }
            .main-content {
                margin-left: 0;
            }
            .cart-sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Language Switcher -->

<!-- Sidebar -->
<div class="sidebar">

    <ul>
    <div class="language-switcher">
        <button class="language-btn <?= $lang === 'bn' ? 'active' : '' ?>" onclick="changeLanguage('bn')">BN</button>
        <button class="language-btn <?= $lang === 'en' ? 'active' : '' ?>" onclick="changeLanguage('en')">EN</button>
    </div>

        <li><a href="customer.php" class="nav-link"><i class="fas fa-home"></i> <span><?= $lang === 'bn' ? 'ড্যাশবোর্ড' : 'Dashboard' ?></span></a></li>
        <li><a href="C_market.php" class="nav-link"><i class="fas fa-store"></i> <span><?= $current['title'] ?></span></a></li>
        <li><a href="C_review.php" class="nav-link"><i class="fas fa-star"></i> <span><?= $lang === 'bn' ? 'পর্যালোচনা' : 'Reviews' ?></span></a></li>
        <li><a href="C_top_selling_products.php" class="nav-link"><i class="fas fa-chart-line"></i> <span><?= $lang === 'bn' ? 'সর্বাধিক বিক্রিত' : 'Top Selling' ?></span></a></li>
        <li><a href="C_order_history.php" class="nav-link"><i class="fas fa-history"></i> <span><?= $lang === 'bn' ? 'অর্ডার ইতিহাস' : 'Order History' ?></span></a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Search Bar -->
    <div class="search-container mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" class="form-control" name="search"
                       placeholder="<?= $current['search_placeholder'] ?>"
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">
                    <?= $current['search_button'] ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    <h3><?= $current['products'] ?></h3>
    <div class="product-grid">
        <?php if ($products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image'] ?? $product['product_image']) ?>"
                         class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>"
                         style="height: 200px; width: 100%; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text">
                            <?= $current['price'] ?>: ৳<?= htmlspecialchars($product['price']) ?> / <?= htmlspecialchars($product['quantity_type']) ?>
                        </p>
                        <p class="card-text">
                            <?= $current['quantity'] ?>: <?= htmlspecialchars($product['quantity']) ?> <?= htmlspecialchars($product['quantity_type']) ?>
                        </p>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <input type="hidden" name="farmer_id" value="<?= $product['farmer_id'] ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-success w-100">
                                <?= $current['add_to_cart'] ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info"><?= $current['no_products'] ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Cart Icon -->
<div class="cart-icon" onclick="toggleCart()">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count"><?= count($cartItems) ?></span>
    <span><?= $current['cart'] ?></span>
</div>

<!-- Cart Sidebar -->
<div class="cart-sidebar" id="cartSidebar">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= $current['cart'] ?></h3>
        <button class="btn btn-sm btn-outline-danger" onclick="toggleCart()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <?php if (!empty($cartItems)): ?>
        <div class="cart-items mb-3">
            <?php foreach ($cartItems as $item): ?>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5><?= htmlspecialchars($item['name']) ?></h5>
                                <p>৳<?= htmlspecialchars($item['price']) ?> / <?= htmlspecialchars($item['quantity_type']) ?></p>
                                <div class="d-flex align-items-center">
                                    <form method="POST" class="me-2">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <input type="hidden" name="farmer_id" value="<?= $item['farmer_id'] ?>">
                                        <input type="hidden" name="update_quantity" value="decrease">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">-</button>
                                    </form>
                                    <span class="mx-2"><?= $item['quantity'] ?></span>
                                    <form method="POST" class="ms-2">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <input type="hidden" name="farmer_id" value="<?= $item['farmer_id'] ?>">
                                        <input type="hidden" name="update_quantity" value="increase">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">+</button>
                                    </form>
                                </div>
                                <p class="mt-2">Total: ৳<?= $item['price'] * $item['quantity'] ?></p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button type="submit" name="remove_from_cart" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-total mb-3">
            <h4 class="text-end"><?= $current['price'] ?>: ৳<?= number_format($cartTotal, 2) ?></h4>
        </div>

        <!-- Payment Methods -->
        <div class="payment-methods mb-3">
            <h5><?= $lang === 'bn' ? 'পেমেন্ট পদ্ধতি নির্বাচন করুন' : 'Select Payment Method' ?>: <span class="text-danger">*</span></h5>
            <div class="payment-options">
                <div class="payment-option" onclick="selectPayment('bkash')">
                    <img src="bkash.jpeg" alt="bKash">
                    <span>bKash</span>
                </div>
                <div class="payment-option" onclick="selectPayment('nagad')">
                    <img src="nagad.jpeg" alt="Nagad">
                    <span>Nagad</span>
                </div>

                <div class="payment-option" onclick="selectPayment('cod')">
                    <img src="cod.jpg" alt="COD">
                    <span>COD</span>
                </div>
            </div>
            <div id="paymentError" class="text-danger mt-2" style="display: none;">
                <?= $lang === 'bn' ? 'দয়া করে একটি পেমেন্ট পদ্ধতি নির্বাচন করুন' : 'Please select a payment method' ?>
            </div>
        </div>

        <form method="POST" action="process_order.php" id="orderForm">
            <input type="hidden" name="payment_method" id="selectedPaymentMethod">
            <button type="submit" class="btn btn-success w-100 py-2" onclick="return validatePayment()">
                <i class="fas fa-shopping-bag"></i> <?= $lang === 'bn' ? 'অর্ডার সম্পন্ন করুন' : 'Place Order' ?>
            </button>
        </form>
    <?php else: ?>
        <div class="alert alert-info"><?= $current['no_products'] ?></div>
    <?php endif; ?>
</div>

<!-- Messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1100;">
        <?= $_SESSION['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1100;">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Language switching
    function changeLanguage(lang) {
        window.location.href = '?lang=' + lang;
    }

    // Toggle cart sidebar
    function toggleCart() {
        document.getElementById('cartSidebar').classList.toggle('active');
    }

    // Select payment method
    function selectPayment(method) {
        document.getElementById('selectedPaymentMethod').value = method;

        // Update UI
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
        document.getElementById('paymentError').style.display = 'none';
    }

    // Validate payment before submission
    function validatePayment() {
        const paymentMethod = document.getElementById('selectedPaymentMethod').value;

        if (!paymentMethod) {
            document.getElementById('paymentError').style.display = 'block';
            document.querySelector('.payment-methods').scrollIntoView({ behavior: 'smooth' });
            return false;
        }

        return confirm("<?= $lang === 'bn' ? 'আপনি কি নিশ্চিত যে আপনি এই অর্ডারটি দিতে চান?' : 'Are you sure you want to place this order?' ?>");
    }

    // Auto-close alerts after 5 seconds
    window.setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
</body>
</html>