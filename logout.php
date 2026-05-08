<?php
session_start(); // সেশন শুরু করা হলো

// ১. শুধু ইউজারনেম বা লগইন রিলেটেড সেশন মুছে দিন
if (isset($_SESSION['username'])) {
    unset($_SESSION['username']);
}

// ২. যদি আপনার অন্য কোনো লগইন ভেরিয়েবল থাকে (যেমন user_id), সেগুলোও আনসেট করুন
// unset($_SESSION['user_id']);

/* এখানে session_destroy() বা session_unset() ব্যবহার করবেন না। 
   কারণ তা করলে $_SESSION['cart'] ডিলিট হয়ে যাবে। 
*/

// ৩. লগইন পেজে পাঠিয়ে দিন
header("Location: login.php");
exit();
?>