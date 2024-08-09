<?php
/*
Plugin Name: WP Fusion - Another Integration
Description: Another integration module for WP Fusion
Plugin URI: https://wpfusion.com/
Version: 1.0
Author: Another Good Plugins
Author URI: https://anothergoodplugins.com/
*/

final class WP_Fusion_AnotherIntegration {
    private static $instance;

    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Fusion_AnotherIntegration ) ) {
            self::$instance = new WP_Fusion_AnotherIntegration();

            // Hook into wpf_crm_loaded to initialize the plugin
            add_action('wp_fusion_init_crm', array(self::$instance, 'init'), 30);
        }

        return self::$instance;
    }

    public function init($crm) {
        BugFu::log(wp_fusion_postTypes()->crm->test());

        if (isset($crm->custom_methods['extension1_function']) && is_callable($crm->custom_methods['extension1_function'])) {
            $result = call_user_func($crm->custom_methods['extension1_function']);
            BugFu::log($result);
        } else {
            BugFu::log('extension1_function is not available.');
        }

        // BugFu::log('CRM object after calling extension1_function: ' . print_r($crm, true));
    }
}

// Initialize the plugin
add_action('plugins_loaded', function() {
    if (class_exists('WPF_CRM_Base')) {
        WP_Fusion_AnotherIntegration::instance();
    }
});
