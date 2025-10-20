<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('database.php');  // Adjust path as needed

// Check if farmer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}



$farmer_id = $_SESSION['user_id'];
// Analytics Functions
function getProductPerformanceReport($farmer_id) {
    global $conn;
     //Calculates the total quantity sold for each product in the last 30 days from delivered orders.
    $query = "WITH ProductSales AS (
        SELECT 
            o.product_id,
            SUM(o.quantity) as total_sold
        FROM orders o
        WHERE o.order_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        AND o.status = 'Delivered'
        GROUP BY o.product_id
    )
    SELECT 
        p.*,
        COALESCE(ps.total_sold, 0) as monthly_sales,
        p.price * COALESCE(ps.total_sold, 0) as monthly_revenue
    FROM farmer_crops p
    LEFT JOIN ProductSales ps ON p.product_id = ps.product_id
    WHERE p.farmer_id = ?
    ORDER BY monthly_sales DESC";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $farmer_id);
        $stmt->execute();
        return $stmt->get_result();
    } catch (Exception $e) {
        error_log("Error in getProductPerformanceReport: " . $e->getMessage());
        return false;
    }
}

function getDetailedSalesAnalysis($farmer_id) {
    global $conn;
    // This query retrieves product sales performance for the past 30 days, including total orders,
    // units sold, revenue, average order size, and current stock, grouped by product 
    // and sorted by total revenue in descending order.
    $query = "SELECT 
        p.name,
        COUNT(DISTINCT o.order_id) as total_orders,
        SUM(o.quantity) as total_units_sold,
        SUM(o.quantity * p.price) as total_revenue,
        AVG(o.quantity) as avg_order_size,
        p.quantity as current_stock
    FROM farmer_crops p
    LEFT JOIN orders o ON p.product_id = o.product_id
    AND o.order_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    WHERE p.farmer_id = ?
    GROUP BY p.product_id, p.name, p.quantity
    ORDER BY total_revenue DESC";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $farmer_id);
        $stmt->execute();
        return $stmt->get_result();
    } catch (Exception $e) {
        error_log("Error in getDetailedSalesAnalysis: " . $e->getMessage());
        return false;
    }
}

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

       <!-- Bootstrap -->
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

       <!-- Font Awesome -->
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
    margin: 20px 0;
    background: #e0f2f1; /* Light greenish background */
    border: 2px solid #a5d6a7;
    border-radius: 10px;
    text-align : center;
}

.analytics-container {
    margin: 0 auto;               /* Center horizontally */
    text-align: center;           /* Center text inside */
    max-width: 1500px;             /* Optional: limit width for better layout */
    padding: 20px;
}

.analytics-card {
    background: #ffffff;
    border-left: 6px solid #8bc34a; /* Green accent */
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 25px;
    padding: 25px;
    transition: transform 0.2s ease;

}

.analytics-card:hover {
    transform: translateY(-5px);
}

.analytics-header {
    border-bottom: 2px solid #c8e6c9;
    margin-bottom: 20px;
    text-align : center;
    padding-bottom: 15px;
}

.analytics-header h3 {
    color: #33691e; /* Deep green */
    font-size: 1.8rem;
    text-align : center;
    font-weight: 700;
    margin: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(145deg, #f1f8e9, #ffffff);
    border-left: 4px solid #cddc39;
    border-radius: 10px;
    padding: 20px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.stat-card .stat-value {
    color: #2c3e50;
    font-size: 26px;
    font-weight: bold;
    margin: 10px 0;
}

.stat-card .stat-label {
    color: #689f38;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.chart-container {
    position: relative;
    height: 350px;
    margin: 20px 0;
    padding: 15px;
    background: #f1f8e9;
    border-radius: 10px;
    border: 2px solid #aed581;
}

.table-responsive {
    margin-top: 20px;
    border-radius: 10px;
    overflow: hidden;
    background: #e8f5e9;
    padding: 10px;
}

.analytics-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.analytics-table th {
    background-color: #aed581;
    color: #1b5e20;
    font-weight: 600;
    padding: 15px;
    text-align: center;
    border-bottom: 2px solid #7cb342;
}

.analytics-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #c5e1a5;
    color: #2e7d32;
}

.analytics-table tr:hover {
    background-color: #f1f8e9;
}

.print-button {
    background-color: #fbc02d;
    color: #4e342e;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.print-button:hover {
    background-color: #f9a825;
}

.trend-indicator {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 8px;
}

.trend-up {
    background-color: #dcedc8;
    color: #33691e;
}

.trend-down {
    background-color: #ffccbc;
    color: #d84315;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .analytics-card {
        padding: 15px;
    }

    .chart-container {
        height: 300px;
    }
}

@media print {
    .print-button {
        display: none;
    }
}

.dashboard-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(135deg, #4CAF50, #8BC34A, #CDDC39);
    color: white;
    padding: 14px 24px;
    border-radius: 40px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transition: transform 0.2s ease, background 0.3s ease;
    z-index: 999;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.dashboard-button:hover {
    background: linear-gradient(135deg, #388E3C, #689F38, #AFB42B);
    transform: scale(1.05);
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
</style>




</head>
<body>
    <div class="analytics-container">
        <!-- Page Header -->
        <div class="analytics-header d-flex justify-content-between align-items-center mb-4">
            <h3>বিশ্লেষণ এবং প্রতিবেদন</h3>
            <button class="print-button" onclick="window.print()">
                <i class="fas fa-print me-2"></i> প্রতিবেদন মুদ্রণ করুন
            </button>
        </div>

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
<div class="dashboard-feed">

        <!-- Stats Overview -->
        <div class="stats-grid">
            <?php
            // Calculate total revenue
            $total_revenue_query = "SELECT SUM(total_amount) as total_revenue 
                                  FROM orders o 
                                  WHERE o.farmer_id = ? 
                                  AND o.order_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                                  AND o.status = 'Delivered'";


            $stmt = $conn->prepare($total_revenue_query);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $total_revenue = $stmt->get_result()->fetch_assoc()['total_revenue'] ?? 0;

            // Calculate total orders
            $total_orders_query = "SELECT COUNT(DISTINCT o.order_id) as total_orders 
                                 FROM orders o  
                                 WHERE o.farmer_id = ?
                                 AND o.status='Delivered'"
                                ;
            $stmt = $conn->prepare($total_orders_query);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $total_orders = $stmt->get_result()->fetch_assoc()['total_orders'] ?? 0;

            // Calculate total products
            $total_products_query = "SELECT COUNT(distinct f.product_id) as total_products 
                                   FROM farmer_crops f 
                                   WHERE farmer_id = ?";
            $stmt = $conn->prepare($total_products_query);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $total_products = $stmt->get_result()->fetch_assoc()['total_products'] ?? 0;
            ?>
            
            <div class="stat-card">
                <div class="stat-label">মোট (Revenue)রাজস্ব (৩০ দিন)</div>
                <div class="stat-value">Tk.<?php echo number_format($total_revenue, 2); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">মোট অর্ডার</div>
                <div class="stat-value"><?php echo number_format($total_orders); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">সক্রিয় পণ্য</div>
                <div class="stat-value"><?php echo number_format($total_products); ?></div>
            </div>
        </div>

        <!-- Product Performance -->
        <div class="analytics-card">
            <div class="analytics-header">
                <h3>পণ্যের পারফরম্যান্স (গত ৩০ দিন)</h3>
            </div>
            <div class="table-responsive">
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>পণ্যের নাম</th>
                            <th>বর্তমান স্টক</th>
                            <th>দাম</th>
                            <th>বিক্রিত ইউনিট</th>
                            <th>Revenue(রাজস্ব)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = getProductPerformanceReport($_SESSION['user_id']);
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                echo "<td>'Tk." . number_format($row['price'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['monthly_sales']) . "</td>";
                                echo "<td>Tk." . number_format($row['monthly_revenue'], 2) . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <a href="farmer.php" class="dashboard-button">
        <i class="fas fa-home"></i> ড্যাশবোর্ডে যান
    </a>

</div>
</body>
</html>
