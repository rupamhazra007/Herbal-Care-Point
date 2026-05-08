<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];

$sql = "SELECT * FROM orders WHERE username = '$user' ORDER BY order_date DESC";
$result = $conn->query($sql);

$total_res = $conn->query("SELECT COUNT(*) as count FROM orders WHERE username = '$user'")->fetch_assoc();
$pending_res = $conn->query("SELECT COUNT(*) as count FROM orders WHERE username = '$user' AND status = 'Pending'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Herbal Care Elite</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e5631;
            --accent: #76ba1b;
            --glass: rgba(255, 255, 255, 0.88);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), 
                        url('https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
        }

        /* Premium Glass Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
            transition: 0.4s;
        }
        .navbar:hover { background: rgba(255, 255, 255, 0.95); }
        .navbar-brand, .nav-link { color: white !important; transition: 0.3s; }
        .navbar:hover .navbar-brand { color: var(--primary) !important; }

        .brand-font { font-family: 'Playfair Display', serif; }

        /* Summary Section */
        .stat-card {
            background: var(--glass);
            border-radius: 24px;
            padding: 25px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .stat-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px rgba(0,0,0,0.4); }

        /* Advanced Order Card */
        .order-wrapper {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.5);
            margin-bottom: 35px;
            overflow: hidden;
            transition: 0.5s;
            box-shadow: 0 15px 45px rgba(0,0,0,0.2);
        }
        .order-wrapper:hover {
            border-color: var(--accent);
            box-shadow: 0 25px 55px rgba(30,86,49,0.25);
        }

        .order-header {
            background: rgba(30, 86, 49, 0.05);
            padding: 20px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed rgba(0,0,0,0.1);
        }

        .order-body { padding: 35px; }

        /* Neon Progress Bar */
        .progress-container {
            height: 10px; background: #e0e0e0; border-radius: 20px; position: relative; margin: 30px 0 15px;
            overflow: hidden;
        }
        .progress-bar-inner {
            height: 100%; border-radius: 20px; transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(90deg, var(--primary), var(--accent));
            box-shadow: 0 0 15px rgba(118,186,27,0.4);
        }

        /* Buttons */
        .btn-elite {
            background: linear-gradient(135deg, #1e5631 0%, #76ba1b 100%);
            color: white; border: none; padding: 12px 35px; border-radius: 100px;
            font-weight: 700; text-decoration: none; display: inline-block;
            transition: 0.4s; box-shadow: 0 10px 20px rgba(30,86,49,0.3);
        }
        .btn-elite:hover {
            transform: scale(1.08); color: white;
            box-shadow: 0 15px 30px rgba(118,186,27,0.4);
        }

        .status-pill {
            font-size: 0.7rem; font-weight: 800; padding: 6px 18px; border-radius: 50px;
            letter-spacing: 1px; text-transform: uppercase;
        }
        .status-Pending { background: #fff3cd; color: #856404; animation: pulse 2s infinite; }
        .status-Delivered { background: #d1e7dd; color: #0f5132; }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }

        .leaf-float {
            position: absolute; opacity: 0.2; color: #fff; z-index: 1;
            animation: floatingLeaf 5s ease-in-out infinite;
        }
        @keyframes floatingLeaf {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(20deg); }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3 brand-font" href="index.php">
                <i class="fas fa-leaf me-2"></i> Herbal Care
            </a>
            <a href="welcome.php" class="btn btn-outline-light rounded-pill px-4 fw-bold">Dashboard</a>
        </div>
    </nav>

    <div class="container py-5">
        <header class="text-center mb-5" data-aos="zoom-out">
            <h1 class="display-3 brand-font text-white fw-bold">My Herbal Journey</h1>
            <p class="text-white-50 fs-5">Tracking your orders for a healthier tomorrow.</p>
        </header>

        <div class="row g-4 mb-5 justify-content-center">
            <div class="col-6 col-md-3" data-aos="fade-right">
                <div class="stat-card">
                    <i class="fas fa-shopping-basket fa-2x text-success mb-2"></i>
                    <div class="text-muted small fw-bold">TOTAL ORDERS</div>
                    <div class="display-6 fw-bold"><?php echo $total_res['count']; ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-left">
                <div class="stat-card" style="border-bottom: 4px solid #ffc107;">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <div class="text-muted small fw-bold text-warning">IN PROGRESS</div>
                    <div class="display-6 fw-bold"><?php echo $pending_res['count']; ?></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <?php if ($result->num_rows > 0): ?>
                    <?php 
                    $delay = 100;
                    while($row = $result->fetch_assoc()): 
                        $status_width = ($row['status'] == 'Pending') ? '35%' : (($row['status'] == 'Shipped') ? '70%' : '100%');
                    ?>
                        <div class="order-wrapper" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <div class="order-header">
                                <span class="fw-bold text-dark"><i class="fas fa-fingerprint me-2 text-success"></i> ID: #ORD-<?php echo $row['id']; ?></span>
                                <span class="status-pill status-<?php echo $row['status']; ?>">
                                    <i class="fas fa-circle-notch fa-spin me-1"></i> <?php echo $row['status']; ?>
                                </span>
                            </div>

                            <div class="order-body">
                                <div class="row g-4 align-items-center text-center text-md-start">
                                    <div class="col-md-3">
                                        <div class="text-muted small fw-bold">ORDERED ON</div>
                                        <div class="fw-bold fs-5"><?php echo date('d M, Y', strtotime($row['order_date'])); ?></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-muted small fw-bold">TOTAL VALUE</div>
                                        <div class="fw-bold fs-3 text-success">₹<?php echo number_format($row['total_price']); ?></div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-muted small fw-bold">QUANTITY</div>
                                        <div class="fw-bold"><?php echo $row['total_products']; ?> Items</div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="track_order.php?id=<?php echo $row['id']; ?>" class="btn-elite">
                                            Manage Order <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="progress-container">
                                    <div class="progress-bar-inner" style="width: <?php echo $status_width; ?>"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted fw-bold" style="font-size: 11px;">
                                    <span><i class="fas fa-check-circle text-success"></i> CONFIRMED</span>
                                    <span class="<?php echo ($row['status'] == 'Shipped' || $row['status'] == 'Delivered') ? 'text-success' : ''; ?>">SHIPPING</span>
                                    <span class="<?php echo ($row['status'] == 'Delivered') ? 'text-success' : ''; ?>">DELIVERED</span>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $delay += 100;
                    endwhile; 
                    ?>
                <?php else: ?>
                    <div class="text-center stat-card py-5" data-aos="zoom-in">
                        <i class="fas fa-box-open fa-5x text-success opacity-25 mb-4"></i>
                        <h2 class="fw-bold">No Orders Found!</h2>
                        <p class="text-muted mb-4">Your health journey hasn't started yet. Let's fix that!</p>
                        <a href="index.php" class="btn-elite">Explore Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });
        
        // Dynamic leaf particles for background
        const createLeaf = () => {
            const leaf = document.createElement('i');
            leaf.className = 'fas fa-leaf leaf-float';
            leaf.style.left = Math.random() * 100 + 'vw';
            leaf.style.top = Math.random() * 100 + 'vh';
            leaf.style.fontSize = Math.random() * 20 + 10 + 'px';
            document.body.appendChild(leaf);
        };
        for(let i=0; i<10; i++) createLeaf();
    </script>
</body>
</html>