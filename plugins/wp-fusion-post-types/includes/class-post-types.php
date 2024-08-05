<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post Type Sync Integration
 */
class WPF_Post_Type_Sync_Integration extends WPF_Integrations_Base {


	public $slug = 'post-type-sync';

	public $name = 'Post Type Sync';

	public function init() {

		// Initialize custom JS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );

		// Add tabs for post types
		add_filter( 'wpf_settings_tabs', array( $this, 'add_post_type_tabs' ) );
        add_filter( 'wpf_configure_sections', array( $this, 'configure_sections' ), 20, 2 );

		

		// Global settings
		add_filter( 'wpf_configure_settings', array( $this, 'register_settings' ), 15, 2 );

		

		// Render field mapping
		add_action( 'wpf_settings_page_content', array( $this, 'render_post_type_field_mapping' ) );

		// Post type sync button AJAX method
		add_action('wp_ajax_wpf_sync_post_type_fields', array($this, 'ajax_sync_post_type_fields'));

		// Post type sync button rendering
		add_action( 'show_field_sync_button', array( $this, 'show_field_sync_button' ), 15, 2 );

		// Save field mappings
		add_action( 'admin_init', array( $this, 'save_field_mappings' ) );

		// Post type actions
		add_action( 'post_updated', array( $this, 'post_updated' ), 10, 3 );


		// Validation.
		add_filter( 'validate_field_postType_post_fields', array( $this, 'validate_field_post_fields' ), 10, 3 );

		add_filter( 'wpf_set_setting_post_fields', array( $this, 'handle_post_type_fields_update' ), 10, 2 );
		add_filter( 'wpf_get_setting_post_fields', array( $this, 'handle_get_post_fields' ) );

		

		
		// Register dynamic actions for all post types
		$this->register_dynamic_actions();
	}

	public function admin_scripts() {
		// Define the path to the JavaScript file using the constant
		$script_url = WPF_CPT_DIR_URL . 'assets/js/wpf-post-types.js';
		
		// Enqueue the script
		wp_enqueue_script( 'wpf-post-types', $script_url, array('jquery'), '1.0', true );
	}

	private function register_dynamic_actions() {
		$post_types = get_post_types(array('public' => true), 'objects');
		$options = get_option('wpf_options');
		
		foreach ($post_types as $post_type) {
			if (isset($options['post_type_sync_' . $post_type->name]) && !empty($options['post_type_sync_' . $post_type->name])) {

				// WPF Post Type Fields Table Rendering
				add_action("show_field_postType_{$post_type->name}_fields", array($this, 'show_field_postType_fields'), 15, 2);
				add_action("show_field_postType_{$post_type->name}_fields_begin", array($this, 'show_field_postType_fields_begin'), 15, 2);
				add_filter( "wpf_{$post_type->name}_meta_fields", array( $this, "prepare_{$post_type->name}_meta_fields" ), 60 );

				// add_filter( "wpf_set_setting_post_type_fields_{$post_type->name}", array( $this, "handle_post_type_fields_update" ), 10, 2 );

				// Hook into post type updates only for post types with a configured list
				// add_action( "save_post_{$post_type->name}", array( $this, 'postType_updated' ), 10 );

				// Load the field mapping into memory.
				$this->{$post_type->name . '_fields'} = wpf_get_option( $post_type->name . '_fields', array() );
			}
		}
	}






	/**
	 * Validation for contact field data
	 *
	 * @access public
	 * @return mixed
	 */
	public function validate_field_post_fields( $input, $setting, $options_class ) {
		//BugFu::log("validate_field_post_fields init");
		BugFu::log($input);

		// Unset the empty ones.
		foreach ( $input as $field => $data ) {

			if ( 'new_field' === $field ) {
				continue;
				BugFu::log("PASS 1");
			}

			if ( empty( $data['active'] ) && empty( $data['crm_field'] ) ) {
				unset( $input[ $field ] );
				// BugFu::log("UNSET");
			}
		}

		// New fields.
		if ( ! empty( $input['new_field']['key'] ) ) {
			BugFu::log("new_field not empty");

			$input[ $input['new_field']['key'] ] = array(
				'active'    => true,
				'type'      => $input['new_field']['type'],
				'crm_field' => $input['new_field']['crm_field'],
			);

			// Track which ones have been custom registered.

			if ( ! isset( $options_class->options['custom_metafields'] ) ) {
				$options_class->options['custom_metafields'] = array();
			}

			if ( ! in_array( $input['new_field']['key'], $options_class->options['custom_metafields'] ) ) {
				$options_class->options['custom_metafields'][] = $input['new_field']['key'];
			}
		}

		unset( $input['new_field'] );

		$input = apply_filters( 'wpf_contact_fields_save', $input );

		return wpf_clean( $input );

	}
	
	

	public function post_updated($post_id, $post, $post_before) {

		$bypass = apply_filters( 'wpf_bypass_post_updated', false, wpf_clean( wp_unslash( $_REQUEST ) ) );

		// This doesn't need to run twice on a page load.
		// remove_action( 'profile_update', array( $this, 'profile_update' ), 10, 2 );

		if ( ! empty( $_POST ) && false === $bypass ) {

			BugFu::log("send post data");

			$post_data = wpf_clean( wp_unslash( $_POST ) );

			//BugFu::log($post_data);

			$this->push_post_meta( $post_id, $post_data );

		}


        // BugFu::log("post_updated_init");
	
		// $options = get_option('wpf_options');

		// if (isset($options['post_type_sync_' . get_post_type( $post )]) && !empty($options['post_type_sync_' . get_post_type( $post )])) {
		// 	BugFu::log("Post type registered");
		// 	// wp_fusion()->crm->connect();
		// 	$test = wp_fusion()->crm->test();
        // 	BugFu::log($test);
		// }

		

    }

	public function push_post_meta( $post_id, $post_meta = false ) {
		BugFu::log("push_post_meta init");

		// if ( ! wpf_get_option( 'push' ) ) {
		// 	return;
		// }

		do_action( 'wpf_push_post_meta_start', $post_id, $post_meta );

		// If nothing's been supplied, get the latest from the DB.

		if ( false === $post_meta ) {
			$post_meta = $this->get_post_meta( $post_id );
		}

		$post_meta = apply_filters( 'wpf_post_update', $post_meta, $post_id );
		// BugFu::log($post_meta);

		// Allows for cancelling via filter.

		if ( null === $post_meta ) {
			wpf_log( 'notice', $post_id, 'Push post meta aborted: no metadata found for post.' );
			return false;
		}

		// get connected post type board
		$post_type = get_post_type( $post_id );
		$options = get_option('wpf_options');
		
	

		// Check if the post_type_sync_ key exists and its value
		if (isset($options['post_type_sync_' . $post_type])) {
			$associated_crm_object_id = $options['post_type_sync_' . $post_type];
		}

		

		if ( empty( $post_meta ) || empty( $associated_crm_object_id ) ) {
			BugFu::log("no post meta or associated_crm_object_id");
			return;
		}

		wpf_log( 'info', $post_id, 'Pushing meta data to ' . wp_fusion()->crm->name . ': ', array( 'meta_array' => $post_meta ) );

		$result = $this->update_post( $post_id, $post_type, $associated_crm_object_id, $post_meta );

		if ( is_wp_error( $result ) ) {

			wpf_log( $result->get_error_code(), $post_id, 'Error while updating meta data: ' . $result->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return false;

		} elseif ( false === $result ) {

			// If nothing was updated.
			return false;

		}

		do_action( 'wpf_pushed_post_meta', $post_id, $associated_crm_object_id, $post_meta );

		return true;

	}



	/**
	 * Update post - Monday
	 *
	 * @access public
	 * @return bool
	 */

	 public function update_post( $post_id, $post_type, $associated_crm_object_id, $post_meta, $map_meta_fields = true ) {
		BugFu::log("update_post init");
		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('monday_key');
		$board_id = wp_fusion()->crm->get_selected_board();
	
		if ( empty($api_key) || empty($board_id) ) {
			return new WP_Error('missing_api_key_or_board_id', __('API key or board ID is missing.', 'wp-fusion'));
		}
	
		// // If set to true, WP Fusion will convert the field keys from WordPress meta keys into the field names in the CRM.
		// if ( $map_meta_fields ) {
		// 	$post_meta = $this->map_post_meta_fields( $post_type, $post_meta );
		// }
	
		// // Prepare the column values in JSON format dynamically
		// $column_values = array();
		// foreach ( $contact_data as $key => $value ) {
		// 	if ( $key === 'email' ) {
		// 		$column_values[$key] = array(
		// 			'email' => $value,
		// 			'text' => $value
		// 		);
		// 	} else {
		// 		$column_values[$key] = $value;
		// 	}
		// }
	
		// $column_values_json = json_encode( $column_values, JSON_UNESCAPED_SLASHES );
	
		// // Prepare the GraphQL mutation
		// $mutation = 'mutation {
		// 	change_multiple_column_values (board_id: ' . $board_id . ', item_id: ' . $contact_id . ', column_values: "' . addslashes( $column_values_json ) . '") {
		// 		id
		// 	}
		// }';
	
		// // Log the mutation for debugging
		// error_log('GraphQL Mutation: ' . $mutation);
	
		// // Make the request to the Monday.com API
		// $response = wp_safe_remote_post(
		// 	'https://api.monday.com/v2',
		// 	array(
		// 		'method'  => 'POST',
		// 		'headers' => array(
		// 			'Authorization' => $api_key,
		// 			'Content-Type'  => 'application/json',
		// 		),
		// 		'body'    => wp_json_encode(array('query' => $mutation)),
		// 	)
		// );
	
		// // Handle the response
		// if ( is_wp_error( $response ) ) {
		// 	error_log('API request error: ' . $response->get_error_message());
		// 	return $response;
		// }
	
		// $body = wp_remote_retrieve_body( $response );
		// error_log('API response body: ' . $body);
	
		// $body_json = json_decode( $body, true );
	
		// // Check if the body or data is null or empty
		// if ( is_null( $body_json ) || !isset( $body_json['data'] ) ) {
		// 	return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		// }
	
		// // Check for errors in the response
		// if ( isset($body_json['errors']) && !empty($body_json['errors']) ) {
		// 	$error_message = isset($body_json['errors'][0]['message']) ? $body_json['errors'][0]['message'] : 'Unknown error';
		// 	return new WP_Error('api_error', __('API error: ', 'wp-fusion') . $error_message);
		// }
	
		// // Ensure the expected data structure is present
		// if ( !isset( $body_json['data']['change_multiple_column_values']['id'] ) ) {
		// 	return new WP_Error('api_error', __('API error: Missing contact ID in response', 'wp-fusion'));
		// }
	
		// return true;
	}

	

	public function register_settings( $settings, $options ) {
		$settings['post_type_sync_header'] = array(
			'title'   => __( 'Post Type Sync', 'wp-fusion' ),
			'type'    => 'heading',
			'section' => 'post-types',
		);

		$exclude_post_types = array('revision', 'nav_menu_item', 'page'); // Add post types you want to exclude

		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$boards = wp_fusion()->settings->get( 'available_lists', array() );
		// BugFu::log($boards);

		foreach ( $post_types as $post_type ) {
			if (in_array($post_type->name, $exclude_post_types)) {
				continue; // Skip excluded post types
			}

			// Define the custom type for the post type with a sync button
			$settings['post_type_sync_' . $post_type->name] = array(
				'title'   => $post_type->label,
				'type'    => 'sync_button',
				'section' => 'post-types',
				'choices' => $boards,
				'attributes'  => array(
					'data-post_type' => $post_type->name,
					'data-nonce'     => wp_create_nonce('wpf_sync_post_type_fields'),
				),
				'post_fields' => array( 'post_type_sync_' . $post_type->name ),
			);
		}

        $settings['postType_post_fields'] = array(
			'title'   => __( 'Post Fields', 'wp-fusion-lite' ),
			'std'     => array(),
			'type'    => 'post-fields',
			'section' => 'post-fields',
			'choices' => array(),
		);

		return $settings;
	}

	public function show_field_postType_fields( $id, $field ) {
		// BugFu::log("show_field_postType_fields init");

		// BugFu::log($field, false);
		
		// Lets group contact fields by integration if we can
		$field_groups = array(
			'wp' => array(
				'title'  => __( 'Standard WordPress Fields', 'wp-fusion-lite' ),
				'fields' => array(),
			),
		);

		$field_groups = apply_filters( 'wpf_meta_field_groups', $field_groups );

		$field_groups['custom'] = array(
			'title'  => __( 'Custom Field Keys (Added Manually)', 'wp-fusion-lite' ),
			'fields' => array(),
		);

		// Append ungrouped fields.
		$field_groups['extra'] = array(
			'title'  => __( 'Additional <code>wp_usermeta</code> Table Fields (For Developers)', 'wp-fusion-lite' ),
			'fields' => array(),
			'url'    => 'https://wpfusion.com/documentation/getting-started/syncing-contact-fields/#additional-fields',
		);

		/**
		 * Filters the available meta fields.
		 *
		 * @since 1.0.0
		 *
		 * @link https://wpfusion.com/documentation/filters/wpf_meta_fields
		 *
		 * @param array $fields    Tags to be removed from the user
		 */

		
		$field['choices'] = apply_filters( 'wpf_post_meta_fields', $field['choices'] );
		// BugFu::log($field['choices']);

		foreach ( wpf_get_option( 'post_fields', array() ) as $key => $data ) {

			if ( ! isset( $field['choices'][ $key ] ) ) {
				$field['choices'][ $key ] = $data;
			}
		}

		if ( empty( $this->options[ $id ] ) ) {
			$this->options[ $id ] = array();
		}

		// Set some defaults to prevent notices, and then rebuild fields array into group structure.

		foreach ( $field['choices'] as $meta_key => $data ) {

			if ( empty( $this->options[ $id ][ $meta_key ] ) || ! isset( $this->options[ $id ][ $meta_key ]['crm_field'] ) || ! isset( $this->options[ $id ][ $meta_key ]['active'] ) ) {
				$this->options[ $id ][ $meta_key ] = array(
					'active'    => false,
					'pull'      => false,
					'crm_field' => false,
				);
			}

			// Set Pull to on by default.

			if ( ! empty( $this->options[ $id ][ $meta_key ] ) && ! empty( $this->options[ $id ][ $meta_key ]['active'] ) && ! isset( $this->options[ $id ][ $meta_key ]['pull'] ) && empty( $data['pseudo'] ) ) {
				$this->options[ $id ][ $meta_key ]['pull'] = true;
			}

			if ( ! empty( $this->options['custom_metafields'] ) && in_array( $meta_key, $this->options['custom_metafields'] ) ) {

				$field_groups['custom']['fields'][ $meta_key ] = $data;

			} elseif ( isset( $data['group'] ) && isset( $field_groups[ $data['group'] ] ) ) {

				$field_groups[ $data['group'] ]['fields'][ $meta_key ] = $data;

			} else {

				$field_groups['extra']['fields'][ $meta_key ] = $data;

			}
		}

		if ( wp_fusion()->crm->hide_additional ) {

			foreach ( $field_groups['extra']['fields'] as $key => $data ) {

				if ( ! isset( $data['active'] ) || $data['active'] != true ) {
					unset( $field_groups['extra']['fields'][ $key ] );
				}
			}
		}

		/**
		 * This filter is used in the CRM integrations to link up default field
		 * pairings. We used to use wpf_initialize_options but that doesn't work
		 * since it runs before any new fields are added by the wpf_meta_fields
		 * filter (above). This filter will likely be removed in a future update
		 * when we standardize how standard fields are managed.
		 *
		 * @since 3.37.24
		 *
		 * @param array $options The WP Fusion options.
		 */

		$this->options = apply_filters( 'wpf_initialize_options_post_fields', $this->options );

		// These fields should be turned on by default

		if ( empty( $this->options['contact_fields']['user_email']['active'] ) ) {
			$this->options['contact_fields']['first_name']['active'] = true;
			$this->options['contact_fields']['last_name']['active']  = true;
			$this->options['contact_fields']['user_email']['active'] = true;
		}

		$field_types = array( 'text', 'date', 'multiselect', 'checkbox', 'state', 'country', 'int', 'raw', 'tel' );

		$field_types = apply_filters( 'wpf_meta_field_types', $field_types );

		echo '<p>' . sprintf( esc_html__( 'For more information on these settings, %1$ssee our documentation%2$s.', 'wp-fusion-lite' ), '<a href="https://wpfusion.com/documentation/getting-started/syncing-contact-fields/" target="_blank">', '</a>' ) . '</p>';
		echo '<br />';

		// Display contact fields table.
		echo '<table id="contact-fields-table" class="table table-hover">';

		echo '<thead>';
		echo '<tr>';
		echo '<th class="sync">' . esc_html__( 'Sync', 'wp-fusion-lite' ) . '</th>';
		// echo '<th class="sync">' . esc_html__( 'Pull', 'wp-fusion-lite' ) . '</th>'; @TODO.
		echo '<th>' . esc_html__( 'Name', 'wp-fusion-lite' ) . '</th>';
		echo '<th>' . esc_html__( 'Meta Field', 'wp-fusion-lite' ) . '</th>';
		echo '<th>' . esc_html__( 'Type', 'wp-fusion-lite' ) . '</th>';
		echo '<th>' . sprintf( esc_html__( '%s Field', 'wp-fusion-lite' ), esc_html( wp_fusion()->crm->name ) ) . '</th>';
		echo '</tr>';
		echo '</thead>';

		if ( empty( $this->options['table_headers'] ) ) {
			$this->options['table_headers'] = array();
		}

		foreach ( $field_groups as $group => $group_data ) {

			if ( empty( $group_data['fields'] ) && $group != 'extra' ) {
				continue;
			}

			// Output group section headers.
			if ( empty( $group_data['title'] ) ) {
				$group_data['title'] = 'none';
			}

			$group_slug = strtolower( str_replace( ' ', '-', $group_data['title'] ) );

			if ( ! isset( $this->options['table_headers'][ $group_slug ] ) ) {
				$this->options['table_headers'][ $group_slug ] = false;
			}

			if ( 'standard-wordpress-fields' !== $group_slug ) { // Skip the first one

				echo '<tbody class="labels">';
				echo '<tr class="group-header"><td colspan="5">';
				echo '<label for="' . esc_attr( $group_slug ) . '" class="group-header-title ' . ( $this->options['table_headers'][ $group_slug ] == true ? 'collapsed' : '' ) . '">';
				echo wp_kses_post( $group_data['title'] );

				if ( isset( $group_data['url'] ) ) {
					echo '<a class="table-header-docs-link" href="' . esc_url( $group_data['url'] ) . '" target="_blank">' . esc_html__( 'View documentation', 'wp-fusion-lite' ) . ' &rarr;</a>';
				}

				echo '<i class="fa fa-angle-down"></i><i class="fa fa-angle-up"></i></label><input type="checkbox" ' . checked( $this->options['table_headers'][ $group_slug ], true, false ) . ' name="wpf_options[table_headers][' . $group_slug . ']" id="' . $group_slug . '" data-toggle="toggle">';
				echo '</td></tr>';
				echo '</tbody>';

			}

			$table_class = 'table-collapse';

			if ( $this->options['table_headers'][ $group_slug ] == true ) {
				$table_class .= ' hide';
			}

			if ( ! empty( $group_data['disabled'] ) ) {
				$table_class .= ' disabled';
			}

			echo '<tbody class="' . esc_attr( $table_class ) . '">';

			foreach ( $group_data['fields'] as $user_meta => $data ) {

				if ( ! is_array( $data ) ) {
					$data = array();
				}

				// Allow hiding for internal fields.
				if ( isset( $data['hidden'] ) ) {
					continue;
				}

				echo '<tr' . ( $this->options[ $id ][ $user_meta ]['active'] == true ? ' class="success" ' : '' ) . '>';
				echo '<td><input class="checkbox contact-fields-checkbox"' . ( empty( $this->options[ $id ][ $user_meta ]['crm_field'] ) || 'user_email' == $user_meta ? ' disabled' : '' ) . ' type="checkbox" id="wpf_cb_' . esc_attr( $user_meta ) . '" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $user_meta ) . '][active]" value="1" ' . checked( $this->options[ $id ][ $user_meta ]['active'], 1, false ) . '/></td>';
				// echo '<td><input class="checkbox"' . ( empty( $this->options[ $id ][ $user_meta ]['crm_field'] ) || ! empty( $data['pseudo'] ) ? ' disabled' : '' ) . ' type="checkbox" id="wpf_cb_pull_' . esc_attr( $user_meta ) . '" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $user_meta ) . '][pull]" value="1" ' . checked( $this->options[ $id ][ $user_meta ]['pull'], 1, false ) . '/></td>';
				echo '<td class="wp_field_label">' . ( isset( $data['label'] ) ? esc_html( wp_strip_all_tags( $data['label'] ) ) : '' );

				if ( 'user_pass' === $user_meta ) {

					$pass_message  = 'It is <em>strongly</em> recommended to leave this field disabled from sync. If it\'s enabled: <br /><br />';
					$pass_message .= '1. Real user passwords will be synced in plain text to ' . wp_fusion()->crm->name . ' when a user registers or changes their password. This is a security issue and may be illegal in your jurisdiction.<br /><br />';
					$pass_message .= '2. User passwords will be loaded from ' . wp_fusion()->crm->name . ' when webhooks are received. If not set up correctly this could result in your users\' passwords being unexpectedly reset, and/or password reset links failing to work.<br /><br />';
					$pass_message .= 'If you are importing users from ' . wp_fusion()->crm->name . ' via a webhook and wish to store their auto-generated password in a custom field, it is sufficient to check the box for <strong>Return Password</strong> on the General settings tab. You can leave this field disabled from syncing.';

					echo ' <i class="fa fa-question-circle wpf-tip wpf-tip-right" data-tip="' . esc_attr( $pass_message ) . '"></i>';
				}

				// Tooltips

				if ( isset( $data['tooltip'] ) ) {
					echo ' <i class="fa fa-question-circle wpf-tip wpf-tip-right" data-tip="' . esc_attr( $data['tooltip'] ) . '"></i>';
				}

				// Track custom registered fields.

				if ( ! empty( $this->options['custom_metafields'] ) && in_array( $user_meta, $this->options['custom_metafields'] ) ) {
					echo ' (' . esc_html__( 'Added by user', 'wp-fusion-lite' ) . ')';
				}

				echo '</td>';
				echo '<td><span class="label label-default">' . esc_html( $user_meta ) . '</span></td>';
				echo '<td class="wp_field_type">';

				if ( ! isset( $data['type'] ) ) {
					$data['type'] = 'text';
				}

				// Allow overriding types via dropdown.
				if ( ! empty( $this->options['contact_fields'][ $user_meta ]['type'] ) ) {
					$data['type'] = $this->options['contact_fields'][ $user_meta ]['type'];
				}

				if ( ! in_array( $data['type'], $field_types ) ) {
					$field_types[] = $data['type'];
				}

				asort( $field_types );

				echo '<select class="wpf_type" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $user_meta ) . '][type]">';

				foreach ( $field_types as $type ) {
					echo '<option value="' . esc_attr( $type ) . '" ' . selected( $data['type'], $type, false ) . '>' . esc_html( $type ) . '</option>';
				}

				echo '<td>';

				wpf_render_post_field_select( $this->options[ $id ][ $user_meta ]['crm_field'], 'wpf_options', 'postType_post_fields', $user_meta );

				// Indicate pseudo-fields that should only be synced one way.
				if ( isset( $data['pseudo'] ) ) {
					echo '<input type="hidden" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $user_meta ) . '][pseudo]" value="1">';
				}

				echo '</td>';

				echo '</tr>';

			}
		}

		// Add new.
		echo '<tr>';
		echo '<td><input class="checkbox contact-fields-checkbox" type="checkbox" disabled id="wpf_cb_new_field" name="wpf_options[contact_fields][new_field][active]" value="1" /></td>';
		echo '<td class="wp_field_label">Add new field</td>';
		echo '<td><input type="text" id="wpf-add-new-field" name="wpf_options[contact_fields][new_field][key]" placeholder="New Field Key" /></td>';
		echo '<td class="wp_field_type">';

		echo '<select class="wpf_type" name="wpf_options[contact_fields][new_field][type]">';

		foreach ( $field_types as $type ) {
			echo '<option value="' . esc_attr( $type ) . '" ' . selected( 'text', $type, false ) . '>' . esc_html( $type ) . '</option>';
		}

		echo '<td>';

		wpf_render_crm_field_select( false, 'wpf_options', 'contact_fields', 'new_field' );

		echo '</td>';

		echo '</tr>';

		echo '</tbody>';

		echo '</table>';

	}



	/**
	 * Filters out internal WordPress fields from showing up in syncable meta fields list and sets labels and types for built in fields
	 *
	 * @since 1.0
	 * @return array
	 */

	 public function prepare_post_meta_fields( $meta_fields ) {
		// Load the reference of standard WP field names and types.
		include __DIR__ . '/wordpress-post-fields.php';
	
		// Sets field types and labels for all built in fields.
		foreach ( $wp_fields as $key => $data ) {
			if ( ! isset( $data['group'] ) ) {
				$data['group'] = 'wp';
			}
			$meta_fields[ $key ] = $data;
		}
	
		// Get any additional wp_usermeta data.
		$all_fields = get_post_meta_keys('post');
		// BugFu::log($all_fields);
	
		// Some fields we can exclude via partials.
		$exclude_fields_partials = array(
			'metaboxhidden_',
			'meta-box-order_',
			'screen_layout_',
			'closedpostboxes_',
			'_contact_id',
			'_tags',
		);
	
		foreach ( $exclude_fields_partials as $partial ) {
			foreach ( $all_fields as $field => $data ) {
				if ( strpos( $field, $partial ) !== false ) {
					unset( $all_fields[ $field ] );
				}
			}
		}
	
		// Sets field types and labels for all built in fields.
		foreach ( $all_fields as $key ) {
			// Skip hidden fields.
			if ( substr( $key, 0, 1 ) === '_' || substr( $key, 0, 5 ) === 'hide_' || substr( $key, 0, 3 ) === 'wp_' ) {
				continue;
			}
	
			if ( ! isset( $meta_fields[ $key ] ) ) {
				$meta_fields[ $key ] = array(
					'label' => ucwords( str_replace( '_', ' ', $key ) ),
					'group' => 'extra',
					'type'  => 'text',
				);
			}
		}
	
		return $meta_fields;
	}
	




	public function show_field_sync_button( $id, $field ) {
		$post_type = $field['attributes']['data-post_type'];

		// Retrieve the saved value from options
		$options = get_option('wpf_options');
		$select_value = isset($options[$id]) ? (string) $options[$id] : '';
		
		$boards = wp_fusion()->settings->get( 'available_lists', array() );

		// Render the select field
		echo '<select style="display:inline-block;margin-right:5px;" id="' . esc_attr( $id ) . '" class="form-control ' . esc_attr( $field['class'] ) . '" name="wpf_options[' . esc_attr( $id ) . ']">';
		echo '<option value="">' . esc_html__( 'Select Board', 'wp-fusion' ) . '</option>';
		foreach ( $boards as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '" ' . selected( $select_value, $value, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';

		// Render the sync button
		echo '<a id="sync-post-type-fields-' . esc_attr( $post_type ) . '" class="button button-primary sync-post-type-fields" data-post_type="' . esc_attr( $post_type ) . '" data-nonce="' . esc_attr( $field['attributes']['data-nonce'] ) . '">';
		echo '<span class="dashicons dashicons-update-alt"></span>';
		echo '<span class="text">' . esc_html__( 'Sync Fields', 'wp-fusion' ) . '</span>';
		echo '</a>';
	}

	public function show_field_sync_button_end( $id, $field ) {

		if ( ! empty( $field['desc'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['desc'] ) . '</span>';
		}
		echo '</td>';
		echo '</tr>';

		echo '</table><div id="connection-output"></div>';
		echo '</div>'; // close CRM div.
		// echo '<table class="form-table">';

	}


	public function ajax_sync_post_type_fields() {
		BugFu::log("ajax_sync_post_type_fields init");
		check_ajax_referer('wpf_sync_post_type_fields', '_ajax_nonce');
	
		$post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';
	
		if (empty($post_type)) {
			wp_send_json_error('Post type not specified');
			return;
		}

		BugFu::log("calling sync_post_type_fields");
		$result = $this->sync_post_type_fields($post_type);
	
		if (true === $result) {
			wp_send_json_success();
		} else {
			if (is_wp_error($result)) {
				wp_send_json_error($result->get_error_message());
			} else {
				wp_send_json_error();
			}
		}
	}

	public function sync_post_type_fields($post_type) {
		BugFu::log("sync_post_type_fields init");

		// Load built in fields first
		// require dirname( __FILE__ ) . '/monday-fields.php';

		$built_in_fields = array();

		// foreach ( $monday_fields as $index => $data ) {
		// 	$built_in_fields[ $data['crm_field'] ] = $data['crm_label'];
		// }

		// asort( $built_in_fields );
        // Fetch the API key

        $api_key = wpf_get_option('monday_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('No API key provided.', 'wp-fusion'));
        }

		$options = get_option('wpf_options');

		// Check if the post_type_sync_ key exists and its value
		if (isset($options['post_type_sync_' . $post_type])) {
			$board = $options['post_type_sync_' . $post_type];
		}

		BugFu::log("selected board: " . $board);

        if (empty($board)) {
            return new WP_Error('no_board_selected', __('No board selected for this post type.', 'wp-fusion'));
        }

        // Prepare the GraphQL query
        $query = '{"query": "{ boards (ids: [' . $board . ']) { columns { id title } } }"}';

        // Make the request
        $response = wp_safe_remote_post(
            'https://api.monday.com/v2',
            array(
                'method'  => 'POST',
                'headers' => array(
                    'Authorization' => $api_key,
                    'Content-Type'  => 'application/json',
                ),
                'body'    => $query,
            )
        );

        // Handle the response
        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
		BugFu::log($body);

        // Check for errors in the response
        if (isset($body['errors']) && !empty($body['errors'])) {
            $error_message = isset($body['errors'][0]['message']) ? $body['errors'][0]['message'] : 'Unknown error';
            return new WP_Error('authentication_error', __('Authentication failed: ', 'wp-fusion') . $error_message);
        }

        if (empty($body['data']['boards'][0]['columns'])) {
            return new WP_Error('no_columns_found', __('No columns found for the selected board.', 'wp-fusion'));
        }

		

        // Process the columns
        $custom_fields = array();
		

        foreach ($body['data']['boards'][0]['columns'] as $column) {
            $custom_fields[$column['id']] = $column['title'];
        }
		BugFu::log($custom_fields);

		$post_fields = array(
			'Standard Fields' => $built_in_fields,
			'Custom Fields'   => $custom_fields,
		);
		
		// 'wpf_set_setting_' . $key fired

        wp_fusion()->settings->set($post_type.'_fields', $post_fields);

        return true;
    }

	public function handle_post_type_fields_update( $value ) {
		BugFu::log("handle_post_type_fields_update init");
		// if ( strpos( $key, 'post_type_fields_' ) === 0 ) {
		// 	// Extract post type from the key.
		// 	$post_type = str_replace( 'post_type_fields_', '', $key );
	
		// 	// Update the specific option for the post type fields.
		// 	update_option( 'post_type_fields_post' . $post_type, $value, false );
		// }
		update_option( 'wpf_post_fields', $value, false );
	
		return $value;
	}

	public function handle_get_post_fields( $fields ) {
		BugFu::log("handle_get_post_fields init");
		BugFu::log($fields);
		// Check if the post fields option is already set in the value.
		// if ( !empty( $value ) ) {
		// 	return $value;
		// }
	
		// Retrieve the setting from the custom option.
		$setting = get_option( 'wpf_post_fields', array() );
		BugFu::log($setting);
		return ! empty( $setting ) ? $setting : $value;
	}
	


	public function show_field_postType_fields_begin( $id, $field ) {

		if ( ! isset( $field['disabled'] ) ) {
			$field['disabled'] = false;
		}

		echo '<tr valign="top"' . ( $field['disabled'] ? ' class="disabled"' : '' ) . '>';
		echo '<td style="padding:0px">';
	}





	// public function add_post_type_tabs( $tabs ) {
		
	// 	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	// 	foreach ( $post_types as $post_type ) {
	// 		$board_id = get_option( 'wpf_post_type_sync_' . $post_type->name );
	// 		if ( $board_id ) {
	// 			$tabs['wpf_' . $post_type->name . '_fields'] = $post_type->label . ' Fields';
	// 		}
	// 	}

		

	// 	return $tabs;
	// }

	// public function render_post_type_field_mapping( $tab ) {
	// 	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	// 	foreach ( $post_types as $post_type ) {
	// 		if ( 'wpf_' . $post_type->name . '_fields' === $tab ) {
	// 			$board_id = get_option( 'wpf_post_type_sync_' . $post_type->name );
	// 			$fields = $this->get_post_type_fields( $post_type->name );
	// 			$board_columns = $this->get_monday_board_columns( $board_id );

	// 			echo '<h2>' . sprintf( __( '%s Fields', 'wp-fusion' ), $post_type->label ) . '</h2>';
	// 			echo '<table class="form-table">';
	// 			foreach ( $fields as $field_key => $field_label ) {
	// 				echo '<tr>';
	// 				echo '<th scope="row">' . esc_html( $field_label ) . '</th>';
	// 				echo '<td>';
	// 				echo '<select name="wpf_field_mapping[' . esc_attr( $post_type->name ) . '][' . esc_attr( $field_key ) . ']">';
	// 				echo '<option value="">' . __( 'Select a column', 'wp-fusion' ) . '</option>';
	// 				foreach ( $board_columns as $column_id => $column_name ) {
	// 					echo '<option value="' . esc_attr( $column_id ) . '">' . esc_html( $column_name ) . '</option>';
	// 				}
	// 				echo '</select>';
	// 				echo '</td>';
	// 				echo '</tr>';
	// 			}
	// 			echo '</table>';
	// 			submit_button();
	// 		}
	// 	}
	// }

	public function get_post_type_fields( $post_type ) {
		// Fetch custom fields for the post type
		// This is a simplified example. You might need to adjust it to fit your actual fields
		return array(
			'field_1' => 'Field 1',
			'field_2' => 'Field 2',
		);
	}

	public function get_monday_board_columns( $board_id ) {
		// Fetch columns from Monday.com API for the specified board
		// Replace with your existing function to get board columns
		return array(
			'col_1' => 'Column 1',
			'col_2' => 'Column 2',
		);
	}

	public function save_field_mappings() {
		if ( isset( $_POST['wpf_field_mapping'] ) ) {
			foreach ( $_POST['wpf_field_mapping'] as $post_type => $fields ) {
				update_option( 'wpf_field_mapping_' . $post_type, $fields );
			}
		}
	}

    public function configure_sections( $page, $options ) {

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		foreach ( $post_types as $post_type ) {
            $board_id = wpf_get_option( 'post_type_sync_' . $post_type->name );
            if ( $board_id ) {
				$page['sections'] = wp_fusion()->settings->insert_setting_after(
					'contact-fields',
					$page['sections'],
					array(
						$post_type->name . '-fields' => sprintf( __( '%s Fields', 'wp-fusion' ), $post_type->label ),
						),
				);
                // $page['sections'][ $post_type->name . '_fields' ] = sprintf( __( '%s Fields', 'wp-fusion' ), $post_type->label ) . ' →';
            }
        }

		$page['sections'] = wp_fusion()->settings->insert_setting_after(
			'advanced',
			$page['sections'],
			array(
				'post-types' => 'Post Type Sync',
				),
		);

		
    
        return $page;
    }
}

new WPF_Post_Type_Sync_Integration();
