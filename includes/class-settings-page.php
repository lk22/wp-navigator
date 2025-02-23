<?php 

if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists('WP_Navigator_Settings') ) {
    class WP_Navigator_Settings {

        /**
         * Adding settings page menu
         *
         * @return void
         */
        public function add_settings_menu(): void {
            //add_options_page('WP Navigator', 'WP Navigator', 'manage_options', 'wp-navigator-settings', [$this, 'admin_page']);
        }

        /**
         * Displaying the settings page
         *
         * @return void
         */
        public function admin_page(): void {
            ?>
            <div class="wrap">
                <h1><?php _e('WP Navigator Settings', 'wp-navigator'); ?></h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('wp-navigator-settings');
                    do_settings_sections('wp-navigator-settings');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Registering settings
         *
         * @return void
         */
        public function register_settings(): void {
            register_setting('wp-navigator-settings', 'wp-navigator-enable');
            register_setting('wp-navigator-settings', 'wp-navigator-enable-in-frontend');
            add_settings_section('wp-navigator-settings', 'WP Navigator Settings', [$this, 'wp_navigator_enable_section'], 'wp-navigator-settings');
            

            add_settings_field('wp-navigator-enable', 'Enable WP Navigator', [$this, 'enable_in_frontend_setting'], 'wp-navigator-settings', 'wp-navigator-settings');
            add_settings_field('wp-navigator-enable-in-frontend', 'Enable WP Navigator in frontend', [$this, 'enable_wp_navigator_setting'], 'wp-navigator-settings', 'wp-navigator-settings');
        }

        /**
         * Enabling section for WP Navigator
         *
         * @return void
         */
        public function wp_navigator_enable_section() {
            echo "<p>Enable WP Navigator</p>";
        }

        public function enable_wp_navigator_setting() {
            echo '<p>' . __('This is the settings field', 'wp-navigator') . '</p>';
            $settings = get_option('wp-navigator-settings');
            $settings = $settings ? $settings : [];
            ?>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="wp-navigator-settings[enable]"><?php _e('Enable WP Navigator', 'wp-navigator'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="wp-navigator-settings[enable]" id="wp-navigator-settings[enable_in_frontend]" value="1" <?php checked(1, $settings['enable'] ?? 0); ?>>
                        </td>
                    </tr>
                </table>
            <?php
        }


        public function enable_in_frontend_setting(): void {
            echo '<p>' . __('This is the settings field', 'wp-navigator') . '</p>';

            $settings = get_option('wp-navigator-settings');
            $settings = $settings ? $settings : [];
            ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="wp-navigator-settings[enable_in_frontend]"><?php _e('Enable WP Navigator', 'wp-navigator'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="wp-navigator-enable-in-frontend" id="wp-navigator-settings[enable]" value="1" <?php checked(1, $settings['enable_in_frontend'] ?? 0); ?>>
                        </td>
                    </tr>
                </table>
            <?php
        }
    }
}

?>