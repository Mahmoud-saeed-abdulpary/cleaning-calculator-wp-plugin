<?php
/**
 * Plugin Name: Cleaning Price Calculator
 * Plugin URI: https://yourcompany.com/cleaning-price-calculator
 * Description: Professional dynamic cleaning price calculator with quote management system for cleaning service companies
 * Version: 1.0.0
 * Author: Your Company
 * Author URI: https://yourcompany.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cleaning-price-calculator
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('CPC_VERSION', '1.0.0');
define('CPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CPC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_cleaning_price_calculator() {
    require_once CPC_PLUGIN_DIR . 'includes/class-cpc-activator.php';
    CPC_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_cleaning_price_calculator() {
    require_once CPC_PLUGIN_DIR . 'includes/class-cpc-deactivator.php';
    CPC_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_cleaning_price_calculator');
register_deactivation_hook(__FILE__, 'deactivate_cleaning_price_calculator');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require CPC_PLUGIN_DIR . 'includes/class-cpc-core.php';

/**
 * Begins execution of the plugin.
 */
function run_cleaning_price_calculator() {
    $plugin = new CPC_Core();
    $plugin->run();
}
run_cleaning_price_calculator();