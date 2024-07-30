<?php

class Monday_Item extends Monday {

	public $version;
	public $url_base;
	public $url;
	public $api_key;

	function __construct( $version, $url_base, $url, $api_key ) {
		$this->version  = $version;
		$this->url_base = $url_base;
		$this->url      = $url;
		$this->api_key  = $api_key;
	}

	function graphql_query( $method, $post_data ) {
		$request_url = "{$this->url_base}";

		// Construct GraphQL query based on the method
		switch ($method) {
			case 'add':
				
				// Check for required parameters
				if (empty($post_data['board_id']) || empty($post_data['item_name'])) {
					return new WP_Error('missing_data', __('Error: board_id and item_name are required.', 'wp-fusion'));
				}

				$query = 'mutation {
					create_item (board_id: ' . $post_data['board_id'] . ', item_name: "' . $post_data['item_name'] . '", column_values: "' . addslashes($post_data['column_values']) . '") {
						id
					}
				}';
				break;
			// Add other methods as needed (e.g., edit, delete)
			default:
				return "Invalid method.";
		}

		$response = wp_safe_remote_post(
			$request_url,
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $this->api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => json_encode(array('query' => $query)),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}

?>