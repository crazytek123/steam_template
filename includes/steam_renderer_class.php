<?php
/**
 * Steam Frontend Renderer Class
 * Handles template rendering and frontend display logic
 */

if (!defined('ABSPATH')) exit;

class DemontekSteamRenderer {
    
    private $template_loader;
    private $fields_manager;
    private $layout_manager;
    
    public function __construct() {
        $this->template_loader = new DemontekSteamTemplateLoader();
        $this->fields_manager = new DemontekSteamFields();
        $this->layout_manager = new DemontekSteamLayout();
        
        $this->setup_hooks();
    }
    
    private function setup_hooks() {
        add_action('wp_head', array($this, 'add_frontend_head_data'));
        add_filter('body_class', array($this, 'add_body_classes'));
        add_action('wp_footer', array($this, 'add_frontend_footer_scripts'));
    }
    
    /**
     * Render the complete Steam layout
     */
    public function render_steam_layout($post_id = null) {
        if (!$post_id) $post_id = get_the_ID();
        
        $template_data = $this->get_template_data($post_id);
        
        // Start output buffering
        ob_start();
        
        try {
            $this->render_layout_wrapper($template_data);
        } catch (Exception $e) {
            ob_end_clean();
            $this->render_error_fallback($e->getMessage());
            return;
        }
        
        return ob_get_clean();
    }
    
    /**
     * Get template data for rendering
     */
    public function get_template_data($post_id) {
        $cache_key = demontek_steam_get_cache_key($post_id, 'template_data');
        $cached_data = wp_cache_get($cache_key, 'demontek_steam');
        
        if ($cached_data !== false && !WP_DEBUG) {
            return $cached_data;
        }
        
        $post = get_post($post_id);
        $steam_data = $this->fields_manager->get_steam_field_data($post_id);
        
        $template_data = array(
            'post_id' => $post_id,
            'post' => $post,
            'steam_data' => $steam_data,
            'extra_sidebar' => get_post_meta($post_id, '_demontek_steam_extra_sidebar', true),
            'content_layout' => get_post_meta($post_id, '_demontek_steam_content_layout', true) ?: 'right',
            'featured_image_url' => $this->get_featured_image_url($post_id, $steam_data),
            'trailer_count' => isset($steam_data['trailers']) ? count($steam_data['trailers']) : 0,
            'main_trailer_id' => $this->get_main_trailer_id($steam_data),
            'prev_post' => get_previous_post(),
            'next_post' => get_next_post(),
            'is_preview' => demontek_steam_is_preview(),
            'completion_status' => $this->fields_manager->get_field_completion_status($post_id)
        );
        
        wp_cache_set($cache_key, $template_data, 'demontek_steam', 600); // 10 minutes
        
        return $template_data;
    }
    
    /**
     * Render the main layout wrapper
     */
    private function render_layout_wrapper($template_data) {
        extract($template_data);
        
        // Hide admin bar for preview mode
        if ($is_preview) {
            show_admin_bar(false);
        }
        
        ?>
        <!-- Dimension Display for Resizable Layout -->
        <div class="demontek-dimension-display" id="dimensionDisplay">
            Video: <span id="videoWidth">0</span>px | Info: <span id="infoWidth">0</span>px
        </div>

        <div class="demontek-steam-layout-wrapper" data-post-id="<?php echo esc_attr($post_id); ?>" data-version="<?php echo esc_attr(DEMONTEK_STEAM_VERSION); ?>">
            <div class="demontek-steam-container" id="steamContainer">
                
                <?php $this->render_header_section($template_data); ?>
                <?php $this->render_purchase_section($template_data); ?>
                <?php $this->render_content_section($template_data); ?>
                
            </div>
        </div>

        <?php if (comments_open() || get_comments_number()): ?>
        <div class="demontek-steam-comments-section">
            <h3>💬 Comments & Discussion</h3>
            <div style="background: rgba(103, 193, 245, 0.1); padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 12px; color: #67c1f5;">
                🔧 Clean comment integration in v1.6.2 - focused on content, not controls!
            </div>
            <?php comments_template(); ?>
        </div>
        <?php endif;
    }
    
    /**
     * Render header section
     */
    private function render_header_section($template_data) {
        extract($template_data);
        
        ?>
        <div class="demontek-steam-header-section">
            
            <?php $this->render_navigation_arrows($prev_post, $next_post); ?>
            
            <div class="demontek-steam-video-section" id="videoSection">
                
                <!-- Resize Handle for Draggable Layout -->
                <div class="demontek-resize-handle" id="resizeHandle" title="Drag to resize sections"></div>
                
                <?php $this->render_video_content($template_data); ?>
                
            </div>

            <div class="demontek-steam-info-section" id="infoSection">
                
                <?php $this->render_featured_image($template_data); ?>
                <?php $this->render_game_header($template_data); ?>
                
            </div>

            <?php $this->render_navigation_dots(); ?>
            
        </div>
        <?php
    }
    
    /**
     * Render navigation arrows
     */
    private function render_navigation_arrows($prev_post, $next_post) {
        if (!$prev_post && !$next_post) return;
        
        ?>
        <div class="demontek-nav-arrow-left <?php echo $prev_post ? '' : 'disabled'; ?>" 
             onclick="<?php echo $prev_post ? "window.location.href='" . esc_js(get_permalink($prev_post)) . "'" : ''; ?>" 
             title="<?php echo $prev_post ? 'Previous: ' . esc_attr($prev_post->post_title) : 'No previous post'; ?>">
            ‹
        </div>
        <div class="demontek-nav-arrow-right <?php echo $next_post ? '' : 'disabled'; ?>" 
             onclick="<?php echo $next_post ? "window.location.href='" . esc_js(get_permalink($next_post)) . "'" : ''; ?>" 
             title="<?php echo $next_post ? 'Next: ' . esc_attr($next_post->post_title) : 'No next post'; ?>">
            ›
        </div>
        <?php
    }
    
    /**
     * Render video content section
     */
    private function render_video_content($template_data) {
        extract($template_data);
        
        ?>
        <div class="demontek-main-trailer">
            <?php if ($main_trailer_id): ?>
                <iframe class="demontek-trailer-iframe" 
                        src="https://www.youtube.com/embed/<?php echo esc_attr($main_trailer_id); ?>?autoplay=0&mute=0&controls=1&showinfo=0&rel=0&enablejsapi=1&origin=<?php echo urlencode(get_site_url()); ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        loading="lazy"></iframe>
                
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
                        🎮 v1.6.2 supports up to 5 trailers with enhanced switching!
                    </div>
                    <?php if (current_user_can('edit_posts')): ?>
                        <a href="<?php echo get_edit_post_link($post_id); ?>" style="color: #67c1f5; text-decoration: underline;">Edit Post to Add Trailers</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($trailer_count > 1): ?>
            <?php $this->render_trailer_gallery($steam_data); ?>
        <?php endif; ?>
        <?php
    }
    
    /**
     * Render trailer gallery
     */
    private function render_trailer_gallery($steam_data) {
        $trailer_labels = array(1 => 'Main', 2 => 'Gameplay', 3 => 'Features', 4 => 'Extended', 5 => 'Bonus');
        
        ?>
        <div class="demontek-trailer-gallery">
            <?php foreach ($steam_data['trailers'] as $num => $trailer_url): 
                $youtube_id = demontek_steam_extract_youtube_id($trailer_url);
                if (!$youtube_id) continue;
                
                $is_active = ($num == 2 && !empty($steam_data['trailers'][2])) || ($num == 1 && empty($steam_data['trailers'][2]));
            ?>
                <div class="demontek-trailer-thumb <?php echo $is_active ? 'active' : ''; ?>" 
                     onclick="switchTrailer(<?php echo $num; ?>, '<?php echo esc_js($youtube_id); ?>')"
                     data-trailer="<?php echo $num; ?>"
                     data-youtube-id="<?php echo esc_attr($youtube_id); ?>"
                     title="Switch to <?php echo $trailer_labels[$num] ?? "Trailer {$num}"; ?>">
                    
                    <div class="demontek-thumb-image" style="background-image: url('<?php echo esc_url(demontek_steam_get_youtube_thumbnail($youtube_id, 'medium')); ?>');"></div>
                    <div class="demontek-thumb-label"><?php echo $trailer_labels[$num] ?? "Trailer {$num}"; ?></div>
                    
                    <div class="demontek-thumb-loading" style="display: none;">
                        <div class="demontek-spinner"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="demontek-trailer-counter">
            <span class="demontek-counter-text"><?php echo count($steam_data['trailers']); ?> trailer<?php echo count($steam_data['trailers']) != 1 ? 's' : ''; ?> available</span>
        </div>
        <?php
    }
    
    /**
     * Render featured image section
     */
    private function render_featured_image($template_data) {
        extract($template_data);
        
        ?>
        <div class="demontek-featured-image-section">
            <?php if ($featured_image_url): ?>
                <img src="<?php echo esc_url($featured_image_url); ?>" 
                     alt="<?php echo esc_attr($post->post_title); ?>" 
                     class="demontek-featured-image">
                <div class="demontek-featured-overlay"></div>
            <?php else: ?>
                <div class="demontek-featured-placeholder">
                    Featured Game Image
                    <div style="font-size: 12px; opacity: 0.7; margin-top: 10px;">
                        Clean display in v1.6.2
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render game header info
     */
    private function render_game_header($template_data) {
        extract($template_data);
        
        ?>
        <div class="demontek-game-header">
            
            <h1 class="demontek-game-title"><?php echo esc_html($post->post_title); ?></h1>
            <div class="demontek-game-subtitle">
                <?php echo esc_html(get_post_meta($post_id, 'game_genre', true) ?: 'Gaming Content'); ?>
            </div>
            
            <?php $this->render_meta_info($template_data); ?>
            <?php $this->render_description($template_data); ?>
            
        </div>
        <?php
    }
    
    /**
     * Render meta information
     */
    private function render_meta_info($template_data) {
        extract($template_data);
        
        ?>
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
        <?php
    }
    
    /**
     * Render description text
     */
    private function render_description($template_data) {
        extract($template_data);
        
        ?>
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
        <?php
    }
    
    /**
     * Render navigation dots
     */
    private function render_navigation_dots() {
        ?>
        <div class="demontek-navigation-dots">
            <div class="demontek-nav-dot active" title="Main View"></div>
            <div class="demontek-nav-dot" title="Gallery View"></div>
            <div class="demontek-nav-dot" title="Reviews"></div>
            <div class="demontek-nav-dot" title="Details"></div>
            <div class="demontek-nav-dot" title="Info"></div>
        </div>
        <?php
    }
    
    /**
     * Render purchase section
     */
    private function render_purchase_section($template_data) {
        extract($template_data);
        
        ?>
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
                           <span>📺</span> Watch on YouTube</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($steam_data['steam_link'])): ?>
                        <a href="<?php echo esc_url($steam_data['steam_link']); ?>" 
                           class="demontek-store-link secondary" target="_blank" rel="noopener">
                           <span>🎮</span> View on Steam</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($steam_data['amazon_link'])): ?>
                        <a href="<?php echo esc_url($steam_data['amazon_link']); ?>" 
                           class="demontek-store-link secondary" target="_blank" rel="noopener">
                           <span>🛒</span> Amazon</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render content section
     */
    private function render_content_section($template_data) {
        ?>
        <div class="demontek-steam-content-section">
            <?php 
            $this->template_loader->load_template_part('content', 'section', $template_data);
            ?>
        </div>
        <?php
    }
    
    /**
     * Get featured image URL with fallback
     */
    private function get_featured_image_url($post_id, $steam_data) {
        $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
        
        if (!$featured_image_url && !empty($steam_data['trailers'][1])) {
            $youtube_id = demontek_steam_extract_youtube_id($steam_data['trailers'][1]);
            if ($youtube_id) {
                $featured_image_url = demontek_steam_get_youtube_thumbnail($youtube_id);
            }
        }
        
        return $featured_image_url;
    }
    
    /**
     * Get main trailer YouTube ID
     */
    private function get_main_trailer_id($steam_data) {
        return demontek_steam_get_main_trailer_id($steam_data);
    }
    
    /**
     * Add frontend head data
     */
    public function add_frontend_head_data() {
        if (!is_single()) return;
        
        $core = demontek_steam()->getCore();
        if (!$core || !$core->should_use_steam_layout(get_the_ID())) {
            return;
        }
        
        $template_data = $this->get_template_data(get_the_ID());
        
        echo '<script type="text/javascript">';
        echo 'window.demontekSteamTemplateData = ' . wp_json_encode($template_data) . ';';
        echo '</script>';
        
        // Add preview-specific styles
        if (demontek_steam_is_preview()) {
            echo '<style type="text/css">
                .admin-bar { display: none !important; }
                body { margin-top: 0 !important; }
                .demontek-steam-layout-wrapper {
                    max-width: 100% !important;
                    margin: 0 !important;
                    padding: 10px !important;
                }
                .demontek-action-buttons a {
                    padding: 6px 12px !important;
                    font-size: 11px !important;
                    margin: 2px !important;
                }
            </style>';
        }
    }
    
    /**
     * Add body classes
     */
    public function add_body_classes($classes) {
        if (!is_single()) return $classes;
        
        $core = demontek_steam()->getCore();
        if (!$core || !$core->should_use_steam_layout(get_the_ID())) {
            return $classes;
        }
        
        $classes[] = 'demontek-steam-layout';
        $classes[] = 'demontek-steam-v' . str_replace('.', '-', DEMONTEK_STEAM_VERSION);
        
        if (demontek_steam_is_preview()) {
            $classes[] = 'demontek-steam-preview';
        }
        
        return $classes;
    }
    
    /**
     * Add frontend footer scripts
     */
    public function add_frontend_footer_scripts() {
        if (!is_single()) return;
        
        $core = demontek_steam()->getCore();
        if (!$core || !$core->should_use_steam_layout(get_the_ID())) {
            return;
        }
        
        echo '<script type="text/javascript">';
        echo 'console.log("🎮 Demontek Steam Template v' . DEMONTEK_STEAM_VERSION . ' loaded");';
        echo '</script>';
    }
    
    /**
     * Render error fallback
     */
    private function render_error_fallback($error_message) {
        ?>
        <div class="demontek-steam-error">
            <h3>Steam Template Error</h3>
            <p>There was an error rendering the Steam layout.</p>
            <?php if (WP_DEBUG): ?>
                <p><strong>Debug Info:</strong> <?php echo esc_html($error_message); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}