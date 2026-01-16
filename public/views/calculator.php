<?php
/**
 * Calculator frontend view
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/public/views
 */

if (!defined('WPINC')) {
    die;
}

$currency = get_option('cpc_currency', 'EUR');
$contact_phone = get_option('cpc_contact_phone', '016092262210');
$form_display = get_option('cpc_quote_form_display', 'modal');
?>

<div class="cpc-calculator" id="cpc-calculator">
    <div class="cpc-calculator-header">
        <h2><?php echo esc_html($atts['title']); ?></h2>
        <p class="cpc-calculator-description"><?php esc_html_e('Calculate your cleaning service price by adding rooms and specifying their area.', 'cleaning-price-calculator'); ?></p>
    </div>
    
    <div class="cpc-calculator-body">
        <!-- Rooms Accordion Section -->
        <div class="cpc-rooms-section">
            <div class="cpc-section-header">
                <h3><?php esc_html_e('Your Rooms', 'cleaning-price-calculator'); ?></h3>
                <button type="button" class="cpc-btn cpc-btn-secondary" id="cpc-add-room">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php esc_html_e('Add Room', 'cleaning-price-calculator'); ?>
                </button>
            </div>
            
            <div class="cpc-accordion" id="cpc-rooms-accordion">
                <!-- Rooms will be added here dynamically -->
            </div>
            
            <div class="cpc-no-rooms" id="cpc-no-rooms">
                <p><?php esc_html_e('No rooms added yet. Click "Add Room" to get started.', 'cleaning-price-calculator'); ?></p>
            </div>
        </div>
        
        <!-- Totals Section -->
        <div class="cpc-totals-section">
            <h3><?php esc_html_e('Price Summary', 'cleaning-price-calculator'); ?></h3>
            
            <div class="cpc-totals-list" id="cpc-totals-list">
                <!-- Totals will be displayed here -->
            </div>
            
            <div class="cpc-grand-total">
                <span><?php esc_html_e('Total Price:', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-total-amount">
                    <span id="cpc-grand-total">0.00</span> <?php echo esc_html($currency); ?>
                </span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="cpc-actions-section">
            <?php if (!empty($contact_phone)): ?>
            <a href="tel:<?php echo esc_attr($contact_phone); ?>" class="cpc-btn cpc-btn-outline">
                <span class="dashicons dashicons-phone"></span>
                <?php esc_html_e('Contact Us', 'cleaning-price-calculator'); ?>
            </a>
            <?php endif; ?>
            
            <button type="button" class="cpc-btn cpc-btn-primary" id="cpc-request-quote">
                <span class="dashicons dashicons-email-alt"></span>
                <?php esc_html_e('Request a Quote', 'cleaning-price-calculator'); ?>
            </button>
        </div>
    </div>
    
    <!-- Quote Form (will be displayed based on settings) -->
    <div class="cpc-quote-form-container" id="cpc-quote-form-container" style="display:none;">
        <?php require CPC_PLUGIN_DIR . 'public/views/quote-form.php'; ?>
    </div>
</div>

<!-- Modal for quote form -->
<?php if ($form_display === 'modal'): ?>
<div class="cpc-modal" id="cpc-quote-modal" style="display:none;">
    <div class="cpc-modal-overlay" id="cpc-modal-overlay">
        <div class="cpc-modal-wrapper">
            <div class="cpc-modal-content">
                <button type="button" class="cpc-modal-close" id="cpc-modal-close">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
                <div id="cpc-modal-body">
                    <?php require CPC_PLUGIN_DIR . 'public/views/quote-form.php'; ?>
                </div>
            </div>
        </div>
    </div>
    
</div>
<?php endif; ?>

<!-- Room template (hidden) -->
<script type="text/template" id="cpc-room-template">
    <div class="cpc-accordion-item" data-room-index="{{index}}">
        <div class="cpc-accordion-header">
            <span class="cpc-room-title"><?php esc_html_e('Room', 'cleaning-price-calculator'); ?> #{{index}}</span>
            <span class="cpc-room-subtotal">
                <span class="cpc-subtotal-label"><?php esc_html_e('Subtotal:', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-subtotal-amount">0.00 <?php echo esc_html($currency); ?></span>
            </span>
            <button type="button" class="cpc-accordion-toggle">
                <span class="dashicons dashicons-arrow-down-alt2"></span>
            </button>
        </div>
        <div class="cpc-accordion-content">
            <div class="cpc-form-row">
                <div class="cpc-form-group">
                    <label><?php esc_html_e('Room Type', 'cleaning-price-calculator'); ?></label>
                    <select class="cpc-room-type" name="room_type_{{index}}" required>
                        <option value=""><?php esc_html_e('Select Room Type', 'cleaning-price-calculator'); ?></option>
                    </select>
                </div>
                
                <div class="cpc-form-group">
                    <label><?php echo esc_html__('Area', 'cleaning-price-calculator') . ' (m²)'; ?></label>
                    <input type="number" class="cpc-room-area" name="room_area_{{index}}" step="0.01" min="0.01" placeholder="0.00" required>
                </div>
            </div>
            
            <div class="cpc-room-actions">
                <button type="button" class="cpc-btn cpc-btn-danger cpc-btn-small cpc-remove-room">
                    <span class="dashicons dashicons-trash"></span>
                    <?php esc_html_e('Remove Room', 'cleaning-price-calculator'); ?>
                </button>
            </div>
        </div>
    </div>
</script>