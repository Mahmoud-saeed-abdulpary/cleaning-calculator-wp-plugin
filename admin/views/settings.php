<?php
/**
 * Settings view
 *
 * @package    Cleaning_Price_Calculator
 * @subpackage Cleaning_Price_Calculator/admin/views
 */

if (!defined('WPINC')) {
    die;
}

// Display messages
if (isset($_GET['message'])) {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(urldecode($_GET['message'])) . '</p></div>';
}
?>

<div class="wrap cpc-admin-wrap">
    <h1><?php esc_html_e('Settings', 'cleaning-price-calculator'); ?></h1>
    
    <h2 class="nav-tab-wrapper cpc-settings-tabs">
        <a href="#cpc-company-settings" class="nav-tab nav-tab-active" data-tab="cpc-company-settings">
            <?php esc_html_e('Company Information', 'cleaning-price-calculator'); ?>
        </a>
        <a href="#cpc-email-settings" class="nav-tab" data-tab="cpc-email-settings">
            <?php esc_html_e('Email Configuration', 'cleaning-price-calculator'); ?>
        </a>
        <a href="#cpc-language-settings" class="nav-tab" data-tab="cpc-language-settings">
            <?php esc_html_e('Language', 'cleaning-price-calculator'); ?>
        </a>
        <a href="#cpc-design-settings" class="nav-tab" data-tab="cpc-design-settings">
            <?php esc_html_e('Design', 'cleaning-price-calculator'); ?>
        </a>
        <a href="#cpc-form-settings" class="nav-tab" data-tab="cpc-form-settings">
            <?php esc_html_e('Form Display', 'cleaning-price-calculator'); ?>
        </a>
    </h2>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('cpc_save_settings', 'cpc_settings_nonce'); ?>
        <input type="hidden" name="action" value="cpc_save_settings">
        
        <!-- Company Information -->
        <div id="cpc-company-settings" class="cpc-settings-section" style="display:none;">
            <h3><?php esc_html_e('Company Information', 'cleaning-price-calculator'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cpc_company_name"><?php esc_html_e('Company Name', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cpc_company_name" name="cpc_company_name" class="regular-text"
                               value="<?php echo esc_attr(get_option('cpc_company_name', '')); ?>">
                        <p class="description"><?php esc_html_e('Your company name for emails and display', 'cleaning-price-calculator'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_contact_phone"><?php esc_html_e('Contact Phone', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cpc_contact_phone" name="cpc_contact_phone" class="regular-text"
                               value="<?php echo esc_attr(get_option('cpc_contact_phone', '')); ?>">
                        <p class="description"><?php esc_html_e('Phone number for the contact button', 'cleaning-price-calculator'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_admin_email"><?php esc_html_e('Admin Email', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="cpc_admin_email" name="cpc_admin_email" class="regular-text"
                               value="<?php echo esc_attr(get_option('cpc_admin_email', get_option('admin_email'))); ?>">
                        <p class="description"><?php esc_html_e('Email address to receive quote notifications', 'cleaning-price-calculator'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_currency"><?php esc_html_e('Currency', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <select id="cpc_currency" name="cpc_currency">
                            <?php
                            $currencies = CPC_Settings::get_currencies();
                            $current_currency = get_option('cpc_currency', 'EUR');
                            foreach ($currencies as $code => $label):
                            ?>
                            <option value="<?php echo esc_attr($code); ?>" <?php selected($current_currency, $code); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Email Configuration -->
        <div id="cpc-email-settings" class="cpc-settings-section" style="display:none;">
            <h3><?php esc_html_e('SMTP Configuration', 'cleaning-price-calculator'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cpc_smtp_enabled"><?php esc_html_e('Enable SMTP', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <select id="cpc_smtp_enabled" name="cpc_smtp_enabled">
                            <option value="no" <?php selected(get_option('cpc_smtp_enabled', 'no'), 'no'); ?>>
                                <?php esc_html_e('No', 'cleaning-price-calculator'); ?>
                            </option>
                            <option value="yes" <?php selected(get_option('cpc_smtp_enabled', 'no'), 'yes'); ?>>
                                <?php esc_html_e('Yes', 'cleaning-price-calculator'); ?>
                            </option>
                        </select>
                        <p class="description"><?php esc_html_e('Enable SMTP for reliable email delivery', 'cleaning-price-calculator'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_smtp_host"><?php esc_html_e('SMTP Host', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cpc_smtp_host" name="cpc_smtp_host" class="regular-text"
                               value="<?php echo esc_attr(get_option('cpc_smtp_host', '')); ?>"
                               placeholder="smtp.example.com">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_smtp_port"><?php esc_html_e('SMTP Port', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="cpc_smtp_port" name="cpc_smtp_port" class="small-text"
                               value="<?php echo esc_attr(get_option('cpc_smtp_port', '587')); ?>">
                        <p class="description"><?php esc_html_e('Usually 587 for TLS or 465 for SSL', 'cleaning-price-calculator'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_smtp_encryption"><?php esc_html_e('Encryption', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <select id="cpc_smtp_encryption" name="cpc_smtp_encryption">
                            <option value="tls" <?php selected(get_option('cpc_smtp_encryption', 'tls'), 'tls'); ?>>TLS</option>
                            <option value="ssl" <?php selected(get_option('cpc_smtp_encryption', 'tls'), 'ssl'); ?>>SSL</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_smtp_username"><?php esc_html_e('SMTP Username', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cpc_smtp_username" name="cpc_smtp_username" class="regular-text"
                               value="<?php echo esc_attr(get_option('cpc_smtp_username', '')); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_smtp_password"><?php esc_html_e('SMTP Password', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="password" id="cpc_smtp_password" name="cpc_smtp_password" class="regular-text"
                               value="<?php echo esc_attr(get_option('cpc_smtp_password', '')); ?>">
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Language Settings -->
        <div id="cpc-language-settings" class="cpc-settings-section" style="display:none;">
            <h3><?php esc_html_e('Language Settings', 'cleaning-price-calculator'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cpc_default_language"><?php esc_html_e('Default Language', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <select id="cpc_default_language" name="cpc_default_language">
                            <?php
                            $languages = CPC_i18n::get_available_languages();
                            $current_lang = get_option('cpc_default_language', 'de_DE');
                            foreach ($languages as $code => $label):
                            ?>
                            <option value="<?php echo esc_attr($code); ?>" <?php selected($current_lang, $code); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Design Settings -->
        <div id="cpc-design-settings" class="cpc-settings-section" style="display:none;">
            <h3><?php esc_html_e('Design Customization', 'cleaning-price-calculator'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cpc_primary_color"><?php esc_html_e('Primary Color', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cpc_primary_color" name="cpc_primary_color" class="cpc-color-picker"
                               value="<?php echo esc_attr(get_option('cpc_primary_color', '#2563eb')); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_button_color"><?php esc_html_e('Button Color', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cpc_button_color" name="cpc_button_color" class="cpc-color-picker"
                               value="<?php echo esc_attr(get_option('cpc_button_color', '#10b981')); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="cpc_accent_color"><?php esc_html_e('Accent Color', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cpc_accent_color" name="cpc_accent_color" class="cpc-color-picker"
                               value="<?php echo esc_attr(get_option('cpc_accent_color', '#f59e0b')); ?>">
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Form Display Settings -->
        <div id="cpc-form-settings" class="cpc-settings-section" style="display:none;">
            <h3><?php esc_html_e('Quote Form Display Mode', 'cleaning-price-calculator'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cpc_quote_form_display"><?php esc_html_e('Display Mode', 'cleaning-price-calculator'); ?></label>
                    </th>
                    <td>
                        <select id="cpc_quote_form_display" name="cpc_quote_form_display">
                            <option value="modal" <?php selected(get_option('cpc_quote_form_display', 'modal'), 'modal'); ?>>
                                <?php esc_html_e('Popup Modal', 'cleaning-price-calculator'); ?>
                            </option>
                            <option value="inline" <?php selected(get_option('cpc_quote_form_display', 'modal'), 'inline'); ?>>
                                <?php esc_html_e('Inline Below Totals', 'cleaning-price-calculator'); ?>
                            </option>
                            <option value="replace" <?php selected(get_option('cpc_quote_form_display', 'modal'), 'replace'); ?>>
                                <?php esc_html_e('Replace Calculator View', 'cleaning-price-calculator'); ?>
                            </option>
                        </select>
                        <p class="description"><?php esc_html_e('How the quote form should be displayed when user clicks "Request a Quote"', 'cleaning-price-calculator'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php submit_button(__('Save All Settings', 'cleaning-price-calculator'), 'primary', 'submit'); ?>
    </form>
</div>