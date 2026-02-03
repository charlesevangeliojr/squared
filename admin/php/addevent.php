<?php
require 'config.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect inputs safely
    $event_name = trim($_POST["event_name"] ?? '');
    $event_date = trim($_POST["event_date"] ?? '');
    $start_time = trim($_POST["start_time"] ?? '');
    $end_time = trim($_POST["end_time"] ?? '');
    $evaluation_link = trim($_POST["evaluation_link"] ?? '');

    // Basic validation
    if (empty($event_name) || empty($event_date) || empty($start_time) || empty($end_time)) {
        header("Location: ../event.php?error=" . urlencode('All fields are required!'));
        exit();
    }

    // Normalize and validate evaluation link if provided
    if ($evaluation_link !== '') {
        // Add scheme if missing
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

    // Prepare insert
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, start_time, end_time, evaluation_link) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        header("Location: ../event.php?error=" . urlencode('Prepare failed: ' . $conn->error));
        exit();
    }

    // Use null if evaluation_link is null
    $stmt->bind_param("sssss", $event_name, $event_date, $start_time, $end_time, $evaluation_link);

    if ($stmt->execute()) {
        header("Location: ../event.php?success=" . urlencode('Event added successfully'));
        exit();
    } else {
        header("Location: ../event.php?error=" . urlencode('Failed to add event: ' . $stmt->error));
        exit();
    }
} else {
    // If accessed directly, redirect to events page
    header("Location: ../event.php");
    exit();
}
?>
