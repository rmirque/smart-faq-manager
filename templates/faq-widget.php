<?php
/**
 * Default FAQ Widget Template
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

// Use the appropriate template based on style
$style = isset($args['style']) ? $args['style'] : 'list';

$template_files = array(
    'accordion' => 'faq-accordion.php',
    'list' => 'faq-list.php',
    'grid' => 'faq-grid.php',
);

$template_file = isset($template_files[$style]) ? $template_files[$style] : 'faq-list.php';
$template_path = SMART_FAQ_PLUGIN_DIR . 'templates/' . $template_file;

if (file_exists($template_path)) {
    include $template_path;
} else {
    // Fallback to basic rendering
    $counter = 1;
    ?>
    <div class="smart-faq-widget smart-faq-<?php echo esc_attr($style); ?> <?php echo esc_attr($args['custom_class']); ?>">
        <?php foreach ($faqs as $faq) : ?>
            <div class="smart-faq-item" data-faq-id="<?php echo esc_attr($faq->id); ?>">
                <div class="smart-faq-question">
                    <?php if ($args['show_numbers']) : ?>
                        <span class="smart-faq-number"><?php echo esc_html($counter); ?>.</span>
                    <?php endif; ?>
                    <?php echo wp_kses_post($faq->question_html); ?>
                </div>
                <div class="smart-faq-answer">
                    <?php echo wp_kses_post($faq->answer_html); ?>
                </div>
            </div>
            <?php $counter++; ?>
        <?php endforeach; ?>
    </div>
    <?php
}



