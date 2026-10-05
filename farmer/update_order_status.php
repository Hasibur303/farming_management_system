<?php
include('../database.php');
require_once dirname(__DIR__) . '/security/bootstrap.php';

// Check if user is logged in
require_role('Farmer');

$farmer_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $new_status = (string) $_POST['status'];
    $allowedStatuses = ['pending', 'Processing', 'Completed', 'Cancelled'];
    if (!in_array($new_status, $allowedStatuses, true)) {
        security_log('order.invalid_status', ['order_id' => $order_id]);
        http_response_code(422);
        exit('Invalid order status.');
    }
    
// First, get the current status of the order
$current_status_query = "SELECT status FROM orders WHERE order_id = ? AND farmer_id = ?";
$stmt = $conn->prepare($current_status_query);
$stmt->bind_param("ii", $order_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
if (!$order) {
    security_log('authorization.ownership_denied', ['resource' => 'order', 'resource_id' => $order_id]);
    http_response_code(403);
    exit('You do not own this order.');
}
$current_status = $order['status'];


// Check if status is changing from pending to processing
if ($current_status === 'pending' && $new_status === 'Processing') {
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get order items
        $items_query = "SELECT product_id, farmer_id, quantity FROM orders WHERE order_id = ? AND farmer_id = ?";
        $stmt = $conn->prepare($items_query);
        $stmt->bind_param("ii", $order_id, $farmer_id);
        $stmt->execute();
        $items_result = $stmt->get_result();
        
        // Update quantity for each product in the order
        while ($item = $items_result->fetch_assoc()) {
            $update_products = "UPDATE farmer_crops SET quantity = quantity - ? WHERE product_id = ? AND farmer_id=?"; 
                               
            $stmt = $conn->prepare($update_products);
            $stmt->bind_param("iii", $item['quantity'], $item['product_id'],$item['farmer_id']);
            $stmt->execute();
            
            // Check if update was successful
            if ($stmt->affected_rows <= 0) {
                throw new Exception("Failed to update products quentity for product ID: " . $item['product_id']);
            }
        }



    // Update order status
    $update_query = "UPDATE orders SET status = ? WHERE order_id = ? AND farmer_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sii", $new_status, $order_id, $farmer_id);
    
    $stmt->execute();


         
            // Commit transaction
            $conn->commit();
            security_log('market.order_status_changed', ['resource_id' => $order_id, 'old_status' => $current_status, 'new_status' => $new_status], 'info');
            
            // Redirect or show success message
            header("Location: order_management.php?success=1");
            exit();
        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            $conn->rollback();
            $error_message = "Error updating order: " . $e->getMessage();
            // Handle the error (redirect with error message or display it)
        }


}else {
    // For other status changes, just update the status without affecting inventory
    $update_query = "UPDATE orders SET status = ? WHERE order_id = ? AND farmer_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sii", $new_status, $order_id, $farmer_id);
    $stmt->execute();
    
    // Redirect or show success message
    header("Location: update_order_status.php?success=1");
    if ($current_status !== $new_status) {
        security_log('market.order_status_changed', ['resource_id' => $order_id, 'old_status' => $current_status, 'new_status' => $new_status], 'info');
    }
    exit();
}

}






// Fetch order details
if (!isset($_GET['id'])) {
    header('Location: order_management.php');
    exit();
}

$order_id = $_GET['id'];
$query = "SELECT * FROM orders WHERE order_id = ? AND farmer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: order_management.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার স্ট্যাটাস আপডেট করুন</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">অর্ডার স্ট্যাটাস আপডেট করুন</h1>
        <div class="card">
            <div class="card-body">
            <a href="order_management.php" class="btn btn-outline-dark mb-3">
                ← অর্ডার ম্যানেজমেন্ট-এ ফিরে যান
            </a>

                <h5 class="card-title">Order #<?= e($order['order_id']) ?></h5>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?= e($order['order_id']) ?>">
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">অর্ডার স্ট্যাটাস</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending(মুলতুবি)</option>
                            <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing(প্রক্রিয়াকরণ)</option>
                            <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped(পাঠানো হয়েছে)</option>
                            <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered(বিতরণ করা হয়েছে)</option>
                            <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled(বাতিল করা হয়েছে)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">স্ট্যাটাস আপডেট করুন</button>
                    <a href="order_management.php?id=<?= e($order['order_id']) ?>" class="btn btn-secondary">বাতিল করুন</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
