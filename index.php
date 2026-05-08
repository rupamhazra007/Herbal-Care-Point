<?php
session_start();
include 'db.php'; 

$is_logged_in = isset($_SESSION['username']) ? 'true' : 'false';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// --- CART COUNT CALCULATION ---
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}

// --- PRODUCT FETCHING ---
$sql = "SELECT * FROM products WHERE 1";

if (isset($_GET['category']) && $_GET['category'] != 'all') {
    $cat = $conn->real_escape_string($_GET['category']);
    $sql .= " AND cat = '$cat'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    $sql .= " AND name LIKE '%$search_term%'";
}

$result = $conn->query($sql);
$filtered_products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $filtered_products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Shop | HerbalCare Point</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { 
            --primary: #1e5631; 
            --accent: #2c7e47;
            --secondary: #d4a373; 
            --light: #f8fbf9; 
            --dark: #0f2b1d; 
            --glass: rgba(255, 255, 255, 0.9);
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--light); overflow-x: hidden; scroll-behavior: smooth; }
        .brand-font { font-family: 'Playfair Display', serif; }

        .navbar { 
            background: var(--glass); 
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0,0,0,0.03); 
            padding: 12px 0; 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
        }
        .nav-link { color: var(--dark); font-weight: 500; transition: 0.3s; position: relative; }
        
        .cart-wrapper { position: relative; display: inline-block; }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4d4d;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .hero-section {
            height: 500px;
            background: linear-gradient(rgba(15, 43, 29, 0.7), rgba(30, 86, 49, 0.4)), url('https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1950&q=80');
            background-size: cover; background-position: center; background-attachment: fixed;
            display: flex; align-items: center; justify-content: center; color: white;
        }

        .filter-box { 
            background: white; padding: 25px; border-radius: 20px; 
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }
        
        .product-card {
            border: none; border-radius: 20px; overflow: hidden; background: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            height: 100%; display: flex; flex-direction: column; position: relative;
        }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        
        .product-img { height: 240px; object-fit: cover; width: 100%; transition: 0.6s; }
        .product-card:hover .product-img { transform: scale(1.1); }
        
        .badge-herbal { 
            background: var(--primary); color: white; padding: 6px 15px; 
            border-radius: 50px; font-size: 0.75rem; position: absolute; top: 15px; left: 15px; z-index: 2; 
        }

        .btn-buy { 
            background: var(--primary); color: white; border: none; width: 100%; 
            border-radius: 50px; padding: 10px; font-weight: 600; margin-bottom: 8px; 
            transition: 0.3s; box-shadow: 0 4px 15px rgba(30, 86, 49, 0.2);
        }
        .btn-buy:hover { background: var(--dark); color: white; }
        
        .btn-cart { 
            background: white; color: var(--primary); border: 2px solid var(--primary); 
            width: 100%; border-radius: 50px; padding: 10px; font-weight: 600; transition: 0.3s; 
        }
        .btn-cart:hover { background: var(--primary); color: white; }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .floating-leaf { animation: floating 3s ease-in-out infinite; }

        .search-input { 
            border-radius: 50px; padding: 15px 30px; border: none; 
            width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.15); outline: none; 
        }
    </style>
</head>
<body>

    <div class="loader-wrapper" id="loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 9999; display: flex; justify-content: center; align-items: center;">
        <div class="text-center">
            <div class="floating-leaf"><i class="fas fa-leaf fa-4x text-success"></i></div>
            <h3 class="mt-4 brand-font text-success">HerbalCare Point</h3>
            <div class="spinner-border text-success mt-2" role="status"></div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand brand-font text-success fs-3 fw-bold" href="index.php">
                <i class="fas fa-leaf me-2"></i>HerbalCare
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link mx-2" href="index.php">Home</a></li>
                    
                    <?php if($is_logged_in == 'true'): ?>
                        <li class="nav-item">
                            <a class="nav-link mx-2 text-success fw-bold" href="my_orders.php">
                                <i class="fas fa-shopping-bag me-1"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="welcome.php" class="btn btn-outline-success rounded-pill px-4 btn-sm">
                                <i class="fas fa-user-circle me-1"></i> Dashboard
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item ms-2">
                        <a href="javascript:void(0)" onclick="addToCart(0, true)" class="cart-wrapper btn btn-success rounded-circle shadow-sm">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if($cart_count > 0): ?>
                                <span class="cart-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <?php if($is_logged_in == 'true'): ?>
                        <li class="nav-item ms-3">
                            <a href="logout.php" class="btn btn-link text-danger text-decoration-none fw-bold small">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-3">
                            <a href="login.php" class="btn btn-success rounded-pill px-4 btn-sm shadow-sm">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container text-center">
            <h1 class="display-2 fw-bold brand-font mb-3" data-aos="fade-down">Pure Nature, Pure You</h1>
            <p class="lead mb-4" data-aos="fade-up" data-aos-delay="200">Premium Herbal Products for your Wellness</p>
            <div class="search-container" data-aos="zoom-in" data-aos-delay="400">
                <form action="index.php" method="GET" class="position-relative max-width-600 mx-auto">
                    <input type="text" name="search" class="search-input" placeholder="Search organic products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-success rounded-pill px-4 position-absolute shadow" style="right: 10px; top: 8px; height: 80%;">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="container py-5">
        <div class="card border-0 rounded-4 overflow-hidden mb-5 shadow-sm" style="background: linear-gradient(45deg, #1e5631, #2c7e47);" data-aos="flip-up">
            <div class="card-body p-5 text-white d-md-flex align-items-center justify-content-between">
                <div>
                    <h2 class="brand-font display-6 fw-bold">Exclusive Offer!</h2>
                    <p class="fs-5 opacity-75">Get 30% discount on all wellness products</p>
                    <div class="mt-3"><span class="bg-white text-success px-4 py-2 rounded-pill fw-bold">Code: HERBAL30</span></div>
                </div>
                <div class="mt-4 mt-md-0">
                    <a href="index.php?category=wellness" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-success">Shop Now</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3">
                <div class="filter-box sticky-top" style="top: 100px;" data-aos="fade-right">
                    <h5 class="fw-bold mb-4 text-success border-bottom pb-2">Category</h5>
                    <form action="index.php" method="GET">
                        <?php if(isset($_GET['search'])): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                        <?php endif; ?>
                        <div class="category-list">
                            <?php 
                                $categories = ['all' => 'All Products', 'hair' => 'Hair Care', 'skin' => 'Skin Care', 'wellness' => 'Wellness'];
                                foreach($categories as $key => $val):
                                    $checked = (isset($_GET['category']) && $_GET['category'] == $key) || (!isset($_GET['category']) && $key == 'all') ? 'checked' : '';
                            ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="category" value="<?= $key ?>" id="cat<?= $key ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="cat<?= $key ?>"><?= $val ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-success w-100 rounded-pill mt-3 py-2 shadow-sm">Apply Filters</button>
                        <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill mt-2 py-2 btn-sm">Reset All</a>
                    </form>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="brand-font fw-bold">
                        <?php echo (isset($_GET['search']) && !empty($_GET['search'])) ? 'Search: '.htmlspecialchars($_GET['search']) : 'Our Collection'; ?>
                    </h3>
                    <div class="text-muted small bg-white px-3 py-1 rounded-pill shadow-sm border">
                        Showing <strong><?php echo count($filtered_products); ?></strong> items
                    </div>
                </div>

                <div class="row g-4">
                    <?php if(empty($filtered_products)): ?>
                        <div class="col-12 text-center py-5" data-aos="fade-up">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="120" class="mb-4 opacity-50" alt="no products">
                            <h4 class="text-muted">Oops! No products found.</h4>
                        </div>
                    <?php else: ?>
                        <?php foreach($filtered_products as $p): ?>
                        <div class="col-lg-4 col-md-6" data-aos="fade-up">
                            <div class="product-card shadow-sm">
                                <?php if(!empty($p['badge'])): ?>
                                    <span class="badge-herbal shadow-sm"><?php echo htmlspecialchars($p['badge']); ?></span>
                                <?php endif; ?>
                                <div class="overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($p['img']); ?>" class="product-img" alt="product" loading="lazy">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-light text-success border small"><?= ucfirst($p['cat']) ?></span>
                                        <span class="text-warning small"><i class="fas fa-star"></i> <?= $p['rating'] ?></span>
                                    </div>
                                    <h5 class="product-title text-truncate mb-2"><?= htmlspecialchars($p['name']) ?></h5>
                                    <p class="small text-muted mb-3" style="height: 40px; overflow: hidden;">
                                        <?= !empty($p['description']) ? htmlspecialchars(mb_strimwidth($p['description'], 0, 65, "...")) : "Natural care for your body." ?>
                                    </p>
                                    <div class="mb-4">
                                        <span class="price fs-4">₹<?= $p['price'] ?></span>
                                        <span class="old-price ms-2 text-decoration-line-through text-muted">₹<?= $p['old_price'] ?></span>
                                    </div>
                                    <div class="mt-auto">
                                        <button onclick="handleBuy(<?= $p['id'] ?>)" class="btn btn-buy shadow-sm">Buy Now</button>
                                        <button onclick="addToCart(<?= $p['id'] ?>)" class="btn btn-cart">
                                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
        window.addEventListener('load', function(){
            const loader = document.getElementById('loader');
            loader.style.transition = '0.5s';
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 500);
        });

        var isLoggedIn = <?php echo $is_logged_in; ?>;
        function addToCart(id, isNav = false) {
            if (!isLoggedIn) return showLoginAlert();
            window.location.href = isNav ? 'cart.php' : 'cart.php?action=add&id=' + id;
        }
        function handleBuy(id) {
            if (!isLoggedIn) return showLoginAlert();
            window.location.href = 'buy.php?id=' + id;
        }
        function showLoginAlert() {
            Swal.fire({
                title: 'Please Login',
                text: 'Join us to purchase pure herbal products!',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#1e5631',
                confirmButtonText: 'Login Now'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'login.php';
            });
        }
    </script>
</body>
</html>