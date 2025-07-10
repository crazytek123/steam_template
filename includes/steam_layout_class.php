<?php
/**
 * Steam Layout Manager Class
 * Handles layout mechanics, responsiveness, and interactive features
 */

if (!defined('ABSPATH')) exit;

class DemontekSteamLayout {
    
    private $core;
    private $fields;
    
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_footer', array($this, 'render_layout_scripts'));
        add_action('wp_head', array($this, 'render_layout_styles'));
        add_action('demontek_steam_template_loaded', array($this, 'init'));
    }
    
    /**
     * Get component instances
     */
    private function get_core() {
        if (!$this->core) {
            $this->core = demontek_steam_get_component('core');
        }
        return $this->core;
    }
    
    private function get_fields() {
        if (!$this->fields) {
            $this->fields = demontek_steam_get_component('fields');
        }
        return $this->fields;
    }
    
    /**
     * Initialize layout manager
     */
    public function init() {
        // Layout manager initialized
        do_action('demontek_steam_layout_loaded');
    }
    
    /**
     * Render layout-specific styles
     */
    public function render_layout_styles() {
        if (!demontek_steam_is_steam_post()) {
            return;
        }
        
        $post_id = get_the_ID();
        $show_zones = demontek_steam_get_field($post_id, '_demontek_steam_zones');
        $is_preview = isset($_GET['demontek_preview']) || isset($_GET['mobile']);
        
        ?>
        <style id="demontek-layout-styles">
            /* Layout Manager Styles v<?php echo DEMONTEK_STEAM_VERSION; ?> */
            
            /* Dimension Display for Resizable Layout */
            .demontek-dimension-display {
                position: fixed;
                top: 10px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.9);
                color: #67c1f5;
                padding: 8px 15px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: bold;
                z-index: 1001;
                border: 1px solid rgba(103, 193, 245, 0.4);
                backdrop-filter: blur(10px);
                display: none;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                transition: all 0.3s ease;
            }

            .demontek-dimension-display.show {
                display: block;
                animation: fadeInDimension 0.3s ease;
            }

            @keyframes fadeInDimension {
                from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
                to { opacity: 1; transform: translateX(-50%) translateY(0); }
            }
            
            /* Resize Handle */
            .demontek-resize-handle {
                position: absolute;
                top: 0;
                right: -5px;
                width: 10px;
                height: 100%;
                background: rgba(103, 193, 245, 0.3);
                cursor: ew-resize;
                z-index: 200;
                transition: all 0.3s ease;
                border-left: 2px dashed rgba(103, 193, 245, 0.5);
            }

            .demontek-resize-handle:hover {
                background: rgba(103, 193, 245, 0.5);
                width: 15px;
                right: -7px;
            }

            .demontek-resize-handle::after {
                content: '⟷';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: #67c1f5;
                font-size: 14px;
                font-weight: bold;
            }
            
            /* Layout Zones for Debugging */
            <?php if ($show_zones && current_user_can('edit_posts')): ?>
            .demontek-zone-label {
                position: absolute;
                top: 5px;
                left: 5px;
                background: rgba(190, 238, 17, 0.9);
                color: #1b2838;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: bold;
                z-index: 50;
                opacity: 1;
                transition: opacity 0.3s ease;
                pointer-events: none;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            }

            .demontek-steam-container {
                border: 2px dashed rgba(190, 238, 17, 0.5) !important;
            }
            
            .demontek-steam-video-section::before {
                content: 'VIDEO SECTION';
                position: absolute;
                top: 5px;
                left: 5px;
                background: rgba(190, 238, 17, 0.9);
                color: #1b2838;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: bold;
                z-index: 50;
            }
            
            .demontek-steam-info-section::before {
                content: 'INFO SECTION';
                position: absolute;
                top: 5px;
                left: 5px;
                background: rgba(190, 238, 17, 0.9);
                color: #1b2838;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: bold;
                z-index: 50;
            }
            <?php endif; ?>
            
            /* Mobile Layout Adjustments */
            @media (max-width: 768px) {
                .demontek-resize-handle {
                    display: none !important;
                }
                
                .demontek-dimension-display {
                    display: none !important;
                }
                
                .demontek-steam-video-section {
                    flex: none !important;
                    width: 100% !important;
                }
                
                .demontek-steam-info-section {
                    flex: none !important;
                    width: 100% !important;
                }
            }
            
            <?php if ($is_preview): ?>
            /* Preview Mode Adjustments */
            .demontek-steam-layout-wrapper {
                margin: 0 !important;
                border-radius: 0 !important;
            }
            
            <?php if (isset($_GET['mobile'])): ?>
            /* Mobile Preview Specific Styles */
            body {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .demontek-steam-layout-wrapper .demontek-store-link {
                padding: 4px 8px !important;
                font-size: 10px !important;
                margin: 2px !important;
            }
            
            .demontek-steam-layout-wrapper .demontek-action-buttons {
                gap: 6px !important;
                flex-wrap: wrap !important;
            }
            
            .demontek-steam-layout-wrapper .demontek-game-title {
                font-size: 18px !important;
            }
            
            .demontek-steam-layout-wrapper .demontek-trailer-thumb {
                min-width: 100px !important;
                height: 60px !important;
            }
            <?php endif; ?>
            <?php endif; ?>
        </style>
        <?php
    }
    
    /**
     * Render layout scripts
     */
    public function render_layout_scripts() {
        if (!demontek_steam_is_steam_post()) {
            return;
        }
        
        $post_id = get_the_ID();
        $steam_data = get_demontek_steam_field_data($post_id);
        
        ?>
        <script id="demontek-layout-scripts">
        // Steam Layout Manager v<?php echo DEMONTEK_STEAM_VERSION; ?>
        (function() {
            'use strict';
            
            // Layout Manager Object
            window.steamLayout = {
                version: '<?php echo DEMONTEK_STEAM_VERSION; ?>',
                isResizing: false,
                startX: 0,
                startVideoWidth: 0,
                startInfoWidth: 0,
                currentPostIndex: 0,
                
                // Initialize layout system
                init: function() {
                    this.setupResizeHandling();
                    this.setupMobileDetection();
                    this.setupNavigationDots();
                    this.setupKeyboardShortcuts();
                    this.setupTrailerSwitching();
                    this.setupPlayButton();
                    
                    console.log('🎮 Steam Layout Manager v' + this.version + ' initialized - Modular & optimized!');
                },
                
                // Setup resizable layout
                setupResizeHandling: function() {
                    const resizeHandle = document.getElementById('resizeHandle');
                    const videoSection = document.getElementById('videoSection');
                    const infoSection = document.getElementById('infoSection');
                    const dimensionDisplay = document.getElementById('dimensionDisplay');
                    
                    if (!resizeHandle || !videoSection || !infoSection) return;
                    
                    const self = this;
                    
                    resizeHandle.addEventListener('mousedown', function(e) {
                        self.startResize(e, videoSection, infoSection, dimensionDisplay);
                    });
                    
                    document.addEventListener('mousemove', function(e) {
                        self.handleResize(e, videoSection, infoSection);
                    });
                    
                    document.addEventListener('mouseup', function() {
                        self.endResize(resizeHandle, dimensionDisplay);
                    });
                    
                    // Update dimensions on window resize
                    window.addEventListener('resize', function() {
                        self.updateDimensions(videoSection, infoSection);
                    });
                    
                    // Initial dimension update
                    this.updateDimensions(videoSection, infoSection);
                },
                
                startResize: function(e, videoSection, infoSection, dimensionDisplay) {
                    this.isResizing = true;
                    this.startX = e.clientX;
                    
                    const videoRect = videoSection.getBoundingClientRect();
                    const infoRect = infoSection.getBoundingClientRect();
                    this.startVideoWidth = videoRect.width;
                    this.startInfoWidth = infoRect.width;
                    
                    if (dimensionDisplay) {
                        dimensionDisplay.classList.add('show');
                    }
                    
                    document.body.style.cursor = 'ew-resize';
                    document.body.style.userSelect = 'none';
                    
                    e.preventDefault();
                },
                
                handleResize: function(e, videoSection, infoSection) {
                    if (!this.isResizing || !videoSection || !infoSection) return;
                    
                    const deltaX = e.clientX - this.startX;
                    const headerRect = videoSection.parentElement.getBoundingClientRect();
                    const containerWidth = headerRect.width - 40; // Account for padding
                    
                    let newVideoWidth = this.startVideoWidth + deltaX;
                    let newInfoWidth = this.startInfoWidth - deltaX;
                    
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
                    
                    // Calculate flex values
                    const totalWidth = newVideoWidth + newInfoWidth;
                    const videoFlex = newVideoWidth / totalWidth;
                    const infoFlex = newInfoWidth / totalWidth;
                    
                    // Apply flex values
                    videoSection.style.flex = videoFlex;
                    infoSection.style.flex = infoFlex;
                    
                    this.updateDimensions(videoSection, infoSection);
                },
                
                endResize: function(resizeHandle, dimensionDisplay) {
                    if (!this.isResizing) return;
                    
                    this.isResizing = false;
                    document.body.style.cursor = '';
                    document.body.style.userSelect = '';
                    
                    if (dimensionDisplay) {
                        setTimeout(() => {
                            dimensionDisplay.classList.remove('show');
                        }, 2000);
                    }
                },
                
                updateDimensions: function(videoSection, infoSection) {
                    const videoWidthSpan = document.getElementById('videoWidth');
                    const infoWidthSpan = document.getElementById('infoWidth');
                    
                    if (videoSection && infoSection && videoWidthSpan && infoWidthSpan) {
                        const videoRect = videoSection.getBoundingClientRect();
                        const infoRect = infoSection.getBoundingClientRect();
                        videoWidthSpan.textContent = Math.round(videoRect.width);
                        infoWidthSpan.textContent = Math.round(infoRect.width);
                    }
                },
                
                // Setup mobile detection
                setupMobileDetection: function() {
                    const container = document.getElementById('steamContainer');
                    if (!container) return;
                    
                    const checkMobile = () => {
                        const isMobile = window.innerWidth <= 768;
                        container.classList.toggle('mobile-mode', isMobile);
                    };
                    
                    checkMobile();
                    window.addEventListener('resize', checkMobile);
                },
                
                // Setup navigation dots
                setupNavigationDots: function() {
                    const dots = document.querySelectorAll('.demontek-nav-dot');
                    dots.forEach((dot, index) => {
                        dot.addEventListener('click', () => {
                            dots.forEach(d => d.classList.remove('active'));
                            dot.classList.add('active');
                            
                            // Enhanced animation
                            dot.style.transform = 'scale(1.3)';
                            setTimeout(() => {
                                dot.style.transform = '';
                            }, 200);
                        });
                    });
                },
                
                // Setup keyboard shortcuts
                setupKeyboardShortcuts: function() {
                    document.addEventListener('keydown', (e) => {
                        // Only for admin users
                        if (!<?php echo current_user_can('edit_posts') ? 'true' : 'false'; ?>) return;
                        
                        switch(e.key.toLowerCase()) {
                            case 'f':
                                if (e.ctrlKey || e.metaKey) {
                                    e.preventDefault();
                                    this.toggleFullscreen();
                                }
                                break;
                            case 'z':
                                if (e.ctrlKey || e.metaKey) {
                                    e.preventDefault();
                                    this.toggleZones();
                                }
                                break;
                            case 'e':
                                if (e.ctrlKey || e.metaKey) {
                                    e.preventDefault();
                                    this.openEditMode();
                                }
                                break;
                        }
                    });
                },
                
                // Setup trailer switching
                setupTrailerSwitching: function() {
                    window.switchTrailer = (trailerNum, youtubeId) => {
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
                        
                        // Update active states
                        thumbs.forEach(thumb => {
                            thumb.classList.toggle('active', thumb.dataset.trailer == trailerNum);
                            if (thumb.dataset.trailer == trailerNum) {
                                thumb.style.transform = 'scale(1.05)';
                                setTimeout(() => {
                                    thumb.style.transform = '';
                                }, 200);
                            }
                        });
                        
                        // Fade transition
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
                        }, 300);
                    };
                },
                
                // Setup play button
                setupPlayButton: function() {
                    const playButton = document.querySelector('.demontek-play-button');
                    const overlay = document.querySelector('.demontek-trailer-overlay');
                    const iframe = document.querySelector('.demontek-trailer-iframe');
                    
                    if (playButton && overlay && iframe) {
                        playButton.addEventListener('click', () => {
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
                        });
                    }
                },
                
                // Toggle fullscreen
                toggleFullscreen: function() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.log('Fullscreen failed:', err);
                        });
                    } else {
                        document.exitFullscreen();
                    }
                },
                
                // Toggle debug zones
                toggleZones: function() {
                    const container = document.getElementById('steamContainer');
                    if (container) {
                        container.classList.toggle('show-zones');
                        const isShowing = container.classList.contains('show-zones');
                        console.log('Debug zones:', isShowing ? 'enabled' : 'disabled');
                    }
                },
                
                // Open edit mode
                openEditMode: function() {
                    const editUrl = '<?php echo get_edit_post_link(get_the_ID()); ?>';
                    if (editUrl) {
                        window.open(editUrl, '_blank');
                    }
                },
                
                // Get layout state
                getLayoutState: function() {
                    const videoSection = document.getElementById('videoSection');
                    const infoSection = document.getElementById('infoSection');
                    
                    if (!videoSection || !infoSection) return null;
                    
                    const videoRect = videoSection.getBoundingClientRect();
                    const infoRect = infoSection.getBoundingClientRect();
                    
                    return {
                        videoWidth: Math.round(videoRect.width),
                        infoWidth: Math.round(infoRect.width),
                        totalWidth: Math.round(videoRect.width + infoRect.width),
                        videoFlex: videoSection.style.flex || '0.6',
                        infoFlex: infoSection.style.flex || '1'
                    };
                },
                
                // Save layout state to localStorage (if available)
                saveLayoutState: function() {
                    const state = this.getLayoutState();
                    if (state && window.localStorage) {
                        try {
                            localStorage.setItem('demontek_steam_layout_' + <?php echo get_the_ID(); ?>, JSON.stringify(state));
                        } catch (e) {
                            // localStorage not available or quota exceeded
                        }
                    }
                },
                
                // Restore layout state from localStorage (if available)
                restoreLayoutState: function() {
                    if (!window.localStorage) return;
                    
                    try {
                        const saved = localStorage.getItem('demontek_steam_layout_' + <?php echo get_the_ID(); ?>);
                        if (saved) {
                            const state = JSON.parse(saved);
                            const videoSection = document.getElementById('videoSection');
                            const infoSection = document.getElementById('infoSection');
                            
                            if (videoSection && infoSection && state.videoFlex && state.infoFlex) {
                                videoSection.style.flex = state.videoFlex;
                                infoSection.style.flex = state.infoFlex;
                                this.updateDimensions(videoSection, infoSection);
                            }
                        }
                    } catch (e) {
                        // Error parsing saved state
                    }
                }
            };
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => steamLayout.init());
            } else {
                steamLayout.init();
            }
            
            // Save layout state before page unload
            window.addEventListener('beforeunload', () => {
                steamLayout.saveLayoutState();
            });
            
            // Restore layout state after initialization
            setTimeout(() => {
                steamLayout.restoreLayoutState();
            }, 100);
            
        })();
        </script>
        
        <!-- Dimension Display Element -->
        <div class="demontek-dimension-display" id="dimensionDisplay">
            Video: <span id="videoWidth">0</span>px | Info: <span id="infoWidth">0</span>px
        </div>
        <?php
    }
    
    /**
     * Get layout configuration for a post
     */
    public function get_layout_config($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $fields = $this->get_fields();
        if (!$fields) {
            return $this->get_default_layout_config();
        }
        
        return array(
            'use_steam' => $fields->get_field_value($post_id, '_demontek_steam_use'),
            'content_layout' => $fields->get_field_value($post_id, '_demontek_steam_content_layout', 'right'),
            'extra_sidebar' => $fields->get_field_value($post_id, '_demontek_steam_extra_sidebar'),
            'show_zones' => $fields->get_field_value($post_id, '_demontek_steam_zones'),
            'resizable' => true,
            'mobile_responsive' => true,
            'version' => DEMONTEK_STEAM_VERSION
        );
    }
    
    /**
     * Get default layout configuration
     */
    private function get_default_layout_config() {
        return array(
            'use_steam' => false,
            'content_layout' => 'right',
            'extra_sidebar' => false,
            'show_zones' => false,
            'resizable' => true,
            'mobile_responsive' => true,
            'version' => DEMONTEK_STEAM_VERSION
        );
    }
    
    /**
     * Get layout classes for container
     */
    public function get_layout_classes($post_id = null) {
        $config = $this->get_layout_config($post_id);
        $classes = array('demontek-steam-container');
        
        $classes[] = 'layout-' . $config['content_layout'];
        
        if ($config['extra_sidebar']) {
            $classes[] = 'has-extra-sidebar';
        }
        
        if ($config['show_zones'] && current_user_can('edit_posts')) {
            $classes[] = 'show-zones';
        }
        
        if ($config['resizable']) {
            $classes[] = 'resizable-layout';
        }
        
        if (wp_is_mobile()) {
            $classes[] = 'mobile-mode';
        }
        
        if (isset($_GET['demontek_preview'])) {
            $classes[] = 'preview-mode';
        }
        
        if (isset($_GET['mobile'])) {
            $classes[] = 'mobile-preview';
        }
        
        return implode(' ', $classes);
    }
    
    /**
     * Check if layout should be resizable
     */
    public function is_resizable($post_id = null) {
        $config = $this->get_layout_config($post_id);
        return $config['resizable'] && !wp_is_mobile() && !isset($_GET['mobile']);
    }
    
    /**
     * Check if zones should be shown
     */
    public function should_show_zones($post_id = null) {
        $config = $this->get_layout_config($post_id);
        return $config['show_zones'] && current_user_can('edit_posts');
    }
    
    /**
     * Get responsive breakpoints
     */
    public function get_responsive_breakpoints() {
        return array(
            'mobile' => 768,
            'tablet' => 1024,
            'desktop' => 1200,
            'large' => 1400
        );
    }
    
    /**
     * Get layout debug information
     */
    public function get_debug_info($post_id = null) {
        $config = $this->get_layout_config($post_id);
        $breakpoints = $this->get_responsive_breakpoints();
        
        return array(
            'config' => $config,
            'breakpoints' => $breakpoints,
            'is_resizable' => $this->is_resizable($post_id),
            'should_show_zones' => $this->should_show_zones($post_id),
            'classes' => $this->get_layout_classes($post_id),
            'version' => DEMONTEK_STEAM_VERSION
        );
    }
    
    /**
     * Render layout debug panel (for admins)
     */
    public function render_debug_panel($post_id = null) {
        if (!current_user_can('edit_posts')) {
            return;
        }
        
        $debug_info = $this->get_debug_info($post_id);
        
        ?>
        <div id="demontek-layout-debug" style="position: fixed; bottom: 20px; right: 20px; background: rgba(0,0,0,0.9); color: white; padding: 15px; border-radius: 8px; font-size: 12px; z-index: 10000; max-width: 300px; display: none;">
            <h4 style="margin: 0 0 10px 0; color: #67c1f5;">Layout Debug v<?php echo DEMONTEK_STEAM_VERSION; ?></h4>
            
            <div><strong>Layout:</strong> <?php echo $debug_info['config']['content_layout']; ?></div>
            <div><strong>Resizable:</strong> <?php echo $debug_info['is_resizable'] ? 'Yes' : 'No'; ?></div>
            <div><strong>Zones:</strong> <?php echo $debug_info['should_show_zones'] ? 'Visible' : 'Hidden'; ?></div>
            <div><strong>Extra Sidebar:</strong> <?php echo $debug_info['config']['extra_sidebar'] ? 'Yes' : 'No'; ?></div>
            
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #333;">
                <strong>Keyboard Shortcuts:</strong><br>
                Ctrl+F: Fullscreen<br>
                Ctrl+Z: Toggle Zones<br>
                Ctrl+E: Edit Post
            </div>
            
            <button onclick="document.getElementById('demontek-layout-debug').style.display='none'" style="position: absolute; top: 5px; right: 5px; background: none; border: none; color: white; cursor: pointer;">×</button>
        </div>
        
        <script>
        // Show debug panel with keyboard shortcut
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                const panel = document.getElementById('demontek-layout-debug');
                panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            }
        });
        </script>
        <?php
    }
    
    /**
     * Add layout body classes
     */
    public function add_body_classes($classes) {
        if (!demontek_steam_is_steam_post()) {
            return $classes;
        }
        
        $classes[] = 'demontek-steam-layout';
        $classes[] = 'steam-v' . str_replace('.', '-', DEMONTEK_STEAM_VERSION);
        
        $config = $this->get_layout_config();
        $classes[] = 'steam-layout-' . $config['content_layout'];
        
        if ($config['extra_sidebar']) {
            $classes[] = 'steam-has-extra-sidebar';
        }
        
        if ($this->is_resizable()) {
            $classes[] = 'steam-resizable';
        }
        
        return $classes;
    }
}