<?php
/**
 * Admin menu management
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin
 */

class CPC_Admin_Menu {
    
    /**
     * Add plugin admin menu
     */
    public function add_plugin_admin_menu() {
        // Main menu
        add_menu_page(
            __('Cleaning Calculator', 'cleaning-price-calculator'),
            __('Cleaning Calculator', 'cleaning-price-calculator'),
            'manage_options',
            'cleaning-price-calculator',
            array($this, 'display_dashboard'),
            'dashicons-calculator',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'cleaning-price-calculator',
            __('Dashboard', 'cleaning-price-calculator'),
            __('Dashboard', 'cleaning-price-calculator'),
            'manage_options',
            'cleaning-price-calculator',
            array($this, 'display_dashboard')
        );
        
        // Room Types submenu
        add_submenu_page(
            'cleaning-price-calculator',
            __('Room Types', 'cleaning-price-calculator'),
            __('Room Types', 'cleaning-price-calculator'),
            'manage_options',
            'cpc-room-types',
            array($this, 'display_room_types')
        );
        
        // Quotes submenu
        add_submenu_page(
            'cleaning-price-calculator',
            __('Quotes', 'cleaning-price-calculator'),
            __('Quotes', 'cleaning-price-calculator'),
            'manage_options',
            'cpc-quotes',
            array($this, 'display_quotes')
        );
        
        // Settings submenu
        add_submenu_page(
            'cleaning-price-calculator',
            __('Settings', 'cleaning-price-calculator'),
            __('Settings', 'cleaning-price-calculator'),
            'manage_options',
            'cpc-settings',
            array($this, 'display_settings')
        );
    }
    
    /**
     * Display dashboard page
     */
    public function display_dashboard() {
        require_once CPC_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Display room types page
     */
    public function display_room_types() {
        require_once CPC_PLUGIN_DIR . 'admin/views/room-types.php';
    }
    
    /**
     * Display quotes page
     */
    public function display_quotes() {
        // Check if viewing a single quote
        if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['quote_id'])) {
            require_once CPC_PLUGIN_DIR . 'admin/views/quote-detail.php';
        } else {
            require_once CPC_PLUGIN_DIR . 'admin/views/quotes-list.php';
        }
    }
    
    /**
     * Display settings page
     */
    public function display_settings() {
        require_once CPC_PLUGIN_DIR . 'admin/views/settings.php';
    }
}