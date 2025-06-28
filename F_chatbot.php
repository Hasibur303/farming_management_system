<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>এআই চ্যাট বট | SmartKirshi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ---------- Theme Variables ---------- */
        :root {
            --sidebar-collapsed: 70px;
            --sidebar-expanded: 240px;
            --bg-dark: #121826;             /* page background */
            --bg-sidebar: #0d111b;          /* sidebar background */
            --bg-card: #1f2937;             /* chat card */
            --accent: #2E7D32;
            --accent-light: #66BB6A;
            --text-light: #f1f1f1;
        }

        /* ---------- Base ---------- */
        * {box-sizing: border-box;}
        body   {margin: 0; background: var(--bg-dark); color: var(--text-light); font-family: 'Segoe UI', sans-serif;}
        a      {text-decoration: none;}

        /* ---------- Header ---------- */
        header {
            height: 64px;
            position: fixed; top: 0; left: 0; right: 0; z-index: 999;
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 24px;
            background: linear-gradient(90deg, var(--accent), var(--accent-light));
            color: #fff;
        }
        header h1 {margin: 0; font-size: 1.5rem;}

        /* ---------- Sidebar ---------- */
        .sidebar {
            position: fixed; top: 64px; left: 0; bottom: 0; z-index: 998;
            width: var(--sidebar-collapsed); overflow: hidden;
            background: var(--bg-sidebar);
            transition: width .3s;
        }
        .sidebar:hover               {width: var(--sidebar-expanded);}
        .sidebar h2                  {color: #9aa4b4; opacity: .6; margin: 0; padding: 24px 20px 10px; font-size: 1rem; text-transform: uppercase;}
        .sidebar a                   {display: flex; align-items: center; gap: 14px; padding: 14px 22px; color: var(--text-light); font-size: .95rem; white-space: nowrap; transition: background .2s;}
        .sidebar a:hover,
        .sidebar a.active            {background: #1a2332;}
        .sidebar .icon               {width: 24px; text-align: center; font-size: 1.1rem;}
        .sidebar .text               {opacity: 0; transition: opacity .25s;}
        .sidebar:hover .text         {opacity: 1;}

        /* ---------- Main ---------- */
        .main-content {
            margin-top: 64px;
            margin-left: var(--sidebar-collapsed);
            padding: 20px;
            transition: margin-left .3s;
        }
        .sidebar:hover ~ .main-content {margin-left: var(--sidebar-expanded);}

        /* ---------- Chat ---------- */
        .chat-container       {max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; height: calc(100vh - 100px - 64px);}
        .chat-box             {flex: 1; background: var(--bg-card); border-radius: 10px; padding: 20px; overflow-y: auto; box-shadow: 0 2px 8px rgba(0,0,0,.4);}
        .message              {display: flex; gap: 10px; margin-bottom: 16px;}
        .bubble               {max-width: 80%; padding: 12px 16px; border-radius: 14px;}
        .msg-user .bubble     {background: var(--accent-light); color: #000; margin-left: auto;}
        .msg-bot  .bubble     {background: #374151;}
        .input-area           {display: flex; gap: 10px; margin-top: 15px;}
        .input-area input     {flex: 1; padding: 10px 14px; border-radius: 8px; border: none; outline: 0;}
        .input-area button    {padding: 10px 18px; border: none; border-radius: 8px; font-weight: 600; color: #fff;
                                background: linear-gradient(90deg, var(--accent), var(--accent-light));}

        /* Loading animation */
        .typing-indicator {
            display: inline-block;
            padding: 12px 16px;
            background: #374151;
            border-radius: 14px;
        }
        .typing-indicator span {
            height: 8px;
            width: 8px;
            background: #9ca3af;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
            animation: bounce 1.5s infinite ease-in-out;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes bounce {
            0%, 60%, 100% {transform: translateY(0);}
            30% {transform: translateY(-5px);}
        }
    </style>
</head>
<body>

<!-- ---------- Header ---------- -->
<header>
    <h1>এআই চ্যাট বট</h1>
    <div class="user-info">
        <span>স্বাগতম, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
    </div>
</header>

<!-- ---------- Sidebar ---------- -->
<div class="sidebar">
    <h2>ন্যাভিগেশন</h2>
    <a href="farmer.php"><i class="fas fa-wallet icon"></i><span class="text">ড্যাশবোর্ড</span></a>
    <a href="F_Smart_Crop_Doctor.php"><i class="fas fa-stethoscope icon"></i><span class="text">স্মার্ট ফসল ডাক্তার</span></a>
    <a href="Agrologist_List.php"><i class="fas fa-tree icon"></i><span class="text">কৃষি‑বিশেষজ্ঞদের সেবা</span></a>
    <a href="F_article.php"><i class="fas fa-pen icon"></i><span class="text">প্রবন্ধ</span></a>
    <a href="F_chatbot.php" class="active"><i class="fas fa-robot icon"></i><span class="text">এআই চ্যাট বট</span></a>
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

<!-- ---------- Main Content ---------- -->
<div class="main-content">
    <div class="chat-container">
        <div id="chatBox" class="chat-box">
            <div class="message msg-bot">
                <div class="bubble">
                    স্বাগতম! আমি SmartKirshi এআই সহকারী। আপনি বাংলা বা ইংরেজি ভাষায় কৃষি সম্পর্কিত যে কোনো প্রশ্ন করতে পারেন। কিভাবে আমি আপনাকে সাহায্য করতে পারি?
                </div>
            </div>
        </div>

        <div class="input-area">
            <input type="text" id="userInput" placeholder="আপনার প্রশ্ন লিখুন…" autocomplete="off">
            <button id="sendBtn"><i class="fas fa-paper-plane"></i> প্রেরণ</button>
        </div>
    </div>
</div>

<!-- ---------- JS ---------- -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function () {
    const welcomeMessages = [

        "আসসালামু আলাইকুম! আমি SmartKirshi এর এআই চ্যাটবট। কৃষি সম্পর্কিত যে কোনো প্রশ্ন করুন।",
        "আসসালামু আলাইকুম! আমি আপনার কৃষি সহায়ক। আপনি কি জানতে চান?"
    ];

    // Show initial message randomly
    const initMessage = welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)];
    addMessage(initMessage, 'bot');

    // Append chat bubbles
    function addMessage(text, sender = 'bot') {
        const cls = sender === 'user' ? 'msg-user' : 'msg-bot';
        const html = `<div class="message ${cls}"><div class="bubble">${text}</div></div>`;
        $('#chatBox').append(html).scrollTop($('#chatBox')[0].scrollHeight);
    }

    // Show typing indicator
    function showTyping() {
        const typingHtml = `<div class="message msg-bot"><div class="bubble typing-indicator"><span></span><span></span><span></span></div></div>`;
        $('#chatBox').append(typingHtml).scrollTop($('#chatBox')[0].scrollHeight);
        return $('#chatBox .typing-indicator').last().parent().parent();
    }

    // Hide typing indicator
    function hideTyping(indicator) {
        indicator.remove();
    }

    // Send message to server
    function send() {
        const text = $('#userInput').val().trim();
        if (!text) return;

        addMessage(text, 'user');
        $('#userInput').val('');

        const typingIndicator = showTyping();

        $.ajax({
            url: 'chatbot_api.php',  // 🔁 Path Fixed Here (very important!)
            type: 'POST',
            data: { message: text },
            dataType: 'json',
            success: function(response) {
                hideTyping(typingIndicator);
                if (response.reply) {
                    addMessage(response.reply, 'bot');
                } else {
                    addMessage('দুঃখিত, আমি আপনার প্রশ্নের উত্তর দিতে পারিনি। অনুগ্রহ করে আবার চেষ্টা করুন।', 'bot');
                }
                console.log('Bot response:', response);
            },
            error: function(xhr, status, error) {
                hideTyping(typingIndicator);
                addMessage('❌ সার্ভারের সাথে সংযোগে সমস্যা হয়েছে। অনুগ্রহ করে পরে আবার চেষ্টা করুন।', 'bot');
                console.error('AJAX Error:', xhr.responseText || error);
            }
        });
    }

    // Event handlers
    $('#sendBtn').on('click', send);
    $('#userInput').on('keypress', function(e) {
        if (e.which === 13) send();
    });

    // Focus input field
    $('#userInput').focus();
});
</script>

</body>
</html>