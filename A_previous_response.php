<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];
// Get farmer's district
$farmer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT district FROM agrologists WHERE user_id='$farmer_id'"));
$farmer_district = $farmer['district'] ?? '';
// Get all agrologists with any booking info (if any) made by this farmer
$query = "SELECT a.*, u.name, b.id as booking_id, b.status, b.appointment_date, b.appointment_mode, b.message as agrologist_reply
          FROM agrologists a
          JOIN users u ON a.user_id = u.user_id
          LEFT JOIN bookings b ON a.user_id = b.agrologist_id AND b.farmer_id = '$farmer_id'
          WHERE a.user_id != '$farmer_id'";
$result = mysqli_query($conn, $query);




?>

<!DOCTYPE html>
<html>
<head>
    <title>Responses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f8ff;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .card:hover {
            box-shadow: 0 0 20px rgba(0,255,200,0.2);
        }
        .photo {
            height: 100px;
            width: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
        .btn-book {
            background-color: #28a745;
            color: white;
            border-radius: 20px;
        }
    </style>
</head>


<body class="p-4">
    <div class="container">
       <?php while ($row = mysqli_fetch_assoc($result)): ?>
           <div class="col-md-4 mb-4">
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

                   <!-- Show booking form if not responded -->
                   <?php if (empty($row['booking_id']) || $row['status'] === 'Pending'): ?>
                       <form action="A_book_agrologist.php" method="post" class="mt-3">
                           <input type="hidden" name="agrologist_id" value="<?php echo $row['user_id']; ?>">
                           <div class="mb-2">
                               <textarea name="message" class="form-control" placeholder="Write a message..." required></textarea>
                           </div>
                           <button type="submit" class="btn btn-book w-100">Request Appointment</button>
                       </form>
                   <?php else: ?>
                       <!-- Show response -->
                       <div class="mt-3 p-2 bg-light rounded">
                           <p class="mb-1"><strong>Status:</strong> <?php echo $row['status']; ?></p>
                           <p class="mb-1"><strong>Appointment:</strong> <?php echo date('M d, Y h:i A', strtotime($row['appointment_date'])); ?></p>
                           <p class="mb-1"><strong>Mode:</strong> <?php echo $row['appointment_mode']; ?></p>
                           <p><strong>Response:</strong> <?php echo nl2br($row['agrologist_reply']); ?></p>
                       </div>
                   <?php endif; ?>
               </div>
           </div>
       <?php endwhile; ?>

        </div>
    S
</body>
</html>
