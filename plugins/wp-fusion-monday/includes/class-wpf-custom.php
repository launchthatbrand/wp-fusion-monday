<?php

class WPF_Monday {

	/**
	 * The CRM slug.
	 *
	 * @var string
	 */
	public $slug = 'monday';

	/**
	 * The CRM name.
	 *
	 * @var string
	 */
	public $name = 'Monday';

	/**
	 * Allows for direct access to the API, bypassing WP Fusion
	 */

	public $app;

	/**
	 * Lets pluggable functions know which features are supported by the CRM
	 */

	public $supports = array( 'add_tags', 'lists', 'events' );

	/**
	 * HTTP API parameters
	 */

	public $params;

	/**
	 * API url for the account
	 */

	public $api_url;

	/**
	 * API key for the account
	 */

	public $api_key;

	/**
	 * Lets us link directly to editing a contact record.
	 *
	 * @var string
	 * @since 3.36.5
	 */

	public $edit_url = '';

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.0
	 */

	public function __construct() {

		$this->api_url = trailingslashit( wpf_get_option( 'monday_url' ) );

		if ( is_admin() ) {
			require_once __DIR__ . '/class-admin.php';
			new WPF_Monday_Admin( $this->slug, $this->name, $this );
		}

		add_filter( 'http_response', array( $this, 'handle_http_response' ), 50, 3 );
	}

	/**
	 * Sets up hooks specific to this CRM
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		$this->get_params();

		add_filter( 'wpf_format_field_value', array( $this, 'format_field_value' ), 10, 3 );
		add_filter( 'wpf_crm_post_data', array( $this, 'format_post_data' ) );

		// Guest site tracking
		// add_action( 'wpf_guest_contact_created', array( $this, 'set_tracking_cookie_guest' ), 10, 2 );
		// add_action( 'wpf_guest_contact_updated', array( $this, 'set_tracking_cookie_guest' ), 10, 2 );

		// Add tracking code to footer
		// add_action( 'wp_footer', array( $this, 'tracking_code_output' ) );

		// if ( ! empty( $this->api_url ) ) {
		// 	$this->edit_url = trailingslashit( preg_replace( '/\.api\-.+?(?=\.)/', '.activehosted', $this->api_url ) ) . 'app/contacts/%d/';
		// }
	}


	/**
	 * Formats POST data received from HTTP Posts into standard format
	 *
	 * @access public
	 * @return array
	 */

	public function format_post_data( $post_data ) {

		if ( isset( $post_data['contact']['id'] ) ) {
			$post_data['contact_id'] = $post_data['contact']['id'];
		}

		if ( ! empty( $post_data['contact']['tags'] ) ) {
			$post_data['tags'] = explode( ', ', $post_data['contact']['tags'] );
		}

		return $post_data;
	}


	/**
	 * Formats user entered data to match AC field formats
	 *
	 * @access public
	 * @return mixed
	 */

	public function format_field_value( $value, $field_type, $field ) {

		if ( 'date' === $field_type && ! empty( $value ) ) {

			// Adjust formatting for date fields.
			$date = gmdate( 'Y-m-d H:i:s', intval( $value ) );

			return $date;

		} elseif ( 'date' === $field_type && empty( $value ) ) {

			return ''; // AC can't sync empty dates. This will prevent the field from syncing.

		} elseif ( is_array( $value ) ) {

			return implode( '||', array_filter( $value ) );

		} elseif ( ( $field_type == 'checkboxes' || $field_type == 'multiselect' ) && empty( $value ) ) {

			$value = null;

		} else {

			return $value;

		}
	}

	/**
	 * Get common params for the HTTP API
	 *
	 * @access public
	 * @return array Params
	 */

	 public function get_params( $api_key = null ) {

		// Get saved data from DB.
		if ( ! $api_key ) {
			$api_key = wpf_get_option( 'monday_key' );
		}

		$this->api_domain = wpf_get_option( 'monday_url' );
		$this->api_key = $api_key;

		$params = array(
			'user-agent' => 'WP Fusion; ' . home_url(),
			'timeout'    => 15,
			'headers'    => array(
				'Authorization' => 'Bearer ' . $api_key,
			),
		);

		$this->params = $params;

		return $params;
	}

	/**
	 * Check HTTP Response for errors and return WP_Error if found
	 *
	 * @access public
	 * @return HTTP Response
	 */

	public function handle_http_response( $response, $args, $url ) {

		if ( strpos( $url, $this->api_url ) !== false && 'WP Fusion; ' . home_url() === $args['user-agent'] ) { // check if the request came from us.

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
	 * Set a cookie to fix tracking for guest checkouts.
	 *
	 * @since 3.37.3
	 *
	 * @param int    $contact_id The contact ID.
	 * @param string $email      The email address.
	 */
	public function set_tracking_cookie_guest( $contact_id, $email ) {

		if ( wpf_is_user_logged_in() || false == wpf_get_option( 'site_tracking' ) ) {
			return;
		}

		if ( headers_sent() ) {
			wpf_log( 'notice', 0, 'Tried and failed to set site tracking cookie for ' . $email . ', because headers have already been sent.' );
			return;
		}

		wpf_log( 'info', 0, 'Starting site tracking session for contact #' . $contact_id . ' with email ' . $email . '.' );

		setcookie( 'wpf_guest', $email, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Output tracking code
	 *
	 * @access public
	 * @return mixed
	 */

	public function tracking_code_output() {

		if ( false == wpf_get_option( 'site_tracking' ) || true == wpf_get_option( 'staging_mode' ) ) {
			return;
		}

		$email   = wpf_get_current_user_email();
		$trackid = wpf_get_option( 'site_tracking_id' );

		if ( empty( $trackid ) ) {
			$trackid = $this->get_tracking_id();
		}

		echo '<!-- Start ActiveCampaign site tracking (by WP Fusion)-->';
		echo '<script type="text/javascript">';
		echo '(function(e,t,o,n,p,r,i){e.visitorGlobalObjectAlias=n;e[e.visitorGlobalObjectAlias]=e[e.visitorGlobalObjectAlias]||function(){(e[e.visitorGlobalObjectAlias].q=e[e.visitorGlobalObjectAlias].q||[]).push(arguments)};e[e.visitorGlobalObjectAlias].l=(new Date).getTime();r=t.createElement("script");r.src=o;r.async=true;i=t.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)})(window,document,"https://diffuser-cdn.app-us1.com/diffuser/diffuser.js","vgo");';
		echo 'vgo("setAccount", "' . esc_js( $trackid ) . '");';
		echo 'vgo("setTrackByDefault", true);';

		// This does not reliably work when the AC forms plugin is active or any other kind of AC site tracking
		if ( ! empty( $email ) ) {
			echo 'vgo("setEmail", "' . esc_js( $email ) . '");';
		}

		echo 'vgo("process");';
		echo '</script>';
		echo '<!-- End ActiveCampaign site tracking -->';
	}

	/**
	 * Get site tracking ID
	 *
	 * @access  public
	 * @return  int Tracking ID
	 */

	public function get_tracking_id() {

		// Get site tracking ID
		$this->connect();

		wp_fusion()->crm->app->version( 1 );
		$me = wp_fusion()->crm->app->api( 'user/me' );

		if ( is_wp_error( $me ) || ! isset( $me->trackid ) ) {
			return false;
		}

		wp_fusion()->settings->set( 'site_tracking_id', $me->trackid );

		return $me->trackid;
	}

	/**
	 * Initialize connection
	 *
	 * @access  public
	 * @return  bool
	 */

	public function connect( $api_url = null, $api_key = null, $test = false ) {
		BugFu::log("connect_init");

		if ( isset( $this->app ) && ! $test ) {
			return true;
		}

		// Get saved data from DB.
		if ( empty( $api_url ) || empty( $api_key ) ) {
			BugFu::log("getting auth from db");
			$api_url = wpf_get_option( 'monday_url' );
			$api_key = wpf_get_option( 'monday_key' );
		}

		if ( ! class_exists( 'Monday' ) ) {
			require_once __DIR__ . '/Monday.class.php';
		}

		// This is for backwards compatibility with folks who might be using the old SDK.
		// WP Fusion no longer uses it.

		$this->app = new Monday( $api_url, $api_key );
		BugFu::log("Monday app initialized");
		BugFu::log($api_url, false);
		BugFu::log($api_key, false);

		// if ( $test ) {

		// 	$response = wp_remote_get( trailingslashit( $api_url ) . 'api/3/tags', $this->get_params( $api_key ) );

		// 	if ( is_wp_error( $response ) ) {
		// 		return $response;
		// 	}
		// }

		BugFu::log(wp_fusion()->crm->app);

		return true;
	}


	/**
	 * Performs initial sync once connection is configured
	 *
	 * @access public
	 * @return bool
	 */

	public function sync() {

		BugFu::log("sync_init");

		

		$this->connect();

		$this->sync_tags();
		$this->sync_lists();
		$this->sync_crm_fields();

		do_action( 'wpf_sync' );

		return true;
	}


	/**
	 * Gets all available tags and saves them to options
	 *
	 * @access public
	 * @return array Tags
	 */

	 public function sync_tags() {
		// Ensure the API key is available
		$api_key = wpf_get_option('monday_key');
	
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
	 * Gets all available lists and saves them to options
	 *
	 * @access public
	 * @return array Lists
	 */

	 public function sync_lists() {

		BugFu::log("sync_lists_init");

		$api_key = $this->api_key;

		BugFu::log($api_key, false);

		if ( empty( $api_key ) ) {
			return array();
		}

		$query = '{"query": "{ boards { id name } }"}';
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

		if ( is_wp_error( $response ) ) {
			return array();
		}

		// Decode the JSON with the JSON_BIGINT_AS_STRING option to handle large integers
		$body = json_decode(wp_remote_retrieve_body($response), true, 512, JSON_BIGINT_AS_STRING);

		BugFu::log($body, false);

		if ( empty( $body['data']['boards'] ) ) {
			return array();
		}

		$boards = array();

		foreach ( $body['data']['boards'] as $board ) {
			$boards[ $board['id'] ] = $board['name'];
		}

		natcasesort( $boards );

		wp_fusion()->settings->set( 'available_lists', $boards );

		BugFu::log($boards, false);

		return $boards;
	}


	/**
	 * Loads all custom fields from CRM and merges with local list
	 *
	 * @access public
	 * @return array CRM Fields
	 */

	 public function get_selected_board() {
		// Retrieve the entire options array
		$options = get_option('wpf_options');
		// BugFu::log($options, false);
	
		// Check if the monday_board key exists and its value
		if (isset($options['monday_board'])) {
			$selected_board = $options['monday_board'];
			return $selected_board;
		} else {
			BugFu::log('monday_board key does not exist in options', false);
			return null;
		}
	}

	 public function sync_crm_fields() {

		// Fetch the API key
		$api_key = wpf_get_option('monday_key');
		if (empty($api_key)) {
			return new WP_Error('no_api_key', __('No API key provided.', 'wp-fusion'));
		}

		// Ensure a board is selected
		$selected_board = $this->get_selected_board();
		
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

		BugFu::log($body['data']['boards'][0]['columns']);
	
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

		BugFu::log($crm_fields);
	
		wp_fusion()->settings->set('crm_fields', $crm_fields);
	
		return $crm_fields;
	}


	/**
	 * Gets contact ID for a user based on email address
	 *
	 * @access public
	 * @return int Contact ID
	 */

	public function get_contact_id( $email_address ) {

		$response = wp_safe_remote_get( $this->api_url . 'api/3/contacts?email=' . rawurlencode( $email_address ), $this->params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $response->contacts ) ) {
			return false;
		} else {
			return $response->contacts[0]->id;
		}
	}


	/**
	 * Gets all tags currently applied to the contact, also update the list of available tags. This uses the old API since the v3 API only uses tag IDs
	 *
	 * @access public
	 * @return void
	 */

	public function get_tags( $contact_id ) {

		$request = add_query_arg(
			array(
				'api_key'    => wpf_get_option( 'ac_key' ),
				'api_action' => 'contact_view',
				'api_output' => 'json',
				'id'         => $contact_id,
			),
			$this->api_url . 'admin/api.php'
		);

		$params                            = $this->get_params();
		$params['timeout']                 = 20;
		$params['headers']['Content-Type'] = 'application/x-www-form-urlencoded';

		$response = wp_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $response->tags ) ) {
			return array();
		}

		return (array) $response->tags;
	}

	/**
	 * Applies tags to a contact. This uses the old API since the v3 API only uses tag IDs
	 *
	 * @access public
	 * @return bool
	 */

	public function apply_tags( $tags, $contact_id ) {

		$request = add_query_arg(
			array(
				'api_key'    => wpf_get_option( 'ac_key' ),
				'api_action' => 'contact_tag_add',
				'api_output' => 'json',
			),
			$this->api_url . 'admin/api.php'
		);

		$data = array(
			'id'   => $contact_id,
			'tags' => $tags,
		);

		$params                            = $this->get_params();
		$params['timeout']                 = 20;
		$params['body']                    = $data;
		$params['headers']['Content-Type'] = 'application/x-www-form-urlencoded';

		$response = wp_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Possibly update available tags if it's a newly created one.
		$available_tags = wpf_get_option( 'available_tags' );

		foreach ( $tags as $tag ) {
			if ( ! isset( $available_tags[ $tag ] ) ) {
				$available_tags[ $tag ] = $tag;
				$needs_update           = true;
			}
		}

		if ( isset( $needs_update ) ) {
			wp_fusion()->settings->set( 'available_tags', $available_tags );
		}

		return true;
	}


	/**
	 * Removes tags from a contact. This uses the old API since the v3 API only uses tag IDs
	 *
	 * @access public
	 * @return bool
	 */

	public function remove_tags( $tags, $contact_id ) {

		$request = add_query_arg(
			array(
				'api_key'    => wpf_get_option( 'ac_key' ),
				'api_action' => 'contact_tag_remove',
				'api_output' => 'json',
			),
			$this->api_url . 'admin/api.php'
		);

		$data = array(
			'id'   => $contact_id,
			'tags' => $tags,
		);

		$params                            = $this->get_params();
		$params['timeout']                 = 20;
		$params['body']                    = $data;
		$params['headers']['Content-Type'] = 'application/x-www-form-urlencoded';

		$response = wp_remote_post( $request, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}

	private function format_contact_data( $data ) {

		$update_data = array(
			'contact' => array(),
		);

		// Fill out the contact array.
		if ( isset( $data['email'] ) ) {
			$update_data['contact']['email'] = $data['email'];
			unset( $data['email'] );
		}

		if ( isset( $data['first_name'] ) ) {
			$update_data['contact']['firstName'] = $data['first_name'];
			unset( $data['first_name'] );
		}

		if ( isset( $data['last_name'] ) ) {
			$update_data['contact']['lastName'] = $data['last_name'];
			unset( $data['last_name'] );
		}

		if ( isset( $data['phone'] ) ) {
			$update_data['contact']['phone'] = $data['phone'];
			unset( $data['phone'] );
		}

		// Fill out the custom fields array.

		if ( ! empty( $data ) ) {

			$update_data['contact']['fieldValues'] = array();

			foreach ( $data as $field => $value ) {

				// Old api.php field format.
				$field = str_replace( 'field[', '', $field );
				$field = str_replace( ',0]', '', $field );

				$update_data['contact']['fieldValues'][] = array(
					'field' => $field,
					'value' => $value,
				);
			}
		}

		return $update_data;
	}


	/**
	 * Adds a contact to a list.
	 *
	 * @since 3.41.36
	 *
	 * @param int $contact_id The contact ID.
	 * @param int $list_id    The list ID.
	 * @return WP_Error|bool True on success, WP_Error on failure.
	 */
	public function add_contact_to_list( $contact_id, $list_id ) {

		$data = array(
			'contactList' => array(
				'list'    => $list_id,
				'contact' => $contact_id,
				'status'  => 1,
			),
		);

		$params         = $this->get_params();
		$params['body'] = wp_json_encode( $data );

		$response = wp_remote_post( $this->api_url . 'api/3/contactLists', $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}

	/**
	 * Adds a new contact (using v1 API since v3 doesn't support adding custom fields in the same API call)
	 *
	 * @access public
	 * @return int|WP_Error Contact ID or WP_Error.
	 */
	public function add_contact( $contact_data, $map_meta_fields = true ) {
		BugFu::log("add_contact_init");

		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('monday_key');
		$board_id = $this->get_selected_board();

		BugFu::log($api_key);
		BugFu::log($board_id);
	
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
	 * Update contact
	 *
	 * @access public
	 * @return bool
	 */

	 public function update_contact( $contact_id, $contact_data, $map_meta_fields = true ) {
		BugFu::log('update_contact_init');
		// Ensure the API key and board ID are available
		$api_key = wpf_get_option('monday_key');
		$board_id = $this->get_selected_board();
	
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
	 * Loads a contact and updates local user meta
	 *
	 * @access public
	 * @return array User meta data that was returned
	 */

	public function load_contact( $contact_id ) {

		$response = wp_safe_remote_get( $this->api_url . 'api/3/contacts/' . $contact_id, $this->get_params() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $response ) ) {
			return false;
		}

		$user_meta = array();

		// Map contact fields.
		$contact_fields = wpf_get_option( 'contact_fields', array() );

		$loaded_data = array(
			'first_name' => $response->contact->{'firstName'},
			'last_name'  => $response->contact->{'lastName'},
			'email'      => $response->contact->{'email'},
			'phone'      => $response->contact->{'phone'},
		);

		// Maybe merge custom fields.
		$custom_fields = wp_list_pluck( $response->{'fieldValues'}, 'value', 'field' );

		if ( ! empty( $custom_fields ) ) {
			$loaded_data = $loaded_data + $custom_fields;
		}

		// Standard fields.
		foreach ( $loaded_data as $field_name => $value ) {

			foreach ( $contact_fields as $meta_key => $field_data ) {

				if ( $field_data['active'] ) {

					// Convert stored v1 custom field names.
					$field_id = str_replace( 'field[', '', $field_data['crm_field'] );
					$field_id = str_replace( ',0]', '', $field_id );

					if ( 'multiselect' === $field_data['type'] && ! empty( $value ) ) {
						$meta_value = array_values( array_filter( explode( '||', $value ) ) );
					} elseif ( ! empty( $value ) ) {
						$meta_value = trim( $value, '||' ); // in case it's a multiselect being loaded as text.
					} else {
						$meta_value = $value;
					}

					if ( strval( $field_id ) === strval( $field_name ) ) {
						$user_meta[ $meta_key ] = $meta_value;
					}
				}
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

	public function load_contacts( $tag_name = false ) {

		$url = $this->api_url . 'api/3/contacts?limit=100';

		if ( $tag_name ) {

			// For this to work we need the tag ID
			$response = wp_safe_remote_get( $this->api_url . 'api/3/tags?search=' . rawurlencode( $tag_name ), $this->get_params() );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$response = json_decode( wp_remote_retrieve_body( $response ) );

			if ( empty( $response->tags ) ) {

				wpf_log( 'error', 0, 'Unable to get tag ID for ' . $tag_name . ', cancelling import.' );
				return false;

			}

			$tag_id = false;

			foreach ( $response->tags as $tag ) {

				if ( $tag_name === $tag->tag ) {
					$tag_id = $tag->id;
					break;
				}
			}
			if ( $tag_id ) {
				$url = add_query_arg( 'tagid', $tag_id, $url );
			}
		}

		// Query will only return contacts on at least one list.

		$contact_ids = array();
		$offset      = 0;
		$proceed     = true;

		while ( $proceed ) {

			// Limit is actually 100, this has been tested.
			$url = add_query_arg( 'offset', $offset, $url );

			$response = wp_safe_remote_get( $url, $this->get_params() );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$response = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $response->contacts ) ) {

				foreach ( $response->contacts as $contact ) {

					$contact_ids[] = $contact->id;

				}

				$offset += 100;

			}

			if ( count( $response->contacts ) < 100 ) {

				$proceed = false;

			}
		}

		return $contact_ids;
	}

	//
	// Deep data stuff
	//

	/**
	 * Gets or creates an ActiveCampaign deep data connection
	 *
	 * @access public
	 * @since  3.24.11
	 * @return int
	 */

	public function get_connection_id() {

		$connection_id = get_option( 'wpf_ac_connection_id' );

		if ( ! empty( $connection_id ) ) {
			return $connection_id;
		}

		$body = array(
			'connection' => array(
				'service'    => 'WP Fusion',
				'externalid' => $_SERVER['SERVER_NAME'],
				'name'       => get_bloginfo(),
				'logoUrl'    => 'https://wpfusion.com/wp-content/uploads/2019/08/logo-mark-500w.png',
				'linkUrl'    => admin_url( 'options-general.php?page=wpf-settings#ecommerce' ),
			),
		);

		$args         = $this->get_params();
		$args['body'] = wp_json_encode( $body );

		wpf_log( 'info', 0, 'Opening new ActiveCampaign Deep Data connection.', array( 'source' => 'wpf-ecommerce' ) );

		$response = wp_safe_remote_post( $this->api_url . 'api/3/connections', $args );

		if ( is_wp_error( $response ) && 'field_invalid' === $response->get_error_code() ) {

			// Try to look up an existing connection.

			unset( $args['body'] );

			$response = wp_safe_remote_get( $this->api_url . 'api/3/connections', $args );

			if ( ! is_wp_error( $response ) ) {

				$response = json_decode( wp_remote_retrieve_body( $response ) );

				foreach ( $response->connections as $connection ) {

					if ( $connection->service == 'WP Fusion' && $connection->externalid == $_SERVER['SERVER_NAME'] ) {

						update_option( 'wpf_ac_connection_id', $connection->id );

						return $connection->id;

					}
				}
			}
		}

		if ( is_wp_error( $response ) ) {

			wpf_log( 'info', 0, 'Unable to open Deep Data Connection: ' . $response->get_error_message(), array( 'source' => 'wpf-ecommerce' ) );
			update_option( 'wpf_ac_connection_id', false );

			return false;

		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_object( $body ) ) {

			return false;

		} elseif ( isset( $body->message ) ) {

			// If Deep Data not enabled.
			wpf_log( 'info', 0, 'Unable to open Deep Data Connection: ' . $body->message, array( 'source' => 'wpf-ecommerce' ) );
			update_option( 'wpf_ac_connection_id', false );

			return false;

		}

		update_option( 'wpf_ac_connection_id', $body->connection->id );

		return $body->connection->id;
	}

	/**
	 * Deletes a registered connection
	 *
	 * @since 3.24.11
	 * @return void
	 */

	public function delete_connection( $connection_id ) {

		$params = $this->get_params();

		$params['method'] = 'DELETE';

		wpf_log( 'notice', 0, 'Deleting ActiveCampaign Deep Data connection ID <strong>' . $connection_id . '</strong>', array( 'source' => 'wpf-ecommerce' ) );

		wp_safe_remote_request( $this->api_url . 'api/3/connections/' . $connection_id, $params );

		delete_option( 'wpf_ac_connection_id' );
	}

	/**
	 * Gets or creates an ActiveCampaign deep data customer
	 *
	 * @since 3.24.11
	 * @return int
	 */

	public function get_customer_id( $contact_id, $connection_id, $order_id = false ) {

		$transient = get_transient( 'wpf_abandoned_cart_' . $contact_id );

		if ( ! empty( $transient ) && ! empty( $transient['customer_id'] ) ) {

			// For cases where we just created the customer via the Abandoned Cart addon
			return $transient['customer_id'];
		}

		$user_id = wp_fusion()->user->get_user_id( $contact_id );

		if ( false !== $user_id ) {

			// Get the customer ID from the cache if it's a registered user

			$customer_id = get_user_meta( $user_id, 'wpf_ac_customer_id', true );

			if ( ! empty( $customer_id ) ) {
				return $customer_id;
			}
		}

		if ( empty( $user_id ) ) {

			$external_id  = 'guest';
			$contact_data = $this->load_contact( $contact_id );

			if ( is_wp_error( $contact_data ) ) {

				wpf_log( 'error', $user_id, 'Error loading contact #' . $contact_id . ': ' . $contact_data->get_error_message(), array( 'source' => 'wpf-ecommerce' ) );
				return false;

			}

			$user_email = $contact_data['user_email'];

		} else {
			$external_id = $user_id;
			$user        = get_userdata( $user_id );
			$user_email  = $user->user_email;
		}

		$params = $this->get_params();

		// Try to look up an existing customer.

		$request  = $this->api_url . 'api/3/ecomCustomers?filters[email]=' . rawurlencode( $user_email ) . '&filters[connectionid]=' . $connection_id;
		$response = wp_safe_remote_get( $request, $params );

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! empty( $body->ecomCustomers ) ) {

			foreach ( $body->ecomCustomers as $customer ) {

				if ( $customer->connectionid == $connection_id ) {

					return $customer->id;

				}
			}
		}

		// If no customer was found, create a new one

		$body = array(
			'ecomCustomer' => array(
				'connectionid' => $connection_id,
				'externalid'   => $external_id,
				'email'        => $user_email,
			),
		);

		wpf_log(
			'info',
			$user_id,
			'Registering new ecomCustomer:',
			array(
				'source'              => 'wpf-ecommerce',
				'meta_array_nofilter' => $body,
			)
		);

		$params['body'] = wp_json_encode( $body );

		$response = wp_safe_remote_post( $this->api_url . 'api/3/ecomCustomers', $params );

		$customer_id = false;

		if ( is_wp_error( $response ) ) {

			if ( 'related_missing' === $response->get_error_code() ) {

				// Connection was deleted or is invalid.
				delete_option( 'wpf_ac_connection_id' );

				wpf_log( 'error', $user_id, 'Error creating customer: ' . $response->get_error_message() . ' It looks like the connection ID ' . $connection_id . ' was deleted. Please re-enable Deep Data via the WP Fusion settings.', array( 'source' => 'wpf-ecommerce' ) );

			} else {
				wpf_log( 'error', $user_id, 'Error creating customer: ' . $response->get_error_message(), array( 'source' => 'wpf-ecommerce' ) );
			}

			return false;

		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( is_object( $body ) ) {
			$customer_id = $body->ecomCustomer->id;
		}

		if ( false === $customer_id ) {

			wpf_log( 'error', $user_id, 'Unable to create customer or find existing customer. Aborting.', array( 'source' => 'wpf-ecommerce' ) );
			return false;

		}

		if ( false !== $user_id ) {
			update_user_meta( $user_id, 'wpf_ac_customer_id', $customer_id );
		}

		return $customer_id;
	}

	/**
	 * Track event.
	 *
	 * Track an event with the AC site tracking API.
	 *
	 * @since  3.36.12
	 *
	 * @link   https://wpfusion.com/documentation/crm-specific-docs/activecampaign-event-tracking/
	 *
	 * @param  string      $event         The event title.
	 * @param  bool|string $event_data    The event description.
	 * @param  bool|string $email_address The user email address.
	 * @return bool|WP_Error True if success, WP_Error if failed.
	 */
	public function track_event( $event, $event_data = false, $email_address = false ) {

		// Get the email address to track.

		if ( empty( $email_address ) ) {
			$email_address = wpf_get_current_user_email();
		}

		if ( false === $email_address ) {
			return; // can't track without an email.
		}

		// Get tracking ID.

		$trackid = wpf_get_option( 'event_tracking_id' );

		if ( ! $trackid ) {

			$this->connect();
			$me = $this->app->api( 'user/me' );

			if ( empty( $me->eventkey ) ) {
				wpf_log( 'error', 0, 'To use event tracking it must first be enabled in your ActiveCampaign account under Settings &raquo; Tracking &raquo; Event Tracking.' );
				return false;
			}

			$trackid = $me->eventkey;

			wp_fusion()->settings->set( 'event_tracking_id', $me->eventkey );

		}

		// Get account ID.

		$actid = wpf_get_option( 'site_tracking_id' );

		if ( ! $actid ) {
			$actid = $this->get_tracking_id();
		}

		// Event names only allow alphanumeric + dash + underscore.
		$event = preg_replace( '/[^\-\_0-9a-zA-Z ]/', '', $event );

		$data = array(
			'actid'     => $actid,
			'key'       => $trackid,
			'event'     => $event,
			'eventdata' => $event_data,
			'visit'     => wp_json_encode(
				array(
					'email' => $email_address,
				)
			),
		);

		$params                            = $this->get_params();
		$params['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
		$params['body']                    = $data;
		$params['blocking']                = false; // we don't need to wait for a response.

		$response = wp_remote_post( 'https://trackcmp.net/event', $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}
}
