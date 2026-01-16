<?php
/**
 * Database operations for the plugin
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/includes
 */

class CPC_Database {
    
    /**
     * Create custom tables for the plugin
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Room Types Table
        $table_room_types = $wpdb->prefix . 'cpc_room_types';
        $sql_room_types = "CREATE TABLE $table_room_types (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            price_per_sqm decimal(10,2) NOT NULL DEFAULT 0.00,
            description text,
            status varchar(20) DEFAULT 'active',
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY sort_order (sort_order)
        ) $charset_collate;";
        
        // Quotes Table
        $table_quotes = $wpdb->prefix . 'cpc_quotes';
        $sql_quotes = "CREATE TABLE $table_quotes (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) NOT NULL,
            customer_phone varchar(50) NOT NULL,
            customer_address text,
            message text,
            total_price decimal(10,2) NOT NULL DEFAULT 0.00,
            currency varchar(10) DEFAULT 'EUR',
            status varchar(20) DEFAULT 'pending',
            ip_address varchar(100),
            user_agent text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY customer_email (customer_email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Quote Items Table
        $table_quote_items = $wpdb->prefix . 'cpc_quote_items';
        $sql_quote_items = "CREATE TABLE $table_quote_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            quote_id bigint(20) unsigned NOT NULL,
            room_type_id bigint(20) unsigned NOT NULL,
            room_type_name varchar(255) NOT NULL,
            area decimal(10,2) NOT NULL,
            price_per_sqm decimal(10,2) NOT NULL,
            subtotal decimal(10,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY quote_id (quote_id),
            KEY room_type_id (room_type_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_room_types);
        dbDelta($sql_quotes);
        dbDelta($sql_quote_items);
    }
    
    /**
     * Insert default room types
     */
    public static function insert_default_room_types() {
        global $wpdb;
        $table = $wpdb->prefix . 'cpc_room_types';
        
        $default_rooms = array(
            array('name' => 'Single Room', 'price_per_sqm' => 5.00, 'description' => 'Standard single room cleaning', 'sort_order' => 1),
            array('name' => 'Double Room', 'price_per_sqm' => 6.00, 'description' => 'Standard double room cleaning', 'sort_order' => 2),
            array('name' => 'Suite', 'price_per_sqm' => 8.00, 'description' => 'Luxury suite cleaning', 'sort_order' => 3),
            array('name' => 'Kitchen', 'price_per_sqm' => 10.00, 'description' => 'Kitchen deep cleaning', 'sort_order' => 4),
            array('name' => 'Bathroom', 'price_per_sqm' => 12.00, 'description' => 'Bathroom sanitization', 'sort_order' => 5),
            array('name' => 'Living Room', 'price_per_sqm' => 5.50, 'description' => 'Living area cleaning', 'sort_order' => 6),
            array('name' => 'Office Space', 'price_per_sqm' => 7.00, 'description' => 'Office cleaning service', 'sort_order' => 7),
        );
        
        foreach ($default_rooms as $room) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE name = %s",
                $room['name']
            ));
            
            if (!$exists) {
                $wpdb->insert($table, $room);
            }
        }
    }
    
    /**
     * Get all active room types
     */
    public static function get_room_types($status = 'active') {
        global $wpdb;
        $table = $wpdb->prefix . 'cpc_room_types';
        
        if ($status) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE status = %s ORDER BY sort_order ASC, name ASC",
                $status
            ));
        }
        
        return $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC, name ASC");
    }
    
    /**
     * Get single room type
     */
    public static function get_room_type($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cpc_room_types';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Insert or update room type
     */
    public static function save_room_type($data, $id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'cpc_room_types';
        
        $prepared_data = array(
            'name' => sanitize_text_field($data['name']),
            'price_per_sqm' => floatval($data['price_per_sqm']),
            'description' => sanitize_textarea_field($data['description'] ?? ''),
            'status' => sanitize_text_field($data['status'] ?? 'active'),
            'sort_order' => intval($data['sort_order'] ?? 0),
        );
        
        if ($id) {
            return $wpdb->update($table, $prepared_data, array('id' => $id));
        }
        
        return $wpdb->insert($table, $prepared_data);
    }
    
    /**
     * Delete room type
     */
    public static function delete_room_type($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cpc_room_types';
        
        return $wpdb->delete($table, array('id' => $id));
    }
    
    /**
     * Save quote
     */
    public static function save_quote($customer_data, $rooms_data) {
        global $wpdb;
        
        $quotes_table = $wpdb->prefix . 'cpc_quotes';
        $items_table = $wpdb->prefix . 'cpc_quote_items';
        
        // Calculate total
        $total = 0;
        foreach ($rooms_data as $room) {
            $total += floatval($room['subtotal']);
        }
        
        // Get currency from settings
        $currency = get_option('cpc_currency', 'EUR');
        
        // Insert quote
        $quote_data = array(
            'customer_name' => sanitize_text_field($customer_data['name']),
            'customer_email' => sanitize_email($customer_data['email']),
            'customer_phone' => sanitize_text_field($customer_data['phone']),
            'customer_address' => sanitize_textarea_field($customer_data['address'] ?? ''),
            'message' => sanitize_textarea_field($customer_data['message'] ?? ''),
            'total_price' => $total,
            'currency' => $currency,
            'ip_address' => self::get_client_ip(),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
        );
        
        $wpdb->insert($quotes_table, $quote_data);
        $quote_id = $wpdb->insert_id;
        
        // Insert quote items
        foreach ($rooms_data as $room) {
            $item_data = array(
                'quote_id' => $quote_id,
                'room_type_id' => intval($room['room_type_id']),
                'room_type_name' => sanitize_text_field($room['room_type_name']),
                'area' => floatval($room['area']),
                'price_per_sqm' => floatval($room['price_per_sqm']),
                'subtotal' => floatval($room['subtotal']),
            );
            
            $wpdb->insert($items_table, $item_data);
        }
        
        return $quote_id;
    }
    
    /**
     * Get quote with items
     */
    public static function get_quote($id) {
        global $wpdb;
        
        $quotes_table = $wpdb->prefix . 'cpc_quotes';
        $items_table = $wpdb->prefix . 'cpc_quote_items';
        
        $quote = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $quotes_table WHERE id = %d",
            $id
        ));
        
        if ($quote) {
            $quote->items = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $items_table WHERE quote_id = %d ORDER BY id ASC",
                $id
            ));
        }
        
        return $quote;
    }
    
    /**
     * Get all quotes with pagination
     */
    public static function get_quotes($limit = 20, $offset = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'cpc_quotes';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));
    }
    
    /**
     * Get total quotes count
     */
    public static function get_quotes_count() {
        global $wpdb;
        $table = $wpdb->prefix . 'cpc_quotes';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table");
    }
    
    /**
     * Get client IP address
     */
    private static function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        
        return sanitize_text_field($ip);
    }
}