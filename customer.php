<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}
$customer_id = $_SESSION['user_id'];

// Cart Count
$stmt = $conn->prepare("SELECT COUNT(*) AS cart_count FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$cart_count = $stmt->get_result()->fetch_assoc()['cart_count'];

// Recommendations
$stmt = $conn->prepare("
SELECT DISTINCT fc.product_id, fc.name AS product_name, fc.price, fc.image, u.name AS farmer_name
    FROM farmer_crops fc
    INNER JOIN farmer f ON fc.farmer_id = f.farmer_id
    INNER JOIN users u ON f.farmer_id = u.user_id
    LEFT JOIN orders o ON fc.product_id = o.product_id AND o.customer_id = ?
    LEFT JOIN product_reviews pr ON fc.product_id = pr.product_id AND pr.customer_id = ?
    WHERE fc.quantity > 0
    AND fc.product_id NOT IN (
        SELECT product_id FROM orders WHERE customer_id = ?
    )
    AND fc.product_id NOT IN (
        SELECT product_id FROM product_reviews WHERE customer_id = ?
    )
    ORDER BY RAND()
    LIMIT 5
");
$stmt->bind_param("iiii", $customer_id, $customer_id, $customer_id, $customer_id);
$stmt->execute();
$recommended_products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Pending Reviews
$stmt = $conn->prepare("
    SELECT DISTINCT o.product_id, fc.name AS product_name, u.name AS farmer_name
    FROM orders o
    INNER JOIN farmer_crops fc ON o.product_id = fc.product_id
    JOIN users u ON fc.farmer_id = u.user_id
    INNER JOIN farmer f ON fc.farmer_id = f.farmer_id
    LEFT JOIN product_reviews pr ON o.product_id = pr.product_id AND o.customer_id = pr.customer_id
    WHERE o.customer_id = ? AND o.status = 'Delivered' AND pr.id IS NULL
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$pending_reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - SmartKirshi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Base Styles */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f9fc;
            color: #333;
        }

        /* Header Styling */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #28a745;
            color: #ffffff;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Header Right Section */
        header .header-right {
            position: absolute;
            right: 20px;
            display: flex;
            align-items: center;
        }

        .header-right .customer-name {
            margin-right: 15px;
            font-size: 1rem;
        }

        /* Logout Button */
        .logout-btn {
            background-color: #dc3545;
            color: #ffffff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        /* Sidebar Navigation */
        .sidebar {
            position: fixed;
            top: 80px;
            left: 0;
            width: 70px;
            height: calc(100% - 80px);
            background-color: #28a745;
            border-right: 1px solid #dddddd;
            transition: width 0.3s ease;
            overflow-x: hidden;
        }

        .sidebar:hover {
            width: 220px;
        }

        /* Sidebar List */
        .sidebar ul {
            padding: 10px 0;
            list-style: none;
            margin: 0;
        }

        .sidebar ul li {
            display: block;
        }

        /* Sidebar Links */
        .sidebar .nav-link {
            color: #ffffff;
            font-size: 16px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav-link i {
            font-size: 24px;
            font-weight: bold;
            margin-right: 30px;
        }

        .sidebar .nav-link:hover {
            background-color: #e9f5ee;
            color: #28a745;
        }

        /* Language Switcher */
        .language-switcher {
            padding: 10px;
            border-bottom: 1px solid #dddddd;
            text-align: center;
        }

        .language-switcher button {
            margin: 2px;
            padding: 5px 10px;
            font-size: 12px;
            border: none;
            background-color: #d20103;
            color: #ffffff;
            border-radius: 4px;
            cursor: pointer;
        }

        .language-switcher button:hover {
            background-color: #ff0b0d;
        }

        /* Page Content */
        .content {
            margin-left: 80px;
            padding: 100px 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar:hover ~ .content {
            margin-left: 230px;
        }

        /* Typography */
        h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #007bff;
        }

        /* Section Container */
        .section {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            border: 1px solid #dddddd;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        /* Product Card */
        .product-card {
            border: 1px solid #cccccc;
            padding: 10px;
            border-radius: 5px;
            background-color: #fdfdfd;
            text-align: center;
        }

        .product-card h4 {
            font-size: 1rem;
            margin: 8px 0;
        }

        .product-card p {
            margin: 0;
            font-size: 0.9rem;
            color: #555555;
        }

        /* View Button */
        .btn-view {
            margin-top: 10px;
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .btn-view:hover {
            background-color: #0056b3;
        }

        /* Pending List */
        ul.pending-list {
            padding-left: 0;
            list-style: none;
            margin: 0;
        }

        ul.pending-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #eeeeee;
        }

        ul.pending-list li:last-child {
            border-bottom: none;
        }

        ul.pending-list p {
            margin: 0;
            font-size: 0.9rem;
        }

    </style>
</head>
<body>

<header>
    <h1 id="title">গ্রাহক ড্যাশবোর্ড - স্মার্টকৃষি</h1>
    <div class="header-right">
        <span class="customer-name" id="welcome">আপনাকে স্বাগতম</span>
        <a href="logout.php" class="logout-btn">লগআউট</a>
    </div>
</header>

<div class="sidebar">
    <div class="language-switcher">
        <button onclick="setLanguage('bn')">BN</button>
        <button onclick="setLanguage('en')">EN</button>
    </div>
    <ul>
        <li><a href="customer.php" class="nav-link"><i class="fas fa-home"></i> <span data-key="dashboard">ড্যাশবোর্ড</span></a></li>
        <li><a href="C_market.php" class="nav-link"><i class="fas fa-store"></i> <span data-key="market">বাজার</span></a></li>
        <li><a href="C_review.php" class="nav-link"><i class="fas fa-star"></i> <span data-key="review">পর্যালোচনা</span></a></li>
        <li><a href="C_top_selling_products.php" class="nav-link"><i class="fas fa-chart-line"></i> <span data-key="top_selling">সর্বাধিক বিক্রিত</span></a></li>
        <li><a href="C_order_history.php" class="nav-link"><i class="fas fa-history"></i> <span data-key="order_history">অর্ডার ইতিহাস</span></a></li>
        <li><a href="C_purchase_history.php" class="nav-link"><i class="fas fa-shopping-cart"></i> <span data-key="purchase_history">ক্রয় ইতিহাস</span></a></li>
        <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span data-key="logout">লগআউট</span></a></li>
    </ul>
</div>

<div class="content">
    <!-- Cart Section -->
    <div class="section">
        <h3 data-key="cart">আপনার কার্ট</h3>
        <p>
            <span data-key="cart_items">বর্তমানে আপনার কার্টে</span>
            <strong><?= $cart_count; ?></strong>
            <span data-key="items">টি পণ্য যুক্ত রয়েছে।</span>
        </p>
        <a href="C_market.php?action=view_cart" class="btn-view" data-key="view_cart">কার্ট প্রদর্শন করুন</a>
    </div>

    <!-- Recommended Products Section -->
    <div class="section">
        <h3 data-key="recommend">আপনার জন্য প্রস্তাবিত পণ্যসমূহ</h3>
        <div class="product-grid">
            <?php if (empty($recommended_products)): ?>
                <p data-key="no_recommend">দুঃখিত, বর্তমানে কোন প্রস্তাবিত পণ্য উপলব্ধ নেই। অনুগ্রহ করে অন্বেষণ চালিয়ে যান।</p>
            <?php else: ?>
                <?php foreach ($recommended_products as $product): ?>
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product['image'] ?? 'images/placeholder.jpg'); ?>" alt="Product Image" style="width:100%; height:130px; object-fit:cover; border-radius:5px;">
                        <h4><?= htmlspecialchars($product['product_name']); ?></h4>
                        <p><span data-key="by">প্রদানকারী:</span> <?= htmlspecialchars($product['farmer_name']); ?></p>
                        <p>মূল্য: ৳<?= htmlspecialchars($product['price']); ?></p>
                        <a href="C_market.php?product_id=<?= $product['product_id'] ?>" class="btn-view" data-key="view_product">বিস্তারিত দেখুন</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Reviews Section -->
    <div class="section">
        <h3 data-key="pending_review">অপেক্ষমাণ পর্যালোচনাসমূহ</h3>
        <?php if (empty($pending_reviews)): ?>
            <p data-key="no_review">বর্তমানে আপনার জন্য কোন পর্যালোচনা বাকি নেই। কেনাকাটা উপভোগ করুন!</p>
        <?php else: ?>
            <ul class="pending-list">
                <?php foreach ($pending_reviews as $review): ?>
                    <li>
                        <p>
                            <strong><?= htmlspecialchars($review['product_name']); ?></strong>
                            <span data-key="by">প্রদানকারী:</span> <?= htmlspecialchars($review['farmer_name']); ?>
                        </p>
                        <a href="C_review.php?product_id=<?= $review['product_id']; ?>" class="btn-view" data-key="write_review">পর্যালোচনা প্রদান করুন</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>


<script>
    const translations = {
        bn: {
            dashboard: "ড্যাশবোর্ড",
            market: "বাজার",
            review: "পর্যালোচনা",
            top_selling: "সর্বাধিক বিক্রিত",
            order_history: "অর্ডার ইতিহাস",
            purchase_history: "ক্রয় ইতিহাস",
            logout: "লগআউট",
            welcome: "আপনাকে স্বাগতম",
            cart: "আপনার কার্ট",
            cart_items: "আপনার কার্টে",
            items: "টি আইটেম আছে।",
            view_cart: "কার্ট দেখুন",
            recommend: "আপনার পছন্দ হতে পারে এমন পণ্য",
            no_recommend: "কোনও সুপারিশ নেই। অন্বেষণ চালিয়ে যান!",
            by: "By:",
            view_product: "পণ্য দেখুন",
            pending_review: "মুলতুবি পর্যালোচনা",
            no_review: "কোনও পর্যালোচনা বাকি নেই। কেনাকাটা চালিয়ে যান!",
            write_review: "পর্যালোচনা লিখুন"
        },
        en: {
            dashboard: "Dashboard",
            market: "Market",
            review: "Reviews",
            top_selling: "Top Selling",
            order_history: "Order History",
            purchase_history: "Purchase History",
            logout: "Logout",
            welcome: "Welcome",
            cart: "Your Cart",
            cart_items: "You have",
            items: "items in your cart.",
            view_cart: "View Cart",
            recommend: "Recommended Products",
            no_recommend: "No recommendations. Keep exploring!",
            by: "By:",
            view_product: "View Product",
            pending_review: "Pending Reviews",
            no_review: "No reviews pending. Keep shopping!",
            write_review: "Write a Review"
        }
    };

    function setLanguage(lang) {
        document.querySelectorAll("[data-key]").forEach(el => {
            const key = el.getAttribute("data-key");
            el.textContent = translations[lang][key];
        });
        document.getElementById("title").textContent = lang === "bn" ? "গ্রাহক ড্যাশবোর্ড - স্মার্টকৃষি" : "Customer Dashboard - SmartKirshi";
        document.getElementById("welcome").textContent = translations[lang].welcome;
    }

    // Default Bangla
    setLanguage('bn');
</script>
</body>
</html>
