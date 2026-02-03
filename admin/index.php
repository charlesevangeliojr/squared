<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Squared Admin</title>
    <link rel="icon" type="image/png" href="../images/Squared_Logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dark-green: #0D5B11;
            --medium-green: #187019;
            --light-green: #69B41E;
            --lime-green: #8DC71E;
            --accent-color: #B88D53;
            --light-bg: #f8fbf9;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f9f6 0%, #e8f4e9 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #333;
            overflow-x: hidden;
        }
        
        .navbar-branding {
            background: linear-gradient(135deg, var(--dark-green) 0%, var(--medium-green) 100%);
            box-shadow: 0 4px 20px rgba(13, 91, 17, 0.15);
            padding: 15px 25px;
            display: flex;
            align-items: center;
            z-index: 1030;
            position: relative;
        }
        
        .navbar-branding::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--light-green), var(--lime-green), var(--accent-color));
        }
        
        .navbar-branding img {
            height: 42px;
            margin-right: 15px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        
        .brand-name {
            font-weight: 800;
            font-size: 1.5rem;
            color: white;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            position: relative;
        }
        
        .background-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--dark-green);
            top: -150px;
            right: -100px;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--light-green);
            bottom: -80px;
            left: -80px;
        }
        
        .shape-3 {
            width: 150px;
            height: 150px;
            background: var(--accent-color);
            top: 50%;
            left: 10%;
        }
        
        .card-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(13, 91, 17, 0.12);
            overflow: hidden;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(13, 91, 17, 0.18);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--dark-green) 0%, var(--medium-green) 100%);
            color: white;
            font-weight: 800;
            font-size: 1.6rem;
            text-align: center;
            padding: 25px;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            transform: translateX(-100%);
        }
        
        .card:hover .card-header::before {
            animation: shine 1.5s ease;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .card-body {
            padding: 35px;
        }
        
        .form-label {
            font-weight: 700;
            color: #444;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 8px;
            color: var(--medium-green);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 15px 20px 15px 45px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
            font-size: 1rem;
            background-color: #f9fdfa;
            height: 55px;
        }
        
        .form-control:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.25rem rgba(105, 180, 30, 0.2);
            background-color: white;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-green);
            font-size: 1.1rem;
            z-index: 5;
        }
        
        .btn-login {
            background: linear-gradient(to right, var(--light-green), var(--lime-green));
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            margin-top: 10px;
            height: 55px;
            box-shadow: 0 4px 15px rgba(105, 180, 30, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: 0.5s;
        }
        
        .btn-login:hover {
            background: linear-gradient(to right, var(--medium-green), var(--light-green));
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(105, 180, 30, 0.4);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
        
        #loginResult {
            min-height: 24px;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: #777;
            font-size: 0.9rem;
        }
        
        .features {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .feature-item {
            text-align: center;
            flex: 1;
            padding: 0 10px;
        }
        
        .feature-icon {
            font-size: 1.5rem;
            color: var(--medium-green);
            margin-bottom: 8px;
        }
        
        .feature-text {
            font-size: 0.85rem;
            color: #666;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #777;
            cursor: pointer;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--medium-green);
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .navbar-branding {
                padding: 12px 15px;
            }
            
            .navbar-branding img {
                height: 35px;
            }
            
            .brand-name {
                font-size: 1.3rem;
            }
            
            .card-body {
                padding: 25px 20px;
            }
            
            .card-header {
                font-size: 1.4rem;
                padding: 20px;
            }
            
            .features {
                flex-direction: column;
                gap: 15px;
            }
            
            .feature-item {
                padding: 0;
            }
        }
        
        /* Animation for form elements */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card-body > * {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .card-body > *:nth-child(1) { animation-delay: 0.1s; }
        .card-body > *:nth-child(2) { animation-delay: 0.2s; }
        .card-body > *:nth-child(3) { animation-delay: 0.3s; }
        .card-body > *:nth-child(4) { animation-delay: 0.4s; }
        .card-body > *:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>

<body>

    <!-- Branding Navbar -->
    <div class="navbar-branding fixed-top">
        <img src="../images/Squared_Logo.png" alt="Squared Logo">
        <span class="brand-name">Squared Admin</span>
    </div>

    <!-- Background Shapes -->
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <!-- Login Form -->
    <div class="main-container">
        <div class="card-container">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-lock me-2"></i>Admin Login
                </div>
                
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" method="POST">
                        <div class="input-group">
                            <span class="input-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                        
                        <div class="input-group">
                            <span class="input-icon">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <button type="submit" class="btn btn-login w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                        </button>
                    </form>

                    <div id="loginResult" class="text-danger text-center mt-3"></div>
                    
                    <div class="features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">Secure Access</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="feature-text">Fast Performance</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="feature-text">Analytics Ready</div>
                        </div>
                    </div>
                    
                    <div class="login-footer">
                        <i class="fas fa-copyright me-1"></i> <?php echo date("Y"); ?> Squared Admin. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const resultBox = document.getElementById('loginResult');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Authenticating...';
            resultBox.textContent = '';
            resultBox.className = 'text-center mt-3';

            // Try first login endpoint
            fetch('php/login.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    resultBox.className = 'text-success text-center mt-3';
                    resultBox.innerHTML = '<i class="fas fa-check-circle me-2"></i>Login successful! Redirecting...';
                    setTimeout(() => {
                        window.location.href = data.redirect || 'php/login.php';
                    }, 1000);
                } else {
                    // Fallback to second login endpoint
                    fetch('admin/php/login.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data2 => {
                        if (data2.success) {
                            resultBox.className = 'text-success text-center mt-3';
                            resultBox.innerHTML = '<i class="fas fa-check-circle me-2"></i>Login successful! Redirecting...';
                            setTimeout(() => {
                                window.location.href = data2.redirect || 'admin/php/login.php';
                            }, 1000);
                        } else {
                            resultBox.className = 'text-danger text-center mt-3';
                            resultBox.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${data2.message || 'Login failed. Please check your credentials.'}`;
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    })
                    .catch(() => {
                        resultBox.className = 'text-danger text-center mt-3';
                        resultBox.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Backup login service error.';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                }
            })
            .catch(() => {
                resultBox.className = 'text-danger text-center mt-3';
                resultBox.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Primary login service error.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
        
        // Password visibility toggle
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Add focus effects to inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>

</body>
</html>