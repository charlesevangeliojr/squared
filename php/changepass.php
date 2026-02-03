<?php
session_start();
require 'config.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['student_id'])) {
    $_SESSION['message'] = "⚠ You must be logged in to change your password.";
    $_SESSION['message_type'] = "warning";
    header("Location: ../index.php");
    exit();
}

// Get logged-in student's ID
$student_id = $_SESSION['student_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input fields
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['message'] = "⚠ All fields are required.";
        $_SESSION['message_type'] = "warning";
        header("Location: ../home.php");
        exit();
    }

    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "❌ New passwords do not match.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../home.php");
        exit();
    }

    // Retrieve current password hash from the database
    $query = "SELECT password_hash FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($password_hash);
        $stmt->fetch();

        // Verify current password
        if (!password_verify($current_password, $password_hash)) {
            $_SESSION['message'] = "❌ Incorrect current password.";
            $_SESSION['message_type'] = "danger";
            header("Location: ../home.php");
            exit();
        }

        // Hash the new password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $update_query = "UPDATE students SET password_hash = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $new_hashed_password, $student_id);

        if ($update_stmt->execute()) {
            $_SESSION['message'] = "🎉 Password changed successfully! 🎉";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "❌ Error changing password. " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }

        $update_stmt->close();
    } else {
        $_SESSION['message'] = "❌ Student not found.";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../home.php");
    exit();
} else {
    header("Location: ../home.php");
    exit();
}
?>
