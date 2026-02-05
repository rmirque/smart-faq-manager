<?php
/**
 * Analytics page handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Analytics
 */
class Smart_FAQ_Analytics {
    
    /**
     * Render analytics page
     */
    public function render_analytics_page() {
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'smart-faq-manager' ) );
        }
        
        $stats = $this->get_analytics_data();
        
        include SMART_FAQ_PLUGIN_DIR . 'admin/partials/analytics-page.php';
    }
    
    /**
     * Track interaction
     *
     * @param int $faq_id FAQ ID
     * @param int $page_id Page ID
     * @param string $interaction_type Interaction type
     */
    public static function track_interaction($faq_id, $page_id, $interaction_type) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_analytics';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Tracking interaction in custom analytics table.
        $wpdb->insert($table, array(
            'faq_id' => $faq_id,
            'page_id' => $page_id,
            'displayed_at' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'interaction_type' => $interaction_type,
        ));
    }
    
    /**
     * Get analytics data
     *
     * @return array Analytics data
     */
    private function get_analytics_data() {
        global $wpdb;
        
        $analytics_table = sprintf('`%s`', esc_sql($wpdb->prefix . 'smart_faq_analytics'));
        $faq_table = sprintf('`%s`', esc_sql($wpdb->prefix . 'smart_faq_items'));
        
        // Top performing FAQs (most displayed with engagement rate)
        $top_faqs_query = sprintf(
            "SELECT f.*, 
                    COUNT(CASE WHEN a.interaction_type = 'display' THEN 1 END) as display_count,
                    COUNT(CASE WHEN a.interaction_type = 'click' THEN 1 END) as click_count
            FROM %s f
            LEFT JOIN %s a ON f.id = a.faq_id
            GROUP BY f.id
            HAVING display_count > 0
            ORDER BY display_count DESC
            LIMIT 10",
            $faq_table,
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom analytics aggregation requires direct query on plugin tables.
        $top_faqs = $wpdb->get_results( $top_faqs_query );
        
        // Underperforming FAQs (never shown or shown < 5 times)
        $underperforming_query = sprintf(
            "SELECT f.*, COUNT(a.id) as display_count 
            FROM %s f
            LEFT JOIN %s a ON f.id = a.faq_id
            WHERE f.status = 'active'
            GROUP BY f.id
            HAVING display_count < 5
            ORDER BY display_count ASC
            LIMIT 10",
            $faq_table,
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom analytics aggregation requires direct query on plugin tables.
        $underperforming_faqs = $wpdb->get_results( $underperforming_query );
        
        // Total stats
        $total_faqs = Smart_FAQ_Database::get_faq_count();
        $active_faqs = Smart_FAQ_Database::get_faq_count('active');
        
        $total_displays_query = sprintf(
            "SELECT COUNT(*) FROM %s",
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Aggregate count on custom analytics table.
        $total_displays = $wpdb->get_var( $total_displays_query );
        
        $displays_last_7_days_query = sprintf(
            "SELECT COUNT(*) FROM %s 
            WHERE displayed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Aggregate count on custom analytics table.
        $displays_last_7_days = $wpdb->get_var( $displays_last_7_days_query );
        
        $displays_last_30_days_query = sprintf(
            "SELECT COUNT(*) FROM %s 
            WHERE displayed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Aggregate count on custom analytics table.
        $displays_last_30_days = $wpdb->get_var( $displays_last_30_days_query );
        
        // Most popular categories
        $category_stats_query = sprintf(
            "SELECT f.category, COUNT(a.id) as display_count
            FROM %s f
            LEFT JOIN %s a ON f.id = a.faq_id
            WHERE f.category != ''
            GROUP BY f.category
            ORDER BY display_count DESC
            LIMIT 10",
            $faq_table,
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom analytics aggregation requires direct query on plugin tables.
        $category_stats = $wpdb->get_results( $category_stats_query );
        
        // Engagement metrics - FAQs with best click/display ratio
        $top_engaged_query = sprintf(
            "SELECT f.*, 
                    COUNT(CASE WHEN a.interaction_type = 'display' THEN 1 END) as display_count,
                    COUNT(CASE WHEN a.interaction_type = 'click' THEN 1 END) as click_count
            FROM %s f
            LEFT JOIN %s a ON f.id = a.faq_id
            WHERE f.status = 'active'
            GROUP BY f.id
            HAVING display_count >= 5
            ORDER BY (click_count / display_count) DESC
            LIMIT 10",
            $faq_table,
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom analytics aggregation requires direct query on plugin tables.
        $top_engaged = $wpdb->get_results( $top_engaged_query );
        
        // Low engagement FAQs
        $low_engaged_query = sprintf(
            "SELECT f.*, 
                    COUNT(CASE WHEN a.interaction_type = 'display' THEN 1 END) as display_count,
                    COUNT(CASE WHEN a.interaction_type = 'click' THEN 1 END) as click_count
            FROM %s f
            LEFT JOIN %s a ON f.id = a.faq_id
            WHERE f.status = 'active'
            GROUP BY f.id
            HAVING display_count >= 10 AND (click_count / display_count) < 0.2
            ORDER BY (click_count / display_count) ASC
            LIMIT 10",
            $faq_table,
            $analytics_table
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom analytics aggregation requires direct query on plugin tables.
        $low_engaged = $wpdb->get_results( $low_engaged_query );
        
        return array(
            'top_faqs' => $top_faqs,
            'underperforming_faqs' => $underperforming_faqs,
            'total_faqs' => $total_faqs,
            'active_faqs' => $active_faqs,
            'total_displays' => $total_displays,
            'displays_last_7_days' => $displays_last_7_days,
            'displays_last_30_days' => $displays_last_30_days,
            'category_stats' => $category_stats,
            'top_engaged' => $top_engaged,
            'low_engaged' => $low_engaged,
        );
    }
}
