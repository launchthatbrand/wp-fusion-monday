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
		add_filter( 'validate_field_post_fields', array( $this, 'validate_field_post_fields' ), 10, 3 );

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
				add_action("show_field_{$post_type->name}_fields", array($this, 'show_field_postType_fields'), 15, 2);
				add_action("show_field_{$post_type->name}_fields_begin", array($this, 'show_field_postType_fields_begin'), 15, 2);

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
		BugFu::log("validate_field_post_fields init");

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
	
		// If set to true, WP Fusion will convert the field keys from WordPress meta keys into the field names in the CRM.
		if ( $map_meta_fields ) {
			$post_meta = $this->map_post_meta_fields( $post_type, $post_meta );
		}
	
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

	public function map_post_meta_fields( $post_type, $post_meta ) {
		BugFu::log("map_post_meta_fields init");
		BugFu::log($post_type);

		if ( ! is_array( $post_meta ) || empty( $post_meta ) ) {
			return array();
		}

		$update_data = array();

		foreach ( $this->{$post_type. '_fields'} as $field => $field_data ) {
			// BugFu::log($field_data );
			

			if ( empty( $field_data['active'] ) || !isset( $field_data['active'] ) || empty( $field_data['crm_field'] ) ) {
				continue;
			}

			// BugFu::log("PASS 2");
		

			// Don't send add_tag_ fields to the CRM as fields.
			if ( strpos( $field_data['crm_field'], 'add_tag_' ) !== false ) {
				continue;
			}

			// If field exists in form and sync is active.
			if ( isset( $post_meta[ $field ] ) ) {
				BugFu::log("PASS");

				if ( empty( $field_data['type'] ) ) {
					$field_data['type'] = 'text';
				}

				$field_data['crm_field'] = strval( $field_data['crm_field'] );

				if ( 'datepicker' === $field_data['type'] ) {

					// We'd been using date and datepicker interchangeably up until
					// 3.38.11, which is confusing. We'll just use "date" going forward.

					$field_data['type'] = 'date';
				}

				/**
				 * Format field value.
				 *
				 * @since 1.0.0
				 *
				 * @link  https://wpfusion.com/documentation/filters/wpf_format_field_value/
				 *
				 * @param mixed  $value     The field value.
				 * @param string $type      The field type.
				 * @param string $crm_field The field ID in the CRM.
				 */

				$value = apply_filters( 'wpf_format_field_value', $post_meta[ $field ], $field_data['type'], $field_data['crm_field'] );

				if ( 'raw' === $field_data['type'] ) {

					// Allow overriding the empty() check by setting the field type to raw.

					$update_data[ $field_data['crm_field'] ] = $value;

				} elseif ( is_null( $value ) ) {

					// Allow overriding empty() check by returning null from wpf_format_field_value.

					$update_data[ $field_data['crm_field'] ] = '';

				} elseif ( false === $value ) {

					// Some CRMs (i.e. Sendinblue) need to be able to sync false as a value to clear checkboxes.

					$update_data[ $field_data['crm_field'] ] = false;

				} elseif ( 0 === $value || '0' === $value ) {

					$update_data[ $field_data['crm_field'] ] = 0;

				} elseif ( empty( $value ) && ! empty( $post_meta[ $field ] ) && 'date' === $field_data['type'] ) {

					// Date conversion failed.
					wpf_log( 'notice', wpf_get_current_post_id(), 'Failed to create timestamp from value <code>' . $post_meta[ $field ] . '</code>. Try setting the field type to <code>text</code> instead, or fixing the format of the input date.' );

				} elseif ( ! empty( $value ) ) {

					$update_data[ $field_data['crm_field'] ] = $value;

				}
			}
		}

		$update_data = apply_filters( 'wpf_map_meta_fields', $update_data, $post_meta );

		return $update_data;

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
		BugFu::log($boards);

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

        $settings['post_fields'] = array(
			'title'   => __( 'Contact Fields2', 'wp-fusion-lite' ),
			'std'     => array(),
			'type'    => 'post-fields',
			'section' => 'post-fields',
			'choices' => array(),
		);

		return $settings;
	}

	public function show_field_postType_fields( $id, $field ) {
		$post_type = 'post'; // Replace with dynamic post type if needed
	
		// Group post fields by meta keys
		$field_groups = array(
			'post_meta_fields' => array(
				'title'  => __( 'Post Meta Fields', 'wp-fusion-lite' ),
				'fields' => format_post_meta_keys( $post_type ),
			),
		);
	
		if ( empty( $this->options[ $id ] ) ) {
			$this->options[ $id ] = array();
		}
	
		// Set some defaults to prevent notices, and then rebuild fields array into group structure.
	
		foreach ( $field_groups['post_meta_fields']['fields'] as $meta_key => $data ) {
			if ( empty( $this->options[ $id ][ $meta_key ] ) || ! isset( $this->options[ $id ][ $meta_key ]['crm_field'] ) || ! isset( $this->options[ $id ][ $meta_key ]['active'] ) ) {
				$this->options[ $id ][ $meta_key ] = array(
					'active'    => false,
					'crm_field' => false,
				);
			}
		}
	
		echo '<p>' . sprintf( esc_html__( 'For more information on these settings, %1$ssee our documentation%2$s.', 'wp-fusion-lite' ), '<a href="https://wpfusion.com/documentation/getting-started/syncing-contact-fields/" target="_blank">', '</a>' ) . '</p>';
		echo '<br />';
	
		// Display contact fields table.
		echo '<table id="contact-fields-table" class="table table-hover">';

		echo '<thead>';
		echo '<tr>';
		echo '<th class="sync">' . esc_html__( 'Sync', 'wp-fusion' ) . '</th>';
		echo '<th>' . esc_html__( 'Names', 'wp-fusion' ) . '</th>';
		echo '<th>' . esc_html__( 'Meta Field', 'wp-fusion' ) . '</th>';
		echo '<th>' . esc_html__( 'Type', 'wp-fusion' ) . '</th>';
		echo '<th>' . sprintf( esc_html__( '%s Field', 'wp-fusion' ), esc_html( wp_fusion()->crm->name ) ) . '</th>';
		echo '</tr>';
		echo '</thead>';
	
		foreach ( $field_groups as $group => $group_data ) {
			if ( empty( $group_data['fields'] ) ) {
				continue;
			}
	
			// Output group section headers.
			if ( empty( $group_data['title'] ) ) {
				$group_data['title'] = 'none';
			}
	
			echo '<tbody class="labels">';
			echo '<tr class="group-header"><td colspan="5">';
			echo '<label for="' . esc_attr( $group ) . '" class="group-header-title">';
			echo wp_kses_post( $group_data['title'] );
			echo '<i class="fa fa-angle-down"></i><i class="fa fa-angle-up"></i></label>';
			echo '</td></tr>';
			echo '</tbody>';
	
			echo '<tbody class="table-collapse">';
	
			foreach ( $group_data['fields'] as $meta_key => $data ) {
				echo '<tr' . ( $this->options[ $id ][ $meta_key ]['active'] == true ? ' class="success"' : '' ) . '>';
				echo '<td><input class="checkbox" type="checkbox" id="wpf_cb_' . esc_attr( $meta_key ) . '" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $meta_key ) . '][active]" value="1" ' . checked( $this->options[ $id ][ $meta_key ]['active'], 1, false ) . '/></td>';
				echo '<td class="wp_field_label">' . esc_html( $data['label'] ) . '</td>';
				echo '<td><span class="label label-default">' . esc_html( $meta_key ) . '</span></td>';
				echo '<td class="wp_field_type">';
	
				echo '<select class="wpf_type" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $meta_key ) . '][type]">';
				$field_types = array( 'text', 'date', 'multiselect', 'checkbox', 'state', 'country', 'int', 'raw', 'tel' );
				foreach ( $field_types as $type ) {
					echo '<option value="' . esc_attr( $type ) . '" ' . selected( $data['type'], $type, false ) . '>' . esc_html( $type ) . '</option>';
				}
				echo '</select>';
	
				echo '<td>';
				wpf_render_post_field_select( $this->options[ $id ][ $meta_key ]['crm_field'], 'wpf_options', $id, $meta_key, $post_type );
				echo '</td>';
	
				echo '</tr>';
			}
	
			echo '</tbody>';
		}
	
		// Add new field row.
		echo '<tr>';
		echo '<td><input class="checkbox contact-fields-checkbox" type="checkbox" disabled id="wpf_cb_new_field" name="wpf_options[' . esc_attr( $id ) . '][new_field][active]" value="1" /></td>';
		echo '<td class="wp_field_label">Add new field</td>';
		echo '<td><input type="text" id="wpf-add-new-field" name="wpf_options[' . esc_attr( $id ) . '][new_field][key]" placeholder="New Field Key" /></td>';
		echo '<td class="wp_field_type">';
	
		echo '<select class="wpf_type" name="wpf_options[' . esc_attr( $id ) . '][new_field][type]">';
		foreach ( $field_types as $type ) {
			echo '<option value="' . esc_attr( $type ) . '">' . esc_html( $type ) . '</option>';
		}
		echo '</select>';
	
		echo '<td>';
		wpf_render_post_field_select( false, 'wpf_options', $id, 'new_field', $post_type );
		echo '</td>';
	
		echo '</tr>';

		echo '</tbody>';

		echo '</table>';

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

        // Check for errors in the response
        if (isset($body['errors']) && !empty($body['errors'])) {
            $error_message = isset($body['errors'][0]['message']) ? $body['errors'][0]['message'] : 'Unknown error';
            return new WP_Error('authentication_error', __('Authentication failed: ', 'wp-fusion') . $error_message);
        }

        if (empty($body['data']['boards'][0]['columns'])) {
            return new WP_Error('no_columns_found', __('No columns found for the selected board.', 'wp-fusion'));
        }

		

        // Process the columns
        $fields = array();

        foreach ($body['data']['boards'][0]['columns'] as $column) {
            $fields[$column['id']] = $column['title'];
        }

		BugFu::log($fields);

        wp_fusion()->settings->set('post_type_fields_' . $post_type, $fields);

        return true;
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
