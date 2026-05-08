<?php
session_start();
include 'db.php'; // ডাটাবেস কানেকশন যুক্ত করা হলো

// 1. Login Check
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please Login First!'); window.location.href='login.php';</script>";
    exit();
}

$user = $_SESSION['username'];

// --- CART LOGIC ---
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add/Remove/Update
if (isset($_GET['action'])) {
    $id = intval($_GET['id']);
    
    if ($_GET['action'] == 'add') {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
    } 
    elseif ($_GET['action'] == 'remove') {
        unset($_SESSION['cart'][$id]);
    }
    elseif ($_GET['action'] == 'decrease') {
        if (isset($_SESSION['cart'][$id]) && $_SESSION['cart'][$id] > 1) {
            $_SESSION['cart'][$id]--;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }
    
    header("Location: cart.php");
    exit();
}

$total_price = 0;
$total_items = 0;
$cart_items = [];

// ডাটাবেস থেকে কার্টে থাকা প্রোডাক্টগুলোর তথ্য আনা
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $qty = $_SESSION['cart'][$id];
            $row['qty'] = $qty;
            $total_price += ($row['price'] * $qty);
            $total_items += $qty;
            $cart_items[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Herbal Care</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #1e5631;
            --accent: #82CD47;
            --light-bg: #f4f9f4;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            min-height: 100vh;
            background: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?q=80&w=1950');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }

        /* Navbar Branding */
        .navbar { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .navbar-brand { font-family: 'Playfair Display', serif; font-size: 1.8rem; }
        .navbar-brand i { color: var(--accent); margin-right: 8px; }

        .cart-header { font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-bottom: 30px; }
        
        /* Premium Card Container */
        .cart-items-container { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(15px);
            padding: 35px; border-radius: 30px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
            border: 1px solid rgba(255,255,255,0.5);
            animation: fadeInL 1s ease-out;
        }

        @keyframes fadeInL {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .cart-item { 
            border-bottom: 1px solid #eee; padding: 25px 0; 
            transition: 0.3s;
        }
        .cart-item:hover { transform: scale(1.01); }
        .cart-item:last-child { border-bottom: none; }
        
        .item-img { 
            width: 110px; height: 110px; object-fit: cover; 
            border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border: 3px solid #fff;
        }
        
        /* Qty Control */
        .qty-control { border: 1px solid #e0e0e0; }
        .qty-btn { 
            background: var(--primary); color: white; border-radius: 50%; 
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none; transition: 0.4s;
        }
        .qty-btn:hover { background: var(--accent); transform: rotate(90deg); color: white; }
        
        /* Checkout Box */
        .checkout-box { 
            background: rgba(255, 255, 255, 0.98); 
            padding: 30px; border-radius: 30px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.15); 
            position: sticky; top: 110px;
            border: 1px solid var(--accent);
        }
        
        .btn-proceed { 
            width: 100%; background: linear-gradient(45deg, var(--primary), #2d6a4f); 
            border: none; border-radius: 50px; padding: 16px; 
            color: white; font-weight: 700; letter-spacing: 1px; transition: 0.5s;
            box-shadow: 0 10px 20px rgba(30, 86, 49, 0.3);
        }
        .btn-proceed:hover { 
            transform: translateY(-5px) scale(1.02); 
            box-shadow: 0 15px 30px rgba(30, 86, 49, 0.4);
            filter: brightness(1.1);
        }

        /* Transitions */
        .page-transition {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #fff; z-index: 9999; display: flex; justify-content: center; align-items: center;
            opacity: 0; pointer-events: none; transition: 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .page-transition.active { opacity: 1; pointer-events: all; }

        .empty-cart-anim { animation: bounceIn 1.2s; }
    </style>
</head>
<body>

    <div class="page-transition" id="transitionLayer">
        <div class="text-center">
            <div class="spinner-grow text-success" role="status" style="width: 3rem; height: 3rem;"></div>
            <h4 class="mt-4 text-success fw-bold animate__animated animate__pulse animate__infinite">Preparing Your Wellness Box...</h4>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand text-success fw-bold animate__animated animate__fadeInLeft" href="index.php">
                <i class="fas fa-leaf"></i> Herbal Care
            </a>
            <div class="ms-auto animate__animated animate__fadeInRight">
                <a href="welcome.php" class="btn btn-outline-success rounded-pill px-4 fw-bold">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <?php if(empty($cart_items)): ?>
            <div class="text-center p-5 bg-white rounded-5 shadow-lg empty-cart-anim">
                <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="180" class="mb-4 animate__animated animate__swing animate__infinite animate__slow">
                <h2 class="fw-bold text-dark">Your basket is resting!</h2>
                <p class="text-muted fs-5">Add some herbal magic to start your journey.</p>
                <a href="index.php" class="btn btn-success rounded-pill px-5 py-3 mt-4 fw-bold shadow-sm">Explore Shop</a>
            </div>
        <?php else: ?>
            
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="cart-items-container">
                        <h2 class="cart-header"><i class="fas fa-shopping-bag me-3 animate__animated animate__bounceIn"></i>My Selection</h2>
                        
                        <?php foreach($cart_items as $index => $item): ?>
                        <div class="cart-item" data-aos="fade-right" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-4">
                                    <img src="<?php echo htmlspecialchars($item['img']); ?>" class="item-img" alt="Product">
                                </div>
                                <div class="col-md-10 col-8">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <span class="fw-bold text-success fs-4">₹<?php echo number_format($item['price']); ?></span>
                                    </div>
                                    
                                    <p class="product-desc">
                                        <?php echo substr(htmlspecialchars($item['description']), 0, 80); ?>...
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="qty-control d-flex align-items-center bg-white rounded-pill px-2 py-1 shadow-sm">
                                            <a href="cart.php?action=decrease&id=<?php echo $item['id']; ?>" class="qty-btn"><i class="fas fa-minus small"></i></a>
                                            <span class="mx-3 fw-bold fs-5"><?php echo $item['qty']; ?></span>
                                            <a href="cart.php?action=add&id=<?php echo $item['id']; ?>" class="qty-btn"><i class="fas fa-plus small"></i></a>
                                        </div>
                                        <button onclick="confirmDelete(<?php echo $item['id']; ?>)" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3 fw-bold">
                                            <i class="fas fa-trash-alt me-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="checkout-box animate__animated animate__fadeInRight">
                        <h4 class="fw-bold mb-4 border-bottom pb-2 text-success">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-3 fs-6">
                            <span class="text-muted">Subtotal (<?php echo $total_items; ?> items)</span>
                            <span class="fw-bold">₹<?php echo number_format($total_price); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 fs-6">
                            <span class="text-muted">Shipping</span>
                            <span class="text-success fw-bold">FREE <i class="fas fa-truck-moving ms-1"></i></span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 mt-4 bg-light p-3 rounded-4">
                            <span class="h5 mb-0 fw-bold">Total Payable</span>
                            <span class="h4 mb-0 fw-bold text-success">₹<?php echo number_format($total_price); ?></span>
                        </div>
                        
                        <button onclick="processCheckout()" class="btn-proceed animate__animated animate__pulse animate__infinite">
                            SECURE CHECKOUT <i class="fas fa-lock ms-2"></i>
                        </button>
                        
                        <div class="text-center mt-4 text-muted small">
                            <p><i class="fas fa-certificate text-success me-1"></i> Guaranteed Natural & Fresh</p>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" width="60" class="mx-2 opacity-50">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" width="40" class="mx-2 opacity-50">
                        </div>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Remove Item?',
                text: "Do you want to clear this product from your bag?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#1e5631',
                confirmButtonText: 'Yes, remove',
                showClass: { popup: 'animate__animated animate__fadeInDown' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'cart.php?action=remove&id=' + id;
                }
            })
        }

        function processCheckout() {
            document.getElementById('transitionLayer').classList.add('active');
            setTimeout(() => {
                window.location.href = 'buy.php';
            }, 1200);
        }
    </script>
</body>
</html>