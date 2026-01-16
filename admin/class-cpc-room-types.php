<?php
/**
 * Room types management
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin
 */

class CPC_Room_Types {
    
    /**
     * Save room type
     */
    public function save_room_type() {
        // Security check
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'cleaning-price-calculator'));
        }
        
        check_admin_referer('cpc_save_room_type', 'cpc_room_type_nonce');
        
        $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : null;
        
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'price_per_sqm' => floatval($_POST['price_per_sqm']),
            'description' => sanitize_textarea_field($_POST['description']),
            'status' => sanitize_text_field($_POST['status']),
            'sort_order' => intval($_POST['sort_order']),
        );
        
        // Validate
        $errors = array();
        
        if (empty($data['name'])) {
            $errors[] = __('Room name is required.', 'cleaning-price-calculator');
        }
        
        if ($data['price_per_sqm'] <= 0) {
            $errors[] = __('Price per square meter must be greater than 0.', 'cleaning-price-calculator');
        }
        
        if (!empty($errors)) {
            $error_message = implode('<br>', $errors);
            wp_redirect(add_query_arg(array(
                'page' => 'cpc-room-types',
                'error' => urlencode($error_message)
            ), admin_url('admin.php')));
            exit;
        }
        
        // Save
        $result = CPC_Database::save_room_type($data, $room_id);
        
        if ($result !== false) {
            $message = $room_id 
                ? __('Room type updated successfully.', 'cleaning-price-calculator')
                : __('Room type created successfully.', 'cleaning-price-calculator');
            
            wp_redirect(add_query_arg(array(
                'page' => 'cpc-room-types',
                'message' => urlencode($message)
            ), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array(
                'page' => 'cpc-room-types',
                'error' => urlencode(__('Failed to save room type.', 'cleaning-price-calculator'))
            ), admin_url('admin.php')));
        }
        exit;
    }
    
    /**
     * Delete room type
     */
    public function delete_room_type() {
        // Security check
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'cleaning-price-calculator'));
        }
        
        check_admin_referer('cpc_delete_room_type_' . $_GET['id']);
        
        $room_id = intval($_GET['id']);
        
        $result = CPC_Database::delete_room_type($room_id);
        
        if ($result !== false) {
            $message = __('Room type deleted successfully.', 'cleaning-price-calculator');
            wp_redirect(add_query_arg(array(
                'page' => 'cpc-room-types',
                'message' => urlencode($message)
            ), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array(
                'page' => 'cpc-room-types',
                'error' => urlencode(__('Failed to delete room type.', 'cleaning-price-calculator'))
            ), admin_url('admin.php')));
        }
        exit;
    }
}