<?php
session_start();
require_once 'php/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index");
    exit();
}

$student_id = $_SESSION['student_id'];
$award_slug = $_POST['award'] ?? '';
$nominee_id = $_POST['nominee_id'] ?? '';

if (!preg_match('/^[a-z0-9\-]{3,64}$/i', $award_slug) || !ctype_digit($nominee_id)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'invalid_input']);
    exit;
}

$conn->begin_transaction();

try {
    // resolve award_id
    $stmt = $conn->prepare("SELECT id, opens_at, closes_at FROM pca_awards WHERE slug = ?");
    $stmt->bind_param("s", $award_slug);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!($award = $res->fetch_assoc())) {
        throw new Exception('award_not_found');
    }
    $award_id = (int)$award['id'];
    $now = time();
    if (($award['opens_at'] && $now < strtotime($award['opens_at'])) ||
        ($award['closes_at'] && $now > strtotime($award['closes_at']))) {
        throw new Exception('voting_closed');
    }

    // check nominee belongs to award
    $stmt = $conn->prepare("SELECT 1 FROM pca_nominees WHERE id = ? AND award_id = ?");
    $stmt->bind_param("ii", $nominee_id, $award_id);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_row()) {
        throw new Exception('nominee_not_found');
    }

    // insert vote
    $stmt = $conn->prepare("INSERT INTO pca_votes (award_id, nominee_id, student_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $award_id, $nominee_id, $student_id);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['ok' => true]);
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    if ($e->getCode() === 1062) {
        http_response_code(409);
        echo json_encode(['ok' => false, 'error' => 'already_voted']);
    } else {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'db_error']);
    }
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
