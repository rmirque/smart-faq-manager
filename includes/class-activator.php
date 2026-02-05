<?php
/**
 * Plugin activation handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Activator
 */
class Smart_FAQ_Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        // Check minimum WordPress version
        if (version_compare(get_bloginfo('version'), '5.8', '<')) {
            // Deactivate the main plugin file (not this include)
            if (defined('SMART_FAQ_PLUGIN_FILE')) {
                deactivate_plugins(plugin_basename(SMART_FAQ_PLUGIN_FILE));
            }
            wp_die(
                esc_html__('Smart FAQ Manager requires WordPress 5.8 or higher.', 'smart-faq-manager'),
                esc_html__('Plugin Activation Error', 'smart-faq-manager'),
                array('back_link' => true)
            );
        }
        
        // Check minimum PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            // Deactivate the main plugin file (not this include)
            if (defined('SMART_FAQ_PLUGIN_FILE')) {
                deactivate_plugins(plugin_basename(SMART_FAQ_PLUGIN_FILE));
            }
            wp_die(
                esc_html__('Smart FAQ Manager requires PHP 7.4 or higher.', 'smart-faq-manager'),
                esc_html__('Plugin Activation Error', 'smart-faq-manager'),
                array('back_link' => true)
            );
        }
        
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Schedule cron job
        if (!wp_next_scheduled('smart_faq_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'smart_faq_daily_cleanup');
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // FAQ Items Table
        $table_faq_items = $wpdb->prefix . 'smart_faq_items';
        $sql_faq_items = "CREATE TABLE $table_faq_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            question text NOT NULL,
            question_html longtext NOT NULL,
            answer text NOT NULL,
            answer_html longtext NOT NULL,
            keywords text,
            category varchar(255) DEFAULT '',
            priority int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'active',
            created_by bigint(20) unsigned DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            view_count bigint(20) unsigned DEFAULT 0,
            display_count bigint(20) unsigned DEFAULT 0,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY category (category),
            KEY priority (priority),
            FULLTEXT KEY search_content (question, answer, keywords)
        ) $charset_collate;";
        
        dbDelta($sql_faq_items);
        
        // Cache Table
        $table_cache = $wpdb->prefix . 'smart_faq_cache';
        $sql_cache = "CREATE TABLE $table_cache (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            page_id bigint(20) unsigned NOT NULL,
            content_hash varchar(64) NOT NULL,
            matched_faq_ids text NOT NULL,
            created_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY page_id (page_id),
            KEY expires_at (expires_at)
        ) $charset_collate;";
        
        dbDelta($sql_cache);
        
        // Analytics Table
        $table_analytics = $wpdb->prefix . 'smart_faq_analytics';
        $sql_analytics = "CREATE TABLE $table_analytics (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            faq_id bigint(20) unsigned NOT NULL,
            page_id bigint(20) unsigned NOT NULL,
            displayed_at datetime NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            interaction_type varchar(20) DEFAULT 'display',
            PRIMARY KEY  (id),
            KEY faq_id (faq_id),
            KEY page_id (page_id),
            KEY displayed_at (displayed_at),
            KEY interaction_type (interaction_type)
        ) $charset_collate;";
        
        dbDelta($sql_analytics);
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        add_option('smart_faq_version', SMART_FAQ_VERSION);
        add_option('smart_faq_cache_duration', 24); // 24 hours
        add_option('smart_faq_max_display', 5);
        add_option('smart_faq_analysis_depth', 1000); // words
        add_option('smart_faq_matching_threshold', 0.2); // Balanced threshold (manual control available)
        add_option('smart_faq_enable_cache', 1);
        add_option('smart_faq_enable_analytics', 1);
        add_option('smart_faq_display_style', 'accordion');
        add_option('smart_faq_show_numbers', 1);
        add_option('smart_faq_keyword_weight', 0.4);
        add_option('smart_faq_content_weight', 0.3);
        add_option('smart_faq_phrase_weight', 0.2);
        add_option('smart_faq_priority_weight', 0.1);
        add_option('smart_faq_category_boost', 1.2);
        add_option('smart_faq_analytics_retention', 90); // days
        add_option('smart_faq_custom_css', '');
        add_option('smart_faq_default_category', 'General');
        add_option('smart_faq_enable_schema', 1); // Enable schema markup by default for SEO
        
        // New enhanced appearance settings defaults
        add_option('smart_faq_animation_type', 'slide');
        add_option('smart_faq_animation_duration', '300');
        add_option('smart_faq_animation_easing', 'ease');
        add_option('smart_faq_gradient_enabled', '0');
        add_option('smart_faq_gradient_color_1', '#667eea');
        add_option('smart_faq_gradient_color_2', '#764ba2');
        add_option('smart_faq_gradient_angle', '135');
        add_option('smart_faq_icon_style', 'arrow-down');
        add_option('smart_faq_icon_position', 'right');
        add_option('smart_faq_icon_size', '16');
        add_option('smart_faq_border_style', 'solid');
        // Default to the built-in "Modern Professional" look that mirrors base styling
        add_option('smart_faq_active_template', 'modern-professional');
        
        // Appearance settings (empty by default - use theme styles)
        add_option('smart_faq_question_bg_color', '');
        add_option('smart_faq_question_text_color', '');
        add_option('smart_faq_answer_bg_color', '');
        add_option('smart_faq_answer_text_color', '');
        add_option('smart_faq_accent_color', '');
        add_option('smart_faq_border_color', '');
        add_option('smart_faq_hover_bg_color', '');
        add_option('smart_faq_font_family', 'inherit');
        add_option('smart_faq_question_font_size', '');
        add_option('smart_faq_answer_font_size', '');
        add_option('smart_faq_question_font_weight', '');
        add_option('smart_faq_border_radius', '');
        add_option('smart_faq_question_padding', '');
        add_option('smart_faq_answer_padding', '');
        add_option('smart_faq_item_margin', '');
        add_option('smart_faq_border_width', '');
        add_option('smart_faq_enable_box_shadow', '');
        add_option('smart_faq_shadow_color', '');
        add_option('smart_faq_shadow_blur', '');
        add_option('smart_faq_enable_hover_effect', '');
        add_option('smart_faq_transition_speed', '');
        
        // Search and interaction settings
        add_option('smart_faq_enable_search', 0);
        add_option('smart_faq_search_placeholder', __('Search FAQs...', 'smart-faq-manager'));
        add_option('smart_faq_show_permalinks', 0);
    }
}
