<?php
/**
 * Fired during plugin activation
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/includes
 */

class CPC_Activator {
    
    /**
     * Plugin activation logic
     */
    public static function activate() {
        // Create database tables
        require_once CPC_PLUGIN_DIR . 'includes/class-cpc-database.php';
        CPC_Database::create_tables();
        CPC_Database::insert_default_room_types();
        
        // Set default options
        self::set_default_options();
        
        // Create plugin version option
        add_option('cpc_version', CPC_VERSION);
        
        // Set activation timestamp
        add_option('cpc_activated_time', current_time('mysql'));
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $defaults = array(
            // Company Information
            'cpc_company_name' => get_bloginfo('name'),
            'cpc_contact_phone' => '',
            'cpc_admin_email' => get_option('admin_email'),
            'cpc_currency' => 'EUR',
            
            // Email Settings
            'cpc_smtp_enabled' => 'no',
            'cpc_smtp_host' => '',
            'cpc_smtp_port' => '587',
            'cpc_smtp_username' => '',
            'cpc_smtp_password' => '',
            'cpc_smtp_encryption' => 'tls',
            'cpc_email_from_name' => get_bloginfo('name'),
            'cpc_email_from_address' => get_option('admin_email'),
            
            // Language Settings
            'cpc_default_language' => 'de_DE',
            
            // Design Settings
            'cpc_primary_color' => '#2563eb',
            'cpc_button_color' => '#10b981',
            'cpc_accent_color' => '#f59e0b',
            
            // Quote Form Display
            'cpc_quote_form_display' => 'modal', // modal, inline, replace
            
            // Email Templates
            'cpc_admin_email_subject' => __('New Quote Request - #{quote_id}', 'cleaning-price-calculator'),
            'cpc_customer_email_subject' => __('Quote Confirmation - #{quote_id}', 'cleaning-price-calculator'),
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
}