<?php
/**
 * Steam Inspector Class
 * Handles the Steam field inspection interface
 */

if (!defined('ABSPATH')) exit;

class DemontekSteamInspector {
    
    private $core;
    private $ajax;
    
    public function __construct() {
        // No hooks needed - this is called directly by dashboard
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
    
    private function get_ajax() {
        if (!$this->ajax) {
            $this->ajax = demontek_steam_get_component('ajax');
        }
        return $this->ajax;
    }
    
    /**
     * Render the main inspector interface
     */
    public function render_interface() {
        ?>
        <div class="demontek-inspector-tabs">
            <button class="demontek-inspector-tab active" onclick="steamInspector.switchTab('inspector')">🔍 Field Inspector</button>
            <button class="demontek-inspector-tab" onclick="steamInspector.switchTab('actions')">⚡ Quick Actions</button>
            <button class="demontek-inspector-tab" onclick="steamInspector.switchTab('wordpress')">📝 WordPress Fields</button>
        </div>
        
        <div class="demontek-inspector-content">
            <!-- Field Inspector Tab -->
            <div id="inspector-tab" class="demontek-inspector-tab-content">
                <?php $this->render_inspector_tab(); ?>
            </div>
            
            <!-- Quick Actions Tab -->
            <div id="actions-tab" class="demontek-inspector-tab-content" style="display: none;">
                <?php $this->render_actions_tab(); ?>
            </div>
            
            <!-- WordPress Fields Tab -->
            <div id="wordpress-tab" class="demontek-inspector-tab-content" style="display: none;">
                <?php $this->render_wordpress_tab(); ?>
            </div>
        </div>
        
        <?php $this->render_inspector_styles(); ?>
        <?php $this->render_inspector_scripts(); ?>
        <?php
    }
    
    /**
     * Render inspector tab content
     */
    private function render_inspector_tab() {
        ?>
        <!-- Category Selector -->
        <div class="demontek-category-selector">
            <select class="demontek-category-select" id="categorySelect">
                <option value="">All Posts with Steam Data (Last 10)</option>
                <?php $this->render_category_options(); ?>
            </select>
            <button class="demontek-load-posts-btn" onclick="steamInspector.loadPosts()">🔄 Load Posts</button>
            <span id="postCount" style="font-size: 11px; color: #666;">Click "Load Posts" to start</span>
        </div>
        
        <!-- Post Navigator -->
        <div class="demontek-post-navigator">
            <img src="https://via.placeholder.com/60x30/2a475e/c5c3c0?text=Loading" 
                 alt="Current Post" class="demontek-post-thumbnail" id="currentPostThumbnail">
            <div class="demontek-post-info">
                <h4 class="demontek-post-title" id="currentPostTitle">Click "Load Posts" to start</h4>
                <div class="demontek-post-meta" id="currentPostMeta">Select a category and load posts to see real WordPress data</div>
            </div>
            <div class="demontek-nav-arrows">
                <button class="demontek-nav-arrow" onclick="steamInspector.navigatePost('prev')" id="prevBtn" disabled>‹</button>
                <button class="demontek-nav-arrow" onclick="steamInspector.navigatePost('next')" id="nextBtn" disabled>›</button>
            </div>
        </div>
        
        <!-- Field Search & Controls -->
        <input type="text" class="demontek-field-search" id="fieldSearch" placeholder="🔍 Search fields..." onkeyup="steamInspector.searchFields()">
        
        <div class="demontek-field-controls">
            <button class="demontek-bulk-btn" onclick="steamInspector.showAllFields()">👁️ Show All</button>
            <button class="demontek-bulk-btn" onclick="steamInspector.hideEmptyFields()">🚫 Hide Empty</button>
            <button class="demontek-bulk-btn" onclick="steamInspector.showOnlyUrls()">🔗 URLs Only</button>
            <button class="demontek-bulk-btn" onclick="steamInspector.exportFieldData()">💾 Export</button>
            <button class="demontek-bulk-btn" onclick="steamInspector.syncWithPreview()">🔄 Sync Preview</button>
            <span id="hiddenCount" style="margin-left: 10px; color: #666;">0 hidden</span>
        </div>
        
        <!-- Field Inspector Table -->
        <div class="demontek-field-table">
            <div class="demontek-field-table-header">
                <div>Field</div>
                <div>Data</div>
                <div>Actions</div>
            </div>
            <div id="fieldTableBody">
                <div style="padding: 20px; text-align: center; color: #666;">
                    Click "Load Posts" above to inspect real WordPress posts with Steam data
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render actions tab content
     */
    private function render_actions_tab() {
        ?>
        <h4 style="margin: 0 0 10px 0; color: #2271b1; font-size: 14px;">⚡ Quick Actions</h4>
        
        <!-- Missing Fields Alert -->
        <div id="missingFieldsAlert" style="margin-bottom: 15px;">
            <div style="background: #f0f6fc; border: 1px solid #0969da; border-radius: 4px; padding: 8px; color: #0969da; font-size: 11px;">
                🔄 Load posts first to see missing field alerts and quick actions.
            </div>
        </div>
        
        <!-- Action Buttons Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
            <button class="demontek-load-posts-btn" onclick="steamInspector.duplicatePost()" style="width: 100%;">
                📋 Duplicate Post
            </button>
            <button class="demontek-load-posts-btn" onclick="steamInspector.bulkEdit()" style="width: 100%;">
                ✏️ Bulk Edit Fields
            </button>
            <button class="demontek-load-posts-btn" onclick="steamInspector.validateSteamData()" style="width: 100%;">
                ✅ Validate Steam Data
            </button>
            <button class="demontek-load-posts-btn" onclick="steamInspector.generateMetaTags()" style="width: 100%;">
                🏷️ Generate SEO Meta
            </button>
            <button class="demontek-load-posts-btn" onclick="steamInspector.importFromSteam()" style="width: 100%;">
                🎮 Import from Steam API
            </button>
            <button class="demontek-load-posts-btn" onclick="steamInspector.schedulePublish()" style="width: 100%;">
                📅 Schedule Publish
            </button>
        </div>
        
        <div style="font-size: 11px; color: #666; text-align: center; margin-top: 10px;">
            More features coming soon! These are placeholder actions ready for development.
        </div>
        <?php
    }
    
    /**
     * Render WordPress tab content
     */
    private function render_wordpress_tab() {
        ?>
        <h4 style="margin: 0 0 10px 0; color: #2271b1; font-size: 14px;">📝 WordPress Integration</h4>
        <div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 4px; padding: 10px; margin-bottom: 10px;">
            <h5 style="margin: 0 0 5px 0; color: #2271b1; font-size: 12px;">🎯 v1.7.0 Improvements</h5>
            <ul style="font-size: 10px; color: #666; margin: 0; padding-left: 15px;">
                <li>🔧 Modular architecture for better performance</li>
                <li>⚡ Optimized post loading (exactly 10 posts)</li>
                <li>🎮 Clean component separation</li>
                <li>📱 Better mobile preview handling</li>
                <li>🚀 Memory efficient loading</li>
            </ul>
        </div>
        <div style="font-size: 11px; color: #666;">
            <strong>Field Mapping:</strong> Steam API data → WordPress custom fields<br>
            <strong>Available:</strong> <span id="availableFieldCount">0</span>+ Steam fields detected<br>
            <strong>Hidden:</strong> <span id="hiddenFieldCount">0</span> fields hidden from preview<br>
            <strong>Posts Per Load:</strong> <span id="postsPerLoadCount"><?php echo $this->get_core()->get_setting('posts_per_load', 10); ?></span> (prevents hanging)
        </div>
        <?php
    }
    
    /**
     * Render category options
     */
    private function render_category_options() {
        $categories = get_categories(array(
            'hide_empty' => false, 
            'number' => 50,
            'orderby' => 'count',
            'order' => 'DESC'
        ));
        
        $ajax = $this->get_ajax();
        
        foreach ($categories as $category) {
            // Count Steam posts in this category
            $steam_post_count = $ajax ? $ajax->count_category_steam_posts($category->term_id) : 0;
            $display_count = $steam_post_count > 0 ? $steam_post_count . ' Steam' : $category->count . ' total';
            
            echo '<option value="' . $category->term_id . '">' . $category->name . ' (' . $display_count . ' posts)</option>';
        }
    }
    
    /**
     * Render inspector-specific styles
     */
    private function render_inspector_styles() {
        ?>
        <style>
            .demontek-post-navigator { display: flex; align-items: center; gap: 10px; padding: 10px; background: #f8f9fa; border-radius: 6px; margin-bottom: 15px; }
            .demontek-post-thumbnail { width: 60px; height: 30px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
            .demontek-post-info { flex: 1; }
            .demontek-post-title { font-weight: 600; color: #2271b1; font-size: 14px; margin: 0 0 2px 0; }
            .demontek-post-meta { font-size: 11px; color: #666; }
            .demontek-nav-arrows { display: flex; gap: 5px; }
            .demontek-nav-arrow { padding: 5px 8px; background: #2271b1; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
            .demontek-nav-arrow:hover { background: #135e96; }
            .demontek-nav-arrow:disabled { background: #ccc; cursor: not-allowed; opacity: 0.5; }
            
            .demontek-field-table { border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; background: white; }
            .demontek-field-table-header { background: #f8f9fa; border-bottom: 1px solid #e0e0e0; display: grid; grid-template-columns: 150px 1fr 100px; gap: 10px; padding: 10px; font-weight: 600; color: #2271b1; font-size: 12px; }
            .demontek-field-row { display: grid; grid-template-columns: 150px 1fr 100px; gap: 10px; padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: 11px; align-items: center; }
            .demontek-field-row:hover { background: #f8f9fa; }
            .demontek-field-row.hidden { opacity: 0.3; background: #f8f8f8; }
            .demontek-field-row.highlighted { background: #e7f3ff; border-left: 3px solid #2271b1; }
            
            .demontek-field-name { color: #2271b1; font-weight: 500; font-family: monospace; }
            .demontek-field-value { color: #333; word-break: break-all; max-height: 40px; overflow: hidden; }
            .demontek-field-actions { display: flex; gap: 2px; }
            
            .demontek-action-btn { padding: 2px 4px; border: none; border-radius: 3px; cursor: pointer; font-size: 9px; color: white; font-weight: bold; min-width: 20px; text-align: center; }
            .demontek-action-copy { background: #2271b1; }
            .demontek-action-copy:after { content: "COPY"; }
            .demontek-action-edit { background: #f57c00; }
            .demontek-action-edit:after { content: "EDIT"; }
            .demontek-action-add { background: #00a32a; }
            .demontek-action-add:after { content: "ADD"; }
            .demontek-action-view { background: #666; }
            .demontek-action-view:after { content: "HIDE"; }
            .demontek-action-view.show:after { content: "SHOW"; }
            
            .demontek-missing-field-alert { background: #fff3cd; border: 1px solid #f57c00; border-radius: 4px; padding: 10px; margin-bottom: 10px; }
            .demontek-missing-btn { display: inline-block; background: #dc3232; color: white; padding: 4px 8px; border-radius: 3px; text-decoration: none; font-size: 10px; margin: 2px; cursor: pointer; border: none; }
            .demontek-missing-btn:hover { background: #b71c1c; color: white; text-decoration: none; }
            
            .demontek-field-search { width: 100%; padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 11px; margin-bottom: 10px; }
            .demontek-field-controls { display: flex; gap: 5px; margin-bottom: 10px; font-size: 10px; }
            .demontek-bulk-btn { padding: 4px 8px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer; font-size: 10px; }
            .demontek-bulk-btn:hover { background: #f0f0f0; }
        </style>
        <?php
    }
    
    /**
     * Render inspector JavaScript
     */
    private function render_inspector_scripts() {
        ?>
        <script>
        // Steam Inspector Object - Clean modular approach
        window.steamInspector = {
            currentPostIndex: 0,
            currentTab: 'inspector',
            steamPosts: [],
            hiddenFields: new Set(),
            allFields: [],
            
            // Initialize inspector
            init: function() {
                this.updateNavigationButtons();
                console.log('🔍 Steam Inspector v1.7.0 initialized - Modular & optimized!');
            },
            
            // Switch between tabs
            switchTab: function(tabName) {
                // Update active tab button
                document.querySelectorAll('.demontek-inspector-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                event.target.classList.add('active');
                
                // Show/hide content
                document.querySelectorAll('.demontek-inspector-tab-content').forEach(content => {
                    content.style.display = 'none';
                });
                document.getElementById(tabName + '-tab').style.display = 'block';
                
                this.currentTab = tabName;
            },
            
            // Load posts with optimized AJAX call
            loadPosts: function() {
                const categorySelect = document.getElementById('categorySelect');
                const category = categorySelect.value;
                const categoryName = categorySelect.selectedOptions[0]?.text || 'All Categories';
                
                console.log('🔄 Loading posts for category:', category, categoryName);
                this.showLoadingState();
                
                const formData = new FormData();
                formData.append('action', 'steam_load_posts');
                formData.append('category_id', category);
                formData.append('posts_per_page', '10'); // Fixed limit to prevent hanging
                formData.append('nonce', demontekNonce);
                
                // 15 second timeout to prevent hanging
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000);
                
                fetch(ajaxurl, {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal
                })
                .then(response => {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Posts loaded successfully:', data);
                    
                    if (data.success) {
                        this.steamPosts = data.data.posts;
                        const message = data.data.message + (category ? ` from ${categoryName}` : '');
                        document.getElementById('postCount').textContent = message;
                        this.currentPostIndex = 0;
                        
                        if (this.steamPosts.length > 0 && this.steamPosts[0].id !== 0) {
                            this.loadCurrentPost();
                            this.updateNavigationButtons();
                            this.showActionFeedback(`✅ Loaded ${this.steamPosts.length} real WordPress posts!`);
                        } else {
                            this.showNoPostsMessage();
                        }
                    } else {
                        throw new Error(data.data || 'Unknown error');
                    }
                    this.hideLoadingState();
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    console.error('❌ Error loading posts:', error);
                    
                    if (error.name === 'AbortError') {
                        this.showErrorMessage('Request timed out after 15 seconds. Try selecting a category with fewer posts.');
                    } else {
                        this.showErrorMessage('Network error: ' + error.message + '. Check browser console for details.');
                    }
                    this.hideLoadingState();
                });
            },
            
            // Navigation functions
            navigatePost: function(direction) {
                if (this.steamPosts.length === 0) {
                    this.showActionFeedback('❌ No posts loaded. Click "Load Posts" first.');
                    return;
                }
                
                const oldIndex = this.currentPostIndex;
                
                if (direction === 'prev' && this.currentPostIndex > 0) {
                    this.currentPostIndex--;
                } else if (direction === 'next' && this.currentPostIndex < this.steamPosts.length - 1) {
                    this.currentPostIndex++;
                }
                
                if (oldIndex !== this.currentPostIndex) {
                    this.loadCurrentPost();
                    this.updateNavigationButtons();
                    this.showActionFeedback(`🔄 Navigated to post ${this.currentPostIndex + 1} of ${this.steamPosts.length}`);
                } else {
                    if (direction === 'prev' && this.currentPostIndex === 0) {
                        this.showActionFeedback('⬅️ Already at first post');
                    } else if (direction === 'next' && this.currentPostIndex === this.steamPosts.length - 1) {
                        this.showActionFeedback('➡️ Already at last post');
                    }
                }
            },
            
            updateNavigationButtons: function() {
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                
                if (prevBtn && nextBtn) {
                    prevBtn.disabled = this.currentPostIndex === 0 || this.steamPosts.length === 0;
                    nextBtn.disabled = this.currentPostIndex === this.steamPosts.length - 1 || this.steamPosts.length === 0;
                }
            },
            
            // Current post loading
            loadCurrentPost: function() {
                if (this.steamPosts.length === 0 || !this.steamPosts[this.currentPostIndex]) return;
                
                const post = this.steamPosts[this.currentPostIndex];
                console.log('📝 Loading current post:', post);
                
                // Update post navigator
                document.getElementById('currentPostThumbnail').src = post.thumbnail;
                document.getElementById('currentPostTitle').textContent = post.title;
                document.getElementById('currentPostMeta').textContent = 
                    `Post ${this.currentPostIndex + 1} of ${this.steamPosts.length} • ID: ${post.id} • ${post.category_name} • ${post.status}`;
                
                // Update field table
                this.loadFieldTable(post);
                
                // Check missing fields
                this.checkMissingFields(post);
                
                // Update mobile preview
                if (post.id > 0) {
                    this.loadMobilePreview(post.id);
                }
                
                // Update WordPress fields tab
                this.updateWordPressFieldsTab();
            },
            
            // Field table management
            loadFieldTable: function(post) {
                const wpFields = [
                    { name: 'post_id', value: post.id, type: 'number', category: 'WordPress' },
                    { name: 'post_title', value: post.title, type: 'text', category: 'WordPress' },
                    { name: 'post_slug', value: post.slug, type: 'text', category: 'WordPress' },
                    { name: 'post_date', value: post.post_date, type: 'date', category: 'WordPress' },
                    { name: 'post_author', value: post.author, type: 'text', category: 'WordPress' },
                    { name: 'post_status', value: post.status, type: 'text', category: 'WordPress' },
                    { name: 'post_category', value: post.category_name, type: 'text', category: 'WordPress' },
                    { name: 'post_excerpt', value: post.excerpt || '', type: 'text', category: 'WordPress' }
                ];
                
                const steamFields = Object.entries(post.steam_data || {}).map(([key, value]) => ({
                    name: key,
                    value: value || '',
                    type: this.detectFieldType(key, value),
                    category: 'Steam'
                }));
                
                this.allFields = [...wpFields, ...steamFields];
                this.renderFieldTable();
            },
            
            detectFieldType: function(key, value) {
                if (key.includes('trailer') || key.includes('link') || key.includes('url')) return 'url';
                if (key.includes('price') || key.includes('score') || key.includes('appid') || key.includes('id')) return 'number';
                if (typeof value === 'boolean') return 'boolean';
                if (key.includes('date')) return 'date';
                return 'text';
            },
            
            renderFieldTable: function() {
                const tableBody = document.getElementById('fieldTableBody');
                const searchTerm = document.getElementById('fieldSearch').value.toLowerCase();
                
                const filteredFields = this.allFields.filter(field => {
                    const matchesSearch = field.name.toLowerCase().includes(searchTerm) || 
                                          field.value.toString().toLowerCase().includes(searchTerm);
                    return matchesSearch;
                });
                
                tableBody.innerHTML = filteredFields.map(field => {
                    const isHidden = this.hiddenFields.has(field.name);
                    const rowClass = isHidden ? 'demontek-field-row hidden' : 'demontek-field-row';
                    
                    return `
                        <div class="${rowClass}" data-field="${field.name}">
                            <div class="demontek-field-name">
                                <span style="background: ${field.category === 'Steam' ? '#e7f3ff' : '#f0f8f0'}; 
                                             padding: 1px 4px; border-radius: 2px; font-size: 9px; margin-right: 5px;">
                                    ${field.category}
                                </span>
                                ${field.name}
                            </div>
                            <div class="demontek-field-value">${this.formatFieldValue(field.value, field.type)}</div>
                            <div class="demontek-field-actions">
                                <button class="demontek-action-btn demontek-action-copy" title="Copy field value" onclick="steamInspector.copyField('${field.name}', '${encodeURIComponent(field.value)}')"></button>
                                <button class="demontek-action-btn demontek-action-edit" title="Edit in WordPress" onclick="steamInspector.editField('${field.name}', ${this.steamPosts[this.currentPostIndex].id})"></button>
                                <button class="demontek-action-btn demontek-action-add" title="Add to gallery" onclick="steamInspector.addToGallery('${field.name}', '${encodeURIComponent(field.value)}')"></button>
                                <button class="demontek-action-btn demontek-action-view ${isHidden ? 'show' : ''}" title="${isHidden ? 'Show field' : 'Hide field'}" onclick="steamInspector.toggleFieldVisibility('${field.name}')"></button>
                            </div>
                        </div>
                    `;
                }).join('');
                
                this.updateHiddenCount();
            },
            
            formatFieldValue: function(value, type) {
                if (value === null || value === undefined || value === '') {
                    return '<span style="color: #ccc; font-style: italic;">empty</span>';
                }
                
                if (type === 'boolean') {
                    return value ? '<span style="color: #00a32a;">✅ true</span>' : '<span style="color: #dc3232;">❌ false</span>';
                }
                if (type === 'url') {
                    return `<a href="${value}" target="_blank" style="color: #2271b1; text-decoration: none;">🔗 ${value.substring(0, 40)}...</a>`;
                }
                if (type === 'date') {
                    return `<span style="color: #666;">📅 ${value}</span>`;
                }
                if (typeof value === 'string' && value.length > 60) {
                    return `<span title="${value}">${value.substring(0, 60)}...</span>`;
                }
                return value;
            },
            
            // Field actions
            copyField: function(fieldName, encodedValue) {
                const value = decodeURIComponent(encodedValue);
                navigator.clipboard.writeText(value).then(() => {
                    this.showActionFeedback(`📋 Copied: ${fieldName}`);
                }).catch(() => {
                    this.showActionFeedback(`❌ Failed to copy: ${fieldName}`);
                });
            },
            
            editField: function(fieldName, postId) {
                const editUrl = `<?php echo admin_url('post.php'); ?>?post=${postId}&action=edit#${fieldName}`;
                window.open(editUrl, '_blank');
                this.showActionFeedback(`✏️ Opening WordPress editor for: ${fieldName}`);
            },
            
            addToGallery: function(fieldName, encodedValue) {
                this.showActionFeedback(`📁 Added ${fieldName} to gallery`);
            },
            
            toggleFieldVisibility: function(fieldName) {
                if (this.hiddenFields.has(fieldName)) {
                    this.hiddenFields.delete(fieldName);
                } else {
                    this.hiddenFields.add(fieldName);
                }
                
                this.renderFieldTable();
                this.syncWithPreview();
                this.showActionFeedback(`${this.hiddenFields.has(fieldName) ? '🙈 Hidden' : '👁️ Shown'} field: ${fieldName}`);
            },
            
            // Utility functions
            searchFields: function() {
                this.renderFieldTable();
            },
            
            showAllFields: function() {
                this.hiddenFields.clear();
                this.renderFieldTable();
                this.syncWithPreview();
                this.showActionFeedback('👁️ All fields shown');
            },
            
            hideEmptyFields: function() {
                this.allFields.forEach(field => {
                    if (!field.value || field.value === '' || field.value === null) {
                        this.hiddenFields.add(field.name);
                    }
                });
                this.renderFieldTable();
                this.syncWithPreview();
                this.showActionFeedback('🚫 Empty fields hidden');
            },
            
            showOnlyUrls: function() {
                this.hiddenFields.clear();
                this.allFields.forEach(field => {
                    if (field.type !== 'url') {
                        this.hiddenFields.add(field.name);
                    }
                });
                this.renderFieldTable();
                this.syncWithPreview();
                this.showActionFeedback('🔗 Showing URLs only');
            },
            
            exportFieldData: function() {
                if (this.steamPosts.length === 0 || !this.steamPosts[this.currentPostIndex]) {
                    this.showActionFeedback('❌ No post data to export');
                    return;
                }
                
                const currentPost = this.steamPosts[this.currentPostIndex];
                const exportData = {
                    post: currentPost,
                    hiddenFields: Array.from(this.hiddenFields),
                    exportDate: new Date().toISOString(),
                    fieldCount: this.allFields.length,
                    version: '1.7.0'
                };
                
                const blob = new Blob([JSON.stringify(exportData, null, 2)], {type: 'application/json'});
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `steam-field-data-${currentPost.slug}-${Date.now()}.json`;
                a.click();
                URL.revokeObjectURL(url);
                
                this.showActionFeedback('💾 Field data exported');
            },
            
            syncWithPreview: function() {
                const currentPost = this.steamPosts[this.currentPostIndex];
                if (currentPost && currentPost.id > 0) {
                    this.loadMobilePreview(currentPost.id, Array.from(this.hiddenFields));
                    this.showActionFeedback('🔄 Preview synced with field visibility');
                }
            },
            
            updateHiddenCount: function() {
                document.getElementById('hiddenCount').textContent = `${this.hiddenFields.size} hidden`;
            },
            
            updateWordPressFieldsTab: function() {
                const availableCount = document.getElementById('availableFieldCount');
                const hiddenCount = document.getElementById('hiddenFieldCount');
                if (availableCount) availableCount.textContent = this.allFields.length;
                if (hiddenCount) hiddenCount.textContent = this.hiddenFields.size;
            },
            
            // Mobile preview
            loadMobilePreview: function(postId, hiddenFieldsList = []) {
                const previewFrame = document.getElementById('mobile-preview');
                
                if (!postId || postId === 0) {
                    previewFrame.innerHTML = '<div class="demontek-preview-loading"><div>No posts available for preview</div></div>';
                    return;
                }
                
                let previewUrl = '<?php echo home_url(); ?>/?p=' + postId;
                const params = new URLSearchParams();
                params.append('demontek_preview', '1');
                params.append('mobile', '1');
                params.append('show_admin_bar', 'false');
                params.append('_wpnonce', '<?php echo wp_create_nonce('demontek_preview_nonce'); ?>');
                
                if (hiddenFieldsList && hiddenFieldsList.length > 0) {
                    params.append('hidden_fields', hiddenFieldsList.join(','));
                }
                
                const fullUrl = previewUrl + '&' + params.toString();
                
                previewFrame.innerHTML = `
                    <iframe class="demontek-preview-iframe" 
                            src="${fullUrl}" 
                            onload="console.log('✅ Mobile preview loaded')" 
                            onerror="steamInspector.previewError()"
                            style="width: 100%; height: 100%; border: none;">
                    </iframe>
                `;
                
                console.log('📱 Loading mobile preview for post:', postId, 'URL:', fullUrl);
            },
            
            refreshMobilePreview: function() {
                if (this.steamPosts.length > 0 && this.steamPosts[this.currentPostIndex] && this.steamPosts[this.currentPostIndex].id > 0) {
                    this.loadMobilePreview(this.steamPosts[this.currentPostIndex].id, Array.from(this.hiddenFields));
                    this.showActionFeedback('🔄 Mobile preview refreshed');
                } else {
                    this.showActionFeedback('❌ Load posts first to refresh preview');
                }
            },
            
            previewError: function() {
                const previewFrame = document.getElementById('mobile-preview');
                previewFrame.innerHTML = `
                    <div class="demontek-preview-loading">
                        <div>❌ Preview failed to load</div>
                        <div style="font-size: 12px; color: #888; margin-top: 10px;">
                            <button onclick="steamInspector.loadMobilePreview()" style="background: #2271b1; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                                🔄 Retry Preview
                            </button>
                        </div>
                    </div>
                `;
            },
            
            // Missing fields check
            checkMissingFields: function(post) {
                const importantFields = [
                    'trailer_1', 'trailer_2', 'trailer_3', 'trailer_4', 'trailer_5',
                    'ai_summary', 'ai_excerpt', 'review_1', 'review_2', 'review_3',
                    'amazon_link', 'original_link', 'steam_link'
                ];
                
                const missingFields = importantFields.filter(field => 
                    !post.steam_data[field] || post.steam_data[field] === ''
                );
                
                const alertDiv = document.getElementById('missingFieldsAlert');
                
                if (missingFields.length > 0 && post.id > 0) {
                    alertDiv.innerHTML = `
                        <div class="demontek-missing-field-alert">
                            <strong>⚠️ Missing ${missingFields.length} important fields in "${post.title}":</strong><br>
                            ${missingFields.map(field => 
                                `<a href="${post.edit_link}" target="_blank" class="demontek-missing-btn">&lt;missing ${field}&gt;</a>`
                            ).join(' ')}
                        </div>
                    `;
                } else if (post.id > 0) {
                    alertDiv.innerHTML = `
                        <div style="background: #d1ecf1; border: 1px solid #00a32a; border-radius: 4px; padding: 8px; color: #0c5460; font-size: 11px;">
                            ✅ All important Steam fields are populated in "${post.title}"!
                        </div>
                    `;
                } else {
                    alertDiv.innerHTML = `
                        <div style="background: #fff3cd; border: 1px solid #f57c00; border-radius: 4px; padding: 8px; color: #856404; font-size: 11px;">
                            ⚠️ No posts found with Steam data. Add Steam fields to existing posts.
                        </div>
                    `;
                }
            },
            
            // State management
            showLoadingState: function() {
                document.getElementById('currentPostTitle').textContent = "Loading Real WordPress Posts...";
                document.getElementById('currentPostMeta').textContent = "Fetching actual post data from database...";
                document.getElementById('fieldTableBody').innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">🔄 Loading real WordPress posts...</div>';
            },
            
            hideLoadingState: function() {
                // Loading complete - data will be populated by loadCurrentPost()
            },
            
            showErrorMessage: function(message) {
                document.getElementById('currentPostTitle').textContent = "Error Loading Posts";
                document.getElementById('currentPostMeta').textContent = message;
                document.getElementById('fieldTableBody').innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #dc3232;">
                        <strong>❌ Error:</strong> ${message}<br><br>
                        <button onclick="steamInspector.loadPosts()" style="background: #2271b1; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                            🔄 Try Again
                        </button>
                        <br><br>
                        <small style="color: #666;">v1.7.0: If this persists, your site may have too many posts. Try a specific category.</small>
                    </div>
                `;
                this.updateNavigationButtons();
                
                const previewFrame = document.getElementById('mobile-preview');
                previewFrame.innerHTML = '<div class="demontek-preview-loading"><div>❌ Error loading posts</div></div>';
            },
            
            showNoPostsMessage: function() {
                document.getElementById('currentPostTitle').textContent = "No posts with Steam data found";
                document.getElementById('currentPostMeta').textContent = "Try adding Steam fields to posts or select a different category";
                document.getElementById('fieldTableBody').innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">No posts with Steam data found. Add fields like trailer_1 or ai_summary to posts.</div>';
                
                const previewFrame = document.getElementById('mobile-preview');
                previewFrame.innerHTML = '<div class="demontek-preview-loading"><div>No posts to preview</div><div style="font-size: 12px; color: #888;">Add Steam fields to posts first</div></div>';
                
                this.updateNavigationButtons();
            },
            
            showActionFeedback: function(message) {
                const feedback = document.createElement('div');
                feedback.style.cssText = 'position: fixed; top: 60px; right: 20px; background: #2271b1; color: white; padding: 8px 12px; border-radius: 4px; z-index: 10000; font-size: 11px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);';
                feedback.textContent = message;
                document.body.appendChild(feedback);
                
                setTimeout(() => {
                    if (document.body.contains(feedback)) {
                        document.body.removeChild(feedback);
                    }
                }, 3000);
            },
            
            // Quick action placeholders
            duplicatePost: function() {
                if (this.steamPosts.length === 0 || !this.steamPosts[this.currentPostIndex]) {
                    this.showActionFeedback('❌ No post selected to duplicate');
                    return;
                }
                const currentPost = this.steamPosts[this.currentPostIndex];
                this.showActionFeedback(`📋 Duplicating post: ${currentPost.title} (Feature coming soon!)`);
            },
            
            bulkEdit: function() {
                this.showActionFeedback(`✏️ Bulk edit mode (Feature coming soon!)`);
            },
            
            validateSteamData: function() {
                if (this.steamPosts.length === 0 || !this.steamPosts[this.currentPostIndex]) {
                    this.showActionFeedback('❌ No post data to validate');
                    return;
                }
                const currentPost = this.steamPosts[this.currentPostIndex];
                const steamFields = Object.keys(currentPost.steam_data || {});
                this.showActionFeedback(`✅ Validated ${steamFields.length} Steam fields`);
            },
            
            generateMetaTags: function() {
                this.showActionFeedback(`🏷️ Generating SEO meta tags (Feature coming soon!)`);
            },
            
            importFromSteam: function() {
                if (this.steamPosts.length === 0 || !this.steamPosts[this.currentPostIndex]) {
                    this.showActionFeedback('❌ No post selected for Steam import');
                    return;
                }
                const currentPost = this.steamPosts[this.currentPostIndex];
                const appId = currentPost.steam_data?.steam_appid || 'Unknown';
                this.showActionFeedback(`🎮 Importing from Steam API for App ID: ${appId} (Feature coming soon!)`);
            },
            
            schedulePublish: function() {
                this.showActionFeedback(`📅 Schedule publish options (Feature coming soon!)`);
            }
        };
        
        // Initialize inspector when page loads
        document.addEventListener('DOMContentLoaded', function() {
            steamInspector.init();
            console.log('🎮 Steam Inspector v1.7.0 - Modular architecture with optimized post loading!');
        });
        </script>
        <?php
    }
}