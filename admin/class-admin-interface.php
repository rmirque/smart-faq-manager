<?php
/**
 * Admin interface handler
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Admin_Interface
 */
class Smart_FAQ_Admin_Interface {
    
    /**
     * Initialize admin interface
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
        add_action('admin_init', array(__CLASS__, 'process_form_submissions'));
        add_action('wp_ajax_smart_faq_toggle_status', array(__CLASS__, 'ajax_toggle_status'));
        add_action('wp_ajax_smart_faq_clear_cache', array(__CLASS__, 'ajax_clear_cache'));
        add_action('wp_ajax_smart_faq_export', array(__CLASS__, 'ajax_export'));
        add_action('wp_ajax_smart_faq_import', array(__CLASS__, 'ajax_import'));
        
        // Initialize meta box for manual FAQ selection
        Smart_FAQ_Meta_Box::init();
        
        // Initialize appearance settings
        Smart_FAQ_Appearance_Settings::init();
        
        // Clear page cache when post is updated
        add_action('save_post', array(__CLASS__, 'clear_page_cache_on_update'));
    }
    
    /**
     * Process form submissions early (before any output)
     */
    public static function process_form_submissions() {
        // Handle FAQ save - verify nonce first
        if (filter_input(INPUT_POST, 'smart_faq_submit', FILTER_SANITIZE_SPECIAL_CHARS)) {
            self::handle_save_faq();
            return;
        }
        
        // Handle FAQ delete - verify nonce first
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
        $faq_id = filter_input(INPUT_GET, 'faq_id', FILTER_VALIDATE_INT);
        if ($action === 'delete' && $faq_id) {
            self::handle_delete_faq();
            return;
        }
        
        // Handle bulk actions - verify nonce first
        $post_action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
        $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS);
        if ($post_action && $nonce && '-1' !== $post_action) {
            self::handle_bulk_actions();
            return;
        }
    }
    
    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __('FAQ Manager', 'smart-faq-manager'),
            __('FAQ Manager', 'smart-faq-manager'),
            'manage_options',
            'smart-faq-manager',
            array(__CLASS__, 'render_faq_list_page'),
            'dashicons-editor-help',
            30
        );
        
        // Submenu: All FAQs (same as main)
        add_submenu_page(
            'smart-faq-manager',
            __('All FAQs', 'smart-faq-manager'),
            __('All FAQs', 'smart-faq-manager'),
            'manage_options',
            'smart-faq-manager',
            array(__CLASS__, 'render_faq_list_page')
        );
        
        // Submenu: Add New
        add_submenu_page(
            'smart-faq-manager',
            __('Add New FAQ', 'smart-faq-manager'),
            __('Add New', 'smart-faq-manager'),
            'manage_options',
            'smart-faq-add',
            array(__CLASS__, 'render_add_edit_page')
        );
        
        // Submenu: Categories
        add_submenu_page(
            'smart-faq-manager',
            __('Categories', 'smart-faq-manager'),
            __('Categories', 'smart-faq-manager'),
            'manage_options',
            'smart-faq-categories',
            array(__CLASS__, 'render_categories_page')
        );
        
        // Submenu: Settings
        add_submenu_page(
            'smart-faq-manager',
            __('Settings', 'smart-faq-manager'),
            __('Settings', 'smart-faq-manager'),
            'manage_options',
            'smart-faq-settings',
            array(new Smart_FAQ_Settings(), 'render_settings_page')
        );
        
        // Submenu: Appearance
        add_submenu_page(
            'smart-faq-manager',
            __('Appearance', 'smart-faq-manager'),
            __('Appearance', 'smart-faq-manager'),
            'manage_options',
            'smart-faq-appearance',
            array(new Smart_FAQ_Appearance_Settings(), 'render_appearance_page')
        );
        
        // Submenu: Analytics
        add_submenu_page(
            'smart-faq-manager',
            __('Analytics', 'smart-faq-manager'),
            __('Analytics', 'smart-faq-manager'),
            'manage_options',
            'smart-faq-analytics',
            array(new Smart_FAQ_Analytics(), 'render_analytics_page')
        );
    }
    
    /**
     * Enqueue admin assets
     *
     * @param string $hook Hook suffix
     */
    public static function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        // Hook names: toplevel_page_smart-faq-manager, faq-manager_page_smart-faq-*, etc.
        if (strpos($hook, 'smart-faq') === false && strpos($hook, 'faq-manager') === false) {
            return;
        }
        
        // Styles
        wp_enqueue_style(
            'smart-faq-admin',
            SMART_FAQ_PLUGIN_URL . 'admin/css/admin-styles.css',
            array(),
            SMART_FAQ_VERSION
        );
        
        // Scripts
        wp_enqueue_script(
            'smart-faq-admin',
            SMART_FAQ_PLUGIN_URL . 'admin/js/admin-scripts.js',
            array('jquery'),
            SMART_FAQ_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('smart-faq-admin', 'smartFaqAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('smart_faq_admin'),
            'strings' => array(
                'confirmDelete' => __('Are you sure you want to delete this FAQ?', 'smart-faq-manager'),
                'confirmClearCache' => __('Are you sure you want to clear all cache?', 'smart-faq-manager'),
            ),
        ));
    }
    
    /**
     * Render FAQ list page
     */
    public static function render_faq_list_page() {
        $list_table = new Smart_FAQ_List_Table();
        $list_table->prepare_items();
        
        include SMART_FAQ_PLUGIN_DIR . 'admin/partials/faq-list.php';
    }
    
    /**
     * Render add/edit page
     */
    public static function render_add_edit_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Viewing edit form is restricted to privileged users via capability checks.
        $faq_id = isset($_GET['faq_id']) ? absint( wp_unslash( $_GET['faq_id'] ) ) : 0;
        $faq = null;
        
        if ($faq_id) {
            $faq = Smart_FAQ_Database::get_faq($faq_id);
            if (!$faq) {
                wp_die( esc_html__( 'FAQ not found.', 'smart-faq-manager' ) );
            }
        }
        
        include SMART_FAQ_PLUGIN_DIR . 'admin/partials/faq-edit.php';
    }
    
    /**
     * Render categories page
     */
    public static function render_categories_page() {
        $categories = Smart_FAQ_Database::get_categories();
        include SMART_FAQ_PLUGIN_DIR . 'admin/partials/categories.php';
    }
    
    /**
     * Handle save FAQ
     */
    private static function handle_save_faq() {
        // Verify nonce
        $save_nonce = isset($_POST['smart_faq_nonce']) ? sanitize_text_field( wp_unslash( $_POST['smart_faq_nonce'] ) ) : '';
        if (empty($save_nonce) || !wp_verify_nonce( $save_nonce, 'smart_faq_save')) {
            wp_die( esc_html__( 'Security check failed.', 'smart-faq-manager' ) );
        }
        
        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'smart-faq-manager' ) );
        }
        
        $faq_id = isset($_POST['faq_id']) ? absint( wp_unslash( $_POST['faq_id'] ) ) : 0;
        
        $data = array(
            'question_html' => isset($_POST['question_html']) ? wp_kses_post( wp_unslash( $_POST['question_html'] ) ) : '',
            'answer_html' => isset($_POST['answer_html']) ? wp_kses_post( wp_unslash( $_POST['answer_html'] ) ) : '',
            'keywords' => isset($_POST['keywords']) ? sanitize_textarea_field( wp_unslash( $_POST['keywords'] ) ) : '',
            'category' => isset($_POST['category']) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '',
            'priority' => isset($_POST['priority']) ? absint( wp_unslash( $_POST['priority'] ) ) : 0,
            'status' => isset($_POST['status']) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
        );
        
        if ($faq_id) {
            // Update existing FAQ
            $result = Smart_FAQ_Manager::update_faq($faq_id, $data);
            
            if (is_wp_error($result)) {
                $message = $result->get_error_message();
                wp_redirect(add_query_arg(array('page' => 'smart-faq-add', 'faq_id' => $faq_id, 'message' => 'error'), admin_url('admin.php')));
                exit;
            }
            
            wp_redirect(add_query_arg(array('page' => 'smart-faq-manager', 'message' => 'updated'), admin_url('admin.php')));
            exit;
        } else {
            // Create new FAQ
            $result = Smart_FAQ_Manager::create_faq($data);
            
            if (is_wp_error($result)) {
                $message = $result->get_error_message();
                wp_redirect(add_query_arg(array('page' => 'smart-faq-add', 'message' => 'error'), admin_url('admin.php')));
                exit;
            }
            
            wp_redirect(add_query_arg(array('page' => 'smart-faq-manager', 'message' => 'created'), admin_url('admin.php')));
            exit;
        }
    }
    
    /**
     * Handle delete FAQ
     */
    private static function handle_delete_faq() {
        // Verify nonce - sanitize FAQ ID first
        $faq_id = isset($_GET['faq_id']) ? absint( wp_unslash( $_GET['faq_id'] ) ) : 0;
        $delete_nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
        if (!$faq_id || empty($delete_nonce) || !wp_verify_nonce( $delete_nonce, 'delete_faq_' . $faq_id)) {
            wp_die( esc_html__( 'Security check failed.', 'smart-faq-manager' ) );
        }
        
        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'smart-faq-manager' ) );
        }
        $result = Smart_FAQ_Manager::delete_faq($faq_id);
        
        if (is_wp_error($result)) {
            wp_redirect(add_query_arg(array('page' => 'smart-faq-manager', 'message' => 'error'), admin_url('admin.php')));
            exit;
        }
        
        wp_redirect(add_query_arg(array('page' => 'smart-faq-manager', 'message' => 'deleted'), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Handle bulk actions
     */
    private static function handle_bulk_actions() {
        // Verify nonce
        $bulk_nonce = isset($_POST['_wpnonce']) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
        if (empty($bulk_nonce) || !wp_verify_nonce( $bulk_nonce, 'bulk-faqs')) {
            wp_die( esc_html__( 'Security check failed.', 'smart-faq-manager' ) );
        }
        
        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'smart-faq-manager' ) );
        }
        
        $action = isset($_POST['action']) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
        $faq_ids = isset($_POST['faq_ids']) ? array_map( 'absint', (array) wp_unslash( $_POST['faq_ids'] ) ) : array();
        
        if (empty($faq_ids)) {
            return;
        }
        
        switch ($action) {
            case 'delete':
                foreach ($faq_ids as $faq_id) {
                    Smart_FAQ_Manager::delete_faq($faq_id);
                }
                break;
                
            case 'activate':
                foreach ($faq_ids as $faq_id) {
                    Smart_FAQ_Database::update_faq($faq_id, array('status' => 'active'));
                }
                break;
                
            case 'deactivate':
                foreach ($faq_ids as $faq_id) {
                    Smart_FAQ_Database::update_faq($faq_id, array('status' => 'inactive'));
                }
                break;
        }
    }
    
    /**
     * AJAX: Toggle FAQ status
     */
    public static function ajax_toggle_status() {
        check_ajax_referer('smart_faq_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error( esc_html__( 'Permission denied.', 'smart-faq-manager' ) );
        }

        $faq_id = isset($_POST['faq_id']) ? absint( wp_unslash( $_POST['faq_id'] ) ) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

        if (!$faq_id || !$status) {
            wp_send_json_error( esc_html__( 'Invalid parameters.', 'smart-faq-manager' ) );
        }
        
        $result = Smart_FAQ_Database::update_faq($faq_id, array('status' => $status));
        
        if ($result) {
            wp_send_json_success( esc_html__( 'Status updated.', 'smart-faq-manager' ) );
        } else {
            wp_send_json_error( esc_html__( 'Failed to update status.', 'smart-faq-manager' ) );
        }
    }
    
    /**
     * AJAX: Clear cache
     */
    public static function ajax_clear_cache() {
        check_ajax_referer('smart_faq_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error( esc_html__( 'Permission denied.', 'smart-faq-manager' ) );
        }
        
        Smart_FAQ_Cache_Manager::clear_all_cache();
        wp_send_json_success( esc_html__( 'Cache cleared.', 'smart-faq-manager' ) );
    }
    
    /**
     * AJAX: Export FAQs
     */
    public static function ajax_export() {
        check_ajax_referer('smart_faq_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__( 'Permission denied.', 'smart-faq-manager' ) );
        }
        
        $json = Smart_FAQ_Manager::export_faqs();
        
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="smart-faq-export-' . gmdate('Y-m-d') . '.json"');
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- exporting raw JSON payload
        echo $json;
        exit;
    }
    
    /**
     * AJAX: Import FAQs
     */
    public static function ajax_import() {
        check_ajax_referer('smart_faq_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error( esc_html__( 'Permission denied.', 'smart-faq-manager' ) );
        }

        if (!isset($_FILES['import_file'])) {
            wp_send_json_error( esc_html__( 'No file uploaded.', 'smart-faq-manager' ) );
        }

        $file = $_FILES['import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File upload handled by WordPress
        $json = file_get_contents($file['tmp_name']);

        $result = Smart_FAQ_Manager::import_faqs($json);

        if (is_wp_error($result)) {
            wp_send_json_error( $result->get_error_message() );
        }

        wp_send_json_success($result);
    }
    
    /**
     * Clear page cache when post is updated
     *
     * @param int $post_id Post ID
     */
    public static function clear_page_cache_on_update($post_id) {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        
        Smart_FAQ_Cache_Manager::clear_page_cache($post_id);
    }
}
