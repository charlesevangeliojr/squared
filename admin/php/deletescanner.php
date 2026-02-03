<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scanner_id = intval($_POST['scanner_id']);

    $deleteQuery = "DELETE FROM event_scanners WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $scanner_id);

    if ($stmt->execute()) {
        header("Location: ../scanner.php?success=Student scanner removed successfully!");
    } else {
        header("Location: ../scanner.php?error=Failed to remove scanner.");
    }
}
?>
