<?php 

if (! defined('ABSPATH')) {
    exit;
}

if ( ! class_exists('WP_Navigator_Settings_Utility') ) {
    class WP_Navigator_Settings_Utility {
        public static function getOption($name) {
            $option = get_option($name);

            if ( $option == "on" ) {
                return true;
            } else if ( $option == "" ) {
                return false;
            } else if (is_string($option)) {
                return $option;
            } 
        }

        public static function setOption($option, $value) : bool {
            return update_option($option, $value);
        }
    }
}

?>