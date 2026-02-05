<?php
/**
 * FAQ Manager - Main business logic
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Manager
 */
class Smart_FAQ_Manager {
    
    /**
     * Create a new FAQ
     *
     * @param array $data FAQ data
     * @return int|WP_Error FAQ ID on success, WP_Error on failure
     */
    public static function create_faq($data) {
        // Validate required fields
        if (empty($data['question_html']) || empty($data['answer_html'])) {
            return new WP_Error('missing_fields', __('Question and answer are required.', 'smart-faq-manager'));
        }
        
        // Sanitize data
        $sanitized_data = self::sanitize_faq_data($data);
        
        // Extract plain text versions for searching
        $sanitized_data['question'] = wp_strip_all_tags($sanitized_data['question_html']);
        $sanitized_data['answer'] = wp_strip_all_tags($sanitized_data['answer_html']);
        
        // Insert FAQ
        $faq_id = Smart_FAQ_Database::insert_faq($sanitized_data);
        
        if (!$faq_id) {
            return new WP_Error('insert_failed', __('Failed to create FAQ.', 'smart-faq-manager'));
        }
        
        return $faq_id;
    }
    
    /**
     * Update an existing FAQ
     *
     * @param int $faq_id FAQ ID
     * @param array $data FAQ data
     * @return bool|WP_Error True on success, WP_Error on failure
     */
    public static function update_faq($faq_id, $data) {
        // Validate FAQ exists
        $existing_faq = Smart_FAQ_Database::get_faq($faq_id);
        if (!$existing_faq) {
            return new WP_Error('faq_not_found', __('FAQ not found.', 'smart-faq-manager'));
        }
        
        // Sanitize data
        $sanitized_data = self::sanitize_faq_data($data);
        
        // Update plain text versions if HTML versions changed
        if (isset($sanitized_data['question_html'])) {
            $sanitized_data['question'] = wp_strip_all_tags($sanitized_data['question_html']);
        }
        if (isset($sanitized_data['answer_html'])) {
            $sanitized_data['answer'] = wp_strip_all_tags($sanitized_data['answer_html']);
        }
        
        // Update FAQ
        $result = Smart_FAQ_Database::update_faq($faq_id, $sanitized_data);
        
        if (!$result) {
            return new WP_Error('update_failed', __('Failed to update FAQ.', 'smart-faq-manager'));
        }
        
        return true;
    }
    
    /**
     * Delete an FAQ
     *
     * @param int $faq_id FAQ ID
     * @return bool|WP_Error True on success, WP_Error on failure
     */
    public static function delete_faq($faq_id) {
        $result = Smart_FAQ_Database::delete_faq($faq_id);
        
        if (!$result) {
            return new WP_Error('delete_failed', __('Failed to delete FAQ.', 'smart-faq-manager'));
        }
        
        return true;
    }
    
    /**
     * Sanitize FAQ data
     *
     * @param array $data FAQ data
     * @return array Sanitized data
     */
    private static function sanitize_faq_data($data) {
        $sanitized = array();
        
        if (isset($data['question_html'])) {
            $sanitized['question_html'] = wp_kses_post($data['question_html']);
        }
        
        if (isset($data['answer_html'])) {
            $sanitized['answer_html'] = wp_kses_post($data['answer_html']);
        }
        
        if (isset($data['keywords'])) {
            $sanitized['keywords'] = sanitize_textarea_field($data['keywords']);
        }
        
        if (isset($data['category'])) {
            $sanitized['category'] = sanitize_text_field($data['category']);
        }
        
        if (isset($data['priority'])) {
            $sanitized['priority'] = absint($data['priority']);
        }
        
        if (isset($data['status'])) {
            $allowed_statuses = array('active', 'inactive', 'draft');
            $sanitized['status'] = in_array($data['status'], $allowed_statuses) ? $data['status'] : 'active';
        }
        
        return $sanitized;
    }
    
    /**
     * Import FAQs from JSON
     *
     * @param string $json JSON data
     * @return array|WP_Error Array with import results or WP_Error
     */
    public static function import_faqs($json) {
        $faqs = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_json', __('Invalid JSON data.', 'smart-faq-manager'));
        }
        
        $imported = 0;
        $failed = 0;
        $errors = array();
        
        foreach ($faqs as $faq_data) {
            $result = self::create_faq($faq_data);
            
            if (is_wp_error($result)) {
                $failed++;
                $errors[] = $result->get_error_message();
            } else {
                $imported++;
            }
        }
        
        return array(
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
        );
    }
    
    /**
     * Export FAQs to JSON
     *
     * @param array $faq_ids FAQ IDs to export (empty for all)
     * @return string JSON data
     */
    public static function export_faqs($faq_ids = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_items';
        
        if (empty($faq_ids)) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Exporting FAQs requires retrieving all rows from custom FAQ table.
            $faqs = $wpdb->get_results("SELECT * FROM {$table}");
        } else {
            // Sanitize IDs and create placeholders
            $ids = array_map('absint', $faq_ids);
            
            if (empty($ids)) {
                return wp_json_encode(array(), JSON_PRETTY_PRINT);
            }
            
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Custom table name combined with placeholders for IDs.
            $query = "SELECT * FROM {$table} WHERE id IN ({$placeholders})";
            $prepare_args = array_merge(array($query), $ids);
            $prepared_query = call_user_func_array(array($wpdb, 'prepare'), $prepare_args);
            if (false === $prepared_query) {
                return wp_json_encode(array(), JSON_PRETTY_PRINT);
            }
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Exporting selected FAQs using prepared statement.
            $faqs = $wpdb->get_results($prepared_query);
        }
        
        return wp_json_encode($faqs, JSON_PRETTY_PRINT);
    }
    
    /**
     * Get FAQ statistics
     *
     * @return array Statistics
     */
    public static function get_statistics() {
        $stats = array(
            'total' => Smart_FAQ_Database::get_faq_count(),
            'active' => Smart_FAQ_Database::get_faq_count('active'),
            'inactive' => Smart_FAQ_Database::get_faq_count('inactive'),
            'draft' => Smart_FAQ_Database::get_faq_count('draft'),
            'categories' => count(Smart_FAQ_Database::get_categories()),
            'cache' => Smart_FAQ_Cache_Manager::get_cache_stats(),
        );
        
        return $stats;
    }
    
    /**
     * Validate FAQ data
     *
     * @param array $data FAQ data
     * @return bool|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_faq_data($data) {
        if (empty($data['question_html'])) {
            return new WP_Error('missing_question', __('Question is required.', 'smart-faq-manager'));
        }
        
        if (empty($data['answer_html'])) {
            return new WP_Error('missing_answer', __('Answer is required.', 'smart-faq-manager'));
        }
        
        if (isset($data['priority'])) {
            $priority = absint($data['priority']);
            if ($priority < 0 || $priority > 100) {
                return new WP_Error('invalid_priority', __('Priority must be between 0 and 100.', 'smart-faq-manager'));
            }
        }
        
        return true;
    }
}
