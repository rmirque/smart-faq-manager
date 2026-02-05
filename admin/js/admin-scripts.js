/**
 * Admin Scripts
 *
 * @package Smart_FAQ_Manager
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Clear cache button (works on both FAQ list page and settings page)
        $('#clear-cache-btn, #clear-all-cache-btn').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm(smartFaqAdmin.strings.confirmClearCache)) {
                return;
            }
            
            var $btn = $(this);
            $btn.prop('disabled', true).text('Clearing...');
            
            $.ajax({
                url: smartFaqAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'smart_faq_clear_cache',
                    nonce: smartFaqAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Cache cleared successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred while clearing cache.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Clear All Cache');
                }
            });
        });
        
        // Export FAQs
        $('#export-faqs-btn').on('click', function(e) {
            e.preventDefault();
            
            var url = smartFaqAdmin.ajaxUrl + '?action=smart_faq_export&nonce=' + smartFaqAdmin.nonce;
            window.location.href = url;
        });
        
        // Import FAQs - Show modal
        $('#import-faqs-btn').on('click', function(e) {
            e.preventDefault();
            $('#import-modal').fadeIn();
        });
        
        // Import FAQs - Close modal
        $('.smart-faq-modal-close').on('click', function() {
            $('#import-modal').fadeOut();
        });
        
        // Close modal on outside click
        $(window).on('click', function(e) {
            if ($(e.target).hasClass('smart-faq-modal')) {
                $('.smart-faq-modal').fadeOut();
            }
        });
        
        // Import form submission
        $('#import-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('action', 'smart_faq_import');
            formData.append('nonce', smartFaqAdmin.nonce);
            
            $.ajax({
                url: smartFaqAdmin.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Import successful! Imported: ' + response.data.imported + ', Failed: ' + response.data.failed);
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred during import.');
                }
            });
        });
        
        // Delete FAQ confirmation
        $('.delete-faq').on('click', function(e) {
            if (!confirm(smartFaqAdmin.strings.confirmDelete)) {
                e.preventDefault();
            }
        });
        
        // Copy FAQ link
        $('.smart-faq-copy-link').on('click', function(e) {
            e.preventDefault();
            
            var faqId = $(this).data('faq-id');
            var link = window.location.origin + '#faq-' + faqId;
            
            // Copy to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(link).then(function() {
                    alert('FAQ link copied to clipboard!\n\n' + link);
                }).catch(function() {
                    // Fallback
                    copyToClipboardFallback(link);
                });
            } else {
                // Fallback for older browsers
                copyToClipboardFallback(link);
            }
        });
        
        // Fallback clipboard copy
        function copyToClipboardFallback(text) {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            alert('FAQ link copied to clipboard!\n\n' + text);
        }
        
        // Toggle FAQ status
        $('.toggle-faq-status').on('click', function(e) {
            e.preventDefault();
            
            var $link = $(this);
            var faqId = $link.data('faq-id');
            var newStatus = $link.data('status');
            
            $.ajax({
                url: smartFaqAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'smart_faq_toggle_status',
                    nonce: smartFaqAdmin.nonce,
                    faq_id: faqId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred.');
                }
            });
        });
        
    });
    
})(jQuery);


