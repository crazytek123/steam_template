<?php
/**
 * Demontek Steam Template - Single Post Template v1.6.1
 * 
 * Template for displaying posts with Steam gaming layout
 * Features: Resizable layout, enhanced YouTube integration, clean interface
 * REMOVED: Floating controls (zones/full/edit buttons) for cleaner experience
 */

get_header();

$post_id = get_the_ID();
$steam_data = get_demontek_steam_field_data($post_id);
$extra_sidebar = get_post_meta($post_id, '_demontek_steam_extra_sidebar', true);
$content_layout = get_post_meta($post_id, '_demontek_steam_content_layout', true) ?: 'right';

$post = get_post($post_id);
$featured_image_url = get_the_post_thumbnail_url($post_id, 'full');

if (!$featured_image_url && !empty($steam_data['trailers'][1])) {
    $youtube_id = extract_youtube_id($steam_data['trailers'][1]);
    if ($youtube_id) {
        $featured_image_url = "https://img.youtube.com/vi/{$youtube_id}/maxresdefault.jpg";
    }
}

$prev_post = get_previous_post();
$next_post = get_next_post();

function extract_youtube_id($url) {
    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

function get_main_trailer_id($steam_data) {
    if (!empty($steam_data['trailers'][2])) {
        return extract_youtube_id($steam_data['trailers'][2]);
    }
    if (!empty($steam_data['trailers'][1])) {
        return extract_youtube_id($steam_data['trailers'][1]);
    }
    return '';
}

$main_trailer_id = get_main_trailer_id($steam_data);
$trailer_count = isset($steam_data['trailers']) ? count($steam_data['trailers']) : 0;

// Hide admin bar for clean preview experience
if (isset($_GET['demontek_preview']) || isset($_GET['mobile'])) {
    show_admin_bar(false);
}
?>

<!-- Dimension Display for Resizable Layout -->
<div class="demontek-dimension-display" id="dimensionDisplay">
    Video: <span id="videoWidth">0</span>px | Info: <span id="infoWidth">0</span>px
</div>

<div class="demontek-steam-layout-wrapper" data-post-id="<?php echo $post_id; ?>" data-version="1.6.1">

    <div class="demontek-steam-container" id="steamContainer">
        
        <div class="demontek-steam-header-section">
            
            <?php if ($prev_post || $next_post): ?>
                <div class="demontek-nav-arrow-left <?php echo $prev_post ? '' : 'disabled'; ?>" 
                     onclick="<?php echo $prev_post ? "window.location.href='" . get_permalink($prev_post) . "'" : ''; ?>" 
                     title="<?php echo $prev_post ? 'Previous: ' . esc_attr($prev_post->post_title) : 'No previous post'; ?>">
                    ‹
                </div>
                <div class="demontek-nav-arrow-right <?php echo $next_post ? '' : 'disabled'; ?>" 
                     onclick="<?php echo $next_post ? "window.location.href='" . get_permalink($next_post) . "'" : ''; ?>" 
                     title="<?php echo $next_post ? 'Next: ' . esc_attr($next_post->post_title) : 'No next post'; ?>">
                    ›
                </div>
            <?php endif; ?>

            <div class="demontek-steam-video-section" id="videoSection">
                
                <!-- Resize Handle for Draggable Layout -->
                <div class="demontek-resize-handle" id="resizeHandle" title="Drag to resize sections"></div>
                
                <div class="demontek-main-trailer">
                    <?php if ($main_trailer_id): ?>
                        <iframe class="demontek-trailer-iframe" 
                                src="https://www.youtube.com/embed/<?php echo esc_attr($main_trailer_id); ?>?autoplay=0&mute=0&controls=1&showinfo=0&rel=0&enablejsapi=1&origin=<?php echo urlencode(get_site_url()); ?>" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                loading="lazy"></iframe>
                        
                        <!-- Enhanced play button with better functionality -->
                        <div class="demontek-trailer-overlay" onclick="playMainTrailer()">
                            <div class="demontek-play-button">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="demontek-trailer-placeholder">
                            <h3>No Trailer Available</h3>
                            <p>Add trailer_1 or trailer_2 custom field</p>
                            <div style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
                                ?? v1.6.1 supports up to 5 trailers with enhanced switching!
                            </div>
                            <?php if (current_user_can('edit_posts')): ?>
                                <a href="<?php echo get_edit_post_link($post_id); ?>" style="color: #67c1f5; text-decoration: underline;">Edit Post to Add Trailers</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($trailer_count > 1): ?>
                <div class="demontek-trailer-gallery">
                    <?php 
                    foreach ($steam_data['trailers'] as $num => $trailer_url): 
                        $youtube_id = extract_youtube_id($trailer_url);
                        if (!$youtube_id) continue;
                        
                        $is_active = ($num == 2 && !empty($steam_data['trailers'][2])) || ($num == 1 && empty($steam_data['trailers'][2]));
                        $labels = [1 => 'Main', 2 => 'Gameplay', 3 => 'Features', 4 => 'Extended', 5 => 'Bonus'];
                    ?>
                        <div class="demontek-trailer-thumb <?php echo $is_active ? 'active' : ''; ?>" 
                             onclick="switchTrailer(<?php echo $num; ?>, '<?php echo esc_js($youtube_id); ?>')"
                             data-trailer="<?php echo $num; ?>"
                             data-youtube-id="<?php echo esc_attr($youtube_id); ?>"
                             title="Switch to <?php echo $labels[$num] ?? "Trailer {$num}"; ?>">
                            
                            <!-- Preload thumbnails for faster loading -->
                            <div class="demontek-thumb-image" style="background-image: url('https://img.youtube.com/vi/<?php echo esc_attr($youtube_id); ?>/mqdefault.jpg');"></div>
                            <div class="demontek-thumb-label"><?php echo $labels[$num] ?? "Trailer {$num}"; ?></div>
                            
                            <!-- Loading indicator -->
                            <div class="demontek-thumb-loading" style="display: none;">
                                <div class="demontek-spinner"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Trailer counter for v1.6.1 -->
                <div class="demontek-trailer-counter">
                    <span class="demontek-counter-text"><?php echo $trailer_count; ?> trailer<?php echo $trailer_count != 1 ? 's' : ''; ?> available</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="demontek-steam-info-section" id="infoSection">
                
                <div class="demontek-featured-image-section">
                    
                    <?php if ($featured_image_url): ?>
                        <img src="<?php echo esc_url($featured_image_url); ?>" 
                             alt="<?php echo esc_attr($post->post_title); ?>" class="demontek-featured-image">
                        <div class="demontek-featured-overlay"></div>
                    <?php else: ?>
                        <div class="demontek-featured-placeholder">
                            Featured Game Image
                            <div style="font-size: 12px; opacity: 0.7; margin-top: 10px;">
                                Clean display in v1.6.1
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="demontek-game-header">
                    
                    <h1 class="demontek-game-title"><?php echo esc_html($post->post_title); ?></h1>
                    <div class="demontek-game-subtitle">
                        <?php echo esc_html(get_post_meta($post_id, 'game_genre', true) ?: 'Gaming Content'); ?>
                    </div>
                    
                    <div class="demontek-meta-info">
                        <div class="demontek-meta-row">
                            <span class="demontek-meta-label">Trailers:</span>
                            <span class="demontek-meta-value"><?php echo $trailer_count; ?>/5</span>
                        </div>
                        <div class="demontek-meta-row">
                            <span class="demontek-meta-label">Published:</span>
                            <span class="demontek-meta-value"><?php echo get_the_date(); ?></span>
                        </div>
                        <div class="demontek-meta-row">
                            <span class="demontek-meta-label">Layout:</span>
                            <span class="demontek-meta-value"><?php echo ucfirst($content_layout); ?></span>
                        </div>
                        <div class="demontek-meta-row">
                            <span class="demontek-meta-label">Reviews:</span>
                            <span class="demontek-meta-value"><?php echo count(array_filter([
                                $steam_data['review_1'] ?? '',
                                $steam_data['review_2'] ?? '',
                                $steam_data['review_3'] ?? ''
                            ])); ?></span>
                        </div>
                        <?php if (!empty($steam_data['developer'])): ?>
                        <div class="demontek-meta-row">
                            <span class="demontek-meta-label">Developer:</span>
                            <span class="demontek-meta-value"><?php echo esc_html($steam_data['developer']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($steam_data['platforms'])): ?>
                        <div class="demontek-meta-row">
                            <span class="demontek-meta-label">Platforms:</span>
                            <span class="demontek-meta-value"><?php echo esc_html($steam_data['platforms']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($steam_data['release_date'])): ?>
                        <div class="demontek-meta-row">
                            <span class="demontek-meta-label">Release Date:</span>
                            <span class="demontek-meta-value"><?php echo esc_html($steam_data['release_date']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="demontek-description-text">
                        <?php 
                        if (!empty($steam_data['ai_summary'])) {
                            echo esc_html(wp_trim_words($steam_data['ai_summary'], 50));
                        } elseif (!empty($steam_data['ai_excerpt'])) {
                            echo esc_html(wp_trim_words($steam_data['ai_excerpt'], 50));
                        } elseif (!empty($post->post_excerpt)) {
                            echo esc_html(wp_trim_words($post->post_excerpt, 50));
                        } elseif (!empty($post->post_content)) {
                            echo esc_html(wp_trim_words(strip_tags($post->post_content), 50));
                        } else {
                            echo 'No description available. Add ai_summary custom field for best results.';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="demontek-navigation-dots">
                <div class="demontek-nav-dot active" title="Main View"></div>
                <div class="demontek-nav-dot" title="Gallery View"></div>
                <div class="demontek-nav-dot" title="Reviews"></div>
                <div class="demontek-nav-dot" title="Details"></div>
                <div class="demontek-nav-dot" title="Info"></div>
            </div>
        </div>

        <!-- Enhanced Purchase section with better positioning -->
        <div class="demontek-purchase-section-horizontal">
            
            <div class="demontek-purchase-content">
                <div class="demontek-price-info">
                    <div class="demontek-discount-badge">FREE</div>
                    <div class="demontek-current-price">Free to Watch</div>
                </div>
                
                <div class="demontek-action-buttons">
                    <?php if (!empty($steam_data['original_link'])): ?>
                        <a href="<?php echo esc_url($steam_data['original_link']); ?>" 
                           class="demontek-store-link primary" target="_blank" rel="noopener">
                           <span>??</span> Watch on YouTube</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($steam_data['steam_link'])): ?>
                        <a href="<?php echo esc_url($steam_data['steam_link']); ?>" 
                           class="demontek-store-link secondary" target="_blank" rel="noopener">
                           <span>??</span> View on Steam</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($steam_data['amazon_link'])): ?>
                        <a href="<?php echo esc_url($steam_data['amazon_link']); ?>" 
                           class="demontek-store-link secondary" target="_blank" rel="noopener">
                           <span>??</span> Amazon</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="demontek-steam-content-section">
            
            <?php if ($extra_sidebar): ?>
            <div class="demontek-content-sidebar extra-left">
                <div class="demontek-sidebar-title">?? Additional Info</div>
                <div class="demontek-sidebar-content">
                    <?php
                    $categories = get_the_category();
                    if ($categories) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 10px 0;">Categories</h4>';
                        foreach ($categories as $category) {
                            echo '<span style="display: inline-block; background: rgba(103, 193, 245, 0.2); color: #67c1f5; padding: 4px 8px; border-radius: 4px; margin: 2px; font-size: 11px;">' . esc_html($category->name) . '</span> ';
                        }
                    }
                    
                    $tags = get_the_tags();
                    if ($tags) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 10px 0;">Tags</h4>';
                        foreach ($tags as $tag) {
                            echo '<span style="display: inline-block; background: rgba(103, 193, 245, 0.1); color: #8f98a0; padding: 4px 8px; border-radius: 4px; margin: 2px; font-size: 11px;">' . esc_html($tag->name) . '</span> ';
                        }
                    }
                    
                    if (!empty($steam_data['developer'])) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 5px 0;">Developer</h4>';
                        echo '<p>' . esc_html($steam_data['developer']) . '</p>';
                    }
                    
                    if (!empty($steam_data['platforms'])) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 5px 0;">Platforms</h4>';
                        echo '<p>' . esc_html($steam_data['platforms']) . '</p>';
                    }
                    
                    if (!empty($steam_data['release_date'])) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 5px 0;">Release Date</h4>';
                        echo '<p>' . esc_html($steam_data['release_date']) . '</p>';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (in_array($content_layout, ['left', 'both'])): ?>
            <div class="demontek-content-sidebar left">
                <div class="demontek-sidebar-title">?? Additional Media</div>
                <div class="demontek-sidebar-content">
                    <?php
                    echo '<h4 style="color: #67c1f5; margin: 15px 0 10px 0;">Post Info</h4>';
                    echo '<p style="font-size: 13px; color: #acb2b8;">Published: ' . get_the_date() . '</p>';
                    echo '<p style="font-size: 13px; color: #acb2b8;">Author: ' . get_the_author() . '</p>';
                    
                    if (!empty($steam_data['platforms'])) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 5px 0;">Platforms</h4>';
                        echo '<p style="font-size: 13px; color: #acb2b8;">' . esc_html($steam_data['platforms']) . '</p>';
                    }
                    
                    if (!empty($steam_data['release_date'])) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 5px 0;">Release Date</h4>';
                        echo '<p style="font-size: 13px; color: #acb2b8;">' . esc_html($steam_data['release_date']) . '</p>';
                    }
                    
                    if (!empty($steam_data['game_genre'])) {
                        echo '<h4 style="color: #67c1f5; margin: 15px 0 5px 0;">Genre</h4>';
                        echo '<p style="font-size: 13px; color: #acb2b8;">' . esc_html($steam_data['game_genre']) . '</p>';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="demontek-content-main">
                <div class="demontek-content-title"><?php echo esc_html($post->post_title); ?></div>
                <div class="demontek-content-text">
                    <?php 
                    if ($post->post_content) {
                        echo wpautop($post->post_content);
                    } else {
                        echo '<p>No content available. Add content to this post for the main description.</p>';
                        if (current_user_can('edit_posts')) {
                            echo '<p><a href="' . get_edit_post_link($post_id) . '" style="color: #67c1f5;">Edit this post to add content</a></p>';
                        }
                    }
                    ?>
                </div>
                
                <!-- Enhanced AI content display -->
                <?php if (!empty($steam_data['ai_excerpt']) && $steam_data['ai_excerpt'] !== $steam_data['ai_summary']): ?>
                <div class="demontek-ai-excerpt">
                    <h4 style="color: #67c1f5; margin-bottom: 10px;">?? AI Summary</h4>
                    <p style="font-style: italic; color: #acb2b8;"><?php echo esc_html($steam_data['ai_excerpt']); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="demontek-author-bio">
                    <h4 style="color: #67c1f5; margin-bottom: 10px;">About the Author</h4>
                    <p><strong><?php echo get_the_author(); ?></strong> - Published on <?php echo get_the_date(); ?></p>
                    <?php if (get_the_author_meta('description')): ?>
                        <p style="font-size: 14px; color: #acb2b8; margin-top: 10px;"><?php echo esc_html(get_the_author_meta('description')); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (in_array($content_layout, ['right', 'both'])): ?>
            <div class="demontek-content-sidebar right">
                <div class="demontek-sidebar-title">? Reviews & Comments</div>
                <div class="demontek-sidebar-content">
                    <?php
                    $has_reviews = false;
                    for ($i = 1; $i <= 3; $i++) {
                        if (!empty($steam_data["review_{$i}"])) {
                            $has_reviews = true;
                            echo '<div class="demontek-review-item">';
                            echo '<div class="demontek-review-header">Community Review #' . $i . '</div>';
                            echo '<div class="demontek-review-text">' . esc_html(wp_trim_words($steam_data["review_{$i}"], 30)) . '</div>';
                            echo '</div>';
                        }
                    }
                    
                    if (!$has_reviews) {
                        echo '<p style="color: #8f98a0; font-style: italic;">No reviews available yet.</p>';
                        if (current_user_can('edit_posts')) {
                            echo '<p style="font-size: 12px;"><a href="' . get_edit_post_link($post_id) . '" style="color: #67c1f5;">Add review_1, review_2, review_3 custom fields</a></p>';
                        }
                    }
                    
                    $comments_count = get_comments_number();
                    if ($comments_count > 0) {
                        echo '<div style="margin-top: 20px; padding: 15px; background: rgba(103, 193, 245, 0.1); border-radius: 6px;">';
                        echo '<h4 style="color: #67c1f5; margin: 0 0 10px 0;">?? WordPress Comments</h4>';
                        echo '<p style="margin: 0; font-size: 13px; color: #acb2b8;">' . $comments_count . ' comment' . ($comments_count != 1 ? 's' : '') . ' on this post.</p>';
                        echo '<a href="#comments" style="color: #67c1f5; font-size: 12px;">View Comments</a>';
                        echo '</div>';
                    }
                    
                    // v1.6.1 Clean interface showcase
                    echo '<div style="margin-top: 20px; padding: 12px; background: rgba(103, 193, 245, 0.1); border-radius: 6px; border-left: 3px solid #67c1f5;">';
                    echo '<h4 style="color: #67c1f5; margin: 0 0 8px 0; font-size: 12px;">?? v1.6.1 Updates</h4>';
                    echo '<ul style="margin: 0; padding-left: 15px; font-size: 11px; color: #acb2b8;">';
                    echo '<li>Cleaned interface (no confusing buttons)</li>';
                    echo '<li>Better admin preview experience</li>';
                    echo '<li>Streamlined for gaming content</li>';
                    echo '<li>Resizable layout still available</li>';
                    echo '</ul>';
                    echo '</div>';
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (comments_open() || get_comments_number()): ?>
<div class="demontek-steam-comments-section">
    <h3>?? Comments & Discussion</h3>
    <div style="background: rgba(103, 193, 245, 0.1); padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 12px; color: #67c1f5;">
        ?? Clean comment integration in v1.6.1 - focused on content, not controls!
    </div>
    <?php comments_template(); ?>
</div>
<?php endif; ?>

<script>
// CLEANED: Enhanced JavaScript for v1.6.1 - REMOVED floating controls, kept core functionality
function playMainTrailer() {
    const iframe = document.querySelector('.demontek-trailer-iframe');
    const overlay = document.querySelector('.demontek-trailer-overlay');
    
    if (iframe && overlay) {
        // Hide overlay with animation
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 300);
        
        // Get current src and add autoplay
        let src = iframe.src;
        if (src.includes('autoplay=0')) {
            src = src.replace('autoplay=0', 'autoplay=1');
        } else {
            src += '&autoplay=1';
        }
        
        // Reload iframe with autoplay
        iframe.src = src;
        
        // Focus on iframe for better user experience
        iframe.focus();
    }
}

function switchTrailer(trailerNum, youtubeId) {
    const iframe = document.querySelector('.demontek-trailer-iframe');
    const thumbs = document.querySelectorAll('.demontek-trailer-thumb');
    const overlay = document.querySelector('.demontek-trailer-overlay');
    
    if (iframe && youtubeId) {
        // Show loading state
        const clickedThumb = document.querySelector(`[data-trailer="${trailerNum}"]`);
        if (clickedThumb) {
            const loadingEl = clickedThumb.querySelector('.demontek-thumb-loading');
            if (loadingEl) loadingEl.style.display = 'flex';
        }
        
        // Update active states with enhanced animation
        thumbs.forEach(thumb => {
            thumb.classList.toggle('active', thumb.dataset.trailer == trailerNum);
            if (thumb.dataset.trailer == trailerNum) {
                thumb.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    thumb.style.transform = '';
                }, 200);
            }
        });
        
        // Fade out current video
        iframe.style.opacity = '0.5';
        iframe.style.transform = 'scale(0.98)';
        
        // Switch video after short delay for better UX
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
        }, 300);
    }
}

// Enhanced resize functionality with live measurements (KEPT - this is useful)
let isResizing = false;
let startX = 0;
let startVideoWidth = 0;
let startInfoWidth = 0;

const resizeHandle = document.getElementById('resizeHandle');
const videoSection = document.getElementById('videoSection');
const infoSection = document.getElementById('infoSection');
const dimensionDisplay = document.getElementById('dimensionDisplay');
const videoWidthSpan = document.getElementById('videoWidth');
const infoWidthSpan = document.getElementById('infoWidth');

function updateDimensions() {
    if (videoSection && infoSection && videoWidthSpan && infoWidthSpan) {
        const videoRect = videoSection.getBoundingClientRect();
        const infoRect = infoSection.getBoundingClientRect();
        videoWidthSpan.textContent = Math.round(videoRect.width);
        infoWidthSpan.textContent = Math.round(infoRect.width);
    }
}

if (resizeHandle) {
    resizeHandle.addEventListener('mousedown', (e) => {
        isResizing = true;
        startX = e.clientX;
        
        const videoRect = videoSection.getBoundingClientRect();
        const infoRect = infoSection.getBoundingClientRect();
        startVideoWidth = videoRect.width;
        startInfoWidth = infoRect.width;
        
        if (dimensionDisplay) {
            dimensionDisplay.classList.add('show');
        }
        document.body.style.cursor = 'ew-resize';
        document.body.style.userSelect = 'none';
        
        // Add visual feedback
        resizeHandle.style.background = 'rgba(103, 193, 245, 0.8)';
        
        e.preventDefault();
    });
}

document.addEventListener('mousemove', (e) => {
    if (!isResizing || !videoSection || !infoSection) return;
    
    const deltaX = e.clientX - startX;
    const headerRect = videoSection.parentElement.getBoundingClientRect();
    const containerWidth = headerRect.width - 40; // Account for padding and nav arrows
    
    let newVideoWidth = startVideoWidth + deltaX;
    let newInfoWidth = startInfoWidth - deltaX;
    
    // Set minimum widths
    const minWidth = 300;
    if (newVideoWidth < minWidth) {
        newVideoWidth = minWidth;
        newInfoWidth = containerWidth - newVideoWidth;
    }
    if (newInfoWidth < minWidth) {
        newInfoWidth = minWidth;
        newVideoWidth = containerWidth - newInfoWidth;
    }
    
    // Calculate flex values - both sections now use flex
    const totalWidth = newVideoWidth + newInfoWidth;
    const videoFlex = newVideoWidth / totalWidth;
    const infoFlex = newInfoWidth / totalWidth;
    
    // Apply flex values to both sections with smooth transition
    videoSection.style.flex = videoFlex;
    infoSection.style.flex = infoFlex;
    
    updateDimensions();
});

document.addEventListener('mouseup', () => {
    if (isResizing) {
        isResizing = false;
        document.body.style.cursor = '';
        document.body.style.userSelect = '';
        
        // Remove visual feedback
        if (resizeHandle) {
            resizeHandle.style.background = '';
        }
        
        if (dimensionDisplay) {
            setTimeout(() => {
                dimensionDisplay.classList.remove('show');
            }, 2000);
        }
    }
});

// Clean initialization for v1.6.1
document.addEventListener('DOMContentLoaded', function() {
    // Preload trailer thumbnails for faster switching
    const trailerThumbs = document.querySelectorAll('.demontek-trailer-thumb');
    trailerThumbs.forEach(thumb => {
        const youtubeId = thumb.dataset.youtubeId;
        if (youtubeId) {
            // Preload high quality thumbnail
            const img = new Image();
            img.src = `https://img.youtube.com/vi/${youtubeId}/maxresdefault.jpg`;
        }
    });
    
    // Enhanced mobile layout detection
    function checkMobileLayout() {
        const container = document.getElementById('steamContainer');
        const isMobile = window.innerWidth <= 768;
        
        if (container) {
            if (isMobile) {
                container.classList.add('mobile-mode');
            } else {
                container.classList.remove('mobile-mode');
            }
        }
    }
    
    checkMobileLayout();
    window.addEventListener('resize', checkMobileLayout);
    
    // Initialize dimensions display
    updateDimensions();
    
    // Enhanced navigation dots functionality
    const dots = document.querySelectorAll('.demontek-nav-dot');
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            dots.forEach(d => d.classList.remove('active'));
            dot.classList.add('active');
            
            // Enhanced dot click animation
            dot.style.transform = 'scale(1.3)';
            setTimeout(() => {
                dot.style.transform = '';
            }, 200);
        });
    });
    
    // Console log for version verification
    console.log('?? Demontek Steam Template v1.6.1 loaded - Clean interface edition!');
    console.log('?? Resizable layout: Drag the blue divider between video and info sections');
    console.log('?? Cleaned: Removed confusing floating controls for better user experience');
});
</script>

<?php get_footer(); ?>