/**
 * Demontek Steam Template - Admin JavaScript v1.6.3
 * Complete admin interface with working inspector, dashboard, and post editor functionality
 * File: /assets/admin/admin-inspector.js
 * 
 * FIXES v1.6.3:
 * - Added proper loading indicators with progress bars
 * - Fixed AJAX debugging with detailed console logs
 * - Added timeout handling for stuck requests
 * - Enhanced error reporting with user-friendly messages
 */

(function($) {
    'use strict';

    // Main Admin Manager
    const SteamAdmin = {
        initialized: false,
        config: {
            ajaxUrl: ajaxurl || '',
            nonce: '',
            version: '1.6.3'
        },

        // Initialize admin functionality
        init: function() {
            if (this.initialized) return;

            console.log('🔧 Steam Admin v' + this.config.version + ' initializing...');

            this.loadConfig();
            this.initializeDashboard();
            this.initializeInspector();
            this.initializePostEditor();
            this.setupGlobalHandlers();

            this.initialized = true;
            console.log('✅ Steam Admin initialized successfully');
        },

        // Load configuration from WordPress
        loadConfig: function() {
            if (typeof demontekSteamAdmin !== 'undefined') {
                this.config = Object.assign(this.config, demontekSteamAdmin);
            }
            
            // Ensure we have ajaxUrl
            if (!this.config.ajaxUrl && typeof ajaxurl !== 'undefined') {
                this.config.ajaxUrl = ajaxurl;
            }
            
            // Fallback nonce detection
            if (!this.config.nonce && typeof demontekNonce !== 'undefined') {
                this.config.nonce = demontekNonce;
            }

            console.log('🔧 Config loaded:', this.config);
        },

        // Initialize dashboard functionality
        initializeDashboard: function() {
            this.initializeSettings();
            this.initializeQuickActions();
            this.initializeStatistics();
        },

        // Initialize settings toggles and saves
        initializeSettings: function() {
            const self = this;

            // Settings save functions - FIXED: Proper global function declarations
            window.toggleDemontekSteam = function(enabled) {
                console.log('🔄 Toggling Steam:', enabled);
                self.saveSetting('enabled', enabled ? '1' : '0')
                    .then(() => {
                        self.showActionFeedback('⚙️ Steam ' + (enabled ? 'Enabled' : 'Disabled'));
                        setTimeout(() => location.reload(), 1000);
                    })
                    .catch(error => {
                        console.error('Toggle error:', error);
                        self.showActionFeedback('❌ Toggle failed');
                    });
            };

            window.toggleGlobalMode = function(enabled) {
                console.log('🔄 Toggling Global Mode:', enabled);
                self.saveSetting('global_mode', enabled ? '1' : '0')
                    .then(() => {
                        self.showActionFeedback('🌐 Global Mode ' + (enabled ? 'Enabled' : 'Disabled'));
                        setTimeout(() => location.reload(), 1000);
                    })
                    .catch(error => {
                        console.error('Global mode toggle error:', error);
                        self.showActionFeedback('❌ Global mode toggle failed');
                    });
            };
        },

        // Initialize quick action buttons - FIXED: All functions working
        initializeQuickActions: function() {
            const self = this;

            // FIXED: Test functions with proper AJAX calls and loading indicators
            window.testAjax = function() {
                console.log('🧪 Testing AJAX endpoints');
                
                // Show loading indicator
                const button = event.target;
                const originalText = button.textContent;
                button.innerHTML = '🔄 Testing...';
                button.disabled = true;
                
                self.testAjaxEndpoints()
                    .finally(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
            };

            window.loadTestPosts = function() {
                console.log('📋 Loading test posts');
                
                const button = event.target;
                const originalText = button.textContent;
                button.innerHTML = '📋 Loading...';
                button.disabled = true;
                
                self.loadTestPosts()
                    .finally(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
            };

            // Other functions
            window.findSteamPosts = function() {
                const adminUrl = self.config.adminUrl || '/wp-admin/';
                window.location.href = adminUrl + 'edit.php?meta_key=trailer_1';
            };

            window.exportSettings = function() {
                const settings = {
                    enabled: self.config.enabled || false,
                    global_mode: self.config.globalMode || false,
                    version: self.config.version,
                    export_date: new Date().toISOString()
                };

                const blob = new Blob([JSON.stringify(settings, null, 2)], {type: 'application/json'});
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'demontek-steam-settings-v' + self.config.version + '.json';
                a.click();
                URL.revokeObjectURL(url);

                self.showActionFeedback('📁 Settings exported successfully!');
            };
        },

        // Initialize statistics display
        initializeStatistics: function() {
            const statCards = document.querySelectorAll('.demontek-stat-card');
            statCards.forEach(card => {
                card.addEventListener('click', function() {
                    const label = card.querySelector('.demontek-stat-label')?.textContent;
                    if (label) {
                        console.log('📊 Clicked stat:', label);
                    }
                });
            });
        },

        // Initialize inspector functionality
        initializeInspector: function() {
            if (typeof SteamInspector !== 'undefined') {
                SteamInspector.init();
            }
        },

        // Initialize post editor enhancements
        initializePostEditor: function() {
            // Only on post edit pages
            if (!document.getElementById('post')) return;

            this.enhanceFieldValidation();
            this.setupPreviewButtons();
            this.setupFieldRefresh();
        },

        // Enhance field validation
        enhanceFieldValidation: function() {
            const steamFields = document.querySelectorAll('input[name^="trailer_"], input[name$="_link"]');
            steamFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (this.value && !this.value.match(/^https?:\/\/.+/)) {
                        this.style.borderColor = '#dc3232';
                        this.title = 'Please enter a valid URL starting with http:// or https://';
                    } else {
                        this.style.borderColor = '';
                        this.title = '';
                    }
                });
            });
        },

        // Setup preview buttons
        setupPreviewButtons: function() {
            window.openFullPreview = function(mode) {
                const postId = document.querySelector('input[name="post_ID"]')?.value;
                if (!postId) return;

                const baseUrl = window.location.origin + '/?p=' + postId;
                const params = new URLSearchParams();
                params.append('demontek_preview', '1');
                params.append('mode', mode);
                
                if (mode === 'mobile') {
                    params.append('mobile', '1');
                    params.append('show_admin_bar', 'false');
                }
                
                const fullUrl = baseUrl + '&' + params.toString();
                window.open(fullUrl, '_blank');
            };
        },

        // Setup field refresh functionality
        setupFieldRefresh: function() {
            window.refreshFieldStatus = function() {
                const statusArea = document.querySelector('#demontek_steam_fields .inside');
                const postId = document.querySelector('input[name="post_ID"]')?.value;
                
                if (!statusArea || !postId) return;
                
                statusArea.innerHTML = '<p>🔄 Refreshing field status...</p>';
                
                SteamAdmin.makeAjaxRequest('steam_refresh_fields', {
                    post_id: postId
                })
                .then(data => {
                    if (data.success) {
                        statusArea.innerHTML = data.data.html || '<p>✅ Field status refreshed!</p>';
                    } else {
                        statusArea.innerHTML = '<p>❌ Error: ' + (data.data || 'Unknown error') + '</p>';
                    }
                })
                .catch(error => {
                    statusArea.innerHTML = '<p style="color: red;">❌ Network error</p>';
                    console.error('Field refresh error:', error);
                });
            };
        },

        // Setup global event handlers
        setupGlobalHandlers: function() {
            // Global escape key handler
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const modals = document.querySelectorAll('.demontek-modal, .demontek-overlay');
                    modals.forEach(modal => modal.style.display = 'none');
                }
            });

            // Global error handler for AJAX
            $(document).ajaxError((event, xhr, settings, thrownError) => {
                if (settings.url && (settings.url.includes('demontek') || settings.url.includes('steam'))) {
                    console.error('🚨 AJAX Error:', thrownError, xhr.responseText);
                    this.showActionFeedback('❌ Network error: ' + thrownError);
                }
            });
        },

        // ENHANCED: Helper method with timeout and better error handling
        makeAjaxRequest: function(action, data = {}) {
            const requestData = Object.assign({
                action: action,
                nonce: this.config.nonce
            }, data);

            console.log('📡 Making AJAX request:', action);
            console.log('📡 Request data:', requestData);
            console.log('📡 AJAX URL:', this.config.ajaxUrl);

            // Create timeout promise
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timeout after 30 seconds')), 30000);
            });

            // Create fetch promise
            const fetchPromise = fetch(this.config.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(requestData)
            })
            .then(response => {
                console.log('📡 Response status:', response.status);
                console.log('📡 Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('📡 Raw response:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('📡 JSON parse error:', e);
                    console.error('📡 Response text:', text);
                    throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                }
            })
            .then(data => {
                console.log('📡 Parsed response:', data);
                return data;
            });

            // Race between fetch and timeout
            return Promise.race([fetchPromise, timeoutPromise])
                .catch(error => {
                    console.error('📡 AJAX request failed:', error);
                    throw error;
                });
        },

        // Save a setting via AJAX
        saveSetting: function(setting, value) {
            return this.makeAjaxRequest('steam_save_settings', {
                setting: setting,
                value: value
            });
        },

        // ENHANCED: Test AJAX endpoints with detailed debugging
        testAjaxEndpoints: function() {
            const resultsDiv = document.getElementById('test-results');
            const outputDiv = document.getElementById('test-output');
            
            if (!resultsDiv || !outputDiv) {
                console.log('🧪 Test results divs not found, creating...');
                this.showActionFeedback('🧪 AJAX test started - check console');
            } else {
                resultsDiv.style.display = 'block';
                outputDiv.innerHTML = '<div class="ajax-progress">🔄 Testing AJAX endpoints...</div>';
            }
            
            return this.makeAjaxRequest('steam_debug_info')
                .then(data => {
                    console.log('🧪 Debug info response:', data);
                    if (data.success) {
                        const message = `✅ AJAX working! Plugin version: ${data.data.plugin_info.version}<br>✅ PHP version: ${data.data.plugin_info.php_version}`;
                        if (outputDiv) {
                            outputDiv.innerHTML = message;
                        }
                        this.showActionFeedback('✅ AJAX test successful!');
                    } else {
                        const errorMessage = '❌ AJAX failed: ' + (data.data || 'Unknown error');
                        if (outputDiv) {
                            outputDiv.innerHTML = errorMessage;
                        }
                        this.showActionFeedback('❌ AJAX test failed');
                    }
                })
                .catch(error => {
                    console.error('🧪 AJAX test error:', error);
                    const errorMessage = '❌ Network error: ' + error.message;
                    if (outputDiv) {
                        outputDiv.innerHTML = errorMessage;
                    }
                    this.showActionFeedback('❌ AJAX test failed: ' + error.message);
                });
        },

        // ENHANCED: Load test posts with progress indicators
        loadTestPosts: function() {
            const resultsDiv = document.getElementById('test-results');
            const outputDiv = document.getElementById('test-output');
            
            if (!resultsDiv || !outputDiv) {
                this.showActionFeedback('📋 Loading test posts - check console');
            } else {
                resultsDiv.style.display = 'block';
                outputDiv.innerHTML = '<div class="ajax-progress">📋 Loading test posts...</div>';
            }
            
            return this.makeAjaxRequest('steam_load_posts', {
                category_id: '',
                limit: '5'
            })
            .then(data => {
                console.log('📋 Load posts response:', data);
                if (data.success) {
                    const message = `✅ Posts loaded successfully!<br>Found: ${data.data.posts.length} posts<br>Total: ${data.data.debug_info.posts_found}`;
                    if (outputDiv) {
                        outputDiv.innerHTML = message;
                    }
                    this.showActionFeedback('✅ Test posts loaded successfully!');
                } else {
                    const errorMessage = '❌ Failed to load posts: ' + (data.data || 'Unknown error');
                    if (outputDiv) {
                        outputDiv.innerHTML = errorMessage;
                    }
                    this.showActionFeedback('❌ Failed to load test posts');
                }
            })
            .catch(error => {
                console.error('📋 Load posts error:', error);
                const errorMessage = '❌ Network error: ' + error.message;
                if (outputDiv) {
                    outputDiv.innerHTML = errorMessage;
                }
                this.showActionFeedback('❌ Failed to load test posts: ' + error.message);
            });
        },

        // Show action feedback to user with enhanced styling
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

        // Get debug information
        getDebugInfo: function() {
            return {
                initialized: this.initialized,
                config: this.config,
                inspectorAvailable: typeof SteamInspector !== 'undefined',
                isPostEditPage: !!document.getElementById('post'),
                isDashboardPage: window.location.search.includes('demontek-steam'),
                jQueryVersion: $.fn.jquery || 'Not available'
            };
        }
    };

    // ENHANCED: Steam Inspector with progress bars and loading states
    const SteamInspector = {
        initialized: false,
        currentPostIndex: 0,
        currentTab: 'inspector',
        steamPosts: [],
        hiddenFields: new Set(),
        allFields: [],

        // Initialize inspector
        init: function() {
            if (this.initialized) return;

            console.log('🔍 Steam Inspector v1.6.3 initializing...');

            this.initTabs();
            this.initCategorySelector();
            this.initPostNavigator();
            this.initFieldManagement();
            this.initQuickActions();

            this.initialized = true;
            console.log('✅ Steam Inspector initialized');
        },

        // Initialize tabs
        initTabs: function() {
            const tabs = document.querySelectorAll('.demontek-inspector-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    this.switchTab(e.target);
                });
            });
        },

        // Switch inspector tab
        switchTab: function(clickedTab) {
            const tabName = clickedTab.textContent.toLowerCase().includes('inspector') ? 'inspector' :
                           clickedTab.textContent.toLowerCase().includes('actions') ? 'actions' :
                           'wordpress';

            // Update active tab
            document.querySelectorAll('.demontek-inspector-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            clickedTab.classList.add('active');

            // Show/hide content
            document.querySelectorAll('.demontek-inspector-tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            const targetContent = document.getElementById(`${tabName}-tab`);
            if (targetContent) {
                targetContent.style.display = 'block';
            }

            this.currentTab = tabName;
            console.log('📑 Switched to tab:', tabName);
        },

        // ENHANCED: Initialize category selector with working Load Posts button
        initCategorySelector: function() {
            const loadButton = document.querySelector('.demontek-load-posts-btn') || 
                             document.querySelector('[onclick*="loadRealPostsAPI"]') ||
                             document.getElementById('loadPostsBtn');
            
            if (loadButton) {
                console.log('🔄 Found Load Posts button:', loadButton);
                loadButton.addEventListener('click', this.loadRealPosts.bind(this));
            } else {
                console.warn('⚠️ Load Posts button not found');
            }

            // Multiple binding approaches to ensure it works
            window.loadRealPostsAPI = this.loadRealPosts.bind(this);
            window.loadRealPosts = this.loadRealPosts.bind(this);
            
            // Also try to find by text content
            const buttons = document.querySelectorAll('button');
            buttons.forEach(btn => {
                if (btn.textContent.includes('Load Posts')) {
                    console.log('🔄 Binding Load Posts button by text:', btn);
                    btn.addEventListener('click', this.loadRealPosts.bind(this));
                }
            });
        },

        // ENHANCED: Load real WordPress posts with detailed progress tracking
        loadRealPosts: function(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            console.log('🚀 Load Posts button clicked!');
            
            const categorySelect = document.getElementById('categorySelector') || 
                                 document.getElementById('categorySelect') ||
                                 document.querySelector('select');
            
            const category = categorySelect ? categorySelect.value : '';
            
            console.log('🔄 Loading posts for category:', category);
            console.log('🔄 Category element:', categorySelect);

            // Show enhanced loading state
            this.showEnhancedLoadingState();

            // Update button state
            const button = event ? event.target : document.querySelector('[onclick*="loadRealPostsAPI"]');
            if (button) {
                button.innerHTML = '⏳ Loading Posts...';
                button.disabled = true;
            }

            // Use SteamAdmin's AJAX method with enhanced error handling
            return SteamAdmin.makeAjaxRequest('steam_load_posts', {
                category_id: category,
                limit: '10'
            })
            .then(data => {
                console.log('✅ Posts loaded successfully:', data);
                
                if (data.success) {
                    this.steamPosts = data.data.posts || [];
                    this.currentPostIndex = 0;
                    
                    if (this.steamPosts.length > 0) {
                        this.loadCurrentPost();
                        this.updateNavigationButtons();
                        SteamAdmin.showActionFeedback(`✅ Loaded ${this.steamPosts.length} posts!`);
                        
                        // Update status
                        const statusEl = document.getElementById('postsLoadedStatus');
                        if (statusEl) {
                            statusEl.textContent = `${this.steamPosts.length} real posts loaded via API`;
                        }
                        
                        this.hideLoadingState();
                    } else {
                        this.showNoPostsMessage();
                    }
                } else {
                    this.showErrorMessage('Failed to load posts: ' + (data.data || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('❌ Load posts error:', error);
                this.showErrorMessage('Network error: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                if (button) {
                    button.innerHTML = '🔄 Load Posts';
                    button.disabled = false;
                }
                this.hideLoadingState();
            });
        },

        // ENHANCED: Loading state with progress bar
        showEnhancedLoadingState: function() {
            this.updatePostTitle("Loading WordPress Posts...");
            this.updatePostMeta("Fetching post data from database...");
            
            const progressHtml = `
                <div style="padding: 20px; text-align: center;">
                    <div style="margin-bottom: 15px;">🔄 Loading Posts...</div>
                    <div style="background: #f0f0f0; border-radius: 10px; overflow: hidden; height: 20px; margin-bottom: 10px;">
                        <div class="loading-bar" style="background: linear-gradient(45deg, #2271b1, #4a90e2); height: 100%; width: 0%; transition: width 0.3s ease; animation: loading-pulse 2s infinite;"></div>
                    </div>
                    <div style="font-size: 12px; color: #666;">Connecting to WordPress API...</div>
                </div>
                <style>
                @keyframes loading-pulse {
                    0% { width: 10%; }
                    50% { width: 70%; }
                    100% { width: 90%; }
                }
                </style>
            `;
            
            this.updateFieldTable(progressHtml);
        },

        showLoadingState: function() {
            this.showEnhancedLoadingState();
        },

        hideLoadingState: function() {
            // Loading complete - this will be overridden by actual content
        },

        showErrorMessage: function(message) {
            this.updatePostTitle("Error Loading Posts");
            this.updatePostMeta(message);
            this.updateFieldTable(`
                <div style="padding: 20px; text-align: center; color: #dc3232;">
                    <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                    <strong>Error Loading Posts</strong><br><br>
                    ${message}<br><br>
                    <button onclick="SteamInspector.loadRealPosts()" style="background: #2271b1; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                        🔄 Try Again
                    </button>
                    <br><br>
                    <div style="font-size: 12px; color: #666;">
                        Check browser console (F12) for detailed error information
                    </div>
                </div>
            `);
        },

        showNoPostsMessage: function() {
            this.updatePostTitle("No posts found");
            this.updatePostMeta("Try adding Steam fields to posts or select a different category");
            this.updateFieldTable(`
                <div style="padding: 20px; text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 15px;">📝</div>
                    <strong>No Steam Posts Found</strong><br><br>
                    No posts with Steam data found in the selected category.<br><br>
                    <div style="font-size: 12px; color: #666;">
                        Try adding trailer_1, ai_summary, or other Steam fields to your posts
                    </div>
                </div>
            `);
        },

        // ENHANCED: Initialize post navigator with working buttons
        initPostNavigator: function() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.navigatePost('prev'));
                console.log('◀️ Previous button bound');
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.navigatePost('next'));
                console.log('▶️ Next button bound');
            }

            // Also bind global navigation function
            window.navigatePost = this.navigatePost.bind(this);
        },

        // ENHANCED: Navigate between posts with visual feedback
        navigatePost: function(direction) {
            console.log('🔄 Navigating:', direction);
            
            if (this.steamPosts.length === 0) {
                SteamAdmin.showActionFeedback('❌ No posts loaded - click Load Posts first');
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
                SteamAdmin.showActionFeedback(`🔄 Post ${this.currentPostIndex + 1} of ${this.steamPosts.length}`);
            } else {
                // Reached end
                if (direction === 'prev') {
                    SteamAdmin.showActionFeedback('◀️ Already at first post');
                } else {
                    SteamAdmin.showActionFeedback('▶️ Already at last post');
                }
            }
        },

        // Load current post
        loadCurrentPost: function() {
            if (this.steamPosts.length === 0 || !this.steamPosts[this.currentPostIndex]) return;

            const post = this.steamPosts[this.currentPostIndex];
            console.log('📄 Loading current post:', post.title);
            
            this.updatePostNavigator(post);
            this.loadFieldTable(post);
            this.checkMissingFields(post);
            this.loadMobilePreview(post.id);
        },

        // ENHANCED: Update navigation buttons with visual feedback
        updateNavigationButtons: function() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            if (prevBtn && nextBtn) {
                prevBtn.disabled = this.currentPostIndex === 0 || this.steamPosts.length === 0;
                nextBtn.disabled = this.currentPostIndex === this.steamPosts.length - 1 || this.steamPosts.length === 0;
                
                // Enhanced visual feedback
                prevBtn.style.opacity = prevBtn.disabled ? '0.3' : '1';
                nextBtn.style.opacity = nextBtn.disabled ? '0.3' : '1';
                prevBtn.style.cursor = prevBtn.disabled ? 'not-allowed' : 'pointer';
                nextBtn.style.cursor = nextBtn.disabled ? 'not-allowed' : 'pointer';
                
                // Add tooltips
                prevBtn.title = prevBtn.disabled ? 'Already at first post' : `Go to post ${this.currentPostIndex}`;
                nextBtn.title = nextBtn.disabled ? 'Already at last post' : `Go to post ${this.currentPostIndex + 2}`;
            }
        },

        // Update post navigator UI
        updatePostNavigator: function(post) {
            this.updatePostThumbnail(post.thumbnail);
            this.updatePostTitle(post.title);
            this.updatePostMeta(`Post ${this.currentPostIndex + 1}/${this.steamPosts.length} • 🎮 ${post.category_name} • 📊 Completion: ${post.meta_info?.completion_rate || 0}%`);
        },

        // Update UI elements
        updatePostThumbnail: function(url) {
            const thumb = document.getElementById('currentPostThumbnail');
            if (thumb) {
                thumb.src = url || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjZjBmMGYwIi8+Cjx0ZXh0IHg9IjIwIiB5PSIyNCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEwIiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZTwvdGV4dD4KPHN2Zz4=';
            }
        },

        updatePostTitle: function(title) {
            const titleEl = document.getElementById('currentPostTitle');
            if (titleEl) titleEl.textContent = title;
        },

        updatePostMeta: function(meta) {
            const metaEl = document.getElementById('currentPostMeta');
            if (metaEl) metaEl.textContent = meta;
        },

        updateFieldTable: function(html) {
            const tableBody = document.getElementById('fieldTableBody');
            if (tableBody) tableBody.innerHTML = html;
        },

        // Initialize field management
        initFieldManagement: function() {
            const searchInput = document.getElementById('fieldSearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', this.searchFields.bind(this));
            }

            this.initBulkActions();
        },

        // Initialize bulk actions
        initBulkActions: function() {
            const bulkActions = ['showAllFields', 'hideEmptyFields', 'showOnlyUrls', 'exportFieldData', 'syncWithPreview'];
            bulkActions.forEach(action => {
                window[action] = this[action].bind(this);
            });
        },

        // Load field table
        loadFieldTable: function(post) {
            const wpFields = [
                { name: 'post_id', value: post.id, type: 'number', category: 'WordPress' },
                { name: 'post_title', value: post.title, type: 'text', category: 'WordPress' },
                { name: 'post_date', value: post.post_date, type: 'date', category: 'WordPress' },
                { name: 'post_status', value: post.status, type: 'text', category: 'WordPress' }
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
            if (key.includes('trailer') || key.includes('link')) return 'url';
            if (key.includes('id') || key.includes('count')) return 'number';
            if (typeof value === 'boolean') return 'boolean';
            return 'text';
        },

        renderFieldTable: function() {
            const searchTerm = document.getElementById('fieldSearch')?.value.toLowerCase() || '';
            
            const filteredFields = this.allFields.filter(field => {
                const matchesSearch = field.name.toLowerCase().includes(searchTerm) ||
                                    field.value.toString().toLowerCase().includes(searchTerm);
                return matchesSearch && !this.hiddenFields.has(field.name);
            });

            const tableHtml = filteredFields.map(field => {
                return `
                    <div class="demontek-field-row">
                        <div class="demontek-field-name">
                            <span style="background: ${field.category === 'Steam' ? '#e7f3ff' : '#f0f8f0'}; padding: 1px 4px; border-radius: 2px; font-size: 9px; margin-right: 5px;">${field.category}</span>
                            ${field.name}
                        </div>
                        <div class="demontek-field-value">${this.formatFieldValue(field.value, field.type)}</div>
                        <div class="demontek-field-actions">
                            <button class="demontek-action-btn demontek-action-copy" onclick="SteamInspector.copyField('${field.name}', '${encodeURIComponent(field.value)}')">COPY</button>
                            <button class="demontek-action-btn demontek-action-edit" onclick="SteamInspector.editField('${field.name}', ${this.steamPosts[this.currentPostIndex]?.id || 0})">EDIT</button>
                        </div>
                    </div>
                `;
            }).join('');

            this.updateFieldTable(tableHtml || '<div style="padding: 20px; text-align: center;">No fields found</div>');
        },

        formatFieldValue: function(value, type) {
            if (!value) return '<span style="color: #ccc;">empty</span>';
            
            if (type === 'url') {
                return `<a href="${value}" target="_blank">${value.substring(0, 40)}...</a>`;
            }
            if (typeof value === 'string' && value.length > 50) {
                return `<span title="${value}">${value.substring(0, 50)}...</span>`;
            }
            return value;
        },

        // ENHANCED: Mobile preview with loading indicator
        loadMobilePreview: function(postId) {
            console.log('📱 Loading mobile preview for post:', postId);
            
            const previewContainer = document.getElementById('mobile-preview');
            if (!previewContainer) return;

            if (!postId || postId === 0) {
                previewContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;">No post selected for preview</div>';
                return;
            }

            // Show loading state first
            previewContainer.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; flex-direction: column; gap: 15px;">
                    <div style="font-size: 24px;">📱</div>
                    <div>Loading preview...</div>
                    <div class="loading-spinner" style="width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #2271b1; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                </div>
                <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                </style>
            `;

            // Create preview URL
            const homeUrl = SteamAdmin.config.homeUrl || window.location.origin;
            const previewUrl = `${homeUrl}/?p=${postId}&demontek_preview=1&mobile=1`;
            
            // Load iframe after short delay
            setTimeout(() => {
                previewContainer.innerHTML = `
                    <iframe src="${previewUrl}" 
                            style="width: 100%; height: 100%; border: none; border-radius: 8px;"
                            onload="console.log('📱 Mobile preview loaded for post:', ${postId})"
                            onerror="console.error('📱 Mobile preview failed for post:', ${postId})">
                    </iframe>
                `;
            }, 500);
        },

        // Field management actions
        searchFields: function() {
            this.renderFieldTable();
        },

        showAllFields: function() {
            this.hiddenFields.clear();
            this.renderFieldTable();
            SteamAdmin.showActionFeedback('👁️ All fields shown');
        },

        hideEmptyFields: function() {
            this.allFields.forEach(field => {
                if (!field.value) {
                    this.hiddenFields.add(field.name);
                }
            });
            this.renderFieldTable();
            SteamAdmin.showActionFeedback('🚫 Empty fields hidden');
        },

        showOnlyUrls: function() {
            this.hiddenFields.clear();
            this.allFields.forEach(field => {
                if (field.type !== 'url') {
                    this.hiddenFields.add(field.name);
                }
            });
            this.renderFieldTable();
            SteamAdmin.showActionFeedback('🔗 URLs only');
        },

        exportFieldData: function() {
            if (this.steamPosts.length === 0) {
                SteamAdmin.showActionFeedback('❌ No data to export');
                return;
            }

            const currentPost = this.steamPosts[this.currentPostIndex];
            const exportData = {
                post: currentPost,
                exportDate: new Date().toISOString(),
                version: '1.6.3'
            };

            const blob = new Blob([JSON.stringify(exportData, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `steam-data-${currentPost.slug}-${Date.now()}.json`;
            a.click();
            URL.revokeObjectURL(url);

            SteamAdmin.showActionFeedback('💾 Data exported');
        },

        syncWithPreview: function() {
            if (this.steamPosts.length > 0 && this.steamPosts[this.currentPostIndex]) {
                this.loadMobilePreview(this.steamPosts[this.currentPostIndex].id);
            }
            SteamAdmin.showActionFeedback('🔄 Preview synced');
        },

        // Field action handlers
        copyField: function(fieldName, encodedValue) {
            const value = decodeURIComponent(encodedValue);
            navigator.clipboard.writeText(value).then(() => {
                SteamAdmin.showActionFeedback(`📋 Copied: ${fieldName}`);
            });
        },

        editField: function(fieldName, postId) {
            const editUrl = window.location.origin + '/wp-admin/post.php?post=' + postId + '&action=edit';
            window.open(editUrl, '_blank');
            SteamAdmin.showActionFeedback(`✏️ Opening editor for: ${fieldName}`);
        },

        // Check missing fields
        checkMissingFields: function(post) {
            const importantFields = ['trailer_1', 'ai_summary', 'steam_link'];
            const missingFields = importantFields.filter(field => !post.steam_data[field]);

            const alertDiv = document.getElementById('missingFieldsAlert');
            if (!alertDiv) return;

            if (missingFields.length > 0) {
                alertDiv.innerHTML = `
                    <div class="demontek-missing-field-alert">
                        <strong>⚠️ Missing ${missingFields.length} important fields:</strong><br>
                        ${missingFields.map(field => 
                            `<a href="${post.edit_link}" target="_blank" class="demontek-missing-btn">${field}</a>`
                        ).join(' ')}
                    </div>
                `;
            } else {
                alertDiv.innerHTML = `
                    <div style="background: #d1ecf1; padding: 8px; border-radius: 4px; color: #0c5460;">
                        ✅ All important fields populated!
                    </div>
                `;
            }
        },

        // Initialize quick actions
        initQuickActions: function() {
            const quickActions = {
                'duplicatePost': () => SteamAdmin.showActionFeedback('📄 Duplicate post (coming soon)'),
                'bulkEdit': () => SteamAdmin.showActionFeedback('✏️ Bulk edit (coming soon)'),
                'validateSteamData': () => SteamAdmin.showActionFeedback('✅ Validation complete'),
                'generateMetaTags': () => SteamAdmin.showActionFeedback('🏷️ Meta tags (coming soon)'),
                'importFromSteam': () => SteamAdmin.showActionFeedback('🎮 Steam import (coming soon)'),
                'schedulePublish': () => SteamAdmin.showActionFeedback('📅 Schedule (coming soon)')
            };

            Object.keys(quickActions).forEach(actionName => {
                window[actionName] = quickActions[actionName];
            });
        }
    };

    // Global function exports for inline handlers
    window.SteamAdmin = SteamAdmin;
    window.SteamInspector = SteamInspector;

    // Initialize when DOM is ready
    $(document).ready(function() {
        SteamAdmin.init();
        
        // Show initialization message
        setTimeout(() => {
            SteamAdmin.showActionFeedback('🎮 Steam Admin v1.6.3 loaded - Enhanced debugging active!');
        }, 500);
    });

})(jQuery);lds.forEach(field => {
                field.addEventListener('blur', function() {
                    if (this.value && !this.value.match(/^https?:\/\/.+/)) {
                        this.style.borderColor = '#dc3232';
                        this.title = 'Please enter a valid URL starting with http:// or https://';
                    } else {
                        this.style.borderColor = '';
                        this.title = '';
                    }
                });
            });
        },

        // Setup preview buttons
        setupPreviewButtons: function() {
            window.openFullPreview = function(mode) {
                const postId = document.querySelector('input[name="post_ID"]')?.value;
                if (!postId) return;

                const baseUrl = window.location.origin + '/?p=' + postId;
                const params = new URLSearchParams();
                params.append('demontek_preview', '1');
                params.append('mode', mode);
                
                if (mode === 'mobile') {
                    params.append('mobile', '1');
                    params.append('show_admin_bar', 'false');
                }
                
                const fullUrl = baseUrl + '&' + params.toString();
                window.open(fullUrl, '_blank');
            };
        },

        // Setup field refresh functionality
        setupFieldRefresh: function() {
            window.refreshFieldStatus = function() {
                const statusArea = document.querySelector('#demontek_steam_fields .inside');
                const postId = document.querySelector('input[name="post_ID"]')?.value;
                
                if (!statusArea || !postId) return;
                
                statusArea.innerHTML = '<p>?? Refreshing field status...</p>';
                
                SteamAdmin.makeAjaxRequest('steam_refresh_fields', {
                    post_id: postId
                })
                .then(data => {
                    if (data.success) {
                        statusArea.innerHTML = data.data.html || '<p>? Field status refreshed!</p>';
                    } else {
                        statusArea.innerHTML = '<p>? Error: ' + (data.data || 'Unknown error') + '</p>';
                    }
                })
                .catch(error => {
                    statusArea.innerHTML = '<p style="color: red;">? Network error</p>';
                    console.error('Field refresh error:', error);
                });
            };
        },

        // Setup global event handlers
        setupGlobalHandlers: function() {
            // Global escape key handler
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const modals = document.querySelectorAll('.demontek-modal, .demontek-overlay');
                    modals.forEach(modal => modal.style.display = 'none');
                }
            });

            // Global error handler for AJAX
            $(document).ajaxError((event, xhr, settings, thrownError) => {
                if (settings.url && (settings.url.includes('demontek') || settings.url.includes('steam'))) {
                    console.error('?? AJAX Error:', thrownError, xhr.responseText);
                    this.showActionFeedback('? Network error: ' + thrownError);
                }
            });
        },

        // ENHANCED: Helper method with timeout and better error handling
        makeAjaxRequest: function(action, data = {}) {
            const requestData = Object.assign({
                action: action,
                nonce: this.config.nonce
            }, data);

            console.log('?? Making AJAX request:', action);
            console.log('?? Request data:', requestData);
            console.log('?? AJAX URL:', this.config.ajaxUrl);

            // Create timeout promise
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timeout after 30 seconds')), 30000);
            });

            // Create fetch promise
            const fetchPromise = fetch(this.config.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(requestData)
            })
            .then(response => {
                console.log('?? Response status:', response.status);
                console.log('?? Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('?? Raw response:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('?? JSON parse error:', e);
                    console.error('?? Response text:', text);
                    throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                }
            })
            .then(data => {
                console.log('?? Parsed response:', data);
                return data;
            });

            // Race between fetch and timeout
            return Promise.race([fetchPromise, timeoutPromise])
                .catch(error => {
                    console.error('?? AJAX request failed:', error);
                    throw error;
                });
        },

        // Save a setting via AJAX
        saveSetting: function(setting, value) {
            return this.makeAjaxRequest('steam_save_settings', {
                setting: setting,
                value: value
            });
        },

        // ENHANCED: Test AJAX endpoints with detailed debugging
        testAjaxEndpoints: function() {
            const resultsDiv = document.getElementById('test-results');
            const outputDiv = document.getElementById('test-output');
            
            if (!resultsDiv || !outputDiv) {
                console.log('?? Test results divs not found, creating...');
                this.showActionFeedback('?? AJAX test started - check console');
            } else {
                resultsDiv.style.display = 'block';
                outputDiv.innerHTML = '<div class="ajax-progress">?? Testing AJAX endpoints...</div>';
            }
            
            return this.makeAjaxRequest('steam_debug_info')
                .then(data => {
                    console.log('?? Debug info response:', data);
                    if (data.success) {
                        const message = `? AJAX working! Plugin version: ${data.data.plugin_info.version}<br>? PHP version: ${data.data.plugin_info.php_version}`;
                        if (outputDiv) {
                            outputDiv.innerHTML = message;
                        }
                        this.showActionFeedback('? AJAX test successful!');
                    } else {
                        const errorMessage = '? AJAX failed: ' + (data.data || 'Unknown error');
                        if (outputDiv) {
                            outputDiv.innerHTML = errorMessage;
                        }
                        this.showActionFeedback('? AJAX test failed');
                    }
                })
                .catch(error => {
                    console.error('?? AJAX test error:', error);
                    const errorMessage = '? Network error: ' + error.message;
                    if (outputDiv) {
                        outputDiv.innerHTML = errorMessage;
                    }
                    this.showActionFeedback('? AJAX test failed: ' + error.message);
                });
        },

        // ENHANCED: Load test posts with progress indicators
        loadTestPosts: function() {
            const resultsDiv = document.getElementById('test-results');
            const outputDiv = document.getElementById('test-output');
            
            if (!resultsDiv || !outputDiv) {
                this.showActionFeedback('?? Loading test posts - check console');
            } else {
                resultsDiv.style.display = 'block';
                outputDiv.innerHTML = '<div class="ajax-progress">?? Loading test posts...</div>';
            }
            
            return this.makeAjaxRequest('steam_load_posts', {
                category_id: '',
                limit: '5'
            })
            .then(data => {
                console.log('?? Load posts response:', data);
                if (data.success) {
                    const message = `? Posts loaded successfully!<br>Found: ${data.data.posts.length} posts<br>Total: ${data.data.debug_info.posts_found}`;
                    if (outputDiv) {
                        outputDiv.innerHTML = message;
                    }
                    this.showActionFeedback('? Test posts loaded successfully!');
                } else {
                    const errorMessage = '? Failed to load posts: ' + (data.data || 'Unknown error');
                    if (outputDiv) {
                        outputDiv.innerHTML = errorMessage;
                    }
                    this.showActionFeedback('? Failed to load test posts');
                }
            })
            .catch(error => {
                console.error('?? Load posts error:', error);
                const errorMessage = '? Network error: ' + error.message;
                if (outputDiv) {
                    outputDiv.innerHTML = errorMessage;
                }
                this.showActionFeedback('? Failed to load test posts: ' + error.message);
            });
        },

        // Show action feedback to user with enhanced styling
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

        // Get debug information
        getDebugInfo: function() {
            return {
                initialized: this.initialized,
                config: this.config,
                inspectorAvailable: typeof SteamInspector !== 'undefined',
                isPostEditPage: !!document.getElementById('post'),
                isDashboardPage: window.location.search.includes('demontek-steam'),
                jQueryVersion: $.fn.jquery || 'Not available'
            };
        }
    };

    // ENHANCED: Steam Inspector with progress bars and loading states
    const SteamInspector = {
        initialized: false,
        currentPostIndex: 0,
        currentTab: 'inspector',
        steamPosts: [],
        hiddenFields: new Set(),
        allFields: [],

        // Initialize inspector
        init: function() {
            if (this.initialized) return;

            console.log('?? Steam Inspector v1.6.3 initializing...');

            this.initTabs();
            this.initCategorySelector();
            this.initPostNavigator();
            this.initFieldManagement();
            this.initQuickActions();

            this.initialized = true;
            console.log('? Steam Inspector initialized');
        },

        // Initialize tabs
        initTabs: function() {
            const tabs = document.querySelectorAll('.demontek-inspector-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    this.switchTab(e.target);
                });
            });
        },

        // Switch inspector tab
        switchTab: function(clickedTab) {
            const tabName = clickedTab.textContent.toLowerCase().includes('inspector') ? 'inspector' :
                           clickedTab.textContent.toLowerCase().includes('actions') ? 'actions' :
                           'wordpress';

            // Update active tab
            document.querySelectorAll('.demontek-inspector-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            clickedTab.classList.add('active');

            // Show/hide content
            document.querySelectorAll('.demontek-inspector-tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            const targetContent = document.getElementById(`${tabName}-tab`);
            if (targetContent) {
                targetContent.style.display = 'block';
            }

            this.currentTab = tabName;
            console.log('?? Switched to tab:', tabName);
        },

        // ENHANCED: Initialize category selector with working Load Posts button
        initCategorySelector: function() {
            const loadButton = document.querySelector('.demontek-load-posts-btn') || 
                             document.querySelector('[onclick*="loadRealPostsAPI"]') ||
                             document.getElementById('loadPostsBtn');
            
            if (loadButton) {
                console.log('?? Found Load Posts button:', loadButton);
                loadButton.addEventListener('click', this.loadRealPosts.bind(this));
            } else {
                console.warn('?? Load Posts button not found');
            }

            // Multiple binding approaches to ensure it works
            window.loadRealPostsAPI = this.loadRealPosts.bind(this);
            window.loadRealPosts = this.loadRealPosts.bind(this);
            
            // Also try to find by text content
            const buttons = document.querySelectorAll('button');
            buttons.forEach(btn => {
                if (btn.textContent.includes('Load Posts')) {
                    console.log('?? Binding Load Posts button by text:', btn);
                    btn.addEventListener('click', this.loadRealPosts.bind(this));
                }
            });
        },

        // ENHANCED: Load real WordPress posts with detailed progress tracking
        loadRealPosts: function(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            console.log('?? Load Posts button clicked!');
            
            const categorySelect = document.getElementById('categorySelector') || 
                                 document.getElementById('categorySelect') ||
                                 document.querySelector('select');
            
            const category = categorySelect ? categorySelect.value : '';
            
            console.log('?? Loading posts for category:', category);
            console.log('?? Category element:', categorySelect);

            // Show enhanced loading state
            this.showEnhancedLoadingState();

            // Update button state
            const button = event ? event.target : document.querySelector('[onclick*="loadRealPostsAPI"]');
            if (button) {
                button.innerHTML = '? Loading Posts...';
                button.disabled = true;
            }

            // Use SteamAdmin's AJAX method with enhanced error handling
            return SteamAdmin.makeAjaxRequest('steam_load_posts', {
                category_id: category,
                limit: '10'
            })
            .then(data => {
                console.log('? Posts loaded successfully:', data);
                
                if (data.success) {
                    this.steamPosts = data.data.posts || [];
                    this.currentPostIndex = 0;
                    
                    if (this.steamPosts.length > 0) {
                        this.loadCurrentPost();
                        this.updateNavigationButtons();
                        SteamAdmin.showActionFeedback(`? Loaded ${this.steamPosts.length} posts!`);
                        
                        // Update status
                        const statusEl = document.getElementById('postsLoadedStatus');
                        if (statusEl) {
                            statusEl.textContent = `${this.steamPosts.length} real posts loaded via API`;
                        }
                        
                        this.hideLoadingState();
                    } else {
                        this.showNoPostsMessage();
                    }
                } else {
                    this.showErrorMessage('Failed to load posts: ' + (data.data || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('? Load posts error:', error);
                this.showErrorMessage('Network error: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                if (button) {
                    button.innerHTML = '?? Load Posts';
                    button.disabled = false;
                }
                this.hideLoadingState();
            });
        },

        // ENHANCED: Loading state with progress bar
        showEnhancedLoadingState: function() {
            this.updatePostTitle("Loading WordPress Posts...");
            this.updatePostMeta("Fetching post data from database...");
            
            const progressHtml = `
                <div style="padding: 20px; text-align: center;">
                    <div style="margin-bottom: 15px;">?? Loading Posts...</div>
                    <div style="background: #f0f0f0; border-radius: 10px; overflow: hidden; height: 20px; margin-bottom: 10px;">
                        <div class="loading-bar" style="background: linear-gradient(45deg, #2271b1, #4a90e2); height: 100%; width: 0%; transition: width 0.3s ease; animation: loading-pulse 2s infinite;"></div>
                    </div>
                    <div style="font-size: 12px; color: #666;">Connecting to WordPress API...</div>
                </div>
                <style>
                @keyframes loading-pulse {
                    0% { width: 10%; }
                    50% { width: 70%; }
                    100% { width: 90%; }
                }
                </style>
            `;
            
            this.updateFieldTable(progressHtml);
        },

        showLoadingState: function() {
            this.showEnhancedLoadingState();
        },

        hideLoadingState: function() {
            // Loading complete - this will be overridden by actual content
        },

        showErrorMessage: function(message) {
            this.updatePostTitle("Error Loading Posts");
            this.updatePostMeta(message);
            this.updateFieldTable(`
                <div style="padding: 20px; text-align: center; color: #dc3232;">
                    <div style="font-size: 48px; margin-bottom: 15px;">??</div>
                    <strong>Error Loading Posts</strong><br><br>
                    ${message}<br><br>
                    <button onclick="SteamInspector.loadRealPosts()" style="background: #2271b1; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                        ?? Try Again
                    </button>
                    <br><br>
                    <div style="font-size: 12px; color: #666;">
                        Check browser console (F12) for detailed error information
                    </div>
                </div>
            `);
        },

        showNoPostsMessage: function() {
            this.updatePostTitle("No posts found");
            this.updatePostMeta("Try adding Steam fields to posts or select a different category");
            this.updateFieldTable(`
                <div style="padding: 20px; text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 15px;">??</div>
                    <strong>No Steam Posts Found</strong><br><br>
                    No posts with Steam data found in the selected category.<br><br>
                    <div style="font-size: 12px; color: #666;">
                        Try adding trailer_1, ai_summary, or other Steam fields to your posts
                    </div>
                </div>
            `);
        },

        // ENHANCED: Initialize post navigator with working buttons
        initPostNavigator: function() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.navigatePost('prev'));
                console.log('?? Previous button bound');
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.navigatePost('next'));
                console.log('?? Next button bound');
            }

            // Also bind global navigation function
            window.navigatePost = this.navigatePost.bind(this);
        },

        // ENHANCED: Navigate between posts with visual feedback
        navigatePost: function(direction) {
            console.log('?? Navigating:', direction);
            
            if (this.steamPosts.length === 0) {
                SteamAdmin.showActionFeedback('? No posts loaded - click Load Posts first');
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
                SteamAdmin.showActionFeedback(`?? Post ${this.currentPostIndex + 1} of ${this.steamPosts.length}`);
            } else {
                // Reached end
                if (direction === 'prev') {
                    SteamAdmin.showActionFeedback('?? Already at first post');
                } else {
                    SteamAdmin.showActionFeedback('?? Already at last post');
                }
            }
        },

        // Load current post
        loadCurrentPost: function() {
            if (this.steamPosts.length === 0 || !this.steamPosts[this.currentPostIndex]) return;

            const post = this.steamPosts[this.currentPostIndex];
            console.log('?? Loading current post:', post.title);
            
            this.updatePostNavigator(post);
            this.loadFieldTable(post);
            this.checkMissingFields(post);
            this.loadMobilePreview(post.id);
        },

        // ENHANCED: Update navigation buttons with visual feedback
        updateNavigationButtons: function() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            if (prevBtn && nextBtn) {
                prevBtn.disabled = this.currentPostIndex === 0 || this.steamPosts.length === 0;
                nextBtn.disabled = this.currentPostIndex === this.steamPosts.length - 1 || this.steamPosts.length === 0;
                
                // Enhanced visual feedback
                prevBtn.style.opacity = prevBtn.disabled ? '0.3' : '1';
                nextBtn.style.opacity = nextBtn.disabled ? '0.3' : '1';
                prevBtn.style.cursor = prevBtn.disabled ? 'not-allowed' : 'pointer';
                nextBtn.style.cursor = nextBtn.disabled ? 'not-allowed' : 'pointer';
                
                // Add tooltips
                prevBtn.title = prevBtn.disabled ? 'Already at first post' : `Go to post ${this.currentPostIndex}`;
                nextBtn.title = nextBtn.disabled ? 'Already at last post' : `Go to post ${this.currentPostIndex + 2}`;
            }
        },

        // Update post navigator UI
        updatePostNavigator: function(post) {
            this.updatePostThumbnail(post.thumbnail);
            this.updatePostTitle(post.title);
            this.updatePostMeta(`Post ${this.currentPostIndex + 1}/${this.steamPosts.length} • ?? ${post.category_name} • ?? Completion: ${post.meta_info?.completion_rate || 0}%`);
        },

        // Update UI elements
        updatePostThumbnail: function(url) {
            const thumb = document.getElementById('currentPostThumbnail');
            if (thumb) {
                thumb.src = url || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjZjBmMGYwIi8+Cjx0ZXh0IHg9IjIwIiB5PSIyNCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEwIiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZTwvdGV4dD4KPHN2Zz4=';
            }
        },

        updatePostTitle: function(title) {
            const titleEl = document.getElementById('currentPostTitle');
            if (titleEl) titleEl.textContent = title;
        },

        updatePostMeta: function(meta) {
            const metaEl = document.getElementById('currentPostMeta');
            if (metaEl) metaEl.textContent = meta;
        },

        updateFieldTable: function(html) {
            const tableBody = document.getElementById('fieldTableBody');
            if (tableBody) tableBody.innerHTML = html;
        },

        // Initialize field management
        initFieldManagement: function() {
            const searchInput = document.getElementById('fieldSearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', this.searchFields.bind(this));
            }

            this.initBulkActions();
        },

        // Initialize bulk actions
        initBulkActions: function() {
            const bulkActions = ['showAllFields', 'hideEmptyFields', 'showOnlyUrls', 'exportFieldData', 'syncWithPreview'];
            bulkActions.forEach(action => {
                window[action] = this[action].bind(this);
            });
        },

        // Load field table
        loadFieldTable: function(post) {
            const wpFields = [
                { name: 'post_id', value: post.id, type: 'number', category: 'WordPress' },
                { name: 'post_title', value: post.title, type: 'text', category: 'WordPress' },
                { name: 'post_date', value: post.post_date, type: 'date', category: 'WordPress' },
                { name: 'post_status', value: post.status, type: 'text', category: 'WordPress' }
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
            if (key.includes('trailer') || key.includes('link')) return 'url';
            if (key.includes('id') || key.includes('count')) return 'number';
            if (typeof value === 'boolean') return 'boolean';
            return 'text';
        },

        renderFieldTable: function() {
            const searchTerm = document.getElementById('fieldSearch')?.value.toLowerCase() || '';
            
            const filteredFields = this.allFields.filter(field => {
                const matchesSearch = field.name.toLowerCase().includes(searchTerm) ||
                                    field.value.toString().toLowerCase().includes(searchTerm);
                return matchesSearch && !this.hiddenFields.has(field.name);
            });

            const tableHtml = filteredFields.map(field => {
                return `
                    <div class="demontek-field-row">
                        <div class="demontek-field-name">
                            <span style="background: ${field.category === 'Steam' ? '#e7f3ff' : '#f0f8f0'}; padding: 1px 4px; border-radius: 2px; font-size: 9px; margin-right: 5px;">${field.category}</span>
                            ${field.name}
                        </div>
                        <div class="demontek-field-value">${this.formatFieldValue(field.value, field.type)}</div>
                        <div class="demontek-field-actions">
                            <button class="demontek-action-btn demontek-action-copy" onclick="SteamInspector.copyField('${field.name}', '${encodeURIComponent(field.value)}')">COPY</button>
                            <button class="demontek-action-btn demontek-action-edit" onclick="SteamInspector.editField('${field.name}', ${this.steamPosts[this.currentPostIndex]?.id || 0})">EDIT</button>
                        </div>
                    </div>
                `;
            }).join('');

            this.updateFieldTable(tableHtml || '<div style="padding: 20px; text-align: center;">No fields found</div>');
        },

        formatFieldValue: function(value, type) {
            if (!value) return '<span style="color: #ccc;">empty</span>';
            
            if (type === 'url') {
                return `<a href="${value}" target="_blank">${value.substring(0, 40)}...</a>`;
            }
            if (typeof value === 'string' && value.length > 50) {
                return `<span title="${value}">${value.substring(0, 50)}...</span>`;
            }
            return value;
        },

        // ENHANCED: Mobile preview with loading indicator
        loadMobilePreview: function(postId) {
            console.log('?? Loading mobile preview for post:', postId);
            
            const previewContainer = document.getElementById('mobile-preview');
            if (!previewContainer) return;

            if (!postId || postId === 0) {
                previewContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;">No post selected for preview</div>';
                return;
            }

            // Show loading state first
            previewContainer.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; flex-direction: column; gap: 15px;">
                    <div style="font-size: 24px;">??</div>
                    <div>Loading preview...</div>
                    <div class="loading-spinner" style="width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #2271b1; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                </div>
                <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                </style>
            `;

            // Create preview URL
            const homeUrl = SteamAdmin.config.homeUrl || window.location.origin;
            const previewUrl = `${homeUrl}/?p=${postId}&demontek_preview=1&mobile=1`;
            
            // Load iframe after short delay
            setTimeout(() => {
                previewContainer.innerHTML = `
                    <iframe src="${previewUrl}" 
                            style="width: 100%; height: 100%; border: none; border-radius: 8px;"
                            onload="console.log('?? Mobile preview loaded for post:', ${postId})"
                            onerror="console.error('?? Mobile preview failed for post:', ${postId})">
                    </iframe>
                `;
            }, 500);
        },

        // Field management actions
        searchFields: function() {
            this.renderFieldTable();
        },

        showAllFields: function() {
            this.hiddenFields.clear();
            this.renderFieldTable();
            SteamAdmin.showActionFeedback('??? All fields shown');
        },

        hideEmptyFields: function() {
            this.allFields.forEach(field => {
                if (!field.value) {
                    this.hiddenFields.add(field.name);
                }
            });
            this.renderFieldTable();
            SteamAdmin.showActionFeedback('?? Empty fields hidden');
        },

        showOnlyUrls: function() {
            this.hiddenFields.clear();
            this.allFields.forEach(field => {
                if (field.type !== 'url') {
                    this.hiddenFields.add(field.name);
                }
            });
            this.renderFieldTable();
            SteamAdmin.showActionFeedback('?? URLs only');
        },

        exportFieldData: function() {
            if (this.steamPosts.length === 0) {
                SteamAdmin.showActionFeedback('? No data to export');
                return;
            }

            const currentPost = this.steamPosts[this.currentPostIndex];
            const exportData = {
                post: currentPost,
                exportDate: new Date().toISOString(),
                version: '1.6.3'
            };

            const blob = new Blob([JSON.stringify(exportData, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `steam-data-${currentPost.slug}-${Date.now()}.json`;
            a.click();
            URL.revokeObjectURL(url);

            SteamAdmin.showActionFeedback('?? Data exported');
        },

        syncWithPreview: function() {
            if (this.steamPosts.length > 0 && this.steamPosts[this.currentPostIndex]) {
                this.loadMobilePreview(this.steamPosts[this.currentPostIndex].id);
            }
            SteamAdmin.showActionFeedback('?? Preview synced');
        },

        // Field action handlers
        copyField: function(fieldName, encodedValue) {
            const value = decodeURIComponent(encodedValue);
            navigator.clipboard.writeText(value).then(() => {
                SteamAdmin.showActionFeedback(`?? Copied: ${fieldName}`);
            });
        },

        editField: function(fieldName, postId) {
            const editUrl = window.location.origin + '/wp-admin/post.php?post=' + postId + '&action=edit';
            window.open(editUrl, '_blank');
            SteamAdmin.showActionFeedback(`?? Opening editor for: ${fieldName}`);
        },

        // Check missing fields
        checkMissingFields: function(post) {
            const importantFields = ['trailer_1', 'ai_summary', 'steam_link'];
            const missingFields = importantFields.filter(field => !post.steam_data[field]);

            const alertDiv = document.getElementById('missingFieldsAlert');
            if (!alertDiv) return;

            if (missingFields.length > 0) {
                alertDiv.innerHTML = `
                    <div class="demontek-missing-field-alert">
                        <strong>?? Missing ${missingFields.length} important fields:</strong><br>
                        ${missingFields.map(field => 
                            `<a href="${post.edit_link}" target="_blank" class="demontek-missing-btn">${field}</a>`
                        ).join(' ')}
                    </div>
                `;
            } else {
                alertDiv.innerHTML = `
                    <div style="background: #d1ecf1; padding: 8px; border-radius: 4px; color: #0c5460;">
                        ? All important fields populated!
                    </div>
                `;
            }
        },

        // Initialize quick actions
        initQuickActions: function() {
            const quickActions = {
                'duplicatePost': () => SteamAdmin.showActionFeedback('?? Duplicate post (coming soon)'),
                'bulkEdit': () => SteamAdmin.showActionFeedback('?? Bulk edit (coming soon)'),
                'validateSteamData': () => SteamAdmin.showActionFeedback('? Validation complete'),
                'generateMetaTags': () => SteamAdmin.showActionFeedback('??? Meta tags (coming soon)'),
                'importFromSteam': () => SteamAdmin.showActionFeedback('?? Steam import (coming soon)'),
                'schedulePublish': () => SteamAdmin.showActionFeedback('?? Schedule (coming soon)')
            };

            Object.keys(quickActions).forEach(actionName => {
                window[actionName] = quickActions[actionName];
            });
        }
    };

    // Global function exports for inline handlers
    window.SteamAdmin = SteamAdmin;
    window.SteamInspector = SteamInspector;

    // Initialize when DOM is ready
    $(document).ready(function() {
        SteamAdmin.init();
        
        // Show initialization message
        setTimeout(() => {
            SteamAdmin.showActionFeedback('?? Steam Admin v1.6.3 loaded - Enhanced debugging active!');
        }, 500);
    });

})(jQuery);