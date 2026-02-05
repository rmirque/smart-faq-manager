<?php
/**
 * Public interface handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Public_Interface
 */
class Smart_FAQ_Public_Interface {
    
    /**
     * Initialize public interface
     */
    public static function init() {
        // Register shortcode
        add_shortcode('smart_faq', array('Smart_FAQ_Shortcode', 'render'));
        
        // Register widget
        add_action('widgets_init', array(__CLASS__, 'register_widget'));
        
        // Register Gutenberg block
        add_action('init', array('Smart_FAQ_Gutenberg_Block', 'register'));
        
        // Enqueue frontend assets
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        
        // AJAX handlers for interaction tracking
        add_action('wp_ajax_smart_faq_track_interaction', array(__CLASS__, 'track_interaction'));
        add_action('wp_ajax_nopriv_smart_faq_track_interaction', array(__CLASS__, 'track_interaction'));
    }
    
    /**
     * Register widget
     */
    public static function register_widget() {
        register_widget('Smart_FAQ_Widget');
    }
    
    /**
     * Enqueue frontend assets
     */
    public static function enqueue_assets() {
        global $post;
        
        // Check if we need to load assets
        $load_assets = false;
        
        if (is_a($post, 'WP_Post')) {
            // Check for shortcode
            if (has_shortcode($post->post_content, 'smart_faq')) {
                $load_assets = true;
            }
            
            // Check for Gutenberg block
            if (has_block('smart-faq/faq-widget', $post)) {
                $load_assets = true;
            }
        }
        
        // Check for active widget
        if (is_active_widget(false, false, 'smart_faq_widget', true)) {
            $load_assets = true;
        }
        
        // Apply filter to allow manual loading
        $load_assets = apply_filters('smart_faq_load_assets', $load_assets);
        
        if (!$load_assets) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'smart-faq-public',
            SMART_FAQ_PLUGIN_URL . 'public/css/public-styles.css',
            array(),
            SMART_FAQ_VERSION
        );
        
        // Add custom appearance styles
        $appearance_styles = Smart_FAQ_Style_Generator::generate_custom_styles();
        if (!empty($appearance_styles)) {
            wp_add_inline_style('smart-faq-public', $appearance_styles);
        }
        
        // Add custom CSS from advanced settings (legacy support)
        $custom_css = get_option('smart_faq_custom_css', '');
        if (!empty($custom_css)) {
            wp_add_inline_style('smart-faq-public', $custom_css);
        }
        
        // Enqueue scripts
        wp_enqueue_script(
            'smart-faq-public',
            SMART_FAQ_PLUGIN_URL . 'public/js/public-scripts.js',
            array('jquery'),
            SMART_FAQ_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('smart-faq-public', 'smartFaqPublic', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('smart_faq_public'),
            'pageId' => get_the_ID(),
        ));
    }
    
    /**
     * AJAX handler for tracking interactions
     */
    public static function track_interaction() {
        // Verify nonce
        $nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'smart_faq_public')) {
            wp_send_json_error(__('Security check failed.', 'smart-faq-manager'));
        }
        
        $faq_id = isset($_POST['faq_id']) ? absint( wp_unslash( $_POST['faq_id'] ) ) : 0;
        $interaction_type = isset($_POST['interaction_type']) ? sanitize_text_field( wp_unslash( $_POST['interaction_type'] ) ) : 'click';
        $page_id = isset($_POST['page_id']) ? absint( wp_unslash( $_POST['page_id'] ) ) : 0;
        
        if (!$faq_id || !$page_id) {
            wp_send_json_error(__('Invalid parameters.', 'smart-faq-manager'));
        }
        
        // Check if analytics is enabled
        if (!get_option('smart_faq_enable_analytics', 1)) {
            wp_send_json_success(); // Return success but don't track
        }
        
        // Track the interaction
        Smart_FAQ_Analytics::track_interaction($faq_id, $page_id, $interaction_type);
        
        wp_send_json_success();
    }
}
