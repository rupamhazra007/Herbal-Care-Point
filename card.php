<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$total_price = 0;
$product_names = [];
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

/* ------------------ ডাটা ক্যালকুলেশন ------------------ */
if ($product_id) {
    $res = $conn->query("SELECT name, price FROM products WHERE id = $product_id");
    if($res && $row = $res->fetch_assoc()){
        $total_price = $row['price'];
        $product_names[] = $row['name'] . " (1)";
    }
} elseif (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $sql = "SELECT id, name, price FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($result && $row = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['id']];
        $total_price += $row['price'] * $qty;
        $product_names[] = $row['name'] . " ($qty)";
    }
} else {
    header("Location: index.php");
    exit();
}

$all_products_string = implode(', ', $product_names);

/* ------------------ পেমেন্ট সাবমিট লজিক ------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_pay'])) {
    $status = "Completed";
    $method = "Card";

    $stmt = $conn->prepare("INSERT INTO orders (username, total_products, total_price, payment_method, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $user, $all_products_string, $total_price, $method, $status);

    if ($stmt->execute()) {
        if (!$product_id) {
            unset($_SESSION['cart']); 
        }
        header("Location: my_orders.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Pay | HerbalCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #1e5631;
            --accent: #82CD47;
            --glass-white: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle, rgba(8, 28, 21, 0.9) 0%, rgba(0, 0, 0, 0.95) 100%), 
                        url('https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            padding: 20px;
        }

        .payment-wrapper {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 45px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 50px 100px rgba(0,0,0,0.6);
            color: #fff;
        }

        /* Branding Style (Matching welcome.php) */
        .brand-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        
        .brand-logo i {
            color: var(--accent);
            animation: leafBounce 3s ease-in-out infinite;
        }

        @keyframes leafBounce {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(10deg); }
        }

        .premium-card-ui {
            background: linear-gradient(135deg, #1b4332, #2d6a4f, #081c15);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }

        .card-number-display {
            font-size: 1.5rem;
            letter-spacing: 4px;
            font-weight: 600;
            margin: 25px 0;
            min-height: 1.5em;
        }

        .form-control-custom {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 15px;
            padding: 14px;
            color: #fff !important;
            margin-bottom: 20px;
        }

        #cardNumber { -webkit-text-security: disc; }

        .btn-pay-premium {
            background: linear-gradient(45deg, var(--accent), #589b37);
            color: #0d2b18; border: none; padding: 18px; border-radius: 50px;
            width: 100%; font-weight: 800; letter-spacing: 2px;
            transition: 0.5s;
            box-shadow: 0 15px 35px rgba(130, 205, 71, 0.3);
        }

        .btn-pay-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(130, 205, 71, 0.5);
            letter-spacing: 3px;
        }

        .chip-box { width: 50px; height: 38px; background: linear-gradient(135deg, #d4af37, #f9e29c); border-radius: 8px; }
    </style>
</head>
<body>

<div class="payment-wrapper animate__animated animate__zoomIn">
    <div class="brand-logo">
        <i class="fas fa-leaf"></i>HerbalCare
    </div>

    <div class="premium-card-ui">
        <div class="d-flex justify-content-between align-items-center">
            <div class="chip-box"></div>
            <div id="card-type-logo"><i class="fas fa-credit-card fa-2x opacity-50"></i></div>
        </div>
        <div class="card-number-display" id="card-display">**** **** **** ****</div>
        <div class="d-flex justify-content-between small text-uppercase fw-bold opacity-75">
            <span id="name-display">Card Holder</span>
            <span id="expiry-display">MM/YY</span>
        </div>
    </div>

    <div class="text-center mb-4">
        <h1 class="fw-800 text-accent">₹<?= number_format($total_price, 2) ?></h1>
        <p class="small opacity-50">SECURE TRANSACTION GATEWAY</p>
    </div>

    <form id="paymentForm" method="POST">
        <input type="text" class="form-control-custom" id="cardNumber" placeholder="Valid Card Number" maxlength="19" required autocomplete="off">
        <input type="text" class="form-control-custom" id="cardHolder" placeholder="Card Holder Name" required autocomplete="off">
        
        <div class="row g-3 mb-4">
            <div class="col-6"><input type="text" class="form-control-custom" id="cardExpiry" placeholder="MM/YY" maxlength="5" required></div>
            <div class="col-6"><input type="password" class="form-control-custom" id="cardCvv" placeholder="CVV" maxlength="3" required></div>
        </div>

        <input type="hidden" name="final_pay" value="1">
        <button type="submit" class="btn-pay-premium">AUTHORIZE SECURE PAY <i class="fas fa-shield-check ms-2"></i></button>
        <a href="buy.php" class="d-block text-center mt-3 text-white-50 text-decoration-none small">Go Back</a>
    </form>
</div>

<script>
    const cardInput = document.getElementById('cardNumber');
    const display = document.getElementById('card-display');
    const logoBox = document.getElementById('card-type-logo');

    const validCards = {
        "7001181012345678": { type: "rupay", cvv: "123", icon: '<i class="fab fa-cc-amazon-pay fa-3x text-info"></i>' },
        "4000123456789010": { type: "visa", cvv: "456", icon: '<i class="fab fa-cc-visa fa-3x text-white"></i>' },
        "5100000000000000": { type: "mastercard", cvv: "789", icon: '<i class="fab fa-cc-mastercard fa-3x text-warning"></i>' },
        "378282246310005":  { type: "mastercard", cvv: "000", icon: '<i class="fab fa-cc-mastercard fa-3x text-warning"></i>' }
    };

    cardInput.addEventListener('input', function (e) {
        let rawVal = e.target.value.replace(/\D/g, ''); 
        e.target.value = rawVal; 
        if(rawVal.length > 0) {
            logoBox.innerHTML = validCards[rawVal] ? validCards[rawVal].icon : '<i class="fas fa-credit-card fa-2x opacity-50"></i>';
            let masked = "";
            for(let i = 0; i < rawVal.length; i++) {
                if(i > 0 && i % 4 === 0) masked += " ";
                masked += (i < 4) ? rawVal[i] : "*";
            }
            display.innerText = masked;
        } else {
            display.innerText = "**** **** **** ****";
            logoBox.innerHTML = '<i class="fas fa-credit-card fa-2x opacity-50"></i>';
        }
    });

    document.getElementById('cardHolder').addEventListener('input', (e) => {
        document.getElementById('name-display').innerText = e.target.value.toUpperCase() || 'Card Holder';
    });

    document.getElementById('cardExpiry').addEventListener('input', (e) => {
        let val = e.target.value.replace(/\D/g, '');
        if(val.length > 2) val = val.substring(0,2) + '/' + val.substring(2,4);
        e.target.value = val;
        document.getElementById('expiry-display').innerText = val || 'MM/YY';
    });

    document.getElementById('paymentForm').addEventListener('submit', function(e){
        e.preventDefault();
        const cardNum = document.getElementById('cardNumber').value;
        const cvv = document.getElementById('cardCvv').value;

        if(!validCards[cardNum] || validCards[cardNum].cvv !== cvv) {
            Swal.fire({ icon: 'error', title: 'Declined', text: 'Invalid Credentials', confirmButtonColor: '#d33' });
            return;
        }

        Swal.fire({
            title: 'Connecting to Bank...',
            text: 'Securing your transaction path',
            timer: 4000,
            timerProgressBar: true,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        }).then(() => {
            Swal.fire({
                title: 'OTP Verification',
                text: 'Security code sent to the registered mobile number',
                input: 'password', 
                inputAttributes: { maxlength: 6, style: 'text-align:center; font-size:28px;' },
                showCancelButton: true,
                confirmButtonText: 'Verify OTP',
                confirmButtonColor: '#1e5631',
                preConfirm: (otp) => {
                    if(otp !== '123456') Swal.showValidationMessage('Wrong PIN (Use 123456)');
                    return otp;
                }
            }).then((res) => {
                if(res.isConfirmed){
                    Swal.fire({
                        title: 'Finalizing Transaction...',
                        text: 'Completing your HerbalCare harvest',
                        timer: 3000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    }).then(() => {
                        Swal.fire({ icon: 'success', title: 'Payment Successful!', text: 'Your order has been placed', showConfirmButton: false, timer: 1500 });
                        setTimeout(() => { e.target.submit(); }, 1600);
                    });
                }
            });
        });
    });
</script>
</body>
</html>