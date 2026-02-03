<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
require 'php/config.php';

// Fetch all scanners with full name (including middle name and suffix)
$query = "SELECT es.id, es.student_id, 
                 s.first_name, 
                 s.middle_name, 
                 s.last_name, 
                 s.suffix, 
                 s.avatar,
                 s.program,
                 s.course,
                 s.sex,
                 es.status 
          FROM event_scanners es
          JOIN students s ON es.student_id = s.student_id
          ORDER BY es.status DESC, s.last_name ASC";
$result = $conn->query($query);

// Store results in array to avoid re-fetching
$scanners = [];
while ($row = $result->fetch_assoc()) {
    $scanners[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Admin - Scanner Authorization</title>
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
        
        .scanner-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        /* Compact card layout */
        .scanner-card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .scanner-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .scanner-main {
            display: flex;
            align-items: center;
            padding: 1rem;
            cursor: pointer;
        }
        
        .scanner-main:hover {
            background-color: #f8f9fa;
        }
        
        .scanner-info {
            flex: 1;
            margin-left: 1rem;
        }
        
        .scanner-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .scanner-id {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .scanner-badges {
            margin-top: 0.5rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .scanner-details {
            padding: 0 1rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .scanner-details.active {
            padding: 1rem;
            max-height: 500px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.75rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-size: 0.9rem;
        }
        
        .scanner-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0.75rem 1rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }
        
        /* Grid layout for cards */
        .scanners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1rem;
        }
        
        /* Toggle icon */
        .toggle-icon {
            transition: transform 0.3s ease;
        }
        
        .toggle-icon.rotated {
            transform: rotate(180deg);
        }
        
        /* Added custom pink background for female badge */
        .bg-pink {
            background-color: #e83e8c !important;
            color: white !important;
        }
        
        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        /* Scanner input group styling */
        .scanner-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .scanner-input-group .form-control {
            flex: 1;
        }
        
        /* Status badge styling */
        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .scanners-grid {
                grid-template-columns: 1fr;
            }
            
            .scanner-main {
                padding: 0.75rem;
            }
            
            .scanner-info {
                margin-left: 0.75rem;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .scanner-actions {
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .scanner-input-group {
                flex-direction: column;
            }
            
            .scanner-input-group .btn {
                width: 100%;
            }
            
            .card-body.py-3 {
                padding: 0.75rem !important;
            }

            .row {
                justify-content: space-evenly;
            }
            
            .nu {
                font-size: 1.5rem !important;
            }
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
            <h2 class="tt">Authorize to Scan QR</h2>

            <!-- Add Scanner Button -->
            <div class="row mb-4">
                <div class="col-12">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addScanner">
                        <i class="bi bi-plus-lg"></i> Add Authorized Student
                    </button>
                </div>
            </div>

            <!-- Scanner Statistics -->
            <div class="row mb-4">
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg1 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Total Scanners</h5>
                            <h3 class="nu mb-0"><?= count($scanners) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg2 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Allowed</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $allowed_count = 0;
                                    foreach ($scanners as $scanner) {
                                        if ($scanner['status'] == 'Allow') $allowed_count++;
                                    }
                                    echo $allowed_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg3 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Denied</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $denied_count = 0;
                                    foreach ($scanners as $scanner) {
                                        if ($scanner['status'] == 'Deny') $denied_count++;
                                    }
                                    echo $denied_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scanner Cards -->
            <?php if (count($scanners) > 0): ?>
                <div class="scanners-grid">
                    <?php foreach ($scanners as $scanner): ?>
                    <?php 
                        $full_name = $scanner['first_name'];
                        if (!empty($scanner['middle_name'])) {
                            $full_name .= ' ' . $scanner['middle_name'];
                        }
                        $full_name .= ' ' . $scanner['last_name'];
                        if (!empty($scanner['suffix'])) {
                            $full_name .= ' ' . $scanner['suffix'];
                        }
                    ?>
                    <div class="scanner-card" id="card-<?= htmlspecialchars($scanner['id']) ?>">
                        <!-- Main Card Content -->
                        <div class="scanner-main" onclick="toggleDetails('<?= htmlspecialchars($scanner['id']) ?>')">
                            <img src="../avatars/<?= htmlspecialchars($scanner['avatar']) ?>.png" 
                                 alt="Avatar" 
                                 class="scanner-avatar"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJDMTMuMTgyIDIgMTQuMjM1MiAyLjIzNTIgMTUuMDg3OSAyLjYzNUMxNS45NDc5IDMuMDM1MiAxNi42NTUzIDMuNjE0OCAxNy4yMTEgNC4zNzE1QzE3Ljc2NjcgNS4xMjgyIDE4LjA5MTggNS45ODg4IDE4LjE4NjUgNi45NTAyQzE4LjI4MTIgNy45MTE1IDE4LjE0NzkgOC44OTk4IDE3Ljc4NjcgOS45MTVDMTcuNDI1NSAxMC45MzA1IDE2Ljg1MzcgMTEuNzc0MiAxNi4wNzE0IDEyLjQ0OTVDMTUuMjg5MiAxMy4xMjQ4IDE0LjM0MjIgMTMuNTAyMiAxMy4yMzA3IDEzLjU4MkMxMi4xMTkxIDEzLjY2MTggMTEuMDA5IDEzLjQ4MTggOS44OTUyIDEzLjA0MkM4Ljc4MTUgMTIuNjAyMiA3Ljg1ODUgMTEuOTU1IDcuMTI2MiAxMS4wOTc3QzYuMzkzOSAxMC4yNDA0IDUuOTMxOCA5LjI0OTIgNS43Mzk5IDguMTI0QzUuNTQ4IDcuMDAwNSA1LjY1ODggNS44MzM1IDYuMDcyMyA0LjYyM0M2LjQ4NTggMy40MTI1IDcuMTE3IDIuNDU4OCA3Ljk2NTggMS43NjFDOC44MTQ3IDEuMDYzOCA5LjgyMjcgMC42NjU1IDExIDAuNjY1NVoiIGZpbGw9IiM2OUI0MUUiLz4KPC9zdmc+'">
                            <div class="scanner-info">
                                <div class="scanner-name">
                                    <?= htmlspecialchars($full_name) ?>
                                </div>
                                <div class="scanner-id"><?= htmlspecialchars($scanner['student_id']) ?></div>
                                <div class="scanner-badges">
                                    <span class="badge <?= $scanner['status'] === 'Allow' ? 'bg-success' : 'bg-danger' ?> me-1">
                                        <?= htmlspecialchars($scanner['status']) ?>
                                    </span>
                                    <span class="badge <?= $scanner['sex'] === 'Male' ? 'bg-info' : 'bg-pink' ?> me-1">
                                        <?= htmlspecialchars($scanner['sex']) ?>
                                    </span>
                                    <span class="badge bg-primary"><?= htmlspecialchars($scanner['program']) ?></span>
                                </div>
                            </div>
                            <div class="toggle-icon">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                        
                        <!-- Collapsible Details -->
                        <div class="scanner-details" id="details-<?= htmlspecialchars($scanner['id']) ?>">
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Middle Name</span>
                                    <span class="detail-value"><?= htmlspecialchars($scanner['middle_name']) ?: '-' ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Course</span>
                                    <span class="detail-value"><?= htmlspecialchars($scanner['course']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Status</span>
                                    <span class="detail-value">
                                        <span class="badge <?= $scanner['status'] === 'Allow' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= htmlspecialchars($scanner['status']) ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="scanner-actions">
                            <div class="btn-group btn-group-sm">
                                <!-- Toggle Allow/Deny -->
                                <form action="php/scannerstatus.php" method="POST" class="d-inline">
                                    <input type="hidden" name="scanner_id" value="<?= $scanner['id'] ?>">
                                    <input type="hidden" name="new_status" value="<?= ($scanner['status'] == 'Allow') ? 'Deny' : 'Allow' ?>">
                                    <button type="submit" class="btn btn-<?= ($scanner['status'] == 'Allow') ? 'warning' : 'success' ?>">
                                        <i class="bi bi-toggle-<?= ($scanner['status'] == 'Allow') ? 'off' : 'on' ?>"></i>
                                        <?= ($scanner['status'] == 'Allow') ? 'Deny' : 'Allow' ?>
                                    </button>
                                </form>

                                <!-- Delete Scanner Button -->
                                <button type="button" 
                                        class="btn btn-outline-danger delete-scanner-btn"
                                        data-scanner-id="<?= htmlspecialchars($scanner['id']) ?>"
                                        data-student-id="<?= htmlspecialchars($scanner['student_id']) ?>"
                                        data-student-name="<?= htmlspecialchars($full_name) ?>">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-qr-code-scan"></i>
                    <h4>No Authorized Scanners</h4>
                    <p>Add students who can scan QR codes for events.</p>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addScanner">
                        <i class="bi bi-plus-lg"></i> Add First Scanner
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Scanner Modal -->
    <div class="modal fade" id="addScanner" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Add Scanner Authorization
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="php/addscanner.php" method="POST" id="scannerForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Student IDs</label>
                            <p class="text-muted small">Enter Student IDs to authorize them for QR code scanning.</p>
                            
                            <div id="scanner-container">
                                <div class="scanner-input-group">
                                    <input type="text" class="form-control scanner-id" name="scanner_ids[]" 
                                           placeholder="Enter Student ID" required>
                                    <button type="button" class="btn btn-success add-scanner">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    You can add multiple Student IDs by clicking the + button
                                </small>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Save Authorizations
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Delete Confirmation Modal (Dynamic) -->
    <div class="modal fade" id="deleteScannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Removal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove scanner authorization for:</p>
                    <div class="alert alert-warning">
                        <strong id="delete-student-id"></strong><br>
                        <strong id="delete-student-name"></strong>
                    </div>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="php/deletescanner.php" method="POST" id="delete-scanner-form">
                        <input type="hidden" name="scanner_id" id="delete-scanner-id">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    
    <script>
        // Toggle scanner details
        function toggleDetails(scannerId) {
            const details = document.getElementById('details-' + scannerId);
            const toggleIcon = details.closest('.scanner-card').querySelector('.toggle-icon');
            
            details.classList.toggle('active');
            toggleIcon.classList.toggle('rotated');
        }

        // Scanner input field management
        document.addEventListener("DOMContentLoaded", function () {
            const scannerContainer = document.getElementById("scanner-container");
            
            // Add new scanner input field
            document.addEventListener("click", function(e) {
                if (e.target.classList.contains("add-scanner")) {
                    const newInputGroup = document.createElement("div");
                    newInputGroup.className = "scanner-input-group";
                    newInputGroup.innerHTML = `
                        <input type="text" class="form-control scanner-id" name="scanner_ids[]" 
                               placeholder="Enter Student ID" required>
                        <button type="button" class="btn btn-danger remove-scanner">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                    `;
                    scannerContainer.appendChild(newInputGroup);
                }
                
                // Remove scanner input field
                if (e.target.classList.contains("remove-scanner")) {
                    const inputGroups = scannerContainer.querySelectorAll(".scanner-input-group");
                    if (inputGroups.length > 1) {
                        e.target.closest(".scanner-input-group").remove();
                    } else {
                        // If it's the last input, just clear it
                        const input = scannerContainer.querySelector(".scanner-id");
                        input.value = "";
                    }
                }
            });
            
            // Form validation
            document.getElementById("scannerForm").addEventListener("submit", function(e) {
                const inputs = this.querySelectorAll(".scanner-id");
                let hasValue = false;
                
                inputs.forEach(input => {
                    if (input.value.trim() !== "") {
                        hasValue = true;
                    }
                });
                
                if (!hasValue) {
                    e.preventDefault();
                    alert("Please enter at least one Student ID.");
                }
            });
            
            // Delete scanner functionality - SINGLE MODAL APPROACH
            const deleteScannerModal = new bootstrap.Modal(document.getElementById('deleteScannerModal'));
            const deleteStudentIdElement = document.getElementById('delete-student-id');
            const deleteStudentNameElement = document.getElementById('delete-student-name');
            const deleteScannerIdElement = document.getElementById('delete-scanner-id');
            
            document.querySelectorAll('.delete-scanner-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const scannerId = this.getAttribute('data-scanner-id');
                    const studentId = this.getAttribute('data-student-id');
                    const studentName = this.getAttribute('data-student-name');
                    
                    // Update modal content
                    deleteStudentIdElement.textContent = studentId;
                    deleteStudentNameElement.textContent = studentName;
                    deleteScannerIdElement.value = scannerId;
                    
                    // Show modal
                    deleteScannerModal.show();
                });
            });
            
            // Handle form submission with AJAX for better UX
            document.getElementById('delete-scanner-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('php/deletescanner.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    if (result.includes('success') || result.trim() === '') {
                        // Close modal
                        deleteScannerModal.hide();
                        
                        // Reload page to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        alert('Error deleting scanner: ' + result);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            });
        });
        
        // Auto-focus on first input when modal opens
        document.getElementById('addScanner').addEventListener('shown.bs.modal', function () {
            const firstInput = this.querySelector('.scanner-id');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>
</body>
</html>