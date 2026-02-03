<?php
require 'config.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $event_id = intval($_GET['id']);
    $new_status = ($_GET['status'] == 'Active') ? 'Active' : 'Inactive';

    $stmt = $conn->prepare("UPDATE events SET status = ? WHERE event_id = ?");
    $stmt->bind_param("si", $new_status, $event_id);

    if ($stmt->execute()) {
        header("Location: ../event.php?success=Event status updated successfully!");
    } else {
        header("Location: ../event.php?error=Failed to update event status.");
    }
    $stmt->close();
}
?>
