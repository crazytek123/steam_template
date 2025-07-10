// Demontek Steam Template - Frontend JavaScript v1.0

document.addEventListener('DOMContentLoaded', function() {
    initializeDemontekSteam();
});

function initializeDemontekSteam() {
    checkMobileLayout();
    setupEventListeners();
    
    if (typeof demontekSteamData !== 'undefined') {
        if (demontekSteamData.showZones) {
            document.getElementById('demontekSteamContainer').classList.add('show-zones');
        }
    }
}

function checkMobileLayout() {
    const container = document.getElementById('demontekSteamContainer');
    const isMobile = window.innerWidth <= 768;
    
    if (container) {
        if (isMobile) {
            container.classList.add('mobile-mode');
        } else {
            container.classList.remove('mobile-mode');
        }
    }
}

function setupEventListeners() {
    window.addEventListener('resize', checkMobileLayout);
    
    document.addEventListener('keydown', function(e) {
        switch(e.key) {
            case 'f':
            case 'F':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    toggleFullscreen();
                }
                break;
            case 'z':
            case 'Z':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    toggleZones();
                }
                break;
        }
    });
    
    document.addEventListener('fullscreenchange', function() {
        const button = document.querySelector('.demontek-fullscreen-btn');
        if (button) {
            if (document.fullscreenElement) {
                button.innerHTML = '? Exit';
            } else {
                button.innerHTML = '? Full';
            }
        }
    });
    
    const dots = document.querySelectorAll('.demontek-nav-dot');
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            dots.forEach(d => d.classList.remove('active'));
            dot.classList.add('active');
        });
    });
}

function switchTrailer(trailerNum, youtubeId) {
    const iframe = document.querySelector('.demontek-trailer-iframe');
    const thumbs = document.querySelectorAll('.demontek-trailer-thumb');
    
    if (iframe && youtubeId) {
        thumbs.forEach(thumb => {
            thumb.classList.toggle('active', thumb.dataset.trailer == trailerNum);
        });
        
        iframe.style.opacity = '0.7';
        setTimeout(() => {
            iframe.src = `https://www.youtube.com/embed/${youtubeId}`;
            iframe.style.opacity = '1';
        }, 300);
    }
}

function toggleZones() {
    const container = document.getElementById('demontekSteamContainer');
    const button = document.querySelector('.demontek-zone-toggle-btn');
    
    if (container && button) {
        container.classList.toggle('show-zones');
        
        if (container.classList.contains('show-zones')) {
            button.innerHTML = '?? Hide Zones';
            button.classList.add('active');
        } else {
            button.innerHTML = '?? Zones';
            button.classList.remove('active');
        }
    }
}

function toggleFullscreen() {
    const button = document.querySelector('.demontek-fullscreen-btn');
    
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(() => {
            if (button) button.innerHTML = '? Exit';
        }).catch(err => {
            console.log('Fullscreen failed:', err);
        });
    } else {
        document.exitFullscreen().then(() => {
            if (button) button.innerHTML = '? Full';
        });
    }
}

// Global functions for inline handlers
window.switchTrailer = switchTrailer;
window.toggleZones = toggleZones;
window.toggleFullscreen = toggleFullscreen;