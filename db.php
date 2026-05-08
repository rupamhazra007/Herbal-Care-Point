<?php
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP এ ডিফল্ট পাসওয়ার্ড ফাঁকা থাকে
$dbname = "herbal_shop";

// কানেকশন তৈরি
$conn = new mysqli($servername, $username, $password, $dbname);

// চেক কানেকশন
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>