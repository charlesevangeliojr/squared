<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $display_order = intval($_POST['display_order']);
    
    $sql = "INSERT INTO slider_images (image_url, display_order) VALUES ('$image_url', $display_order)";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Slider image added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    $conn->close();
    header('Location: ../slider.php');
    exit();
}
?>