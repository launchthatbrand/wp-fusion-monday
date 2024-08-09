<?php

/**
 * Handles the settings in the admin
 *
 * @since <creation_version>
 */

class WPF_Lite_Webhooks_Admin {

	/**
	 * Contains all plugin settings
	 */
	public $options = array();


	/**
	 * Make batch processing utility publicly accessible
	 */
	public $batch;


	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @return void
	 */

	public function __construct() {

		if ( is_admin() ) {

			$this->options = get_option( 'wpf_options', array() ); // No longer loading this on the frontend.

			$this->init();

			// add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_item' ), 100 );

		}

		// Default settings.
		//add_filter( 'wpf_get_setting_license_key', array( $this, 'get_license_key' ) );
		// add_filter( 'wpf_get_setting_contact_fields', array( $this, 'get_contact_fields' ) );

	}


    /**
	 * Fires up settings in admin
	 *
	 * @since 1.0
	 * @return void
	 */

	private function init() {
        // BugFu::log('WP_Fusion_Lite_Webhooks_Settings::init()');

		// $this->includes();

		// Custom fields.
		
		// CRM setup layouts.
		
		// Resync button at top.
	
		// AJAX.

		// Setup scripts and initialize.
		// add_filter( 'wpf_meta_fields', array( $this, 'prepare_meta_fields' ), 60 );

		add_filter( 'wpf_configure_settings', array( $this, 'configure_settings' ), 20, 2 ); // Initial settings.
		// add_filter( 'wpf_configure_settings', array( $this, 'configure_settings' ), 100, 2 ); // Settings cleanup / tweaks.

	

		// Fire up the options framework.
		// new WP_Fusion_Options( $this->get_setup(), $this->get_sections() );

	}

    public function configure_settings( $settings, $options ) {
        

		// Unset the 'webhooks_lite_notice' setting
		if ( isset( $settings['webhooks_lite_notice'] ) ) {
			unset( $settings['webhooks_lite_notice'] );
		}

		
        // ACCESS KEY

		$new_settings = array();
		
		$new_settings['access_key_desc'] = array(
			'type'    => 'paragraph',
			'section' => 'main',
			'desc'    => sprintf( __( 'Webhooks allow you to send data from %s back to your website. See <a href="https://wpfusion.com/documentation/webhooks/about-webhooks/" target="_blank">our documentation</a> for more information on creating webhooks.', 'wp-fusion' ), wp_fusion()->crm->name ),
		);

		$new_settings['access_key'] = array(
			'title'   => __( 'Access Key', 'wp-fusion' ),
			'desc'    => __( 'Use this key when sending data back to WP Fusion via a webhook or ThriveCart.', 'wp-fusion' ),
			'type'    => 'text',
			'section' => 'main',
		);

		$new_settings['webhook_url'] = array(
			'title'   => __( 'Webhook Base URL', 'wp-fusion' ),
			'desc'    => sprintf( __( 'This is the base URL for sending webhooks back to your site. <a href="http://wpfusion.com/documentation/#webhooks" target="_blank">See the documentation</a> for more information on how to structure the URL.', 'wp-fusion' ), wp_fusion()->crm->name ),
			'type'    => 'webhook_url',
			'section' => 'main',
		);

		$new_settings['test_webhooks'] = array(
			'title'   => __( 'Test Webhooks', 'wp-fusion' ),
			'desc'    => __( 'Click this button to test your site\'s ability to receive incoming webhooks.', 'wp-fusion' ),
			'type'    => 'text',
			'section' => 'main',
		);


        $settings = wp_fusion()->settings->insert_setting_after( 'access_key_header', $settings, $new_settings );
		return $settings;

    }




        
		
}

new WPF_Lite_Webhooks_Admin;