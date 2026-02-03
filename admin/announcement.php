<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
require 'php/config.php';

// Set charset to UTF-8 immediately after connection
$conn->set_charset("utf8mb4");

// Fetch announcements from the database
$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Admin - Announcements</title>
    <link rel="icon" type="image/png" href="../images/Squared_Logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --color-primary: #0C5A11;
            --color-secondary: #69B41E;
            --color-accent: #69B41E;
        }
        
        .page-item.active .page-link {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
            color: white;
        }
        
        .page-link {
            color: var(--color-primary);
        }
        
        .page-link:hover {
            color: var(--color-secondary);
        }
        
        /* Compact card layout */
        .announcement-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
            overflow: hidden;
            position: relative;
        }
        
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .announcement-main {
            padding: 1.5rem;
            cursor: pointer;
            position: relative;
        }
        
        .announcement-main:hover {
            background-color: #f8f9fa;
        }
        
        .announcement-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .announcement-content {
            flex: 1;
        }
        
        .announcement-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--color-primary);
            font-size: 1.25rem;
            line-height: 1.3;
        }
        
        .announcement-preview {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .announcement-badges {
            margin-top: 0.75rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .announcement-details {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .announcement-details.active {
            padding: 1.5rem;
            max-height: 500px;
        }
        
        .announcement-full-content {
            font-size: 1rem;
            line-height: 1.6;
            color: #495057;
            white-space: pre-line;
        }
        
        .announcement-actions {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }
        
        /* Grid layout for cards */
        .announcements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        
        /* Toggle icon */
        .toggle-icon {
            transition: transform 0.3s ease;
            color: var(--color-primary);
            font-size: 1.2rem;
        }
        
        .toggle-icon.rotated {
            transform: rotate(180deg);
        }
        
        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
            color: #6c757d;
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }
        
        /* Time badge styling */
        .time-badge {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        /* FIXED: Correct z-index hierarchy */
        #sidebar {
            z-index: 1035 !important; /* Higher than modal backdrop */
        }

        .modal-backdrop {
            z-index: 1030 !important; /* Lower than sidebar */
        }

        .modal {
            z-index: 1040 !important; /* Higher than sidebar */
        }

        .navbar {
            z-index: 1030 !important;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .announcements-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .announcement-main {
                padding: 1rem;
                flex-direction: column;
                text-align: center;
            }
            
            .announcement-icon {
                margin-right: 0;
                margin-bottom: 1rem;
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .announcement-title {
                font-size: 1.1rem;
            }
            
            .announcement-preview {
                font-size: 0.9rem;
            }
            
            .announcement-details {
                padding: 0 1rem;
            }
            
            .announcement-details.active {
                padding: 1rem;
            }
            
            .announcement-actions {
                padding: 0.75rem 1rem;
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .announcement-actions .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 2px;
            }
            
            .card-body.py-3 {
                padding: 0.75rem !important;
            }
            
            .nu {
                font-size: 1.5rem !important;
            }
            
            /* Statistics cards on mobile */
            .row.mb-4 .col-md-3 {
                margin-bottom: 1rem;
            }

            .row {
                justify-content: space-evenly;
            }
            
            /* Ensure sidebar overlay works properly on mobile */
            .sidebar-overlay {
                z-index: 1025 !important;
            }
        }
        
        @media (max-width: 576px) {
            .announcement-main {
                flex-direction: column;
                text-align: center;
            }
            
            .announcement-icon {
                margin: 0 auto 1rem auto;
            }
        }
        
        /* Custom scrollbar for announcement content */
        .announcement-full-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .announcement-full-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .announcement-full-content::-webkit-scrollbar-thumb {
            background: var(--color-secondary);
            border-radius: 10px;
        }
        
        .announcement-full-content::-webkit-scrollbar-thumb:hover {
            background: var(--color-primary);
        }
    </style>
</head>

<body>
    <!-- Include Navigation -->
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div id="content">
        <div class="container-fluid">
            <h2 class="tt">Announcements</h2>

            <!-- Add Announcement Button -->
            <div class="row mb-4">
                <div class="col-12">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                        <i class="bi bi-megaphone"></i> Add Announcement
                    </button>
                </div>
            </div>

            <!-- Announcement Statistics -->
            <div class="row mb-4">
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg1 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Total Announcements</h5>
                            <h3 class="nu mb-0"><?= $result->num_rows ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg2 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">This Month</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $current_month = date('Y-m');
                                    $month_count = 0;
                                    $result->data_seek(0);
                                    while ($row = $result->fetch_assoc()) {
                                        if (date('Y-m', strtotime($row['created_at'])) == $current_month) {
                                            $month_count++;
                                        }
                                    }
                                    $result->data_seek(0);
                                    echo $month_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg3 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">This Week</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $current_week = date('Y-W');
                                    $week_count = 0;
                                    $result->data_seek(0);
                                    while ($row = $result->fetch_assoc()) {
                                        if (date('Y-W', strtotime($row['created_at'])) == $current_week) {
                                            $week_count++;
                                        }
                                    }
                                    $result->data_seek(0);
                                    echo $week_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements Grid -->
            <?php if ($result->num_rows > 0): ?>
                <div class="announcements-grid">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="announcement-card" id="card-<?= htmlspecialchars($row['id']) ?>">
                        <!-- Main Card Content -->
                        <div class="announcement-main d-flex align-items-start" onclick="toggleDetails('<?= htmlspecialchars($row['id']) ?>')">
                            <div class="announcement-icon">
                                <i class="bi bi-megaphone"></i>
                            </div>
                            <div class="announcement-content">
                                <div class="announcement-title">
                                    <?= htmlspecialchars($row['title']) ?>
                                </div>
                                <div class="announcement-preview">
                                    <?= htmlspecialchars(substr($row['content'], 0, 150)) ?><?= strlen($row['content']) > 150 ? '...' : '' ?>
                                </div>
                                <div class="announcement-badges">
                                    <span class="badge time-badge me-2">
                                        <i class="bi bi-clock"></i> 
                                        <?= date("M j, Y", strtotime($row['created_at'])) ?>
                                    </span>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-chat"></i> 
                                        <?= ceil(str_word_count($row['content']) / 10) ?> min read
                                    </span>
                                </div>
                            </div>
                            <div class="toggle-icon">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                        
                        <!-- Collapsible Details -->
                        <div class="announcement-details" id="details-<?= htmlspecialchars($row['id']) ?>">
                            <div class="announcement-full-content">
                                <?= nl2br(htmlspecialchars($row['content'])) ?>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> 
                                    Posted on: <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="announcement-actions">
                            <div class="btn-group btn-group-sm">
                                <!-- Delete Button -->
                                <button class="btn btn-outline-danger delete-announcement-btn" 
                                        data-announcement-id="<?= $row['id'] ?>" 
                                        data-announcement-title="<?= htmlspecialchars($row['title']) ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-megaphone"></i>
                    <h4>No Announcements Yet</h4>
                    <p>Create your first announcement to keep students informed.</p>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                        <i class="bi bi-plus-lg"></i> Create First Announcement
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Announcement Modal -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-megaphone"></i> Add Announcement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAnnouncementForm" action="php/addnotify.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter announcement title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Content</label>
                            <textarea name="content" class="form-control" rows="8" placeholder="Enter announcement content..." required></textarea>
                            <div class="form-text">
                                <span id="charCount">0</span> characters
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="bi bi-send"></i> Post Announcement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h5 class="modal-title text-danger text-center w-100">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete this announcement?</p>
                    <p class="text-danger fw-bold fs-5" id="deleteAnnouncementTitle"></p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteAnnouncementForm" action="php/deletenotify.php" method="POST" class="d-inline">
                        <input type="hidden" name="delete_id" id="deleteAnnouncementId">
                        <button type="submit" class="btn btn-danger ms-2">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    
    <script>
        // Toggle announcement details
        function toggleDetails(announcementId) {
            const details = document.getElementById('details-' + announcementId);
            const toggleIcon = details.closest('.announcement-card').querySelector('.toggle-icon');
            
            details.classList.toggle('active');
            toggleIcon.classList.toggle('rotated');
        }

        document.addEventListener("DOMContentLoaded", function () {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            // Delete announcement functionality
            document.querySelectorAll('.delete-announcement-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const announcementId = this.getAttribute('data-announcement-id');
                    const announcementTitle = this.getAttribute('data-announcement-title');
                    
                    document.getElementById('deleteAnnouncementId').value = announcementId;
                    document.getElementById('deleteAnnouncementTitle').textContent = announcementTitle;
                    
                    deleteModal.show();
                });
            });
            
            // Character count for content textarea
            const contentTextarea = document.querySelector('textarea[name="content"]');
            const charCount = document.getElementById('charCount');
            
            if (contentTextarea && charCount) {
                contentTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
            
            // Form validation
            document.getElementById('addAnnouncementForm').addEventListener('submit', function(e) {
                const title = this.querySelector('input[name="title"]').value.trim();
                const content = this.querySelector('textarea[name="content"]').value.trim();
                
                if (title.length < 3) {
                    e.preventDefault();
                    alert('Title must be at least 3 characters long.');
                    return;
                }
                
                if (content.length < 10) {
                    e.preventDefault();
                    alert('Content must be at least 10 characters long.');
                    return;
                }
            });
            
            // Auto-focus on title input when modal opens
            document.getElementById('addAnnouncementModal').addEventListener('shown.bs.modal', function () {
                const titleInput = this.querySelector('input[name="title"]');
                if (titleInput) {
                    titleInput.focus();
                }
            });
        });
    </script>
</body>
</html>
