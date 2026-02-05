<?php
/**
 * Admin Categories Page Template
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e('FAQ Categories', 'smart-faq-manager'); ?></h1>
    <p class="description" style="margin-top: 10px; margin-bottom: 20px; font-size: 14px;">
        <?php esc_html_e('Organize your FAQs with categories. FAQs in categories matching page categories receive a relevance boost.', 'smart-faq-manager'); ?>
    </p>
    
    <div class="smart-faq-categories">
        <?php if (!empty($categories)) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Category Name', 'smart-faq-manager'); ?></th>
                        <th><?php esc_html_e('FAQ Count', 'smart-faq-manager'); ?></th>
                        <th><?php esc_html_e('Actions', 'smart-faq-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $wpdb;
                    $table = $wpdb->prefix . 'smart_faq_items';
                    
                    foreach ($categories as $category) :
                        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Aggregating counts from custom FAQ table.
                        $count = $wpdb->get_var(
                            $wpdb->prepare(
                                "SELECT COUNT(*) FROM %i WHERE category = %s",
                                $table,
                                $category
                            )
                        );
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($category); ?></strong></td>
                            <td><?php echo esc_html(number_format_i18n($count)); ?></td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-manager&category=' . urlencode($category))); ?>">
                                    <?php esc_html_e('View FAQs', 'smart-faq-manager'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php esc_html_e('No categories found. Categories are created automatically when you add FAQs.', 'smart-faq-manager'); ?></p>
        <?php endif; ?>
    </div>
    
    <div class="smart-faq-category-info">
        <h2><?php esc_html_e('About Categories', 'smart-faq-manager'); ?></h2>
        <p><?php esc_html_e('Categories help organize your FAQs and improve matching accuracy. FAQs in categories matching page categories receive a boost in relevance scoring.', 'smart-faq-manager'); ?></p>
        <p><?php esc_html_e('Categories are created automatically when you assign them to FAQs. Simply enter a category name when creating or editing an FAQ.', 'smart-faq-manager'); ?></p>
    </div>
</div>
