<?php
/*
Plugin Name: WP Fusion - Monday.com Integration
Plugin URI: https://wpfusion.com/
Description: Monday.com integration for WP Fusion
Version: 1.0.0
Author: Very Good Plugins
Author URI: https://verygoodplugins.com/
Text Domain: wp-fusion-monday
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPF_MONDAY_VERSION', '1.0.0' );
define( 'WPF_MONDAY_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPF_MONDAY_URL', plugin_dir_url( __FILE__ ) );

// Check if WP Fusion is active
if ( ! function_exists( 'wp_fusion' ) ) {
	return;
}

class WPF_Monday {

	/**
	 * Contains API params
	 */
	public $params;

	/**
	 * Lets pluggable functions know which features are supported by the CRM
	 */
	public $supports;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {

		$this->slug     = 'monday';
		$this->name     = 'Monday.com';
		$this->supports = array();

		// Set up admin options
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/includes/admin/class-admin.php';
			new WPF_Monday_Admin( $this->slug, $this->name, $this );
		}

		add_filter( 'wpf_crm_addons', array( $this, 'register_addon' ) );
	}

	/**
	 * Registers addon with WP Fusion
	 *
	 * @access public
	 * @return array Addons
	 */
	public function register_addon( $addons ) {

		$addons[ $this->slug ] = $this->name;

		return $addons;

	}

}

new WPF_Monday();
