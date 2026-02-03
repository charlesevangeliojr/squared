<?php
session_start();
require_once 'php/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if student is an allowed scanner
$scanner_sql = "SELECT * FROM event_scanners WHERE student_id = ? AND status = 'allow'";
$scanner_stmt = $conn->prepare($scanner_sql);
$scanner_stmt->bind_param("s", $student_id);
$scanner_stmt->execute();
$scanner_result = $scanner_stmt->get_result();
$isScanner = $scanner_result->num_rows > 0;

// Fetch student data
$sql = "SELECT student_id, first_name, middle_name, last_name, suffix, sex, avatar, program, course, email, created_at, qr_code 
        FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Is there any active (ON) award?
$hasVoting = false;
if (isset($conn)) {
  $q = $conn->query("SELECT COUNT(*) AS c FROM pca_awards WHERE status='on'");
  if ($q && ($row = $q->fetch_assoc())) $hasVoting = ((int)$row['c'] > 0);
  if ($q) $q->free();
}

if (!$student) {
    echo "Student not found.";
    exit();
}

// Format the filename (e.g., "20231234_Dela_Cruz_Juan_A.png")
$student_name = strtoupper($student['last_name']) . "_" . strtoupper($student['first_name']);
if (!empty($student['middle_name'])) {
    $student_name .= "_" . strtoupper(substr($student['middle_name'], 0, 1));
}
if (!empty($student['suffix'])) {
    $student_name .= "_" . strtoupper($student['suffix']);
}
$filename = $student_id . "_" . $student_name . ".png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared QR-Card</title>
    <link rel="icon" type="image/png" href="images/Squared_Logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="css/qrcard.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="images/Squared_Logo.png" alt="Squared Logo"> Squared Card
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="qrcard.php">QR-Card</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notify.php">Announcements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="myattendancerecord.php">My Attendance</a>
                </li>
                <?php if ($hasVoting): ?>
  <li class="nav-item">
    <a class="nav-link" href="voting.php">Vote</a>
  </li>
<?php endif; ?>
                <?php if ($isScanner): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="event_scanner.php">Scan</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="php/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div id="profile-card" class="profile-container">
        <div class="qr-code">
            <img src="qr_images/<?php echo htmlspecialchars($student['qr_code']); ?>.png" alt="QR Code">
        </div>
        <div class="student-name">
            <span class="last-name"><?php echo htmlspecialchars(strtoupper($student['last_name'])); ?></span><br>
            <span class="first-name">
                <?php echo htmlspecialchars(strtolower($student['first_name'])) . ' ' . (!empty($student['middle_name']) ? strtoupper(substr($student['middle_name'], 0, 1)) . '.' : '') . ' ' . htmlspecialchars(strtolower($student['suffix'])); ?>
            </span>
        </div>
        <div class="badge-container">
            <?php echo htmlspecialchars($student['student_id']); ?>
        </div>
        <img class="avatar" src="avatars/<?php echo htmlspecialchars($student['avatar']); ?>.png" alt="Avatar">
        <div class="student-info">
            <p>PROGRAM:<span><?php echo htmlspecialchars($student['program']); ?></span></p>
            <p>COURSE:<span><?php echo htmlspecialchars($student['course']); ?></span></p>
        </div>
    </div>
    <div class="button-container">
        <button class="btn btn-green btn-lg" onclick="downloadProfileCard()">Download Image</button>
    </div>

    <script>
        function downloadProfileCard() {
            const profileCard = document.getElementById('profile-card');
            html2canvas(profileCard, {
                scale: 9, // High resolution
                useCORS: true // Ensure external images load properly
            }).then(canvas => {
                let link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = "<?php echo $filename; ?>";
                link.click();
            });
        }
    </script>

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('js/sw.js');
  }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
