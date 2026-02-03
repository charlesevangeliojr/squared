<?php
session_start();
require 'php/config.php';

// Set charset to UTF-8 immediately after connection
$conn->set_charset("utf8mb4");

// Or if using mysqli directly:
mysqli_set_charset($conn, "utf8mb4");

$query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($query);
?>

<style>
.announcement-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8fdf0 100%);
    position: relative;
    overflow: hidden;
}

.announcement-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #187C19, #0D5B11);
}

.announcement-header {
    position: relative;
    margin-bottom: 3rem;
}

.announcement-header h1 {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #187C19, #69B41E);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.announcement-header h1::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #8DC71E);
    border-radius: 2px;
}

.announcement-header .lead {
    font-size: 1.2rem;
    color: #666;
    margin-top: 2rem;
}

.announcement-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 3px solid transparent;
    position: relative;
    max-width: 900px;
    margin: 0 auto;
}

.announcement-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #8DC71E);
    transform: scaleX(0);
    transition: transform 0.3s ease;
    z-index: 2;
}

.announcement-card:hover::before {
    transform: scaleX(1);
}

.announcement-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(105, 180, 30, 0.2);
    border-color: #69B41E;
}

.announcement-body {
    padding: 2.5rem;
    position: relative;
}

.announcement-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 1.5rem;
    position: relative;
}

.announcement-content {
    font-size: 1.1rem;
    color: #4a5568;
    line-height: 1.7;
    margin-bottom: 2rem;
    background: #f8fdf0;
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid #69B41E;
}

.announcement-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.announcement-date {
    font-size: 0.9rem;
    color: #69B41E;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.announcement-badge {
    font-size: 0.8rem;
    padding: 0.4rem 1rem;
    background: #f1f8e9;
    color: #187C19;
    border-radius: 15px;
    font-weight: 600;
}

.no-announcement {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
    border-left: 5px solid #69B41E;
}

.no-announcement-icon {
    font-size: 4rem;
    color: #e2e8f0;
    margin-bottom: 1.5rem;
}

.no-announcement h3 {
    color: #4a5568;
    margin-bottom: 1rem;
}

.no-announcement p {
    color: #718096;
    margin-bottom: 2rem;
}

.back-button {
    background: linear-gradient(135deg, #69B41E, #8DC71E);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.back-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(105, 180, 30, 0.4);
    color: white;
}

/* Animation */
.announcement-card, .no-announcement {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .announcement-header h1 {
        font-size: 2.2rem;
    }
    
    .announcement-header .lead {
        font-size: 1.1rem;
        padding: 0 1rem;
    }
    
    .announcement-body {
        padding: 1.5rem;
    }
    
    .announcement-title {
        font-size: 1.5rem;
    }
    
    .announcement-content {
        font-size: 1rem;
        padding: 1rem;
    }
    
    .announcement-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}

@media (max-width: 576px) {
    .announcement-header h1 {
        font-size: 1.8rem;
    }
    
    .announcement-body {
        padding: 1.25rem;
    }
    
    .announcement-title {
        font-size: 1.3rem;
    }
    
    .no-announcement {
        padding: 2rem 1.5rem;
    }
    
    .announcement-content {
        padding: 0.75rem;
    }
}
</style>

<div class="announcement-section py-5">
    <div class="container">
        <!-- Enhanced Header matching avatars-section -->
        <div class="announcement-header text-center">
            <h1>Latest Announcement</h1>
            <p class="lead">
                Stay updated with the latest news from Squared QR System
            </p>
        </div>

        <?php if ($row = $result->fetch_assoc()): ?>
            <!-- Announcement Card with avatar-style design -->
            <div class="announcement-card">
                <div class="announcement-body">
                    <h3 class="announcement-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                    </div>
                    <div class="announcement-meta">
                        <div class="announcement-date">
                            <i class="bi bi-calendar-event"></i>
                            Posted on: <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?>
                        </div>
                        <div class="announcement-badge">
                            <i class="bi bi-megaphone-fill"></i> Latest Update
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- No Announcement State -->
            <div class="no-announcement">
                <div class="no-announcement-icon">
                    <i class="bi bi-megaphone"></i>
                </div>
                <h3>No Announcements Available</h3>
                <p>There are currently no announcements to display. Check back later for updates.</p>
                <a href="javascript:history.back()" class="back-button">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        <?php endif; ?>

        <!-- Back Button -->
        <?php if ($result->num_rows > 0): ?>
            <!-- <div class="text-center mt-4">
                <a href="javascript:history.back()" class="back-button">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div> -->
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any additional JavaScript functionality here if needed
    });
</script>
