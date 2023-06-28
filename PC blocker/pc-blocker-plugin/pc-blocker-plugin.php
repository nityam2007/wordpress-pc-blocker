<?php
/*
Plugin Name: PC Blocker Plugin
Description: Blocks PC users, except admins and logged-in users, and provides mobile site settings.
Version: 1.0
Author: NityamAS / Nityam2007
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Register the plugin activation hook
register_activation_hook(__FILE__, 'pc_blocker_plugin_activate');

// Plugin activation function
function pc_blocker_plugin_activate() {
    // Add default options on plugin activation
    add_option('mobile_site_settings_enable', false);
    add_option('mobile_site_settings_url', '');
    add_option('mobile_site_settings_enable_pc_block', false);
    add_option('mobile_site_settings_home_page_url', '');
}

// Register the plugin deactivation hook
register_deactivation_hook(__FILE__, 'pc_blocker_plugin_deactivate');

// Plugin deactivation function
function pc_blocker_plugin_deactivate() {
    // Delete options on plugin deactivation
    delete_option('mobile_site_settings_enable');
    delete_option('mobile_site_settings_url');
    delete_option('mobile_site_settings_enable_pc_block');
    delete_option('mobile_site_settings_home_page_url');
}

// Add a new top-level menu
function add_mobile_site_menu() {
    add_menu_page(
        'Mobile Site Settings', // Page title
        'Mobile Site', // Menu title
        'manage_options', // Capability required
        'mobile-site-settings', // Menu slug
        'mobile_site_settings_page', // Callback function
        'dashicons-smartphone', // Icon URL
        30 // Menu position
    );
}
add_action('admin_menu', 'add_mobile_site_menu');

// Callback function for the mobile site settings page
function mobile_site_settings_page() {
    // Check if the form is submitted and update the settings
    if (isset($_POST['mobile_site_settings_submit'])) {
        // Sanitize and save the settings
        $enable_mobile_site = isset($_POST['enable_mobile_site']) ? true : false;
        $mobile_site_url = isset($_POST['mobile_site_url']) ? esc_url($_POST['mobile_site_url']) : '';
        $enable_pc_block = isset($_POST['enable_pc_block']) ? true : false;
        $home_page_url = isset($_POST['home_page_url']) ? esc_url($_POST['home_page_url']) : '';

        update_option('mobile_site_settings_enable', $enable_mobile_site);
        update_option('mobile_site_settings_url', $mobile_site_url);
        update_option('mobile_site_settings_enable_pc_block', $enable_pc_block);
        update_option('mobile_site_settings_home_page_url', $home_page_url);

        echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
    }

    // Retrieve the current settings
    $enable_mobile_site = get_option('mobile_site_settings_enable', false);
    $mobile_site_url = get_option('mobile_site_settings_url', '');
    $enable_pc_block = get_option('mobile_site_settings_enable_pc_block', false);
    $home_page_url = get_option('mobile_site_settings_home_page_url', '');

    // Output the settings form
    ?>
    <div class="wrap">
        <h1>Mobile Site Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Mobile Site</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_mobile_site" <?php checked($enable_mobile_site, true); ?>>
                            Enable the mobile site
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mobile Site URL</th>
                    <td>
                        <input type="text" name="mobile_site_url" value="<?php echo esc_attr($mobile_site_url); ?>" class="regular-text">
                        <p class="description">Enter the URL of the mobile site.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enable PC User Block</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_pc_block" <?php checked($enable_pc_block, true); ?>>
                            Enable PC user block
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Home Page URL</th>
                    <td>
                        <input type="text" name="home_page_url" value="<?php echo esc_attr($home_page_url); ?>" class="regular-text">
                        <p class="description">Enter the URL of the home page to redirect blocked PC users.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="mobile_site_settings_submit" class="button button-primary" value="Save Settings">
            </p>
        </form>
    </div>
    <?php
}

// Block PC users who are not admins or logged-in users
function block_pc_users() {
    if (get_option('mobile_site_settings_enable_pc_block', false) && !current_user_can('manage_options') && !wp_is_mobile()) {
        wp_redirect(get_option('mobile_site_settings_home_page_url', home_url()));
        exit;
    }
}
add_action('template_redirect', 'block_pc_users');
