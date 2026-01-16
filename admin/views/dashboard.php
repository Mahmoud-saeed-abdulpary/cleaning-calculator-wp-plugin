<?php
/**
 * Dashboard view
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin/views
 */

if (!defined('WPINC')) {
    die;
}

// Get statistics
$total_room_types = count(CPC_Database::get_room_types());
$total_quotes = CPC_Database::get_quotes_count();
$recent_quotes = CPC_Database::get_quotes(5, 0);
$currency = get_option('cpc_currency', 'EUR');
?>

<div class="wrap cpc-admin-wrap">
    <h1><?php esc_html_e('Cleaning Price Calculator - Dashboard', 'cleaning-price-calculator'); ?></h1>
    
    <div class="cpc-dashboard-stats">
        <div class="cpc-stat-box">
            <div class="cpc-stat-icon">
                <span class="dashicons dashicons-building"></span>
            </div>
            <div class="cpc-stat-content">
                <h3><?php echo esc_html($total_room_types); ?></h3>
                <p><?php esc_html_e('Room Types', 'cleaning-price-calculator'); ?></p>
            </div>
        </div>
        
        <div class="cpc-stat-box">
            <div class="cpc-stat-icon">
                <span class="dashicons dashicons-format-quote"></span>
            </div>
            <div class="cpc-stat-content">
                <h3><?php echo esc_html($total_quotes); ?></h3>
                <p><?php esc_html_e('Total Quotes', 'cleaning-price-calculator'); ?></p>
            </div>
        </div>
        
        <div class="cpc-stat-box">
            <div class="cpc-stat-icon">
                <span class="dashicons dashicons-admin-generic"></span>
            </div>
            <div class="cpc-stat-content">
                <h3><?php esc_html_e('Active', 'cleaning-price-calculator'); ?></h3>
                <p><?php esc_html_e('Plugin Status', 'cleaning-price-calculator'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="cpc-dashboard-section">
        <h2><?php esc_html_e('Recent Quote Requests', 'cleaning-price-calculator'); ?></h2>
        
        <?php if (!empty($recent_quotes)): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'cleaning-price-calculator'); ?></th>
                    <th><?php esc_html_e('Date', 'cleaning-price-calculator'); ?></th>
                    <th><?php esc_html_e('Customer', 'cleaning-price-calculator'); ?></th>
                    <th><?php esc_html_e('Email', 'cleaning-price-calculator'); ?></th>
                    <th><?php esc_html_e('Total Price', 'cleaning-price-calculator'); ?></th>
                    <th><?php esc_html_e('Actions', 'cleaning-price-calculator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_quotes as $quote): ?>
                <tr>
                    <td><?php echo esc_html($quote->id); ?></td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quote->created_at))); ?></td>
                    <td><?php echo esc_html($quote->customer_name); ?></td>
                    <td><?php echo esc_html($quote->customer_email); ?></td>
                    <td><?php echo esc_html(number_format($quote->total_price, 2)) . ' ' . esc_html($currency); ?></td>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-quotes&action=view&quote_id=' . $quote->id)); ?>" class="button button-small">
                            <?php esc_html_e('View', 'cleaning-price-calculator'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="cpc-view-all">
            <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-quotes')); ?>" class="button button-primary">
                <?php esc_html_e('View All Quotes', 'cleaning-price-calculator'); ?>
            </a>
        </p>
        <?php else: ?>
        <p><?php esc_html_e('No quotes received yet.', 'cleaning-price-calculator'); ?></p>
        <?php endif; ?>
    </div>
    
    <div class="cpc-dashboard-section">
        <h2><?php esc_html_e('Quick Actions', 'cleaning-price-calculator'); ?></h2>
        <div class="cpc-quick-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-room-types')); ?>" class="button button-large">
                <span class="dashicons dashicons-building"></span>
                <?php esc_html_e('Manage Room Types', 'cleaning-price-calculator'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-settings')); ?>" class="button button-large">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php esc_html_e('Settings', 'cleaning-price-calculator'); ?>
            </a>
        </div>
    </div>
    
    <div class="cpc-dashboard-section">
        <h2><?php esc_html_e('Shortcode Usage', 'cleaning-price-calculator'); ?></h2>
        <p><?php esc_html_e('Use this shortcode to display the calculator on any page or post:', 'cleaning-price-calculator'); ?></p>
        <code class="cpc-shortcode">[cleaning_price_calculator]</code>
        <p class="description"><?php esc_html_e('Compatible with Elementor, Gutenberg, and Classic Editor', 'cleaning-price-calculator'); ?></p>
    </div>
</div>