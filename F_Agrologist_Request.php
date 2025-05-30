<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // assuming this is farmer id

// Handle request form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_text'])) {
    $farmer_name = $_SESSION['username']; // assuming username stored in session
    $request_text = mysqli_real_escape_string($conn, $_POST['request_text']);
    $query = "INSERT INTO farmer_requests (farmer_name, request_text)
              VALUES ('$farmer_name', '$request_text')";
    mysqli_query($conn, $query);
    header("Location: farmer.php");
    exit();
}

// Fetch previous requests
$result = mysqli_query($conn, "SELECT * FROM farmer_requests WHERE farmer_name = '{$_SESSION['username']}' ORDER BY request_date DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - SmartAgri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
 <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - SmartAgri</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
    /* Global Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: #f7f8fa;
        color: #333;
        padding-top: 70px; /* Add padding to prevent content from overlapping with the fixed header */
    }

/* Header Styles */
header {
    background-color: #4CAF50;
    color: white;
    padding: 1.2rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    border-bottom: 2px solid #388E3C; /* Elegant border at the bottom */
    transition: background-color 0.3s ease; /* Smooth transition */
    padding-left: 5rem; /* Ensure space for the user info on the right */
    padding-right: 5rem; /* Ensure space for the user info on the right */
    text-align: center; /* Center the title */
}

/* Header Title */
header h1 {
    font-size: 2rem; /* Larger font size for prominence */
    font-weight: 700;
    letter-spacing: 1px; /* Letter spacing for a more refined look */
    margin: 0;
    color: #ffffff;
    font-family: 'Roboto', sans-serif; /* Modern font */
    flex-grow: 1; /* This will ensure the title takes the remaining space */
}

/* User Info Container */
.user-info {
    display: flex;
    align-items: center;
    font-family: 'Roboto', sans-serif;
    font-weight: 500;
    color: white;
}

/* User Info Text */
.user-info span {
    font-size: 1rem;
    margin-right: 15px;
    letter-spacing: 0.5px;
}

/* Logout Button Styles */
.user-info a {
    background-color: #d32f2f; /* Red button */
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 600;
    transition: background-color 0.3s ease, transform 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
}

.user-info a:hover {
    background-color: #c62828; /* Darker red on hover */
    transform: translateY(-3px); /* Subtle lift effect */
}

/* Responsive Enhancements */
@media (max-width: 768px) {
    header {
        padding: 1rem;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    header h1 {
        font-size: 1.6rem;
        margin-bottom: 10px;
    }

    .user-info {
        margin-top: 10px;
    }

    .user-info a {
        margin-top: 10px;
    }
}


        /* Sidebar Styles */
        .sidebar {
            width: 60px; /* Initially narrow */
            background-color: #1f2937;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 10px;
            transition: width 0.3s ease; /* Smooth expansion */
            overflow: hidden; /* Hide content when collapsed */
            z-index: 999; /* Ensure it stays above content */
        }

        .sidebar:hover {
            width: 250px; /* Full width on hover */
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 600;
            transition: opacity 0.3s ease;
        }

        /* Links inside sidebar */
        .sidebar a {
            color: #b0bec5;
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            margin-bottom: 10px;
            font-weight: 500;
            transition: background 0.3s, padding-left 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #4b5563;
            color: white;
            padding-left: 20px; /* Add space on hover for extra elegance */
        }

        .sidebar a .icon {
            width: 30px;
            text-align: center;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }

        .sidebar a:hover .icon {
            transform: translateX(5px); /* Slide effect for icons */
        }

        .sidebar a .text {
            display: none; /* Hide text initially */
            font-size: 1rem;
            transition: opacity 0.3s ease;
        }

        .sidebar:hover a .text {
            display: block; /* Show text on hover */
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        /* Icons and Text Visibility */
        .sidebar a .text {
            opacity: 0;
        }

        .sidebar:hover a .text {
            opacity: 1;
        }

        .sidebar a {
            justify-content: center;
        }

        /* Premium Hover Effects */
        .sidebar a:hover {
            background-color: #3b4a59;
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateX(5px); /* Slight movement to the right */
        }

        .sidebar a .icon {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover .icon {
            color: #4CAF50; /* Change icon color on hover */
            transform: translateX(5px); /* Add icon animation */
        }


        .dashboard-feed {
            margin-left: 270px;
            padding: 2rem;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0d6efd;
        }

        ./* General Card Styles */
.feed-section {
    background: linear-gradient(145deg, #ffffff, #f3f3f3);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.feed-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.feed-section h2 {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    border-bottom: 2px solid #4CAF50;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

/* Orders List */
.orders-list .order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: background 0.3s, transform 0.3s;
}

.orders-list .order-item:hover {
    background: #f3f3f3;
    transform: translateY(-3px);
}

.orders-list .order-info {
    flex: 1;
    color: #555;
}

.orders-list .order-info h4 {
    font-size: 1.2rem;
    color: #2c3e50;
}

.orders-list .status-pending {
    color: #e67e22;
    font-weight: bold;
}

.orders-list .status-completed {
    color: #27ae60;
    font-weight: bold;
}

.orders-list .status-cancelled {
    color: #e74c3c;
    font-weight: bold;
}

/* Alerts */
.alert-item {
    display: flex;
    align-items: center;
    background: #fffbf2;
    border: 1px solid #ffebcd;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.alert-item .alert-icon {
    font-size: 1.5rem;
    margin-right: 10px;
    color: #ffcc00;
}

/* Trends */
.trend-item {
    background: linear-gradient(135deg, #f4f4f4, #e8e8e8);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 15px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

.trend-item h4 {
    font-size: 1.2rem;
    color: #4CAF50;
    margin-bottom: 10px;
}

.trend-item p {
    color: #666;
    font-size: 1rem;
}

/* Buttons (if applicable) */
.button-primary {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
}

.button-primary:hover {
    background-color: #45a049;
}

/* Responsive Enhancements */
@media (max-width: 768px) {
    .feed-section {
        padding: 15px;
    }

    .orders-list .order-item {
        flex-direction: column;
        align-items: flex-start;
    }
}

.weather-widget {
    position: fixed;
    top: 200px;
    right: 20px;
    width: 250px;
    background: #007BFF;
    color: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    font-family: 'Segoe UI', sans-serif;
    z-index: 999;
    transition: transform 0.3s ease;
}

.weather-widget:hover {
    transform: scale(1.03);
}

.weather-header h3 {
    margin: 0 0 5px;
    font-size: 18px;
}

.weather-info h2 {
    margin: 10px 0;
    font-size: 36px;
    font-weight: bold;
}

.weather-info p {
    margin: 5px 0;
}

.weather-box {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #007BFF;

    color: #fff;
    padding: 20px;
    border-radius: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    font-family: 'Segoe UI', sans-serif;
    z-index: 1000;
    min-width: 220px;
    transition: all 0.3s ease;
}
#weather-city {
    font-size: 20px;
    font-weight: 600;
}
#weather-temp {
    font-size: 36px;
    font-weight: bold;
    margin: 5px 0;
}
#weather-desc {
    font-size: 16px;
    font-style: italic;
}
    </style>

    <!-- Add this script in the head -->
    <script>
        // Define selectCrop in the window object to make it globally available
        window.selectCrop = function(productId, cropName) {
            console.log('SelectCrop called:', productId, cropName); // Debug line
            const productIdElement = document.getElementById('product_id');
            const selectedCropElement = document.getElementById('selected-crop');

            if (productIdElement && selectedCropElement) {
                productIdElement.value = productId;
                selectedCropElement.textContent = cropName;
            }
        }

        // Error handling
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.log('Error: ' + msg + '\nURL: ' + url + '\nLine: ' + lineNo + '\nColumn: ' + columnNo + '\nError: ' + error);
            return false;
        };
    </script>
</head>
<body>
<header>
    <h1>কৃষক ড্যাশবোর্ড</h1>
    <div class="user-info">
        <span>স্বাগতম, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="btn btn-danger ms-3">Logout</a>
    </div>
</header>


    <div class="sidebar">
        <h2>ন্যাভিগেশন</h2>
        <a href="farmer.php">
                    <i class="fas fa-wallet icon"></i>
                    <span class="text">ড্যাশবোর্ড</span>
                </a>


        <a href="F_Smart_Crop_Doctor.php">
                           	<i class="fas fa-stethoscope"></i>
                            <span class="text">  স্মার্ট ফসল ডাক্তার</span>
                        </a>
                        <a href="F_Agrologist_Request.php">
                                            <i class="fas fa-seedling icon"></i>
                                            <span class="text">কৃষি বিশেষজ্ঞদের সেবা</span>
                                        </a>
        <a href="crop_management.php">
            <i class="fas fa-seedling icon"></i>
            <span class="text">ফসল/পণ্য ব্যবস্থাপনা</span>
        </a>
        <a href="Buy.php">
            <i class="fas fa-shopping-cart icon"></i>
            <span class="text">সরবরাহকারীদের কাছ থেকে কিনুন</span>
        </a>
        <a href="F_labour_list.php">
                            <i class="fas fa-list icon"></i>
                            <span class="text"> শ্রমিক তালিকা </span>
                        </a>
        <a href="labour_jobs.php">
            <i class="fas fa-briefcase icon"></i>
            <span class="text">শ্রমিকের চাকরির পোস্ট</span>
        </a>

        <a href="farmer_applications.php">
                    <i class="fas fa-briefcase icon"></i>
                    <span class="text">শ্রমিকের আবেদন</span>
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

<!-- Agrologist Request Section -->
<div class="container mt-5 mb-5 p-4 bg-light rounded shadow">
    <h4 class="mb-3 text-success">🔍 অনুরোধ পাঠান Agrologist-কে</h4>
    <form method="POST">
        <div class="mb-3">
            <textarea name="request_text" class="form-control" rows="4" placeholder="আপনার সমস্যাটি লিখুন..." required></textarea>
        </div>
        <button type="submit" class="btn btn-success">✅ অনুরোধ পাঠান</button>
    </form>
</div>

<!-- Previous Requests -->
<div class="container mb-5 p-4 bg-white rounded shadow">
    <h5 class="mb-3">📋 আপনার পূর্বের অনুরোধসমূহ</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-success">
                <tr>
                    <th>তারিখ</th>
                    <th>আপনার অনুরোধ</th>
                    <th>অবস্থা</th>
                    <th>Agrologist-এর উত্তর</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= date("d M Y, h:i A", strtotime($row['request_date'])) ?></td>
                        <td><?= htmlspecialchars($row['request_text']) ?></td>
                        <td><span class="badge bg-<?= $row['status'] === 'Pending' ? 'warning' : 'success' ?>">
                            <?= $row['status'] ?></span></td>
                        <td><?= $row['agrologist_response'] ? htmlspecialchars($row['agrologist_response']) : '⏳ অপেক্ষায়' ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


</body>

</html>
