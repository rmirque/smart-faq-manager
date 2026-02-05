<?php
/**
 * Admin Analytics Page Template (clean, normalized)
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e('FAQ Analytics', 'smart-faq-manager'); ?></h1>
    <p class="description" style="margin-top: 10px; font-size: 14px;">
        <?php esc_html_e('Track FAQ performance, engagement rates, and identify opportunities for improvement.', 'smart-faq-manager'); ?>
    </p>

    <div class="smart-faq-analytics-dashboard">
        <div class="analytics-overview">
            <h2><?php esc_html_e('Performance Overview', 'smart-faq-manager'); ?></h2>
            <div class="analytics-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html(number_format_i18n($stats['total_faqs'])); ?></div>
                    <div class="stat-label"><?php esc_html_e('Total FAQs', 'smart-faq-manager'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html(number_format_i18n($stats['active_faqs'])); ?></div>
                    <div class="stat-label"><?php esc_html_e('Active FAQs', 'smart-faq-manager'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html(number_format_i18n($stats['total_displays'])); ?></div>
                    <div class="stat-label"><?php esc_html_e('Total Displays', 'smart-faq-manager'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html(number_format_i18n($stats['displays_last_7_days'])); ?></div>
                    <div class="stat-label"><?php esc_html_e('Last 7 Days', 'smart-faq-manager'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html(number_format_i18n($stats['displays_last_30_days'])); ?></div>
                    <div class="stat-label"><?php esc_html_e('Last 30 Days', 'smart-faq-manager'); ?></div>
                </div>
            </div>
        </div>

        <div class="analytics-section">
            <h2><?php esc_html_e('Top Performing FAQs', 'smart-faq-manager'); ?></h2>
            <?php if (!empty($stats['top_faqs'])) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Question', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Category', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Displays', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Clicks', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Engagement', 'smart-faq-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['top_faqs'] as $faq) : ?>
                            <?php $engagement_rate = $faq->display_count > 0 ? ($faq->click_count / $faq->display_count) * 100 : 0; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-add&faq_id=' . $faq->id)); ?>">
                                        <?php echo esc_html(wp_trim_words(wp_strip_all_tags($faq->question_html), 15)); ?>
                                    </a>
                                </td>
                                <td><?php echo $faq->category ? esc_html($faq->category) : 'Ã¢â‚¬â€'; ?></td>
                                <td><?php echo esc_html(number_format_i18n($faq->display_count)); ?></td>
                                <td><?php echo esc_html(number_format_i18n($faq->click_count)); ?></td>
                                <td>
                                    <?php $bar_class = $engagement_rate >= 50 ? '' : ($engagement_rate >= 30 ? 'medium' : 'low');
                                          $text_class = $engagement_rate >= 50 ? 'engagement-high' : ($engagement_rate >= 30 ? 'engagement-medium' : 'engagement-low'); ?>
                                    <strong class="<?php echo esc_attr($text_class); ?>"><?php echo esc_html(number_format($engagement_rate, 1)); ?>%</strong>
                                    <div class="engagement-bar">
                                        <div class="engagement-bar-fill <?php echo esc_attr($bar_class); ?>" style="width: <?php echo esc_attr($engagement_rate); ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('No data available yet.', 'smart-faq-manager'); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($stats['top_engaged'])) : ?>
        <div class="analytics-section">
            <h2><?php esc_html_e('Best Engagement', 'smart-faq-manager'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Question', 'smart-faq-manager'); ?></th>
                        <th><?php esc_html_e('Category', 'smart-faq-manager'); ?></th>
                        <th><?php esc_html_e('Engagement', 'smart-faq-manager'); ?></th>
                        <th><?php esc_html_e('Suggestion', 'smart-faq-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['top_engaged'] as $faq) : ?>
                        <?php $engagement_rate = $faq->display_count > 0 ? ($faq->click_count / $faq->display_count) * 100 : 0; ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-add&faq_id=' . $faq->id)); ?>">
                                    <?php echo esc_html(wp_trim_words(wp_strip_all_tags($faq->question_html), 15)); ?>
                                </a>
                            </td>
                            <td><?php echo $faq->category ? esc_html($faq->category) : 'Ã¢â‚¬â€'; ?></td>
                            <td>
                                <strong class="engagement-high"><?php echo esc_html(number_format($engagement_rate, 1)); ?>%</strong>
                                <div class="engagement-bar">
                                    <div class="engagement-bar-fill" style="width: <?php echo esc_attr($engagement_rate); ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <span class="smart-faq-badge-success"><?php echo esc_html($faq->click_count); ?> clicks</span>
                                <span class="smart-faq-text-muted"> / <?php echo esc_html($faq->display_count); ?> displays</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="analytics-section">
            <h2><?php esc_html_e('Low Engagement FAQs', 'smart-faq-manager'); ?></h2>
            <?php if (!empty($stats['low_engaged'])) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Question', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Category', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Engagement', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Suggestion', 'smart-faq-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['low_engaged'] as $faq) : ?>
                            <?php $engagement_rate = $faq->display_count > 0 ? ($faq->click_count / $faq->display_count) * 100 : 0; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-add&faq_id=' . $faq->id)); ?>">
                                        <?php echo esc_html(wp_trim_words(wp_strip_all_tags($faq->question_html), 15)); ?>
                                    </a>
                                </td>
                                <td><?php echo $faq->category ? esc_html($faq->category) : 'Ã¢â‚¬â€'; ?></td>
                                <td>
                                    <strong class="engagement-low"><?php echo esc_html(number_format($engagement_rate, 1)); ?>%</strong>
                                    <div class="engagement-bar">
                                        <div class="engagement-bar-fill low" style="width: <?php echo esc_attr($engagement_rate); ?>%"></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="dashicons dashicons-lightbulb" style="color: #f59e0b; font-size: 16px; vertical-align: middle;"></span>
                                    <?php esc_html_e('Make question more compelling or answer more concise', 'smart-faq-manager'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('All FAQs have good engagement!', 'smart-faq-manager'); ?></p>
            <?php endif; ?>
        </div>

        <div class="analytics-section">
            <h2><?php esc_html_e('Underperforming FAQs', 'smart-faq-manager'); ?></h2>
            <?php if (!empty($stats['underperforming_faqs'])) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Question', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Category', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Displays', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Suggestion', 'smart-faq-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['underperforming_faqs'] as $faq) : ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-add&faq_id=' . $faq->id)); ?>">
                                        <?php echo esc_html(wp_trim_words(wp_strip_all_tags($faq->question_html), 15)); ?>
                                    </a>
                                </td>
                                <td><?php echo $faq->category ? esc_html($faq->category) : 'Ã¢â‚¬â€'; ?></td>
                                <td><?php echo esc_html(number_format_i18n($faq->display_count)); ?></td>
                                <td><?php esc_html_e('Consider adding more keywords or increasing priority', 'smart-faq-manager'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('All FAQs are performing well!', 'smart-faq-manager'); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($stats['category_stats'])) : ?>
            <div class="analytics-section">
                <h2><?php esc_html_e('Category Performance', 'smart-faq-manager'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Category', 'smart-faq-manager'); ?></th>
                            <th><?php esc_html_e('Total Displays', 'smart-faq-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['category_stats'] as $cat_stat) : ?>
                            <tr>
                                <td><?php echo esc_html($cat_stat->category); ?></td>
                                <td><?php echo esc_html(number_format_i18n($cat_stat->display_count)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>


