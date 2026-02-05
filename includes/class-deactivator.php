<?php
/**
 * Plugin deactivation handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Deactivator
 */
class Smart_FAQ_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear scheduled cron jobs
        wp_clear_scheduled_hook('smart_faq_daily_cleanup');
        
        // Clear all plugin transients
        self::clear_transients();
        
        // Clear object cache if available
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Note: We do NOT delete database tables or options
        // This preserves user data in case they reactivate
    }
    
    /**
     * Clear all plugin transients
     */
    private static function clear_transients() {
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Removing plugin-specific transients from options table on deactivation.
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_smart_faq_%' 
            OR option_name LIKE '_transient_timeout_smart_faq_%'"
        );
    }
}

