<?php
// sidebar.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Navigation -->
<div id="sidebar" class="collapsed">
    <div class="sidebar-header">
        <img src="../images/Squared_Logo.png" alt="Squared Logo" class="sidebar-logo">
        <span class="sidebar-title">Squared Admin</span>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-house-door"></i> 
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="students.php" class="nav-link text-white <?= $current_page == 'students.php' ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span class="nav-text">Students</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="scanner.php" class="nav-link text-white <?= $current_page == 'scanner.php' ? 'active' : '' ?>">
                <i class="bi bi-qr-code-scan"></i>
                <span class="nav-text">Auth to Scan</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="event.php" class="nav-link text-white <?= $current_page == 'event.php' ? 'active' : '' ?>">
                <i class="bi bi-calendar-event"></i>
                <span class="nav-text">Events</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="announcement.php" class="nav-link text-white <?= $current_page == 'announcement.php' ? 'active' : '' ?>">
                <i class="bi bi-megaphone"></i>
                <span class="nav-text">Announcements</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="voting.php" class="nav-link text-white <?= $current_page == 'voting.php' ? 'active' : '' ?>">
                <i class="bi bi-hand-thumbs-up"></i>
                <span class="nav-text">Voting</span>
            </a>
        </li>
                <li class="nav-item">
            <a href="slider.php" class="nav-link text-white <?= $current_page == 'slider.php' ? 'active' : '' ?>">
                <i class="bi bi-images"></i>
                <span class="nav-text">Slider</span>
            </a>
        </li>
        <li class="nav-item logout-item">
            <a href="php/logout.php" class="nav-link text-white">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-text">Logout</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <button class="btn btn-sm btn-outline-light sidebar-toggle">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
</div>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>