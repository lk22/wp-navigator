<?php 
    /**
     * Plugin Name: WP Navigator
     * Plugin URI: http://www.wp-navigator.com
     * Description: WP Navigator is a powerful plugin that allows you to navigate through your admin panel with ease.
     * Version: 1.0.0
     * Author: Leo Knudsen
     * Author URI: http://www.wp-navigator.com
     */

    /**
     * TODO: render navigations indifferent of the context (frontend or backend)
     * TODO: add support for custom post types
     * TODO: add support for custom hotkeys in settings page
     * TODO: add support for suggestions in terms of most used actions
     * TODO: add support for customizing the navigator in settings page
     * TODO: add support for customizing the navigator with custom CSS in settings page
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    require_once 'class-navigation-suggestions.php';
    require_once 'class-settings-page.php';

    /**
     * Main WP Navigator plugin class
     * 
     * @todo add dynamic support for navigation for all custom post types
     */
    class WP_Navigator {

        /**
         * Plugin Constructor 
         */
        public function __construct() {
            add_action('init', [$this, 'init']);
            load_textdomain(
                'wp-navigator', 
                WP_NAVIGATOR_PATH . 'languages/wp-navigator-' . get_locale() . '.mo'
            );
        }

        /**
         * Initializing plugin actions
         *
         * @return void
         */
        public function init(): void {
            add_filter('admin_menu', [new WP_Navigator_Settings, 'add_settings_menu']);
            // register actions
            add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
            add_action('wp_enqueue_scripts', [$this, 'admin_enqueue_scripts']); // register the navigator when on the frontend
            add_action('admin_enqueue_styles', [$this, 'admin_enqueue_styles']);
            add_action('admin_footer', [$this, 'register_navigator']);
            // register the navigator when on the frontend
            add_action('wp_footer', [$this, 'register_navigator']);
            add_action('admin_init', [$this, 'store_menu_structure']);
        }

        /**
         * Enqueuing admin related scripts
         *
         * @return void
         */
        public function admin_enqueue_scripts(): void {
            wp_register_script('wp-navigator-admin-typeahead', WP_NAVIGATOR_URL . 'assets/js/dependencies/typeahead.js', ['jquery']);
            wp_enqueue_script('wp-navigator-admin-typeahead');
            wp_register_script('wp-navigator-admin', WP_NAVIGATOR_URL . 'assets/js/admin.js', ['jquery'], WP_NAVIGATOR_VERSION, true);
            wp_enqueue_script('wp-navigator-admin');

            $menu = get_transient('admin_menu');
            $submenu = get_transient('admin_submenu');

            // get all post types
            $allPostTypes = get_post_types(['public' => true]);
            $allTaxonomy = get_taxonomies(['public' => true]);

            $fullSubMenu = [];
            $postTypesArray = [];
            $taxonomiesArray = [];

            if ( $menu ) {
                foreach ($menu as $item) {
                    if ( ! empty($submenu[$item[2]]) ) {
                        $fullSubMenu = array_merge($fullSubMenu, $submenu[$item[2]]);
                    }
                }
            }

            if ( $submenu ) {
                foreach ($submenu as $item) {
                    $fullSubMenu = array_merge($fullSubMenu, $item);
                }
            }

            // prevent errors if the menu or submenu is not countable
            if ( (is_countable($menu) || is_object($menu)) && (is_countable($fullSubMenu) || is_object($fullSubMenu)) ) {
                $fullMenuTree = array_merge($menu, $fullSubMenu);
            } else {
                $fullMenuTree = [];
            }

            foreach ( $allPostTypes as $postType ) {
                $types = get_posts([
                    "post_type" => $postType,
                    "posts_per_page" => -1
                ]);

                foreach ( $types as $type ) {
                    $postUrl = "post.php?post=" . $type->ID . "&action=edit";
        
                    $postTypesArray[] = [
                        ucfirst($postType) .": " . $type->post_title,
                        "",
                        admin_url($postUrl)
                    ];
                }
            }

            foreach ( $allTaxonomy as $taxonomy ) {
                $types = get_terms([
                    "taxonomy" => $taxonomy,
                    "hide_empty" => false
                ]);

                foreach ( $types as $type ) {
                    $taxUrl = "edit-tags.php?taxonomy=post_tag&tag_ID=" . $type->term_id . "&post_type=post";

                    $taxonomiesArray[] = [
                        "Taxonomy: " . $type->name,
                        "",
                        admin_url($taxUrl)
                    ];
                }
            }

            $fullMenuTree = array_merge(
                $fullMenuTree,
                $postTypesArray,
                $taxonomiesArray
            );

            wp_localize_script('wp-navigator-admin', 'wp_navigator_plugin', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp-navigator-nonce'),
                'base_url' => WP_NAVIGATOR_ADMIN_BASE_URL,
                "menu" => get_transient('admin_menu'),
                "submenu" => get_transient('admin_submenu'),
                "full_menu" => $fullMenuTree
            ]);
        }

        /**
         * Enqueuing admin related styles
         *
         * @return void
         */
        public function admin_enqueue_styles(): void {
            wp_enqueue_style('wp-navigator-admin', WP_NAVIGATOR_URL . 'assets/css/admin.css', WP_NAVIGATOR_VERSION);
            wp_register_style('wp-navigator-admin');
        }

        /**
         * Registering navigator component to show on every admin page
         *
         * @return void
         */
        public function register_navigator() {

            // only reigster the navigator if the user is logged in and admin
            if ( ! is_user_logged_in() || ! current_user_can('manage_options') ) {
                return;
            }

            if ( is_user_logged_in() && ! current_user_can('manage_options') ) {
                return;
            }

            $backend_context = is_admin();

            $suggestions = new WP_Navigation_Suggestions();
            
            wp_enqueue_style('wp-navigation-admin', WP_NAVIGATOR_URL . 'assets/css/admin.css', WP_NAVIGATOR_VERSION);
            echo '<div id="wp-navigator-wrapper">
                <div id="wp-navigator-button">
                    <img src="' . admin_url('images/wordpress-logo.svg') . '" alt="Wordpress Logo">
                    <span>Open WP Navigator</span>
                </div>
            </div>';
            echo '<div id="wp-navigator-modal" style="display: none;">';
                echo '<div class="wp-navigator-modal-dialog">';
                    echo '<div class="dialog-header">';
                        // echo the wordpress logo here
                        echo "<div class='logo'>";
                            echo "<img src='" . admin_url('images/wordpress-logo.svg') . "' alt='Wordpress Logo'>";
                        echo "</div>"; 
                        echo '<div class="quick-suggestions">';
                            echo '<h2>WordPress Navigator</h2>';
                            foreach ( $suggestions->get_suggestions(get_user_locale()) as $suggestion ) {
                                if ( ! $backend_context ) {
                                    $suggestion['url'] = admin_url($suggestion['url']); 
                                }
 
                                echo '<p style="display: none;" data-suggestion="' . $suggestion['path'] . '" style="font-weight: bold; cursor: pointer; margin: 0;"><a style="color: #000000;" href="' . $suggestion['url'] . '">' . $suggestion['label'] . '</a></p>';
                            }
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="dialog-body">';
                        echo '<input type="text" id="wp-navigator-search" placeholder="Search for your action" data-link="" autofocus>';
                        echo '<div id="wp-navigator-results"></div>';
                    echo '</div>';
                echo '</div>';
            echo "</div>";
        }

        /**
         * Store menu structure in transient objects
         *
         * @return void
         */
        public function store_menu_structure() {
            global $menu, $submenu;
            set_transient('admin_menu', $menu, 12 * HOUR_IN_SECONDS);
            set_transient('admin_submenu', $submenu, 12 * HOUR_IN_SECONDS);
        }
    }
?>