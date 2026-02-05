<?php
/**
 * Database operations handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Database
 */
class Smart_FAQ_Database {
    
    /**
     * Get FAQ by ID
     *
     * @param int $faq_id FAQ ID
     * @return object|null FAQ object or null
     */
    public static function get_faq($faq_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Accessing custom plugin table with prepared statement.
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM %i WHERE id = %d",
            $table,
            $faq_id
        ));
    }
    
    /**
     * Get all active FAQs
     *
     * @param array $args Query arguments
     * @return array Array of FAQ objects
     */
    public static function get_active_faqs($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        $defaults = array(
            'status' => 'active',
            'category' => '',
            'orderby' => 'priority',
            'order' => 'DESC',
            'limit' => 100,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_clauses = array('status = %s');
        $params = array($args['status']);
        
        if (!empty($args['category'])) {
            $where_clauses[] = 'category = %s';
            $params[] = $args['category'];
        }
        
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        $limit = absint($args['limit']);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Custom table name built dynamically is safe.
        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where_clauses);
        
        if ($orderby) {
            $sql .= " ORDER BY {$orderby}";
        }
        
        if ($limit > 0) {
            $sql .= " LIMIT %d";
            $params[] = $limit;
        }
        
        $prepare_args = $params;
        array_unshift($prepare_args, $sql);
        $prepared_sql = call_user_func_array(array($wpdb, 'prepare'), $prepare_args);
        if (false === $prepared_sql) {
            return array();
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Query prepared via $wpdb->prepare above for custom plugin table.
        return $wpdb->get_results($prepared_sql);
    }
    
    /**
     * Search FAQs using fulltext search
     *
     * @param string $search_terms Search terms
     * @param int $limit Limit results
     * @return array Array of FAQ objects
     */
    public static function search_faqs($search_terms, $limit = 30) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Performing fulltext search on custom FAQ table.
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i 
            WHERE status = 'active' 
            AND MATCH(question, answer, keywords) AGAINST(%s IN NATURAL LANGUAGE MODE)
            ORDER BY priority DESC 
            LIMIT %d",
            $table,
            $search_terms,
            $limit
        ));
    }
    
    /**
     * Insert new FAQ
     *
     * @param array $data FAQ data
     * @return int|false FAQ ID on success, false on failure
     */
    public static function insert_faq($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        $defaults = array(
            'question' => '',
            'question_html' => '',
            'answer' => '',
            'answer_html' => '',
            'keywords' => '',
            'category' => '',
            'priority' => 0,
            'status' => 'active',
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
            'view_count' => 0,
            'display_count' => 0,
        );
        
        $data = wp_parse_args($data, $defaults);
        
        do_action('smart_faq_before_insert', $data);
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Inserting row into custom plugin table.
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            $faq_id = $wpdb->insert_id;
            do_action('smart_faq_after_insert', $faq_id, $data);
            return $faq_id;
        }
        
        return false;
    }
    
    /**
     * Update FAQ
     *
     * @param int $faq_id FAQ ID
     * @param array $data FAQ data
     * @return bool True on success, false on failure
     */
    public static function update_faq($faq_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        $data['updated_at'] = current_time('mysql');
        
        do_action('smart_faq_before_update', $faq_id, $data);
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Updating row in custom plugin table.
        $result = $wpdb->update(
            $table,
            $data,
            array('id' => $faq_id),
            null,
            array('%d')
        );
        
        if ($result !== false) {
            do_action('smart_faq_after_update', $faq_id, $data);
            // Clear cache when FAQ is updated
            Smart_FAQ_Cache_Manager::clear_all_cache();
            return true;
        }
        
        return false;
    }
    
    /**
     * Delete FAQ
     *
     * @param int $faq_id FAQ ID
     * @return bool True on success, false on failure
     */
    public static function delete_faq($faq_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        do_action('smart_faq_before_delete', $faq_id);
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Deleting row from custom plugin table.
        $result = $wpdb->delete($table, array('id' => $faq_id), array('%d'));
        
        if ($result) {
            do_action('smart_faq_after_delete', $faq_id);
            // Clear cache when FAQ is deleted
            Smart_FAQ_Cache_Manager::clear_all_cache();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get FAQ count
     *
     * @param string $status Status filter
     * @return int FAQ count
     */
    public static function get_faq_count($status = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        if ($status) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Counting rows in custom FAQ table.
            return $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM %i WHERE status = %s",
                $table,
                $status
            ));
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Counting all rows in custom FAQ table.
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $table));
    }
    
    /**
     * Get all categories
     *
     * @return array Array of category names
     */
    public static function get_categories() {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Retrieving distinct categories from custom FAQ table.
        $categories = $wpdb->get_col(
            $wpdb->prepare("SELECT DISTINCT category FROM %i WHERE category != '' ORDER BY category", $table)
        );
        
        return $categories ? $categories : array();
    }
    
    /**
     * Increment display count
     *
     * @param int $faq_id FAQ ID
     */
    public static function increment_display_count($faq_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Updating display count in custom FAQ table.
        $wpdb->query($wpdb->prepare(
            "UPDATE %i SET display_count = display_count + 1 WHERE id = %d",
            $table,
            $faq_id
        ));
    }
}
