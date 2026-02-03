<?php
session_start();
require 'config.php'; // Ensure this file contains your database connection

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$student_id = $_SESSION['student_id']; // Retrieve the student ID from the session

// Prepare SQL query to fetch student data
$sql = "SELECT student_id, first_name, middle_name, last_name, suffix, sex, avatar, program, course, email 
        FROM students 
        WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    echo json_encode($user_data); // Return user data as JSON
} else {
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
