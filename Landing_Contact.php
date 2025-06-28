<?php
// DB connection
$host = 'localhost';
$db = 'farming_management';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $conn->real_escape_string($_POST['name']);
  $contact_info = $conn->real_escape_string($_POST['email']);
  $subject = $conn->real_escape_string($_POST['subject']);
  $message = $conn->real_escape_string($_POST['message']);

$sql = "INSERT INTO landing_contact (name, contact_info, subject, message)
        VALUES ('$name', '$contact_info', '$subject', '$message')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Message Sent Successfully!');</script>";
  } else {
    echo "<script>alert('Error sending message');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact - SmartTicket</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #2E7D32, #66BB6A);
      min-height: 100vh;
      color: #333;
      padding: 20px;
    }
    .contact-container {
      background: #ffffffee;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      padding: 40px;
      max-width: 1000px;
      margin: auto;
    }
    .form-control, .form-label {
      border-radius: 10px;
    }
    .btn-send {
      background: linear-gradient(to right, #388E3C, #66BB6A);
      color: white;
      border-radius: 30px;
      padding: 10px 30px;
      font-weight: bold;
      border: none;
    }
    .btn-send:hover {
      background: linear-gradient(to right, #66BB6A, #388E3C);
    }
    .info-box i {
      color: #2E7D32;
      font-size: 20px;
      margin-right: 10px;
    }
    .faq-question {
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="contact-container">
    <h2 class="text-center mb-4 text-success">যোগাযোগ করুন</h2>

    <div class="row">
      <!-- Contact Form -->
      <div class="col-md-6">
        <form method="POST">
          <div class="mb-3">
            <label for="name" class="form-label">নাম</label>
            <input type="text" class="form-control" name="name" required />
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">ইমেইল / ফোন</label>
            <input type="text" class="form-control" name="email" required />
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">বিষয়</label>
            <input type="text" class="form-control" name="subject" required />
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">বার্তা</label>
            <textarea class="form-control" name="message" rows="5" required></textarea>
          </div>
          <button type="submit" class="btn btn-send">পাঠান</button>
        </form>
      </div>

      <!-- Contact Info -->
      <div class="col-md-6">
        <h5 class="fw-bold mb-3">কোম্পানির তথ্য</h5>
        <p class="info-box"><i class="fas fa-map-marker-alt"></i>ঢাকা, বাংলাদেশ</p>
        <p class="info-box"><i class="fas fa-phone"></i>+880 1730 202960</p>
        <p class="info-box"><i class="fas fa-envelope"></i>support@smarttkrishi.com</p>
        <p class="info-box"><i class="fas fa-clock"></i>সাপোর্ট সময়: সকাল ৯টা - সন্ধ্যা ৬টা</p>

        <h5 class="fw-bold mt-4 mb-2">আমাদের অনুসরণ করুন</h5>
        <div class="d-flex gap-3">
          <a href="#"><i class="fab fa-facebook fa-lg"></i></a>
          <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
          <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#"><i class="fab fa-youtube fa-lg"></i></a>
          <a href="#"><i class="fab fa-whatsapp fa-lg"></i></a>
        </div>
      </div>
    </div>

    <!-- Google Map -->
    <div class="mt-5">
      <h5 class="fw-bold">আমাদের অবস্থান</h5>
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9028432032645!2d90.39134531543113!3d23.750903994836735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8917c4b8cc7%3A0xf74f6ef2f9a8052e!2sDhaka!5e0!3m2!1sen!2sbd!4v1686943054707!5m2!1sen!2sbd" width="100%" height="250" style="border:0; border-radius: 15px;" allowfullscreen="" loading="lazy"></iframe>
    </div>

    <!-- FAQ -->
       <div class="mt-5">
          <h5 class="fw-bold">সাধারণ জিজ্ঞাসা</h5>
          <ul>
            <li><span class="faq-question">কিভাবে একটি ফসলের উপযুক্ত মৌসুম নির্ধারণ করবো?</span><br>SmartKrishi তে প্রতিটি ফসলের জন্য মৌসুমি রোডম্যাপ দেয়া আছে যা আপনাকে সঠিক সময়ে চাষ করতে সাহায্য করবে।</li>
            <li><span class="faq-question">কৃষি বিশেষজ্ঞের পরামর্শ কিভাবে পাবো?</span><br>ড্যাশবোর্ড থেকে 'কৃষি-বিশেষজ্ঞদের সেবা' মেনুতে ক্লিক করে আপনি বিশেষজ্ঞদের তালিকা ও যোগাযোগ পদ্ধতি দেখতে পাবেন।</li>
            <li><span class="faq-question">ফসলের রোগ শনাক্ত করার উপায় কী?</span><br>‘স্মার্ট ফসল ডাক্তার’ ফিচার ব্যবহার করে আপনার গাছের ছবি আপলোড করে আপনি রোগ ও সমাধান জানতে পারবেন।</li>
            <li><span class="faq-question">SmartKrishi কি মোবাইল থেকে ব্যবহার করা যায়?</span><br>হ্যাঁ, এটি পুরোপুরি মোবাইল ফ্রেন্ডলি এবং খুব শীঘ্রই অ্যাপ চালু হবে।</li>
          </ul>
        </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
