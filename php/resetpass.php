<?php
include 'config.php';
$message = "";

// Check if student_id is provided in the URL
if (!isset($_GET['student_id'])) {
    header("Location: forgotpass.php"); // Redirect back if no ID
    exit();
}

$student_id = $_GET['student_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    } else {
        // Hash the new password before storing it
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $sql = "UPDATE students SET password_hash=? WHERE student_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $student_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Password updated successfully. <a href='../index.php'>Login here</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to update password. Try again.</div>";
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h3>Reset Password</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Update Password</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="index.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
