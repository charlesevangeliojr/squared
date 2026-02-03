<?php
include 'php/config.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);

    // Check if the student ID and email exist in the database
    $sql = "SELECT student_id FROM students WHERE student_id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Redirect user to the password reset page
        header("Location: resetpass.php?student_id=$student_id");
        exit();
    } else {
        $message = "<div class='alert alert-danger text-center'>Invalid Student ID or Email.</div>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Squared</title>
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
                        <div class="forgot-password-title">Forgot Password</div>
                        <p class="small-text">Enter your Student ID and Email to proceed.</p>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control" name="student_id" required placeholder="Enter your Student ID">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required placeholder="Enter your Email">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Retrieve Password</button>
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
