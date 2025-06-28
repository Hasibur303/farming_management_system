<?php
// farmer_request.php — Agrologist handles farmer booking requests (no location column)
session_start();
require_once 'database.php';

// 🔐 Auth check: only logged‑in agrologist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Agrologist') {
    header('Location: login.php');
    exit();
}
$agrologist_id = (int) $_SESSION['user_id'];

/* ------------------------------------------------------------------
   FETCH BOOKING REQUESTS FOR DASHBOARD TABLE/LIST
   ------------------------------------------------------------------ */
$reqSql  = "SELECT b.*, u.name AS farmer_name
             FROM bookings b
             JOIN users u ON b.farmer_id = u.user_id
             WHERE b.agrologist_id = ?
             ORDER BY b.request_date DESC";
$reqStmt = $conn->prepare($reqSql);
$reqStmt->bind_param('i', $agrologist_id);
$reqStmt->execute();
$requests = $reqStmt->get_result();

/* ------------------------------------------------------------------
   HANDLE REPLY (ACCEPT / DECLINE)
   ------------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $request_id = (int) $_POST['request_id'];
    $response   = trim($_POST['response'] ?? '');
    $decision   = $_POST['decision'] ?? 'declined';

    if ($decision === 'accepted') {
        $appointment_date = $_POST['appointment_date'] ?? '';
        $appointment_mode = $_POST['appointment_mode'] ?? 'online'; // online / offline

        $sql = "UPDATE bookings
                   SET message          = CONCAT(message, '\n\nAgrologist Reply: ', ?),
                       status           = 'Accepted',
                       appointment_date = ?,
                       appointment_mode = ?
                 WHERE id = ? AND agrologist_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssii', $response, $appointment_date, $appointment_mode, $request_id, $agrologist_id);

    } else { // declined
        $sql = "UPDATE bookings
                   SET message = CONCAT(message, '\n\nAgrologist Reply: ', ?),
                       status  = 'Declined'
                 WHERE id = ? AND agrologist_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sii', $response, $request_id, $agrologist_id);
    }

    if ($stmt->execute()) {
        $_SESSION['flash'] = 'রেসপন্স সফলভাবে আপডেট হয়েছে!';
    } else {
        $_SESSION['flash'] = 'ত্রুটি: ' . $conn->error;
    }

    header('Location: agrologist.php');
    exit();
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
            color: white; /* text color */
        }

        .sidebar ul li i {
            margin-right: 15px;
            min-width: 35px;
            text-align: center;
            color: white; /* icon color */
        }

        /* Hover effect with darker background to maintain white text visibility */
        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* Optional: if you're using anchor tags inside li */
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .sidebar ul li a:hover {
            color: white;
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
        <li><a href="A_agro_article.php"><i class="fas fa-pen"></i><span class="d-none d-md-inline">Articals</span></a></li>
        <li><a href="farmer_request.php"><i class="fas fa-seedling"></i><span class="d-none d-md-inline"> Farmer Requests</span></a></li>
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
            <p><strong>Appointment Type:</strong> <?php echo $row['appointment_mode']; ?></p>

            <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
            <?php if ($row['status'] === 'pending'): ?>
                <button class="btn btn-reply btn-sm" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $row['id']; ?>">Reply</button>
            <?php endif; ?>
        </div>

      <!-- Reply Modal -->
      <div class="modal fade" id="replyModal<?php echo $row['id']; ?>" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content bg-dark text-light">
                  <div class="modal-header">
                      <h5 class="modal-title">Respond to Farmer Request</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <form method="post">
                      <div class="modal-body">
                          <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">

                          <div class="mb-3">
                              <label class="form-label">Decision:</label>
                              <select name="decision" class="form-select" required onchange="handleDecisionChange(this, <?php echo $row['id']; ?>)">
                                  <option value="">-- Select --</option>
                                  <option value="accepted">Accept</option>
                                  <option value="declined">Decline</option>
                              </select>
                          </div>

                          <div id="acceptFields<?php echo $row['id']; ?>" style="display: none;">
                              <div class="mb-3">
                                  <label class="form-label">Appointment Time:</label>
                                  <input type="datetime-local" name="appointment_time" class="form-control">
                              </div>

                              <?php if ($row['appointment_mode'] == 'offline'): ?>

                              <?php endif; ?>
                          </div>

                          <div class="mb-3">
                              <label class="form-label">Optional Message:</label>
                              <textarea name="response" class="form-control" rows="3"></textarea>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="submit" name="reply" class="btn btn-primary">Submit</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

    <?php endwhile; ?>

</div>





<script>
function handleDecisionChange(select, id) {
    const acceptFields = document.getElementById('acceptFields' + id);
    if (select.value === 'accepted') {
        acceptFields.style.display = 'block';
    } else {
        acceptFields.style.display = 'none';
    }
}

function toggleLocationField(modeSelect, id) {
    const locationField = document.getElementById('locationField' + id);
    if (modeSelect.value === 'offline') {
        locationField.style.display = 'block';
    } else {
        locationField.style.display = 'none';
    }
}
</script>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
