<?php
/**
 * Meta Box for Manual FAQ Selection
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Meta_Box
 */
class Smart_FAQ_Meta_Box {
    
    /**
     * Initialize meta box
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_box'));
        add_action('save_post', array(__CLASS__, 'save_meta_box'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
    }
    
    /**
     * Add meta box to post/page editor
     */
    public static function add_meta_box() {
        $post_types = get_post_types(array('public' => true));
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'smart_faq_manual_selection',
                __('Smart FAQ - Manual Selection', 'smart-faq-manager'),
                array(__CLASS__, 'render_meta_box'),
                $post_type,
                'side',
                'default'
            );
        }
    }
    
    /**
     * Render meta box content
     *
     * @param WP_Post $post Current post object
     */
    public static function render_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('smart_faq_meta_box', 'smart_faq_meta_box_nonce');
        
        // Get current settings
        $manual_mode = get_post_meta($post->ID, '_smart_faq_manual_mode', true);
        $selected_faqs = get_post_meta($post->ID, '_smart_faq_selected', true);
        
        if (!is_array($selected_faqs)) {
            $selected_faqs = array();
        }
        
        // Get all active FAQs
        $all_faqs = Smart_FAQ_Database::get_active_faqs(array('limit' => 200, 'orderby' => 'question'));
        
        ?>
        <div class="smart-faq-meta-box">
            <p>
                <label>
                    <input type="radio" name="smart_faq_manual_mode" value="auto" <?php checked($manual_mode, 'auto'); ?> <?php checked($manual_mode, ''); ?>>
                    <strong><?php esc_html_e('Automatic Matching', 'smart-faq-manager'); ?></strong>
                </label>
                <br>
                <span class="description"><?php esc_html_e('Let the plugin automatically select relevant FAQs', 'smart-faq-manager'); ?></span>
            </p>
            
            <p>
                <label>
                    <input type="radio" name="smart_faq_manual_mode" value="supplement" <?php checked($manual_mode, 'supplement'); ?>>
                    <strong><?php esc_html_e('Manual + Automatic', 'smart-faq-manager'); ?></strong>
                </label>
                <br>
                <span class="description"><?php esc_html_e('Show selected FAQs first, then fill with automatic matches', 'smart-faq-manager'); ?></span>
            </p>
            
            <p>
                <label>
                    <input type="radio" name="smart_faq_manual_mode" value="manual" <?php checked($manual_mode, 'manual'); ?>>
                    <strong><?php esc_html_e('Manual Only', 'smart-faq-manager'); ?></strong>
                </label>
                <br>
                <span class="description"><?php esc_html_e('Show ONLY the FAQs you select below', 'smart-faq-manager'); ?></span>
            </p>
            
            <hr style="margin: 15px 0;">
            
            <div class="smart-faq-selection-container" style="<?php echo ($manual_mode === 'auto' || empty($manual_mode)) ? 'opacity: 0.5;' : ''; ?>">
                <p><strong><?php esc_html_e('Select FAQs:', 'smart-faq-manager'); ?></strong></p>
                
                <?php if (empty($all_faqs)) : ?>
                    <p class="description">
                        <?php esc_html_e('No FAQs available.', 'smart-faq-manager'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=smart-faq-add')); ?>">
                            <?php esc_html_e('Create your first FAQ', 'smart-faq-manager'); ?>
                        </a>
                    </p>
                <?php else : ?>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fafafa;">
                        <?php foreach ($all_faqs as $faq) : ?>
                            <label style="display: block; margin-bottom: 8px; cursor: pointer;">
                                <input type="checkbox" 
                                       name="smart_faq_selected[]" 
                                       value="<?php echo esc_attr($faq->id); ?>"
                                       <?php checked(in_array($faq->id, $selected_faqs)); ?>>
                                <span style="font-size: 12px;">
                                    <?php echo esc_html(wp_trim_words(wp_strip_all_tags($faq->question_html), 12)); ?>
                                    <?php if (!empty($faq->category)) : ?>
                                        <span style="color: #666;">
                                            [<?php echo esc_html($faq->category); ?>]
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="description" style="margin-top: 10px;">
                        <?php
                        /* translators: %d: selected FAQ count. */
                        printf(
                            /* translators: %d: selected FAQ count. */
                            wp_kses_post( __( 'Selected: <span id="smart-faq-count">%d</span> FAQs', 'smart-faq-manager' ) ),
                            absint( count( $selected_faqs ) )
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <hr style="margin: 15px 0;">
            
            <p class="description">
                <strong><?php esc_html_e('Tip:', 'smart-faq-manager'); ?></strong>
                <?php esc_html_e('Use manual selection for landing pages or when you want precise control.', 'smart-faq-manager'); ?>
            </p>
        </div>
        
        <style>
            .smart-faq-meta-box label {
                cursor: pointer;
            }
            .smart-faq-selection-container {
                transition: opacity 0.3s;
            }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Update selection container opacity based on mode
            $('input[name="smart_faq_manual_mode"]').on('change', function() {
                var mode = $(this).val();
                var $container = $('.smart-faq-selection-container');
                
                if (mode === 'auto') {
                    $container.css('opacity', '0.5');
                } else {
                    $container.css('opacity', '1');
                }
            });
            
            // Update count
            $('input[name="smart_faq_selected[]"]').on('change', function() {
                var count = $('input[name="smart_faq_selected[]"]:checked').length;
                $('#smart-faq-count').text(count);
            });
        });
        </script>
        <?php
    }
    
    /**
     * Save meta box data
     *
     * @param int $post_id Post ID
     */
    public static function save_meta_box($post_id) {
        // Check if nonce is set
        if (!isset($_POST['smart_faq_meta_box_nonce'])) {
            return;
        }
        
        // Verify nonce
        $meta_box_nonce = sanitize_text_field( wp_unslash( $_POST['smart_faq_meta_box_nonce'] ) );
        if (!wp_verify_nonce( $meta_box_nonce, 'smart_faq_meta_box')) {
            return;
        }
        
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save manual mode
        if (isset($_POST['smart_faq_manual_mode'])) {
            $mode = sanitize_text_field( wp_unslash( $_POST['smart_faq_manual_mode'] ) );
            update_post_meta($post_id, '_smart_faq_manual_mode', $mode);
        }
        
        // Save selected FAQs
        if (isset($_POST['smart_faq_selected']) && is_array($_POST['smart_faq_selected'])) {
            $selected = array_map( 'absint', (array) wp_unslash( $_POST['smart_faq_selected'] ) );
            update_post_meta($post_id, '_smart_faq_selected', $selected);
        } else {
            update_post_meta($post_id, '_smart_faq_selected', array());
        }
        
        // Clear cache for this page
        Smart_FAQ_Cache_Manager::clear_page_cache($post_id);
    }
    
    /**
     * Enqueue assets for meta box
     */
    public static function enqueue_assets($hook) {
        global $post;
        
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        if (!$post) {
            return;
        }
        
        // Assets are inline in the meta box for simplicity
    }
}


