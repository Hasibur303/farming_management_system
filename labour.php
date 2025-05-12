<?php
session_start();
include 'database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$labour_id = $_SESSION['user_id'];

// Language handling
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'bn';
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['bn', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'];

// Language texts
$text = [
    'bn' => [
        'title' => 'শ্রমিকের ড্যাশবোর্ড',
        'welcome' => 'স্বাগতম, শ্রমিক!',
        'update_profile' => 'আপনার প্রোফাইল হালনাগাদ করুন',
        'upload_image' => 'প্রোফাইল ছবি আপলোড করুন:',
        'daily_salary' => 'প্রতিদিনের বেতন (টাকায়):',
        'bio' => 'আপনার সম্পর্কে সংক্ষিপ্ত বিবরণ:',
        'save_profile' => 'প্রোফাইল সংরক্ষণ করুন',
        'job_list' => 'কৃষকদের চাহিদা',
        'farmer_name' => 'কৃষকের নাম',
        'requirement' => 'চাহিদা',
        'location' => 'অবস্থান',
        'no_jobs' => 'এই মুহূর্তে কোনো চাহিদা নেই।',
        'logout' => 'লগ আউট',
        'dashboard' => 'ড্যাশবোর্ড',
        'profile' => 'প্রোফাইল',
        'jobs' => 'কাজের তালিকা',
        'appliedjobs' => 'আবেদনকৃত চাকরির তালিকা',
        'contractrequest' => 'চুক্তির অনুরোধ',
        'notifications' => 'নোটিফিকেশন',
        'settings' => 'সেটিংস',
        'totaljobpost'=> 'মোট চাকরির পদ',
        'jobsyouapplied'=> 'আবেদনকৃত চাকরি',
        'acceptedjobs'=> 'গৃহীত চাকরি',
        'processingjobs'=> 'চাকরি প্রক্রিয়াধীন',
    ],
    'en' => [
        'title' => 'Labour Dashboard',
        'welcome' => 'Welcome, Labourer!',
        'update_profile' => 'Update Your Profile',
        'upload_image' => 'Upload Profile Image:',
        'daily_salary' => 'Daily Salary (in BDT):',
        'bio' => 'Short Description About You:',
        'save_profile' => 'Save Profile',
        'job_list' => 'Farmer Requirements',
        'farmer_name' => 'Farmer Name',
        'requirement' => 'Requirement',
        'location' => 'Location',
        'no_jobs' => 'No job requirements at the moment.',
        'logout' => 'Logout',
        'dashboard' => 'Dashboard',
        'profile' => 'Profile',
        'jobs' => 'Job List',
        'appliedjobs' => 'Applied Job List',
        'contractrequest' => 'Contract Request',
        'notifications' => 'Notifications',
        'settings' => 'Settings',
        'totaljobpost'=> 'Total Job Post',
        'jobsyouapplied'=> 'Jobs You Applied',
        'acceptedjobs'=> 'Accepted Jobs',
        'processingjobs'=> 'Processing Jobs',
    ]
];
$current_text = $text[$lang];

// Function to safely fetch counts
function getCount($conn, $query) {
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return isset($row['total']) ? (int)$row['total'] : 0;
    }
    return 0;
}

// Job metrics
$totalJobs = getCount($conn, "SELECT COUNT(*) AS total FROM labour_jobpost");
$appliedJobs = getCount($conn, "SELECT COUNT(*) AS total FROM job_applications WHERE labour_id = " . intval($labour_id));
$acceptedJobs = getCount($conn, "SELECT COUNT(*) AS total FROM job_applications WHERE labour_id = " . intval($labour_id) . " AND status = 'Accepted'");
$processingJobs = getCount($conn, "SELECT COUNT(*) AS total FROM job_applications WHERE labour_id = " . intval($labour_id) . " AND status = 'Pending'");


// Fetch district and last login of this labour
$labourInfoQuery = mysqli_query($conn, "SELECT district, last_login FROM labour WHERE user_id = $labour_id");

if ($labourInfoQuery && mysqli_num_rows($labourInfoQuery) > 0) {
    $labourInfo = mysqli_fetch_assoc($labourInfoQuery);
    $labourDistrict = $labourInfo['district'];
    $lastLogin = $labourInfo['last_login'];
} else {
    // Handle missing data gracefully
    $labourDistrict = '';
    $lastLogin = '2000-01-01 00:00:00'; // fallback date
}

// Get new job posts in same district after last login
$notificationsQuery = mysqli_query($conn, "
    SELECT * FROM labour_jobpost
    WHERE district = '$labourDistrict'
    AND post_date > '$lastLogin'
    ORDER BY post_date DESC
");

$newJobs = [];
while ($row = mysqli_fetch_assoc($notificationsQuery)) {
    $newJobs[] = $row;
}
?>


<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $current_text['title'] ?> | SmartKirshi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            background: #f0f4f8;
        }
        .sidebar {
            width: 70px;
            background: linear-gradient(180deg, #2E7D32, #1B5E20);
            color: white;
            height: 100vh;
            padding: 20px 10px;
            position: fixed;
            transition: width 0.3s;
            overflow: hidden;
        }
        .sidebar:hover { width: 250px; }
        .sidebar h2 {
            font-size: 24px; margin-bottom: 30px; white-space: nowrap; opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar:hover h2 { opacity: 1; }
        .sidebar a {
            display: flex; align-items: center; padding: 10px;
            color: white; text-decoration: none; border-radius: 8px;
            margin-bottom: 10px; white-space: nowrap;
            background-color: rgba(255,255,255,0.1);
            transition: background 0.2s;
        }
        .sidebar a:hover { background-color: rgba(255,255,255,0.3); }
        .sidebar a span {
            margin-left: 10px; display: none; transition: opacity 0.3s;
        }
        .sidebar:hover a span { display: inline; opacity: 1; }
        .logout-sidebar { color: #ffdddd; background-color: #c62828; }
        .main {
            margin-left: 270px; width: calc(100% - 270px); padding: 20px 40px;
        }
        .top-bar {
            display: flex; justify-content: space-between; align-items: center;
        }
        .logout-button {
            background-color: #d32f2f; border: none; padding: 10px 18px;
            color: white; border-radius: 6px; cursor: pointer; font-weight: bold;
        }
        .logout-button:hover { background-color: #b71c1c; }
        .language-btn { background-color: #388E3C; margin-left: 10px; }
        h2 { color: #2E7D32; }
        .dashboard-metrics {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 40px;
        }

        .metric-card {
            background: #1e1e2f;
            border-radius: 20px;
            padding: 20px;
            width: 220px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            position: relative;
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-8px);
        }

        /* Circular icon box */
        .metric-card .circle {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            animation: pulseGlow 2.5s ease-in-out infinite;
        }

        /* Circle colors for each card */
        .metric-card:nth-child(1) .circle {
            background-color: #e91e63; /* Pink */
        }

        .metric-card:nth-child(2) .circle {
            background-color: #3f51b5; /* Indigo */
        }

        .metric-card:nth-child(3) .circle {
            background-color: #4caf50; /* Green */
        }

        .metric-card:nth-child(4) .circle {
            background-color: #ff9800; /* Orange */
        }

        /* Glow animation */
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 10px rgba(255,255,255,0.2);
            }
            50% {
                box-shadow: 0 0 20px rgba(255,255,255,0.4);
            }
        }

        .metric-card .icon {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .metric-card h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .metric-card p {
            margin-top: 10px;
            font-size: 16px;
            font-weight: 600;
            color: #ccc;
            letter-spacing: 0.5px;
        }

        @keyframes greenPulse {
            0% {
                background: linear-gradient(135deg, #71DC47, #A8E063);
            }
            50% {
                background: linear-gradient(135deg, #50C878, #9BE15D);
            }
            100% {
                background: linear-gradient(135deg, #71DC47, #A8E063);
            }
        }

        .weather-widget {
            position: fixed;
            top: 200px;
            right: 20px;
            width: 250px;
            color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            font-family: 'Segoe UI', sans-serif;
            z-index: 999;
            transition: transform 0.3s ease;

            animation: greenPulse 6s infinite ease-in-out;
            background-size: 400% 400%;
        }

        .weather-widget:hover {
            transform: scale(1.03);
        }

        .weather-header h3 {
            margin: 0 0 5px;
            font-size: 18px;
        }

        .weather-info h2 {
            margin: 10px 0;
            font-size: 36px;
            font-weight: bold;
        }

        .weather-info p {
            margin: 5px 0;
        }

        .weather-box {
            position: fixed;
            bottom: 20px;
            right: 20px;
            color: #fff;
            padding: 20px;
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            font-family: 'Segoe UI', sans-serif;
            z-index: 1000;
            min-width: 220px;
            transition: all 0.3s ease;

            animation: greenPulse 6s infinite ease-in-out;
            background-size: 400% 400%;
        }

        #weather-city {
            font-size: 20px;
            font-weight: 600;
        }

        #weather-temp {
            font-size: 36px;
            font-weight: bold;
            margin: 5px 0;
        }

        #weather-desc {
            font-size: 16px;
            font-style: italic;
        }



       .tip-section {
           max-width: 900px;
           margin: 30px auto; /* Centered */
           padding: 15px 25px;
           border-radius: 15px;
           background: linear-gradient(135deg, #e0ffe0, #c2f7c2);
           box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
           font-family: 'Segoe UI', sans-serif;
           color: #333;
       }

       .tip-section h3 {
           font-size: 20px;
           margin-bottom: 20px;
           font-weight: bold;
           color: #1b5e20;
           text-align: center;
       }

       .tips-container {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
           gap: 15px;
       }

       .tip-card {
           background: #ffffff;
           border-left: 6px solid #4caf50;
           padding: 12px 14px;
           border-radius: 10px;
           box-shadow: 0 2px 8px rgba(0,0,0,0.1);
           display: flex;
           align-items: center;
           gap: 10px;
           transition: transform 0.3s ease;
           min-height: 70px;
       }

       .tip-card i {
           font-size: 22px;
           color: #388e3c;
       }

       .tip-card p {
           margin: 0;
           font-size: 15px;
           line-height: 1.4;
       }

       .tip-card:hover {
           transform: scale(1.03);
       }



        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fefefe;
            padding: 20px 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 450px;
            text-align: center;
            position: relative;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            animation: fadeIn 0.4s ease;
            font-family: 'Segoe UI', sans-serif;
        }

        .modal-content h3 {
            margin-bottom: 15px;
            color: #2e7d32;
            font-size: 22px;
        }

        #modal-tips {
            text-align: left;
            padding-left: 20px;
        }

        #modal-tips li {
            margin-bottom: 10px;
            font-size: 16px;
            color: #444;
            list-style-type: "✅ ";
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 20px;
            cursor: pointer;
            color: #888;
        }

        .close-btn:hover {
            color: #000;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }


    </style>
</head>
<body>

<div class="sidebar">
    <h2>SmartKirshi</h2>
    <a href="labour.php">🏠 <span><?= $current_text['dashboard'] ?></span></a>
    <a href="L_profile.php">🧑‍🌾 <span><?= $current_text['profile'] ?></span></a>
    <a href="L_job.php">📋 <span><?= $current_text['jobs'] ?></span></a>
    <a href="L_contract_message.php">💬 <span><?= $current_text['contractrequest'] ?></span></a>
    <a href="L_apply_job_list.php">📋 <span><?= $current_text['appliedjobs'] ?></span></a>
    <a href="notifications.php">🔔 <span><?= $current_text['notifications'] ?></span></a>
    <a href="settings.php">⚙️ <span><?= $current_text['settings'] ?></span></a>
    <a class="logout-sidebar" href="logout.php">🚪 <span><?= $current_text['logout'] ?></span></a>
</div>

<div class="main">
    <div class="top-bar">
        <h1><?= $current_text['title'] ?></h1>
        <div>
            <a href="?lang=bn"><button class="logout-button language-btn">🇧🇩 Bn</button></a>
            <a href="?lang=en"><button class="logout-button language-btn">🇬🇧 En</button></a>
            <a href="logout.php"><button class="logout-button">🚪 <?= $current_text['logout'] ?></button></a>
        </div>
    </div>

    <div class="dashboard-metrics">
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-briefcase icon"></i>
                <h3><?= $totalJobs ?></h3>
            </div>
            <?= $current_text['totaljobpost'] ?>
        </div>
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-paper-plane icon"></i>
                <h3><?= $appliedJobs ?></h3>
            </div>
            <?= $current_text['jobsyouapplied'] ?>
        </div>
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-check-circle icon"></i>
                <h3><?= $acceptedJobs ?></h3>
            </div>
            <?= $current_text['acceptedjobs'] ?>
        </div>
        <div class="metric-card">
            <div class="circle">
                <i class="fas fa-spinner icon"></i>
                <h3><?= $processingJobs ?></h3>
            </div>
            <?= $current_text['processingjobs'] ?>
        </div>
    </div>

</div>


<?php if (!empty($newJobs)): ?>
    <div class="notification-box" style="background: #1f1f1f; color: #fff; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
        <h3 style="color: #00ff99;">🔔 New Job Notifications</h3>
        <ul>
            <?php foreach ($newJobs as $job): ?>
                <li style="margin-bottom: 10px;">
                    <strong>Caption:</strong> <?= htmlspecialchars($job['caption']) ?><br>
                    <strong>Location:</strong> <?= htmlspecialchars($job['district']) ?><br>
                    <strong>Posted on:</strong> <?= date('d M Y h:i A', strtotime($job['post_date'])) ?>
                </li>
                <hr style="border-color: #333;">
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p style="color: #aaa;">No new job notifications.</p>
<?php endif; ?>



<!-- Weather Widget -->
<div id="weather-widget" class="weather-box">
<h3>🌤️ আবহাওয়ার আপডেট</h3>
    <div id="weather-city"> Loading weather...</div>
    <div id="weather-temp"></div>
    <div id="weather-desc"></div>
    <p>আর্দ্রতা: <span id="humidity">--%</span></p>
          <p>বায়ুর গতি: <span id="wind">-- কিমি/ঘণ্টা</span></p>
    <p>পরবর্তী ২ ঘণ্টার সম্ভাব্য অবস্থা: <span id="next-forecast">লোড হচ্ছে...</span></p>
</div>

<!-- Weather Script in Bangla -->
<script>
const apiKey = "07e734ebc19510e488064f54a0f45dd8"; // OpenWeatherMap API key

// English to Bangla weather mapping
const banglaDescriptions = {
    "clear sky": "পরিষ্কার আকাশ",
    "few clouds": "হালকা মেঘ",
    "scattered clouds": "বিক্ষিপ্ত মেঘ",
    "broken clouds": "ভাঙা মেঘ",
    "overcast clouds": "ঘন মেঘ",
    "shower rain": "বৃষ্টির ঝরনা",
    "light rain": "হালকা বৃষ্টি",
    "moderate rain": "মাঝারি বৃষ্টি",
    "heavy intensity rain": "ভারী বৃষ্টি",
    "rain": "বৃষ্টি",
    "thunderstorm": "বজ্রসহ ঝড়",
    "snow": "তুষারপাত",
    "mist": "কুয়াশা"
};

function translate(desc) {
    return banglaDescriptions[desc.toLowerCase()] || desc;
}

// Fetch current weather
function fetchWeather(lat, lon) {
    fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`)
        .then(res => res.json())
        .then(data => {
            updateWeather(data);
            fetchForecast(data.name); // Call forecast with city name
        })
        .catch(() => fetchWeatherByCity("Dhaka,BD"));
}

function fetchWeatherByCity(city) {
    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`)
        .then(res => res.json())
        .then(data => {
            updateWeather(data);
            fetchForecast(city); // Call forecast with city name
        })
        .catch(() => {
            document.getElementById('weather-city').textContent = "আবহাওয়া তথ্য পাওয়া যায়নি";
        });
}

function updateWeather(data) {
    document.getElementById('weather-city').textContent = `${data.name} এর আবহাওয়া`;
    document.getElementById('weather-temp').textContent = `${data.main.temp}°C`;
    const englishDesc = data.weather[0].description;
    document.getElementById('weather-desc').textContent = `অবস্থা: ${translate(englishDesc)}`;
    document.getElementById('humidity').textContent = `${data.main.humidity}%`;
    document.getElementById('wind').textContent = `${data.wind.speed} কিমি/ঘণ্টা`;

    // ✅ Weather-based tip logic
    const tipElement = document.getElementById("weatherSafetyTip");
    const temp = parseFloat(data.main.temp); // Get actual temperature from API

    if (tipElement) {
        if (temp > 35) {
            tipElement.innerText = "☀️ তাপদাহ চলছে – হালকা পোশাক পরুন এবং বেশি করে পানি পান করুন।";
        } else if (temp > 30) {
            tipElement.innerText = "🌤️ গরম আবহাওয়া – রোদে কাজের সময় টুপি ও পানি সঙ্গে রাখুন।";
        } else if (temp > 20) {
            tipElement.innerText = "⛅ আবহাওয়া সহনীয় – এখন কাজ করার জন্য ভালো সময়।";
        } else if (temp > 10) {
            tipElement.innerText = "🌥️ ঠান্ডা আবহাওয়া – গরম কাপড় পরে কাজ করুন।";
        } else {
            tipElement.innerText = "❄️ প্রচণ্ড ঠান্ডা – যথাসম্ভব গরম পোশাক পরে নিরাপদে থাকুন।";
        }
    }
}


// Fetch forecast for next 2 hours
function fetchForecast(city) {
    fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${city}&appid=${apiKey}&units=metric`)
        .then(res => res.json())
        .then(data => {
            if (data.list && data.list.length >= 2) {
                const desc1 = data.list[0].weather[0].description;
                const desc2 = data.list[1].weather[0].description;
                const banglaDesc1 = translate(desc1);
                const banglaDesc2 = translate(desc2);
                document.getElementById("next-forecast").textContent = `${banglaDesc1} → ${banglaDesc2}`;

                // OPTIONAL: Only include this if you added <span id="rain"> in HTML
                // document.getElementById("rain").textContent = `${Math.round(data.list[0].pop * 100)}%`;
            } else {
                document.getElementById("next-forecast").textContent = "তথ্য নেই";
            }
        })
        .catch(() => {
            document.getElementById("next-forecast").textContent = "পূর্বাভাস লোড হয়নি";
        });
}


if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        pos => fetchWeather(pos.coords.latitude, pos.coords.longitude),
        () => fetchWeatherByCity("Dhaka,BD")
    );
} else {
    fetchWeatherByCity("Dhaka,BD");
}
</script>



<div class="tip-section">
    <h3>🧑‍🌾 প্রশিক্ষণ ও নিরাপত্তা টিপস</h3>
    <div class="tips-container">
        <!-- Tip 1 -->
        <div class="tip-card">
            <i class="fas fa-hard-hat"></i>
            <p>ক্ষেতে নিরাপদ থাকার উপায়</p>
        </div>

        <!-- Tip 2 -->
        <div class="tip-card">
            <i class="fas fa-tools"></i>
            <p>ফসলের যত্নে সেরা সরঞ্জাম</p>
        </div>

        <!-- Weather-Based Tip -->
        <div class="tip-card weather-tip">
            <i class="fas fa-sun"></i>
            <p id="weatherSafetyTip">তাপদাহে কাজের সময় পানি পান করুন</p>
        </div>
    </div>
</div>

<!-- Modal Popup -->
<div id="tipModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h3 id="modal-title"></h3>
    <ul id="modal-tips">
      <!-- Dynamic tips go here -->
    </ul>
  </div>
</div>

<script>
document.querySelectorAll('.tip-card').forEach((card, index) => {
    card.addEventListener('click', () => {
        const modal = document.getElementById("tipModal");
        const title = document.getElementById("modal-title");
        const list = document.getElementById("modal-tips");
        list.innerHTML = ""; // Clear previous tips

        if (index === 0) {
            title.textContent = "ক্ষেতে নিরাপদ থাকার উপায়";
            [
                "সুরক্ষা জুতা ব্যবহার করুন কাজের সময়।",
                "কীটনাশক ব্যবহার করলে মাস্ক ও গ্লাভস পরুন।",
                "রোদে দীর্ঘ সময় কাজের সময় টুপি ও পানি সঙ্গে রাখুন।",
                "বজ্রপাতের সময় খোলা মাঠে কাজ করা এড়িয়ে চলুন।",
                "ধারালো যন্ত্রপাতি ব্যবহারের সময় সতর্ক থাকুন।",
                "কাজ শেষে যন্ত্রপাতি নিরাপদ স্থানে রাখুন।",
                "বিদ্যুৎ চালিত যন্ত্র ব্যবহার করার আগে সঠিকভাবে পরীক্ষা করুন।",
                "কাঠ বা বাঁশের মই ব্যবহার করলে সেটি যেন মজবুত হয় তা নিশ্চিত করুন।",
                "বিষাক্ত কীটনাশক শিশুদের নাগালের বাইরে রাখুন।",
                "হঠাৎ অসুস্থতা বা দুর্ঘটনার জন্য পাশে প্রাথমিক চিকিৎসার কিট রাখুন।"
            ]
             .forEach(tip => {
                const li = document.createElement("li");
                li.textContent = tip;
                list.appendChild(li);
             });
        } else if (index === 1) {
            title.textContent = "ফসলের যত্নে সেরা সরঞ্জাম";
            [
                "নিয়মিত নির্ধারিত সময়ে সেচ দিন, অতিরিক্ত বা কম পানি এড়িয়ে চলুন।",
                "জমির ধরণ ও ফসল অনুযায়ী সঠিক সার ব্যবহার করুন।",
                "ফসলের বৃদ্ধির সময় নিয়মিত আগাছা পরিষ্কার করুন।",
                "ক্ষতিকর পোকামাকড় নিয়ন্ত্রণে জৈব কীটনাশক ব্যবহার করুন।",
                "ফসলের পাতা, কান্ড বা শিকড়ে অস্বাভাবিকতা দেখা দিলে দ্রুত বিশেষজ্ঞের পরামর্শ নিন।",
                "ফসল রোপণের আগে মাটি পরীক্ষা করে নিন মাটির গুণমান নিশ্চিত করার জন্য।",
                "ফসল ঘনভাবে না লাগিয়ে নির্দিষ্ট দূরত্ব বজায় রাখুন, যাতে পর্যাপ্ত আলো ও বাতাস পায়।",
                "পোকামাকড় নিরীক্ষণের জন্য ফাঁদ (যেমন ফেরোমন ট্র্যাপ) ব্যবহার করুন।",
                "ফসল কাটার সময় সঠিক উপায় অনুসরণ করুন যাতে ফলন নষ্ট না হয়।",
                "মৌসুম অনুযায়ী সঠিক জাতের বীজ নির্বাচন করুন।"
            ]

             .forEach(tip => {
                const li = document.createElement("li");
                li.textContent = tip;
                list.appendChild(li);
             });
        }

        modal.style.display = "flex";
    });
});

// Close modal
document.querySelector(".close-btn").onclick = () => {
    document.getElementById("tipModal").style.display = "none";
};

window.onclick = (event) => {
    const modal = document.getElementById("tipModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
};
</script>




</body>
</html>
