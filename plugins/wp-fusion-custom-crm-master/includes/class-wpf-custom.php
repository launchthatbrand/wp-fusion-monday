<?php

/**
 * WP Fusion Custom CRM class.
 *
 * @since x.x.x
 */
class WPF_Custom {

	/**
	 * Contains API params
	 */

	 public $params;

	/**
	 * The CRM slug.
	 *
	 * @var string
	 * @since x.x.x
	 */

	public $slug = 'custom';

	/**
	 * The CRM name.
	 *
	 * @var string
	 * @since x.x.x
	 */

	public $name = 'Custom';

	/**
	 * Contains API url
	 *
	 * @var string
	 * @since x.x.x
	 */

	public $url = 'https://myapi.com';

	/**
	 * Monday OAuth stuff
	 */
 
	 public $api_domain;

	/**
	 * Declares how this CRM handles tags and fields.
	 *
	 * "add_tags" means that tags are applied over the API as strings (no tag IDs).
	 * With add_tags enabled, WP Fusion will allow users to type new tag names into the tag select boxes.
	 *
	 * "add_tags_api" means that tags can be created via an API call. Uses the add_tag() method.
	 *
	 * "lists" means contacts can be added to lists in addition to tags. Requires the sync_lists() method.
	 *
	 * "add_fields" means that custom field / attrubute keys don't need to exist first in the CRM to be used.
	 * With add_fields enabled, WP Fusion will allow users to type new filed names into the CRM Field select boxes.
	 *
	 * "events" enables the integration for Event Tracking: https://wpfusion.com/documentation/event-tracking/event-tracking-overview/.
	 *
	 * "events_multi_key" enables the integration for Event Tracking with multiple keys: https://wpfusion.com/documentation/event-tracking/event-tracking-overview/#multi-key-events.
	 *
	 * @var array<string>
	 * @since x.x.x
	 */

	public $supports = array(
		'add_tags',
		'add_tags_api',
		'lists',
		'add_fields',
		'events_multi_key',
	);

	/**
	 * Lets us link directly to editing a contact record in the CRM.
	 *
	 * @var string
	 * @since x.x.x
	 */
	public $edit_url = '';


	/**
	 * Client ID for OAuth (if applicable).
	 *
	 * @var string
	 * @since x.x.x
	 */
	public $client_id = '959bd865-5a24-4a43-a8bf-05a69c537938';

	/**
	 * Client secret for OAuth (if applicable).
	 *
	 * @var string
	 * @since x.x.x
	 */
	public $client_secret = '56cc5735-c274-4e43-99d4-3660d816a624';

	/**
	 * Authorization URL for OAuth (if applicable).
	 *
	 * @var string
	 * @since x.x.x
	 */
	public $auth_url = 'https://mycrm.com/oauth/authorize';

	/**
	 * Get things started.
	 *
	 * @since x.x.x
	 */
	public function __construct() {

		// Set up admin options.
		if ( is_admin() ) {
			require_once __DIR__ . '/class-wpf-custom-admin.php';
			new WPF_Custom_Admin( $this->slug, $this->name, $this );
		}

		// Error handling.
		add_filter( 'http_response', array( $this, 'handle_http_response' ), 50, 3 );
	}

	/**
	 * Sets up hooks specific to this CRM.
	 *
	 * This function only runs if this CRM is the active CRM.
	 *
	 * @since x.x.x
	 */
	public function init() {

		add_filter( 'wpf_format_field_value', array( $this, 'format_field_value' ), 10, 3 );
		add_filter( 'wpf_crm_post_data', array( $this, 'format_post_data' ) );

		// Allows for linking directly to contact records in the CRM.
		$this->edit_url = trailingslashit( wp_fusion()->settings->get( 'custom_url' ) ) . 'app/contacts/%d/';

		// Sets the base URL for API calls.
		$this->url = wpf_get_option( 'custom_url' );
	}


	/**
	 * Format field value.
	 *
	 * Formats outgoing data to match CRM field formats. This will vary
	 * depending on the data formats accepted by the CRM.
	 *
	 * @since  x.x.x
	 *
	 * @link https://wpfusion.com/documentation/getting-started/syncing-contact-fields/#field-types
	 *
	 * @param  mixed  $value      The value.
	 * @param  string $field_type The field type ('text', 'date', 'multiselect', 'checkbox').
	 * @param  string $field      The CRM field identifier.
	 * @return mixed  The field value.
	 */
	public function format_field_value( $value, $field_type, $field ) {

		if ( 'date' === $field_type && ! empty( $value ) ) {

			// Dates come in as a timestamp.

			$date = gmdate( 'm/d/Y H:i:s', $value );

			return $date;

		} elseif ( is_array( $value ) ) {

			return implode( ', ', array_filter( $value ) );

		} elseif ( 'multiselect' === $field_type && empty( $value ) ) {

			$value = null;

		} else {

			return $value;

		}
	}


	/**
	 * Formats post data.
	 *
	 * This runs when a webhook is received and extracts the contact ID (and optionally
	 * tags) from the webhook payload.
	 *
	 * @since  x.x.x
	 *
	 * @link https://wpfusion.com/documentation/webhooks/about-webhooks/
	 *
	 * @param  array $post_data The post data.
	 * @return array $post_data The formatted post data.
	 */
	public function format_post_data( $post_data ) {

		$payload = json_decode( file_get_contents( 'php://input' ) );

		if ( ! empty( $payload ) ) {

			$post_data['contact_id'] = $payload->contact->id; // the contact ID is required.

			// You can optionally POST an array of tags to the update or update_tags endpoints.
			// If you do, WP Fusion will skip the API call to load the tags and instead save
			// them directly from the payload to the user's meta.
			$post_data['tags'] = wp_list_pluck( $payload->contact->tags, 'name' );
		}

		return $post_data;
	}

	/**
	 * Gets params for API calls.
	 *
	 * @since x.x.x
	 *
	 * @param string $api_key The API key.
	 * @return array<string|mixed> $params The API parameters.
	 */
	public function get_params( $api_key = null ) {

		// Get saved data from DB.
		if ( ! $api_key ) {
			$api_key = wpf_get_option( 'custom_key' );
		}

		$this->api_domain = wpf_get_option( 'custom_url' );

		$params = array(
			'user-agent' => 'WP Fusion; ' . home_url(),
			'timeout'    => 15,
			'headers'    => array(
				'Authorization' => 'Bearer ' . $api_key,
			),
		);

		return $params;
	}

	/**
	 * Refresh an access token from a refresh token. Remove if not using OAuth.
	 *
	 * @since x.x.x
	 *
	 * @return string|WP_Error An access token or error.
	 */
	public function refresh_token() {

		$refresh_token = wpf_get_option( "{$this->slug}_refresh_token" );

		$params = array(
			'user-agent' => 'WP Fusion; ' . home_url(),
			'headers'    => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
			'body'       => array(
				'grant_type'    => 'refresh_token',
				'client_id'     => $this->client_id,
				'client_secret' => $this->client_secret,
				'redirect_uri'  => "https://wpfusion.com/oauth/?action=wpf_get_{$this->slug}_token",
				'refresh_token' => $refresh_token,
			),
		);

		$response = wp_safe_remote_post( $this->auth_url, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body_json = json_decode( wp_remote_retrieve_body( $response ) );

		$this->get_params( $body_json->access_token );

		wp_fusion()->settings->set( "{$this->slug}_token", $body_json->access_token );

		return $body_json->access_token;
	}

	/**
	 * Gets the default fields.
	 *
	 * @since x.x.x
	 *
	 * @return array<string, array> The default fields in the CRM.
	 */
	public static function get_default_fields() {

		return array(
			'first_name'     => array(
				'crm_label' => 'First Name',
				'crm_field' => 'f_name',
			),
			'last_name'      => array(
				'crm_label' => 'Last Name',
				'crm_field' => 'l_name',
			),
			'user_email'     => array(
				'crm_label' => 'Email',
				'crm_field' => 'email',
				'crm_type'  => 'email',
			),
		);
	}


	/**
	 * Check HTTP Response for errors and return WP_Error if found.
	 *
	 * @since x.x.x
	 *
	 * @param  array  $response The HTTP response.
	 * @param  array  $args     The HTTP request arguments.
	 * @param  string $url      The HTTP request URL.
	 * @return array|WP_Error The response or WP_Error on error.
	 */
	public function handle_http_response( $response, $args, $url ) {

		if ( strpos( $url, $this->url ) !== false && 'WP Fusion; ' . home_url() === $args['user-agent'] ) { // check if the request came from us.

			$body_json     = json_decode( wp_remote_retrieve_body( $response ) );
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( 401 === $response_code ) {

				// Handle refreshing an OAuth token. Remove if not using OAuth.

				if ( strpos( $body_json->message, 'expired' ) !== false ) {

					$access_token = $this->refresh_token();

					if ( is_wp_error( $access_token ) ) {
						return $access_token;
					}

					$args['headers']['Authorization'] = 'Bearer ' . $access_token;

					$response = wp_safe_remote_request( $url, $args );

				} else {

					$response = new WP_Error( 'error', 'Invalid API credentials.' );

				}
			} elseif ( isset( $body_json->success ) && false === (bool) $body_json->success && isset( $body_json->message ) ) {

				$response = new WP_Error( 'error', $body_json->message );

			} elseif ( 500 === $response_code ) {

				$response = new WP_Error( 'error', __( 'An error has occurred in API server. [error 500]', 'wp-fusion' ) );

			}
		}

		return $response;
	}


	/**
	 * Initialize connection.
	 *
	 * This is run during the setup process to validate that the user has
	 * entered the correct API credentials.
	 *
	 * @since  x.x.x
	 *
	 * @param  string $api_url The first API credential.
	 * @param  string $api_key The second API credential.
	 * @param  bool   $test    Whether to validate the credentials.
	 * @return bool|WP_Error A WP_Error will be returned if the API credentials are invalid.
	 */
	public function connect( $api_url = null, $api_key = null, $test = false ) {

		if ( false === $test ) {
			return true;
		}

		$request  = $api_url . '/endpoint/';
		$response = wp_safe_remote_get( $request, $this->get_params( $api_key ) );

		// Validate the connection.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}


	/**
	 * Performs initial sync once connection is configured.
	 *
	 * @since x.x.x
	 *
	 * @return bool
	 */
	public function sync() {
		// Check if a board has been selected
		$selected_board = wp_fusion()->settings->get( 'monday_board' );
		if ( empty( $selected_board ) ) {
			return; // Skip sync if no board is selected
		}
	
		// Proceed with the sync operation if a board is selected
		$this->sync_tags();
		$this->sync_lists(); // if $this->supports( 'lists' );
		$this->sync_crm_fields();
	
		do_action( 'wpf_sync' );
	
		return true;
	}


	/**
	 * Gets all available tags and saves them to options.
	 *
	 * @since  x.x.x
	 *
	 * @return array|WP_Error Either the available tags in the CRM, or a WP_Error.
	 */
	public function sync_tags() {
		// Ensure the API key is available
		$api_key = wpf_get_option('custom_key');
	
		if ( empty($api_key) ) {
			return new WP_Error('missing_api_key', __('API key is missing.', 'wp-fusion'));
		}
	
		// Prepare the GraphQL query
		$query = '{"query": "{ tags { id name } }"}';
	
		// Make the request to the Monday.com API
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
		if ( is_wp_error( $response ) ) {
			error_log('API request error: ' . $response->get_error_message());
			return $response;
		}
	
		$body = wp_remote_retrieve_body( $response );
		error_log('API response body: ' . $body);
	
		$body_json = json_decode( $body, true );
	
		// Check if the body or data is null or empty
		if ( is_null( $body_json ) || !isset( $body_json['data'] ) ) {
			return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		}
	
		// Check for errors in the response
		if ( isset($body_json['errors']) && !empty($body_json['errors']) ) {
			$error_message = isset($body_json['errors'][0]['message']) ? $body_json['errors'][0]['message'] : 'Unknown error';
			return new WP_Error('api_error', __('API error: ', 'wp-fusion') . $error_message);
		}
	
		// Process the tags
		$available_tags = array();
	
		if ( !empty($body_json['data']['tags']) ) {
			foreach ( $body_json['data']['tags'] as $tag ) {
				$available_tags[$tag['id']] = sanitize_text_field($tag['name']);
			}
		}
	
		asort($available_tags);
	
		wp_fusion()->settings->set('available_tags', $available_tags);
	
		return $available_tags;
	}
	


	/**
	 * Gets all available lists and saves them to options.
	 *
	 * @since  x.x.x
	 *
	 * @return array|WP_Error Either the available lists in the CRM, or a WP_Error.
	 */
	public function sync_lists() {

		$request  = $this->url . '/endpoint/';
		$response = wp_safe_remote_get( $request, $this->get_params() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		$available_lists = array();

		// Load available lists into $available_lists like 'list_id' => 'list Label'.
		if ( ! empty( $response->lists ) ) {

			foreach ( $response->lists as $list ) {

				$list_id                    = (int) $list->id;
				$available_lists[ $list_id ] = sanitize_text_field( $list->label );
			}
		}

		wp_fusion()->settings->set( 'available_lists', $available_lists );

		return $available_lists;
	}

	/**
	 * Loads all custom fields from CRM and merges with local list.
	 *
	 * @since  x.x.x
	 *
	 * @return array|WP_Error Either the available fields in the CRM, or a WP_Error.
	 */
	public function sync_crm_fields() {

		// Fetch the API key
		$api_key = wpf_get_option('custom_key');
		if (empty($api_key)) {
			return new WP_Error('no_api_key', __('No API key provided.', 'wp-fusion'));
		}
	
		// Ensure a board is selected
		$selected_board = wp_fusion()->settings->get('monday_board');
		if (empty($selected_board)) {
			return new WP_Error('no_board_selected', __('No board selected.', 'wp-fusion'));
		}
	
		// Prepare the GraphQL query
		$query = '{"query": "{ boards (ids: [' . $selected_board . ']) { columns { id title } } }"}';
	
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
		$built_in_fields = array();
		$custom_fields   = array();
	
		foreach ($body['data']['boards'][0]['columns'] as $column) {
			// Assuming all columns are custom fields in Monday.com
			$custom_fields[$column['id']] = $column['title'];
		}
	
		asort($built_in_fields);
		asort($custom_fields);
	
		$crm_fields = array(
			'Standard Fields' => $built_in_fields,
			'Custom Fields'   => $custom_fields,
		);
	
		wp_fusion()->settings->set('crm_fields', $crm_fields);
	
		return $crm_fields;
	}
	
	
	


	/**
	 * Gets contact ID for a user based on email address.
	 *
	 * @since  x.x.x
	 *
	 * @param  string $email_address The email address to look up.
	 * @return int|WP_Error The contact ID in the CRM.
	 */
	public function get_contact_id( $email_address ) {
		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('custom_key');
		$board_id = wpf_get_option('monday_board');
	
		if ( empty($api_key) || empty($board_id) ) {
			return new WP_Error('missing_api_key_or_board_id', __('API key or board ID is missing.', 'wp-fusion'));
		}
	
		// Prepare the GraphQL query
		$query = '{
			items_page_by_column_values (limit: 50, board_id: ' . $board_id . ', columns: [{column_id: "email", column_values: ["' . $email_address . '"]}]) {
				items {
					id
					name
					column_values(ids: "email") {
						text
					}
				}
			}
		}';
	
		// Make the request to the Monday.com API
		$response = wp_safe_remote_post(
			'https://api.monday.com/v2',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(array('query' => $query)),
			)
		);
	
		// Handle the response
		if ( is_wp_error( $response ) ) {
			return $response;
		}
	
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
	
		if ( empty( $body['data']['items_page_by_column_values']['items'] ) ) {
			return new WP_Error('no_items_found', __('No items found for the given email address.', 'wp-fusion'));
		}
	
		// Assuming the first item returned is the desired contact
		$contact_id = $body['data']['items_page_by_column_values']['items'][0]['id'];
	
		return (int) $contact_id;
	}

	/**
	 * Creates a new tag and returns the ID.
	 *
	 * Requires add_tags_api to be enabled in $this->supports.
	 *
	 * @since  x.x.x
	 *
	 * @param  string       $tag_name The tag name.
	 * @return int|WP_Error $tag_id   The tag id returned from API or WP Error.
	 */
	public function add_tag( $tag_name ) {

		$params = $this->get_params();

		$data = array(
			'name' => $tag_name,
		);

		$params['body'] = wp_json_encode( $data );

		$request  = $this->url . '/tags';
		$response = wp_safe_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $response->id;
	}


	/**
	 * Gets all tags currently applied to the contact in the CRM.
	 *
	 * @since x.x.x
	 *
	 * @param int $contact_id The contact ID to load the tags for.
	 * @return array|WP_Error The tags currently applied to the contact in the CRM.
	 */
	public function get_tags() {
		// Ensure the API key is available
		$api_key = wpf_get_option('custom_key');
	
		if ( empty($api_key) ) {
			return new WP_Error('missing_api_key', __('API key is missing.', 'wp-fusion'));
		}
	
		// Prepare the GraphQL query to fetch tags
		$query = '{"query": "{ tags { id name } }"}';
	
		// Make the request to the Monday.com API
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
		if ( is_wp_error( $response ) ) {
			error_log('API request error: ' . $response->get_error_message());
			return $response;
		}
	
		$body = wp_remote_retrieve_body( $response );
		error_log('API response body: ' . $body);
	
		$body_json = json_decode( $body, true );
	
		// Check if the body or data is null or empty
		if ( is_null( $body_json ) || !isset( $body_json['data'] ) ) {
			return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		}
	
		// Check for errors in the response
		if ( isset($body_json['errors']) && !empty($body_json['errors']) ) {
			$error_message = isset($body_json['errors'][0]['message']) ? $body_json['errors'][0]['message'] : 'Unknown error';
			return new WP_Error('api_error', __('API error: ', 'wp-fusion') . $error_message);
		}
	
		if ( empty($body_json['data']['tags']) ) {
			return array();
		}
	
		// Parse response to create an array of tag ids
		$tags = array();
		$available_tags = array();
	
		foreach ( $body_json['data']['tags'] as $tag ) {
			$tags[] = $tag['id'];
			$available_tags[$tag['name']] = $tag['name'];
		}
	
		asort( $available_tags );
	
		// Update available tags list
		wp_fusion()->settings->set('available_tags', $available_tags);
	
		return $tags;
	}

	/**
	 * Applies tags to a contact.
	 *
	 * @since x.x.x
	 *
	 * @param array $tags       A numeric array of tags to apply to the contact.
	 * @param int   $contact_id The contact ID to apply the tags to.
	 * @return bool|WP_Error Either true, or a WP_Error if the API call failed.
	 */
	public function apply_tags( $tags, $contact_id ) {
		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('custom_key');
		$board_id = wpf_get_option('monday_board');
	
		if ( empty($api_key) || empty($board_id) ) {
			return new WP_Error('missing_api_key_or_board_id', __('API key or board ID is missing.', 'wp-fusion'));
		}
	
		// Prepare the tag IDs array
		$tag_ids = array();
		foreach ( $tags as $tag ) {
			$tag_ids[] = $tag; // Assuming $tags contains tag IDs. Adjust if necessary.
		}
	
		// Prepare the column values in JSON format
		$column_values = array(
			'tags' => array(
				'tag_ids' => $tag_ids
			)
		);
	
		$column_values_json = json_encode( $column_values, JSON_UNESCAPED_SLASHES );
	
		// Prepare the GraphQL mutation
		$mutation = 'mutation {
			change_multiple_column_values(item_id: ' . $contact_id . ', board_id: ' . $board_id . ', column_values: "' . addslashes( $column_values_json ) . '") {
				id
			}
		}';
	
		// Log the mutation for debugging
		error_log('GraphQL Mutation: ' . $mutation);
	
		// Make the request to the Monday.com API
		$response = wp_safe_remote_post(
			'https://api.monday.com/v2',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(array('query' => $mutation)),
			)
		);
	
		// Handle the response
		if ( is_wp_error( $response ) ) {
			error_log('API request error: ' . $response->get_error_message());
			return $response;
		}
	
		$body = wp_remote_retrieve_body( $response );
		error_log('API response body: ' . $body);
	
		$body_json = json_decode( $body, true );
	
		// Check if the body or data is null or empty
		if ( is_null( $body_json ) || !isset( $body_json['data'] ) ) {
			return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		}
	
		// Check for errors in the response
		if ( isset($body_json['errors']) && !empty($body_json['errors']) ) {
			$error_message = isset($body_json['errors'][0]['message']) ? $body_json['errors'][0]['message'] : 'Unknown error';
			return new WP_Error('api_error', __('API error: ', 'wp-fusion') . $error_message);
		}
	
		// Ensure the expected data structure is present
		if ( !isset( $body_json['data']['change_multiple_column_values']['id'] ) ) {
			return new WP_Error('api_error', __('API error: Missing contact ID in response', 'wp-fusion'));
		}
	
		return true;
	}
	

	/**
	 * Removes tags from a contact.
	 *
	 * @since  x.x.x
	 *
	 * @param  array $tags       A numeric array of tags to remove from the contact.
	 * @param  int   $contact_id The contact ID to remove the tags from.
	 * @return bool|WP_Error Either true, or a WP_Error if the API call failed.
	 */
	public function remove_tags( $tags, $contact_id ) {
		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('custom_key');
		$board_id = wpf_get_option('monday_board');
	
		if ( empty($api_key) || empty($board_id) ) {
			return new WP_Error('missing_api_key_or_board_id', __('API key or board ID is missing.', 'wp-fusion'));
		}
	
		// Fetch the current tags for the item
		$query = '{
			items (ids: [' . $contact_id . ']) {
				column_values {
					... on TagsValue {
						tag_ids
						text
					}
				}
			}
		}';
	
		// Log the query for debugging
		error_log('GraphQL Query: ' . $query);
	
		$response = wp_safe_remote_post(
			'https://api.monday.com/v2',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(array('query' => $query)),
			)
		);
	
		if ( is_wp_error( $response ) ) {
			error_log('API request error: ' . $response->get_error_message());
			return $response;
		}
	
		$body = wp_remote_retrieve_body( $response );
		error_log('API response body: ' . $body);
	
		$body_json = json_decode( $body, true );
	
		if ( is_null( $body_json ) || !isset( $body_json['data']['items'][0]['column_values'][0]['tag_ids'] ) ) {
			return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		}
	
		// Extract the current tag IDs
		$current_tags = $body_json['data']['items'][0]['column_values'][0]['tag_ids'];
		error_log('Current Tags: ' . $current_tags);
	
		// Remove the specified tags
		$new_tags = array_diff( $current_tags, $tags );
		error_log('New Tags: ' . $new_tags);
	
		// Prepare the column values in JSON format
		$column_values = array(
			'tags' => array(
				'tag_ids' => array_values($new_tags) // Ensure the array is re-indexed
			)
		);
	
		$column_values_json = json_encode( $column_values, JSON_UNESCAPED_SLASHES );
	
		// Prepare the GraphQL mutation to update the item
		$mutation = 'mutation {
			change_multiple_column_values(item_id: ' . $contact_id . ', board_id: ' . $board_id . ', column_values: "' . addslashes( $column_values_json ) . '") {
				id
			}
		}';
	
		// Log the mutation for debugging
		error_log('GraphQL Mutation: ' . $mutation);
	
		// Make the request to update the item
		$response = wp_safe_remote_post(
			'https://api.monday.com/v2',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(array('query' => $mutation)),
			)
		);
	
		if ( is_wp_error( $response ) ) {
			error_log('API request error: ' . $response->get_error_message());
			return $response;
		}
	
		$body = wp_remote_retrieve_body( $response );
		error_log('API response body: ' . $body);
	
		$body_json = json_decode( $body, true );
	
		if ( is_null( $body_json ) || !isset( $body_json['data']['change_multiple_column_values']['id'] ) ) {
			return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		}
	
		return true;
	}
	
	


	/**
	 * Adds a new contact.
	 *
	 * @since x.x.x
	 *
	 * @param array $contact_data An associative array of contact fields and field values.
	 * @return int|WP_Error Contact ID on success, or WP Error.
	 */
	// public function add_contact( $contact_data, $map_meta_fields = true ) {
	// 	// Ensure the API key and board ID are available
	// 	$api_key = wpf_get_option('custom_key');
	// 	$board_id = wpf_get_option('monday_board');
	
	// 	if ( empty($api_key) || empty($board_id) ) {
	// 		return new WP_Error('missing_api_key_or_board_id', __('API key or board ID is missing.', 'wp-fusion'));
	// 	}
	
	// 	// If set to true, WP Fusion will convert the field keys from WordPress meta keys into the field names in the CRM.
	// 	if ( $map_meta_fields ) {
	// 		$contact_data = wp_fusion()->crm_base->map_meta_fields( $contact_data );
	// 	}
	
	// 	// Prepare the column values in JSON format
	// 	$column_values_json = json_encode( $contact_data );
	
	// 	// Prepare the GraphQL mutation
	// 	$mutation = 'mutation {
	// 		create_item (board_id: ' . $board_id . ', item_name: "' . esc_js( $contact_data['name'] ) . '", column_values: \'' . addslashes( $column_values_json ) . '\') {
	// 			id
	// 		}
	// 	}';
	
	// 	// Log the mutation for debugging
	// 	error_log('GraphQL Mutation: ' . $mutation);
	
	// 	// Make the request to the Monday.com API
	// 	$response = wp_safe_remote_post(
	// 		'https://api.monday.com/v2',
	// 		array(
	// 			'method'  => 'POST',
	// 			'headers' => array(
	// 				'Authorization' => $api_key,
	// 				'Content-Type'  => 'application/json',
	// 			),
	// 			'body'    => wp_json_encode(array('query' => $mutation)),
	// 		)
	// 	);
	
	// 	// Handle the response
	// 	if ( is_wp_error( $response ) ) {
	// 		error_log('API request error: ' . $response->get_error_message());
	// 		return $response;
	// 	}
	
	// 	$body = wp_remote_retrieve_body( $response );
	// 	error_log('API response body: ' . $body);
	
	// 	$body_json = json_decode( $body, true );
	
	// 	// Check if the body or data is null or empty
	// 	if ( is_null( $body_json ) || !isset( $body_json['data'] ) ) {
	// 		return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
	// 	}
	
	// 	// Check for errors in the response
	// 	if ( isset($body_json['errors']) && !empty($body_json['errors']) ) {
	// 		$error_message = isset($body_json['errors'][0]['message']) ? $body_json['errors'][0]['message'] : 'Unknown error';
	// 		return new WP_Error('api_error', __('API error: ', 'wp-fusion') . $error_message);
	// 	}
	
	// 	// Ensure the expected data structure is present
	// 	if ( !isset( $body_json['data']['create_item']['id'] ) ) {
	// 		return new WP_Error('api_error', __('API error: Missing contact ID in response', 'wp-fusion'));
	// 	}
	
	// 	// Get new contact ID out of response
	// 	return $body_json['data']['create_item']['id'];
	// }

	public function add_contact( $contact_data, $map_meta_fields = true ) {
		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('custom_key');
		$board_id = wpf_get_option('monday_board');
	
		if ( empty($api_key) || empty($board_id) ) {
			return new WP_Error('missing_api_key_or_board_id', __('API key or board ID is missing.', 'wp-fusion'));
		}
	
		// If set to true, WP Fusion will convert the field keys from WordPress meta keys into the field names in the CRM.
		if ( $map_meta_fields ) {
			$contact_data = wp_fusion()->crm_base->map_meta_fields( $contact_data );
		}
	
		// Prepare the column values in JSON format dynamically
		$column_values = array();
		foreach ( $contact_data as $key => $value ) {
			if ( $key === 'email' ) {
				$column_values[$key] = array(
					'email' => $value,
					'text' => $value
				);
			} else {
				$column_values[$key] = $value;
			}
		}
	
		$column_values_json = json_encode( $column_values, JSON_UNESCAPED_SLASHES );
	
		// Prepare the GraphQL mutation
		$mutation = 'mutation {
			create_item (board_id: ' . $board_id . ', item_name: "' . esc_js( $contact_data['name'] ) . '", column_values: "' . addslashes( $column_values_json ) . '") {
				id
			}
		}';
	
		// Log the mutation for debugging
		error_log('GraphQL Mutation: ' . $mutation);
	
		// Make the request to the Monday.com API
		$response = wp_safe_remote_post(
			'https://api.monday.com/v2',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(array('query' => $mutation)),
			)
		);
	
		// Handle the response
		if ( is_wp_error( $response ) ) {
			error_log('API request error: ' . $response->get_error_message());
			return $response;
		}
	
		$body = wp_remote_retrieve_body( $response );
		error_log('API response body: ' . $body);
	
		$body_json = json_decode( $body, true );
	
		// Check if the body or data is null or empty
		if ( is_null( $body_json ) || !isset( $body_json['data'] ) ) {
			return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		}
	
		// Check for errors in the response
		if ( isset($body_json['errors']) && !empty($body_json['errors']) ) {
			$error_message = isset($body_json['errors'][0]['message']) ? $body_json['errors'][0]['message'] : 'Unknown error';
			return new WP_Error('api_error', __('API error: ', 'wp-fusion') . $error_message);
		}
	
		// Ensure the expected data structure is present
		if ( !isset( $body_json['data']['create_item']['id'] ) ) {
			return new WP_Error('api_error', __('API error: Missing contact ID in response', 'wp-fusion'));
		}
	
		// Get new contact ID out of response
		return $body_json['data']['create_item']['id'];
	}
	
	
	
	
	
	
	
	
	
	

	/**
	 * Updates an existing contact record.
	 *
	 * @since x.x.x
	 *
	 * @param int   $contact_id   The ID of the contact to update.
	 * @param array $contact_data An associative array of contact fields and field values.
	 * @return bool|WP_Error Error if the API call failed.
	 */
	public function update_contact( $contact_id, $contact_data, $map_meta_fields = true ) {
		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('custom_key');
		$board_id = wpf_get_option('monday_board');
	
		if ( empty($api_key) || empty($board_id) ) {
			return new WP_Error('missing_api_key_or_board_id', __('API key or board ID is missing.', 'wp-fusion'));
		}
	
		// If set to true, WP Fusion will convert the field keys from WordPress meta keys into the field names in the CRM.
		if ( $map_meta_fields ) {
			$contact_data = wp_fusion()->crm_base->map_meta_fields( $contact_data );
		}
	
		// Prepare the column values in JSON format dynamically
		$column_values = array();
		foreach ( $contact_data as $key => $value ) {
			if ( $key === 'email' ) {
				$column_values[$key] = array(
					'email' => $value,
					'text' => $value
				);
			} else {
				$column_values[$key] = $value;
			}
		}
	
		$column_values_json = json_encode( $column_values, JSON_UNESCAPED_SLASHES );
	
		// Prepare the GraphQL mutation
		$mutation = 'mutation {
			change_multiple_column_values (board_id: ' . $board_id . ', item_id: ' . $contact_id . ', column_values: "' . addslashes( $column_values_json ) . '") {
				id
			}
		}';
	
		// Log the mutation for debugging
		error_log('GraphQL Mutation: ' . $mutation);
	
		// Make the request to the Monday.com API
		$response = wp_safe_remote_post(
			'https://api.monday.com/v2',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(array('query' => $mutation)),
			)
		);
	
		// Handle the response
		if ( is_wp_error( $response ) ) {
			error_log('API request error: ' . $response->get_error_message());
			return $response;
		}
	
		$body = wp_remote_retrieve_body( $response );
		error_log('API response body: ' . $body);
	
		$body_json = json_decode( $body, true );
	
		// Check if the body or data is null or empty
		if ( is_null( $body_json ) || !isset( $body_json['data'] ) ) {
			return new WP_Error('api_error', __('API error: Invalid response', 'wp-fusion'));
		}
	
		// Check for errors in the response
		if ( isset($body_json['errors']) && !empty($body_json['errors']) ) {
			$error_message = isset($body_json['errors'][0]['message']) ? $body_json['errors'][0]['message'] : 'Unknown error';
			return new WP_Error('api_error', __('API error: ', 'wp-fusion') . $error_message);
		}
	
		// Ensure the expected data structure is present
		if ( !isset( $body_json['data']['change_multiple_column_values']['id'] ) ) {
			return new WP_Error('api_error', __('API error: Missing contact ID in response', 'wp-fusion'));
		}
	
		return true;
	}
	

	/**
	 * Loads a contact record from the CRM and maps CRM fields to WordPress fields
	 *
	 * @since x.x.x
	 *
	 * @param int $contact_id The ID of the contact to load.
	 * @return array|WP_Error User meta data that was returned.
	 */
	public function load_contact( $contact_id ) {

		$request  = $this->url . '/endpoint/' . $contact_id;
		$response = wp_safe_remote_get( $request, $this->get_params() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$user_meta      = array();
		$contact_fields = wp_fusion()->settings->get( 'contact_fields' );
		$response       = json_decode( wp_remote_retrieve_body( $response ), true );

		foreach ( $contact_fields as $field_id => $field_data ) {

			if ( $field_data['active'] && isset( $response['data'][ $field_data['crm_field'] ] ) ) {
				$user_meta[ $field_id ] = $response['data'][ $field_data['crm_field'] ];
			}
		}

		return $user_meta;
	}


	/**
	 * Gets a list of contact IDs based on tag
	 *
	 * @since x.x.x
	 *
	 * @param string $tag The tag ID or name to search for.
	 * @return array|WP_Error Contact IDs returned or error.
	 */
	public function load_contacts( $tag ) {

		$request  = $this->url . '/endpoint/tag/' . $tag;
		$response = wp_safe_remote_get( $request, $this->get_params() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$contact_ids = array();
		$response    = json_decode( wp_remote_retrieve_body( $response ) );

		// Iterate over the contacts returned in the response and build an array such that $contact_ids = array(1,3,5,67,890);.
		foreach ( $response as $contact ) {
			$contact_ids[] = $contact->id;
		}

		return $contact_ids;
	}

	/**
	 * Track event.
	 *
	 * Track an event with the AC site tracking API.
	 *
	 * @since  x.x.x
	 *
	 * @link   https://wpfusion.com/documentation/event-tracking/event-tracking-overview/
	 *
	 * @param  string      $event         The event title.
	 * @param  array       $event_data    The event data (associative array).
	 * @param  bool|string $email_address The user email address.
	 * @return bool|WP_Error True if success, WP_Error if failed.
	 */
	public function track_event( $event, $event_data = array(), $email_address = false ) {

		// Get the email address to track.

		if ( empty( $email_address ) ) {
			$email_address = wpf_get_current_user_email();
		}

		if ( false === $email_address ) {
			return false; // can't track without an email.
		}

		$data = array(
			'email'       => $email_address,
			'event_name'  => $event,
			'event_value' => $event_data,
		);

		$params             = $this->get_params();
		$params['body']     = wp_json_encode( $data );
		$params['blocking'] = false; // we don't need to wait for a response.

		$response = wp_safe_remote_post( $this->url . '/track-event/', $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}
}
