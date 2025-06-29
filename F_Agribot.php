<?php
/* ---------- Server‑side section ---------- */
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ---------- AJAX: return latest record as JSON ---------- */
if (isset($_GET['latest'])) {
    $stmt = $conn->prepare(
        "SELECT temperature, humidity, soil_moisture, recorded_at
         FROM agribot_readings
         WHERE user_id = ?
         ORDER BY recorded_at DESC LIMIT 1");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc() ?: [
        'temperature'   => null,
        'humidity'      => null,
        'soil_moisture' => null,
        'recorded_at'   => null
    ];
    header('Content‑Type: application/json');
    echo json_encode($result);
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Agribot সেন্সর ড্যাসবোর্ড | SmartKirshi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js 4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root{
            --sidebar-collapsed:70px; --sidebar-expanded:240px;
            --bg-dark:#121826; --bg-sidebar:#0d111b; --bg-card:#1f2937;
            --accent:#2E7D32; --accent-light:#66BB6A; --text-light:#f1f1f1;
        }
        *{box-sizing:border-box;}
        body{margin:0;background:var(--bg-dark);color:var(--text-light);font-family:'Segoe UI',sans-serif;}
        a{text-decoration:none}

        header{height:64px;position:fixed;top:0;left:0;right:0;z-index:998;
            display:flex;justify-content:space-between;align-items:center;padding:12px 24px;
            background:linear-gradient(90deg,var(--accent),var(--accent-light));color:#fff;}
        header h1{margin:0;font-size:1.5rem;}

        .sidebar{position:fixed;top:64px;left:0;bottom:0;width:var(--sidebar-collapsed);
            background:var(--bg-sidebar);overflow:hidden;transition:.3s;width:var(--sidebar-collapsed);}
        .sidebar:hover{width:var(--sidebar-expanded);}
        .sidebar a{display:flex;align-items:center;gap:14px;padding:14px 22px;color:var(--text-light);
            transition:background .2s;font-size:.95rem;white-space:nowrap;}
        .sidebar a:hover,.sidebar a.active{background:#1a2332;}
        .sidebar .icon{width:24px;text-align:center;font-size:1.1rem;}
        .sidebar .text{opacity:0;transition:opacity .25s;}
        .sidebar:hover .text{opacity:1;}

        .main-content{margin-top:64px;margin-left:var(--sidebar-collapsed);padding:20px;
            transition:margin-left .3s;}
        .sidebar:hover~.main-content{margin-left:var(--sidebar-expanded);}

        /* Gauge cards */
        .gauge-card{background:var(--bg-card);border-radius:16px;padding:20px;text-align:center;
            box-shadow:0 2px 6px rgba(0,0,0,.4);height:100%;}
        .gauge-card canvas{max-width:200px;margin:auto;}
        .gauge-title{margin-top:10px;font-weight:600;font-size:1.05rem;}
        .advice{margin-top:12px;font-size:.9rem;color:#ddd;}

        /* Responsive grid */
        @media (min-width:768px){
            .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
        }
    </style>
</head>
<body>
<!-- ---------- Header ---------- -->
<header>
    <h1>Agribot সেন্সর ড্যাশবোর্ড</h1>
    <div>
        স্বাগতম, <?=htmlspecialchars($_SESSION['username'])?>
        <a href="logout.php" class="btn btn-sm btn-danger ms-3">Logout</a>
    </div>
</header>

<!-- ---------- Sidebar ---------- -->
<div class="sidebar">
    <h2 class="text-center pt-4 text-muted" style="font-size:1rem;">মেনু</h2>
    <a href="farmer.php"><i class="fas fa-home icon"></i><span class="text">ড্যাশবোর্ড</span></a>
    <a href="F_Smart_Crop_Doctor.php"><i class="fas fa-stethoscope icon"></i><span class="text">স্মার্ট ফসল ডাক্তার</span></a>
    <a href="F_Agribot.php" class="active"><i class="fas fa-robot icon"></i><span class="text">অ্যাগ্রিবট</span></a>
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

<!-- ---------- Main ---------- -->
<main class="main-content">
    <h2 class="mb-4">লাইভ সেন্সর তথ্য</h2>

    <div class="grid-3" id="gaugeGrid">
        <!-- Gauge cards inserted by JS -->
    </div>
</main>

<script>
/* ---------- Chart helper ---------- */
function buildGauge(ctx, label, value, max, unit){
    return new Chart(ctx,{
        type:'doughnut',
        data:{labels:['',''],
            datasets:[{data:[value, max-value],
                borderWidth:0,
                backgroundColor:['#66BB6A11','#37415144'],
                cutout:'75%'}]},
        options:{
            responsive:true,
            rotation:-90,
            circumference:180,
            plugins:{
                tooltip:{enabled:false},
                legend:{display:false},
                title:{display:false}
            },
            animation:{duration:800},
            layout:{padding:0}
        },
        plugins:[{
            id:'valueText',
            afterDraw(chart){
                const{ctx,chartArea:{top,bottom}}=chart;
                ctx.save();
                ctx.font="600 22px 'Segoe UI'";
                ctx.fillStyle="#f1f1f1";
                ctx.textAlign="center";
                ctx.textBaseline="middle";
                ctx.fillText(value+unit,chart.getDatasetMeta(0).data[0].x,(top+bottom)/1.1);
            }
        }]
    });
}

/* ---------- Advice logic ---------- */
function createAdvice(temp,humid,soil){
    let msgs=[];
    if(temp!==null){
        if(temp>=35) msgs.push("উচ্চ তাপমাত্রা! দুপুরের আগেই সেচ দিন ও আর্দ্রতা পর্যবেক্ষণ করুন।");
        else if(temp<=15) msgs.push("নিম্ন তাপমাত্রা, অতিরিক্ত সেচ এড়িয়ে চলুন।");
    }
    if(soil!==null){
        if(soil<35) msgs.push("মাটি শুষ্ক; এখনই সেচ দিন। শসা, মরিচ জাতীয় ফসল আরও জলচিত্ৰ।");
        else if(soil>70) msgs.push("মাটিতে আর্দ্রতা বেশি; মূলো, কলই শিমের মতো ফসল ভাল হয়।");
    }
    if(humid!==null){
        if(humid>85) msgs.push("বায়ু‑আর্দ্রতা বেশি; ছত্রাকজনিত রোগের ঝুঁকি বাড়ে, নিরীখ করুন।");
    }
    return msgs.join("<br>");
}

/* ---------- Build cards once ---------- */
const grid=document.getElementById("gaugeGrid");
grid.innerHTML=[ 'Temperature (°C)','Humidity (%)','Soil Moisture (%)' ]
    .map(lbl=>`
        <div class="gauge-card">
            <canvas></canvas>
            <div class="gauge-title">${lbl}</div>
            <div class="advice"></div>
        </div>`).join('');
const gauges=[...grid.querySelectorAll('canvas')].map(c=>null);

/* ---------- Refresh function ---------- */
async function refresh(){
    try{
        const res=await fetch("Agribot.php?latest=1");
        const data=await res.json()||{};
        const {temperature:nullTemp,humidity:nullHum,soil_moisture:nullSoil}=data;
        const t=parseFloat(data.temperature)??null,
              h=parseFloat(data.humidity)??null,
              s=parseFloat(data.soil_moisture)??null;

        const vals=[t,h,s], maxes=[50,100,100], units=['°C','%','%'];

        vals.forEach((v,i)=>{
            const canvas=grid.querySelectorAll('canvas')[i];
            if(gauges[i]) gauges[i].destroy();
            gauges[i]=buildGauge(canvas, '', v??0, maxes[i], units[i]);
        });

        /* Advice */
        const adv=createAdvice(t,h,s);
        grid.querySelectorAll('.advice').forEach(el=>{el.innerHTML=adv});
    }catch(e){console.error(e);}
}
refresh();               // initial load
setInterval(refresh,30000); // 30‑second auto‑refresh
</script>
</body>
</html>
