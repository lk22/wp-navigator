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
            add_menu_page("WP Navigator", "WP Navigator", "manage_options", "wp-navigator", [$this, 'admin_page']);
        }
        
        /**
         * Registering initial settings
         *
         * @return void
         */
        public function register_initial_settings(): void {
            register_setting('wp-navigator-settings', 'wp-navigator-settings');
            
            add_settings_section('wp-navigator-settings-section', 'WP Navigator Settings', [$this, 'settings_section'], 'wp-navigator-settings');
        }

        public function settings_section(): void {
            echo "Settings for WP Navigator";
        }
        
        /**
         * rendering settings page section
         *
         * @return void
         */
        public function admin_page() {
            $this->register_initial_settings();
            ob_start();

            ?>
            <div class="wrap">
                <h1>WP Navigator</h1>

                <?php
                    $this->settings_section();
                ?>
                <form method="post" action="options.php">
                    <?php 
                        settings_fields('wp-navigator-settings');
                        do_settings_sections('wp-navigator-settings');
                        $settings = get_option('wp-navigator-settings');
                    ?> 
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Disable WP Navigator</th>
                            <td>
                                <input 
                                    type="checkbox" 
                                    name="wp-navigator-settings[disable]" 
                                    value="1" 
                                    <?php echo $settings && isset($settings['disable']) ? 'checked="checked"' : ''; ?>
                                >
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Enable WP Navigator on the frontend</th>
                            <td>
                                <input 
                                    type="checkbox" 
                                    name="wp-navigator-settings[enable_in_frontend]" 
                                    value="1" 
                                    <?php echo $settings && isset($settings["enable_in_frontend"]) ? 'chceked="checked"' : ''; ?>
                                >
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }
    }
}

?>