<?php
session_start();
include 'db_connect.php'; 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['username'];
$total_price = 0;
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if ($product_id) {
    $res = $conn->query("SELECT price FROM products WHERE id = $product_id");
    if($row = $res->fetch_assoc()) {
        $total_price = $row['price'];
    }
} elseif (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $res = $conn->query("SELECT price, id FROM products WHERE id IN ($ids)");
    while($row = $res->fetch_assoc()) {
        $total_price += ($row['price'] * $_SESSION['cart'][$row['id']]);
    }
} else {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure UPI Payment | HerbalCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700;900&display=swap');
        
        :root {
            --primary: #1e5631;
            --accent: #82CD47;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=1920&q=80') center/cover fixed no-repeat;
            padding: 20px;
        }
        .qr-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            padding: 45px;
            border-radius: 45px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            text-align: center;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 45px 90px rgba(0, 0, 0, 0.5);
            animation: zoomIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        /* Branding Style (Space removed) */
        .brand-area {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
                .brand-area i {
            color: var(--accent);
            animation: leafBounce 3s ease-in-out infinite;
            filter: drop-shadow(0 0 10px rgba(130, 205, 71, 0.3));
        }
        @keyframes leafBounce {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(12deg); }
        }
        .price-tag {
            background: #f1f8f4;
            color: var(--primary);
            padding: 15px 30px;
            border-radius: 60px;
            display: inline-block;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 25px;
            border: 2px solid var(--accent);
            animation: pulseGlow 2s infinite;
        }
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(130, 205, 71, 0.5); }
            70% { box-shadow: 0 0 0 15px rgba(130, 205, 71, 0); }
            100% { box-shadow: 0 0 0 0 rgba(130, 205, 71, 0); }
        }
        .qr-wrapper {
            background: #fff;
            padding: 18px;
            border-radius: 30px;
            display: inline-block;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            margin-bottom: 25px;
            border: 1.5px solid #f0f0f0;
            transition: 0.4s;
        }
        
        .qr-wrapper:hover { transform: scale(1.03); }

        .qr-wrapper img {
            width: 220px;
            height: 220px;
            border-radius: 12px;
        }

        .btn-paid {
            background: linear-gradient(45deg, var(--primary), #40916c);
            border: none;
            color: #fff;
            padding: 20px;
            border-radius: 55px;
            width: 100%;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: 0.5s;
            box-shadow: 0 12px 25px rgba(30, 86, 49, 0.35);
        }

        .btn-paid:hover {
            letter-spacing: 4px;
            transform: translateY(-5px);
            box-shadow: 0 18px 35px rgba(30, 86, 49, 0.5);
            filter: brightness(1.1);
        }

        .upi-icons {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 30px;
            opacity: 0.5;
        }

        .cancel-link {
            color: #777;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: 0.3s;
            margin-top: 25px;
            display: inline-block;
        }

        .cancel-link:hover { color: #d9534f; transform: translateX(-3px); }
    </style>
</head>
<body>

<div class="qr-card animate__animated animate__zoomIn">
    <div class="brand-area">
        <i class="fas fa-leaf"></i>HerbalCare
    </div>
    
    <p class="text-muted small mb-4 fw-bold text-uppercase" style="letter-spacing: 1px;">Secure Merchant Payment</p>
    
    <div class="price-tag">
        ₹<?php echo number_format($total_price, 2); ?>
    </div>

    <div class="qr-wrapper animate__animated animate__fadeInUp animate__delay-1s">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=upi://pay?pa=YOUR_UPI_ID@okaxis%26pn=HerbalCare%26am=<?= $total_price ?>%26cu=INR" alt="Secure QR">
    </div>

    <div class="mb-4">
        <span class="badge rounded-pill bg-light text-success px-4 py-2 border shadow-sm">
            <i class="fas fa-shield-check me-2"></i> 256-Bit Encrypted
        </span>
    </div>
    
    <button class="btn-paid animate__animated animate__pulse animate__infinite" onclick="verifyPayment()">
        I HAVE PAID <i class="fas fa-check-circle ms-2"></i>
    </button>

    <div class="upi-icons">
        <i class="fab fa-google-pay fa-2x"></i>
        <i class="fas fa-mobile-alt fa-2x"></i>
        <i class="fas fa-university fa-2x"></i>
    </div>

    <a href="buy.php<?= $product_id ? '?id='.$product_id : '' ?>" class="cancel-link">
        <i class="fas fa-arrow-left me-2"></i> Abort Transaction
    </a>
</div>

<script>
function verifyPayment() {
    Swal.fire({
        title: 'Verifying Hash...',
        html: 'Checking transaction status with gateway',
        timer: 2500,
        timerProgressBar: true,
        background: '#fff',
        color: '#1e5631',
        didOpen: () => { Swal.showLoading(); }
    }).then(() => {
        let url = 'confirm.php?method=UPI';
        <?php if($product_id) echo "url += '&id=$product_id';"; ?>
        window.location.href = url;
    });
}
</script>
</body>
</html>
