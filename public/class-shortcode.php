<?php
/**
 * Shortcode handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Shortcode
 */
class Smart_FAQ_Shortcode {
    
    /**
     * Render shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public static function render($atts) {
        $defaults = array(
            'limit' => get_option('smart_faq_max_display', 5),
            'category' => '',
            'style' => get_option('smart_faq_display_style', 'accordion'),
            'threshold' => get_option('smart_faq_matching_threshold', 0.3),
            'cache' => 'true',
            'show_numbers' => get_option('smart_faq_show_numbers', 1),
            'custom_class' => '',
            'search' => '', // Empty = use global setting, 'true' = force enable, 'false' = force disable
            'debug' => 'false', // Enable debug output
        );
        
        $atts = shortcode_atts($defaults, $atts, 'smart_faq');
        
        // Sanitize attributes
        $atts['limit'] = absint($atts['limit']);
        $atts['category'] = sanitize_text_field($atts['category']);
        $atts['style'] = sanitize_text_field($atts['style']);
        $atts['threshold'] = floatval($atts['threshold']);
        $atts['cache'] = $atts['cache'] === 'true' || $atts['cache'] === '1';
        $atts['show_numbers'] = $atts['show_numbers'] === 'true' || $atts['show_numbers'] === '1' || $atts['show_numbers'] === 1;
        $atts['custom_class'] = sanitize_html_class($atts['custom_class']);
        $atts['debug'] = $atts['debug'] === 'true' || $atts['debug'] === '1';
        
        // Apply filters to allow modifications
        $atts = apply_filters('smart_faq_shortcode_atts', $atts);
        
        // Get current page ID
        $page_id = get_the_ID();
        
        if (!$page_id) {
            return '';
        }
        
        // Build matcher arguments
        $matcher_args = array(
            'limit' => $atts['limit'],
            'category' => $atts['category'],
            'threshold' => $atts['threshold'],
            'use_cache' => $atts['cache'],
        );
        
        // Get matching FAQs
        $faqs = Smart_FAQ_Matcher::find_matching_faqs($page_id, $matcher_args);
        
        if (empty($faqs)) {
            return '';
        }
        
        // Build renderer arguments
        $renderer_args = array(
            'style' => $atts['style'],
            'show_numbers' => $atts['show_numbers'],
            'custom_class' => $atts['custom_class'],
            'show_schema' => get_option('smart_faq_enable_schema', 1),
            'show_debug' => $atts['debug'],
        );
        
        // Handle search attribute
        if ($atts['search'] === 'false' || $atts['search'] === '0') {
            $renderer_args['disable_search'] = true;
        }
        
        // Render FAQs
        return Smart_FAQ_Widget_Renderer::render($faqs, $renderer_args);
    }
}
