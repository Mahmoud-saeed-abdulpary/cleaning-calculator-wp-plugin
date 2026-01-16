(function($) {
    'use strict';

    $(document).ready(function() {
        initColorPickers();
        initSettingsTabs();
        initDeleteConfirmation();
    });

    /**
     * Initialize color pickers
     */
    function initColorPickers() {
        if ($.fn.wpColorPicker) {
            $('.cpc-color-picker').wpColorPicker({
                change: function(event, ui) {
                    $(this).val(ui.color.toString());
                }
            });
        }
    }

    /**
     * Initialize settings tabs
     */
    function initSettingsTabs() {
        // Only run on settings page
        if (!$('.cpc-settings-tabs').length) {
            return;
        }

        // Tab click handler
        $('.cpc-settings-tabs .nav-tab').on('click', function(e) {
            e.preventDefault();
            
            const targetTab = $(this).attr('href').substring(1);
            
            // Update tab navigation
            $('.cpc-settings-tabs .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show/hide sections
            $('.cpc-settings-section').hide();
            $('#' + targetTab).show();
            
            // Update URL hash without scrolling
            if (history.pushState) {
                history.pushState(null, null, '#' + targetTab);
            } else {
                window.location.hash = targetTab;
            }
        });

        // Check for hash in URL on page load
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const targetTab = $('.cpc-settings-tabs .nav-tab[href="#' + hash + '"]');
            
            if (targetTab.length) {
                targetTab.click();
            } else {
                // Show first tab if hash doesn't match
                showFirstTab();
            }
        } else {
            // Show first tab by default
            showFirstTab();
        }
    }

    /**
     * Show the first tab
     */
    function showFirstTab() {
        $('.cpc-settings-tabs .nav-tab').first().addClass('nav-tab-active');
        $('.cpc-settings-section').first().show();
    }

    /**
     * Initialize delete confirmation dialogs
     */
    function initDeleteConfirmation() {
        $('.button-link-delete').on('click', function(e) {
            const confirmMessage = cpcAdmin && cpcAdmin.strings && cpcAdmin.strings.confirmDelete 
                ? cpcAdmin.strings.confirmDelete 
                : 'Are you sure you want to delete this item?';
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    }

    /**
     * Show temporary notice
     */
    function showNotice(message, type) {
        const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        const notice = $('<div>', {
            class: 'notice cpc-notice ' + noticeClass + ' is-dismissible',
            html: '<p>' + message + '</p>'
        });

        $('.cpc-admin-wrap h1').after(notice);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

})(jQuery);