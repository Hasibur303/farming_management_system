<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $farmer_id = $_SESSION['user_id'];

       $photo_name = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $photo_name = time() . '_' . basename($_FILES['photo']['name']);
            $target_path = $upload_dir . $photo_name;
            move_uploaded_file($_FILES['photo']['tmp_name'], $target_path);
        }

   // Insert post with photo
    $sql = "INSERT INTO help_posts (farmer_id, title, content, photo)
            VALUES ('$farmer_id', '$title', '$content', " . ($photo_name ? "'$photo_name'" : "NULL") . ")";
    mysqli_query($conn, $sql);

    header("Location: help_post.php");
    exit();
}

// Fetch all posts with farmer name
$posts = mysqli_query($conn, "
    SELECT hp.*, u.name AS farmer_name
    FROM help_posts hp
    JOIN users u ON hp.farmer_id = u.user_id
    ORDER BY hp.created_at DESC
");

// Fetch all comments with agrologist name and photo
$comments_result = mysqli_query($conn, "
    SELECT c.*, u.name AS agrologist_name, a.photo
    FROM help_comments c
    JOIN users u ON c.user_id = u.user_id
    LEFT JOIN agrologists a ON c.user_id = a.user_id
");

$comments = [];
while ($comment = mysqli_fetch_assoc($comments_result)) {
    $comments[$comment['post_id']][] = $comment;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Help Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #e0f2f1;
            font-family: 'Segoe UI', sans-serif;
        }
        header{
              background:linear-gradient(to right,#2E7D32,#66BB6A);
              color:#fff;padding:1rem 3rem;display:flex;justify-content:space-between;
              align-items:center;position:fixed;top:0;left:0;right:0;z-index:1000;
              box-shadow:0 4px 8px rgba(0,0,0,.1);border-bottom:1px solid #2E7D32}
            header h1{font-size:1.75rem;font-weight:600;letter-spacing:.5px;margin:0;flex-grow:1;text-align:center}
            .user-info{display:flex;align-items:center;font-weight:500;color:#fff}
            .user-info span{font-size:1rem;margin-right:15px}
            .user-info a{
              background:#d32f2f;color:#fff;padding:8px 16px;border-radius:5px;
              font-weight:600;text-decoration:none;transition:.3s;
              box-shadow:0 4px 8px rgba(0,0,0,.1)}
            .user-info a:hover{background:#c62828;transform:translateY(-3px)}

            @media(max-width:768px){
              header{flex-direction:column;padding:1rem 2rem}
              header h1{font-size:1.6rem;margin-bottom:10px}
              .user-info{margin-top:10px}
            }

        /* ---------- Sidebar ---------- */
            .sidebar{
              width:60px;height:100vh;position:fixed;top:0;left:0;background:#1f2937;
              color:#fff;padding:20px 10px;overflow-y:auto;transition:width .3s;z-index:999}
            .sidebar:hover{width:250px}
            .sidebar a{
              display:flex;align-items:center;justify-content:center;color:#b0bec5;
              text-decoration:none;padding:12px 20px;border-radius:5px;margin-bottom:10px;
              font-weight:500;transition:.3s}
            .sidebar a:hover{
              background:#3b4a59;color:#fff;box-shadow:0 4px 8px rgba(0,0,0,.1);transform:translateX(5px)}
            .sidebar a.active{background:#324152;color:#fff}
            .sidebar a .icon{width:30px;text-align:center;margin-right:10px;transition:.3s}
            .sidebar a .text{opacity:0;transition:opacity .3s}
            .sidebar:hover a{justify-content:flex-start}
            .sidebar:hover a .text{opacity:1}
        .form-container, .post-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 80px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        textarea, input[type="text"] {
            border-radius: 10px !important;
        }
        .comment-box {
            margin-top: 15px;
            background: #f1f1f1;
            padding: 10px 15px;
            border-radius: 10px;
        }
        .comment-box img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
        }
        .agrologist-comment {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        h4 {
            color: #00796b;
        }
        .collapse.show {
                    max-height: none !important;
                }
    </style>
</head>
<body class="p-4">
<header>
        <h1>কৃষকদের সাহায্যের পোস্ট</h1>
        <div class="user-info">

            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> লগ আউট</a>
        </div>
    </header>
<!-- ===== Sidebar ===== -->
    <div class="sidebar">
            <ul class="list-unstyled">
                <li><a href="A_previous_response.php"><i class="fas fa-history icon"></i><span class="text">পূর্ববর্তী প্রতিক্রিয়া</span></a></li>
                <li><a href="A_previous_response.php"><i class="fas fa-history icon"></i><span class="text">পূর্ববর্তী প্রতিক্রিয়া</span></a></li>
                <li><a href="help_post.php"><i class="fas fa-question-circle icon"></i><span class="text">সাহায্যের পোস্ট</span></a></li>
                <li><a href="farmer.php"><i class="fas fa-tachometer-alt icon"></i><span class="text">ড্যাশবোর্ড</span></a></li>

                <li><a href="F_Smart_Crop_Doctor.php"><i class="fas fa-stethoscope icon"></i><span class="text">স্মার্ট ফসল ডাক্তার</span></a></li>

                <li><a href="Agrologist_List.php" class="active"><i class="fas fa-tree icon"></i><span class="text">কৃষি-বিশেষজ্ঞ সেবা</span></a></li>

                <li><a href="F_article.php"><i class="fas fa-pen icon"></i><span class="text">প্রবন্ধ পড়ুন</span></a></li>

                <li><a href="crop_management.php"><i class="fas fa-seedling icon"></i><span class="text">ফসল/পণ্য ব্যবস্থাপনা</span></a></li>

                <li><a href="Buy.php"><i class="fas fa-shopping-cart icon"></i><span class="text">সরবরাহকারী থেকে কেনা</span></a></li>

                <li><a href="F_labour_list.php"><i class="fas fa-list icon"></i><span class="text">শ্রমিক তালিকা</span></a></li>

                <li><a href="labour_jobs.php"><i class="fas fa-briefcase icon"></i><span class="text">শ্রমিকের চাকরি</span></a></li>

                <li><a href="farmer_applications.php"><i class="fas fa-file-alt icon"></i><span class="text">শ্রমিকের আবেদন</span></a></li>

                <li><a href="rent_page.php"><i class="fas fa-truck-moving icon"></i><span class="text">ভাড়া পরিষেবা</span></a></li>

                <li><a href="addNewProduct.php"><i class="fas fa-plus-circle icon"></i><span class="text">নতুন পণ্য যোগ</span></a></li>

                <li><a href="farmer/order_management.php"><i class="fas fa-clipboard-list icon"></i><span class="text">অর্ডার ম্যানেজমেন্ট</span></a></li>

                <li><a href="farmer/inventory_management.php"><i class="fas fa-boxes icon"></i><span class="text">ইনভেন্টরি</span></a></li>

                <li><a href="farmer/financial_overview.php"><i class="fas fa-wallet icon"></i><span class="text">আর্থিক সারসংক্ষেপ</span></a></li>

                <li><a href="analytics_report.php"><i class="fas fa-chart-bar icon"></i><span class="text">বিশ্লেষণ ও প্রতিবেদন</span></a></li>

                <li><a href="logout.php"><i class="fas fa-sign-out-alt icon"></i><span class="text">লগ আউট</span></a></li>
            </ul>
        </div>
    <div class="container">
        <!-- Create Post Form -->
        <div class="form-container">
            <h4>Create Help Post</h4>
          <form method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                  <input type="text" name="title" class="form-control" placeholder="Post Title" required>
              </div>
              <div class="mb-3">
                  <textarea name="content" class="form-control" rows="4" placeholder="Describe your problem..." required></textarea>
              </div>
              <div class="mb-3">
                  <label for="photo">Upload Photo (optional):</label>
                  <input type="file" name="photo" class="form-control" accept="image/*">
              </div>
              <button type="submit" class="btn btn-success">Post Help</button>
          </form>
        </div>

        <!-- List of Posts -->
        <h4 class="mb-3">All Help Posts</h4>


        <?php
        while ($post = mysqli_fetch_assoc($posts)):

            $post_id = $post['post_id'];
            $collapse_id = 'collapse-comments-' . $post_id;

            // Use preloaded comments
            $comments_array = isset($comments[$post_id]) ? $comments[$post_id] : [];
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
                <form method="POST" action="A_comment.php" class="mt-3">
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
