<?php
session_start();
include 'database.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['crop_image'])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["crop_image"]["name"]);

    if (move_uploaded_file($_FILES["crop_image"]["tmp_name"], $targetFile)) {
        echo "<p style='color:green;'>Image uploaded successfully!</p>";
        // Here you'll send the image to the Python API and get the diagnosis
    } else {
        echo "<p style='color:red;'>Sorry, there was an error uploading your file.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - SmartKrishi</title>
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
    /* Global Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
       background: url('http://localhost/farming_management_system/AiDoctor.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #333;
        padding-top: 100px;

        font-family: Arial, sans-serif;
        position: relative;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: rgba(255, 255, 255, 0.05); /* Light overlay to soften background for readability */
        z-index: -1;
    }

.container {
    position: relative;
    max-width: 500px;
    margin: auto;

    background-size: cover;
    padding: 30px;
    box-shadow: 0 4px 25px rgba(0, 255, 255, 0.2);
    overflow: hidden;
    z-index: 1;

    backdrop-filter: blur(6px);
    border: 5px solid rgba(0, 255, 255, 0.5);
    background-blend-mode: overlay;
}

.container::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 255, 255, 0.2); /* soft blue tech glow */
    backdrop-filter: blur(4px);
    z-index: -1;
}



@keyframes techGlow {
    0%, 100% {
        box-shadow: 0 0 10px #0ff, 0 0 20px #0ff;
    }
    50% {
        box-shadow: 0 0 15px #0ff, 0 0 25px #0ff;
    }
}

.container {
    animation: techGlow 3s infinite ease-in-out;
}





    h2 {
        text-align: center;
        color: #ffffff;
        margin-bottom: 20px;
    }

    input[type="file"] {
        display: block;
        margin: 20px auto;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #preview {
        max-width: 100%;
        margin: 20px auto;
        display: none;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    button {
        display: block;
        background-color: #3182ce;
        color: white;
        border: none;
        padding: 12px 24px;
        margin: 20px auto;
        cursor: pointer;
        border-radius: 6px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #2b6cb0;
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
    <h1>স্মার্ট ফসল ডাক্তার</h1>
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

    <div class="container">
            <h2>Smart Crop Doctor</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="file" name="crop_image" id="crop_image" accept="image/*" required onchange="previewImage(event)">
                <img id="preview" src="#" alt="Image Preview">
                <button type="submit">Diagnose</button>
            </form>
        </div>

        <script>
            function previewImage(event) {
                const image = document.getElementById('preview');
                image.src = URL.createObjectURL(event.target.files[0]);
                image.style.display = 'block';
            }
        </script>


</body>

</html>
