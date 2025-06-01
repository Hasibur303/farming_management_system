<?php
session_start();
include('database.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    try {
        // Debug: Print received data
        error_log("Received POST data: " . print_r($_POST, true));
        
        // Validate inputs
        if (empty($_POST['product_id']) || empty($_POST['name']) || 
        empty($_POST['description']) || empty($_POST['price']) || 
        empty($_POST['quantity']) || empty($_POST['quantity_type'])) {
        throw new Exception("All fields are required");
    }

    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $farmer_id = $_SESSION['user_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $quantity_type = $_POST['quantity_type'];
    $status = 'available';
        // Handle file upload if present
        $image_path = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            }
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO farmer_crops 
        (farmer_id, product_id, name, description, price, quantity, quantity_type, image, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("iissdisss", $farmer_id, $product_id, $name, 
        $description, $price, $quantity, $quantity_type, $image_path, $status);
    
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Return success response for AJAX
        echo json_encode(['status' => 'success', 'message' => 'Product added successfully']);
        exit;

    } catch (Exception $e) {
        error_log("Error adding product: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: #f9f9f9;
            font-family: 'Roboto', sans-serif;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #1f2937;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .sidebar a {
            color: #b0bec5;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            margin-bottom: 10px;
            font-weight: 500;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar a:hover {
            background-color: #4b5563;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            background: linear-gradient(45deg, #3b8d99, #6b6b83);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .btn-primary {
            background: #3b8d99;
            border: none;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #6b6b83;
        }

        .table {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(45deg, #3b8d99, #6b6b83);
            color: white;
        }

        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f1f1f1;
            cursor: pointer;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
            color: #777;
        }
    </style>

</head>
<body>

<div class="sidebar">
        <h2>ন্যাভিগেশন</h2>
        <a href="farmer.php">
                    <i class="fas fa-wallet icon"></i>
                    <span class="text">ড্যাশবোর্ড</span>
                </a>
        <a href="crop_management.php">
            <i class="fas fa-seedling icon"></i>
            <span class="text">ফসল/পণ্য ব্যবস্থাপনা</span>
        </a>
        <a href="Buy.php">
            <i class="fas fa-shopping-cart icon"></i>
            <span class="text">সরবরাহকারীদের কাছ থেকে কিনুন</span>
        </a>
        <a href="labour_jobs.php">
            <i class="fas fa-briefcase icon"></i>
            <span class="text">শ্রমিকের চাকরির পোস্ট</span>
        </a>

        <a href="rent_page.php">
            <i class="fas fa-shopping-cart icon"></i>
            <span class="text">ভাড়ার পরিষেবা</span>
        </a>
        <a href="addNewProduct.php">
            <i class="fas fa-plus-circle icon"></i>
            <span class="text">নতুন পণ্য যোগ করুন</span>
        </a>
        <a href="farmer/order_management.php">
            <i class="fas fa-clipboard-list icon"></i>
            <span class="text">অর্ডার ম্যানেজমেন্ট</span>
        </a>
        <a href="farmer/inventory_management.php">
            <i class="fas fa-boxes icon"></i>
            <span class="text">ইনভেন্টরি ম্যানেজমেন্ট</span>
        </a>
        <a href="farmer/financial_overview.php">
            <i class="fas fa-wallet icon"></i>
            <span class="text">আর্থিক সারসংক্ষেপ</span>
        </a>
        <a href="analytics_report.php">
            <i class="fas fa-chart-bar icon"></i>
            <span class="text">বিশ্লেষণ এবং প্রতিবেদন</span>
        </a>
    </div>



<div class="container py-4">
    <div class="header">
        <h1><i class="fas fa-seedling"></i> ফসল ব্যবস্থাপনা</h1>
        <p>আপনার ফসল এবং মজুদ সহজেই পরিচালনা করুন</p>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            পণ্যটি সফলভাবে যোগ করা হয়েছে!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>




    <!-- Search Box -->
    <div class="mb-4">
        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="input-group">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="ফসল অনুসন্ধান করুন..."
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> অনুসন্ধান করুন
                </button>
            </div>
        </form>
    </div>

<h3 class="mt-5 mb-4 text-success text-center">পণ্যের তালিকা</h3>

<div class="container">
    <div class="row">
        <?php
        try {
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = '%' . $_GET['search'] . '%';
                $stmt = $conn->prepare("SELECT id, name, image, quantity_type FROM products WHERE name LIKE ?");
                $stmt->bind_param("s", $search);
            } else {
                $stmt = $conn->prepare("SELECT id, name, image, quantity_type FROM products");
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($product = $result->fetch_assoc()) {
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 shadow border-0">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= htmlspecialchars($product['image']); ?>"
                                     class="card-img-top"
                                     alt="Image of <?= htmlspecialchars($product['name']); ?>"
                                     style="height: 180px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body text-center">
                                <h5 class="card-title text-primary"><?= htmlspecialchars($product['name']); ?></h5>
                                <p class="text-muted mb-2">পরিমাণের ধরন: <?= htmlspecialchars($product['quantity_type']); ?></p>
                                <button type="button"
                                        class="btn btn-sm btn-success"
                                        onclick="selectCrop(
                                            '<?= $product['id']; ?>',
                                            '<?= htmlspecialchars($product['name']); ?>',
                                            '<?= htmlspecialchars($product['quantity_type']); ?>'
                                        )">
                                    নির্বাচন করুন
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12 text-center"><div class="alert alert-warning">কোনো পণ্য পাওয়া যায়নি।</div></div>';
            }

            $stmt->close();
        } catch (Exception $e) {
            echo '<div class="col-12 text-center text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</div>
<h3 class="mt-5 mb-4 text-success text-center">আমার ফসলের তালিকা</h3>

<div class="container">
    <div class="row">
        <?php
        // Fetch farmer's crops from farmer_crops table
        $farmer_id = $_SESSION['user_id'];
        $query = "SELECT fc.*, fc.product_id, fc.name, fc.image, fc.quantity, fc.quantity_type, fc.price
                  FROM farmer_crops fc
                  WHERE fc.farmer_id = ?
                  ORDER BY fc.product_id DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $farmer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow border-0">
                    <?php if (!empty($row['image'])): ?>
                        <img src="<?= htmlspecialchars($row['image']); ?>"
                             class="card-img-top"
                             alt="<?= htmlspecialchars($row['name']); ?>"
                             style="height: 180px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary"><?= htmlspecialchars($row['name']); ?></h5>
                        <p class="mb-1"><strong>পরিমাণ:</strong> <?= htmlspecialchars($row['quantity']) . ' ' . htmlspecialchars($row['quantity_type']); ?></p>
                        <p class="mb-2"><strong>দাম:</strong> <?= htmlspecialchars($row['price']); ?> টাকা</p>
                        <span class="badge bg-success">আইডি: <?= htmlspecialchars($row['product_id']); ?></span>
                    </div>
                </div>
            </div>
        <?php
            }
        } else {
            echo '<div class="col-12 text-center"><div class="alert alert-warning">কোনো ফসল যোগ করা হয়নি।</div></div>';
        }

        $stmt->close();
        ?>
    </div>
</div>



    
<!-- Premium Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(45deg, #198754, #0d6efd); border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <h5 class="modal-title fw-bold" id="addProductModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>পণ্য যোগ করুন
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body bg-light rounded-bottom-4 px-4 py-3">
                <form method="POST" enctype="multipart/form-data" id="addProductForm" class="needs-validation" novalidate>
                    <input type="hidden" id="product_id" name="product_id">
                    <input type="hidden" id="quantity_type" name="quantity_type">
                    <input type="hidden" name="add_product" value="1">

                    <!-- Selected Crop Display -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">নির্বাচিত ফসল:</label>
                        <div class="form-control bg-white border border-success shadow-sm px-3 py-2" id="selected-crop" style="font-weight: 500;">
                            কোনটিই নয়
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Product Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">পণ্যের নাম</label>
                            <input type="text" class="form-control shadow-sm" id="name" name="name" required>
                            <div class="invalid-feedback">দয়া করে একটি পণ্যের নাম দিন।</div>
                        </div>

                        <!-- Price -->
                        <div class="col-md-6">
                            <label for="price" class="form-label">মূল্য</label>
                            <div class="input-group shadow-sm">
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required min="0">
                                <span class="input-group-text quantity-type-label bg-success text-white"></span>
                            </div>
                            <div class="invalid-feedback">দয়া করে একটি বৈধ মূল্য প্রদান করুন।</div>
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">পরিমাণ</label>
                            <div class="input-group shadow-sm">
                                <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                                <span class="input-group-text quantity-type-label bg-success text-white"></span>
                            </div>
                            <div class="invalid-feedback">দয়া করে একটি বৈধ পরিমাণ প্রদান করুন।</div>
                        </div>

                        <!-- Photo -->
                        <div class="col-md-6">
                            <label for="photo" class="form-label">ছবি</label>
                            <input type="file" class="form-control shadow-sm" id="photo" name="photo">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="description" class="form-label">বিবরণ</label>
                            <textarea class="form-control shadow-sm" id="description" name="description" rows="3" required></textarea>
                            <div class="invalid-feedback">দয়া করে একটি বিবরণ দিন।</div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-0 mt-4 px-0">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> বন্ধ করুন
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-check-circle me-1"></i> পণ্য যোগ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Function to open add product modal
function openAddProductModal() {
    const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
    modal.show();
}


    // Function to handle crop selection
    function selectCrop(productId, cropName, quantityType) {
    console.log('selectCrop called with :', productId, cropName, quantityType);
    
    // Set the product ID and quantity type in the hidden inputs
    document.getElementById('product_id').value = productId;
    document.getElementById('quantity_type').value = quantityType;

    // Update the selected crop name display
    document.getElementById('selected-crop').textContent = cropName;

    // Set the name field with the selected crop name
    document.getElementById('name').value = cropName;

// Update quantity type labels
const quantityTypeLabels = document.getElementsByClassName('quantity-type-label');
const priceDisplayText = quantityType === 'Per-KG' ? '/kg' : '/piece';
const quantityDisplayText = quantityType === 'Per-KG' ? 'kg' : 'pieces';

   // Convert HTMLCollection to Array and update each label
Array.from(quantityTypeLabels).forEach((label, index) => {
    // First label is for price, second is for quantity
    if (index === 0) {
        label.textContent = priceDisplayText;
    } else {
        label.textContent = quantityDisplayText;
    }
});

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
    modal.show();
}


    // Form submission handling
    document.addEventListener('DOMContentLoaded', function() {
        const addProductForm = document.getElementById('addProductForm');
        
        if (addProductForm) {
            addProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }
                
                const formData = new FormData(this);
                
                // Show loading state
                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.innerHTML = 'Adding...';
                submitButton.disabled = true;

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;

                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                        modal.hide();
                        
                        // Reset form
                        addProductForm.reset();
                        document.getElementById('selected-crop').textContent = 'None';
                        
                        // Show success message
                        alert(data.message);
                        
                        // Refresh the page
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                    alert('An error occurred. Please try again.');
                });
            });
        }
    });

    // Debug logging
    console.log('Script loaded');
    console.log('Modal element:', document.getElementById('addProductModal'));
    console.log('Form element:', document.getElementById('addProductForm'));
</script>
</body>
</html>