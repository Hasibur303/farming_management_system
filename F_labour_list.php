<?php
session_start();
include 'database.php';

if (isset($_POST['submit_contract'])) {
    $labour_id = $_POST['labour_id'];
    $district = $_POST['district'];
    $start_date = $_POST['start_date'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $address = $_POST['address'];
    $photoName = '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photoName = time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/" . $photoName);
    }

    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO contract (user_id, name, labour_id, start_date, description, amount, photo, address, district) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissdsss", $user_id, $username, $labour_id, $start_date, $description, $amount, $photoName, $address, $district);

    if ($stmt->execute()) {
        echo "<script>alert('চুক্তি সফলভাবে পাঠানো হয়েছে।');window.location.href='F_labour_list.php';</script>";
    } else {
        echo "ত্রুটি হয়েছে: " . $stmt->error;
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

</style>

</head>
<body>
<header>
    <h1>  শ্রমিক তালিকা </h1>
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




<?php
$selectedDistrict = isset($_GET['district']) ? $_GET['district'] : '';
?>
<div class="container my-4">
    <form method="GET" class="row g-3 align-items-center">
        <div class="col-auto">
            <label for="district" class="form-label fw-bold">জেলা অনুসারে খুঁজুন:</label>
        </div>
        <div class="col-auto">
            <select name="district" id="district" class="form-select">
                <option value="">সকল জেলা</option>
                <?php
$districts = [
    "বাগেরহাট", "বান্দরবান", "বরগুনা", "বরিশাল", "ভোলা", "বগুড়া", "ব্রাহ্মণবাড়িয়া", "চাঁদপুর", "চাঁপাইনবাবগঞ্জ",
    "চট্টগ্রাম", "চুয়াডাঙ্গা", "কুমিল্লা", "কক্সবাজার", "ঢাকা", "দিনাজপুর", "ফরিদপুর", "ফেনী", "গাইবান্ধা",
    "গাজীপুর", "গোপালগঞ্জ", "হবিগঞ্জ", "জামালপুর", "যশোর", "ঝালকাঠি", "ঝিনাইদহ", "জয়পুরহাট", "খাগড়াছড়ি",
    "খুলনা", "কিশোরগঞ্জ", "কুড়িগ্রাম", "কুষ্টিয়া", "লক্ষ্মীপুর", "লালমনিরহাট", "মাদারীপুর", "মাগুরা", "মানিকগঞ্জ",
    "মেহেরপুর", "মৌলভীবাজার", "মুন্সীগঞ্জ", "ময়মনসিংহ", "নওগাঁ", "নড়াইল", "নারায়ণগঞ্জ", "নরসিংদী", "নাটোর",
    "নেত্রকোনা", "নীলফামারী", "নোয়াখালী", "পাবনা", "পঞ্চগড়", "পটুয়াখালী", "পিরোজপুর", "রাজবাড়ী", "রাজশাহী",
    "রাঙামাটি", "রংপুর", "সাতক্ষীরা", "শরীয়তপুর", "শেরপুর", "সিরাজগঞ্জ", "সুনামগঞ্জ", "সিলেট", "টাঙ্গাইল", "ঠাকুরগাঁও"
];
                foreach ($districts as $district) {
                    $selected = ($district == $selectedDistrict) ? 'selected' : '';
                    echo "<option value='$district' $selected>$district</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-success">অনুসন্ধান করুন</button>
        </div>
    </form>
</div>

<div class="container">
    <div class="row">
        <?php
        $sql = "SELECT * FROM labour";
        if ($selectedDistrict !== '') {
            $sql .= " WHERE district = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $selectedDistrict);
        } else {
            $stmt = $conn->prepare($sql);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($labour = $result->fetch_assoc()) {
                $lastLogin = new DateTime($labour['last_login']);
                $now = new DateTime();
                $interval = $now->diff($lastLogin)->days;
                $status = ($interval <= 3) ? "সক্রিয় শ্রমিক" : "নিষ্ক্রিয় শ্রমিক";
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm p-3 h-100">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?= htmlspecialchars($labour['photo']) ?>" alt="Photo" class="rounded-circle me-3" width="200" height="200" style="object-fit:cover;">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($labour['name']) ?></h5>
                                <p class="mb-1"><?= htmlspecialchars($labour['description']) ?></p>
                                <p class="mb-1"><strong>বয়স:</strong> <?= $labour['age'] ?> বছর</p>
                                <p class="mb-1"><strong>দৈনিক বেতন:</strong> ৳<?= $labour['salary_per_day'] ?></p>
                                <p class="mb-1"><strong>অভিজ্ঞতা:</strong> <?= $labour['job_experience'] ?> বছর</p>
                                <p class="mb-1"><strong>অবস্থান:</strong> <?= htmlspecialchars($labour['location']) ?>, <?= htmlspecialchars($labour['district']) ?></p>
                                <span class="badge <?= ($status === "সক্রিয় শ্রমিক") ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $status ?>
                                </span>


                                <!-- Contract Proposal Button -->
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#contractModal<?= $labour['id'] ?>">
                                    চুক্তির প্রস্তাব
                                </button>

                                <!-- Contract Modal -->
                                <div class="modal fade" id="contractModal<?= $labour['id'] ?>" tabindex="-1">
                                  <div class="modal-dialog">
                                    <form action="" method="POST" enctype="multipart/form-data" class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">চুক্তির প্রস্তাব দিন</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <input type="hidden" name="labour_id" value="<?= $labour['user_id'] ?>">
                                        <div class="mb-2">
                                          <label class="form-label">জেলা</label>
                                          <select name="district" class="form-select" required>
                                            <?php foreach ($districts as $dist): ?>
                                              <option value="<?= $dist ?>"><?= $dist ?></option>
                                            <?php endforeach; ?>
                                          </select>
                                        </div>
                                        <div class="mb-2">
                                          <label class="form-label">শুরুর তারিখ</label>
                                          <input type="date" name="start_date" class="form-control" required>
                                        </div>
                                        <div class="mb-2">
                                          <label class="form-label">বর্ণনা</label>
                                          <textarea name="description" class="form-control" required></textarea>
                                        </div>
                                        <div class="mb-2">
                                          <label class="form-label">টাকার পরিমাণ (৳)</label>
                                          <input type="number" name="amount" class="form-control" required>
                                        </div>
                                        <div class="mb-2">
                                          <label class="form-label">ছবি (ঐচ্ছিক)</label>
                                          <input type="file" name="photo" class="form-control" accept="image/*">
                                        </div>
                                        <div class="mb-2">
                                          <label class="form-label">ঠিকানা</label>
                                          <textarea name="address" class="form-control" required></textarea>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="submit" name="submit_contract" class="btn btn-success">পাঠান</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12'><p class='text-center text-muted'>নির্বাচিত জেলার জন্য কোন শ্রমিক পাওয়া যায়নি।</p></div>";
        }
        ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
