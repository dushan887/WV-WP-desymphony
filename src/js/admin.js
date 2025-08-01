(function($) {
    'use strict';

    // ====================================================
    // Auth Pages Functions
    // ====================================================
    function handleAuthAjaxResponse(response) {
        var noticeArea = $('#wv-addon-admin-notifications-area');
        if (!response) {
            noticeArea.html('<div class="notice notice-error"><p>Unknown error.</p></div>');
            return;
        }
        if (response.success) {
            var msg = response.data.message ? response.data.message : 'Success!';
            noticeArea.html('<div class="notice notice-success"><p>' + msg + '</p></div>');
        } else {
            var errMsg = (response.data && response.data.message) ? response.data.message : 'Error';
            noticeArea.html('<div class="notice notice-error"><p>' + errMsg + '</p></div>');
        }
    }

    function saveAuthSettings(action, callback) {
        var data = {
            action: action,
            nonce: wvAddonAdmin.auth_nonce
        };

        // Gather all auth settings (dropdowns and textareas)
        $('select[name^="wv_addon_auth_page_"], textarea[name^="wv_addon_auth_success_msg_"]').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        $.post(ajaxurl, data, function(response) {
            handleAuthAjaxResponse(response);
            if (typeof callback === 'function') {
                callback(response);
            }
        });
    }

    // Auth Settings button event bindings
    $(document).on('click', '#wv-addon-save-auth-settings', function(e) {
        e.preventDefault();
        saveAuthSettings('wv_addon_save_auth_pages');
    });

    $(document).on('click', '#wv-addon-regenerate-auth-pages', function(e) {
        e.preventDefault();
        saveAuthSettings('wv_addon_save_auth_pages', function() {
            var genData = {
                action: 'wv_addon_regenerate_auth_pages',
                nonce: wvAddonAdmin.auth_nonce
            };
            $.post(ajaxurl, genData, function(response) {
                handleAuthAjaxResponse(response);
            });
        });
    });

    $(document).on('click', '#wv-addon-create-update-missing-pages', function(e) {
        e.preventDefault();
        saveAuthSettings('wv_addon_save_auth_pages', function() {
            var createData = {
                action: 'wv_addon_create_update_missing_pages',
                nonce: wvAddonAdmin.auth_nonce
            };
            $.post(ajaxurl, createData, function(response) {
                handleAuthAjaxResponse(response);
            });
        });
    });


    // ====================================================
    // Dashboard Pages Functions
    // ====================================================
    function handleDashboardAjaxResponse(response) {
        var noticeArea = $('#wv-addon-admin-notifications-area');
        if (!response) {
            noticeArea.html('<div class="notice notice-error"><p>Unknown error.</p></div>');
            return;
        }
        if (response.success) {
            var msg = response.data.message ? response.data.message : 'Success!';
            noticeArea.html('<div class="notice notice-success"><p>' + msg + '</p></div>');
        } else {
            var errMsg = (response.data && response.data.message) ? response.data.message : 'Error';
            noticeArea.html('<div class="notice notice-error"><p>' + errMsg + '</p></div>');
        }
    }

    function saveDashboardSettings(action, callback) {
        var data = {
            action: action,
            nonce: wvAddonAdmin.dashboard_nonce
        };

        // Gather all dashboard settings from dropdowns with names starting with "wv_addon_dashboard_page_"
        $('select[name^="wv_addon_dashboard_page_"]').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        $.post(ajaxurl, data, function(response) {
            handleDashboardAjaxResponse(response);
            if (typeof callback === 'function') {
                callback(response);
            }
        });
    }

    // Dashboard Settings button event bindings
    $(document).on('click', '#wv-addon-save-dashboard-settings', function(e) {
        e.preventDefault();
        saveDashboardSettings('wv_addon_save_dashboard_pages');
    });

    $(document).on('click', '#wv-addon-regenerate-dashboard-pages', function(e) {
        e.preventDefault();
        saveDashboardSettings('wv_addon_save_dashboard_pages', function() {
            var genData = {
                action: 'wv_addon_regenerate_dashboard_pages',
                nonce: wvAddonAdmin.dashboard_nonce
            };
            $.post(ajaxurl, genData, function(response) {
                handleDashboardAjaxResponse(response);
            });
        });
    });

    $(document).on('click', '#wv-addon-create-update-missing-dashboard-pages', function(e) {
        e.preventDefault();
        saveDashboardSettings('wv_addon_save_dashboard_pages', function() {
            var createData = {
                action: 'wv_addon_create_update_missing_dashboard_pages',
                nonce: wvAddonAdmin.dashboard_nonce
            };
            $.post(ajaxurl, createData, function(response) {
                handleDashboardAjaxResponse(response);
            });
        });
    });

    // ====================================================
    // Create / Update Exhibitor↔Co‑Exhibitor Links Table
    // ====================================================
    $(document).on('click', '#wv-addon-install-exhibitor-links-table', function(e){
        e.preventDefault();
        var noticeArea = $('#wv-addon-admin-notifications-area').empty();
    
        $.post(ajaxurl, {
        action: 'wv_addon_install_exhibitor_links_table',
        nonce: wvAddonAdmin.links_nonce
        }, function(response){
        if (response && response.success) {
            noticeArea.html(
            '<div class="notice notice-success"><p>' + response.data.message + '</p></div>'
            );
        } else {
            var msg = (response && response.data && response.data.message)
                    ? response.data.message
                    : 'Unknown error';
            noticeArea.html(
            '<div class="notice notice-error"><p>' + msg + '</p></div>'
            );
        }
        });
    });

    // ====================================================
    // Create / Update Exhibitor Products Table
    // ====================================================
    $(document).on('click', '#wv-addon-install-exhibitor-products-table', function(e) {
        e.preventDefault();
        var noticeArea = $('#wv-addon-admin-notifications-area').empty();

        $.post(ajaxurl, {
            action: 'wv_addon_install_exhibitor_products_table',
            nonce: wvAddonAdmin.products_nonce
        }, function(response) {
            if (response && response.success) {
                noticeArea.html(
                    '<div class="notice notice-success"><p>' + response.data.message + '</p></div>'
                );
            } else {
                var msg = (response && response.data && response.data.message)
                    ? response.data.message
                    : 'Unknown error';
                noticeArea.html(
                    '<div class="notice notice-error"><p>' + msg + '</p></div>'
                );
            }
        });
    });

    // ====================================================
    // Create / Update Exhibitor Favorites Table
    // ====================================================
    $(document).on('click', '#wv-addon-install-favorites-table', function(e) {
        e.preventDefault();
        var noticeArea = $('#wv-addon-admin-notifications-area').empty();

        $.post(ajaxurl, {
            action: 'wv_addon_install_favorites_table',
            nonce: wvAddonAdmin.favorites_nonce
        }, function(response) {
            if (response && response.success) {
                noticeArea.html(
                    '<div class="notice notice-success"><p>' + response.data.message + '</p></div>'
                );
            } else {
                var msg = (response && response.data && response.data.message)
                    ? response.data.message
                    : 'Unknown error';
                noticeArea.html(
                    '<div class="notice notice-error"><p>' + msg + '</p></div>'
                );
            }
        });
    });
    
})(jQuery);
