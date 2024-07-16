<?php

class WPF_Monday_CRM extends WPF_CRM_Base {

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
		$this->name     = 'Monday.com';
		$this->supports = array();

		// Set up admin options
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/admin/class-admin.php';
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
		add_filter( 'wpf_crm_push_user_meta', array( $this, 'format_user_meta' ), 10, 2 );
		add_filter( 'wpf_crm_pull_user_meta', array( $this, 'format_user_meta' ), 10, 2 );

	}

	/**
	 * Format user meta coming from Monday
	 *
	 * @access public
	 * @return array
	 */

	public function format_user_meta( $user_meta, $user_id ) {

		if ( ! empty( $user_meta['user_id'] ) ) {
			$user_meta['monday_user_id'] = $user_meta['user_id'];
			unset( $user_meta['user_id'] );
		}

		return $user_meta;

	}

	/**
	 * Formats POST data received from HTTP Posts into standard format
	 *
	 * @access public
	 * @return array
	 */

	public function format_post_data( $post_data ) {

		if ( isset( $post_data['contact_id'] ) ) {
			$post_data['monday_user_id'] = $post_data['contact_id'];
			unset( $post_data['contact_id'] );
		}

		return $post_data;

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
			'user-agent' => 'WP Fusion; ' . home_url(),
			'timeout'    => 30,
			'headers'    => array(
				'Content-Type'  => 'application/json',
				'Authorization' => $api_key,
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

		if ( ! $test ) {
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

		// TODO: Implement tag syncing logic here

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

		$crm_fields = array();

		// TODO: Implement field syncing logic here

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

		// TODO: Implement get_contact_id logic here

		return false;
	}

	/**
	 * Gets all tags currently applied to the user, also update the list of available tags
	 *
	 * @access public
	 * @return void
	 */
	public function get_tags( $contact_id ) {

		// TODO: Implement get_tags logic here

		return array();
	}

	/**
	 * Applies tags to a contact
	 *
	 * @access public
	 * @return bool
	 */
	public function apply_tags( $tags, $contact_id ) {

		// TODO: Implement apply_tags logic here

		return true;
	}

	/**
	 * Removes tags from a contact
	 *
	 * @access public
	 * @return bool
	 */
	public function remove_tags( $tags, $contact_id ) {

		// TODO: Implement remove_tags logic here

		return true;
	}

	/**
	 * Adds a new contact
	 *
	 * @access public
	 * @return int Contact ID
	 */
	public function add_contact( $data ) {

		// TODO: Implement add_contact logic here

		return $contact_id;
	}

	/**
	 * Update contact
	 *
	 * @access public
	 * @return bool
	 */
	public function update_contact( $contact_id, $data ) {

		// TODO: Implement update_contact logic here

		return true;
	}

	/**
	 * Loads a contact and updates local user meta
	 *
	 * @access public
	 * @return array User meta data that was returned
	 */
	public function load_contact( $contact_id ) {

		// TODO: Implement load_contact logic here

		return $user_meta;
	}

	/**
	 * Gets a list of contact IDs based on tag
	 *
	 * @access public
	 * @return array Contact IDs returned
	 */
	public function load_contacts( $tag ) {

		// TODO: Implement load_contacts logic here

		return $contact_ids;
	}

}
