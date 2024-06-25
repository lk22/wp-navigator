<?php 
    /**
     * Plugin Name: WP Navigator
     * Plugin URI: http://www.wp-navigator.com
     * Description: WP Navigator is a powerful plugin that allows you to create an unlimited number of navigation menus for your website.
     * Version: 1.0.0
     * Author: Leo Knudsen
     * Author URI: http://www.wp-navigator.com
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    /**
     * Main WP Navigator plugin class
     */
    class WP_Navigator {

        public $menu;
        public $submenu;

        /**
         * Plugin Constructor 
         */
        public function __construct() {
            add_action('init', [$this, 'init']);
        }

        /**
         * Initializing plugin actions
         *
         * @return void
         */
        public function init(): void {
            add_filter('admin_menu', [$this, 'admin_menu']);
            add_action('wp_ajax_wp_navigator_search', [$this, 'wp_navigator_search']);

            add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
            add_action('admin_enqueue_styles', [$this, 'admin_enqueue_styles']);
            add_action('admin_footer', [$this, 'register_navigator']);
            add_action('admin_init', [$this, 'store_menu_structure']);
        }

        /**
         * Enqueuing admin related scripts
         *
         * @return void
         */
        public function admin_enqueue_scripts(): void {
            wp_register_script('wp-navigator-admin-typeahead', WP_NAVIGATOR_URL . 'assets/js/dependencies/typeahead.js');
            wp_enqueue_script('wp-navigator-admin-typeahead');
            wp_register_script('wp-navigator-admin', WP_NAVIGATOR_URL . 'assets/js/admin.js', ['jquery'], WP_NAVIGATOR_VERSION, true);
            wp_enqueue_script('wp-navigator-admin');

            $menu = get_transient('admin_menu');
            $submenu = get_transient('admin_submenu');

            $fullSubMenu = [];

            foreach ($menu as $item) {
                if ( ! empty($submenu[$item[2]]) ) {
                    $fullSubMenu = array_merge($fullSubMenu, $submenu[$item[2]]);
                }
            }

            $fullMenuTree = array_merge($menu, $fullSubMenu);
            $postsArray = [];
            $pagesArray = [];
            $categoriesArray = [];
            $tagsArray = [];

            $posts = get_posts([
                'post_type' => 'post',
                'posts_per_page' => -1,
            ]);

            foreach ($posts as $post) {
                $postsArray[] = [
                    "Post: " . $post->post_title,
                    "",
                    admin_url('post.php?post=' . $post->ID . '&action=edit'),
                ];
            }

            $pages = get_posts([
                'post_type' => 'page',
                'posts_per_page' => -1,
            ]);

            foreach ($pages as $page) {
                $pagesArray[] = [
                    "Page: " . $page->post_title,
                    "",
                    admin_url('post.php?post=' . $page->ID . '&action=edit'),
                ];
            }

            $categories = get_terms([
                'taxonomy' => 'category',
                'hide_empty' => false,
            ]);

            foreach ( $categories as $category ) {
                $categoriesArray[] = [
                    "Category: " . $category->name,
                    "",
                    admin_url('edit-tags.php?taxonomy=category&tag_ID=' . $category->term_id . '&post_type=post'),
                ];
            }

            $tags = get_terms([
                'taxonomy' => 'post_tag',
                'hide_empty' => false,
            ]);

            foreach ( $tags as $tag ) {
                $tagsArray[] = [
                    "Tag: " . $tag->name,
                    "",
                    admin_url('edit-tags.php?taxonomy=post_tag&tag_ID=' . $tag->term_id . '&post_type=post'),
                ];
            }

            $fullMenuTree = array_merge(
                $fullMenuTree, 
                $postsArray, 
                $pagesArray, 
                $categoriesArray, 
                $tagsArray
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
         * Register administration menu for the plugin
         *
         * @return void
         */
        public function admin_menu(): void {
            add_menu_page('WP Navigator', 'WP Navigator', 'manage_options', 'wp-navigator', [$this, 'admin_page']);
        }

        /**
         * rendering template for the admin page
         *
         * @return void
         */
        public function admin_page() : void {
            
            echo "<pre>";
            print_r([$this->menu, $this->submenu]);
            echo "</pre>";
            echo '<h1>WP Navigator</h1>';
            echo '<p>WP Navigator is a powerful plugin that allows you to create an unlimited number of navigation menus for your website.</p>';
            echo '<p>Version: 1.0.0</p>';
            echo '<p>Author: Leo Knudsen</p>';
            echo '<h2>Quick usage guide</h2>';
            echo "<p>use following keystrokes to open your navigator</p>";
            echo "<p>ctrl + shift + n</p>";
            echo "<p>command + f</p>";
            echo "<p>This will open you navigation menu for quickly move to your action</p>";
        }

        /**
         * Registering navigator component to show on every admin page
         *
         * @return void
         */
        public function register_navigator(): void {
            ?>
                <style>
                    #wp-navigator-modal {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: none;
                        z-index: 9999;
                    }

                    #wp-navigator-modal .wp-navigator-modal-dialog {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        background: #fff;
                        padding: 20px;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                        width: 800px;
                        clear: both;
                    }

                    .wp-navigator-modal-dialog h1 {
                        font-size: 18px;
                        margin: 0 0 10px;
                    }

                    .wp-navigator-modal-dialog input {
                        width: 100%;
                        padding: 10px;
                        margin: 10px 0;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                    }

                    .wp-navigator-modal-dialog .dialog-body {
                        height: auto;
                    }

                    .twitter-typeahead {
                        width: 100%;
                    }

                    .tt-menu {
                        height: auto;
                        position: relative !important;
                    }

                    .tt-suggestions {
                        width: 100%;
                        background: #fff;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                        position: relative;
                        top: 100%;
                        left: 0;
                        z-index: 9999;
                        margin-bottom: 10px !important;
                        padding: 10px !important;
                    }

                    .tt-suggestion a {
                        color: #1a1a1a;
                        text-decoration: none;
                        font-size: 18px;
                    }
                </style>
            <?php
            echo '<div id="wp-navigator-modal">';
                echo '<div class="wp-navigator-modal-dialog">';
                    echo '<div class="dialog-header">';
                        echo '<h1>Wordpress Admin Navigator</h1>';
                    echo '</div>';
                    echo '<div class="dialog-body">';
                        echo '<input type="text" id="wp-navigator-search" class="typeahead" placeholder="Search for your action">';
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