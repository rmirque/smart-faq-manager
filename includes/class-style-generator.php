<?php
/**
 * Style Generator - Dynamic CSS based on appearance settings
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Style_Generator
 */
class Smart_FAQ_Style_Generator {
    
    /**
     * Generate custom styles based on appearance settings
     *
     * @return string CSS string
     */
    public static function generate_custom_styles() {
        $css = '';
        
        // Get all appearance settings
        $settings = self::get_appearance_settings();
        
        // If no customizations, return empty (use theme defaults)
        if (empty(array_filter($settings))) {
            return '';
        }
        
        // Start building CSS
        $css .= "/* Smart FAQ Manager - Custom Appearance */\n";
        
        // FAQ Item styles
        $css .= ".smart-faq-item {\n";
        if (!empty($settings['border_color'])) {
            $css .= "    border-color: {$settings['border_color']} !important;\n";
        }
        if (!empty($settings['border_width'])) {
            $css .= "    border-width: {$settings['border_width']}px !important;\n";
        }
        if (!empty($settings['border_radius'])) {
            $css .= "    border-radius: {$settings['border_radius']}px !important;\n";
        }
        if (!empty($settings['item_margin'])) {
            $css .= "    margin-bottom: {$settings['item_margin']}px !important;\n";
        }
        if (!empty($settings['answer_bg_color'])) {
            $css .= "    background: {$settings['answer_bg_color']} !important;\n";
        }
        $css .= "}\n\n";
        
        // Box shadow
        if (!empty($settings['enable_box_shadow']) && $settings['enable_box_shadow'] === '1') {
            $shadow_color = !empty($settings['shadow_color']) ? $settings['shadow_color'] : 'rgba(0,0,0,0.1)';
            $shadow_blur = !empty($settings['shadow_blur']) ? $settings['shadow_blur'] : '8';
            $css .= ".smart-faq-item:hover {\n";
            $css .= "    box-shadow: 0 2px {$shadow_blur}px {$shadow_color} !important;\n";
            $css .= "}\n\n";
        }
        
        // Question styles
        $css .= ".smart-faq-question {\n";
        if (!empty($settings['question_bg_color'])) {
            $css .= "    background: {$settings['question_bg_color']} !important;\n";
        }
        if (!empty($settings['question_text_color'])) {
            $css .= "    color: {$settings['question_text_color']} !important;\n";
        }
        if (!empty($settings['question_font_size'])) {
            $css .= "    font-size: {$settings['question_font_size']}px !important;\n";
        }
        if (!empty($settings['question_font_weight'])) {
            $css .= "    font-weight: {$settings['question_font_weight']} !important;\n";
        }
        if (!empty($settings['question_padding'])) {
            $css .= "    padding: {$settings['question_padding']}px !important;\n";
        }
        if (!empty($settings['font_family']) && $settings['font_family'] !== 'inherit') {
            $css .= "    font-family: {$settings['font_family']} !important;\n";
        }
        $css .= "}\n\n";
        
        // Hover effects
        if (!empty($settings['enable_hover_effect']) && $settings['enable_hover_effect'] === '1') {
            $css .= ".smart-faq-accordion .smart-faq-question:hover {\n";
            if (!empty($settings['hover_bg_color'])) {
                $css .= "    background: {$settings['hover_bg_color']} !important;\n";
            }
            $css .= "}\n\n";
        }
        
        // Transition speed
        if (!empty($settings['transition_speed'])) {
            $css .= ".smart-faq-item {\n";
            $css .= "    transition: all {$settings['transition_speed']}ms ease !important;\n";
            $css .= "}\n\n";
        }
        
        // Question styles
        $css .= ".smart-faq-question {\n";
        if (!empty($settings['question_bg_color'])) {
            $css .= "    background: {$settings['question_bg_color']} !important;\n";
        }
        if (!empty($settings['question_text_color'])) {
            $css .= "    color: {$settings['question_text_color']} !important;\n";
        }
        if (!empty($settings['question_font_size'])) {
            $css .= "    font-size: {$settings['question_font_size']}px !important;\n";
        }
        if (!empty($settings['question_padding'])) {
            $css .= "    padding: {$settings['question_padding']}px !important;\n";
        }
        if (!empty($settings['question_font_weight'])) {
            $css .= "    font-weight: {$settings['question_font_weight']} !important;\n";
        }
        if (!empty($settings['font_family']) && $settings['font_family'] !== 'inherit') {
            $css .= "    font-family: {$settings['font_family']} !important;\n";
        }
        $css .= "}\n\n";
        
        // Answer styles - use more specific selectors to preserve accordion behavior
        $css .= ".smart-faq-answer {\n";
        if (!empty($settings['answer_text_color'])) {
            $css .= "    color: {$settings['answer_text_color']} !important;\n";
        }
        if (!empty($settings['answer_font_size'])) {
            $css .= "    font-size: {$settings['answer_font_size']}px !important;\n";
        }
        if (!empty($settings['font_family']) && $settings['font_family'] !== 'inherit') {
            $css .= "    font-family: {$settings['font_family']} !important;\n";
        }
        if (!empty($settings['border_color'])) {
            $css .= "    border-top-color: {$settings['border_color']} !important;\n";
        }
        $css .= "}\n\n";
        
        // Apply background and padding only to visible/open answers to preserve accordion behavior
        if (!empty($settings['answer_bg_color']) || !empty($settings['answer_padding'])) {
            $css .= ".smart-faq-list .smart-faq-answer,\n";
            $css .= ".smart-faq-item.active .smart-faq-answer {\n";
            if (!empty($settings['answer_bg_color'])) {
                $css .= "    background: {$settings['answer_bg_color']} !important;\n";
            }
            if (!empty($settings['answer_padding'])) {
                $css .= "    padding: {$settings['answer_padding']}px !important;\n";
            }
            $css .= "}\n\n";
        }
        
        // Ensure accordion items start closed (override any template interference)
        $css .= ".smart-faq-accordion .smart-faq-item .smart-faq-answer {\n";
        $css .= "    max-height: 0 !important;\n";
        $css .= "    overflow: hidden !important;\n";
        $css .= "    padding: 0 20px !important;\n";
        $css .= "}\n\n";
        
        $css .= ".smart-faq-accordion .smart-faq-item.active .smart-faq-answer {\n";
        $css .= "    max-height: 2000px !important;\n";
        $css .= "    padding: 15px 20px !important;\n";
        if (!empty($settings['answer_padding'])) {
            $css .= "    padding: {$settings['answer_padding']}px !important;\n";
        }
        $css .= "}\n\n";
        
        // Number and accent colors
        if (!empty($settings['accent_color'])) {
            $css .= ".smart-faq-number {\n";
            $css .= "    color: {$settings['accent_color']} !important;\n";
            $css .= "}\n\n";
            
            $css .= ".smart-faq-accordion .smart-faq-question::after {\n";
            $css .= "    color: {$settings['accent_color']} !important;\n";
            $css .= "}\n\n";
        }
        
        // Enhanced features
        
        // Border style
        if (!empty($settings['border_style'])) {
            $css .= ".smart-faq-item {\n";
            $css .= "    border-style: {$settings['border_style']} !important;\n";
            $css .= "}\n\n";
        }
        
        // Gradient backgrounds
        if (!empty($settings['gradient_enabled']) && $settings['gradient_enabled'] === '1') {
            if (!empty($settings['gradient_color_1']) && !empty($settings['gradient_color_2'])) {
                $angle = !empty($settings['gradient_angle']) ? $settings['gradient_angle'] : '135';
                $gradient = "linear-gradient({$angle}deg, {$settings['gradient_color_1']}, {$settings['gradient_color_2']})";
                
                $css .= ".smart-faq-item {\n";
                $css .= "    background: {$gradient} !important;\n";
                $css .= "}\n\n";
                
                $css .= ".smart-faq-question {\n";
                $css .= "    background: {$gradient} !important;\n";
                $css .= "}\n\n";
            }
        }
        
        // Icon styling
        if (!empty($settings['icon_size'])) {
            $css .= ".smart-faq-question::after, .smart-faq-question::before {\n";
            $css .= "    font-size: {$settings['icon_size']}px !important;\n";
            $css .= "}\n\n";
        }
        
        if (!empty($settings['icon_position']) && $settings['icon_position'] === 'left') {
            $css .= ".smart-faq-question {\n";
            $css .= "    flex-direction: row-reverse !important;\n";
            $css .= "}\n\n";
            
            $css .= ".smart-faq-question::after {\n";
            $css .= "    margin-right: 8px !important;\n";
            $css .= "    margin-left: 0 !important;\n";
            $css .= "}\n\n";
        }
        
        // Animation styles
        if (!empty($settings['animation_type']) && $settings['animation_type'] !== 'none') {
            $duration = !empty($settings['animation_duration']) ? $settings['animation_duration'] : '300';
            $easing = !empty($settings['animation_easing']) ? $settings['animation_easing'] : 'ease';
            
            $css .= ".smart-faq-item {\n";
            $css .= "    transition: all {$duration}ms {$easing} !important;\n";
            $css .= "}\n\n";
            
            // Animation classes for different types
            switch ($settings['animation_type']) {
                case 'fade':
                    $css .= ".smart-faq-item.animating {\n";
                    $css .= "    opacity: 0;\n";
                    $css .= "    transition: opacity {$duration}ms {$easing} !important;\n";
                    $css .= "}\n\n";
                    break;
                    
                case 'slide':
                    // Only apply to accordion answers and preserve existing overflow behavior
                    $css .= ".smart-faq-accordion .smart-faq-answer {\n";
                    $css .= "    transition: max-height {$duration}ms {$easing}, padding {$duration}ms {$easing} !important;\n";
                    $css .= "}\n\n";
                    break;
                    
                case 'zoom':
                    $css .= ".smart-faq-item.animating {\n";
                    $css .= "    transform: scale(0.95);\n";
                    $css .= "    transition: transform {$duration}ms {$easing} !important;\n";
                    $css .= "}\n\n";
                    break;
            }
        }
        
        // Custom CSS - add at the end to allow overrides
        if (!empty($settings['custom_css'])) {
            $css .= "/* Custom CSS */\n";
            $css .= wp_strip_all_tags($settings['custom_css']) . "\n\n";
        }
        
        return $css;
    }
    
    /**
     * Get all appearance settings
     *
     * @return array Appearance settings
     */
    private static function get_appearance_settings() {
        return array(
            // Colors
            'question_bg_color' => get_option('smart_faq_question_bg_color', ''),
            'question_text_color' => get_option('smart_faq_question_text_color', ''),
            'answer_bg_color' => get_option('smart_faq_answer_bg_color', ''),
            'answer_text_color' => get_option('smart_faq_answer_text_color', ''),
            'accent_color' => get_option('smart_faq_accent_color', ''),
            'border_color' => get_option('smart_faq_border_color', ''),
            'hover_bg_color' => get_option('smart_faq_hover_bg_color', ''),
            
            // Typography
            'font_family' => get_option('smart_faq_font_family', 'inherit'),
            'question_font_size' => get_option('smart_faq_question_font_size', ''),
            'answer_font_size' => get_option('smart_faq_answer_font_size', ''),
            'question_font_weight' => get_option('smart_faq_question_font_weight', ''),
            
            // Spacing
            'border_radius' => get_option('smart_faq_border_radius', ''),
            'question_padding' => get_option('smart_faq_question_padding', ''),
            'answer_padding' => get_option('smart_faq_answer_padding', ''),
            'item_margin' => get_option('smart_faq_item_margin', ''),
            'border_width' => get_option('smart_faq_border_width', ''),
            
            // Effects
            'enable_box_shadow' => get_option('smart_faq_enable_box_shadow', ''),
            'shadow_color' => get_option('smart_faq_shadow_color', ''),
            'shadow_blur' => get_option('smart_faq_shadow_blur', ''),
            'enable_hover_effect' => get_option('smart_faq_enable_hover_effect', ''),
            'transition_speed' => get_option('smart_faq_transition_speed', ''),
            
            // Animation
            'animation_type' => get_option('smart_faq_animation_type', ''),
            'animation_duration' => get_option('smart_faq_animation_duration', ''),
            'animation_easing' => get_option('smart_faq_animation_easing', ''),
            
            // Gradients
            'gradient_enabled' => get_option('smart_faq_gradient_enabled', ''),
            'gradient_color_1' => get_option('smart_faq_gradient_color_1', ''),
            'gradient_color_2' => get_option('smart_faq_gradient_color_2', ''),
            'gradient_angle' => get_option('smart_faq_gradient_angle', ''),
            
            // Icons
            'icon_style' => get_option('smart_faq_icon_style', ''),
            'icon_position' => get_option('smart_faq_icon_position', ''),
            'icon_size' => get_option('smart_faq_icon_size', ''),
            
            // Borders
            'border_style' => get_option('smart_faq_border_style', ''),
            
            // Custom CSS
            'custom_css' => get_option('smart_faq_custom_css', ''),
        );
    }
    
    /**
     * Reset appearance settings to defaults
     */
    public static function reset_appearance_settings() {
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
        
        foreach ($settings as $setting) {
            delete_option($setting);
        }
    }
    
    /**
     * Clear styles cache
     */
    public static function clear_styles_cache() {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Clearing plugin-specific transient entries for regenerated styles.
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_smart_faq_styles_%'");
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Clearing plugin-specific transient entries for regenerated styles.
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_smart_faq_styles_%'");
        
        // Clear any WordPress object cache
        wp_cache_delete('smart_faq_appearance_settings', 'smart_faq');
        
        // Force regenerate styles on next request
        delete_transient('smart_faq_custom_styles');
    }
}

