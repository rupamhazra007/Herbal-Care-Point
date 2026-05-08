<?php
session_start();

// মেসেজ পাঠানোর লজিক (Simulated)
$msg_sent = false;
if (isset($_POST['send_msg'])) {
    // ভবিষ্যতে মেইল পাঠানোর কোড এখানে বসবে
    $msg_sent = true;
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Herbal Care</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* 1. NEW UNIQUE BACKGROUND IMAGE */
        body { 
            font-family: 'Poppins', sans-serif; 
            /* A fresh, scenic nature background (Distinct from others) */
            background: url('https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        
        .brand-font { font-family: 'Playfair Display', serif; }

        /* Navbar */
        .navbar { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .nav-link { color: #1e5631 !important; font-weight: 600; }

        /* 2. GLASS CONTAINER */
        .contact-container {
            /* High contrast white container to stand out against the landscape */
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 25px;
            padding: 50px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            margin-top: 60px;
            margin-bottom: 60px;
            
            /* Entrance Animation */
            animation: slideUp 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes slideUp { 
            from { opacity: 0; transform: translateY(80px); } 
            to { opacity: 1; transform: translateY(0); } 
        }

        /* LEFT SIDE: INFO BOXES */
        .info-box { 
            margin-bottom: 30px; 
            display: flex; 
            align-items: center; 
            gap: 20px; 
            padding: 15px;
            border-radius: 15px;
            transition: 0.4s;
            background: rgba(236, 245, 236, 0.6); 
            border: 1px solid rgba(30, 86, 49, 0.1);
        }
        
        .info-box:hover { 
            background: #fff; 
            transform: translateX(10px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .info-icon {
            width: 55px; height: 55px; background: #1e5631;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: #fff; 
            box-shadow: 0 4px 10px rgba(30, 86, 49, 0.3);
            transition: 0.4s;
        }
        
        .info-box:hover .info-icon { transform: rotate(360deg) scale(1.1); }

        .info-title { font-size: 1.1rem; font-weight: 700; color: #1e5631; margin-bottom: 2px; }
        .info-desc { font-size: 0.95rem; color: #444; font-weight: 500; }

        /* RIGHT SIDE: FORM */
        .form-control {
            background: #f8f9fa; 
            border: 1px solid #ddd; 
            padding: 15px;
            border-radius: 12px; 
            color: #333; 
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .form-control:focus { 
            background: white; 
            border-color: #1e5631; 
            box-shadow: 0 0 0 4px rgba(30, 86, 49, 0.1); 
            transform: translateY(-2px);
        }
        
        .btn-send {
            width: 100%; padding: 15px; 
            background: linear-gradient(45deg, #1e5631, #143d23); 
            border: none; color: white; font-weight: bold; 
            border-radius: 50px; font-size: 1.1rem; letter-spacing: 1px;
            transition: 0.4s; box-shadow: 0 5px 15px rgba(30, 86, 49, 0.3);
        }
        .btn-send:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 10px 25px rgba(30, 86, 49, 0.5); 
        }

        /* SOCIAL ICONS */
        .social-links { margin-top: 40px; }
        .social-btn {
            display: inline-flex; width: 45px; height: 45px; 
            background: #f0f0f0; border: 1px solid #ddd;
            border-radius: 50%; color: #1e5631; 
            align-items: center; justify-content: center;
            margin-right: 12px; text-decoration: none; 
            transition: 0.3s; font-size: 1.2rem;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
        }
        .social-btn:hover { 
            background: #1e5631; color: white; 
            transform: translateY(-5px); 
            box-shadow: 0 5px 15px rgba(30,86,49,0.3);
        }

        /* MAP */
        .map-frame {
            width: 100%; height: 220px; border-radius: 20px; 
            border: 4px solid #fff; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 25px; overflow: hidden;
            transition: 0.3s;
        }
        .map-frame:hover { transform: scale(1.02); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand text-success fw-bold fs-3 brand-font" href="index.php"><i class="fas fa-leaf"></i> Herbal Care</a>
            <div class="ms-auto">
                <a href="index.php" class="nav-link"><i class="fas fa-home me-1"></i> Home</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="contact-container">
                    <div class="row g-5">
                        
                        <div class="col-md-5 border-end border-secondary border-opacity-10">
                            <h2 class="brand-font mb-2 text-dark">Get in Touch</h2>
                            <p class="mb-5 text-muted small">We'd love to hear from you. Our team is here to help.</p>

                            <div class="info-box">
                                <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                                <div>
                                    <div class="info-title">Helpline</div>
                                    <div class="info-desc">+91 98765 43210</div>
                                    <div class="text-success small fw-bold">Available 24/7</div>
                                </div>
                            </div>

                            <div class="info-box">
                                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <div class="info-title">Email Support</div>
                                    <div class="info-desc">support@herbalcare.com</div>
                                </div>
                            </div>

                            <div class="info-box">
                                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <div class="info-title">Visit Us</div>
                                    <div class="info-desc">123 Green Street, Kolkata</div>
                                </div>
                            </div>

                            <div class="social-links">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">Follow Us</h6>
                                <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                                <a href="https://wa.me/919876543210" class="social-btn text-success border-success"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <h3 class="brand-font mb-4 text-dark">Send a Message</h3>
                            
                            <form action="" method="POST">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" name="name" placeholder="Your Name" value="<?php echo $user_name; ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="subject" placeholder="Subject (Order Issue, Inquiry)" required>
                                <textarea name="message" class="form-control" rows="4" placeholder="How can we help you?" required></textarea>
                                
                                <button type="submit" name="send_msg" class="btn-send">
                                    Send Message <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </form>

                            <div class="map-frame">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3684.052673322062!2d88.43093257507796!3d22.577123979490217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0275927b0061ad%3A0x496c2fab98874c86!2sSalt%20Lake%20City%2C%20Kolkata%2C%20West%20Bengal!5e0!3m2!1sen!2sin!4v1703830000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if($msg_sent): ?>
            Swal.fire({
                title: 'Message Sent!',
                text: 'Thank you for contacting us. We will reply soon.',
                icon: 'success',
                confirmButtonColor: '#1e5631',
                background: '#fff url(https://www.transparenttextures.com/patterns/cubes.png)',
                backdrop: `rgba(0,0,0,0.5)`
            });
        <?php endif; ?>
    </script>

</body>
</html>