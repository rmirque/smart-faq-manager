<?php
/**
 * Appearance Settings Page Handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Appearance_Settings
 */
class Smart_FAQ_Appearance_Settings {
    
    /**
     * Initialize appearance settings
     */
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_init', array(__CLASS__, 'handle_form_submission'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        
        // AJAX handlers
        add_action('wp_ajax_smart_faq_apply_template', array(__CLASS__, 'ajax_apply_template'));
        add_action('wp_ajax_smart_faq_save_template', array(__CLASS__, 'ajax_save_template'));
    }
    
    /**
     * Register appearance settings
     */
    public static function register_settings() {
        // Simply register all settings for sanitization - no UI rendering needed since we use direct field rendering
        $settings = array(
            'smart_faq_question_bg_color',
            'smart_faq_question_text_color',
            'smart_faq_answer_bg_color',
            'smart_faq_answer_text_color',
            'smart_faq_accent_color',
            'smart_faq_border_color',
            'smart_faq_hover_bg_color',
            'smart_faq_font_family',
            'smart_faq_question_font_size',
            'smart_faq_answer_font_size',
            'smart_faq_question_font_weight',
            'smart_faq_border_radius',
            'smart_faq_question_padding',
            'smart_faq_answer_padding',
            'smart_faq_item_margin',
            'smart_faq_border_width',
            'smart_faq_enable_box_shadow',
            'smart_faq_shadow_color',
            'smart_faq_shadow_blur',
            'smart_faq_enable_hover_effect',
            'smart_faq_transition_speed',
            'smart_faq_animation_type',
            'smart_faq_animation_duration',
            'smart_faq_animation_easing',
            'smart_faq_gradient_enabled',
            'smart_faq_gradient_color_1',
            'smart_faq_gradient_color_2',
            'smart_faq_gradient_angle',
            'smart_faq_icon_style',
            'smart_faq_icon_position',
            'smart_faq_icon_size',
            'smart_faq_border_style',
            'smart_faq_custom_css',
            'smart_faq_active_template',
        );
        
        foreach ($settings as $setting) {
            register_setting('smart-faq-appearance', $setting, array(__CLASS__, 'sanitize_setting'));
        }
    }
    
    /**
     * Handle form submission manually to ensure all fields are saved
     */
    public static function handle_form_submission() {
        if (!isset($_POST['submit']) || !isset($_POST['smart_faq_appearance_nonce'])) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Verify nonce
        $appearance_nonce = sanitize_text_field( wp_unslash( $_POST['smart_faq_appearance_nonce'] ) );
        if (!wp_verify_nonce( $appearance_nonce, 'smart_faq_appearance_save')) {
            return;
        }
        
        // Define all possible fields
        $fields = array(
            // Colors
            'smart_faq_question_bg_color',
            'smart_faq_question_text_color',
            'smart_faq_answer_bg_color',
            'smart_faq_answer_text_color',
            'smart_faq_accent_color',
            'smart_faq_border_color',
            'smart_faq_hover_bg_color',
            'smart_faq_shadow_color',
            
            // Typography
            'smart_faq_font_family',
            'smart_faq_question_font_size',
            'smart_faq_answer_font_size',
            'smart_faq_question_font_weight',
            
            // Spacing
            'smart_faq_border_radius',
            'smart_faq_question_padding',
            'smart_faq_answer_padding',
            'smart_faq_item_margin',
            'smart_faq_border_width',
            
            // Effects
            'smart_faq_enable_box_shadow',
            'smart_faq_shadow_blur',
            'smart_faq_enable_hover_effect',
            'smart_faq_transition_speed',
            
            // New settings
            'smart_faq_animation_type',
            'smart_faq_animation_duration',
            'smart_faq_animation_easing',
            'smart_faq_gradient_enabled',
            'smart_faq_gradient_color_1',
            'smart_faq_gradient_color_2',
            'smart_faq_gradient_angle',
            'smart_faq_icon_style',
            'smart_faq_icon_position',
            'smart_faq_icon_size',
            'smart_faq_border_style',
            'smart_faq_custom_css',
            'smart_faq_active_template',
        );
        
        // Save all fields
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $raw = wp_unslash( $_POST[$field] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized per field below.
                // Handle checkboxes
                if (in_array($field, array('smart_faq_enable_box_shadow', 'smart_faq_enable_hover_effect', 'smart_faq_gradient_enabled'), true)) {
                    update_option($field, $raw === '1' ? '1' : '0');
                } elseif ($field === 'smart_faq_custom_css') {
                    // Special handling for custom CSS - preserve CSS while removing potentially dangerous content
                    update_option($field, wp_kses_post( $raw ));
                } else {
                    update_option($field, sanitize_text_field( $raw ));
                }
            } else {
                // Unset checkboxes if not submitted
                if (in_array($field, array('smart_faq_enable_box_shadow', 'smart_faq_enable_hover_effect', 'smart_faq_gradient_enabled'), true)) {
                    update_option($field, '0');
                }
            }
        }
        
        // Clear cache after saving
        if (class_exists('Smart_FAQ_Style_Generator')) {
            Smart_FAQ_Style_Generator::clear_styles_cache();
        }
    }
    
    /**
     * Render appearance page
     */
    public function render_appearance_page() {
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have permission to access this page.', 'smart-faq-manager') );
        }
        
        include SMART_FAQ_PLUGIN_DIR . 'admin/partials/appearance-page.php';
    }
    
    /**
     * Enqueue assets for appearance page
     */
    public static function enqueue_assets($hook) {
        if ($hook !== 'faq-manager_page_smart-faq-appearance') {
            return;
        }
        
        // WordPress color picker and core assets
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // CodeMirror for CSS editor (if needed for custom CSS)
        wp_enqueue_script('codemirror');
        wp_enqueue_script('codemirror-css');
        wp_enqueue_style('codemirror');
        
        // Custom appearance page styles
        wp_enqueue_style(
            'smart-faq-appearance',
            SMART_FAQ_PLUGIN_URL . 'admin/css/admin-styles.css',
            array('wp-color-picker', 'codemirror'),
            SMART_FAQ_VERSION
        );
        
        // Localize script data for AJAX (using a dummy handle since we inline JS now)
        wp_add_inline_script('wp-color-picker', 'var smartFaqAdmin = ' . wp_json_encode(array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('smart_faq_admin_nonce')
        )) . ';');
    }
    
    /**
     * Get available templates
     */
    public static function get_available_templates() {
        if (class_exists('Smart_FAQ_Style_Templates')) {
            return Smart_FAQ_Style_Templates::get_available_templates();
        }
        return array();
    }
    
    /**
     * Apply template
     */
    public static function apply_template($template_id) {
        if (class_exists('Smart_FAQ_Style_Templates')) {
            return Smart_FAQ_Style_Templates::apply_template($template_id);
        }
        return false;
    }
    
    /**
     * Get current template
     */
    public static function get_current_template() {
        if (class_exists('Smart_FAQ_Style_Templates')) {
            return Smart_FAQ_Style_Templates::get_current_template();
        }
        return null;
    }
    
    /**
     * Export current as template
     */
    public static function export_current_as_template($template_name, $template_description) {
        if (class_exists('Smart_FAQ_Style_Templates')) {
            return Smart_FAQ_Style_Templates::export_current_as_template($template_name, $template_description);
        }
        return array();
    }
    
    /**
     * Sanitize setting
     */
    public static function sanitize_setting($value) {
        // Sanitize hex colors
        if (preg_match('/^#[a-fA-F0-9]{6}$/', $value) || preg_match('/^#[a-fA-F0-9]{3}$/', $value)) {
            return sanitize_hex_color($value);
        }
        
        // Sanitize rgba colors
        if (strpos($value, 'rgba') === 0 || strpos($value, 'rgb') === 0) {
            return sanitize_text_field($value);
        }
        
        // Sanitize numbers
        if (is_numeric($value)) {
            return absint($value);
        }
        
        // Default: sanitize as text
        return sanitize_text_field($value);
    }
    
    /**
     * Render template preview HTML
     */
    public static function render_template_preview($template) {
        // Gracefully handle templates with no explicit colors (e.g., Modern Professional)
        $colors = isset($template['colors']) ? $template['colors'] : array();
        $typography = isset($template['typography']) ? $template['typography'] : array();
        $spacing = isset($template['spacing']) ? $template['spacing'] : array();
        $effects = isset($template['effects']) ? $template['effects'] : array();
        
        // Build inline styles
        // Use sensible defaults mirroring base public CSS
        $question_bg = isset($colors['question_bg_color']) ? $colors['question_bg_color'] : '#f9f9f9';
        $question_text = isset($colors['question_text_color']) ? $colors['question_text_color'] : '#333333';
        $answer_bg = isset($colors['answer_bg_color']) ? $colors['answer_bg_color'] : '#ffffff';
        $answer_text = isset($colors['answer_text_color']) ? $colors['answer_text_color'] : '#555555';
        $accent_color = isset($colors['accent_color']) ? $colors['accent_color'] : '#2271b1';
        $border_color = isset($colors['border_color']) ? $colors['border_color'] : '#dddddd';
        
        $question_font_size = isset($typography['question_font_size']) ? $typography['question_font_size'] : '16';
        $answer_font_size = isset($typography['answer_font_size']) ? $typography['answer_font_size'] : '16';
        $border_radius = isset($spacing['border_radius']) ? $spacing['border_radius'] : '4';
        $question_padding = isset($spacing['question_padding']) ? $spacing['question_padding'] : '12';
        
        // Generate mini FAQ preview
        $preview_html = '
        <div class="template-preview-faq" style="transform: scale(0.6); transform-origin: top left; width: 167%; height: 200px; overflow: hidden; pointer-events: none;">
            <div style="
                background: ' . esc_attr($question_bg) . ';
                color: ' . esc_attr($question_text) . ';
                padding: ' . esc_attr($question_padding) . 'px;
                border: 1px solid ' . esc_attr($border_color) . ';
                border-radius: ' . esc_attr($border_radius) . 'px ' . esc_attr($border_radius) . 'px 0 0;
                font-size: ' . esc_attr($question_font_size) . 'px;
                font-weight: 600;
                margin-bottom: 1px;
            ">
                What is your return policy?
            </div>
            <div style="
                background: ' . esc_attr($answer_bg) . ';
                color: ' . esc_attr($answer_text) . ';
                padding: ' . esc_attr($question_padding) . 'px;
                border: 1px solid ' . esc_attr($border_color) . ';
                border-radius: 0 0 ' . esc_attr($border_radius) . 'px ' . esc_attr($border_radius) . 'px;
                font-size: ' . esc_attr($answer_font_size) . 'px;
                margin-bottom: 8px;
            ">
                We accept returns within 30 days...
            </div>
            <div style="
                background: ' . esc_attr($question_bg) . ';
                color: ' . esc_attr($question_text) . ';
                padding: ' . esc_attr($question_padding) . 'px;
                border: 1px solid ' . esc_attr($border_color) . ';
                border-radius: ' . esc_attr($border_radius) . 'px;
                font-size: ' . esc_attr($question_font_size) . 'px;
                font-weight: 600;
            ">
                How long does shipping take?
            </div>
        </div>';
        
        return $preview_html;
    }
    
    /**
     * Render gradient settings card
     */
    public static function render_gradient_card() {
        $enabled = get_option('smart_faq_gradient_enabled', '0');
        $color1 = get_option('smart_faq_gradient_color_1', '#667eea');
        $color2 = get_option('smart_faq_gradient_color_2', '#764ba2');
        $angle = get_option('smart_faq_gradient_angle', '135');
        ?>
        <div class="smart-faq-control-card">
            <h4><?php esc_html_e('Gradient Background', 'smart-faq-manager'); ?></h4>
            <label>
                <input type="checkbox" name="smart_faq_gradient_enabled" value="1" <?php checked($enabled, '1'); ?>>
                <?php esc_html_e('Enable gradient backgrounds', 'smart-faq-manager'); ?>
            </label>
            <div class="smart-faq-control-group" style="margin-top: 15px;">
                <div>
                    <label><?php esc_html_e('Color 1', 'smart-faq-manager'); ?></label>
                    <input type="text" name="smart_faq_gradient_color_1" value="<?php echo esc_attr($color1); ?>" class="smart-faq-color-field">
                </div>
                <div>
                    <label><?php esc_html_e('Color 2', 'smart-faq-manager'); ?></label>
                    <input type="text" name="smart_faq_gradient_color_2" value="<?php echo esc_attr($color2); ?>" class="smart-faq-color-field">
                </div>
                <div>
                    <label><?php esc_html_e('Angle (deg)', 'smart-faq-manager'); ?></label>
                    <input type="number" name="smart_faq_gradient_angle" value="<?php echo esc_attr($angle); ?>" min="0" max="360" class="small-text">
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render animation settings card
     */
    public static function render_animation_card() {
        $type = get_option('smart_faq_animation_type', 'slide');
        $duration = get_option('smart_faq_animation_duration', '300');
        $easing = get_option('smart_faq_animation_easing', 'ease');
        ?>
        <div class="smart-faq-control-card">
            <h4><?php esc_html_e('Animation Settings', 'smart-faq-manager'); ?></h4>
            <div class="smart-faq-control-group">
                <div>
                    <label><?php esc_html_e('Animation Type', 'smart-faq-manager'); ?></label>
                    <select name="smart_faq_animation_type" class="regular-text">
                        <option value="none" <?php selected($type, 'none'); ?>><?php esc_html_e('None', 'smart-faq-manager'); ?></option>
                        <option value="fade" <?php selected($type, 'fade'); ?>><?php esc_html_e('Fade', 'smart-faq-manager'); ?></option>
                        <option value="slide" <?php selected($type, 'slide'); ?>><?php esc_html_e('Slide', 'smart-faq-manager'); ?></option>
                        <option value="zoom" <?php selected($type, 'zoom'); ?>><?php esc_html_e('Zoom', 'smart-faq-manager'); ?></option>
                        <option value="bounce" <?php selected($type, 'bounce'); ?>><?php esc_html_e('Bounce', 'smart-faq-manager'); ?></option>
                    </select>
                </div>
                <div>
                    <label><?php esc_html_e('Duration (ms)', 'smart-faq-manager'); ?></label>
                    <input type="number" name="smart_faq_animation_duration" value="<?php echo esc_attr($duration); ?>" min="100" max="1000" class="small-text">
                </div>
                <div>
                    <label><?php esc_html_e('Easing', 'smart-faq-manager'); ?></label>
                    <select name="smart_faq_animation_easing" class="regular-text">
                        <option value="ease" <?php selected($easing, 'ease'); ?>><?php esc_html_e('Ease', 'smart-faq-manager'); ?></option>
                        <option value="ease-in" <?php selected($easing, 'ease-in'); ?>><?php esc_html_e('Ease-in', 'smart-faq-manager'); ?></option>
                        <option value="ease-out" <?php selected($easing, 'ease-out'); ?>><?php esc_html_e('Ease-out', 'smart-faq-manager'); ?></option>
                        <option value="ease-in-out" <?php selected($easing, 'ease-in-out'); ?>><?php esc_html_e('Ease-in-out', 'smart-faq-manager'); ?></option>
                        <option value="linear" <?php selected($easing, 'linear'); ?>><?php esc_html_e('Linear', 'smart-faq-manager'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render icon settings card
     */
    public static function render_icon_card() {
        $style = get_option('smart_faq_icon_style', 'arrow-down');
        $position = get_option('smart_faq_icon_position', 'right');
        $size = get_option('smart_faq_icon_size', '16');
        ?>
        <div class="smart-faq-control-card">
            <h4><?php esc_html_e('Icon Settings', 'smart-faq-manager'); ?></h4>
            <div class="smart-faq-control-group">
                <div>
                    <label><?php esc_html_e('Icon Style', 'smart-faq-manager'); ?></label>
                    <select name="smart_faq_icon_style" class="regular-text">
                        <option value="arrow-down" <?php selected($style, 'arrow-down'); ?>><?php esc_html_e('Arrow Down', 'smart-faq-manager'); ?></option>
                        <option value="chevron-down" <?php selected($style, 'chevron-down'); ?>><?php esc_html_e('Chevron Down', 'smart-faq-manager'); ?></option>
                        <option value="plus-minus" <?php selected($style, 'plus-minus'); ?>><?php esc_html_e('Plus/Minus', 'smart-faq-manager'); ?></option>
                        <option value="none" <?php selected($style, 'none'); ?>><?php esc_html_e('None', 'smart-faq-manager'); ?></option>
                    </select>
                </div>
                <div>
                    <label><?php esc_html_e('Position', 'smart-faq-manager'); ?></label>
                    <select name="smart_faq_icon_position" class="regular-text">
                        <option value="right" <?php selected($position, 'right'); ?>><?php esc_html_e('Right', 'smart-faq-manager'); ?></option>
                        <option value="left" <?php selected($position, 'left'); ?>><?php esc_html_e('Left', 'smart-faq-manager'); ?></option>
                    </select>
                </div>
                <div>
                    <label><?php esc_html_e('Size (px)', 'smart-faq-manager'); ?></label>
                    <input type="number" name="smart_faq_icon_size" value="<?php echo esc_attr($size); ?>" min="8" max="32" class="small-text">
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler to apply template
     */
    public static function ajax_apply_template() {
        // Check nonce
        $nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if (empty($nonce) || !wp_verify_nonce( $nonce, 'smart_faq_admin_nonce')) {
            wp_send_json_error( esc_html__( 'Invalid nonce', 'smart-faq-manager' ) );
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error( esc_html__( 'Unauthorized', 'smart-faq-manager' ) );
            return;
        }

        $template_id = isset($_POST['template_id']) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';

        if (empty($template_id)) {
            wp_send_json_error( esc_html__( 'Template ID is required', 'smart-faq-manager' ) );
            return;
        }
        
        // Check if template exists
        if (class_exists('Smart_FAQ_Style_Templates')) {
            $template = Smart_FAQ_Style_Templates::get_template($template_id);
            if (!$template) {
                /* translators: %s: template identifier */
                wp_send_json_error( sprintf( esc_html__( 'Template not found: %s', 'smart-faq-manager' ), esc_html( $template_id ) ) );
                return;
            }
        }

        $success = self::apply_template($template_id);

        if ($success) {
            wp_send_json_success( esc_html__( 'Template applied successfully', 'smart-faq-manager' ) );
        } else {
            wp_send_json_error( esc_html__( 'Failed to apply template - check error logs', 'smart-faq-manager' ) );
        }
    }
    
    /**
     * AJAX handler to save current settings as template
     */
    public static function ajax_save_template() {
        check_ajax_referer('smart_faq_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__( 'Unauthorized', 'smart-faq-manager' ) );
        }
        
        $template_name = isset($_POST['template_name']) ? sanitize_text_field( wp_unslash( $_POST['template_name'] ) ) : '';
        $template_description = isset($_POST['template_description']) ? sanitize_text_field( wp_unslash( $_POST['template_description'] ) ) : '';
        
        if (empty($template_name)) {
            wp_send_json_error( esc_html__( 'Template name is required', 'smart-faq-manager' ) );
            return;
        }
        
        // Export current settings as template
        $template_data = self::export_current_as_template($template_name, $template_description);
        
        if (!empty($template_data)) {
            wp_send_json_success( esc_html__( 'Template saved successfully', 'smart-faq-manager' ) );
        } else {
            wp_send_json_error( esc_html__( 'Failed to save template', 'smart-faq-manager' ) );
        }
    }
}

