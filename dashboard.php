
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

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
   <!-- Include Swiper CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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


     .smartkrishi-footer {
       font-family: 'Poppins', sans-serif;
       color: #fff;
     }

     .footer-main {
       display: flex;
       flex-wrap: wrap;
       justify-content: space-around;
       background: linear-gradient(90deg, #43cea2, #185a9d);
       padding: 50px 20px;
     }

     .footer-section {
       flex: 1 1 250px;
       margin: 20px;
     }

     .footer-section h3,
     .footer-section h4 {
       margin-bottom: 15px;
       color: #fff;
     }

     .footer-section p,
     .footer-section a {
       font-size: 15px;
       color: #f0f0f0;
       text-decoration: none;
       line-height: 1.8;
     }

     .footer-section ul {
       list-style: none;
       padding: 0;
     }

     .footer-section ul li {
       margin-bottom: 10px;
     }

     .footer-section a:hover {
       color: #ffe082;
     }

     .social-icons {
       margin-top: 20px;
     }

     .social-icons a {
       margin-right: 10px;
       font-size: 20px;
       color: #fff;
       transition: 0.3s;
     }

     .social-icons a:hover {
       color: #ffe082;
       transform: scale(1.2);
     }

     .footer-bottom {
       text-align: center;
       background: #154276;
       padding: 15px;
       font-size: 14px;
       color: #ddd;
     }

     @media (max-width: 768px) {
       .footer-main {
         flex-direction: column;
         text-align: center;
       }

       .footer-section {
         margin: 20px auto;
       }

       .social-icons a {
         margin: 0 8px;
       }
     }


     body {
       font-family: 'Segoe UI', sans-serif;
       background-color: #f5f5f5;
     }
     .section-title {
       text-align: center;
       font-size: 2rem;
       font-weight: bold;
       margin: 40px 0 20px;
     }
     .info-card {
       background: linear-gradient(135deg, #4caf50, #81c784);
       color: white;
       border-radius: 12px;
       padding: 20px;
       margin: 10px 0;
       transition: 0.3s;
     }
     .info-card:hover {
       transform: scale(1.02);
       box-shadow: 0 10px 20px rgba(0,0,0,0.2);
     }
     .service-btns button {
       width: 100%;
       margin-bottom: 10px;
     }
     .fade-img {
       transition: opacity 0.4s ease-in-out;
     }
     .service-image {
       width: 70%;
       max-width: 70%;
       height: 300px;
       object-fit: cover;
       border-radius: 10px;
     }
     @media (max-width: 768px) {
       .service-image {
         height: 300px;
       }
     }

     /* Contact Us section */
     #contact {
       background: linear-gradient(135deg, #f3f4f6, #d1e8e4);
       padding: 60px 20px;
       font-family: 'Poppins', sans-serif;
     }
     .contact-header {
       text-align: center;
       background: linear-gradient(90deg, #43cea2, #185a9d);
       color: white;
       padding: 30px 20px;
       border-radius: 12px;
       margin-bottom: 40px;
       box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
     }
     .contact-header h1 {
       margin: 0;
       font-size: 2.5em;
     }
     .contact-header p {
       margin-top: 10px;
       font-size: 1.1em;
     }
     .contact-container {
       display: flex;
       flex-wrap: wrap;
       justify-content: center;
       gap: 30px;
     }
     .creator {
       background: white;
       padding: 30px;
       width: 300px;
       border-radius: 15px;
       box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
       text-align: center;
       transition: transform 0.3s, box-shadow 0.3s;
     }
     .creator:hover {
       transform: translateY(-10px);
       box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
     }
     .creator img {
       width: 150px;
       height: 150px;
       border-radius: 50%;
       object-fit: cover;
       margin-bottom: 20px;
       border: 5px solid #43cea2;
     }
     .creator-info h3 {
       margin: 10px 0;
       font-size: 1.4em;
       color: #185a9d;
     }
     .creator-info p {
       margin: 6px 0;
       font-size: 0.95em;
     }
     .creator-info a {
       color: #43cea2;
       text-decoration: none;
       font-weight: bold;
       transition: color 0.3s;
     }
     .creator-info a:hover {
       color: #185a9d;
     }
     @media (max-width: 768px) {
       .creator {
         width: 90%;
       }
       .contact-header h1 {
         font-size: 2em;
       }
     }


     .review-section {
       background: #f8f9fa;
       padding: 60px 20px;
       text-align: center;
     }

     .review-title {
       font-size: 2.5rem;
       margin-bottom: 40px;
       color: #185a9d;
       font-weight: bold;
     }

     .review-container {
       overflow: hidden;
       max-width: 1200px;
       margin: auto;
       position: relative;
     }

     .review-slider {
       display: flex;
       transition: transform 1.5s ease-in-out;
       width: 100%;
     }

     .review-slide {
       display: flex;
       justify-content: space-around;
       width: 100%;
       flex: 0 0 100%;
     }

     .review-card {
       background: #ffffff;
       border-radius: 12px;
       box-shadow: 0 10px 20px rgba(0,0,0,0.1);
       padding: 20px;
       width: 28%;
       margin: 0 10px;
       transition: 1 s;
     }

     .review-card:hover {
       transform: translateY(-5px);
     }

     .review-card img {
       width: 80px;
       height: 80px;
       object-fit: cover;
       border-radius: 50%;
       border: 3px solid #43cea2;
       margin-bottom: 15px;
     }

     .stars {
       color: #ffcc00;
       font-size: 1.2rem;
       margin-bottom: 10px;
     }

     .review-card h4 {
       font-size: 1.2rem;
       font-weight: 600;
       color: #185a9d;
       margin-bottom: 10px;
     }

     .review-card p {
       font-size: 0.95rem;
       color: #333;
     }

     @media (max-width: 768px) {
       .review-slide {
         flex-direction: column;
         align-items: center;
       }

       .review-card {
         width: 80%;
         margin: 15px 0;
       }

       .review-slider {
         width: 300%;
       }
     }

    .add-review-btn {
      background: linear-gradient(90deg, #43cea2, #185a9d);
      color: #fff;
      padding: 12px 25px;
      border: none;
      border-radius: 25px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .add-review-btn:hover {
      background: linear-gradient(90deg, #185a9d, #43cea2);
    }

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 400px;
      margin: 100px auto;
      position: relative;
    }

    .modal-content h3 {
      margin-bottom: 20px;
      color: #185a9d;
    }

    .modal-content input,
    .modal-content textarea {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #aaa;
      border-radius: 6px;
    }

    .modal-content button {
      background-color: #43cea2;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
    }

    .modal-content button:hover {
      background-color: #185a9d;
    }

    .close {
      position: absolute;
      right: 15px;
      top: 10px;
      font-size: 24px;
      cursor: pointer;
    }



    /* Stat Box Style */
    .stat-box {
      background: linear-gradient(135deg, #43cea2, #185a9d);
      color: #fff;
      border-radius: 16px;
      padding: 30px 20px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }

    .stat-box:hover {
      transform: scale(1.05);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .stat-box .circle {
      width: 80px;
      height: 80px;
      background: white;
      color: #185a9d;
      font-weight: bold;
      font-size: 1.3rem;
      border-radius: 50%;
      margin: 0 auto 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .stat-box .label {
      font-size: 1.1rem;
      font-weight: 600;
    }


    .tip-section {
      background: linear-gradient(90deg, #43cea2, #185a9d);
      padding: 40px 0;
      color: #fff;
      text-align: center;
    }
    .tip-section h2 {
      font-size: 28px;
      margin-bottom: 30px;
      font-weight: bold;
    }
    .swiper {
      width: 90%;
      max-width: 1000px;
      margin: auto;
    }
    .swiper-slide {
      background: #ffffff20;
      border-radius: 20px;
      padding: 20px;
      transition: 0.3s;
      height: 200px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .swiper-slide:hover {
      background: #ffffff40;
      transform: scale(1.05);
    }
    .tip-title {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .tip-body {
      font-size: 16px;
    }


    /* Chatbot button */
    .chatbot-btn {
      position: fixed;
      bottom: 25px;
      right: 25px;
      background: linear-gradient(45deg, #43cea2, #185a9d);
      color: white;
      border: none;
      padding: 15px;
      border-radius: 50%;
      font-size: 22px;
      cursor: pointer;
      z-index: 1000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      transition: 0.3s;
    }
    .chatbot-btn:hover {
      transform: scale(1.1);
    }

    /* Chat window */
    .chatbot-window {
      position: fixed;
      bottom: 90px;
      right: 25px;
      width: 320px;
      max-width: 90%;
      height: 420px;
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.4);
      display: none;
      flex-direction: column;
      z-index: 999;
    }

    .chat-header {
      background: linear-gradient(45deg, #43cea2, #185a9d);
      padding: 15px;
      color: white;
      font-weight: bold;
      text-align: center;
    }

    .chat-body {
      flex: 1;
      padding: 10px;
      overflow-y: auto;
      font-size: 15px;
      background: #f4f4f4;
    }

    .chat-body p {
      background: #e0ffe0;
      padding: 8px 12px;
      border-radius: 12px;
      margin: 6px 0;
      max-width: 80%;
      align-self: flex-start;
    }

    .chat-body .reply {
      background: #d0e7ff;
      align-self: flex-end;
    }

    .chat-footer {
      display: flex;
      padding: 10px;
      background: #fff;
      border-top: 1px solid #ccc;
    }

    .chat-footer input {
      flex: 1;
      padding: 8px;
      border: none;
      border-radius: 20px;
      background: #f0f0f0;
      outline: none;
    }

    .chat-footer button {
      background: #43cea2;
      color: white;
      border: none;
      margin-left: 8px;
      padding: 8px 12px;
      border-radius: 20px;
      cursor: pointer;
      transition: 0.2s;
    }

    .chat-footer button:hover {
      background: #185a9d;
    }


    .event-section {
      padding: 40px 20px;
      background: linear-gradient(90deg, #43cea2, #185a9d);
      color: white;
      font-family: 'Siyam Rupali', sans-serif;
    }
    .section-title {
      text-align: center;
      font-size: 28px;
      margin-bottom: 30px;
      font-weight: bold;
      border-bottom: 2px solid white;
      display: inline-block;
      padding-bottom: 10px;
    }
    .notice-board {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .notice-card {
      background: rgba(255, 255, 255, 0.1);
      border-left: 5px solid #ffeb3b;
      border-radius: 12px;
      padding: 20px;
      width: 300px;
      transition: 0.3s;
    }
    .notice-card:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: scale(1.03);
    }
    .notice-card h5 {
      font-size: 18px;
      margin-bottom: 10px;
      color: #ffeb3b;
    }
    .notice-card p {
      font-size: 15px;
    }
    .notice-card .date {
      display: block;
      margin-top: 10px;
      font-size: 13px;
      color: #c1f1ff;
      text-align: right;
    }
    @media (max-width: 768px) {
      .notice-card {
        width: 100%;
      }
    }


        body {
          background: linear-gradient(to right, #fdfcfb, #e2d1c3);
          font-family: 'Segoe UI', sans-serif;
        }
        .calendar-card {
          background: linear-gradient(135deg, #e0f7e9, #c8e6c9);
          border: none;
          border-radius: 16px;
          box-shadow: 0 8px 20px rgba(0,0,0,0.1);
          margin-bottom: 24px;
          transition: transform 0.2s ease;
        }
        .calendar-card:hover {
          transform: scale(1.01);
        }
        .card-header {
          background-color: transparent;
          font-size: 20px;
          font-weight: bold;
          color: #2e7d32;
        }
        .month-task {
          padding: 10px 15px;
          border-left: 4px solid #4caf50;
          margin-bottom: 10px;
          background-color: #ffffffdd;
          border-radius: 8px;
        }
        .month-task strong {
          color: #2e7d32;
        }

          body {
              background: linear-gradient(to right, #fff8e1, #e0f7fa);
              font-family: 'Segoe UI', sans-serif;
            }
            .calendar-title {
              text-align: center;
              font-size: 28px;
              font-weight: bold;
              margin: 30px 0;
              color: #2e7d32;
            }
            .month-grid {
              display: grid;
              grid-template-columns: repeat(4, 1fr);
              gap: 20px;
              padding: 20px;
            }
            .month-box {
              background: linear-gradient(135deg, #a5d6a7, #81c784);
              border-radius: 16px;
              box-shadow: 0 8px 20px rgba(0,0,0,0.1);
              padding: 16px;
              color: #ffffff;
              transition: transform 0.2s;
            }
            .month-box:hover {
              transform: scale(1.03);
            }
            .month-name {
              font-size: 20px;
              font-weight: bold;
              margin-bottom: 12px;
              text-align: center;
            }
            .crop-tag {
              background-color: rgba(255, 255, 255, 0.2);
              padding: 8px 12px;
              margin: 5px 0;
              border-radius: 12px;
              display: flex;
              align-items: center;
              gap: 8px;
            }
            .crop-tag img {
              width: 20px;
              height: 20px;
            }



                body {
                  background: linear-gradient(to right, #e0f7fa, #fff8e1);
                  font-family: 'Segoe UI', sans-serif;
                }
                .section-title {
                  text-align: center;
                  font-size: 32px;
                  font-weight: bold;
                  margin: 40px 0;
                  color: #2e7d32;
                }
                .story-card {
                  background: linear-gradient(135deg, #a5d6a7, #81c784);
                  border-radius: 16px;
                  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
                  padding: 20px;
                  color: #ffffff;
                  transition: transform 0.2s;
                }
                .story-card:hover {
                  transform: scale(1.03);
                }
                .story-image {
                  width: 100%;
                  height: 200px;
                  object-fit: cover;
                  border-radius: 12px;
                  margin-bottom: 15px;
                }
                .story-title {
                  font-size: 20px;
                  font-weight: bold;
                  margin-bottom: 10px;
                }
                .story-description {
                  font-size: 16px;
                  margin-bottom: 15px;
                }
                .story-video {
                  width: 100%;
                  height: 200px;
                  border: none;
                  border-radius: 12px;
                }

  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="1.png" alt="Logo" style="height: 35px; margin-right: 10px;">
        স্মার্ট কৃষি
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">হোম</a></li>
          <li class="nav-item">
            <a class="nav-link" href="#guidline-section">গাইডলাইন</a>
          </li>
            <li class="nav-item">
                        <a class="nav-link" href="#sheba-section">সেবাসমূহ</a>
                      </li>
          <li class="nav-item">
          <a class="nav-link" href="#contact-section">যোগাযোগ</a>
          </li>
          <li class="nav-item"><a class="nav-link" href="blog.php">ব্লগ</a></li>
          <li class="nav-item">
            <a class="btn btn-outline-light me-2" href="login.php">লগইন</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-success" href="register.php">সাইন আপ</a>
          </li>

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

    <!-- 📢 ইভেন্ট / সরকারি ঘোষণা সেকশন -->
    <section class="event-section">
      <div class="container">
        <h2 class="section-title">📢 ইভেন্ট / সরকারি ঘোষণা</h2>
        <div class="notice-board">
          <div class="notice-card">
            <h5>🌾 কৃষি ঋণ আবেদন শুরু</h5>
            <p>ক্ষুদ্র ও মাঝারি কৃষকদের জন্য সহজ শর্তে কৃষি ঋণ প্রদান শুরু হয়েছে। বিস্তারিত জানুন স্থানীয় কৃষি অফিসে।</p>
            <span class="date">২০ জুলাই, ২০২৫</span>
          </div>
          <div class="notice-card">
            <h5>💸 সারের ভর্তুকি তালিকা প্রকাশ</h5>
            <p>২০২৫ সালের জন্য সরকার নির্ধারিত সার ভর্তুকি তালিকা ওয়েবসাইটে প্রকাশিত হয়েছে।</p>
            <span class="date">২২ জুলাই, ২০২৫</span>
          </div>
          <div class="notice-card">
            <h5>🎓 কৃষক প্রশিক্ষণ প্রোগ্রাম</h5>
            <p>নতুন প্রযুক্তি ব্যবহারে আগ্রহী কৃষকদের জন্য বিনামূল্যে ৩ দিনের প্রশিক্ষণ কোর্স ঘোষণা করা হয়েছে।</p>
            <span class="date">২৪ জুলাই, ২০২৫</span>
          </div>
        </div>
      </div>
    </section>



  <!-- ============================= -->
  <!-- 🌾 ফসল গাইডলাইন সেকশন শুরু -->
  <!-- ============================= -->
  <section id="guidline-section" style="background: linear-gradient(135deg, #e8f5e9, #fffde7); padding: 60px 20px; font-family: 'Poppins', sans-serif;">
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


 <section class="tip-section">
      <h2> স্মার্ট কৃষি টিপস</h2>
      <div class="swiper mySwiper">
        <div class="swiper-wrapper">
          <!-- Tip 1 -->
          <div class="swiper-slide">
            <div class="tip-title">✅ বীজ বপনের সময়</div>
            <div class="tip-body">মাটি ভালোভাবে প্রস্তুত করুন ও নির্ধারিত গভীরতায় বপন করুন।</div>
          </div>

          <!-- Tip 2 -->
          <div class="swiper-slide">
            <div class="tip-title">💧 পানি সেচ</div>
            <div class="tip-body">প্রথম ১০ দিনের মধ্যে দিনে ১বার করে সেচ দেওয়া উত্তম।</div>
          </div>

          <!-- Tip 3 -->
          <div class="swiper-slide">
            <div class="tip-title">🌿 সার ব্যবস্থাপনা</div>
            <div class="tip-body">প্রতি ১৫ দিনে জৈব সার ব্যবহার করলে উৎপাদন ভালো হয়।</div>
          </div>

          <!-- Tip 4 -->
          <div class="swiper-slide">
            <div class="tip-title">🌞 আলো ও ছায়া</div>
            <div class="tip-body">ফসলের জন্য দিনে অন্তত ৬ ঘণ্টা রোদ থাকা প্রয়োজন।</div>
          </div>

          <!-- Tip 5 -->
          <div class="swiper-slide">
            <div class="tip-title">🐛 কীটনাশক ব্যবহার</div>
            <div class="tip-body">প্রয়োজনে বিশেষজ্ঞ পরামর্শ নিয়ে পরিমিত ব্যবহার করুন।</div>
          </div>

          <!-- Tip 6 -->
          <div class="swiper-slide">
            <div class="tip-title">📅 সঠিক সময় ফসল কাটুন</div>
            <div class="tip-body">ফলন পরিপক্ব হওয়ার পর দ্রুত সংগ্রহ করা জরুরি।</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Include Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
      var swiper = new Swiper(".mySwiper", {
        slidesPerView: 3,
        spaceBetween: 20,
        loop: true,
        autoplay: {
          delay: 3000,
        },
        breakpoints: {
          0: {
            slidesPerView: 1
          },
          576: {
            slidesPerView: 2
          },
          768: {
            slidesPerView: 3
          }
        }
      });
    </script>



 <div class="container">
    <div class="calendar-title">📅 ফসল চাষ ক্যালেন্ডার (মাসভিত্তিক)</div>

    <div class="month-grid">
      <!-- জানুয়ারি -->
      <div class="month-box">
        <div class="month-name">জানুয়ারি</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/rice.png"/> ধান - জমি প্রস্তুতি</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/tomato.png"/> টমেটো - ফল ধরা</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/potato.png"/> আলু - রোগ দমন</div>
      </div>

      <!-- ফেব্রুয়ারি -->
      <div class="month-box">
        <div class="month-name">ফেব্রুয়ারি</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/rice.png"/> ধান - রোপণ</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/tomato.png"/> টমেটো - সংগ্রহ</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/potato.png"/> আলু - সংগ্রহ</div>
      </div>

      <!-- মার্চ -->
      <div class="month-box">
        <div class="month-name">মার্চ</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/rice.png"/> ধান - সেচ ও সার</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/vegetables-basket.png"/> শাকসবজি - বপন</div>
      </div>

      <!-- এপ্রিল -->
      <div class="month-box">
        <div class="month-name">এপ্রিল</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/rice.png"/> ধান - আগাছা দমন</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/mango.png"/> আম - মুকুল</div>
      </div>

      <!-- মে -->
      <div class="month-box">
        <div class="month-name">মে</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/rice.png"/> ধান - কাটাই</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/mango.png"/> আম - সংগ্রহ</div>
      </div>

      <!-- জুন -->
      <div class="month-box">
        <div class="month-name">জুন</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/chili-pepper.png"/> মরিচ - রোপণ</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/jackfruit.png"/> কাঁঠাল - সংগ্রহ</div>
      </div>

      <!-- জুলাই -->
      <div class="month-box">
        <div class="month-name">জুলাই</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/brinjal.png"/> বেগুন - রোপণ</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/okra.png"/> ঢেঁড়স - বপন</div>
      </div>

      <!-- আগস্ট -->
      <div class="month-box">
        <div class="month-name">আগস্ট</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/cauliflower.png"/> ফুলকপি - বপন</div>
      </div>

      <!-- সেপ্টেম্বর -->
      <div class="month-box">
        <div class="month-name">সেপ্টেম্বর</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/cabbage.png"/> বাঁধাকপি - রোপণ</div>
      </div>

      <!-- অক্টোবর -->
      <div class="month-box">
        <div class="month-name">অক্টোবর</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/tomato.png"/> টমেটো - বীজতলা প্রস্তুতি</div>
      </div>

      <!-- নভেম্বর -->
      <div class="month-box">
        <div class="month-name">নভেম্বর</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/potato.png"/> আলু - রোপণ</div>
      </div>

      <!-- ডিসেম্বর -->
      <div class="month-box">
        <div class="month-name">ডিসেম্বর</div>
        <div class="crop-tag"><img src="https://img.icons8.com/color/48/tomato.png"/> টমেটো - সার প্রয়োগ</div>
      </div>

    </div>
  </div>

<!-- Section 1: চলুন আমাদের সম্পর্কে জানি -->
<div class="container">
  <div class="section-title">চলুন আমাদের সম্পর্কে জানি?</div>
  <div class="row align-items-center">
    <div class="col-md-4 text-center">
      <img src="1.png" alt="Farmer with tablet" class="img-fluid rounded">
    </div>
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-6">
          <div class="info-card">
            <h5>২৪/৭ সেবা</h5>
            <p>আমাদের স্মার্টকৃষি প্ল্যাটফর্ম সারা বছর এবং ২৪ ঘণ্টা খোলা থাকে, যাতে কৃষকেরা সহজেই উপকার পেতে পারেন।</p>
          </div>
          <div class="info-card">
            <h5>উচ্চ মানের ও বিশ্লেষণভিত্তিক সেবা</h5>
            <p>সঠিক পরামর্শ ও বিশ্লেষণ ভিত্তিক ফলাফল প্রদান করি।</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-card">
            <h5>শিক্ষালাভ ও অভিজ্ঞতা বৃদ্ধি</h5>
            <p>কৃষি ও প্রযুক্তিতে শিক্ষালাভ ও উৎপাদন বৃদ্ধির সুযোগ তৈরি করি।</p>
          </div>
          <div class="info-card">
            <h5>গ্রাহক সেবা ও বিশেষজ্ঞ পরামর্শ</h5>
            <p>বিশেষজ্ঞদের সঙ্গে সরাসরি যোগাযোগ এবং উন্নত সমাধান।</p>
          </div>
        </div>
      </div>
      <!-- Statistics Section -->
      <div class="container my-5">
        <div class="row g-4 justify-content-center">

          <!-- Single Box -->
          <div class="col-6 col-md-3">
            <div class="stat-box text-center">
              <div class="circle">৫০+</div>
              <div class="label">প্রকল্প</div>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="stat-box text-center">
              <div class="circle">৫০০+</div>
              <div class="label">সেবা দিচ্ছি</div>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="stat-box text-center">
              <div class="circle">২৫০+</div>
              <div class="label">পণ্য</div>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="stat-box text-center">
              <div class="circle">৫০+</div>
              <div class="label">মাঠকর্মী</div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>



<!-- Chatbot UI -->
<button class="chatbot-btn" onclick="toggleChat()">
  <i class="fas fa-comment-dots"></i>
</button>

<div class="chatbot-window" id="chatWindow">
  <div class="chat-header">🔎 স্মার্ট কৃষি সহায়তা</div>

  <div class="chat-body" id="chatBody">
    <p>👋 হ্যালো! কিভাবে সাহায্য করতে পারি?</p>

    <!-- Suggested Questions -->
    <div id="suggestedQuestions">
      <button onclick="handleSuggestion('ধান চাষের সময় কী সার দিতে হয়?')">🌾 ধান চাষে সার</button>
      <button onclick="handleSuggestion('কোন ফসল এখন বপন করা ভাল?')">📅 সিজন অনুযায়ী ফসল</button>
      <button onclick="handleSuggestion('পোকামাকড় দমন কিভাবে করব?')">🐛 পোকা দমন</button>
      <button onclick="handleSuggestion('জমিতে পানি সেচ কতবার দেবো?')">💧 পানি সেচ</button>
    </div>
  </div>

  <div class="chat-footer">
    <input type="text" id="chatInput" placeholder="আপনার প্রশ্ন লিখুন...">
    <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
  </div>
</div>

<!-- Chatbot CSS (add in <style> or external CSS) -->
<style>
.chatbot-window {
  position: fixed;
  bottom: 80px;
  right: 20px;
  width: 300px;
  background: #fefefe;
  border-radius: 10px;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
  display: none;
  flex-direction: column;
  overflow: hidden;
  z-index: 999;
}
.chat-header {
  background: #28a745;
  color: white;
  padding: 12px;
  font-weight: bold;
}
.chat-body {
  padding: 10px;
  max-height: 300px;
  overflow-y: auto;
  font-family: sans-serif;
}
.chat-footer {
  display: flex;
  border-top: 1px solid #ddd;
}
.chat-footer input {
  flex: 1;
  padding: 8px;
  border: none;
  outline: none;
}
.chat-footer button {
  background: #28a745;
  border: none;
  color: white;
  padding: 0 15px;
  cursor: pointer;
}
.chatbot-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #28a745;
  color: white;
  border: none;
  padding: 15px;
  border-radius: 50%;
  font-size: 20px;
  cursor: pointer;
}
.reply {
  text-align: right;
  background: #dcf8c6;
  padding: 6px 10px;
  margin: 5px 0;
  border-radius: 8px;
}
#suggestedQuestions button {
  background: #eee;
  border: none;
  border-radius: 8px;
  padding: 6px 10px;
  margin: 4px 2px;
  cursor: pointer;
  font-size: 14px;
}
#suggestedQuestions button:hover {
  background: #d4edda;
}
</style>

<!-- Chatbot JS -->
<script>
function toggleChat() {
  const chat = document.getElementById("chatWindow");
  chat.style.display = chat.style.display === "flex" ? "none" : "flex";
}

function handleSuggestion(text) {
  document.getElementById("chatInput").value = text;
  sendMessage();
}

function sendMessage() {
  const input = document.getElementById("chatInput");
  const message = input.value.trim();
  const chatBody = document.getElementById("chatBody");

  if (message !== "") {
    // Show user message
    const userMsg = document.createElement("p");
    userMsg.className = "reply";
    userMsg.textContent = message;
    chatBody.appendChild(userMsg);

    // Bot reply logic
    const botReply = document.createElement("p");
    setTimeout(() => {
      let reply = "";

      if (message.includes("ধান") && message.includes("সার")) {
        reply = "ধান চাষে ইউরিয়া, টিএসপি এবং এমওপি সারের সঠিক ব্যবহার ফলন বাড়াতে সাহায্য করে। চাষের ১৫ দিন পর প্রথম ডোজ দিন।";
      } else if (message.includes("বপন") || message.includes("সিজন")) {
        reply = "এই মৌসুমে পাট, মরিচ এবং শাক সবজি চাষের জন্য উপযুক্ত সময়।";
      } else if (message.includes("পোকা")) {
        reply = "জৈব পদ্ধতিতে যেমন নিম তেল বা লেবুজাতীয় তরল ব্যবহার করে পোকা দমন করা যায়।";
      } else if (message.includes("পানি") || message.includes("সেচ")) {
        reply = "সাধারণভাবে ধান জমিতে প্রতি ৭-১০ দিনে সেচ দেওয়া প্রয়োজন, তবে মাটির ধরন অনুযায়ী পরিবর্তন হতে পারে।";
      } else {
        reply = "দুঃখিত! আমি বুঝতে পারিনি। অনুগ্রহ করে আরেকটু স্পষ্ট করে বলুন।";
      }

      botReply.textContent = reply;
      chatBody.appendChild(botReply);
      chatBody.scrollTop = chatBody.scrollHeight;
    }, 500);

    input.value = "";
    chatBody.scrollTop = chatBody.scrollHeight;
  }
}
</script>

<!-- Font Awesome for Icons (Add in <head>) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <section id="sheba-section" style="background: linear-gradient(135deg, #e8f5e9, #fffde7); padding: 60px 20px; font-family: 'Poppins', sans-serif;">

<!-- Section 2: আমাদের সেবাসমূহ -->
<div class="container my-5">
  <div class="section-title">আমাদের সেবাসমূহ</div>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="info-card">ফসল চাষে পরামর্শ</div>
    </div>
    <div class="col-md-4">
      <div class="info-card bg-warning text-dark">সার প্রয়োগে পরামর্শ</div>
    </div>
    <div class="col-md-4">
      <div class="info-card">কৃষি সরঞ্জাম ভাড়া</div>
    </div>
    <div class="col-md-4">
      <div class="info-card">কৃষি সরঞ্জাম যন্ত্রাংশ</div>
    </div>
    <div class="col-md-4">
      <div class="info-card">বিশেষজ্ঞ পরামর্শ</div>
    </div>
    <div class="col-md-4">
      <div class="info-card">আবহাওয়া ওয়ার্নিং</div>
    </div>
  </div>
</div>

<!-- Section 3: পূর্ব ও পরবর্তী সেবা -->
<div class="container my-5">
  <div class="section-title">পূর্ব ও পরবর্তী সেবা</div>
  <div class="row align-items-start">
    <div class="col-md-7">
      <img id="serviceImage" src="images/tea-leaves-tea-garden.jpg" class="fade-img service-image" alt="Service image">
      <p id="serviceText" class="mt-3">কৃষি সরঞ্জাম ভাড়া সেবা সম্পর্কে বিস্তারিত তথ্য এখানে দেখা যাবে।</p>
    </div>
    <div class="col-md-5 service-btns">
      <button class="btn btn-outline-success mb-2" onclick="changeService(0)">কৃষি সরঞ্জাম ভাড়া</button>
      <button class="btn btn-outline-success mb-2" onclick="changeService(1)">উপকরণ ভাড়া</button>
      <button class="btn btn-outline-success mb-2" onclick="changeService(2)">বিশেষজ্ঞ পরামর্শ</button>
      <button class="btn btn-outline-success mb-2" onclick="changeService(3)">ফসল চাষে পূর্বাভাস</button>
      <button class="btn btn-outline-success mb-2" onclick="changeService(4)">সার প্রয়োগে পরামর্শ</button>
    </div>
  </div>
</div>


  <section id="contact" style="background: linear-gradient(135deg, #e8f5e9, #fffde7); padding: 60px 20px; font-family: 'Poppins', sans-serif;">


    <div class="contact-header">
        <h1>আমাদের সাথে যোগাযোগ করুন</h1>
        <p>কৃষি ব্যবস্থাপনা ব্যবস্থার নির্মাতাদের সাথে সংযোগ স্থাপন করুন</p>
    </div>
    <div class="contact-container">
        <div class="creator">
            <img src="mmh.jpg" alt="Muhtasim Masum Hasnayen">
            <div class="creator-info">
                <h3>মুহতাসিম মাসুম হাসনায়েন</h3>
                <p><strong>ফোন:</strong> 01730202960</p>
                <p><strong>ইমেইল:</strong> <a href="mailto:hasnayenmasum@gmail.com">hasnayenmasum@gmail.com</a></p>
                <p><strong>ফেসবুক:</strong> <a href="https://www.facebook.com/mh.masum.908" target="_blank">Muhtasim's Facebook</a></p>
            </div>
        </div>
        <div class="creator">
            <img src="Hasibur.jpg" alt="Md Hasibur Rahman">
            <div class="creator-info">
                <h3>মোঃ হাসিবুর রহমান</h3>
                <p><strong>ফোন:</strong> 01580491525</p>
                <p><strong>ইমেইল:</strong> <a href="mailto:hasibur@gmail.com">hasibur@gmail.com</a></p>
                <p><strong>ফেসবুক:</strong> <a href="https://www.facebook.com/hasibur.rahmam.77" target="_blank">Hasibur's Facebook</a></p>
            </div>
        </div>
        <div class="creator">
            <img src="sabbir.jpg" alt="Md Sabbir Ahmed">
            <div class="creator-info">
                <h3>মোঃ সাব্বির আহমেদ</h3>
                <p><strong>ফোন:</strong> 01722835319</p>
                <p><strong>ইমেইল:</strong> <a href="mailto:mdsabbirahmed1703@gmail.com">mdsabbirahmed1703@gmail.com</a></p>
                <p><strong>ফেসবুক:</strong> <a href="https://www.facebook.com/sabbir.ahmed.445453" target="_blank">Sabbir's Facebook</a></p>
            </div>
        </div>
        <div class="creator">
            <img src="rafi.jpg" alt="Ar Rafi Hossain Ishty">
            <div class="creator-info">
                <h3>আর রাফি হোসেন ইশতি</h3>
                <p><strong>ফোন:</strong> +880 1867-726881</p>
                <p><strong>ইমেইল:</strong> <a href="mailto:aisty223793@bscse.uiu.ac.bd">aisty223793@bscse.uiu.ac.bd</a></p>
                <p><strong>ফেসবুক:</strong> <a href="https://www.facebook.com/arrafi.hossain.102" target="_blank">Rafi's Facebook</a></p>
            </div>
        </div>
    </div>
</section>

<!-- Optional fade animation CSS -->
<style>
  .fade-img {
    transition: opacity 0.4s ease-in-out;
    max-width: 100%;
    border-radius: 10px;
  }
</style>

<script>
  const images = [
    "images/farmerontractor.jpg",
    "images/techfarm.jpg",
    "images/AgriSpecialist.jpg",
    "images/agriforcast.jpg",
    "images/sprayfertilizer.jpg"
  ];

  const texts = [
    "আপনি সহজেই কৃষি যন্ত্রপাতি ভাড়া নিতে পারবেন।",
    "উন্নত প্রযুক্তির উপকরণ সহজে ভাড়া নিন।",
    "বিশেষজ্ঞদের পরামর্শ নিয়ে সমস্যা সমাধান করুন।",
    "চাষের জন্য সঠিক সময় ও পূর্বাভাস সম্পর্কে জানুন।",
    "সার ব্যবহারের পদ্ধতি ও সঠিক পরিমাণে জেনে নিন।"
  ];

  function changeService(index) {
    const img = document.getElementById("serviceImage");
    const text = document.getElementById("serviceText");
    img.style.opacity = 0;
    setTimeout(() => {
      img.src = images[index];
      text.innerText = texts[index];
      img.style.opacity = 1;
    }, 300);
  }
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<section class="review-section">
  <h2 class="review-title">গ্রাহক মন্তব্য</h2>
  <div class="review-container">
    <div class="review-slider">
      <!-- Slide 1 -->
      <div class="review-slide">
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="Customer 1">
          <div class="stars">★★★★★</div>
          <h4>সাবিহা আক্তার</h4>
          <p>স্মার্টকৃষি প্ল্যাটফর্মটি আমাকে আমার পণ্যের জন্য আরও বেশি ক্রেতা পেতে সাহায্য করেছে। অসাধারণ!</p>
        </div>
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Customer 2">
          <div class="stars">★★★★★</div>
          <h4>রাশেদুল ইসলাম</h4>
          <p>খুবই ভালো সার্ভিস। সরাসরি কৃষকের সাথে যুক্ত হয়ে আমি খাঁটি পণ্য পেয়েছি।</p>
        </div>
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/men/3.jpg" alt="Customer 3">
          <div class="stars">★★★★★</div>
          <h4>তানভীর হাসান</h4>
          <p>এই অ্যাপটি কৃষকের জন্য খুবই উপকারী। প্রশিক্ষণ মডিউলগুলো চমৎকার।</p>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="review-slide">
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/women/4.jpg" alt="Customer 4">
          <div class="stars">★★★★★</div>
          <h4>মেহজাবিন চৌধুরী</h4>
          <p>আমি আমার পরিবারের জন্য অর্গানিক সবজি কিনে খুবই সন্তুষ্ট।</p>
        </div>
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/men/5.jpg" alt="Customer 5">
          <div class="stars">★★★★★</div>
          <h4>সাইফুল হক</h4>
          <p>পণ্য অর্ডার করা এবং ডেলিভারি প্রক্রিয়া খুবই সহজ এবং নির্ভরযোগ্য।</p>
        </div>
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/women/6.jpg" alt="Customer 6">
          <div class="stars">★★★★★</div>
          <h4>নুসরাত জাহান</h4>
          <p>স্মার্ট কৃষি আমাকে সঠিক সময় বীজ ও পরামর্শ দিয়েছে। ধন্যবাদ!</p>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="review-slide">
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/men/7.jpg" alt="Customer 7">
          <div class="stars">★★★★★</div>
          <h4>হাফিজুর রহমান</h4>
          <p>অ্যাপে দেওয়া আবহাওয়া সতর্কতা ও চাষ পদ্ধতি আমাকে অনেক সহায়তা করেছে।</p>
        </div>
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/women/8.jpg" alt="Customer 8">
          <div class="stars">★★★★★</div>
          <h4>শিরিন আক্তার</h4>
          <p>এখানকার সেবা ও পণ্যের মান অসাধারণ। খুবই সন্তুষ্ট!</p>
        </div>
        <div class="review-card">
          <img src="https://randomuser.me/api/portraits/men/9.jpg" alt="Customer 9">
          <div class="stars">★★★★★</div>
          <h4>মিজানুর রহমান</h4>
          <p>আমার চাষের ফলাফল অনেক ভালো হয়েছে স্মার্টকৃষির সহায়তায়।</p>
        </div>
      </div>
    </div>
  </div>
</section>


<script>
  let currentIndex = 0;
  const slides = document.querySelectorAll('.review-slide');
  const totalSlides = slides.length;

  setInterval(() => {
    currentIndex = (currentIndex + 1) % totalSlides;
    document.querySelector('.review-slider').style.transform = `translateX(-${currentIndex * 100}%)`;
  }, 5000);
</script>

<!-- Add Review Button -->
<div style="text-align:center; margin-top: 30px;">
  <button class="add-review-btn" onclick="openModal()">✍️ আপনার মন্তব্য দিন</button>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>আপনার রিভিউ দিন</h3>

    <!-- নাম -->
    <input type="text" id="reviewerName" placeholder="আপনার নাম লিখুন">

    <!-- রেটিং -->
    <input type="number" id="reviewRating" min="1" max="5" placeholder="রেটিং (১-৫)">

    <!-- মন্তব্য -->
    <textarea id="reviewText" placeholder="আপনার মন্তব্য লিখুন"></textarea>

    <!-- ✅ ছবি আপলোড অপশন -->
    <label for="reviewImage">ছবি আপলোড করুন:</label>
    <input type="file" id="reviewImage" accept="image/*">

    <!-- সাবমিট বাটন -->
    <button onclick="submitReview()">সাবমিট করুন</button>
  </div>
</div>

<script>
function openModal() {
  document.getElementById("reviewModal").style.display = "block";
}

function closeModal() {
  document.getElementById("reviewModal").style.display = "none";
}

function submitReview() {
  const name = document.getElementById("reviewerName").value;
  const rating = document.getElementById("reviewRating").value;
  const text = document.getElementById("reviewText").value;

  if (!name || !rating || !text) {
    alert("অনুগ্রহ করে সব ঘর পূরণ করুন");
    return;
  }

  const stars = "★".repeat(rating) + "☆".repeat(5 - rating);

  const newCard = document.createElement("div");
  newCard.className = "review-card";
  newCard.innerHTML = `
    <img src="https://randomuser.me/api/portraits/lego/${Math.floor(Math.random()*10)}.jpg" alt="${name}">
    <div class="stars">${stars}</div>
    <h4>${name}</h4>
    <p>${text}</p>
  `;

  // Add to first slide (you can also make dynamic placement)
  document.querySelector(".review-slide").appendChild(newCard);
  closeModal();

  // Optional: Clear input fields
  document.getElementById("reviewerName").value = "";
  document.getElementById("reviewRating").value = "";
  document.getElementById("reviewText").value = "";
}
</script>


<div class="container">
    <div class="section-title"> সফল কৃষকের গল্প</div>

    <div class="row g-4">
      <!-- Story Card 1 -->
      <div class="col-md-4">
        <div class="story-card">
          <img src="https://example.com/farmer1.jpg" alt="কৃষক ১" class="story-image">
          <div class="story-title">নাসিমার চা বাগানের সাফল্য</div>
          <div class="story-description">নাসিমা, বাংলাদেশের একজন ছোট কৃষক, চা বাগানে সাফল্যের গল্প শেয়ার করেছেন।</div>
          <iframe class="story-video" src="https://www.youtube.com/embed/PBloxp55oyM" allowfullscreen></iframe>
        </div>
      </div>

      <!-- Story Card 2 -->
      <div class="col-md-4">
        <div class="story-card">
          <img src="https://example.com/farmer2.jpg" alt="কৃষক ২" class="story-image">
          <div class="story-title">দুধ উৎপাদনে মিল্ক ভিটার সাফল্য</div>
          <div class="story-description">মিল্ক ভিটা, বাংলাদেশের একটি দীর্ঘমেয়াদী সাফল্যের গল্প।</div>
          <iframe class="story-video" src="https://www.youtube.com/embed/FWx5rzy3RTg" allowfullscreen></iframe>
        </div>
      </div>

      <!-- Story Card 3 -->
      <div class="col-md-4">
        <div class="story-card">
          <img src="https://example.com/farmer3.jpg" alt="কৃষক ৩" class="story-image">
          <div class="story-title">সাক গার্ডেনিং ও রেইজড বেড পদ্ধতি</div>
          <div class="story-description">বাংলাদেশের উপকূলীয় অঞ্চলে সাক গার্ডেনিং ও রেইজড বেড পদ্ধতির সাফল্য।</div>
          <iframe class="story-video" src="https://www.youtube.com/embed/L7UppUeh3SI" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
<footer class="smartkrishi-footer">
  <div class="footer-main">
  <img src="1.png" alt="SmartKrishi Logo" style="height: 300px; margin-bottom: 15px;">
    <div class="footer-section about">

      <h3>স্মার্টকৃষি</h3>
      <p>আমাদের লক্ষ্য কৃষিকে প্রযুক্তিনির্ভর করে আধুনিক ও টেকসই সমাধান প্রদান করা। কৃষক, ক্রেতা ও সরবরাহকারীদের এক প্ল্যাটফর্মে সংযুক্ত করাই আমাদের উদ্দেশ্য।</p>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-whatsapp"></i></a>
        <a href="mailto:smartkrishi.bd@gmail.com"><i class="far fa-envelope"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
    <div class="footer-section links">
      <h4>দ্রুত লিংক</h4>
      <ul>
        <li><a href="dashboard.php">হোম</a></li>
        <li><a href="#">বাজার</a></li>
        <li><a href="#">চাষী তথ্য</a></li>
        <li><a href="contact.php">যোগাযোগ</a></li>
      </ul>
    </div>


    <div class="footer-section contact text-center">

      <h4>যোগাযোগ</h4>
      <p><i class="fas fa-map-marker-alt"></i> কৃষি ভবন, আগারগাঁও, ঢাকা</p>
      <p><i class="fas fa-phone-alt"></i> +৮৮ ০১৭১২৩৪৫৬৭৮</p>
      <p><i class="far fa-envelope"></i> smartkrishi.bd@gmail.com</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© ২০২৫ স্মার্টকৃষি | সর্বস্বত্ব সংরক্ষিত</p>
  </div>
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
