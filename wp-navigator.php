<?php

/**
 * Plugin Name: WP Navigator
 * Plugin URI: https://www.wp-navigator.com
 * Description: WP Navigator is a powerful plugin that allows you to create an unlimited number of navigation menus for your website.
 * Version: 1.0.0
 * Author: Leo Knudsen
 * Author URI: https://www.wp-navigator.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-navigator
 * Domain Path: /languages
 * Requires at least: 6.4.3
 * Tested up to: 6.4.3
 */

 if ( ! defined( 'ABSPATH' ) ) {
    exit;
 }

 if ( ! defined( 'WP_NAVIGATOR_PATH' ) ) {
    define( 'WP_NAVIGATOR_PATH', plugin_dir_path( __FILE__ ) );
 }

if ( ! defined( 'WP_NAVIGATOR_URL' ) ) {
    define( 'WP_NAVIGATOR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WP_NAVIGATOR_VERSION' ) ) {
    define( 'WP_NAVIGATOR_VERSION', '1.0.0' );
}

if ( ! defined( 'WP_NAVIGATOR_BASENAME' ) ) {
    define( 'WP_NAVIGATOR_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'WP_NAVIGATOR_DIR' ) ) {
    define( 'WP_NAVIGATOR_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WP_NAVIGATOR_FILE' ) ) {
    define( 'WP_NAVIGATOR_FILE', __FILE__ );
}

if ( ! defined('WP_NAVIGATOR_ADMIN_BASE_URL') ) {
    define( 'WP_NAVIGATOR_ADMIN_BASE_URL', admin_url() );
}

if ( ! class_exists('WP_Navigator') ) {
    require_once WP_NAVIGATOR_PATH . 'includes/class-wp-navigator.php';

    function wp_navigator() {
        return new WP_Navigator();
    }
}

add_action( 'plugins_loaded', 'wp_navigator' );