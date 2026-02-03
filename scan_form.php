<?php
session_start();
include 'php/config.php';

$event_id = $_GET['event_id'] ?? null;
$scanner_id = $_GET['scanner_id'] ?? null;

if (!$event_id || !$scanner_id) {
    header("Location: index.php");
    exit();
}

// Fetch event name from database
$event_name = "Event";
$stmt = $conn->prepare("SELECT event_name FROM events WHERE event_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $event_name = htmlspecialchars($row['event_name']);
    }
    $stmt->close();
}

// Determine department based on event name or use default
$department = "CBA"; // Default department
$dept_colors = [
    'CBA' => '#0D5B11',
    'CELA' => '#187C19', 
    'CJE' => '#69B41E',
    'HME' => '#8DC71E',
    'ITE' => '#B8D53D'
];

// Try to extract department from event name
$event_upper = strtoupper($event_name);
foreach(array_keys($dept_colors) as $dept) {
    if (strpos($event_upper, $dept) !== false) {
        $department = $dept;
        break;
    }
}

$dept_color = $dept_colors[$department];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>QR Attendance Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        :root {
            --primary-color: <?= $dept_color ?>;
            --dark-bg: #121212;
            --card-bg: #1e1e1e;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            position: fixed;
            background: var(--dark-bg);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        /* Main container */
        .container {
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 0;
            max-width: 100%;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-color), #0a4a0d);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .scanner-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .header-info {
            flex: 1;
        }

        .event-name {
            font-size: 16px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .scanner-id {
            font-size: 12px;
            opacity: 0.9;
        }

        .time-display {
            font-size: 14px;
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }

        /* Scanner Area */
        .scanner-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: relative;
            min-height: 0;
        }

        .scanner-frame {
            flex: 1;
            position: relative;
            background: black;
            border-radius: 20px;
            overflow: hidden;
            border: 3px solid var(--primary-color);
            box-shadow: 0 0 30px rgba(13, 91, 17, 0.3);
        }

        #qr-reader {
            width: 100%;
            height: 100%;
        }

        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            pointer-events: none;
        }

        .scanner-instruction {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            font-size: 14px;
            z-index: 2;
        }

        /* Camera Controls */
        .camera-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }

        .camera-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.7);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
        }

        .camera-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .camera-btn:active {
            transform: scale(0.95);
        }

        .camera-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(13, 91, 17, 0.5);
        }

        /* Status Bar */
        .status-container {
            background: var(--card-bg);
            margin: 16px;
            padding: 16px;
            border-radius: 15px;
            border-left: 4px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 0;
        }

        .status-icon {
            font-size: 28px;
            color: var(--primary-color);
        }

        .status-text h4 {
            font-size: 16px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .status-text p {
            font-size: 13px;
            color: var(--text-secondary);
            margin: 0;
        }

        .status-success {
            border-left-color: #28a745;
        }
        .status-success .status-icon {
            color: #28a745;
        }

        .status-error {
            border-left-color: #dc3545;
        }
        .status-error .status-icon {
            color: #dc3545;
        }

        /* Statistics */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 0 16px;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .stat-box {
            background: var(--card-bg);
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Footer */
        .footer {
            background: var(--card-bg);
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .btn-finish {
            background: linear-gradient(135deg, var(--primary-color), #0a4a0d);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Loader */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Animations */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(13, 91, 17, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(13, 91, 17, 0); }
            100% { box-shadow: 0 0 0 0 rgba(13, 91, 17, 0); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Hide scanner UI elements */
        #qr-reader__dashboard_section {
            display: none !important;
        }

        #qr-reader__scan_region {
            height: 100% !important;
        }

        #qr-reader__scan_region video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        /* Inactivity warning */
        .inactivity-note {
            text-align: center;
            font-size: 12px;
            color: var(--text-secondary);
            padding: 8px;
            margin: 0 16px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <!-- Hidden Form -->
    <form id="attendanceForm" style="display: none;">
        <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
        <input type="hidden" name="scanner_id" value="<?= htmlspecialchars($scanner_id) ?>">
        <input type="hidden" name="student_id" id="student_id">
    </form>

    <!-- Feedback Sounds (add fallback) -->
    <audio id="beepSound" preload="auto">
        <source src="fx/beep.wav" type="audio/wav">
    </audio>
    <audio id="errorSound" preload="auto">
        <source src="fx/error.wav" type="audio/wav">
    </audio>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="scanner-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="header-info">
                <div class="event-name"><?= $event_name ?></div>
                <div class="scanner-id">Scanner ID: <?= $scanner_id ?></div>
            </div>
            <div class="time-display">
                <i class="fas fa-clock"></i>
                <span id="currentTime">--:--</span>
            </div>
        </div>

        <!-- Scanner Area -->
        <div class="scanner-section">
            <div class="scanner-frame pulse">
                <div id="qr-reader"></div>
                <div class="scanner-overlay"></div>
                <div class="scanner-instruction">
                    <i class="fas fa-arrows-alt"></i> Align QR code within frame
                </div>
                
                <!-- Camera Control - Switch Camera Button -->
                <div class="camera-controls">
                    <button class="camera-btn" id="switchCameraBtn" title="Switch Camera">
                        <i class="fas fa-camera-rotate"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-number" id="scanCount">0</div>
                <div class="stat-label">Total Scans</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="successCount">0</div>
                <div class="stat-label">Successful</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="errorCount">0</div>
                <div class="stat-label">Errors</div>
            </div>
        </div>

        <!-- Status Message -->
        <div class="status-container" id="resultMessage">
            <i class="fas fa-qrcode status-icon"></i>
            <div class="status-text">
                <h4>Ready to Scan</h4>
                <p>Position QR code in camera view</p>
            </div>
        </div>

        <!-- Inactivity Warning -->
        <div class="inactivity-note">
            <i class="fas fa-exclamation-triangle"></i>
            Closes automatically after 1 minute of inactivity
        </div>

        <!-- Footer -->
        <div class="footer">
            <button class="btn-finish" onclick="finishScanning()">
                <i class="fas fa-check-circle"></i>
                Finish Scanning
            </button>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader">
        <div class="loader-spinner"></div>
    </div>

    <script>
        // Initialize variables
        let canScan = true;
        let inactivityTimer;
        let qrScanner = null;
        const SCAN_DELAY = 1000;
        const INACTIVITY_LIMIT = 60000;
        
        // Statistics
        let scanCount = 0;
        let successCount = 0;
        let errorCount = 0;

        // Camera state
        let isFrontCamera = false;
        let currentFacingMode = "environment";

        // Update time display
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            document.getElementById('currentTime').textContent = timeString;
        }

        // Initialize time update
        setInterval(updateTime, 1000);
        updateTime();

        // Show status message
        function showMessage(text, isSuccess, details = '') {
            const msgEl = document.getElementById('resultMessage');
            const icon = msgEl.querySelector('.status-icon');
            const title = msgEl.querySelector('h4');
            const subtitle = msgEl.querySelector('p');
            
            // Update styling
            msgEl.className = 'status-container';
            msgEl.classList.add(isSuccess ? 'status-success' : 'status-error');
            
            // Update icon
            icon.className = `fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'} status-icon`;
            
            // Update text
            title.textContent = text;
            subtitle.textContent = details || (isSuccess ? 'Attendance recorded successfully' : 'Please try again');
            
            // Add animation
            msgEl.style.transform = 'scale(1.02)';
            setTimeout(() => msgEl.style.transform = 'scale(1)', 200);
        }

        // Reset to ready state
        function resetMessage() {
            setTimeout(() => {
                const msgEl = document.getElementById('resultMessage');
                const icon = msgEl.querySelector('.status-icon');
                const title = msgEl.querySelector('h4');
                const subtitle = msgEl.querySelector('p');
                
                msgEl.className = 'status-container';
                icon.className = 'fas fa-qrcode status-icon';
                title.textContent = 'Ready to Scan';
                subtitle.textContent = 'Position QR code in camera view';
            }, 2000);
        }

        // Update statistics
        function updateStats(type) {
            scanCount++;
            if (type === 'success') successCount++;
            if (type === 'error') errorCount++;
            
            document.getElementById('scanCount').textContent = scanCount;
            document.getElementById('successCount').textContent = successCount;
            document.getElementById('errorCount').textContent = errorCount;
            
            // Animate the update
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                counter.style.transform = 'scale(1.2)';
                setTimeout(() => counter.style.transform = 'scale(1)', 200);
            });
        }

        // Toggle loader
        function toggleLoader(show) {
            document.getElementById('loader').style.display = show ? 'flex' : 'none';
        }

        // Reset inactivity timer
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                finishScanning();
            }, INACTIVITY_LIMIT);
        }

        // Finish scanning
        function finishScanning() {
            if (qrScanner) {
                qrScanner.stop().catch(console.error);
            }
            window.location.href = "event_scanner.php";
        }

        // Handle QR scan
        function handleScannedQR(decodedText) {
            if (!canScan) return;
            canScan = false;
            resetInactivityTimer();

            // Set student ID and submit
            document.getElementById('student_id').value = decodedText;
            const formData = new FormData(document.getElementById('attendanceForm'));

            toggleLoader(true);

            fetch('php/record_attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Success!', true, data.message || 'Attendance recorded');
                    try {
                        document.getElementById('beepSound').play();
                    } catch (e) {
                        console.log('Audio play failed:', e);
                    }
                    updateStats('success');
                } else {
                    showMessage('Error', false, data.message || 'Attendance failed');
                    try {
                        document.getElementById('errorSound').play();
                    } catch (e) {
                        console.log('Audio play failed:', e);
                    }
                    if (navigator.vibrate) navigator.vibrate(200);
                    updateStats('error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Network Error', false, 'Please check connection');
                try {
                    document.getElementById('errorSound').play();
                } catch (e) {
                    console.log('Audio play failed:', e);
                }
                if (navigator.vibrate) navigator.vibrate([200, 100, 200]);
                updateStats('error');
            })
            .finally(() => {
                toggleLoader(false);
                setTimeout(() => {
                    canScan = true;
                    resetMessage();
                }, SCAN_DELAY);
            });
        }

        // Switch camera
        function switchCamera() {
            if (!qrScanner) return;
            
            canScan = false;
            toggleLoader(true);
            
            // Toggle camera direction
            isFrontCamera = !isFrontCamera;
            currentFacingMode = isFrontCamera ? "user" : "environment";
            
            // Update button state
            const switchBtn = document.getElementById('switchCameraBtn');
            switchBtn.classList.toggle('active', isFrontCamera);
            switchBtn.innerHTML = isFrontCamera ? '<i class="fas fa-mobile-screen"></i>' : '<i class="fas fa-camera-rotate"></i>';
            switchBtn.title = isFrontCamera ? 'Switch to Back Camera' : 'Switch to Front Camera';
            
            // Stop current scanner
            qrScanner.stop().then(() => {
                // Restart with new camera
                return qrScanner.start(
                    { facingMode: currentFacingMode },
                    { 
                        fps: 10,
                        qrbox: { width: 250, height: 250 }
                    },
                    handleScannedQR,
                    (error) => {
                        // Ignore scanning errors
                    }
                );
            }).then(() => {
                toggleLoader(false);
                setTimeout(() => {
                    canScan = true;
                }, 500);
            }).catch(error => {
                console.error("Camera switch error:", error);
                showMessage('Camera Error', false, 'Failed to switch camera');
                toggleLoader(false);
                canScan = true;
            });
        }

        // Initialize QR Scanner
        function initScanner() {
            // Stop existing scanner if any
            if (qrScanner) {
                qrScanner.stop().catch(() => {});
            }
            
            // Create new scanner
            qrScanner = new Html5Qrcode("qr-reader");
            
            // Start scanner
            qrScanner.start(
                { facingMode: currentFacingMode },
                { 
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                handleScannedQR,
                (error) => {
                    // Ignore scanning errors
                }
            ).catch(error => {
                console.error("Scanner error:", error);
                showMessage('Camera Error', false, 'Please allow camera access');
            });
        }

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Start scanner
            setTimeout(initScanner, 100);
            
            // Setup inactivity timer
            resetInactivityTimer();
            
            // Setup camera control button
            document.getElementById('switchCameraBtn').addEventListener('click', switchCamera);
            
            // Reset timer on user interaction
            ['click', 'touchstart', 'keydown'].forEach(event => {
                document.addEventListener(event, resetInactivityTimer);
            });
            
            // Handle page visibility changes
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (qrScanner) {
                        qrScanner.stop().catch(() => {});
                    }
                } else {
                    setTimeout(initScanner, 500);
                }
            });
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (qrScanner) {
                qrScanner.stop().catch(() => {});
            }
        });
    </script>
</body>
</html>