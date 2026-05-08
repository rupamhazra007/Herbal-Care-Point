<?php
session_start();
// ডাটাবেস কানেকশন ফাইল ইনক্লুড করা হলো
include 'db_connect.php'; 

$login_status = "";

if (isset($_POST['login_btn'])) {
    $login_id = mysqli_real_escape_string($conn, $_POST['login_id']); 
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$login_id' OR phone = '$login_id' OR username = '$login_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['fullname'];
            $_SESSION['user_id'] = $row['username']; 
            $_SESSION['email'] = $row['email'];
            $login_status = "success";
        } else {
            $login_status = "error";
        }
    } else {
        if ($login_id == "admin@gmail.com" && $password == "12345") {
            $_SESSION['username'] = "Admin"; 
            $login_status = "success"; 
        } else {
            $login_status = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Login | HerbalCare</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e5631 0%, #2d8a4e 100%);
            --glass-bg: rgba(255, 255, 255, 0.12);
            --accent: #82CD47;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            /* ব্যাকগ্রাউন্ড ইমেজ এবং লজিক অপরিবর্তিত রাখা হয়েছে */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Floating Animation for Glass Container */
        .glass-container {
            width: 1000px;
            max-width: 95%;
            min-height: 600px;
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        /* Left Panel with Deep Green Glossy Look */
        .left-panel {
            flex: 1.2;
            padding: 60px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(30, 86, 49, 0.85);
            position: relative;
        }

        .brand-area {
            animation: fadeInLeft 1s both;
        }

        .brand-area i {
            font-size: 3.5rem;
            color: var(--accent);
            margin-bottom: 15px;
            filter: drop-shadow(0 0 10px rgba(130, 205, 71, 0.5));
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.2rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .brand-desc {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        /* Feature List with Staggered Animation */
        .features li {
            list-style: none;
            margin-bottom: 20px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            transform: translateX(-20px);
            opacity: 0;
            animation: slideInRight 0.5s forwards;
        }

        .features li:nth-child(1) { animation-delay: 0.3s; }
        .features li:nth-child(2) { animation-delay: 0.5s; }
        .features li:nth-child(3) { animation-delay: 0.7s; }

        .features i {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--accent);
        }

        @keyframes slideInRight {
            to { transform: translateX(0); opacity: 1; }
        }

        /* Right Panel - Form Area */
        .right-panel {
            flex: 1;
            padding: 60px;
            background: rgba(255, 255, 255, 0.98);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #1e5631;
            font-weight: 700;
            margin-bottom: 10px;
        }

        /* Premium Input Fields */
        .input-group-custom {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group-custom input {
            width: 100%;
            padding: 15px 50px;
            border: 2px solid #eee;
            border-radius: 15px;
            outline: none;
            transition: 0.4s;
            background: #fdfdfd;
        }

        .input-group-custom input:focus {
            border-color: #1e5631;
            background: #fff;
            box-shadow: 0 10px 20px rgba(30, 86, 49, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #bbb;
            transition: 0.3s;
        }

        .input-group-custom input:focus + .input-icon {
            color: #1e5631;
            transform: translateY(-50%) scale(1.2);
        }

        /* Luxury Login Button */
        .btn-login {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 15px;
            background: var(--primary-gradient);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.4s;
            box-shadow: 0 10px 25px rgba(30, 86, 49, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(30, 86, 49, 0.4);
            filter: brightness(1.1);
        }

        .back-home {
            position: absolute;
            top: 30px;
            left: 30px;
            z-index: 100;
            color: white;
            text-decoration: none;
            background: rgba(0,0,0,0.4);
            padding: 10px 25px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            transition: 0.3s;
        }

        .back-home:hover {
            background: var(--accent);
            color: #1e5631;
            transform: translateX(-5px);
        }

        @media (max-width: 900px) {
            .glass-container { flex-direction: column; width: 90%; height: auto; }
            .left-panel { padding: 40px; text-align: center; }
            .features li { justify-content: center; }
            .right-panel { padding: 40px; }
        }
    </style>
</head>
<body>

    <a href="welcome.php" class="back-home"><i class="fas fa-arrow-left me-2"></i> Return Home</a>

    <div class="glass-container animate__animated animate__zoomIn">
        
        <div class="left-panel">
            <div class="brand-area">
                <i class="fas fa-leaf"></i>
                <h1 class="brand-title">HerbalCare</h1>
                <p class="brand-desc">
                    Experience the pinnacle of natural wellness. Our clinically tested Ayurvedic solutions bring you the purity of nature with modern luxury.
                </p>
            </div>
            
            <ul class="features">
                <li><i class="fas fa-crown"></i> Purest Himalayan Ingredients</li>
                <li><i class="fas fa-certificate"></i> ISO & Lab Certified Safety</li>
                <li><i class="fas fa-bolt"></i> Ultra-Fast Global Shipping</li>
            </ul>
        </div>

        <div class="right-panel">
            <div class="form-header text-center mb-5">
                <h2 class="animate__animated animate__fadeInDown">Welcome Back</h2>
                <p class="text-muted">Enter your credentials to access your sanctuary.</p>
            </div>

            <form action="" method="POST" class="animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                <div class="input-group-custom">
                    <input type="text" name="login_id" placeholder="Email, Phone or Username" required>
                    <i class="fas fa-envelope-open input-icon"></i>
                </div>

                <div class="input-group-custom">
                    <input type="password" name="password" id="passwordInput" placeholder="Secret Password" required>
                    <i class="fas fa-key input-icon"></i>
                    <i class="fas fa-eye toggle-password" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #bbb;" onclick="togglePassword()"></i>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <label class="small text-muted" style="cursor: pointer;"><input type="checkbox" class="me-1"> Keep me signed in</label>
                    <a href="#" class="small" style="color: #1e5631; text-decoration: none; font-weight: 600;">Recovery?</a>
                </div>

                <button type="submit" name="login_btn" class="btn-login">SIGN IN TO ACCOUNT</button>
            </form>

            <div class="text-center mt-5">
                <span class="text-muted small">New to our community?</span> <br>
                <a href="register.php" style="color: #1e5631; font-weight: 800; text-decoration: none; border-bottom: 2px solid #82CD47;">Create a New Account</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passField = document.getElementById("passwordInput");
            const icon = document.querySelector(".toggle-password");
            if (passField.type === "password") {
                passField.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passField.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        <?php if($login_status == "success"): ?>
            Swal.fire({
                title: 'Authentication Verified!',
                text: 'Welcome to your premium dashboard.',
                icon: 'success',
                background: '#fff',
                confirmButtonColor: '#1e5631',
                showClass: { popup: 'animate__animated animate__bounceIn' }
            }).then(() => { window.location.href = 'index.php'; });
        <?php elseif($login_status == "error"): ?>
            Swal.fire({
                title: 'Access Denied!',
                text: 'The credentials provided do not match our records.',
                icon: 'error',
                confirmButtonColor: '#d33',
                showClass: { popup: 'animate__animated animate__headShake' }
            });
        <?php endif; ?>
    </script>
</body>
</html>