<?php
/**
 * Room types management view
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin/views
 */

if (!defined('WPINC')) {
    die;
}

// Get all room types
$room_types = CPC_Database::get_room_types(null);
$currency = get_option('cpc_currency', 'EUR');

// Check if editing
$editing = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id']);
$edit_room = null;

if ($editing) {
    $edit_room = CPC_Database::get_room_type(intval($_GET['id']));
}

// Display messages
if (isset($_GET['message'])) {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(urldecode($_GET['message'])) . '</p></div>';
}
if (isset($_GET['error'])) {
    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html(urldecode($_GET['error'])) . '</p></div>';
}
?>

<div class="wrap cpc-admin-wrap">
    <h1><?php esc_html_e('Room Types Management', 'cleaning-price-calculator'); ?></h1>
    
    <div class="cpc-two-column-layout">
        <!-- Left Column: Add/Edit Form -->
        <div class="cpc-column cpc-form-column">
            <div class="cpc-card">
                <h2><?php echo $editing ? esc_html__('Edit Room Type', 'cleaning-price-calculator') : esc_html__('Add New Room Type', 'cleaning-price-calculator'); ?></h2>
                
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="cpc-form">
                    <?php wp_nonce_field('cpc_save_room_type', 'cpc_room_type_nonce'); ?>
                    <input type="hidden" name="action" value="cpc_save_room_type">
                    <?php if ($editing): ?>
                    <input type="hidden" name="room_id" value="<?php echo esc_attr($edit_room->id); ?>">
                    <?php endif; ?>
                    
                    <div class="cpc-form-group">
                        <label for="name"><?php esc_html_e('Room Type Name', 'cleaning-price-calculator'); ?> *</label>
                        <input type="text" id="name" name="name" class="regular-text" required
                               value="<?php echo $editing ? esc_attr($edit_room->name) : ''; ?>">
                        <p class="description"><?php esc_html_e('e.g., Single Room, Kitchen, Bathroom', 'cleaning-price-calculator'); ?></p>
                    </div>
                    
                    <div class="cpc-form-group">
                        <label for="price_per_sqm"><?php echo esc_html__('Price per m²', 'cleaning-price-calculator') . ' (' . esc_html($currency) . ')'; ?> *</label>
                        <input type="number" id="price_per_sqm" name="price_per_sqm" class="regular-text" step="0.01" min="0" required
                               value="<?php echo $editing ? esc_attr($edit_room->price_per_sqm) : ''; ?>">
                        <p class="description"><?php esc_html_e('Base price per square meter', 'cleaning-price-calculator'); ?></p>
                    </div>
                    
                    <div class="cpc-form-group">
                        <label for="description"><?php esc_html_e('Description', 'cleaning-price-calculator'); ?></label>
                        <textarea id="description" name="description" class="large-text" rows="3"><?php echo $editing ? esc_textarea($edit_room->description) : ''; ?></textarea>
                        <p class="description"><?php esc_html_e('Optional description for this room type', 'cleaning-price-calculator'); ?></p>
                    </div>
                    
                    <div class="cpc-form-group">
                        <label for="sort_order"><?php esc_html_e('Sort Order', 'cleaning-price-calculator'); ?></label>
                        <input type="number" id="sort_order" name="sort_order" class="small-text" min="0"
                               value="<?php echo $editing ? esc_attr($edit_room->sort_order) : '0'; ?>">
                        <p class="description"><?php esc_html_e('Lower numbers appear first', 'cleaning-price-calculator'); ?></p>
                    </div>
                    
                    <div class="cpc-form-group">
                        <label for="status"><?php esc_html_e('Status', 'cleaning-price-calculator'); ?></label>
                        <select id="status" name="status">
                            <option value="active" <?php echo ($editing && $edit_room->status === 'active') ? 'selected' : ''; ?>>
                                <?php esc_html_e('Active', 'cleaning-price-calculator'); ?>
                            </option>
                            <option value="inactive" <?php echo ($editing && $edit_room->status === 'inactive') ? 'selected' : ''; ?>>
                                <?php esc_html_e('Inactive', 'cleaning-price-calculator'); ?>
                            </option>
                        </select>
                    </div>
                    
                    <div class="cpc-form-actions">
                        <?php submit_button($editing ? __('Update Room Type', 'cleaning-price-calculator') : __('Add Room Type', 'cleaning-price-calculator'), 'primary', 'submit', false); ?>
                        <?php if ($editing): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-room-types')); ?>" class="button">
                            <?php esc_html_e('Cancel', 'cleaning-price-calculator'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Column: List -->
        <div class="cpc-column cpc-list-column">
            <div class="cpc-card">
                <h2><?php esc_html_e('Existing Room Types', 'cleaning-price-calculator'); ?></h2>
                
                <?php if (!empty($room_types)): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Name', 'cleaning-price-calculator'); ?></th>
                            <th><?php echo esc_html__('Price/m²', 'cleaning-price-calculator') . ' (' . esc_html($currency) . ')'; ?></th>
                            <th><?php esc_html_e('Status', 'cleaning-price-calculator'); ?></th>
                            <th><?php esc_html_e('Actions', 'cleaning-price-calculator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($room_types as $room): ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($room->name); ?></strong>
                                <?php if ($room->description): ?>
                                <br><span class="description"><?php echo esc_html($room->description); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html(number_format($room->price_per_sqm, 2)); ?></td>
                            <td>
                                <span class="cpc-status cpc-status-<?php echo esc_attr($room->status); ?>">
                                    <?php echo esc_html(ucfirst($room->status)); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=cpc-room-types&action=edit&id=' . $room->id)); ?>" class="button button-small">
                                    <?php esc_html_e('Edit', 'cleaning-price-calculator'); ?>
                                </a>
                                <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=cpc_delete_room_type&id=' . $room->id), 'cpc_delete_room_type_' . $room->id)); ?>" 
                                   class="button button-small button-link-delete" 
                                   onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this room type?', 'cleaning-price-calculator'); ?>');">
                                    <?php esc_html_e('Delete', 'cleaning-price-calculator'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p><?php esc_html_e('No room types found. Add your first room type using the form.', 'cleaning-price-calculator'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>