// js/script.js
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    const sidebarLinks = document.querySelectorAll('#sidebar .nav-link');
    const sidebarToggleBtn = document.querySelector('.sidebar-toggle');
    
    // Initialize sidebar state
    function initSidebar() {
        const sidebarState = localStorage.getItem('sidebarState');
        const isMobile = window.innerWidth <= 767.98;
        
        if (isMobile) {
            // On mobile: always start closed
            sidebar.classList.remove('mobile-open');
            sidebar.classList.add('collapsed');
            content.classList.add('expanded');
            if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        } else {
            // On desktop: use saved state
            if (sidebarState === 'collapsed') {
                sidebar.classList.add("collapsed");
                content.classList.add("expanded");
            } else {
                sidebar.classList.remove("collapsed");
                content.classList.remove("expanded");
            }
        }
    }
    
    // Toggle sidebar
    function toggleSidebar() {
        const isMobile = window.innerWidth <= 767.98;
        
        if (isMobile) {
            const isOpening = !sidebar.classList.contains("mobile-open");
            
            if (isOpening) {
                // Opening sidebar on mobile
                sidebar.classList.add("mobile-open");
                sidebar.classList.remove("collapsed");
                content.classList.remove("expanded");
                if (sidebarOverlay) sidebarOverlay.classList.add("active");
                document.body.style.overflow = 'hidden';
            } else {
                // Closing sidebar on mobile
                closeMobileSidebar();
            }
        } else {
            // Desktop toggle
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("expanded");
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains("collapsed");
            localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
        }
    }
    
    // Close sidebar on mobile
    function closeMobileSidebar() {
        sidebar.classList.remove("mobile-open");
        sidebar.classList.add("collapsed");
        content.classList.add("expanded");
        if (sidebarOverlay) sidebarOverlay.classList.remove("active");
        document.body.style.overflow = '';
    }
    
    // Close sidebar on mobile when clicking overlay
    function closeSidebarOnOverlay() {
        if (window.innerWidth <= 767.98) {
            closeMobileSidebar();
        }
    }
    
    // Close sidebar on mobile when clicking a link
    function closeSidebarOnLinkClick() {
        if (window.innerWidth <= 767.98) {
            closeMobileSidebar();
        }
    }
    
    // Handle window resize
    function handleResize() {
        const isMobile = window.innerWidth <= 767.98;
        
        if (isMobile) {
            // On mobile resize, ensure sidebar is closed if not explicitly open
            if (!sidebar.classList.contains("mobile-open")) {
                closeMobileSidebar();
            }
        } else {
            // On desktop resize, ensure mobile classes are removed
            sidebar.classList.remove("mobile-open");
            if (sidebarOverlay) sidebarOverlay.classList.remove("active");
            document.body.style.overflow = '';
            initSidebar(); // Re-initialize desktop state
        }
    }
    
    // Event listeners
    if (toggleBtn) {
        toggleBtn.addEventListener("click", toggleSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", closeSidebarOnOverlay);
    }
    
    // Add click event to sidebar links (except logout)
    sidebarLinks.forEach(link => {
        if (!link.getAttribute('href') || !link.getAttribute('href').includes('logout.php')) {
            link.addEventListener('click', closeSidebarOnLinkClick);
        }
    });
    
    // Handle internal sidebar toggle button
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', toggleSidebar);
    }
    
    // Initialize
    initSidebar();
    
    // Handle window resize with debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 250);
    });
    
    // Keyboard accessibility - close sidebar on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && window.innerWidth <= 767.98 && sidebar.classList.contains('mobile-open')) {
            closeMobileSidebar();
        }
    });
});

// View toggle functionality (if you add grid/list view later)
document.addEventListener('DOMContentLoaded', function() {
    const gridViewRadio = document.getElementById('gridView');
    const listViewRadio = document.getElementById('listView');
    const gridViewContent = document.getElementById('gridViewContent');
    const accordionView = document.querySelector('.accordion');
    
    if (gridViewRadio && listViewRadio && gridViewContent && accordionView) {
        gridViewRadio.addEventListener('change', function() {
            if (this.checked) {
                gridViewContent.style.display = 'grid';
                accordionView.style.display = 'none';
            }
        });
        
        listViewRadio.addEventListener('change', function() {
            if (this.checked) {
                gridViewContent.style.display = 'none';
                accordionView.style.display = 'block';
            }
        });
    }
});