<?php
/**
 * Admin FAQ Add/Edit Page Template
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_edit = isset($faq) && $faq;
$page_title = $is_edit ? __('Edit FAQ', 'smart-faq-manager') : __('Add New FAQ', 'smart-faq-manager');
$categories = Smart_FAQ_Database::get_categories();
?>

<div class="wrap">
    <h1>
        <span class="dashicons dashicons-edit" style="font-size: 28px; vertical-align: middle; margin-right: 8px; color: #667eea;"></span>
        <?php echo esc_html( $page_title ); ?>
    </h1>
    
    <?php
    $message = filter_input(INPUT_GET, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
    $allowed_messages = array('error');
    if ($message && in_array($message, $allowed_messages, true)) {
        $notice_messages = array(
            'error'   => esc_html__( 'An error occurred. Please try again.', 'smart-faq-manager' ),
        );
        if (isset($notice_messages[$message])) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $notice_messages[$message] ) . '</p></div>';
        }
    }
    ?>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=smart-faq-add')); ?>">
        <?php wp_nonce_field('smart_faq_save', 'smart_faq_nonce'); ?>
        <input type="hidden" name="smart_faq_submit" value="1" />
        
        <?php if ($is_edit) : ?>
            <input type="hidden" name="faq_id" value="<?php echo esc_attr($faq->id); ?>" />
        <?php endif; ?>
        
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php esc_html_e('Question', 'smart-faq-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <?php
                            $question_content = $is_edit ? $faq->question_html : '';
                            wp_editor($question_content, 'question_html', array(
                                'textarea_name' => 'question_html',
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                                'teeny' => true,
                            ));
                            ?>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php esc_html_e('Answer', 'smart-faq-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <?php
                            $answer_content = $is_edit ? $faq->answer_html : '';
                            wp_editor($answer_content, 'answer_html', array(
                                'textarea_name' => 'answer_html',
                                'textarea_rows' => 10,
                                'media_buttons' => true,
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                
                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php esc_html_e('Publish', 'smart-faq-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <div class="submitbox">
                                <div class="misc-pub-section">
                                    <label>
                                        <strong><?php esc_html_e('Status:', 'smart-faq-manager'); ?></strong>
                                        <select name="status">
                                            <option value="active" <?php echo $is_edit ? selected($faq->status, 'active', false) : ''; ?>>
                                                <?php esc_html_e('Active', 'smart-faq-manager'); ?>
                                            </option>
                                            <option value="inactive" <?php echo $is_edit ? selected($faq->status, 'inactive', false) : ''; ?>>
                                                <?php esc_html_e('Inactive', 'smart-faq-manager'); ?>
                                            </option>
                                            <option value="draft" <?php echo $is_edit ? selected($faq->status, 'draft', false) : ''; ?>>
                                                <?php esc_html_e('Draft', 'smart-faq-manager'); ?>
                                            </option>
                                        </select>
                                    </label>
                                </div>
                                
                                <div id="major-publishing-actions">
                                    <div id="publishing-action">
                                        <?php
                                        $submit_label = $is_edit
                                            ? __('Update', 'smart-faq-manager')
                                            : __('Publish', 'smart-faq-manager');
                                        ?>
                                        <input type="submit" class="button button-primary button-large" value="<?php echo esc_attr( $submit_label ); ?>" />
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php esc_html_e('Category', 'smart-faq-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <input type="text" name="category" list="category-list" value="<?php echo $is_edit ? esc_attr($faq->category) : ''; ?>" class="widefat" placeholder="<?php esc_attr_e('Enter or select category', 'smart-faq-manager'); ?>" />
                            <datalist id="category-list">
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo esc_attr($category); ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <p class="description"><?php esc_html_e('Categorize this FAQ for better organization.', 'smart-faq-manager'); ?></p>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php esc_html_e('Priority', 'smart-faq-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <input type="range" name="priority" min="0" max="100" value="<?php echo $is_edit ? esc_attr($faq->priority) : '50'; ?>" class="widefat" id="priority-slider" />
                            <div class="priority-value">
                                <span id="priority-display"><?php echo $is_edit ? esc_html($faq->priority) : '50'; ?></span>
                            </div>
                            <p class="description"><?php esc_html_e('Higher priority FAQs are more likely to be displayed. (0-100)', 'smart-faq-manager'); ?></p>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php esc_html_e('Keywords', 'smart-faq-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <textarea name="keywords" rows="4" class="widefat"><?php echo $is_edit ? esc_textarea($faq->keywords) : ''; ?></textarea>
                            <p class="description"><?php esc_html_e('Comma-separated keywords to improve matching (optional).', 'smart-faq-manager'); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($is_edit && isset($faq->display_count)) : ?>
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2><?php esc_html_e('Statistics', 'smart-faq-manager'); ?></h2>
                            </div>
                            <div class="inside">
                                <p><strong><?php esc_html_e('Times Displayed:', 'smart-faq-manager'); ?></strong> <?php echo esc_html($faq->display_count); ?></p>
                                <p><strong><?php esc_html_e('Created:', 'smart-faq-manager'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($faq->created_at))); ?></p>
                                <p><strong><?php esc_html_e('Last Updated:', 'smart-faq-manager'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($faq->updated_at))); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#priority-slider').on('input', function() {
        $('#priority-display').text($(this).val());
    });
});
</script>
