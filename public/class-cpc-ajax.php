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
        // Verify nonce
        if (!check_ajax_referer('cpc_frontend_nonce', 'nonce', false)) {
            error_log('CPC: Nonce verification failed for get_room_types');
            wp_send_json_error('Security check failed');
            return;
        }
        
        $room_types = CPC_Database::get_room_types('active');
        
        if ($room_types) {
            error_log('CPC: Returning ' . count($room_types) . ' room types');
            wp_send_json_success($room_types);
        } else {
            error_log('CPC: No room types found');
            wp_send_json_error(__('No room types available', 'cleaning-price-calculator'));
        }
    }
    
    /**
     * Submit quote via AJAX
     */
    public function submit_quote() {
        error_log('CPC: Quote submission started');
        error_log('CPC: POST data: ' . print_r($_POST, true));
        
        // Verify nonce
        if (!check_ajax_referer('cpc_frontend_nonce', 'nonce', false)) {
            error_log('CPC: Nonce verification failed for submit_quote');
            wp_send_json_error(array('message' => 'Security check failed'));
            return;
        }
        
        error_log('CPC: Nonce verified successfully');
        
        // Validate customer data
        $customer_data = array(
            'name' => isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
            'email' => isset($_POST['email']) ? sanitize_email($_POST['email']) : '',
            'phone' => isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '',
            'address' => isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '',
            'message' => isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '',
        );
        
        error_log('CPC: Customer data: ' . print_r($customer_data, true));
        
        // Validate rooms data
        $rooms_json = isset($_POST['rooms']) ? stripslashes($_POST['rooms']) : '[]';
        error_log('CPC: Rooms JSON: ' . $rooms_json);
        
        $rooms_data = json_decode($rooms_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('CPC: JSON decode error: ' . json_last_error_msg());
            wp_send_json_error(array('message' => 'Invalid room data format'));
            return;
        }
        
        error_log('CPC: Decoded rooms data: ' . print_r($rooms_data, true));
        
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
        
        if (empty($rooms_data) || !is_array($rooms_data)) {
            $errors[] = __('Please add at least one room', 'cleaning-price-calculator');
        }
        
        if (!empty($errors)) {
            error_log('CPC: Validation errors: ' . print_r($errors, true));
            wp_send_json_error(array('message' => implode(', ', $errors)));
            return;
        }
        
        // Validate and prepare rooms data
        $validated_rooms = array();
        foreach ($rooms_data as $room) {
            if (!isset($room['room_type_id']) || empty($room['room_type_id'])) {
                error_log('CPC: Skipping room with no room_type_id');
                continue;
            }
            
            $room_type = CPC_Database::get_room_type(intval($room['room_type_id']));
            
            if (!$room_type) {
                error_log('CPC: Room type not found: ' . $room['room_type_id']);
                continue;
            }
            
            $area = isset($room['area']) ? floatval($room['area']) : 0;
            
            if ($area <= 0) {
                error_log('CPC: Skipping room with invalid area: ' . $area);
                continue;
            }
            
            $subtotal = $area * floatval($room_type->price_per_sqm);
            
            $validated_rooms[] = array(
                'room_type_id' => $room_type->id,
                'room_type_name' => $room_type->name,
                'area' => $area,
                'price_per_sqm' => $room_type->price_per_sqm,
                'subtotal' => $subtotal,
            );
        }
        
        error_log('CPC: Validated rooms: ' . print_r($validated_rooms, true));
        
        if (empty($validated_rooms)) {
            error_log('CPC: No valid rooms after validation');
            wp_send_json_error(array('message' => __('No valid rooms provided', 'cleaning-price-calculator')));
            return;
        }
        
        // Save quote
        try {
            $quote_id = CPC_Database::save_quote($customer_data, $validated_rooms);
            error_log('CPC: Quote saved with ID: ' . $quote_id);
            
            if ($quote_id) {
                // Send emails
                try {
                    $this->send_quote_emails($quote_id, $customer_data, $validated_rooms);
                    error_log('CPC: Emails sent successfully');
                } catch (Exception $e) {
                    error_log('CPC: Error sending emails: ' . $e->getMessage());
                    // Continue even if emails fail
                }
                
                wp_send_json_success(array(
                    'message' => __('Quote submitted successfully! We will contact you soon.', 'cleaning-price-calculator'),
                    'quote_id' => $quote_id,
                ));
            } else {
                error_log('CPC: Failed to save quote - no ID returned');
                wp_send_json_error(array('message' => __('Failed to save quote. Please try again.', 'cleaning-price-calculator')));
            }
        } catch (Exception $e) {
            error_log('CPC: Exception saving quote: ' . $e->getMessage());
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
        
        error_log('CPC: Preparing to send emails to: ' . $admin_email);
        
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
        
        $admin_result = wp_mail($admin_email, $admin_subject, $admin_message, array('Content-Type: text/html; charset=UTF-8'));
        error_log('CPC: Admin email sent: ' . ($admin_result ? 'success' : 'failed'));
        
        // Send to customer
        $customer_subject = str_replace('{quote_id}', $quote_id, get_option('cpc_customer_email_subject', __('Quote Confirmation - #{quote_id}', 'cleaning-price-calculator')));
        $customer_message = $this->get_email_template('customer', $customer_data, $rooms_html, $quote_id);
        
        $customer_result = wp_mail($customer_data['email'], $customer_subject, $customer_message, array('Content-Type: text/html; charset=UTF-8'));
        error_log('CPC: Customer email sent: ' . ($customer_result ? 'success' : 'failed'));
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