<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}


class WPF_Thrive_Autoresponder_Main {
	/**
	 * Access token.
	 *
	 * @since 3.40.24
	 * @var string
	 */
	private $access_token;

	/**
	 * APi instance.
	 *
	 * @since 3.40.24
	 * @var strong
	 */
	private $api_instance;

	const API_KEY = 'wpfusion';

	/**
	 * Get autoresponder title.
	 *
	 * @since 3.40.24
	 * @return string
	 */
	public function get_title() {
		return sprintf( __( 'WP Fusion - %s', 'wp-fusion' ), wp_fusion()->crm->name );
	}

	/**
	 * Get autoresponder key.
	 *
	 * @since 3.40.24
	 * @return string
	 */
	public function get_key() {
		return static::API_KEY;
	}


	public function __construct() {
		new WPF_Thrive_Autoresponder_API();
		WPF_Thrive_Autoresponder_Hooks::init();

	}

	/**
	 * Thrive Ultimatum breaks without this.
	 *
	 * @since 3.40.38
	 */
	public function get_email_merge_tag() {
		return false;
	}

	/**
	 * Thrive Leads breaks without this.
	 *
	 * @since 3.40.38
	 */
	public function get_api_data() {
		return array(
			'lists'         => $this->get_lists(),
			'custom_fields' => $this->get_custom_fields(),
		);
	}


	/**
	 * Return API Instance.
	 *
	 * @since 3.40.24
	 * @return object
	 */
	public function get_api_instance() {
		if ( empty( $this->api_instance ) ) {
			try {
				$this->api_instance = new WPF_Thrive_Autoresponder_API();
			} catch ( \Exception $e ) {
				Utils::log_error( 'Error while instantiating the API! Error message: ' . $e->getMessage() );
			}
		}

		return $this->api_instance;
	}

	/**
	 * When the 'Connect' button is pressed, save the new credentials and re-generate the access token.
	 * If 'Disconnect' is pressed, clear the credentials and the token.
	 * If 'Test connection' is pressed, run a test API call and print the result.
	 *
	 * @since 3.40.24
	 * @param array $post_data
	 */
	public function on_form_action( $post_data ) {
		return true;
	}


	/**
	 * Get form args from posted data.
	 *
	 * @since 3.40.24
	 * @param array $data
	 * @return array
	 */
	private function wpf_get_form_args( $data ) {
		$update_data = array();

		// Email field.
		if ( ! empty( $data['email'] ) ) {
			$update_data['email'] = $data['email'];
		}

		// Format name to first, last.
		if ( ! empty( $data['name'] ) ) {

			$names                          = explode( ' ', $data['name'], 2 );
			$first_name_key                 = wpf_get_crm_field( 'first_name' );
			$update_data[ $first_name_key ] = $names[0];

			if ( isset( $names[1] ) ) {

				$last_name_key                 = wpf_get_crm_field( 'last_name' );
				$update_data[ $last_name_key ] = $names[1];

			}
		}

		// Map custom fields.

		if ( isset( $data['tve_mapping'] ) ) {

			$form_data = thrive_safe_unserialize( base64_decode( $data['tve_mapping'] ) );

			if ( ! empty( $form_data ) ) {
				foreach ( $form_data as $key => $value ) {
					if ( isset( $value['wpfusion'] ) ) {
						$update_data[ $value['wpfusion'] ] = $data[ $key ];
					}
				}
			}

		}

		// Tags.
		$apply_tags = array();

		if ( ! empty( $data['wpfusion_tags'] ) ) {
			$apply_tags = $this->format_tags( explode( ',', $data['wpfusion_tags'] ) );
		}

		$args = array(
			'email_address'    => $data['email'],
			'update_data'      => $update_data,
			'apply_tags'       => $apply_tags,
			'integration_slug' => 'thrive_api_connection',
			'integration_name' => 'Thrive API Connection',
			'form_id'          => false,
			'form_title'       => false,
			'form_edit_link'   => false,
		);

		if ( isset( $data['thrive_leads'] ) ) {
			$args['form_id']        = $data['thrive_leads']['tl_data']['form_type_id'];
			$args['form_title']     = $data['thrive_leads']['tl_data']['form_name'];
			$args['form_edit_link'] = admin_url( 'post.php?action=architect&tve=true&post=' . $data['thrive_leads']['tl_data']['form_type_id'] . '&_key=' . $data['thrive_leads']['tl_data']['_key'] );
		}

		return $args;

	}

	/**
	 * @param string $list_identifier - the ID of the mailing list
	 * @param array  $data            - an array of what we want to send as subscriber data
	 * @param bool   $is_update
	 *
	 * @since 3.40.24
	 * @return boolean
	 */
	public function add_subscriber( $list_identifier, $data, $is_update = false ) {
		$success = false;
		try {
			$args = $this->wpf_get_form_args( $data );

			if ( $is_update === true ) {
				$contact_id = wp_fusion()->crm->get_contact_id( $data['email'] );
				if ( $contact_id ) {
					$args['update_data']['contact_id'] = $contact_id;
					WPF_Forms_Helper::process_form_data( $args );
				}
			} else {
				$contact_id = WPF_Forms_Helper::process_form_data( $args );
			}
			$success = true;
		} catch ( \Exception $e ) {
			Utils::log_error( 'Error while adding/updating the subscriber! Error message: ' . $e->getMessage() );
		}

		return $success;
	}

	/**
	 * @param array $data
	 *
	 * @since 3.40.24
	 * @return mixed
	 */
	public function process_subscriber_data( $data ) {
		return wp_fusion()->crm->format_post_data();
	}

	/**
	 * Get credentials.
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public static function get_credentials() {
		return array();
	}

	/**
	 * Check to see if it's connected.
	 *
	 * @since 3.40.24
	 * @return boolean
	 */
	public function is_connected() {
		return true;
	}

	/**
	 * Test connection.
	 *
	 * @since 3.40.24
	 * @return boolean
	 */
	public function test_connection() {
		if ( ! $this->is_connected() ) {
			return false;
		}
	}

	/**
	 * Get crm lists.
	 *
	 * @since 3.40.24
	 * @param boolean $is_testing_connection Check if testing.
	 * @return array
	 */
	public function get_lists( $is_testing_connection = false ) {
		if ( ! $this->is_connected() ) {
			return array();
		}

		$formatted_lists = array();
		$lists           = wp_fusion()->settings->get( 'available_lists', array() );

		if ( ! empty( $lists ) ) {
			foreach ( $lists as $key => $val ) {
				$formatted_lists[] = array(
					'id'   => $key,
					'name' => $val,
				);
			}
		} else {
			$formatted_lists[] = array(
				'id'   => 'default',
				'name' => __( 'Default List', 'wp-fusion' ),
			);
		}

		return $formatted_lists;
	}

	/**
	 * Since custom fields are enabled, this is set to true.
	 *
	 * @since 3.40.24
	 * @return bool
	 */
	public function has_custom_fields() {
		return true;
	}

	/**
	 * Since the implementation covers the WP Fusion global custom fields, this function returns all of them.
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public function get_custom_fields_by_list() {
		return $this->get_api_custom_fields();
	}

	/**
	 * Returns all the types of custom field mappings.
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public function get_custom_fields() {
		return $this->get_api_custom_fields();
	}

	/**
	 * Retrieves all the used custom fields. Currently it returns all the inter-group (global) ones.
	 *
	 * @param array $params  which may contain `list_id`
	 * @param bool  $force
	 * @param bool  $get_all whether to get lists with their custom fields
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public function get_api_custom_fields( $params = array(), $force = false, $get_all = true ) {

		$crm_fields = wp_fusion()->settings->get_crm_fields_flat();

		$api_fields = array();

		if ( empty( $crm_fields ) ) {
			return array();
		}

		foreach ( $crm_fields as $key => $value ) {
			$api_fields[] = array(
				'id'    => $key,
				'name'  => $value,
				'label' => $value,
			);
		}
		return $api_fields;
	}

	/**
	 * Builds custom fields mapping for automations.
	 * Called from Thrive Automator when the custom fields are processed.
	 *
	 * @param $automation_data Automation data.
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public function build_automation_custom_fields( $automation_data ) {
		return $this->get_api_custom_fields();
	}

	/**
	 * Enables the tag feature inside Thrive Architect & Automator.
	 *
	 * @since 3.40.24
	 * @return bool
	 */
	public function has_tags() {
		return true;
	}

	/**
	 * False by default.
	 *
	 * In order to implement forms:
	 * - set this to true;
	 * - implement get_forms_key() - used by both Thrive Automator and Thrive Architect
	 * - implement get_forms() - used by both Thrive Automator and Thrive Architect
	 * - handle the form data inside the add_subscriber() function
	 * - if needed, adapt autoresponders\WP Fusion\assets\js\editor.js to suit your API - used by Thrive Architect
	 *
	 * A working example can be found in the WP Fusion folder.
	 *
	 * @since 3.40.24
	 * @return bool
	 */
	public function has_forms() {
		return false;
	}

	/**
	 * API-unique tag identifier.
	 *
	 * @since 3.40.24
	 * @return string
	 */
	public function get_tags_key() {
		return $this->get_key() . '_tags';
	}

	/**
	 * Enables the mailing list, forms, opt-in type and tag features inside Thrive Automator.
	 * Check the parent method for an explanation of the config structure.
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public function get_automator_add_autoresponder_mapping_fields() {
		/**
		 * Some usage examples for this:
		 *
		 * A basic configuration only for mailing lists is "[ 'autoresponder' => [ 'mailing_list' ] ]".
		 * If the custom fields rely on the mailing list, they are added like this: "[ 'autoresponder' => [ 'mailing_list' => [ 'api_fields' ] ] ]"
		 * If the custom fields don't rely on the mailing list ( global custom fields ), the config is: "[ 'autoresponder' => [ 'mailing_list', 'api_fields' ] ]"
		 *
		 * Config for mailing list, custom fields (global), tags: "[ 'autoresponder' => [ 'mailing_list', 'api_fields', 'tag_input' ] ]"
		 *
		 * Config for mailing list, tags, and forms that depend on the mailing lists:
		 * "[ 'autoresponder' => [ 'mailing_list' => [ 'form_list' ], 'api_fields' => [], 'tag_input' => [] ] ]"
		 * ^ If one of the keys has a corresponding array, empty arrays must be added to the other keys in order to respect the structure.
		 */

		return array(
			'autoresponder' => array(
				'api_fields' => array(),
				'tag_input'  => array(),
			),
		);
	}

	/**
	 * Get field mappings specific to an API with tags. Has to be set like this in order to enable tags inside Automator.
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public function get_automator_tag_autoresponder_mapping_fields() {
		return array( 'autoresponder' => array( 'tag_input' ) );
	}

	/**
	 * Extra data to localize inside Thrive Architect.
	 * Nothing is needed by default, so we return an empty array.
	 *
	 * @since 3.40.25
	 * @return array
	 */
	public function get_data_for_setup() {
		return array();
	}

	/**
	 * Converts tag names to IDs and removes invalid entries.
	 *
	 * @since 3.40.39
	 *
	 * @param array $tags The tags.
	 * @return array The tags.
	 */
	public function format_tags( $tags ) {

		foreach ( $tags as $i => $tag ) {

			$tag_id = wp_fusion()->user->get_tag_id( $tag );

			if ( false === $tag_id ) {

				wpf_log( 'notice', 0, 'Warning: ' . $tag . ' is not a valid tag name or ID.' );
				unset( $tags[ $i ] );
				continue;

			}

			$tags[ $i ] = $tag_id;

		}

		return $tags;

	}

	/**
	 * This is called from Thrive Automator when the 'Tag user' automation is triggered.
	 * In this case, we want to add the received tags to the received subscriber and mailing list.
	 * This is only done if the subscriber already exists.
	 *
	 * @since 3.40.24
	 *
	 * @param string $email The subscriber's email address.
	 * @param string $tags  The tags to apply.
	 * @param array  $extra ???.
	 * @return bool Whether or not the subscriber exists.
	 */
	public function update_tags( $email, $tags = '', $extra = array() ) {

		$subscriber_exists = false;
		$contact_id        = wp_fusion()->crm->get_contact_id( $email );
		$tags              = $this->format_tags( explode( ',', $tags ) );

		if ( $contact_id ) {
			try {
				wp_fusion()->crm->apply_tags( $tags, $contact_id );
				$subscriber_exists = true;
			} catch ( \Exception $e ) {
				Utils::log_error( 'Error while fetching the subscriber! Error message: ' . $e->getMessage() );
			}
		}

		return $subscriber_exists;
	}

	/**
	 * Get the thumbnail url.
	 *
	 * @since 3.40.24
	 * @return string
	 */
	public static function get_thumbnail() {
		return WPF_DIR_URL . '/assets/img/logo-wide-color.png';
	}

	/**
	 * Get link to the option page of crm.
	 *
	 * @since 3.40.24
	 * @return string
	 */
	public static function get_link_to_controls_page() {
		return get_admin_url() . '/options-general.php?page=wpf-settings#integrations';
	}
}

/**
 * Autoresponder Hooks for crm.
 *
 * @since 3.40.24
 */
class WPF_Thrive_Autoresponder_Hooks {

	/**
	 * Init
	 *
	 * @since 3.40.24
	 */
	public static function init() {
		add_action( 'tcb_editor_enqueue_scripts', array( __CLASS__, 'enqueue_architect_scripts' ) );

		add_filter( 'tcb_lead_generation_apis_with_tag_support', array( __CLASS__, 'tcb_apis_with_tags' ) );
	}


	/**
	 * Enqueue an additional script inside Thrive Architect in order to add some custom hooks which integrate WP Fusion with the Lead Generation element API Connections.
	 *
	 * @since 3.40.24
	 */
	public static function enqueue_architect_scripts() {

		wp_enqueue_script( 'wpf-thrive-api-connection', WPF_DIR_URL . 'assets/js/wpf-thrive-api-connection.js', array( 'tve_editor' ) );

		$localized_data = array(
			'api_logo' => WPF_DIR_URL . '/assets/img/logo-sm-trans.png',
			'api_key'  => 'wpfusion',
		);

		wp_localize_script( 'wpf-thrive-api-connection', 'wpf_thrive_api', $localized_data );

	}

	/**
	 * Add WP Fusion to the list of supported APIs with tags. Required inside TCB.
	 *
	 * @param $apis
	 *
	 * @since 3.40.24
	 * @return mixed
	 */
	public static function tcb_apis_with_tags( $apis ) {
		$apis[] = 'wpfusion';

		return $apis;
	}
}


/**
 * Autoresponder API.
 *
 * @since 3.40.24
 */
class WPF_Thrive_Autoresponder_API {

	/**
	 * Get crm user by email.
	 *
	 * @param integer $list_id
	 * @param string  $email
	 *
	 * @since 3.40.24
	 * @return mixed
	 */
	public function get_subscriber_by_email( $list_id, $email ) {
		$contact    = '';
		$contact_id = wp_fusion()->crm->get_contact_id( $email );
		if ( $contact_id ) {
			$contact = wp_fusion()->crm->load_contact( $contact_id );
		}
		return $contact;
	}

	/**
	 * Get crm lists.
	 *
	 * @since 3.40.24
	 * @return array
	 */
	public function get_lists() {
		$formatted_lists = array();
		$lists           = wp_fusion()->settings->get( 'available_lists', array() );

		if ( ! empty( $lists ) ) {
			foreach ( $lists as $key => $val ) {
				$formatted_lists[] = array(
					'id'   => $key,
					'name' => $val,
				);
			}
		}

		return $formatted_lists;
	}

}


class WPF_Thrive_API_Connection extends WPF_Integrations_Base {

	/**
	 * Get registered autoresponders.
	 *
	 * @since 3.40.24
	 * @var array
	 */
	public static $registered_autoresponders = array();

	/**
	 * The slug for WP Fusion's module tracking.
	 *
	 * @since 3.40.24
	 * @var string $slug
	 */

	public $slug = 'thrive-api-connection';

	/**
	 * The plugin name for WP Fusion's module tracking.
	 *
	 * @since 3.40.24
	 * @var string $name
	 */
	public $name = 'Thrive API Connection';

	/**
	 * The link to the documentation on the WP Fusion website.
	 *
	 * @since 3.40.24
	 * @var string $docs_url
	 */
	public $docs_url = false;


	/**
	 * Init class.
	 *
	 * @since 3.40.24
	 */
	public function init() {

		static::$registered_autoresponders['wpfusion'] = new WPF_Thrive_Autoresponder_Main();

		add_filter( 'tvd_api_available_connections', array( $this, 'add_api_to_connection_list' ), 10, 3 );
		add_filter( 'tvd_third_party_autoresponders', array( $this, 'add_api_to_thrive_dashboard_list' ), 10, 2 );

	}


	/**
	 * Hook that adds the autoresponder to the list of available APIs that gets retrieved by Thrive Architect and Thrive Automator.
	 *
	 * @param $autoresponders The autoresponders registered
	 * @param $only_connected Check if it's connected
	 * @param $include_all - a flag that is set to true when all the connections ( including third party APIs ) must be shown
	 *
	 * @since 3.40.24
	 * @return mixed
	 */
	public static function add_api_to_connection_list( $autoresponders, $only_connected, $api_filter ) {
		$include_3rd_party_apis = ! empty( $api_filter['include_3rd_party_apis'] );

		if ( ( $include_3rd_party_apis || $only_connected ) && static::should_include_autoresponders( $api_filter ) ) {
			foreach ( static::$registered_autoresponders as $autoresponder_key => $autoresponder_instance ) {
				/* @var Autoresponder $autoresponder_data */
				if ( $include_3rd_party_apis || $autoresponder_instance->is_connected() ) {
					$autoresponders[ $autoresponder_key ] = $autoresponder_instance;
				}
			}
		}

		return $autoresponders;
	}

	/**
	 * Hook that adds the card of this API to the Thrive Dashboard API Connection page.
	 *
	 * @param array $autoresponders
	 * @param bool  $localize
	 *
	 * @since 3.40.24
	 * @return mixed
	 */
	public static function add_api_to_thrive_dashboard_list( $autoresponders, $localize ) {
		foreach ( static::$registered_autoresponders as $key => $autoresponder_instance ) {
			$autoresponders[ $key ] = $autoresponder_instance;
		}

		return $autoresponders;
	}

	/**
	 * Check if it should include autoresponders.
	 *
	 * @param array $api_filter
	 *
	 * @since 3.40.24
	 * @return bool
	 */
	public static function should_include_autoresponders( $api_filter ) {
		$type = 'autoresponder';

		if ( empty( $api_filter['include_types'] ) ) {
			$should_include_api = ! in_array( $type, $api_filter['exclude_types'], true );
		} else {
			$should_include_api = in_array( $type, $api_filter['include_types'], true );
		}

		return $should_include_api;
	}


}

new WPF_Thrive_API_Connection();
