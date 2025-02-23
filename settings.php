<?php

if ( ! defined('ABSPATH') ) {
    exit;
}

require 'includes/class-settings-utility.php';

// add settings page
if ( ! function_exists('wp_bc_setup_menu') ) {
   function wp_bc_setup_menu() {
      add_options_page('WP Navigator  Settings', 'WP Navigator', 'manage_options', 'wp_navigator_settings', 'wp_navigator_settings_page');
   }
}

add_action( 'admin_menu', 'wp_bc_setup_menu' );

if ( ! function_exists('wp_navigator_settings_page') ) {
   function wp_navigator_settings_page() {
       ?>
       <div class="wrap">
           <h1>WP Navigator options</h1>
           <?php var_dump(WP_Navigator_Settings_Utility::getOption('wp_navigator_enable'))?>
           <form method="post" action="options.php">
               <?php
               settings_fields('wp_navigator_settings');
               do_settings_sections('wp_navigator_settings');
               do_settings_sections('wp_navigator_hotkey_settings');
               submit_button();
               ?>
           </form>
       </div>
       <?php
   }
}

add_action('admin_init', 'wp_navigator_register_settincs');

if ( ! function_exists('wp_navigator_register_settincs') ) {
   function wp_navigator_register_settincs() {
       register_setting('wp_navigator_settings', 'wp_navigator_enable');
       register_setting('wp_navigator_settings', 'wp_bc_client_secret');

       add_settings_section('wp_navigator_settings', 'Navigator control section', 'wp_navigator_control_section', 'wp_navigator_settings');
       add_settings_section('wp_navigator_hotkey_settings', 'Hotkey settings', 'wp_navigator_hotkey_settings', 'wp_navigator_settings');

       add_settings_field('wp_navigator_enable', 'Enable WP Navigator', 'wp_navigator_enable', 'wp_navigator_settings', 'wp_navigator_settings');
       add_settings_field('wp_navigator_enable_in_frontend', 'Enable in frontend context', 'wp_navigator_enable_in_frontend_context', 'wp_navigator_settings', 'wp_navigator_settings');

       add_settings_field('wp_navigator_enable_hotkeys', 'Enable hotkeys', 'wp_navigator_enable_hotkeys', 'wp_navigator_settings', 'wp_navigator_hotkey_settings');
       add_settings_field('wp_navigator_hotkey_selection', 'Select hotkey', 'wp_navigator_hotkey_selection', 'wp_navigator_settings', 'wp_navigator_hotkey_settings');
   }
}

if ( ! function_exists('wp_navigator_control_section') ) {
    /**
     * Control section for WP Navigator
     *
     * @return void
     */
	function wp_navigator_control_section() {
		echo "<p>Control use of WP Navigator</p>";
	}
}

if ( ! function_exists('wp_navigator_enable') ) {
    /**
     * Rendering the enable setting for WP Navigator
     *
     * @return void
     */
   function wp_navigator_enable() {
       $enabled = get_option('wp_navigator_enable');
       $is_enabled = $enabled ? 'checked' : ''; 
       echo "<input type='checkbox' name='wp_navigator_enable' " . $is_enabled . " />";
   }
}

if ( ! function_exists('wp_navigator_enable_in_frontend_context') ) {
    /**
     * rendering the enable in frontend context setting for WP Navigator
     *
     * @return void
     */
   function wp_navigator_enable_in_frontend_context() {
       $enabled_in_frontend = get_option('wp_navigator_enable_in_frontend');
       $is_enabled = $enabled_in_frontend ? 'checked' : '';
       echo "<input type='checkbox' name='wp_navigator_enable_in_frontend');' " . $is_enabled . " />";
   }
}

if ( ! function_exists('wp_navigator_hotkey_settings') ) {
    /**
     * Rendering settings group for hotkey settings
     *
     * @return void
     */
    function wp_navigator_hotkey_settings() {
        echo "<p>Hotkey options</p>";
    }
}

if ( ! function_exists('wp_navigator_enable_hotkeys') ) {
    /**
     * rendering check field for Enabling hotkeys for WP Navigator
     *
     * @return void
     */
    function wp_navigator_enable_hotkeys() {
        $enabled_hotkeys = get_option('wp_navigator_enable_hotkeys');
        $is_enabled = $enabled_hotkeys ? 'checked' : '';
        echo "<input type='checkbox' name='wp_navigator_enable_hotkeys' " . $is_enabled . " />";
    }
}

if ( ! function_exists('wp_navigator_hotkey_selection') ) {
    /**
     * rendering text field for selecting hotkey for WP Navigator if hotkeys are enabled
     *
     * @return void
     */
    function wp_navigator_hotkey_selection() {
        $hotkey = get_option('wp_navigator_hotkey_selection');
        $is_hotkeys_enabled = get_option('wp_navigator_enable_hotkeys');
        if ( $is_hotkeys_enabled ) {
            echo "<input type='text' name='wp_navigator_hotkey_selection' value='$hotkey' />";
        } else {
            echo "<input type='text' name='wp_navigator_hotkey_selection' value='' disabled />";
        }
    }
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_settings_link');

if ( ! function_exists('add_settings_link') ) {
	function add_settings_link($links) {
		$settings_link = '<a href="admin.php?page=wp_navigator_settings">Settings</a>';
		array_push($links, $settings_link);
		return $links;
	}
}
