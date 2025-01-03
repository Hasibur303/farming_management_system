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

// Handle adding new supply
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supply'])) {
    $supply_name = $_POST['supply_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    try {
        $sql = "INSERT INTO supplies (supplier_id, supply_name, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isid", $_SESSION['user_id'], $supply_name, $quantity, $price);

        if ($stmt->execute()) {
            $success_message = "Supply added successfully!";
        } else {
            $error = "Failed to add supply.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch supplies for the logged-in supplier
$supplier_id = $_SESSION['user_id'];
try {
    $sql = "SELECT * FROM supplies WHERE supplier_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $supplies_result = $stmt->get_result();
} catch (Exception $e) {
    $error = "Error fetching supplies: " . $e->getMessage();
}

// Fetch orders related to this supplier
try {
    $sql = "SELECT o.*, p.name AS product_name FROM orders o 
            JOIN supplier_products sp ON o.product_id = sp.product_id
            JOIN products p ON p.id = sp.product_id
            WHERE sp.supplier_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $orders_result = $stmt->get_result();
} catch (Exception $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard - AgriBuzz</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f7f8fc;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: #5cb85c;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border-bottom: 2px solid #4cae4c;
        }
        header h1 {
            margin: 0;
        }
        header a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin-left: 15px;
        }
        .form-container, .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            padding: 20px;
        }
        .form-container h2, .table-container h2 {
            margin-top: 0;
            font-size: 20px;
            color: #5cb85c;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
        }
        form input[type="text"], form input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form input[type="submit"] {
            background: #5cb85c;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }
        form input[type="submit"]:hover {
            background: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table thead th {
            background: #5cb85c;
            color: white;
            text-align: left;
            padding: 10px;
        }
        table tbody td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Supplier Dashboard - AgriBuzz</h1>
        <a href="logout.php">Logout</a>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>Add New Supply</h2>
            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success"><?= htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <form method="POST" action="supplier.php">
                <label for="supply_name">Supply Name:</label>
                <input type="text" name="supply_name" required>

                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" required>

                <label for="price">Price:</label>
                <input type="text" name="price" required>

                <input type="submit" name="add_supply" value="Add Supply">
            </form>
        </div>

        <div class="table-container">
            <h2>Your Supplies</h2>
            <table>
                <thead>
                    <tr>
                        <th>Supply ID</th>
                        <th>Supply Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($supply = $supplies_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($supply['supply_id']); ?></td>
                            <td><?= htmlspecialchars($supply['supply_name']); ?></td>
                            <td><?= htmlspecialchars($supply['quantity']); ?></td>
                            <td><?= htmlspecialchars($supply['price']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h2>Your Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Customer ID</th>
                        <th>Quantity</th>
                        <th>Order Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_id']); ?></td>
                            <td><?= htmlspecialchars($order['product_name']); ?></td>
                            <td><?= htmlspecialchars($order['customer_id']); ?></td>
                            <td><?= htmlspecialchars($order['quantity']); ?></td>
                            <td><?= htmlspecialchars($order['order_date']); ?></td>
                            <td><?= htmlspecialchars($order['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
