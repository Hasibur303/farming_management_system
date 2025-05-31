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
        $sql = "UPDATE agrologists SET full_name=?, sector=?, district=?, qualification=?, experience_years=?, specialization=?, photo=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi",$full_name, $sector, $district, $qualification, $experience, $specialization, $photo_name, $agrologist_id);
    } else {
        // Insert
        $sql = "INSERT INTO agrologists (user_id, full_name, sector, district, qualification, experience_years, specialization, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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

        .sidebar ul li {
            padding: 15px 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .sidebar ul li:hover {
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
        <li><a href="#"><i class="fas fa-seedling"></i><span class="d-none d-md-inline"> Farmer Requests</span></a></li>
        <li><a href="#"><i class="fas fa-sign-out-alt"></i><span class="d-none d-md-inline"> Logout</span></a></li>
    </ul>
</div>

<div class="main-content">
    <div class="topbar">
        <h2>🌾 Agrologist Profile</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>


<div class="profile-form">
    <form method="POST" enctype="multipart/form-data">
        <!-- form fields (same as before) -->
    </form>
</div>
<form method="POST" enctype="multipart/form-data" class="mt-4">
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control" required value="<?= $agrologist['full_name'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Sector</label>
        <input type="text" name="sector" class="form-control" required value="<?= $agrologist['sector'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">District</label>
        <input type="text" name="district" class="form-control" required value="<?= $agrologist['district'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Qualification</label>
        <textarea name="qualification" class="form-control" required><?= $agrologist['qualification'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Experience</label>
        <textarea name="experience" class="form-control" required><?= $agrologist['experience'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Specialization</label>
        <input type="text" name="specialization" class="form-control" required value="<?= $agrologist['specialization'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Profile Photo</label>
        <input type="file" name="photo" class="form-control">
        <?php if (!empty($agrologist['photo'])): ?>
            <img src="uploads/<?= $agrologist['photo'] ?>" alt="Profile Photo" width="100" class="mt-2">
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Save Profile</button>
</form>




    <hr class="text-light mt-4">
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Profile saved successfully!</div>
<?php endif; ?>



</body>
</html>
