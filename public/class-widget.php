<?php
/**
 * WordPress Widget
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Widget
 */
class Smart_FAQ_Widget extends WP_Widget {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'smart_faq_widget',
            __('Smart FAQ', 'smart-faq-manager'),
            array(
                'description' => __('Display contextually relevant FAQs', 'smart-faq-manager'),
                'classname' => 'smart-faq-widget',
            )
        );
    }
    
    /**
     * Widget output
     *
     * @param array $args Widget arguments
     * @param array $instance Widget instance
     */
    public function widget($args, $instance) {
        $page_id = get_the_ID();
        
        if (!$page_id) {
            return;
        }
        
        // Get settings - 0 or empty means use global setting
        $limit = (isset($instance['limit']) && absint($instance['limit']) > 0) ? absint($instance['limit']) : get_option('smart_faq_max_display', 5);
        $category = !empty($instance['category']) ? sanitize_text_field($instance['category']) : '';
        $style = !empty($instance['style']) ? sanitize_text_field($instance['style']) : 'list';
        $show_title = !empty($instance['show_title']) ? $instance['show_title'] : false;
        $title = !empty($instance['title']) ? $instance['title'] : __('Frequently Asked Questions', 'smart-faq-manager');
        
        // Get matching FAQs
        $matcher_args = array(
            'limit' => $limit,
            'category' => $category,
        );
        
        $faqs = Smart_FAQ_Matcher::find_matching_faqs($page_id, $matcher_args);
        
        if (empty($faqs)) {
            return;
        }
        
        // Output widget
        echo isset( $args['before_widget'] ) ? wp_kses_post( $args['before_widget'] ) : '';

        if ($show_title) {
            echo isset( $args['before_title'] ) ? wp_kses_post( $args['before_title'] ) : '';
            echo esc_html($title);
            echo isset( $args['after_title'] ) ? wp_kses_post( $args['after_title'] ) : '';
        }
        
        $renderer_args = array(
            'style' => $style,
            'show_numbers' => false,
            'show_schema' => false, // Schema already in shortcode/block
        );
        
        echo wp_kses_post( Smart_FAQ_Widget_Renderer::render($faqs, $renderer_args) );

        echo isset( $args['after_widget'] ) ? wp_kses_post( $args['after_widget'] ) : '';
    }
    
    /**
     * Widget form
     *
     * @param array $instance Widget instance
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Frequently Asked Questions', 'smart-faq-manager');
        $show_title = !empty($instance['show_title']) ? $instance['show_title'] : false;
        $limit = !empty($instance['limit']) ? absint($instance['limit']) : get_option('smart_faq_max_display', 5);
        $category = !empty($instance['category']) ? $instance['category'] : '';
        $style = !empty($instance['style']) ? $instance['style'] : 'list';
        
        $categories = Smart_FAQ_Database::get_categories();
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'smart-faq-manager'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label>
                <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_title')); ?>" name="<?php echo esc_attr($this->get_field_name('show_title')); ?>" value="1" <?php checked($show_title, 1); ?>>
                <?php esc_html_e('Show Title', 'smart-faq-manager'); ?>
            </label>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                <?php esc_html_e('Number of FAQs:', 'smart-faq-manager'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" min="0" max="20" value="<?php echo esc_attr($limit); ?>" placeholder="<?php echo esc_attr(get_option('smart_faq_max_display', 5)); ?>">
            <br>
            <span class="description">
                <?php
                /* translators: %d: global maximum FAQs to display. */
                printf(
                    /* translators: %d: global maximum FAQs to display. */
                    esc_html__( 'Leave blank or set to 0 to use global setting (%d)', 'smart-faq-manager' ),
                    absint( get_option( 'smart_faq_max_display', 5 ) )
                );
                ?>
            </span>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>">
                <?php esc_html_e('Display Style:', 'smart-faq-manager'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="accordion" <?php selected($style, 'accordion'); ?>><?php esc_html_e('Accordion', 'smart-faq-manager'); ?></option>
                <option value="list" <?php selected($style, 'list'); ?>><?php esc_html_e('List', 'smart-faq-manager'); ?></option>
                <option value="grid" <?php selected($style, 'grid'); ?>><?php esc_html_e('Grid', 'smart-faq-manager'); ?></option>
            </select>
        </p>
        
        <?php if (!empty($categories)) : ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('category')); ?>">
                    <?php esc_html_e('Filter by Category:', 'smart-faq-manager'); ?>
                </label>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                    <option value=""><?php esc_html_e('All Categories', 'smart-faq-manager'); ?></option>
                    <?php foreach ($categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat); ?>" <?php selected($category, $cat); ?>>
                            <?php echo esc_html($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php endif; ?>
        <?php
    }
    
    /**
     * Update widget
     *
     * @param array $new_instance New instance
     * @param array $old_instance Old instance
     * @return array Updated instance
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_title'] = !empty($new_instance['show_title']) ? 1 : 0;
        // 0 or empty = use global setting
        $instance['limit'] = isset($new_instance['limit']) ? absint($new_instance['limit']) : 0;
        $instance['category'] = !empty($new_instance['category']) ? sanitize_text_field($new_instance['category']) : '';
        $instance['style'] = !empty($new_instance['style']) ? sanitize_text_field($new_instance['style']) : 'list';
        
        return $instance;
    }
}
