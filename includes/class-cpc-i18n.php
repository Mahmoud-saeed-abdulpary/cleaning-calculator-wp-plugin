<?php
/**
 * Define the internationalization functionality
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/includes
 */

class CPC_i18n {
    
    /**
     * Load the plugin text domain for translation
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'cleaning-price-calculator',
            false,
            dirname(CPC_PLUGIN_BASENAME) . '/languages/'
        );
    }
    
    /**
     * Get available languages
     */
    public static function get_available_languages() {
        return array(
            'de_DE' => __('German', 'cleaning-price-calculator'),
            'en_US' => __('English', 'cleaning-price-calculator'),
            'ar' => __('Arabic', 'cleaning-price-calculator'),
        );
    }
}