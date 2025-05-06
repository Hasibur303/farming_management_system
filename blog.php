<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>কৃষি ব্লগ - Agricultural Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #e3f2fd, #bbdefb);
            color: #333;
            overflow-x: hidden;
        }

        header {
            background: linear-gradient(to right, #4caf50, #8bc34a);
            color: white;
            text-align: center;
            padding: 30px 20px;
            border-bottom: 5px solid #2e7d32;
        }
        header h1 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: 700;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
        }

        nav {
            background: linear-gradient(to right, #2e7d32, #388e3c);
            padding: 15px 0;
            display: flex;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            font-size: 1.1rem;
            padding: 8px 15px;
            border-radius: 25px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        nav a:hover {
            background: white;
            color: #4caf50;
        }

        .hero {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(to bottom right, #81c784, #aed581);
            color: white;
            box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.2);
        }
        .hero h2 {
            font-size: 2.8rem;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        .hero p {
            font-size: 1.3rem;
            margin-top: 15px;
            font-weight: 300;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .container h2 {
            font-size: 2rem;
            color: #2e7d32;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }
        .container p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
            text-align: center;
        }

        .filter-btn {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            font-size: 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .filter-btn:hover, .filter-btn.active {
            background-color: #2e7d32;
        }

        .video-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .video-card {
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .video-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .video-card iframe {
            width: 100%;
            height: 200px;
            border: none;
        }

        .video-info {
            padding: 10px;
            background-color: white;
        }

        .video-info h4 {
            margin: 5px 0;
            font-size: 1.1rem;
        }

        .video-info p {
            font-size: 0.9rem;
            color: #666;
        }

        footer {
            background: linear-gradient(to right, #4caf50, #8bc34a);
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 1rem;
            margin-top: 40px;
        }

        footer a {
            color: #aed581;
            text-decoration: none;
            font-weight: bold;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>কৃষি ব্যবস্থাপনা ব্যবস্থা</h1>
    </header>
    <nav>
        <a href="dashboard.php">হোম</a>
        <a href="login.php">লগইন</a>
        <a href="contact.php">যোগাযোগ</a>
    </nav>

    <div class="hero">
        <h2>আধুনিক কৃষিকাজের জন্য উদ্ভাবনী সমাধান</h2>
        <p>কৃষির ভবিষ্যৎ গঠনকারী সর্বশেষ প্রবণতা এবং প্রযুক্তিগুলি অন্বেষণ করুন।</p>
    </div>

    <div class="container">
        <h2>ভিডিওর মাধ্যমে কৃষি বিষয়গুলি অন্বেষণ করুন</h2>
        <p>এই কিউরেটেড ভিডিওগুলির মাধ্যমে টেকসই পদ্ধতি এবং আধুনিক কৃষি কৌশল সম্পর্কে আরও জানুন।</p>


        <div style="text-align: center; margin-bottom: 30px;">
            <input
                type="text"
                id="videoSearch"
                placeholder="ভিডিওর শিরোনাম লিখুন..."
                style="padding: 12px 20px; width: 60%; max-width: 400px; border: 2px solid #4caf50; border-radius: 30px 0 0 30px; font-size: 1rem; outline: none;"
            >
            <button
                onclick="filterVideos()"
                style="padding: 12px 20px; background-color: #4caf50; color: white; border: none; font-size: 1rem; border-radius: 0 30px 30px 0; cursor: pointer;"
            >
                খুঁজুন
            </button>
        </div>


        <script>
            function filterVideos() {
                const query = document.getElementById('videoSearch').value.toLowerCase();
                const videoCards = document.querySelectorAll('.video-card');

                videoCards.forEach(card => {
                    const title = card.querySelector('h4').textContent.toLowerCase();
                    card.style.display = title.includes(query) ? 'block' : 'none';
                });
            }
        </script>



        <!-- Filter Buttons -->
        <div style="text-align: center; margin-bottom: 30px;">
            <button class="filter-btn active" data-filter="all">সব</button>
            <button class="filter-btn" data-filter="প্রযুক্তি">প্রযুক্তি</button>
            <button class="filter-btn" data-filter="গবাদি">পশু-পাখি</button>
            <button class="filter-btn" data-filter="ছাদ বাগান">ছাদ বাগান</button>
        </div>

        <!-- Video Cards -->
        <div class="video-container">
            <div class="video-card" data-category="প্রযুক্তি">
                <iframe src="https://www.youtube.com/embed/Vf_shMr3pbw" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>বাংলাদেশে কি অর্গানিক ফার্মিং সম্ভব? | Rise of Organic Farming in Bangladesh</h4>
                    <p>AgriTech BD • 850K views • 8 months ago</p>
                </div>
            </div>

            <div class="video-card" data-category="প্রযুক্তি">
                <iframe src="https://www.youtube.com/embed/jo8Joe8XOB4" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>স্মার্ট কৃষি উদ্যোক্তার সাফল্য | Ami khamari | Channel24</h4>
                    <p>BD Krishi Channel • 670K views • 2 years ago</p>
                </div>
            </div>

            <div class="video-card" data-category="ধান">
                <iframe src="https://www.youtube.com/embed/0BxQSe9pHrY" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>দেশের প্রথম বাণিজ্যিক আনার বাগান | Shykh Seraj | Channel i |</h4>
                    <p>Green Field BD • 300K views • 10 months ago</p>
                </div>
            </div>

            <div class="video-card" data-category="সবজি">
                <iframe src="https://www.youtube.com/embed/4ZGoTTwKUCY" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>শীতে চুয়াডাঙ্গার বিখ্যাত কলস গুড় || Panorama Documentary</h4>
                    <p>Agri Village BD • 500K views • 1 year ago</p>
                </div>
            </div>

            <div class="video-card" data-category="গবাদি">
                <iframe src="https://www.youtube.com/embed/ERS1RvMVyQk" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>১৬`শ টাকা পুঁজিতে এখন কোটিপতি | Duck Farming | Nagorik TV Special</h4>
                    <p>Milk Farm BD • 200K views • 1.5 years ago</p>
                </div>
            </div>


            <div class="video-card" data-category="প্রযুক্তি">
                <iframe src="https://www.youtube.com/embed/iwl58ID80Vs" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>বাংলাদেশে প্রযুক্তি নির্ভর ক্যাটেল ফার্ম । Nahar Dairy । Technologically Advanced Cattle Ranch in Bd</h4>
                    <p>Success Farming BD • 290K views • 1 year ago</p>
                </div>
            </div>

            <div class="video-card" data-category="প্রযুক্তি">
                <iframe src="https://www.youtube.com/embed/aDF3Khvhlpg" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>Floating farms in Bangladesh enables year-round farming</h4>
                    <p>Smart Agro BD • 150K views • 6 months ago</p>
                </div>
            </div>

            <div class="video-card" data-category="প্রযুক্তি">
                <iframe src="https://www.youtube.com/embed/y8LoUy4DsqU" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>পিরোজপুরের নাজিরপুরে ভাসমান চাষ | Shykh Seraj | Channel i |</h4>
                    <p>AgroBangla • 240K views • 1 year ago</p>
                </div>
            </div>

            <div class="video-card" data-category="ধান">
                <iframe src="https://www.youtube.com/embed/samPcxjxq90" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>হযরতের ১০০০ বিঘা আয়তনের ফল বাগান | Shykh Seraj | Channel i </h4>
                    <p>BD Farmers Hub • 190K views • 1 year ago</p>
                </div>
            </div>

            <div class="video-card" data-category="গবাদি">
                <iframe src="https://www.youtube.com/embed/sZHqD6j-46g" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>বেইজিং হাঁসের খামারে</h4>
                    <p>Green Bangladesh • 270K views • 8 months ago</p>
                </div>
            </div>

            <div class="video-card" data-category="গবাদি">
                <iframe src="https://www.youtube.com/embed/kWrOuyGCU68" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>ইউটিউবে কোয়েল পালন শিখে মাসে আয় লাখ টাকা | Manikganj | Young Entrepreneur | Koel Bird | Somoy TV</h4>
                    <p>Livestock BD • 300K views • 2 years ago</p>
                </div>
            </div>

            <div class="video-card" data-category="ছাদ বাগান">
                <iframe src="https://www.youtube.com/embed/SM1n1KwCgFw" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>ছাদে ছাদে সবজি আর ফলের বাগান | Rooftoop garden | Faridpur | Ekhon TV</h4>
                    <p>AgriTech Now • 200K views • 1 year ago</p>
                </div>
            </div>

            <div class="video-card" data-category="গবাদি">
                <iframe src="https://www.youtube.com/embed/tVF91XPLvbg" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>বারান্দায় ৭০ জোড়া ফিঞ্চ পাখি পালন করে সফল খামারি হাবিবুল্লাহ | #birds #finch</h4>
                    <p>Bd Birds Care • 1.2M views • 1 year ago</p>
                </div>
            </div>


            <div class="video-card" data-category="ছাদ বাগান">
                <iframe src="https://www.youtube.com/embed/S7QEPIQNrAQ" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>ছাদে কংক্রিটের পার্মানেন্ট টব বানানোর সহজ উপায়, making of rooftop garden</h4>
                    <p>Krishoker Bondhu • 120K views • 10 months ago</p>
                </div>
            </div>

            <div class="video-card" data-category="ছাদ বাগান">
                <iframe src="https://www.youtube.com/embed/5_9emMKmQG4" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>ছাদবাগান নাকি মিনি পার্ক? জানতে হলে দেখতে হবে</h4>
                    <p>Machinery BD • 95K views • 1 year ago</p>
                </div>
            </div>
            <div class="video-card" data-category="গবাদি">
                <iframe src="https://www.youtube.com/embed/bh-9tpVoZVo" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>ইউটিউব দেখে গরুর খামার করে ঘুরে দাঁড়িয়েছেন জেসমিন | Cow Farming | Somoy TV</h4>
                    <p>Somoy TV • 1.2M views • 1 year ago</p>
                </div>
            </div>


           <div class="video-card" data-category="প্রযুক্তি">
               <iframe src="https://www.youtube.com/embed/MTB5AuEHqMk" allowfullscreen></iframe>
               <div class="video-info">
                   <h4>আধুনিক করলা চাষ পদ্ধতি মাটি ছাড়া পানির উপরে</h4>
                   <p>Probashi Top BD • 237K views • 3 days ago</p>
               </div>
           </div>


            <div class="video-card" data-category="প্রযুক্তি">
                <iframe src="https://www.youtube.com/embed/7tYpAIn1G-8" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>স্বল্প খরচে আধুনিক কৃষি প্রযুক্তি | Modern Agricultural Technology in Bangladesh</h4>
                    <p>কৃষি টিভি • 1.2M views • 1 year ago</p>
                </div>
            </div>
            <div class="video-card" data-category="প্রযুক্তি">
                <iframe src="https://www.youtube.com/embed/AiS8LI8Vp_k" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>স্মার্ট কৃষি প্রযুক্তি | Smart Agriculture Technology in Bangladesh</h4>
                    <p>কৃষি সংবাদ • 950K views • 10 months ago</p>
                </div>
            </div>



            <div class="video-card" data-category="ছাদ বাগান">
                <iframe src="https://www.youtube.com/embed/447PEbmVHRw" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>খিলগাঁওয়ের রুমানা খানের ছাদকৃষি | পর্ব ১১০ | Shykh Seraj | Channel i </h4>
                    <p>Bangla Krishi TV • 85K views • 11 months ago</p>
                </div>
            </div>

            <div class="video-card" data-category="ছাদ বাগান">
                <iframe src="https://www.youtube.com/embed/wN1ZwqVeePk" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>উত্তরায় কানিজ ফাতিমা ফেরদৌসির ছাদকৃষি | পর্ব ৩৩৯ | Shykh Seraj | Channel i</h4>
                    <p>Drone Farming BD • 110K views • 1 year ago</p>
                </div>
            </div>

            <div class="video-card" data-category="ছাদ বাগান">
                <iframe src="https://www.youtube.com/embed/tU9HhsgtVs4" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>Deepto Krishi | EP-1549 | অ্যাকোয়াপনিক্স পদ্ধতিতে ছাদেই চাষ, ছাদচাষিদের বারো মাস!! | Deepto TV</h4>
                    <p>Krishi Academy BD • 130K views • 6 months ago</p>
                </div>
            </div>

            <div class="video-card" data-category="গবাদি">
                <iframe src="https://www.youtube.com/embed/tY5YskhnKUU" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>স্বল্প বিনিয়োগে পরিকল্পিত গরুর খামার- র.ই মানিক চিত্রপুরী। R.I. MANIK CHITROPURI</h4>
                    <p>চিত্রপুরী • 1.1M views • 2 years ago</p>
                </div>
            </div>


            <div class="video-card" data-category="ছাদ বাগান">
                <iframe src="https://www.youtube.com/embed/9ArHSod6I_o" allowfullscreen></iframe>
                <div class="video-info">
                    <h4>মোহাম্মপুরে আসফিয়া সাবিনার ছাদকৃষি | পর্ব ৩০৬ | Shykh Seraj | Channel i |</h4>
                    <p>Mushroom BD • 170K views • 1 year ago</p>
                </div>
            </div>

        </div>
    </div>

    <footer>
        <p>&copy; ২০২৫ কৃষি ব্যবস্থাপনা ব্যবস্থা। সর্বস্বত্ব সংরক্ষিত।</p>
        <p><a href="privacy-policy.html">গোপনীয়তা নীতি</a> | <a href="terms-of-service.html">পরিষেবার শর্তাবলী</a></p>
    </footer>

    <script>
        const filterButtons = document.querySelectorAll('.filter-btn');
        const videoCards = document.querySelectorAll('.video-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filter = button.getAttribute('data-filter');

                videoCards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    if (filter === 'all' || category.includes(filter)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
