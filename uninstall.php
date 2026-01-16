<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package    Cleaning_Price_Calculator
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Drop custom tables
global $wpdb;

$table_room_types = $wpdb->prefix . 'cpc_room_types';
$table_quotes = $wpdb->prefix . 'cpc_quotes';
$table_quote_items = $wpdb->prefix . 'cpc_quote_items';

$wpdb->query("DROP TABLE IF EXISTS $table_quote_items");
$wpdb->query("DROP TABLE IF EXISTS $table_quotes");
$wpdb->query("DROP TABLE IF EXISTS $table_room_types");

// Delete all plugin options
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'cpc_%'");

// Clear any cached data
wp_cache_flush();