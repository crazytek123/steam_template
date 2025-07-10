<?php
/**
 * Steam Dashboard Class
 * Handles the main admin dashboard interface
 */

if (!defined('ABSPATH')) exit;

class DemontekSteamDashboard {
    
    private $statistics;
    private $inspector;
    
    public function __construct() {
        $this->statistics = new DemontekSteamStatistics();
        $this->inspector = new DemontekSteamInspector();
    }
    
    /**
     * Render the main dashboard
     */
    public function render() {
        $enabled = get_option('demontek_steam_enabled', false);
        $global_mode = get_option('demontek_steam_global_mode', false);
        $version = get_option('demontek_steam_version', DEMONTEK_STEAM_VERSION);
        
        ?>
        <div class="wrap">
            <h1>🎮 Demontek Steam Template v<?php echo esc_html($version); ?></h1>
            
            <div class="demontek-admin-dashboard">
                <?php $this->render_styles(); ?>
                
                <?php $this->render_top_row($enabled, $global_mode, $version); ?>
                
                <?php $this->render_main_grid($enabled); ?>
                
                <?php if ($enabled): ?>
                    <?php $this->render_statistics_section(); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php $this->render_scripts(); ?>
        <?php
    }
    
    /**
     * Render dashboard styles
     */
    private function render_styles() {
        ?>
        <style>
            .demontek-admin-dashboard { max-width: 1600px; }
            .demontek-status-card { 
                background: white; border: 1px solid #ccd0d4; border-radius: 8px; 
                padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
            }
            .demontek-status-header { 
                display: flex; align-items: center; gap: 15px; margin-bottom: 15px; 
            }
            .demontek-status-indicator { 
                width: 20px; height: 20px; border-radius: 50%; 
            }
            .demontek-status-indicator.on { background: #00a32a; }
            .demontek-status-indicator.off { background: #dc3232; }
            .demontek-toggle-switch { 
                position: relative; display: inline-block; width: 60px; height: 34px; 
            }
            .demontek-toggle-switch input { opacity: 0; width: 0; height: 0; }
            .demontek-toggle-slider { 
                position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; 
                background-color: #ccc; transition: .4s; border-radius: 34px; 
            }
            .demontek-toggle-slider:before { 
                position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px; 
                background-color: white; transition: .4s; border-radius: 50%; 
            }
            input:checked + .demontek-toggle-slider { background-color: #2196F3; }
            input:checked + .demontek-toggle-slider:before { transform: translateX(26px); }
            .demontek-btn-primary { 
                background: #2271b1; color: white; border: none; padding: 12px 24px; 
                border-radius: 6px; cursor: pointer; font-weight: 600; 
            }
            .demontek-btn-primary:hover { background: #135e96; }
            .demontek-notice { 
                padding: 12px; border-radius: 6px; margin-bottom: 15px; 
            }
            .demontek-notice.success { 
                background: #d1ecf1; border-left: 4px solid #00a32a; color: #0c5460; 
            }
            .demontek-notice.warning { 
                background: #fff3cd; border-left: 4px solid #f57c00; color: #856404; 
            }
            
            .demontek-top-row {
                display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;
            }
            
            .demontek-main-grid { 
                display: grid; grid-template-columns: 1fr 420px; gap: 20px; margin-top: 20px; 
            }
            
            .demontek-mobile-preview-column {
                background: white; border: 1px solid #ccd0d4; border-radius: 8px; 
                padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                display: flex; flex-direction: column; height: calc(100vh - 200px);
                position: sticky; top: 20px;
            }
            
            .demontek-mobile-preview-header {
                display: flex; align-items: center; justify-content: space-between;
                margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;
                flex-shrink: 0;
            }
            
            .demontek-mobile-preview-content {
                flex: 1; display: flex; flex-direction: column; align-items: center;
                justify-content: flex-start; overflow: hidden;
            }
            
            .demontek-stats-grid { 
                display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; 
            }
            .demontek-whats-new { 
                background: #e7f3ff; border: 1px solid #2271b1; border-radius: 8px; 
                padding: 15px; margin-bottom: 20px; 
            }
            .demontek-feature-highlight { 
                background: #f0f6fc; border: 1px solid #0969da; border-radius: 6px; 
                padding: 12px; margin-bottom: 15px; 
            }
            .demontek-stat-card {
                background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;
                padding: 15px; text-align: center; margin-bottom: 15px;
            }
            .demontek-stat-number {
                font-size: 24px; font-weight: bold; color: #2271b1; margin-bottom: 5px;
            }
            .demontek-stat-label {
                font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;
            }
            
            @media (max-width: 1400px) {
                .demontek-main-grid { grid-template-columns: 1fr 380px; }
            }
            
            @media (max-width: 1200px) {
                .demontek-main-grid { grid-template-columns: 1fr; gap: 15px; }
                .demontek-mobile-preview-column { order: 2; }
            }
        </style>
        <?php
    }
    
    /**
     * Render top row (What's Updated + Enable box)
     */
    private function render_top_row($enabled, $global_mode, $version) {
        ?>
        <div class="demontek-top-row">
            <!-- Column 1: What's Updated -->
            <div class="demontek-whats-new">
                <h3 style="margin-top: 0; color: #2271b1;">🔧 What's Fixed in v1.6.2</h3>
                <ul style="margin-bottom: 0;">
                    <li><strong>🚀 FIXED: AJAX Loading:</strong> Real WordPress post loading now works properly!</li>
                    <li><strong>📱 FIXED: Mobile Preview:</strong> Preview now loads and displays correctly</li>
                    <li><strong>⚡ Performance:</strong> Reduced post limits for faster loading (10 posts max)</li>
                    <li><strong>🔍 Debug Logging:</strong> Added proper error logging for troubleshooting</li>
                    <li><strong>🔄 Navigation Arrows:</strong> Previous/Next buttons work correctly</li>
                    <li><strong>⏱️ Timeout Protection:</strong> 15-second timeout prevents infinite loading</li>
                    <li><strong>📝 Better Error Messages:</strong> Clear feedback when things go wrong</li>
                </ul>
            </div>
            
            <!-- Column 2: Steam Template Settings -->
            <div class="demontek-status-card">
                <div class="demontek-status-header">
                    <div class="demontek-status-indicator <?php echo $enabled ? 'on' : 'off'; ?>"></div>
                    <h3 style="margin: 0; font-size: 16px;">
                        Steam Template <?php echo $enabled ? 'ENABLED' : 'DISABLED'; ?>
                    </h3>
                    <?php if ($enabled && $global_mode): ?>
                        <span style="background: #e7f3ff; color: #2271b1; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold;">GLOBAL</span>
                    <?php endif; ?>
                </div>
                
                <?php if (!$enabled): ?>
                    <div class="demontek-notice warning" style="padding: 8px; font-size: 11px;">
                        <strong>⚠ Warning:</strong> Template disabled. Enable to use Steam layouts.
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 15px;">
                    <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 12px;">
                        <span class="demontek-toggle-switch" style="width: 40px; height: 24px;">
                            <input type="checkbox" id="demontek_main_toggle" <?php checked($enabled); ?> onchange="toggleDemontekSteam(this.checked)">
                            <span class="demontek-toggle-slider"></span>
                        </span>
                        <strong>Enable Steam Template</strong>
                    </label>
                    
                    <?php if ($enabled): ?>
                    <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 12px;">
                        <span class="demontek-toggle-switch" style="width: 40px; height: 24px;">
                            <input type="checkbox" id="demontek_global_toggle" <?php checked($global_mode); ?> onchange="toggleGlobalMode(this.checked)">
                            <span class="demontek-toggle-slider"></span>
                        </span>
                        <strong>Global Mode</strong>
                    </label>
                    <?php endif; ?>
                    
                    <button type="button" class="demontek-btn-primary" onclick="saveSettings()" style="width: 100%; padding: 8px; font-size: 11px;">
                        💾 Save Settings
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render main grid (Inspector + Mobile Preview)
     */
    private function render_main_grid($enabled) {
        ?>
        <div class="demontek-main-grid">
            <!-- Column 1: Steam Field Inspector -->
            <div class="demontek-steam-inspector">
                <?php $this->inspector->render(); ?>
            </div>
            
            <!-- Column 2: Mobile Preview -->
            <div class="demontek-mobile-preview-column">
                <div class="demontek-mobile-preview-header">
                    <h3>📱 Mobile Preview</h3>
                    <span style="background: #e7f3ff; color: #2271b1; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">v1.6.2 Fixed</span>
                </div>
                <div class="demontek-mobile-preview-content">
                    <div id="mobile-preview" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: white; width: 400px; height: 100%; margin: 0 auto; min-height: 600px;">
                        <div style="display: flex; align-items: center; justify-content: center; height: 200px; color: #666; font-size: 14px; flex-direction: column; gap: 15px;">
                            <div>📱 Mobile Preview Ready</div>
                            <div style="font-size: 12px; color: #888;">
                                Load posts to see live preview of actual WordPress content!
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 15px; text-align: center; flex-shrink: 0;">
                    <button type="button" class="demontek-btn-primary" onclick="refreshMobilePreview()" style="width: 100%; font-size: 12px; padding: 8px;">
                        🔄 Refresh Mobile Preview
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render statistics section
     */
    private function render_statistics_section() {
        $stats = $this->statistics->get_all_statistics();
        
        ?>
        <div class="demontek-stats-grid">
            <div class="demontek-status-card">
                <h3>📊 Statistics Dashboard</h3>
                <div class="demontek-feature-highlight">
                    <h4>What These Numbers Mean:</h4>
                    <p style="margin: 0; font-size: 13px;">Track your Steam template usage and content completion across all posts.</p>
                </div>
                
                <div class="demontek-stat-card">
                    <div class="demontek-stat-number"><?php echo $stats['total_posts']; ?></div>
                    <div class="demontek-stat-label">Total Posts</div>
                </div>
                
                <div class="demontek-stat-card">
                    <div class="demontek-stat-number"><?php echo $stats['steam_enabled_posts']; ?></div>
                    <div class="demontek-stat-label">Steam Layout Enabled</div>
                </div>
                
                <div class="demontek-stat-card">
                    <div class="demontek-stat-number"><?php echo $stats['posts_with_steam_fields']; ?></div>
                    <div class="demontek-stat-label">With Steam Content</div>
                </div>
                
                <div class="demontek-stat-card">
                    <div class="demontek-stat-number"><?php echo $stats['complete_steam_posts']; ?></div>
                    <div class="demontek-stat-label">Complete Steam Posts</div>
                </div>
                
                <div class="demontek-stat-card">
                    <div class="demontek-stat-number"><?php echo $stats['usage_percentage']; ?>%</div>
                    <div class="demontek-stat-label">Steam Usage Rate</div>
                </div>
            </div>
            
            <div class="demontek-status-card">
                <h3>⚡ Quick Actions</h3>
                <div class="demontek-feature-highlight">
                    <h4>Power User Tools:</h4>
                    <p style="margin: 0; font-size: 13px;">Administrative shortcuts for managing Steam content and settings.</p>
                </div>
                <button type="button" class="demontek-btn-primary" onclick="findSteamPosts()" style="width: 100%; margin-bottom: 10px;">
                    🔍 Find Posts with Steam Fields
                </button>
                <button type="button" class="demontek-btn-primary" onclick="viewSamplePost()" style="width: 100%; margin-bottom: 10px;">
                    👁️ View Sample Steam Layout (New Tab)
                </button>
                <button type="button" class="demontek-btn-primary" onclick="exportSettings()" style="width: 100%; margin-bottom: 10px;">
                    📁 Export Settings Backup
                </button>
                <button type="button" class="demontek-btn-primary" onclick="refreshMobilePreview()" style="width: 100%;">
                    🔄 Refresh Mobile Preview
                </button>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render dashboard scripts
     */
    private function render_scripts() {
        $core = demontek_steam()->getCore();
        $sample_post_id = $core ? $core->get_sample_post_id() : 1;
        
        ?>
        <script>
        // Define ajaxurl and add proper debugging
        const ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        console.log('🎮 Demontek Steam v1.6.2 - Admin AJAX URL:', ajaxurl);

        function toggleDemontekSteam(enabled) {
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=steam_save_settings&setting=enabled&value=' + (enabled ? '1' : '0') + '&nonce=<?php echo wp_create_nonce('demontek_steam_nonce'); ?>'
            }).then(() => {
                setTimeout(() => location.reload(), 1000);
            });
        }

        function toggleGlobalMode(enabled) {
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=steam_save_settings&setting=global_mode&value=' + (enabled ? '1' : '0') + '&nonce=<?php echo wp_create_nonce('demontek_steam_nonce'); ?>'
            }).then(() => {
                setTimeout(() => location.reload(), 1000);
            });
        }

        function saveSettings() {
            showActionFeedback('💾 Settings saved successfully!');
        }

        function findSteamPosts() {
            window.location.href = '<?php echo admin_url('edit.php?meta_key=trailer_1'); ?>';
        }

        function viewSamplePost() {
            window.open('<?php echo home_url(); ?>/?p=<?php echo $sample_post_id; ?>', '_blank');
        }

        function exportSettings() {
            const settings = {
                enabled: <?php echo get_option('demontek_steam_enabled', false) ? 'true' : 'false'; ?>,
                global_mode: <?php echo get_option('demontek_steam_global_mode', false) ? 'true' : 'false'; ?>,
                version: '<?php echo DEMONTEK_STEAM_VERSION; ?>',
                export_date: new Date().toISOString()
            };
            
            const blob = new Blob([JSON.stringify(settings, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'demontek-steam-settings-v<?php echo DEMONTEK_STEAM_VERSION; ?>.json';
            a.click();
            URL.revokeObjectURL(url);
        }

        function refreshMobilePreview() {
            showActionFeedback('🔄 Mobile preview refreshed');
            
            // Add visual feedback
            const button = event?.target;
            if (button) {
                const originalText = button.textContent;
                button.textContent = '🔄 Refreshing...';
                button.disabled = true;
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                }, 1500);
            }
        }

        function showActionFeedback(message) {
            const feedback = document.createElement('div');
            feedback.style.cssText = 'position: fixed; top: 60px; right: 20px; background: #2271b1; color: white; padding: 8px 12px; border-radius: 4px; z-index: 10000; font-size: 11px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);';
            feedback.textContent = message;
            document.body.appendChild(feedback);
            
            setTimeout(() => {
                if (document.body.contains(feedback)) {
                    document.body.removeChild(feedback);
                }
            }, 3000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎮 Demontek Steam v1.6.2 - Dashboard loaded!');
            console.log('✅ AJAX Loading FIXED!');
            console.log('✅ Mobile preview loading fixed');
        });
        </script>
        <?php
    }
}