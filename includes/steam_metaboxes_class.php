<?php
/**
 * Steam Metaboxes Class
 * Handles metaboxes on post edit screens
 */

if (!defined('ABSPATH')) exit;

class DemontekSteamMetaboxes {
    
    private $core;
    private $fields;
    
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_metabox_assets'));
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
     * Add meta boxes
     */
    public function add_meta_boxes() {
        $core = $this->get_core();
        if (!$core || !$core->get_setting('enabled')) {
            return;
        }
        
        // Steam Controls metabox (sidebar)
        add_meta_box(
            'demontek_steam_controls',
            '🎮 Demontek Steam Controls v' . DEMONTEK_STEAM_VERSION,
            array($this, 'render_controls_metabox'),
            'post',
            'side',
            'high'
        );
        
        // Single Post Enhancer metabox (main)
        add_meta_box(
            'demontek_single_post_enhancer',
            '🎮 Steam Single Post Enhancer',
            array($this, 'render_enhancer_metabox'),
            'post',
            'normal',
            'default'
        );
        
        // Steam Fields Status metabox (main)
        add_meta_box(
            'demontek_steam_fields_status',
            '📊 Steam Fields Status',
            array($this, 'render_status_metabox'),
            'post',
            'normal',
            'default'
        );
    }
    
    /**
     * Enqueue metabox assets
     */
    public function enqueue_metabox_assets($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        global $post;
        if (!$post || $post->post_type !== 'post') {
            return;
        }
        
        // Add metabox-specific styles
        wp_add_inline_style('wp-admin', $this->get_metabox_styles());
        
        // Add metabox-specific scripts
        wp_add_inline_script('jquery', $this->get_metabox_scripts());
    }
    
    /**
     * Render Steam Controls metabox
     */
    public function render_controls_metabox($post) {
        wp_nonce_field('demontek_steam_post_nonce', 'demontek_steam_post_nonce');
        
        $fields = $this->get_fields();
        if (!$fields) {
            echo '<p>Fields component not available</p>';
            return;
        }
        
        $use_steam = $fields->get_field_value($post->ID, '_demontek_steam_use');
        $extra_sidebar = $fields->get_field_value($post->ID, '_demontek_steam_extra_sidebar');
        $content_layout = $fields->get_field_value($post->ID, '_demontek_steam_content_layout', 'right');
        $show_zones = $fields->get_field_value($post->ID, '_demontek_steam_zones');
        
        ?>
        <div class="demontek-metabox-controls">
            
            <div class="demontek-control-group">
                <label class="demontek-control-label">
                    <input type="checkbox" name="_demontek_steam_use" value="1" <?php checked($use_steam); ?>>
                    <strong>Use Steam Layout for This Post</strong>
                </label>
                <p class="demontek-control-description">Enable the Steam gaming layout for this specific post</p>
            </div>
            
            <div class="demontek-control-group">
                <label class="demontek-control-label"><strong>Content Layout Configuration:</strong></label>
                <select name="_demontek_steam_content_layout" class="demontek-control-select">
                    <option value="right" <?php selected($content_layout, 'right'); ?>>Right Sidebar Only</option>
                    <option value="both" <?php selected($content_layout, 'both'); ?>>Both Sidebars</option>
                    <option value="left" <?php selected($content_layout, 'left'); ?>>Left Sidebar Only</option>
                    <option value="full" <?php selected($content_layout, 'full'); ?>>No Sidebars (Full Width)</option>
                </select>
                <p class="demontek-control-description">Choose how content is arranged on your Steam layout</p>
            </div>
            
            <div class="demontek-control-group">
                <label class="demontek-control-label">
                    <input type="checkbox" name="_demontek_steam_extra_sidebar" value="1" <?php checked($extra_sidebar); ?>>
                    <strong>Enable Extra Left Sidebar</strong>
                </label>
                <p class="demontek-control-description">Add an additional left sidebar for extra content</p>
            </div>
            
            <?php if (current_user_can('edit_posts')): ?>
            <div class="demontek-control-group">
                <label class="demontek-control-label">
                    <input type="checkbox" name="_demontek_steam_zones" value="1" <?php checked($show_zones); ?>>
                    <strong>Show Debug Zones</strong>
                </label>
                <p class="demontek-control-description">Show layout debug zones (admin only)</p>
            </div>
            <?php endif; ?>
            
            <div class="demontek-preview-actions">
                <div class="demontek-action-grid">
                    <button type="button" class="button button-primary demontek-preview-btn" onclick="openSteamPreview('desktop', <?php echo $post->ID; ?>)">
                        🖥️ Desktop Preview
                    </button>
                    <button type="button" class="button button-primary demontek-preview-btn" onclick="openSteamPreview('mobile', <?php echo $post->ID; ?>)">
                        📱 Mobile Preview
                    </button>
                </div>
                
                <button type="button" class="button button-secondary demontek-refresh-btn" onclick="refreshSteamFields(<?php echo $post->ID; ?>)" style="width: 100%; margin-top: 10px;">
                    🔄 Refresh Field Status
                </button>
            </div>
            
            <div class="demontek-version-info">
                <div class="demontek-info-badge">
                    <strong>v<?php echo DEMONTEK_STEAM_VERSION; ?>:</strong> Modular architecture with optimized post loading (10 posts max), improved performance, and better mobile preview!
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Single Post Enhancer metabox
     */
    public function render_enhancer_metabox($post) {
        $fields = $this->get_fields();
        if (!$fields) {
            echo '<p>Fields component not available</p>';
            return;
        }
        
        $supported_fields = $fields->get_supported_fields();
        
        ?>
        <div class="demontek-enhancer-wrapper">
            <div class="demontek-enhancer-header">
                <h3>🎮 Single Post Enhancer v<?php echo DEMONTEK_STEAM_VERSION; ?></h3>
                <p>Quickly manage Steam-specific content fields for this post. These fields control what appears in your Steam layout.</p>
            </div>
            
            <div class="demontek-version-highlight">
                <strong>✨ v<?php echo DEMONTEK_STEAM_VERSION; ?> Features:</strong> Modular architecture, optimized performance, improved field validation, and enhanced YouTube integration!
            </div>
            
            <?php $this->render_field_groups($supported_fields, $post->ID); ?>
        </div>
        <?php
    }
    
    /**
     * Render field groups
     */
    private function render_field_groups($supported_fields, $post_id) {
        $field_groups = array(
            'trailers' => '🎬 Video Content',
            'content' => '📝 Game Content', 
            'reviews' => '⭐ Reviews',
            'links' => '🔗 External Links',
            'metadata' => '📋 Game Information'
        );
        
        echo '<div class="demontek-field-groups">';
        
        foreach ($field_groups as $group_key => $group_label) {
            if (!isset($supported_fields[$group_key])) {
                continue;
            }
            
            echo '<div class="demontek-field-group">';
            echo '<h4 class="demontek-group-title">' . esc_html($group_label) . '</h4>';
            echo '<div class="demontek-group-fields">';
            
            foreach ($supported_fields[$group_key] as $field_key => $field_config) {
                $this->render_single_field($field_key, $field_config, $post_id);
            }
            
            echo '</div></div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render a single field
     */
    private function render_single_field($field_key, $field_config, $post_id) {
        $fields = $this->get_fields();
        $value = $fields->get_field_value($post_id, $field_key);
        $is_required = $field_config['required'] ?? false;
        
        ?>
        <div class="demontek-field-item <?php echo $is_required ? 'required' : ''; ?>">
            <label class="demontek-field-label">
                <?php echo esc_html($field_config['label']); ?>
                <?php if ($is_required): ?>
                    <span class="demontek-required">*</span>
                <?php endif; ?>
            </label>
            
            <div class="demontek-field-input">
                <?php $fields->render_field_input($field_key, $post_id); ?>
            </div>
            
            <?php if (!empty($field_config['description'])): ?>
                <p class="demontek-field-description"><?php echo esc_html($field_config['description']); ?></p>
            <?php endif; ?>
            
            <?php if ($field_config['type'] === 'url' && !empty($value)): ?>
                <div class="demontek-field-preview">
                    <a href="<?php echo esc_url($value); ?>" target="_blank" rel="noopener">🔗 Preview Link</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render Steam Fields Status metabox
     */
    public function render_status_metabox($post) {
        $this->render_field_status_content($post->ID);
    }
    
    /**
     * Render field status content (used by AJAX too)
     */
    public function render_field_status_content($post_id) {
        $fields = $this->get_fields();
        if (!$fields) {
            echo '<p>Fields component not available</p>';
            return;
        }
        
        $completion_status = $fields->get_completion_status($post_id);
        $missing_required = $fields->get_missing_required_fields($post_id);
        $missing_important = $fields->get_missing_important_fields($post_id);
        $supported_fields = $fields->get_supported_fields();
        
        ?>
        <div class="demontek-status-content">
            
            <!-- Completion Overview -->
            <div class="demontek-completion-overview">
                <div class="demontek-completion-score">
                    <div class="demontek-score-number"><?php echo $completion_status['populated_fields']; ?>/<?php echo $completion_status['total_fields']; ?></div>
                    <div class="demontek-score-label">FIELDS POPULATED (<?php echo $completion_status['completion_percentage']; ?>%)</div>
                </div>
                
                <div class="demontek-readiness-status">
                    <?php
                    $readiness_class = '';
                    $readiness_text = '';
                    
                    if ($completion_status['completion_percentage'] >= 80) {
                        $readiness_class = 'excellent';
                        $readiness_text = 'STEAM READY 🚀';
                    } elseif ($completion_status['completion_percentage'] >= 50) {
                        $readiness_class = 'good';
                        $readiness_text = 'PARTIALLY READY ⚡';
                    } else {
                        $readiness_class = 'needs-work';
                        $readiness_text = 'NEEDS WORK 🔧';
                    }
                    ?>
                    <div class="demontek-readiness-badge <?php echo $readiness_class; ?>">
                        <?php echo $readiness_text; ?>
                    </div>
                </div>
            </div>
            
            <!-- Version Info -->
            <div class="demontek-version-info-status">
                <strong>🎯 v<?php echo DEMONTEK_STEAM_VERSION; ?> Features:</strong> Modular architecture, optimized loading, enhanced field validation, improved mobile preview!
            </div>
            
            <!-- Missing Fields Alerts -->
            <?php if (!empty($missing_required)): ?>
            <div class="demontek-missing-alert required">
                <h4>⚠️ Missing Required Fields</h4>
                <div class="demontek-missing-list">
                    <?php foreach ($missing_required as $field_key): ?>
                        <span class="demontek-missing-field required"><?php echo esc_html($field_key); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($missing_important)): ?>
            <div class="demontek-missing-alert important">
                <h4>💡 Missing Important Fields</h4>
                <p>These fields greatly improve your Steam layout:</p>
                <div class="demontek-missing-list">
                    <?php foreach ($missing_important as $field_key): ?>
                        <span class="demontek-missing-field important"><?php echo esc_html($field_key); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Field Status by Category -->
            <div class="demontek-field-status-by-category">
                <?php foreach ($supported_fields as $category => $category_fields): ?>
                    <?php if ($category === 'layout') continue; // Skip layout fields ?>
                    
                    <div class="demontek-category-status">
                        <h4 class="demontek-category-title">
                            <?php 
                            $category_icons = array(
                                'trailers' => '🎬',
                                'content' => '📝',
                                'reviews' => '⭐',
                                'links' => '🔗',
                                'metadata' => '📋'
                            );
                            echo $category_icons[$category] ?? '📄';
                            echo ' ' . ucfirst($category);
                            ?>
                        </h4>
                        
                        <div class="demontek-category-fields">
                            <?php foreach ($category_fields as $field_key => $field_config): ?>
                                <?php 
                                $value = $fields->get_field_value($post_id, $field_key);
                                $has_value = !empty($value);
                                $is_required = $field_config['required'] ?? false;
                                ?>
                                <div class="demontek-field-status-item">
                                    <span class="demontek-field-name">
                                        <?php echo esc_html($field_config['label']); ?>
                                        <?php if ($is_required): ?>
                                            <span class="demontek-required-indicator">*</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="demontek-field-status <?php echo $has_value ? 'populated' : 'empty'; ?>">
                                        <?php echo $has_value ? '✅' : '❌'; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Quick Tips -->
            <div class="demontek-quick-tips">
                <h4>💡 Quick Tips</h4>
                <ul>
                    <li><strong>Minimum recommended:</strong> Add <code>trailer_1</code> and <code>ai_summary</code> for optimal Steam layout results.</li>
                    <li><strong>YouTube URLs:</strong> Use full YouTube URLs (e.g., https://youtube.com/watch?v=VIDEO_ID)</li>
                    <li><strong>Performance:</strong> v<?php echo DEMONTEK_STEAM_VERSION; ?> loads exactly 10 posts to prevent hanging!</li>
                    <li><strong>Mobile Preview:</strong> Use the preview buttons above to test your Steam layout.</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get metabox styles
     */
    private function get_metabox_styles() {
        return '
            .demontek-metabox-controls .demontek-control-group {
                margin-bottom: 20px;
                padding: 15px;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                background: #f9f9f9;
            }
            
            .demontek-control-label {
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
                color: #2271b1;
            }
            
            .demontek-control-select {
                width: 100%;
                padding: 8px;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
            }
            
            .demontek-control-description {
                margin: 5px 0 0 0;
                font-size: 12px;
                color: #666;
                font-style: italic;
            }
            
            .demontek-preview-actions {
                margin-top: 20px;
                padding-top: 15px;
                border-top: 2px solid #2271b1;
            }
            
            .demontek-action-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 10px;
            }
            
            .demontek-preview-btn,
            .demontek-refresh-btn {
                font-size: 11px;
                padding: 8px 12px;
            }
            
            .demontek-version-info {
                margin-top: 15px;
                padding: 10px;
                background: #e7f3ff;
                border: 1px solid #67c1f5;
                border-radius: 4px;
            }
            
            .demontek-info-badge {
                font-size: 11px;
                color: #2271b1;
                line-height: 1.4;
            }
            
            .demontek-enhancer-wrapper .demontek-enhancer-header {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e0e0e0;
            }
            
            .demontek-enhancer-header h3 {
                margin: 0 0 10px 0;
                color: #2271b1;
            }
            
            .demontek-version-highlight {
                background: #e7f3ff;
                border: 1px solid #67c1f5;
                border-radius: 6px;
                padding: 12px;
                margin-bottom: 20px;
                font-size: 12px;
                color: #2271b1;
            }
            
            .demontek-field-groups {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
            
            .demontek-field-group {
                background: #f9f9f9;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                padding: 15px;
            }
            
            .demontek-group-title {
                margin: 0 0 15px 0;
                color: #2271b1;
                font-size: 14px;
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 8px;
            }
            
            .demontek-field-item {
                margin-bottom: 15px;
            }
            
            .demontek-field-item.required .demontek-field-label {
                font-weight: bold;
            }
            
            .demontek-field-label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
                color: #2271b1;
                font-size: 12px;
            }
            
            .demontek-required {
                color: #dc3232;
            }
            
            .demontek-field-description {
                margin: 5px 0 0 0;
                font-size: 11px;
                color: #666;
                font-style: italic;
            }
            
            .demontek-field-preview {
                margin-top: 5px;
            }
            
            .demontek-field-preview a {
                font-size: 11px;
                color: #2271b1;
                text-decoration: none;
            }
            
            .demontek-status-content .demontek-completion-overview {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #f8f9fa;
                border: 1px solid #e9ecef;
                border-radius: 6px;
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .demontek-completion-score {
                text-align: center;
            }
            
            .demontek-score-number {
                font-size: 32px;
                font-weight: bold;
                color: #2271b1;
                line-height: 1;
            }
            
            .demontek-score-label {
                font-size: 12px;
                color: #666;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-top: 5px;
            }
            
            .demontek-readiness-badge {
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: bold;
                font-size: 12px;
                text-align: center;
            }
            
            .demontek-readiness-badge.excellent {
                background: #d1ecf1;
                color: #0c5460;
                border: 1px solid #00a32a;
            }
            
            .demontek-readiness-badge.good {
                background: #fff3cd;
                color: #856404;
                border: 1px solid #f57c00;
            }
            
            .demontek-readiness-badge.needs-work {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #dc3232;
            }
            
            .demontek-version-info-status {
                background: #e7f3ff;
                border: 1px solid #67c1f5;
                border-radius: 4px;
                padding: 10px;
                margin-bottom: 15px;
                font-size: 11px;
                color: #2271b1;
            }
            
            .demontek-missing-alert {
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 15px;
            }
            
            .demontek-missing-alert.required {
                background: #f8d7da;
                border: 1px solid #dc3232;
                color: #721c24;
            }
            
            .demontek-missing-alert.important {
                background: #fff3cd;
                border: 1px solid #f57c00;
                color: #856404;
            }
            
            .demontek-missing-alert h4 {
                margin: 0 0 8px 0;
                font-size: 13px;
            }
            
            .demontek-missing-list {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                margin-top: 8px;
            }
            
            .demontek-missing-field {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 10px;
                font-weight: bold;
                font-family: monospace;
            }
            
            .demontek-missing-field.required {
                background: #dc3232;
                color: white;
            }
            
            .demontek-missing-field.important {
                background: #f57c00;
                color: white;
            }
            
            .demontek-category-status {
                margin-bottom: 15px;
                border: 1px solid #e0e0e0;
                border-radius: 4px;
                overflow: hidden;
            }
            
            .demontek-category-title {
                margin: 0;
                padding: 8px 12px;
                background: #f8f9fa;
                border-bottom: 1px solid #e0e0e0;
                font-size: 12px;
                color: #2271b1;
            }
            
            .demontek-category-fields {
                padding: 10px;
            }
            
            .demontek-field-status-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 4px 0;
                border-bottom: 1px solid #f0f0f0;
                font-size: 11px;
            }
            
            .demontek-field-status-item:last-child {
                border-bottom: none;
            }
            
            .demontek-field-name {
                font-weight: 500;
            }
            
            .demontek-required-indicator {
                color: #dc3232;
                font-weight: bold;
            }
            
            .demontek-field-status.populated {
                color: #00a32a;
            }
            
            .demontek-field-status.empty {
                color: #dc3232;
            }
            
            .demontek-quick-tips {
                background: #f0f6fc;
                border: 1px solid #0969da;
                border-radius: 6px;
                padding: 15px;
                margin-top: 20px;
            }
            
            .demontek-quick-tips h4 {
                margin: 0 0 10px 0;
                color: #0969da;
                font-size: 13px;
            }
            
            .demontek-quick-tips ul {
                margin: 0;
                padding-left: 18px;
            }
            
            .demontek-quick-tips li {
                margin-bottom: 5px;
                font-size: 11px;
                line-height: 1.4;
            }
            
            .demontek-quick-tips code {
                background: rgba(9, 105, 218, 0.1);
                padding: 1px 4px;
                border-radius: 2px;
                font-size: 10px;
            }
        ';
    }
    
    /**
     * Get metabox scripts
     */
    private function get_metabox_scripts() {
        return '
            function openSteamPreview(mode, postId) {
                const baseUrl = "' . home_url() . '/?p=" + postId;
                const params = new URLSearchParams();
                params.append("demontek_preview", "1");
                params.append("mode", mode);
                params.append("_wpnonce", "' . wp_create_nonce('demontek_preview_nonce') . '");
                
                if (mode === "mobile") {
                    params.append("mobile", "1");
                    params.append("show_admin_bar", "false");
                }
                
                const fullUrl = baseUrl + "&" + params.toString();
                window.open(fullUrl, "_blank");
            }
            
            function refreshSteamFields(postId) {
                const statusArea = document.querySelector("#demontek_steam_fields_status .inside");
                if (statusArea) {
                    statusArea.innerHTML = "<p>🔄 Refreshing field status...</p>";
                    
                    fetch(ajaxurl, {
                        method: "POST",
                        headers: {"Content-Type": "application/x-www-form-urlencoded"},
                        body: "action=steam_refresh_fields&post_id=" + postId + "&nonce=' . wp_create_nonce('demontek_steam_nonce') . '"
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            statusArea.innerHTML = data.data.html;
                        } else {
                            statusArea.innerHTML = "<p>❌ Error refreshing fields: " + data.data + "</p>";
                        }
                    })
                    .catch(error => {
                        statusArea.innerHTML = "<p>❌ Network error: " + error.message + "</p>";
                    });
                }
            }
            
            // Auto-refresh field status when fields are changed
            document.addEventListener("DOMContentLoaded", function() {
                const fieldInputs = document.querySelectorAll(".demontek-enhancer-wrapper input, .demontek-enhancer-wrapper textarea, .demontek-enhancer-wrapper select");
                fieldInputs.forEach(function(input) {
                    input.addEventListener("change", function() {
                        // Debounce the refresh
                        clearTimeout(window.steamFieldRefreshTimeout);
                        window.steamFieldRefreshTimeout = setTimeout(function() {
                            const postId = document.querySelector("#post_ID").value;
                            if (postId) {
                                refreshSteamFields(postId);
                            }
                        }, 1000);
                    });
                });
            });
        ';
    }
}