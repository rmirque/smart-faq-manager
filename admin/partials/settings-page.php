<?php
/**
 * Admin Settings Page Template
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e('Smart FAQ Manager Settings', 'smart-faq-manager'); ?></h1>
    <p class="description" style="margin-top: 10px; margin-bottom: 20px; font-size: 14px;">
        <?php esc_html_e('Configure how FAQs are matched, displayed, and cached. Changes to matching settings will automatically clear the cache.', 'smart-faq-manager'); ?>
    </p>
    
    <?php settings_errors(); ?>
    
    <div class="smart-faq-info-card">
        <h3><?php esc_html_e('Quick Tips', 'smart-faq-manager'); ?></h3>
        <p><?php esc_html_e('Lower the matching threshold (0.15-0.20) for more FAQ matches. Increase max display to show more FAQs per page. Adjust algorithm weights to fine-tune relevance scoring.', 'smart-faq-manager'); ?></p>
    </div>
    
    <!-- Cache Status and Tools -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 25px; margin: 25px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h2 style="margin-top: 0; color: #1f2937; font-size: 18px; font-weight: 600; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            <span class="dashicons dashicons-database" style="color: #667eea;"></span>
            <?php esc_html_e('Cache & Tools', 'smart-faq-manager'); ?>
        </h2>
        
        <?php
        $cache_stats = Smart_FAQ_Cache_Manager::get_cache_stats();
        $faq_stats = Smart_FAQ_Manager::get_statistics();
        ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
            <div style="background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">
                <div style="font-size: 24px; font-weight: 700; color: #667eea;"><?php echo esc_html($cache_stats['active']); ?></div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase;"><?php esc_html_e('Active Cache Entries', 'smart-faq-manager'); ?></div>
            </div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">
                <div style="font-size: 24px; font-weight: 700; color: #10b981;"><?php echo esc_html($faq_stats['active']); ?></div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase;"><?php esc_html_e('Active FAQs', 'smart-faq-manager'); ?></div>
            </div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">
                <div style="font-size: 24px; font-weight: 700; color: #f59e0b;">
                    <?php echo esc_html(get_option('smart_faq_cache_duration', 24)); ?>h
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase;"><?php esc_html_e('Cache Duration', 'smart-faq-manager'); ?></div>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px;">
            <button type="button" id="clear-all-cache-btn" class="button button-secondary">
                <span class="dashicons dashicons-update" style="font-size: 16px; vertical-align: middle; margin-top: -2px;"></span>
                <?php esc_html_e('Clear All Cache', 'smart-faq-manager'); ?>
            </button>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-analytics')); ?>" class="button button-secondary">
                <span class="dashicons dashicons-chart-bar" style="font-size: 16px; vertical-align: middle; margin-top: -2px;"></span>
                <?php esc_html_e('View Analytics', 'smart-faq-manager'); ?>
            </a>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-manager')); ?>" class="button button-secondary">
                <span class="dashicons dashicons-list-view" style="font-size: 16px; vertical-align: middle; margin-top: -2px;"></span>
                <?php esc_html_e('Manage FAQs', 'smart-faq-manager'); ?>
            </a>
        </div>
        
        <p class="description" style="margin-top: 15px; color: #6b7280;">
            <?php esc_html_e('Cache is automatically cleared when you save settings that affect FAQ matching. Use "Clear All Cache" if FAQs aren\'t updating after changes.', 'smart-faq-manager'); ?>
        </p>
        
    </div>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('smart-faq-settings');
        do_settings_sections('smart-faq-settings');
        ?>
        
        <div style="background: #fff3cd; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0;">
                <strong><?php esc_html_e('Note:', 'smart-faq-manager'); ?></strong>
                <?php esc_html_e('After saving settings that affect FAQ matching, the cache will be automatically cleared. If you don\'t see changes immediately, try refreshing your page or manually clearing the cache from the FAQ Manager list page.', 'smart-faq-manager'); ?>
            </p>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>
