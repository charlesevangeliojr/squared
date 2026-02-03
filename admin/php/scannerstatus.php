<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scanner_id = intval($_POST['scanner_id']);
    $new_status = ($_POST['new_status'] == 'Allow') ? 'Allow' : 'Deny';

    $stmt = $conn->prepare("UPDATE event_scanners SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $scanner_id);
    
    if ($stmt->execute()) {
        header("Location: ../scanner.php?success=Status updated successfully!");
    } else {
        header("Location: ../scanner.php?error=Failed to update status.");
    }
}
?>
