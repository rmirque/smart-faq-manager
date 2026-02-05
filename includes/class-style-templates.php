<?php
/**
 * Style Templates - Pre-built FAQ design templates
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Style_Templates
 */
class Smart_FAQ_Style_Templates {
    
    /**
     * Get all available templates
     *
     * @return array Template definitions
     */
    public static function get_available_templates() {
        return array(
            'modern-professional' => array(
                'name' => __('Modern Professional', 'smart-faq-manager'),
                'description' => __('Clean, modern look that follows the plugin’s default styling and your theme’s fonts', 'smart-faq-manager'),
                // Keep arrays empty so we don’t override the base CSS/theme styles
                'colors' => array(),
                'typography' => array(
                    'font_family' => 'inherit',
                ),
                'spacing' => array(),
                'effects' => array(),
            ),
            'modern-gradient' => array(
                'name' => __('Modern Gradient', 'smart-faq-manager'),
                'description' => __('Purple/blue gradients, rounded corners, smooth shadows', 'smart-faq-manager'),
                'colors' => array(
                    'question_bg_color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'question_text_color' => '#ffffff',
                    'answer_bg_color' => '#ffffff',
                    'answer_text_color' => '#374151',
                    'accent_color' => '#667eea',
                    'border_color' => '#e5e7eb',
                    'hover_bg_color' => 'linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%)',
                    'shadow_color' => 'rgba(102, 126, 234, 0.2)',
                ),
                'typography' => array(
                    'font_family' => 'inherit',
                    'question_font_size' => '16',
                    'answer_font_size' => '16',
                    'question_font_weight' => '600',
                ),
                'spacing' => array(
                    'border_radius' => '12',
                    'question_padding' => '20',
                    'answer_padding' => '20',
                    'item_margin' => '15',
                    'border_width' => '0',
                ),
                'effects' => array(
                    'enable_box_shadow' => '1',
                    'shadow_blur' => '15',
                    'enable_hover_effect' => '1',
                    'transition_speed' => '300',
                    'animation_type' => 'slide',
                    'animation_duration' => '300',
                    'gradient_enabled' => '1',
                ),
            ),
            
            'minimal-clean' => array(
                'name' => __('Minimal Clean', 'smart-faq-manager'),
                'description' => __('Lots of whitespace, thin borders, light colors', 'smart-faq-manager'),
                'colors' => array(
                    'question_bg_color' => 'transparent',
                    'question_text_color' => '#1f2937',
                    'answer_bg_color' => 'transparent',
                    'answer_text_color' => '#4b5563',
                    'accent_color' => '#10b981',
                    'border_color' => '#f3f4f6',
                    'hover_bg_color' => '#f9fafb',
                    'shadow_color' => 'rgba(0, 0, 0, 0.05)',
                ),
                'typography' => array(
                    'font_family' => 'inherit',
                    'question_font_size' => '16',
                    'answer_font_size' => '16',
                    'question_font_weight' => '500',
                ),
                'spacing' => array(
                    'border_radius' => '0',
                    'question_padding' => '24',
                    'answer_padding' => '24',
                    'item_margin' => '20',
                    'border_width' => '0',
                ),
                'effects' => array(
                    'enable_box_shadow' => '0',
                    'shadow_blur' => '4',
                    'enable_hover_effect' => '1',
                    'transition_speed' => '200',
                    'animation_type' => 'fade',
                    'animation_duration' => '200',
                    'gradient_enabled' => '0',
                ),
            ),
            'bold-vibrant' => array(
                'name' => __('Bold & Vibrant', 'smart-faq-manager'),
                'description' => __('Bright accent colors, thick borders, high contrast', 'smart-faq-manager'),
                'colors' => array(
                    'question_bg_color' => '#fef3c7',
                    'question_text_color' => '#92400e',
                    'answer_bg_color' => '#ffffff',
                    'answer_text_color' => '#374151',
                    'accent_color' => '#f59e0b',
                    'border_color' => '#f59e0b',
                    'hover_bg_color' => '#fde68a',
                    'shadow_color' => 'rgba(245, 158, 11, 0.3)',
                ),
                'typography' => array(
                    'font_family' => 'Arial, sans-serif',
                    'question_font_size' => '18',
                    'answer_font_size' => '16',
                    'question_font_weight' => '700',
                ),
                'spacing' => array(
                    'border_radius' => '8',
                    'question_padding' => '20',
                    'answer_padding' => '20',
                    'item_margin' => '16',
                    'border_width' => '3',
                ),
                'effects' => array(
                    'enable_box_shadow' => '1',
                    'shadow_blur' => '10',
                    'enable_hover_effect' => '1',
                    'transition_speed' => '200',
                    'animation_type' => 'bounce',
                    'animation_duration' => '400',
                    'gradient_enabled' => '0',
                ),
            ),
            'dark-mode' => array(
                'name' => __('Dark Mode', 'smart-faq-manager'),
                'description' => __('Dark backgrounds, light text, glowing accents', 'smart-faq-manager'),
                'colors' => array(
                    'question_bg_color' => '#1f2937',
                    'question_text_color' => '#f9fafb',
                    'answer_bg_color' => '#111827',
                    'answer_text_color' => '#d1d5db',
                    'accent_color' => '#06b6d4',
                    'border_color' => '#374151',
                    'hover_bg_color' => '#374151',
                    'shadow_color' => 'rgba(6, 182, 212, 0.3)',
                ),
                'typography' => array(
                    'font_family' => 'inherit',
                    'question_font_size' => '16',
                    'answer_font_size' => '16',
                    'question_font_weight' => '600',
                ),
                'spacing' => array(
                    'border_radius' => '10',
                    'question_padding' => '20',
                    'answer_padding' => '20',
                    'item_margin' => '15',
                    'border_width' => '1',
                ),
                'effects' => array(
                    'enable_box_shadow' => '1',
                    'shadow_blur' => '20',
                    'enable_hover_effect' => '1',
                    'transition_speed' => '300',
                    'animation_type' => 'zoom',
                    'animation_duration' => '300',
                    'gradient_enabled' => '0',
                ),
            ),
            'corporate-blue' => array(
                'name' => __('Corporate Blue', 'smart-faq-manager'),
                'description' => __('Professional blue theme, structured layout', 'smart-faq-manager'),
                'colors' => array(
                    'question_bg_color' => '#eff6ff',
                    'question_text_color' => '#1e40af',
                    'answer_bg_color' => '#ffffff',
                    'answer_text_color' => '#3730a3',
                    'accent_color' => '#2563eb',
                    'border_color' => '#dbeafe',
                    'hover_bg_color' => '#dbeafe',
                    'shadow_color' => 'rgba(37, 99, 235, 0.15)',
                ),
                'typography' => array(
                    'font_family' => 'inherit',
                    'question_font_size' => '16',
                    'answer_font_size' => '16',
                    'question_font_weight' => '600',
                ),
                'spacing' => array(
                    'border_radius' => '6',
                    'question_padding' => '18',
                    'answer_padding' => '18',
                    'item_margin' => '14',
                    'border_width' => '2',
                ),
                'effects' => array(
                    'enable_box_shadow' => '1',
                    'shadow_blur' => '8',
                    'enable_hover_effect' => '1',
                    'transition_speed' => '250',
                    'animation_type' => 'slide',
                    'animation_duration' => '250',
                    'gradient_enabled' => '0',
                ),
            ),
            'soft-pastel' => array(
                'name' => __('Soft Pastel', 'smart-faq-manager'),
                'description' => __('Gentle colors, soft shadows, rounded elements', 'smart-faq-manager'),
                'colors' => array(
                    'question_bg_color' => '#fdf2f8',
                    'question_text_color' => '#831843',
                    'answer_bg_color' => '#ffffff',
                    'answer_text_color' => '#be185d',
                    'accent_color' => '#ec4899',
                    'border_color' => '#fce7f3',
                    'hover_bg_color' => '#fce7f3',
                    'shadow_color' => 'rgba(236, 72, 153, 0.1)',
                ),
                'typography' => array(
                    'font_family' => 'inherit',
                    'question_font_size' => '16',
                    'answer_font_size' => '16',
                    'question_font_weight' => '500',
                ),
                'spacing' => array(
                    'border_radius' => '16',
                    'question_padding' => '22',
                    'answer_padding' => '22',
                    'item_margin' => '18',
                    'border_width' => '1',
                ),
                'effects' => array(
                    'enable_box_shadow' => '1',
                    'shadow_blur' => '12',
                    'enable_hover_effect' => '1',
                    'transition_speed' => '350',
                    'animation_type' => 'fade',
                    'animation_duration' => '350',
                    'gradient_enabled' => '0',
                ),
            ),
            'neumorphic' => array(
                'name' => __('Neumorphic', 'smart-faq-manager'),
                'description' => __('Modern soft UI, subtle 3D effects', 'smart-faq-manager'),
                'colors' => array(
                    'question_bg_color' => '#f0f0f3',
                    'question_text_color' => '#5a5a5a',
                    'answer_bg_color' => '#f8f9fa',
                    'answer_text_color' => '#495057',
                    'accent_color' => '#6c757d',
                    'border_color' => 'transparent',
                    'hover_bg_color' => '#e4e4e7',
                    'shadow_color' => 'rgba(255, 255, 255, 0.8)',
                ),
                'typography' => array(
                    'font_family' => 'inherit',
                    'question_font_size' => '16',
                    'answer_font_size' => '16',
                    'question_font_weight' => '500',
                ),
                'spacing' => array(
                    'border_radius' => '20',
                    'question_padding' => '24',
                    'answer_padding' => '24',
                    'item_margin' => '20',
                    'border_width' => '0',
                ),
                'effects' => array(
                    'enable_box_shadow' => '1',
                    'shadow_blur' => '15',
                    'enable_hover_effect' => '1',
                    'transition_speed' => '300',
                    'animation_type' => 'zoom',
                    'animation_duration' => '300',
                    'gradient_enabled' => '0',
                ),
            ),
        );
    }
    
    /**
     * Get template by ID
     *
     * @param string $template_id Template identifier
     * @return array|false Template array or false if not found
     */
    public static function get_template($template_id) {
        $templates = self::get_available_templates();
        return isset($templates[$template_id]) ? $templates[$template_id] : false;
    }
    
    /**
     * Apply template settings to WordPress options
     *
     * @param string $template_id Template identifier
     * @return bool Success status
     */
    public static function apply_template($template_id) {
        $template = self::get_template($template_id);
        
        if (!$template) {
            return false;
        }
        
        // Clear existing appearance settings to avoid carry-over between templates,
        // but preserve any user custom CSS.
        $preserve_custom_css = get_option('smart_faq_custom_css', '');
        if (class_exists('Smart_FAQ_Style_Generator')) {
            Smart_FAQ_Style_Generator::reset_appearance_settings();
            if ($preserve_custom_css !== '') {
                update_option('smart_faq_custom_css', $preserve_custom_css);
            }
        }
        
        // Apply colors
        if (isset($template['colors'])) {
            foreach ($template['colors'] as $option_name => $value) {
                update_option('smart_faq_' . $option_name, $value);
            }
        }
        
        // Apply typography
        if (isset($template['typography'])) {
            foreach ($template['typography'] as $option_name => $value) {
                update_option('smart_faq_' . $option_name, $value);
            }
        }
        
        // Apply spacing
        if (isset($template['spacing'])) {
            foreach ($template['spacing'] as $option_name => $value) {
                update_option('smart_faq_' . $option_name, $value);
            }
        }
        
        // Apply effects
        if (isset($template['effects'])) {
            foreach ($template['effects'] as $option_name => $value) {
                update_option('smart_faq_' . $option_name, $value);
            }
        }
        
        // Set active template
        update_option('smart_faq_active_template', $template_id);
        
        // Clear any caching to ensure frontend updates immediately
        delete_transient('smart_faq_custom_styles');
        wp_cache_delete('smart_faq_appearance_settings', 'smart_faq');
        
        // Force regenerate styles by calling the style generator
        if (class_exists('Smart_FAQ_Style_Generator')) {
            Smart_FAQ_Style_Generator::clear_styles_cache();
        }
        
        return true;
    }
    
    /**
     * Get currently active template
     *
     * @return string|null Active template ID or null
     */
    public static function get_current_template() {
        return get_option('smart_faq_active_template', null);
    }
    
    /**
     * Export current settings as template
     *
     * @param string $template_name Template name
     * @param string $template_description Template description
     * @return array Template definition
     */
    public static function export_current_as_template($template_name, $template_description) {
        return array(
            'name' => $template_name,
            'description' => $template_description,
            'colors' => array(
                'question_bg_color' => get_option('smart_faq_question_bg_color', ''),
                'question_text_color' => get_option('smart_faq_question_text_color', ''),
                'answer_bg_color' => get_option('smart_faq_answer_bg_color', ''),
                'answer_text_color' => get_option('smart_faq_answer_text_color', ''),
                'accent_color' => get_option('smart_faq_accent_color', ''),
                'border_color' => get_option('smart_faq_border_color', ''),
                'hover_bg_color' => get_option('smart_faq_hover_bg_color', ''),
                'shadow_color' => get_option('smart_faq_shadow_color', ''),
            ),
            'typography' => array(
                'font_family' => get_option('smart_faq_font_family', 'inherit'),
                'question_font_size' => get_option('smart_faq_question_font_size', ''),
                'answer_font_size' => get_option('smart_faq_answer_font_size', ''),
                'question_font_weight' => get_option('smart_faq_question_font_weight', ''),
            ),
            'spacing' => array(
                'border_radius' => get_option('smart_faq_border_radius', ''),
                'question_padding' => get_option('smart_faq_question_padding', ''),
                'answer_padding' => get_option('smart_faq_answer_padding', ''),
                'item_margin' => get_option('smart_faq_item_margin', ''),
                'border_width' => get_option('smart_faq_border_width', ''),
            ),
            'effects' => array(
                'enable_box_shadow' => get_option('smart_faq_enable_box_shadow', ''),
                'shadow_blur' => get_option('smart_faq_shadow_blur', ''),
                'enable_hover_effect' => get_option('smart_faq_enable_hover_effect', ''),
                'transition_speed' => get_option('smart_faq_transition_speed', ''),
                'animation_type' => get_option('smart_faq_animation_type', ''),
                'animation_duration' => get_option('smart_faq_animation_duration', ''),
                'gradient_enabled' => get_option('smart_faq_gradient_enabled', ''),
            ),
        );
    }
}
