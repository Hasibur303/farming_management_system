<?php
session_start();
include 'database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$agrologist_id=$_SESSION['user_id'];
// Fetch profile
$query = "SELECT * FROM agrologists WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $agrologist_id);
$stmt->execute();
$result = $stmt->get_result();
$agrologist = $result->fetch_assoc();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $sector = $_POST['sector'];
    $district = $_POST['district'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $specialization = $_POST['specialization'];

    // Upload photo if exists
    $photo_name = $agrologist['photo'] ?? '';
    if ($_FILES['photo']['name']) {
        $photo_name = uniqid() . '_' . basename($_FILES["photo"]["name"]);
        $target = "uploads/" . $photo_name;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target);
    }

    if ($agrologist) {
        // Update
        $sql = "UPDATE agrologists SET full_name=?, sector=?, district=?, qualification=?, experience=?, specialization=?, photo=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi",$full_name, $sector, $district, $qualification, $experience, $specialization, $photo_name, $agrologist_id);
    } else {
        // Insert
        $sql = "INSERT INTO agrologists (user_id, full_name, sector, district, qualification, experience, specialization, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $agrologist_id, $full_name, $sector, $district, $qualification, $experience, $specialization, $photo_name);
    }

    if ($stmt->execute()) {
        header("Location: A_profile.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}




?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agrologist Dashboard | SmartKirshi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            color: #000;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            width: 70px;
            background: linear-gradient(180deg, #0f2027, #203a43, #2c5364); /* dark navy to blue gradient */
            color: #fff;
            height: 100vh;
            transition: width 0.3s ease;
            overflow-x: hidden;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 80;
        }

        .sidebar:hover {
            width: 220px;
        }

        .sidebar .logo {
            text-align: center;
            padding: 20px 10px;
            background: linear-gradient(to right, #1c1c2d, #24243e); /* matching dark tone */
        }

        .sidebar .logo img {
            width: 50px;
            transition: transform 0.3s;
        }

        .sidebar:hover .logo img {
            transform: rotate(360deg);
        }

        .sidebar ul {
            list-style: none;
            padding: 150px 0;
        }

        .sidebar ul li a{
        color: #fff;
            padding: 15px 20px;
            white-space: nowrap;
            text-decoration: none;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .sidebar ul li a:hover {
        color: #fff;
        text-decoration: none;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar ul li i {
            margin-right: 15px;
            min-width: 35px;
            text-align: center;
        }

        /* Topbar */
        .topbar {
            padding: 15px 30px;
            background: linear-gradient(to right, #1f1f2e, #2a2a40);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Logout Button */
        .logout-btn {
            color: #fff;
            background-color: #e74c3c;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        /* Cards */
        .dashboard-metrics {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }

        .card-box {
            background: linear-gradient(145deg, #1f2b37, #293544); /* subtle blue/grey glassy */
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 213, 255, 0.2);
            width: 30%;
            color: #ffffff;
        }

        .card-box h2 {
            color: #00d1b2;
        }

        /* Requests */
        .request-box {
            background: linear-gradient(to right, #1f2b37, #263445);
            margin: 15px;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 0 8px rgba(0, 255, 255, 0.1);
            color: #ffffff;
        }

        /* Reply Button */
        .btn-reply {
            background-color: #3498db;
            border: none;
            padding: 6px 12px;
            color: #fff;
            border-radius: 5px;
        }

        .btn-reply:hover {
            background-color: #2980b9;
        }

        /* Main Content */
        .main-content {
            margin-left: 80px;
            padding: 30px;
            transition: margin-left 0.3s;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 220px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-metrics {
                flex-direction: column;
                align-items: center;
            }

            .card-box {
                width: 90%;
                margin-bottom: 15px;
            }

            .sidebar {
                width: 60px;
            }

            .sidebar:hover {
                width: 180px;
            }

            .main-content {
                margin-left: 60px;
            }

            .sidebar:hover ~ .main-content {
                margin-left: 180px;
            }
        }

        /* Profile Form Card */
        .profile-form {
            background: linear-gradient(to right, #1f2b37, #263445);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 213, 255, 0.1);
            max-width: 800px;
            margin: 30px auto;
            color: #ffffff;
        }

        /* Labels and Inputs */
        .profile-form .form-label {
            font-weight: 500;
            color: #b2ebf2;
        }

        .profile-form .form-control,
        .profile-form textarea {
            background-color: #1c2c38;
            border: 1px solid #3d5a73;
            color: #fff;
            border-radius: 6px;
        }

        .profile-form .form-control:focus {
            border-color: #00bcd4;
            box-shadow: 0 0 0 0.2rem rgba(0, 188, 212, 0.25);
            background-color: #223546;
            color: #ffffff;
        }

        /* Button */
        .profile-form button.btn-primary {
            background-color: #00bcd4;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .profile-form button.btn-primary:hover {
            background-color: #0097a7;
        }

        /* Alert Message */
        .alert-success {
            background-color: #2e7d32;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Uploaded Image Preview */
        .profile-form img {
            border-radius: 10px;
            border: 2px solid #00acc1;
            margin-top: 10px;
        }



    </style>
    <!-- Font Awesome for icons -->
      <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</head>
<body>

<div class="sidebar">
    <ul>
        <li><a href="agrologist.php"><i class="fas fa-dashboard"></i><span class="d-none d-md-inline"> Dashboard</span></a></li>
        <li><a href="A_profile.php"><i class="fas fa-user-md"></i><span class="d-none d-md-inline"> Profile</span></a></li>
        <li><a href="A_agro_article.php"><i class="fas fa-pen"></i><span class="d-none d-md-inline">Articals</span></a></li>
        <li><a href="#"><i class="fas fa-seedling"></i><span class="d-none d-md-inline"> Farmer Requests</span></a></li>
        <li><a href="#"><i class="fas fa-sign-out-alt"></i><span class="d-none d-md-inline"> Logout</span></a></li>
    </ul>
</div>

<div class="main-content">
    <div class="topbar">
        <h2>🌾 Agrologist Profile</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>


<div class="profile-form p-4 bg-light rounded shadow-sm">
    <form method="POST" enctype="multipart/form-data">
        <!-- Full Name -->
        <div class="mb-3">
            <label class="form-label">পুরো নাম</label>
            <input type="text" name="full_name" class="form-control" required value="<?= $agrologist['full_name'] ?? '' ?>">
        </div>

        <!-- Sector -->
        <div class="mb-3">
            <label class="form-label">খাত (Sector)</label>
            <input type="text" name="sector" class="form-control" required value="<?= $agrologist['sector'] ?? '' ?>">
        </div>

        <!-- District Dropdown -->
        <div class="mb-3">
            <label class="form-label">জেলা</label>
            <select name="district" class="form-select" required>
                <option value="">-- জেলা নির্বাচন করুন --</option>
                <?php
                $districts = [
                    "বরগুনা", "বরিশাল", "ভোলা", "ঝালকাঠি", "পটুয়াখালী", "পিরোজপুর",
                    "বান্দরবান", "ব্রাহ্মণবাড়িয়া", "চাঁদপুর", "চট্টগ্রাম", "কুমিল্লা", "কক্সবাজার", "ফেনী", "খাগড়াছড়ি", "লক্ষ্মীপুর", "নোয়াখালী", "রাঙ্গামাটি",
                    "ঢাকা", "ফরিদপুর", "গাজীপুর", "গোপালগঞ্জ", "কিশোরগঞ্জ", "মাদারীপুর", "মানিকগঞ্জ", "মুন্সীগঞ্জ", "নারায়ণগঞ্জ", "নরসিংদী", "রাজবাড়ী", "শরীয়তপুর", "টাঙ্গাইল",
                    "বাগেরহাট", "চুয়াডাঙ্গা", "যশোর", "ঝিনাইদহ", "খুলনা", "কুষ্টিয়া", "মাগুরা", "মেহেরপুর", "নড়াইল", "সাতক্ষীরা",
                    "জামালপুর", "ময়মনসিংহ", "নেত্রকোনা", "শেরপুর",
                    "বগুড়া", "জয়পুরহাট", "নওগাঁ", "নাটোর", "চাঁপাইনবাবগঞ্জ", "পাবনা", "রাজশাহী", "সিরাজগঞ্জ",
                    "দিনাজপুর", "গাইবান্ধা", "কুড়িগ্রাম", "লালমনিরহাট", "নীলফামারী", "পঞ্চগড়", "রংপুর", "ঠাকুরগাঁও",
                    "হবিগঞ্জ", "মৌলভীবাজার", "সুনামগঞ্জ", "সিলেট"
                ];
                foreach ($districts as $d) {
                    $selected = ($agrologist['district'] ?? '') === $d ? 'selected' : '';
                    echo "<option value=\"$d\" $selected>$d</option>";
                }
                ?>
            </select>
        </div>

        <!-- Qualification -->
        <div class="mb-3">
            <label class="form-label">যোগ্যতা</label>
            <textarea name="qualification" class="form-control" rows="3" required><?= $agrologist['qualification'] ?? '' ?></textarea>
        </div>

        <!-- Experience -->
        <div class="mb-3">
            <label class="form-label">অভিজ্ঞতা</label>
            <textarea name="experience" class="form-control" rows="3" required><?= $agrologist['experience'] ?? '' ?></textarea>
        </div>

        <!-- Specialization -->
        <?php
        $selectedSpecializations = isset($agrologist['specialization']) ? explode(',', $agrologist['specialization']) : [];
        ?>

        <div class="mb-3">
            <label class="form-label">বিশেষজ্ঞ ক্ষেত্র</label>
            <select name="specialization[]" class="form-select" multiple required>
                <option value="ইন্টিগ্রেটেড ক্রপ ম্যানেজমেন্ট (ICM)" <?= in_array('ইন্টিগ্রেটেড ক্রপ ম্যানেজমেন্ট (ICM)', $selectedSpecializations) ? 'selected' : '' ?>>ইন্টিগ্রেটেড ক্রপ ম্যানেজমেন্ট (ICM)</option>
                <option value="টেকসই সেচ পদ্ধতি" <?= in_array('টেকসই সেচ পদ্ধতি', $selectedSpecializations) ? 'selected' : '' ?>>টেকসই সেচ পদ্ধতি</option>
                <option value="পশু স্বাস্থ্য ও টিকা" <?= in_array('পশু স্বাস্থ্য ও টিকা', $selectedSpecializations) ? 'selected' : '' ?>>পশু স্বাস্থ্য ও টিকা</option>
                <option value="পোলট্রি ম্যানেজমেন্ট" <?= in_array('পোলট্রি ম্যানেজমেন্ট', $selectedSpecializations) ? 'selected' : '' ?>>পোলট্রি ম্যানেজমেন্ট</option>
                <option value="মাছ চাষ প্রযুক্তি" <?= in_array('মাছ চাষ প্রযুক্তি', $selectedSpecializations) ? 'selected' : '' ?>>মাছ চাষ প্রযুক্তি</option>
                <option value="ফসল রোগ ও পোকামাকড় ব্যবস্থাপনা" <?= in_array('ফসল রোগ ও পোকামাকড় ব্যবস্থাপনা', $selectedSpecializations) ? 'selected' : '' ?>>ফসল রোগ ও পোকামাকড় ব্যবস্থাপনা</option>
                <option value="জলবায়ু সহনশীল কৃষি" <?= in_array('জলবায়ু সহনশীল কৃষি', $selectedSpecializations) ? 'selected' : '' ?>>জলবায়ু সহনশীল কৃষি</option>
                <option value="জৈব সার ও কম্পোস্টিং" <?= in_array('জৈব সার ও কম্পোস্টিং', $selectedSpecializations) ? 'selected' : '' ?>>জৈব সার ও কম্পোস্টিং</option>
                <option value="ডেইরি খামার ব্যবস্থাপনা" <?= in_array('ডেইরি খামার ব্যবস্থাপনা', $selectedSpecializations) ? 'selected' : '' ?>>ডেইরি খামার ব্যবস্থাপনা</option>
                <option value="বীজ উৎপাদন ও সংরক্ষণ" <?= in_array('বীজ উৎপাদন ও সংরক্ষণ', $selectedSpecializations) ? 'selected' : '' ?>>বীজ উৎপাদন ও সংরক্ষণ</option>
            </select>
            <small class="form-text text-muted">একাধিক অপশন নির্বাচন করতে Ctrl (Windows) অথবা ⌘ (Mac) চেপে রাখুন।</small>
        </div>


        <!-- Photo -->
        <div class="mb-3">
            <label class="form-label">প্রোফাইল ছবি</label><br>
            <?php if (!empty($agrologist['photo'])): ?>
                        <img src="uploads/<?= $agrologist['photo'] ?>" alt="Profile Photo" width="100" class="mt-2">
                    <?php endif; ?>
            <input type="file" name="photo" class="form-control">
        </div>

        <!-- Submit -->
        <div class="text-end">
            <button type="submit" class="btn btn-success">প্রোফাইল আপডেট করুন</button>
        </div>
    </form>
</div>


    <hr class="text-light mt-4">
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Profile saved successfully!</div>
<?php endif; ?>



</body>
</html>
