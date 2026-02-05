<?php
/**
 * Admin FAQ List Page Template
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('FAQ Manager', 'smart-faq-manager'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-add')); ?>" class="page-title-action">
        <span class="dashicons dashicons-plus-alt" style="font-size: 16px; vertical-align: middle; margin-top: -2px;"></span>
        <?php esc_html_e('Add New', 'smart-faq-manager'); ?>
    </a>
    <hr class="wp-header-end">
    
    <?php
    // Display messages
    $message = filter_input(INPUT_GET, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
    $allowed_messages = array('created', 'updated', 'deleted', 'error');
    if ($message && in_array($message, $allowed_messages, true)) {
        $messages = array(
            'created' => __('FAQ created successfully.', 'smart-faq-manager'),
            'updated' => __('FAQ updated successfully.', 'smart-faq-manager'),
            'deleted' => __('FAQ deleted successfully.', 'smart-faq-manager'),
            'error' => __('An error occurred. Please try again.', 'smart-faq-manager'),
        );
        
        if (isset($messages[$message])) {
            $class = $message === 'error' ? 'error' : 'updated';
            echo '<div class="notice notice-' . esc_attr($class) . ' is-dismissible"><p>' . esc_html($messages[$message]) . '</p></div>';
        }
    }
    ?>
    
    <div class="smart-faq-admin-header">
        <div class="smart-faq-welcome">
            <h2><?php esc_html_e('Manage Your FAQs', 'smart-faq-manager'); ?></h2>
            <p><?php esc_html_e('Create and organize your frequently asked questions. FAQs are automatically matched to relevant pages based on content analysis.', 'smart-faq-manager'); ?></p>
        </div>
        <div class="smart-faq-stats">
            <?php
            $stats = Smart_FAQ_Manager::get_statistics();
            ?>
            <div class="stat-box">
                <span class="stat-number"><?php echo esc_html($stats['total']); ?></span>
                <span class="stat-label"><?php esc_html_e('Total FAQs', 'smart-faq-manager'); ?></span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo esc_html($stats['active']); ?></span>
                <span class="stat-label"><?php esc_html_e('Active', 'smart-faq-manager'); ?></span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo esc_html($stats['categories']); ?></span>
                <span class="stat-label"><?php esc_html_e('Categories', 'smart-faq-manager'); ?></span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo esc_html($stats['cache']['active']); ?></span>
                <span class="stat-label"><?php esc_html_e('Cached Pages', 'smart-faq-manager'); ?></span>
            </div>
        </div>
        
        <div class="smart-faq-actions">
            <button type="button" class="button" id="clear-cache-btn">
                <?php esc_html_e('Clear All Cache', 'smart-faq-manager'); ?>
            </button>
            <a href="#" class="button" id="export-faqs-btn">
                <?php esc_html_e('Export FAQs', 'smart-faq-manager'); ?>
            </a>
            <button type="button" class="button" id="import-faqs-btn">
                <?php esc_html_e('Import FAQs', 'smart-faq-manager'); ?>
            </button>
        </div>
    </div>
    
    <div style="background: #fff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="get">
            <?php wp_nonce_field('smart_faq_filters', 'smart_faq_filters_nonce'); ?>
            <input type="hidden" name="page" value="smart-faq-manager" />
            <?php
            $list_table->search_box(esc_html__('Search FAQs', 'smart-faq-manager'), 'faq');
            $list_table->display();
            ?>
        </form>
    </div>
</div>

<div id="import-modal" class="smart-faq-modal" style="display: none;">
    <div class="smart-faq-modal-content">
        <span class="smart-faq-modal-close">&times;</span>
        <h2><?php esc_html_e('Import FAQs', 'smart-faq-manager'); ?></h2>
        <form id="import-form" method="post" enctype="multipart/form-data">
            <input type="file" name="import_file" accept=".json" required />
            <p class="description"><?php esc_html_e('Select a JSON file containing FAQ data.', 'smart-faq-manager'); ?></p>
            <button type="submit" class="button button-primary"><?php esc_html_e('Import', 'smart-faq-manager'); ?></button>
        </form>
    </div>
</div>
