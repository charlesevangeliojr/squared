<?php
// voting.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
require 'php/config.php';

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$CSRF = $_SESSION['csrf_token'];

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function valid_slug($s) { return preg_match('/^[a-z0-9\-]{3,64}$/i', $s); }

// Flash messages
function set_flash($ok = null, $err = null) {
    if ($ok) $_SESSION['flash_ok'] = $ok;
    if ($err) $_SESSION['flash_err'] = $err;
}
function get_flash() {
    $out = [$_SESSION['flash_ok'] ?? null, $_SESSION['flash_err'] ?? null];
    unset($_SESSION['flash_ok'], $_SESSION['flash_err']);
    return $out;
}

$flash = null; $err = null;

// POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
        $err = "Security check failed. Please try again.";
    } else {
        $action = $_POST['action'] ?? '';

        // Create Award
        if ($action === 'create_award') {
            $slug = trim($_POST['slug'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $opens = trim($_POST['opens_at'] ?? '');
            $closes = trim($_POST['closes_at'] ?? '');

            if (!valid_slug($slug) || $title === '') {
                $err = "Enter a valid slug (letters/numbers/dashes) and a title.";
            } else {
                $stmt = $conn->prepare("INSERT INTO pca_awards (slug, title, opens_at, closes_at) VALUES (?, ?, ?, ?)");
                $opens = $opens !== '' ? $opens : null;
                $closes = $closes !== '' ? $closes : null;
                $stmt->bind_param("ssss", $slug, $title, $opens, $closes);
                try {
                    $stmt->execute();
                    set_flash("Award/Category created successfully.");
                    header("Location: voting.php?tab=award");
                    exit();
                } catch (mysqli_sql_exception $e) {
                    $err = ($e->getCode() === 1062) ? "That slug already exists." : "Database error while creating award.";
                } finally { $stmt->close(); }
            }
        }

        // Add Nominee
        if ($action === 'add_nominee' && !$err) {
            $award_id = (int)($_POST['award_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $photo_url = trim($_POST['photo_url'] ?? '');

            if ($award_id <= 0 || $name === '') {
                $err = "Please select an award and provide a nominee name.";
            } else {
                // File upload handling
                if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp'];
                    $mime = mime_content_type($_FILES['photo']['tmp_name']);
                    if (!isset($allowed[$mime])) {
                        $err = "Only JPG, PNG, or WEBP files are allowed.";
                    } else {
                        $dir = __DIR__ . '/uploads/pca';
                        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                        $fname = uniqid('pca_', true) . $allowed[$mime];
                        $destFs = $dir . '/' . $fname;
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $destFs)) {
                            $photo_url = 'uploads/pca/' . $fname;
                        } else {
                            $err = "Failed to save uploaded image.";
                        }
                    }
                }

                if (!$err) {
                    $stmt = $conn->prepare("INSERT INTO pca_nominees (award_id, name, description, photo_url) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $award_id, $name, $description, $photo_url);
                    try {
                        $stmt->execute();
                        set_flash("Nominee added successfully.");
                        header("Location: voting.php?tab=nominee");
                        exit();
                    } catch (mysqli_sql_exception $e) {
                        $err = "Database error while adding nominee.";
                    } finally { $stmt->close(); }
                }
            }
        }

        // Delete Award
        if ($action === 'delete_award' && !$err) {
            $aid = (int)($_POST['award_id'] ?? 0);
            if ($aid > 0) {
                // Check for existing votes
                $chk = $conn->prepare("SELECT COUNT(*) FROM pca_votes WHERE award_id = ?");
                $chk->bind_param("i", $aid);
                $chk->execute();
                $chk->bind_result($vc);
                $chk->fetch();
                $chk->close();
                if ($vc > 0) {
                    set_flash(null, "Cannot delete: award already has votes.");
                    header("Location: voting.php?tab=award");
                    exit();
                }

                $stmt = $conn->prepare("DELETE FROM pca_awards WHERE id = ?");
                $stmt->bind_param("i", $aid);
                try {
                    $stmt->execute();
                    set_flash("Award deleted successfully.");
                    header("Location: voting.php?tab=award");
                    exit();
                } finally { $stmt->close(); }
            }
        }

        // Delete Nominee
        if ($action === 'delete_nominee' && !$err) {
            $nid = (int)($_POST['nominee_id'] ?? 0);
            if ($nid > 0) {
                $stmt = $conn->prepare("DELETE FROM pca_nominees WHERE id = ?");
                $stmt->bind_param("i", $nid);
                try {
                    $stmt->execute();
                    set_flash("Nominee deleted successfully.");
                    header("Location: voting.php?tab=nominee");
                    exit();
                } catch (mysqli_sql_exception $e) {
                    $err = "Database error while deleting nominee.";
                } finally { $stmt->close(); }
            }
        }

        // Toggle Award Status
        if ($action === 'toggle_award' && !$err) {
            $aid = (int)($_POST['award_id'] ?? 0);
            $newStatus = $_POST['new_status'] === 'off' ? 'off' : 'on';
            if ($aid > 0) {
                $stmt = $conn->prepare("UPDATE pca_awards SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $newStatus, $aid);
                try {
                    $stmt->execute();
                    set_flash("Award status updated.");
                    header("Location: voting.php?tab=award");
                    exit();
                } catch (mysqli_sql_exception $e) {
                    $err = "Database error while updating award status.";
                } finally { $stmt->close(); }
            }
        }
    }
}

// Fetch data
$awards = [];
$res = $conn->query("SELECT id, slug, title, status FROM pca_awards ORDER BY id DESC");
while ($row = $res->fetch_assoc()) { $awards[] = $row; }
$res->free();

$defaultResultsSlug = !empty($awards) ? $awards[0]['slug'] : '';

$nomineesByAward = [];
if (!empty($awards)) {
    $res = $conn->query("
        SELECT n.id, n.award_id, n.name, LEFT(n.description, 200) AS blurb, n.photo_url
        FROM pca_nominees n
        ORDER BY n.award_id DESC, n.name ASC
    ");
    while ($row = $res->fetch_assoc()) {
        $nomineesByAward[$row['award_id']][] = $row;
    }
    $res->close();
}

list($flashOk, $flashErr) = get_flash();
$flash = $flashOk;
$err = $flashErr ?: $err;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Admin - Voting Management</title>
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
        .voting-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .voting-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }
        
        .card-header .card-title {
            margin-bottom: 0;
            font-weight: 600;
        }
        
        .nominee-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .results-progress {
            height: 28px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Award status badges */
        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
        
        /* Tab styling */
        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px 8px 0 0;
            margin-right: 4px;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--color-primary);
            background-color: #f8f9fa;
        }
        
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            border-radius: 8px 8px 0 0;
        }
        
        /* Nominee cards */
        .nominee-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .nominee-card:hover {
            border-color: var(--color-secondary);
            box-shadow: 0 2px 8px rgba(105, 180, 30, 0.1);
        }
        
        /* Results cards */
        .result-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            background: white;
        }
        
        .result-card:hover {
            border-color: var(--color-primary);
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
            .voting-card {
                margin-bottom: 1rem;
            }
            
            .card-header {
                padding: 1rem;
            }
            
            .nav-tabs .nav-link {
                padding: 10px 16px;
                font-size: 0.9rem;
                margin-right: 2px;
            }
            
            .nominee-photo {
                width: 50px;
                height: 50px;
            }
            
            .results-progress {
                height: 24px;
                font-size: 0.8rem;
            }
            
            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            /* Statistics cards on mobile */
            .row.mb-4 .col-md-3 {
                margin-bottom: 1rem;
            }
            
            /* Ensure sidebar overlay works properly on mobile */
            .sidebar-overlay {
                z-index: 1025 !important;
            }
            
            /* Stack form elements on mobile */
            .row .col-lg-6 {
                margin-bottom: 1.5rem;
            }
            .row {
                justify-content: space-evenly;
            }
            
        }
        
        @media (max-width: 576px) {
            .nav-tabs {
                flex-direction: column;
            }
            
            .nav-tabs .nav-link {
                margin-right: 0;
                margin-bottom: 4px;
                border-radius: 8px;
            }
            
            .nav-tabs .nav-link.active {
                border-radius: 8px;
            }
            
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start !important;
            }
            
            .card-header .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }
        
        /* Form enhancements */
        .form-label {
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--color-secondary);
            box-shadow: 0 0 0 0.2rem rgba(105, 180, 30, 0.25);
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
            <h2 class="tt">Voting Management</h2>

            <!-- Voting Statistics -->
            <div class="row mb-4">
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg1 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Total Awards</h5>
                            <h3 class="nu mb-0"><?= count($awards) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg2 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Active Awards</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $active_count = 0;
                                    foreach ($awards as $award) {
                                        if ($award['status'] === 'on') $active_count++;
                                    }
                                    echo $active_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg3 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Total Nominees</h5>
                            <h3 class="nu mb-0">
                                <?php 
                                    $nominee_count = 0;
                                    foreach ($nomineesByAward as $nominees) {
                                        $nominee_count += count($nominees);
                                    }
                                    echo $nominee_count;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($flash): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?= h($flash) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($err): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> <?= h($err) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="votingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="award-tab" data-bs-toggle="tab" data-bs-target="#award-pane" type="button">
                        <i class="bi bi-trophy"></i> Awards
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="nominee-tab" data-bs-toggle="tab" data-bs-target="#nominee-pane" type="button">
                        <i class="bi bi-person"></i> Nominees
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results-pane" type="button">
                        <i class="bi bi-graph-up"></i> Results
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="votingTabContent">
                <!-- Awards Tab -->
                <div class="tab-pane fade show active" id="award-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card voting-card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-plus-circle"></i> Create New Award
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                        <input type="hidden" name="action" value="create_award">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Slug</label>
                                            <input type="text" name="slug" class="form-control" placeholder="pca-2025" required>
                                            <div class="form-text text-muted">Use letters, numbers, and dashes only (3-64 characters)</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Title</label>
                                            <input type="text" name="title" class="form-control" placeholder="People's Choice Award 2025" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Opens At</label>
                                                <input type="datetime-local" name="opens_at" class="form-control">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Closes At</label>
                                                <input type="datetime-local" name="closes_at" class="form-control">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 py-2">
                                            <i class="bi bi-check-lg"></i> Create Award
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card voting-card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-trophy"></i> Existing Awards
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($awards)): ?>
                                        <div class="empty-state">
                                            <i class="bi bi-trophy"></i>
                                            <h5>No Awards Created</h5>
                                            <p class="text-muted">Create your first award to get started.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Slug</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($awards as $a): ?>
                                                    <tr>
                                                        <td class="fw-medium"><?= h($a['title']) ?></td>
                                                        <td><code class="text-primary"><?= h($a['slug']) ?></code></td>
                                                        <td>
                                                            <span class="badge status-badge <?= $a['status'] === 'on' ? 'bg-success' : 'bg-secondary' ?>">
                                                                <?= strtoupper($a['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <form method="post" class="d-inline">
                                                                    <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                                                    <input type="hidden" name="action" value="toggle_award">
                                                                    <input type="hidden" name="award_id" value="<?= (int)$a['id'] ?>">
                                                                    <input type="hidden" name="new_status" value="<?= $a['status'] === 'on' ? 'off' : 'on' ?>">
                                                                    <button type="submit" class="btn btn-<?= $a['status'] === 'on' ? 'success' : 'secondary' ?>">
                                                                        <?= $a['status'] === 'on' ? 'On' : 'Off' ?>
                                                                    </button>
                                                                </form>
                                                                <form method="post" onsubmit="return confirm('Delete this award and all its nominees?');" class="d-inline">
                                                                    <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                                                    <input type="hidden" name="action" value="delete_award">
                                                                    <input type="hidden" name="award_id" value="<?= (int)$a['id'] ?>">
                                                                    <button type="submit" class="btn btn-outline-danger">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nominees Tab -->
                <div class="tab-pane fade" id="nominee-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card voting-card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-person-plus"></i> Add Nominee
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($awards)): ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i> Create an award first before adding nominees.
                                        </div>
                                    <?php else: ?>
                                        <form method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                            <input type="hidden" name="action" value="add_nominee">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Award Category</label>
                                                <select name="award_id" class="form-select" required>
                                                    <option value="">Select Award...</option>
                                                    <?php foreach ($awards as $a): ?>
                                                        <option value="<?= (int)$a['id'] ?>"><?= h($a['title']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nominee Name</label>
                                                <input type="text" name="name" class="form-control" placeholder="Enter nominee name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Description</label>
                                                <textarea name="description" class="form-control" rows="3" placeholder="Brief description or bio..."></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Photo URL</label>
                                                <input type="url" name="photo_url" class="form-control" placeholder="https://example.com/photo.jpg">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Or Upload Photo</label>
                                                <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                                <div class="form-text text-muted">JPG, PNG, or WEBP files only (max 2MB)</div>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100 py-2">
                                                <i class="bi bi-plus-lg"></i> Add Nominee
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card voting-card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-people"></i> Existing Nominees
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($nomineesByAward)): ?>
                                        <div class="empty-state">
                                            <i class="bi bi-person"></i>
                                            <h5>No Nominees Added</h5>
                                            <p class="text-muted">Add nominees to your awards to start voting.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($awards as $a): 
                                            $aid = (int)$a['id'];
                                            if (empty($nomineesByAward[$aid])) continue;
                                        ?>
                                            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><?= h($a['title']) ?></h6>
                                            <div class="vstack gap-3 mb-4">
                                                <?php foreach ($nomineesByAward[$aid] as $n): ?>
                                                <div class="nominee-card d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <?php if (!empty($n['photo_url'])): ?>
                                                            <img src="<?= h($n['photo_url']) ?>" class="nominee-photo" onerror="this.style.display='none'">
                                                        <?php else: ?>
                                                            <div class="nominee-photo bg-light d-flex align-items-center justify-content-center">
                                                                <i class="bi bi-person text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="fw-bold text-dark"><?= h($n['name']) ?></div>
                                                            <div class="text-muted small"><?= h($n['blurb']) ?></div>
                                                        </div>
                                                    </div>
                                                    <form method="post" onsubmit="return confirm('Delete this nominee?');">
                                                        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                                        <input type="hidden" name="action" value="delete_nominee">
                                                        <input type="hidden" name="nominee_id" value="<?= (int)$n['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Tab -->
                <div class="tab-pane fade" id="results-pane" role="tabpanel">
                    <div class="card voting-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-graph-up"></i> Live Results
                            </h5>
                            <?php if (!empty($awards)): ?>
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 fw-bold text-white">Award Category:</label>
                                <select id="results-award" class="form-select form-select-sm" style="width: auto;">
                                    <?php foreach ($awards as $a): ?>
                                        <option value="<?= h($a['slug']) ?>" <?= $a['slug']===$defaultResultsSlug?'selected':'' ?>>
                                            <?= h($a['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (empty($awards)): ?>
                                <div class="empty-state">
                                    <i class="bi bi-trophy"></i>
                                    <h5>No Awards Available</h5>
                                    <p class="text-muted">Create awards to view voting results.</p>
                                </div>
                            <?php else: ?>
                                <div id="results-meta" class="alert alert-info mb-4"></div>
                                <div id="results-list" class="vstack gap-3"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    
    <script>
        // Tab handling from URL parameter
        (function(){
            const params = new URLSearchParams(location.search);
            const tab = params.get('tab');
            if (tab) {
                const btn = document.querySelector(`[data-bs-target="#${tab}-pane"]`);
                if (btn && window.bootstrap) {
                    new bootstrap.Tab(btn).show();
                }
            }
        })();

        // Live Results loader
        (function(){
            const awardSel = document.getElementById('results-award');
            const listEl = document.getElementById('results-list');
            const metaEl = document.getElementById('results-meta');
            let timer = null;

            async function loadResults(slug){
                if (!slug || !listEl) return;
                try {
                    const r = await fetch(`php/pca_results.php?award=${encodeURIComponent(slug)}`, {cache:'no-store'});
                    const data = await r.json();
                    if (!data.ok) throw new Error('Bad response');

                    const total = data.results.reduce((s,x) => s + (x.votes||0), 0);
                    metaEl.innerHTML = `<i class="bi bi-info-circle"></i> <strong>${data.title || slug}</strong> — Total votes: <span class="badge bg-primary">${total}</span>`;
                    metaEl.className = 'alert alert-info mb-4';

                    listEl.innerHTML = '';
                    if (data.results.length === 0) {
                        listEl.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-inbox display-4 d-block mb-2"></i>No votes recorded yet.</div>';
                        return;
                    }

                    data.results.forEach(item => {
                        const pct = total > 0 ? Math.round((item.votes / total) * 100) : 0;
                        const row = document.createElement('div');
                        row.className = 'result-card';
                        row.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <strong class="text-primary fs-5">${escapeHtml(item.name)}</strong>
                                <span class="badge bg-success fs-6">${item.votes} votes</span>
                            </div>
                            <div class="progress results-progress" role="progressbar">
                                <div class="progress-bar bg-success" style="width:${pct}%">
                                    ${pct}%
                                </div>
                            </div>
                        `;
                        listEl.appendChild(row);
                    });
                } catch(e){
                    metaEl.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Unable to load results at the moment.';
                    metaEl.className = 'alert alert-warning mb-4';
                    listEl.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-exclamation-circle display-4 d-block mb-2"></i>Failed to load results</div>';
                }
            }

            function escapeHtml(s){
                return String(s)
                    .replaceAll('&','&amp;')
                    .replaceAll('<','&lt;')
                    .replaceAll('>','&gt;')
                    .replaceAll('"','&quot;')
                    .replaceAll("'",'&#039;');
            }

            function startAutoRefresh(){
                if (!awardSel) return;
                const slug = awardSel.value;
                loadResults(slug);
                if (timer) clearInterval(timer);
                timer = setInterval(() => loadResults(awardSel.value), 10000);
            }

            // Event listeners for results tab
            const resultsTab = document.getElementById('results-tab');
            if (resultsTab && awardSel) {
                awardSel.addEventListener('change', () => loadResults(awardSel.value));
                resultsTab.addEventListener('shown.bs.tab', startAutoRefresh);
                resultsTab.addEventListener('hide.bs.tab', () => { if (timer) clearInterval(timer); });
                if (resultsTab.classList.contains('active')) startAutoRefresh();
            }
        })();
    </script>
</body>
</html>