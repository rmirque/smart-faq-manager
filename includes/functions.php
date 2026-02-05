<?php
/**
 * Helper functions
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get FAQs for current page
 *
 * @param array $args Arguments
 * @return array Array of FAQ objects
 */
function smart_faq_get_faqs($args = array()) {
    $page_id = get_the_ID();
    
    if (!$page_id) {
        return array();
    }
    
    return Smart_FAQ_Matcher::find_matching_faqs($page_id, $args);
}

/**
 * Display FAQ widget
 *
 * @param array $args Arguments
 */
function smart_faq_display($args = array()) {
    $faqs = smart_faq_get_faqs($args);
    echo wp_kses_post( Smart_FAQ_Widget_Renderer::render($faqs, $args) );
}

/**
 * Get FAQ by ID
 *
 * @param int $faq_id FAQ ID
 * @return object|null FAQ object
 */
function smart_faq_get_faq($faq_id) {
    return Smart_FAQ_Database::get_faq($faq_id);
}

/**
 * Check if FAQ plugin is active
 *
 * @return bool
 */
function smart_faq_is_active() {
    return defined('SMART_FAQ_VERSION');
}

/**
 * Get plugin option with default
 *
 * @param string $option Option name
 * @param mixed $default Default value
 * @return mixed Option value
 */
function smart_faq_get_option($option, $default = false) {
    return get_option('smart_faq_' . $option, $default);
}

/**
 * Update plugin option
 *
 * @param string $option Option name
 * @param mixed $value Option value
 * @return bool
 */
function smart_faq_update_option($option, $value) {
    return update_option('smart_faq_' . $option, $value);
}

/**
 * Clear all FAQ cache
 */
function smart_faq_clear_cache() {
    Smart_FAQ_Cache_Manager::clear_all_cache();
}

/**
 * Clear FAQ cache for specific page
 *
 * @param int $page_id Page ID
 */
function smart_faq_clear_page_cache($page_id) {
    Smart_FAQ_Cache_Manager::clear_page_cache($page_id);
}

/**
 * Log error message
 *
 * @param string $message Error message
 * @param string $level Error level (ERROR, WARNING, INFO, DEBUG)
 */
function smart_faq_log($message, $level = 'ERROR') {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $log_message = sprintf(
            '[Smart FAQ Manager] [%s] %s: %s',
            current_time('mysql'),
            $level,
            $message
        );
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Allow logging when WP_DEBUG is enabled.
        error_log($log_message);
    }
}

/**
 * Get FAQ categories
 *
 * @return array Array of category names
 */
function smart_faq_get_categories() {
    return Smart_FAQ_Database::get_categories();
}

/**
 * Enqueue FAQ styles
 */
function smart_faq_enqueue_styles() {
    wp_enqueue_style(
        'smart-faq-public',
        SMART_FAQ_PLUGIN_URL . 'public/css/public-styles.css',
        array(),
        SMART_FAQ_VERSION
    );
    
    // Add custom CSS from settings
    $custom_css = get_option('smart_faq_custom_css', '');
    if (!empty($custom_css)) {
        wp_add_inline_style('smart-faq-public', $custom_css);
    }
}

/**
 * Enqueue FAQ scripts
 */
function smart_faq_enqueue_scripts() {
    wp_enqueue_script(
        'smart-faq-public',
        SMART_FAQ_PLUGIN_URL . 'public/js/public-scripts.js',
        array(),
        SMART_FAQ_VERSION,
        true
    );
}



