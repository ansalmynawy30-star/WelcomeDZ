<?php

$host = "localhost";
$user = "root";           // افتراضي في XAMPP
$pass = "";               // غالباً فاضي في XAMPP
$dbname = "login";  // اسم القاعدة اللي عملتها
 
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>