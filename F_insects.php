<?php
// ========================= F_insects.php (Final) =========================
// Farmer-side insect identification page using Kindwise Insect.id API
// -------------------------------------------------------------------------

session_start();
include 'database.php';
require 'insects_config.php';

$diagnosis = null;
$errorMsg  = null;

// ---------- 1) File upload + API call ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['insect_image'])) {
    $dir = __DIR__ . '/uploads/';
    if (!is_dir($dir)) { mkdir($dir, 0755, true); }

    $relative = 'uploads/' . uniqid('insect_') . '_' . basename($_FILES['insect_image']['name']);
    $absolute = __DIR__ . '/' . $relative;

    if (move_uploaded_file($_FILES['insect_image']['tmp_name'], $absolute)) {
        $diagnosis = identifyInsect($absolute);
        if (!$diagnosis['success']) {
            $errorMsg  = $diagnosis['message'];
            $diagnosis = null;
        }
    } else {
        $errorMsg = "ফাইল আপলোড ব্যর্থ হয়েছে!";
    }
}

// ---------- 2) Kindwise Insect.id API helper ----------
function identifyInsect(string $path): array
{
    if (INSECT_API_KEY === '') {
        return [
            'success' => false,
            'message' => 'Insect identification is not configured. Add INSECT_API_KEY to .env.',
        ];
    }

    $imageB64 = base64_encode(file_get_contents($path));
    $payload = [
        'images'         => [$imageB64],
        'similar_images' => true,
    ];

    $url = INSECT_ENDPOINT . '?details=url,common_names,description,taxonomy';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Api-Key: ' . INSECT_API_KEY,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $raw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    file_put_contents(__DIR__ . '/debug_response_insect.json', $raw);

    if ($err) return ['success'=>false,'message'=>$err];
    if (!in_array($httpCode, [200, 201])) return ['success'=>false,'message'=>"API HTTP $httpCode"];

    $json = json_decode($raw, true);
    if (!$json) return ['success'=>false,'message'=>'Invalid JSON'];

    return ['success'=>true,'data'=>$json];
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <title>কীটপতঙ্গ শনাকতকরণ</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
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
    * { box-sizing: border-box; }
    body { margin: 0; background: var(--bg-dark); color: var(--text-light); font-family: 'Segoe UI', sans-serif; }
    a { text-decoration: none; }
    header {
      height: 64px; position: fixed; top: 0; left: 0; right: 0; z-index: 999;
      display: flex; justify-content: space-between; align-items: center;
      padding: 12px 24px; background: linear-gradient(90deg, var(--accent), var(--accent-light)); color: #fff;
    }
    header h1 { margin: 0; font-size: 1.5rem; }
    .sidebar {
      position: fixed; top: 64px; left: 0; bottom: 0; z-index: 998;
      width: var(--sidebar-collapsed); overflow: hidden;
      background: var(--bg-sidebar); transition: width .3s;
    }
    .sidebar:hover { width: var(--sidebar-expanded); }
    .sidebar h2 {
      color: #9aa4b4; opacity: .6; margin: 0; padding: 24px 20px 10px;
      font-size: 1rem; text-transform: uppercase;
    }
    .sidebar a {
      display: flex; align-items: center; gap: 14px; padding: 14px 22px;
      color: var(--text-light); font-size: .95rem; white-space: nowrap;
      transition: background .2s;
    }
    .sidebar a:hover, .sidebar a.active { background: #1a2332; }
    .sidebar .icon { width: 24px; text-align: center; font-size: 1.1rem; }
    .sidebar .text { opacity: 0; transition: opacity .25s; }
    .sidebar:hover .text { opacity: 1; }
    .content {
      padding: 90px 32px 32px calc(var(--sidebar-collapsed) + 16px);
      transition: padding-left .3s;
    }
    .sidebar:hover ~ .content { padding-left: calc(var(--sidebar-expanded) + 16px); }
    .card-custom {
      background: var(--bg-card); border: none; border-radius: 12px;
      padding: 24px; color: var(--text-light);
    }
    #preview { display: none; max-width: 300px; border-radius: 8px; margin-top: 16px; }
  </style>
</head>
<body>
  <header>
    <h1>কীটপতঙ্গ শনাকতকরণ</h1>
    <div class="user-info d-flex align-items-center gap-2">
      <span>স্বাগতম, <?= htmlspecialchars($_SESSION['username'] ?? 'ব্যবহারকারী'); ?></span>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </header>
  <div class="sidebar">
      <h2 class="text-center pt-4 text-muted" style="font-size:1rem;">মেনু</h2>
      <a href="farmer.php"><i class="fas fa-home icon"></i><span class="text">ড্যাশবোর্ড</span></a>
      <a href="F_Smart_Crop_Doctor.php"><i class="fas fa-stethoscope icon"></i><span class="text">স্মার্ট ফসল ডাক্তার</span></a>
      <a href="F_Agribot.php" ><i class="fas fa-robot icon"></i><span class="text">অ্যাগ্রিবট</span></a>
       <a href="F_Doctor.php">
                                <i class="fas fa-stethoscope icon"></i>
                                <span class="text">রোগ শনাক্তকরণ</span>
                            </a>
          <a href="F_insects.php"class="active" ><i class="fas fa-bug icon"></i><span class="text">কীটপতঙ্গ সনাক্তকরণ  </span></a>

      <a href="Agrologist_List.php"><i class="fas fa-tree icon"></i><span class="text">কৃষি‑বিশেষজ্ঞদের সেবা</span></a>
          <a href="F_article.php"><i class="fas fa-pen icon"></i><span class="text">প্রবন্ধ</span></a>
          <a href="F_chatbot.php"><i class="fas fa-robot icon"></i><span class="text">এআই চ্যাট বট</span></a>
          <a href="crop_management.php"><i class="fas fa-seedling icon"></i><span class="text">ফসল/পণ্য ব্যবস্থাপনা</span></a>
          <a href="Buy.php"><i class="fas fa-shopping-cart icon"></i><span class="text">কিনুন</span></a>
          <a href="F_labour_list.php"><i class="fas fa-list icon"></i><span class="text">শ্রমিক তালিকা</span></a>
          <a href="labour_jobs.php"><i class="fas fa-briefcase icon"></i><span class="text">চাকরি পোস্ট</span></a>
          <a href="farmer_applications.php"><i class="fas fa-file-signature icon"></i><span class="text">শ্রমিকের আবেদন</span></a>
          <a href="rent_page.php"><i class="fas fa-tractor icon"></i><span class="text">ভাড়ার পরিষেবা</span></a>
          <a href="addNewProduct.php"><i class="fas fa-plus-circle icon"></i><span class="text">নতুন পণ্য</span></a>
          <a href="farmer/order_management.php"><i class="fas fa-clipboard-list icon"></i><span class="text">অর্ডার ম্যানেজমেন্ট</span></a>
          <a href="farmer/inventory_management.php"><i class="fas fa-boxes icon"></i><span class="text">ইনভেন্টরি</span></a>
          <a href="farmer/financial_overview.php"><i class="fas fa-wallet icon"></i><span class="text">আর্থিক সারসংক্ষেপ</span></a>
          <a href="analytics_report.php"><i class="fas fa-chart-bar icon"></i><span class="text">বিশ্লেষণ</span></a>
  </div>
  <div class="content">
    <h2 class="mb-4">Insect Identifier</h2>
    <?php if ($errorMsg): ?>
      <div class="alert alert-danger py-2 px-3 mb-4"><?= $errorMsg; ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
      <label class="form-label">কীটপতঙ্গের ছবি দিন</label>
      <input type="file" name="insect_image" id="insect_image" accept="image/*" class="form-control" required onchange="previewImage(event)">
      <img id="preview" alt="Preview">
      <button type="submit" class="btn btn-success mt-3">Identify</button>
    </form>
    <?php if ($diagnosis):
      $top = $diagnosis['data']['result']['classification']['suggestions'][0] ?? null;
    ?>
    <div class="card-custom">
      <h3>সনাক্তন ফলাফল</h3>
      <?php if ($top): ?>
        <p><strong>নাম:</strong> <?= $top['name']; ?></p>
        <p><strong>নিশ্চয়তা:</strong> <?= round(($top['probability'] ?? 0) * 100, 2); ?>%</p>
        <?php if (!empty($top['details']['description']['value'])): ?>
          <p><strong>বর্ণনা:</strong> <?= $top['details']['description']['value']; ?></p>
        <?php endif; ?>
        <?php if (!empty($top['details']['taxonomy'])): ?>
          <p><strong>Taxonomy:</strong>
            <?= $top['details']['taxonomy']['order'] ?? ''; ?> »
            <?= $top['details']['taxonomy']['family'] ?? ''; ?> »
            <?= $top['details']['taxonomy']['genus'] ?? ''; ?>
          </p>
        <?php endif; ?>
      <?php else: ?>
        <p>কোনো কীটপতঙ্গ সনাক্ত করা যায়নি।</p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <script>
    function previewImage(e) {
      const img = document.getElementById('preview');
      img.src = URL.createObjectURL(e.target.files[0]);
      img.style.display = 'block';
    }
  </script>
</body>
</html>
