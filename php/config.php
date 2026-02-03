<?php
$host = "localhost";
$user = "root"; // Default user in XAMPP/LAMP/MAMP
$password = "ite2025"; // Default is empty
$database = "squared";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
