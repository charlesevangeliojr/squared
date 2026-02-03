<?php
session_start(); // Start the session
require 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $_SESSION['message'] = "⚠ All fields are required!";
        $_SESSION['message_type'] = "warning";
    } else {
        $query = "INSERT INTO announcements (title, content) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $title, $content);

        if ($stmt->execute()) {
            $_SESSION['message'] = "🎉 Announcement added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "❌ Error adding announcement!";
            $_SESSION['message_type'] = "danger";
        }

        $stmt->close();
    }

    $conn->close();
    header("Location: ../add_announcement.php");
    exit();
}
?>