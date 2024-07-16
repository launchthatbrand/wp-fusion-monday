<?php
/*
 * Plugin Name: WP Fusion - Monday CRM
 * Plugin URI: https://wpfusion.com/
 * Description: Monday CRM module for WP Fusion
 * Version: 1.0.0
 * Author: Very Good Plugins
 * Author URI: https://verygoodplugins.com/
 *
 * @package wp-fusion
 * @subpackage wp-fusion-monday-crm
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpf-monday.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpf-monday-admin.php';

/**
 * Adds the Monday CRM integration to WP Fusion
 *
 * @since 1.0.0
 * @return array Integrations
 */
function wpf_add_monday_crm( $crm ) {

	$crm['monday'] = 'Monday';
	return $crm;

}
add_filter( 'wpf_crm_addons', 'wpf_add_monday_crm' );
