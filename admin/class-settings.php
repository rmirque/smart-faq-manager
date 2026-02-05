<?php
/**
 * Settings page handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Settings
 */
class Smart_FAQ_Settings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
        
        // Clear cache when critical display settings change
        add_action('update_option_smart_faq_max_display', array($this, 'clear_cache_on_setting_change'));
        add_action('update_option_smart_faq_matching_threshold', array($this, 'clear_cache_on_setting_change'));
        add_action('update_option_smart_faq_keyword_weight', array($this, 'clear_cache_on_setting_change'));
        add_action('update_option_smart_faq_content_weight', array($this, 'clear_cache_on_setting_change'));
        add_action('update_option_smart_faq_phrase_weight', array($this, 'clear_cache_on_setting_change'));
        add_action('update_option_smart_faq_priority_weight', array($this, 'clear_cache_on_setting_change'));
    }
    
    /**
     * Clear cache when settings change
     */
    public function clear_cache_on_setting_change() {
        Smart_FAQ_Cache_Manager::clear_all_cache();
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Cache settings section
        add_settings_section(
            'smart_faq_cache_settings',
            __('Cache Settings', 'smart-faq-manager'),
            array($this, 'render_cache_section'),
            'smart-faq-settings'
        );
        
        add_settings_field(
            'smart_faq_enable_cache',
            __('Enable Cache', 'smart-faq-manager'),
            array($this, 'render_checkbox_field'),
            'smart-faq-settings',
            'smart_faq_cache_settings',
            array('option_name' => 'smart_faq_enable_cache')
        );
        
        add_settings_field(
            'smart_faq_cache_duration',
            __('Cache Duration (hours)', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_cache_settings',
            array('option_name' => 'smart_faq_cache_duration', 'min' => 1, 'max' => 168)
        );
        
        // Display settings section
        add_settings_section(
            'smart_faq_display_settings',
            __('Display Settings', 'smart-faq-manager'),
            array($this, 'render_display_section'),
            'smart-faq-settings'
        );
        
        add_settings_field(
            'smart_faq_max_display',
            __('Maximum FAQs to Display', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_display_settings',
            array('option_name' => 'smart_faq_max_display', 'min' => 1, 'max' => 20)
        );
        
        add_settings_field(
            'smart_faq_display_style',
            __('Default Display Style', 'smart-faq-manager'),
            array($this, 'render_select_field'),
            'smart-faq-settings',
            'smart_faq_display_settings',
            array(
                'option_name' => 'smart_faq_display_style',
                'options' => array(
                    'accordion' => __('Accordion', 'smart-faq-manager'),
                    'list' => __('List', 'smart-faq-manager'),
                    'grid' => __('Grid', 'smart-faq-manager'),
                ),
            )
        );
        
        add_settings_field(
            'smart_faq_show_numbers',
            __('Show Question Numbers', 'smart-faq-manager'),
            array($this, 'render_checkbox_field'),
            'smart-faq-settings',
            'smart_faq_display_settings',
            array('option_name' => 'smart_faq_show_numbers')
        );
        
        add_settings_field(
            'smart_faq_enable_search',
            __('Enable FAQ Search Box', 'smart-faq-manager'),
            array($this, 'render_checkbox_field'),
            'smart-faq-settings',
            'smart_faq_display_settings',
            array('option_name' => 'smart_faq_enable_search', 'label' => __('Show search box above FAQ widget', 'smart-faq-manager'))
        );
        
        add_settings_field(
            'smart_faq_search_placeholder',
            __('Search Placeholder Text', 'smart-faq-manager'),
            array($this, 'render_text_field'),
            'smart-faq-settings',
            'smart_faq_display_settings',
            array('option_name' => 'smart_faq_search_placeholder', 'default' => __('Search FAQs...', 'smart-faq-manager'))
        );
        
        add_settings_field(
            'smart_faq_show_permalinks',
            __('Show Permalink Icons', 'smart-faq-manager'),
            array($this, 'render_checkbox_field'),
            'smart-faq-settings',
            'smart_faq_display_settings',
            array('option_name' => 'smart_faq_show_permalinks', 'label' => __('Show link icons for direct FAQ linking', 'smart-faq-manager'))
        );
        
        add_settings_field(
            'smart_faq_enable_schema',
            __('Enable FAQ Schema Markup', 'smart-faq-manager'),
            array($this, 'render_checkbox_field'),
            'smart-faq-settings',
            'smart_faq_display_settings',
            array('option_name' => 'smart_faq_enable_schema', 'label' => __('Generate Schema.org JSON-LD markup for better SEO', 'smart-faq-manager'))
        );
        
        // Matching algorithm settings section
        add_settings_section(
            'smart_faq_algorithm_settings',
            __('Matching Algorithm Settings', 'smart-faq-manager'),
            array($this, 'render_algorithm_section'),
            'smart-faq-settings'
        );
        
        add_settings_field(
            'smart_faq_matching_threshold',
            __('Minimum Relevance Threshold', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_algorithm_settings',
            array(
                'option_name' => 'smart_faq_matching_threshold', 
                'min' => 0, 
                'max' => 1, 
                'step' => 0.05,
                'description' => __('Recommended: 0.10-0.20. Lower = more matches, Higher = stricter matches.', 'smart-faq-manager')
            )
        );
        
        add_settings_field(
            'smart_faq_analysis_depth',
            __('Content Analysis Depth (words)', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_algorithm_settings',
            array('option_name' => 'smart_faq_analysis_depth', 'min' => 100, 'max' => 5000, 'step' => 100)
        );
        
        add_settings_field(
            'smart_faq_keyword_weight',
            __('Keyword Weight', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_algorithm_settings',
            array('option_name' => 'smart_faq_keyword_weight', 'min' => 0, 'max' => 1, 'step' => 0.1)
        );
        
        add_settings_field(
            'smart_faq_content_weight',
            __('Content Weight', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_algorithm_settings',
            array('option_name' => 'smart_faq_content_weight', 'min' => 0, 'max' => 1, 'step' => 0.1)
        );
        
        add_settings_field(
            'smart_faq_phrase_weight',
            __('Phrase Weight', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_algorithm_settings',
            array('option_name' => 'smart_faq_phrase_weight', 'min' => 0, 'max' => 1, 'step' => 0.1)
        );
        
        add_settings_field(
            'smart_faq_priority_weight',
            __('Priority Weight', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_algorithm_settings',
            array('option_name' => 'smart_faq_priority_weight', 'min' => 0, 'max' => 1, 'step' => 0.1)
        );
        
        add_settings_field(
            'smart_faq_category_boost',
            __('Category Match Boost', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_algorithm_settings',
            array('option_name' => 'smart_faq_category_boost', 'min' => 1, 'max' => 2, 'step' => 0.1)
        );
        
        // Analytics settings section
        add_settings_section(
            'smart_faq_analytics_settings',
            __('Analytics Settings', 'smart-faq-manager'),
            array($this, 'render_analytics_section'),
            'smart-faq-settings'
        );
        
        add_settings_field(
            'smart_faq_enable_analytics',
            __('Enable Analytics', 'smart-faq-manager'),
            array($this, 'render_checkbox_field'),
            'smart-faq-settings',
            'smart_faq_analytics_settings',
            array('option_name' => 'smart_faq_enable_analytics')
        );
        
        add_settings_field(
            'smart_faq_analytics_retention',
            __('Data Retention (days)', 'smart-faq-manager'),
            array($this, 'render_number_field'),
            'smart-faq-settings',
            'smart_faq_analytics_settings',
            array('option_name' => 'smart_faq_analytics_retention', 'min' => 7, 'max' => 365)
        );
        
        // Advanced settings section
        add_settings_section(
            'smart_faq_advanced_settings',
            __('Advanced Settings', 'smart-faq-manager'),
            array($this, 'render_advanced_section'),
            'smart-faq-settings'
        );
        
        add_settings_field(
            'smart_faq_custom_css',
            __('Custom CSS', 'smart-faq-manager'),
            array($this, 'render_textarea_field'),
            'smart-faq-settings',
            'smart_faq_advanced_settings',
            array('option_name' => 'smart_faq_custom_css')
        );
        
        // Register all options
        $options = array(
            'smart_faq_enable_cache',
            'smart_faq_cache_duration',
            'smart_faq_max_display',
            'smart_faq_display_style',
            'smart_faq_show_numbers',
            'smart_faq_enable_search',
            'smart_faq_search_placeholder',
            'smart_faq_show_permalinks',
            'smart_faq_matching_threshold',
            'smart_faq_analysis_depth',
            'smart_faq_keyword_weight',
            'smart_faq_content_weight',
            'smart_faq_phrase_weight',
            'smart_faq_priority_weight',
            'smart_faq_category_boost',
            'smart_faq_enable_analytics',
            'smart_faq_analytics_retention',
            'smart_faq_custom_css',
            'smart_faq_enable_schema',
        );
        
        foreach ($options as $option) {
            register_setting('smart-faq-settings', $option, array($this, 'sanitize_setting'));
        }
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have permission to access this page.', 'smart-faq-manager') );
        }
        
        include SMART_FAQ_PLUGIN_DIR . 'admin/partials/settings-page.php';
    }
    
    /**
     * Render section callbacks
     */
    public function render_cache_section() {
        echo '<p>' . esc_html__( 'Configure caching to improve performance.', 'smart-faq-manager' ) . '</p>';
    }
    
    public function render_display_section() {
        echo '<p>' . esc_html__( 'Configure how FAQs are displayed on your site.', 'smart-faq-manager' ) . '</p>';
    }
    
    public function render_algorithm_section() {
        echo '<p>' . esc_html__( 'Fine-tune the FAQ matching algorithm. Total weights should equal 1.0.', 'smart-faq-manager' ) . '</p>';
    }
    
    public function render_analytics_section() {
        echo '<p>' . esc_html__( 'Configure analytics tracking for FAQ performance.', 'smart-faq-manager' ) . '</p>';
    }
    
    public function render_advanced_section() {
        echo '<p>' . esc_html__( 'Advanced configuration options.', 'smart-faq-manager' ) . '</p>';
    }
    
    /**
     * Render checkbox field
     */
    public function render_checkbox_field($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name, 0);
        ?>
        <label>
            <input type="hidden" name="<?php echo esc_attr($option_name); ?>" value="0" />
            <input type="checkbox" name="<?php echo esc_attr($option_name); ?>" value="1" <?php checked($value, 1); ?> />
            <?php echo isset($args['label']) ? esc_html($args['label']) : ''; ?>
        </label>
        <?php
    }
    
    /**
     * Render number field
     */
    public function render_number_field($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name);
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 100;
        $step = isset($args['step']) ? $args['step'] : 1;
        ?>
        <input type="number" name="<?php echo esc_attr($option_name); ?>" value="<?php echo esc_attr($value); ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" step="<?php echo esc_attr($step); ?>" class="small-text" />
        <?php if (isset($args['description'])) : ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }
    
    /**
     * Render select field
     */
    public function render_select_field($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name);
        $options = $args['options'];
        ?>
        <select name="<?php echo esc_attr($option_name); ?>">
            <?php foreach ($options as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    /**
     * Render text field
     */
    public function render_text_field($args) {
        $option_name = $args['option_name'];
        $default = isset($args['default']) ? $args['default'] : '';
        $value = get_option($option_name, $default);
        ?>
        <input type="text" name="<?php echo esc_attr($option_name); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php
    }
    
    /**
     * Render textarea field
     */
    public function render_textarea_field($args) {
        $option_name = $args['option_name'];
        $value = get_option($option_name);
        ?>
        <textarea name="<?php echo esc_attr($option_name); ?>" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($value); ?></textarea>
        <?php
    }
    
    /**
     * Sanitize setting
     */
    public function sanitize_setting($value) {
        return sanitize_text_field($value);
    }
}

