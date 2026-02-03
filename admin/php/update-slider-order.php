<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $display_order = intval($_POST['display_order']);
    
    $sql = "UPDATE slider_images SET display_order = $display_order WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Display order updated successfully!";
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