<?php
// php/sidebar.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!-- Sidebar Navigation -->
<div id="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="students.php" class="nav-link text-white">
                <i class="bi bi-people"></i> Students
            </a>
        </li>
        <li class="nav-item">
            <a href="scanner.php" class="nav-link text-white">
                <i class="bi bi-qr-code-scan"></i> Auth to Scan
            </a>
        </li>
        <li class="nav-item">
            <a href="event.php" class="nav-link text-white">
                <i class="bi bi-calendar-event"></i> Events
            </a>
        </li>
        <li class="nav-item">
            <a href="announcement.php" class="nav-link text-white">
                <i class="bi bi-megaphone"></i> Announcements
            </a>
        </li>
        <li class="nav-item">
            <a href="nav_voting.php" class="nav-link text-white">
                <i class="bi bi-hand-thumbs-up"></i> Voting
            </a>
        </li>
        <li class="nav-item">
            <a href="php/logout.php" class="nav-link text-white">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>