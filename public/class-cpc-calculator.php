<?php
/**
 * Calculator shortcode and rendering
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/public
 */

class CPC_Calculator {
    
    /**
     * Render the calculator shortcode
     */
    public function render_calculator($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Cleaning Price Calculator', 'cleaning-price-calculator'),
        ), $atts, 'cleaning_price_calculator');
        
        ob_start();
        require CPC_PLUGIN_DIR . 'public/views/calculator.php';
        return ob_get_clean();
    }
}