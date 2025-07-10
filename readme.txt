=== Demontek Steam Template ===
Contributors: Demontek
Tags: gaming, steam, template, layout, youtube, trailers, reviews
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.6.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Steam-inspired gaming layout template for WordPress with REAL API integration, resizable layouts, enhanced YouTube support, and modular architecture.

== Description ==

Transform your gaming content with the **Demontek Steam Template** - a comprehensive WordPress plugin that brings Steam's sleek gaming interface to your posts. Perfect for game reviews, trailers, and gaming content!

= 🎮 Key Features =

* **Steam-Inspired Layout** - Authentic Steam gaming interface design
* **Real WordPress API Integration** - Live data loading with AJAX endpoints
* **Resizable Layout System** - Drag-to-resize video and info sections
* **Enhanced YouTube Integration** - Up to 5 trailers with smooth switching
* **Mobile Responsive** - Optimized for all devices
* **Modular Architecture** - Clean, maintainable code structure
* **Debug Console** - Real-time performance monitoring
* **Live Navigation** - Previous/Next post functionality

= 🚀 What's New in v1.6.3 =

**🚨 CRITICAL FIXES:**
* Fixed JavaScript syntax error that broke duplicate post function
* Fixed debug console loading issues
* Enhanced error handling throughout AJAX functions
* Improved post list refresh behavior

**🏗️ MODULAR ARCHITECTURE:**
* Extracted edit post functionality to separate class
* Created `/includes/class-post-editor.php` for better organization
* Enhanced field management with improved validation
* Safer code structure for future enhancements

**⚡ PERFORMANCE IMPROVEMENTS:**
* Better error recovery and user feedback
* Enhanced AJAX endpoint reliability
* Improved memory usage and load times
* More robust JavaScript error handling

= 📋 Custom Fields Supported =

**Trailers & Media:**
* `trailer_1` - Main game trailer (YouTube URL)
* `trailer_2` - Gameplay footage (YouTube URL)  
* `trailer_3` - Features showcase (YouTube URL)
* `trailer_4` - Extended content (YouTube URL)
* `trailer_5` - Bonus material (YouTube URL)

**Content & Descriptions:**
* `ai_summary` - AI-generated game summary
* `ai_excerpt` - Short AI description
* `game_genre` - Game genre/category

**Reviews & Social:**
* `review_1` - Community review #1
* `review_2` - Community review #2
* `review_3` - Community review #3

**External Links:**
* `original_link` - YouTube channel/video URL
* `steam_link` - Steam store page URL
* `amazon_link` - Amazon purchase link URL
* `developer` - Game developer name
* `platforms` - Available platforms
* `release_date` - Game release date

**Layout Controls:**
* `_demontek_steam_use` - Enable Steam layout (checkbox)
* `_demontek_steam_extra_sidebar` - Extra left sidebar (checkbox)
* `_demontek_steam_content_layout` - Layout type (select: right/left/both/full)

= 🛠️ Installation =

1. **Upload Plugin Files:**
   - Upload the entire `demontek-steam-template` folder to `/wp-content/plugins/`
   - Ensure the `/includes/` directory exists with `class-post-editor.php`

2. **Activate Plugin:**
   - Go to WordPress Admin → Plugins
   - Find "Demontek Steam Template" and click "Activate"

3. **Configure Settings:**
   - Navigate to "Demontek Steam" in the admin menu
   - Enable the template system
   - Choose Global Mode or per-post control

4. **Add Content:**
   - Edit any post to see Steam meta boxes
   - Use "Single Post Enhancer" to add trailer URLs and content
   - Configure layout options in the sidebar controls

5. **Test & Preview:**
   - Use preview buttons for real-time testing
   - Check both desktop and mobile layouts
   - Verify interactive features work correctly

= ⚡ Quick Start Guide =

**For Game Reviews:**
1. Add `trailer_1` (main trailer URL)
2. Add `ai_summary` (game description)
3. Add `game_genre` (game category)
4. Enable Steam layout in post sidebar
5. Preview and publish!

**For Enhanced Gaming Content:**
1. Add up to 5 trailer URLs for comprehensive coverage
2. Include developer info and platform details
3. Add community reviews for social proof
4. Link to Steam and purchase pages
5. Use resizable layout for optimal presentation

= 🔧 Advanced Features =

**Real-Time API Integration:**
- Live post loading with category filtering
- Navigation between posts via AJAX
- Real-time field completion tracking
- Performance monitoring and debugging

**Resizable Layout System:**
- Drag the blue divider to resize sections
- Live dimension display during resize
- Minimum width constraints for usability
- Responsive breakpoints for mobile

**Enhanced YouTube Support:**
- Automatic thumbnail extraction
- Smooth trailer switching
- Preloaded thumbnails for faster loading
- Enhanced play button functionality

**Debug & Development:**
- Built-in debug console with performance metrics
- Real-time error tracking and logging
- Database query monitoring
- Memory usage optimization

= 📱 Mobile Optimization =

* Responsive design works on all screen sizes
* Touch-friendly controls and navigation
* Optimized loading for mobile connections
* Automatic layout adjustments for small screens

= 🎨 Customization =

**CSS Customization:**
- Override styles in your theme's `style.css`
- All classes prefixed with `demontek-`
- CSS custom properties for easy color changes
- Responsive breakpoints at 768px and 480px

**Template Customization:**
- Copy `single-steam.php` to your theme directory
- Modify layout structure as needed
- Add custom hooks and filters
- Integrate with your theme's design system

**PHP Customization:**
- Use `demontek_steam_field_data` filter
- Hook into `demontek_steam_template_loaded` action
- Extend functionality with custom classes
- Modify completion rate calculations

= 🔍 Troubleshooting =

**Common Issues:**

*Plugin not working after activation:*
- Check if `/includes/class-post-editor.php` exists
- Verify file permissions are correct
- Ensure WordPress requirements are met

*JavaScript errors in console:*
- Clear browser cache and reload
- Check for plugin conflicts
- Verify admin AJAX URLs are correct

*Duplicate post function not working:*
- Ensure user has edit_posts and publish_posts capabilities
- Check JavaScript console for errors
- Verify AJAX nonce is valid

*Layout not displaying correctly:*
- Confirm Steam layout is enabled for the post
- Check if custom fields are populated
- Verify theme compatibility

**Debug Console:**
Use the built-in debug console (admin area) to:
- Monitor performance metrics
- Track database queries
- View error logs
- Check system status

= 🚀 What's Coming Next =

**Planned Edit Post Enhancements:**
- Live preview in WordPress editor
- Enhanced field validation with real-time feedback
- Steam field shortcuts and templates
- Tabbed interface for better organization
- Drag-and-drop trailer ordering

**Additional Modular Extractions:**
- AJAX handlers → `class-ajax-handlers.php`
- Admin interface → `class-admin-interface.php`
- Quick actions → `class-quick-actions.php`
- Field inspector → `class-field-inspector.php`

= 📊 System Requirements =

**Minimum Requirements:**
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Modern browser with JavaScript enabled

**Recommended:**
- WordPress 6.0+
- PHP 8.0+
- 512MB PHP memory limit
- SSD storage for better performance

= 🆘 Support =

**Documentation:**
- Inline code comments for developers
- WordPress Codex standards followed
- Comprehensive error handling
- Detailed debug information

**Getting Help:**
- Check the debug console for system information
- Review browser console for JavaScript errors
- Test with default WordPress theme first
- Disable other plugins to check for conflicts

= 📜 Changelog =

= 1.6.3 - 2024-01-XX =

**🚨 Critical Fixes:**
* Fixed JavaScript syntax error in `duplicatePost()` function that broke AJAX functionality
* Fixed debug console loading and display issues
* Enhanced error handling with proper try-catch blocks and user feedback
* Improved post list refresh behavior after duplication

**🏗️ Modular Architecture:**
* Extracted edit post page functionality to `/includes/class-post-editor.php`
* Created modular class structure for better code organization
* Enhanced field management with improved validation and error handling
* Safer code structure preparation for future enhancements

**⚡ Performance Improvements:**
* Better error recovery and user feedback systems
* Enhanced AJAX endpoint reliability and response handling
* Improved memory usage and load times
* More robust JavaScript error handling throughout

**🔧 Technical Improvements:**
* Enhanced URL validation for link fields with proper error logging
* Improved completion rate calculation with better field detection
* Better thumbnail extraction and fallback handling
* Enhanced debug information with architecture status

= 1.6.2 - Previous Release =
* Real WordPress API integration with live data loading
* Enhanced debugging console with performance metrics
* Live post navigation with previous/next functionality
* Improved mobile responsiveness and user experience
* Enhanced field inspector with real-time data sync

= 1.6.1 - Previous Release =
* Cleaned interface removing confusing floating controls
* Better admin preview experience
* Streamlined for gaming content focus
* Maintained resizable layout functionality

= 1.6.0 - Previous Release =
* Complete resizable layout system with drag-to-resize functionality
* Enhanced YouTube integration with improved video playback
* Functional admin controls with zone toggle and fullscreen mode
* Comprehensive admin dashboard with live preview tabs

= 📄 License =

This plugin is licensed under the GPLv2 (or later) license.
https://www.gnu.org/licenses/gpl-2.0.html

= 🎮 Credits =

Developed by Demontek for the WordPress gaming community.
Inspired by Steam's gaming interface design.
Built with modern web standards and WordPress best practices.

== Installation ==

See the detailed installation instructions in the Description section above.

== Frequently Asked Questions ==

= Is this plugin compatible with my theme? =

Yes! The plugin is designed to work with any WordPress theme. It uses its own template system that doesn't interfere with your theme's design.

= Can I customize the Steam layout? =

Absolutely! You can copy the template files to your theme directory and modify them, or override the CSS styles in your theme's stylesheet.

= How many trailers can I add per post? =

You can add up to 5 trailers per post using the `trailer_1` through `trailer_5` custom fields.

= Does this work with YouTube videos only? =

Currently, the plugin is optimized for YouTube videos, but you can add any video URL to the custom fields.

= Will this slow down my website? =

No! The plugin only loads its assets on posts that use the Steam layout, and it's optimized for performance with lazy loading and efficient code.

= Can I use this for non-gaming content? =

While designed for gaming content, you can adapt it for any content that benefits from a media-rich layout with multiple videos and detailed information.

== Screenshots ==

1. **Steam Layout Frontend** - Beautiful Steam-inspired gaming layout with resizable sections
2. **Admin Dashboard** - Comprehensive admin interface with live preview and debugging
3. **Edit Post Interface** - Enhanced edit post screen with Steam field management
4. **Mobile Layout** - Fully responsive design optimized for mobile devices
5. **Debug Console** - Real-time performance monitoring and system information
6. **Field Inspector** - Live field management with completion tracking

== Upgrade Notice ==

= 1.6.3 =
Critical JavaScript error fixes and modular architecture improvements. Safe upgrade with full backward compatibility. Highly recommended for all users.

= 1.6.2 =
Major update with real WordPress API integration and enhanced functionality. Backup recommended before upgrade.

= 1.6.1 =
Interface improvements and bug fixes. Safe upgrade recommended.

= 1.6.0 =
Major feature release with resizable layouts. Test in staging environment first.