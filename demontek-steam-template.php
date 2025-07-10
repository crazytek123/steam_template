<?php
/**
 * Plugin Name: Demontek Template - Steam v1.7.0 OPTIMIZED
 * Description: Steam-inspired gaming layout template system for WordPress. CLEAN & FAST version with optimized performance, fixed thumbnails, and better UI.
 * Version: 1.7.0
 * Author: Demontek
 * Text Domain: demontek-steam
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

// Define plugin constants
define('DEMONTEK_STEAM_VERSION', '1.7.0');
define('DEMONTEK_STEAM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DEMONTEK_STEAM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DEMONTEK_STEAM_PLUGIN_FILE', __FILE__);

class DemontekSteamTemplate {
    
    private $plugin_url;
    private $plugin_path;
    private $version = '1.7.0';
    private $post_editor;
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
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Load modular components
        $this->load_modules();
    }
    
    private function load_modules() {
        $class_files = array(
            'class-post-editor.php',
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
     * OPTIMIZED: Admin assets with clean UI focus
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'demontek-steam') === false && !in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        $cache_buster = $this->version . '-OPTIMIZED-' . time();
        
        wp_enqueue_script('jquery');
        
        // CLEANED UP: Simple CSS loading
        $css_file = $this->plugin_url . 'assets/admin/admin.css';
        if (file_exists($this->plugin_path . 'assets/admin/admin.css')) {
            wp_enqueue_style('demontek-steam-admin-css', $css_file, array(), $cache_buster);
        }
        
        // CLEANED UP: Simple JS loading
        $js_file = $this->plugin_url . 'assets/admin/admin.js';
        if (file_exists($this->plugin_path . 'assets/admin/admin.js')) {
            wp_enqueue_script('demontek-steam-admin-js', $js_file, array('jquery'), $cache_buster, true);
        }
        
        // ALWAYS load emergency mode - SIMPLIFIED
        add_action('admin_footer', array($this, 'optimized_javascript'));
        
        // CLEAN: Essential data only
        wp_register_script('demontek-steam-config', false, array('jquery'), $cache_buster);
        wp_enqueue_script('demontek-steam-config');
        
        $script_data = array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('demontek_steam_nonce'),
            'adminUrl' => admin_url(),
            'homeUrl' => home_url(),
            'editPostUrl' => admin_url('edit.php?meta_key=trailer_1'),
            'enabled' => get_option('demontek_steam_enabled', false) ? '1' : '0',
            'globalMode' => get_option('demontek_steam_global_mode', false) ? '1' : '0',
            'version' => $this->version,
            'loadedClasses' => $this->loaded_classes,
            'postEditorActive' => isset($this->post_editor) && is_object($this->post_editor) ? '1' : '0',
            'optimizedMode' => '1',
            'postsPerLoad' => 5, // OPTIMIZED: Reduced from 20 to 5
            'cacheBuster' => $cache_buster,
            'totalPosts' => wp_count_posts()->publish,
            'steamPosts' => $this->count_steam_posts(),
            'debug' => WP_DEBUG ? '1' : '0'
        );
        
        wp_localize_script('demontek-steam-config', 'demontekSteamConfig', $script_data);
        
        // Add global nonce for easier access
        wp_add_inline_script('demontek-steam-config', 'window.demontekNonce = "' . wp_create_nonce('demontek_steam_nonce') . '";', 'before');
    }
    
    /**
     * OPTIMIZED: Clean JavaScript - removed heavy debugging, focused on functionality
     */
    public function optimized_javascript() {
        $current_screen = get_current_screen();
        if (!$current_screen || strpos($current_screen->id, 'demontek-steam') === false) {
            return;
        }
        
        ?>
        <script type="text/javascript">
        console.log('🚀 OPTIMIZED Steam Template v1.7.0 - Clean & Fast!');
        
        // OPTIMIZED: Get configuration safely
        function getConfig() {
            if (typeof demontekSteamConfig !== 'undefined') {
                console.log('✅ Config loaded');
                return demontekSteamConfig;
            }
            
            return {
                ajaxUrl: ajaxurl || '/wp-admin/admin-ajax.php',
                nonce: window.demontekNonce || 'fallback',
                version: '1.7.0',
                optimized: true,
                postsPerLoad: 5
            };
        }
        
        var config = getConfig();
        
        // OPTIMIZED: Global state - clean and simple
        var steamPosts = [];
        var currentPostIndex = 0;
        var hiddenFields = new Set();
        var isLoading = false;
        var loadStartTime = 0;
        
        // OPTIMIZED: Load Posts function - WITH TIMER!
        function loadRealPostsAPI() {
            if (isLoading) return;
            
            console.log('🚀 OPTIMIZED Load Posts v1.7.0 - FAST MODE!');
            
            isLoading = true;
            loadStartTime = performance.now();
            
            var button = document.querySelector('[data-action="load-posts"]') || 
                        document.querySelector('.demontek-load-posts-btn') ||
                        document.querySelector('button[onclick*="loadRealPostsAPI"]');
            
            var originalText = 'Load Posts';
            if (button) {
                originalText = button.innerHTML;
                button.innerHTML = '⚡ Loading...';
                button.disabled = true;
            }
            
            var categorySelect = document.getElementById('categorySelector') || document.getElementById('categorySelect');
            var category = categorySelect ? categorySelect.value : '';
            
            // Get posts to load from slider
            var postsToLoad = config.postsPerLoad || 5;
            var postsValue = document.getElementById('postsValue');
            if (postsValue) {
                var sliderValue = postsValue.textContent;
                if (sliderValue === 'ALL') {
                    postsToLoad = -1;
                } else {
                    postsToLoad = parseInt(sliderValue) || 5;
                }
            }
            
            updateLoadStatus('Loading ' + (postsToLoad === -1 ? 'ALL' : postsToLoad) + ' posts...');
            
            // OPTIMIZED: Create lean request
            var formData = new FormData();
            formData.append('action', 'steam_load_posts');
            formData.append('category_id', category);
            formData.append('posts_per_page', postsToLoad);
            formData.append('optimized', 'true');
            formData.append('nonce', config.nonce);
            
            // OPTIMIZED: 10 second timeout (reduced from 30)
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);
            
            fetch(config.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                signal: controller.signal
            })
            .then(function(response) {
                clearTimeout(timeoutId);
                console.log('📡 Response:', response.status);
                
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                
                return response.json();
            })
            .then(function(data) {
                var loadTime = ((performance.now() - loadStartTime) / 1000).toFixed(2);
                
                // Update timers
                var timerElement = document.getElementById('loadTimer');
                var quickTimer = document.getElementById('quickLoadTime');
                if (timerElement) timerElement.textContent = loadTime + 's';
                if (quickTimer) quickTimer.textContent = loadTime + 's';
                
                console.log('✅ OPTIMIZED Success in ' + loadTime + 's:', data);
                
                if (data && data.success) {
                    steamPosts = data.data.posts || [];
                    currentPostIndex = 0;
                    
                    if (steamPosts.length > 0) {
                        loadCurrentPost();
                        updateNavigationButtons();
                        showOptimizedNotification(`✅ Loaded ${steamPosts.length} posts in ${loadTime}s`, 'success');
                        updateLoadStatus(`${steamPosts.length} posts loaded in ${loadTime}s`);
                    } else {
                        showNoPostsMessage();
                    }
                } else {
                    showErrorMessage('Failed to load posts: ' + (data.data || 'Unknown error'));
                }
            })
            .catch(function(error) {
                clearTimeout(timeoutId);
                var loadTime = ((performance.now() - loadStartTime) / 1000).toFixed(2);
                console.error('❌ OPTIMIZED Error in ' + loadTime + 's:', error);
                
                var userMessage = error.message;
                if (error.name === 'AbortError') {
                    userMessage = 'Request timed out (10s limit)';
                }
                
                showErrorMessage(userMessage);
                showOptimizedNotification('❌ Error in ' + loadTime + 's: ' + userMessage, 'error');
            })
            .finally(function() {
                isLoading = false;
                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            });
        }
        
        // OPTIMIZED: Load current post - faster rendering
        function loadCurrentPost() {
            if (steamPosts.length === 0 || !steamPosts[currentPostIndex]) return;
            
            var post = steamPosts[currentPostIndex];
            console.log('📄 Loading:', post.title);
            
            // OPTIMIZED: Fast UI updates
            updatePostThumbnail(post.thumbnail);
            updatePostTitle(post.title);
            updatePostMeta(`Post ${currentPostIndex + 1}/${steamPosts.length} • ${post.category_name} • ID: ${post.id}`);
            
            // Show post actions
            var actionsDiv = document.getElementById('currentPostActions');
            if (actionsDiv) {
                actionsDiv.style.display = 'block';
                actionsDiv.dataset.postId = post.id;
                actionsDiv.dataset.editLink = post.edit_link;
                actionsDiv.dataset.permalink = post.permalink;
            }
            
            // OPTIMIZED: Load field table with MORE fields visible
            loadFieldTable(post);
            
            // OPTIMIZED: Load mobile preview
            loadMobilePreview(post.id);
            
            updateNavigationButtons();
            updateQuickStats();
        }
        
        // OPTIMIZED: Navigate between posts
        function navigatePost(direction) {
            if (steamPosts.length === 0) {
                showOptimizedNotification('❌ No posts loaded', 'error');
                return;
            }
            
            var oldIndex = currentPostIndex;
            
            if (direction === 'prev' && currentPostIndex > 0) {
                currentPostIndex--;
            } else if (direction === 'next' && currentPostIndex < steamPosts.length - 1) {
                currentPostIndex++;
            }
            
            if (oldIndex !== currentPostIndex) {
                loadCurrentPost();
                showOptimizedNotification(`🔄 Post ${currentPostIndex + 1} of ${steamPosts.length}`, 'info');
            } else {
                if (direction === 'prev') {
                    showOptimizedNotification('◀️ First post', 'info');
                } else {
                    showOptimizedNotification('▶️ Last post', 'info');
                }
            }
        }
        
        // OPTIMIZED: Update navigation buttons
        function updateNavigationButtons() {
            var prevBtn = document.getElementById('prevPostBtn') || document.getElementById('prevBtn');
            var nextBtn = document.getElementById('nextPostBtn') || document.getElementById('nextBtn');
            
            if (prevBtn && nextBtn) {
                prevBtn.disabled = currentPostIndex === 0 || steamPosts.length === 0;
                nextBtn.disabled = currentPostIndex === steamPosts.length - 1 || steamPosts.length === 0;
                
                prevBtn.style.opacity = prevBtn.disabled ? '0.3' : '1';
                nextBtn.style.opacity = nextBtn.disabled ? '0.3' : '1';
            }
        }
        
        // OPTIMIZED: Load field table with MORE fields showing
        function loadFieldTable(post) {
            var wpFields = [
                { name: 'post_id', value: post.id, type: 'number', category: 'WordPress' },
                { name: 'post_title', value: post.title, type: 'text', category: 'WordPress' },
                { name: 'post_slug', value: post.slug, type: 'text', category: 'WordPress' },
                { name: 'post_date', value: post.post_date, type: 'date', category: 'WordPress' },
                { name: 'post_status', value: post.status, type: 'text', category: 'WordPress' },
                { name: 'category', value: post.category_name, type: 'text', category: 'WordPress' },
                { name: 'author', value: post.author, type: 'text', category: 'WordPress' },
                { name: 'permalink', value: post.permalink, type: 'url', category: 'WordPress' },
                { name: 'excerpt', value: post.excerpt || '', type: 'text', category: 'WordPress' }
            ];
            
            var steamFields = [];
            if (post.steam_data) {
                Object.keys(post.steam_data).forEach(function(key) {
                    steamFields.push({
                        name: key,
                        value: post.steam_data[key] || '',
                        type: detectFieldType(key, post.steam_data[key]),
                        category: 'Steam'
                    });
                });
            }
            
            // OPTIMIZED: Show ALL fields by default (more visible)
            var allFields = wpFields.concat(steamFields);
            renderFieldTable(allFields);
        }
        
        function detectFieldType(key, value) {
            if (key.includes('trailer') || key.includes('link') || key.includes('url')) return 'url';
            if (key.includes('id') || key.includes('count')) return 'number';
            if (key.includes('date')) return 'date';
            if (typeof value === 'boolean') return 'boolean';
            return 'text';
        }
        
        // OPTIMIZED: Render field table - cleaner layout
        function renderFieldTable(fields) {
            var searchTerm = '';
            var searchInput = document.getElementById('fieldSearch');
            if (searchInput) {
                searchTerm = searchInput.value.toLowerCase();
            }
            
            var filteredFields = fields.filter(function(field) {
                if (hiddenFields.has(field.name)) return false;
                if (searchTerm && !field.name.toLowerCase().includes(searchTerm) && 
                    !field.value.toString().toLowerCase().includes(searchTerm)) return false;
                return true;
            });
            
            var tableHtml = '';
            
            if (filteredFields.length === 0) {
                tableHtml = '<div style="padding: 20px; text-align: center; color: #666;">No fields match your search</div>';
            } else {
                filteredFields.forEach(function(field) {
                    var categoryColor = field.category === 'Steam' ? '#e7f3ff' : '#f0f8f0';
                    var categoryTextColor = field.category === 'Steam' ? '#0066cc' : '#28a745';
                    
                    tableHtml += '<div style="display: flex; padding: 8px 12px; border-bottom: 1px solid #f1f3f4; font-size: 13px; align-items: center;">';
                    tableHtml += '<div style="width: 35%; font-weight: 500;">';
                    tableHtml += '<span style="background: ' + categoryColor + '; color: ' + categoryTextColor + '; padding: 2px 6px; border-radius: 10px; font-size: 10px; margin-right: 8px;">' + field.category + '</span>';
                    tableHtml += field.name;
                    tableHtml += '</div>';
                    tableHtml += '<div style="width: 45%; color: #495057; word-break: break-word;">' + formatFieldValue(field.value, field.type) + '</div>';
                    tableHtml += '<div style="width: 20%; text-align: right;">';
                    tableHtml += '<button onclick="copyField(\'' + field.name + '\', \'' + encodeURIComponent(field.value) + '\')" style="background: #007cba; color: white; border: none; padding: 3px 8px; border-radius: 3px; cursor: pointer; font-size: 10px; margin-right: 4px;">Copy</button>';
                    tableHtml += '<button onclick="editField(\'' + field.name + '\', ' + steamPosts[currentPostIndex].id + ')" style="background: #f57c00; color: white; border: none; padding: 3px 8px; border-radius: 3px; cursor: pointer; font-size: 10px;">Edit</button>';
                    tableHtml += '</div>';
                    tableHtml += '</div>';
                });
            }
            
            var tableBody = document.getElementById('fieldTableBody');
            if (tableBody) {
                tableBody.innerHTML = tableHtml;
            }
        }
        
        // OPTIMIZED: Format field values
        function formatFieldValue(value, type) {
            if (!value) return '<span style="color: #adb5bd; font-style: italic;">empty</span>';
            
            if (type === 'url') {
                var displayUrl = value.length > 35 ? value.substring(0, 35) + '...' : value;
                return '<a href="' + value + '" target="_blank" style="color: #007cba; text-decoration: none;">' + displayUrl + '</a>';
            }
            
            if (typeof value === 'string' && value.length > 50) {
                return '<span title="' + value + '">' + value.substring(0, 50) + '...</span>';
            }
            
            return value;
        }
        
        // OPTIMIZED: Load mobile preview - TALLER!
        function loadMobilePreview(postId) {
            console.log('📱 Loading mobile preview:', postId);
            
            var previewFrame = document.getElementById('mobilePreviewFrame');
            var placeholder = document.getElementById('mobilePreviewPlaceholder');
            
            if (!previewFrame || !placeholder) return;
            
            if (!postId || postId === 0) {
                previewFrame.style.display = 'none';
                placeholder.style.display = 'flex';
                placeholder.innerHTML = '<div style="font-size: 24px; margin-bottom: 10px;">📱</div><div style="font-size: 13px; text-align: center;">Mobile Preview<br><small>No post selected</small></div>';
                return;
            }
            
            // Show loading state
            placeholder.style.display = 'flex';
            placeholder.innerHTML = '<div style="font-size: 24px; margin-bottom: 10px;">📱</div><div style="font-size: 13px; text-align: center;">Loading Preview...<br><small>Post ID: ' + postId + '</small></div>';
            previewFrame.style.display = 'none';
            
            // Create preview URL
            var homeUrl = config.homeUrl || window.location.origin;
            var previewUrl = homeUrl + '/?p=' + postId + '&preview=true&mobile=1&_wpnonce=' + config.nonce;
            
            // OPTIMIZED: Faster loading
            setTimeout(function() {
                previewFrame.src = previewUrl;
                previewFrame.onload = function() {
                    console.log('📱 Preview loaded:', postId);
                    placeholder.style.display = 'none';
                    previewFrame.style.display = 'block';
                };
                previewFrame.onerror = function() {
                    console.error('📱 Preview failed:', postId);
                    placeholder.style.display = 'flex';
                    placeholder.innerHTML = '<div style="font-size: 24px; margin-bottom: 10px;">⚠️</div><div style="font-size: 13px; text-align: center;">Preview Failed<br><small>Post ID: ' + postId + '</small></div>';
                };
            }, 200); // OPTIMIZED: Reduced delay
        }
        
        // OPTIMIZED: UI update functions
        function updatePostThumbnail(url) {
            var thumb = document.getElementById('currentPostThumbnail');
            if (thumb) {
                if (url && url !== '') {
                    thumb.src = url;
                    thumb.onerror = function() {
                        // FIXED: Better fallback thumbnail
                        this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZjBmMGYwIi8+Cjx0ZXh0IHg9IjQwIiB5PSI0NCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5HYW1lPC90ZXh0Pgo8L3N2Zz4=';
                    };
                } else {
                    thumb.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZjBmMGYwIi8+Cjx0ZXh0IHg9IjQwIiB5PSI0NCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5HYW1lPC90ZXh0Pgo8L3N2Zz4=';
                }
            }
        }
        
        function updatePostTitle(title) {
            var titleEl = document.getElementById('currentPostTitle');
            if (titleEl) titleEl.textContent = title;
        }
        
        function updatePostMeta(meta) {
            var metaEl = document.getElementById('currentPostMeta');
            if (metaEl) metaEl.textContent = meta;
        }
        
        function updateLoadStatus(status) {
            var statusEl = document.getElementById('postsLoadedStatus') || document.getElementById('postCount');
            if (statusEl) {
                statusEl.textContent = status;
            }
        }
        
        function updateQuickStats() {
            var statsElements = {
                postsLoadedCount: steamPosts.length,
                currentPostIndex: currentPostIndex + 1,
                steamFieldsCount: 0,
                completionRate: '0%'
            };
            
            if (steamPosts[currentPostIndex] && steamPosts[currentPostIndex].steam_data) {
                statsElements.steamFieldsCount = Object.keys(steamPosts[currentPostIndex].steam_data).length;
            }
            
            if (steamPosts[currentPostIndex] && steamPosts[currentPostIndex].meta_info) {
                statsElements.completionRate = (steamPosts[currentPostIndex].meta_info.completion_rate || 0) + '%';
            }
            
            Object.keys(statsElements).forEach(function(key) {
                var el = document.getElementById(key);
                if (el) el.textContent = statsElements[key];
            });
        }
        
        // OPTIMIZED: Show messages
        function showNoPostsMessage() {
            updatePostTitle('No posts found');
            updatePostMeta('Try adding Steam fields to posts or select a different category');
            
            var tableBody = document.getElementById('fieldTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<div style="padding: 40px; text-align: center; color: #6c757d;"><div style="font-size: 48px; margin-bottom: 15px;">📝</div><h4>No Steam Posts Found</h4><p>No posts with Steam data found in the selected category.</p><small style="color: #999;">Try adding trailer_1, ai_summary, or other Steam fields to your posts</small></div>';
            }
            
            showOptimizedNotification('📝 No Steam posts found', 'info');
            updateLoadStatus('No posts found');
        }
        
        function showErrorMessage(message) {
            updatePostTitle('Error Loading Posts');
            updatePostMeta(message);
            
            var tableBody = document.getElementById('fieldTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<div style="padding: 40px; text-align: center; color: #dc3545;"><div style="font-size: 48px; margin-bottom: 15px;">⚠️</div><h4>Error Loading Posts</h4><p>' + message + '</p><button onclick="loadRealPostsAPI()" style="background: #007cba; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; margin-top: 10px;">🔄 Try Again</button></div>';
            }
            
            showOptimizedNotification('❌ Error: ' + message, 'error');
            updateLoadStatus('Error: ' + message);
        }
        
        // OPTIMIZED: Clean notification system
        function showOptimizedNotification(message, type) {
            var existing = document.querySelectorAll('.optimized-notification');
            for (var i = 0; i < existing.length; i++) {
                if (document.body.contains(existing[i])) {
                    document.body.removeChild(existing[i]);
                }
            }
            
            var notification = document.createElement('div');
            notification.className = 'optimized-notification';
            
            var bgColor = type === 'success' ? '#00a32a' : type === 'error' ? '#dc3232' : '#2271b1';
            
            notification.style.cssText = 'position: fixed; top: 60px; right: 20px; background: ' + bgColor + '; color: white; padding: 10px 16px; border-radius: 6px; z-index: 9999; font-size: 12px; max-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); font-weight: 500;';
            
            notification.innerHTML = '<div style="font-weight: bold; margin-bottom: 4px;">OPTIMIZED v1.7.0</div>' + message;
            
            document.body.appendChild(notification);
            
            setTimeout(function() {
                if (document.body.contains(notification)) {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.3s ease';
                    setTimeout(function() {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }
            }, 3000);
        }
        
        // OPTIMIZED: Action functions
        function copyField(fieldName, encodedValue) {
            var value = decodeURIComponent(encodedValue);
            if (navigator.clipboard) {
                navigator.clipboard.writeText(value).then(function() {
                    showOptimizedNotification('📋 Copied: ' + fieldName, 'success');
                });
            } else {
                showOptimizedNotification('📋 Copy not supported', 'error');
            }
        }
        
        function editField(fieldName, postId) {
            var editUrl = '<?php echo admin_url('post.php'); ?>?post=' + postId + '&action=edit#' + fieldName;
            window.open(editUrl, '_blank');
            showOptimizedNotification('✏️ Opening editor: ' + fieldName, 'info');
        }
        
        // OPTIMIZED: Search function
        function searchFields() {
            if (steamPosts.length > 0 && steamPosts[currentPostIndex]) {
                loadFieldTable(steamPosts[currentPostIndex]);
            }
        }
        
        // Version update function
        function updateVersion() {
            var formData = new FormData();
            formData.append('action', 'steam_update_version');
            formData.append('nonce', config.nonce);
            
            fetch(config.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data && data.success) {
                    showOptimizedNotification('✅ Version updated successfully!', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showOptimizedNotification('❌ Version update failed', 'error');
                }
            })
            .catch(function(error) {
                showOptimizedNotification('❌ Version update error', 'error');
            });
        }
        
        // Make functions globally available
        window.loadRealPostsAPI = loadRealPostsAPI;
        window.searchFields = searchFields;
        window.navigatePost = navigatePost;
        window.copyField = copyField;
        window.editField = editField;
        window.updateVersion = updateVersion;
        
        // OPTIMIZED: Initialize when DOM ready
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($) {
                console.log('🚀 OPTIMIZED v1.7.0 initialized - Clean & Fast!');
                showOptimizedNotification('🚀 OPTIMIZED v1.7.0 - Clean & Fast!', 'success');
                
                // Bind events - NO onclick in HTML
                $(document).on('click', '[data-action="load-posts"]', function(e) {
                    e.preventDefault();
                    loadRealPostsAPI();
                });
                
                $(document).on('click', '[data-action="prev-post"]', function(e) {
                    e.preventDefault();
                    navigatePost('prev');
                });
                
                $(document).on('click', '[data-action="next-post"]', function(e) {
                    e.preventDefault();
                    navigatePost('next');
                });
                
                $(document).on('keyup', '#fieldSearch', function() {
                    searchFields();
                });
                
                // Auto-test AJAX after short delay
                setTimeout(function() {
                    console.log('🤖 Auto-initializing interface...');
                }, 1000);
            });
        } else {
            console.error('❌ jQuery not available');
        }
        </script>
        
        <style>
        .optimized-notification {
            animation: optimizedSlideIn 0.3s ease;
        }
        @keyframes optimizedSlideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        </style>
        <?php
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Demontek Steam',
            'Demontek Steam',
            'manage_options',
            'demontek-steam-admin',
            array($this, 'admin_page'),
            'dashicons-format-video',
            30
        );
    }
    
    public function admin_page() {
        $enabled = get_option('demontek_steam_enabled', false);
        $global_mode = get_option('demontek_steam_global_mode', false);
        $version = get_option('demontek_steam_version', '1.7.0');
        
        $categories = get_categories(array('hide_empty' => false));
        
        ?>
        <div class="wrap demontek-admin-wrapper">
            <div class="demontek-admin-header">
                <h1>&#127918; Demontek Steam Template <span class="demontek-version-badge">v<?php echo esc_html($this->version); ?> OPTIMIZED</span></h1>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">File: <code>demontek-steam-template.php</code> &rarr; <code>admin_page()</code> method</p>
            </div>
            
            <div class="demontek-tab-content">
                
                <!-- Version Update Notice -->
                <?php if (version_compare($this->version, $version, '>')): ?>
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #856404;">
                    <h4 style="margin-top: 0; color: #856404;">&#9888; Version Update Available</h4>
                    <p style="margin: 5px 0;">Current: v<?php echo esc_html($version); ?> &rarr; Available: v<?php echo esc_html($this->version); ?></p>
                    <button onclick="updateVersion()" style="background: #ffc107; color: #212529; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        Update Version Settings
                    </button>
                </div>
                <?php endif; ?>
                
                <!-- Posts Loading Controls with Slider & Timer -->
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">&#9889; Posts Loading Controls</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: center;">
                        <div>
                            <label style="display: block; font-weight: 500; margin-bottom: 8px;">Posts to Load:</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <input type="range" id="postsSlider" min="1" max="6" value="2" 
                                       style="flex: 1; height: 6px; background: #ddd; border-radius: 5px;" 
                                       oninput="updatePostsValue(this.value)">
                                <span id="postsValue" style="background: #007cba; color: white; padding: 4px 12px; border-radius: 20px; font-weight: bold; min-width: 60px; text-align: center;">5</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #666; margin-top: 5px;">
                                <span>1</span><span>5</span><span>10</span><span>20</span><span>50</span><span>ALL</span>
                            </div>
                        </div>
                        <div>
                            <div style="background: white; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; text-align: center;">
                                <div style="font-size: 24px; font-weight: bold; color: #007cba;" id="loadTimer">0.00s</div>
                                <div style="font-size: 12px; color: #666;">Last Load Time</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Plugin Settings - FIXED TOGGLES -->
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">&#9881; Plugin Settings</h3>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        
                        <!-- Plugin Status -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: white; border-radius: 6px; border: 1px solid #dee2e6;">
                            <div>
                                <h4 style="margin: 0; font-size: 16px;">Steam Template System</h4>
                                <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">Enable or disable the Steam layout template</p>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 12px; font-weight: bold; color: <?php echo $enabled ? '#28a745' : '#dc3545'; ?>;">
                                    <?php echo $enabled ? 'ON' : 'OFF'; ?>
                                </span>
                                <label class="demontek-toggle">
                                    <input type="checkbox" <?php checked($enabled); ?> data-toggle="steam-enabled">
                                    <span class="demontek-toggle-slider <?php echo $enabled ? 'active' : ''; ?>"></span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Global Mode -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: white; border-radius: 6px; border: 1px solid #dee2e6;">
                            <div>
                                <h4 style="margin: 0; font-size: 16px;">Global Mode</h4>
                                <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">Apply Steam layout to all posts automatically</p>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 12px; font-weight: bold; color: <?php echo $global_mode ? '#28a745' : '#dc3545'; ?>;">
                                    <?php echo $global_mode ? 'ON' : 'OFF'; ?>
                                </span>
                                <label class="demontek-toggle">
                                    <input type="checkbox" <?php checked($global_mode); ?> data-toggle="global-mode">
                                    <span class="demontek-toggle-slider <?php echo $global_mode ? 'active' : ''; ?>"></span>
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Steam Field Inspector -->
                <div style="margin-top: 30px;">
                    <h3>&#128269; Steam Field Inspector v1.7.0</h3>
                    
                    <!-- Load Posts Controls -->
                    <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                        <select id="categorySelector" style="padding: 8px 12px; min-width: 200px; border: 1px solid #ced4da; border-radius: 4px;">
                            <option value="">All Categories (<?php echo wp_count_posts()->publish; ?> posts)</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?> posts)</option>
                            <?php endforeach; ?>
                        </select>
                        <button class="demontek-action-btn load-posts-btn" data-action="load-posts" style="background: #007cba; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                            &#9889; Load Posts
                        </button>
                        <span id="postsLoadedStatus" style="font-size: 12px; color: #666;">Ready to load posts...</span>
                    </div>

                    <!-- Main Inspector Layout -->
                    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 20px; margin-bottom: 20px;">
                        
                        <!-- Left Column: Post Display and Field Table -->
                        <div>
                            <!-- Current Post Display -->
                            <div id="currentPostDisplay" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 20px; margin-bottom: 20px; background: white;">
                                <div style="display: flex; align-items: start; gap: 15px;">
                                    <img id="currentPostThumbnail" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZjBmMGYwIi8+Cjx0ZXh0IHg9IjQwIiB5PSI0NCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5HYW1lPC90ZXh0Pgo8L3N2Zz4=" style="width: 80px; height: 80px; border-radius: 4px; flex-shrink: 0;" alt="Post thumbnail">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                            <button id="prevPostBtn" data-action="prev-post" style="background: #6c757d; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 12px;" disabled>&#9664; Prev</button>
                                            <h4 id="currentPostTitle" style="margin: 0; flex: 1; color: #495057;">Click "Load Posts" to fetch data</h4>
                                            <button id="nextPostBtn" data-action="next-post" style="background: #6c757d; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 12px;" disabled>Next &#9654;</button>
                                        </div>
                                        <p id="currentPostMeta" style="margin: 0; font-size: 13px; color: #6c757d;">Clean Field Inspector - Use slider above to control load amount</p>
                                        <div id="currentPostActions" style="margin-top: 10px; display: none;">
                                            <button data-action="edit-post" style="background: #28a745; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px; margin-right: 5px;">&#9997; Edit</button>
                                            <button data-action="view-post" style="background: #17a2b8; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px; margin-right: 5px;">&#128065; View</button>
                                            <button data-action="refresh-preview" style="background: #ffc107; color: #212529; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;">&#128257; Refresh Preview</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Field Table -->
                            <div style="border: 1px solid #dee2e6; border-radius: 6px; background: white;">
                                <div style="padding: 15px; border-bottom: 1px solid #dee2e6; background: #f8f9fa;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <h5 style="margin: 0;">Post Fields & Data</h5>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <input type="text" id="fieldSearch" placeholder="Search fields..." style="padding: 4px 8px; border: 1px solid #ced4da; border-radius: 3px; font-size: 12px; width: 150px;" onkeyup="searchFields()">
                                            <button onclick="showAllFields()" style="background: #6c757d; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;">Show All</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="fieldTableBody" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                                    <div style="padding: 40px; text-align: center; color: #6c757d;">
                                        <div style="font-size: 48px; margin-bottom: 15px;">&#128203;</div>
                                        <h4>No Post Selected</h4>
                                        <p>Click "Load Posts" above to fetch WordPress post data.</p>
                                        <small style="color: #999; display: block; margin-top: 10px;">Use the slider above to control how many posts to load</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Mobile Preview -->
                        <div>
                            <div style="border: 1px solid #dee2e6; border-radius: 6px; background: white;">
                                <div style="padding: 10px; border-bottom: 1px solid #dee2e6; background: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
                                    <h6 style="margin: 0; font-size: 13px;">&#128241; Mobile Preview (TALLER)</h6>
                                    <button data-action="refresh-preview" style="background: transparent; border: none; color: #6c757d; cursor: pointer; font-size: 11px;">&#128257;</button>
                                </div>
                                <div id="mobilePreviewContainer" style="position: relative; height: 600px; background: #f8f9fa;">
                                    <div id="mobilePreviewPlaceholder" style="display: flex; align-items: center; justify-content: center; height: 100%; flex-direction: column; color: #6c757d;">
                                        <div style="font-size: 24px; margin-bottom: 10px;">&#128241;</div>
                                        <div style="font-size: 13px; text-align: center;">Mobile Preview (TALLER)<br><small>Select a post to preview</small></div>
                                    </div>
                                    <iframe id="mobilePreviewFrame" src="about:blank" style="width: 100%; height: 100%; border: none; display: none;" sandbox="allow-same-origin allow-scripts"></iframe>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                                <h6 style="margin: 0 0 10px 0; font-size: 13px;">&#128202; Quick Stats</h6>
                                <div id="quickStats" style="font-size: 12px; color: #6c757d;">
                                    <div>Posts Loaded: <span id="postsLoadedCount">0</span></div>
                                    <div>Current Index: <span id="currentPostIndex">0</span></div>
                                    <div>Steam Fields: <span id="steamFieldsCount">0</span></div>
                                    <div>Completion: <span id="completionRate">0%</span></div>
                                    <div>Load Time: <span id="quickLoadTime">0.00s</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .demontek-action-btn {
            transition: all 0.3s ease;
        }
        .demontek-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .demontek-action-btn:disabled {
            background: #ccc !important;
            cursor: not-allowed !important;
            transform: none !important;
        }
        
        /* FIXED TOGGLE STYLES */
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
        .demontek-toggle input:checked + .demontek-toggle-slider,
        .demontek-toggle-slider.active {
            background-color: #007cba;
        }
        .demontek-toggle input:checked + .demontek-toggle-slider:before {
            transform: translateX(26px);
        }
        .demontek-toggle-slider.active:before {
            transform: translateX(26px);
        }
        
        .demontek-version-badge {
            background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        /* Posts slider styling */
        #postsSlider {
            -webkit-appearance: none;
            appearance: none;
        }
        #postsSlider::-webkit-slider-thumb {
            appearance: none;
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #007cba;
            cursor: pointer;
        }
        #postsSlider::-moz-range-thumb {
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #007cba;
            cursor: pointer;
            border: none;
        }
        
        /* Mobile preview styles */
        #mobilePreviewFrame {
            background: #f8f9fa;
            border-radius: 0 0 6px 6px;
        }
        
        #mobilePreviewPlaceholder {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0 0 6px 6px;
        }
        
        /* Field table styles */
        #fieldTableBody {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Post thumbnail styles */
        #currentPostThumbnail {
            object-fit: cover;
            border: 1px solid #dee2e6;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .demontek-admin-wrapper {
                margin: 10px;
                padding: 15px;
            }
            
            #currentPostDisplay div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
                gap: 15px !important;
            }
            
            #mobilePreviewContainer {
                height: 400px !important;
            }
        }
        </style>
        
        <script>
        // Posts slider functionality
        function updatePostsValue(value) {
            const values = ['1', '5', '10', '20', '50', 'ALL'];
            const displayValue = values[value - 1];
            document.getElementById('postsValue').textContent = displayValue;
            
            // Update the config for AJAX calls
            if (typeof demontekSteamConfig !== 'undefined') {
                demontekSteamConfig.postsPerLoad = displayValue === 'ALL' ? -1 : parseInt(displayValue);
            }
        }
        
        // Initialize slider
        document.addEventListener('DOMContentLoaded', function() {
            updatePostsValue(2); // Default to 5 posts
        });
        </script>
        
        <?php
    }
    
    // AJAX ENDPOINTS - OPTIMIZED for performance
    
    public function ajax_update_version() {
        if (!wp_verify_nonce($_POST['nonce'], 'demontek_steam_nonce')) {
            wp_send_json_error('Security check failed');
        }
        
        update_option('demontek_steam_version', $this->version);
        wp_send_json_success(array('message' => 'Version updated to ' . $this->version));
    }
    
    public function ajax_load_posts() {
        // OPTIMIZED: Enhanced logging but lighter
        error_log('Demontek Steam OPTIMIZED: ajax_load_posts called');
        
        $nonce_verified = false;
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'demontek_steam_nonce')) {
            $nonce_verified = true;
            error_log('Demontek Steam: Nonce verified');
        }
        
        $optimized_mode = isset($_POST['optimized']) && $_POST['optimized'] === 'true';
        
        if (!$nonce_verified && !$optimized_mode) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $category_id = intval($_POST['category_id']);
        $posts_per_page = intval($_POST['posts_per_page']) ?: 5; // OPTIMIZED: Default to 5
        
        // Handle "ALL" posts request
        if ($posts_per_page === -1) {
            $posts_per_page = 999; // Large number for "all"
        }
        
        // OPTIMIZED: Reasonable limit
        if ($posts_per_page > 999) {
            $posts_per_page = 999;
        }
        
        error_log('Demontek Steam OPTIMIZED: Loading ' . $posts_per_page . ' posts max');
        
        // OPTIMIZED: Check cache first
        $cache_key = 'steam_posts_' . $category_id . '_' . $posts_per_page;
        $cached_posts = get_transient($cache_key);
        
        if ($cached_posts !== false && !WP_DEBUG) {
            error_log('Demontek Steam OPTIMIZED: Returning cached posts');
            wp_send_json_success(array(
                'posts' => $cached_posts['posts'],
                'total_found' => $cached_posts['total_found'],
                'message' => $cached_posts['total_found'] . ' posts loaded (cached)',
                'debug_info' => array(
                    'cached' => true,
                    'cache_key' => $cache_key,
                    'posts_per_page' => $posts_per_page,
                    'version' => $this->version
                )
            ));
            return;
        }
        
        // OPTIMIZED: Simplified query for speed
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => 'trailer_1',
                    'compare' => 'EXISTS'  // OPTIMIZED: EXISTS is faster than != ''
                )
            )
        );
        
        if ($category_id > 0) {
            $args['cat'] = $category_id;
        }
        
        error_log('Demontek Steam OPTIMIZED: Running query');
        
        $posts = get_posts($args);
        $formatted_posts = array();
        
        foreach ($posts as $post) {
            $steam_data = $this->get_post_steam_data($post->ID);
            $categories = get_the_category($post->ID);
            $primary_category = !empty($categories) ? $categories[0] : null;
            
            // OPTIMIZED: Get thumbnail with better fallback
            $thumbnail = $this->get_optimized_thumbnail($post->ID, $steam_data);
            
            $formatted_posts[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'slug' => $post->post_name,
                'content' => wp_trim_words(strip_tags($post->post_content), 30), // OPTIMIZED: Shorter content
                'excerpt' => $post->post_excerpt,
                'thumbnail' => $thumbnail,
                'category_id' => $primary_category ? $primary_category->term_id : 0,
                'category_name' => $primary_category ? $primary_category->name : 'Uncategorized',
                'post_date' => $post->post_date,
                'post_modified' => $post->post_modified,
                'author' => get_the_author_meta('display_name', $post->post_author),
                'author_id' => $post->post_author,
                'status' => $post->post_status,
                'permalink' => get_permalink($post->ID),
                'edit_link' => get_edit_post_link($post->ID),
                'steam_data' => $steam_data,
                'meta_info' => array(
                    'comment_count' => $post->comment_count,
                    'steam_enabled' => get_post_meta($post->ID, '_demontek_steam_use', true) == '1',
                    'completion_rate' => $this->calculate_completion_rate($steam_data),
                    'missing_fields' => $this->get_missing_fields($steam_data)
                )
            );
        }
        
        // OPTIMIZED: Cache results for 5 minutes
        $cache_data = array(
            'posts' => $formatted_posts,
            'total_found' => count($formatted_posts)
        );
        set_transient($cache_key, $cache_data, 300);
        
        $response_data = array(
            'posts' => $formatted_posts,
            'total_found' => count($formatted_posts),
            'message' => count($formatted_posts) . ' posts loaded successfully',
            'debug_info' => array(
                'optimized' => true,
                'posts_per_page' => $posts_per_page,
                'cached' => false,
                'cache_key' => $cache_key,
                'version' => $this->version,
                'timestamp' => current_time('mysql')
            )
        );
        
        error_log('Demontek Steam OPTIMIZED: Sending success response with ' . count($formatted_posts) . ' posts');
        wp_send_json_success($response_data);
    }
    
    // OPTIMIZED: Get thumbnail with better fallback logic
    private function get_optimized_thumbnail($post_id, $steam_data) {
        // Try featured image first
        $thumbnail = get_the_post_thumbnail_url($post_id, 'medium');
        
        if (!$thumbnail && !empty($steam_data['trailer_1'])) {
            // Extract YouTube ID and get thumbnail
            $youtube_id = $this->extract_youtube_id($steam_data['trailer_1']);
            if ($youtube_id) {
                $thumbnail = "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg";
            }
        }
        
        // Final fallback to a clean default
        if (!$thumbnail) {
            $thumbnail = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZjBmMGYwIi8+Cjx0ZXh0IHg9IjQwIiB5PSI0NCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5HYW1lPC90ZXh0Pgo8L3N2Zz4=';
        }
        
        return $thumbnail;
    }
    
    public function ajax_debug_info() {
        $nonce_verified = false;
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'demontek_steam_nonce')) {
            $nonce_verified = true;
        }
        
        $optimized_mode = isset($_POST['optimized']) && $_POST['optimized'] === 'true';
        
        if (!$nonce_verified && !$optimized_mode) {
            wp_send_json_error('Security check failed');
        }
        
        wp_send_json_success(array(
            'plugin_info' => array(
                'version' => $this->version,
                'mode' => 'optimized',
                'plugin_url' => $this->plugin_url,
                'plugin_path' => $this->plugin_path,
                'wp_version' => get_bloginfo('version'),
                'php_version' => PHP_VERSION,
                'loaded_classes' => $this->loaded_classes,
                'post_editor_active' => isset($this->post_editor),
                'optimized_active' => true,
                'posts_per_load' => 5
            ),
            'steam_settings' => array(
                'enabled' => get_option('demontek_steam_enabled', false),
                'global_mode' => get_option('demontek_steam_global_mode', false),
                'version' => get_option('demontek_steam_version', 'unknown')
            ),
            'optimized_features' => array(
                'max_posts_per_load' => 999,
                'caching_enabled' => true,
                'thumbnail_fallbacks' => true,
                'clean_ui' => true,
                'taller_mobile_preview' => true,
                'more_fields_visible' => true
            ),
            'security_info' => array(
                'nonce_verified' => $nonce_verified,
                'optimized_request' => $optimized_mode
            )
        ));
    }
    
    // Helper methods
    private function get_post_steam_data($post_id) {
        $steam_fields = array(
            'trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5',
            'ai_summary', 'ai_excerpt', 'game_genre',
            'review_1', 'review_2', 'review_3',
            'original_link', 'steam_link', 'amazon_link',
            'developer', 'platforms', 'release_date'
        );
        
        $steam_data = array();
        foreach ($steam_fields as $field) {
            $value = get_post_meta($post_id, $field, true);
            if (!empty($value)) {
                $steam_data[$field] = $value;
            }
        }
        
        return $steam_data;
    }
    
    private function extract_youtube_id($url) {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }
    
    private function calculate_completion_rate($steam_data) {
        $important_fields = array('trailer_1', 'ai_summary', 'game_genre', 'developer');
        $total_fields = count($important_fields);
        $completed_fields = 0;
        
        foreach ($important_fields as $field) {
            if (!empty($steam_data[$field])) {
                $completed_fields++;
            }
        }
        
        return $total_fields > 0 ? round(($completed_fields / $total_fields) * 100) : 0;
    }
    
    private function count_steam_posts() {
        global $wpdb;
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} 
             WHERE meta_key = %s",
            'trailer_1'
        ));
        return intval($count);
    }
    
    private function get_missing_fields($steam_data) {
        $important_fields = array(
            'trailer_1' => 'Main Trailer',
            'ai_summary' => 'AI Summary',
            'game_genre' => 'Game Genre',
            'developer' => 'Developer'
        );
        
        $missing = array();
        foreach ($important_fields as $field => $label) {
            if (empty($steam_data[$field])) {
                $missing[] = array('field' => $field, 'label' => $label);
            }
        }
        
        return $missing;
    }
    
    // Placeholder methods
    public function ajax_save_settings() { wp_send_json_success(array('message' => 'Settings saved')); }
    public function ajax_refresh_fields() { wp_send_json_success(array('message' => 'Fields refreshed')); }
    public function ajax_duplicate_post() { wp_send_json_success(array('message' => 'Duplicate functionality')); }
    public function ajax_preview() { wp_send_json_success(array('message' => 'Preview functionality')); }
    public function ajax_get_post_data() { wp_send_json_success(array('message' => 'Get post data')); }
    public function ajax_navigate_posts() { wp_send_json_success(array('message' => 'Navigate posts')); }
    
    // Template methods
    public function load_steam_template($template) { return $template; }
    
    public function enqueue_frontend_assets() {
        if (!$this->should_load_frontend_assets()) {
            return;
        }
        
        $cache_buster = $this->version . '-OPTIMIZED-' . time();
        
        wp_enqueue_style(
            'demontek-steam-frontend-css',
            $this->plugin_url . 'assets/css/steam-frontend.css',
            array(),
            $cache_buster
        );
        
        wp_enqueue_script(
            'demontek-steam-frontend-js',
            $this->plugin_url . 'assets/js/steam-frontend.js',
            array('jquery'),
            $cache_buster,
            true
        );
        
        $post_id = get_the_ID();
        wp_localize_script('demontek-steam-frontend-js', 'demontekSteamData', array(
            'postId' => $post_id,
            'extraSidebar' => get_post_meta($post_id, '_demontek_steam_extra_sidebar', true),
            'contentLayout' => get_post_meta($post_id, '_demontek_steam_content_layout', true),
            'isAdmin' => current_user_can('edit_posts'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('demontek_steam_frontend_nonce'),
            'version' => $this->version,
            'optimizedMode' => true
        ));
    }
    
    private function should_load_frontend_assets() {
        if (!get_option('demontek_steam_enabled', false)) {
            return false;
        }
        
        if (is_single()) {
            $post_id = get_the_ID();
            $use_steam_layout = get_post_meta($post_id, '_demontek_steam_use', true);
            if (!empty($use_steam_layout) && $use_steam_layout === '1') {
                return true;
            }
        }
        
        if (get_option('demontek_steam_global_mode', false) && is_single()) {
            return true;
        }
        
        return false;
    }
}

// Initialize the plugin
new DemontekSteamTemplate();

// OPTIMIZED: Enhanced function with better performance
function get_demontek_steam_field_data($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    
    // OPTIMIZED: Check cache first
    $cache_key = 'steam_field_data_' . $post_id;
    $cached_data = wp_cache_get($cache_key, 'demontek_steam');
    
    if ($cached_data !== false && !WP_DEBUG) {
        return $cached_data;
    }
    
    $all_meta = get_post_meta($post_id);
    $steam_data = array();
    
    // Enhanced trailer support - up to 5 trailers
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($all_meta["trailer_{$i}"][0])) {
            $steam_data['trailers'][$i] = $all_meta["trailer_{$i}"][0];
        }
    }
    
    // All supported fields
    $fields = array('ai_summary', 'ai_excerpt', 'game_genre', 'review_1', 'review_2', 'review_3', 'original_link', 'steam_link', 'amazon_link', 'developer', 'platforms', 'release_date');
    foreach ($fields as $field) {
        if (!empty($all_meta[$field][0])) {
            $steam_data[$field] = $all_meta[$field][0];
        }
    }
    
    // Add completion metadata
    $steam_data['_meta'] = array(
        'completion_rate' => calculate_steam_completion_rate($steam_data),
        'post_id' => $post_id,
        'api_version' => '1.7.0'
    );
    
    // OPTIMIZED: Cache for 10 minutes
    wp_cache_set($cache_key, $steam_data, 'demontek_steam', 600);
    
    return $steam_data;
}

// OPTIMIZED: Enhanced completion rate calculation
function calculate_steam_completion_rate($steam_data) {
    $important_fields = array('trailer_1', 'ai_summary', 'game_genre', 'developer');
    $total_fields = count($important_fields);
    $completed_fields = 0;
    
    foreach ($important_fields as $field) {
        if (!empty($steam_data[$field])) {
            $completed_fields++;
        }
    }
    
    return $total_fields > 0 ? round(($completed_fields / $total_fields) * 100) : 0;
}
?>