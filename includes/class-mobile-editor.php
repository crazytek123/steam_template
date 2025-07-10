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
        // Get categories for selector
        $categories = get_categories(array(
            'hide_empty' => false,
            'number' => 50,
            'orderby' => 'count',
            'order' => 'DESC'
        ));
        
        // Get current post from session or first available
        $current_post_id = isset($_SESSION['mobile_editor_post_id']) ? intval($_SESSION['mobile_editor_post_id']) : 0;
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

                <!-- Category Selector and Post Loading -->
                <div class="mobile-post-loader">
                    <div class="loader-header">
                        <h3>🎮 Load Gaming Posts</h3>
                        <div class="loader-actions">
                            <a href="post-new.php" class="button button-primary">➕ New Post</a>
                            <a href="admin.php?page=demontek-steam" class="button button-secondary">🎮 Dashboard</a>
                            <button class="button button-secondary" onclick="toggleExportMode()">📤 Export/Import</button>
                        </div>
                    </div>
                    
                    <div class="category-selector-row">
                        <div class="category-selector">
                            <select id="categorySelector" style="width: 200px;">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category->term_id; ?>">
                                    <?php echo $category->name; ?> (<?php echo $category->count; ?> posts)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="button button-primary" onclick="loadPosts()" id="loadPostsBtn">
                            🔄 Load Posts
                        </button>
                        <div id="loadStatus" style="margin-left: 10px; color: #666; font-size: 12px;"></div>
                    </div>
                    
                    <!-- Export/Import Panel (Hidden by default) -->
                    <div id="exportImportPanel" style="display: none; background: #f0f0f0; padding: 15px; border-radius: 6px; margin-top: 10px;">
                        <h4>📤 Export/Import Data</h4>
                        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <button class="button button-secondary" onclick="exportCurrentPost()">📤 Export Current Post</button>
                            <button class="button button-secondary" onclick="exportAllPosts()">📤 Export All Loaded Posts</button>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="file" id="importFile" accept=".json" style="flex: 1;">
                            <button class="button button-primary" onclick="importData()">📥 Import Data</button>
                        </div>
                        <div id="exportStatus" style="margin-top: 10px; font-size: 12px; color: #666;"></div>
                    </div>
                </div>

                <!-- Post Navigation -->
                <div class="post-navigation-bar" id="postNavigationBar" style="display: none;">
                    <div class="nav-info">
                        <span id="postCounter">Post 1/1</span>
                        <span id="postCategory">• Gaming</span>
                        <span id="postId">• ID: 12000</span>
                    </div>
                    <div class="nav-controls">
                        <button class="button" onclick="previousPost()" id="prevBtn">◀ Prev</button>
                        <button class="button" onclick="nextPost()" id="nextBtn">Next ▶</button>
                    </div>
                </div>

                <!-- Current Post Display -->
                <div class="current-post-display" id="currentPostDisplay" style="display: none;">
                    <div class="post-header">
                        <h3 id="currentPostTitle">No post selected</h3>
                        <div class="post-meta">
                            <span id="currentPostStatus">Status: None</span>
                            <span id="currentPostDate">Date: None</span>
                        </div>
                    </div>
                    <div class="post-actions">
                        <button class="button button-primary" onclick="refreshCurrentPost()">🔄 Refresh</button>
                        <a href="#" id="classicEditLink" class="button button-secondary" target="_blank">✏️ Edit Classic</a>
                        <a href="#" id="viewPostLink" class="button button-secondary" target="_blank">👁️ View Post</a>
                    </div>
                </div>

                <!-- Component Status Bar (Initially Hidden) -->
                <div class="component-status-bar" style="display: none;">
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

                <!-- Architecture Visualization (Initially Hidden) -->
                <div class="architecture-map" style="display: none;">
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

                <!-- Settings Grid (Initially Hidden) -->
                <div class="settings-grid" style="display: none;">
                    
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
                                <a href="<?php echo $current_post ? get_permalink($current_post->ID) : '#'; ?>" target="_blank" class="button button-small">🔗 View Live</a>
                            </div>
                        </div>
                        <div class="panel-content" style="padding: 10px;">
                            <?php $this->render_mobile_preview($current_post); ?>
                        </div>
                    </div>
                </div>
                <!-- End Settings Grid -->
            </div>

            <?php $this->render_mobile_editor_scripts(); ?>
        </body>
        </html>
        <?php
    }
    
    /**
     * Render main controller tab content
     */
    private function render_main_controller_tab($post = null) {
        $use_steam = $post ? get_post_meta($post->ID, '_demontek_steam_use', true) : false;
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
    private function render_title_scheme_tab($post = null) {
        $use_steam = $post ? get_post_meta($post->ID, '_demontek_steam_use', true) : false;
        ?>
        <div class="mobile-setting">
            <label>Post Title</label>
            <input type="text" id="postTitle" value="<?php echo $post ? esc_attr($post->post_title) : ''; ?>" placeholder="Enter post title">
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                🔌 Updates: Main post title
            </small>
        </div>

        <div class="mobile-setting">
            <label>AI Summary</label>
            <textarea id="aiSummary" rows="3" placeholder="AI-generated game summary"><?php echo $post ? esc_textarea(get_post_meta($post->ID, 'ai_summary', true)) : ''; ?></textarea>
            <small style="color: #646970; font-size: 10px; margin-top: 2px; display: block;">
                📤 Displays: In Steam layout description area
            </small>
        </div>

        <div class="mobile-setting">
            <label>Game Genre</label>
            <input type="text" id="gameGenre" value="<?php echo $post ? esc_attr(get_post_meta($post->ID, 'game_genre', true)) : ''; ?>" placeholder="e.g., Action, RPG, Strategy">
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
    private function render_featured_image_tab($post = null) {
        $use_steam = $post ? get_post_meta($post->ID, '_demontek_steam_use', true) : false;
        ?>
        <div class="mobile-setting">
            <label>Featured Image</label>
            <div class="image-upload-area" id="imageUploadArea">
                <?php if ($post && has_post_thumbnail($post->ID)): ?>
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
                <div><strong>Image:</strong> <?php echo ($post && has_post_thumbnail($post->ID)) ? 'Set' : 'None'; ?></div>
                <div><strong>Events:</strong> imageChanged</div>
                <div><strong>Steam Mode:</strong> <?php echo $use_steam ? 'Ready' : 'Disabled'; ?></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render custom fields tab content
     */
    private function render_custom_fields_tab($post = null) {
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
                    <textarea id="<?php echo $field; ?>" rows="2" placeholder="Enter <?php echo strtolower($label); ?>"><?php echo $post ? esc_textarea(get_post_meta($post->ID, $field, true)) : ''; ?></textarea>
                <?php else: ?>
                    <input type="text" id="<?php echo $field; ?>" value="<?php echo $post ? esc_attr(get_post_meta($post->ID, $field, true)) : ''; ?>" placeholder="Enter <?php echo strtolower($label); ?>">
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
     * Render mobile preview (complete field display)
     */
    private function render_mobile_preview($post = null) {
        ?>
        <div class="mobile-preview-container">
            <div class="mobile-preview-frame" id="mobilePreviewFrame">
                <?php if ($post): ?>
                <div class="mobile-preview-header">
                    <div class="mobile-preview-title"><?php echo esc_html($post->post_title ?: 'Untitled Post'); ?></div>
                    <div class="mobile-preview-meta">
                        <span class="preview-date"><?php echo date('M j, Y', strtotime($post->post_date)); ?></span>
                        <span class="preview-author">By <?php echo get_the_author_meta('display_name', $post->post_author); ?></span>
                        <span class="preview-id">ID: <?php echo $post->ID; ?></span>
                    </div>
                </div>
                
                <div class="mobile-preview-content">
                    <?php if (has_post_thumbnail($post->ID)): ?>
                    <div class="mobile-preview-image">
                        <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Steam Layout Status -->
                    <div class="preview-field-group">
                        <strong>🎮 Steam Layout:</strong>
                        <span id="previewSteamStatus" class="steam-status <?php echo get_post_meta($post->ID, '_demontek_steam_use', true) ? 'enabled' : 'disabled'; ?>">
                            <?php echo get_post_meta($post->ID, '_demontek_steam_use', true) ? '✅ Enabled' : '❌ Disabled'; ?>
                        </span>
                    </div>
                    
                    <!-- Basic Fields -->
                    <div class="preview-field-group">
                        <strong>🎮 Game Genre:</strong>
                        <span id="previewGameGenre"><?php echo get_post_meta($post->ID, 'game_genre', true) ?: 'Not set'; ?></span>
                    </div>
                    
                    <div class="preview-field-group">
                        <strong>📝 AI Summary:</strong>
                        <div id="previewAiSummary" class="preview-text-content">
                            <?php echo get_post_meta($post->ID, 'ai_summary', true) ?: 'No summary available'; ?>
                        </div>
                    </div>
                    
                    <div class="preview-field-group">
                        <strong>📄 AI Excerpt:</strong>
                        <div id="previewAiExcerpt" class="preview-text-content">
                            <?php echo get_post_meta($post->ID, 'ai_excerpt', true) ?: 'No excerpt available'; ?>
                        </div>
                    </div>
                    
                    <!-- All Trailers -->
                    <div class="preview-field-group">
                        <strong>🎬 Trailers:</strong>
                        <div class="preview-trailers" id="previewTrailers">
                            <?php
                            $trailers = array();
                            for ($i = 1; $i <= 5; $i++) {
                                $trailer = get_post_meta($post->ID, "trailer_$i", true);
                                if (!empty($trailer)) {
                                    $trailers[] = "<strong>Trailer $i:</strong> <a href='$trailer' target='_blank'>$trailer</a>";
                                }
                            }
                            echo !empty($trailers) ? implode('<br>', $trailers) : 'No trailers added';
                            ?>
                        </div>
                    </div>
                    
                    <!-- All Links -->
                    <div class="preview-field-group">
                        <strong>🔗 Links:</strong>
                        <div class="preview-links" id="previewLinks">
                            <?php
                            $links = array();
                            $link_fields = array(
                                'steam_link' => 'Steam Store',
                                'original_link' => 'Original Source', 
                                'amazon_link' => 'Amazon'
                            );
                            foreach ($link_fields as $field => $label) {
                                $link = get_post_meta($post->ID, $field, true);
                                if (!empty($link)) {
                                    $links[] = "<strong>$label:</strong> <a href='$link' target='_blank'>$link</a>";
                                }
                            }
                            echo !empty($links) ? implode('<br>', $links) : 'No links added';
                            ?>
                        </div>
                    </div>
                    
                    <!-- All Reviews -->
                    <div class="preview-field-group">
                        <strong>⭐ Reviews:</strong>
                        <div class="preview-reviews" id="previewReviews">
                            <?php
                            $reviews = array();
                            for ($i = 1; $i <= 3; $i++) {
                                $review = get_post_meta($post->ID, "review_$i", true);
                                if (!empty($review)) {
                                    $reviews[] = "<strong>Review $i:</strong> " . esc_html($review);
                                }
                            }
                            echo !empty($reviews) ? implode('<br><br>', $reviews) : 'No reviews added';
                            ?>
                        </div>
                    </div>
                    
                    <!-- Game Details -->
                    <div class="preview-field-group">
                        <strong>🎯 Game Details:</strong>
                        <div class="preview-details" id="previewDetails">
                            <?php
                            $details = array();
                            $detail_fields = array(
                                'developer' => 'Developer',
                                'platforms' => 'Platforms',
                                'release_date' => 'Release Date'
                            );
                            foreach ($detail_fields as $field => $label) {
                                $value = get_post_meta($post->ID, $field, true);
                                if (!empty($value)) {
                                    $details[] = "<strong>$label:</strong> " . esc_html($value);
                                }
                            }
                            echo !empty($details) ? implode('<br>', $details) : 'No details added';
                            ?>
                        </div>
                    </div>
                    
                    <!-- Layout Settings -->
                    <div class="preview-field-group">
                        <strong>⚙️ Layout Settings:</strong>
                        <div class="preview-layout" id="previewLayout">
                            <?php
                            $layout_settings = array();
                            $extra_sidebar = get_post_meta($post->ID, '_demontek_steam_extra_sidebar', true);
                            $content_layout = get_post_meta($post->ID, '_demontek_steam_content_layout', true);
                            
                            if ($extra_sidebar) {
                                $layout_settings[] = "Extra Sidebar: ✅ Enabled";
                            }
                            if ($content_layout) {
                                $layout_settings[] = "Content Layout: " . ucfirst($content_layout);
                            }
                            
                            echo !empty($layout_settings) ? implode('<br>', $layout_settings) : 'Default layout settings';
                            ?>
                        </div>
                    </div>
                    
                    <!-- Raw Field Count -->
                    <div class="preview-field-group">
                        <strong>📊 Field Summary:</strong>
                        <div class="preview-summary" id="previewSummary">
                            <?php
                            $all_fields = array(
                                'trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5',
                                'ai_summary', 'ai_excerpt', 'game_genre',
                                'review_1', 'review_2', 'review_3',
                                'original_link', 'steam_link', 'amazon_link',
                                'developer', 'platforms', 'release_date'
                            );
                            
                            $filled_fields = 0;
                            $total_fields = count($all_fields);
                            
                            foreach ($all_fields as $field) {
                                if (!empty(get_post_meta($post->ID, $field, true))) {
                                    $filled_fields++;
                                }
                            }
                            
                            $completion_rate = $total_fields > 0 ? round(($filled_fields / $total_fields) * 100) : 0;
                            echo "Fields filled: $filled_fields/$total_fields ($completion_rate%)";
                            ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- No Post Selected - Show Empty Structure -->
                <div class="mobile-preview-header">
                    <div class="mobile-preview-title">No Post Selected</div>
                    <div class="mobile-preview-meta">
                        <span class="preview-date">Select a post to see preview</span>
                    </div>
                </div>
                
                <div class="mobile-preview-content">
                    <div class="no-post-selected" style="padding: 40px 20px; text-align: center; color: #666;">
                        <h3>📱 Complete Field Preview</h3>
                        <p>Load posts to see all Steam fields displayed here:</p>
                        <ul style="text-align: left; margin: 20px 0;">
                            <li>🎮 Steam Layout Status</li>
                            <li>📝 Game Genre & Descriptions</li>
                            <li>🎬 All 5 Trailer Slots</li>
                            <li>🔗 Steam, Amazon & Original Links</li>
                            <li>⭐ All 3 Review Slots</li>
                            <li>🎯 Developer & Platform Details</li>
                            <li>⚙️ Layout Configuration</li>
                            <li>📊 Completion Statistics</li>
                        </ul>
                        <p><strong>👆 Click "Load Posts" above to get started!</strong></p>
                    </div>
                </div>
                <?php endif; ?>
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

            /* Post Loader */
            .mobile-post-loader {
                background: white;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-left: 4px solid #00a32a;
            }

            .loader-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
            }

            .loader-header h3 {
                margin: 0;
                color: #23282d;
            }

            .loader-actions {
                display: flex;
                gap: 10px;
            }

            .category-selector-row {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 10px;
            }

            .category-selector select {
                padding: 8px 12px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 14px;
            }

            /* Post Navigation */
            .post-navigation-bar {
                background: #f8f9fa;
                padding: 12px 20px;
                margin-bottom: 15px;
                border-radius: 6px;
                border-left: 4px solid #007cba;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .nav-info {
                display: flex;
                gap: 10px;
                font-size: 12px;
                color: #666;
            }

            .nav-controls {
                display: flex;
                gap: 8px;
            }

            /* Current Post Display */
            .current-post-display {
                background: white;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-left: 4px solid #2271b1;
            }

            .post-header h3 {
                margin: 0 0 5px 0;
                color: #23282d;
            }

            .post-meta {
                display: flex;
                gap: 15px;
                margin-bottom: 15px;
                font-size: 12px;
                color: #666;
            }

            .post-actions {
                display: flex;
                gap: 10px;
            }

            /* Export/Import Panel */
            #exportImportPanel {
                animation: slideDown 0.3s ease;
            }

            @keyframes slideDown {
                from { height: 0; opacity: 0; }
                to { height: auto; opacity: 1; }
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
                overflow-y: auto;
                border-radius: 0 0 6px 6px;
                border: 1px solid #e0e0e0;
            }

            .mobile-preview-frame {
                background: white;
                margin: 0;
                padding: 0;
                height: 100%;
                overflow-y: auto;
            }

            .mobile-preview-header {
                background: #f8f9fa;
                padding: 15px;
                border-bottom: 1px solid #e0e0e0;
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .mobile-preview-title {
                font-size: 16px;
                font-weight: bold;
                color: #23282d;
                margin-bottom: 5px;
            }

            .mobile-preview-meta {
                font-size: 12px;
                color: #666;
                display: flex;
                gap: 15px;
            }

            .mobile-preview-content {
                padding: 15px;
            }

            .mobile-preview-image {
                margin-bottom: 15px;
                text-align: center;
            }

            .mobile-preview-image img {
                max-width: 100%;
                height: auto;
                border-radius: 6px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }

            .preview-field-group {
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .preview-field-group:last-child {
                border-bottom: none;
            }

            .preview-field-group strong {
                display: block;
                margin-bottom: 5px;
                color: #23282d;
                font-size: 13px;
            }

            .preview-field-group span,
            .preview-field-group div {
                font-size: 12px;
                color: #666;
                line-height: 1.4;
            }

            .preview-text-content {
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                border-left: 3px solid #007cba;
                font-style: italic;
            }

            .preview-trailers,
            .preview-links,
            .preview-reviews {
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                font-size: 11px;
                max-height: 120px;
                overflow-y: auto;
            }

            .preview-trailers {
                border-left: 3px solid #ff6b6b;
            }

            .preview-links {
                border-left: 3px solid #4ecdc4;
            }

            .preview-links a {
                color: #007cba;
                text-decoration: none;
                word-break: break-all;
            }

            .preview-links a:hover {
                text-decoration: underline;
            }

            .preview-reviews {
                border-left: 3px solid #ffe66d;
            }

            .steam-status {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: bold;
            }

            .steam-status.enabled {
                background: #d1ecf1;
                color: #0c5460;
            }

            .steam-status.disabled {
                background: #f8d7da;
                color: #721c24;
            }

            .no-post-preview {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .no-post-preview h3 {
                font-size: 18px;
                margin: 0;
            }
            
            .no-post-preview p {
                margin: 0;
                line-height: 1.4;
            }
            
            .no-post-preview ol {
                line-height: 1.6;
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
                
                .category-selector-row {
                    flex-direction: column;
                    gap: 10px;
                }
                
                .post-navigation-bar {
                    flex-direction: column;
                    gap: 10px;
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
                    this.currentPostId = 0;
                    this.loadedPosts = [];
                    this.currentPostIndex = 0;
                    this.saveTimeouts = {};
                    this.isCustomMode = false;
                    this.exportMode = false;
                    this.init();
                }
                
                init() {
                    console.log('Mobile Editor initializing...');
                    this.setupEventListeners();
                    this.initializeComponents();
                    this.setupAutoSave();
                    console.log('📱 Demontek Mobile Editor v<?php echo $this->version; ?> initialized');
                    
                    // Add a click event to the refresh button
                    const refreshBtn = document.getElementById('refreshPreviewBtn');
                    if (refreshBtn) {
                        refreshBtn.addEventListener('click', () => {
                            console.log('Refresh preview button clicked');
                            this.updatePreview();
                        });
                    }
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
                
                loadPosts() {
                    const categorySelector = document.getElementById('categorySelector');
                    const loadBtn = document.getElementById('loadPostsBtn');
                    const loadStatus = document.getElementById('loadStatus');
                    
                    if (!categorySelector) return;
                    
                    const categoryId = categorySelector.value;
                    
                    // Show loading state
                    loadBtn.disabled = true;
                    loadBtn.textContent = '🔄 Loading...';
                    loadStatus.textContent = 'Loading posts...';
                    
                    const data = {
                        action: 'steam_load_posts',
                        category_id: categoryId,
                        nonce: demontekSteam.nonce
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
                            console.log('Posts loaded successfully:', result.data);
                            this.loadedPosts = result.data.posts || [];
                            this.currentPostIndex = 0;
                            
                            if (this.loadedPosts.length > 0) {
                                this.currentPostId = this.loadedPosts[0].ID;
                                console.log('Current post ID set to:', this.currentPostId);
                                this.displayCurrentPost();
                                this.showPostNavigation();
                                this.loadCurrentPostData();
                                this.setupFieldListeners(); // Setup field listeners after loading posts
                                loadStatus.textContent = `${this.loadedPosts.length} posts loaded successfully`;
                                
                                // Show the tabbed interface
                                this.showTabbedInterface();
                            } else {
                                loadStatus.textContent = 'No posts found in this category';
                            }
                        } else {
                            loadStatus.textContent = 'Error loading posts';
                            console.error('Load posts error:', result.data);
                        }
                        
                        // Reset button
                        loadBtn.disabled = false;
                        loadBtn.textContent = '🔄 Load Posts';
                    })
                    .catch(error => {
                        console.error('Load posts error:', error);
                        loadStatus.textContent = 'Error loading posts';
                        loadBtn.disabled = false;
                        loadBtn.textContent = '🔄 Load Posts';
                    });
                }
                
                showTabbedInterface() {
                    // Show component status bar and tabbed interface
                    const statusBar = document.querySelector('.component-status-bar');
                    const archMap = document.querySelector('.architecture-map');
                    const settingsGrid = document.querySelector('.settings-grid');
                    
                    if (statusBar) statusBar.style.display = 'flex';
                    if (archMap) archMap.style.display = 'block';
                    if (settingsGrid) settingsGrid.style.display = 'grid';
                }
                
                displayCurrentPost() {
                    if (!this.loadedPosts[this.currentPostIndex]) {
                        console.log('No post at current index:', this.currentPostIndex);
                        return;
                    }
                    
                    const post = this.loadedPosts[this.currentPostIndex];
                    console.log('Displaying current post:', post);
                    const display = document.getElementById('currentPostDisplay');
                    
                    if (display) {
                        display.style.display = 'block';
                        
                        // Update post info
                        document.getElementById('currentPostTitle').textContent = post.post_title || 'Untitled Post';
                        document.getElementById('currentPostStatus').textContent = `Status: ${post.post_status}`;
                        document.getElementById('currentPostDate').textContent = `Date: ${post.post_date}`;
                        
                        // Update links
                        document.getElementById('classicEditLink').href = `post.php?post=${post.ID}&action=edit`;
                        document.getElementById('viewPostLink').href = post.permalink || '#';
                        
                        // Update the preview view live link
                        const viewLiveLink = document.getElementById('viewLiveLink');
                        if (viewLiveLink) {
                            viewLiveLink.href = post.permalink || '#';
                        }
                        
                        console.log('Current post displayed successfully');
                    } else {
                        console.log('Current post display element not found');
                    }
                }
                
                showPostNavigation() {
                    const navBar = document.getElementById('postNavigationBar');
                    if (navBar) {
                        navBar.style.display = 'flex';
                        this.updateNavigationInfo();
                    }
                }
                
                updateNavigationInfo() {
                    const counter = document.getElementById('postCounter');
                    const category = document.getElementById('postCategory');
                    const postId = document.getElementById('postId');
                    
                    if (counter) counter.textContent = `Post ${this.currentPostIndex + 1}/${this.loadedPosts.length}`;
                    if (postId && this.loadedPosts[this.currentPostIndex]) {
                        postId.textContent = `• ID: ${this.loadedPosts[this.currentPostIndex].ID}`;
                    }
                    
                    // Update navigation buttons
                    const prevBtn = document.getElementById('prevBtn');
                    const nextBtn = document.getElementById('nextBtn');
                    
                    if (prevBtn) prevBtn.disabled = this.currentPostIndex === 0;
                    if (nextBtn) nextBtn.disabled = this.currentPostIndex === this.loadedPosts.length - 1;
                }
                
                previousPost() {
                    if (this.currentPostIndex > 0) {
                        this.currentPostIndex--;
                        this.currentPostId = this.loadedPosts[this.currentPostIndex].ID;
                        this.displayCurrentPost();
                        this.updateNavigationInfo();
                        this.loadCurrentPostData();
                        this.setupFieldListeners(); // Re-setup field listeners for new post
                        this.showNotification('⬅️ Previous post loaded', 'info');
                    }
                }
                
                nextPost() {
                    if (this.currentPostIndex < this.loadedPosts.length - 1) {
                        this.currentPostIndex++;
                        this.currentPostId = this.loadedPosts[this.currentPostIndex].ID;
                        this.displayCurrentPost();
                        this.updateNavigationInfo();
                        this.loadCurrentPostData();
                        this.setupFieldListeners(); // Re-setup field listeners for new post
                        this.showNotification('➡️ Next post loaded', 'info');
                    }
                }
                
                loadCurrentPostData() {
                    if (!this.currentPostId) return;
                    
                    const data = {
                        action: 'mobile_get_post_data',
                        post_id: this.currentPostId,
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
                            this.populateFields(result.data);
                            
                            // Update the preview panel with new post data
                            this.refreshPreviewPanel(result.data);
                        } else {
                            console.error('Error loading post data:', result.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading post data:', error);
                    });
                }
                
                refreshPreviewPanel(postData) {
                    console.log('refreshPreviewPanel called with:', postData);
                    
                    // This would ideally reload the preview section with new post data
                    // For now, we'll update the preview content with current field values
                    const previewFrame = document.getElementById('mobilePreviewFrame');
                    if (previewFrame) {
                        console.log('Preview frame found, updating...');
                        // Update the preview header
                        const previewTitle = document.querySelector('.mobile-preview-title');
                        if (previewTitle) {
                            previewTitle.textContent = postData.post_title || 'Untitled Post';
                            console.log('Preview title updated');
                        }
                        
                        const previewMeta = document.querySelector('.mobile-preview-meta');
                        if (previewMeta) {
                            const postDate = new Date(postData.post_date || Date.now());
                            const authorName = 'Author'; // Would need to get from post data
                            previewMeta.innerHTML = `
                                <span class="preview-date">${postDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                                <span class="preview-author">By ${authorName}</span>
                            `;
                            console.log('Preview meta updated');
                        }
                        
                        // Update live link
                        const viewLiveLink = document.getElementById('viewLiveLink');
                        if (viewLiveLink && postData.preview_link) {
                            viewLiveLink.href = postData.preview_link;
                        }
                        
                        // Update all preview fields with loaded data
                        setTimeout(() => {
                            console.log('About to update preview from refreshPreviewPanel');
                            this.updatePreview();
                        }, 100);
                    } else {
                        console.log('Preview frame not found');
                    }
                }
                
                populateFields(data) {
                    console.log('populateFields called with data:', data);
                    
                    // Populate all form fields with post data
                    const fields = data.fields || {};
                    
                    // Main fields
                    const postTitle = document.getElementById('postTitle');
                    if (postTitle) {
                        postTitle.value = data.post_title || '';
                        console.log('Post title populated:', data.post_title);
                    }
                    
                    const aiSummary = document.getElementById('aiSummary');
                    if (aiSummary) {
                        aiSummary.value = fields.ai_summary || '';
                        console.log('AI summary populated:', fields.ai_summary);
                    }
                    
                    const gameGenre = document.getElementById('gameGenre');
                    if (gameGenre) {
                        gameGenre.value = fields.game_genre || '';
                        console.log('Game genre populated:', fields.game_genre);
                    }
                    
                    // Steam toggle
                    const steamToggle = document.getElementById('enableSteamLayout');
                    if (steamToggle) {
                        steamToggle.checked = fields._demontek_steam_use == '1';
                        this.isCustomMode = steamToggle.checked;
                        this.updateComponentStatus();
                        this.updateMainToggleDisplay(this.isCustomMode);
                        console.log('Steam toggle populated:', fields._demontek_steam_use);
                    }
                    
                    // All other fields
                    const allFields = [
                        'trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5',
                        'original_link', 'steam_link', 'amazon_link',
                        'review_1', 'review_2', 'review_3',
                        'developer', 'platforms', 'release_date'
                    ];
                    
                    allFields.forEach(fieldId => {
                        const element = document.getElementById(fieldId);
                        if (element) {
                            element.value = fields[fieldId] || '';
                            console.log(`${fieldId} populated:`, fields[fieldId]);
                        }
                    });
                    
                    // Update preview after populating all fields
                    setTimeout(() => {
                        console.log('About to update preview after field population');
                        this.updatePreview();
                        console.log('Preview updated after field population');
                    }, 200);
                }
                
                updateMainToggleDisplay(isCustomMode) {
                    const status = document.getElementById('steamLayoutStatus');
                    const modeStatus = document.getElementById('modeStatus');
                    const modeStatusBox = document.getElementById('modeStatusBox');
                    
                    if (status) {
                        status.textContent = isCustomMode ? 'Enabled' : 'Disabled';
                        status.className = isCustomMode ? 'toggle-status on' : 'toggle-status off';
                    }
                    
                    if (modeStatus && modeStatusBox) {
                        if (isCustomMode) {
                            modeStatus.innerHTML = '🎮 STEAM MODE: All gaming components active';
                            modeStatusBox.className = 'mode-status-box custom';
                        } else {
                            modeStatus.innerHTML = '📝 STANDARD MODE: Regular WordPress post';
                            modeStatusBox.className = 'mode-status-box';
                        }
                    }
                }
                
                // Test function to simulate field changes
                testPreviewUpdate() {
                    console.log('Testing preview update...');
                    
                    // Test updating title
                    const titleElement = document.getElementById('postTitle');
                    if (titleElement) {
                        titleElement.value = 'Test Title Changed';
                        this.updateMobileTitle('Test Title Changed');
                    }
                    
                    // Test updating summary
                    const summaryElement = document.getElementById('aiSummary');
                    if (summaryElement) {
                        summaryElement.value = 'Test summary changed';
                        this.updateMobileDescription('Test summary changed');
                    }
                    
                    // Test updating genre
                    const genreElement = document.getElementById('gameGenre');
                    if (genreElement) {
                        genreElement.value = 'Test Genre';
                        this.updateMobileCategory('Test Genre');
                    }
                    
                    console.log('Preview test completed');
                }
                
                // Export/Import functionality
                toggleExportMode() {
                    this.exportMode = !this.exportMode;
                    const panel = document.getElementById('exportImportPanel');
                    if (panel) {
                        panel.style.display = this.exportMode ? 'block' : 'none';
                    }
                }
                
                exportCurrentPost() {
                    if (!this.currentPostId) {
                        this.showNotification('❌ No post selected', 'error');
                        return;
                    }
                    
                    const post = this.loadedPosts[this.currentPostIndex];
                    if (!post) {
                        this.showNotification('❌ Post data not available', 'error');
                        return;
                    }
                    
                    const exportData = {
                        post_id: this.currentPostId,
                        post_title: post.post_title,
                        export_date: new Date().toISOString(),
                        fields: this.getAllFieldValues()
                    };
                    
                    this.downloadJSON(exportData, `post-${this.currentPostId}-export.json`);
                    this.showNotification('📤 Post exported successfully', 'success');
                    
                    // Update export status
                    const exportStatus = document.getElementById('exportStatus');
                    if (exportStatus) {
                        exportStatus.textContent = `Exported "${post.post_title}" (ID: ${this.currentPostId})`;
                    }
                }
                
                exportAllPosts() {
                    if (this.loadedPosts.length === 0) {
                        this.showNotification('❌ No posts loaded', 'error');
                        return;
                    }
                    
                    const exportData = {
                        export_date: new Date().toISOString(),
                        total_posts: this.loadedPosts.length,
                        posts: this.loadedPosts.map(post => ({
                            post_id: post.ID,
                            post_title: post.post_title,
                            // Would need to fetch fields for each post
                        }))
                    };
                    
                    this.downloadJSON(exportData, `all-posts-export.json`);
                    this.showNotification(`📤 ${this.loadedPosts.length} posts exported`, 'success');
                }
                
                importData() {
                    const fileInput = document.getElementById('importFile');
                    const file = fileInput.files[0];
                    
                    if (!file) {
                        this.showNotification('❌ Please select a file', 'error');
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        try {
                            const importData = JSON.parse(e.target.result);
                            // Process import data
                            this.processImportData(importData);
                            this.showNotification('📥 Data imported successfully', 'success');
                            
                            // Update export status
                            const exportStatus = document.getElementById('exportStatus');
                            if (exportStatus) {
                                exportStatus.textContent = `Imported data from ${file.name}`;
                            }
                        } catch (error) {
                            this.showNotification('❌ Invalid JSON file', 'error');
                            console.error('Import error:', error);
                        }
                    };
                    reader.readAsText(file);
                }
                
                downloadJSON(data, filename) {
                    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }
                
                
                processImportData(importData) {
                    // Process imported data and populate fields
                    if (importData.fields) {
                        Object.keys(importData.fields).forEach(fieldId => {
                            const element = document.getElementById(fieldId);
                            if (element) {
                                element.value = importData.fields[fieldId];
                                // Trigger change event to update preview
                                element.dispatchEvent(new Event('input'));
                            }
                        });
                    }
                    
                    // Update preview after import
                    this.updatePreview();
                }
                
                getAllFieldValues() {
                    const fields = {};
                    const allFields = [
                        'postTitle', 'aiSummary', 'gameGenre',
                        'trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5',
                        'original_link', 'steam_link', 'amazon_link',
                        'review_1', 'review_2', 'review_3',
                        'developer', 'platforms', 'release_date'
                    ];
                    
                    allFields.forEach(fieldId => {
                        const element = document.getElementById(fieldId);
                        if (element) {
                            fields[fieldId] = element.value;
                        }
                    });
                    
                    return fields;
                }
                
                setupFieldListeners() {
                    const fieldSelectors = [
                        '#postTitle', '#aiSummary', '#gameGenre',
                        '#trailer_1', '#trailer_2', '#trailer_3', '#trailer_4', '#trailer_5',
                        '#original_link', '#steam_link', '#amazon_link',
                        '#review_1', '#review_2', '#review_3',
                        '#developer', '#platforms', '#release_date'
                    ];
                    
                    // Store reference to this for the event handlers
                    const self = this;
                    
                    fieldSelectors.forEach(selector => {
                        const element = document.querySelector(selector);
                        if (element) {
                            // Remove any existing listeners
                            element.removeEventListener('input', element._fieldChangeHandler);
                            element.removeEventListener('change', element._fieldChangeHandler);
                            
                            // Create new handler
                            element._fieldChangeHandler = function(e) {
                                self.handleFieldChange(e.target.id, e.target.value);
                            };
                            
                            element.addEventListener('input', element._fieldChangeHandler);
                            element.addEventListener('change', element._fieldChangeHandler);
                        }
                    });
                    
                    console.log('Field listeners set up for post ID:', this.currentPostId);
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
                    if (!this.currentPostId) {
                        this.showNotification('❌ Please load a post first', 'error');
                        // Reset toggle
                        const steamToggle = document.getElementById('enableSteamLayout');
                        if (steamToggle) steamToggle.checked = false;
                        return;
                    }
                    
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
                    if (!this.currentPostId) {
                        this.showNotification('❌ Please load a post first', 'error');
                        return;
                    }
                    
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
                    if (!this.currentPostId) return;
                    
                    for (const [fieldId, value] of Object.entries(this.saveTimeouts)) {
                        this.saveField(fieldId, value);
                        delete this.saveTimeouts[fieldId];
                    }
                }
                
                saveField(fieldName, value) {
                    if (!this.currentPostId) {
                        console.warn('No post selected for saving');
                        return;
                    }
                    
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
                    console.log('updateMobileTitle called with:', title);
                    const titleElement = document.querySelector('.mobile-preview-title');
                    if (titleElement) {
                        titleElement.textContent = title || 'Untitled Post';
                        console.log('Title updated in preview');
                    } else {
                        console.log('Title element not found');
                    }
                }
                
                updateMobileDescription(description) {
                    console.log('updateMobileDescription called with:', description);
                    const descElement = document.getElementById('previewAiSummary');
                    if (descElement) {
                        descElement.textContent = description || 'No summary available';
                        console.log('Description updated in preview');
                    } else {
                        console.log('Description element not found');
                    }
                }
                
                updateMobileCategory(category) {
                    console.log('updateMobileCategory called with:', category);
                    const categoryElement = document.getElementById('previewGameGenre');
                    if (categoryElement) {
                        categoryElement.textContent = category || 'Not set';
                        console.log('Category updated in preview');
                    } else {
                        console.log('Category element not found');
                    }
                }
                
                updatePreviewField(fieldId, value) {
                    // Update specific preview fields based on field ID
                    const fieldMappings = {
                        'trailer_1': () => this.updatePreviewTrailers(),
                        'trailer_2': () => this.updatePreviewTrailers(),
                        'trailer_3': () => this.updatePreviewTrailers(),
                        'trailer_4': () => this.updatePreviewTrailers(),
                        'trailer_5': () => this.updatePreviewTrailers(),
                        'original_link': () => this.updatePreviewLinks(),
                        'steam_link': () => this.updatePreviewLinks(),
                        'amazon_link': () => this.updatePreviewLinks(),
                        'review_1': () => this.updatePreviewReviews(),
                        'review_2': () => this.updatePreviewReviews(),
                        'review_3': () => this.updatePreviewReviews(),
                        'enableSteamLayout': () => this.updatePreviewSteamStatus()
                    };
                    
                    if (fieldMappings[fieldId]) {
                        fieldMappings[fieldId]();
                    }
                }
                
                updatePreviewTrailers() {
                    console.log('updatePreviewTrailers called');
                    const trailerElements = [
                        document.getElementById('trailer_1'),
                        document.getElementById('trailer_2'),
                        document.getElementById('trailer_3'),
                        document.getElementById('trailer_4'),
                        document.getElementById('trailer_5')
                    ];
                    
                    const trailers = [];
                    trailerElements.forEach((element, index) => {
                        if (element && element.value) {
                            trailers.push(`Trailer ${index + 1}: ${element.value}`);
                        }
                    });
                    
                    const previewElement = document.getElementById('previewTrailers');
                    if (previewElement) {
                        previewElement.innerHTML = trailers.length > 0 ? trailers.join('<br>') : 'No trailers added';
                        console.log('Trailers updated in preview:', trailers);
                    } else {
                        console.log('Trailer preview element not found');
                    }
                }
                
                updatePreviewLinks() {
                    console.log('updatePreviewLinks called');
                    const linkFields = {
                        'steam_link': 'Steam Store',
                        'original_link': 'Original Source',
                        'amazon_link': 'Amazon'
                    };
                    
                    const links = [];
                    Object.keys(linkFields).forEach(fieldId => {
                        const element = document.getElementById(fieldId);
                        if (element && element.value) {
                            const label = linkFields[fieldId];
                            links.push(`${label}: <a href="${element.value}" target="_blank">${element.value}</a>`);
                        }
                    });
                    
                    const previewElement = document.getElementById('previewLinks');
                    if (previewElement) {
                        previewElement.innerHTML = links.length > 0 ? links.join('<br>') : 'No links added';
                        console.log('Links updated in preview:', links);
                    } else {
                        console.log('Links preview element not found');
                    }
                }
                
                updatePreviewReviews() {
                    console.log('updatePreviewReviews called');
                    const reviewElements = [
                        document.getElementById('review_1'),
                        document.getElementById('review_2'),
                        document.getElementById('review_3')
                    ];
                    
                    const reviews = [];
                    reviewElements.forEach((element, index) => {
                        if (element && element.value) {
                            reviews.push(`Review ${index + 1}: ${element.value}`);
                        }
                    });
                    
                    const previewElement = document.getElementById('previewReviews');
                    if (previewElement) {
                        previewElement.innerHTML = reviews.length > 0 ? reviews.join('<br><br>') : 'No reviews added';
                        console.log('Reviews updated in preview:', reviews);
                    } else {
                        console.log('Reviews preview element not found');
                    }
                }
                
                updatePreviewSteamStatus() {
                    console.log('updatePreviewSteamStatus called');
                    const steamToggle = document.getElementById('enableSteamLayout');
                    const statusElement = document.getElementById('previewSteamStatus');
                    
                    if (steamToggle && statusElement) {
                        const isEnabled = steamToggle.checked;
                        statusElement.textContent = isEnabled ? '✅ Enabled' : '❌ Disabled';
                        statusElement.className = isEnabled ? 'steam-status enabled' : 'steam-status disabled';
                        console.log('Steam status updated in preview:', isEnabled);
                    } else {
                        console.log('Steam status elements not found');
                    }
                }
                
                updatePreview() {
                    console.log('updatePreview called');
                    
                    try {
                        // First check if preview elements exist
                        const previewFrame = document.getElementById('mobilePreviewFrame');
                        console.log('Preview frame exists:', !!previewFrame);
                        
                        // Update all preview fields with current values
                        const postTitle = document.getElementById('postTitle');
                        const aiSummary = document.getElementById('aiSummary');
                        const gameGenre = document.getElementById('gameGenre');
                        
                        console.log('Form elements found:', {
                            postTitle: !!postTitle,
                            aiSummary: !!aiSummary,
                            gameGenre: !!gameGenre
                        });
                        
                        if (postTitle) {
                            console.log('Updating title:', postTitle.value);
                            this.updateMobileTitle(postTitle.value);
                        }
                        if (aiSummary) {
                            console.log('Updating summary:', aiSummary.value);
                            this.updateMobileDescription(aiSummary.value);
                        }
                        if (gameGenre) {
                            console.log('Updating genre:', gameGenre.value);
                            this.updateMobileCategory(gameGenre.value);
                        }
                        
                        // Update all other preview fields
                        this.updatePreviewTrailers();
                        this.updatePreviewLinks();
                        this.updatePreviewReviews();
                        this.updatePreviewSteamStatus();
                        
                        console.log('Preview updated with current field values');
                    } catch (error) {
                        console.error('Error updating preview:', error);
                    }
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
            
            // Global functions for button clicks
            function loadPosts() {
                if (window.mobileEditor) {
                    window.mobileEditor.loadPosts();
                }
            }
            
            function previousPost() {
                if (window.mobileEditor) {
                    window.mobileEditor.previousPost();
                }
            }
            
            function nextPost() {
                if (window.mobileEditor) {
                    window.mobileEditor.nextPost();
                }
            }
            
            function refreshCurrentPost() {
                if (window.mobileEditor) {
                    window.mobileEditor.loadCurrentPostData();
                    window.mobileEditor.showNotification('🔄 Post data refreshed', 'info');
                }
            }
            
            function toggleExportMode() {
                if (window.mobileEditor) {
                    window.mobileEditor.toggleExportMode();
                }
            }
            
            function exportCurrentPost() {
                if (window.mobileEditor) {
                    window.mobileEditor.exportCurrentPost();
                }
            }
            
            function exportAllPosts() {
                if (window.mobileEditor) {
                    window.mobileEditor.exportAllPosts();
                }
            }
            
            function importData() {
                if (window.mobileEditor) {
                    window.mobileEditor.importData();
                }
            }
            
            // Global functions for backward compatibility
            function selectPost(postId) {
                // Legacy function - not used in new interface
                console.log('selectPost called with:', postId);
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
            
            function testPreviewUpdate() {
                if (window.mobileEditor) {
                    window.mobileEditor.testPreviewUpdate();
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