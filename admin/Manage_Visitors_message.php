<?php
session_start();
include '../database.php'; // DB connection

// 🔐 Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// 🗑️ Delete single message if requested
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM landing_contact WHERE id = $id");
    header("Location: Manage_Visitors_message.php?msg=deleted");
    exit();
}

// Fetch all messages
$result = $conn->query("SELECT id, name, contact_info, subject, message, submitted_at FROM landing_contact ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>দর্শনার্থীর বার্তা পরিচালনা | SmartKrishi Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-w: 260px;
            --green-1: #28a745;
            --green-2: #218838;
            --blue-dark: #3e4e60;
            --blue-grey: #4b5c6b;
        }
        body {margin:0;font-family:'Segoe UI',sans-serif;background:#f4f7f6;}

        /* Sidebar */
        .sidebar{position:fixed;top:0;left:-210px;height:100vh;width:var(--sidebar-w);background:linear-gradient(to bottom,var(--blue-dark),var(--blue-grey));padding-top:40px;transition:left .3s ease-in-out;z-index:1000;}
        .sidebar:hover{left:0;}
        .sidebar .nav-link{display:flex;justify-content:space-between;align-items:center;color:#fff;padding:15px 20px;font-size:16px;text-decoration:none;transition:background .3s,padding-left .3s;}
        .sidebar .nav-link i{margin-left:10px;order:1;}
        .sidebar .nav-link:hover,.sidebar .nav-link.active{background:linear-gradient(to right,#007bff,#0056b3);}

        /* Header */
        header{background:linear-gradient(to right,var(--green-1),var(--green-2));color:#fff;padding:15px 30px;text-align:center;border-bottom:3px solid #1e7e34;position:sticky;top:0;z-index:900;}
        header h1{margin:0;font-size:22px;}
        header a{color:#fff;text-decoration:none;font-size:14px;}
        header a:hover{text-decoration:underline;}

        /* Main */
        .main-content{margin-left:0;padding:30px;transition:margin-left .3s ease;}
        .sidebar:hover ~ .main-content{margin-left:var(--sidebar-w);}
        .table thead{background:#28a745;color:#fff;}
        .table tbody tr:hover{background:#eaf7ea;}
        .truncate{max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-home"></i><span>অ্যাডমিন ড্যাশবোর্ড</span></a></li>
            <li class="nav-item"><a class="nav-link" href="../analytics/analytics.php"><i class="fas fa-chart-bar"></i><span>বিশ্লেষণ</span></a></li>
            <li class="nav-item"><a class="nav-link" href="./performance.php"><i class="fas fa-chart-line"></i><span>কর্মক্ষমতা</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="Manage_Visitors_message.php"><i class="fas fa-users"></i><span>দর্শনার্থীর বার্তা</span></a></li>
            <li class="nav-item"><a class="nav-link" href="manage_farmers.php"><i class="fas fa-user-tie"></i><span>কৃষক পরিচালনা</span></a></li>
            <li class="nav-item"><a class="nav-link" href="manage_suppliers.php"><i class="fas fa-truck"></i><span>সরবরাহকারী পরিচালনা</span></a></li>
            <li class="nav-item"><a class="nav-link" href="manage_products.php"><i class="fas fa-seedling"></i><span>পণ্য পরিচালনা</span></a></li>
            <li class="nav-item"><a class="nav-link" href="manage_customers.php"><i class="fas fa-user-friends"></i><span>গ্রাহক পরিচালনা</span></a></li>
            <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>লগআউট</span></a></li>
        </ul>
    </div>

    <!-- Header -->
    <header>
        <h1>দর্শনার্থীর পাঠানো বার্তা</h1>
        <a href="../logout.php">লগআউট</a>
    </header>

    <!-- Main -->
    <div class="main-content container-fluid">
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                বার্তা সফলভাবে মুছে ফেলা হয়েছে।
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-success text-white fw-bold">সকল বার্তা</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>নাম</th>
                                <th>যোগাযোগ</th>
                                <th>বিষয়</th>
                                <th>বার্তা</th>
                                <th>তারিখ</th>
                                <th class="text-center">কর্ম</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id']; ?></td>
                                        <td><?= htmlspecialchars($row['name']); ?></td>
                                        <td><?= htmlspecialchars($row['contact_info']); ?></td>
                                        <td class="truncate" title="<?= htmlspecialchars($row['subject']); ?>"><?= htmlspecialchars($row['subject']); ?></td>
                                        <td class="truncate" title="<?= htmlspecialchars($row['message']); ?>"><?= htmlspecialchars($row['message']); ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime($row['submitted_at'])); ?></td>
                                        <td class="text-center">
                                            <a href="#viewModal" data-bs-toggle="modal" data-bs-target="#view<?= $row['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('আপনি কি নিশ্চিত মুছে ফেলতে চান?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <!-- View Modal -->
                                    <div class="modal fade" id="view<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">বার্তার বিস্তারিত</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>নাম:</strong> <?= htmlspecialchars($row['name']); ?></p>
                                                    <p><strong>যোগাযোগ:</strong> <?= htmlspecialchars($row['contact_info']); ?></p>
                                                    <p><strong>বিষয়:</strong> <?= htmlspecialchars($row['subject']); ?></p>
                                                    <p><strong>বার্তা:</strong><br><?= nl2br(htmlspecialchars($row['message'])); ?></p>
                                                    <p><strong>পাঠানো হয়েছে:</strong> <?= date('d-m-Y H:i', strtotime($row['submitted_at'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4">কোনো বার্তা পাওয়া যায়নি।</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>