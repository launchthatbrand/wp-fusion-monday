<?php

class WPF_Monday_Admin {

	private $slug;
	private $name;
	private $crm;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function __construct( $slug, $name, $crm ) {

		$this->slug = $slug;
		$this->name = $name;
		$this->crm  = $crm;

		// Load option group
		$this->option_group = 'wpf_options';
		$this->options      = get_option( 'wpf_options', array() );
		// BugFu::log($this->options);

		// Settings
		add_filter( 'wpf_configure_settings', array( $this, 'register_connection_settings' ), 10, 2 );
		add_action( 'show_field_monday_header_begin', array( $this, 'show_field_monday_header_begin' ), 10, 2 );
		add_action( 'show_field_monday_options_begin', array( $this, 'show_field_monday_options_begin' ), 10, 2 );

		// AJAX
		add_action( 'wp_ajax_wpf_sync_workspaces', array( $this, 'wp_ajax_wpf_sync_workspaces' ) );
		add_action( 'wp_ajax_wpf_test_connection_' . $this->slug, array( $this, 'test_connection' ) );

		add_action( 'show_field_test_connection', array( $this, 'show_field_test_connection' ), 10, 2 );
		add_action( 'show_field_monday_footer_end', array( $this, 'show_field_monday_footer_end' ), 10, 2 );
		add_action( 'show_field_select2', array( $this, 'show_field_select2' ), 10, 2 );
		add_action( 'show_field_select3', array( $this, 'show_field_select3' ), 10, 2 );
		add_action( 'show_field_select3_end', array( $this, 'show_field_select3_end' ), 10, 2 );

		if ( wpf_get_option( 'crm' ) == $this->slug ) {
			$this->init();
		}

	}

	/**
	 * Hooks to run when this CRM is selected as active
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function init() {
		
		add_filter( 'wpf_initialize_options_contact_fields', array( $this, 'add_default_fields' ), 10 );
		add_filter( 'wpf_configure_settings', array( $this, 'register_settings' ), 10, 2 );

		// add_filter( 'validate_field_custom_field', array( $this, 'validate_field_custom_field' ), 10, 2 );
		add_action( 'validate_field_monday_workspace', array( $this, 'validate_field_monday_workspace' ), 10, 3 );
		add_action( 'validate_field_monday_board', array( $this, 'validate_field_monday_board' ), 10, 3 );
		// add_action( 'validate_field_monday_tag_field', array( $this, 'validate_field_monday_tag_field' ), 10, 3 );


		add_action( 'wpf_resync_contact', array( $this, 'resync_lists' ) );

		add_filter( 'validate_field_site_tracking', array( $this, 'validate_site_tracking' ), 10, 2 );
		add_filter( 'wpf_initialize_options', array( $this, 'maybe_get_tracking_id' ), 10 );

		add_filter( 'wpf_get_setting_available_crm_tag_fields', array( $this, 'get_setting_available_crm_tag_fields' ) );
		add_filter( 'wpf_get_setting_available_lists', array( $this, 'get_setting_available_lists' ) );
		
	}





	public function get_setting_available_lists() {
		// BugFu::log("get_setting_available_lists_init");
		$lists = get_option( 'wpf_available_lists', array() );
		return $lists;
	}

	public function get_setting_available_crm_tag_fields() {
		// BugFu::log("get_setting_available_crm_tag_fields init");
		$fields = get_option( 'wpf_available_crm_tag_fields', array() );
		return $fields;
	}



	public function validate_field_custom_field( $value, $field ) {

		BugFu::log( "validate_field_custom_field init" );

		wp_fusion()->settings->set( 'custom_field', $value );

		return $value;
	}


	public function wp_ajax_wpf_sync_workspaces(){
		BugFu::log("wp_ajax_wpf_sync_workspaces_init");
		wp_fusion()->crm->sync_workspaces();
		
		wp_send_json_success();
	}

	



	/**
	 * Show field Text.
	 *
	 * @param        $id
	 * @param string $field
	 *
	 * @param null   $subfield_id
	 *
	 * @since  0.1
	 * @access private
	 */
	public function show_field_test_connection( $id, $field, $subfield_id = null ) {
		// BugFu::log($this->options);
		// BugFu::log($id);

		// Retrieve the value from the options array
		$value = isset( $this->options[ $id ] ) ? $this->options[ $id ] : '';

		BugFu::log($value);

		if ( ! isset( $field['allow_null'] ) ) {
			if ( empty( $field['std'] ) ) {
				$field['allow_null'] = true;
			} else {
				$field['allow_null'] = false;
			}
		}

		if ( ! isset( $field['class'] ) ) {
			$field['class'] = '';
		}

		if ( ! isset( $field['disabled'] ) ) {
			$field['disabled'] = false;
		}

		if ( empty( $field['std'] ) && ! empty( $field['placeholder'] ) ) {
			$field['std'] = $field['placeholder'];
		}


		//---------------------------------

		if ( isset( $field['choices'] ) && is_array( $field['choices'] ) ) {
			
			if ( count( $field['choices'] ) > 10 ) {
				$field['class'] .= 'select4-search';
			}
			
			echo '<select id="' . esc_attr( $id ) . '" class="select4 ' . esc_attr( $field['class'] ) . '" name="' . $this->option_group . '[' . esc_attr( $id ) . ']" ' . ( $field['disabled'] ? 'disabled="true"' : '' ) . ' ' . ( $field['placeholder'] ? 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' ) . ' ' . ( $field['allow_null'] == false ? 'data-allow-clear="false"' : '' ) . ' ' . ( ! empty( $unlock ) ? 'data-unlock="' . esc_attr( trim( $unlock ) ) . '"' : '' ) . '>';
			
			if ( $field['allow_null'] == true || ! empty( $field['placeholder'] ) ) {
				echo '<option></option>';}
			
				foreach ($field['choices'] as $choice_value => $choice_label) {

					// Check if the current option value is an array and has an 'id' key
					if (is_array($this->options[$id]) && isset($this->options[$id]['id'])) {
						if (is_array($choice_label) && isset($choice_label['title'])) {
							echo '<option value="' . esc_attr($choice_value) . '"' . selected($this->options[$id]['id'], $choice_value, false) . '>' . esc_html($choice_label['title']) . '</option>';
						} else {
							echo '<option value="' . esc_attr($choice_value) . '"' . selected($this->options[$id]['id'], $choice_value, false) . '>' . esc_html($choice_label) . '</option>';
						}
					} else {
						// Handle cases where $this->options[$id] is not an array
						echo '<option value="' . esc_attr($choice_value) . '"' . selected($this->options[$id], $choice_value, false) . '>' . esc_html(is_array($choice_label) && isset($choice_label['title']) ? $choice_label['title'] : $choice_label) . '</option>';
					}
				}

			echo '</select>';

		} else {

			if ( isset( $field['format'] ) && $field['format'] == 'phone' ) {
				echo '<input id="' . esc_attr( $id ) . '" class="form-control bfh-phone ' . esc_attr( $field['class'] ) . '" data-format="(ddd) ddd-dddd" type="text" id="' . esc_attr( $id ) . '" name="' . $this->option_group . '[' . esc_attr( $id ) . ']" placeholder="' . esc_attr( $field['std'] ) . '" value="' . esc_attr( $this->{ $id } ) . '" ' . ( $field['disabled'] ? 'disabled="true"' : '' ) . '>';
			} else {
				echo '<input id="' . esc_attr( $id ) . '"  class="form-control ' . esc_attr( $field['class'] ) . '" style="display:inline-block;margin-right:5px;" type="text" name="' . $this->option_group . '[' . esc_attr( $id ) . ']" placeholder="' . esc_attr( $field['std'] ) . '" value="' . esc_attr( $value ) . '" ' . ( $field['disabled'] || $field['input_disabled'] ? 'disabled="true"' : '' ) . '>';
			}

		}

		

		//---------------------------------


		

		if ( false == wpf_get_option( 'connection_configured' )) {

			$tip = sprintf( __( 'Connect to Monday and refresh available workspaces from %s. Does not modify any user data or permissions.', 'wp-fusion-lite' ), wp_fusion()->crm->name );

			echo '<a id="test-monday-connection" data-post-fields="' . esc_attr( implode( ',', $field['post_fields'] ) ) . '" class="button button-primary wpf-tip wpf-tip-right test-connection-button" style="padding-left:8px;" data-tip="' . esc_attr( $tip ) . '">';
			echo '<span class="dashicons dashicons-update-alt" style="position:static;margin-right:0;"></span>';
			echo '<span class="text">' . esc_html__( 'Connect', 'wp-fusion-lite' ) . '</span>';
			echo '</a>';

		} else if ( !empty( $value ) ) {

			$tip = sprintf( __( 'Refresh available workspaces from %s. Does not modify any user data or permissions.', 'wp-fusion-lite' ), wp_fusion()->crm->name );

			echo '<a id="test-connection" data-post-fields="' . esc_attr( implode( ',', $field['post_fields'] ) ) . '" class="button button-primary wpf-tip wpf-tip-right test-connection-button" data-tip="' . esc_attr( $tip ) . '">';
			echo '<span class="dashicons dashicons-update-alt"></span>';
			echo '<span class="text">' . sprintf( esc_html__( 'Refresh %s', 'wp-fusion-lite' ), esc_html( $field['child'] ) ) . '</span>';
			echo '</a>';

		}
	}

	/**
	 * Close out API validate field
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function show_field_monday_footer_end( $id, $field ) {
		if ( ! empty( $field['desc'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['desc'] ) . '</span>';
		}
		echo '</td>';
		echo '</tr>';

		echo '</table><div id="connection-output"></div>';
		echo '</div>'; // close CRM div.
		echo '</div>'; // close CRM div.
	}





	public function validate_field_monday_tag_field($value, $field) {
		BugFu::log("validate_field_monday_tag_field init");
	
		// Determine the type based on the value
		$tag_field_type = 'tags'; // Default to 'tags'
		
		// Logic to determine the type
		if (strpos($value, 'dropdown_') !== false) {
			$tag_field_type = 'dropdown';
		}
	
		// Save the value as an array with both ID and type
		$value_with_type = array(
			'id'   => $value,
			'type' => $tag_field_type,
		);
	
		// Save the value with type
		// wp_fusion()->settings->set('monday_tag_field', $value_with_type);
	
		return $value_with_type;
	}



	/**
	 * Resync tags/multiselect field when the tag type is saved and validate the tag type.
	 *
	 * @since  3.41.16
	 *
	 * @param  string      $input   The input.
	 * @param  array       $setting The setting configuration.
	 * @param  WPF_Options $options The options class.
	 * @return string|WP_Error The input or error on validation failure.
	 */
	public function validate_field_monday_workspace( $value, $field, $options ) {
		BugFu::log( "validate_field_monday_workspace init " . $value );

		// Set these temporarily so sync_tags() works.
		wp_fusion()->settings->options['monday_workspace']     = $value;
		
		$result = $this->crm->sync_lists();

		return $value;
	}



	/**
	 * Resync tags/multiselect field when the tag type is saved and validate the tag type.
	 *
	 * @since  3.41.16
	 *
	 * @param  string      $input   The input.
	 * @param  array       $setting The setting configuration.
	 * @param  WPF_Options $options The options class.
	 * @return string|WP_Error The input or error on validation failure.
	 */
	public function validate_field_monday_board( $value, $field, $options ) {

		BugFu::log( "validate_field_monday_board init " . $value );

		// Set these temporarily so sync_tags() works.
		wp_fusion()->settings->options['monday_board']     = $value;

		$this->crm->sync_crm_tag_fields();

		return $value;
	}


	/**
	 * Loads ActiveCampaign connection information on settings page
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function register_connection_settings( $settings, $options ) {
		// BugFu::log("register_connection_settings init");

		$new_settings = array();

		$new_settings['monday_header'] = array(
			'title'   => __( 'Monday Configuration', 'wp-fusion' ),
			'type'    => 'heading',
			'section' => 'setup',
		);

		$new_settings['monday_url'] = array(
			'title'   => __( 'API URL', 'wp-fusion' ),
			'desc'    => __( 'Enter the API URL for your Monday account (find it under Settings >> Developer in your account).', 'wp-fusion' ),
			'type'    => 'text',
			'placeholder' => 'https://api.monday.com/v2',
			'section' => 'setup',
			'std'         => $this->crm->api_url, // Set the default value
		);

		$new_settings['monday_key'] = array(
			'title'       => __( 'API Key', 'wp-fusion' ),
			'child'       => __( 'Workspaces', 'wp-fusion-lite' ),
			'desc'        => __( 'The API key will appear beneath the API URL on the Developer settings page.', 'wp-fusion' ),
			'type'        => 'test_connection',
			'section'     => 'setup',
			'class'       => 'api_key',
			'post_fields' => array( 'monday_url', 'monday_key' ),
		);

		

		// $settings = wp_fusion()->settings->insert_setting_after( 'crm', $settings, $new_settings );

		// $new_settings = array();

		if ( ! empty( $options['connection_configured'] ) && 'monday' === wpf_get_option( 'crm' ) ) {
			$new_settings['monday_options'] = array(
				'type'    => 'heading',
				'section' => 'setup',
			);

			$new_settings['monday_workspace'] = array(
				'title'       => __( 'Monday Workspace', 'wp-fusion' ),
				'child'       => __( 'Boards', 'wp-fusion-lite' ),
				'type'        => 'test_connection',
				'choices'     => wp_fusion()->settings->get( 'available_workspaces', array() ),
				'placeholder' => __( 'Select a workspace', 'wp-fusion' ),
				'section'     => 'setup',
				'desc'    => __( 'Which workspace to sync Users to. For more information, see <a href="https://wpfusion.com/documentation/crm-specific-docs/zoho-tags/" target="_blank">Tags with Zoho</a>.', 'wp-fusion-lite' ),
				'post_fields' => array( 'monday_workspace' ),
			);			

			$new_settings['monday_board'] = array(
				'title'       => __( 'Monday Board', 'wp-fusion' ),
				'child'       => __( 'Tag Fields', 'wp-fusion-lite' ),
				'disabled'    => isset( $options['monday_workspace'] ) ? false : true,
				'desc'        => __( 'Select the Monday.com board to sync with.', 'wp-fusion' ),
				'type'        => 'test_connection',
				'placeholder' => 'Select Board',
				'section'     => 'setup',
				'choices'     => wp_fusion()->settings->get( 'available_lists', array() ),
				'post_fields' => array( 'monday_board' ),
			);

			$new_settings['monday_tag_field'] = array(
				'title'       => __( 'Monday Tags Field', 'wp-fusion' ),
				'child'       => __( 'Available Tags &amp; Fields', 'wp-fusion-lite' ),
				'disabled'    => isset( $options['monday_board'] ) ? false : true,
				'desc'        => __( 'Select the Monday.com board field to use for contact tags.', 'wp-fusion' ),
				'type'        => 'test_connection',
				'placeholder' => 'Select Tag Field',
				'section'     => 'setup',
				'choices'     => wp_fusion()->settings->get( 'available_crm_tag_fields', array() ),
				'post_fields' => array( 'monday_tag_field' ),
			);
			$new_settings['monday_footer'] = array(
				'type'    => 'heading',
				'section' => 'setup',
			);

			
		}
		$settings = wp_fusion()->settings->insert_setting_after( 'crm', $settings, $new_settings );

		return $settings;

	}

	/**
	 * Loads ActiveCampaign specific settings fields
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function register_settings( $settings, $options ) {

		if( ! isset( $options['available_lists'] ) ) {
			$options['available_lists'] = array();
		}

		$new_settings['ac_lists'] = array(
			'title'       => __( 'Lists', 'wp-fusion' ),
			'desc'        => __( 'New contacts will be automatically added to the selected lists.', 'wp-fusion' ),
			'type'        => 'multi_select',
			'placeholder' => 'Select lists',
			'section'     => 'main',
			'choices'     => $options['available_lists']
		);

		$settings = wp_fusion()->settings->insert_setting_after( 'assign_tags', $settings, $new_settings );

		if ( ! isset( $settings['create_users']['unlock']['ac_lists'] ) ) {
			$settings['create_users']['unlock'][] = 'ac_lists';
		}

		$settings['ac_lists']['disabled'] = ( wpf_get_option( 'create_users' ) == 0 ? true : false );

		// Add site tracking option
		$new_settings = array();

		$new_settings['site_tracking_header'] = array(
			'title'   => __( 'ActiveCampaign Site Tracking', 'wp-fusion' ),
			'type'    => 'heading',
			'section' => 'main'
		);

		$new_settings['site_tracking'] = array(
			'title'   => __( 'Site Tracking', 'wp-fusion' ),
			'desc'    => __( 'Enable <a target="_blank" href="https://help.activecampaign.com/hc/en-us/articles/221493708-How-to-set-up-Site-Tracking">ActiveCampaign site tracking</a>.', 'wp-fusion' ),
			'type'    => 'checkbox',
			'section' => 'main'
		);

		$new_settings['site_tracking_id'] = array(
			'type'    => 'hidden',
			'section' => 'main',
		);

		$settings = wp_fusion()->settings->insert_setting_after( 'login_meta_sync', $settings, $new_settings );

		$new_settings = array();

		$new_settings['ac_import_p'] = array(
			'desc'    => __( '<strong>Note:</strong> Contacts cannot be imported from ActiveCampaign unless they are on at least one list.' ),
			'type'    => 'paragraph',
			'class'   => 'wpf-notice notice notice-info',
			'section' => 'import',
		);

		$settings = wp_fusion()->settings->insert_setting_after( 'import_users_p', $settings, $new_settings );

		return $settings;

	}


	/**
	 * Loads standard ActiveCampaign field names and attempts to match them up with standard local ones
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function add_default_fields( $options ) {

		if ( $options['connection_configured'] == true ) {

			require_once dirname( __FILE__ ) . '/monday-fields.php';

			foreach ( $options['contact_fields'] as $field => $data ) {

				if ( isset( $monday_fields[ $field ] ) && empty( $options['contact_fields'][ $field ]['crm_field'] ) ) {
					$options['contact_fields'][ $field ] = array_merge( $options['contact_fields'][ $field ], $monday_fields[ $field ] );
				}

			}

		}

		return $options;

	}

	/**
	 * Enable / disable site tracking depending on selected option
	 *
	 * @access public
	 * @return bool Input
	 */

	public function validate_site_tracking( $input, $setting ) {

		$previous = wpf_get_option( 'site_tracking' );

		// Activate site tracking
		if ( true == $input && false == $previous ) {

			wp_fusion()->crm->connect();

			if ( is_object( wp_fusion()->crm->app ) ) {
				wp_fusion()->crm->app->version( 2 );
				wp_fusion()->crm->app->api( 'tracking/site/status', array( 'status' => 'enable' ) );
				wp_fusion()->crm->app->api( 'tracking/whitelist', array( 'domain' => home_url() ) );
			} else {
				$input = new WP_Error( 'error', 'Unable to enable site tracking, couldn\'t connect to ActiveCampaign.' );
			}
		}

		return $input;

	}

	/**
	 * Gets and saves tracking ID if site tracking is enabled
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function maybe_get_tracking_id( $options ) {

		if ( isset( $options['site_tracking'] ) && $options['site_tracking'] == true && empty( $options['site_tracking_id'] ) ) {

			$this->crm->connect();
			$trackid = $this->crm->get_tracking_id();

			if ( empty( $trackid ) ) {
				return $options;
			}

			$options['site_tracking_id'] = $trackid;
			wp_fusion()->settings->set( 'site_tracking_id', $trackid );

		}

		return $options;

	}


	/**
	 * Puts a div around the Infusionsoft configuration section so it can be toggled
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function show_field_monday_header_begin( $id, $field ) {

		echo '</table>';
		$crm = wpf_get_option( 'crm' );
		echo '<div id="' . esc_attr( $this->slug ) . '" class="crm-config ' . ( $crm == false || $crm != $this->slug ? 'hidden' : 'crm-active' ) . '" data-name="' . esc_attr( $this->name ) . '" data-crm="' . esc_attr( $this->slug ) . '">';

	}

	public function show_field_monday_options_begin( $id, $field ) {

		echo '</table>';
		$crm = wpf_get_option( 'crm' );
		echo '<div id="' . esc_attr( $this->slug ) . '" class="crm-config ' . ( $crm == false || $crm != $this->slug ? 'hidden' : 'crm-active' ) . '" data-name="' . esc_attr( $this->name ) . '" data-crm="' . esc_attr( $this->slug ) . '">';

	}

	/**
	 * Verify connection credentials
	 *
	 * @access public
	 * @return bool
	 */

	public function test_connection() {

		BugFu::log("test_connection_init");

		check_ajax_referer( 'wpf_settings_nonce' );

		BugFu::log($_POST['monday_url'], false);
		BugFu::log($_POST['monday_key'], false);

		$api_url = isset( $_POST['monday_url'] ) ? esc_url_raw( wp_unslash( $_POST['monday_url'] ) ) : false;
		$api_key = isset( $_POST['monday_key'] ) ? sanitize_text_field( wp_unslash( $_POST['monday_key'] ) ) : false;

		$connection = $this->crm->connect( $api_url, $api_key, true );

		// BugFu::log($this->crm, false);
		// BugFu::log(wp_fusion()->crm->app, false);

		if ( is_wp_error( $connection ) ) {
			BugFu::log("Connection Error". $connection->get_error_message());

			wp_send_json_error( $connection->get_error_message() );

		} else {

			$options                          = array();
			$options['monday_url']                = $api_url;
			$options['monday_key']                = $api_key;
			$options['crm']                   = $this->slug;
			$options['connection_configured'] = true;
			wp_fusion()->settings->set_multiple( $options );

			wp_send_json_success();

		}

		die();

	}



	

	/**
	 * Triggered by Resync Contact button, loads lists for contact and saves to user meta
	 *
	 * @access public
	 * @return void
	 */

	public function resync_lists( $user_id ) {
		BugFu::log("resync_lists_init");

		if ( is_wp_error( $this->crm->connect() ) ) {
			return false;
		}

		$contact_id = wp_fusion()->user->get_contact_id( $user_id );

		$result = $this->crm->app->api( 'contact/view?id=' . $contact_id );

		$lists = array();

		if ( ! empty( $result->lists ) ) {

			foreach ( $result->lists as $list_object ) {

				$lists[] = $list_object->listid;

			}
		}

		update_user_meta( $user_id, 'activecampaign_lists', $lists );

	}

	/**
	 * Show Select field.
	 *
	 * @param string $id
	 * @param array  $field
	 * @param null   $subfield_id
	 */
	public function show_field_select2( $id, $field, $subfield_id = null ) {
		BugFu::log("show_field_select2_init");

		if ( ! isset( $field['allow_null'] ) ) {
			if ( empty( $field['std'] ) ) {
				$field['allow_null'] = true;
			} else {
				$field['allow_null'] = false;
			}
		}

		if ( ! isset( $field['placeholder'] ) ) {
			$field['placeholder'] = false;
		}

		$unlock = '';

		if ( isset( $field['unlock'] ) ) {

			foreach ( $field['unlock'] as $target ) {
				$unlock .= $target . ' ';
			}
		}

		if ( count( $field['choices'] ) > 10 ) {
			$field['class'] .= 'select4-search';
		}

		echo '<select id="' . esc_attr( $id ) . '" class="select4 ' . esc_attr( $field['class'] ) . '" name="' . $this->option_group . '[' . esc_attr( $id ) . ']" ' . ( $field['disabled'] ? 'disabled="true"' : '' ) . ' ' . ( $field['placeholder'] ? 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' ) . ' ' . ( $field['allow_null'] == false ? 'data-allow-clear="false"' : '' ) . ' ' . ( ! empty( $unlock ) ? 'data-unlock="' . esc_attr( trim( $unlock ) ) . '"' : '' ) . '>';
		if ( $field['allow_null'] == true || ! empty( $field['placeholder'] ) ) {
			echo '<option></option>';}

		if ( isset( $field['choices'] ) && is_array( $field['choices'] ) ) {
			foreach ( $field['choices'] as $value => $label ) {
				echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->options[ $id ], $value, false ) . '>' . esc_html( $label ) . '</option>';
			}
		}

		echo '</select>';

		if ( false !== wpf_get_option( 'connection_configured' )) {

			$tip = sprintf( __( 'Refresh all custom fields and available tags from %s. Does not modify any user data or permissions.', 'wp-fusion-lite' ), wp_fusion()->crm->name );

			echo '<a id="test-connection" class="button button-primary wpf-tip wpf-tip-right test-connection-button" style="padding-left:8px;" data-tip="' . esc_attr( $tip ) . '">';
			echo '<span class="dashicons dashicons-update-alt" style="position:static;margin-right:0;"></span>';
			echo '<span class="text">' . sprintf( esc_html__( 'Refresh %1$ss', 'wp-fusion-lite' ), esc_html( $field['title'] ) ) . '</span>';
			echo '</a>';

		}

	}


	
	/**
	 * Show Select field.
	 *
	 * @param string $id
	 * @param array  $field
	 * @param null   $subfield_id
	 */
	public function show_field_select3( $id, $field, $subfield_id = null ) {
		BugFu::log("show_field_select3_init");

		if ( ! isset( $field['allow_null'] ) ) {
			if ( empty( $field['std'] ) ) {
				$field['allow_null'] = true;
			} else {
				$field['allow_null'] = false;
			}
		}

		if ( ! isset( $field['placeholder'] ) ) {
			$field['placeholder'] = false;
		}

		$unlock = '';

		if ( isset( $field['unlock'] ) ) {

			foreach ( $field['unlock'] as $target ) {
				$unlock .= $target . ' ';
			}
		}

		if ( count( $field['choices'] ) > 10 ) {
			$field['class'] .= 'select4-search';
		}

		echo '<select id="' . esc_attr( $id ) . '" class="select4 ' . esc_attr( $field['class'] ) . '" name="' . $this->option_group . '[' . esc_attr( $id ) . ']" ' . ( $field['disabled'] ? 'disabled="true"' : '' ) . ' ' . ( $field['placeholder'] ? 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' ) . ' ' . ( $field['allow_null'] == false ? 'data-allow-clear="false"' : '' ) . ' ' . ( ! empty( $unlock ) ? 'data-unlock="' . esc_attr( trim( $unlock ) ) . '"' : '' ) . '>';
		if ( $field['allow_null'] == true || ! empty( $field['placeholder'] ) ) {
			echo '<option></option>';}

		if ( isset( $field['choices'] ) && is_array( $field['choices'] ) ) {
			foreach ( $field['choices'] as $value => $label ) {
				echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->options[ $id ], $value, false ) . '>' . esc_html( $label ) . '</option>';
			}
		}

		echo '</select>';

		if ( false !== wpf_get_option( 'connection_configured' )) {

			$tip = sprintf( __( 'Refresh all custom fields and available tags from %s. Does not modify any user data or permissions.', 'wp-fusion-lite' ), wp_fusion()->crm->name );

			echo '<a id="test-connection" class="button button-primary wpf-tip wpf-tip-right test-connection-button" style="margin-left: 5px;padding-left:8px;" data-tip="' . esc_attr( $tip ) . '">';
			echo '<span class="dashicons dashicons-update-alt" style="position:static;margin-right:0;"></span>';
			echo '<span class="text">' . esc_html__( 'Refresh Available Tags &amp; Fields', 'wp-fusion' ) . '</span>';
			echo '</a>';

		}

	}

	/**
	 * Close out API validate field
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function show_field_select3_end( $id, $field ) {

		if ( ! empty( $field['desc'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['desc'] ) . '</span>';
		}
		echo '</td>';
		echo '</tr>';

		echo '</table><div id="connection-output"></div>';
		echo '</div>'; // close CRM div.
		// echo '<table class="form-table">';

	}

}
