<?php
$servername = "localhost";
$username = "root"; // XAMPP এর ডিফল্ট ইউজারনেম
$password = "";     // XAMPP এর ডিফল্ট পাসওয়ার্ড (ফাঁকা থাকে)
$dbname = "herbal_shop";

// কানেকশন তৈরি করা
$conn = new mysqli($servername, $username, $password, $dbname);

// কানেকশন চেক করা
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>