<?php
session_start();
include 'config.php'; // Database connection

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("Access Denied: You must be logged in.");
}

$student_id = $_SESSION['student_id'];

// Get and sanitize form inputs
$first_name   = trim($_POST['first_name'] ?? '');
$middle_name  = trim($_POST['middle_name'] ?? '');
$last_name    = trim($_POST['last_name'] ?? '');
$suffix       = trim($_POST['suffix'] ?? '');
$sex          = trim($_POST['sex'] ?? '');
$avatar       = trim($_POST['avatar'] ?? '');
$email        = trim($_POST['email'] ?? '');
$program      = trim($_POST['program'] ?? '');
$course       = trim($_POST['course'] ?? '');

// 🔒 Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "❌ <b>Invalid email format.</b> ❌";
    $_SESSION['message_type'] = "danger";
    header("Location: ../home.php");
    exit();
}

// 🔍 Get existing program and course if unchanged
$sql = "SELECT program, course FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (empty($program)) $program = $row['program'];
    if (empty($course))  $course = $row['course'];
}
$stmt->close();

// ❗Check for duplicate email (excluding current student)
$check_sql = "SELECT student_id FROM students WHERE email = ? AND student_id != ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ss", $email, $student_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $_SESSION['message'] = "❌ <b>Email already in use by another account.</b> ❌";
    $_SESSION['message_type'] = "danger";
    $check_stmt->close();
    $conn->close();
    header("Location: ../home.php");
    exit();
}
$check_stmt->close();

// ✅ Perform the update
$update_sql = "UPDATE students SET 
    first_name = ?, 
    middle_name = ?, 
    last_name = ?, 
    suffix = ?, 
    sex = ?, 
    avatar = ?, 
    email = ?, 
    program = ?, 
    course = ? 
    WHERE student_id = ?";

$stmt = $conn->prepare($update_sql);
$stmt->bind_param("ssssssssss",
    $first_name,
    $middle_name,
    $last_name,
    $suffix,
    $sex,
    $avatar,
    $email,
    $program,
    $course,
    $student_id
);

if ($stmt->execute()) {
    $_SESSION['message'] = "🎉 <b>Profile updated successfully!</b> 🎉";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "❌ <b>Error updating profile:</b> " . $conn->error;
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
$conn->close();

// ✅ Redirect back to home
header("Location: ../home.php");
exit();
?>
