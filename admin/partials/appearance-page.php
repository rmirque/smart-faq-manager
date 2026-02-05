<?php
/**
 * New Appearance Settings Page - Built from Scratch
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get templates for template tab
$templates = Smart_FAQ_Appearance_Settings::get_available_templates();
$current_template = Smart_FAQ_Appearance_Settings::get_current_template();

// Handle form submission if not yet processed
if (
    isset($_POST['submit'], $_POST['smart_faq_appearance_nonce']) &&
    wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['smart_faq_appearance_nonce'] ) ), 'smart_faq_appearance_save' )
) {
    Smart_FAQ_Appearance_Settings::handle_form_submission();
}

// Enqueue WordPress color picker
wp_enqueue_style('wp-color-picker');
wp_enqueue_script('wp-color-picker');
?>

<div class="wrap">
    <h1><?php esc_html_e('FAQ Appearance Settings', 'smart-faq-manager'); ?></h1>
    
    <div class="nav-tab-wrapper">
        <a href="#tab-colors" class="nav-tab nav-tab-active"><?php esc_html_e('Colors', 'smart-faq-manager'); ?></a>
        <a href="#tab-typography" class="nav-tab"><?php esc_html_e('Typography', 'smart-faq-manager'); ?></a>
        <a href="#tab-layout" class="nav-tab"><?php esc_html_e('Layout', 'smart-faq-manager'); ?></a>
        <a href="#tab-effects" class="nav-tab"><?php esc_html_e('Effects', 'smart-faq-manager'); ?></a>
        <a href="#tab-templates" class="nav-tab"><?php esc_html_e('Templates', 'smart-faq-manager'); ?></a>
        <a href="#tab-advanced" class="nav-tab"><?php esc_html_e('Advanced', 'smart-faq-manager'); ?></a>
    </div>
    
    <form method="post" action="">
        <?php wp_nonce_field('smart_faq_appearance_save', 'smart_faq_appearance_nonce'); ?>
        
        <!-- Colors Tab -->
        <div id="tab-colors" class="tab-panel active">
            <h3><?php esc_html_e('Color Settings', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Customize the colors of your FAQ widget. Leave blank to use theme defaults.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="smart_faq_question_bg_color"><?php esc_html_e('Question Background', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_question_bg_color" name="smart_faq_question_bg_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_question_bg_color', '#f9f9f9')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Background color for question sections.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_question_text_color"><?php esc_html_e('Question Text', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_question_text_color" name="smart_faq_question_text_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_question_text_color', '#333333')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Text color for questions.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_answer_bg_color"><?php esc_html_e('Answer Background', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_answer_bg_color" name="smart_faq_answer_bg_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_answer_bg_color', '#ffffff')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Background color for answer sections.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_answer_text_color"><?php esc_html_e('Answer Text', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_answer_text_color" name="smart_faq_answer_text_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_answer_text_color', '#666666')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Text color for answers.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_accent_color"><?php esc_html_e('Accent Color', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_accent_color" name="smart_faq_accent_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_accent_color', '#0073aa')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Color for icons, numbers, and accent elements.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_border_color"><?php esc_html_e('Border Color', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_border_color" name="smart_faq_border_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_border_color', '#eeeeee')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Border color for FAQ items.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_hover_bg_color"><?php esc_html_e('Hover Background', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_hover_bg_color" name="smart_faq_hover_bg_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_hover_bg_color', '#f0f0f0')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Background color when hovering over questions.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Typography Tab -->
        <div id="tab-typography" class="tab-panel">
            <h3><?php esc_html_e('Typography Settings', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Control font family, sizes, and weights.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="smart_faq_font_family"><?php esc_html_e('Font Family', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <?php $font_value = get_option('smart_faq_font_family', 'inherit'); ?>
                        <select id="smart_faq_font_family" name="smart_faq_font_family" class="regular-text">
                            <option value="inherit" <?php selected($font_value, 'inherit'); ?>><?php esc_html_e('Inherit from Theme', 'smart-faq-manager'); ?></option>
                            <option value="-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, sans-serif" <?php selected($font_value, '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'); ?>><?php esc_html_e('System Default', 'smart-faq-manager'); ?></option>
                            <option value="Arial, sans-serif" <?php selected($font_value, 'Arial, sans-serif'); ?>><?php esc_html_e('Arial', 'smart-faq-manager'); ?></option>
                            <option value="Georgia, serif" <?php selected($font_value, 'Georgia, serif'); ?>><?php esc_html_e('Georgia', 'smart-faq-manager'); ?></option>
                            <option value="&quot;Times New Roman&quot;, serif" <?php selected($font_value, '"Times New Roman", serif'); ?>><?php esc_html_e('Times New Roman', 'smart-faq-manager'); ?></option>
                            <option value="Verdana, sans-serif" <?php selected($font_value, 'Verdana, sans-serif'); ?>><?php esc_html_e('Verdana', 'smart-faq-manager'); ?></option>
                            <option value="&quot;Courier New&quot;, monospace" <?php selected($font_value, '"Courier New", monospace'); ?>><?php esc_html_e('Courier New', 'smart-faq-manager'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Font family for all FAQ text.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_question_font_size"><?php esc_html_e('Question Font Size (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_question_font_size" name="smart_faq_question_font_size" 
                               value="<?php echo esc_attr(get_option('smart_faq_question_font_size', '16')); ?>" 
                               min="10" max="32" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Font size for questions (10-32px).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_answer_font_size"><?php esc_html_e('Answer Font Size (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_answer_font_size" name="smart_faq_answer_font_size" 
                               value="<?php echo esc_attr(get_option('smart_faq_answer_font_size', '14')); ?>" 
                               min="10" max="24" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Font size for answers (10-24px).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_question_font_weight"><?php esc_html_e('Question Font Weight', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <?php $weight_value = get_option('smart_faq_question_font_weight', '600'); ?>
                        <select id="smart_faq_question_font_weight" name="smart_faq_question_font_weight" class="regular-text">
                            <option value="" <?php selected($weight_value, ''); ?>><?php esc_html_e('Default (600)', 'smart-faq-manager'); ?></option>
                            <option value="normal" <?php selected($weight_value, 'normal'); ?>><?php esc_html_e('Normal (400)', 'smart-faq-manager'); ?></option>
                            <option value="500" <?php selected($weight_value, '500'); ?>><?php esc_html_e('Medium (500)', 'smart-faq-manager'); ?></option>
                            <option value="600" <?php selected($weight_value, '600'); ?>><?php esc_html_e('Semi-Bold (600)', 'smart-faq-manager'); ?></option>
                            <option value="bold" <?php selected($weight_value, 'bold'); ?>><?php esc_html_e('Bold (700)', 'smart-faq-manager'); ?></option>
                            <option value="800" <?php selected($weight_value, '800'); ?>><?php esc_html_e('Extra Bold (800)', 'smart-faq-manager'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Font weight for question text.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
            </table>
                        </div>
        
        <!-- Layout Tab -->
        <div id="tab-layout" class="tab-panel">
            <h3><?php esc_html_e('Spacing & Layout', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Adjust spacing, padding, and layout properties.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="smart_faq_border_radius"><?php esc_html_e('Border Radius (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_border_radius" name="smart_faq_border_radius" 
                               value="<?php echo esc_attr(get_option('smart_faq_border_radius', '4')); ?>" 
                               min="0" max="50" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Rounded corners for FAQ items (0-50).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_question_padding"><?php esc_html_e('Question Padding (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_question_padding" name="smart_faq_question_padding" 
                               value="<?php echo esc_attr(get_option('smart_faq_question_padding', '15')); ?>" 
                               min="0" max="50" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Internal spacing for questions (0-50).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_answer_padding"><?php esc_html_e('Answer Padding (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_answer_padding" name="smart_faq_answer_padding" 
                               value="<?php echo esc_attr(get_option('smart_faq_answer_padding', '15')); ?>" 
                               min="0" max="50" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Internal spacing for answers (0-50).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_item_margin"><?php esc_html_e('Item Margin (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_item_margin" name="smart_faq_item_margin" 
                               value="<?php echo esc_attr(get_option('smart_faq_item_margin', '10')); ?>" 
                               min="0" max="50" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Spacing between FAQ items (0-50).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_border_width"><?php esc_html_e('Border Width (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_border_width" name="smart_faq_border_width" 
                               value="<?php echo esc_attr(get_option('smart_faq_border_width', '1')); ?>" 
                               min="0" max="10" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Border thickness for FAQ items (0-10).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_border_style"><?php esc_html_e('Border Style', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <?php $border_value = get_option('smart_faq_border_style', 'solid'); ?>
                        <select id="smart_faq_border_style" name="smart_faq_border_style" class="regular-text">
                            <option value="solid" <?php selected($border_value, 'solid'); ?>><?php esc_html_e('Solid', 'smart-faq-manager'); ?></option>
                            <option value="dashed" <?php selected($border_value, 'dashed'); ?>><?php esc_html_e('Dashed', 'smart-faq-manager'); ?></option>
                            <option value="dotted" <?php selected($border_value, 'dotted'); ?>><?php esc_html_e('Dotted', 'smart-faq-manager'); ?></option>
                            <option value="double" <?php selected($border_value, 'double'); ?>><?php esc_html_e('Double', 'smart-faq-manager'); ?></option>
                            <option value="none" <?php selected($border_value, 'none'); ?>><?php esc_html_e('None', 'smart-faq-manager'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Style of borders around FAQ items.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
            </table>
                    </div>
                    
        <!-- Effects Tab -->
        <div id="tab-effects" class="tab-panel">
            <h3><?php esc_html_e('Gradient Background', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Enable and customize gradient backgrounds for your FAQ elements.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Gradient', 'smart-faq-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="smart_faq_gradient_enabled" value="1" 
                                   <?php checked(get_option('smart_faq_gradient_enabled'), '1'); ?> />
                            <?php esc_html_e('Use gradient background instead of solid color', 'smart-faq-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_gradient_color_1"><?php esc_html_e('Gradient Color 1', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_gradient_color_1" name="smart_faq_gradient_color_1" 
                               value="<?php echo esc_attr(get_option('smart_faq_gradient_color_1', '#667eea')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('First color of the gradient.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_gradient_color_2"><?php esc_html_e('Gradient Color 2', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_gradient_color_2" name="smart_faq_gradient_color_2" 
                               value="<?php echo esc_attr(get_option('smart_faq_gradient_color_2', '#764ba2')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Second color of the gradient.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_gradient_angle"><?php esc_html_e('Gradient Angle (deg)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_gradient_angle" name="smart_faq_gradient_angle" 
                               value="<?php echo esc_attr(get_option('smart_faq_gradient_angle', '135')); ?>" 
                               min="0" max="360" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Gradient direction in degrees (0-360).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
            </table>
            
            <h3><?php esc_html_e('Animation Settings', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Control animation types, duration, and easing for FAQ interactions.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="smart_faq_animation_type"><?php esc_html_e('Animation Type', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <?php $anim_value = get_option('smart_faq_animation_type', 'slide'); ?>
                        <select id="smart_faq_animation_type" name="smart_faq_animation_type" class="regular-text">
                            <option value="fade" <?php selected($anim_value, 'fade'); ?>><?php esc_html_e('Fade', 'smart-faq-manager'); ?></option>
                            <option value="slide" <?php selected($anim_value, 'slide'); ?>><?php esc_html_e('Slide', 'smart-faq-manager'); ?></option>
                            <option value="bounce" <?php selected($anim_value, 'bounce'); ?>><?php esc_html_e('Bounce', 'smart-faq-manager'); ?></option>
                            <option value="zoom" <?php selected($anim_value, 'zoom'); ?>><?php esc_html_e('Zoom', 'smart-faq-manager'); ?></option>
                            <option value="none" <?php selected($anim_value, 'none'); ?>><?php esc_html_e('None', 'smart-faq-manager'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Animation style for FAQ interactions.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_animation_duration"><?php esc_html_e('Animation Duration (ms)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_animation_duration" name="smart_faq_animation_duration" 
                               value="<?php echo esc_attr(get_option('smart_faq_animation_duration', '300')); ?>" 
                               min="100" max="1000" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Animation duration in milliseconds (100-1000).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_animation_easing"><?php esc_html_e('Animation Easing', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <?php $easing_value = get_option('smart_faq_animation_easing', 'ease'); ?>
                        <select id="smart_faq_animation_easing" name="smart_faq_animation_easing" class="regular-text">
                            <option value="ease" <?php selected($easing_value, 'ease'); ?>><?php esc_html_e('Ease', 'smart-faq-manager'); ?></option>
                            <option value="ease-in" <?php selected($easing_value, 'ease-in'); ?>><?php esc_html_e('Ease In', 'smart-faq-manager'); ?></option>
                            <option value="ease-out" <?php selected($easing_value, 'ease-out'); ?>><?php esc_html_e('Ease Out', 'smart-faq-manager'); ?></option>
                            <option value="ease-in-out" <?php selected($easing_value, 'ease-in-out'); ?>><?php esc_html_e('Ease In Out', 'smart-faq-manager'); ?></option>
                            <option value="linear" <?php selected($easing_value, 'linear'); ?>><?php esc_html_e('Linear', 'smart-faq-manager'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Animation easing function.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
            </table>
            
            <h3><?php esc_html_e('Icon Settings', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Customize arrow icons and their appearance in accordion-style FAQs.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="smart_faq_icon_style"><?php esc_html_e('Icon Style', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <?php $icon_value = get_option('smart_faq_icon_style', 'arrow-down'); ?>
                        <select id="smart_faq_icon_style" name="smart_faq_icon_style" class="regular-text">
                            <option value="arrow-down" <?php selected($icon_value, 'arrow-down'); ?>><?php esc_html_e('Arrow Down (Ã¢â€“Â¼)', 'smart-faq-manager'); ?></option>
                            <option value="arrow-right" <?php selected($icon_value, 'arrow-right'); ?>><?php esc_html_e('Arrow Right (Ã¢â€“Âº)', 'smart-faq-manager'); ?></option>
                            <option value="plus" <?php selected($icon_value, 'plus'); ?>><?php esc_html_e('Plus (+)', 'smart-faq-manager'); ?></option>
                            <option value="chevron-down" <?php selected($icon_value, 'chevron-down'); ?>><?php esc_html_e('Chevron Down', 'smart-faq-manager'); ?></option>
                            <option value="chevron-right" <?php selected($icon_value, 'chevron-right'); ?>><?php esc_html_e('Chevron Right', 'smart-faq-manager'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Icon style for accordion controls.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_icon_position"><?php esc_html_e('Icon Position', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <?php $position_value = get_option('smart_faq_icon_position', 'right'); ?>
                        <select id="smart_faq_icon_position" name="smart_faq_icon_position" class="regular-text">
                            <option value="left" <?php selected($position_value, 'left'); ?>><?php esc_html_e('Left', 'smart-faq-manager'); ?></option>
                            <option value="right" <?php selected($position_value, 'right'); ?>><?php esc_html_e('Right', 'smart-faq-manager'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Position of icons relative to text.', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_icon_size"><?php esc_html_e('Icon Size (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_icon_size" name="smart_faq_icon_size" 
                               value="<?php echo esc_attr(get_option('smart_faq_icon_size', '16')); ?>" 
                               min="8" max="32" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Size of icons in pixels (8-32).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
            </table>
            
            <h3><?php esc_html_e('Visual Effects', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Configure visual effects like shadows, hover states, and transitions.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Box Shadow', 'smart-faq-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="smart_faq_enable_box_shadow" value="1" 
                                   <?php checked(get_option('smart_faq_enable_box_shadow'), '1'); ?> />
                            <?php esc_html_e('Add shadow effects to FAQ items', 'smart-faq-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_shadow_color"><?php esc_html_e('Shadow Color', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="smart_faq_shadow_color" name="smart_faq_shadow_color" 
                               value="<?php echo esc_attr(get_option('smart_faq_shadow_color', 'rgba(0,0,0,0.1)')); ?>" 
                               class="color-field" />
                        <p class="description"><?php esc_html_e('Color and opacity of shadows (can use rgba values).', 'smart-faq-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_shadow_blur"><?php esc_html_e('Shadow Blur (px)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_shadow_blur" name="smart_faq_shadow_blur" 
                               value="<?php echo esc_attr(get_option('smart_faq_shadow_blur', '8')); ?>" 
                               min="0" max="50" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Shadow blur radius (0-50px).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Hover Effect', 'smart-faq-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="smart_faq_enable_hover_effect" value="1" 
                                   <?php checked(get_option('smart_faq_enable_hover_effect'), '1'); ?> />
                            <?php esc_html_e('Add hover effects to questions', 'smart-faq-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="smart_faq_transition_speed"><?php esc_html_e('Transition Speed (ms)', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smart_faq_transition_speed" name="smart_faq_transition_speed" 
                               value="<?php echo esc_attr(get_option('smart_faq_transition_speed', '300')); ?>" 
                               min="100" max="1000" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e('Speed of hover and state transitions (100-1000ms).', 'smart-faq-manager'); ?></span>
                    </td>
                </tr>
            </table>
                    </div>
                    
        <!-- Templates Tab -->
        <div id="tab-templates" class="tab-panel">
            <h3><?php esc_html_e('Template Management', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Choose from professional templates or save your custom designs. Templates will override your manual settings.', 'smart-faq-manager'); ?></p>
            
            <div class="template-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                <?php foreach ($templates as $template_id => $template) : ?>
                    <?php
                    $is_active = ($current_template === $template_id);
                    $card_style = sprintf('border: 2px solid %s; padding: 15px; border-radius: 5px; background: #fff;', $is_active ? '#0073aa' : '#ddd');
                    $button_style = $is_active ? 'background: #0073aa;' : '';
                    ?>
                    <div class="template-card" style="<?php echo esc_attr($card_style); ?>">
                        <div class="template-preview" style="<?php echo esc_attr('min-height: 120px; margin-bottom: 10px; background: #f9f9f9; border-radius: 3px; display: flex; align-items: center; justify-content: center;'); ?>">
                            <?php echo wp_kses_post( Smart_FAQ_Appearance_Settings::render_template_preview( $template ) ); ?>
                        </div>
                        <div class="template-info">
                            <h4><?php echo esc_html($template['name']); ?></h4>
                            <p><?php echo esc_html($template['description']); ?></p>
                            <button type="button" class="button button-primary apply-template-btn" 
                                    data-template-id="<?php echo esc_attr($template_id); ?>" 
                                    style="<?php echo esc_attr($button_style); ?>">
                                <?php echo $is_active ? esc_html__('Active', 'smart-faq-manager') : esc_html__('Apply Template', 'smart-faq-manager'); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Advanced Tab -->
        <div id="tab-advanced" class="tab-panel">
            <h3><?php esc_html_e('Custom CSS', 'smart-faq-manager'); ?></h3>
            <p><?php esc_html_e('Add custom CSS for complete control over FAQ styling.', 'smart-faq-manager'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="smart_faq_custom_css"><?php esc_html_e('Custom CSS', 'smart-faq-manager'); ?></label>
                    </th>
                    <td>
                        <textarea id="smart_faq_custom_css" name="smart_faq_custom_css" 
                                  rows="10" cols="50" class="large-text code"><?php echo esc_textarea(get_option('smart_faq_custom_css', '')); ?></textarea>
                        <p class="description">
                            <?php esc_html_e('Available selectors:', 'smart-faq-manager'); ?>
                            <code>.smart-faq-widget</code>, <code>.smart-faq-item</code>, 
                            <code>.smart-faq-question</code>, <code>.smart-faq-answer</code>, <code>.smart-faq-number</code>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php submit_button(__('Save All Settings', 'smart-faq-manager')); ?>
    </form>
    </div>
    
    <style>
/* Simple inline CSS for tabs */
.tab-panel { 
    display: none; 
    padding: 20px 0; 
    border-top: 1px solid #ccd0d4;
    margin-top: -1px;
}
.tab-panel.active { 
    display: block; 
}
.nav-tab-wrapper {
    margin-bottom: 0;
}
.nav-tab {
    cursor: pointer;
}
.template-grid {
    max-width: 100%;
}
.template-card:hover {
    border-color: #0073aa !important;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Simple tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.tab-panel').removeClass('active');
        $(target).addClass('active');
    });
    
    // Initialize color pickers
    if ($.fn.wpColorPicker) {
        $('.color-field').wpColorPicker();
    }
    
    // Template application via AJAX
    $('.apply-template-btn').on('click', function(e) {
        e.preventDefault();
        var templateId = $(this).data('template-id');
        var $button = $(this);
        
        $.ajax({
            url: '<?php echo esc_url( admin_url('admin-ajax.php') ); ?>',
            type: 'POST',
            data: {
                action: 'smart_faq_apply_template',
                template_id: templateId,
                nonce: '<?php echo esc_attr( wp_create_nonce('smart_faq_admin_nonce') ); ?>'
            },
            success: function(response) {
                console.log('Template apply response:', response);
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error applying template: ' + (response.data || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr, status, error);
                alert('Error applying template: ' + error + ' (Status: ' + status + ')');
            }
        });
    });
});
</script>

