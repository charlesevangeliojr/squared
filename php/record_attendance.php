<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? '';
    $scanner_id = $_POST['scanner_id'] ?? '';
    $student_id = trim($_POST['student_id'] ?? '');

    if ($event_id && $scanner_id && $student_id) {
        // Check if already recorded
        $checkSql = "SELECT * FROM attendance WHERE event_id = ? AND student_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("is", $event_id, $student_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Already scanned.']);
            exit;
        }

        // Record new attendance
        $insertSql = "INSERT INTO attendance (event_id, scanner_id, student_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("iis", $event_id, $scanner_id, $student_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Attendance recorded!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saving attendance.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
