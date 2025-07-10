<?php
/**
 * Steam Video Section Template Part
 * Handles main trailer and trailer gallery
 */

if (!defined('ABSPATH')) exit;

// Get main trailer ID and trailer count
$main_trailer_id = demontek_steam_get_main_trailer_id($steam_data);
$trailer_count = isset($steam_data['trailers']) ? count($steam_data['trailers']) : 0;

?>
<div class="demontek-steam-video-section" id="videoSection">
    
    <!-- Resize Handle (for desktop resizable layout) -->
    <div class="demontek-resize-handle" id="resizeHandle" title="Drag to resize sections"></div>
    
    <!-- Main Trailer -->
    <div class="demontek-main-trailer">
        <?php if ($main_trailer_id): ?>
            <!-- YouTube Embed -->
            <iframe class="demontek-trailer-iframe" 
                    src="https://www.youtube.com/embed/<?php echo esc_attr($main_trailer_id); ?>?autoplay=0&mute=0&controls=1&showinfo=0&rel=0&enablejsapi=1&origin=<?php echo urlencode(get_site_url()); ?>" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    loading="lazy"></iframe>
            
            <!-- Play Button Overlay -->
            <div class="demontek-trailer-overlay">
                <div class="demontek-play-button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
            </div>
            
        <?php else: ?>
            <!-- No Trailer Placeholder -->
            <div class="demontek-trailer-placeholder">
                <h3>No Trailer Available</h3>
                <p>Add trailer_1 or trailer_2 custom field</p>
                <div style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
                    🎮 v<?php echo DEMONTEK_STEAM_VERSION; ?> supports up to 5 trailers with enhanced switching!
                </div>
                <?php if (current_user_can('edit_posts')): ?>
                    <a href="<?php echo get_edit_post_link($post->ID); ?>" style="color: #67c1f5; text-decoration: underline;">
                        Edit Post to Add Trailers
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($trailer_count > 1): ?>
    <!-- Trailer Gallery -->
    <div class="demontek-trailer-gallery">
        <?php 
        $labels = array(1 => 'Main', 2 => 'Gameplay', 3 => 'Features', 4 => 'Extended', 5 => 'Bonus');
        
        foreach ($steam_data['trailers'] as $num => $trailer_url): 
            $youtube_id = demontek_steam_extract_youtube_id($trailer_url);
            if (!$youtube_id) continue;
            
            $is_active = ($youtube_id === $main_trailer_id);
        ?>
            <div class="demontek-trailer-thumb <?php echo $is_active ? 'active' : ''; ?>" 
                 onclick="switchTrailer(<?php echo $num; ?>, '<?php echo esc_js($youtube_id); ?>')"
                 data-trailer="<?php echo $num; ?>"
                 data-youtube-id="<?php echo esc_attr($youtube_id); ?>"
                 title="Switch to <?php echo $labels[$num] ?? "Trailer {$num}"; ?>">
                
                <!-- Thumbnail Image -->
                <div class="demontek-thumb-image" 
                     style="background-image: url('https://img.youtube.com/vi/<?php echo esc_attr($youtube_id); ?>/mqdefault.jpg');">
                </div>
                
                <!-- Label -->
                <div class="demontek-thumb-label">
                    <?php echo $labels[$num] ?? "Trailer {$num}"; ?>
                </div>
                
                <!-- Loading Indicator -->
                <div class="demontek-thumb-loading" style="display: none;">
                    <div class="demontek-spinner"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Trailer Counter -->
    <div class="demontek-trailer-counter">
        <span class="demontek-counter-text">
            <?php echo $trailer_count; ?> trailer<?php echo $trailer_count != 1 ? 's' : ''; ?> available
        </span>
    </div>
    <?php endif; ?>
    
</div>

<script>
// Video section specific functionality
if (typeof window.steamTemplate === 'undefined') {
    window.steamTemplate = {};
}

// Enhanced trailer switching function
window.steamTemplate.switchTrailer = function(trailerNum, youtubeId) {
    const iframe = document.querySelector('.demontek-trailer-iframe');
    const thumbs = document.querySelectorAll('.demontek-trailer-thumb');
    const overlay = document.querySelector('.demontek-trailer-overlay');
    
    if (!iframe || !youtubeId) return;
    
    // Show loading state
    const clickedThumb = document.querySelector(`[data-trailer="${trailerNum}"]`);
    if (clickedThumb) {
        const loadingEl = clickedThumb.querySelector('.demontek-thumb-loading');
        if (loadingEl) loadingEl.style.display = 'flex';
    }
    
    // Update active states with animation
    thumbs.forEach(thumb => {
        thumb.classList.toggle('active', thumb.dataset.trailer == trailerNum);
        if (thumb.dataset.trailer == trailerNum) {
            thumb.style.transform = 'scale(1.05)';
            setTimeout(() => {
                thumb.style.transform = '';
            }, 200);
        }
    });
    
    // Smooth video transition
    iframe.style.opacity = '0.5';
    iframe.style.transform = 'scale(0.98)';
    
    setTimeout(() => {
        const origin = encodeURIComponent(window.location.origin);
        const newSrc = `https://www.youtube.com/embed/${youtubeId}?autoplay=0&mute=0&controls=1&showinfo=0&rel=0&enablejsapi=1&origin=${origin}`;
        
        iframe.src = newSrc;
        iframe.style.opacity = '1';
        iframe.style.transform = 'scale(1)';
        
        // Show overlay again
        if (overlay) {
            overlay.style.display = 'flex';
            overlay.style.opacity = '1';
        }
        
        // Hide loading state
        if (clickedThumb) {
            const loadingEl = clickedThumb.querySelector('.demontek-thumb-loading');
            if (loadingEl) loadingEl.style.display = 'none';
        }
        
        console.log('🎬 Switched to trailer:', trailerNum, youtubeId);
    }, 300);
};

// Global function for backward compatibility
window.switchTrailer = window.steamTemplate.switchTrailer;

// Play button functionality
document.addEventListener('DOMContentLoaded', function() {
    const playButton = document.querySelector('.demontek-play-button');
    const overlay = document.querySelector('.demontek-trailer-overlay');
    const iframe = document.querySelector('.demontek-trailer-iframe');
    
    if (playButton && overlay && iframe) {
        playButton.addEventListener('click', function() {
            // Hide overlay with animation
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300);
            
            // Add autoplay to iframe
            let src = iframe.src;
            if (src.includes('autoplay=0')) {
                src = src.replace('autoplay=0', 'autoplay=1');
            } else {
                src += '&autoplay=1';
            }
            iframe.src = src;
            iframe.focus();
            
            console.log('▶️ Main trailer autoplay activated');
        });
    }
});
</script>