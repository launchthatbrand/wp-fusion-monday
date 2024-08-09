<?php

/**
 * Plugin Name: WP Fusion - Monday CRM
 * Description: Boostrap for connecting WP Fusion to a custom CRM
 * Plugin URI: https://verygoodplugins.com/
 * Version: 1.1.7
 * Author: Very Good Plugins
 * Author URI: https://verygoodplugins.com/
*/

/**
 * @copyright Copyright (c) 2021. All rights reserved.
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
 */

// deny direct access.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


if ( ! class_exists( 'WPF_Monday' ) ) {
	include_once __DIR__ . '/includes/class-wpf-custom.php';
}

include_once __DIR__ . '/includes/wpf-custom-utilities.php';

if ( is_admin() ) {
	// require_once WPF_EC_DIR_PATH . 'includes/admin/class-notices.php';
	require_once  __DIR__ . '/includes/admin/admin-functions.php';
	//require_once WPF_EC_DIR_PATH . 'includes/admin/class-upgrades.php';
}

// if ( ! class_exists( 'WPF_Post_Type' ) ) {
// 	include_once __DIR__ . '/includes/class-post-type.php';
// }

/**
 * Add our custom CRM class to the list of registered CRMs
 *
 * @since  1.0.0
 *
 * @param  array $crms The array of registered CRM modules.
 * @return array $crms The array of registered CRM modules.
 */
function wpf_monday_crm( $crms ) {

	$crms['monday'] = 'WPF_Monday';
	return $crms;
}

add_filter( 'wpf_crms', 'wpf_monday_crm' );


// function wpf_include_custom_integration() {

// 	if ( class_exists( 'My/PluginDependencyClass' ) ) {
// 		include_once dirname( __FILE__ ) . '/includes/class-example-ecommerce-integration.php';
// 	}

// 	if ( class_exists( 'MyFormsPlugin' ) ) {
// 		include_once dirname( __FILE__ ) . '/includes/class-example-forms-integration.php';
// 	}

	
// 	// include_once dirname( __FILE__ ) . '/includes/class-example-membership-integration.php';
// 	// include_once dirname( __FILE__ ) . '/includes/class-wpf-monday-api.php';
// 	// include_once dirname( __FILE__ ) . '/includes/wpf-custom-utilities.php';
// 	// include_once dirname( __FILE__ ) . '/includes/class-post-type.php';

	
	

// }

// add_action( 'wp_fusion_init', 'wpf_include_custom_integration' );