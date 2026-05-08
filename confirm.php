<?php
session_start();
include 'db_connect.php'; // নিশ্চিত হোন আপনার ফাইলের নাম db_connect.php ই আছে

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$total_price = 0;
$product_names = [];
$method = isset($_GET['method']) ? $_GET['method'] : 'COD';
$order_id = rand(10000, 99999);

/* ==========================================================
   লজিক: সিঙ্গেল আইটেম নাকি কার্ট চেক করা
   ========================================================== */

if (isset($_GET['id']) && !empty($_GET['id'])) {
    // ১. সিঙ্গেল আইটেম বাই লজিক (buy.php?id=...)
    $p_id = intval($_GET['id']);
    $sql_p = "SELECT name, price FROM products WHERE id = $p_id";
    $res_p = $conn->query($sql_p);
    
    if($row = $res_p->fetch_assoc()){
        $total_price = $row['price'];
        $product_names[] = $row['name'] . " (1)";
    }
    // এখানে unset($_SESSION['cart']) করা হয়নি, তাই কার্ট নিরাপদ থাকবে।
} 
elseif (!empty($_SESSION['cart'])) {
    // ২. কার্ট চেকআউট লজিক
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $sql_c = "SELECT id, name, price FROM products WHERE id IN ($ids)";
    $res_c = $conn->query($sql_c);

    while($row = $res_c->fetch_assoc()){
        $qty = $_SESSION['cart'][$row['id']];
        $total_price += ($row['price'] * $qty);
        $product_names[] = $row['name'] . " ($qty)";
    }
    
    // শুধুমাত্র কার্ট থেকে অর্ডার করলেই সেশন ক্লিয়ার হবে
    unset($_SESSION['cart']); 
} 
else {
    header("Location: index.php");
    exit();
}

/* ==========================================================
   ডাটাবেসে অর্ডার সেভ করা
   ========================================================== */

$total_products_string = $conn->real_escape_string(implode(', ', $product_names));

$sql_insert = "INSERT INTO orders (username, total_products, total_price, payment_method, status) 
               VALUES ('$user', '$total_products_string', '$total_price', '$method', 'Pending')";

$insert_status = false;
if ($conn->query($sql_insert) === TRUE) {
    $insert_status = true;
} else {
    die("Database Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | Herbal Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1540420773420-3366772f4999?w=1950');
            background-size: cover; background-position: center;
            height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0;
        }
        .success-card {
            background: rgba(255, 255, 255, 0.98);
            padding: 40px; border-radius: 25px; text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            max-width: 500px; width: 90%;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .check-icon { font-size: 60px; color: #1e5631; margin-bottom: 20px; }
        .order-info { background: #f1f8f4; border-radius: 15px; padding: 20px; margin: 20px 0; text-align: left; border-left: 5px solid #1e5631; }
        .btn-home { background: #1e5631; color: white; padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-home:hover { background: #143d23; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="success-card">
        <i class="fas fa-check-circle check-icon"></i>
        <h2 class="fw-bold text-success" style="font-family: 'Playfair Display'">Order Placed!</h2>
        <p class="text-muted">High-five, <b><?= htmlspecialchars($user) ?></b>! Your nature-friendly products are on their way.</p>

        <div class="order-info">
            <p class="mb-1 small text-muted">Order ID: <span class="text-dark fw-bold">#ORD-<?= $order_id ?></span></p>
            <p class="mb-1 small text-muted">Items: <span class="text-dark fw-bold"><?= $total_products_string ?></span></p>
            <p class="mb-1 small text-muted">Total: <span class="text-dark fw-bold">₹<?= number_format($total_price, 2) ?></span></p>
            <p class="mb-0 small text-muted">Method: <span class="text-dark fw-bold"><?= $method ?></span></p>
        </div>

        <div class="d-flex justify-content-center gap-2 mt-4">
            <a href="my_orders.php" class="btn btn-outline-success rounded-pill px-4">Track Order</a>
            <a href="index.php" class="btn-home">Shop More</a>
        </div>
    </div>

</body>
</html>