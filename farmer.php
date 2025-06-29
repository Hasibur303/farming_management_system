<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // assuming this is farmer id
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
    background: linear-gradient(to right, #2E7D32, #66BB6A); /* Elegant green gradient */
    color: white;
    padding: 1rem 3rem; /* Standard padding */
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid #2E7D32;
    transition: background 0.3s ease;
    text-align: center;
}

/* Header Title */
header h1 {
    font-size: 1.75rem; /* Standard professional size */
    font-weight: 600;
    letter-spacing: 0.5px; /* Slight letter spacing for formality */
    margin: 0;
    color: #ffffff;
    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; /* Clean, formal fonts */
    flex-grow: 1;
    text-align: center;
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
            overflow-y: auto;  /* 👈 Enables vertical scrolling */
            overflow-x: hidden; /* 👈 Prevents horizontal scrollbars */
            z-index: 999; /* Ensure it stays above content */
            scrollbar-width: thin; /* Optional: thinner scrollbar for Firefox */
            scrollbar-color: #888 transparent; /* Optional: scrollbar color */
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
            font-family: 'Poppins', sans-serif;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.8rem;
            justify-content: center;
            padding: 2rem;
        }

        .stat-card {
            background: #1e1e2f;
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
        }

        /* Glowing animated circle */
        .stat-circle {
            width: 110px;
            height: 110px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            border: 6px solid #ffc107;
            animation: statGlow 2.5s ease-in-out infinite;
            box-shadow: 0 0 12px rgba(255, 193, 7, 0.5);
            background: linear-gradient(135deg, #0d6efd, #003c9e);
        }

        /* Optional: different colors for each stat */
        .stat-card:nth-child(1) .stat-circle {
            background: #e91e63;
        }
        .stat-card:nth-child(2) .stat-circle {
            background: #3f51b5;
        }
        .stat-card:nth-child(3) .stat-circle {
            background: #4caf50;
        }
        .stat-card:nth-child(4) .stat-circle {
            background: #ff9800;
        }

        @keyframes statGlow {
            0%, 100% {
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.25);
            }
            50% {
                box-shadow: 0 0 18px rgba(255, 255, 255, 0.5);
            }
        }

        .stat-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: #ccc;
            margin-top: 0.75rem;
            letter-spacing: 0.4px;
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


.dashboard-sections {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin: 20px;
  }

  .feed-section {
    background: #1f2937; /* Premium dark card background */
    color: #f9fafb;
    border-radius: 15px;
    padding: 20px;
    flex: 1;
    min-width: 300px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
  }

  .feed-section:hover {
    transform: translateY(-5px);
  }

  .feed-section h2 {
    font-size: 20px;
    border-bottom: 1px solid #374151;
    padding-bottom: 10px;
    margin-bottom: 15px;
    color: #10b981;
  }

  .order-item,
  .alert-item,
  .trend-item {
    background: #111827;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid #10b981;
  }

  .order-info p,
  .alert-item p,
  .trend-item p {
    margin: 5px 0;
    font-size: 14px;
  }

  .status-pending {
    color: #f59e0b;
  }

  .status-completed {
    color: #10b981;
  }

  .status-cancelled {
    color: #ef4444;
  }

  .alert-icon {
    font-size: 20px;
    margin-right: 8px;
  }

  @media (max-width: 1024px) {
    .dashboard-sections {
      flex-direction: column;
    }
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
                    <a href="F_Agribot.php" class="active"><i class="fas fa-robot icon"></i><span class="text">অ্যাগ্রিবট</span></a>



        <a href="F_Smart_Crop_Doctor.php">
                           	<i class="fas fa-stethoscope"></i>
                            <span class="text">  স্মার্ট ফসল ডাক্তার</span>
                        </a>
                        <a href="Agrologist_List.php">
                                            <i class="fas fa-tree icon"></i>
                                            <span class="text">কৃষি-বিশেষজ্ঞদের সেবা</span>
                                        </a>
        <a href="F_article.php">
                        <i class="fas fa-pen icon"></i>
                        <span class="text">কৃষি-বিশেষজ্ঞদের প্রবন্ধ পরুন</span>
        </a>
        <a href="F_chatbot.php">
                                <i class="fas fa-pen icon"></i>
                                <span class="text">এআই চ্যাট বট</span>
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

<div class="dashboard-feed">
    <!-- Statistics Summary -->
    
   <div class="stats-cards">

       <!-- Today's Sales -->
       <div class="stat-card">
           <?php
           $today = date('Y-m-d');
           $stmt = $conn->prepare("
               SELECT COALESCE(SUM(total_amount), 0) as total
               FROM orders
               WHERE farmer_id = ?
               AND DATE(order_date) = ?
               AND status = 'Delivered'
           ");
           $stmt->bind_param("is", $_SESSION['user_id'], $today);
           $stmt->execute();
           $result = $stmt->get_result()->fetch_assoc();
           ?>
           <div class="stat-circle">৳<?= number_format($result['total'], 0) ?></div>
           <div class="stat-label">আজকের বিক্রয়</div>
       </div>

       <!-- Monthly Sales -->
       <div class="stat-card">
           <?php
           $firstDayOfMonth = date('Y-m-01');
           $lastDayOfMonth = date('Y-m-t');
           $stmt = $conn->prepare("
               SELECT COALESCE(SUM(total_amount), 0) as total
               FROM orders
               WHERE farmer_id = ?
               AND DATE(order_date) BETWEEN ? AND ?
               AND status = 'Delivered'
           ");
           $stmt->bind_param("iss", $_SESSION['user_id'], $firstDayOfMonth, $lastDayOfMonth);
           $stmt->execute();
           $result = $stmt->get_result()->fetch_assoc();
           ?>
           <div class="stat-circle">৳<?= number_format($result['total'], 0) ?></div>
           <div class="stat-label">এই মাসের বিক্রয়</div>
       </div>

       <!-- Active Listings -->
       <div class="stat-card">
           <?php
           $stmt = $conn->prepare("
               SELECT COUNT(*) as count
               FROM farmer_crops
               WHERE farmer_id = ? AND quantity > 0
           ");
           $stmt->bind_param("i", $_SESSION['user_id']);
           $stmt->execute();
           $result = $stmt->get_result()->fetch_assoc();
           ?>
           <div class="stat-circle"><?= $result['count'] ?></div>
           <div class="stat-label">সক্রিয় তালিকা</div>
       </div>

       <!-- Pending Orders -->
       <div class="stat-card">
           <?php
           $stmt = $conn->prepare("
               SELECT COUNT(*) as count
               FROM orders
               WHERE farmer_id = ?
               AND status = 'Pending'
           ");
           $stmt->bind_param("i", $_SESSION['user_id']);
           $stmt->execute();
           $result = $stmt->get_result()->fetch_assoc();
           ?>
           <div class="stat-circle"><?= $result['count'] ?></div>
           <div class="stat-label">মুলতুবি অর্ডার</div>
       </div>

   </div>

<!-- Container for side-by-side sections -->
<div class="dashboard-sections">
  <!-- Recent Orders Section -->
  <div class="recent-orders feed-section">
    <h2>সাম্প্রতিক অর্ডারগুলি</h2>
    <?php
    $stmt = $conn->prepare("
        SELECT order_id, total_amount, status, order_date
        FROM orders
        WHERE farmer_id = ?
        ORDER BY order_date DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $recent_orders = $stmt->get_result();
    ?>
    <div class="orders-list">
      <?php while ($order = $recent_orders->fetch_assoc()): ?>
        <div class="order-item">
          <div class="order-info">
            <h4>Order #<?= htmlspecialchars($order['order_id']) ?></h4>
            <p>Amount: TK. <?= number_format($order['total_amount'], 2) ?></p>
            <p>Status: <span class="status-<?= strtolower($order['status']) ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span></p>
            <p class="order-date">Ordered: <?= date('M d, Y H:i', strtotime($order['order_date'])) ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Low Stock Alerts -->
  <div class="low-stock-alerts feed-section">
    <h2>কম স্টক সতর্কতা</h2>
    <?php
    $stmt = $conn->prepare("
        SELECT name, quantity, quantity_type
        FROM farmer_crops
        WHERE farmer_id = ? AND quantity <= 5
        ORDER BY quantity ASC
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $low_stock = $stmt->get_result();
    ?>
    <div class="alerts-list">
      <?php while ($item = $low_stock->fetch_assoc()): ?>
        <div class="alert-item">
          <span class="alert-icon">⚠️</span>
          <p><?= htmlspecialchars($item['name']) ?> - Only <?= htmlspecialchars($item['quantity']) ?>
             <?= htmlspecialchars($item['quantity_type']) ?> left</p>
        </div>
      <?php endwhile; ?>
    </div>
    <!-- Weather Widget -->
    <div id="weather-widget" class="weather-box">
    <h3>🌤️ আবহাওয়ার আপডেট</h3>
        <div id="weather-city"> Loading weather...</div>
        <div id="weather-temp"></div>
        <div id="weather-desc"></div>
        <p>আর্দ্রতা: <span id="humidity">--%</span></p>
              <p>বায়ুর গতি: <span id="wind">-- কিমি/ঘণ্টা</span></p>
        <p>পরবর্তী ২ ঘণ্টার সম্ভাব্য অবস্থা: <span id="next-forecast">লোড হচ্ছে...</span></p>
    </div>

    <!-- Weather Script in Bangla -->
    <script>
    const apiKey = "07e734ebc19510e488064f54a0f45dd8"; // OpenWeatherMap API key

    // English to Bangla weather mapping
    const banglaDescriptions = {
        "clear sky": "পরিষ্কার আকাশ",
        "few clouds": "হালকা মেঘ",
        "scattered clouds": "বিক্ষিপ্ত মেঘ",
        "broken clouds": "ভাঙা মেঘ",
        "overcast clouds": "ঘন মেঘ",
        "shower rain": "বৃষ্টির ঝরনা",
        "light rain": "হালকা বৃষ্টি",
        "moderate rain": "মাঝারি বৃষ্টি",
        "heavy intensity rain": "ভারী বৃষ্টি",
        "rain": "বৃষ্টি",
        "thunderstorm": "বজ্রসহ ঝড়",
        "snow": "তুষারপাত",
        "mist": "কুয়াশা"
    };

    function translate(desc) {
        return banglaDescriptions[desc.toLowerCase()] || desc;
    }

    // Fetch current weather
    function fetchWeather(lat, lon) {
        fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`)
            .then(res => res.json())
            .then(data => {
                updateWeather(data);
                fetchForecast(data.name); // Call forecast with city name
            })
            .catch(() => fetchWeatherByCity("Dhaka,BD"));
    }

    function fetchWeatherByCity(city) {
        fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`)
            .then(res => res.json())
            .then(data => {
                updateWeather(data);
                fetchForecast(city); // Call forecast with city name
            })
            .catch(() => {
                document.getElementById('weather-city').textContent = "আবহাওয়া তথ্য পাওয়া যায়নি";
            });
    }

    function updateWeather(data) {
        document.getElementById('weather-city').textContent = `${data.name} এর আবহাওয়া`;
        document.getElementById('weather-temp').textContent = `${data.main.temp}°C`;
        const englishDesc = data.weather[0].description;
        document.getElementById('weather-desc').textContent = `অবস্থা: ${translate(englishDesc)}`;
        document.getElementById('humidity').textContent = `${data.main.humidity}%`;
        document.getElementById('wind').textContent = `${data.wind.speed} কিমি/ঘণ্টা`;
    }

    // Fetch forecast for next 2 hours
    function fetchForecast(city) {
        fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${city}&appid=${apiKey}&units=metric`)
            .then(res => res.json())
            .then(data => {
                if (data.list && data.list.length >= 2) {
                    const desc1 = data.list[0].weather[0].description;
                    const desc2 = data.list[1].weather[0].description;
                    const banglaDesc1 = translate(desc1);
                    const banglaDesc2 = translate(desc2);
                    document.getElementById("next-forecast").textContent = `${banglaDesc1} → ${banglaDesc2}`;

                    // OPTIONAL: Only include this if you added <span id="rain"> in HTML
                    // document.getElementById("rain").textContent = `${Math.round(data.list[0].pop * 100)}%`;
                } else {
                    document.getElementById("next-forecast").textContent = "তথ্য নেই";
                }
            })
            .catch(() => {
                document.getElementById("next-forecast").textContent = "পূর্বাভাস লোড হয়নি";
            });
    }


    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            pos => fetchWeather(pos.coords.latitude, pos.coords.longitude),
            () => fetchWeatherByCity("Dhaka,BD")
        );
    } else {
        fetchWeatherByCity("Dhaka,BD");
    }
    </script>
  </div>

  <!-- Price Trends -->
  <div class="price-trends feed-section">
    <h2>বাজার মূল্যের প্রবণতা</h2>
    <div class="trends-list">
      <?php
      $stmt = $conn->prepare("
          SELECT fc1.name,
                 AVG(fc2.price) as avg_price,
                 MAX(fc2.price) as max_price,
                 MIN(fc2.price) as min_price
          FROM farmer_crops fc1
          JOIN farmer_crops fc2 ON fc1.name = fc2.name
          WHERE fc1.farmer_id = ?
          GROUP BY fc1.name
      ");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();
      $trends = $stmt->get_result();
      ?>
      <?php while ($trend = $trends->fetch_assoc()): ?>
        <div class="trend-item">
          <h4><?= htmlspecialchars($trend['name']) ?></h4>
          <p>Average Price: TK. <?= number_format($trend['avg_price'], 2) ?></p>
          <p>Range: TK. <?= number_format($trend['min_price'], 2) ?> -
             TK. <?= number_format($trend['max_price'], 2) ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>


</body>

</html>
