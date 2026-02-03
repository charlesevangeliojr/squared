<?php
session_start();
require 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    $query = "DELETE FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "🗑️ Announcement deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Error deleting announcement!";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../announcement.php");
    exit();
}
?>
