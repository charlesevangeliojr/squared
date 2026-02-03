<style>
.programs-section {
    background: linear-gradient(135deg, #f8fdf0 0%, #ffffff 50%, #e8f5e9 100%);
    position: relative;
    overflow: hidden;
}

.programs-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #69B41E, #187C19, #0D5B11);
}

.programs-header {
    position: relative;
    margin-bottom: 3rem;
}

.programs-header h1 {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #187C19, #69B41E);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.programs-header h1::after {
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

.programs-header .lead {
    font-size: 1.2rem;
    color: #666;
    margin-top: 2rem;
}

.programs-grid {
    /* Desktop: force a single horizontal row of cards with horizontal scroll */
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(280px, 1fr);
    grid-template-rows: 1fr;
    gap: 2rem;
    padding: 1rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-snap-type: x mandatory;
}

.program-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem 1.5rem;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 3px solid transparent;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 280px;
    /* ensure cards don't shrink too small in horizontal row */
    min-width: 280px;
    scroll-snap-align: start;
}

.program-card::before {
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

.program-card:hover::before {
    transform: scaleX(1);
}

.program-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(105, 180, 30, 0.15);
    border-color: #69B41E;
}

.program-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    background: white;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.3s ease;
    border: 3px solid transparent;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.program-card:hover .program-icon {
    transform: scale(1.1) rotate(5deg);
    border-color: #69B41E;
}

.program-icon img {
    width: 70px;
    height: 70px;
    object-fit: contain;
    transition: all 0.3s ease;
}

.program-card:hover .program-icon img {
    transform: scale(1.1);
}

.program-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.8rem;
    line-height: 1.4;
}

.program-abbreviation {
    font-size: 1rem;
    color: #69B41E;
    font-weight: 700;
    padding: 0.5rem 1.2rem;
    background: #f1f8e9;
    border-radius: 25px;
    display: inline-block;
    border: 2px solid #e8f5e9;
    transition: all 0.3s ease;
}

.program-card:hover .program-abbreviation {
    background: #69B41E;
    color: white;
    border-color: #69B41E;
}

/* Color variants for different programs */
.program-card.cba .program-icon { 
    background: linear-gradient(135deg, #FFD700, #FFF176); 
    border-color: #FFD700;
}
.program-card.cela .program-icon { 
    background: linear-gradient(135deg, #2196F3, #64B5F6); 
    border-color: #2196F3;
}
.program-card.cje .program-icon { 
    background: linear-gradient(135deg, #212121, #424242); 
    border-color: #212121;
}
.program-card.hme .program-icon { 
    background: linear-gradient(135deg, #E53935, #FF5252); 
    border-color: #E53935;
}
.program-card.ite .program-icon { 
    background: linear-gradient(135deg, #9C27B0, #BA68C8); 
    border-color: #9C27B0;
}

.program-card.cba:hover .program-icon { 
    background: linear-gradient(135deg, #FFF176, #FFD700); 
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
}
.program-card.cela:hover .program-icon { 
    background: linear-gradient(135deg, #64B5F6, #2196F3); 
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);
}
.program-card.cje:hover .program-icon { 
    background: linear-gradient(135deg, #424242, #212121); 
    box-shadow: 0 8px 25px rgba(33, 33, 33, 0.3);
}
.program-card.hme:hover .program-icon { 
    background: linear-gradient(135deg, #FF5252, #E53935); 
    box-shadow: 0 8px 25px rgba(229, 57, 53, 0.3);
}
.program-card.ite:hover .program-icon { 
    background: linear-gradient(135deg, #BA68C8, #9C27B0); 
    box-shadow: 0 8px 25px rgba(156, 39, 176, 0.3);
}

/* Animation */
.program-card {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

.program-card:nth-child(1) { animation-delay: 0.1s; }
.program-card:nth-child(2) { animation-delay: 0.2s; }
.program-card:nth-child(3) { animation-delay: 0.3s; }
.program-card:nth-child(4) { animation-delay: 0.4s; }
.program-card:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .programs-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        padding: 0.5rem;
    }
    
    .programs-header h1 {
        font-size: 2.2rem;
    }
    
    .programs-header .lead {
        font-size: 1.1rem;
        padding: 0 1rem;
    }
    
    .program-card {
        padding: 2rem 1rem;
        min-height: 250px;
    }
    
    .program-icon {
        width: 80px;
        height: 80px;
    }
    
    .program-icon img {
        width: 60px;
        height: 60px;
    }
    
    .program-name {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .programs-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .programs-header h1 {
        font-size: 1.8rem;
    }
    
    .program-card {
        padding: 1.5rem 1rem;
        min-height: 220px;
    }
    
    .program-icon {
        width: 70px;
        height: 70px;
    }
    
    .program-icon img {
        width: 50px;
        height: 50px;
    }
    
    .program-name {
        font-size: 0.9rem;
    }
    
    .program-abbreviation {
        font-size: 0.9rem;
        padding: 0.4rem 1rem;
    }
}

/* Loading animation */
.program-icon img.loading {
    opacity: 0;
}

.program-icon img.loaded {
    opacity: 1;
    transition: opacity 0.3s ease;
}
</style>

<div class="programs-section py-5">
    <div class="container">
        <!-- Enhanced Header -->
        <div class="programs-header text-center">
            <h1 class="fw-bold">Academic Programs</h1>
            <p class="lead">
                Discover the diverse range of programs designed to shape future leaders and professionals
            </p>
        </div>

        <div class="programs-grid">
            <!-- CBA -->
            <div class="program-card cba">
                <div class="program-icon">
                    <img src="https://dcc.edu.ph/wp-content/uploads/2021/04/BA-WEB.png" 
                         alt="College of Business Administration" 
                         class="loading"
                         onload="this.classList.add('loaded')">
                </div>
                <h3 class="program-name">College of Business Administration</h3>
                <div class="program-abbreviation">CBA</div>
            </div>

            <!-- CELA -->
            <div class="program-card cela">
                <div class="program-icon">
                    <img src="https://dcc.edu.ph/wp-content/uploads/2021/04/CELA-WEB.png" 
                         alt="College of Education and Liberal Arts" 
                         class="loading"
                         onload="this.classList.add('loaded')">
                </div>
                <h3 class="program-name">College of Education and Liberal Arts</h3>
                <div class="program-abbreviation">CELA</div>
            </div>

            <!-- CJE -->
            <div class="program-card cje">
                <div class="program-icon">
                    <img src="https://dcc.edu.ph/wp-content/uploads/2021/04/CJE-WEB.png" 
                         alt="Criminal Justice Education" 
                         class="loading"
                         onload="this.classList.add('loaded')">
                </div>
                <h3 class="program-name">Criminal Justice Education</h3>
                <div class="program-abbreviation">CJE</div>
            </div>

            <!-- HME -->
            <div class="program-card hme">
                <div class="program-icon">
                    <img src="https://dcc.edu.ph/wp-content/uploads/2021/04/HM-LOGO-revised-2.0-768x765.png" 
                         alt="Hospitality Management Education" 
                         class="loading"
                         onload="this.classList.add('loaded')">
                </div>
                <h3 class="program-name">Hospitality Management Education</h3>
                <div class="program-abbreviation">HME</div>
            </div>

            <!-- ITE -->
            <div class="program-card ite">
                <div class="program-icon">
                    <img src="https://dcc.edu.ph/wp-content/uploads/2021/04/ITE-WEB.png" 
                         alt="Information Technology Education" 
                         class="loading"
                         onload="this.classList.add('loaded')">
                </div>
                <h3 class="program-name">Information Technology Education</h3>
                <div class="program-abbreviation">ITE</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image loading
    document.querySelectorAll('.program-icon img').forEach(img => {
        if (img.complete) {
            img.classList.add('loaded');
        }
    });
});
</script>