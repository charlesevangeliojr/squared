<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM slider_images WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Slider image deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    $conn->close();
}

header('Location: ../slider.php');
exit();
?>