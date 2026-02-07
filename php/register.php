<?php
session_start();
require 'config.php';
require '../phpqrcode/qrlib.php';

$qr_output_dir = "../qr_images/";
$web_qr_dir = "../qr_images/";

// Ensure output directory exists
if (!is_dir($qr_output_dir)) {
    mkdir($qr_output_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ==================== reCAPTCHA v2 VERIFICATION ====================
    // Check if reCAPTCHA response exists
    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        $_SESSION['modal_message'] = "❌ Please complete the 'I'm not a robot' verification.";
        $_SESSION['modal_type'] = "danger";
        header("Location: ../index.php");
        exit();
    }

    // Your reCAPTCHA Secret Key
    $secretKey = "6LfQ32MsAAAAAF910chGBdjPMqleukkXRdFU6bDN";
    $captchaResponse = $_POST['g-recaptcha-response'];
    
    // Verify with Google reCAPTCHA API
    $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $secretKey,
        'response' => $captchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    );
    
    // Use cURL for better error handling
    $ch = curl_init($verifyURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        // If cURL fails, you might want to log this error
        error_log("reCAPTCHA cURL Error: " . $curlError);
        $_SESSION['modal_message'] = "❌ reCAPTCHA verification service unavailable. Please try again.";
        $_SESSION['modal_type'] = "danger";
        header("Location: ../index.php");
        exit();
    }
    
    $result = json_decode($response);
    
    // Check if verification was successful
    if (!$result || !$result->success) {
        $errorMsg = "❌ reCAPTCHA verification failed.";
        if (isset($result->{'error-codes'})) {
            $errors = implode(", ", $result->{'error-codes'});
            error_log("reCAPTCHA Errors: " . $errors);
            
            // Common error messages for users
            if (in_array('missing-input-secret', $result->{'error-codes'}) || 
                in_array('invalid-input-secret', $result->{'error-codes'})) {
                $errorMsg = "❌ Server configuration error. Please contact administrator.";
            } elseif (in_array('timeout-or-duplicate', $result->{'error-codes'})) {
                $errorMsg = "❌ reCAPTCHA expired. Please verify again.";
            }
        }
        
        $_SESSION['modal_message'] = $errorMsg;
        $_SESSION['modal_type'] = "danger";
        header("Location: ../index.php");
        exit();
    }
    // ==================== END reCAPTCHA VERIFICATION ====================

    // Continue with registration if reCAPTCHA passes
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

    // Generate QR code
    $safe_filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student_id);
    $qr_filename = $safe_filename . ".png";
    $qr_path = $qr_output_dir . $qr_filename;

    QRcode::png($student_id, $qr_path, QR_ECLEVEL_L, 50, 1);

    // Insert into DB
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