<?php
declare(strict_types=1);

if (defined('SMARTKRISHI_FARMER_SIDEBAR_RENDERED')) {
    return;
}
define('SMARTKRISHI_FARMER_SIDEBAR_RENDERED', true);

$scriptDirectory = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
$projectBase = str_ends_with(strtolower($scriptDirectory), '/farmer')
    ? str_replace('\\', '/', dirname($scriptDirectory))
    : $scriptDirectory;
$projectBase = rtrim($projectBase, '/');

$farmerNavItems = [
    ['farmer.php', 'fa-home', 'ড্যাশবোর্ড'],
    ['F_Smart_Crop_Doctor.php', 'fa-stethoscope', 'স্মার্ট ফসল ডাক্তার'],
    ['F_Doctor.php', 'fa-leaf', 'রোগ শনাক্তকরণ'],
    ['F_insects.php', 'fa-bug', 'কীটপতঙ্গ সনাক্তকরণ'],
    ['F_Agribot.php', 'fa-robot', 'অ্যাগ্রিবট'],
    ['Agrologist_List.php', 'fa-user-md', 'কৃষি-বিশেষজ্ঞ সেবা'],
    ['F_article.php', 'fa-newspaper', 'কৃষি প্রবন্ধ'],
    ['F_chatbot.php', 'fa-comments', 'এআই চ্যাট বট'],
    ['crop_management.php', 'fa-seedling', 'ফসল ও পণ্য'],
    ['Buy.php', 'fa-shopping-cart', 'সরবরাহকারীর কাছ থেকে কিনুন'],
    ['F_labour_list.php', 'fa-users', 'শ্রমিক তালিকা'],
    ['labour_jobs.php', 'fa-briefcase', 'চাকরির পোস্ট'],
    ['farmer_applications.php', 'fa-file-alt', 'শ্রমিকের আবেদন'],
    ['rent_page.php', 'fa-tools', 'ভাড়ার সেবা'],
    ['addNewProduct.php', 'fa-plus-circle', 'নতুন পণ্য'],
    ['farmer/order_management.php', 'fa-clipboard-list', 'অর্ডার ম্যানেজমেন্ট'],
    ['farmer/inventory_management.php', 'fa-boxes', 'ইনভেন্টরি'],
    ['farmer/financial_overview.php', 'fa-wallet', 'আর্থিক সারসংক্ষেপ'],
    ['analytics_report.php', 'fa-chart-bar', 'বিশ্লেষণ ও প্রতিবেদন'],
];

$currentPath = ltrim(str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '')), '/');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    body.has-farmer-common-sidebar {
        padding-left: 72px !important;
    }
    body.has-farmer-common-sidebar .sidebar:not(.farmer-common-sidebar) {
        display: none !important;
    }
    .farmer-common-sidebar {
        box-sizing: border-box !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 64px !important;
        height: 100vh !important;
        padding: 14px 7px !important;
        margin: 0 !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        background: #1f2937 !important;
        color: #fff !important;
        z-index: 10050 !important;
        transition: width .25s ease !important;
        box-shadow: 2px 0 8px rgba(0, 0, 0, .18) !important;
    }
    .farmer-common-sidebar:hover,
    .farmer-common-sidebar:focus-within {
        width: 260px !important;
    }
    .farmer-common-sidebar .farmer-nav-title {
        height: 38px;
        margin: 0 8px 12px;
        color: #fff;
        font-size: 1.05rem;
        line-height: 38px;
        white-space: nowrap;
        opacity: 0;
        transition: opacity .15s ease;
    }
    .farmer-common-sidebar:hover .farmer-nav-title,
    .farmer-common-sidebar:focus-within .farmer-nav-title {
        opacity: 1;
    }
    .farmer-common-sidebar a {
        box-sizing: border-box !important;
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        min-height: 43px !important;
        margin: 0 0 6px !important;
        padding: 9px 10px !important;
        border-radius: 7px !important;
        color: #cbd5e1 !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        transition: background-color .2s ease, color .2s ease !important;
    }
    .farmer-common-sidebar a:hover,
    .farmer-common-sidebar a:focus,
    .farmer-common-sidebar a.active {
        background: #374151 !important;
        color: #fff !important;
    }
    .farmer-common-sidebar .farmer-nav-icon {
        width: 30px !important;
        min-width: 30px !important;
        margin: 0 12px 0 0 !important;
        text-align: center !important;
        font-size: 1rem !important;
    }
    .farmer-common-sidebar .farmer-nav-text {
        opacity: 0;
        transition: opacity .15s ease;
    }
    .farmer-common-sidebar:hover .farmer-nav-text,
    .farmer-common-sidebar:focus-within .farmer-nav-text {
        opacity: 1;
    }
    body.has-farmer-common-sidebar > .dashboard-feed,
    body.has-farmer-common-sidebar > .main-content,
    body.has-farmer-common-sidebar > .container {
        box-sizing: border-box;
        margin-left: 0 !important;
        max-width: none;
    }
</style>
<script>document.body.classList.add('has-farmer-common-sidebar');</script>
<nav class="sidebar farmer-common-sidebar" aria-label="Farmer navigation">
    <h2 class="farmer-nav-title">ন্যাভিগেশন</h2>
    <?php foreach ($farmerNavItems as [$path, $icon, $label]):
        $href = $projectBase . '/' . $path;
        $active = str_ends_with(strtolower($currentPath), strtolower(ltrim($href, '/')));
    ?>
        <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"<?= $active ? ' class="active" aria-current="page"' : '' ?>>
            <i class="fas <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?> farmer-nav-icon" aria-hidden="true"></i>
            <span class="farmer-nav-text"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
    <?php endforeach; ?>
</nav>
