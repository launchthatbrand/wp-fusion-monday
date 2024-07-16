<?php

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
	 * @since   2.0
	 */

	public function __construct() {

		$this->slug     = 'monday';
		$this->name     = 'Monday';
		$this->supports = array( 'add_tags' );

		// Set up admin options
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/class-wpf-monday-admin.php';
			new WPF_Monday_Admin( $this->slug, $this->name, $this );
		}

	}

	/**
	 * Sets up hooks specific to this CRM
	 *
	 * @access public
	 * @return void
	 */

	public function init() {

		add_filter( 'wpf_crm_post_data', array( $this, 'format_post_data' ) );
		add_filter( 'wpf_format_field_value', array( $this, 'format_field_value' ), 10, 3 );

	}

	/**
	 * Formats POST data received from HTTP Posts into standard format
	 *
	 * @access public
	 * @return array
	 */

	public function format_post_data( $post_data ) {

		if ( isset( $post_data['contact_id'] ) ) {
			return $post_data;
		}

		$payload = json_decode( file_get_contents( 'php://input' ) );

		$post_data['contact_id'] = $payload->event->pulseid;

		return $post_data;

	}

	/**
	 * Formats user entered data to match Monday field formats
	 *
	 * @access public
	 * @return mixed
	 */

	public function format_field_value( $value, $field_type, $field ) {

		if ( $field_type == 'datepicker' || $field_type == 'date' ) {

			// Adjust formatting for date fields
			$date = date( 'Y-m-d', $value );

			return $date;

		} elseif ( $field_type == 'checkbox' ) {

			// Checkbox fields are either 'checked' or 'unchecked'
			if ( ! empty( $value ) ) {
				return 'checked';
			} else {
				return 'unchecked';
			}

		} else {

			return $value;

		}

	}


	/**
	 * Gets params for API calls
	 *
	 * @access  public
	 * @return  array Params
	 */

	public function get_params( $api_key = null ) {

		// Get saved data from DB
		if ( empty( $api_key ) ) {
			$api_key = wpf_get_option( 'monday_api_key' );
		}

		$this->params = array(
			'user-agent'  => 'WP Fusion; ' . home_url(),
			'timeout'     => 30,
			'httpversion' => '1.1',
			'headers'     => array(
				'Authorization' => 'Bearer ' . $api_key,
				'Content-Type'  => 'application/json',
			),
		);

		return $this->params;
	}


	/**
	 * Initialize connection
	 *
	 * @access  public
	 * @return  bool
	 */

	public function connect( $api_key = null, $test = false ) {

		if ( $test == false ) {
			return true;
		}

		if ( ! $this->params ) {
			$this->get_params( $api_key );
		}

		$request  = 'https://api.monday.com/v2';
		$response = wp_safe_remote_get( $request, $this->params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}


	/**
	 * Performs initial sync once connection is configured
	 *
	 * @access public
	 * @return bool
	 */

	public function sync() {

		if ( is_wp_error( $this->connect() ) ) {
			return false;
		}

		$this->sync_tags();
		$this->sync_crm_fields();

		do_action( 'wpf_sync' );

		return true;

	}


	/**
	 * Gets all available tags and saves them to options
	 *
	 * @access public
	 * @return array Lists
	 */

	public function sync_tags() {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$available_tags = array();

		$query = '
		{
			boards(limit:100) {
				name
				id
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! empty( $response->data->boards ) ) {

			foreach ( $response->data->boards as $board ) {
				$available_tags[ $board->id ] = $board->name;
			}

		}

		wp_fusion()->settings->set( 'available_tags', $available_tags );

		return $available_tags;
	}


	/**
	 * Loads all custom fields from CRM and merges with local list
	 *
	 * @access public
	 * @return array CRM Fields
	 */

	public function sync_crm_fields() {

		if ( ! $this->params ) {
			$this->get_params();
		}

		// Load built in fields first
		require dirname( __FILE__ ) . '/admin/monday-fields.php';

		$crm_fields = array_merge( $monday_fields, $this->get_custom_fields() );

		wp_fusion()->settings->set( 'crm_fields', $crm_fields );

		return $crm_fields;
	}


	/**
	 * Gets contact ID for a user based on email address
	 *
	 * @access public
	 * @return int Contact ID
	 */

	public function get_contact_id( $email_address ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$query = '
		query {
			items_by_column_values (board_id: 123456, column_id: "email", column_value: "' . $email_address . '") {
				id
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $response->data->items_by_column_values ) ) {
			return false;
		}

		return $response->data->items_by_column_values[0]->id;

	}


	/**
	 * Gets all tags currently applied to the user, also update the list of available tags
	 *
	 * @access public
	 * @return void
	 */

	public function get_tags( $contact_id ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$tags = array();

		$query = '
		{
			items (ids: ' . $contact_id . ') {
				board {
					id
				}
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! empty( $response->data->items ) ) {
			$tags[] = $response->data->items[0]->board->id;
		}

		return $tags;
	}

	/**
	 * Applies tags to a contact
	 *
	 * @access public
	 * @return bool
	 */

	public function apply_tags( $tags, $contact_id ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$board_id = $tags[0];

		$query = '
		mutation {
			duplicate_item (
				board_id: ' . $board_id . ',
				item_id: ' . $contact_id . ',
				with_updates: true
			) {
				id
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;

	}

	/**
	 * Removes tags from a contact
	 *
	 * @access public
	 * @return bool
	 */

	public function remove_tags( $tags, $contact_id ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$board_id = $tags[0];

		$query = '
		mutation {
			delete_item (item_id: ' . $contact_id . ') {
				id
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;

	}


	/**
	 * Adds a new contact
	 *
	 * @access public
	 * @return int Contact ID
	 */

	public function add_contact( $data ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$board_id = wpf_get_option( 'monday_users_board' );

		if ( empty( $board_id ) ) {
			return new WP_Error( 'error', 'No users board configured in the Monday CRM settings.' );
		}

		$query = '
		mutation {
			create_item (
				board_id: ' . $board_id . ',
				item_name: "' . $data['user_email'] . '",
				column_values: "' . json_encode( $data ) . '"
			) {
				id
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $response->data->create_item->id;

	}

	/**
	 * Update contact
	 *
	 * @access public
	 * @return bool
	 */

	public function update_contact( $contact_id, $data ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$query = '
		mutation {
			change_multiple_column_values (
				item_id: ' . $contact_id . ',
				board_id: 123456,
				column_values: "' . json_encode( $data ) . '"
			) {
				id
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}

	/**
	 * Loads a contact and updates local user meta
	 *
	 * @access public
	 * @return array User meta data that was returned
	 */

	public function load_contact( $contact_id ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$query = '
		{
			items (ids: ' . $contact_id . ') {
				column_values {
					id
					value
					text
				}
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$user_meta      = array();
		$contact_fields = wpf_get_option( 'contact_fields' );
		$response       = json_decode( wp_remote_retrieve_body( $response ) );

		foreach ( $response->data->items[0]->column_values as $field ) {

			$key = array_search( $field->id,  $contact_fields );

			if ( $key !== false ) {
				$user_meta[ $key ] = $field->text;
			}

		}

		return $user_meta;
	}


	/**
	 * Gets a list of contact IDs based on tag
	 *
	 * @access public
	 * @return array Contact IDs returned
	 */

	public function load_contacts( $tag ) {

		if ( ! $this->params ) {
			$this->get_params();
		}

		$contact_ids = array();

		$query = '
		{
			boards (ids: ' . $tag . ') {
				items {
					id
				}
			}
		}';

		$request        = 'https://api.monday.com/v2';
		$params         = $this->params;
		$params['body'] = json_encode( array( 'query' => $query ) );

		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		foreach ( $response->data->boards[0]->items as $item ) {
			$contact_ids[] = $item->id;
		}

		return $contact_ids;

	}

	/**
	 * Track event.
	 *
	 * Track an event with the Monday API.
	 *
	 * @since  3.38.16
	 *
	 * @param  string      $event      The event title.
	 * @param  bool|string $event_data The event description.
	 * @param  int         $user_id    The user ID.
	 * @return bool|WP_Error True if success, WP_Error if failed.
	 */
	public function track_event( $event, $event_data = false, $user_id = false ) {

		// Monday doesn't support event tracking
		return true;

	}

}
