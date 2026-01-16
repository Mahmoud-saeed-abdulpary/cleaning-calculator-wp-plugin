<?php
/**
 * Quote detail view
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin/views
 */

if (!defined('WPINC')) {
    die;
}

// Get quote ID
$quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : 0;

if (!$quote_id) {
    wp_die(__('Invalid quote ID', 'cleaning-price-calculator'));
}

// Get quote data
$quote = CPC_Database::get_quote($quote_id);

if (!$quote) {
    wp_die(__('Quote not found', 'cleaning-price-calculator'));
}

$currency = get_option('cpc_currency', 'EUR');
$company_name = get_option('cpc_company_name', get_bloginfo('name'));

// Handle PDF export
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    check_admin_referer('cpc_export_quote_' . $quote_id);
    $quotes_handler = new CPC_Quotes();
    $quotes_handler->export_quote_pdf($quote_id);
    exit;
}
?>

<div class="wrap cpc-admin-wrap">
    <div class="cpc-quote-header">
        <div>
            <h1>
                <?php esc_html_e('Quote Details', 'cleaning-price-calculator'); ?> 
                <span style="color: #6b7280;">#<?php echo esc_html($quote->id); ?></span>
            </h1>
            <p class="description">
                <?php 
                printf(
                    esc_html__('Submitted on %s at %s', 'cleaning-price-calculator'),
                    date_i18n(get_option('date_format'), strtotime($quote->created_at)),
                    date_i18n(get_option('time_format'), strtotime($quote->created_at))
                );
                ?>
            </p>
        </div>
        <div>
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=cpc-quotes&action=view&quote_id=' . $quote->id . '&export=pdf'), 'cpc_export_quote_' . $quote->id)); ?>" 
               class="button button-secondary">
                <span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
                <?php esc_html_e('Export PDF', 'cleaning-price-calculator'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-quotes')); ?>" class="button">
                <span class="dashicons dashicons-arrow-left-alt2" style="margin-top: 3px;"></span>
                <?php esc_html_e('Back to Quotes', 'cleaning-price-calculator'); ?>
            </a>
        </div>
    </div>
    
    <!-- Customer Information -->
    <div class="cpc-card">
        <h2>
            <span class="dashicons dashicons-admin-users" style="color: #2563eb;"></span>
            <?php esc_html_e('Customer Information', 'cleaning-price-calculator'); ?>
        </h2>
        
        <div class="cpc-quote-meta">
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('Full Name', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value"><?php echo esc_html($quote->customer_name); ?></span>
            </div>
            
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('Email Address', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value">
                    <a href="mailto:<?php echo esc_attr($quote->customer_email); ?>">
                        <?php echo esc_html($quote->customer_email); ?>
                    </a>
                </span>
            </div>
            
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('Phone Number', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value">
                    <a href="tel:<?php echo esc_attr($quote->customer_phone); ?>">
                        <?php echo esc_html($quote->customer_phone); ?>
                    </a>
                </span>
            </div>
            
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('Status', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value">
                    <span class="cpc-status cpc-status-<?php echo esc_attr($quote->status); ?>">
                        <?php echo esc_html(ucfirst($quote->status)); ?>
                    </span>
                </span>
            </div>
        </div>
        
        <?php if (!empty($quote->customer_address)): ?>
        <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 6px;">
            <span class="cpc-meta-label"><?php esc_html_e('Address', 'cleaning-price-calculator'); ?></span>
            <p style="margin: 5px 0 0 0; font-size: 15px; color: #1f2937;">
                <?php echo nl2br(esc_html($quote->customer_address)); ?>
            </p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($quote->message)): ?>
        <div style="margin-top: 20px; padding: 15px; background: #eff6ff; border-left: 4px solid #2563eb; border-radius: 6px;">
            <span class="cpc-meta-label" style="color: #1d4ed8;">
                <span class="dashicons dashicons-format-chat" style="font-size: 16px;"></span>
                <?php esc_html_e('Customer Message', 'cleaning-price-calculator'); ?>
            </span>
            <p style="margin: 10px 0 0 0; font-size: 15px; color: #1f2937; line-height: 1.6;">
                <?php echo nl2br(esc_html($quote->message)); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Quote Items -->
    <div class="cpc-card" style="margin-top: 25px;">
        <h2>
            <span class="dashicons dashicons-building" style="color: #2563eb;"></span>
            <?php esc_html_e('Selected Rooms', 'cleaning-price-calculator'); ?>
        </h2>
        
        <?php if (!empty($quote->items)): ?>
        <div class="cpc-quote-items-table">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;"><?php esc_html_e('#', 'cleaning-price-calculator'); ?></th>
                        <th><?php esc_html_e('Room Type', 'cleaning-price-calculator'); ?></th>
                        <th style="width: 150px;"><?php esc_html_e('Area (m²)', 'cleaning-price-calculator'); ?></th>
                        <th style="width: 150px;"><?php esc_html_e('Price per m²', 'cleaning-price-calculator'); ?></th>
                        <th style="width: 150px;"><?php esc_html_e('Subtotal', 'cleaning-price-calculator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $item_number = 1;
                    foreach ($quote->items as $item): 
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($item_number++); ?></strong></td>
                        <td>
                            <strong><?php echo esc_html($item->room_type_name); ?></strong>
                        </td>
                        <td>
                            <?php echo esc_html(number_format($item->area, 2)); ?> m²
                        </td>
                        <td>
                            <?php echo esc_html(number_format($item->price_per_sqm, 2)); ?> <?php echo esc_html($currency); ?>
                        </td>
                        <td>
                            <strong style="color: #2563eb; font-size: 15px;">
                                <?php echo esc_html(number_format($item->subtotal, 2)); ?> <?php echo esc_html($currency); ?>
                            </strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f9fafb;">
                        <td colspan="4" style="text-align: right; font-weight: 700; font-size: 16px; padding: 20px;">
                            <?php esc_html_e('Total Price:', 'cleaning-price-calculator'); ?>
                        </td>
                        <td style="padding: 20px;">
                            <strong style="color: #2563eb; font-size: 20px;">
                                <?php echo esc_html(number_format($quote->total_price, 2)); ?> <?php echo esc_html($currency); ?>
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <p><?php esc_html_e('No items found for this quote.', 'cleaning-price-calculator'); ?></p>
        <?php endif; ?>
    </div>
    
    <!-- Technical Information -->
    <div class="cpc-card" style="margin-top: 25px;">
        <h2>
            <span class="dashicons dashicons-info" style="color: #6b7280;"></span>
            <?php esc_html_e('Technical Information', 'cleaning-price-calculator'); ?>
        </h2>
        
        <div class="cpc-quote-meta">
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('Quote ID', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value">#<?php echo esc_html($quote->id); ?></span>
            </div>
            
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('Submission Date', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value">
                    <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quote->created_at))); ?>
                </span>
            </div>
            
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('IP Address', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value"><?php echo esc_html($quote->ip_address); ?></span>
            </div>
            
            <div class="cpc-meta-item">
                <span class="cpc-meta-label"><?php esc_html_e('Currency', 'cleaning-price-calculator'); ?></span>
                <span class="cpc-meta-value"><?php echo esc_html($quote->currency); ?></span>
            </div>
        </div>
        
        <?php if (!empty($quote->user_agent)): ?>
        <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 6px;">
            <span class="cpc-meta-label"><?php esc_html_e('User Agent', 'cleaning-price-calculator'); ?></span>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #6b7280; word-break: break-all;">
                <?php echo esc_html($quote->user_agent); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions -->
    <div class="cpc-card" style="margin-top: 25px;">
        <h2>
            <span class="dashicons dashicons-admin-generic" style="color: #2563eb;"></span>
            <?php esc_html_e('Quick Actions', 'cleaning-price-calculator'); ?>
        </h2>
        
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="mailto:<?php echo esc_attr($quote->customer_email); ?>?subject=<?php echo esc_attr(sprintf(__('Re: Quote #%d', 'cleaning-price-calculator'), $quote->id)); ?>" 
               class="button button-primary">
                <span class="dashicons dashicons-email" style="margin-top: 3px;"></span>
                <?php esc_html_e('Send Email', 'cleaning-price-calculator'); ?>
            </a>
            
            <a href="tel:<?php echo esc_attr($quote->customer_phone); ?>" class="button">
                <span class="dashicons dashicons-phone" style="margin-top: 3px;"></span>
                <?php esc_html_e('Call Customer', 'cleaning-price-calculator'); ?>
            </a>
            
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=cpc-quotes&action=view&quote_id=' . $quote->id . '&export=pdf'), 'cpc_export_quote_' . $quote->id)); ?>" 
               class="button">
                <span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
                <?php esc_html_e('Download PDF', 'cleaning-price-calculator'); ?>
            </a>
            
            <button type="button" class="button" onclick="window.print();">
                <span class="dashicons dashicons-printer" style="margin-top: 3px;"></span>
                <?php esc_html_e('Print', 'cleaning-price-calculator'); ?>
            </button>
        </div>
    </div>
    
    <!-- Summary Box for Print -->
    <div class="cpc-print-summary" style="display: none;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1><?php echo esc_html($company_name); ?></h1>
            <h2><?php esc_html_e('Quote', 'cleaning-price-calculator'); ?> #<?php echo esc_html($quote->id); ?></h2>
            <p><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($quote->created_at))); ?></p>
        </div>
    </div>
</div>

<style>
@media print {
    .cpc-quote-header,
    .button,
    .dashicons,
    #wpadminbar,
    #adminmenuback,
    #adminmenuwrap,
    .update-nag,
    .notice {
        display: none !important;
    }
    
    .cpc-print-summary {
        display: block !important;
    }
    
    .cpc-card {
        page-break-inside: avoid;
        border: 1px solid #ddd;
        box-shadow: none;
    }
    
    .wrap {
        margin: 0;
    }
}

/* Enhanced responsive styles */
@media screen and (max-width: 782px) {
    .cpc-quote-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .cpc-quote-meta {
        grid-template-columns: 1fr;
    }
    
    .cpc-quote-items-table {
        overflow-x: auto;
    }
}
</style>