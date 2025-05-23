<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>কৃষি ব্লগ - Agricultural Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');

        :root {
            --primary: #4caf50;
            --primary-dark: #2e7d32;
            --secondary: #8bc34a;
            --accent: #aed581;
            --light-bg: #e3f2fd;
            --text-dark: #333;
            --text-light: #fff;
            --shadow: rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, var(--light-bg), #c8e6c9);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Header */
       header {
           background: linear-gradient(90deg, #43cea2, #185a9d);
           color: var(--text-light);
           display: flex;
           justify-content: space-between;
           align-items: center;
           padding: 10px 30px;
           border-bottom: 4px solid var(--primary-dark);
           flex-wrap: wrap;
       }

       header h1 {
           font-size: 1.5rem;
           font-weight: 600;
           margin: 0;
           text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
       }

       nav {
           display: flex;
           gap: 10px;
       }

       nav a {
           color: var(--text-light);
           text-decoration: none;
           font-size: 0.9rem;
           font-weight: 500;
           padding: 6px 14px;
           border-radius: 20px;
           transition: var(--transition);
           background: rgba(255, 255, 255, 0.1);
       }

       nav a:hover {
           background-color: white;
           color: var(--primary-dark);
       }


        /* Hero Section */
        .hero {
            text-align: center;
            padding: 20px 5px;
            background: linear-gradient(to bottom right, #81c784, var(--accent));
            color: var(--text-light);
            box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.2);
        }

        .hero h2 {
            font-size: 1.5rem;
            margin-bottom: 7px;
            font-weight: 500;
        }

        .hero p {
            font-size: 1rem;
            font-weight: 200;
            max-width: 500px;
            margin: auto;
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .container h2 {
            font-size: 2.2rem;
            color: var(--primary-dark);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }

        .container p {
            font-size: 1.2rem;
            color: #444;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Buttons */
        .filter-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 24px;
            margin: 6px;
            font-size: 1rem;
            border-radius: 30px;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn:hover,
        .filter-btn.active {
            background-color: var(--primary-dark);
        }

        /* Video Cards */
        .video-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .video-card {
            border-radius: var(--radius);
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
        }

        .video-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .video-card iframe {
            width: 100%;
            height: 200px;
            border: none;
        }

        .video-info {
            padding: 15px;
            background-color: white;
        }

        .video-info h4 {
            margin: 8px 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .video-info p {
            font-size: 0.95rem;
            color: #666;
        }

        /* Footer */
        footer {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 1rem;
            margin-top: 60px;
        }

        footer a {
            color: #dcedc8;
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
        <h1>স্মার্টকৃষি</h1>
        <nav>
            <a href="dashboard.php">হোম</a>
            <a href="contact.php">আমাদের সম্পর্কে</a>
            <a href="login.php">লগইন</a>
            <a href="register.php">সাইন আপ</a>
        </nav>
    </header>



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
