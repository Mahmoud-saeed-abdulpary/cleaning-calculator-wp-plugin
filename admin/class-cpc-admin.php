<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin
 */

class CPC_Admin {
    
    /**
     * The ID of this plugin
     */
    private $plugin_name;
    
    /**
     * The version of this plugin
     */
    private $version;
    
    /**
     * Initialize the class and set its properties
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area
     */
    public function enqueue_styles() {
        if ($this->is_plugin_page()) {
            wp_enqueue_style(
                $this->plugin_name,
                CPC_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                $this->version,
                'all'
            );
            
            // Color picker
            wp_enqueue_style('wp-color-picker');
        }
    }
    
    /**
     * Register the JavaScript for the admin area
     */
    public function enqueue_scripts() {
        if ($this->is_plugin_page()) {
            wp_enqueue_script(
                $this->plugin_name,
                CPC_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery', 'wp-color-picker'),
                $this->version,
                false
            );
            
            wp_localize_script($this->plugin_name, 'cpcAdmin', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cpc_admin_nonce'),
                'strings' => array(
                    'confirmDelete' => __('Are you sure you want to delete this item?', 'cleaning-price-calculator'),
                    'saved' => __('Saved successfully', 'cleaning-price-calculator'),
                    'error' => __('An error occurred', 'cleaning-price-calculator'),
                ),
            ));
        }
    }
    
    /**
     * Check if current page is a plugin admin page
     */
    private function is_plugin_page() {
        $screen = get_current_screen();
        return $screen && strpos($screen->id, 'cleaning-price-calculator') !== false;
    }
}