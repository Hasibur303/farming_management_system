<?php
session_start();
include 'database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Sample agrologist ID
$agrologist_id = $_SESSION['user_id'];

// Fetch farmer requests
$requests = mysqli_query($conn, "
    SELECT b.*, u.name AS farmer_name
    FROM bookings b
    JOIN users u ON b.farmer_id = u.user_id
    WHERE b.agrologist_id = $agrologist_id
    ORDER BY b.request_date DESC
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
     $request_id = $_POST['request_id'];
        $response = mysqli_real_escape_string($conn, $_POST['response']);
        $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
        $appointment_mode = mysqli_real_escape_string($conn, $_POST['appointment_mode']);

        mysqli_query($conn, "UPDATE bookings
            SET
                message = CONCAT(message, '\n\nAgrologist Reply: ', '$response'),
                status = 'Responded',
                appointment_date = '$appointment_date',
                appointment_mode = '$appointment_mode'
            WHERE id = $request_id");

        header("Location: agrologist.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Appointment | SmartKirshi</title>
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
        <h2>🌾 Farmer Appointment</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>



    <hr class="text-light mt-4">

    <h4>📋 Farmer Requests</h4>
    <?php while ($row = mysqli_fetch_assoc($requests)): ?>
        <div class="request-box">
            <p><strong>Farmer Name:</strong> <?php echo $row['farmer_name']; ?></p>
            <p><strong>Message:</strong> <?php echo nl2br($row['message']); ?></p>
            <p><strong>Date:</strong> <?php echo $row['request_date']; ?></p>
            <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
            <?php if ($row['status'] === 'Pending'): ?>
                <button class="btn btn-reply btn-sm" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $row['id']; ?>">Reply</button>
            <?php endif; ?>
        </div>

        <!-- Reply Modal -->
        <div class="modal fade" id="replyModal<?php echo $row['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header">
                        <h5 class="modal-title">Reply to Farmer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                            <div class="mb-3">
                                <label for="response" class="form-label">Your Response:</label>
                                <textarea name="response" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="reply" class="btn btn-primary">Send</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

</div>








<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
