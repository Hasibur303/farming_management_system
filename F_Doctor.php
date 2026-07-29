<?php
// ================= Smart Crop Doctor =================
// Author: ChatGPT (generated code)
// -----------------------------------------------------
// PHP pre‑processing (session, DB, Kindwise API call)
//------------------------------------------------------

session_start();
include 'database.php';     // your DB connection (if needed for logging)
require 'config.php';        // contains KINDWISE_API_KEY & KINDWISE_ENDPOINT

$diagnosis = null;
$errorMsg  = null;

// ---------- 1) File upload & API Call ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['crop_image'])) {
    $dir = __DIR__ . '/uploads/';          // absolute path safer
    if (!is_dir($dir)) { mkdir($dir, 0755, true); }

    $newNameRel = 'uploads/' . uniqid('crop_') . '_' . basename($_FILES['crop_image']['name']); // relative path for later use
    $newNameAbs = __DIR__ . '/' . $newNameRel;

    if (move_uploaded_file($_FILES['crop_image']['tmp_name'], $newNameAbs)) {
        $diagnosis = identifyCrop($newNameAbs);   // Kindwise API
        if (!$diagnosis['success']) {
            $errorMsg  = $diagnosis['message'];
            $diagnosis = null;
        }
    } else {
        $errorMsg = "ছবি আপলোডে সমস্যা হয়েছে।";
    }
}

// ---------- 2) Kindwise API helper ----------
function identifyCrop(string $path): array
{
    if (KINDWISE_API_KEY === '') {
        return [
            'success' => false,
            'message' => 'Crop identification is not configured. Add KINDWISE_API_KEY to .env.',
        ];
    }

    $cfile  = new CURLFile($path, mime_content_type($path), basename($path));
    $fields = ['images' => $cfile /* ,'similar_images' => 'false' */];

    $ch = curl_init(KINDWISE_ENDPOINT);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Api-Key: ' . KINDWISE_API_KEY],
        CURLOPT_POSTFIELDS     => $fields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $raw      = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    file_put_contents(__DIR__ . '/debug_response.json', $raw); // debug log

    if ($err)            return ['success'=>false,'message'=>$err];
    if (!in_array($httpCode, [200,201]))
                        return ['success'=>false,'message'=>"API HTTP $httpCode"];
    $json = json_decode($raw, true);
    if (!$json)          return ['success'=>false,'message'=>'Invalid JSON returned'];
    return ['success'=>true,'data'=>$json];
}

// -----------------------------------------------------
// HTML + CSS + JS  (Single page, no external CSS needed)
// -----------------------------------------------------
?>
<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>স্মার্ট ফসল ডাক্তার</title>
    <!-- Bootstrap & FontAwesome CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        /* ---------- Theme Variables ---------- */
        :root {
            --sidebar-collapsed: 70px;
            --sidebar-expanded: 240px;
            --bg-dark: #121826;
            --bg-sidebar: #0d111b;
            --bg-card: #1f2937;
            --accent: #2E7D32;
            --accent-light: #66BB6A;
            --text-light: #f1f1f1;
        }

        /* ---------- Base ---------- */
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg-dark);color:var(--text-light);font-family:'Segoe UI',sans-serif}
        a{text-decoration:none}

        /* ---------- Header ---------- */
        header{
            height:64px;position:fixed;top:0;left:0;right:0;z-index:999;
            display:flex;justify-content:space-between;align-items:center;
            padding:12px 24px;background:linear-gradient(90deg,var(--accent),var(--accent-light));
            color:#fff
        }
        header h1{margin:0;font-size:1.5rem}

        /* ---------- Sidebar ---------- */
        .sidebar{
            position:fixed;top:64px;left:0;bottom:0;z-index:998;
            width:var(--sidebar-collapsed);overflow:hidden;background:var(--bg-sidebar);
            transition:width .3s
        }
        .sidebar:hover{width:var(--sidebar-expanded)}
        .sidebar h2{color:#9aa4b4;opacity:.6;margin:0;padding:24px 20px 10px;font-size:1rem;text-transform:uppercase}
        .sidebar a{display:flex;align-items:center;gap:14px;padding:14px 22px;color:var(--text-light);font-size:.95rem;white-space:nowrap;transition:background .2s}
        .sidebar a:hover,.sidebar a.active{background:#1a2332}
        .sidebar .icon{width:24px;text-align:center;font-size:1.1rem}
        .sidebar .text{opacity:0;transition:opacity .25s}
        .sidebar:hover .text{opacity:1}

        /* ---------- Main Content ---------- */
        .content{
            padding:90px 32px 32px calc(var(--sidebar-collapsed) + 16px);
            transition:padding-left .3s;
        }
        .sidebar:hover ~ .content{padding-left:calc(var(--sidebar-expanded) + 16px)}

        /* ---------- Card ---------- */
        .card-custom{
            background:var(--bg-card);
            border:none;
            border-radius:12px;
            padding:24px;
            color:var(--text-light);
        }
        .card-custom h3{margin-top:0;margin-bottom:16px}

        /* ---------- File input preview ---------- */
        #preview{display:none;max-width:300px;border-radius:8px;margin-top:16px}
    </style>
</head>
<body>
<!-- ---------- Header ---------- -->
<header>
    <h1>স্মার্ট ফসল ডাক্তার</h1>
    <div class="user-info d-flex align-items-center gap-2">
        <span>স্বাগতম, <?= htmlspecialchars($_SESSION['username'] ?? 'ব্যবহারকারী'); ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</header>

<!-- ---------- Sidebar ---------- -->
<div class="sidebar">
    <h2>ন্যাভিগেশন</h2>
    <a href="farmer.php"><i class="fas fa-wallet icon"></i><span class="text">ড্যাশবোর্ড</span></a>
    <a href="F_Agribot.php"><i class="fas fa-robot icon"></i><span class="text">অ্যাগ্রিবট</span></a>
    <a href="F_Smart_Crop_Doctor.php" class="active"><i class="fas fa-stethoscope icon"></i><span class="text">স্মার্ট ফসল ডাক্তার</span></a>
    <a href="Agrologist_List.php"><i class="fas fa-tree icon"></i><span class="text">কৃষি‑বিশেষজ্ঞদের সেবা</span></a>
    <a href="F_article.php"><i class="fas fa-pen icon"></i><span class="text">প্রবন্ধ</span></a>
    <a href="F_chatbot.php"><i class="fas fa-robot icon"></i><span class="text">এআই চ্যাট বট</span></a>
    <a href="crop_management.php"><i class="fas fa-seedling icon"></i><span class="text">ফসল/পণ্য</span></a>
    <a href="Buy.php"><i class="fas fa-shopping-cart icon"></i><span class="text">কিনুন</span></a>
    <a href="F_labour_list.php"><i class="fas fa-list icon"></i><span class="text">শ্রমিক তালিকা</span></a>
    <a href="labour_jobs.php"><i class="fas fa-briefcase icon"></i><span class="text">চাকরি পোস্ট</span></a>
    <a href="farmer_applications.php"><i class="fas fa-file-signature icon"></i><span class="text">শ্রমিক আবেদন</span></a>
    <a href="rent_page.php"><i class="fas fa-tractor icon"></i><span class="text">ভাড়ার পরিষেবা</span></a>
    <a href="addNewProduct.php"><i class="fas fa-plus-circle icon"></i><span class="text">নতুন পণ্য</span></a>
    <a href="farmer/order_management.php"><i class="fas fa-clipboard-list icon"></i><span class="text">অর্ডার ম্যানেজ</span></a>
    <a href="farmer/inventory_management.php"><i class="fas fa-boxes icon"></i><span class="text">ইনভেন্টরি</span></a>
    <a href="farmer/financial_overview.php"><i class="fas fa-wallet icon"></i><span class="text">আর্থিক</span></a>
    <a href="analytics_report.php"><i class="fas fa-chart-bar icon"></i><span class="text">বিশ্লেষণ</span></a>
</div>

<!-- ---------- Main Content ---------- -->
<div class="content">
    <h2 class="mb-4">Smart Crop Doctor</h2>

    <?php if ($errorMsg): ?>
        <div class="alert alert-danger py-2 px-3 mb-4"><?= $errorMsg; ?></div>
    <?php endif; ?>

    <!-- Upload form -->
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <label class="form-label">ফসলের ছবি দিন</label>
        <input type="file" name="crop_image" id="crop_image" accept="image/*" class="form-control" required onchange="previewImage(event)">
        <img id="preview" alt="Preview">
        <button type="submit" class="btn btn-success mt-3">Diagnose</button>
    </form>

    <?php if ($diagnosis):
        $cropSug = $diagnosis['data']['result']['crop']['suggestions'][0] ?? null;
        $disease = $diagnosis['data']['result']['disease']['suggestions'][0] ?? null;
    ?>
        <div class="card-custom">
            <h3>রোগ শনাক্তকরণ ফলাফল</h3>
            <?php if ($cropSug): ?>
                <p><strong>ফসল:</strong> <?= $cropSug['name']; ?> (<?= $cropSug['scientific_name']; ?>)</p>
            <?php endif; ?>
            <?php if ($disease): ?>
                <p><strong>সম্ভাব্য রোগ/পেস্ট:</strong> <?= $disease['name']; ?> (<?= $disease['scientific_name']; ?>)</p>
                <p><strong>নিশ্চয়তা:</strong> <?= round($disease['probability'] * 100, 2); ?>%</p>
                <?php if (!empty($disease['details']['treatment'])): ?>
                    <p class="mb-1"><strong>চিকিৎসা/দমন কৌশল:</strong></p>
                    <ul class="mb-0">
                        <?php foreach ($disease['details']['treatment'] as $type => $tip): ?>
                            <li><em><?= ucfirst($type); ?>:</em> <?= $tip; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <p>কোন রোগ সনাক্ত করা যায়নি।</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>




<!-- ---------- Scripts ---------- -->
<script>
function previewImage(e){
    const img=document.getElementById('preview');
    img.src=URL.createObjectURL(e.target.files[0]);
    img.style.display='block';
}
</script>
</body>
</html>
