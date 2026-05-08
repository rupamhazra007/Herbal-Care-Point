<?php
session_start();
// ডাটাবেস কানেকশন ফাইল ইনক্লুড করা হলো
include 'db_connect.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$fullname = $user; 
try {
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()) {
        $fullname = !empty($row['fullname']) ? $row['fullname'] : $user;
    }
} catch (Exception $e) { }

$order_count = 0;
$stmt_order = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE username = ?");
$stmt_order->bind_param("s", $user);
$stmt_order->execute();
$res_order = $stmt_order->get_result();
if($row_o = $res_order->fetch_assoc()) {
    $order_count = $row_o['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Profile | HerbalCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #11321d;
            --accent-light: #82CD47;
            --glass-bg: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.25);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(17, 50, 29, 0.7), rgba(0, 0, 0, 0.8)), 
                        url('https://images.unsplash.com/photo-1473448912268-2022ce9509d8?q=80&w=2041&auto=format&fit=crop') center/cover no-repeat fixed;
            overflow-y: auto;
            padding: 40px 15px;
        }

        .profile-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            width: 100%;
            max-width: 480px;
            padding: 50px 35px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            text-align: center;
            color: #fff;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* লোগো এবং ব্র্যান্ড নেম স্টাইল */
        .brand-logo-area {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 25px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .brand-logo-area i {
            color: var(--accent-light);
        }

        .profile-img-container {
            width: 130px;
            height: 130px;
            margin: 0 auto 20px;
            position: relative;
        }

        .profile-img-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--accent-light);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .online-status {
            width: 18px;
            height: 18px;
            background: #2ecc71;
            border: 3px solid #1c1c1c;
            border-radius: 50%;
            position: absolute;
            bottom: 8px;
            right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }

        .user-info h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 5px;
            background: linear-gradient(to right, #fff, var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-id-tag {
            background: rgba(130, 205, 71, 0.2);
            color: var(--accent-light);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 30px;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
        }

        .stat-item i {
            font-size: 1.6rem;
            color: var(--accent-light);
            margin-bottom: 10px;
        }

        .stat-item .value {
            font-size: 1.8rem;
            font-weight: 800;
            display: block;
        }

        .action-btns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-custom {
            padding: 12px;
            border-radius: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-primary-custom {
            background: var(--accent-light);
            color: var(--primary-dark);
        }

        .btn-primary-custom:hover {
            background: #fff;
            transform: translateY(-3px);
        }

        .btn-outline-custom {
            border: 2px solid var(--glass-border);
            color: #fff;
        }

        .btn-outline-custom:hover {
            background: var(--glass-bg);
            border-color: #fff;
        }

        .logout-link {
            display: inline-block;
            margin-top: 25px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .logout-link:hover { color: #ff6b6b; }
    </style>
</head>
<body>

    <div class="profile-card animate__animated animate__zoomIn">
        
        <div class="brand-logo-area">
            <i class="fas fa-leaf"></i>HerbalCare
        </div>

        <div class="profile-img-container">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullname); ?>&background=82CD47&color=11321d&size=200&bold=true" alt="User">
            <div class="online-status"></div>
        </div>

        <div class="user-info">
            <h2><?php echo htmlspecialchars($fullname); ?></h2>
            <div class="user-id-tag">@<?php echo htmlspecialchars(strtolower($user)); ?></div>
        </div>

        <div class="stat-item animate__animated animate__fadeInUp">
            <i class="fas fa-box-open"></i>
            <span class="value"><?php echo $order_count; ?></span>
            <span class="label small text-uppercase opacity-50" style="letter-spacing: 1px;">Total Orders</span>
        </div>

        <div class="action-btns">
            <a href="welcome.php" class="btn-custom btn-outline-custom">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="my_orders.php" class="btn-custom btn-primary-custom">
                <i class="fas fa-shopping-bag"></i> Orders
            </a>
        </div>

        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt me-1"></i> Log Out Account
        </a>
    </div>

</body>
</html>