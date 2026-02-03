<?php
session_start();
require 'php/config.php'; // Database connection

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
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

$scanner_id = null;
if ($isScanner) {
    $scanner_row = $scanner_result->fetch_assoc();
    $scanner_id = $scanner_row['id'];
}

// Fetch events from the database
$query = "SELECT * FROM events ORDER BY event_date DESC, start_time DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Scan</title>
    <link rel="icon" type="image/png" href="images/Squared_Logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
                body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .card {
            border-radius: 15px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            padding: 0;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-info, .btn-secondary {
            border-radius: 10px;
            font-weight: 600;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.5em 0.75em;
        }

        h2.tt {
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
        }
    </style>
</head>

<body>
<body class="bg-light">


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


<div class="container mt-5">
    <!-- Header Section -->
    <div class="scan-header text-center mb-5">
        <h1 class="display-5 fw-bold text-dark mb-3">Event Scanner</h1>
        <p class="text-muted fs-5">Select an event below to start scanning attendance</p>
        <?php if ($isScanner): ?>
            <div class="scanner-badge d-inline-flex align-items-center bg-primary bg-opacity-10 px-4 py-2 rounded-pill border border-primary border-opacity-25">
                <i class="bi bi-check-circle-fill text-primary me-2 fs-5"></i>
                <span class="text-primary fw-semibold">You are authorized as a scanner</span>
            </div>
        <?php else: ?>
            <div class="scanner-badge d-inline-flex align-items-center bg-warning bg-opacity-10 px-4 py-2 rounded-pill border border-warning border-opacity-25">
                <i class="bi bi-exclamation-circle-fill text-warning me-2 fs-5"></i>
                <span class="text-warning fw-semibold">Scanner access required</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Events Grid -->
    <div class="row g-4">
        <?php 
        $eventCounter = 0;
        while ($row = $result->fetch_assoc()): 
            $eventCounter++;
            $isActive = $row['status'] == 'Active';
            $isToday = date('Y-m-d') == $row['event_date'];
            $isFuture = date('Y-m-d') < $row['event_date'];
        ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="event-card card border-0 shadow-lg h-100 overflow-hidden" 
                     data-event-status="<?php echo strtolower($row['status']); ?>">
                    
                    <!-- Card Header with Status Indicator -->
                    <div class="card-header position-relative border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge <?php echo $isActive ? 'bg-primary' : 'bg-secondary'; ?> bg-opacity-10 text-<?php echo $isActive ? 'primary' : 'secondary'; ?> border border-<?php echo $isActive ? 'primary' : 'secondary'; ?> border-opacity-25 px-3 py-2 rounded-pill fw-semibold">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                    <?php if ($isToday): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold ms-2">
                                            Today
                                        </span>
                                    <?php elseif ($isFuture): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill fw-semibold ms-2">
                                            Upcoming
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-3 fs-4">
                                    <?php echo htmlspecialchars($row['event_name']); ?>
                                </h5>
                            </div>
                            <div class="event-icon">
                                <i class="bi bi-calendar-event fs-1 <?php echo $isActive ? 'text-primary' : 'text-secondary'; ?> opacity-75"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body pt-0">
                        <!-- Event Details -->
                        <div class="event-details mb-4">
                            <!-- Date -->
                            <div class="detail-item d-flex align-items-center mb-3">
                                <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-calendar-date text-primary fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Date</small>
                                    <strong class="text-dark"><?php echo htmlspecialchars($row['event_date']); ?></strong>
                                </div>
                            </div>
                            
                            <!-- Time Range -->
                            <div class="time-range bg-light rounded-3 p-3 mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <small class="text-muted d-block">Start Time</small>
                                            <strong class="text-primary fs-6">
                                                <i class="bi bi-clock me-1"></i>
                                                <?php echo date("g:i A", strtotime($row['start_time'])); ?>
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <small class="text-muted d-block">End Time</small>
                                            <strong class="text-primary fs-6">
                                                <i class="bi bi-clock-fill me-1"></i>
                                                <?php echo date("g:i A", strtotime($row['end_time'])); ?>
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($isActive): ?>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 50%"></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Event ID -->
                            <div class="event-id text-center">
                                <small class="text-muted">
                                    <i class="bi bi-hash me-1"></i>
                                    Event ID: <?php echo htmlspecialchars($row['event_id']); ?>
                                </small>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <?php if ($isActive && $isScanner): ?>
                            <a href="scan_form.php?event_id=<?= $row['event_id']; ?>&scanner_id=<?= $scanner_id ?>" 
                               class="scan-action-btn btn btn-primary w-100 py-3 fw-bold fs-5 rounded-3 border-0 shadow-sm position-relative overflow-hidden">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-upc-scan me-3 fs-4"></i>
                                    <span>Start Scanning</span>
                                </div>
                                <div class="hover-effect position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-10"></div>
                            </a>
                        <?php else: ?>
                            <button class="scan-action-btn btn <?php echo $isActive ? 'btn-outline-primary' : 'btn-secondary'; ?> w-100 py-3 fw-bold fs-5 rounded-3 shadow-sm" 
                                    disabled>
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-upc-scan me-3 fs-4"></i>
                                    <span>
                                        <?php if (!$isActive): ?>
                                            Event Inactive
                                        <?php elseif (!$isScanner): ?>
                                            Access Restricted
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

        <!-- Empty State -->
        <?php if ($eventCounter == 0): ?>
            <div class="col-12">
                <div class="empty-state text-center py-5 my-5">
                    <div class="empty-icon mb-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-dark fw-bold mb-3">No Events Available</h3>
                    <p class="text-muted mb-4">There are currently no events scheduled for scanning.</p>
                    <?php if (!$isScanner): ?>
                        <div class="alert alert-warning d-inline-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            You need scanner authorization to access scanning features
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Color Variables from your palette */
    :root {
        --color-dark: #0D5B11;
        --color-primary: #187C19;
        --color-success: #69B41E;
        --color-info: #8DC71E;
        --color-light: #B8D53D;
    }

    .scan-header {
        background: linear-gradient(135deg, #f0f9f0 0%, #e6f7e6 100%);
        padding: 2.5rem;
        border-radius: 20px;
        border: 1px solid rgba(24, 124, 25, 0.1);
    }

    .event-card {
        border-radius: 20px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .event-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(24, 124, 25, 0.1) !important;
        border-color: rgba(24, 124, 25, 0.2);
    }

    .card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 1.5rem 1.5rem 0;
    }

    .event-icon {
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .event-card:hover .event-icon {
        opacity: 1;
        transform: scale(1.05);
    }

    .icon-wrapper {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .event-card:hover .icon-wrapper {
        background: rgba(24, 124, 25, 0.15) !important;
        transform: scale(1.1);
    }

    .time-range {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9f5e9 100%);
        border: 1px solid rgba(24, 124, 25, 0.1);
    }

    /* Custom button colors using your palette */
    .btn-primary {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-dark) 100%) !important;
        border-color: var(--color-primary) !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--color-dark) 0%, #0a4a0d 100%) !important;
        border-color: var(--color-dark) !important;
    }

    .btn-outline-primary {
        color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
    }

    .btn-outline-primary:hover {
        background-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
        color: white !important;
    }

    .text-primary {
        color: var(--color-primary) !important;
    }

    .border-primary {
        border-color: var(--color-primary) !important;
    }

    .bg-primary {
        background-color: var(--color-primary) !important;
    }

    .bg-primary.bg-opacity-10 {
        background-color: rgba(24, 124, 25, 0.1) !important;
    }

    .border-primary.border-opacity-25 {
        border-color: rgba(24, 124, 25, 0.25) !important;
    }

    /* Success variations */
    .text-success {
        color: var(--color-success) !important;
    }

    .border-success {
        border-color: var(--color-success) !important;
    }

    .bg-success {
        background-color: var(--color-success) !important;
    }

    .bg-success.bg-opacity-10 {
        background-color: rgba(105, 180, 30, 0.1) !important;
    }

    .border-success.border-opacity-25 {
        border-color: rgba(105, 180, 30, 0.25) !important;
    }

    /* Info variations */
    .text-info {
        color: var(--color-info) !important;
    }

    .border-info {
        border-color: var(--color-info) !important;
    }

    .bg-info {
        background-color: var(--color-info) !important;
    }

    .bg-info.bg-opacity-10 {
        background-color: rgba(141, 199, 30, 0.1) !important;
    }

    .border-info.border-opacity-25 {
        border-color: rgba(141, 199, 30, 0.25) !important;
    }

    .scan-action-btn {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-success) 100%) !important;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .scan-action-btn:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(24, 124, 25, 0.3) !important;
        background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-primary) 100%) !important;
    }

    .scan-action-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-outline-primary:disabled {
        color: #6c757d !important;
        border-color: #dee2e6 !important;
        background: #f8f9fa !important;
    }

    .hover-effect {
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }

    .scan-action-btn:hover .hover-effect {
        transform: translateX(100%);
    }

    .progress-bar {
        background: linear-gradient(90deg, var(--color-primary), var(--color-success)) !important;
        transition: width 1.5s ease-in-out;
    }

    .event-card:hover .progress-bar {
        width: 100%;
    }

    .empty-state {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 20px;
        padding: 3rem;
        border: 2px dashed #dee2e6;
    }

    @media (max-width: 768px) {
        .scan-header {
            padding: 1.5rem;
        }
        
        .scan-action-btn {
            padding: 0.75rem !important;
            font-size: 1rem !important;
        }
    }
</style>

<script>
    // Add animation to cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.event-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('js/sw.js');
  }
</script>

</body>

</html>