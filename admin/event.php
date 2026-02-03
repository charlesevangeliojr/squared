<?php
session_start();

// 🔒 Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

require 'php/config.php';

// Set timezone to Hong Kong/Singapore
date_default_timezone_set('Asia/Singapore');

// Get current time
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

// Auto update event status based on current time
$update_sql = "UPDATE events 
               SET status = CASE 
                    WHEN event_date = ? AND start_time <= ? AND end_time >= ? THEN 'Active'
                    ELSE 'Inactive'
                END";

$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("sss", $currentDate, $currentTime, $currentTime);
$update_stmt->execute();
$update_stmt->close();

// Fetch events from the database including the evaluation link
$query = "SELECT event_id, event_name, event_date, start_time, end_time, status, evaluation_link 
          FROM events 
          ORDER BY event_date DESC, start_time DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Admin - Events</title>
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
        .event-card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .event-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .event-main {
            display: flex;
            align-items: center;
            padding: 1rem;
            cursor: pointer;
        }
        
        .event-main:hover {
            background-color: #f8f9fa;
        }
        
        .event-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .event-info {
            flex: 1;
            margin-left: 1rem;
        }
        
        .event-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }
        
        .event-date {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .event-badges {
            margin-top: 0.5rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .event-details {
            padding: 0 1rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .event-details.active {
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
        
        .event-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0.75rem 1rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }
        
        /* Grid layout for cards */
        .events-grid {
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
        
        /* Status badge styling */
        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
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
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .event-main {
                padding: 0.75rem;
            }
            
            .event-info {
                margin-left: 0.75rem;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .event-actions {
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .event-actions .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 2px;
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
            
            .event-name {
                font-size: 1rem;
            }
            
            /* Ensure sidebar overlay works properly on mobile */
            .sidebar-overlay {
                z-index: 1025 !important;
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
            <h2 class="tt">Event Management</h2>

            <!-- Add Event Button -->
            <div class="row mb-4">
                <div class="col-12">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <i class="bi bi-plus-lg"></i> Add New Event
                    </button>
                </div>
            </div>

            <!-- Event Statistics -->
            <div class="row mb-4">
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg1 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Total Events</h5>
                            <h3 class="nu mb-0"><?= $result->num_rows ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg2 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Active</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $active_count = 0;
                                    $result->data_seek(0);
                                    while ($row = $result->fetch_assoc()) {
                                        if ($row['status'] == 'Active') $active_count++;
                                    }
                                    $result->data_seek(0);
                                    echo $active_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg3 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Inactive</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $inactive_count = 0;
                                    $result->data_seek(0);
                                    while ($row = $result->fetch_assoc()) {
                                        if ($row['status'] == 'Inactive') $inactive_count++;
                                    }
                                    $result->data_seek(0);
                                    echo $inactive_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Cards -->
            <?php if ($result->num_rows > 0): ?>
                <div class="events-grid">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="event-card" id="card-<?= htmlspecialchars($row['event_id']) ?>">
                        <!-- Main Card Content -->
                        <div class="event-main" onclick="toggleDetails('<?= htmlspecialchars($row['event_id']) ?>')">
                            <div class="event-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="event-info">
                                <div class="event-name">
                                    <?= htmlspecialchars($row['event_name']) ?>
                                </div>
                                <div class="event-date">
                                    <?= htmlspecialchars($row['event_date']) ?>
                                </div>
                                <div class="event-badges">
                                    <span class="badge status-badge <?= ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                    <span class="badge bg-primary">
                                        <?= date("g:i A", strtotime($row['start_time'])) ?> - <?= date("g:i A", strtotime($row['end_time'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="toggle-icon">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                        
                        <!-- Collapsible Details -->
                        <div class="event-details" id="details-<?= htmlspecialchars($row['event_id']) ?>">
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Event Name</span>
                                    <span class="detail-value"><?= htmlspecialchars($row['event_name']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Date</span>
                                    <span class="detail-value"><?= htmlspecialchars($row['event_date']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Start Time</span>
                                    <span class="detail-value"><?= date("g:i A", strtotime($row['start_time'])) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">End Time</span>
                                    <span class="detail-value"><?= date("g:i A", strtotime($row['end_time'])) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Status</span>
                                    <span class="detail-value">
                                        <span class="badge <?= ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </span>
                                </div>
                                <?php if (!empty($row['evaluation_link'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Evaluation Link</span>
                                    <span class="detail-value">
                                        <a href="<?= htmlspecialchars($row['evaluation_link']) ?>" target="_blank" class="text-decoration-none">
                                            <i class="bi bi-link-45deg"></i> Open Link
                                        </a>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="event-actions">
                            <div class="btn-group btn-group-sm">
                                <!-- Edit Button -->
                                <button class="btn btn-outline-warning edit-event-btn" 
                                        data-event-id="<?= $row['event_id'] ?>"
                                        data-event-name="<?= htmlspecialchars($row['event_name']) ?>"
                                        data-event-date="<?= htmlspecialchars($row['event_date']) ?>"
                                        data-start-time="<?= htmlspecialchars($row['start_time']) ?>"
                                        data-end-time="<?= htmlspecialchars($row['end_time']) ?>"
                                        data-evaluation-link="<?= htmlspecialchars($row['evaluation_link']) ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>

                                <!-- Data Actions Button -->
                                <button class="btn btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#moreActionsModal<?= $row['event_id'] ?>">
                                    <i class="bi bi-gear"></i> Actions
                                </button>

                                <!-- Delete Button -->
                                <button class="btn btn-outline-danger delete-event-btn" 
                                        data-event-id="<?= $row['event_id'] ?>"
                                        data-event-name="<?= htmlspecialchars($row['event_name']) ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-calendar-event"></i>
                    <h4>No events found</h4>
                    <p>Click "Add New Event" to create your first event.</p>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <i class="bi bi-plus-lg"></i> Add First Event
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus"></i> Add New Event
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addEventForm" action="php/addevent.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Event Name</label>
                            <input type="text" class="form-control" name="event_name" placeholder="Enter event name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Event Date</label>
                            <input type="date" class="form-control" name="event_date" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Start Time</label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">End Time</label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Evaluation Link</label>
                            <input type="url" class="form-control" name="evaluation_link" 
                                   placeholder="https://example.com/evaluation (optional)">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Save Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal (Single Dynamic Modal) -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square"></i> Edit Event
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEventForm" action="php/edit_event.php" method="POST">
                        <input type="hidden" name="event_id" id="editEventId">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Event Name</label>
                            <input type="text" class="form-control" name="event_name" id="editEventName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Event Date</label>
                            <input type="date" class="form-control" name="event_date" id="editEventDate" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Start Time</label>
                                <input type="time" class="form-control" name="start_time" id="editStartTime" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">End Time</label>
                                <input type="time" class="form-control" name="end_time" id="editEndTime" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Evaluation Link</label>
                            <input type="url" class="form-control" name="evaluation_link" id="editEvaluationLink"
                                   placeholder="https://example.com/evaluation">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal (Single Dynamic Modal) -->
    <div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h5 class="modal-title text-danger text-center w-100">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete this event?</p>
                    <p class="text-danger fw-bold fs-5" id="deleteEventName"></p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteEventForm" action="php/deleteevent.php" method="POST" class="d-inline">
                        <input type="hidden" name="event_id" id="deleteEventId">
                        <button type="submit" class="btn btn-danger ms-2">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- More Actions Modals -->
    <?php $result->data_seek(0); while ($row = $result->fetch_assoc()): ?>
    <div class="modal fade" id="moreActionsModal<?= $row['event_id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-gear"></i> Event Data - "<?= htmlspecialchars($row['event_name']) ?>"
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <a href="php/export_excel.php?event_id=<?= $row['event_id'] ?>" 
                           class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Download as Excel
                        </a>
                        <a href="php/event_view.php?event_id=<?= $row['event_id'] ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="bi bi-eye"></i> View Attendance Data
                        </a>
                        <?php if (!empty($row['evaluation_link'])): ?>
                        <a href="<?= htmlspecialchars($row['evaluation_link']) ?>" 
                           class="btn btn-info" target="_blank">
                            <i class="bi bi-link-45deg"></i> Evaluation Form
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    
    <script>
        // Toggle event details
        function toggleDetails(eventId) {
            const details = document.getElementById('details-' + eventId);
            const toggleIcon = details.closest('.event-card').querySelector('.toggle-icon');
            
            details.classList.toggle('active');
            toggleIcon.classList.toggle('rotated');
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Edit Event Modal Handler
            const editEventModal = new bootstrap.Modal(document.getElementById('editEventModal'));
            const deleteEventModal = new bootstrap.Modal(document.getElementById('deleteEventModal'));
            
            // Edit event buttons
            document.querySelectorAll('.edit-event-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('editEventId').value = this.dataset.eventId;
                    document.getElementById('editEventName').value = this.dataset.eventName;
                    document.getElementById('editEventDate').value = this.dataset.eventDate;
                    document.getElementById('editStartTime').value = this.dataset.startTime;
                    document.getElementById('editEndTime').value = this.dataset.endTime;
                    document.getElementById('editEvaluationLink').value = this.dataset.evaluationLink || '';
                    
                    editEventModal.show();
                });
            });
            
            // Delete event buttons
            document.querySelectorAll('.delete-event-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('deleteEventId').value = this.dataset.eventId;
                    document.getElementById('deleteEventName').textContent = this.dataset.eventName;
                    
                    deleteEventModal.show();
                });
            });
            
            // Form validation
            document.getElementById('addEventForm').addEventListener('submit', function(e) {
                const startTime = this.querySelector('input[name="start_time"]').value;
                const endTime = this.querySelector('input[name="end_time"]').value;
                
                if (startTime >= endTime) {
                    e.preventDefault();
                    alert('End time must be after start time.');
                }
            });
            
            document.getElementById('editEventForm').addEventListener('submit', function(e) {
                const startTime = this.querySelector('input[name="start_time"]').value;
                const endTime = this.querySelector('input[name="end_time"]').value;
                
                if (startTime >= endTime) {
                    e.preventDefault();
                    alert('End time must be after start time.');
                }
            });
        });
    </script>
</body>
</html>