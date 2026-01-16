<?php
/**
 * Quote request form
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/public/views
 */

if (!defined('WPINC')) {
    die;
}
?>

<div class="cpc-quote-form">
    <h3><?php esc_html_e('Request a Quote', 'cleaning-price-calculator'); ?></h3>
    <p class="cpc-form-description"><?php esc_html_e('Fill in your details and we will contact you with a detailed quote.', 'cleaning-price-calculator'); ?></p>
    
    <form id="cpc-quote-form-element" method="post" novalidate>
        <div class="cpc-form-group">
            <label for="cpc-customer-name">
                <?php esc_html_e('Full Name', 'cleaning-price-calculator'); ?> *
            </label>
            <input type="text" id="cpc-customer-name" name="name" class="cpc-form-control" required placeholder="<?php esc_attr_e('John Doe', 'cleaning-price-calculator'); ?>">
        </div>
        
        <div class="cpc-form-row">
            <div class="cpc-form-group">
                <label for="cpc-customer-email">
                    <?php esc_html_e('Email Address', 'cleaning-price-calculator'); ?> *
                </label>
                <input type="email" id="cpc-customer-email" name="email" class="cpc-form-control" required placeholder="<?php esc_attr_e('john@example.com', 'cleaning-price-calculator'); ?>">
            </div>
            
            <div class="cpc-form-group">
                <label for="cpc-customer-phone">
                    <?php esc_html_e('Phone Number', 'cleaning-price-calculator'); ?> *
                </label>
                <input type="tel" id="cpc-customer-phone" name="phone" class="cpc-form-control" required placeholder="<?php esc_attr_e('+1234567890', 'cleaning-price-calculator'); ?>">
            </div>
        </div>
        
        <div class="cpc-form-group">
            <label for="cpc-customer-address">
                <?php esc_html_e('Address', 'cleaning-price-calculator'); ?>
            </label>
            <input type="text" id="cpc-customer-address" name="address" class="cpc-form-control" placeholder="<?php esc_attr_e('123 Main St, City, Country', 'cleaning-price-calculator'); ?>">
        </div>
        
        <div class="cpc-form-group">
            <label for="cpc-customer-message">
                <?php esc_html_e('Message', 'cleaning-price-calculator'); ?>
            </label>
            <textarea id="cpc-customer-message" name="message" class="cpc-form-control" rows="4" placeholder="<?php esc_attr_e('Any additional information...', 'cleaning-price-calculator'); ?>"></textarea>
        </div>
        
        <div class="cpc-form-notice" id="cpc-form-notice" style="display:none;"></div>
        
        <div class="cpc-form-actions">
            <button type="submit" class="cpc-btn cpc-btn-primary cpc-btn-block" id="cpc-submit-quote">
                <span class="cpc-btn-text"><?php esc_html_e('Submit Quote Request', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-btn-loader" style="display:none;">
                    <span class="dashicons dashicons-update-alt"></span>
                    <?php esc_html_e('Submitting...', 'cleaning-price-calculator'); ?>
                </span>
            </button>
        </div>
    </form>
</div>