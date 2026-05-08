<?php
session_start();
include 'db_connect.php';

// ১. লগিন চেক
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}

$order_id = $_GET['id'];
$user = $_SESSION['username'];

// ২. অর্ডার ডিটেইলস আনা
$sql = "SELECT * FROM orders WHERE id = '$order_id' AND username = '$user'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<script>alert('Invalid Order ID'); window.location.href='my_orders.php';</script>";
    exit();
}

$order = $result->fetch_assoc();
$status = $order['status'];
$order_date = strtotime($order['order_date']);

// তারিখ ক্যালকুলেশন
$processing_date = strtotime('+1 day', $order_date);
$delivery_day = strtotime('+3 days', $order_date); 

// ৩. স্ট্যাটাস লজিক
$steps = [
    'Placed' => [
        'title' => 'Order Placed',
        'desc' => 'We have received your order.',
        'icon' => 'fa-clipboard-check',
        'date' => date('d M, h:i A', $order_date),
        'active' => false
    ],
    'Processing' => [
        'title' => 'Processing',
        'desc' => 'Your order is being packed.',
        'icon' => 'fa-box-open',
        'date' => date('d M, h:i A', $processing_date),
        'active' => false
    ],
    'Shipped' => [
        'title' => 'Out for Delivery',
        'desc' => 'Your package is on the way.',
        'icon' => 'fa-shipping-fast',
        'date' => date('d M, 9:00 AM', $delivery_day),
        'active' => false
    ],
    'Delivered' => [
        'title' => 'Delivered',
        'desc' => 'Package has been delivered.',
        'icon' => 'fa-home',
        'date' => date('d M, 5:00 PM', $delivery_day),
        'active' => false
    ]
];

// একটিভ স্ট্যাটাস লজিক
if ($status == 'Pending') {
    $steps['Placed']['active'] = true;
} elseif ($status == 'Processing') {
    $steps['Placed']['active'] = true;
    $steps['Processing']['active'] = true;
} elseif ($status == 'Shipped') {
    $steps['Placed']['active'] = true;
    $steps['Processing']['active'] = true;
    $steps['Shipped']['active'] = true;
} elseif ($status == 'Delivered') {
    $steps['Placed']['active'] = true;
    $steps['Processing']['active'] = true;
    $steps['Shipped']['active'] = true;
    $steps['Delivered']['active'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #<?php echo $order_id; ?> | Herbal Care</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* 1. BACKGROUND IMAGE */
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        .navbar { background: rgba(255, 255, 255, 0.9); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .brand-font { font-family: 'Playfair Display', serif; }

        /* 2. GLASS CARD */
        .glass-container {
            background: rgba(255, 255, 255, 0.15); 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            margin-top: 50px;
            margin-bottom: 50px;
            animation: slideUp 0.8s ease-out;
            color: #fff;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }

        /* HEADER */
        .track-header {
            border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 20px; margin-bottom: 40px;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;
        }
        .track-header h4 { color: #fff; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        
        .status-label { 
            display: block; font-size: 0.9rem; color: #fff; opacity: 0.9; font-weight: 500;
            text-shadow: 0 1px 2px rgba(0,0,0,0.6); margin-bottom: 5px;
        }
        .status-badge {
            background: rgba(255,255,255,0.9); color: #1e5631; padding: 5px 15px; 
            border-radius: 20px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* 3. FIXED TIMELINE DESIGN */
        .timeline { position: relative; }
        
        /* The Vertical Line */
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 25px; /* Center of the 50px icon */
            width: 4px;
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-50%); /* Perfectly center the line */
            z-index: 0; /* Behind the icons */
            border-radius: 2px;
        }

        .timeline-item { 
            position: relative; 
            margin-bottom: 40px; 
            padding-left: 80px; /* Space for icon */
            z-index: 1; 
        }
        .timeline-item:last-child { margin-bottom: 0; }

        /* Icons (Circles) */
        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.6); /* Solid background to hide line */
            border: 3px solid rgba(255,255,255,0.5);
            display: flex; align-items: center; justify-content: center;
            color: #ccc;
            font-size: 1.2rem;
            transition: 0.4s;
            z-index: 2; /* Sit on top of the line */
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        /* Active State Styling */
        .timeline-item.active .timeline-icon {
            border-color: #4caf50;
            background: #4caf50; /* Green background hides the line behind it */
            color: white;
            transform: scale(1.15);
            box-shadow: 0 0 20px rgba(76, 175, 80, 0.6);
        }
        
        /* Active Line Coloring */
        /* This creates a green line overlay on top of the white line for completed steps */
        .timeline-item.active::after {
            content: '';
            position: absolute;
            top: 50px; /* Start from bottom of icon */
            left: 25px;
            height: calc(100% + 40px); /* Extend to next item */
            width: 4px;
            background: #4caf50;
            transform: translateX(-50%);
            z-index: 0;
        }
        /* Remove line for the very last item so it doesn't hang */
        .timeline-item:last-child.active::after { display: none; }
        /* Fix for previous items line length */
        .timeline-item.active:not(:last-child)::after {
             height: 100%; /* Just fill the gap to the next one */
        }

        /* Text Styling */
        .timeline-title { font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 2px; text-shadow: 0 1px 3px rgba(0,0,0,0.6); }
        .timeline-desc { font-size: 0.95rem; color: #eee; margin-bottom: 2px; font-weight: 300; text-shadow: 0 1px 2px rgba(0,0,0,0.4); }
        
        .timeline-date { 
            font-size: 0.85rem; color: #fff; font-weight: 600; 
            background: rgba(255,255,255,0.2); padding: 3px 12px; border-radius: 20px; 
            display: inline-block; margin-top: 8px; backdrop-filter: blur(5px);
        }

        /* BUTTONS */
        .btn-home {
            background: #fff; color: #1e5631; border-radius: 50px; padding: 12px 30px;
            font-weight: 700; text-decoration: none; transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-home:hover { background: #f0f0f0; color: #143d23; transform: translateY(-3px); }

        .btn-support {
            border: 2px solid white; color: white; border-radius: 50px; padding: 10px 25px;
            font-weight: 600; text-decoration: none; transition: 0.3s; background: transparent;
        }
        .btn-support:hover { background: white; color: #1e5631; }

        /* Animations */
        .timeline-item { opacity: 0; transform: translateX(-20px); animation: slideIn 0.6s forwards; }
        @keyframes slideIn { to { opacity: 1; transform: translateX(0); } }
        .timeline-item:nth-child(1) { animation-delay: 0.2s; }
        .timeline-item:nth-child(2) { animation-delay: 0.4s; }
        .timeline-item:nth-child(3) { animation-delay: 0.6s; }
        .timeline-item:nth-child(4) { animation-delay: 0.8s; }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand text-success fw-bold fs-3 brand-font" href="index.php"><i class="fas fa-leaf"></i> Herbal Care</a>
            <div class="ms-auto">
                <a href="my_orders.php" class="btn btn-outline-success rounded-pill btn-sm fw-bold">My Orders</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="glass-container">
                    
                    <div class="track-header">
                        <div>
                            <h4>Order #ORD-<?php echo str_pad($order_id, 5, '0', STR_PAD_LEFT); ?></h4>
                            <p>Amount: <strong>₹<?php echo number_format($order['total_price']); ?></strong> | Items: <?php echo $order['total_products']; ?></p>
                        </div>
                        <div class="text-end">
                            <span class="status-label">Current Status</span>
                            <span class="status-badge"><?php echo $status; ?></span>
                        </div>
                    </div>

                    <div class="timeline">
                        
                        <?php foreach($steps as $key => $step): ?>
                        <div class="timeline-item <?php echo $step['active'] ? 'active' : ''; ?>">
                            <div class="timeline-icon">
                                <i class="fas <?php echo $step['icon']; ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title"><?php echo $step['title']; ?></div>
                                <div class="timeline-desc"><?php echo $step['desc']; ?></div>
                                <div class="timeline-date">
                                    <i class="far fa-clock me-1"></i> <?php echo $step['date']; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </div>

                    <div class="text-center mt-5 pt-3 border-top border-light border-opacity-25">
                        <a href="my_orders.php" class="btn-support me-3"><i class="fas fa-arrow-left"></i> Back</a>
                        <a href="contact.php" class="btn-home"><i class="fas fa-headset"></i> Need Help?</a>
                    </div>

                </div>

            </div>
        </div>
    </div>

</body>
</html>