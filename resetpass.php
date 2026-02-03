<?php
session_start();
include 'php/config.php';
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
        $message = "<div class='alert alert-danger text-center'>Passwords do not match.</div>";
    } else {
        // Hash the new password before storing it
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $sql = "UPDATE students SET password_hash=? WHERE student_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $student_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>Password updated successfully. <a href='index.php'>Login here</a></div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Failed to update password. Try again.</div>";
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
    <title>Reset Password | Squared</title>
    <link rel="icon" type="image/png" href="images/Squared_Logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/resetpass.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Navbar-style Branding -->
    <div class="navbar-branding">
        <img src="images/Squared_Logo.png" alt="Squared Logo">
        <span class="brand-name">Squared</span>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="">
                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <div class="forgot-password-title">Reset Password</div> 
                        <p class="small-text">Enter your new password below to continue.</p>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required placeholder="Enter your new password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" required placeholder="Confirm your new password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Password</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="index.php" class="back-to-login">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
