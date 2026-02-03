<?php
// Set timezone to Asia/Singapore (UTC+8)
date_default_timezone_set('Asia/Singapore');

// ✅ Direct DB connection here
$host = "localhost";
$user = "root";
$password = "ite2025";
$database = "squared";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    file_put_contents(__DIR__ . '/cron_log.txt', "DB Connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
    exit("Connection failed: " . $conn->connect_error);
}

// Get current date and time
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

// Auto-update event status
$update_sql = "UPDATE events 
               SET status = CASE 
                    WHEN event_date = ? AND start_time <= ? AND end_time >= ? THEN 'Active'
                    ELSE 'Inactive'
                END";

$stmt = $conn->prepare($update_sql);
$stmt->bind_param("sss", $currentDate, $currentTime, $currentTime);
$stmt->execute();
$stmt->close();

?>
