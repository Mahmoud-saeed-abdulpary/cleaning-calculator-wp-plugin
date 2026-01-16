<?php
/**
 * AJAX request handlers
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/public
 */

class CPC_Ajax {
    
    /**
     * Get room types via AJAX
     */
    public function get_room_types() {
        check_ajax_referer('cpc_frontend_nonce', 'nonce');
        
        $room_types = CPC_Database::get_room_types('active');
        
        if ($room_types) {
            wp_send_json_success($room_types);
        } else {
            wp_send_json_error(__('No room types available', 'cleaning-price-calculator'));
        }
    }
    
    /**
     * Submit quote via AJAX
     */
    public function submit_quote() {
        check_ajax_referer('cpc_frontend_nonce', 'nonce');
        
        // Validate customer data
        $customer_data = array(
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'address' => sanitize_textarea_field($_POST['address'] ?? ''),
            'message' => sanitize_textarea_field($_POST['message'] ?? ''),
        );
        
        // Validate rooms data
        $rooms_data = isset($_POST['rooms']) ? json_decode(stripslashes($_POST['rooms']), true) : array();
        
        // Validation
        $errors = array();
        
        if (empty($customer_data['name'])) {
            $errors[] = __('Name is required', 'cleaning-price-calculator');
        }
        
        if (empty($customer_data['email']) || !is_email($customer_data['email'])) {
            $errors[] = __('Valid email is required', 'cleaning-price-calculator');
        }
        
        if (empty($customer_data['phone'])) {
            $errors[] = __('Phone number is required', 'cleaning-price-calculator');
        }
        
        if (empty($rooms_data)) {
            $errors[] = __('Please add at least one room', 'cleaning-price-calculator');
        }
        
        if (!empty($errors)) {
            wp_send_json_error(array('message' => implode(', ', $errors)));
            return;
        }
        
        // Validate and prepare rooms data
        $validated_rooms = array();
        foreach ($rooms_data as $room) {
            $room_type = CPC_Database::get_room_type(intval($room['room_type_id']));
            
            if (!$room_type) {
                continue;
            }
            
            $area = floatval($room['area']);
            $subtotal = $area * floatval($room_type->price_per_sqm);
            
            $validated_rooms[] = array(
                'room_type_id' => $room_type->id,
                'room_type_name' => $room_type->name,
                'area' => $area,
                'price_per_sqm' => $room_type->price_per_sqm,
                'subtotal' => $subtotal,
            );
        }
        
        if (empty($validated_rooms)) {
            wp_send_json_error(array('message' => __('No valid rooms provided', 'cleaning-price-calculator')));
            return;
        }
        
        // Save quote
        $quote_id = CPC_Database::save_quote($customer_data, $validated_rooms);
        
        if ($quote_id) {
            // Send emails
            $this->send_quote_emails($quote_id, $customer_data, $validated_rooms);
            
            wp_send_json_success(array(
                'message' => __('Quote submitted successfully! We will contact you soon.', 'cleaning-price-calculator'),
                'quote_id' => $quote_id,
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to save quote. Please try again.', 'cleaning-price-calculator')));
        }
    }
    
    /**
     * Send quote emails
     */
    private function send_quote_emails($quote_id, $customer_data, $rooms_data) {
        $company_name = get_option('cpc_company_name', get_bloginfo('name'));
        $admin_email = get_option('cpc_admin_email', get_option('admin_email'));
        $currency = get_option('cpc_currency', 'EUR');
        
        // Calculate total
        $total = 0;
        foreach ($rooms_data as $room) {
            $total += $room['subtotal'];
        }
        
        // Build rooms table HTML
        $rooms_html = '<table style="width:100%; border-collapse: collapse; margin: 20px 0;">';
        $rooms_html .= '<thead><tr style="background:#f5f5f5;">';
        $rooms_html .= '<th style="padding:10px; border:1px solid #ddd;">' . __('Room Type', 'cleaning-price-calculator') . '</th>';
        $rooms_html .= '<th style="padding:10px; border:1px solid #ddd;">' . __('Area (m²)', 'cleaning-price-calculator') . '</th>';
        $rooms_html .= '<th style="padding:10px; border:1px solid #ddd;">' . __('Price/m²', 'cleaning-price-calculator') . '</th>';
        $rooms_html .= '<th style="padding:10px; border:1px solid #ddd;">' . __('Subtotal', 'cleaning-price-calculator') . '</th>';
        $rooms_html .= '</tr></thead><tbody>';
        
        foreach ($rooms_data as $room) {
            $rooms_html .= '<tr>';
            $rooms_html .= '<td style="padding:10px; border:1px solid #ddd;">' . esc_html($room['room_type_name']) . '</td>';
            $rooms_html .= '<td style="padding:10px; border:1px solid #ddd;">' . number_format($room['area'], 2) . '</td>';
            $rooms_html .= '<td style="padding:10px; border:1px solid #ddd;">' . number_format($room['price_per_sqm'], 2) . ' ' . $currency . '</td>';
            $rooms_html .= '<td style="padding:10px; border:1px solid #ddd;">' . number_format($room['subtotal'], 2) . ' ' . $currency . '</td>';
            $rooms_html .= '</tr>';
        }
        
        $rooms_html .= '</tbody></table>';
        $rooms_html .= '<p style="font-size:18px; font-weight:bold; text-align:right;">' . __('Total:', 'cleaning-price-calculator') . ' ' . number_format($total, 2) . ' ' . $currency . '</p>';
        
        // Configure SMTP if enabled
        $this->configure_smtp();
        
        // Send to admin
        $admin_subject = str_replace('{quote_id}', $quote_id, get_option('cpc_admin_email_subject', __('New Quote Request - #{quote_id}', 'cleaning-price-calculator')));
        $admin_message = $this->get_email_template('admin', $customer_data, $rooms_html, $quote_id);
        
        wp_mail($admin_email, $admin_subject, $admin_message, array('Content-Type: text/html; charset=UTF-8'));
        
        // Send to customer
        $customer_subject = str_replace('{quote_id}', $quote_id, get_option('cpc_customer_email_subject', __('Quote Confirmation - #{quote_id}', 'cleaning-price-calculator')));
        $customer_message = $this->get_email_template('customer', $customer_data, $rooms_html, $quote_id);
        
        wp_mail($customer_data['email'], $customer_subject, $customer_message, array('Content-Type: text/html; charset=UTF-8'));
    }
    
    /**
     * Configure SMTP
     */
    private function configure_smtp() {
        if (get_option('cpc_smtp_enabled') === 'yes') {
            add_action('phpmailer_init', array($this, 'setup_phpmailer'));
        }
    }
    
    /**
     * Setup PHPMailer
     */
    public function setup_phpmailer($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = get_option('cpc_smtp_host');
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = get_option('cpc_smtp_port', 587);
        $phpmailer->Username = get_option('cpc_smtp_username');
        $phpmailer->Password = get_option('cpc_smtp_password');
        $phpmailer->SMTPSecure = get_option('cpc_smtp_encryption', 'tls');
        $phpmailer->From = get_option('cpc_email_from_address', get_option('admin_email'));
        $phpmailer->FromName = get_option('cpc_email_from_name', get_bloginfo('name'));
    }
    
    /**
     * Get email template
     */
    private function get_email_template($type, $customer_data, $rooms_html, $quote_id) {
        $company_name = get_option('cpc_company_name', get_bloginfo('name'));
        
        $message = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . esc_html($company_name) . '</h1>
                    <p>' . ($type === 'admin' ? __('New Quote Request', 'cleaning-price-calculator') : __('Quote Confirmation', 'cleaning-price-calculator')) . '</p>
                </div>
                <div class="content">';
        
        if ($type === 'admin') {
            $message .= '
                <h2>' . __('Quote Details', 'cleaning-price-calculator') . '</h2>
                <p><strong>' . __('Quote ID:', 'cleaning-price-calculator') . '</strong> #' . $quote_id . '</p>
                <p><strong>' . __('Customer Name:', 'cleaning-price-calculator') . '</strong> ' . esc_html($customer_data['name']) . '</p>
                <p><strong>' . __('Email:', 'cleaning-price-calculator') . '</strong> ' . esc_html($customer_data['email']) . '</p>
                <p><strong>' . __('Phone:', 'cleaning-price-calculator') . '</strong> ' . esc_html($customer_data['phone']) . '</p>';
            
            if (!empty($customer_data['address'])) {
                $message .= '<p><strong>' . __('Address:', 'cleaning-price-calculator') . '</strong> ' . esc_html($customer_data['address']) . '</p>';
            }
            
            if (!empty($customer_data['message'])) {
                $message .= '<p><strong>' . __('Message:', 'cleaning-price-calculator') . '</strong></p><p>' . nl2br(esc_html($customer_data['message'])) . '</p>';
            }
        } else {
            $message .= '
                <p>' . sprintf(__('Dear %s,', 'cleaning-price-calculator'), esc_html($customer_data['name'])) . '</p>
                <p>' . __('Thank you for your quote request. We have received your information and will contact you shortly.', 'cleaning-price-calculator') . '</p>
                <p><strong>' . __('Quote ID:', 'cleaning-price-calculator') . '</strong> #' . $quote_id . '</p>';
        }
        
        $message .= '
                <h3>' . __('Selected Rooms', 'cleaning-price-calculator') . '</h3>
                ' . $rooms_html . '
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ' . esc_html($company_name) . '. ' . __('All rights reserved.', 'cleaning-price-calculator') . '</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $message;
    }
}