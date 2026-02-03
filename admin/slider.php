<?php
// slider.php
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

// Create uploads directory if it doesn't exist
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
        $err = "Security check failed. Please try again.";
    } else {
        $action = $_POST['action'] ?? '';

        // Add Slider Image
        if ($action === 'add_slider_image') {
            $display_order = intval($_POST['display_order'] ?? 1);
            
            // Handle file upload
            if (isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['slider_image'];
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedTypes)) {
                    $err = "Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.";
                } else {
                    // Generate unique filename
                    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileName = 'slider_' . uniqid() . '.' . $fileExtension;
                    $filePath = $uploadDir . $fileName;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file['tmp_name'], $filePath)) {
                        $image_url = 'uploads/' . $fileName;
                        
                        $stmt = $conn->prepare("INSERT INTO slider_images (image_url, display_order) VALUES (?, ?)");
                        $stmt->bind_param("si", $image_url, $display_order);
                        try {
                            $stmt->execute();
                            set_flash("Slider image uploaded successfully.");
                            header("Location: slider.php");
                            exit();
                        } catch (mysqli_sql_exception $e) {
                            // Delete the uploaded file if database insert fails
                            unlink($filePath);
                            $err = "Database error while adding slider image.";
                        } finally { 
                            $stmt->close(); 
                        }
                    } else {
                        $err = "Failed to upload image. Please try again.";
                    }
                }
            } else {
                $uploadError = $_FILES['slider_image']['error'] ?? UPLOAD_ERR_NO_FILE;
                if ($uploadError === UPLOAD_ERR_NO_FILE) {
                    $err = "Please select an image to upload.";
                } else {
                    $err = "File upload error. Code: " . $uploadError;
                }
            }
        }

        // Delete Slider Image
        if ($action === 'delete_slider_image' && !$err) {
            $image_id = (int)($_POST['image_id'] ?? 0);
            if ($image_id > 0) {
                // First, get the image URL to delete the file
                $stmt = $conn->prepare("SELECT image_url FROM slider_images WHERE id = ?");
                $stmt->bind_param("i", $image_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $imagePath = '../' . $row['image_url'];
                    
                    // Delete from database
                    $deleteStmt = $conn->prepare("DELETE FROM slider_images WHERE id = ?");
                    $deleteStmt->bind_param("i", $image_id);
                    try {
                        $deleteStmt->execute();
                        
                        // Delete the physical file
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                        
                        set_flash("Slider image deleted successfully.");
                        header("Location: slider.php");
                        exit();
                    } catch (mysqli_sql_exception $e) {
                        $err = "Database error while deleting slider image.";
                    } finally { 
                        $deleteStmt->close(); 
                    }
                }
                $stmt->close();
            }
        }

        // Update Display Order
        if ($action === 'update_order' && !$err) {
            $image_id = (int)($_POST['image_id'] ?? 0);
            $display_order = intval($_POST['display_order'] ?? 1);
            if ($image_id > 0) {
                $stmt = $conn->prepare("UPDATE slider_images SET display_order = ? WHERE id = ?");
                $stmt->bind_param("ii", $display_order, $image_id);
                try {
                    $stmt->execute();
                    set_flash("Display order updated successfully.");
                    header("Location: slider.php");
                    exit();
                } catch (mysqli_sql_exception $e) {
                    $err = "Database error while updating display order.";
                } finally { $stmt->close(); }
            }
        }
    }
}

// Fetch slider images
$slider_images = [];
$res = $conn->query("SELECT id, image_url, display_order FROM slider_images ORDER BY display_order ASC");
while ($row = $res->fetch_assoc()) { $slider_images[] = $row; }
$res->free();

list($flashOk, $flashErr) = get_flash();
$flash = $flashOk;
$err = $flashErr ?: $err;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Admin - Slider Management</title>
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
        .slider-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .slider-card:hover {
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
        
        .slider-image-preview {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        /* Status badges */
        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
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
        
        /* File upload styling */
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .file-upload-area:hover {
            border-color: var(--color-secondary);
            background: #f0f8e8;
        }
        
        .file-upload-area.dragover {
            border-color: var(--color-primary);
            background: #e8f5e8;
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
        
        /* Preview section */
        .preview-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .preview-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* FIXED: Correct z-index hierarchy */
        #sidebar {
            z-index: 1035 !important;
        }

        .modal-backdrop {
            z-index: 1030 !important;
        }

        .modal {
            z-index: 1040 !important;
        }

        .navbar {
            z-index: 1030 !important;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .slider-card {
                margin-bottom: 1rem;
            }
            
            .card-header {
                padding: 1rem;
            }
            
            .slider-image-preview {
                width: 100px;
                height: 60px;
            }
            
            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .preview-section {
                padding: 1rem;
            }
            
            .file-upload-area {
                padding: 1.5rem 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start !important;
            }
            
            .card-header .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
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
            <h2 class="tt">Slider Management</h2>

            <!-- Slider Statistics -->
            <div class="row mb-4">
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg1 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Total Images</h5>
                            <h3 class="nu mb-0"><?= count($slider_images) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg2 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Active Images</h5>
                            <h3 class="nu mb-0"><?= count($slider_images) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card bg3 text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="card-title mb-1">Display Order</h5>
                            <h3 class="nu mb-0">1-<?= count($slider_images) ?></h3>
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

            <div class="row">
                <!-- Add New Image -->
                <div class="col-lg-6 mb-4">
                    <div class="card slider-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-plus-circle"></i> Upload New Slider Image
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post" id="addImageForm" enctype="multipart/form-data">
                                <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                <input type="hidden" name="action" value="add_slider_image">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Image</label>
                                    <div class="file-upload-area" id="fileUploadArea">
                                        <i class="bi bi-cloud-arrow-up display-4 text-muted mb-3"></i>
                                        <p class="mb-2">Drag & drop your image here or click to browse</p>
                                        <p class="small text-muted mb-3">Supported formats: JPG, PNG, GIF, WebP</p>
                                        <input type="file" name="slider_image" id="sliderImage" 
                                               class="form-control d-none" accept="image/*" required>
                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('sliderImage').click()">
                                            <i class="bi bi-folder2-open"></i> Choose File
                                        </button>
                                        <div id="fileName" class="mt-2 small text-primary fw-bold"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Display Order</label>
                                    <input type="number" name="display_order" class="form-control" 
                                           value="<?= count($slider_images) + 1 ?>" min="1" required>
                                    <div class="form-text text-muted">
                                        Lower numbers appear first in the slider
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div class="preview-section">
                                    <label class="form-label fw-bold">Image Preview</label>
                                    <div id="imagePreview" class="text-center">
                                        <p class="text-muted">Select an image to see preview</p>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success w-100 py-2 mt-3">
                                    <i class="bi bi-upload"></i> Upload Slider Image
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Current Slider Images -->
                <div class="col-lg-6">
                    <div class="card slider-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-images"></i> Current Slider Images
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($slider_images)): ?>
                                <div class="empty-state">
                                    <i class="bi bi-images"></i>
                                    <h5>No Slider Images</h5>
                                    <p class="text-muted">Upload your first slider image to get started.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Preview</th>
                                                <th>Image</th>
                                                <th>Order</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($slider_images as $image): ?>
                                            <tr>
                                                <td>
                                                    <img src="../<?= h($image['image_url']) ?>" 
                                                         class="slider-image-preview" 
                                                         onerror="this.src='../images/placeholder.jpg'"
                                                         alt="Slider Image">
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?= h(basename($image['image_url'])) ?></small>
                                                </td>
                                                <td>
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                                        <input type="hidden" name="action" value="update_order">
                                                        <input type="hidden" name="image_id" value="<?= (int)$image['id'] ?>">
                                                        <input type="number" name="display_order" 
                                                               value="<?= (int)$image['display_order'] ?>" 
                                                               class="form-control form-control-sm" 
                                                               style="width: 80px;"
                                                               onchange="this.form.submit()">
                                                    </form>
                                                </td>
                                                <td>
                                                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this slider image?');" class="d-inline">
                                                        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                                                        <input type="hidden" name="action" value="delete_slider_image">
                                                        <input type="hidden" name="image_id" value="<?= (int)$image['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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

            <!-- Preview Section -->
            <div class="card slider-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye"></i> Live Preview
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($slider_images)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Upload slider images to see preview.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Slider is active with <?= count($slider_images) ?> images.
                        </div>
                        <div class="preview-section">
                            <p class="fw-bold text-primary mb-3">Current Slider Order:</p>
                            <div class="row">
                                <?php foreach ($slider_images as $index => $image): ?>
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <div class="card">
                                        <img src="../<?= h($image['image_url']) ?>" 
                                             class="card-img-top preview-image"
                                             onerror="this.src='../images/placeholder.jpg'"
                                             alt="Slider Image <?= $index + 1 ?>">
                                        <div class="card-body text-center">
                                            <small class="text-muted">Order: <?= $image['display_order'] ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    
    <script>
        // File upload and preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('sliderImage');
            const fileUploadArea = document.getElementById('fileUploadArea');
            const fileName = document.getElementById('fileName');
            const imagePreview = document.getElementById('imagePreview');
            
            // File input change event
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    fileName.textContent = file.name;
                    updatePreview(file);
                }
            });
            
            // Drag and drop functionality
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                fileUploadArea.classList.add('dragover');
            });
            
            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');
            });
            
            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    fileName.textContent = files[0].name;
                    updatePreview(files[0]);
                }
            });
            
            // Click to select file
            fileUploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            function updatePreview(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML = `
                            <img src="${e.target.result}" class="preview-image" alt="Preview">
                            <div class="mt-2 small text-muted">
                                ${file.name} (${(file.size / 1024).toFixed(1)} KB)
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.innerHTML = '<p class="text-danger">Please select a valid image file.</p>';
                }
            }
        });
    </script>
</body>
</html>