<?php
session_start();
require 'config.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $suffix = trim($_POST['suffix']);
    $sex = trim($_POST['sex']);
    $program = trim($_POST['program']);
    $course = trim($_POST['course']);
    $email = trim($_POST['email']);
    $avatar = trim($_POST['avatar']);

    // Validate required fields
    if (empty($student_id) || empty($first_name) || empty($last_name) || empty($sex) || empty($program) || empty($course) || empty($email) || empty($avatar)) {
        $_SESSION['message'] = "⚠ All required fields must be filled!";
        $_SESSION['message_type'] = "warning";
        header("Location: ../students.php");
        exit();
    }

    // Prepare SQL statement to update student record
    $query = "UPDATE students SET 
                first_name = ?, 
                middle_name = ?, 
                last_name = ?, 
                suffix = ?, 
                sex = ?, 
                program = ?, 
                course = ?, 
                email = ?, 
                avatar = ? 
              WHERE student_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssss", $first_name, $middle_name, $last_name, $suffix, $sex, $program, $course, $email, $avatar, $student_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Student profile updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating student profile!";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../students.php");
    exit();
}
?>
