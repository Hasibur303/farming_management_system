<?php
session_start();
include 'database.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Language handling
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
        'title' => 'অর্ডার নিশ্চিতকরণ',
        'thank_you' => 'ধন্যবাদ! আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে',
        'order_id' => 'অর্ডার আইডি',
        'date' => 'তারিখ',
        'payment_method' => 'পেমেন্ট পদ্ধতি',
        'total_amount' => 'মোট পরিমাণ',
        'items' => 'আইটেমসমূহ',
        'product' => 'পণ্য',
        'quantity' => 'পরিমাণ',
        'price' => 'দাম',
        'total' => 'মোট',
        'continue_shopping' => 'শপিং চালিয়ে যান'
    ],
    'en' => [
        'title' => 'Order Confirmation',
        'thank_you' => 'Thank you! Your order has been placed successfully',
        'order_id' => 'Order ID',
        'date' => 'Date',
        'payment_method' => 'Payment Method',
        'total_amount' => 'Total Amount',
        'items' => 'Items',
        'product' => 'Product',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'total' => 'Total',
        'continue_shopping' => 'Continue Shopping'
    ]
];
$current = $text[$lang];

// Get payment ID from URL
$paymentId = $_GET['payment_id'] ?? 0;
$userId = $_SESSION['user_id'];

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, p.method as payment_method, p.amount as total_amount
    FROM orders o
    JOIN payments p ON o.payment_id = p.payment_id
    WHERE o.customer_id = ? AND o.payment_id = ?
    ORDER BY o.order_date DESC
");
$stmt->bind_param("ii", $userId, $paymentId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($orders)) {
    $_SESSION['error'] = ($lang === 'bn' ? 'অর্ডার পাওয়া যায়নি' : 'Order not found');
    header("Location: C_market.php");
    exit();
}

// Get first order for summary info
$firstOrder = $orders[0];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $current['title'] ?> | SmartKrishi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .confirmation-header {
            color: #28a745;
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-items th {
            background: #28a745;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .order-items td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .order-items tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total-row {
            font-weight: bold;
            background: #e9ecef !important;
        }
        .btn-continue {
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn-continue:hover {
            background: #218838;
            color: white;
        }
        @media (max-width: 768px) {
            .confirmation-container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-container">
            <div class="confirmation-header">
                <h2><?= $current['title'] ?></h2>
                <div class="alert alert-success">
                    <?= $current['thank_you'] ?>
                </div>
            </div>

            <div class="order-summary">
                <h4><?= $current['title'] ?></h4>
                <p><strong><?= $current['order_id'] ?>:</strong> #<?= $firstOrder['order_id'] ?></p>
                <p><strong><?= $current['date'] ?>:</strong> <?= date('F j, Y, g:i a', strtotime($firstOrder['order_date'])) ?></p>
                <p><strong><?= $current['payment_method'] ?>:</strong> <?= strtoupper($firstOrder['payment_method']) ?></p>
                <p><strong><?= $current['total_amount'] ?>:</strong> ৳<?= number_format($firstOrder['total_amount'], 2) ?></p>
            </div>

            <div class="order-items">
                <h4><?= $current['items'] ?></h4>
                <table>
                    <thead>
                        <tr>
                            <th><?= $current['product'] ?></th>
                            <th><?= $current['quantity'] ?></th>
                            <th><?= $current['price'] ?></th>
                            <th><?= $current['total'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['crop_name']) ?></td>
                            <td><?= $order['quantity'] ?></td>
                            <td>৳<?= number_format($order['total_amount'] / $order['quantity'], 2) ?></td>
                            <td>৳<?= number_format($order['total_amount'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;"><strong><?= $current['total_amount'] ?>:</strong></td>
                            <td><strong>৳<?= number_format($firstOrder['total_amount'], 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="C_market.php" class="btn btn-continue">
                    <?= $current['continue_shopping'] ?>
                </a>
            </div>
        </div>
    </div>
</body>
</html>