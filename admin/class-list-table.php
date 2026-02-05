<?php
/**
 * FAQ List Table
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Class Smart_FAQ_List_Table
 */
class Smart_FAQ_List_Table extends WP_List_Table {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => 'faq',
            'plural' => 'faqs',
            'ajax' => false,
        ));
    }
    
    /**
     * Get columns
     *
     * @return array
     */
    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'question' => __('Question', 'smart-faq-manager'),
            'category' => __('Category', 'smart-faq-manager'),
            'status' => __('Status', 'smart-faq-manager'),
            'priority' => __('Priority', 'smart-faq-manager'),
            'display_count' => __('Displays', 'smart-faq-manager'),
            'created_at' => __('Created', 'smart-faq-manager'),
        );
    }
    
    /**
     * Get sortable columns
     *
     * @return array
     */
    public function get_sortable_columns() {
        return array(
            'question' => array('question', false),
            'category' => array('category', false),
            'status' => array('status', false),
            'priority' => array('priority', true),
            'display_count' => array('display_count', false),
            'created_at' => array('created_at', false),
        );
    }
    
    /**
     * Get bulk actions
     *
     * @return array
     */
    public function get_bulk_actions() {
        return array(
            'delete' => __('Delete', 'smart-faq-manager'),
            'activate' => __('Activate', 'smart-faq-manager'),
            'deactivate' => __('Deactivate', 'smart-faq-manager'),
        );
    }
    
    /**
     * Column: Checkbox
     *
     * @param object $item FAQ item
     * @return string
     */
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="faq_ids[]" value="%d" />', $item->id);
    }
    
    /**
     * Column: Question
     *
     * @param object $item FAQ item
     * @return string
     */
    public function column_question($item) {
        $question = wp_strip_all_tags($item->question_html);
        $question = mb_substr($question, 0, 100);
        
        $edit_url = add_query_arg(array(
            'page' => 'smart-faq-add',
            'faq_id' => $item->id,
        ), admin_url('admin.php'));
        
        $delete_url = wp_nonce_url(
            add_query_arg(array(
                'page' => 'smart-faq-manager',
                'action' => 'delete',
                'faq_id' => $item->id,
            ), admin_url('admin.php')),
            'delete_faq_' . $item->id
        );
        
        $actions = array(
            'edit' => sprintf('<a href="%s">%s</a>', esc_url($edit_url), __('Edit', 'smart-faq-manager')),
            'copy_link' => sprintf('<a href="#" class="smart-faq-copy-link" data-faq-id="%d">%s</a>', $item->id, __('Copy Link', 'smart-faq-manager')),
            'delete' => sprintf('<a href="%s" class="delete-faq">%s</a>', esc_url($delete_url), __('Delete', 'smart-faq-manager')),
        );
        
        return sprintf(
            '<strong><a href="%s">%s</a></strong> %s',
            esc_url($edit_url),
            esc_html($question),
            $this->row_actions($actions)
        );
    }
    
    /**
     * Column: Category
     *
     * @param object $item FAQ item
     * @return string
     */
    public function column_category($item) {
        return !empty($item->category) ? esc_html($item->category) : '—';
    }
    
    /**
     * Column: Status
     *
     * @param object $item FAQ item
     * @return string
     */
    public function column_status($item) {
        $statuses = array(
            'active' => '<span class="status-badge status-active">' . __('Active', 'smart-faq-manager') . '</span>',
            'inactive' => '<span class="status-badge status-inactive">' . __('Inactive', 'smart-faq-manager') . '</span>',
            'draft' => '<span class="status-badge status-draft">' . __('Draft', 'smart-faq-manager') . '</span>',
        );
        
        return isset($statuses[$item->status]) ? $statuses[$item->status] : $item->status;
    }
    
    /**
     * Column: Priority
     *
     * @param object $item FAQ item
     * @return string
     */
    public function column_priority($item) {
        return absint($item->priority);
    }
    
    /**
     * Column: Display count
     *
     * @param object $item FAQ item
     * @return string
     */
    public function column_display_count($item) {
        return number_format_i18n($item->display_count);
    }
    
    /**
     * Column: Created date
     *
     * @param object $item FAQ item
     * @return string
     */
    public function column_created_at($item) {
        return date_i18n(get_option('date_format'), strtotime($item->created_at));
    }
    
    /**
     * Prepare items
     */
    public function prepare_items() {
        global $wpdb;
        $table = sprintf('`%s`', esc_sql( $wpdb->prefix . 'smart_faq_items' ));
        
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;
        
        // Get orderby and order
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Sorting links do not include nonces.
        $orderby_raw = isset($_GET['orderby']) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'created_at';
        $orderby = sanitize_sql_orderby( $orderby_raw );
        if (empty($orderby)) {
            $orderby = 'created_at';
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Sorting links do not include nonces.
        $order_param = isset($_GET['order']) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
        $order_param = strtolower( $order_param );
        $order = $order_param === 'asc' ? 'ASC' : 'DESC';

        $filters_nonce_value = isset($_GET['smart_faq_filters_nonce']) ? sanitize_text_field( wp_unslash( $_GET['smart_faq_filters_nonce'] ) ) : '';
        $filters_nonce_valid = !empty($filters_nonce_value) && wp_verify_nonce( $filters_nonce_value, 'smart_faq_filters' );

        // Filter by status
        if ($filters_nonce_valid && isset($_GET['status']) && $_GET['status'] !== '') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $status = sanitize_text_field( wp_unslash( $_GET['status'] ) );
        }

        // Filter by category
        if ($filters_nonce_valid && isset($_GET['category']) && $_GET['category'] !== '') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $category = sanitize_text_field( wp_unslash( $_GET['category'] ) );
        }

        // Search
        if ($filters_nonce_valid && isset($_GET['s']) && $_GET['s'] !== '') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $search_term = sanitize_text_field( wp_unslash( $_GET['s'] ) );
            $search = '%' . $wpdb->esc_like( $search_term ) . '%';
        }
        
        $where_clauses = array('1=1');
        $params = array();

        if ($filters_nonce_valid && isset($status) && '' !== $status) {
            $where_clauses[] = 'status = %s';
            $params[] = $status;
        }

        if ($filters_nonce_valid && isset($category) && '' !== $category) {
            $where_clauses[] = 'category = %s';
            $params[] = $category;
        }

        if ($filters_nonce_valid && isset($search)) {
            $where_clauses[] = '(question LIKE %s OR answer LIKE %s OR keywords LIKE %s)';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $where_sql = implode(' AND ', $where_clauses);

        $select_sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        $select_params = array_merge($params, array($per_page, $offset));
        array_unshift($select_params, $select_sql);
        $prepared_select = call_user_func_array(array($wpdb, 'prepare'), $select_params);
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Querying custom FAQ table with prepared statement.
        $items = $wpdb->get_results( $prepared_select );
        
        $count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        if (!empty($params)) {
            $count_params = $params;
            array_unshift($count_params, $count_sql);
            $prepared_count = call_user_func_array(array($wpdb, 'prepare'), $count_params);
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Counting rows in custom FAQ table with prepared statement.
            $total_items = (int) $wpdb->get_var( $prepared_count );
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Counting rows in custom FAQ table with no placeholders required.
            $total_items = (int) $wpdb->get_var( $count_sql );
        }
        
        $this->items = $items;
        
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ));
        
        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns(),
        );
    }
    
    /**
     * Display extra tablenav
     *
     * @param string $which
     */
    protected function extra_tablenav($which) {
        if ($which !== 'top') {
            return;
        }
        
        $categories = Smart_FAQ_Database::get_categories();
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Displaying sanitized filter values
        $current_category = isset($_GET['category']) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Displaying sanitized filter values
        $current_status = isset($_GET['status']) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
        ?>
        <div class="alignleft actions">
            <select name="status">
                <option value=""><?php esc_html_e('All Statuses', 'smart-faq-manager'); ?></option>
                <option value="active" <?php selected($current_status, 'active'); ?>><?php esc_html_e('Active', 'smart-faq-manager'); ?></option>
                <option value="inactive" <?php selected($current_status, 'inactive'); ?>><?php esc_html_e('Inactive', 'smart-faq-manager'); ?></option>
                <option value="draft" <?php selected($current_status, 'draft'); ?>><?php esc_html_e('Draft', 'smart-faq-manager'); ?></option>
            </select>
            
            <?php if (!empty($categories)) : ?>
                <select name="category">
                    <option value=""><?php esc_html_e('All Categories', 'smart-faq-manager'); ?></option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo esc_attr($category); ?>" <?php selected($current_category, $category); ?>>
                            <?php echo esc_html($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            
            <input type="submit" class="button" value="<?php echo esc_attr__('Filter', 'smart-faq-manager'); ?>" />
        </div>
        <?php
    }
    
    /**
     * Display no items message
     */
    public function no_items() {
        esc_html_e('No FAQs found.', 'smart-faq-manager');
    }
}
