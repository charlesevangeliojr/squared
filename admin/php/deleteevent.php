<?php
require 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate event_id
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;

    if ($event_id > 0) {
        // Prepare and execute delete statement
        $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);

        if ($stmt->execute()) {
            // Redirect back with success message
            header("Location: ../event.php?success=Event deleted successfully!");
        } else {
            // Redirect with error
            header("Location: ../event.php?error=Failed to delete event.");
        }

        $stmt->close();
    } else {
        // Invalid ID
        header("Location: ../event.php?error=Invalid event ID.");
    }
} else {
    // Direct access not allowed
    header("Location: ../event.php");
}
?>
