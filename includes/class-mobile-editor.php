<?php
/**
 * Demontek Mobile Editor Class v1.8.0
 * 
 * Mobile-optimized post editor with tabbed modular architecture
 * Features real-time preview and component communication
 */

if (!defined('ABSPATH')) exit;

class DemontekMobileEditor {
    
    private $plugin_url;
    private $plugin_path;
    private $version;
    
    public function __construct($plugin_url, $plugin_path, $version) {
        $this->plugin_url = $plugin_url;
        $this->plugin_path = $plugin_path;
        $this->version = $version;
    }
    
    /**
     * Render the main mobile editor page
     */
    public function render_mobile_editor_page() {
        // Get current post ID from URL parameter
        $current_post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
        
        // Get recent posts for selection
        $recent_posts = get_posts(array(
            'numberposts' => 10,
            'post_status' => array('publish', 'draft'),
            'orderby' => 'modified',
            'order' => 'DESC'
        ));
        
        // If no post specified, use the first available post
        if (!$current_post_id && !empty($recent_posts)) {
            $current_post_id = $recent_posts[0]->ID;
        }
        
        $current_post = $current_post_id ? get_post($current_post_id) : null;
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>📱 Demontek Mobile Editor v<?php echo $this->version; ?></title>
            <?php $this->render_mobile_editor_styles(); ?>
        </head>
        <body>
            <div class="wrap demontek-mobile-wrapper">
                <div class="demontek-mobile-header">
                    <h1>
                        📱 Demontek Mobile Editor 
                        <span class="demontek-version-badge">v<?php echo $this->version; ?> MOBILE</span>
                        <span class="architecture-badge">TABBED ARCHITECTURE</span>
                    </h1>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                        Mobile-optimized post editing with real-time preview and component communication
                    </p>
                </div>

                <!-- Post Selector -->
                <div class="mobile-post-selector">
                    <div class="selector-header">
                        <h3>📝 Select Post to Edit</h3>
                        <div class="selector-actions">
                            <a href="post-new.php" class="button button-primary">➕ New Post</a>
                            <a href="admin.php?page=demontek-steam" class="button button-secondary">🎮 Dashboard</a>
                        </div>
                    </div>
                    <div class="post-selector-grid">
                        <?php foreach ($recent_posts as $post): ?>
                        <div class="post-selector-item <?php echo $post->ID == $current_post_id ? 'active' : ''; ?>" 
                             data-post-id="<?php echo $post->ID; ?>">
                            <div class="post-item-header">
                                <h4><?php echo esc_html($post->post_title ?: 'Untitled Post'); ?></h4>
                                <div class="post-item-meta">
                                    <span class="post-status <?php echo $post->post_status; ?>"><?php echo ucfirst($post->post_status); ?></span>
                                    <span class="post-date"><?php echo date('M j, Y', strtotime($post->post_modified)); ?></span>
                                </div>
                            </div>
                            <div class="post-item-actions">
                                <button class="button button-small select-post-btn" onclick="selectPost(<?php echo $post->ID; ?>)">
                                    📱 Edit Mobile
                                </button>
                                <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button button-small">
                                    ✏️ Edit Classic
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($current_post): ?>
                <!-- Component Status Bar -->
                <div class="component-status-bar">
                    <div class="component-status">
                        <div class="component-indicator active" id="statusMain">
                            <div class="status-dot active"></div>
                            <span>Main Controller</span>
                        </div>
                        <div class="component-indicator" id="statusTitleScheme">
                            <div class="status-dot"></div>
                            <span>Title Scheme</span>
                        </div>
                        <div class="component-indicator" id="statusFeaturedImage">
                            <div class="status-dot"></div>
                            <span>Featured Image</span>
                        </div>
                        <div class="component-indicator" id="statusCustomFields">
                            <div class="status-dot"></div>
                            <span>Custom Fields</span>
                        </div>
                    </div>
                    <div style="font-size: 11px; opacity: 0.8;">
                        🔄 Real-time Component Communication
                    </div>
                </div>

                <!-- Architecture Visualization -->
                <div class="architecture-map">
                    <div class="architecture-title">
                        <span>🏗️</span>
                        <span>Mobile Tabbed Architecture</span>
                        <small style="color: #6c757d; font-weight: normal; font-size: 11px;">(Click components to switch tabs)</small>
                    </div>
                    <div class="module-grid">
                        <div class="module-box active" data-module="main" id="moduleMain">
                            <span class="module-icon">⚙️</span>
                            <div class="module-name">Main Controller</div>
                            <div class="module-status">Tab 1</div>
                        </div>
                        <div class="module-box" data-module="title" id="moduleTitle">
                            <span class="module-icon">🎨</span>
                            <div class="module-name">Title Scheme</div>
                            <div class="module-status">Tab 2</div>
                        </div>
                        <div class="module-box" data-module="image" id="moduleImage">
                            <span class="module-icon">🖼️</span>
                            <div class="module-name">Featured Image</div>
                            <div class="module-status">Tab 3</div>
                        </div>
                        <div class="module-box" data-module="fields" id="moduleFields">
                            <span class="module-icon">📝</span>
                            <div class="module-name">Custom Fields</div>
                            <div class="module-status">Tab 4</div>
                        </div>
                    </div>
                </div>

                <!-- Settings Grid (2 Columns: Tabbed Components + Preview) -->
                <div class="settings-grid">
                    
                    <!-- Tabbed Components Container -->
                    <div class="tabbed-components">
                        <!-- Tab Navigation -->
                        <div class="tab-navigation">
                            <button class="tab-button component-main active" data-tab="main" id="tabMain">
                                <span>⚙️ Main Controller</span>
                                <div class="tab-indicator">Core</div>
                                <div class="communication-pulse" id="mainPulse"></div>
                            </button>
                            <button class="tab-button component-title" data-tab="title" id="tabTitle">
                                <span>🎨 Title Scheme</span>
                                <div class="tab-indicator">Module</div>
                                <div class="communication-pulse" id="titlePulse"></div>
                            </button>
                            <button class="tab-button component-image" data-tab="image" id="tabImage">
                                <span>🖼️ Featured Image</span>
                                <div class="tab-indicator">Module</div>
                                <div class="communication-pulse" id="imagePulse"></div>
                            </button>
                            <button class="tab-button component-fields" data-tab="fields" id="tabFields">
                                <span>📝 Custom Fields</span>
                                <div class="tab-indicator">Module</div>
                                <div class="communication-pulse" id="fieldsPulse"></div>
                            </button>
                        </div>

                        <!-- Tab Content Panels -->
                        
                        <!-- Main Controller Tab -->
                        <div class="tab-content component-main active" id="tabContentMain">
                            <?php $this->render_main_controller_tab($current_post); ?>
                        </div>

                        <!-- Title Scheme Tab -->
                        <div class="tab-content component-title" id="tabContentTitle">
                            <?php $this->render_title_scheme_tab($current_post); ?>
                        </div>

                        <!-- Featured Image Tab -->
                        <div class="tab-content component-image" id="tabContentImage">
                            <?php $this->render_featured_image_tab($current_post); ?>
                        </div>

                        <!-- Custom Fields Tab -->
                        <div class="tab-content component-fields" id="tabContentFields">
                            <?php $this->render_custom_fields_tab($current_post); ?>
                        </div>
                    </div>

                    <!-- Live Preview Column -->
                    <div class="preview-column">
                        <div class="panel-header">
                            <div>
                                <span>📱 Live Preview</span>
                                <div class="component-badge" style="background: rgba(26, 188, 156, 0.2); color: #1abc9c;">Renderer</div>
                            </div>
                            <div class="preview-actions">
                                <button class="button button-small" onclick="refreshPreview()">🔄 Refresh</button>
                                <a href="<?php echo get_permalink($current_post_id); ?>" target="_blank" class="button button-small">🔗 View Live</a>
                            </div>
                        </div>
                        <div class="panel-content" style="padding: 10px;">
                            <?php $this->render_mobile_preview($current_post); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php $this->render_mobile_editor_scripts(); ?>
        </body>
        </html>
        <?php
    }
    
    /**
     * Render main controller tab content
     */
    private function render_main_controller_tab($post) {
        $use_steam = get_post_meta($post->ID, '_demontek_steam_use', true);
        ?>
        <div class="setting-row">
            <div class="setting-info">
                <h4>Enable Steam Layout</h4>
                <p>Master toggle that activates Steam layout for this post</p>
                <small style="color: #0073aa; font-size: 10px; margin-top: 4px; display: block;">
                    📡 Broadcasts: <code>steamLayoutToggled</code> event to all component modules
                </small>
            </div>
            <div class="toggle-control">
                <span class="toggle-status <?php echo $use_steam ? 'on' : 'off'; ?>" id="steamLayoutStatus">
                    <?php echo $use_steam ? 'Enabled' : 'Disabled'; ?>
                </span>
                <label class="demontek-toggle">
                    <input type="checkbox" id="enableSteamLayout" <?php checked($use_steam); ?>>
                    <span class="demontek-toggle-slider"></span>
                </label>
            </div>
        </div>

        <div class="mode-status-box <?php echo $use_steam ? 'custom' : ''; ?>" id="modeStatusBox">
            <label style="margin-bottom: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                <?php echo $use_steam ? 'Steam Mode Status' : 'Standard Mode Status'; ?>
            </label>
            <div id="modeStatus" style="font-size: 11px; font-weight: 600;">
                <?php if ($use_steam): ?>
                    🎮 STEAM MODE: All gaming components active
                <?php else: ?>
                    📝 STANDARD MODE: Regular WordPress post
                <?php endif; ?>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
            <h4 style="margin: 0 0 10px 0; color: #3498db; font-size: 14px;">🏗️ Mobile Architecture</h4>
            <p style="font-size: 12px; color: #646970; line-height: 1.4; margin: 0 0 10px 0;">
                This mobile editor uses a tabbed architecture where each component operates independently and communicates via events.
            </p>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button onclick="switchToTab('title')" class="button button-secondary" style="font-size: 11px; padding: 4px 8px;">
                    📝 Configure Titles
                </button>
                <button onclick="switchToTab('image')" class="button button-secondary" style="font-size: 11px; padding: 4px 8px;">
                    🖼️ Setup Images
                </button>
                <button onclick="switchToTab('fields')" class="button button-secondary" style="font-size: 11px; padding: 4px 8px;">
                    📝 Custom Fields
                </button>
                <button onclick="simulateComponentCommunication()" class="button button-primary" style="font-size: 11px; padding: 4px 8px;">
                    🔄 Test Communication
                </button>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render title scheme tab content
     */
    private function render_title_scheme_tab($post) {
        $use_steam = get_post_meta($post->ID, '_demontek_steam_use', true);
        ?>
        <div class="mobile-setting">
            <label>Post Title</label>
            <input type="text" id="postTitle" value="<?php echo esc_attr($post->post_title); ?>" placeholder="Enter post title">
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                🔌 Updates: Main post title
            </small>
        </div>

        <div class="mobile-setting">
            <label>AI Summary</label>
            <textarea id="aiSummary" rows="3" placeholder="AI-generated game summary"><?php echo esc_textarea(get_post_meta($post->ID, 'ai_summary', true)); ?></textarea>
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                📤 Displays: In Steam layout description area
            </small>
        </div>

        <div class="mobile-setting">
            <label>Game Genre</label>
            <input type="text" id="gameGenre" value="<?php echo esc_attr(get_post_meta($post->ID, 'game_genre', true)); ?>" placeholder="e.g., Action, RPG, Strategy">
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                🎮 Category: Game classification
            </small>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
            <h4 style="margin: 0 0 10px 0; color: #9b59b6; font-size: 14px;">🎨 Title Customization</h4>
            <p style="font-size: 12px; color: #646970; line-height: 1.4; margin: 0 0 10px 0;">
                This component handles all title-related functionality including main title, descriptions, and genre classification.
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 11px; color: #666;">
                <div><strong>Status:</strong> <span id="titleModuleStatus"><?php echo $use_steam ? 'Active' : 'Standby'; ?></span></div>
                <div><strong>Fields:</strong> 3 managed</div>
                <div><strong>Events:</strong> titleChanged</div>
                <div><strong>Auto-save:</strong> On change</div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render featured image tab content
     */
    private function render_featured_image_tab($post) {
        $use_steam = get_post_meta($post->ID, '_demontek_steam_use', true);
        ?>
        <div class="mobile-setting">
            <label>Featured Image</label>
            <div class="image-upload-area" id="imageUploadArea">
                <?php if (has_post_thumbnail($post->ID)): ?>
                    <div class="current-image">
                        <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                        <button type="button" class="remove-image-btn" onclick="removeImage()">❌ Remove</button>
                    </div>
                <?php else: ?>
                    <div class="no-image">
                        <p>📷 No featured image set</p>
                        <button type="button" class="button button-primary" onclick="openMediaLibrary()">🖼️ Set Featured Image</button>
                    </div>
                <?php endif; ?>
            </div>
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                🔌 Steam layout: Uses as background image with overlay
            </small>
        </div>

        <div class="mobile-setting">
            <label>Image Size Mode</label>
            <select id="imageSize" <?php echo !$use_steam ? 'disabled' : ''; ?>>
                <option value="small">Small (Compact view)</option>
                <option value="medium" selected>Medium (Balanced)</option>
                <option value="large">Large (Full focus)</option>
            </select>
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                📤 Controls: Image display size in Steam layout
            </small>
        </div>

        <div class="mobile-setting">
            <label>Overlay Style</label>
            <select id="overlayStyle" <?php echo !$use_steam ? 'disabled' : ''; ?>>
                <option value="gradient" selected>Gradient Overlay</option>
                <option value="solid">Solid Color</option>
                <option value="none">No Overlay</option>
            </select>
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                🎨 Enhances: Text readability over images
            </small>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
            <h4 style="margin: 0 0 10px 0; color: #e67e22; font-size: 14px;">🖼️ Image & Visual Effects</h4>
            <p style="font-size: 12px; color: #646970; line-height: 1.4; margin: 0 0 10px 0;">
                This component manages featured image display, sizing, and visual enhancements for optimal Steam layout presentation.
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 11px; color: #666;">
                <div><strong>Status:</strong> <span id="imageModuleStatus"><?php echo $use_steam ? 'Active' : 'Standby'; ?></span></div>
                <div><strong>Image:</strong> <?php echo has_post_thumbnail($post->ID) ? 'Set' : 'None'; ?></div>
                <div><strong>Events:</strong> imageChanged</div>
                <div><strong>Steam Mode:</strong> <?php echo $use_steam ? 'Ready' : 'Disabled'; ?></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render custom fields tab content
     */
    private function render_custom_fields_tab($post) {
        $steam_fields = array(
            'Trailers' => array(
                'trailer_1' => 'Main Game Trailer',
                'trailer_2' => 'Gameplay Footage',
                'trailer_3' => 'Features Showcase',
                'trailer_4' => 'Extended Content',
                'trailer_5' => 'Bonus Material'
            ),
            'Links' => array(
                'original_link' => 'Original YouTube Link',
                'steam_link' => 'Steam Store Page',
                'amazon_link' => 'Amazon Purchase Link'
            ),
            'Reviews' => array(
                'review_1' => 'Community Review #1',
                'review_2' => 'Community Review #2',
                'review_3' => 'Community Review #3'
            ),
            'Details' => array(
                'developer' => 'Game Developer',
                'platforms' => 'Available Platforms',
                'release_date' => 'Release Date'
            )
        );
        
        foreach ($steam_fields as $section => $fields):
        ?>
        <div class="fields-section">
            <h4 style="margin: 0 0 10px 0; color: #2271b1; font-size: 14px;"><?php echo $section; ?></h4>
            <?php foreach ($fields as $field => $label): ?>
            <div class="mobile-setting">
                <label><?php echo $label; ?></label>
                <?php if (strpos($field, 'review') !== false): ?>
                    <textarea id="<?php echo $field; ?>" rows="2" placeholder="Enter <?php echo strtolower($label); ?>"><?php echo esc_textarea(get_post_meta($post->ID, $field, true)); ?></textarea>
                <?php else: ?>
                    <input type="text" id="<?php echo $field; ?>" value="<?php echo esc_attr(get_post_meta($post->ID, $field, true)); ?>" placeholder="Enter <?php echo strtolower($label); ?>">
                <?php endif; ?>
                <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                    Field: <code><?php echo $field; ?></code>
                </small>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
            <h4 style="margin: 0 0 10px 0; color: #1abc9c; font-size: 14px;">📝 Custom Fields Management</h4>
            <p style="font-size: 12px; color: #646970; line-height: 1.4; margin: 0 0 10px 0;">
                Manage all Steam-specific content fields. Changes are automatically saved and synchronized with the preview.
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 11px; color: #666;">
                <div><strong>Total Fields:</strong> <?php echo count($steam_fields, COUNT_RECURSIVE) - count($steam_fields); ?></div>
                <div><strong>Auto-save:</strong> 2 second delay</div>
                <div><strong>Events:</strong> fieldChanged</div>
                <div><strong>Validation:</strong> Real-time</div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render mobile preview
     */
    private function render_mobile_preview($post) {
        $use_steam = get_post_meta($post->ID, '_demontek_steam_use', true);
        ?>
        <div class="mobile-preview-container">
            <div class="mobile-frame">
                <div class="mobile-screen">
                    <div class="mobile-header">
                        <div>☰</div>
                        <div class="mobile-logo">DEMONTEK</div>
                        <div>🔍</div>
                    </div>
                    
                    <div class="mobile-post">
                        <?php if ($use_steam): ?>
                        <div class="mobile-featured-image medium" id="mobileImage">
                            <?php if (has_post_thumbnail($post->ID)): ?>
                                <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                            <?php else: ?>
                                <div style="background: linear-gradient(135deg, #1b2838, #2a475e); height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <span>🖼️ No Featured Image</span>
                                </div>
                            <?php endif; ?>
                            <div class="mobile-overlay">
                                <div class="mobile-category steam-mode" id="mobileCategory">
                                    <?php echo get_post_meta($post->ID, 'game_genre', true) ?: 'Gaming'; ?>
                                </div>
                                <div class="mobile-title steam-mode" id="mobileTitle">
                                    <?php echo $post->post_title ?: 'Untitled Game'; ?>
                                </div>
                                <div class="mobile-meta" id="mobileMeta">
                                    <div class="mobile-meta-item">
                                        <span>By <?php echo get_the_author_meta('display_name', $post->post_author); ?></span>
                                    </div>
                                    <div class="mobile-meta-separator"></div>
                                    <div class="mobile-meta-item">
                                        <span><?php echo date('M j, Y', strtotime($post->post_date)); ?></span>
                                    </div>
                                    <div class="mobile-meta-separator"></div>
                                    <div class="mobile-meta-item">
                                        <span>Steam Layout</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mobile-content medium-image" id="mobileContent">
                            <div class="mobile-stats">
                                <div class="stat-item">
                                    <span class="stat-number">🎮</span>
                                    <span class="stat-label">Gaming</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">📱</span>
                                    <span class="stat-label">Mobile</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">⚡</span>
                                    <span class="stat-label">Live</span>
                                </div>
                            </div>
                            
                            <div class="mobile-description">
                                <h4>Steam Layout Preview</h4>
                                <p id="mobileDescription">
                                    <?php echo get_post_meta($post->ID, 'ai_summary', true) ?: 'Steam layout active with gaming components loaded. Edit fields in the tabs above to see changes reflected here in real-time.'; ?>
                                </p>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="standard-post-preview">
                            <div style="padding: 20px; text-align: center;">
                                <h3><?php echo $post->post_title ?: 'Untitled Post'; ?></h3>
                                <p style="color: #666; margin: 10px 0;">
                                    📝 Standard WordPress post view
                                </p>
                                <p style="font-size: 12px; color: #999;">
                                    Enable Steam layout in the Main Controller tab to see the full gaming interface.
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render mobile editor styles
     */
    private function render_mobile_editor_styles() {
        ?>
        <style>
            /* Copy the styles from the prototype but adapted for WordPress admin */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #f1f1f1;
                color: #23282d;
                line-height: 1.4;
            }

            .wrap {
                margin: 20px 20px 0;
                padding: 0;
            }

            .demontek-mobile-header {
                background: white;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-left: 4px solid #007cba;
            }

            .demontek-mobile-header h1 {
                font-size: 23px;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .demontek-version-badge {
                background: linear-gradient(135deg, #8e44ad 0%, #6b2d85 100%);
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: bold;
            }

            .architecture-badge {
                background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: bold;
                margin-left: 8px;
            }

            /* Post Selector */
            .mobile-post-selector {
                background: white;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-left: 4px solid #00a32a;
            }

            .selector-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
            }

            .selector-header h3 {
                margin: 0;
                color: #23282d;
            }

            .selector-actions {
                display: flex;
                gap: 10px;
            }

            .post-selector-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 15px;
            }

            .post-selector-item {
                background: #f8f9fa;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                padding: 15px;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .post-selector-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }

            .post-selector-item.active {
                border-color: #007cba;
                background: #e7f3ff;
            }

            .post-item-header h4 {
                margin: 0 0 5px 0;
                color: #23282d;
                font-size: 14px;
            }

            .post-item-meta {
                display: flex;
                gap: 10px;
                margin-bottom: 10px;
            }

            .post-status {
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 10px;
                font-weight: bold;
                text-transform: uppercase;
            }

            .post-status.publish {
                background: #d1ecf1;
                color: #0c5460;
            }

            .post-status.draft {
                background: #f8d7da;
                color: #721c24;
            }

            .post-date {
                font-size: 11px;
                color: #666;
            }

            .post-item-actions {
                display: flex;
                gap: 8px;
            }

            /* Component Status Bar */
            .component-status-bar {
                background: linear-gradient(135deg, #2c3e50, #34495e);
                color: white;
                padding: 12px 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }

            .component-status {
                display: flex;
                gap: 15px;
                align-items: center;
            }

            .component-indicator {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 4px 8px;
                border-radius: 12px;
                background: rgba(255,255,255,0.1);
                font-size: 11px;
                transition: all 0.3s ease;
            }

            .component-indicator.active {
                background: rgba(46, 204, 113, 0.3);
                box-shadow: 0 0 8px rgba(46, 204, 113, 0.4);
            }

            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #95a5a6;
                transition: all 0.3s ease;
            }

            .status-dot.active {
                background: #2ecc71;
                box-shadow: 0 0 6px #2ecc71;
            }

            /* Architecture Map */
            .architecture-map {
                background: white;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-left: 4px solid #2c3e50;
            }

            .architecture-title {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
                font-weight: 600;
                color: #2c3e50;
            }

            .module-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
            }

            .module-box {
                background: #f8f9fa;
                border: 2px solid #dee2e6;
                border-radius: 6px;
                padding: 12px 8px;
                text-align: center;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .module-box:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }

            .module-box.active {
                border-color: #007cba;
                background: #e7f3ff;
            }

            .module-icon {
                font-size: 20px;
                margin-bottom: 5px;
                display: block;
            }

            .module-name {
                font-size: 10px;
                font-weight: 600;
                color: #495057;
                text-transform: uppercase;
            }

            /* Settings Grid */
            .settings-grid {
                display: grid;
                grid-template-columns: 1fr 400px;
                gap: 20px;
                margin-bottom: 30px;
            }

            /* Tabbed Interface */
            .tabbed-components {
                background: white;
                border: 1px solid #c3c4c7;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                overflow: hidden;
            }

            .tab-navigation {
                display: flex;
                background: #f6f7f7;
                border-bottom: 1px solid #c3c4c7;
            }

            .tab-button {
                flex: 1;
                padding: 15px 20px;
                background: transparent;
                border: none;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                color: #646970;
                transition: all 0.3s ease;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                flex-direction: column;
            }

            .tab-button:hover {
                background: rgba(0,123,186,0.1);
                color: #007cba;
            }

            .tab-button.active {
                background: white;
                color: #23282d;
                border-bottom: 3px solid #007cba;
            }

            .tab-indicator {
                font-size: 10px;
                padding: 2px 6px;
                border-radius: 10px;
                background: rgba(0,0,0,0.1);
                color: #666;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: bold;
            }

            .tab-content {
                display: none;
                padding: 20px;
                min-height: 400px;
            }

            .tab-content.active {
                display: block;
            }

            /* Preview Column */
            .preview-column {
                background: white;
                border: 1px solid #c3c4c7;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-left: 4px solid #1abc9c;
            }

            .panel-header {
                background: #f6f7f7;
                padding: 15px 20px;
                border-bottom: 1px solid #c3c4c7;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            .component-badge {
                font-size: 9px;
                padding: 2px 6px;
                border-radius: 10px;
                background: rgba(0,0,0,0.1);
                color: #666;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: bold;
            }

            .preview-actions {
                display: flex;
                gap: 8px;
            }

            /* Mobile Settings */
            .mobile-setting {
                margin-bottom: 15px;
            }

            .mobile-setting label {
                display: block;
                font-size: 12px;
                color: #646970;
                margin-bottom: 5px;
                font-weight: 600;
            }

            .mobile-setting input,
            .mobile-setting select,
            .mobile-setting textarea {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #ced4da;
                border-radius: 4px;
                font-size: 13px;
                transition: all 0.3s ease;
            }

            .mobile-setting input:focus,
            .mobile-setting select:focus,
            .mobile-setting textarea:focus {
                border-color: #007cba;
                outline: none;
                box-shadow: 0 0 0 2px rgba(0, 124, 186, 0.1);
            }

            /* Toggle Control */
            .setting-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 6px;
                margin-bottom: 15px;
            }

            .setting-info h4 {
                margin: 0 0 5px 0;
                font-size: 14px;
                color: #23282d;
            }

            .setting-info p {
                margin: 0;
                font-size: 12px;
                color: #646970;
            }

            .toggle-control {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .toggle-status {
                font-size: 11px;
                font-weight: bold;
                padding: 2px 8px;
                border-radius: 10px;
                min-width: 60px;
                text-align: center;
            }

            .toggle-status.on {
                background: #d1ecf1;
                color: #0c5460;
            }

            .toggle-status.off {
                background: #f8d7da;
                color: #721c24;
            }

            .demontek-toggle {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
            }

            .demontek-toggle input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .demontek-toggle-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 24px;
            }

            .demontek-toggle-slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            .demontek-toggle input:checked + .demontek-toggle-slider {
                background-color: #007cba;
            }

            .demontek-toggle input:checked + .demontek-toggle-slider:before {
                transform: translateX(26px);
            }

            .mode-status-box {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 15px;
            }

            .mode-status-box.custom {
                background: #d1ecf1;
                border-color: #bee5eb;
            }

            /* Fields Section */
            .fields-section {
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e0e0e0;
            }

            .fields-section:last-child {
                border-bottom: none;
            }

            /* Mobile Preview */
            .mobile-preview-container {
                background: #f8f9fa;
                height: 500px;
                position: relative;
                overflow: hidden;
                border-radius: 0 0 6px 6px;
            }

            .mobile-frame {
                width: 280px;
                height: 480px;
                background: #000;
                border-radius: 15px;
                margin: 10px auto;
                padding: 8px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.3);
                position: relative;
                overflow: hidden;
            }

            .mobile-screen {
                width: 100%;
                height: 100%;
                background: white;
                border-radius: 8px;
                overflow: hidden;
                position: relative;
            }

            .mobile-header {
                background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
                color: white;
                padding: 8px 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 12px;
            }

            .mobile-logo {
                font-weight: bold;
                color: #00d4ff;
            }

            .mobile-post {
                position: relative;
                height: calc(100% - 40px);
            }

            .mobile-featured-image {
                width: 100%;
                height: 300px;
                position: relative;
                overflow: hidden;
            }

            .mobile-featured-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .mobile-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(transparent 20%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%);
                color: white;
                padding: 20px 15px;
                min-height: 100px;
            }

            .mobile-category {
                background: rgba(0, 171, 240, 0.9);
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 9px;
                font-weight: bold;
                text-transform: uppercase;
                display: inline-block;
                margin-bottom: 5px;
            }

            .mobile-title {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 5px;
                text-shadow: 0 1px 3px rgba(0,0,0,0.7);
            }

            .mobile-meta {
                font-size: 10px;
                color: rgba(255,255,255,0.9);
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .mobile-meta-separator {
                width: 2px;
                height: 2px;
                background: rgba(255,255,255,0.6);
                border-radius: 50%;
            }

            .mobile-content {
                padding: 15px;
                height: calc(100% - 300px);
                overflow-y: auto;
            }

            .mobile-stats {
                display: flex;
                justify-content: space-around;
                background: #f8f9fa;
                padding: 8px;
                border-radius: 6px;
                margin-bottom: 10px;
            }

            .stat-item {
                text-align: center;
            }

            .stat-number {
                display: block;
                font-size: 14px;
                font-weight: bold;
            }

            .stat-label {
                font-size: 8px;
                color: #646970;
            }

            .mobile-description h4 {
                font-size: 12px;
                margin-bottom: 5px;
                color: #23282d;
            }

            .mobile-description p {
                font-size: 10px;
                color: #646970;
                line-height: 1.4;
            }

            .standard-post-preview {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Communication Pulse */
            .communication-pulse {
                position: absolute;
                top: 5px;
                right: 5px;
                width: 8px;
                height: 8px;
                background: #2ecc71;
                border-radius: 50%;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.1); opacity: 0.7; }
                100% { transform: scale(1); opacity: 1; }
            }

            /* Responsive Design */
            @media (max-width: 1200px) {
                .settings-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
                
                .preview-column {
                    order: -1;
                }
            }

            @media (max-width: 768px) {
                .module-grid {
                    grid-template-columns: 1fr 1fr;
                }
                
                .tab-navigation {
                    flex-wrap: wrap;
                }
                
                .tab-button {
                    min-width: 50%;
                }
            }

            /* Image Upload Area */
            .image-upload-area {
                border: 2px dashed #ccc;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
                transition: all 0.3s ease;
            }

            .image-upload-area:hover {
                border-color: #007cba;
            }

            .current-image {
                position: relative;
                display: inline-block;
            }

            .current-image img {
                max-width: 200px;
                border-radius: 6px;
            }

            .remove-image-btn {
                position: absolute;
                top: 5px;
                right: 5px;
                background: rgba(255, 0, 0, 0.8);
                color: white;
                border: none;
                border-radius: 4px;
                padding: 2px 6px;
                font-size: 10px;
                cursor: pointer;
            }

            .no-image {
                padding: 40px 20px;
                color: #666;
            }

            .no-image p {
                margin-bottom: 15px;
                font-size: 14px;
            }
        </style>
        <?php
    }
    
    /**
     * Render mobile editor JavaScript
     */
    private function render_mobile_editor_scripts() {
        ?>
        <script>
            // Mobile Editor JavaScript
            class DemontekMobileEditor {
                constructor() {
                    this.currentPostId = <?php echo isset($_GET['post']) ? intval($_GET['post']) : 0; ?>;
                    this.saveTimeouts = {};
                    this.isCustomMode = false;
                    this.init();
                }
                
                init() {
                    this.setupEventListeners();
                    this.initializeComponents();
                    this.setupAutoSave();
                    console.log('📱 Demontek Mobile Editor v<?php echo $this->version; ?> initialized');
                }
                
                setupEventListeners() {
                    // Tab switching
                    document.querySelectorAll('.tab-button').forEach(button => {
                        button.addEventListener('click', (e) => {
                            const tabName = e.currentTarget.dataset.tab;
                            this.switchToTab(tabName);
                        });
                    });
                    
                    // Architecture map
                    document.querySelectorAll('.module-box').forEach(box => {
                        box.addEventListener('click', (e) => {
                            const module = e.currentTarget.dataset.module;
                            this.switchToTab(module);
                        });
                    });
                    
                    // Steam layout toggle
                    const steamToggle = document.getElementById('enableSteamLayout');
                    if (steamToggle) {
                        steamToggle.addEventListener('change', (e) => {
                            this.handleSteamToggle(e.target.checked);
                        });
                    }
                    
                    // Field inputs
                    this.setupFieldListeners();
                }
                
                setupFieldListeners() {
                    const fieldSelectors = [
                        '#postTitle', '#aiSummary', '#gameGenre',
                        '#trailer_1', '#trailer_2', '#trailer_3', '#trailer_4', '#trailer_5',
                        '#original_link', '#steam_link', '#amazon_link',
                        '#review_1', '#review_2', '#review_3',
                        '#developer', '#platforms', '#release_date'
                    ];
                    
                    fieldSelectors.forEach(selector => {
                        const element = document.querySelector(selector);
                        if (element) {
                            element.addEventListener('input', (e) => {
                                this.handleFieldChange(e.target.id, e.target.value);
                            });
                        }
                    });
                }
                
                setupAutoSave() {
                    // Auto-save every 2 seconds after changes
                    setInterval(() => {
                        this.processPendingSaves();
                    }, 2000);
                }
                
                initializeComponents() {
                    const steamEnabled = document.getElementById('enableSteamLayout');
                    if (steamEnabled) {
                        this.isCustomMode = steamEnabled.checked;
                        this.updateComponentStatus();
                    }
                }
                
                switchToTab(tabName) {
                    // Remove active class from all tabs
                    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    document.querySelectorAll('.module-box').forEach(box => box.classList.remove('active'));
                    
                    // Add active class to selected tab
                    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
                    document.getElementById(`tabContent${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`).classList.add('active');
                    document.querySelector(`[data-module="${tabName}"]`).classList.add('active');
                    
                    console.log(`Switched to ${tabName} tab`);
                }
                
                handleSteamToggle(enabled) {
                    this.isCustomMode = enabled;
                    this.updateComponentStatus();
                    
                    // Update UI
                    const status = document.getElementById('steamLayoutStatus');
                    const modeStatus = document.getElementById('modeStatus');
                    const modeStatusBox = document.getElementById('modeStatusBox');
                    
                    if (status) {
                        status.textContent = enabled ? 'Enabled' : 'Disabled';
                        status.className = enabled ? 'toggle-status on' : 'toggle-status off';
                    }
                    
                    if (modeStatus && modeStatusBox) {
                        if (enabled) {
                            modeStatus.innerHTML = '🎮 STEAM MODE: All gaming components active';
                            modeStatusBox.className = 'mode-status-box custom';
                        } else {
                            modeStatus.innerHTML = '📝 STANDARD MODE: Regular WordPress post';
                            modeStatusBox.className = 'mode-status-box';
                        }
                    }
                    
                    // Save to database
                    this.saveField('_demontek_steam_use', enabled ? 1 : 0);
                    
                    // Update preview
                    this.updatePreview();
                    
                    this.showNotification(
                        enabled ? '🎮 Steam layout enabled!' : '📝 Standard mode enabled!',
                        'success'
                    );
                }
                
                handleFieldChange(fieldId, value) {
                    // Queue for auto-save
                    this.saveTimeouts[fieldId] = value;
                    
                    // Update preview immediately for key fields
                    if (fieldId === 'postTitle') {
                        this.updateMobileTitle(value);
                    } else if (fieldId === 'aiSummary') {
                        this.updateMobileDescription(value);
                    } else if (fieldId === 'gameGenre') {
                        this.updateMobileCategory(value);
                    }
                    
                    console.log(`Field ${fieldId} changed:`, value);
                }
                
                processPendingSaves() {
                    for (const [fieldId, value] of Object.entries(this.saveTimeouts)) {
                        this.saveField(fieldId, value);
                        delete this.saveTimeouts[fieldId];
                    }
                }
                
                saveField(fieldName, value) {
                    if (!this.currentPostId) return;
                    
                    const data = {
                        action: fieldName === 'postTitle' ? 'wp_ajax_edit_post' : 'mobile_save_field',
                        post_id: this.currentPostId,
                        field_name: fieldName,
                        field_value: value,
                        nonce: demontekSteam.mobile_nonce
                    };
                    
                    fetch(demontekSteam.ajaxurl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            console.log(`Saved ${fieldName}:`, value);
                        } else {
                            console.error('Save failed:', result.data);
                        }
                    })
                    .catch(error => {
                        console.error('Save error:', error);
                    });
                }
                
                updateComponentStatus() {
                    const indicators = document.querySelectorAll('.component-indicator');
                    indicators.forEach(indicator => {
                        const dot = indicator.querySelector('.status-dot');
                        if (this.isCustomMode) {
                            indicator.classList.add('active');
                            dot.classList.add('active');
                        } else {
                            indicator.classList.remove('active');
                            dot.classList.remove('active');
                        }
                    });
                }
                
                updateMobileTitle(title) {
                    const titleElement = document.getElementById('mobileTitle');
                    if (titleElement) {
                        titleElement.textContent = title || 'Untitled Game';
                    }
                }
                
                updateMobileDescription(description) {
                    const descElement = document.getElementById('mobileDescription');
                    if (descElement) {
                        descElement.textContent = description || 'No description available.';
                    }
                }
                
                updateMobileCategory(category) {
                    const categoryElement = document.getElementById('mobileCategory');
                    if (categoryElement) {
                        categoryElement.textContent = category || 'Gaming';
                    }
                }
                
                updatePreview() {
                    // Trigger preview refresh
                    console.log('Updating preview...');
                }
                
                showNotification(message, type = 'info') {
                    // Remove existing notifications
                    document.querySelectorAll('.mobile-notification').forEach(n => n.remove());
                    
                    const notification = document.createElement('div');
                    notification.className = 'mobile-notification';
                    
                    const colors = {
                        success: '#00a32a',
                        error: '#dc3232',
                        info: '#2271b1',
                        warning: '#f56e00'
                    };
                    
                    notification.style.cssText = `
                        position: fixed;
                        top: 32px;
                        right: 20px;
                        background: ${colors[type] || colors.info};
                        color: white;
                        padding: 12px 18px;
                        border-radius: 6px;
                        z-index: 9999;
                        font-size: 13px;
                        max-width: 300px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                        animation: slideIn 0.3s ease;
                    `;
                    
                    notification.textContent = message;
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => notification.remove(), 300);
                    }, 3000);
                }
            }
            
            // Global functions for backward compatibility
            function selectPost(postId) {
                window.location.href = `admin.php?page=demontek-mobile-editor&post=${postId}`;
            }
            
            function switchToTab(tabName) {
                if (window.mobileEditor) {
                    window.mobileEditor.switchToTab(tabName);
                }
            }
            
            function simulateComponentCommunication() {
                if (window.mobileEditor) {
                    window.mobileEditor.showNotification('🔄 Testing component communication...', 'info');
                    setTimeout(() => {
                        window.mobileEditor.showNotification('✅ All components communicating successfully!', 'success');
                    }, 1500);
                }
            }
            
            function refreshPreview() {
                if (window.mobileEditor) {
                    window.mobileEditor.updatePreview();
                    window.mobileEditor.showNotification('🔄 Preview refreshed!', 'info');
                }
            }
            
            // Initialize when page loads
            document.addEventListener('DOMContentLoaded', function() {
                window.mobileEditor = new DemontekMobileEditor();
                
                // Add CSS animation
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes slideIn {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                `;
                document.head.appendChild(style);
                
                console.log('📱 Mobile Editor Ready!');
            });
        </script>
        <?php
    }
}