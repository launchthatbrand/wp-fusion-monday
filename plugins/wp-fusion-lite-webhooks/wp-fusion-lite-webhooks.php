<?php

/*
Plugin Name: WP Fusion Lite - Incoming Webhooks Addon
Description: Allows modify wordpress data from external third party services.
Plugin URI: https://wpfusion.com/
Version: 1.3.1
Author: Very Good Plugins
Author URI: https://verygoodplugins.com/
Text Domain: wp-fusion
*/

/**
 * @copyright Copyright (c) 2016. All rights reserved.
 *
 * @license   Released under the GPL license http://www.opensource.org/licenses/gpl-license.php
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 *
 */

define( 'WPF_WEBHOOKS_VERSION', '1.3.1' );

// deny direct access
if(!function_exists('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}


final class WP_Fusion_Lite_Webhooks {

	/** Singleton *************************************************************/

	/**
	 * @var WP_Fusion_Webhooks The one true WP_Fusion_Webhooks
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * The integrations handler instance variable
	 *
	 * @var WPF_Integrations
	 * @since 2.0
	 */
	public $integrations;


	/**
	 * The settings instance variable
	 *
	 * @var WP_Fusion_Settings
	 * @since 1.0
	 */
	public $settings;


	/**
	 * Main WP_Fusion_Webhooks Instance
	 *
	 * Insures that only one instance of WP_Fusion_Webhooks exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true WP_Fusion_Webhooks
	 */

	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Fusion_Lite_Webhooks ) ) {
            // BugFu::log('WP_Fusion_Lite_Webhooks::instance()');

			self::$instance = new WP_Fusion_Lite_Webhooks;
			self::$instance->setup_constants();
			self::$instance->includes();

			// self::$instance->updater();

		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @access protected
	 * @return void
	 */

	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-fusion' ), '1.6' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @access protected
	 * @return void
	 */

	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-fusion' ), '1.6' );
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @return void
	 */

	private function setup_constants() {

		if(!defined('WPF_LITE_WEBHOOKS_DIR_PATH')) {
			define('WPF_LITE_WEBHOOKS_DIR_PATH', plugin_dir_path(__FILE__));
		}

		if(!defined('WPF_LITE_WEBHOOKS_PLUGIN_PATH')) {
			define('WPF_LITE_WEBHOOKS_PLUGIN_PATH', plugin_basename(__FILE__));
		}

		if(!defined('WPF_LITE_WEBHOOKS_DIR_URL')) {
			define('WPF_LITE_WEBHOOKS_DIR_URL', plugin_dir_url(__FILE__));
		}
	}


	/**
	 * Include required files
	 *
	 * @access private
	 * @return void
	 */

	private function includes() {
        require_once WPF_LITE_WEBHOOKS_DIR_PATH .'includes/admin/class-admin.php';
		require_once WPF_LITE_WEBHOOKS_DIR_PATH .'includes/class-api.php';

	}

	/**
	 * Set up EDD updater
	 *
	 * @access public
	 * @return void
	 */

	public function updater() {

		if( ! is_admin() )
			return;
		
		$license_status = wp_fusion()->settings->get( 'license_status' );
		$license_key = wp_fusion()->settings->get( 'license_key' );

		if($license_status == 'valid') {
			
			// setup the updater
			$edd_updater = new WPF_Plugin_Updater( WPF_STORE_URL, __FILE__, array( 
					'version' 	=> WPF_WEBHOOKS_VERSION,
					'license' 	=> $license_key,
					'item_id' 	=> 12262,
					'author'    => 'Very Good Plugins',
				)
			);

		} else {

			global $pagenow;

			// if ( 'plugins.php' === $pagenow ) {
			// 	add_action( 'after_plugin_row_' . WPF_WEBHOOKS_PLUGIN_PATH, array( wp_fusion(), 'wpf_update_message' ), 10, 3);
			// }
		}

	}


}


/**
 * The main function responsible for returning the one true WP Fusion Webhooks
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wpf_geo = wp_fusion_webhooks(); ?>
 *
 * @return object The one true WP Fusion Webhooks
 */

function wp_fusion_lite_webhooks() {

	if ( ! function_exists( 'wp_fusion' ) )
		return;

	return WP_Fusion_Lite_Webhooks::instance();

}

add_action( 'plugins_loaded', 'wp_fusion_lite_webhooks', 10 );

?>
