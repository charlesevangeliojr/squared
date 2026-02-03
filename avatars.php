<style>
.avatars-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8fdf0 100%);
    position: relative;
    overflow: hidden;
}

.avatars-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #187C19, #0D5B11);
}

.avatars-header {
    position: relative;
    margin-bottom: 3rem;
}

.avatars-header h1 {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #187C19, #69B41E);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.avatars-header h1::after {
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

.avatars-header .lead {
    font-size: 1.2rem;
    color: #666;
    margin-top: 2rem;
}

.avatar-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 3px solid transparent;
    height: 100%;
    position: relative;
    cursor: pointer;
}

.avatar-card::before {
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

.avatar-card:hover::before {
    transform: scaleX(1);
}

.avatar-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 20px 40px rgba(105, 180, 30, 0.2);
    border-color: #69B41E;
}

.avatar-image {
    height: 250px;
    object-fit: cover;
    object-position: top;
    transition: all 0.3s ease;
    position: relative;
}

.avatar-card:hover .avatar-image {
    transform: scale(1.05);
}

.avatar-body {
    padding: 1.5rem;
    text-align: center;
    position: relative;
}

.avatar-name {
    font-size: 1.5rem;
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 0.5rem;
    position: relative;
}

.avatar-role {
    font-size: 0.9rem;
    color: #69B41E;
    font-weight: 600;
    margin-bottom: 1rem;
    padding: 0.3rem 1rem;
    background: #f1f8e9;
    border-radius: 15px;
    display: inline-block;
}

.avatar-description {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.5;
    margin-bottom: 1rem;
    min-height: 60px;
}

.avatar-traits {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
    margin-top: 1rem;
}

.trait-tag {
    font-size: 0.75rem;
    padding: 0.3rem 0.8rem;
    background: #e8f5e9;
    color: #187C19;
    border-radius: 12px;
    font-weight: 600;
    border: 1px solid #c8e6c9;
}

/* Individual Avatar Colors */
.avatar-card.joy .avatar-name { color: #E91E63; }
.avatar-card.joy .avatar-role { background: #fce4ec; color: #C2185B; }
.avatar-card.joy .trait-tag { background: #fce4ec; color: #C2185B; border-color: #f8bbd9; }

.avatar-card.sevi .avatar-name { color: #2196F3; }
.avatar-card.sevi .avatar-role { background: #e3f2fd; color: #1976D2; }
.avatar-card.sevi .trait-tag { background: #e3f2fd; color: #1976D2; border-color: #bbdefb; }

.avatar-card.samantha .avatar-name { color: #9C27B0; }
.avatar-card.samantha .avatar-role { background: #f3e5f5; color: #7B1FA2; }
.avatar-card.samantha .trait-tag { background: #f3e5f5; color: #7B1FA2; border-color: #e1bee7; }

.avatar-card.zeke .avatar-name { color: #FF9800; }
.avatar-card.zeke .avatar-role { background: #fff3e0; color: #F57C00; }
.avatar-card.zeke .trait-tag { background: #fff3e0; color: #F57C00; border-color: #ffcc80; }

/* Animation */
.avatar-card {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

.avatar-card:nth-child(1) { animation-delay: 0.1s; }
.avatar-card:nth-child(2) { animation-delay: 0.2s; }
.avatar-card:nth-child(3) { animation-delay: 0.3s; }
.avatar-card:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile-only modal trigger */
@media (max-width: 768px) {
    .avatar-card {
        cursor: pointer;
    }
    
    .avatar-description,
    .avatar-traits {
        display: none;
    }
    
    .avatar-body {
        padding: 1rem;
    }
    
    .view-details {
        display: block !important;
        font-size: 0.8rem;
        color: #69B41E;
        font-weight: 600;
        margin-top: 0.5rem;
    }
}

@media (min-width: 769px) {
    .avatar-card {
        cursor: default;
    }
    
    .view-details {
        display: none !important;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .avatars-header h1 {
        font-size: 2.2rem;
    }
    
    .avatars-header .lead {
        font-size: 1.1rem;
        padding: 0 1rem;
    }
    
    .avatar-image {
        height: 200px;
    }
    
    .avatar-name {
        font-size: 1.3rem;
    }
    
    .avatar-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .avatars-header h1 {
        font-size: 1.8rem;
    }
    
    .avatar-image {
        height: 180px;
    }
    
    .avatar-name {
        font-size: 1.2rem;
    }
}

/* Modal Styles */
.avatar-modal .modal-content {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}

.avatar-modal .modal-header {
    background: linear-gradient(135deg, #69B41E, #8DC71E);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
}

.avatar-modal .modal-body {
    padding: 0;
}

.avatar-modal-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    object-position: top;
}

.avatar-modal-content {
    padding: 1.5rem;
}

.avatar-modal-name {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.avatar-modal-role {
    font-size: 1rem;
    color: #69B41E;
    font-weight: 600;
    margin-bottom: 1rem;
    padding: 0.5rem 1.2rem;
    background: #f1f8e9;
    border-radius: 20px;
    display: inline-block;
}

.avatar-modal-description {
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.avatar-modal-traits {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.avatar-modal-tag {
    font-size: 0.8rem;
    padding: 0.4rem 0.9rem;
    background: #e8f5e9;
    color: #187C19;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid #c8e6c9;
}

/* Modal Avatar Colors */
.avatar-modal.joy .modal-header { background: linear-gradient(135deg, #E91E63, #F06292); }
.avatar-modal.joy .avatar-modal-role { background: #fce4ec; color: #C2185B; }
.avatar-modal.joy .avatar-modal-tag { background: #fce4ec; color: #C2185B; border-color: #f8bbd9; }

.avatar-modal.sevi .modal-header { background: linear-gradient(135deg, #2196F3, #64B5F6); }
.avatar-modal.sevi .avatar-modal-role { background: #e3f2fd; color: #1976D2; }
.avatar-modal.sevi .avatar-modal-tag { background: #e3f2fd; color: #1976D2; border-color: #bbdefb; }

.avatar-modal.samantha .modal-header { background: linear-gradient(135deg, #9C27B0, #BA68C8); }
.avatar-modal.samantha .avatar-modal-role { background: #f3e5f5; color: #7B1FA2; }
.avatar-modal.samantha .avatar-modal-tag { background: #f3e5f5; color: #7B1FA2; border-color: #e1bee7; }

.avatar-modal.zeke .modal-header { background: linear-gradient(135deg, #FF9800, #FFB74D); }
.avatar-modal.zeke .avatar-modal-role { background: #fff3e0; color: #F57C00; }
.avatar-modal.zeke .avatar-modal-tag { background: #fff3e0; color: #F57C00; border-color: #ffcc80; }
</style>

<div class="avatars-section py-5">
    <div class="container">
        <!-- Enhanced Header -->
        <div class="avatars-header text-center">
            <h1>Squared Avatars</h1>
            <p class="lead">
                Meet the unique personalities that represent the spirit of Squared QR System
            </p>
        </div>

        <div class="row g-4">
            <!-- JOY Avatar -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="avatar-card joy" onclick="openAvatarModal('joy')">
                    <img src="avatars/JOY.jpg" class="avatar-image w-100" alt="JOY Avatar">
                    <div class="avatar-body">
                        <h3 class="avatar-name">JOY</h3>
                        <div class="avatar-role">The Optimist</div>
                        <p class="avatar-description">
                            Always cheerful and energetic, JOY brings positivity to every interaction. 
                            She represents the happy moments of campus life.
                        </p>
                        <div class="avatar-traits">
                            <span class="trait-tag">Energetic</span>
                            <span class="trait-tag">Friendly</span>
                            <span class="trait-tag">Optimistic</span>
                        </div>
                        <div class="view-details" style="display: none;">
                            Tap to view details
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEVI Avatar -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="avatar-card sevi" onclick="openAvatarModal('sevi')">
                    <img src="avatars/SEVI.jpg" class="avatar-image w-100" alt="SEVI Avatar">
                    <div class="avatar-body">
                        <h3 class="avatar-name">SEVI</h3>
                        <div class="avatar-role">The Analyst</div>
                        <p class="avatar-description">
                            Logical and detail-oriented, SEVI ensures everything runs smoothly. 
                            he's the brains behind efficient operations.
                        </p>
                        <div class="avatar-traits">
                            <span class="trait-tag">Analytical</span>
                            <span class="trait-tag">Organized</span>
                            <span class="trait-tag">Precise</span>
                        </div>
                        <div class="view-details" style="display: none;">
                            Tap to view details
                        </div>
                    </div>
                </div>
            </div>

            <!-- SAMANTHA Avatar -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="avatar-card samantha" onclick="openAvatarModal('samantha')">
                    <img src="avatars/SAMANTHA.jpg" class="avatar-image w-100" alt="SAMANTHA Avatar">
                    <div class="avatar-body">
                        <h3 class="avatar-name">SAMANTHA</h3>
                        <div class="avatar-role">The Innovator</div>
                        <p class="avatar-description">
                            Creative and forward-thinking, SAMANTHA brings fresh ideas to the table. 
                            She represents innovation and progress.
                        </p>
                        <div class="avatar-traits">
                            <span class="trait-tag">Creative</span>
                            <span class="trait-tag">Visionary</span>
                            <span class="trait-tag">Modern</span>
                        </div>
                        <div class="view-details" style="display: none;">
                            Tap to view details
                        </div>
                    </div>
                </div>
            </div>

            <!-- ZEKE Avatar -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="avatar-card zeke" onclick="openAvatarModal('zeke')">
                    <img src="avatars/ZEKE.jpg" class="avatar-image w-100" alt="ZEKE Avatar">
                    <div class="avatar-body">
                        <h3 class="avatar-name">ZEKE</h3>
                        <div class="avatar-role">The Guardian</div>
                        <p class="avatar-description">
                            Strong and reliable, ZEKE represents security and trust. 
                            He ensures every transaction and interaction is protected.
                        </p>
                        <div class="avatar-traits">
                            <span class="trait-tag">Reliable</span>
                            <span class="trait-tag">Secure</span>
                            <span class="trait-tag">Strong</span>
                        </div>
                        <div class="view-details" style="display: none;">
                            Tap to view details
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <div class="bg-light rounded-3 p-4 shadow-sm">
                <h4 class="text-success mb-3">Choose Your Avatar</h4>
                <p class="text-muted mb-3">
                    Select your favorite avatar during registration to personalize your Squared QR experience
                </p>
                <button class="btn btn-success btn-lg px-4" onclick="scrollToRegister()">
                    Register Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Modals -->
<div class="modal fade avatar-modal joy" id="avatarModalJoy" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">The Optimist</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="avatars/JOY.jpg" class="avatar-modal-image w-100" alt="JOY Avatar">
                <div class="avatar-modal-content">
                    <h3 class="avatar-modal-name">JOY</h3>
                    <div class="avatar-modal-role">The Optimist</div>
                    <p class="avatar-modal-description">
                        Always cheerful and energetic, JOY brings positivity to every interaction. 
                        She represents the happy moments of campus life and spreads enthusiasm 
                        throughout the Squared community.
                    </p>
                    <div class="avatar-modal-traits">
                        <span class="avatar-modal-tag">Energetic</span>
                        <span class="avatar-modal-tag">Friendly</span>
                        <span class="avatar-modal-tag">Optimistic</span>
                        <span class="avatar-modal-tag">Positive</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade avatar-modal sevi" id="avatarModalSevi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">The Analyst</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="avatars/SEVI.jpg" class="avatar-modal-image w-100" alt="SEVI Avatar">
                <div class="avatar-modal-content">
                    <h3 class="avatar-modal-name">SEVI</h3>
                    <div class="avatar-modal-role">The Analyst</div>
                    <p class="avatar-modal-description">
                        Logical and detail-oriented, SEVI ensures everything runs smoothly. 
                        He's the brains behind efficient operations and loves optimizing 
                        processes for better performance.
                    </p>
                    <div class="avatar-modal-traits">
                        <span class="avatar-modal-tag">Analytical</span>
                        <span class="avatar-modal-tag">Organized</span>
                        <span class="avatar-modal-tag">Precise</span>
                        <span class="avatar-modal-tag">Efficient</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade avatar-modal samantha" id="avatarModalSamantha" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">The Innovator</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="avatars/SAMANTHA.jpg" class="avatar-modal-image w-100" alt="SAMANTHA Avatar">
                <div class="avatar-modal-content">
                    <h3 class="avatar-modal-name">SAMANTHA</h3>
                    <div class="avatar-modal-role">The Innovator</div>
                    <p class="avatar-modal-description">
                        Creative and forward-thinking, SAMANTHA brings fresh ideas to the table. 
                        She represents innovation and progress, always looking for new ways to 
                        improve the student experience.
                    </p>
                    <div class="avatar-modal-traits">
                        <span class="avatar-modal-tag">Creative</span>
                        <span class="avatar-modal-tag">Visionary</span>
                        <span class="avatar-modal-tag">Modern</span>
                        <span class="avatar-modal-tag">Innovative</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade avatar-modal zeke" id="avatarModalZeke" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">The Guardian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="avatars/ZEKE.jpg" class="avatar-modal-image w-100" alt="ZEKE Avatar">
                <div class="avatar-modal-content">
                    <h3 class="avatar-modal-name">ZEKE</h3>
                    <div class="avatar-modal-role">The Guardian</div>
                    <p class="avatar-modal-description">
                        Strong and reliable, ZEKE represents security and trust. 
                        He ensures every transaction and interaction is protected, 
                        providing peace of mind for all users.
                    </p>
                    <div class="avatar-modal-traits">
                        <span class="avatar-modal-tag">Reliable</span>
                        <span class="avatar-modal-tag">Secure</span>
                        <span class="avatar-modal-tag">Strong</span>
                        <span class="avatar-modal-tag">Trustworthy</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image loading
    document.querySelectorAll('.avatar-image').forEach(img => {
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', function() {
                this.classList.add('loaded');
            });
        }
    });

    // Show/hide view details text based on screen size
    function updateViewDetailsVisibility() {
        const viewDetailsElements = document.querySelectorAll('.view-details');
        const isMobile = window.innerWidth <= 768;
        
        viewDetailsElements.forEach(el => {
            el.style.display = isMobile ? 'block' : 'none';
        });
        
        // Update cursor style for cards
        const avatarCards = document.querySelectorAll('.avatar-card');
        avatarCards.forEach(card => {
            card.style.cursor = isMobile ? 'pointer' : 'default';
        });
    }

    // Initial check
    updateViewDetailsVisibility();
    
    // Update on resize
    window.addEventListener('resize', updateViewDetailsVisibility);
});

function openAvatarModal(avatarType) {
    // Only open modal on mobile devices
    if (window.innerWidth <= 768) {
        const modalId = `avatarModal${avatarType.charAt(0).toUpperCase() + avatarType.slice(1)}`;
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    }
}

function scrollToRegister() {
    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    registerModal.show();
}
</script>