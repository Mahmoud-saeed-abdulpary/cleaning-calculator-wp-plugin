<?php
/**
 * Fired during plugin deactivation
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/includes
 */

class CPC_Deactivator {
    
    /**
     * Plugin deactivation logic
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear scheduled cron jobs if any
        wp_clear_scheduled_hook('cpc_daily_cleanup');
    }
}