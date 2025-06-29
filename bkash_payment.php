<?php
session_start();
include 'database.php';

// Language handling
if (!isset($_SESSION['lang'])) $_SESSION['lang'] = 'bn';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['bn', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'];

// Text translations
$text = [
    'bn' => [
        'title' => 'বিকাশ পেমেন্ট',
        'number' => 'বিকাশ নম্বর',
        'trxid' => 'লেনদেন আইডি',
        'amount' => 'টাকার পরিমাণ',
        'submit' => 'জমা দিন',
        'error' => 'ত্রুটি',
        'success' => 'সফল',
        'invalid_payment' => 'অবৈধ পেমেন্ট',
        'invalid_number' => 'অবৈধ বিকাশ নম্বর',
        'invalid_trx' => 'অবৈধ লেনদেন আইডি'
    ],
    'en' => [
        'title' => 'bKash Payment',
        'number' => 'bKash Number',
        'trxid' => 'Transaction ID',
        'amount' => 'Amount (BDT)',
        'submit' => 'Submit',
        'error' => 'Error',
        'success' => 'Success',
        'invalid_payment' => 'Invalid payment',
        'invalid_number' => 'Invalid bKash number',
        'invalid_trx' => 'Invalid transaction ID'
    ]
];
$current = $text[$lang];

// Verify payment belongs to user
if (!isset($_GET['payment_id']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$paymentId = $_GET['payment_id'];
$userId = $_SESSION['user_id'];

// Get payment details
$stmt = $conn->prepare("SELECT * FROM payments WHERE payment_id = ? AND user_id = ?");
$stmt->bind_param("ii", $paymentId, $userId);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

if (!$payment) {
    $_SESSION['error'] = $current['invalid_payment'];
    header("Location: C_market.php");
    exit();
}

// Process payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bkashNumber = $_POST['bkash_number'];
    $trxId = $_POST['trx_id'];
    $amount = (float)$_POST['amount'];

    // Validate inputs
    $errors = [];

    if (!preg_match('/^01[3-9]\d{8}$/', $bkashNumber)) {
        $errors[] = $current['invalid_number'];
    }

    if (empty($trxId) || strlen($trxId) < 10) {
        $errors[] = $current['invalid_trx'];
    }

    if (abs($amount - $payment['amount']) > 1) { // Allow small rounding differences
        $errors[] = "Amount doesn't match order total";
    }

    if (empty($errors)) {
        // Mark payment as completed
        $update = $conn->prepare("
            UPDATE payments SET
                status = 'completed',
                transaction_id = ?,
                account_number = ?,
                verification_date = NOW()
            WHERE payment_id = ?
        ");
        $update->bind_param("ssi", $trxId, $bkashNumber, $paymentId);

        if ($update->execute()) {
            // Update order status
            $conn->query("UPDATE orders SET status = 'processing' WHERE payment_id = $paymentId");

            $_SESSION['message'] = $current['success'];
            header("Location: order_confirmation.php?payment_id=$paymentId");
            exit();
        } else {
            $errors[] = "Database error";
        }
    }

    if (!empty($errors)) {
        $errorMessage = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $current['title'] ?> | SmartKrishi</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fdf2f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 60px 20px;
            margin: 0;
        }
        h1 {
            color: #e2136e;
            margin-bottom: 20px;
        }
        form {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
            border-top: 8px solid #e2136e;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: 0.3s;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="number"]:focus {
            border-color: #e2136e;
            outline: none;
            box-shadow: 0 0 0 2px rgba(226,19,110,0.2);
        }
        button {
            padding: 14px;
            width: 100%;
            background: #e2136e;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background: #c51162;
        }
        .back {
            margin-top: 20px;
            text-align: center;
        }
        .back a {
            text-decoration: none;
            color: #e2136e;
            font-weight: bold;
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .back a:hover {
            background: rgba(226,19,110,0.1);
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            height: 60px;
        }
        .error {
            color: #d32f2f;
            background: #ffebee;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #d32f2f;
        }
        .payment-info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .payment-info p {
            margin: 5px 0;
            color: #333;
        }
        .payment-info .amount {
            font-weight: bold;
            color: #e2136e;
            font-size: 18px;
        }
    </style>
</head>
<body>
 <h1><?= $current['title'] ?></h1>
    <div class="logo">
        <img src="bkash.jpeg" alt="bKash Logo">
    </div>


    <?php if (isset($errorMessage)): ?>
        <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <div class="payment-info">
        <p><?= ($lang === 'bn' ? 'পরিশোধের পরিমাণ:' : 'Payment Amount:') ?></p>
        <p class="amount">৳<?= number_format($payment['amount'], 2) ?></p>
        <p><?= ($lang === 'bn' ? 'পেমেন্ট আইডি:' : 'Payment ID:') ?> <?= $paymentId ?></p>
    </div>

    <form method="POST" action="">
        <div class="form-group">
            <label><?= $current['number'] ?>:</label>
            <input type="text" name="bkash_number" placeholder="<?= ($lang === 'bn' ? '০১XXXXXXXXX' : '01XXXXXXXXX') ?>" required>
        </div>
        <div class="form-group">
            <label><?= $current['trxid'] ?>:</label>
            <input type="text" name="trx_id" placeholder="<?= ($lang === 'bn' ? 'TRX1234567890' : 'TRX1234567890') ?>" required>
        </div>
        <div class="form-group">
            <label><?= $current['amount'] ?>:</label>
            <input type="number" name="amount" value="<?= number_format($payment['amount'], 2) ?>" step="0.01" required>
        </div>
        <button type="submit"><?= $current['submit'] ?></button>
    </form>

    <div class="back">
        <a href="C_market.php"><?= ($lang === 'bn' ? 'বাজারে ফিরে যান' : 'Back to Market') ?></a>
    </div>

    <script>
        // Auto-format bKash number
        document.querySelector('input[name="bkash_number"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^\d]/g, '').substring(0, 11);
        });

        // Validate before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const bkashNumber = document.querySelector('input[name="bkash_number"]').value;
            if (!/^01[3-9]\d{8}$/.test(bkashNumber)) {
                alert("<?= $current['invalid_number'] ?>");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>