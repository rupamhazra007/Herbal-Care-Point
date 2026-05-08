<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$cart_items = [];
$total_price = 0;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $buy_id = (int)$_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $buy_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $row['qty'] = 1; 
        $total_price = $row['price'];
        $cart_items[] = $row;
    } else {
        header("Location: index.php");
        exit();
    }
} elseif (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['qty'] = $qty;
        $total_price += $row['price'] * $qty;
        $cart_items[] = $row;
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
    <title>Checkout | HerbalCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #1e5631;
            --accent: #82CD47;
            --glass: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: url('https://images.unsplash.com/photo-1540420773420-3366772f4999?q=80&w=2000&auto=format&fit=crop') center/cover fixed no-repeat;
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(8, 28, 21, 0.9), rgba(27, 67, 50, 0.6));
            z-index: -1;
        }

        .checkout-container {
            max-width: 1250px;
            margin: 40px auto;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 40px;
            padding: 45px;
            box-shadow: 0 50px 80px rgba(0,0,0,0.4);
        }

        .site-logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .brand-leaf {
            font-size: 3rem;
            color: var(--accent);
            animation: leafPulse 3s infinite;
        }

        @keyframes leafPulse {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(10deg); }
        }

        .site-logo {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(to right, #ffffff, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .input-group-custom {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label-custom {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
            letter-spacing: 1px;
        }

        .form-control-modern {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 15px 20px;
            color: #fff;
            width: 100%;
            transition: all 0.4s ease;
        }

        .form-control-modern:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--accent);
            box-shadow: 0 0 25px rgba(130, 205, 71, 0.2);
            outline: none;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .pay-card {
            background: rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 25px 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .pay-card i {
            font-size: 2rem;
            margin-bottom: 12px;
            display: block;
            color: #fff;
        }

        input[type="radio"]:checked + .pay-card {
            background: var(--accent);
            color: var(--primary);
            border-color: #fff;
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 15px 30px rgba(130, 205, 71, 0.4);
        }

        input[type="radio"]:checked + .pay-card i { color: var(--primary); }

        .product-preview-pane {
            background: rgba(0, 0, 0, 0.25);
            border-radius: 35px;
            padding: 30px;
            height: 100%;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .image-viewport {
            width: 100%;
            height: 280px;
            background: rgba(255,255,255,0.03);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .img-fluid-contain {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 15px 30px rgba(0,0,0,0.5));
            animation: floatImg 4s ease-in-out infinite;
        }

        @keyframes floatImg {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .desc-text {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 20px;
            font-size: 0.95rem;
            color: #d1d1d1;
            line-height: 1.6;
            border-left: 4px solid var(--accent);
        }

        .order-btn {
            background: linear-gradient(45deg, var(--accent), #589b37);
            color: #1e5631;
            border: none;
            padding: 18px;
            border-radius: 22px;
            width: 100%;
            font-weight: 800;
            font-size: 1.2rem;
            transition: 0.4s;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .order-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(130, 205, 71, 0.4);
            filter: brightness(1.1);
        }

        .total-display {
            font-size: 3rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
    </style>
</head>
<body>

<div class="container checkout-container">
    <div class="glass-card animate__animated animate__zoomIn">
        <div class="text-center mb-5">
            <div class="site-logo-container">
                <i class="fas fa-leaf brand-leaf"></i>
                <h1 class="site-logo">HerbalCare</h1>
            </div>
            <p class="text-white-50">Natural Remedies • Trusted Healing • Secure Checkout</p>
        </div>

        <div class="row g-5">
            <div class="col-lg-6">
                <h4 class="mb-4 d-flex align-items-center"><i class="fas fa-shipping-fast me-3 text-accent"></i> Shipping Details</h4>
                
                <div class="input-group-custom">
                    <label class="form-label-custom">Recipient Full Name</label>
                    <input type="text" id="name" class="form-control-modern" value="<?=htmlspecialchars($user)?>" placeholder="Enter name">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-custom">
                            <label class="form-label-custom">Phone Number</label>
                            <input type="number" id="phone" class="form-control-modern" placeholder="017XXXXXXXX">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-custom">
                            <label class="form-label-custom">Alt. Phone (Optional)</label>
                            <input type="number" id="phone2" class="form-control-modern" placeholder="Backup number">
                        </div>
                    </div>
                </div>

                <div class="input-group-custom">
                    <label class="form-label-custom">Detailed Shipping Address</label>
                    <textarea id="address" class="form-control-modern" rows="3" placeholder="House no, Street name, City, Pincode..."></textarea>
                </div>

                <h4 class="mt-5 mb-4 d-flex align-items-center"><i class="fas fa-credit-card me-3 text-accent"></i> Payment Method</h4>
                
                <div class="payment-grid">
                    <label>
                        <input type="radio" name="payment" value="cod" checked hidden>
                        <div class="pay-card">
                            <i class="fas fa-truck-loading"></i>
                            <span>CASH ON DELIVERY</span>
                        </div>
                    </label>

                    <label>
                        <input type="radio" name="payment" value="card" hidden>
                        <div class="pay-card">
                            <i class="fas fa-credit-card"></i>
                            <span>DEBIT/CREDIT CARD</span>
                        </div>
                    </label>

                    <label>
                        <input type="radio" name="payment" value="upi" hidden>
                        <div class="pay-card">
                            <i class="fas fa-mobile-alt"></i>
                            <span>UPI / BKASH</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="product-preview-pane animate__animated animate__fadeInRight">
                    <div style="max-height: 550px; overflow-y: auto; padding-right: 15px;">
                        <?php foreach($cart_items as $item): ?>
                        <div class="mb-4 pb-4 border-bottom border-white-50">
                            <div class="image-viewport">
                                <img src="<?=htmlspecialchars($item['img'])?>" class="img-fluid-contain" alt="Product">
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h3 class="fw-800 mb-0" style="font-family: 'Playfair Display';"><?=htmlspecialchars($item['name'])?></h3>
                                <span class="badge rounded-pill bg-light text-dark px-3 py-2">Qty: <?=$item['qty']?></span>
                            </div>

                            <div class="desc-text mb-2">
                                <i class="fas fa-info-circle me-1 text-accent"></i> <?=nl2br(htmlspecialchars($item['description']))?>
                            </div>
                            <h4 class="text-end text-accent fw-bold">₹<?=number_format($item['price'] * $item['qty'])?></h4>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top border-white-50">
                        <p class="mb-0 opacity-75 fw-600 uppercase small">TOTAL PAYABLE AMOUNT</p>
                        <h1 class="total-display mb-4">₹<?=number_format($total_price)?></h1>
                        
                        <button class="order-btn animate__animated animate__pulse animate__infinite" onclick="processOrder()">
                            PLACE YOUR ORDER <i class="fas fa-check-circle ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function processOrder() {
        const name = document.getElementById('name').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const address = document.getElementById('address').value.trim();
        const payment = document.querySelector('input[name="payment"]:checked').value;

        if(!name || !phone || !address) {
            Swal.fire({
                icon: 'warning',
                title: 'Details Required',
                text: 'Please fill in all shipping fields.',
                background: '#081c15',
                color: '#fff',
                confirmButtonColor: '#82CD47'
            });
            return;
        }

        Swal.fire({
            title: 'Verifying Details...',
            text: 'Please wait while we secure your order',
            timer: 1500,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        }).then(() => {
            let target = 'confirm.php';
            if(payment === 'upi') target = 'qr.php';
            if(payment === 'card') target = 'card.php';
            
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');
            if(productId) target += (target.includes('?') ? '&' : '?') + 'id=' + productId;
            
            window.location.href = target;
        });
    }
</script>
</body>
</html>