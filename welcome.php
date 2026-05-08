<?php
session_start();

// লগিন স্ট্যাটাস চেক
$is_logged_in = isset($_SESSION['username']) ? 'true' : 'false';

// আপনার চাহিদা অনুযায়ী নতুন ইমেজ লোকেশন সেট করা হলো
$products = [
    ["name" => "Kesh King Bhringraj Oil", "price" => "359", "image" => "images/herball.png"],
    ["name" => "Organic Chyawanprash", "price" => "454", "image" => "images/chawan.png"],
    ["name" => "Aloe Vera Hair Gel", "price" => "269", "image" => "images/alovera.png"],
    ["name" => "Turmeric Powder", "price" => "120", "image" => "images/tumeric.png"]
];

// Demo Categories
$categories = [
    ["title" => "Herbal Medicines", "img" => "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600"],
    ["title" => "Skin Care", "img" =>"https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?w=600&q=80"],
    ["title" => "Hair Care", "img" => "https://images.unsplash.com/photo-1522337660859-02fbefca4702?w=600"],
    ["title" => "Immunity Boosters", "img" => "https://images.unsplash.com/photo-1512069772995-ec65ed45afd6?w=600"],
    ["title" => "Herbal Supplements", "img" => "https://images.unsplash.com/photo-1471864190281-a93a3070b6de?w=600"],
    ["title" => "Organic Wellness", "img" => "https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=600"]
];
?>

<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbal Care Point | Natural Healing</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #1e5631; 
            --accent: #d4a373;  
            --light: #f4f9f4;
            --dark-bg: #0d1a10;
        }
        
        body { font-family: 'Poppins', sans-serif; background-color: #fdfbf7; overflow-x: hidden; color: #333; }
        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }
        
        /* Smooth Navbar */
        .navbar { background: rgba(255, 255, 255, 0.98); box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 15px 0; transition: 0.4s; }
        .nav-link { font-weight: 500; color: #444; margin: 0 12px; transition: 0.3s; position: relative; }
        .nav-link:hover { color: var(--primary); transform: translateY(-2px); }
        .nav-link::after { content: ''; width: 0; height: 2px; background: var(--accent); position: absolute; bottom: 0; left: 0; transition: 0.3s; }
        .nav-link:hover::after { width: 100%; }
        
        /* Hero Animation */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center; background-attachment: fixed;
            display: flex; align-items: center; justify-content: center; text-align: center; color: white;
        }
        .hero-btn { padding: 14px 40px; border-radius: 50px; font-weight: 600; transition: 0.4s; text-transform: uppercase; letter-spacing: 1px; }
        .btn-shop { background-color: var(--accent); border: none; color: white; box-shadow: 0 4px 15px rgba(212, 163, 115, 0.4); }
        .btn-shop:hover { transform: scale(1.05); background-color: #bfa15f; }

        /* Sections */
        section { padding: 100px 0; }
        .section-title { color: var(--primary); font-weight: 700; margin-bottom: 60px; position: relative; transition: 0.3s; }
        .section-title:hover { color: var(--accent); transform: scale(1.05); }
        .section-title::after { content: '✿'; color: var(--accent); position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%); font-size: 20px; }

        /* Stats Animation */
        .stats-row { background: var(--primary); color: white; padding: 60px 0; border-radius: 20px; margin-top: 50px; position: relative; z-index: 10; transition: 0.4s; }
        .stats-row:hover { box-shadow: 0 20px 40px rgba(0,0,0,0.2); transform: translateY(-5px); }
        .stat-item h2 { font-size: 3rem; font-weight: 800; color: var(--accent); }

        /* Hover Zoom for Images */
        .card, .cat-card { overflow: hidden; border-radius: 20px; transition: 0.4s ease; border: none; }
        .card img, .cat-card img { transition: 0.6s ease; }
        .card:hover img, .cat-card:hover img { transform: scale(1.1); }
        .card:hover, .cat-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }

        /* Feature Cards */
        .feature-card { padding: 40px; background: white; border-radius: 20px; transition: 0.4s; border-bottom: 4px solid transparent; height: 100%; }
        .feature-card:hover { border-bottom: 4px solid var(--accent); transform: translateY(-10px); }
        .feature-icon { font-size: 45px; color: var(--primary); margin-bottom: 25px; transition: 0.3s; }
        .feature-card:hover .feature-icon { transform: scale(1.2) rotate(10deg); color: var(--accent); }

        /* Info Cards */
        .info-card { background: white; padding: 30px; border-radius: 20px; border: 1px solid rgba(0,0,0,0.05); transition: 0.3s; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.02); text-align: center; }
        .info-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-color: var(--accent); }

        /* Developer Section Animation */
        .developer-wrapper { background: #08110a; padding: 50px 0; border-top: 1px solid rgba(212, 163, 115, 0.1); position: relative; overflow: hidden; }
        .ambient-light { position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(30, 86, 49, 0.3) 0%, rgba(0,0,0,0) 70%); border-radius: 50%; filter: blur(60px); z-index: 0; animation: floatLight 10s infinite alternate; }
        @keyframes floatLight { from { transform: translate(-10%, -10%); } to { transform: translate(20%, 20%); } }
        
        .dev-card-glass { position: relative; z-index: 1; text-align: center; display: flex; flex-direction: column; align-items: center; transition: 0.5s; }
        .dev-card-glass:hover { transform: scale(1.05); }

        .dev-name-shine {
            font-size: 2.5rem; font-weight: 800; font-family: 'Playfair Display', serif;
            background: linear-gradient(to right, #fff 20%, var(--accent) 50%, #fff 80%);
            background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: shineText 3s linear infinite;
        }
        @keyframes shineText { to { background-position: 200% center; } }

        /* Footer Link Hover */
        .footer-link { transition: 0.3s; display: block; text-decoration: none; color: #888; }
        .footer-link:hover { color: var(--accent); transform: translateX(5px); }

        /* WhatsApp Pulse */
        .whatsapp-float { position: fixed; bottom: 30px; right: 30px; background: #25d366; color: white; width: 65px; height: 65px; border-radius: 50%; font-size: 35px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4); z-index: 1000; animation: pulseGlow 2s infinite; text-decoration: none; transition: 0.3s; }
        .whatsapp-float:hover { transform: scale(1.1) rotate(15deg); }
        @keyframes pulseGlow { 0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); } 70% { box-shadow: 0 0 0 20px rgba(37, 211, 102, 0); } 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); } }

        /* Smooth Image Box */
        .consult-box img { width: 100%; height: 100%; object-fit: cover; min-height: 400px; transition: 0.5s; }
        .consult-box:hover img { filter: brightness(1.1); }
    </style>
</head>
<body>

    <a href="https://wa.me/919564740729" class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand text-success fw-bold fs-2 animate__animated animate__fadeInLeft" href="#home">
                <i class="fas fa-leaf me-2"></i>HerbalCare
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center animate__animated animate__fadeInRight">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#shop">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="#offers">Offers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#support">Support</a></li>
                    
                    <?php if(!isset($_SESSION['username'])): ?>
                        <li class="nav-item ms-lg-3"><a href="login.php" class="btn btn-outline-success rounded-pill px-4">Login</a></li>
                    <?php else: ?>
                        <li class="nav-item dropdown ms-3">
                            <a class="nav-link dropdown-toggle fw-bold text-success" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 animate__animated animate__fadeIn">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-cog me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header id="home" class="hero">
        <div class="container">
            <span class="badge bg-success px-3 py-2 rounded-pill mb-3 animate__animated animate__fadeInDown">#1 Organic Store in India</span>
            <h1 class="display-2 fw-bold mb-3 animate__animated animate__fadeInUp">Renew Your Soul With <br><span style="color: var(--accent)">Nature's Touch</span></h1>
            <p class="fs-5 mb-5 opacity-75 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">Traditional Ayurvedic healing methods combined with modern lab-tested purity.</p>
            <div class="animate__animated animate__zoomIn" style="animation-delay: 0.6s;">
                <button onclick="handleRedirect('index.php')" class="btn hero-btn btn-shop me-3">Explore Store <i class="fas fa-arrow-right ms-2"></i></button>
                <a href="#consult" class="btn hero-btn btn-outline-light">Book Consultation</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row stats-row text-center g-4" data-aos="fade-up">
            <div class="col-md-3 col-6 stat-item"><h2>15k+</h2><p class="mb-0 opacity-75">Happy Clients</p></div>
            <div class="col-md-3 col-6 stat-item"><h2>100%</h2><p class="mb-0 opacity-75">Organic Certified</p></div>
            <div class="col-md-3 col-6 stat-item"><h2>50+</h2><p class="mb-0 opacity-75">Expert Doctors</p></div>
            <div class="col-md-3 col-6 stat-item"><h2>24/7</h2><p class="mb-0 opacity-75">Customer Support</p></div>
        </div>
    </div>

    <section>
        <div class="container text-center">
            <h2 class="section-title" data-aos="fade-down">Our Core Values</h2>
            <div class="row g-4 mt-2">
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                    <div class="feature-card">
                        <i class="fas fa-seedling feature-icon"></i>
                        <h4>Pure Sourcing</h4>
                        <p class="text-muted">Directly from the Himalayan foothills to your doorstep.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                    <div class="feature-card">
                        <i class="fas fa-flask feature-icon"></i>
                        <h4>Lab Verified</h4>
                        <p class="text-muted">Every batch is tested for 100% purity and zero chemicals.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                    <div class="feature-card">
                        <i class="fas fa-hand-holding-heart feature-icon"></i>
                        <h4>Cruelty Free</h4>
                        <p class="text-muted">No animals were harmed. We love nature as much as you do.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
                    <div class="feature-card">
                        <i class="fas fa-truck-loading feature-icon"></i>
                        <h4>Eco Packaging</h4>
                        <p class="text-muted">We use biodegradable materials to keep our Earth green.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="categories" class="bg-light">
        <div class="container text-center">
            <h2 class="section-title" data-aos="fade-right">Shop By Category</h2>
            <div class="row g-4">
                <?php foreach($categories as $index => $cat): ?>
                <div class="col-lg-2 col-md-4 col-6" data-aos="flip-left" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="cat-card position-relative shadow-sm" style="height: 250px; cursor: pointer;">
                        <img src="<?php echo $cat['img']; ?>" class="w-100 h-100 object-fit-cover" alt="">
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-50 text-white">
                            <h6 class="mb-0"><?php echo $cat['title']; ?></h6>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="offers">
        <div class="container">
            <div class="deal-section text-center" data-aos="zoom-out">
                <h2 class="display-4 fw-bold mb-4">Weekly Wellness Flash Sale!</h2>
                <p class="lead mb-5">Get <span class="text-warning fw-bold">FLAT 30% OFF</span> on all Immunity Boosters.</p>
                <div class="d-flex justify-content-center gap-3 mb-5">
                    <div class="timer-box animate__animated animate__pulse animate__infinite"><span id="hours" class="d-block fs-2 fw-bold">00</span>Hours</div>
                    <div class="timer-box animate__animated animate__pulse animate__infinite" style="animation-delay: 0.2s;"><span id="minutes" class="d-block fs-2 fw-bold">00</span>Mins</div>
                    <div class="timer-box animate__animated animate__pulse animate__infinite" style="animation-delay: 0.4s;"><span id="seconds" class="d-block fs-2 fw-bold">00</span>Secs</div>
                </div>
                <button onclick="handleRedirect('index.php')" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow">Claim Offer Now</button>
            </div>
        </div>
    </section>

    <section id="shop">
        <div class="container text-center">
            <h2 class="section-title" data-aos="fade-up">Best Sellers</h2>
            <div class="row g-4">
                <?php foreach($products as $index => $prod): ?>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <div class="card p-3">
                        <img src="<?php echo $prod['image']; ?>" class="rounded-4 mb-3" style="height:200px; object-fit:cover;" onerror="this.src='https://via.placeholder.com/300x300?text=Product+Image'">
                        <h6 class="fw-bold"><?php echo $prod['name']; ?></h6>
                        <h5 class="text-success fw-bold">₹<?php echo $prod['price']; ?></h5>
                        <button onclick="handleRedirect('cart.php')" class="btn btn-success w-100 rounded-pill mt-3">Add To Cart</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="container text-center my-5" data-aos="zoom-in">
        <div class="amazon-ad-box" style="background: #fff; padding: 25px; border-radius: 20px; border: 1px dashed #ff9900; transition: 0.3s; display: inline-block; width: 100%; max-width: 600px;">
            <p class="small text-muted mb-2">Suggested Products on Amazon</p>
            <a href="https://amzn.to/3GfXzYp" target="_blank" class="text-decoration-none">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg" alt="Amazon India" style="height: 35px; margin: 15px 0;" onerror="this.src='https://via.placeholder.com/200x50?text=Visit+Amazon+India'">
                <h6 class="mt-2 text-dark" style="font-weight: 600;">Check Best Natural Supplements & Herbs</h6>
                <div class="btn btn-sm btn-warning rounded-pill px-4 fw-bold shadow-sm">Shop on Amazon <i class="fas fa-external-link-alt ms-1"></i></div>
            </a>
        </div>
    </div>

    <section id="consult" class="bg-light">
        <div class="container">
            <div class="consult-box row align-items-center g-0 bg-white rounded-5 shadow-sm overflow-hidden" data-aos="fade-up">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop" class="w-100 h-100 object-fit-cover" alt="">
                </div>
                <div class="col-lg-6 p-5" data-aos="fade-left">
                    <h2 class="fw-bold mb-4 text-success">Expert Consultation</h2>
                    <p class="text-muted mb-4">Talk to our certified Ayurvedic practitioners and get a personalized lifestyle and diet plan tailored just for you.</p>
                    <form id="callbackForm">
                        <input type="text" class="form-control rounded-pill mb-3 px-4 py-2" placeholder="Your Name" required>
                        <input type="tel" class="form-control rounded-pill mb-4 px-4 py-2" placeholder="Phone Number" required>
                        <button type="submit" class="btn btn-success w-100 rounded-pill py-2 shadow">Request Callback</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section id="support" style="background:#f4f9f4;">
        <div class="container">
            <h2 class="section-title text-center" data-aos="fade-up">Help & Policy</h2>
            <div class="row g-4">
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                    <div class="info-card">
                        <i class="fas fa-user-shield"></i>
                        <h5>Privacy Policy</h5>
                        <p>We ensure your data is 100% safe. We never share your personal information with third parties.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                    <div class="info-card">
                        <i class="fas fa-truck"></i>
                        <h5>Shipping Terms</h5>
                        <p>Fast delivery within 3-5 business days. Free shipping on orders above ₹999 across India.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                    <div class="info-card">
                        <i class="fas fa-undo-alt"></i>
                        <h5>Return & Refund</h5>
                        <p>Not satisfied? Easy 7-day return policy. Refunds are processed directly to your original payment method.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
                    <div class="info-card">
                        <i class="fas fa-headset"></i>
                        <h5>Contact Support</h5>
                        <p>24/7 Support available. Reach us via WhatsApp or email for any product related queries.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="developer-wrapper">
        <div class="ambient-light" style="top: 0; left: 0;"></div>
        <div class="container">
            <div class="dev-card-glass" data-aos="zoom-in">
                <div class="dev-line"></div>
                <h2 class="dev-name">Developed By <span class="dev-name-shine">RUPAM</span></h2>
                <div class="dev-subtext">Lead Architect & Developer</div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <div class="row g-5">
                <div class="col-lg-4" data-aos="fade-up">
                    <h4 class="text-success mb-4 fw-bold"><i class="fas fa-leaf me-2"></i>HerbalCare</h4>
                    <p class="pe-lg-5 text-start">Dedicated to bringing the purest forms of nature to your lifestyle. We believe in healing from within.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 text-start" data-aos="fade-up" data-aos-delay="100">
                    <h5>Quick Explore</h5>
                    <a href="#home" class="footer-link">Home</a>
                    <a href="#shop" class="footer-link">Shop Online</a>
                    <a href="#offers" class="footer-link">Offers</a>
                    <a href="#support" class="footer-link">Policies</a>
                </div>
                <div class="col-lg-3 col-md-6 text-start" data-aos="fade-up" data-aos-delay="200">
                    <h5>Support Center</h5>
                    <a href="#support" class="footer-link">Privacy Policy</a>
                    <a href="#support" class="footer-link">Shipping</a>
                    <a href="#support" class="footer-link">Return & Refund</a>
                    <a href="#support" class="footer-link">Contact</a>
                </div>
                <div class="col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <h5 class="text-start">Newsletter</h5>
                    <div class="input-group">
                        <input type="email" class="form-control rounded-start-pill border-0" placeholder="Email Address">
                        <button class="btn btn-success rounded-end-pill px-4">Join</button>
                    </div>
                </div>
            </div>
            <hr class="mt-5 border-secondary opacity-25">
            <div class="text-center small pt-3 opacity-50">
                &copy; 2025 Herbal Care Point. Engineered by Rupam.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Init Scroll Animation
        AOS.init({ 
            duration: 1000, 
            once: false, 
            mirror: true 
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.style.padding = '10px 0';
                nav.style.background = '#fff';
            } else {
                nav.style.padding = '15px 0';
                nav.style.background = 'rgba(255, 255, 255, 0.98)';
            }
        });

        var isLoggedIn = <?php echo $is_logged_in; ?>;

        function handleRedirect(destination) {
            if (isLoggedIn) {
                window.location.href = destination;
            } else {
                Swal.fire({
                    title: 'Login Required',
                    text: 'Join our community to access these features!',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#1e5631',
                    confirmButtonText: 'Login Now'
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = 'login.php';
                });
            }
        }

        // Consultation Form
        document.getElementById('callbackForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            Swal.fire({
                icon: 'success',
                title: 'Message sent to the team!',
                text: 'Thank you for reaching out. We will contact you shortly.',
                confirmButtonColor: '#1e5631',
                timer: 4000,
                timerProgressBar: true,
                showClass: { popup: 'animate__animated animate__backInDown' },
                hideClass: { popup: 'animate__animated animate__backOutUp' }
            });
            this.reset(); 
        });

        // Live Timer
        function startTimer() {
            const targetTime = new Date().getTime() + (24 * 60 * 60 * 1000);
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = targetTime - now;
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("hours").innerHTML = hours < 10 ? "0" + hours : hours;
                document.getElementById("minutes").innerHTML = minutes < 10 ? "0" + minutes : minutes;
                document.getElementById("seconds").innerHTML = seconds < 10 ? "0" + seconds : seconds;
                if (distance < 0) {
                    clearInterval(x);
                }
            }, 1000);
        }
        startTimer();
    </script>
</body>
</html>