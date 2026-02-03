<?php
// navbar.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!-- Top Navigation Bar -->
<nav class="navbar navbar-dark">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand">Squared Admin</span>
        
        <div class="d-flex align-items-center">
            <div class="user-info text-white me-3 d-none d-md-block">
                <small>Welcome, Admin</small>
            </div>
            <div class="mobile-logout d-md-none">
                <a href="php/logout.php" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</nav>