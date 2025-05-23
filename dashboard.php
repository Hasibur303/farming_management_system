
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>স্মার্ট কৃষি হোমপেজ</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
  html {
    scroll-behavior: smooth;
  }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    .navbar {
      background: linear-gradient(90deg, #43cea2, #185a9d);
    }
    .navbar-brand, .nav-link {
      color: #fff !important;
    }
    .hero {
      background: url('R (2).jpeg') no-repeat center center/cover;
      height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-shadow: 2px 2px 4px #000;
    }
    .hero h1 {
      font-size: 3rem;
    }
    .features {
      padding: 60px 0;
    }
    .feature-box {
      text-align: center;
      padding: 20px;
    }
    .feature-box i {
      font-size: 3rem;
      color: #185a9d;
      margin-bottom: 20px;
    }
    footer {
      background-color: #333;
      color: #fff;
      padding: 20px 0;
      text-align: center;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">স্মার্ট কৃষি</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">হোম</a></li>
          <li class="nav-item">
            <a class="nav-link" href="#sheba-section">সেবা</a>
          </li>

          <li class="nav-item"><a class="nav-link" href="contact.php">যোগাযোগ</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">লগইন</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container text-center">
      <h1>স্মার্ট কৃষিতে স্বাগতম</h1>
      <p>আপনার কৃষি ব্যবস্থাপনার ডিজিটাল সমাধান</p>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="container">
      <div class="row">
        <div class="col-md-4 feature-box">
          <i class="fas fa-seedling"></i>
          <h3>ফসল ব্যবস্থাপনা</h3>
          <p>আপনার ফসলের তথ্য সহজেই ট্র্যাক করুন।</p>
        </div>
        <div class="col-md-4 feature-box">
          <i class="fas fa-cloud-sun"></i>
          <h3>আবহাওয়া পূর্বাভাস</h3>
          <p>সঠিক আবহাওয়া তথ্যের মাধ্যমে পরিকল্পনা করুন।</p>
        </div>
        <div class="col-md-4 feature-box">
          <i class="fas fa-chart-line"></i>
          <h3>উৎপাদন বিশ্লেষণ</h3>
          <p>উৎপাদনের ডেটা বিশ্লেষণ করে উন্নতি করুন।</p>
        </div>
      </div>
    </div>
  </section>





  <!-- ============================= -->
  <!-- 🌾 ফসল গাইডলাইন সেকশন শুরু -->
  <!-- ============================= -->
  <section id="sheba-section" style="background: linear-gradient(135deg, #e8f5e9, #fffde7); padding: 60px 20px; font-family: 'Poppins', sans-serif;">
    <div style="max-width: 1000px; margin: auto; text-align: center;">
      <h2 style="font-size: 2.5em; color: #2e7d32; margin-bottom: 30px;">ফসল চাষ গাইডলাইন</h2>

      <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; align-items: flex-start;">
        <!-- Dropdown & Button -->
        <div style="flex: 1; min-width: 250px;">
          <label for="cropSelect" style="font-size: 1.2em; font-weight: bold; display: block; margin-bottom: 10px;">ফসল নির্বাচন করুন:</label>
          <select id="cropSelect" style="padding: 12px 20px; width: 100%; font-size: 1em; border-radius: 10px; border: 1px solid #ccc;">
            <option value="">-- ফসল নির্বাচন করুন --</option>
            <option value="ধান">ধান</option>
            <option value="গম">গম</option>
            <option value="আলু">আলু</option>
            <option value="পেঁয়াজ">পেঁয়াজ</option>
            <option value="টমেটো">টমেটো</option>
            <option value="লাউ">লাউ</option>
            <option value="মরিচ">মরিচ</option>
            <option value="বেগুন">বেগুন</option>
            <option value="শিম">শিম</option>
            <option value="কপি">কপি</option>
            <option value="মূলা">মূলা</option>
            <option value="সালাদ পাতা">সালাদ পাতা</option>
            <option value="শসা">শসা</option>
            <option value="করলা">করলা</option>
            <option value="পাট">পাট</option>
            <option value="সরিষা">সরিষা</option>
            <option value="চিনাবাদাম">চিনাবাদাম</option>
            <option value="ছোলা">ছোলা</option>
            <option value="মসুর ডাল">মসুর ডাল</option>
            <option value="মুগ ডাল">মুগ ডাল</option>
            <option value="খেসারি ডাল">খেসারি ডাল</option>
            <option value="ভুট্টা">ভুট্টা</option>
            <option value="তিল">তিল</option>
            <option value="তরমুজ">তরমুজ</option>
            <option value="কাঁকরোল">কাঁকরোল</option>
            <option value="লাল শাক">লাল শাক</option>
            <option value="পালং শাক">পালং শাক</option>
            <option value="ঝিঙে">ঝিঙে</option>
            <option value="কচু">কচু</option>
          </select>
          <button onclick="showGuide()" style="margin-top: 15px; background: linear-gradient(90deg, #43cea2, #185a9d); color: white; padding: 10px 25px; border: none; border-radius: 25px; font-size: 1em; cursor: pointer; transition: 0.3s;">দেখুন</button>
        </div>

        <!-- Output / Display -->
        <div id="guideOutput" style="flex: 2; min-width: 300px; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); text-align: left;">
          <p style="font-size: 1.1em; color: #555;">আপনার নির্বাচিত ফসল সম্পর্কিত গাইডলাইন এখানে প্রদর্শিত হবে।</p>
        </div>
      </div>
    </div>
  </section>




  <!-- Footer -->
  <footer>
    <p>&copy; ২০২৫ স্মার্ট কৃষি। সর্বস্বত্ব সংরক্ষিত।</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  function showGuide() {
    const crop = document.getElementById("cropSelect").value;
    const output = document.getElementById("guideOutput");

    if (crop === "") {
      output.innerHTML = "<p style='color:red;'>ফসল নির্বাচন করুন।</p>";
      return;
    }

    fetch("get_crop_guide.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "crop_name=" + encodeURIComponent(crop)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        output.innerHTML = `
          <h3 style="color: #2e7d32;">${crop}</h3>
          <h4 style="color: #1b5e20;">ফসল পরিচিতি:</h4>
          <p>${data.info}</p>
          <h4 style="color: #1b5e20;">কর্মপদ্ধতি / রোডম্যাপ:</h4>
          <p>${data.roadmap}</p>
        `;
      } else {
        output.innerHTML = `<p style="color: red;">${data.error}</p>`;
      }
    })
    .catch(error => {
      output.innerHTML = "<p style='color:red;'>সার্ভার ত্রুটি। আবার চেষ্টা করুন।</p>";
      console.error(error);
    });
  }
  </script>
</body>
</html>
