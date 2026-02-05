<?php
/**
 * Widget rendering system
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Widget_Renderer
 */
class Smart_FAQ_Widget_Renderer {
    
    /**
     * Render FAQ widget
     *
     * @param array $faqs Array of FAQ objects
     * @param array $args Display arguments
     * @return string HTML output
     */
    public static function render($faqs, $args = array()) {
        if (empty($faqs)) {
            return self::render_empty_state();
        }
        
        $defaults = array(
            'style' => get_option('smart_faq_display_style', 'accordion'),
            'show_numbers' => (get_option('smart_faq_show_numbers', 1) == 1),
            'custom_class' => '',
            'show_schema' => (get_option('smart_faq_enable_schema', 1) == 1),
            'show_debug' => false,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Track analytics
        if (get_option('smart_faq_enable_analytics', 1) == 1) {
            self::track_faq_display($faqs, get_the_ID());
        }
        
        // Increment display counts
        foreach ($faqs as $faq) {
            Smart_FAQ_Database::increment_display_count($faq->id);
        }
        
        // Get template
        $template = self::locate_template($args['style']);
        
        // Start output buffering
        ob_start();
        
        // Debug info for admins (only when explicitly requested)
        if ($args['show_debug'] && current_user_can('manage_options')) {
            /* translators: 1: number of FAQs displayed, 2: requested FAQ limit. */
            $debug_message = sprintf(
                /* translators: 1: number of FAQs displayed, 2: requested FAQ limit. */
                esc_html__( 'Displaying %1$d FAQs | Requested limit: %2$d', 'smart-faq-manager' ),
                count($faqs),
                absint( get_option('smart_faq_max_display', 5) )
            );
            printf(
                '<div style="background: #fff3cd; border-left: 4px solid #f59e0b; padding: 12px; margin-bottom: 15px; font-size: 12px;"><strong>%s</strong> %s</div>',
                esc_html__( 'Debug Info:', 'smart-faq-manager' ),
                esc_html( $debug_message )
            );
        }
        
        // Add action hook before widget
        do_action('smart_faq_before_widget', $faqs, $args);
        
        // Add search box if enabled
        $enable_search = (get_option('smart_faq_enable_search', 0) == 1);
        if ($enable_search && !isset($args['disable_search'])) {
            echo wp_kses_post( self::render_search_box($args) );
        }
        
        // Include template
        if ($template) {
            include $template;
        } else {
            self::render_default_template($faqs, $args);
        }
        
        // Add action hook after widget
        do_action('smart_faq_after_widget', $faqs, $args);
        
        // Add schema markup
        if ($args['show_schema']) {
            $schema_graph = self::render_schema_markup($faqs);
            if (!empty($schema_graph)) {
                $schema_json = wp_json_encode(
                    $schema_graph,
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                );
                if (false !== $schema_json) {
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD output requires raw JSON string.
                    echo '<script type="application/ld+json">' . $schema_json . '</script>';
                }
            }
        }
        
        $output = ob_get_clean();
        
        return apply_filters('smart_faq_widget_html', $output, $faqs, $args);
    }
    
    /**
     * Locate template file
     *
     * @param string $style Template style
     * @return string|false Template path or false
     */
    private static function locate_template($style) {
        $template_name = "faq-{$style}.php";
        
        // Check if template exists in theme
        $theme_template = locate_template(array(
            "smart-faq/{$template_name}",
            $template_name,
        ));
        
        if ($theme_template) {
            return $theme_template;
        }
        
        // Use plugin template
        $plugin_template = SMART_FAQ_PLUGIN_DIR . "templates/{$template_name}";
        
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
        
        // Apply filter for custom template paths
        return apply_filters('smart_faq_template_path', false, $style);
    }
    
    /**
     * Render default template
     *
     * @param array $faqs FAQs
     * @param array $args Arguments
     */
    private static function render_default_template($faqs, $args) {
        $counter = 1;
        $custom_class = isset($args['custom_class']) ? $args['custom_class'] : '';
        $style = isset($args['style']) ? $args['style'] : 'list';
        $show_numbers = isset($args['show_numbers']) ? ($args['show_numbers'] == 1 || $args['show_numbers'] === '1' || $args['show_numbers'] === true) : (get_option('smart_faq_show_numbers', 1) == 1);
        ?>
        <div class="smart-faq-widget smart-faq-<?php echo esc_attr($style); ?> <?php echo esc_attr($custom_class); ?>">
            <?php foreach ($faqs as $faq) : ?>
                <?php do_action('smart_faq_before_faq_item', $faq, $args); ?>
                <div class="smart-faq-item" data-faq-id="<?php echo esc_attr($faq->id); ?>">
                    <div class="smart-faq-question">
                        <?php if ($show_numbers) : ?>
                            <span class="smart-faq-number"><?php echo esc_html($counter); ?>.</span>
                        <?php endif; ?>
                        <?php echo wp_kses_post($faq->question_html); ?>
                    </div>
                    <div class="smart-faq-answer">
                        <?php echo wp_kses_post($faq->answer_html); ?>
                    </div>
                </div>
                <?php do_action('smart_faq_after_faq_item', $faq, $args); ?>
                <?php $counter++; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render empty state
     *
     * @return string HTML output
     */
    private static function render_empty_state() {
        if (current_user_can('manage_options')) {
            return '<div class="smart-faq-empty">' . 
                   esc_html__('No relevant FAQs found. Add FAQs in the WordPress admin.', 'smart-faq-manager') . 
                   '</div>';
        }
        return '';
    }
    
    /**
     * Render Schema.org markup
     *
     * @param array $faqs FAQs
     * @return array JSON-LD schema graph
     */
    private static function render_schema_markup($faqs) {
        if (empty($faqs)) {
            return array();
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array(),
        );
        
        foreach ($faqs as $faq) {
            // Clean and validate question text
            $question_text = self::sanitize_schema_text($faq->question_html);
            if (empty($question_text)) {
                continue; // Skip if no valid question
            }
            
            // Clean and validate answer text
            $answer_text = self::sanitize_schema_text($faq->answer_html);
            if (empty($answer_text)) {
                continue; // Skip if no valid answer
            }
            
            $question_item = array(
                '@type' => 'Question',
                'name' => $question_text,
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                ),
            );
            
            // Use 'text' property for plain text answers
            // Add 'html' property if the answer contains rich content
            if ($faq->answer_html !== wp_strip_all_tags($faq->answer_html, false)) {
                // Answer contains HTML, provide both formats
                $question_item['acceptedAnswer']['html'] = wp_kses_post($faq->answer_html);
                $question_item['acceptedAnswer']['text'] = $answer_text;
            } else {
                // Plain text answer
                $question_item['acceptedAnswer']['text'] = $answer_text;
            }
            
            // Add FAQ ID as identifier if available
            if (isset($faq->id)) {
                $question_item['identifier'] = 'faq-' . intval($faq->id);
            }
            
            $schema['mainEntity'][] = $question_item;
        }
        
        // Only return markup if we have valid FAQ entries
        if (empty($schema['mainEntity'])) {
            return array();
        }

        return $schema;
    }
    
    /**
     * Sanitize text for schema markup
     *
     * @param string $html HTML content
     * @return string Cleaned text
     */
    private static function sanitize_schema_text($html) {
        if (empty($html)) {
            return '';
        }
        
        // Remove all HTML tags and decode entities
        $text = wp_strip_all_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Trim whitespace and normalize line breaks
        $text = trim($text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Ensure text is not empty after processing
        return $text;
    }
    
    /**
     * Track FAQ display for analytics
     *
     * @param array $faqs FAQs displayed
     * @param int $page_id Page ID
     */
    private static function track_faq_display($faqs, $page_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'smart_faq_analytics';
        
        $user_id = get_current_user_id();
        $displayed_at = current_time('mysql');
        
        foreach ($faqs as $faq) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Tracking analytics in custom plugin table.
            $wpdb->insert($table, array(
                'faq_id' => $faq->id,
                'page_id' => $page_id,
                'displayed_at' => $displayed_at,
                'user_id' => $user_id,
                'interaction_type' => 'display',
            ));
        }
    }
    
    /**
     * Render search box
     *
     * @param array $args Arguments
     * @return string Search box HTML
     */
    private static function render_search_box($args) {
        $placeholder = get_option('smart_faq_search_placeholder', __('Search FAQs...', 'smart-faq-manager'));
        
        ob_start();
        ?>
        <div class="smart-faq-search">
            <input type="text" 
                   class="smart-faq-search-input" 
                   placeholder="<?php echo esc_attr($placeholder); ?>"
                   aria-label="<?php echo esc_attr($placeholder); ?>">
            <span class="smart-faq-search-clear" style="display: none;">&times;</span>
            <span class="smart-faq-search-count"></span>
        </div>
        <?php
        return ob_get_clean();
    }
}
