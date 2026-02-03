<?php
// Start session early
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Default response
$response = ['success' => false, 'message' => 'Invalid request.'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $response['message'] = 'Please enter both username and password.';
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // ✅ Store session variables
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];

            // ✅ Force sticky session route in session ID (optional, if Apache needs it)
            if (!str_contains(session_id(), '.web1')) {
                session_id(session_id() . '.web1');
            }

            // ✅ Use relative redirect (avoid relying on $_SERVER['HTTP_HOST'])
            $response = [
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => '../admin/dashboard.php'
            ];
        } else {
            $response['message'] = 'Incorrect password.';
        }
    } else {
        $response['message'] = 'Account not found.';
    }

    $stmt->close();
}

echo json_encode($response);
exit();
