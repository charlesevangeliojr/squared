<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

// 🔒 Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php'); // Redirect if not logged in
    exit();
}

require 'php/config.php'; // Database connection

$search = $_GET['search'] ?? '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 60;
$offset = ($page - 1) * $records_per_page;

// Set connection to UTF-8
$conn->set_charset("utf8mb4");

$sql = "SELECT student_id, first_name, middle_name, last_name, suffix, sex, avatar, program, course, email, created_at, qr_code 
        FROM students 
        WHERE student_id LIKE ? 
        OR first_name LIKE ? 
        OR middle_name LIKE ? 
        OR last_name LIKE ? 
        OR suffix LIKE ? 
        OR sex LIKE ? 
        OR program LIKE ? 
        OR course LIKE ? 
        OR email LIKE ? 
        OR created_at LIKE ?
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$searchParam = "%$search%";
$stmt->bind_param("ssssssssssii", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Admin - Students</title>
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
        
        .avatar-option {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .avatar-option label {
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .avatar-option label:hover {
            transform: scale(1.05);
        }
        
        .avatar-option img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid transparent;
            transition: border-color 0.2s ease;
        }
        
        .avatar-option input:checked + img {
            border-color: var(--color-accent);
        }
        
        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .qr-code {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        /* Compact card layout */
        .student-card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .student-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .student-main {
            display: flex;
            align-items: center;
            padding: 1rem;
            cursor: pointer;
        }
        
        .student-main:hover {
            background-color: #f8f9fa;
        }
        
        .student-info {
            flex: 1;
            margin-left: 1rem;
        }
        
        .student-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .student-id {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .student-badges {
            margin-top: 0.5rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .student-details {
            padding: 0 1rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .student-details.active {
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
        
        .student-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0.75rem 1rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }
        
        /* Grid layout for cards */
        .students-grid {
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
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .students-grid {
                grid-template-columns: 1fr;
            }
            
            .student-main {
                padding: 0.75rem;
            }
            
            .student-info {
                margin-left: 0.75rem;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .student-actions {
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
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
            <h2 class="tt">Student Records</h2>

            <!-- Search Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" placeholder="Search Students..." class="form-control me-2" 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <?php if ($search): ?>
                        <a href="students.php" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Student Count -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Showing <?= $result->num_rows ?> student(s) 
                                <?php if ($search): ?>matching "<?= htmlspecialchars($search) ?>"<?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Cards -->
            <?php if ($result->num_rows > 0): ?>
                <div class="students-grid">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="student-card" id="card-<?= htmlspecialchars($row['student_id']) ?>">
                        <!-- Main Card Content -->
                        <div class="student-main" onclick="toggleDetails('<?= htmlspecialchars($row['student_id']) ?>')">
                            <img src="../avatars/<?= htmlspecialchars($row['avatar']) ?>.png" 
                                 alt="Avatar" 
                                 class="student-avatar"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJDMTMuMTgyIDIgMTQuMjM1MiAyLjIzNTIgMTUuMDg3OSAyLjYzNUMxNS45NDc5IDMuMDM1MiAxNi42NTUzIDMuNjE0OCAxNy4yMTEgNC4zNzE1QzE3Ljc2NjcgNS4xMjgyIDE4LjA5MTggNS45ODg4IDE4LjE4NjUgNi45NTAyQzE4LjI4MTIgNy45MTE1IDE4LjE0NzkgOC44OTk4IDE3Ljc4NjcgOS45MTVDMTcuNDI1NSAxMC45MzA1IDE2Ljg1MzcgMTEuNzc0MiAxNi4wNzE0IDEyLjQ0OTVDMTUuMjg5MiAxMy4xMjQ4IDE0LjM0MjIgMTMuNTAyMiAxMy4yMzA3IDEzLjU4MkMxMi4xMTkxIDEzLjY2MTggMTEuMDA5IDEzLjQ4MTggOS44OTUyIDEzLjA0MkM4Ljc4MTUgMTIuNjAyMiA3Ljg1ODUgMTEuOTU1IDcuMTI2MiAxMS4wOTc3QzYuMzkzOSAxMC4yNDA0IDUuOTMxOCA5LjI0OTIgNS43Mzk5IDguMTI0QzUuNTQ4IDcuMDAwNSA1LjY1ODggNS44MzM1IDYuMDcyMyA0LjYyM0M2LjQ4NTggMy40MTI1IDcuMTE3IDIuNDU4OCA3Ljk2NTggMS43NjFDOC44MTQ3IDEuMDYzOCA5LjgyMjcgMC42NjU1IDExIDAuNjY1NVoiIGZpbGw9IiM2OUI0MUUiLz4KPC9zdmc+'">
                            <div class="student-info">
                                <div class="student-name">
                                    <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                                    <?php if ($row['suffix']): ?>
                                        <span class="text-muted"><?= htmlspecialchars($row['suffix']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="student-id"><?= htmlspecialchars($row['student_id']) ?></div>
                                <div class="student-badges">
                                    <span class="badge <?= $row['sex'] === 'Male' ? 'bg-info' : 'bg-pink' ?> me-1">
                                        <?= htmlspecialchars($row['sex']) ?>
                                    </span>
                                    <span class="badge bg-primary"><?= htmlspecialchars($row['program']) ?></span>
                                </div>
                            </div>
                            <div class="toggle-icon">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                        
                        <!-- Collapsible Details -->
                        <div class="student-details" id="details-<?= htmlspecialchars($row['student_id']) ?>">
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Middle Name</span>
                                    <span class="detail-value"><?= htmlspecialchars($row['middle_name']) ?: '-' ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Course</span>
                                    <span class="detail-value"><?= htmlspecialchars($row['course']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Email</span>
                                    <span class="detail-value"><?= htmlspecialchars($row['email']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Registered</span>
                                    <span class="detail-value"><?= date('M j, Y', strtotime($row['created_at'])) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">QR Code</span>
                                    <span class="detail-value">
                                        <img src="../qr_images/<?= htmlspecialchars($row['qr_code']) ?>.png" 
                                             alt="QR Code" 
                                             class="qr-code"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1zbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMyIgeT0iMyIgd2lkdGg9IjYiIGhlaWdodD0iNiIgZmlsbD0iIzBDNUExMSIvPgo8cmVjdCB4PSIxNSIgeT0iMyIgd2lkdGg9IjYiIGhlaWdodD0iNiIgZmlsbD0iIzBDNUExMSIvPgo8cmVjdCB4PSIzIiB5PSIxNSIgd2lkdGg9IjYiIGhlaWdodD0iNiIgZmlsbD0iIzBDNUExMSIvPgo8cmVjdCB4PSI5IiB5PSI5IiB3aWR0aD0iNiIgaGVpZ2h0PSI2IiBmaWxsPSIjMEM1QTExIi8+CjxyZWN0IHg9IjE1IiB5PSI5IiB3aWR0aD0iNiIgaGVpZ2h0PSI2IiBmaWxsPSIjMEM1QTExIi8+CjxyZWN0IHg9IjkiIHk9IjE1IiB3aWR0aD0iNiIgaGVpZ2h0PSI2IiBmaWxsPSIjMEM1QTExIi8+CjxyZWN0IHg9IjE1IiB5PSIxNSIgd2lkdGg9IjYiIGhlaWdodD0iNiIgZmlsbD0iIzBDNUExMSIvPgo8L3N2Zz4='">
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="student-actions">
                            <div class="btn-group btn-group-sm">
                                <!-- Edit Button -->
                                <button type="button" 
                                        class="btn btn-outline-warning edit-profile-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#profileModal"
                                        data-id="<?= htmlspecialchars($row['student_id']) ?>"
                                        data-first="<?= htmlspecialchars($row['first_name']) ?>"
                                        data-middle="<?= htmlspecialchars($row['middle_name']) ?>"
                                        data-last="<?= htmlspecialchars($row['last_name']) ?>"
                                        data-suffix="<?= htmlspecialchars($row['suffix']) ?>"
                                        data-sex="<?= htmlspecialchars($row['sex']) ?>"
                                        data-program="<?= htmlspecialchars($row['program']) ?>"
                                        data-course="<?= htmlspecialchars($row['course']) ?>"
                                        data-email="<?= htmlspecialchars($row['email']) ?>"
                                        data-avatar="<?= htmlspecialchars($row['avatar']) ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                
                                <!-- Delete Button -->
                                <button type="button" 
                                        class="btn btn-outline-danger delete-btn"
                                        data-student-id="<?= htmlspecialchars($row['student_id']) ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <h4>No students found</h4>
                    <p>
                        <?php if ($search): ?>
                            No students match "<?= htmlspecialchars($search) ?>". Try a different search term.
                        <?php else: ?>
                            There are no students in the database yet.
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php
            // Get total number of matching records
            $count_sql = "SELECT COUNT(*) FROM students 
                          WHERE student_id LIKE ? 
                          OR first_name LIKE ? 
                          OR middle_name LIKE ? 
                          OR last_name LIKE ? 
                          OR suffix LIKE ? 
                          OR sex LIKE ? 
                          OR program LIKE ? 
                          OR course LIKE ? 
                          OR email LIKE ? 
                          OR created_at LIKE ?";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bind_param("ssssssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
            $count_stmt->execute();
            $count_stmt->bind_result($total_records);
            $count_stmt->fetch();
            $total_pages = ceil($total_records / $records_per_page);
            ?>

            <?php if ($total_pages > 1): ?>
            <nav aria-label="Student pagination">
                <ul class="pagination justify-content-center mt-4">
                    <!-- Previous Button -->
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php
                    $range = 2;
                    $start = max(1, $page - $range);
                    $end = min($total_pages, $page + $range);

                    if ($start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search) . '&page=1">1</a></li>';
                        if ($start > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor;

                    if ($end < $total_pages) {
                        if ($end < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search) . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next Button -->
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Message Modal -->
    <?php if (isset($_SESSION['message'])): ?>
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content d-flex justify-content-center align-items-center text-center p-3"
                style="background: none; border: none; box-shadow: none;">
                <div class="alert alert-<?= $_SESSION['message_type'] ?> text-dark fw-bold mb-0" role="alert">
                    <?= $_SESSION['message'] ?>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
            messageModal.show();
        });
    </script>

    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h5 class="modal-title text-danger text-center w-100" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete student with</p>
                    <p class="text-danger fw-bold fs-5" id="displayStudentId"></p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger ms-2" id="confirmDeleteBtn">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">
                        <i class="bi bi-person-gear"></i> Edit Student Profile
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" action="php/update.php" method="POST">
                        <!-- Avatar Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Choose Avatar</label>
                            <div class="avatar-option">
                                <label>
                                    <input type="radio" name="avatar" value="JOY" hidden>
                                    <img src="../avatars/JOY.png" alt="JOY" onerror="this.src='../avatars/JOY.jpg'">
                                    <div class="text-center small mt-1">JOY</div>
                                </label>
                                <label>
                                    <input type="radio" name="avatar" value="SEVI" hidden>
                                    <img src="../avatars/SEVI.png" alt="SEVI" onerror="this.src='../avatars/SEVI.jpg'">
                                    <div class="text-center small mt-1">SEVI</div>
                                </label>
                                <label>
                                    <input type="radio" name="avatar" value="SAMANTHA" hidden>
                                    <img src="../avatars/SAMANTHA.png" alt="SAMANTHA" onerror="this.src='../avatars/SAMANTHA.jpg'">
                                    <div class="text-center small mt-1">SAMANTHA</div>
                                </label>
                                <label>
                                    <input type="radio" name="avatar" value="ZEKE" hidden>
                                    <img src="../avatars/ZEKE.png" alt="ZEKE" onerror="this.src='../avatars/ZEKE.jpg'">
                                    <div class="text-center small mt-1">ZEKE</div>
                                </label>
                            </div>
                        </div>

                        <!-- Student ID (Read-only) -->
                        <div class="mb-3">
                            <label for="registerStudentId" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="registerStudentId" name="student_id" readonly>
                        </div>

                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middle_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="suffix" class="form-label">Suffix</label>
                                <select class="form-control" id="suffix" name="suffix">
                                    <option value="">None</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>
                            </div>
                        </div>

                        <!-- Sex -->
                        <div class="mb-3">
                            <label for="sex" class="form-label">Sex</label>
                            <select class="form-control" id="sex" name="sex" required>
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <!-- Program and Course -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="program" class="form-label">Program</label>
                                <select id="program" name="program" class="form-control" onchange="updateCourses()">
                                    <option value="">Select Program</option>
                                    <option value="ITE">Information Technology Education (ITE)</option>
                                    <option value="CELA">College of Education, Liberal Arts (CELA)</option>
                                    <option value="CBA">College of Business Administration (CBA)</option>
                                    <option value="HME">Hospitality Management & Entrepreneurship (HME)</option>
                                    <option value="CJE">College of Criminal Justice Education (CJE)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="course" class="form-label">Course</label>
                                <select id="course" name="course" class="form-control">
                                    <option value="">Select Course</option>
                                </select>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="gmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="gmail" name="email" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    
    <script>
        // Toggle student details
        function toggleDetails(studentId) {
            const details = document.getElementById('details-' + studentId);
            const toggleIcon = details.closest('.student-card').querySelector('.toggle-icon');
            
            details.classList.toggle('active');
            toggleIcon.classList.toggle('rotated');
        }

        // Edit profile modal functionality
        document.addEventListener("DOMContentLoaded", function () {
            let editProfileButtons = document.querySelectorAll(".edit-profile-btn");

            editProfileButtons.forEach(button => {
                button.addEventListener("click", function () {
                    document.getElementById("registerStudentId").value = this.dataset.id;
                    document.getElementById("firstName").value = this.dataset.first;
                    document.getElementById("middleName").value = this.dataset.middle;
                    document.getElementById("lastName").value = this.dataset.last;
                    document.getElementById("suffix").value = this.dataset.suffix;
                    document.getElementById("sex").value = this.dataset.sex;
                    document.getElementById("program").value = this.dataset.program;
                    document.getElementById("course").dataset.selected = this.dataset.course;
                    document.getElementById("gmail").value = this.dataset.email;

                    // Ensure course updates when program is selected
                    updateCourses();

                    // Auto-select avatar
                    let selectedAvatar = this.dataset.avatar;
                    document.querySelectorAll("input[name='avatar']").forEach(input => {
                        input.checked = (input.value === selectedAvatar);
                    });
                });
            });
        });

        // Course update function
        function updateCourses() {
            const courses = {
                ITE: ["Bachelor of Science in Information Technology"],
                CELA: [
                    "Bachelor of Arts Major in History",
                    "Bachelor of Arts Major in Political Science",
                    "Bachelor of Elementary Education – Generalist",
                    "Bachelor of Special Needs Education",
                    "Bachelor of Secondary Education Major in English",
                    "Bachelor of Secondary Education Major in Mathematics",
                    "Bachelor of Secondary Education Major in Science",
                    "Bachelor of Secondary Education Major in Social Studies",
                    "Bachelor of Technology and Livelihood Education Major in Home Economics"
                ],
                CBA: [
                    "Bachelor of Science in Business Administration Major in Financial Management",
                    "Bachelor of Science in Business Administration Major in Human Resource Management",
                    "Bachelor of Science in Business Administration Major in Marketing Management"
                ],
                HME: ["Bachelor of Science in Hospitality Management"],
                CJE: ["Bachelor of Science in Criminology"]
            };

            const programDropdown = document.getElementById("program");
            const courseDropdown = document.getElementById("course");
            const selectedProgram = programDropdown.value;
            const currentCourse = courseDropdown.dataset.selected || "";

            courseDropdown.innerHTML = "<option value=''>Select Course</option>";

            if (selectedProgram && courses[selectedProgram]) {
                courses[selectedProgram].forEach(course => {
                    const option = document.createElement("option");
                    option.text = course;
                    option.value = course;
                    if (course === currentCourse) {
                        option.selected = true;
                    }
                    courseDropdown.add(option);
                });

                if (courses[selectedProgram].length === 1) {
                    courseDropdown.value = courses[selectedProgram][0];
                }
            }
        }

        // Delete functionality
        document.addEventListener("DOMContentLoaded", function () {
            let selectedStudentId = null;

            // Set up modal trigger
            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", function () {
                    selectedStudentId = this.getAttribute("data-student-id");
                    document.getElementById('displayStudentId').textContent = selectedStudentId;
                });
            });

            // Handle confirm delete
            document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
                if (!selectedStudentId) return;

                fetch('php/deletestudent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'student_id=' + encodeURIComponent(selectedStudentId)
                })
                .then(response => response.text())
                .then(result => {
                    if (result.includes("success")) {
                        // Remove the card from DOM
                        const card = document.getElementById("card-" + selectedStudentId);
                        if (card) {
                            card.remove();
                        }

                        // Close modal
                        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                        deleteModal.hide();
                        
                        // Show success message
                        alert("Student deleted successfully!");
                    } else {
                        alert("Could not delete student. Please try again.");
                    }
                })
                .catch(error => {
                    alert("Error deleting student: " + error);
                });
            });
        });
    </script>
</body>
</html>
