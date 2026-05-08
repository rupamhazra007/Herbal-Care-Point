<?php
session_start();
include 'db_connect.php'; 

$reg_status = "";

if (isset($_POST['register_btn'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password !== $cpassword) {
        $reg_status = "pass_mismatch";
    } 
    elseif (strlen($password) < 6) {
        $reg_status = "weak_pass";
    } 
    else {
        $check_user = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($check_user);

        if ($result->num_rows > 0) {
            $reg_status = "user_exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (fullname, username, email, phone, address, password) 
                    VALUES ('$fullname', '$email', '$email', '$phone', '$address', '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                $reg_status = "success";
            } else {
                $reg_status = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Register | HerbalCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        
        :root {
            --primary: #1e5631;
            --accent: #82CD47;
            --glass: rgba(255, 255, 255, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0; padding: 0;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            overflow-x: hidden; 
            overflow-y: auto;
            background: #08110a;
            padding: 50px 0;
        }

        /* অটো-মুভিং ব্যাকগ্রাউন্ড */
        .bg-scroller {
            position: fixed; top: 0; left: 0; width: 100%; height: 140%;
            background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), 
                        url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover; background-position: center;
            z-index: -1; animation: scrollBg 25s linear infinite alternate;
        }
        @keyframes scrollBg { 0% { transform: translateY(0); } 100% { transform: translateY(-15%); } }

        /* Premium Glass Container */
        .reg-container {
            width: 1000px; max-width: 92%; display: flex;
            background: var(--glass); backdrop-filter: blur(20px);
            border-radius: 35px; border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            overflow: hidden; 
            z-index: 10;
            transition: 0.4s ease;
        }

        .info-panel {
            flex: 0.9; padding: 50px; color: white;
            background: rgba(30, 86, 49, 0.88);
            display: flex; flex-direction: column; justify-content: center;
            position: relative;
        }

        .brand-logo-area { font-family: 'Playfair Display', serif; font-size: 2.6rem; font-weight: 900; }
        .brand-logo-area i { color: var(--accent); margin-right: 12px; }

        .form-panel { flex: 1.1; padding: 50px; background: rgba(255, 255, 255, 0.98); position: relative; }

        /* Modern Input Styling with Animation */
        .input-box { position: relative; margin-bottom: 20px; transition: 0.3s; }
        .input-box input {
            width: 100%; padding: 14px 20px 14px 50px; border: 2px solid #eee;
            border-radius: 15px; outline: none; transition: 0.3s; font-weight: 600;
            background: #fafafa;
        }
        .input-box input:focus { 
            border-color: var(--primary); 
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 86, 49, 0.08);
        }
        .input-icon { 
            position: absolute; left: 20px; top: 50%; 
            transform: translateY(-50%); color: #aaa; 
            transition: 0.3s; 
        }
        .input-box input:focus + .input-icon { color: var(--primary); transform: translateY(-50%) scale(1.15); }

        /* Luxury Button Animation */
        .btn-register {
            width: 100%; padding: 16px; border: none; border-radius: 50px;
            background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
            color: white; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1.5px; transition: 0.4s;
            box-shadow: 0 10px 25px rgba(27, 67, 50, 0.3);
            position: relative; overflow: hidden;
        }
        .btn-register:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 15px 35px rgba(27, 67, 50, 0.4);
            filter: brightness(1.1);
        }
        .btn-register:active { transform: translateY(-1px); }

        .back-home {
            position: fixed; top: 25px; left: 25px; z-index: 1000;
            color: white; background: rgba(0,0,0,0.45); padding: 10px 24px;
            border-radius: 50px; backdrop-filter: blur(10px); text-decoration: none;
            font-size: 0.9rem; transition: 0.3s; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .back-home:hover { background: var(--accent); color: var(--primary); transform: translateX(-5px); }

        @media (max-width: 992px) {
            .reg-container { flex-direction: column; }
            .info-panel { padding: 45px 30px; text-align: center; }
            .form-panel { padding: 40px 25px; }
            body { padding: 30px 0; }
        }
    </style>
</head>
<body>

    <div class="bg-scroller"></div>
    
    <a href="welcome.php" class="back-home"><i class="fas fa-chevron-left me-2"></i> RETURN HOME</a>

    <div class="reg-container animate__animated animate__fadeInUp">
        
        <div class="info-panel">
            <div class="brand-logo-area animate__animated animate__fadeInDown animate__delay-1s">
                <i class="fas fa-leaf"></i>HerbalCare
            </div>
            <h1 class="fw-bold mt-4 animate__animated animate__fadeInLeft animate__delay-1s">The Pure Path<br>to Wellness</h1>
            <p class="mt-3 opacity-75 animate__animated animate__fadeInLeft animate__delay-1s">
                Join our community of natural living. Experience 100% organic solutions tailored for your body and soul.
            </p>
            <div class="mt-4 small animate__animated animate__fadeInUp animate__delay-1s">
                <p><i class="fas fa-shield-check me-2 text-info"></i> Trusted by 15k+ Users</p>
                <p><i class="fas fa-certificate me-2 text-info"></i> Certified Organic Source</p>
            </div>
        </div>

        <div class="form-panel">
            <h3 class="fw-bold mb-4 text-success" style="font-family: 'Playfair Display';">Join the Journey</h3>
            <form action="" method="POST">
                <div class="input-box">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <i class="fas fa-user-circle input-icon"></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                <div class="input-box">
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                    <i class="fas fa-phone-alt input-icon"></i>
                </div>
                <div class="input-box">
                    <input type="text" name="address" placeholder="Residential Address" required>
                    <i class="fas fa-location-dot input-icon"></i>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="input-box">
                            <input type="password" name="password" placeholder="Password" required>
                            <i class="fas fa-key input-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-box">
                            <input type="password" name="cpassword" placeholder="Confirm" required>
                            <i class="fas fa-user-shield input-icon"></i>
                        </div>
                    </div>
                </div>
                <button type="submit" name="register_btn" class="btn-register mt-2">CREATE ACCOUNT</button>
            </form>
            <p class="text-center mt-4 small text-muted">
                Already part of the family? <a href="login.php" class="text-success fw-bold text-decoration-none">SIGN IN HERE</a>
            </p>
        </div>
    </div>

    <script>
        // নোটিফিকেশন হ্যান্ডলিং
        <?php if($reg_status == "success"): ?>
            Swal.fire({ 
                title: 'Success!', 
                text: 'Welcome to HerbalCare!', 
                icon: 'success',
                confirmButtonColor: '#1e5631'
            }).then(() => { window.location.href = 'login.php'; });
        <?php elseif($reg_status == "user_exists"): ?>
            Swal.fire({ 
                title: 'Note!', 
                text: 'Email already registered.', 
                icon: 'info',
                confirmButtonColor: '#1e5631'
            });
        <?php elseif($reg_status == "pass_mismatch"): ?>
             Swal.fire({ 
                title: 'Error!', 
                text: 'Passwords do not match.', 
                icon: 'warning',
                confirmButtonColor: '#1e5631'
            });
        <?php endif; ?>
    </script>
</body>
</html>