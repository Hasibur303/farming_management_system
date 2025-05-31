<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // assuming this is farmer id
// Fetch articles
$sql = "SELECT * FROM agro_articles ORDER BY created_at DESC";
$result = $conn->query($sql);


$sql = "
    SELECT
        agro_articles.*,
        agrologists.full_name,
        agrologists.photo
    FROM agro_articles
    JOIN agrologists ON agro_articles.agrologist_id = agrologists.user_id
    ORDER BY agro_articles.created_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Articleboard - SmartKrishi</title>
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


body {
            font-family: 'SolaimanLipi', sans-serif;
            background-color: #f9f9f9;
        }
        .article-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .article-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .article-content {
            padding: 15px 20px;
            flex-grow: 1;
        }

        .article-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #28a745;
        }

        .article-meta {
            font-size: 0.85rem;
            color: #777;
        }

        .agrologist-info {
            background-color: #f6f6f6;
            border-bottom: 1px solid #ddd;
        }

        .agrologist-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #28a745;
        }

    </style>
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
                        <a href="Agrologist_List.php">
                                            <i class="fas fa-tree icon"></i>
                                            <span class="text">কৃষি-বিশেষজ্ঞদের সেবা</span>
                                        </a>
        <a href="F_article.php">
                        <i class="fas fa-pen icon"></i>
                        <span class="text">কৃষি-বিশেষজ্ঞদের প্রবন্ধ পরুন</span>
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


    <div class="container mt-4">
        <h2 class="mb-4 text-center text-success">কৃষি-বিশেষজ্ঞদের প্রবন্ধ</h2>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="article-card">
                            <!-- Agrologist Info -->
                            <div class="agrologist-info d-flex align-items-center p-2">
                                <img src="<?php echo htmlspecialchars($row['photo']); ?>" class="agrologist-photo me-2" alt="Agrologist Photo">
                                <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                            </div>

                            <!-- Article Image -->
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" class="article-img" alt="Article Image">
                            <?php endif; ?>

                            <!-- Content -->
                            <div class="article-content">
                                <div class="article-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                <div class="article-meta mb-2">প্রকাশিত: <?php echo date('d M, Y', strtotime($row['created_at'])); ?></div>
                                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">কোনো প্রবন্ধ পাওয়া যায়নি।</p>
            <?php endif; ?>
        </div>
    </div>



</body>

</html>
