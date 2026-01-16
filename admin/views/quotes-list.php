<?php
/**
 * Quotes list view
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin/views
 */

if (!defined('WPINC')) {
    die;
}

// Pagination settings
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Get quotes
$quotes = CPC_Database::get_quotes($per_page, $offset);
$total_quotes = CPC_Database::get_quotes_count();
$total_pages = ceil($total_quotes / $per_page);
$currency = get_option('cpc_currency', 'EUR');

// Display messages
if (isset($_GET['message'])) {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(urldecode($_GET['message'])) . '</p></div>';
}
if (isset($_GET['error'])) {
    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html(urldecode($_GET['error'])) . '</p></div>';
}
?>

<div class="wrap cpc-admin-wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Quote Requests', 'cleaning-price-calculator'); ?></h1>
    
    <?php if ($total_quotes > 0): ?>
    <p class="description">
        <?php
        printf(
            esc_html(_n('You have %s quote request.', 'You have %s quote requests.', $total_quotes, 'cleaning-price-calculator')),
            '<strong>' . number_format($total_quotes) . '</strong>'
        );
        ?>
    </p>
    <?php endif; ?>
    
    <hr class="wp-header-end">
    
    <?php if (!empty($quotes)): ?>
    
    <div class="cpc-card" style="margin-top: 20px;">
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary" style="width: 60px;">
                        <?php esc_html_e('ID', 'cleaning-price-calculator'); ?>
                    </th>
                    <th scope="col" class="manage-column" style="width: 180px;">
                        <?php esc_html_e('Date', 'cleaning-price-calculator'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php esc_html_e('Customer Name', 'cleaning-price-calculator'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php esc_html_e('Email', 'cleaning-price-calculator'); ?>
                    </th>
                    <th scope="col" class="manage-column" style="width: 150px;">
                        <?php esc_html_e('Phone', 'cleaning-price-calculator'); ?>
                    </th>
                    <th scope="col" class="manage-column" style="width: 120px;">
                        <?php esc_html_e('Total Price', 'cleaning-price-calculator'); ?>
                    </th>
                    <th scope="col" class="manage-column" style="width: 100px;">
                        <?php esc_html_e('Status', 'cleaning-price-calculator'); ?>
                    </th>
                    <th scope="col" class="manage-column" style="width: 150px;">
                        <?php esc_html_e('Actions', 'cleaning-price-calculator'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $quote): ?>
                <tr>
                    <td class="column-primary" data-colname="<?php esc_attr_e('ID', 'cleaning-price-calculator'); ?>">
                        <strong>#<?php echo esc_html($quote->id); ?></strong>
                        <button type="button" class="toggle-row">
                            <span class="screen-reader-text"><?php esc_html_e('Show more details', 'cleaning-price-calculator'); ?></span>
                        </button>
                    </td>
                    <td data-colname="<?php esc_attr_e('Date', 'cleaning-price-calculator'); ?>">
                        <?php 
                        echo esc_html(date_i18n(
                            get_option('date_format') . ' ' . get_option('time_format'), 
                            strtotime($quote->created_at)
                        )); 
                        ?>
                        <br>
                        <span class="description">
                            <?php 
                            printf(
                                esc_html__('%s ago', 'cleaning-price-calculator'),
                                human_time_diff(strtotime($quote->created_at), current_time('timestamp'))
                            );
                            ?>
                        </span>
                    </td>
                    <td data-colname="<?php esc_attr_e('Customer Name', 'cleaning-price-calculator'); ?>">
                        <strong><?php echo esc_html($quote->customer_name); ?></strong>
                    </td>
                    <td data-colname="<?php esc_attr_e('Email', 'cleaning-price-calculator'); ?>">
                        <a href="mailto:<?php echo esc_attr($quote->customer_email); ?>">
                            <?php echo esc_html($quote->customer_email); ?>
                        </a>
                    </td>
                    <td data-colname="<?php esc_attr_e('Phone', 'cleaning-price-calculator'); ?>">
                        <a href="tel:<?php echo esc_attr($quote->customer_phone); ?>">
                            <?php echo esc_html($quote->customer_phone); ?>
                        </a>
                    </td>
                    <td data-colname="<?php esc_attr_e('Total Price', 'cleaning-price-calculator'); ?>">
                        <strong style="color: #2563eb; font-size: 15px;">
                            <?php echo esc_html(number_format($quote->total_price, 2)); ?> 
                            <?php echo esc_html($currency); ?>
                        </strong>
                    </td>
                    <td data-colname="<?php esc_attr_e('Status', 'cleaning-price-calculator'); ?>">
                        <span class="cpc-status cpc-status-<?php echo esc_attr($quote->status); ?>">
                            <?php echo esc_html(ucfirst($quote->status)); ?>
                        </span>
                    </td>
                    <td data-colname="<?php esc_attr_e('Actions', 'cleaning-price-calculator'); ?>">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-quotes&action=view&quote_id=' . $quote->id)); ?>" 
                           class="button button-primary button-small">
                            <span class="dashicons dashicons-visibility" style="margin-top: 3px;"></span>
                            <?php esc_html_e('View Details', 'cleaning-price-calculator'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col"><?php esc_html_e('ID', 'cleaning-price-calculator'); ?></th>
                    <th scope="col"><?php esc_html_e('Date', 'cleaning-price-calculator'); ?></th>
                    <th scope="col"><?php esc_html_e('Customer Name', 'cleaning-price-calculator'); ?></th>
                    <th scope="col"><?php esc_html_e('Email', 'cleaning-price-calculator'); ?></th>
                    <th scope="col"><?php esc_html_e('Phone', 'cleaning-price-calculator'); ?></th>
                    <th scope="col"><?php esc_html_e('Total Price', 'cleaning-price-calculator'); ?></th>
                    <th scope="col"><?php esc_html_e('Status', 'cleaning-price-calculator'); ?></th>
                    <th scope="col"><?php esc_html_e('Actions', 'cleaning-price-calculator'); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <?php if ($total_pages > 1): ?>
    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <span class="displaying-num">
                <?php
                printf(
                    esc_html(_n('%s item', '%s items', $total_quotes, 'cleaning-price-calculator')),
                    number_format($total_quotes)
                );
                ?>
            </span>
            <?php
            $page_links = paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total' => $total_pages,
                'current' => $current_page,
                'type' => 'list',
            ));
            
            if ($page_links) {
                echo '<span class="pagination-links">' . $page_links . '</span>';
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    
    <div class="cpc-card" style="margin-top: 20px; text-align: center; padding: 60px 20px;">
        <span class="dashicons dashicons-format-quote" style="font-size: 80px; color: #e5e7eb; width: 80px; height: 80px;"></span>
        <h2 style="color: #6b7280; margin: 20px 0 10px 0;">
            <?php esc_html_e('No Quote Requests Yet', 'cleaning-price-calculator'); ?>
        </h2>
        <p style="color: #9ca3af; margin: 0 0 30px 0;">
            <?php esc_html_e('Quote requests submitted through your calculator will appear here.', 'cleaning-price-calculator'); ?>
        </p>
        <a href="<?php echo esc_url(admin_url('admin.php?page=cleaning-price-calculator')); ?>" class="button button-primary">
            <?php esc_html_e('View Dashboard', 'cleaning-price-calculator'); ?>
        </a>
    </div>
    
    <?php endif; ?>
    
    <div class="cpc-dashboard-section" style="margin-top: 30px;">
        <h3><?php esc_html_e('Export Options', 'cleaning-price-calculator'); ?></h3>
        <p class="description">
            <?php esc_html_e('Export functionality for bulk quotes will be available in a future update.', 'cleaning-price-calculator'); ?>
        </p>
    </div>
</div>

<style>
/* Responsive table styles */
@media screen and (max-width: 782px) {
    .wp-list-table td.column-primary {
        padding-left: 50px;
        position: relative;
    }
    
    .wp-list-table .toggle-row {
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
    }
}
</style>