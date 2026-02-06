<?php
/**
 * Plugin Name: Smart FAQ Manager
 * Plugin URI: https://wordpress.org/plugins/smart-faq-manager
 * Description: Dynamically display contextually relevant FAQs based on page content using local content analysis
 * Version: 1.0
 * Requires at least: 6.2
 * Tested up to: 6.9.1
 * Requires PHP: 7.4
 * Author: Smart FAQ Manager Team
 * Author URI: https://wordpress.org/plugins/smart-faq-manager
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-faq-manager
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SMART_FAQ_VERSION', '1.0');
define('SMART_FAQ_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMART_FAQ_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SMART_FAQ_PLUGIN_FILE', __FILE__);

/**
 * Plugin activation hook
 */
function smart_faq_activate() {
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-activator.php';
    Smart_FAQ_Activator::activate();
}
register_activation_hook(__FILE__, 'smart_faq_activate');

/**
 * Plugin deactivation hook
 */
function smart_faq_deactivate() {
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-deactivator.php';
    Smart_FAQ_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'smart_faq_deactivate');

/**
 * Initialize the plugin
 */
function smart_faq_init() {
    // Load core classes
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-database.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-cache-manager.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-faq-manager.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-content-analyzer.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-faq-matcher.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-widget-renderer.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-style-generator.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-style-templates.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/functions.php';
    
    // Load admin classes
    if (is_admin()) {
        require_once SMART_FAQ_PLUGIN_DIR . 'admin/class-admin-interface.php';
        require_once SMART_FAQ_PLUGIN_DIR . 'admin/class-list-table.php';
        require_once SMART_FAQ_PLUGIN_DIR . 'admin/class-settings.php';
        require_once SMART_FAQ_PLUGIN_DIR . 'admin/class-appearance-settings.php';
        require_once SMART_FAQ_PLUGIN_DIR . 'admin/class-analytics.php';
        require_once SMART_FAQ_PLUGIN_DIR . 'admin/class-meta-box.php';
        
        // Initialize admin
        Smart_FAQ_Admin_Interface::init();
    }
    
    // Load public classes
    require_once SMART_FAQ_PLUGIN_DIR . 'public/class-public-interface.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'public/class-shortcode.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'public/class-widget.php';
    require_once SMART_FAQ_PLUGIN_DIR . 'public/class-gutenberg-block.php';
    
    // Initialize public interface
    Smart_FAQ_Public_Interface::init();
}
add_action('plugins_loaded', 'smart_faq_init');

/**
 * Schedule cron jobs
 */
function smart_faq_schedule_cron() {
    if (!wp_next_scheduled('smart_faq_daily_cleanup')) {
        wp_schedule_event(time(), 'daily', 'smart_faq_daily_cleanup');
    }
}
add_action('wp', 'smart_faq_schedule_cron');

/**
 * Daily cleanup task
 */
function smart_faq_daily_cleanup_task() {
    require_once SMART_FAQ_PLUGIN_DIR . 'includes/class-cache-manager.php';
    Smart_FAQ_Cache_Manager::cleanup_expired_cache();
}
add_action('smart_faq_daily_cleanup', 'smart_faq_daily_cleanup_task');
