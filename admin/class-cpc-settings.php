<?php
/**
 * Settings management
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin
 */

class CPC_Settings {
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Company Information
        register_setting('cpc_company_settings', 'cpc_company_name');
        register_setting('cpc_company_settings', 'cpc_contact_phone');
        register_setting('cpc_company_settings', 'cpc_admin_email');
        register_setting('cpc_company_settings', 'cpc_currency');
        
        // Email Settings
        register_setting('cpc_email_settings', 'cpc_smtp_enabled');
        register_setting('cpc_email_settings', 'cpc_smtp_host');
        register_setting('cpc_email_settings', 'cpc_smtp_port');
        register_setting('cpc_email_settings', 'cpc_smtp_username');
        register_setting('cpc_email_settings', 'cpc_smtp_password');
        register_setting('cpc_email_settings', 'cpc_smtp_encryption');
        register_setting('cpc_email_settings', 'cpc_email_from_name');
        register_setting('cpc_email_settings', 'cpc_email_from_address');
        
        // Language Settings
        register_setting('cpc_language_settings', 'cpc_default_language');
        
        // Design Settings
        register_setting('cpc_design_settings', 'cpc_primary_color');
        register_setting('cpc_design_settings', 'cpc_button_color');
        register_setting('cpc_design_settings', 'cpc_accent_color');
        
        // Form Display Settings
        register_setting('cpc_form_settings', 'cpc_quote_form_display');
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        // Security check
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'cleaning-price-calculator'));
        }
        
        check_admin_referer('cpc_save_settings', 'cpc_settings_nonce');
        
        // Company Information
        if (isset($_POST['cpc_company_name'])) {
            update_option('cpc_company_name', sanitize_text_field($_POST['cpc_company_name']));
        }
        if (isset($_POST['cpc_contact_phone'])) {
            update_option('cpc_contact_phone', sanitize_text_field($_POST['cpc_contact_phone']));
        }
        if (isset($_POST['cpc_admin_email'])) {
            update_option('cpc_admin_email', sanitize_email($_POST['cpc_admin_email']));
        }
        if (isset($_POST['cpc_currency'])) {
            update_option('cpc_currency', sanitize_text_field($_POST['cpc_currency']));
        }
        
        // Email Settings
        if (isset($_POST['cpc_smtp_enabled'])) {
            update_option('cpc_smtp_enabled', sanitize_text_field($_POST['cpc_smtp_enabled']));
        }
        if (isset($_POST['cpc_smtp_host'])) {
            update_option('cpc_smtp_host', sanitize_text_field($_POST['cpc_smtp_host']));
        }
        if (isset($_POST['cpc_smtp_port'])) {
            update_option('cpc_smtp_port', sanitize_text_field($_POST['cpc_smtp_port']));
        }
        if (isset($_POST['cpc_smtp_username'])) {
            update_option('cpc_smtp_username', sanitize_text_field($_POST['cpc_smtp_username']));
        }
        if (isset($_POST['cpc_smtp_password'])) {
            update_option('cpc_smtp_password', sanitize_text_field($_POST['cpc_smtp_password']));
        }
        if (isset($_POST['cpc_smtp_encryption'])) {
            update_option('cpc_smtp_encryption', sanitize_text_field($_POST['cpc_smtp_encryption']));
        }
        if (isset($_POST['cpc_email_from_name'])) {
            update_option('cpc_email_from_name', sanitize_text_field($_POST['cpc_email_from_name']));
        }
        if (isset($_POST['cpc_email_from_address'])) {
            update_option('cpc_email_from_address', sanitize_email($_POST['cpc_email_from_address']));
        }
        
        // Language Settings
        if (isset($_POST['cpc_default_language'])) {
            update_option('cpc_default_language', sanitize_text_field($_POST['cpc_default_language']));
        }
        
        // Design Settings
        if (isset($_POST['cpc_primary_color'])) {
            update_option('cpc_primary_color', sanitize_hex_color($_POST['cpc_primary_color']));
        }
        if (isset($_POST['cpc_button_color'])) {
            update_option('cpc_button_color', sanitize_hex_color($_POST['cpc_button_color']));
        }
        if (isset($_POST['cpc_accent_color'])) {
            update_option('cpc_accent_color', sanitize_hex_color($_POST['cpc_accent_color']));
        }
        
        // Form Display Settings
        if (isset($_POST['cpc_quote_form_display'])) {
            update_option('cpc_quote_form_display', sanitize_text_field($_POST['cpc_quote_form_display']));
        }
        
        wp_redirect(add_query_arg(array(
            'page' => 'cpc-settings',
            'message' => urlencode(__('Settings saved successfully.', 'cleaning-price-calculator'))
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Get available currencies
     */
    public static function get_currencies() {
        return array(
            'EUR' => '€ ' . __('Euro', 'cleaning-price-calculator'),
            'USD' => '$ ' . __('US Dollar', 'cleaning-price-calculator'),
            'GBP' => '£ ' . __('British Pound', 'cleaning-price-calculator'),
            'CHF' => 'CHF ' . __('Swiss Franc', 'cleaning-price-calculator'),
            'AED' => 'د.إ ' . __('UAE Dirham', 'cleaning-price-calculator'),
            'SAR' => 'ر.س ' . __('Saudi Riyal', 'cleaning-price-calculator'),
        );
    }
}