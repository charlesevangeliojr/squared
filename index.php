<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="wEdwk8iKmgviE4W-Q8PFCtYfItnkS0B_jTWXk66AQNw">
    <title>Squared - QR Attendance</title>
    <link rel="icon" type="image/png" href="images/Squared_Logo.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#4caf50">
    <!-- Primary Meta Tags -->
    <meta name="title" content="Squared QR — Fast QR Attendance System">
    <meta name="description" content="Track attendance easily with QR scanning. Fast. Secure. Student-friendly.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://squared-qr.duckdns.org/">
    <meta property="og:title" content="Squared QR — Fast QR Attendance System">
    <meta property="og:description" content="Track attendance easily with QR scanning. Fast. Secure. Student-friendly.">
    <meta property="og:image" content="https://squared-qr.duckdns.org/images/Squared_Logo.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://squared-qr.duckdns.org/">
    <meta property="twitter:title" content="Squared QR — Fast QR Attendance System">
    <meta property="twitter:description"
        content="Track attendance easily with QR scanning. Fast. Secure. Student-friendly.">
    <meta property="twitter:image" content="https://squared-qr.duckdns.org/images/Squared_Logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">


</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="images/Squared_Logo.png" alt="Squared Logo"> Squared
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal"
                            data-bs-target="#registerModal">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>
  
    
<div class="hero">
  <div class="slider" id="slider">
    <?php
    include 'php/config.php';
    
    // Fetch slider images from database ordered by display_order
    $sql = "SELECT image_url FROM slider_images ORDER BY display_order ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Display images from database
        while($image = $result->fetch_assoc()) {
            echo '<div class="slide" style="background-image: url(\'' . $image['image_url'] . '\');"></div>';
        }
    } else {
        // Fallback images if no images in database
        echo '<div class="slide" style="background-image: url(\'images/Cover.jpg\');"></div>';
        echo '<div class="slide" style="background-image: url(\'images/Cover1.jpg\');"></div>';
        echo '<div class="slide" style="background-image: url(\'images/Cover2.jpg\');"></div>';
    }
    
    ?>
  </div>

  <div class="overlay"></div>
  
<?php include 'clock.php'; ?>
  
  <div class="content">
    <h1>Transforming Queues, Elevating Transactions</h1>
    <p>Track and manage attendance with ease using Squared's QR scanning system.</p>
    <button class="btn btn-hero" data-bs-toggle="modal" data-bs-target="#registerModal">Register Now!</button>
  </div>
</div>

<!--just add include here -->
<div id="student-counts-wrapper" class="text-light py-3 mt-3">
  <div class="d-flex justify-content-center flex-wrap">
    <?php include 'student_count.php'; ?>
  </div>
</div>

<?php include 'avatars.php'; ?>

    <img src="images/Squared.png" alt="Squared" class="w-100 mt-4" style="height: auto;">

<div class="ratio ratio-16x9">
  <iframe
    src="https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2Freel%2F1427204562748792%2F&show_text=true&width=560&t=0"
    allowfullscreen
    frameborder="0"
    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
  </iframe>
</div>


<?php include 'notify_landing.php'; ?>
<?php include 'programs.php'; ?>
<?php include 'about.php'; ?>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Student Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" method="POST" action="php/login.php">
                        <div class="mb-3">
                            <label for="studentId" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="studentId" name="student_id"
                                autocomplete="username" required>
                        </div>
                        <div class="mb-2">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    autocomplete="current-password" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password', 'togglePasswordIcon')">
                                    <i id="togglePasswordIcon" class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Remember Me & Forgot Password -->
                        <div id="loginMessage" class="text-danger mb-3"></div>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember Me</label>
                            </div>
                            <a href="forgotpass.php" class="text-primary">Forgot Password?</a>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" action="php/register.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Choose an Avatar <span class="text-danger"></span></label>
                            <div class="avatar-option">
                                <label>
                                    <input type="radio" name="avatar" value="JOY" hidden required>
                                    <img src="avatars/JOY.png" alt="JOY">
                                    <div class="text-center">JOY</div>
                                </label>
                                <label>
                                    <input type="radio" name="avatar" value="SEVI" hidden required>
                                    <img src="avatars/SEVI.png" alt="SEVI">
                                    <div class="text-center">SEVI</div>
                                </label>
                                <label>
                                    <input type="radio" name="avatar" value="SAMANTHA" hidden required>
                                    <img src="avatars/SAMANTHA.png" alt="SAMANTHA">
                                    <div class="text-center">SAMANTHA</div>
                                </label>
                                <label>
                                    <input type="radio" name="avatar" value="ZEKE" hidden required>
                                    <img src="avatars/ZEKE.png" alt="ZEKE">
                                    <div class="text-center">ZEKE</div>
                                </label>
                            </div>
                            <p id="avatarError" class="text-danger" style="display: none;"></p>
                        </div>
                        <div class="mb-3">
                            <label for="registerStudentId" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="registerStudentId" name="student_id" required>
                        </div>
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
                        <div class="mb-3">
                            <label for="sex" class="form-label">Sex</label>
                            <select class="form-control" id="sex" name="sex" required>
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="program" class="form-label">Program</label>
                            <select class="form-control" id="program" name="program" onchange="updateCourses()"
                                required>
                                <option value="">Select Program</option>
                                <option value="ITE">Information Technology Education - ITE</option>
                                <option value="CELA">College of Education and Liberal Arts - CELA</option>
                                <option value="CBA">College of Business Administration - CBA</option>
                                <option value="HME">Hospitality Management Education - HME</option>
                                <option value="CJE">Criminal Justice Education - CJE</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course</label>
                            <select class="form-control" id="course" name="course" required>
                                <option value="">Select Course</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="gmail" class="form-label">Gmail</label>
                            <input type="email" class="form-control" id="gmail" name="email" required>
                            <p id="emailError" class="text-danger" style="display: none;">⚠ Please enter a valid email.
                            </p>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="registerPassword" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="registerConfirmps" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="registerConfirmps" name="confirmps"
                                    required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('registerConfirmps', 'togglePasswordIcon')">
                                    <i id="togglePasswordIcon" class="bi bi-eye"></i>
                                </button>
                            </div>
                            <p id="matchMessage" class="text-danger" style="display: none;"></p>
                        </div>
                        <!-- Terms Checkbox -->
                        <div class="mb-3 form-check text-center">
                            <input type="checkbox" class="form-check-input" id="termsCheckbox" required>
                            <label class="form-check-label" for="termsCheckbox">
                                I agree to the
                                <a href="#" onclick="openTermsModal()">Terms and Conditions</a> and
                                <a href="#" onclick="openPrivacyModal()">Privacy Policy</a>.
                            </label>
                        </div>

                        <!-- Register Button -->
                        <button type="submit" class="btn btn-secondary w-100" id="registerButton"
                            disabled>Register</button>

                </div>
            </div>
        </div>

        <!-- Terms and Conditions Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title w-100 text-center" id="termsModalLabel">Terms and Conditions</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center px-4">
                        <p class="text-start">Welcome to <strong>Squared</strong>, a student-led initiative of Davao
                            Central College. By registering and using our services, you agree to the following terms:
                        </p>
                        <p class="text-start"><strong>1. Membership</strong><br>All enrolled students of DCC are
                            eligible to register. Registration grants you access to a QR Code (SQCC) and Control Number
                            (SCN) to engage in DCC events and services.</p>
                        <p class="text-start"><strong>2. Use of QR Codes</strong><br>Your personal QR Code may be
                            scanned for attendance, evaluations, transactions, and other campus-related services.
                            Misuse, forgery, or tampering with your code may result in disciplinary action.</p>
                        <p class="text-start"><strong>3. Responsibilities</strong><br>As a registered member, you agree
                            to use Squared services responsibly, follow DCC’s conduct guidelines, and avoid interfering
                            with the operation of any Squared systems.</p>
                        <p class="text-start"><strong>4. Updates & Availability</strong><br>Services may evolve over
                            time. Squared reserves the right to update features or policies to improve performance and
                            user experience. Notifications will be posted on the site when necessary.</p>
                        <p class="text-start"><strong>5. Student Conduct</strong><br>Participation in Squared events
                            requires students to behave with integrity, respect, and cooperation with fellow students,
                            faculty, and system administrators.</p>
                        <p class="text-start">If you do not agree with any part of these terms, please refrain from
                            registering or using Squared services.</p>
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy Policy Modal -->
        <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title w-100 text-center" id="privacyModalLabel">Privacy Policy</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center px-4">
                        <p class="text-start"><strong>Privacy Policy</strong> explains how Squared collects, uses, and
                            protects the information you provide through the registration and QR Code system.</p>
                        <p class="text-start"><strong>1. Information Collected</strong><br>We collect personal
                            information such as your name, student ID, program, email, and event attendance. This data
                            is used solely to facilitate Squared services and improve event operations.</p>
                        <p class="text-start"><strong>2. QR Code Usage</strong><br>Your QR Code is uniquely tied to your
                            student record and is only used for official DCC purposes like attendance and event
                            tracking. It is never shared with unauthorized individuals.</p>
                        <p class="text-start"><strong>3. Data Security</strong><br>Squared employs secure systems to
                            store and manage your information. Only authorized personnel have access to sensitive data,
                            and all handling complies with institutional standards.</p>
                        <p class="text-start"><strong>4. Consent</strong><br>By registering for Squared, you give your
                            consent for your data to be collected and processed according to this policy. You may
                            request to review or delete your data by contacting the Squared committee.</p>
                        <p class="text-start"><strong>5. Third-Party Sharing</strong><br>Squared does not sell or share
                            your information with third parties unless legally required or with explicit student
                            consent.</p>
                        <p class="text-start">If you have any concerns about your data privacy, please contact the
                            Office of Student Affairs and Services (OSAS) or the Squared Committee.</p>
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Success/Error Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center"> <!-- Center text in modal -->
                <div class="modal-header d-flex flex-column align-items-center border-0">
                    <h5 class="modal-title w-100" id="messageModalLabel">Notification</h5>
                </div>
                <div class="modal-body">
                    <p id="modalMessage"></p>
                </div>
                <div class="modal-footer d-flex justify-content-center border-0">
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'founders.php'; ?>

    <footer class="footer-armygreen text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-3">Follow us on Social Media</p>
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <a href="https://www.facebook.com/profile.php?id=61557988749271" target="_blank"
                    class="text-light text-decoration-none social-link">
                    <i class="bi-facebook me-1"></i> Facebook
                </a>
                <a href="https://www.instagram.com/squared.qr?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                    target="_blank" class="text-light text-decoration-none social-link">
                    <i class="bi-instagram me-1"></i> Instagram
                </a>
                <a href="https://www.tiktok.com/@squared_qr" target="_blank"
                    class="text-light text-decoration-none social-link">
                    <i class="bi-tiktok me-1"></i> TikTok
                </a>
                <a href="https://www.youtube.com/@Squared-qr" target="_blank"
                    class="text-light text-decoration-none social-link">
                    <i class="bi-youtube me-1"></i> YouTube
                </a>
            </div>
            <hr class="bg-light my-3">
            <small>&copy;
                <?= date('Y') ?> Squared. All rights reserved.
            </small>
            <!-- <small class="d-block mt-1">
                Idea by
                <a href="https://www.facebook.com/robertopelaeztacbobo" target="_blank"
                    class="text-white text-decoration-underline fw-bold">
                    Roberto Jr. P. Tacbobo
                </a>
            </small> -->
                        <small class="d-block mt-1">
                Developed by
                <a href="https://www.facebook.com/chazy.mushie" target="_blank"
                    class="text-white text-decoration-underline fw-bold">
                    Charles S. Evangelio Jr.
                </a>
            </small>
        </div>
    </footer>

    <!-- Install App Modal (hidden by default) -->
    <div class="modal fade" id="installModal" tabindex="-1" aria-labelledby="installModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="installModalLabel">Install App</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Would you like to install Squared QR to your device for quick access?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button id="installButton" class="btn btn-success">Install App</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let deferredPrompt; // To hold the beforeinstallprompt event

        // Check if the app is installed already
        function isAppInstalled() {
            // For Android, checking the 'beforeinstallprompt' event presence
            return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        }

        // Detect mobile device (Android or iOS)
        function isMobile() {
            return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        }

        // Listen for the beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            // Check if the app is already installed or not on mobile
            if (isAppInstalled() || !isMobile()) {
                return;  // Don't show the modal if the app is installed or it's not a mobile device
            }

            // Prevent the default installation prompt
            e.preventDefault();
            // Save the event to trigger it later
            deferredPrompt = e;

            // Show the install modal
            const installModal = new bootstrap.Modal(document.getElementById('installModal'));
            installModal.show(); // Show the modal
        });

        // When the user clicks the "Install App" button inside the modal
        document.getElementById('installButton').addEventListener('click', () => {
            // Show the install prompt
            deferredPrompt.prompt();

            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                } else {
                    console.log('User dismissed the install prompt');
                }
                deferredPrompt = null;
                // Close the modal after the prompt
                const installModal = bootstrap.Modal.getInstance(document.getElementById('installModal'));
                installModal.hide();
            });
        });
    </script>
    <script src="js/pass.js"></script>
    <script src="js/avatarerror.js"></script>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function (event) {
            event.preventDefault();

            let studentId = document.getElementById("studentId").value.trim();
            let password = document.getElementById("password").value.trim();
            let loginMessage = document.getElementById("loginMessage");

            fetch("php/login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `student_id=${encodeURIComponent(studentId)}&password=${encodeURIComponent(password)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        window.location.href = "home.php"; // Redirect on successful login
                    } else {
                        loginMessage.textContent = data.message;
                    }
                })
                .catch(error => {
                    loginMessage.textContent = "An error occurred. Please try again.";
                });
        });
    </script>
    <script>
        fetch('php/session.php')
            .then(response => response.json())
            .then(data => {
                if (data.show_modal) {
                    document.getElementById("modalMessage").innerHTML = data.message;
                    document.getElementById("messageModal").classList.add(data.type === "success" ? "text-success" : "text-danger");
                    var messageModal = new bootstrap.Modal(document.getElementById("messageModal"));
                    messageModal.show();
                }
            });

        document.querySelector("form").addEventListener("submit", function (event) {
            const avatarSelected = document.querySelector('input[name="avatar"]:checked');
            if (!avatarSelected) {
                document.getElementById("avatarError").style.display = "block";
                event.preventDefault(); // Prevent form submission
            }
        });

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
            const program = document.getElementById("program").value;
            const courseDropdown = document.getElementById("course");
            courseDropdown.innerHTML = "<option value=''>Select Course</option>"; // Reset options
            if (program && courses[program]) {
                courses[program].forEach(course => {
                    const option = document.createElement("option");
                    option.text = course;
                    courseDropdown.add(option);
                });
            }
        }
    </script>

    <script>
        const termsCheckbox = document.getElementById("termsCheckbox");
        const registerButton = document.getElementById("registerButton");

        termsCheckbox.addEventListener('change', function () {
            if (this.checked) {
                registerButton.disabled = false;
                registerButton.classList.remove("btn-secondary");
                registerButton.classList.add("btn-success");
            } else {
                registerButton.disabled = true;
                registerButton.classList.add("btn-secondary");
                registerButton.classList.remove("btn-success");
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Method 1: If using a hash (like #login)
        const hash = window.location.hash;
        if (hash === "#login") {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }

        // Method 2: If using ?modal=login
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('modal') === 'login') {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }
    </script>
    <script>
        function openTermsModal() {
            const terms = new bootstrap.Modal(document.getElementById('termsModal'), {
                backdrop: 'static',
                focus: true
            });
            terms.show();
        }

        function openPrivacyModal() {
            const privacy = new bootstrap.Modal(document.getElementById('privacyModal'), {
                backdrop: 'static',
                focus: true
            });
            privacy.show();
        }
    </script>

<script>
  const slider = document.getElementById('slider');
  const slides = slider.children;
  const totalSlides = slides.length;

  // Clone the first slide and append it
  const firstClone = slides[0].cloneNode(true);
  slider.appendChild(firstClone);

  let index = 0;

  function slideNext() {
    index++;
    slider.style.transition = 'transform 1s ease-in-out';
    slider.style.transform = `translateX(-${index * 100}%)`;

    if (index === totalSlides) {
      setTimeout(() => {
        slider.style.transition = 'none';
        slider.style.transform = 'translateX(0)';
        index = 0;
      }, 600); // Match with transition duration
    }
  }

  setInterval(slideNext, 5000); // Slide every 5 seconds
</script>


<script>
// Animate numbers from start to end smoothly
function animateValue(id, start, end, duration) {
    if (start === end) return;
    let range = end - start;
    let current = start;
    let increment = end > start ? 1 : -1;
    let stepTime = Math.max(Math.floor(duration / Math.abs(range)), 20);
    let obj = document.getElementById(id);
    let timer = setInterval(function() {
        current += increment;
        obj.textContent = current.toLocaleString();
        if (current === end) {
            clearInterval(timer);
        }
    }, stepTime);
}

// Update all displayed numbers with animation
function updateNumbers(newData) {
    animateValue('total-students', parseInt(document.getElementById('total-students').textContent.replace(/,/g, '')), newData.total, 800);
    animateValue('count-cba', parseInt(document.getElementById('count-cba').textContent.replace(/,/g, '')), newData.CBA, 800);
    animateValue('count-cela', parseInt(document.getElementById('count-cela').textContent.replace(/,/g, '')), newData.CELA, 800);
    animateValue('count-cje', parseInt(document.getElementById('count-cje').textContent.replace(/,/g, '')), newData.CJE, 800);
    animateValue('count-hme', parseInt(document.getElementById('count-hme').textContent.replace(/,/g, '')), newData.HME, 800);
    animateValue('count-ite', parseInt(document.getElementById('count-ite').textContent.replace(/,/g, '')), newData.ITE, 800);
}

// Fetch new data as JSON and animate the changes
function fetchAndAnimate() {
    fetch('student_counts_json.php')
        .then(response => response.json())
        .then(data => {
            updateNumbers(data);
        })
        .catch(err => console.error('Failed to fetch data', err));
}

// Auto-refresh every 30 seconds
setInterval(fetchAndAnimate, 1000);
</script>
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('js/sw.js');
  }
</script>

<script>
  function updateClock() {
    const now = new Date();
    const clockElement = document.getElementById("liveClock");

    const options = {
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
      hour12: true,
    };

    clockElement.textContent = now.toLocaleTimeString('en-US', options);
  }

  setInterval(updateClock, 1000); // Update every second
  updateClock(); // Initial call
</script>

</body>

</html>