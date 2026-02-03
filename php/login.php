<?php
// Start session early
session_start();
require_once 'config.php'; // DB connection

header('Content-Type: application/json');

// Force clear any previous session data
if (isset($_SESSION['student_id'])) {
    // Destroy old session fully
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    session_start(); // Start fresh session
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}

// Get input safely
$student_id = trim($_POST['student_id'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($student_id) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit();
}

// Validate credentials
$sql = "SELECT student_id, password_hash FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo json_encode(["status" => "error", "message" => "Invalid Student ID."]);
    exit();
}

if (!password_verify($password, $student['password_hash'])) {
    echo json_encode(["status" => "error", "message" => "Wrong Password."]);
    exit();
}

// ✅ Successful login: reset and store session data
$_SESSION['student_id'] = $student['student_id'];

// Optional: Sticky session support
define('ROUTE_NAME', 'web1'); // Change per backend
if (!str_contains(session_id(), '.' . ROUTE_NAME)) {
    session_id(session_id() . '.' . ROUTE_NAME);
}

// ✅ Set secure session cookie
setcookie(session_name(), session_id(), [
    'expires'  => time() + 3600,
    'path'     => '/',
    'domain'   => $_SERVER['HTTP_HOST'],
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

echo json_encode(["status" => "success", "message" => "Login successful."]);
exit();
?>
