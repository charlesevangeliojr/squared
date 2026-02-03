<?php
require 'php/config.php';
require 'phpqrcode/qrlib.php';

// Use the qr_images directory in the project
$qr_output_dir = "qr_images/";

// Ensure output directory exists with proper permissions
if (!is_dir($qr_output_dir)) {
    if (!mkdir($qr_output_dir, 0755, true)) {
        die("❌ Failed to create directory: $qr_output_dir");
    }
    echo "✅ Directory created: $qr_output_dir\n";
} else {
    echo "✅ Directory exists: $qr_output_dir\n";
}

// Check if directory is writable
if (!is_writable($qr_output_dir)) {
    die("❌ Directory is not writable: $qr_output_dir");
}

echo "✅ Directory is writable\n";

// Fetch all student IDs and qr_code fields
$sql = "SELECT student_id, qr_code FROM students";
$result = $conn->query($sql);

if (!$result) {
    die("❌ Query failed: " . $conn->error);
}

if ($result->num_rows === 0) {
    die("❌ No students found in database.");
}

echo "✅ Found " . $result->num_rows . " students\n";

// QR settings
$matrixPointSize = 8; // Reduced for better compatibility
$margin = 1;
$success_count = 0;
$error_count = 0;

while ($row = $result->fetch_assoc()) {
    $student_id = trim($row['student_id']);
    $qr_code_filename = trim($row['qr_code']);
    
    // Skip if student_id or qr_code is empty
    if (empty($student_id) || empty($qr_code_filename)) {
        echo "❌ Skipping - Empty student_id or qr_code: $student_id\n";
        $error_count++;
        continue;
    }

    // Sanitize filename to be safe
    $safe_filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $qr_code_filename);
    $full_path = $qr_output_dir . $safe_filename . ".png";

    try {
        // Generate QR with student_id as content, but qr_code as filename
        QRcode::png($student_id, $full_path, QR_ECLEVEL_L, $matrixPointSize, $margin);
        
        // Verify the file was created
        if (file_exists($full_path) && filesize($full_path) > 0) {
            echo "✅ QR saved to $full_path for student ID: $student_id\n";
            $success_count++;
        } else {
            echo "❌ File not created or empty: $full_path\n";
            $error_count++;
        }
        
    } catch (Exception $e) {
        echo "❌ Error generating QR for $student_id: " . $e->getMessage() . "\n";
        $error_count++;
    }
}

echo "\n📊 Generation Summary:\n";
echo "✅ Success: $success_count\n";
echo "❌ Errors: $error_count\n";
echo "📋 Total processed: " . ($success_count + $error_count) . "\n";

$conn->close();
?>