<?php
require_once 'config.php';

$username = "squared"; // Change this
$password = "admin_squared_123"; // Change this

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "Admin user created successfully!";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
