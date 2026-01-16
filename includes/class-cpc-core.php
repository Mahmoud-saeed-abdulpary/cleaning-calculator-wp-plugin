<?php
/**
 * The core plugin class
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/includes
 */

class CPC_Core {
    
    /**
     * The loader that's responsible for maintaining and registering all hooks
     */
    protected $loader;
    
    /**
     * The unique identifier of this plugin
     */
    protected $plugin_name;
    
    /**
     * The current version of the plugin
     */
    protected $version;
    
    /**
     * Define the core functionality of the plugin
     */
    public function __construct() {
        $this->version = CPC_VERSION;
        $this->plugin_name = 'cleaning-price-calculator';
        
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    /**
     * Load the required dependencies for this plugin
     */
    private function load_dependencies() {
        // Core classes
        require_once CPC_PLUGIN_DIR . 'includes/class-cpc-loader.php';
        require_once CPC_PLUGIN_DIR . 'includes/class-cpc-i18n.php';
        require_once CPC_PLUGIN_DIR . 'includes/class-cpc-database.php';
        
        // Admin classes
        require_once CPC_PLUGIN_DIR . 'admin/class-cpc-admin.php';
        require_once CPC_PLUGIN_DIR . 'admin/class-cpc-admin-menu.php';
        require_once CPC_PLUGIN_DIR . 'admin/class-cpc-room-types.php';
        require_once CPC_PLUGIN_DIR . 'admin/class-cpc-quotes.php';
        require_once CPC_PLUGIN_DIR . 'admin/class-cpc-settings.php';
        
        // Public classes
        require_once CPC_PLUGIN_DIR . 'public/class-cpc-public.php';
        require_once CPC_PLUGIN_DIR . 'public/class-cpc-calculator.php';
        require_once CPC_PLUGIN_DIR . 'public/class-cpc-ajax.php';
        
        $this->loader = new CPC_Loader();
    }
    
    /**
     * Define the locale for this plugin for internationalization
     */
    private function set_locale() {
        $plugin_i18n = new CPC_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     */
    private function define_admin_hooks() {
        $plugin_admin = new CPC_Admin($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Admin menu
        $admin_menu = new CPC_Admin_Menu();
        $this->loader->add_action('admin_menu', $admin_menu, 'add_plugin_admin_menu');
        
        // Room types management
        $room_types = new CPC_Room_Types();
        $this->loader->add_action('admin_post_cpc_save_room_type', $room_types, 'save_room_type');
        $this->loader->add_action('admin_post_cpc_delete_room_type', $room_types, 'delete_room_type');
        
        // Settings
        $settings = new CPC_Settings();
        $this->loader->add_action('admin_init', $settings, 'register_settings');
        $this->loader->add_action('admin_post_cpc_save_settings', $settings, 'save_settings');
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     */
    private function define_public_hooks() {
        $plugin_public = new CPC_Public($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Calculator shortcode
        $calculator = new CPC_Calculator();
        add_shortcode('cleaning_price_calculator', array($calculator, 'render_calculator'));
        
        // AJAX handlers
        $ajax = new CPC_Ajax();
        $this->loader->add_action('wp_ajax_cpc_get_room_types', $ajax, 'get_room_types');
        $this->loader->add_action('wp_ajax_nopriv_cpc_get_room_types', $ajax, 'get_room_types');
        $this->loader->add_action('wp_ajax_cpc_submit_quote', $ajax, 'submit_quote');
        $this->loader->add_action('wp_ajax_nopriv_cpc_submit_quote', $ajax, 'submit_quote');
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress
     */
    public function run() {
        $this->loader->run();
    }
    
    /**
     * The name of the plugin
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }
    
    /**
     * The reference to the class that orchestrates the hooks
     */
    public function get_loader() {
        return $this->loader;
    }
    
    /**
     * Retrieve the version number of the plugin
     */
    public function get_version() {
        return $this->version;
    }
}