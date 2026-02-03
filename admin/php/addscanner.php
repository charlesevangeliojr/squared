<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_ids = $_POST['scanner_ids']; // Array of student IDs

    foreach ($student_ids as $student_id) {
        $student_id = trim($student_id);

        // Check if the student is already assigned as a scanner
        $checkQuery = "SELECT id FROM event_scanners WHERE student_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            continue; // Skip if the student is already assigned
        }

        // Insert new scanner assignment (Default to Allow)
        $insertQuery = "INSERT INTO event_scanners (student_id, status) VALUES (?, 'Allow')";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
    }

    header("Location: ../scanner.php?success=Students added successfully!");
}
?>
