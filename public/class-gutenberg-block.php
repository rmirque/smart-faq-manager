<?php
/**
 * Gutenberg Block handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Gutenberg_Block
 */
class Smart_FAQ_Gutenberg_Block {
    
    /**
     * Register Gutenberg block
     */
    public static function register() {
        // Check if Gutenberg is available
        if (!function_exists('register_block_type')) {
            return;
        }
        
        // Register block
        register_block_type('smart-faq/faq-widget', array(
            'attributes' => array(
                'limit' => array(
                    'type' => 'number',
                    'default' => 5,
                ),
                'category' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'style' => array(
                    'type' => 'string',
                    'default' => 'accordion',
                ),
                'showNumbers' => array(
                    'type' => 'boolean',
                    // No default here; inherit global setting unless explicitly set in block
                ),
            ),
            'render_callback' => array(__CLASS__, 'render_block'),
        ));
        
        // Enqueue block editor assets
        add_action('enqueue_block_editor_assets', array(__CLASS__, 'enqueue_block_editor_assets'));
    }
    
    /**
     * Render block
     *
     * @param array $attributes Block attributes
     * @return string HTML output
     */
    public static function render_block($attributes) {
        // Robustly resolve current page ID (editor SSR may not set the loop)
        $page_id = get_the_ID();
        if (!$page_id) {
            // Block renderer often passes post_id in REST request.
            $request_post_id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            if (!$request_post_id) {
                $request_post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            }
            if ($request_post_id) {
                $page_id = absint( $request_post_id );
            }
        }
        if (!$page_id) {
            global $post;
            if (isset($post) && is_object($post) && !empty($post->ID)) {
                $page_id = (int) $post->ID;
            }
        }
        
        if (!$page_id) {
            // In both editor (admin) and REST preview contexts, return a visible placeholder
            return '<div class="smart-faq-editor-placeholder">' .
                   __('Smart FAQ will render here once a page context is available.', 'smart-faq-manager') .
                   '</div>';
        }
        
        // Get matching FAQs - 0 means use global setting
        $block_limit = isset($attributes['limit']) ? absint($attributes['limit']) : 0;
        $matcher_args = array(
            'limit' => $block_limit > 0 ? $block_limit : get_option('smart_faq_max_display', 5),
            'category' => isset($attributes['category']) ? sanitize_text_field($attributes['category']) : '',
        );
        
        $faqs = Smart_FAQ_Matcher::find_matching_faqs($page_id, $matcher_args);
        
        if (empty($faqs)) {
            if (is_admin()) {
                return '<div class="smart-faq-editor-placeholder">' . 
                       __('No matching FAQs found. FAQs will be displayed based on page content when published.', 'smart-faq-manager') . 
                       '</div>';
            }
            // Frontend: gracefully render an empty wrapper to avoid SSR empty notices in some contexts
            return '<div class="smart-faq-widget smart-faq-empty"></div>';
        }
        
        // Render FAQs
        // Determine show_numbers respecting global setting if not explicitly set in block
        $global_show_numbers = (int) get_option('smart_faq_show_numbers', 1) === 1;
        $show_numbers = array_key_exists('showNumbers', $attributes)
            ? (bool) $attributes['showNumbers']
            : $global_show_numbers;

        $renderer_args = array(
            'style' => isset($attributes['style']) ? sanitize_text_field($attributes['style']) : 'accordion',
            'show_numbers' => $show_numbers,
            'show_schema' => get_option('smart_faq_enable_schema', 1),
        );
        
        return Smart_FAQ_Widget_Renderer::render($faqs, $renderer_args);
    }
    
    /**
     * Enqueue block editor assets
     */
    public static function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'smart-faq-block-editor',
            SMART_FAQ_PLUGIN_URL . 'public/js/block-editor.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-server-side-render'),
            SMART_FAQ_VERSION,
            true
        );
        
        // Pass data to block editor
        wp_localize_script('smart-faq-block-editor', 'smartFaqBlock', array(
            'categories' => Smart_FAQ_Database::get_categories(),
            'defaults' => array(
                'showNumbers' => (int) get_option('smart_faq_show_numbers', 1) === 1,
            ),
        ));
    }
}
