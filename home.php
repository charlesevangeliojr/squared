<?php
session_start();
require_once 'php/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index");
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

// Is there any active (ON) award?
$hasVoting = false;
if (isset($conn)) {
  $q = $conn->query("SELECT COUNT(*) AS c FROM pca_awards WHERE status='on'");
  if ($q && ($row = $q->fetch_assoc())) $hasVoting = ((int)$row['c'] > 0);
  if ($q) $q->free();
}

// Fetch student data
$sql = "SELECT student_id, first_name, middle_name, last_name, suffix, sex, avatar, program, course, email, created_at, qr_code 
        FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squared Home</title>
    <link rel="icon" type="image/png" href="images/Squared_Logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="images/Squared_Logo.png" alt="Squared Logo"> Squared Home
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

    <div class="container mt-4 pt-5">
        <div class="card profile-card mx-auto">
            <div class="card-head profile-header">
                <h3>Welcome,
                    <?php echo htmlspecialchars($student['first_name']); ?>!
                </h3>
            </div>
            <div class="card-body text-center">
                <img src="avatars/<?php echo htmlspecialchars($student['avatar']); ?>.png" class="profile-avatar"
                    alt="Avatar">
                <h5>
                    <?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . $student['last_name'] . ($student['suffix'] ? ' ' . $student['suffix'] : '')); ?>
                </h5>
                <p class="text-muted">
                    <?php echo htmlspecialchars($student['program']); ?> -
                    <?php echo htmlspecialchars($student['course']); ?>
                </p>
                <hr>
                <p><strong>Student ID:</strong>
                    <?php echo htmlspecialchars($student['student_id']); ?>
                </p>
                <p><strong>Sex:</strong>
                    <?php echo htmlspecialchars($student['sex']); ?>
                </p>
                <p><strong>Email:</strong>
                    <?php echo htmlspecialchars($student['email']); ?>
                </p>
                <p><strong>Joined on:</strong>
                    <?php echo date("F j, Y", strtotime($student['created_at'])); ?>
                </p>
            </div>

            <div class="text-center mt-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#profileModal" class="btn btn-greend">Edit
                    Profile</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#pass" class="btn btn-greend">
                    Change Pass
                </a>
            </div>
        </div>

        <!-- QR Card -->
        <div class="card qr-card mx-auto mt-4">
            <div class="card-head qr-header">
                <h4>Your QR Code</h4>
            </div>
            <div class="card-body text-center">
                <img src="qr_images/<?php echo htmlspecialchars($student['qr_code']); ?>.png" class="qr-image" alt="QR Code">
                <p class="mt-3">Use this QR code for attendance and verification purposes.</p>
            </div>

            <div class="text-center mt-3">
                <a href="qrcard.php" class="btn btn-greend">Download QR-Card</a>
            </div>
        </div>

<!-- Change Password Modal -->
<div class="modal fade" id="pass" tabindex="-1" aria-labelledby="passLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="php/changepass.php" method="POST">
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" 
                                onclick="togglePassword('currentPassword', 'toggleCurrentPass')">
                                <i id="toggleCurrentPass" class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" 
                                onclick="togglePassword('confirmPassword', 'toggleConfirmPass')">
                                <i id="toggleConfirmPass" class="bi bi-eye"></i>
                            </button>
                        </div>
                        <p id="matchMessage" class="text-danger" style="display: none;"></p>
                    </div>

                    <!-- Save Changes Button -->
                    <div class="justify-content-center">
                        <button type="submit" class="btn btn-success w-100">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


        <!-- Edit Profile Modal -->
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="registerForm" action="php/update.php" method="POST">

                            <!-- Avatar Selection -->
                            <div class="mb-3">
                                <label class="form-label">Choose an Avatar</label>
                                <div class="avatar-option">
                                    <label>
                                        <input type="radio" name="avatar" value="JOY" hidden required>
                                        <img src="avatars/JOY.jpg" alt="JOY">
                                        <div class="text-center">JOY</div>
                                    </label>
                                    <label>
                                        <input type="radio" name="avatar" value="SEVI" hidden required>
                                        <img src="avatars/SEVI.jpg" alt="SEVI">
                                        <div class="text-center">SEVI</div>
                                    </label>
                                    <label>
                                        <input type="radio" name="avatar" value="SAMANTHA" hidden required>
                                        <img src="avatars/SAMANTHA.jpg" alt="SAMANTHA">
                                        <div class="text-center">SAMANTHA</div>
                                    </label>
                                    <label>
                                        <input type="radio" name="avatar" value="ZEKE" hidden required>
                                        <img src="avatars/ZEKE.jpg" alt="ZEKE">
                                        <div class="text-center">ZEKE</div>
                                    </label>
                                </div>
                            </div>

                            <!-- Student ID (Read-only) -->
                            <div class="mb-3">
                                <label for="registerStudentId" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="registerStudentId" name="student_id"
                                    readonly>
                            </div>

                            <!-- Personal Information -->
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middle_name">
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>

                            <!-- Suffix -->
                            <div class="mb-3">
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
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label for="course" class="form-label">Course</label>
                                <select id="course" name="course" class="form-control">
                                    <option value="">Select Course</option>
                                </select>

                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="gmail" class="form-label">Gmail</label>
                                <input type="email" class="form-control" id="gmail" name="email" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="js/changepass.js"></script>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    fetch('php/fetch.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            document.getElementById("registerStudentId").value = data.student_id;
            document.getElementById("firstName").value = data.first_name;
            document.getElementById("middleName").value = data.middle_name || "";
            document.getElementById("lastName").value = data.last_name;
            document.getElementById("suffix").value = data.suffix || "";
            document.getElementById("sex").value = data.sex;
            
            // Set program first
            document.getElementById("program").value = data.program;
            
            // Wait until courses update, then set the correct course
            setTimeout(() => {
                document.getElementById("course").value = data.course;
            }, 100); // Slight delay to ensure the list updates

            document.getElementById("gmail").value = data.email;

            // Select the correct avatar
            let avatarInput = document.querySelector(`input[name="avatar"][value="${data.avatar}"]`);
            if (avatarInput) avatarInput.checked = true;

            // Update courses based on the selected program
            updateCourses();
        })
        .catch(error => console.error('Error fetching user data:', error));
});

    </script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    updateCourses(); // Ensure courses match the selected program on page load
});

// Function to update course list based on selected program
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

    // Get selected program and existing course value
    const selectedProgram = programDropdown.value;
    const currentCourse = courseDropdown.dataset.selected || "";

    // Clear and add the default "Select Course" option
    courseDropdown.innerHTML = "<option value=''>Select Course</option>";

    if (selectedProgram && courses[selectedProgram]) {
        courses[selectedProgram].forEach(course => {
            const option = document.createElement("option");
            option.text = course;
            option.value = course;
            if (course === currentCourse) {
                option.selected = true; // Auto-select the student's saved course
            }
            courseDropdown.add(option);
        });

        // Auto-select course if only one exists (like in ITE)
        if (courses[selectedProgram].length === 1) {
            courseDropdown.value = courses[selectedProgram][0];
        }
    }
}
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($_SESSION['message'])): ?>
    <!-- Success/Error Modal -->
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
        // Show modal when page loads
        document.addEventListener("DOMContentLoaded", function () {
            var myModal = new bootstrap.Modal(document.getElementById('messageModal'));
            myModal.show();
        });
    </script>

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('js/sw.js');
  }
</script>

    <?php
    // Remove message after displaying to prevent it from showing again on refresh
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
    <?php endif; ?>
</body>

</html>