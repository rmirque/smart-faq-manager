<?php
/**
 * Fired when the plugin is uninstalled
 * 
 * @package Smart_FAQ_Manager
 */

// Exit if accessed directly or not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check user capabilities
if (!current_user_can('activate_plugins')) {
    exit;
}

global $wpdb;

// Delete database tables
$tables = array(
    $wpdb->prefix . 'smart_faq_items',
    $wpdb->prefix . 'smart_faq_cache',
    $wpdb->prefix . 'smart_faq_analytics',
);

foreach ($tables as $table) {
    $table_name = sprintf('`%s`', esc_sql($table));
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Removing plugin-specific tables on uninstall.
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
}

// Delete all plugin options
$options_table = sprintf('`%s`', esc_sql( $wpdb->options ));
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Removing plugin options on uninstall.
$wpdb->query("DELETE FROM {$options_table} WHERE option_name LIKE 'smart_faq_%'");

// Delete all transients
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Removing plugin transients on uninstall.
$wpdb->query("DELETE FROM {$options_table} WHERE option_name LIKE '_transient_smart_faq_%' OR option_name LIKE '_transient_timeout_smart_faq_%'");

// Delete all post meta (manual FAQ selections)
$postmeta_table = sprintf('`%s`', esc_sql( $wpdb->postmeta ));
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Removing plugin post meta on uninstall.
$wpdb->query("DELETE FROM {$postmeta_table} WHERE meta_key LIKE '_smart_faq_%'");

// Delete widget instances
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Removing plugin widget options on uninstall.
$wpdb->query("DELETE FROM {$options_table} WHERE option_name LIKE '%smart_faq_widget%'");

// Clear scheduled cron jobs
wp_clear_scheduled_hook('smart_faq_daily_cleanup');

// Clear any cached data
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Delete any uploaded files (if any in future)
$upload_dir = wp_upload_dir();
$plugin_upload_dir = $upload_dir['basedir'] . '/smart-faq-manager';
if (is_dir($plugin_upload_dir)) {
    if (function_exists('wp_delete_directory')) {
        wp_delete_directory($plugin_upload_dir);
    } else {
        if (!function_exists('request_filesystem_credentials')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        if (WP_Filesystem()) {
            global $wp_filesystem;
            $wp_filesystem->delete(trailingslashit($plugin_upload_dir), true);
        }
    }
}

// Flush rewrite rules (do this last)
flush_rewrite_rules();
