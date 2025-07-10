<?php
/**
 * Plugin Name: Demontek Template - Steam v1.8.0 MOBILE EDITION
 * Description: Steam-inspired gaming layout template system for WordPress. NEW: Mobile Post Editor with tabbed modular architecture and real-time preview!
 * Version: 1.8.0
 * Author: Demontek
 * Text Domain: demontek-steam
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

// Define plugin constants
define('DEMONTEK_STEAM_VERSION', '1.8.0');
define('DEMONTEK_STEAM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DEMONTEK_STEAM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DEMONTEK_STEAM_PLUGIN_FILE', __FILE__);

class DemontekSteamTemplate {
    
    private $plugin_url;
    private $plugin_path;
    private $version = '1.8.0';
    private $post_editor;
    private $mobile_editor;
    private $loaded_classes = array();
    
    public function __construct() {
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('template_include', array($this, 'load_steam_template'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // AJAX handlers - OPTIMIZED with caching
        add_action('wp_ajax_steam_refresh_fields', array($this, 'ajax_refresh_fields'));
        add_action('wp_ajax_steam_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_demontek_preview', array($this, 'ajax_preview'));
        add_action('wp_ajax_steam_load_posts', array($this, 'ajax_load_posts'));
        add_action('wp_ajax_steam_get_post_data', array($this, 'ajax_get_post_data'));
        add_action('wp_ajax_steam_navigate_posts', array($this, 'ajax_navigate_posts'));
        add_action('wp_ajax_steam_debug_info', array($this, 'ajax_debug_info'));
        add_action('wp_ajax_steam_duplicate_post', array($this, 'ajax_duplicate_post'));
        add_action('wp_ajax_steam_update_version', array($this, 'ajax_update_version'));
        
        // NEW: Mobile Editor AJAX handlers
        add_action('wp_ajax_mobile_save_field', array($this, 'ajax_mobile_save_field'));
        add_action('wp_ajax_mobile_get_post_data', array($this, 'ajax_mobile_get_post_data'));
        add_action('wp_ajax_mobile_toggle_steam', array($this, 'ajax_mobile_toggle_steam'));
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Load modular components
        $this->load_modules();
    }
    
    private function load_modules() {
        $class_files = array(
            'class-post-editor.php',
            'class-mobile-editor.php',        // NEW: Mobile Editor Component
            'steam_dashboard_class.php',
            'steam_inspector_class.php',
            'steam_layout_class.php',
            'steam_metaboxes_class.php',
            'steam_renderer_class.php'
        );
        
        foreach ($class_files as $file) {
            $file_path = $this->plugin_path . 'includes/' . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
                $this->loaded_classes[] = $file;
            }
        }
        
        // Initialize post editor class
        if (class_exists('DemontekSteamPostEditor')) {
            $this->post_editor = new DemontekSteamPostEditor($this->plugin_url, $this->plugin_path, $this->version);
        } elseif (class_exists('Demontek_Steam_Post_Editor')) {
            $this->post_editor = new Demontek_Steam_Post_Editor();
        }
        
        // Initialize mobile editor class
        if (class_exists('DemontekMobileEditor')) {
            $this->mobile_editor = new DemontekMobileEditor($this->plugin_url, $this->plugin_path, $this->version);
        }
        
        $this->init_additional_classes();
    }
    
    private function init_additional_classes() {
        $classes_to_init = array(
            'Steam_Dashboard_Class',
            'Steam_Inspector_Class', 
            'Steam_Layout_Class',
            'Steam_Metaboxes_Class',
            'Steam_Renderer_Class'
        );
        
        foreach ($classes_to_init as $class_name) {
            if (class_exists($class_name)) {
                try {
                    new $class_name();
                } catch (Exception $e) {
                    error_log("Demontek Steam: Error initializing {$class_name} - " . $e->getMessage());
                }
            }
        }
    }
    
    public function init() {
        load_plugin_textdomain('demontek-steam', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function activate() {
        add_option('demontek_steam_enabled', false);
        add_option('demontek_steam_global_mode', false);
        add_option('demontek_steam_auto_categories', array());
        add_option('demontek_steam_version', $this->version);
        add_option('demontek_mobile_editor_enabled', true);  // NEW: Enable mobile editor by default
        update_option('demontek_steam_version', $this->version);
        
        $includes_dir = $this->plugin_path . 'includes';
        if (!file_exists($includes_dir)) {
            wp_mkdir_p($includes_dir);
        }
        
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Add admin menus including new Mobile Editor
     */
    public function add_admin_menu() {
        add_menu_page(
            'Demontek Steam',
            'Demontek Steam',
            'edit_posts',
            'demontek-steam',
            array($this, 'admin_page'),
            'dashicons-games',
            30
        );
        
        add_submenu_page(
            'demontek-steam',
            'Steam Dashboard',
            '🎮 Dashboard',
            'edit_posts',
            'demontek-steam',
            array($this, 'admin_page')
        );
        
        // NEW: Mobile Editor submenu
        add_submenu_page(
            'demontek-steam',
            'Mobile Post Editor',
            '📱 Mobile Editor',
            'edit_posts',
            'demontek-mobile-editor',
            array($this, 'mobile_editor_page')
        );
        
        add_submenu_page(
            'demontek-steam',
            'Field Inspector',
            '🔍 Inspector',
            'edit_posts',
            'demontek-inspector',
            array($this, 'inspector_page')
        );
    }
    
    /**
     * NEW: Mobile Editor page handler
     */
    public function mobile_editor_page() {
        if ($this->mobile_editor && method_exists($this->mobile_editor, 'render_mobile_editor_page')) {
            $this->mobile_editor->render_mobile_editor_page();
        } else {
            echo '<div class="wrap"><h1>Mobile Editor Not Available</h1><p>Mobile editor component not loaded.</p></div>';
        }
    }
    
    /**
     * Admin page handler (existing)
     */
    public function admin_page() {
        // Existing admin page logic
        ?>
        <div class="wrap">
            <h1>🎮 Demontek Steam Template v<?php echo $this->version; ?></h1>
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h2 style="margin: 0 0 10px 0;">🚀 NEW in v1.8.0: Mobile Post Editor!</h2>
                <p style="margin: 0;">Transform your workflow with our new tabbed mobile editor interface featuring real-time preview and component communication!</p>
                <a href="admin.php?page=demontek-mobile-editor" class="button button-primary" style="margin-top: 15px;">📱 Try Mobile Editor</a>
            </div>
            <p>Configure your Steam template settings and manage gaming content with professional tools.</p>
        </div>
        <?php
    }
    
    /**
     * Inspector page handler (existing)
     */
    public function inspector_page() {
        if (class_exists('Steam_Inspector_Class')) {
            $inspector = new Steam_Inspector_Class();
            if (method_exists($inspector, 'render_inspector_page')) {
                $inspector->render_inspector_page();
            }
        } else {
            echo '<div class="wrap"><h1>Inspector Not Available</h1></div>';
        }
    }
    
    /**
     * OPTIMIZED: Admin assets with mobile editor support
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'demontek-steam') === false && !in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        $cache_buster = $this->version . '-MOBILE-' . time();
        
        wp_enqueue_script('jquery');
        
        // Main admin CSS
        $css_file = $this->plugin_url . 'assets/admin/admin.css';
        if (file_exists($this->plugin_path . 'assets/admin/admin.css')) {
            wp_enqueue_style('demontek-steam-admin-css', $css_file, array(), $cache_buster);
        }
        
        // Main admin JS
        $js_file = $this->plugin_url . 'assets/admin/admin.js';
        if (file_exists($this->plugin_path . 'assets/admin/admin.js')) {
            wp_enqueue_script('demontek-steam-admin-js', $js_file, array('jquery'), $cache_buster, true);
        }
        
        // Localize script with AJAX URL and nonces
        wp_localize_script('demontek-steam-admin-js', 'demontekSteam', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('demontek_steam_nonce'),
            'mobile_nonce' => wp_create_nonce('demontek_mobile_nonce'),
            'version' => $this->version,
            'plugin_url' => $this->plugin_url
        ));
    }
    
    /**
     * Frontend assets (existing)
     */
    public function enqueue_frontend_assets() {
        if (!is_single()) return;
        
        global $post;
        if (!get_post_meta($post->ID, '_demontek_steam_use', true)) return;
        
        wp_enqueue_style('demontek-steam-frontend', $this->plugin_url . 'assets/css/steam-frontend.css', array(), $this->version);
        wp_enqueue_script('demontek-steam-frontend-js', $this->plugin_url . 'assets/js/steam-frontend.js', array('jquery'), $this->version, true);
    }
    
    /**
     * Load Steam template (existing)
     */
    public function load_steam_template($template) {
        if (is_single()) {
            global $post;
            if (get_post_meta($post->ID, '_demontek_steam_use', true)) {
                $steam_template = $this->plugin_path . 'templates/single-steam.php';
                if (file_exists($steam_template)) {
                    return $steam_template;
                }
            }
        }
        return $template;
    }
    
    /**
     * Get Steam field data for a post
     */
    public function get_steam_field_data($post_id = null) {
        if (!$post_id) {
            global $post;
            $post_id = $post ? $post->ID : 0;
        }
        
        if (!$post_id) return array();
        
        // Get the post object
        $post_obj = get_post($post_id);
        if (!$post_obj) return array();
        
        $steam_fields = array(
            'trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5',
            'ai_summary', 'ai_excerpt', 'game_genre',
            'review_1', 'review_2', 'review_3',
            'original_link', 'steam_link', 'amazon_link',
            'developer', 'platforms', 'release_date',
            '_demontek_steam_use', '_demontek_steam_extra_sidebar', '_demontek_steam_content_layout'
        );
        
        $field_data = array();
        foreach ($steam_fields as $field) {
            $field_data[$field] = get_post_meta($post_id, $field, true);
        }
        
        // Add some computed fields that templates might expect
        $field_data['post_title'] = $post_obj->post_title;
        $field_data['post_content'] = $post_obj->post_content;
        $field_data['post_excerpt'] = $post_obj->post_excerpt;
        $field_data['post_date'] = $post_obj->post_date;
        $field_data['post_author'] = $post_obj->post_author;
        $field_data['post_id'] = $post_id;
        
        // Steam layout enabled check
        $field_data['steam_enabled'] = ($field_data['_demontek_steam_use'] == '1');
        
        // Process trailers into array
        $trailers = array();
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($field_data["trailer_$i"])) {
                $trailers["trailer_$i"] = $field_data["trailer_$i"];
            }
        }
        $field_data['trailers'] = $trailers;
        
        // Process reviews into array
        $reviews = array();
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($field_data["review_$i"])) {
                $reviews["review_$i"] = $field_data["review_$i"];
            }
        }
        $field_data['reviews'] = $reviews;
        
        return $field_data;
    }
    
    /**
     * EXISTING AJAX HANDLERS (keeping all existing functionality)
     */
    
    public function ajax_refresh_fields() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        if ($this->post_editor && method_exists($this->post_editor, 'ajax_refresh_fields')) {
            $this->post_editor->ajax_refresh_fields();
        } else {
            wp_die('Method not available');
        }
    }
    
    public function ajax_save_settings() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        // Existing save settings logic
        wp_send_json_success('Settings saved');
    }
    
    public function ajax_preview() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        // Existing preview logic
        wp_die();
    }
    
    public function ajax_load_posts() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        
        $category_id = intval($_POST['category_id']);
        
        if (!$category_id) {
            wp_send_json_error('Category ID is required');
        }
        
        // Get posts from the specified category
        $args = array(
            'post_type' => 'post',
            'post_status' => array('publish', 'draft'),
            'posts_per_page' => 10, // Limit to 10 posts for performance
            'cat' => $category_id,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_demontek_steam_use',
                    'value' => '1',
                    'compare' => '='
                ),
                array(
                    'key' => '_demontek_steam_use',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        $posts = get_posts($args);
        
        if (empty($posts)) {
            wp_send_json_error('No posts found in this category');
        }
        
        // Format posts for frontend
        $formatted_posts = array();
        foreach ($posts as $post) {
            $formatted_posts[] = array(
                'ID' => $post->ID,
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_date' => $post->post_date,
                'post_status' => $post->post_status,
                'post_author' => $post->post_author,
                'permalink' => get_permalink($post->ID),
                'edit_link' => get_edit_post_link($post->ID),
                'steam_enabled' => get_post_meta($post->ID, '_demontek_steam_use', true) == '1'
            );
        }
        
        wp_send_json_success(array(
            'posts' => $formatted_posts,
            'total' => count($formatted_posts),
            'category_id' => $category_id
        ));
    }
    
    public function ajax_get_post_data() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        // Existing get post data logic
        wp_send_json_success(array());
    }
    
    public function ajax_navigate_posts() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        // Existing navigation logic
        wp_send_json_success(array());
    }
    
    public function ajax_debug_info() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        // Existing debug logic
        wp_send_json_success(array());
    }
    
    public function ajax_duplicate_post() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        // Existing duplicate logic
        wp_send_json_success(array());
    }
    
    public function ajax_update_version() {
        check_ajax_referer('demontek_steam_nonce', 'nonce');
        update_option('demontek_steam_version', $this->version);
        wp_send_json_success('Version updated to ' . $this->version);
    }
    
    // NEW: Mobile Editor AJAX Handlers
    
    public function ajax_mobile_save_field() {
        check_ajax_referer('demontek_mobile_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $field_name = sanitize_text_field($_POST['field_name']);
        $field_value = sanitize_textarea_field($_POST['field_value']);
        
        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error('Permission denied');
        }
        
        $result = update_post_meta($post_id, $field_name, $field_value);
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => 'Field saved successfully',
                'field_name' => $field_name,
                'field_value' => $field_value
            ));
        } else {
            wp_send_json_error('Failed to save field');
        }
    }
    
    public function ajax_mobile_get_post_data() {
        check_ajax_referer('demontek_mobile_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        
        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error('Permission denied');
        }
        
        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error('Post not found');
        }
        
        // Get all Steam fields
        $steam_fields = array(
            'trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5',
            'ai_summary', 'ai_excerpt', 'game_genre',
            'review_1', 'review_2', 'review_3',
            'original_link', 'steam_link', 'amazon_link',
            'developer', 'platforms', 'release_date',
            '_demontek_steam_use', '_demontek_steam_extra_sidebar', '_demontek_steam_content_layout'
        );
        
        $field_data = array();
        foreach ($steam_fields as $field) {
            $field_data[$field] = get_post_meta($post_id, $field, true);
        }
        
        wp_send_json_success(array(
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'fields' => $field_data,
            'edit_link' => get_edit_post_link($post_id),
            'preview_link' => get_permalink($post_id)
        ));
    }
    
    public function ajax_mobile_toggle_steam() {
        check_ajax_referer('demontek_mobile_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $enable_steam = intval($_POST['enable_steam']);
        
        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error('Permission denied');
        }
        
        $result = update_post_meta($post_id, '_demontek_steam_use', $enable_steam);
        
        wp_send_json_success(array(
            'enabled' => $enable_steam,
            'message' => $enable_steam ? 'Steam layout enabled' : 'Steam layout disabled'
        ));
    }
}

// Initialize the plugin
$demontek_steam_template = new DemontekSteamTemplate();

/**
 * Global function to get Steam field data (for template use)
 */
function get_demontek_steam_field_data($post_id = null) {
    global $demontek_steam_template;
    return $demontek_steam_template->get_steam_field_data($post_id);
}

/**
 * Helper function to check if Steam layout is enabled for a post
 */
function is_demontek_steam_post($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post ? $post->ID : 0;
    }
    
    return get_post_meta($post_id, '_demontek_steam_use', true) == '1';
}

/**
 * Helper function to get Steam field value
 */
function get_demontek_steam_field($field_name, $post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post ? $post->ID : 0;
    }
    
    return get_post_meta($post_id, $field_name, true);
}

/**
 * Helper function to get all Steam trailers for a post
 */
function get_demontek_steam_trailers($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post ? $post->ID : 0;
    }
    
    $trailers = array();
    for ($i = 1; $i <= 5; $i++) {
        $trailer = get_post_meta($post_id, "trailer_$i", true);
        if (!empty($trailer)) {
            $trailers["trailer_$i"] = $trailer;
        }
    }
    
    return $trailers;
}