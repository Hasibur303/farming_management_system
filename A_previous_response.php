<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

/* কৃষকের জেলা বের করা */
$farmer = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT district FROM agrologists WHERE user_id='$farmer_id'"));
$farmer_district = $farmer['district'] ?? '';

/* কৃষক যাদের বুক করেছেন / করবেন এমন সব কৃষি-বিশেষজ্ঞ */
$query = "SELECT a.*, u.name,
                 b.id          AS booking_id,
                 b.status,
                 b.appointment_date,
                 b.appointment_mode,
                 b.message     AS agrologist_reply
          FROM agrologists a
          JOIN users u ON a.user_id = u.user_id
          LEFT JOIN bookings b
                 ON a.user_id = b.agrologist_id
                AND b.farmer_id = '$farmer_id'
          WHERE a.user_id != '$farmer_id'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পূর্ববর্তী প্রতিক্রিয়া – স্মার্টকৃষি</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap ও Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
    /* ---------- Global ---------- */
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body{background:#f7f8fa;color:#333;padding-top:70px}

    /* ---------- Header ---------- */
    header{
      background:linear-gradient(to right,#2E7D32,#66BB6A);
      color:#fff;padding:1rem 3rem;display:flex;justify-content:space-between;
      align-items:center;position:fixed;top:0;left:0;right:0;z-index:1000;
      box-shadow:0 4px 8px rgba(0,0,0,.1);border-bottom:1px solid #2E7D32}
    header h1{font-size:1.75rem;font-weight:600;flex-grow:1;text-align:center;margin:0}
    .user-info{display:flex;align-items:center;font-weight:500;color:#fff}
    .user-info a{
      background:#d32f2f;color:#fff;padding:8px 16px;border-radius:5px;
      font-weight:600;text-decoration:none;transition:.3s;
      box-shadow:0 4px 8px rgba(0,0,0,.1)}
    .user-info a:hover{background:#c62828;transform:translateY(-3px)}

    @media(max-width:768px){
      header{flex-direction:column;padding:1rem 2rem}
      header h1{font-size:1.6rem;margin-bottom:10px}
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

    /* ---------- Main ---------- */
    .main-content{margin-left:270px;padding:2rem}
    @media(max-width:768px){
      .sidebar:hover~.main-content{margin-left:180px}
      .main-content{margin-left:60px;padding:1.5rem}
    }

    /* ---------- Cards ---------- */
    .card{
      background:linear-gradient(to bottom right,#1f2b37,#263445);
      border:none;border-radius:15px;box-shadow:0 0 12px rgba(0,213,255,.15);
      color:#fff;transition:.3s}
    .card:hover{box-shadow:0 0 20px rgba(0,255,200,.2);transform:translateY(-3px)}
    .photo{
      height:100px;width:100px;border-radius:50%;object-fit:cover;border:3px solid #00d1b2}
    .card h5{font-size:18px;color:#00d1b2;margin-bottom:5px}
    .card p{font-size:14px;color:#ddd;margin:0}
    .badge.bg-success{background:#2ecc71;font-size:12px;padding:5px 10px;border-radius:20px}

    textarea.form-control{
      background:#1f2b37;border:1px solid #00d1b2;color:#fff;border-radius:10px;resize:none}
    textarea::placeholder{color:#bbb}
    .btn-book{background:#00d1b2;color:#fff;border:none;border-radius:25px;font-weight:bold;transition:.3s}
    .btn-book:hover{background:#00bfa5}
    </style>
</head>

<body>
    <!-- ===== Header ===== -->
    <header>
        <h1>পূর্ববর্তী প্রতিক্রিয়া</h1>
        <div class="user-info">
            <span>স্বাগতম, <?= htmlspecialchars($_SESSION['username'] ?? 'ব্যবহারকারী'); ?></span>
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

    <!-- ===== Main ===== -->
    <div class="main-content">
        <h2 class="mb-4">আপনার কৃষি-বিশেষজ্ঞ সম্পর্কিত প্রতিক্রিয়া ও অ্যাপয়েন্টমেন্ট</h2>

        <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 mb-4">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <img src="uploads/<?= htmlspecialchars($row['photo']); ?>"
                             class="photo me-3" alt="প্রোফাইল ছবি">
                        <div>
                            <h5><?= htmlspecialchars($row['name']); ?></h5>
                            <p>সেক্টর: <?= htmlspecialchars($row['sector']); ?><br>
                               জেলা: <?= htmlspecialchars($row['district']); ?></p>
                            <?php if ($row['district'] == $farmer_district): ?>
                                <span class="badge bg-success">নিকটবর্তী</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- যদি এখনো সাড়া পাননি / Pending -->
                    <?php if (empty($row['booking_id']) || $row['status'] === 'Pending'): ?>
                      <form action="A_book_agrologist.php" method="post" class="mt-3">
                          <input type="hidden" name="agrologist_id"
                                 value="<?= $row['user_id']; ?>">

                          <div class="mb-2">
                              <label class="form-label text-white">বার্তা লিখুন</label>
                              <textarea name="message" class="form-control"
                                        placeholder="আপনার বার্তা লিখুন..." required></textarea>
                          </div>

                          <button type="submit" class="btn btn-book w-100">অ্যাপয়েন্টমেন্ট অনুরোধ</button>
                      </form>

                    <?php else: ?>
                      <!-- ইতোমধ্যে সাড়া পাওয়া -->
                      <div class="mt-3 p-2 rounded text-white" style="background-color: #001f3f;">
                          <p class="mb-1"><strong>অবস্থা:</strong>
                             <?= htmlspecialchars($row['status']); ?></p>
                          <p class="mb-1"><strong>তারিখ / সময়:</strong>
                             <?= date('d M Y, h:i A',
                                      strtotime($row['appointment_date'])); ?></p>
                          <p class="mb-1"><strong>মোড:</strong>
                             <?= htmlspecialchars($row['appointment_mode']); ?></p>
                          <p><strong>জবাব:</strong><br>
                             <?= nl2br(htmlspecialchars($row['agrologist_reply'])); ?></p>
                      </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
