<?php
session_start();
require 'config.php';
require '../phpqrcode/qrlib.php';

$qr_output_dir = "../qr_images/";
$web_qr_dir = "../qr_images/"; // relative path for HTML (symlinked from QR dir)

// Ensure output directory exists
if (!is_dir($qr_output_dir)) {
    mkdir($qr_output_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'];
    $suffix = $_POST['suffix'] ?? '';
    $sex = $_POST['sex'];
    $avatar = $_POST['avatar'];
    $program = $_POST['program'];
    $course = $_POST['course'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Generate QR code image (content = student ID, filename = student_id.png)
    $safe_filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student_id);
    $qr_filename = $safe_filename . ".png";
    $qr_path = $qr_output_dir . $qr_filename;

    QRcode::png($student_id, $qr_path, QR_ECLEVEL_L, 50, 1); // High-res, margin 1

    // Insert into DB: store just the filename (will be used like /qr_images/12345.png)
    $sql = "INSERT INTO students (
                student_id, first_name, middle_name, last_name, suffix, sex, avatar, program, course, email, password_hash, qr_code
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssss",
        $student_id, $first_name, $middle_name, $last_name, $suffix,
        $sex, $avatar, $program, $course, $email, $password, $safe_filename
    );

    try {
        if ($stmt->execute()) {
            $_SESSION['modal_message'] = "🎉 Registration Successful! Your account has been registered.";
            $_SESSION['modal_type'] = "success";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $error_message = $e->getMessage();
            if (strpos($error_message, "student_id") !== false) {
                $_SESSION['modal_message'] = "⚠️ Duplicate Entry! Student ID <b>$student_id</b> is already registered.";
            } elseif (strpos($error_message, "email") !== false) {
                $_SESSION['modal_message'] = "⚠️ Duplicate Entry! Email <b>$email</b> is already registered.";
            } elseif (strpos($error_message, "qr_code") !== false) {
                $_SESSION['modal_message'] = "⚠️ Duplicate QR Code file already exists.";
            } else {
                $_SESSION['modal_message'] = "⚠️ Duplicate Entry in one or more fields.";
            }
            $_SESSION['modal_type'] = "warning";
        } else {
            $_SESSION['modal_message'] = "❌ Registration Failed! " . $e->getMessage();
            $_SESSION['modal_type'] = "danger";
        }
    }

    $stmt->close();
    $conn->close();

    header("Location: ../index.php");
    exit();
}
?>
