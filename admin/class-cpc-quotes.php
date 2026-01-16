<?php
/**
 * Quotes management
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin
 */

class CPC_Quotes {
    
    /**
     * Export quote as PDF
     */
    public function export_quote_pdf($quote_id) {
        // Get quote data
        $quote = CPC_Database::get_quote($quote_id);
        
        if (!$quote) {
            wp_die(__('Quote not found.', 'cleaning-price-calculator'));
        }
        
        // Basic HTML to PDF conversion
        // For production, consider using a library like TCPDF or Dompdf
        $this->generate_simple_pdf($quote);
    }
    
    /**
     * Generate simple PDF (placeholder for real PDF library)
     */
    private function generate_simple_pdf($quote) {
        $company_name = get_option('cpc_company_name', get_bloginfo('name'));
        $currency = get_option('cpc_currency', 'EUR');
        
        // Set headers for PDF download
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="quote-' . $quote->id . '.html"');
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo esc_html__('Quote', 'cleaning-price-calculator') . ' #' . $quote->id; ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .info-section { margin: 20px 0; }
                .info-label { font-weight: bold; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #f5f5f5; }
                .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><?php echo esc_html($company_name); ?></h1>
                <h2><?php echo esc_html__('Quote', 'cleaning-price-calculator') . ' #' . $quote->id; ?></h2>
                <p><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($quote->created_at))); ?></p>
            </div>
            
            <div class="info-section">
                <p><span class="info-label"><?php esc_html_e('Customer Name:', 'cleaning-price-calculator'); ?></span> <?php echo esc_html($quote->customer_name); ?></p>
                <p><span class="info-label"><?php esc_html_e('Email:', 'cleaning-price-calculator'); ?></span> <?php echo esc_html($quote->customer_email); ?></p>
                <p><span class="info-label"><?php esc_html_e('Phone:', 'cleaning-price-calculator'); ?></span> <?php echo esc_html($quote->customer_phone); ?></p>
                <?php if (!empty($quote->customer_address)): ?>
                <p><span class="info-label"><?php esc_html_e('Address:', 'cleaning-price-calculator'); ?></span> <?php echo esc_html($quote->customer_address); ?></p>
                <?php endif; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th><?php esc_html_e('Room Type', 'cleaning-price-calculator'); ?></th>
                        <th><?php esc_html_e('Area (m²)', 'cleaning-price-calculator'); ?></th>
                        <th><?php esc_html_e('Price/m²', 'cleaning-price-calculator'); ?></th>
                        <th><?php esc_html_e('Subtotal', 'cleaning-price-calculator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quote->items as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item->room_type_name); ?></td>
                        <td><?php echo esc_html(number_format($item->area, 2)); ?></td>
                        <td><?php echo esc_html(number_format($item->price_per_sqm, 2)) . ' ' . esc_html($currency); ?></td>
                        <td><?php echo esc_html(number_format($item->subtotal, 2)) . ' ' . esc_html($currency); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total">
                <?php esc_html_e('Total:', 'cleaning-price-calculator'); ?> 
                <?php echo esc_html(number_format($quote->total_price, 2)) . ' ' . esc_html($currency); ?>
            </div>
            
            <?php if (!empty($quote->message)): ?>
            <div class="info-section">
                <p><span class="info-label"><?php esc_html_e('Message:', 'cleaning-price-calculator'); ?></span></p>
                <p><?php echo nl2br(esc_html($quote->message)); ?></p>
            </div>
            <?php endif; ?>
        </body>
        </html>
        <?php
        exit;
    }
}