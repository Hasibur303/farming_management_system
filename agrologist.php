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

// Count metrics
$total = mysqli_num_rows($requests);
$responded = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE agrologist_id=$agrologist_id AND status='Responded'"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE agrologist_id=$agrologist_id AND status='Pending'"));

$posts = mysqli_query($conn, "
    SELECT p.*, u.name AS farmer_name
    FROM help_posts p
    JOIN users u ON p.farmer_id = u.user_id
    ORDER BY p.created_at DESC
");



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

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .sidebar ul li a:hover {
            color: #fff;
            text-decoration: none;
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
        .post-box {
            border-left: 5px solid #3498db;
            background: #fefefe;
            transition: transform 0.2s ease;
        }

        .post-box:hover {
            transform: scale(1.01);
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .collapse.show {
            max-height: none !important;
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
        <h2>🌾 Agrologist Dashboard</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-metrics">
        <div class="card-box">
            <h2><?php echo $total; ?></h2>
            <p>Total Requests</p>
        </div>
        <div class="card-box">
            <h2><?php echo $responded; ?></h2>
            <p>Responded</p>
        </div>
        <div class="card-box">
            <h2><?php echo $pending; ?></h2>
            <p>Pending</p>
        </div>
    </div>

    <div class="mt-5">
        <h3 class="text-dark mb-4">📝 Farmer Help Posts</h3>
       <?php

       // Assuming $posts is a result of a query fetching all posts from all farmers
       while ($post = mysqli_fetch_assoc($posts)):
           $post_id = $post['post_id'];
           $collapse_id = 'collapse-comments-' . $post_id;

           // Fetch comments for this post
          $comments_query = mysqli_query($conn, "
              SELECT c.*, u.name AS agrologist_name, a.photo
              FROM help_comments c
              JOIN users u ON c.user_id = u.user_id
              LEFT JOIN agrologists a ON c.user_id = a.user_id

               WHERE c.post_id = $post_id
               ORDER BY c.comment_date DESC
           ");

           $comments_array = [];
           while ($row = mysqli_fetch_assoc($comments_query)) {
               $comments_array[] = $row;
           }
           $comment_count = count($comments_array);
       ?>
           <div class="post-box mb-4 p-4 rounded shadow-sm bg-white">
               <h5 class="text-primary"><?php echo htmlspecialchars($post['title']); ?></h5>
               <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

               <?php if (!empty($post['photo'])): ?>
                   <div class="mt-2">
                       <img src="uploads/<?php echo htmlspecialchars($post['photo']); ?>" class="img-fluid rounded" style="max-width: 100%; max-height: 400px;">
                   </div>
               <?php endif; ?>

               <small class="text-muted">
                   Posted by: <?php echo htmlspecialchars($post['farmer_name']); ?>
                   on <?php echo $post['created_at']; ?>
               </small>


               <!-- Comments -->
               <div class="mt-3">
                   <strong>Agrologist Comments:</strong>
                   <?php if ($comment_count > 0): ?>
                       <!-- Show Latest Comment -->
                       <div class="d-flex align-items-start mt-2">
                           <img src="uploads/<?php echo $comments_array[0]['photo']; ?>" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                           <div>
                               <strong><?php echo htmlspecialchars($comments_array[0]['agrologist_name']); ?></strong><br>
                               <span><?php echo nl2br(htmlspecialchars($comments_array[0]['comment'])); ?></span>
                           </div>
                       </div>

                       <!-- More Comments Collapsible -->
                       <?php if ($comment_count > 1): ?>
                           <div class="collapse mt-2 bg-light p-2" id="<?php echo $collapse_id; ?>">
                               <?php for ($i = 1; $i < $comment_count; $i++): ?>
                                   <div class="d-flex align-items-start mt-2">
                                       <img src="uploads/<?php echo $comments_array[$i]['photo']; ?>" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                       <div>
                                           <strong><?php echo htmlspecialchars($comments_array[$i]['agrologist_name']); ?></strong><br>
                                           <span><?php echo nl2br(htmlspecialchars($comments_array[$i]['comment'])); ?></span>
                                       </div>
                                   </div>
                               <?php endfor; ?>
                           </div>

                           <!-- Toggle View All -->
                           <a class="text-primary mt-1 d-block" style="cursor: pointer;" data-bs-toggle="collapse" href="#<?php echo $collapse_id; ?>" role="button" aria-expanded="false" aria-controls="<?php echo $collapse_id; ?>">
                               View all <?php echo $comment_count; ?> comments
                           </a>
                       <?php endif; ?>
                   <?php else: ?>
                       <p class="text-muted">No comments yet.</p>
                   <?php endif; ?>
               </div>

               <!-- Comment Form -->
               <form method="POST" action="comment.php" class="mt-3">
                   <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                   <textarea name="comment" class="form-control mb-2" placeholder="Write a comment..." required></textarea>
                   <button type="submit" class="btn btn-sm btn-primary">Add Comment</button>
               </form>
           </div>
       <?php endwhile; ?>

</div>


</div>







<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
