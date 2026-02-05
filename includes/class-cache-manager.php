<?php
/**
 * Cache management handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Cache_Manager
 */
class Smart_FAQ_Cache_Manager {
    
    /**
     * Get cached FAQs for a page
     *
     * @param int $page_id Page ID
     * @param string $content_hash Content hash for validation
     * @return array|false Array of FAQ IDs with scores or false
     */
    public static function get_cached_faqs($page_id, $content_hash) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_cache';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Accessing custom cache table for rendered FAQ data.
        $cache = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM %i WHERE page_id = %d AND content_hash = %s AND expires_at > NOW()",
            $table,
            $page_id,
            $content_hash
        ));
        
        if ($cache && !empty($cache->matched_faq_ids)) {
            return json_decode($cache->matched_faq_ids, true);
        }
        
        return false;
    }
    
    /**
     * Set cached FAQs for a page
     *
     * @param int $page_id Page ID
     * @param string $content_hash Content hash
     * @param array $faq_data Array of FAQ IDs with scores
     * @return bool True on success
     */
    public static function set_cached_faqs($page_id, $content_hash, $faq_data) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_cache';
        
        $cache_duration = absint(get_option('smart_faq_cache_duration', 24));
        $expiry_timestamp = current_time('timestamp', true) + ($cache_duration * HOUR_IN_SECONDS);
        $expires_at = gmdate('Y-m-d H:i:s', $expiry_timestamp);
        
        $data = array(
            'page_id' => $page_id,
            'content_hash' => $content_hash,
            'matched_faq_ids' => wp_json_encode($faq_data),
            'created_at' => current_time('mysql'),
            'expires_at' => $expires_at,
        );
        
        // Check if cache already exists for this page
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Checking for existing cache entry in custom plugin table.
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM %i WHERE page_id = %d",
            $table,
            $page_id
        ));
        
        if ($existing) {
            // Update existing cache
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Updating cache entry in custom plugin table.
            $result = $wpdb->update(
                $table,
                $data,
                array('page_id' => $page_id)
            );
        } else {
            // Insert new cache
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Inserting cache entry in custom plugin table.
            $result = $wpdb->insert($table, $data);
        }
        
        do_action('smart_faq_cache_created', $page_id, $faq_data);
        
        return $result !== false;
    }
    
    /**
     * Clear cache for specific page
     *
     * @param int $page_id Page ID
     * @return bool True on success
     */
    public static function clear_page_cache($page_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_cache';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Deleting cache rows from custom plugin table.
        $result = $wpdb->delete($table, array('page_id' => $page_id));
        
        do_action('smart_faq_cache_cleared', $page_id);
        
        return $result !== false;
    }
    
    /**
     * Clear all cache
     *
     * @return bool True on success
     */
    public static function clear_all_cache() {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_cache';
        
        // Clear database cache
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Truncating custom cache table as part of cache reset.
        $result = $wpdb->query($wpdb->prepare("TRUNCATE TABLE %i", $table));

        // Clear WordPress transients
        $options_table = $wpdb->options;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Removing plugin transients from options table.
        $wpdb->query($wpdb->prepare(
            "DELETE FROM %i 
            WHERE option_name LIKE %s 
            OR option_name LIKE %s",
            $options_table,
            $wpdb->esc_like('_transient_smart_faq_') . '%',
            $wpdb->esc_like('_transient_timeout_smart_faq_') . '%'
        ));
        
        // Clear WordPress object cache if available
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear specific cache groups (only if function exists)
        if (function_exists('wp_cache_delete_group')) {
            wp_cache_delete_group('smart_faq');
        }
        
        do_action('smart_faq_cache_cleared', 'all');
        
        return $result !== false;
    }
    
    /**
     * Cleanup expired cache entries
     */
    public static function cleanup_expired_cache() {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_cache';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cleaning expired cache entries from custom table.
        $wpdb->query($wpdb->prepare("DELETE FROM %i WHERE expires_at < NOW()", $table));
        
        // Also clean up analytics if retention period is set
        $retention = get_option('smart_faq_analytics_retention', 90);
        if ($retention > 0) {
            $analytics_table = $wpdb->prefix . 'smart_faq_analytics';
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cleaning expired analytics rows based on retention policy.
            $wpdb->query($wpdb->prepare(
                "DELETE FROM %i WHERE displayed_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $analytics_table,
                $retention
            ));
        }
    }
    
    /**
     * Get cache statistics
     *
     * @return array Cache statistics
     */
    public static function get_cache_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_cache';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Retrieving cache statistics from custom table.
        $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $table));
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Retrieving cache statistics from custom table.
        $active = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE expires_at > NOW()", $table));
        $expired = $total - $active;
        
        return array(
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
        );
    }
}
