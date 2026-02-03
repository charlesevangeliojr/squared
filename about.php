<style>
.about-section {
    background: linear-gradient(135deg, #f8fdf0 0%, #ffffff 100%);
    position: relative;
    overflow: hidden;
}

.about-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #187C19, #0D5B11);
}

.about-header {
    position: relative;
    margin-bottom: 3rem;
}

.about-header h2 {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #187C19, #69B41E);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.about-header h2::after {
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

.logo-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    margin: 2rem 0;
}

.logo-item {
    text-align: center;
    transition: transform 0.3s ease;
}

.logo-item:hover {
    transform: scale(1.05);
}

.logo-img {
    height: 100px;
    width: auto;
    transition: all 0.3s ease;
}

.logo-item:hover .logo-img {
    filter: drop-shadow(0 5px 15px rgba(105, 180, 30, 0.3));
}

.logo-label {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: #666;
    font-weight: 600;
}

.about-content {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border-left: 5px solid #69B41E;
    position: relative;
    overflow: hidden;
}

.about-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(105, 180, 30, 0.03) 0%, rgba(141, 199, 30, 0.03) 100%);
    z-index: 0;
}

.about-content > * {
    position: relative;
    z-index: 1;
}

.feature-highlights {
    margin-top: 4rem;
}

.feature-card {
    background: white;
    border: none;
    border-radius: 15px;
    padding: 2rem 1.5rem;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    border-top: 4px solid transparent;
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #8DC71E);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.feature-card:hover::before {
    transform: scaleX(1);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(105, 180, 30, 0.15);
}

.feature-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #69B41E, #8DC71E);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(10deg);
    background: linear-gradient(135deg, #8DC71E, #69B41E);
}

.feature-card h5 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
}

.feature-card p {
    color: #666;
    line-height: 1.6;
}

/* Color variants for features */
.feature-card:nth-child(1) .feature-icon { background: linear-gradient(135deg, #69B41E, #8DC71E); }
.feature-card:nth-child(2) .feature-icon { background: linear-gradient(135deg, #2196F3, #64B5F6); }
.feature-card:nth-child(3) .feature-icon { background: linear-gradient(135deg, #FF9800, #FFB74D); }

.feature-card:nth-child(1):hover .feature-icon { background: linear-gradient(135deg, #8DC71E, #69B41E); }
.feature-card:nth-child(2):hover .feature-icon { background: linear-gradient(135deg, #64B5F6, #2196F3); }
.feature-card:nth-child(3):hover .feature-icon { background: linear-gradient(135deg, #FFB74D, #FF9800); }

/* Animation */
.about-content p {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease forwards;
}

.about-content p:nth-child(1) { animation-delay: 0.1s; }
.about-content p:nth-child(2) { animation-delay: 0.2s; }
.about-content p:nth-child(3) { animation-delay: 0.3s; }
.about-content p:nth-child(4) { animation-delay: 0.4s; }

.feature-card {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

.feature-card:nth-child(1) { animation-delay: 0.5s; }
.feature-card:nth-child(2) { animation-delay: 0.6s; }
.feature-card:nth-child(3) { animation-delay: 0.7s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .about-header h2 {
        font-size: 2.2rem;
    }
    
    .logo-container {
        gap: 1.5rem;
    }
    
    .logo-img {
        height: 80px;
    }
    
    .about-content {
        padding: 2rem 1.5rem;
    }
    
    .feature-card {
        margin-bottom: 1.5rem;
        padding: 1.5rem 1rem;
    }
    
    .feature-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .about-header h2 {
        font-size: 1.8rem;
    }
    
    .logo-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .about-content {
        padding: 1.5rem 1rem;
    }
    
    .feature-card h5 {
        font-size: 1.1rem;
    }
}

/* Stats Section */
.stats-section {
    background: linear-gradient(135deg, #187C19, #69B41E);
    color: white;
    padding: 3rem 0;
    margin-top: 4rem;
    border-radius: 20px;
}

.stat-item {
    text-align: center;
    padding: 1rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}
</style>

<section class="about-section py-5" id="about-squared">
    <div class="container">
        <!-- Enhanced Header -->
        <div class="about-header text-center">
            <h2 class="fw-bold">About Squared</h2>
            <div class="logo-container">
                <div class="logo-item">
                    <img src="images/dcc_logo.png" alt="Davao Central College Logo" class="logo-img">
                    <div class="logo-label">Davao Central College</div>
                </div>
                <div class="logo-item">
                    <img src="images/Squared_Logo.png" alt="Squared QR Logo" class="logo-img">
                    <div class="logo-label">Squared QR System</div>
                </div>
            </div>
            <p class="lead text-muted fs-5">
                Revolutionizing Davao Central College Main Campus Experiences — powered by QR technology.
            </p>
        </div>

        <!-- Enhanced Content -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="about-content">
                    <p class="mb-4 fs-6">
                        <strong class="text-success">Squared</strong> is a modern web-based system developed for DCC students to
                        streamline attendance, event participation, and transactions using QR code technology.
                    </p>
                    <p class="mb-4 fs-6">
                        Originally launched through Google Forms, Squared has now evolved into a complete web
                        application. Students can register on the platform, automatically generate their own QR
                        codes, and use them for quick and efficient scanning during DCC activities.
                    </p>
                    <p class="mb-4 fs-6">
                        Whether it's attending an event, verifying identity, or checking in for official activities,
                        Squared simplifies the process for both students and organizers — reducing queues, manual
                        tracking, and paperwork.
                    </p>
                    <p class="mb-0 fs-6">
                        The goal of Squared is to enhance accessibility, boost efficiency, and improve the overall
                        experience of every DCC event — all with a simple scan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Enhanced Features Section -->
        <div class="feature-highlights">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <h5>Scan QR Codes</h5>
                        <p>Quick and secure attendance tracking with instant QR code scanning technology.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h5>View Analytics</h5>
                        <p>Comprehensive analytics and detailed attendance reports with real-time data.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h5>Manage Users</h5>
                        <p>Efficiently manage users and their attendance records with advanced tools.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional Stats Section -->
        <!-- <div class="stats-section mt-5">
            <div class="row text-center">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number" id="total-students">0</div>
                        <div class="stat-label">Active Students</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Programs</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Courses</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Digital</div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats numbers
    function animateStats() {
        const totalStudents = document.getElementById('total-students');
        if (totalStudents) {
            const target = parseInt(document.getElementById('total-students-count')?.textContent || '0');
            animateValue(totalStudents, 0, target, 2000);
        }
    }

    function animateValue(element, start, end, duration) {
        if (start === end) return;
        const range = end - start;
        const increment = end > start ? 1 : -1;
        const stepTime = Math.max(Math.floor(duration / Math.abs(range)), 20);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            element.textContent = current.toLocaleString();
            if (current === end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    // Initialize animations after a short delay
    setTimeout(animateStats, 1000);
});
</script>