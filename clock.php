<div class="clock-overlay text-center text-white position-absolute w-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <span id="liveDateTime" class="datetime-display"></span>
            </div>
        </div>
    </div>
</div>

<style>
.clock-overlay {
    position: absolute;
    top: 97%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 3;
    text-align: center;
    width: 100%;
    pointer-events: none;
    background: linear-gradient(135deg, rgba(105, 180, 30, 0.9) 0%, rgba(24, 124, 25, 0.9) 100%);
    padding: 8px 0;
    backdrop-filter: blur(10px);
    border-top: 2px solid rgba(255, 255, 255, 0.3);
}

.datetime-display {
    font-size: 0.95rem;
    font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
    letter-spacing: 0.5px;
}

/* Responsive design */
@media (max-width: 768px) {
    .datetime-display {
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .clock-overlay {
        top: 97.5%;
        padding: 6px 0;
    }
    
    .datetime-display {
        font-size: 0.8rem;
    }
}
</style>

<script>
function updateClock() {
    const now = new Date();
    const dateTimeElement = document.getElementById("liveDateTime");

    // Set timezone to Philippines (Manila)
    const timeZone = 'Asia/Manila';

    // All in one line: Day, Date, Time, Timezone
    const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        hour12: true,
        timeZone: timeZone,
        timeZoneName: 'short'
    };

    dateTimeElement.textContent = now.toLocaleDateString('en-US', options);
}

// Initialize clock immediately
updateClock();

// Update every second
setInterval(updateClock, 1000);

// Also update when page becomes visible again
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        updateClock();
    }
});
</script>