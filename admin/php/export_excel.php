<?php
require_once 'config.php';

$event_id = $_GET['event_id'] ?? null;
if (!$event_id) die("No event ID provided.");

// Get event name
$event_stmt = $conn->prepare("SELECT event_name, event_date FROM events WHERE event_id = ?");
$event_stmt->bind_param("i", $event_id);
$event_stmt->execute();
$event_info = $event_stmt->get_result()->fetch_assoc();
$event_name = $event_info['event_name'] ?? 'Unknown Event';
$event_date = date("F j, Y", strtotime($event_info['event_date'] ?? 'now'));

// Headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=event_attendance_{$event_id}.xls");
header("Pragma: no-cache");
header("Expires: 0");

// HTML structure with embedded style
echo "
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #d1e7dd;
        }
        .header {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
        .event-row {
            font-size: 16px;
            font-weight: bold;
        }
        .left {
            text-align: left;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>
";


// Header with logo
echo "
<table>
    <tr>
        <td colspan='7' class='header'>
            {$logoHtml}
            <div>Squared Attendance System</div>
        </td>
    </tr>
    <tr class='event-row'>
        <td colspan='7' class='header'>
            <strong>Event:</strong> " . htmlspecialchars($event_name) . "
        </td>
    </tr>
    <tr><td colspan='7'>&nbsp;</td></tr>
</table>
";


// Table headers
echo "<table border='1'>
<tr>
    <th>Student ID</th>
    <th>Full Name</th>
    <th>Sex</th>
    <th>Program</th>
    <th>Course</th>
    <th>Date</th>
    <th>Time</th>
</tr>";

// Get attendance
$query = "SELECT a.student_id, s.first_name, s.middle_name, s.last_name, s.suffix,
                 s.program, s.course, s.sex, a.scanned_at
          FROM attendance a
          JOIN students s ON a.student_id = s.student_id
          WHERE a.event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $fullName = $row['last_name'] . ', ' . $row['first_name'];
    if ($row['middle_name']) $fullName .= ' ' . strtoupper($row['middle_name'][0]) . '.';
    if ($row['suffix']) $fullName .= ' ' . $row['suffix'];

    $scannedDate = date("F j, Y", strtotime($row['scanned_at']));
    $scannedTime = date("g:i A", strtotime($row['scanned_at']));

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
    echo "<td>" . htmlspecialchars($fullName) . "</td>";
    echo "<td>" . htmlspecialchars($row['sex']) . "</td>";
    echo "<td>" . htmlspecialchars($row['program']) . "</td>";
    echo "<td>" . htmlspecialchars($row['course']) . "</td>";
    echo "<td>" . htmlspecialchars($scannedDate) . "</td>";
    echo "<td>" . htmlspecialchars($scannedTime) . "</td>";
    echo "</tr>";
}

echo "</table></body></html>";
