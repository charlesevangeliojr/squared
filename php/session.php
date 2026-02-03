<?php
session_start();

$response = ["show_modal" => false];

if (isset($_SESSION['modal_message'])) {
    $response["show_modal"] = true;
    $response["message"] = $_SESSION['modal_message'];
    $response["type"] = $_SESSION['modal_type'];

    // Clear session after displaying
    unset($_SESSION['modal_message']);
    unset($_SESSION['modal_type']);
}

header("Content-Type: application/json");
echo json_encode($response);
?>
