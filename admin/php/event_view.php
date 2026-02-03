<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if config.php exists
if (!file_exists('config.php')) {
    die("Error: config.php file not found. Please check if the file exists.");
}

require_once 'config.php';

// Check if $conn is set in config.php
if (!isset($conn) || !$conn) {
    die("Error: Database connection not established. Please check your config.php file.");
}

// Set UTF-8 encoding
header('Content-Type: text/html; charset=utf-8');

// Get event_id from URL
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if (!$event_id) {
    die("Error: Invalid event ID. Please provide a valid event_id parameter.");
}

// Set UTF-8 for database connection
if (!$conn->set_charset("utf8mb4")) {
    die("Error setting charset: " . $conn->error);
}

// Get event details
$event_name = 'Unknown Event';
$event_date = null;
$start_time = null;
$end_time = null;

$event_stmt = $conn->prepare("SELECT event_name, event_date, start_time, end_time, status FROM events WHERE event_id = ?");
if (!$event_stmt) {
    die("Error preparing event query: " . $conn->error);
}

$event_stmt->bind_param("i", $event_id);
if (!$event_stmt->execute()) {
    die("Error executing event query: " . $event_stmt->error);
}

$event_result = $event_stmt->get_result();
if ($event_result && $event_result->num_rows > 0) {
    $row = $event_result->fetch_assoc();
    $event_name = $row['event_name'] ?? 'Unknown Event';
    $event_date = $row['event_date'] ?? null;
    $start_time = $row['start_time'] ?? null;
    $end_time = $row['end_time'] ?? null;
    $event_status = $row['status'] ?? 'Inactive';
}
$event_stmt->close();

// Get attendance data
$attendance_data = [];
$male_count = 0;
$female_count = 0;
$unique_courses = [];
$unique_programs = [];

$stmt = $conn->prepare("SELECT a.student_id, s.first_name, s.middle_name, s.last_name, s.suffix, s.sex, s.program, s.course, a.scanned_at
                        FROM attendance a
                        JOIN students s ON a.student_id = s.student_id
                        WHERE a.event_id = ?
                        ORDER BY a.scanned_at DESC");

if (!$stmt) {
    die("Error preparing attendance query: " . $conn->error);
}

$stmt->bind_param("i", $event_id);
if (!$stmt->execute()) {
    die("Error executing attendance query: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $attendance_data[] = $row;
        if (isset($row['sex'])) {
            $gender = strtolower($row['sex']);
            if ($gender == 'male') $male_count++;
            if ($gender == 'female') $female_count++;
        }
        if (isset($row['course'])) {
            $unique_courses[$row['course']] = true;
        }
        if (isset($row['program'])) {
            $unique_programs[$row['program']] = true;
        }
    }
}
$stmt->close();

$attendance_count = count($attendance_data);

// Generate URLs - Remove the "squared" part from URL
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$current_path = str_replace('/squared', '', $_SERVER['REQUEST_URI']);
$current_url = $base_url . $current_path;
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($current_url);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - <?= htmlspecialchars($event_name) ?></title>
    <link rel="icon" type="image/png" href="../../images/Squared_Logo.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-dark: #0D5B11;
            --primary-medium: #187C19;
            --primary-light: #69B41E;
            --accent-light: #8DC71E;
            --accent-lighter: #B8D53D;
            --bg-light: #f8f9fa;
            --header-height: 140px;
        }
        
        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding-top: var(--header-height); /* Space for fixed header */
            padding-bottom: 20px;
            font-size: 14px;
        }
        
        /* Fixed Header */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Header Container */
        .header-container {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            color: white;
            padding: .75rem;
        }
        
        .header-logo {
            height: 40px;
            filter: brightness(0) invert(1);
        }
        
        .header-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .event-title {
            font-size: 0.9rem;
            opacity: 0.95;
        }
        
        .status-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .status-active {
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--primary-dark);
        }
        
        .status-inactive {
            background-color: rgba(255, 255, 255, 0.7);
            color: #666;
        }
        
        /* Stats Bar (compact) */
        .stats-bar {
            background: white;
            padding: 0.45rem 0.5rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.4rem;
            border-bottom: 1px solid #eee;
            justify-items: center;
        }
        
        .stat-item {
            text-align: center;
            width: 100%;
            max-width: 220px;
        }
        
        .stat-number {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-dark);
            line-height: 1;
        }
        
        .stat-label {
            font-size: 0.65rem;
            color: #666;
            margin-top: 0.15rem;
        }
        
        /* Search Bar */
        .search-bar {
            background: white;
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-direction: column;
            gap: 0rem;
        }
        /* Filters */
        .filters-bar {
            background: transparent;
            width: 100%;
        }
        .filters-bar .row {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: nowrap; /* keep in single row */
        }
        .filters-bar .col-4 {
            display: flex;
            align-items: center;
            padding-left: 0;
            padding-right: 0;
            flex: 1 1 33.333%;
            min-width: 0; /* allow shrinking */
        }
        .filters-bar .col-4 .form-select {
            min-width: 0;
            width: 100%;
            font-size: 0.85rem;
            padding: 0.2rem 0.4rem;
        }
        .filters-bar .form-select.form-select-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.8rem;
        }
        /* Responsive: keep single row on small screens; shrink controls to fit */
        @media (max-width: 576px) {
            .search-bar {
                padding: 0.5rem;
            }
            .filters-bar .row {
                gap: 0.25rem;
            }
            .filters-bar .col-4 {
                flex: 1 1 0;
                min-width: 0;
                text-align: left !important;
            }
            .filters-bar .col-4 .form-select {
                width: 100%;
                min-width: 0;
                font-size: 0.82rem;
                padding: 0.18rem 0.35rem;
            }
        }
        
        /* Action Buttons */
        .action-bar {
            background: white;
            padding: 0.75rem;
            display: flex;
            gap: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .action-btn {
            flex: 1;
            padding: 0.6rem;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }
        
        .btn-primary-custom {
            background-color: var(--primary-medium);
            border-color: var(--primary-medium);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-outline-custom {
            background-color: white;
            border: 1px solid var(--accent-light);
            color: var(--primary-medium);
        }
        
        .btn-outline-custom:hover {
            background-color: var(--accent-lighter);
            border-color: var(--accent-light);
        }
        
        /* Share Link */
        .share-link {
            background: white;
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
            display: none; /* Initially hidden */
        }
        
        .share-link.active {
            display: block;
        }
        
        /* QR Code */
        .qr-container {
            background: white;
            padding: 1.5rem;
            text-align: center;
            display: none;
        }
        
        .qr-container.active {
            display: block;
        }
        
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
        }
        
        /* Main Content Container - FIXED PADDING */
        .main-content {
            margin-top: 1rem;
        }
        
        /* Table Container */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: calc(var(--header-height) + -3rem) 1rem 1rem 1rem;
        }
        
        /* Mobile Table View */
        .mobile-table {
            display: block;
            width: 100%;
        }
        
        @media (min-width: 992px) {
            .mobile-table {
                display: none;
            }
        }
        
        /* Desktop Table */
        .desktop-table {
            display: none;
        }
        
        @media (min-width: 992px) {
            .desktop-table {
                display: block;
            }
        }
        
/* Mobile Row - Simplified (compact) */
        .mobile-row {
            display: grid;
            grid-template-columns: 48px 1fr auto;
            gap: 0.5rem;
            padding: 0.5rem 0.6rem;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        
        .mobile-row:first-child {
            margin-top: 0;
        }

        .mobile-row:nth-child(even) {
            background-color: rgba(184, 213, 61, 0.04);
        }
        
        .row-number {
            text-align: center;
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 0.85rem;
        }
        
        .row-main {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }
        
        .student-id {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 0.88rem;
        }
        
        .student-name {
            font-size: 0.82rem;
            color: #333;
            line-height: 1.1;
        }
        
        .row-details {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }
        
        .student-gender {
            font-size: 0.8rem;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .male-badge {
            background-color: rgba(33, 150, 243, 0.1);
            color: #1976d2;
        }
        
        .female-badge {
            background-color: rgba(233, 30, 99, 0.1);
            color: #c2185b;
        }
        
        .student-time {
            font-size: 0.75rem;
            color: #666;
        }
        
        /* Desktop Table Styles - Compact & Sticky Header */
        .desktop-table th {
            background-color: var(--primary-medium);
            color: white;
            padding: 0.5rem 0.6rem;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            position: sticky;
            top: var(--header-height);
            z-index: 10;
        }
        
        .desktop-table td {
            padding: 0.45rem 0.6rem;
            vertical-align: middle;
            border-top: 1px solid #eee;
            font-size: 0.85rem;
        }
        
        .desktop-table tbody tr:hover {
            background-color: rgba(105, 180, 30, 0.08);
        }
        
        .desktop-table tbody tr:nth-child(even) {
            background-color: rgba(184, 213, 61, 0.03);
        }
        
        /* Table Summary */
        .table-summary {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }
        
        /* No Data */
        .no-data {
            text-align: center;
            padding: 3rem 1rem;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            color: #666;
            padding: 1.5rem 1rem;
            font-size: 0.8rem;
            margin-top: 1rem;
        }
        
        /* Loading Animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            :root { --header-height: 160px; }
            body {
                padding-top: var(--header-height); /* More space for mobile header */
            }
            
            .header-title {
                font-size: 1rem;
            }
            
            .event-title {
                font-size: 0.8rem;
            }
            
            .mobile-row {
                grid-template-columns: 40px 1fr auto;
                padding: 0.75rem;
                gap: 0.5rem;
            }
            
            .student-id {
                font-size: 0.9rem;
            }
            
            .student-name {
                font-size: 0.85rem;
            }
            
            /* Adjust table header position for mobile */
            .desktop-table th {
                top: var(--header-height); /* Adjust for mobile header height */
            }
        }
        
        @media (min-width: 768px) and (max-width: 991px) {
            :root { --header-height: 145px; }
            body {
                padding-top: var(--header-height);
            }
            
            .desktop-table th {
                top: var(--header-height);
            }
        }
        
        @media (min-width: 992px) {
            :root { --header-height: 140px; }
            body {
                padding-top: var(--header-height);
            }
            
            .fixed-header {
                padding: .2rem;
            }
            
            .header-container {
                padding: 1.5rem;
                border-radius: 10px 10px 0 0;
            }
            
            .stats-bar {
                border-radius: 0;
            }
            
            .table-container {
                margin: calc(var(--header-height) + 1.5rem) 1rem 1rem 1rem;
            }
            
            .desktop-table th {
                top: 0px; /* Adjust for desktop header height */
            }
        }
        
        /* Make sure the first row is visible (use header height variable) */
        .table-responsive {
            max-height: calc(100vh - var(--header-height) - 100px);
            overflow-y: auto;
        }
        
        /* Ensure first row has proper spacing */
        .desktop-table tbody tr:first-child td {
            padding-top: 0.75rem;
        }
        
        .mobile-row:first-child {
            padding-top: 1rem;
        }
    </style>

</head>
<body>
    <!-- Fixed Header -->
    <div class="fixed-header">
        <!-- Header -->
        <div class="header-container">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="header-title">Attendance View</div>
                    <div class="event-title d-flex align-items-center">
                        <span class="me-2"><?= htmlspecialchars($event_name) ?></span>
                        <span class="status-badge <?= $event_status == 'Active' ? 'status-active' : 'status-inactive' ?>">
                            <?= $event_status ?>
                        </span>
                    </div>
                    <?php if ($event_date): ?>
                        <div class="small mt-1">
                            <i class="bi bi-calendar me-1"></i>
                            <?= date('M j, Y', strtotime($event_date)) ?>
                            <?php if ($start_time && $end_time): ?>
                                • <i class="bi bi-clock ms-2 me-1"></i>
                                <?= date('g:i A', strtotime($start_time)) ?> - <?= date('g:i A', strtotime($end_time)) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-end">
                    <div class="h4 fw-bold mb-0"><?= $attendance_count ?></div>
                    <div class="small opacity-90">Attendees</div>
                </div>
            </div>
        </div>


        <!-- Search Bar -->
        <div class="search-bar">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search by name or ID..." id="searchInput">
                <button class="btn btn-primary-custom" type="button" onclick="searchTable()">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <!-- Filters -->
            <div class="filters-bar mt-2">
                <div class="row g-2 align-items-center">
                    <div class="col-4 text-start">
                        <select id="filterProgram" class="form-select form-select-sm">
                            <option value="">All Programs</option>
                            <?php foreach (array_keys($unique_programs) as $prog): ?>
                                <option value="<?= htmlspecialchars($prog) ?>"><?= htmlspecialchars($prog) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4 text-center">
                        <select id="filterCourse" class="form-select form-select-sm">
                            <option value="">All Courses</option>
                            <?php foreach (array_keys($unique_courses) as $course): ?>
                                <option value="<?= htmlspecialchars($course) ?>"><?= htmlspecialchars($course) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4 text-end">
                        <select id="filterGender" class="form-select form-select-sm">
                            <option value="">All Genders</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-bar">
            <button class="btn btn-primary-custom action-btn" onclick="copyShareLink()">
                <i class="bi bi-link"></i> Copy Link
            </button>
            <button class="btn btn-outline-custom action-btn" onclick="toggleQR()">
                <i class="bi bi-qr-code"></i> QR Code
            </button>
            <button class="btn btn-outline-custom action-btn" onclick="refreshPage()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <!-- Share Link (Hidden by default) -->
        <div class="share-link" id="shareLinkContainer">
            <div class="input-group">
                <input type="text" class="form-control" id="shareLinkInput" value="<?= htmlspecialchars($current_url) ?>" readonly>
                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                    <i class="bi bi-clipboard"></i>
                </button>
            </div>
        </div>

        <!-- QR Code (Hidden by default) -->
        <div class="qr-container" id="qrContainer">
            <img src="<?= $qr_code_url ?>" alt="QR Code" class="qr-code img-fluid">
            <p class="mt-2 mb-0 text-muted small">Scan to view on mobile</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-0">
        <!-- Table Container -->
        <div class="table-container">
            <!-- Table Summary -->
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Attendance List
                    <span class="badge bg-primary ms-2"><?= $attendance_count ?> records</span>
                </h6>
                <div class="small text-muted d-none d-md-block">
                    Last updated: <?= date('g:i A') ?>
                </div>
            </div>

            <!-- Mobile View (Simplified) -->
            <div class="mobile-table">
                <?php if (empty($attendance_data)): ?>
                    <div class="no-data">
                        <i class="bi bi-people display-4 text-muted mb-3"></i>
                        <h5>No Attendance Records</h5>
                        <p class="text-muted">No students have checked in yet.</p>
                    </div>
                <?php else: ?>
                    <div id="mobileData">
                    <?php foreach ($attendance_data as $index => $row): ?>
                        <?php
                            // Build full name (simplified)
                            $fullName = htmlspecialchars($row['last_name'] . ', ' . $row['first_name']);
                            if (!empty($row['middle_name'])) {
                                $fullName .= ' ' . strtoupper($row['middle_name'][0]) . '.';
                            }
                            if (!empty($row['suffix'])) {
                                $fullName .= ' ' . htmlspecialchars($row['suffix']);
                            }
                            
                            $scannedTime = date("g:i A", strtotime($row['scanned_at']));
                        ?>
                        <div class="mobile-row fade-in" data-program="<?= htmlspecialchars($row['program'] ?? '') ?>" data-course="<?= htmlspecialchars($row['course'] ?? '') ?>" data-sex="<?= strtolower(htmlspecialchars($row['sex'] ?? '')) ?>" style="animation-delay: <?= $index * 0.03 ?>s">
                            <div class="row-number"><?= $index + 1 ?></div>
                            <div class="row-main">
                                <div class="student-id"><?= htmlspecialchars($row['student_id']) ?></div>
                                <div class="student-name"><?= $fullName ?></div>
                            </div>
                            <div class="row-details">
                                <span class="student-gender <?= strtolower($row['sex']) == 'male' ? 'male-badge' : 'female-badge' ?>">
                                    <?= htmlspecialchars($row['sex'][0]) ?>
                                </span>
                                <div class="student-time"><?= $scannedTime ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Desktop View -->
            <div class="desktop-table">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="attendanceTable">
                        <thead>
                            <tr>
                                <th width="60" class="text-center">#</th>
                                <th width="120">Student ID</th>
                                <th>Full Name</th>
                                <th width="100" class="text-center">Gender</th>
                                <th width="150">Program</th>
                                <th width="150">Course</th>
                                <th width="120" class="text-center">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendance_data)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-people display-4 text-muted mb-3"></i>
                                        <h5>No Attendance Records</h5>
                                        <p class="text-muted">No students have checked in yet.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($attendance_data as $index => $row): ?>
                                    <?php
                                        // Build full name
                                        $fullName = htmlspecialchars($row['last_name'] . ', ' . $row['first_name']);
                                        if (!empty($row['middle_name'])) {
                                            $fullName .= ' ' . strtoupper($row['middle_name'][0]) . '.';
                                        }
                                        if (!empty($row['suffix'])) {
                                            $fullName .= ' ' . htmlspecialchars($row['suffix']);
                                        }
                                        
                                        $scannedTime = date("g:i A", strtotime($row['scanned_at']));
                                    ?>
                                    <tr class="fade-in" data-program="<?= htmlspecialchars($row['program'] ?? '') ?>" data-course="<?= htmlspecialchars($row['course'] ?? '') ?>" data-sex="<?= strtolower(htmlspecialchars($row['sex'] ?? '')) ?>" style="animation-delay: <?= $index * 0.03 ?>s">
                                        <td class="text-center fw-bold"><?= $index + 1 ?></td>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($row['student_id']) ?></td>
                                        <td><?= $fullName ?></td>
                                        <td class="text-center">
                                            <span class="student-gender <?= strtolower($row['sex']) == 'male' ? 'male-badge' : 'female-badge' ?>">
                                                <?= htmlspecialchars($row['sex']) ?>
                                            </span>
                                        </td>
                                        <td class="small"><?= htmlspecialchars($row['program']) ?></td>
                                        <td class="small"><?= htmlspecialchars($row['course']) ?></td>
                                        <td class="text-center"><?= $scannedTime ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                <div class="small text-muted">
                    Showing <?= $attendance_count ?> record<?= $attendance_count != 1 ? 's' : '' ?>
                </div>
                <button class="btn btn-sm btn-outline-secondary" onclick="scrollToTop()">
                    <i class="bi bi-arrow-up"></i> Back to Top
                </button>
            </div>
        </div>

        <!-- Page Footer -->
        <div class="footer">
            <div class="mb-2">
                <span class="badge bg-light text-dark me-2">
                    <i class="bi bi-clock-history me-1"></i>
                    Generated: <?= date('M j, Y g:i A') ?>
                </span>
                <span class="badge bg-light text-dark">
                    <i class="bi bi-phone me-1"></i>
                    Squared Attendance
                </span>
            </div>
            <small>Auto-refresh every 2 minutes • Last sync: <span id="lastSync">Just now</span></small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateLastSync();
            setInterval(updateLastSync, 60000);
            
            // Auto-refresh every 2 minutes
            setInterval(() => {
                if (!document.hidden) {
                    refreshPage();
                }
            }, 120000);
            
            // Initialize search and filters
            document.getElementById('searchInput').addEventListener('input', searchTable);
            const fp = document.getElementById('filterProgram');
            const fc = document.getElementById('filterCourse');
            const fg = document.getElementById('filterGender');
            const clearBtn = document.getElementById('clearFilters');
            if (fp) fp.addEventListener('change', searchTable);
            if (fc) fc.addEventListener('change', searchTable);
            if (fg) fg.addEventListener('change', searchTable);
            if (clearBtn) clearBtn.addEventListener('click', () => {
                if (fp) fp.value = '';
                if (fc) fc.value = '';
                if (fg) fg.value = '';
                document.getElementById('searchInput').value = '';
                searchTable();
            });
        });
        
        // Update last sync time
        function updateLastSync() {
            const now = new Date();
            document.getElementById('lastSync').textContent = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        
        // Show share link
        function copyShareLink() {
            const shareLinkContainer = document.getElementById('shareLinkContainer');
            shareLinkContainer.classList.add('active');
            
            // Scroll to show the link
            setTimeout(() => {
                shareLinkContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
            
            // Auto-copy after showing
            setTimeout(copyToClipboard, 300);
        }
        
        // Copy to clipboard
        function copyToClipboard() {
            const shareLinkInput = document.getElementById('shareLinkInput');
            shareLinkInput.select();
            shareLinkInput.setSelectionRange(0, 99999);
            
            navigator.clipboard.writeText(shareLinkInput.value).then(() => {
                showToast('Link copied to clipboard!', 'success');
            }).catch(err => {
                document.execCommand('copy');
                showToast('Link copied to clipboard!', 'info');
            });
        }
        
        // Toggle QR code
        function toggleQR() {
            const qrContainer = document.getElementById('qrContainer');
            const shareLinkContainer = document.getElementById('shareLinkContainer');
            
            // Toggle QR
            qrContainer.classList.toggle('active');
            // Hide share link if showing
            shareLinkContainer.classList.remove('active');
            
            if (qrContainer.classList.contains('active')) {
                // Scroll to show QR
                setTimeout(() => {
                    qrContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
                showToast('QR code displayed. Scan with your camera.', 'info');
            }
        }
        
        // Search functionality + filters
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase().trim();
            const program = (document.getElementById('filterProgram')?.value || '').toLowerCase().trim();
            const course = (document.getElementById('filterCourse')?.value || '').toLowerCase().trim();
            const gender = (document.getElementById('filterGender')?.value || '').toLowerCase().trim();
            
            // Mobile search + filters
            let mobileMatches = 0;
            document.querySelectorAll('.mobile-row').forEach(row => {
                const text = row.textContent.toLowerCase();
                const rprog = (row.dataset.program || '').toLowerCase();
                const rcourse = (row.dataset.course || '').toLowerCase();
                const rsex = (row.dataset.sex || '').toLowerCase();

                const matchesText = !filter || text.includes(filter);
                const matchesProg = !program || rprog === program;
                const matchesCourse = !course || rcourse === course;
                const matchesGender = !gender || rsex === gender;

                if (matchesText && matchesProg && matchesCourse && matchesGender) {
                    row.style.display = 'grid';
                    mobileMatches++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Desktop search + filters
            let desktopMatches = 0;
            document.querySelectorAll('#attendanceTable tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                const rprog = (row.dataset.program || '').toLowerCase();
                const rcourse = (row.dataset.course || '').toLowerCase();
                const rsex = (row.dataset.sex || '').toLowerCase();

                const matchesText = !filter || text.includes(filter);
                const matchesProg = !program || rprog === program;
                const matchesCourse = !course || rcourse === course;
                const matchesGender = !gender || rsex === gender;

                if (matchesText && matchesProg && matchesCourse && matchesGender) {
                    row.style.display = '';
                    desktopMatches++;
                } else {
                    row.style.display = 'none';
                }
            });

            const matches = window.innerWidth < 992 ? mobileMatches : desktopMatches;
            if ((filter || program || course || gender) && matches === 0) {
                showToast('No matching records found', 'warning');
            } else if (filter || program || course || gender) {
                showToast(`Found ${matches} matching record${matches !== 1 ? 's' : ''}`, 'info');
            }
        }
        
        // Refresh page
        function refreshPage() {
            document.body.classList.add('loading');
            showToast('Refreshing attendance data...', 'info');
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
        
        // Scroll to top
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Toast notification
        function showToast(message, type = 'info') {
            // Remove existing toasts
            const existing = document.querySelectorAll('.custom-toast');
            existing.forEach(toast => toast.remove());
            
            const toast = document.createElement('div');
            toast.className = `custom-toast position-fixed ${type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning' : 'bg-info'} text-white px-3 py-2 rounded shadow`;
            toast.style.cssText = `
                bottom: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 300px;
                animation: slideIn 0.3s ease;
                font-size: 0.85rem;
            `;
            
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.getElementById('searchInput');
                searchInput.focus();
                searchInput.select();
            }
            
            // Ctrl/Cmd + L to copy link
            if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
                e.preventDefault();
                copyShareLink();
            }
            
            // Ctrl/Cmd + R to refresh
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                refreshPage();
            }
            
            // Escape to clear search
            if (e.key === 'Escape') {
                const searchInput = document.getElementById('searchInput');
                searchInput.value = '';
                searchTable();
                searchInput.blur();
            }
        });
        
        // Handle visibility change
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                updateLastSync();
            }
        });
    </script>
</body>
</html>