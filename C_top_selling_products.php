<?php
session_start();
include 'database.php';


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
                header("Location: customer.php");
                exit();
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
        $_SESSION['message'] = "পণ্যটি সফলভাবে কার্টে যোগ করা হয়েছে!";
    } else {
        $_SESSION['error'] = "কার্টে পণ্য যোগ করার সময় সমস্যা হয়েছে।";
    }
}

      // Fetch cart items
      $cartStmt = $conn->prepare("
      SELECT c.*, fc.name, fc.price, fc.quantity_type, fc.image,c.farmer_id
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




    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user_id'];
        $action = $_POST['update_quantity'];
    
        if ($action === 'increase') {
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        } elseif ($action === 'decrease') {
            $stmt = $conn->prepare("UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE user_id = ? AND product_id = ?");
        }
    
        $stmt->bind_param("ii", $userId, $productId);
        if ($stmt->execute()) {
            $_SESSION['message'] = "কার্ট সফলভাবে আপডেট করা হয়েছে!";
            header("Location: " . $_SERVER['PHP_SELF']);
    exit();
        } else {
            $_SESSION['error'] = "কার্ট আপডেট করার সময় ত্রুটি।";
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
        farmer_crops.farmer_id,
        products.id AS product_id,
        products.name AS product_name,
        products.price AS product_price,
        products.image AS product_image,
        SUM(orders.quantity) AS total_sold
    FROM
        orders
    JOIN
        products ON orders.product_id = products.id
     join farmer_crops ON products.id = farmer_crops.product_id   
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
    <title>সর্বাধিক বিক্রিত পণ্য- স্মার্টকৃষি</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <link rel="stylesheet" type="text/css" href="./css/customer.css">

</head>
   
<body>


<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="customer.php" class="nav-link"><i class="fas fa-home"></i> ড্যাশবোর্ড</a></li>
        <li><a href="C_market.php" class="nav-link"><i class="fas fa-store"></i> বাজার</a></li>
        <li><a href="C_review.php" class="nav-link"><i class="fas fa-star"></i> পর্যালোচনা</a></li>
        <li><a href="C_top_selling_products.php" class="nav-link"><i class="fas fa-chart-line"></i> সর্বাধিক বিক্রিত</a></li>
        <li><a href="C_order_history.php" class="nav-link"><i class="fas fa-history"></i> অর্ডার ইতিহাস</a></li>
        <li><a href="C_purchase_history.php" class="nav-link"><i class="fas fa-shopping-cart"></i> ক্রয়ের ইতিহাস</a></li>
        <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> লগআউট</a></li>
    </ul>
</div>
<header>
        <h1>সর্বাধিক বিক্রিত পণ্য- স্মার্টকৃষি </h1>
        <a href="logout.php" class="button">লগআউট</a>
        <!-- Cart Icon -->
        <div class="cart-icon" onclick="toggleCart()">
            কার্ট <span class="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span>
        </div>




        <!-- Update the cart section with confirmation -->
        <div class="cart-sidebar" id="cartSidebar">
            <h2>বাজারের কার্ট</h2>
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
                        <input type="hidden" name="update_quantity" value="decrease">
                         <input type="hidden" name="update_quantity" value="decrease">
                         <input type="hidden" name="farmer_id" value="<?= $item['farmer_id'] ?>">
                        <button type="submit" class="btn-decrement">-</button>
                    </form>
                    <span><?= htmlspecialchars($item['quantity']) ?></span>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <input type="hidden" name="farmer_id" value="<?= $item['farmer_id'] ?>">
                        <input type="hidden" name="update_quantity" value="increase">
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
                    <form method="POST" class="place-order-form" onsubmit="return confirmOrder()">
                        <button type="submit" name="place_order" class="btn-primary">অর্ডার দিন</button>
                    </form>
                <?php else: ?>
                    <p>তোমার কার্ট খালি।</p>
                <?php endif; ?>
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
</header>

<div class="top-selling-products">
    <?php if (!empty($topSellingProducts)): ?>
        <?php foreach ($topSellingProducts as $product): ?>
            <div class="product-card-item">
                <img
                    src="<?= htmlspecialchars($product['product_image']); ?>"
                    alt="<?= htmlspecialchars($product['product_name']); ?>"
                    class="product-img"
                >
                <h4><?= htmlspecialchars($product['product_name']); ?></h4>
                <p>Price: TK. <?= htmlspecialchars($product['product_price']); ?></p>
                <p>Sold: <?= htmlspecialchars($product['total_sold']); ?></p>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                    <input type="hidden" name="farmer_id" value="<?= htmlspecialchars($product['farmer_id']); ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-button">কার্ট এ যোগ করুন</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>এই মুহূর্তে কোনও সর্বাধিক বিক্রিত পণ্য পাওয়া যাচ্ছে না।</p>
    <?php endif; ?>
</div>



<script>
function toggleCart() {
    document.getElementById('cartSidebar').classList.toggle('active');
}
function confirmOrder() {
    return confirm('আপনি কি নিশ্চিত যে আপনি এই অর্ডারটি দিতে চান?');
}
</script>
</body>
</html>
