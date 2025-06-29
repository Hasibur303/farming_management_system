<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $paymentMethod = $_POST['payment_method'] ?? 'cod';

    try {
        // Start transaction
        $conn->begin_transaction();

        // 1. Get cart items
        $stmt = $conn->prepare("
            SELECT c.*, fc.name as crop_name, fc.price, u.name as customer_name
            FROM cart c
            JOIN farmer_crops fc ON c.product_id = fc.product_id AND c.farmer_id = fc.farmer_id
            JOIN users u ON c.user_id = u.user_id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $cartItems = $stmt->get_result();

        if ($cartItems->num_rows === 0) {
            throw new Exception("Your cart is empty");
        }

        // 2. Create payment record
        $paymentStmt = $conn->prepare("
            INSERT INTO payments (user_id, amount, method, status, payment_date)
            VALUES (?, ?, ?, 'pending', NOW())
        ");

        // Calculate total amount
        $totalAmount = 0;
        $items = $cartItems->fetch_all(MYSQLI_ASSOC);
        foreach ($items as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $paymentStmt->bind_param("ids", $userId, $totalAmount, $paymentMethod);
        $paymentStmt->execute();
        $paymentId = $conn->insert_id;

        // 3. Create orders
        $orderStmt = $conn->prepare("
            INSERT INTO orders (
                farmer_id, customer_id, product_id, customer_name,
                crop_name, quantity, total_amount, status, order_date, payment_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?)
        ");

        foreach ($items as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $orderStmt->bind_param(
                "iiissidi",
                $item['farmer_id'],
                $userId,
                $item['product_id'],
                $item['customer_name'],
                $item['crop_name'],
                $item['quantity'],
                $itemTotal,
                $paymentId
            );
            $orderStmt->execute();
        }

        // 4. Clear cart
        $clearCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearCart->bind_param("i", $userId);
        $clearCart->execute();

        // Commit transaction
        $conn->commit();

        // Redirect to payment gateway if not COD
        if ($paymentMethod !== 'cod') {
            header("Location: {$paymentMethod}_payment.php?payment_id={$paymentId}");
            exit();
        }

        $_SESSION['message'] = "Order placed successfully!";
        header("Location: order_confirmation.php?payment_id={$paymentId}");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: C_market.php");
        exit();
    }
}

header("Location: C_market.php");
exit();