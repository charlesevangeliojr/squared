<?php
session_start();
require 'config.php'; // Database connection

// Only proceed if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and collect POST data
    $event_id = intval($_POST['event_id'] ?? 0);
    $event_name = trim($_POST['event_name'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $evaluation_link = trim($_POST['evaluation_link'] ?? ''); // Collect evaluation link

    // Basic validation
    if (!$event_id || !$event_name || !$event_date || !$start_time || !$end_time) {
        // Redirect with error
        header("Location: ../event.php?error=" . urlencode('Missing required fields'));
        exit;
    }

    // Normalize and validate evaluation link if provided
    if ($evaluation_link !== '') {
        if (!preg_match('#^https?://#i', $evaluation_link)) {
            $evaluation_link_candidate = 'https://' . $evaluation_link;
        } else {
            $evaluation_link_candidate = $evaluation_link;
        }

        if (filter_var($evaluation_link_candidate, FILTER_VALIDATE_URL)) {
            $evaluation_link = $evaluation_link_candidate;
        } else {
            header("Location: ../event.php?error=" . urlencode('Invalid evaluation link'));
            exit();
        }
    } else {
        $evaluation_link = null;
    }

    // Prepare update query to include evaluation link
    $sql = "UPDATE events 
            SET event_name = ?, event_date = ?, start_time = ?, end_time = ?, evaluation_link = ? 
            WHERE event_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ../event.php?error=" . urlencode('Prepare failed: ' . $conn->error));
        exit();
    }

    $stmt->bind_param("sssssi", $event_name, $event_date, $start_time, $end_time, $evaluation_link, $event_id);

    if ($stmt->execute()) {
        header("Location: ../event.php?success=" . urlencode('Event updated successfully'));
    } else {
        header("Location: ../event.php?error=" . urlencode('Failed to update event: ' . $stmt->error));
    }

    $stmt->close();
    $conn->close();
} else {
    // Invalid access, redirect back
    header("Location: ../event.php");
    exit;
}
?>
