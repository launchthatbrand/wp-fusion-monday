<?php

if ( ! defined( "MONDAY_URL" ) ||  ! defined( "MONDAY_API_KEY" )  ) {
	require_once( dirname( __FILE__ ) . "/config.php" );
}

require_once( "Connector.class.php" );

class Monday extends Monday_Connector {

	public $url_base;
	public $url;
	public $api_key;
	public $track_email;
	public $track_actid;
	public $track_key;
	public $version = 1;
	public $debug = false;

	function __construct( $url, $api_key, $api_user = "", $api_pass = "" ) {
		$this->url_base = $this->url = $url;
		$this->api_key  = $api_key;
		parent::__construct( $url, $api_key, $api_user, $api_pass );
	}

	function version( $version ) {
		$this->version = (int) $version;
		if ( $version == 2 ) {
			$this->url_base = $this->url_base . "/2";
		}
	}

	function api( $path, $post_data = array() ) {
		BugFu::log("api_init");
		
		$components = explode( "/", $path );
		$component  = $components[0];
	
		if ( count( $components ) > 2 ) {
			array_shift( $components );
			$method_str = implode( "_", $components );
			$components = array( $component, $method_str );
		}
	
		if ( preg_match( "/\?/", $components[1] ) ) {
			$method_arr = explode( "?", $components[1] );
			$method     = $method_arr[0];
			$params     = $method_arr[1];
		} else {
			if ( isset( $components[1] ) ) {
				$method = $components[1];
				$params = "";
			} else {
				return "Invalid method.";
			}
		}
	
		$class = ucwords( $component );
		$class = "Monday_" . $class;

		BugFu::log($class, false);
	
		$add_tracking = false;
		if ( $class == "Monday_Tracking" ) {
			$add_tracking = true;
		}
	
		$class = new $class( $this->version, $this->url_base, $this->url, $this->api_key );
	
		if ( $add_tracking ) {
			$class->track_email = $this->track_email;
			$class->track_actid = $this->track_actid;
			$class->track_key   = $this->track_key;
		}
	
		$class->debug = $this->debug;
	
		// Check if we're dealing with Monday.com API
		if ($component == 'item') {
			$response = $class->graphql_query($method, $post_data);
		} else {
			$response = $class->$method($params, $post_data);
		}
	
		return $response;
	}

}

// require_once( "Account.class.php" );
// require_once( "Auth.class.php" );
// require_once( "Automation.class.php" );
// require_once( "Campaign.class.php" );
// require_once( "Contact.class.php" );
require_once( "Item.class.php" );
// require_once( "Design.class.php" );
// require_once( "Form.class.php" );
// require_once( "Group.class.php" );
// require_once( "List.class.php" );
// require_once( "Message.class.php" );
// require_once( "Settings.class.php" );
// require_once( "Subscriber.class.php" );
// require_once( "Tracking.class.php" );
// require_once( "User.class.php" );
// require_once( "Webhook.class.php" );

?>