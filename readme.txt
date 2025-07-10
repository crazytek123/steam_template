=== Demontek Steam Template ===
Contributors: Demontek
Tags: gaming, steam, template, layout, youtube, trailers, reviews, mobile, editor
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Steam-inspired gaming layout template for WordPress with REVOLUTIONARY Mobile Editor, tabbed modular architecture, and real-time preview system.

== Description ==

Transform your gaming content with the **Demontek Steam Template** - now featuring our groundbreaking **Mobile Editor** that revolutionizes how you create and manage gaming content on WordPress!

= 🚀 NEW in v1.8.0: Mobile Editor Revolution! =

**📱 Mobile-First Post Editor:**
* Tabbed modular architecture for organized editing
* Real-time preview always visible
* Component communication system with visual feedback
* Mobile-optimized interface perfect for editing on any device
* Instant field updates with auto-save functionality

**🧩 Tabbed Component System:**
* **Main Controller Tab** - Master Steam layout toggle and system overview
* **Title Scheme Tab** - Post title, AI summary, and genre management
* **Featured Image Tab** - Image upload, sizing, and overlay controls
* **Custom Fields Tab** - All Steam fields organized by category

**⚡ Real-Time Features:**
* Live preview updates as you type
* Component status indicators with pulse animations
* Architecture visualization showing active modules
* Instant field validation and feedback
* Auto-save every 2 seconds

= 🎮 Core Features =

* **Steam-Inspired Layout** - Authentic Steam gaming interface design
* **Real WordPress API Integration** - Live data loading with AJAX endpoints
* **Resizable Layout System** - Drag-to-resize video and info sections
* **Enhanced YouTube Integration** - Up to 5 trailers with smooth switching
* **Mobile Responsive** - Optimized for all devices
* **Modular Architecture** - Clean, maintainable code structure
* **Debug Console** - Real-time performance monitoring
* **Live Navigation** - Previous/Next post functionality

= 🎯 What's New in v1.8.0 =

**🌟 REVOLUTIONARY MOBILE EDITOR:**
* Complete mobile-first post editing interface
* Tabbed component architecture for organized workflow
* Real-time preview with instant field updates
* Component communication visualization
* Mobile-optimized design perfect for phones and tablets

**🏗️ ENHANCED MODULAR ARCHITECTURE:**
* New `class-mobile-editor.php` component
* Enhanced AJAX handlers for mobile functionality
* Improved component communication system
* Better separation of concerns across modules

**⚡ PERFORMANCE IMPROVEMENTS:**
* Optimized mobile rendering with efficient DOM updates
* Enhanced auto-save system with intelligent queuing
* Improved memory usage for mobile devices
* Better caching for frequently accessed data

**🎨 UI/UX ENHANCEMENTS:**
* Beautiful tabbed interface with smooth transitions
* Component status indicators with pulse animations
* Architecture map showing active modules
* Improved notification system with better positioning

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
   - Ensure the `/includes/` directory exists with all class files
   - Verify `class-mobile-editor.php` is present for mobile functionality

2. **Activate Plugin:**
   - Go to WordPress Admin → Plugins
   - Find "Demontek Steam Template" and click "Activate"

3. **Access Mobile Editor:**
   - Go to WordPress Admin → Demontek Steam → 📱 Mobile Editor
   - Select a post to edit or create a new one
   - Experience the revolutionary mobile editing interface!

**Quick Start with Mobile Editor:**
1. Navigate to **Demontek Steam → 📱 Mobile Editor**
2. Select a post from the grid or create a new one
3. Toggle Steam layout in the Main Controller tab
4. Add content using the organized tabbed interface
5. Watch real-time preview updates as you type!

= 🔧 Advanced Features =

**Mobile Editor Workflow:**
- **Post Selection Grid** - Choose from recent posts with status indicators
- **Component Status Bar** - Real-time status of all modules
- **Architecture Map** - Visual representation of active components
- **Tabbed Interface** - Organized editing with dedicated component tabs
- **Live Preview** - Always-visible mobile preview with instant updates

**Real-Time API Integration:**
- Live post loading with category filtering
- Navigation between posts via AJAX
- Real-time field completion tracking
- Performance monitoring and debugging

**Component Communication:**
- Event-driven architecture with visual feedback
- Component status indicators with pulse animations
- Real-time field validation and error handling
- Auto-save with intelligent queuing system

**Enhanced Mobile Experience:**
- Touch-friendly interface optimized for mobile devices
- Responsive design that works on all screen sizes
- Gesture-friendly navigation and controls
- Optimized loading for mobile connections

= 📱 Mobile Editor Guide =

**Getting Started:**
1. **Access**: Go to Demontek Steam → 📱 Mobile Editor
2. **Select Post**: Choose from the post grid or create new
3. **Enable Steam**: Toggle Steam layout in Main Controller tab
4. **Edit Content**: Use tabbed interface to organize your workflow
5. **Preview**: Watch changes appear instantly in the live preview

**Tab Organization:**
- **Main Controller** - Master controls and system overview
- **Title Scheme** - Post title, AI summary, genre
- **Featured Image** - Image management and display options
- **Custom Fields** - All Steam fields organized by category

**Pro Tips:**
- Use the architecture map to quickly switch between components
- Watch the component status bar for real-time feedback
- Auto-save runs every 2 seconds - no manual saving needed!
- Preview updates instantly as you type for immediate feedback

= 🎨 Customization =

**CSS Customization:**
- Override styles in your theme's `style.css`
- All classes prefixed with `demontek-`
- CSS custom properties for easy color changes
- Mobile-first responsive design with breakpoints

**Template Customization:**
- Copy `single-steam.php` to your theme directory
- Modify layout structure as needed
- Add custom hooks and filters
- Integrate with your theme's design system

**PHP Customization:**
- Use `demontek_steam_field_data` filter
- Hook into `demontek_steam_template_loaded` action
- Extend functionality with custom classes
- Add custom mobile editor components

= 🔍 Troubleshooting =

**Common Issues:**

*Mobile Editor not loading:*
- Check if `/includes/class-mobile-editor.php` exists
- Verify file permissions are correct (644 for files, 755 for directories)
- Ensure WordPress requirements are met (WP 5.0+, PHP 7.4+)

*Preview not updating:*
- Check browser console for JavaScript errors
- Verify AJAX URLs are correct in browser network tab
- Clear browser cache and reload

*Component communication not working:*
- Ensure all component files are present in `/includes/`
- Check that nonces are being generated correctly
- Verify user has `edit_posts` capability

**Mobile Editor Debugging:**
- Open browser dev tools to monitor console
- Check Network tab for AJAX request/response
- Verify component status indicators show green dots
- Use "Test Communication" button to verify system health

= 🚀 What's Coming Next =

**Planned Mobile Editor Enhancements:**
- **Drag & Drop Interface** - Reorder trailers and content sections
- **Advanced Preview Modes** - Desktop, tablet, and mobile previews
- **Template Library** - Pre-built gaming content templates
- **AI Integration** - Auto-generate descriptions and tags
- **Bulk Operations** - Edit multiple posts simultaneously

**Additional Features:**
- **Theme Integration** - Better compatibility with popular themes
- **Performance Dashboard** - Real-time performance metrics
- **Advanced Field Types** - Rich text, media galleries, custom components
- **Export/Import** - Share configurations between sites

= 📊 System Requirements =

**Minimum Requirements:**
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Modern browser with JavaScript enabled
- Mobile device or desktop with responsive design support

**Recommended for Best Mobile Experience:**
- WordPress 6.0+
- PHP 8.0+
- 512MB PHP memory limit
- SSD storage for faster loading
- Modern mobile browser (Chrome, Safari, Firefox)

= 🆘 Support =

**Documentation:**
- Comprehensive inline help in mobile editor
- Component tooltips and status indicators
- Built-in troubleshooting tools
- WordPress Codex standards followed

**Getting Help:**
- Use the debug console in mobile editor for system info
- Check browser console for JavaScript errors
- Test with default WordPress theme first
- Disable other plugins to check for conflicts

**Mobile Editor Support:**
- Component status indicators show real-time health
- Architecture map helps identify issues
- Auto-save prevents data loss
- Real-time validation provides immediate feedback

= 📜 Changelog =

= 1.8.0 - 2024-01-XX =

**🚀 REVOLUTIONARY MOBILE EDITOR:**
* Complete mobile-first post editing interface with tabbed architecture
* Real-time preview with instant field updates and component communication
* Component status indicators with pulse animations and visual feedback
* Architecture map showing active modules and system health
* Auto-save system with intelligent queuing and error handling

**🏗️ ENHANCED MODULAR ARCHITECTURE:**
* New `class-mobile-editor.php` component with full mobile functionality
* Enhanced AJAX handlers: `mobile_save_field`, `mobile_get_post_data`, `mobile_toggle_steam`
* Improved component communication system with event-driven architecture
* Better separation of concerns across all plugin modules

**⚡ PERFORMANCE IMPROVEMENTS:**
* Optimized mobile rendering with efficient DOM updates and caching
* Enhanced auto-save system with 2-second intervals and queue management
* Improved memory usage for mobile devices with lazy loading
* Better error handling and user feedback throughout the system

**🎨 UI/UX ENHANCEMENTS:**
* Beautiful tabbed interface with smooth transitions and hover effects
* Component status bar with real-time indicators and pulse animations
* Post selection grid with status indicators and quick actions
* Improved notification system with better positioning and animations

**🔧 TECHNICAL IMPROVEMENTS:**
* Enhanced WordPress integration with proper nonce handling
* Improved field validation with real-time feedback
* Better mobile responsiveness with touch-friendly controls
* Enhanced debugging capabilities with console logging

= 1.7.0 - Previous Release =
* Optimized performance with better memory management
* Enhanced field validation and error handling
* Improved mobile responsiveness and user experience
* Better component separation and modular architecture

= 1.6.3 - Previous Release =
* Fixed JavaScript syntax error that broke AJAX functionality
* Fixed debug console loading and display issues
* Enhanced error handling with proper try-catch blocks and user feedback
* Improved post list refresh behavior after duplication

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

**Special Thanks:**
- Steam for interface inspiration
- WordPress community for best practices
- Mobile-first design principles
- Component-based architecture patterns

== Installation ==

See the detailed installation instructions in the Description section above.

== Frequently Asked Questions ==

= How do I access the new Mobile Editor? =

Go to WordPress Admin → Demontek Steam → 📱 Mobile Editor. You'll see a post selection grid where you can choose an existing post or create a new one.

= Does the Mobile Editor work on desktop computers? =

Yes! While optimized for mobile devices, the Mobile Editor works perfectly on desktop computers, tablets, and any device with a modern browser.

= Can I still use the traditional WordPress editor? =

Absolutely! The Mobile Editor is an addition to your existing workflow. You can still use the classic WordPress editor, and there are quick links to switch between them.

= How does the real-time preview work? =

The preview updates instantly as you type. Changes to titles, descriptions, and other fields are reflected immediately in the mobile preview pane.

= Is the Mobile Editor compatible with my theme? =

Yes! The Mobile Editor is a standalone admin interface that doesn't affect your theme. It edits the same post data that your theme displays.

= What happens if I disable the plugin? =

All your content remains safe as regular WordPress posts. The Steam layout won't display, but all your content and custom fields are preserved.

= Can I customize the Mobile Editor interface? =

The Mobile Editor uses CSS that can be overridden. However, it's designed to be complete out-of-the-box. Focus on customizing the frontend Steam layout instead.

= How does auto-save work in the Mobile Editor? =

Changes are automatically saved every 2 seconds. You'll see confirmation notifications, and there's no need to manually save your work.

= Can I use this for non-gaming content? =

While designed for gaming content, you can adapt it for any media-rich content that benefits from trailers, reviews, and detailed information.

= Does this work with Gutenberg/Block Editor? =

Yes! The plugin adds custom fields that work with both classic and block editors. The Mobile Editor provides an alternative interface for managing these fields.

== Screenshots ==

1. **Mobile Editor Interface** - Revolutionary tabbed interface with real-time preview
2. **Component Communication** - Visual feedback showing active modules and status
3. **Post Selection Grid** - Choose from recent posts with status indicators
4. **Steam Layout Frontend** - Beautiful Steam-inspired gaming layout
5. **Architecture Map** - Visual representation of plugin components
6. **Real-time Preview** - Instant updates as you edit content
7. **Traditional Admin** - Enhanced WordPress admin with Steam controls
8. **Mobile Responsive** - Perfect display on all screen sizes

== Upgrade Notice ==

= 1.8.0 =
REVOLUTIONARY UPDATE: New Mobile Editor with tabbed architecture, real-time preview, and component communication system! This is a major feature release that transforms how you create gaming content. Safe upgrade with full backward compatibility. Highly recommended for all users!

= 1.7.0 =
Performance optimizations and enhanced modular architecture. Safe upgrade recommended.

= 1.6.3 =
Critical JavaScript error fixes and modular architecture improvements. Safe upgrade with full backward compatibility. Highly recommended for all users.

= 1.6.2 =
Major update with real WordPress API integration and enhanced functionality. Backup recommended before upgrade.

= 1.6.1 =
Interface improvements and bug fixes. Safe upgrade recommended.

= 1.6.0 =
Major feature release with resizable layouts. Test in staging environment first.