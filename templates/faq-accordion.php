<?php
/**
 * FAQ Accordion Template
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$counter = 1;
$custom_class = isset($args['custom_class']) ? $args['custom_class'] : '';
// Check if show_numbers is set in args, otherwise use the global setting
$show_numbers = isset($args['show_numbers']) ? ($args['show_numbers'] == 1 || $args['show_numbers'] === '1' || $args['show_numbers'] === true) : (get_option('smart_faq_show_numbers', 1) == 1);
?>

<div class="smart-faq-widget smart-faq-accordion <?php echo esc_attr($custom_class); ?>">
    <?php foreach ($faqs as $faq) : ?>
        <?php do_action('smart_faq_before_faq_item', $faq, $args); ?>
        
        <div class="smart-faq-item" id="faq-<?php echo esc_attr($faq->id); ?>" data-faq-id="<?php echo esc_attr($faq->id); ?>">
            <div class="smart-faq-question" role="button" tabindex="0" aria-expanded="false">
                <?php if ($show_numbers) : ?>
                    <span class="smart-faq-number"><?php echo esc_html($counter); ?>.</span>
                <?php endif; ?>
                <span class="smart-faq-question-text"><?php echo wp_kses_post($faq->question_html); ?></span>
                <?php if (get_option('smart_faq_show_permalinks', 0) == 1) : ?>
                    <a href="#faq-<?php echo esc_attr($faq->id); ?>"
                       class="smart-faq-permalink"
                       title="<?php echo esc_attr__('Link to this FAQ', 'smart-faq-manager'); ?>"
                       aria-label="<?php echo esc_attr__('Permalink', 'smart-faq-manager'); ?>"
                       onclick="event.stopPropagation();">&#128279;</a>
                <?php endif; ?>
            </div>
            <div class="smart-faq-answer" role="region">
                <?php echo wp_kses_post($faq->answer_html); ?>
            </div>
        </div>
        
        <?php do_action('smart_faq_after_faq_item', $faq, $args); ?>
        <?php $counter++; ?>
    <?php endforeach; ?>
</div>

