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
        .form-container, .post-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
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
