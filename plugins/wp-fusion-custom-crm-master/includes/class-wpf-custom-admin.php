<?php

class WPF_Custom_Admin {

	
	

	/**
	 * The CRM slug
	 *
	 * @var string
	 * @since x.x.x
	 */

	private $slug;

	/**
	 * The CRM name
	 *
	 * @var string
	 * @since x.x.x
	 */

	private $name;

	/**
	 * The CRM object
	 *
	 * @var object
	 * @since x.x.x
	 */

	private $crm;

	/**
	 * Get things started.
	 *
	 * @since x.x.x
	 */
	public function __construct( $slug, $name, $crm ) {

		$this->slug = $slug;
		$this->name = $name;
		$this->crm  = $crm;

		add_filter( 'wpf_configure_settings', array( $this, 'register_connection_settings' ), 15, 2 );
		add_action( 'show_field_custom_header_begin', array( $this, 'show_field_custom_header_begin' ), 10, 2 );

		// AJAX callback to test the connection.
		add_action( 'wp_ajax_wpf_test_connection_' . $this->slug, array( $this, 'test_connection' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_select2' ) );

		if ( wp_fusion()->settings->get( 'crm' ) === $this->slug ) {
			$this->init();
		}
	}

	/**
	 * Hooks to run in the admin when this CRM is selected as active.
	 *
	 * @since x.x.x
	 */
	public function init() {

		add_filter( 'wpf_initialize_options_contact_fields', array( $this, 'add_default_fields' ) );
		add_filter( 'wpf_configure_settings', array( $this, 'register_settings' ), 10, 2 );
	}

	public function enqueue_select2() {
        // wp_enqueue_style( 'select4', WPF_DIR_URL . 'includes/admin/options/lib/select2/select4.min.css' );
		// wp_enqueue_script( 'select4', WPF_DIR_URL . 'includes/admin/options/lib/select2/select4.min.js', array( 'jquery' ), '4.0.1', true );
        wp_enqueue_script( 'wpf-custom-admin', plugin_dir_url( __FILE__ ) . 'js/wpf-custom-admin.js', array('jquery', 'select4'), '1.1', true );
    }


	/**
	 * Gets the OAuth URL for the initial connection. Remove if not using OAuth.
	 *
	 * If we're using the WP Fusion app, send the request through wpfusion.com,
	 * otherwise allow a custom app.
	 *
	 * @since  x.x.x
	 *
	 * @return string The URL.
	 */
	public function get_oauth_url() {

		$admin_url = str_replace( 'http://', 'https://', get_admin_url() ); // must be HTTPS for the redirect to work.

		$args = array(
			'redirect' => rawurlencode( $admin_url . 'options-general.php?page=wpf-settings' ),
			'action'   => "wpf_get_{$this->slug}_token",
		);

		return apply_filters( "wpf_{$this->slug}_auth_url", add_query_arg( $args, 'https://wpfusion.com/oauth/' ) );
	}

	/**
	 * Listen for an OAuth response and maybe complete setup. Remove if not using OAuth.
	 *
	 * @since x.x.x
	 */
	public function maybe_oauth_complete() {

		if ( isset( $_GET['code'] ) && isset( $_GET['crm'] ) && $_GET['crm'] === $this->slug ) {

			$code = sanitize_text_field( wp_unslash( $_GET['code'] ) );

			$body = array(
				'grant_type'    => 'authorization_code',
				'code'          => $code,
				'client_id'     => $this->crm->client_id,
				'client_secret' => $this->crm->client_secret,
				'redirect_uri'  => "https://wpfusion.com/oauth/?action=wpf_get_{$this->slug}_token",
			);

			$params = array(
				'timeout'    => 30,
				'user-agent' => 'WP Fusion; ' . home_url(),
				'body'       => wp_json_encode( $body ),
				'headers'    => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				),
			);

			$response = wp_safe_remote_post( $this->crm->auth_url, $params );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$response = json_decode( wp_remote_retrieve_body( $response ) );

			wp_fusion()->settings->set( "{$this->slug}_refresh_token", $response->refresh_token );
			wp_fusion()->settings->set( "{$this->slug}_token", $response->access_token );
			wp_fusion()->settings->set( 'crm', $this->slug );

			wp_safe_redirect( admin_url( 'options-general.php?page=wpf-settings#setup' ) );
			exit;

		}
	}


	/**
	 * Loads CRM connection information on settings page.
	 *
	 * @since x.x.x
	 *
	 * @param array $settings The registered settings on the options page.
	 * @param array $options  The options saved in the database.
	 * @return array $settings The settings.
	 */

	 public function register_connection_settings( $settings, $options ) {

		$new_settings = array();

		$new_settings['custom_header'] = array(
			'title'   => __( 'Monday Configuration', 'wp-fusion-lite' ),
			'type'    => 'heading',
			'section' => 'setup',
		);

		$auth_url = 'https://wpfusion.com/oauth/?redirect=' . urlencode( admin_url( 'options-general.php?page=wpf-settings&crm=zoho' ) ) . '&action=wpf_get_zoho_token&client_id=' . $this->crm->client_id;
		$auth_url = apply_filters( 'wpf_zoho_auth_url', $auth_url );

		if ( ! empty( $options['connection_configured'] ) && 'custom' === wpf_get_option( 'crm' ) ) {

		$new_settings['monday_board'] = array(
			'title'       => __( 'Select Monday.com Board', 'wp-fusion' ),
			'desc'        => __( 'Select the Monday.com board to sync with.', 'wp-fusion' ),
			'type'        => 'select',
			'placeholder' => 'Select Board',
			'section'     => 'setup',
			'choices'     => WPF_Monday_API::get_monday_boards(),
		);
		}

		$new_settings['custom_url'] = array(
			'title'   => __( 'URL', 'wp-fusion' ),
			'desc'    => __( 'URL to your Zoho CRM.', 'wp-fusion' ),
			'type'    => 'text',
			'section' => 'setup',
		);

		$new_settings['custom_key'] = array(
			'title'       => __( 'API Key', 'wp-fusion' ),
			'desc'        => __( 'Your API key.', 'wp-fusion' ),
			'section'     => 'setup',
			'class'       => 'api_key',
			'type'        => 'api_validate', // api_validate field type causes the Test Connection button to be appended to the input.
			'post_fields' => array( 'custom_url', 'custom_key' ), // This tells us which settings fields need to be POSTed when the Test Connection button is clicked.
		);

			// $new_settings['custom_header']['desc']  = '<table class="form-table"><tr>';
			// $new_settings['custom_header']['desc'] .= '<th scope="row"><label>Authorize</label></th>';
			// $new_settings['custom_header']['desc'] .= '<td><a class="button button-primary" href="' . esc_url( $auth_url ) . '">Authorize with Zoho</a><br /><span class="description">You\'ll be taken to Zoho to authorize WP Fusion and generate access keys for this site.</td>';
			// $new_settings['custom_header']['desc'] .= '</tr></table></div><table class="form-table">';

		// } else {

			

		$settings = wp_fusion()->settings->insert_setting_after( 'crm', $settings, $new_settings );

		return $settings;

	}


	/**
	 * Loads standard CRM field names and attempts to match them up with
	 * standard local ones.
	 *
	 * @since  x.x.x
	 *
	 * @param  array $options The options.
	 * @return array The options.
	 */
	public function add_default_fields( $options ) {

		$standard_fields = $this->crm->get_default_fields();

		foreach ( $options['contact_fields'] as $field => $data ) {

			if ( isset( $standard_fields[ $field ] ) && empty( $options['contact_fields'][ $field ]['crm_field'] ) ) {
				$options['contact_fields'][ $field ] = array_merge( $options['contact_fields'][ $field ], $standard_fields[ $field ] );
			}
		}

		return $options;
	}

	/**
	 * Loads CRM specific settings fields
	 *
	 * @since x.x.x
	 *
	 * @param array $settings The registered settings on the options page.
	 * @param array $options  The options saved in the database.
	 */
	public function register_settings( $settings, $options ) {

		// // Add site tracking option
		// $site_tracking = array();

		// $site_tracking['site_tracking_header'] = array(
		// 	'title'   => sprintf( __( '%s Settings', 'wp-fusion' ), $this->name ),
		// 	'type'    => 'heading',
		// 	'section' => 'main',
		// );

		// $site_tracking['site_tracking'] = array(
		// 	'title'   => __( 'Site Tracking', 'wp-fusion' ),
		// 	'desc'    => printf( __( 'Enable %s site tracking</a>.', 'wp-fusion' ), $this->name ),
		// 	'type'    => 'checkbox',
		// 	'section' => 'main',
		// );

		// $settings = wp_fusion()->settings->insert_setting_after( 'login_meta_sync', $settings, $site_tracking );

		return $settings;

	}


	/**
	 * Puts a div around the CRM configuration section so it can be toggled
	 *
	 * @since x.x.x
	 *
	 * @param string $id    The ID of the field.
	 * @param array  $field The field properties.
	 */
	public function show_field_custom_header_begin( $id, $field ) {

		echo '</table>';
		$crm = wpf_get_option( 'crm' );
		echo '<div id="' . esc_attr( $this->slug ) . '" class="crm-config ' . ( $crm == false || $crm != $this->slug ? 'hidden' : 'crm-active' ) . '" data-name="' . esc_attr( $this->name ) . '" data-crm="' . esc_attr( $this->slug ) . '">';
	}


	/**
	 * Verify connection credentials.
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function test_connection() {

		check_ajax_referer( 'wpf_settings_nonce' );

		$api_url = esc_url_raw( wp_unslash( $_POST['custom_url'] ) );
		$api_key = sanitize_text_field( wp_unslash( $_POST['custom_key'] ) );

		$connection = $this->crm->connect( $api_url, $api_key, true );

		if ( is_wp_error( $connection ) ) {

			// Connection failed.
			wp_send_json_error( $connection->get_error_message() );

		} else {

			// Save the API credentials.

			$options = array(
				'custom_url'            => $api_url,
				'custom_key'            => $api_key,
				'crm'                   => $this->slug,
				'connection_configured' => true,
			);

			wp_fusion()->settings->set_multiple( $options );

			wp_send_json_success();

		}
	}
}


add_action( 'user_register', 'wpse_67444_first_last_display_name' );

function wpse_67444_first_last_display_name( $user_id )
{
    $data = get_userdata( $user_id );
    // check if these data are available in your real code!
    wp_update_user( 
        array (
            'ID' => $user_id, 
            'display_name' => "$data->first_name $data->last_name"
        ) 
    );
}