<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];
$farmer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT district FROM agrologists WHERE user_id='$farmer_id'"));
$farmer_district = $farmer['district'] ?? '';

$query = "SELECT a.*, u.name FROM agrologists a
          JOIN users u ON a.user_id = u.user_id
          WHERE a.user_id != '$farmer_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Agrologists</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            color: #000;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            width: 70px;
            background: linear-gradient(180deg, #0f2027, #203a43, #2c5364);
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
            background: linear-gradient(to right, #1c1c2d, #24243e);
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

        .main-content {
            margin-left: 80px;
            padding: 30px;
            transition: margin-left 0.3s;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 220px;
        }

        .card {
            background: linear-gradient(to bottom right, #1f2b37, #263445);
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 12px rgba(0, 213, 255, 0.15);
            transition: 0.3s;
            color: #fff;
        }

        .card:hover {
            box-shadow: 0 0 20px rgba(0, 255, 200, 0.2);
            transform: translateY(-3px);
        }

        .photo {
            height: 90px;
            width: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #00d1b2;
        }

        .card h5 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #00d1b2;
        }

        .card p {
            font-size: 14px;
            margin: 0;
            color: #ddd;
        }

        .badge.bg-success {
            background-color: #2ecc71;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
        }

        textarea.form-control {
            background: #1f2b37;
            border: 1px solid #00d1b2;
            color: #fff;
            border-radius: 10px;
            resize: none;
        }

        textarea.form-control::placeholder {
            color: #bbb;
        }

        .btn-book {
            background: #00d1b2;
            color: #fff;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-book:hover {
            background: #00bfa5;
            color: #fff;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
            }

            .sidebar {
                width: 60px;
            }

            .sidebar:hover {
                width: 180px;
            }

            .sidebar:hover ~ .main-content {
                margin-left: 180px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">

        <ul>
            <li><i class="bi bi-clock-history"></i><a href="A_previous_response.php" class="text-white text-decoration-none">View Previous Responses</a></li>
            <li><i class="bi bi-plus-circle"></i><a href="help_post.php" class="text-white text-decoration-none">Create Help Post</a></li>
            <li><i class="bi bi-box-arrow-right"></i><a href="logout.php" class="text-white text-decoration-none">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Available Agrologists</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 mb-4">
                    <div class="card p-3">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?php echo $row['photo']; ?>" class="photo me-3">
                            <div>
                                <h5><?php echo $row['name']; ?></h5>
                                <p>Sector: <?php echo $row['sector']; ?><br>
                                   District: <?php echo $row['district']; ?></p>
                                <?php if ($row['district'] == $farmer_district): ?>
                                    <span class="badge bg-success">Nearby</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <form action="A_book_agrologist.php" method="post" class="mt-3">
                            <input type="hidden" name="agrologist_id" value="<?php echo $row['user_id']; ?>">
                           <div class="mb-2">
                               <label class="form-label">Appointment Type</label>
                               <select name="appointment_type" class="form-select" required>
                                   <option value="">-- Select Type --</option>
                                   <option value="online">Online</option>
                                   <option value="offline">Offline</option>
                               </select>
                           </div>

                           <div class="mb-2">
                               <textarea name="message" class="form-control" placeholder="Write a message..." required></textarea>
                           </div>



                           <button type="submit" class="btn btn-book w-100">Request Appointment</button>

                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
