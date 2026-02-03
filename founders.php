<style>
/* Enhanced Founders Section Styles */
.founders-section {
    background: linear-gradient(135deg, #f8fdf0 0%, #e8f5e9 100%);
    position: relative;
    overflow: hidden;
}

.founders-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #187C19, #0D5B11);
}

.founder-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    border-radius: 20px;
    overflow: hidden;
    background: white;
    position: relative;
    z-index: 1;
}

.founder-card::before {
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

.founder-card:hover::before {
    transform: scaleX(1);
}

.founder-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 40px rgba(105, 180, 30, 0.15) !important;
}

.founder-img-container {
    position: relative;
    margin-bottom: 1.5rem;
}

.founder-img-wrapper {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #69B41E;
    box-shadow: 0 8px 20px rgba(105, 180, 30, 0.3);
    margin: 0 auto;
    position: relative;
    transition: all 0.3s ease;
}

.founder-card:hover .founder-img-wrapper {
    border-color: #8DC71E;
    box-shadow: 0 12px 25px rgba(141, 199, 30, 0.4);
    transform: scale(1.05);
}

.founder-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.3s ease;
}

.founder-card:hover .founder-img {
    transform: scale(1.1);
}

.founder-role-badge {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #69B41E, #187C19);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(105, 180, 30, 0.3);
    white-space: nowrap;
}

.founder-social-links {
    margin-top: 1rem;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease 0.1s;
}

.founder-card:hover .founder-social-links {
    opacity: 1;
    transform: translateY(0);
}

.social-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f1f8e9;
    color: #69B41E;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 4px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.social-icon:hover {
    background: #69B41E;
    color: white;
    transform: translateY(-2px);
    border-color: #69B41E;
}

/* Enhanced section header */
.founders-header {
    position: relative;
    margin-bottom: 3rem;
}

.founders-header h2 {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #187C19, #69B41E);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.founders-header h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #8DC71E);
    border-radius: 2px;
}

.founders-header .lead {
    font-size: 1.2rem;
    color: #5a5a5a;
    margin-top: 1.5rem;
}

/* Mobile-first responsive design */
@media (max-width: 768px) {
    .founders-section {
        padding: 3rem 0 !important;
    }
    
    .founders-header h2 {
        font-size: 2rem;
    }
    
    .founders-header .lead {
        font-size: 1.1rem;
        padding: 0 1rem;
    }
    
    .founder-img-wrapper {
        width: 150px;
        height: 150px;
    }
    
    .founder-card {
        margin-bottom: 2rem;
    }
    
    .founder-card:hover {
        transform: translateY(-8px) scale(1.01);
    }
    
    .founder-social-links {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 576px) {
    .founders-section {
        padding: 2rem 0 !important;
    }
    
    .founders-header h2 {
        font-size: 1.8rem;
    }
    
    .founder-img-wrapper {
        width: 130px;
        height: 130px;
    }
    
    .founder-role-badge {
        font-size: 0.7rem;
        padding: 4px 12px;
    }
    
    .social-icon {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
    }
    
    .founder-card .card-body {
        padding: 1.25rem !important;
    }
}

/* Animation classes */
.fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.8s ease forwards;
}

.founder-card:nth-child(1) { animation-delay: 0.1s; }
.founder-card:nth-child(2) { animation-delay: 0.2s; }
.founder-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading state for images */
.founder-img.loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Remove loading state when image is loaded */
.founder-img.loaded {
    animation: none;
    background: none;
}
</style>

<section class="founders-section py-5" id="founders">
    <div class="container">
        <!-- Enhanced Header -->
        <div class="founders-header text-center">
            <h2 class="fw-bold mb-3">Meet Our Visionary Team</h2>
            <p class="lead">
                The passionate minds revolutionizing campus experiences at Davao Central College
            </p>
        </div>

        <div class="row justify-content-center g-4">
            <!-- Founder -->
            <div class="col-12 col-md-6 col-lg-4 fade-in-up">
                <div class="card founder-card shadow-lg h-100">
                    <div class="card-body text-center p-4">
                        <div class="founder-img-container">
                            <div class="founder-img-wrapper">
                                <img src="images/founders/roberto.png" alt="Roberto P. Tacbobo Jr." class="founder-img loading" onload="this.classList.add('loaded')">
                            </div>
                            <div class="founder-role-badge">Founder</div>
                        </div>
                        
                        <h5 class="card-title fw-bold text-dark mb-2">Roberto P. Tacbobo Jr.</h5>
                        <p class="text-muted mb-3">Visionary Leader</p>
                        <p class="card-text text-dark mb-4">
The innovative mind behind Squared QR, dedicated to transforming traditional campus processes 
into seamless digital experiences through cutting-edge technology solutions.
                        </p>
                        
                        <div class="founder-social-links">
                            <a href="https://www.instagram.com/tacky_ed" target="_blank" class="social-icon" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com/robertopelaeztacbobo" target="_blank" class="social-icon" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="mailto:robertojrpelaeztacbobo.dcc@gmail.com" class="social-icon" title="Email">
                                <i class="bi bi-envelope-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Co-Founder 1 -->
            <div class="col-12 col-md-6 col-lg-4 fade-in-up">
                <div class="card founder-card shadow-lg h-100">
                    <div class="card-body text-center p-4">
                        <div class="founder-img-container">
                            <div class="founder-img-wrapper">
                                <img src="images/founders/charles.png" alt="Charles S. Evangelio Jr." class="founder-img loading" onload="this.classList.add('loaded')">
                            </div>
                            <div class="founder-role-badge">Co-Founder</div>
                        </div>
                        
                        <h5 class="card-title fw-bold text-dark mb-2">Charles S. Evangelio Jr.</h5>
                        <p class="text-muted mb-3">Technical Lead & Developer</p>
                        <p class="card-text text-dark mb-4">
Full-stack developer who architectured and built the Squared QR system from ground up, 
ensuring robust performance and exceptional user experience across all platforms.
                        </p>
                        
                        <div class="founder-social-links">
                            <a href="https://www.facebook.com/chazy.mushie" target="_blank" class="social-icon" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://github.com/charlesevangeliojr" target="_blank" class="social-icon" title="GitHub">
                                <i class="bi bi-github"></i>
                            </a>
                            <a href="mailto:charles123evangelio@gmail.com" class="social-icon" title="Email">
                                <i class="bi bi-envelope-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Co-Founder 2 -->
            <div class="col-12 col-md-6 col-lg-4 fade-in-up">
                <div class="card founder-card shadow-lg h-100">
                    <div class="card-body text-center p-4">
                        <div class="founder-img-container">
                            <div class="founder-img-wrapper">
                                <img src="images/founders/pitch.png" alt="Angel Pitch R. Geronggay" class="founder-img loading" onload="this.classList.add('loaded')">
                            </div>
                            <div class="founder-role-badge">Co-Founder</div>
                        </div>
                        
                        <h5 class="card-title fw-bold text-dark mb-2">Angel Pitch R. Geronggay</h5>
                        <p class="text-muted mb-3">Creative Strategist</p>
                        <p class="card-text text-dark mb-4">
Creative director and strategic partner focused on user experience design and 
ensuring the platform meets the diverse needs of the DCC student community.
                        </p>
                        
                        <div class="founder-social-links">
                            <a href="https://www.instagram.com/sme_paesthetichy/" target="_blank" class="social-icon" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com/paesthetichy" target="_blank" class="social-icon" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="mailto:geronggayangelpitch@gmail.com" class="social-icon" title="Email">
                                <i class="bi bi-envelope-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5 fade-in-up">
            <div class="bg-light rounded-3 p-4 shadow-sm">
                <h4 class="text-success mb-3">Join Our Mission</h4>
                <p class="text-muted mb-3">
                    Be part of the revolution in campus digital transformation
                </p>
                <button class="btn btn-success btn-lg px-4" onclick="scrollToRegister()">
                    Get Register Now!
                </button>
            </div>
        </div>
    </div>
</section>

<script>
// Intersection Observer for fade-in animations
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all fade-in elements
    document.querySelectorAll('.fade-in-up').forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });

    // Handle image loading states
    document.querySelectorAll('.founder-img').forEach(img => {
        if (img.complete) {
            img.classList.add('loaded');
        }
    });
});

// Scroll to register function
function scrollToRegister() {
    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    registerModal.show();
}

// Enhanced hover effects for mobile touch
document.querySelectorAll('.founder-card').forEach(card => {
    card.addEventListener('touchstart', function() {
        this.classList.add('hover-effect');
    });
    
    card.addEventListener('touchend', function() {
        setTimeout(() => {
            this.classList.remove('hover-effect');
        }, 300);
    });
});
</script>