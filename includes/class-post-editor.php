<?php
/**
 * Demontek Steam Post Editor Class v1.6.3
 * 
 * Handles all edit post page functionality for Steam template
 * Extracted from main plugin file for modular architecture
 */

if (!defined('ABSPATH')) exit;

class DemontekSteamPostEditor {
    
    private $plugin_url;
    private $plugin_path;
    private $version;
    
    public function __construct($plugin_url, $plugin_path, $version) {
        $this->plugin_url = $plugin_url;
        $this->plugin_path = $plugin_path;
        $this->version = $version;
        
        // Hook into WordPress
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post_meta'));
        
        // AJAX handlers for edit post page features
        add_action('wp_ajax_demontek_full_preview', array($this, 'ajax_full_preview'));
        add_action('wp_ajax_steam_refresh_fields', array($this, 'ajax_refresh_fields'));
    }
    
    /**
     * Add meta boxes to post edit screen
     */
    public function add_meta_boxes() {
        if (!get_option('demontek_steam_enabled', false)) return;
        
        add_meta_box(
            'demontek_steam_controls',
            '?? Demontek Steam Controls v1.6.3',
            array($this, 'render_post_controls'),
            'post',
            'side',
            'high'
        );
        
        add_meta_box(
            'demontek_single_post_enhancer',
            '?? Demontek Single Post Enhancer',
            array($this, 'render_single_post_enhancer'),
            'post',
            'normal',
            'default'
        );
        
        add_meta_box(
            'demontek_steam_fields',
            '?? Steam Fields Status',
            array($this, 'render_field_status'),
            'post',
            'normal',
            'default'
        );
    }
    
    /**
     * Render post controls in sidebar
     */
    public function render_post_controls($post) {
        wp_nonce_field('demontek_steam_post_nonce', 'demontek_steam_post_nonce');
        
        $use_steam = get_post_meta($post->ID, '_demontek_steam_use', true);
        $extra_sidebar = get_post_meta($post->ID, '_demontek_steam_extra_sidebar', true);
        $content_layout = get_post_meta($post->ID, '_demontek_steam_content_layout', true) ?: 'right';
        
        ?>
        <div class="demontek-post-controls">
            <style>
                .demontek-control-item { 
                    margin-bottom: 20px; 
                    padding: 15px; 
                    border: 1px solid #e0e0e0; 
                    border-radius: 6px; 
                    background: #f9f9f9;
                }
                .demontek-control-item:last-child { margin-bottom: 0; }
                .demontek-control-item label { 
                    display: block; 
                    font-weight: 600; 
                    margin-bottom: 8px; 
                    color: #2271b1; 
                }
                .demontek-control-item select { 
                    width: 100%; 
                    padding: 8px; 
                    border: 1px solid #ccd0d4; 
                    border-radius: 4px; 
                }
                .demontek-preview-actions { 
                    display: grid; 
                    grid-template-columns: 1fr 1fr; 
                    gap: 8px; 
                    margin-top: 15px;
                }
                .demontek-preview-actions .button { 
                    padding: 8px 12px; 
                    font-size: 11px; 
                    text-align: center;
                }
                .demontek-admin-actions {
                    margin-top: 15px;
                    padding-top: 15px;
                    border-top: 2px solid #2271b1;
                }
                .demontek-admin-actions .button {
                    width: 100%;
                    margin-bottom: 5px;
                }
                .demontek-new-feature {
                    background: #e7f3ff;
                    border: 1px solid #67c1f5;
                    border-radius: 4px;
                    padding: 8px;
                    margin-top: 8px;
                    font-size: 11px;
                    color: #2271b1;
                }
            </style>
            
            <div class="demontek-control-item">
                <label>
                    <input type="checkbox" name="demontek_steam_use" value="1" <?php checked($use_steam); ?>>
                    Use Steam Layout for This Post
                </label>
                <small style="color: #666;">Enable the Steam gaming layout for this specific post</small>
            </div>
            
            <div class="demontek-control-item">
                <label>Content Layout Configuration:</label>
                <select name="demontek_steam_content_layout">
                    <option value="right" <?php selected($content_layout, 'right'); ?>>Right Sidebar Only</option>
                    <option value="both" <?php selected($content_layout, 'both'); ?>>Both Sidebars</option>
                    <option value="left" <?php selected($content_layout, 'left'); ?>>Left Sidebar Only</option>
                    <option value="full" <?php selected($content_layout, 'full'); ?>>No Sidebars (Full Width)</option>
                </select>
                <small style="color: #666;">Choose how content is arranged on your Steam layout</small>
                <div class="demontek-new-feature">
                    ?? v1.6.3: Fixed JavaScript errors + Modular architecture!
                </div>
            </div>
            
            <div class="demontek-control-item">
                <label>
                    <input type="checkbox" name="demontek_steam_extra_sidebar" value="1" <?php checked($extra_sidebar); ?>>
                    Enable Extra Left Sidebar
                </label>
                <small style="color: #666;">Add an additional left sidebar for extra content</small>
            </div>
            
            <div class="demontek-admin-actions">
                <div class="demontek-preview-actions">
                    <button type="button" class="button button-primary" onclick="openFullPreview('desktop')">
                        ??? Desktop Preview
                    </button>
                    <button type="button" class="button button-primary" onclick="openFullPreview('mobile')">
                        ?? Mobile Preview
                    </button>
                </div>
                
                <button type="button" class="button button-secondary" onclick="refreshFieldStatus()">
                    ?? Refresh Field Status
                </button>
                
                <div style="margin-top: 10px; padding: 8px; background: #e7f3ff; border-radius: 4px; font-size: 11px; color: #2271b1;">
                    <strong>v1.6.3:</strong> Fixed critical JavaScript errors, extracted edit post functionality for better performance!
                </div>
            </div>
        </div>
        
        <script>
        function openFullPreview(mode) {
            const form = document.getElementById('post');
            const formData = new FormData(form);
            formData.append('action', 'demontek_full_preview');
            formData.append('post_id', <?php echo $post->ID; ?>);
            formData.append('mode', mode);
            formData.append('nonce', '<?php echo wp_create_nonce('demontek_steam_nonce'); ?>');
            
            // Create a form and submit it to open in new tab
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = ajaxurl;
            tempForm.target = '_blank';
            tempForm.style.display = 'none';
            
            for (let [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                tempForm.appendChild(input);
            }
            
            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);
        }
        
        function refreshFieldStatus() {
            const statusArea = document.querySelector('#demontek_steam_fields .inside');
            if (statusArea) {
                statusArea.innerHTML = '<p>?? Refreshing field status...</p>';
                
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=steam_refresh_fields&post_id=<?php echo $post->ID; ?>&nonce=<?php echo wp_create_nonce('demontek_steam_nonce'); ?>'
                })
                .then(response => response.text())
                .then(html => {
                    statusArea.innerHTML = html;
                })
                .catch(error => {
                    console.error('?? Field refresh error:', error);
                    statusArea.innerHTML = '<p style="color: red;">? Error refreshing fields</p>';
                });
            }
        }
        </script>
        <?php
    }
    
    /**
     * Render single post enhancer with all Steam fields
     */
    public function render_single_post_enhancer($post) {
        $steam_fields = array(
            'trailer_1' => 'Main Trailer URL',
            'trailer_2' => 'Gameplay Trailer URL', 
            'trailer_3' => 'Features Trailer URL',
            'trailer_4' => 'Extended Trailer URL',
            'trailer_5' => 'Bonus Trailer URL',
            'ai_summary' => 'AI Generated Summary',
            'ai_excerpt' => 'AI Generated Excerpt',
            'game_genre' => 'Game Genre/Category',
            'review_1' => 'Community Review #1',
            'review_2' => 'Community Review #2',
            'review_3' => 'Community Review #3',
            'original_link' => 'Original YouTube Link',
            'steam_link' => 'Steam Store Link',
            'amazon_link' => 'Amazon Purchase Link',
            'developer' => 'Game Developer',
            'platforms' => 'Available Platforms',
            'release_date' => 'Release Date'
        );
        
        echo '<div class="demontek-enhancer-wrapper">';
        echo '<p><strong>?? Single Post Enhancer v1.6.3</strong> - Enhanced edit post experience with modular architecture! These fields control what appears in your Steam layout.</p>';
        
        echo '<div style="background: #e7f3ff; border: 1px solid #67c1f5; border-radius: 6px; padding: 12px; margin-bottom: 20px;">';
        echo '<strong style="color: #2271b1;">?? v1.6.3 Updates:</strong> Fixed critical JavaScript errors! Extracted edit post functionality to modular class. Enhanced field management with better error handling!';
        echo '</div>';
        
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">';
        
        $field_count = 0;
        foreach ($steam_fields as $field => $label) {
            if ($field_count == 8) {
                echo '</div><div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">';
            }
            
            $value = get_post_meta($post->ID, $field, true);
            $is_url = strpos($field, 'link') !== false || strpos($field, 'trailer') !== false;
            
            echo '<div style="background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #e0e0e0;">';
            echo '<label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2271b1;">' . $label . '</label>';
            
            if ($is_url) {
                echo '<input type="url" name="' . $field . '" value="' . esc_attr($value) . '" style="width: 100%; padding: 8px; border: 1px solid #ccd0d4; border-radius: 4px;" placeholder="https://...">';
            } else {
                echo '<textarea name="' . $field . '" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccd0d4; border-radius: 4px; resize: vertical;">' . esc_textarea($value) . '</textarea>';
            }
            
            echo '<small style="color: #666; display: block; margin-top: 5px;">';
            if (strpos($field, 'trailer') !== false) {
                echo 'YouTube URL for video trailer (v1.6.3 with enhanced error handling)';
            } elseif (strpos($field, 'ai_') !== false) {
                echo 'AI generated content for layout';
            } elseif (strpos($field, 'review') !== false) {
                echo 'Community review text';
            } else {
                echo 'Text or URL field';
            }
            echo '</small>';
            echo '</div>';
            
            $field_count++;
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Render field status overview
     */
    public function render_field_status($post) {
        $this->output_field_status($post->ID);
    }
    
    /**
     * Output field status with completion tracking
     */
    private function output_field_status($post_id) {
        $steam_fields = array(
            'Trailers' => array('trailer_1' => 'Main Trailer', 'trailer_2' => 'Gameplay', 'trailer_3' => 'Features', 'trailer_4' => 'Extended', 'trailer_5' => 'Bonus'),
            'AI Content' => array('ai_summary' => 'AI Summary', 'ai_excerpt' => 'AI Excerpt', 'game_genre' => 'Game Genre'),
            'Reviews' => array('review_1' => 'Review 1', 'review_2' => 'Review 2', 'review_3' => 'Review 3'),
            'Links & Info' => array('original_link' => 'YouTube Link', 'steam_link' => 'Steam Link', 'amazon_link' => 'Amazon Link', 'developer' => 'Developer', 'platforms' => 'Platforms', 'release_date' => 'Release Date'),
        );
        
        $all_meta = get_post_meta($post_id);
        $total_fields = 0;
        $populated_fields = 0;
        
        foreach ($steam_fields as $category => $fields) {
            foreach ($fields as $field => $label) {
                $total_fields++;
                if (!empty($all_meta[$field][0])) {
                    $populated_fields++;
                }
            }
        }
        
        $completion_rate = $total_fields > 0 ? round(($populated_fields / $total_fields) * 100) : 0;
        
        echo '<div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px; text-align: center;">';
        echo '<div style="font-size: 24px; font-weight: bold; color: #2271b1;">' . $populated_fields . '/' . $total_fields . '</div>';
        echo '<div style="font-size: 12px; color: #666;">FIELDS POPULATED (' . $completion_rate . '%)</div>';
        
        $readiness = $completion_rate >= 80 ? 'STEAM READY ??' : ($completion_rate >= 50 ? 'PARTIAL ??' : 'NEEDS WORK ??');
        echo '<div style="margin-top: 8px; font-weight: bold; color: ' . ($completion_rate >= 80 ? '#00a32a' : ($completion_rate >= 50 ? '#f57c00' : '#dc3232')) . ';">' . $readiness . '</div>';
        echo '</div>';
        
        echo '<div style="background: #e7f3ff; border: 1px solid #67c1f5; border-radius: 6px; padding: 10px; margin-bottom: 15px; font-size: 12px; color: #2271b1;">';
        echo '<strong>?? v1.6.3:</strong> Fixed JavaScript errors, extracted edit post functionality, enhanced field management with better error handling!';
        echo '</div>';
        
        foreach ($steam_fields as $category => $fields) {
            echo '<h4 style="color: #2271b1; margin: 15px 0 8px 0;">' . $category . '</h4>';
            foreach ($fields as $field => $label) {
                $value = isset($all_meta[$field][0]) ? $all_meta[$field][0] : '';
                $has_value = !empty($value);
                echo '<div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f0f0;">';
                echo '<span style="font-weight: 500;">' . $label . '</span>';
                echo '<span style="color: ' . ($has_value ? '#00a32a' : '#dc3232') . '; font-weight: bold;">';
                echo $has_value ? '?' : '?';
                echo '</span></div>';
            }
        }
        
        echo '<div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-radius: 4px; font-size: 12px;">';
        echo '<strong>?? Tip:</strong> For best Steam layout results, add at least <code>trailer_1</code> and <code>ai_summary</code> custom fields. v1.6.3 features modular architecture and enhanced error handling!';
        echo '</div>';
    }
    
    /**
     * Save post meta data from edit post page
     */
    public function save_post_meta($post_id) {
        if (!isset($_POST['demontek_steam_post_nonce']) || !wp_verify_nonce($_POST['demontek_steam_post_nonce'], 'demontek_steam_post_nonce')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        // Save control settings
        update_post_meta($post_id, '_demontek_steam_use', isset($_POST['demontek_steam_use']) ? 1 : 0);
        update_post_meta($post_id, '_demontek_steam_extra_sidebar', isset($_POST['demontek_steam_extra_sidebar']) ? 1 : 0);
        update_post_meta($post_id, '_demontek_steam_content_layout', sanitize_text_field($_POST['demontek_steam_content_layout'] ?? 'right'));
        
        // Save Steam fields with enhanced validation
        $steam_fields = array('trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5', 'ai_summary', 'ai_excerpt', 'game_genre', 'review_1', 'review_2', 'review_3', 'original_link', 'steam_link', 'amazon_link', 'developer', 'platforms', 'release_date');
        
        foreach ($steam_fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_textarea_field($_POST[$field]);
                
                // Enhanced URL validation for link fields
                if (strpos($field, 'link') !== false || strpos($field, 'trailer') !== false) {
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                        // Log validation error but still save the value for user to fix
                        error_log("Demontek Steam v1.6.3: Invalid URL format for field {$field}: {$value}");
                    }
                }
                
                update_post_meta($post_id, $field, $value);
            }
        }
    }
    
    /**
     * AJAX handler for full preview
     */
    public function ajax_full_preview() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $mode = sanitize_text_field($_POST['mode']);
        
        if (!current_user_can('edit_post', $post_id)) {
            wp_die('Permission denied');
        }
        
        $post = get_post($post_id);
        $steam_data = get_demontek_steam_field_data($post_id);
        
        // Get form data for preview
        $use_steam = isset($_POST['demontek_steam_use']) ? 1 : 0;
        $extra_sidebar = isset($_POST['demontek_steam_extra_sidebar']) ? 1 : 0;
        $content_layout = sanitize_text_field($_POST['demontek_steam_content_layout'] ?? 'right');
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Steam Layout Preview v1.6.3 - <?php echo esc_html($post->post_title); ?></title>
            <style>
                body { margin: 0; padding: 20px; background: #f0f0f0; }
                .preview-header { 
                    background: #2271b1; 
                    color: white; 
                    padding: 15px 20px; 
                    border-radius: 8px; 
                    margin-bottom: 20px; 
                    display: flex; 
                    justify-content: space-between; 
                    align-items: center;
                }
                .preview-container { 
                    background: white; 
                    border-radius: 8px; 
                    overflow: hidden; 
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    <?php if ($mode === 'mobile'): ?>
                    max-width: 375px;
                    margin: 0 auto;
                    <?php endif; ?>
                }
                .preview-controls {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }
                .preview-btn {
                    background: rgba(255,255,255,0.2);
                    border: 1px solid rgba(255,255,255,0.3);
                    color: white;
                    padding: 8px 16px;
                    border-radius: 4px;
                    cursor: pointer;
                    text-decoration: none;
                }
                .preview-btn:hover {
                    background: rgba(255,255,255,0.3);
                }
                .preview-badge {
                    background: #beee11;
                    color: #1b2838;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 11px;
                    font-weight: bold;
                }
            </style>
            <?php wp_head(); ?>
        </head>
        <body>
            <div class="preview-header">
                <div>
                    <h2 style="margin: 0;">?? Steam Layout Preview - <?php echo ucfirst($mode); ?> Mode</h2>
                    <small>Post: <?php echo esc_html($post->post_title); ?> | Layout: <?php echo ucfirst($content_layout); ?> | <span class="preview-badge">v1.6.3 Fixed</span></small>
                </div>
                <div class="preview-controls">
                    <button class="preview-btn" onclick="window.close()">? Close</button>
                    <a href="<?php echo get_edit_post_link($post_id); ?>" class="preview-btn">?? Edit Post</a>
                </div>
            </div>
            
            <div class="preview-container">
                <?php
                // Include the Steam template CSS
                echo '<link rel="stylesheet" href="' . $this->plugin_url . 'assets/css/steam-frontend.css">';
                
                // Create a mock Steam layout for preview
                $this->render_preview_layout($post, $steam_data, $use_steam, false, $extra_sidebar, $content_layout, $mode);
                ?>
            </div>
            
            <?php if ($mode === 'mobile'): ?>
            <script>
                // Add mobile-specific adjustments
                document.body.style.padding = '10px';
                document.querySelector('.preview-container').style.transform = 'scale(1)';
            </script>
            <?php endif; ?>
        </body>
        </html>
        <?php
        wp_die();
    }
    
    /**
     * AJAX handler for field refresh
     */
    public function ajax_refresh_fields() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        $post_id = intval($_POST['post_id']);
        if (!current_user_can('edit_post', $post_id)) wp_die('Permission denied');
        $this->output_field_status($post_id);
        wp_die();
    }
    
    /**
     * Render preview layout (simplified version)
     */
    private function render_preview_layout($post, $steam_data, $use_steam, $show_zones, $extra_sidebar, $content_layout, $mode) {
        // This renders the actual Steam layout for preview
        $template_path = $this->plugin_path . 'templates/single-steam.php';
        if (file_exists($template_path)) {
            include($template_path);
        } else {
            echo '<p style="padding: 40px; text-align: center; color: #666;">Steam template not found. Check file permissions.</p>';
        }
    }
}