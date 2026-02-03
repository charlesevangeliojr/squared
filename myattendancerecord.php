<?php
session_start();
require 'php/config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Check if student is an allowed scanner
$scanner_sql = "SELECT * FROM event_scanners WHERE student_id = ? AND status = 'allow'";
$scanner_stmt = $conn->prepare($scanner_sql);
$scanner_stmt->bind_param("s", $student_id);
$scanner_stmt->execute();
$scanner_result = $scanner_stmt->get_result();
$isScanner = $scanner_result->num_rows > 0;

// Get student info
$student_sql = "SELECT * FROM students WHERE student_id = ?";
$student_stmt = $conn->prepare($student_sql);
$student_stmt->bind_param("s", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();

// Is there any active (ON) award?
$hasVoting = false;
if (isset($conn)) {
  $q = $conn->query("SELECT COUNT(*) AS c FROM pca_awards WHERE status='on'");
  if ($q && ($row = $q->fetch_assoc())) $hasVoting = ((int)$row['c'] > 0);
  if ($q) $q->free();
}


if ($student_result->num_rows === 0) {
    echo "Student not found.";
    exit;
}
$student = $student_result->fetch_assoc();

// Get attendance data including evaluation link
$attendance_sql = "SELECT e.event_name, e.event_date, e.start_time, e.end_time, e.evaluation_link, a.scanned_at
                   FROM attendance a
                   JOIN events e ON a.event_id = e.event_id
                   WHERE a.student_id = ?
                   ORDER BY a.scanned_at DESC";

$att_stmt = $conn->prepare($attendance_sql);
$att_stmt->bind_param("s", $student_id);
$att_stmt->execute();
$att_result = $att_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared My Attendance</title>
    <link rel="icon" type="image/png" href="images/Squared_Logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/attendance.css">

    <style>
        /* Define color variables used across this page so text/gradients are visible */
        :root {
            --color-dark: #0D5B11;
            --color-primary: #0C5A11;
            --color-success: #69B41E;
            --color-info: #8DC71E;
            --color-light: #B8D53D;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="images/Squared_Logo.png" alt="Squared Logo"> Squared
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="qrcard.php">QR-Card</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notify.php">Announcements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="myattendancerecord.php">My Attendance</a>
                </li>
                <?php if ($hasVoting): ?>
  <li class="nav-item">
    <a class="nav-link" href="voting.php">Vote</a>
  </li>
<?php endif; ?>
                <?php if ($isScanner): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="event_scanner.php">Scan</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="php/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-4">
    <h2 class="text-center attendance-header">
        <i class="bi bi-calendar-check me-2"></i>My Attendance Record
    </h2>

    <?php if ($att_result->num_rows > 0): ?>
    <div class="row g-4 mt-2">
        <?php while ($row = $att_result->fetch_assoc()): 
            $eventDate = new DateTime($row['event_date']);
            $scannedDate = new DateTime($row['scanned_at']);
            $isRecent = (new DateTime())->diff($scannedDate)->days < 7;
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="card attendance-card shadow-sm border-0 h-100">
                <div class="card-header bg-gradient d-flex justify-content-between align-items-center" 
                     style="background: linear-gradient(135deg, var(--color-primary), var(--color-success));">
                    <h5 class="card-title mb-0 text-Green">
                        <i class="bi bi-calendar-event me-2"></i><?= htmlspecialchars($row['event_name']) ?>
                    </h5>
                    <?php if ($isRecent): ?>
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-star-fill me-1"></i>Recent
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <div class="event-date mb-3 p-3 rounded" 
                         style="background-color: rgba(184, 213, 61, 0.1); border-left: 4px solid var(--color-info);">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-date fs-4 me-3" style="color: var(--color-dark);"></i>
                            <div>
                                <div class="fw-bold" style="color: var(--color-dark);">
                                    <?= htmlspecialchars($row['event_date']) ?>
                                </div>
                                <small class="text-muted">Event Date</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="scanned-time mb-3 p-3 rounded" 
                         style="background-color: rgba(141, 199, 30, 0.1); border-left: 4px solid var(--color-success);">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history fs-4 me-3" style="color: var(--color-primary);"></i>
                            <div>
                                <div class="fw-bold" style="color: var(--color-primary);">
                                    <?= date("M d, Y g:i A", strtotime($row['scanned_at'])) ?>
                                </div>
                                <small class="text-muted">Scanned At</small>
                            </div>
                        </div>
                    </div>

                    <!-- Display the evaluation form link if available -->
                    <?php if (!empty($row['evaluation_link'])): ?>
                    <div class="evaluation-section mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" style="color: var(--color-dark);">
                                <i class="bi bi-clipboard-check me-2"></i>Event Evaluation
                            </span>
                            <span class="badge rounded-pill" style="background-color: var(--color-info);">
                                Available
                            </span>
                        </div>
                        <a href="<?= htmlspecialchars($row['evaluation_link']) ?>" 
                           target="_blank" 
                           class="btn w-100 btn-evaluation"
                           style="background-color: var(--color-success); color: white; border: none;">
                            <i class="bi bi-pencil-square me-2"></i>Fill Evaluation Form
                        </a>
                        <small class="text-muted d-block mt-2 text-center">
                            Your feedback helps improve future events
                        </small>
                    </div>
                    <?php else: ?>
                    <div class="evaluation-section mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" style="color: var(--color-dark);">
                                <i class="bi bi-clipboard-x me-2"></i>Event Evaluation
                            </span>
                            <span class="badge rounded-pill bg-secondary">
                                Unavailable
                            </span>
                        </div>
                        <div class="text-center p-3 rounded" 
                             style="background-color: rgba(141, 199, 30, 0.05);">
                            <i class="bi bi-link-45deg fs-4 d-block mb-2" style="color: var(--color-light);"></i>
                            <span class="text-muted">No Evaluation Link Available</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">
                        <i class="bi bi-check-circle-fill me-1" style="color: var(--color-success);"></i>
                        Attendance Confirmed
                    </small>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="no-records text-center py-5">
        <div class="icon-container mb-4">
            <i class="bi bi-calendar-x" style="font-size: 4rem; color: var(--color-light);"></i>
        </div>
        <h4 class="mb-3" style="color: var(--color-dark);">No Attendance Records Found</h4>
        <p class="text-muted mb-4">You haven't attended any events yet. Start exploring available events!</p>
        <a href="#" class="btn" style="background-color: var(--color-primary); color: white;">
            <i class="bi bi-calendar-event me-2"></i>Browse Events
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.attendance-header {
    color: var(--color-dark);
    font-weight: 700;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid var(--color-success);
    background: linear-gradient(90deg, var(--color-dark), var(--color-primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.attendance-card {
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(141, 199, 30, 0.2);
}

.attendance-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(105, 180, 30, 0.15);
}

.card-header.bg-gradient {
    padding: 1rem 1.25rem;
}

.btn-evaluation {
    border-radius: 8px;
    padding: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-evaluation:hover {
    background-color: var(--color-primary) !important;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(24, 124, 25, 0.3);
}

.badge {
    font-weight: 500;
}

.event-date, .scanned-time {
    transition: background-color 0.3s ease;
}

.event-date:hover, .scanned-time:hover {
    background-color: rgba(184, 213, 61, 0.15) !important;
}

.no-records {
    background: linear-gradient(135deg, rgba(184, 213, 61, 0.05), rgba(141, 199, 30, 0.05));
    border-radius: 15px;
    padding: 3rem !important;
    margin: 2rem 0;
}

.icon-container {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .attendance-card {
        margin-bottom: 1.5rem;
    }
}
</style>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('js/sw.js');
  }
</script>
</body>
</html>
