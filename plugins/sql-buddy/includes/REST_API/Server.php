<?php
/**
 * Server class.
 *
 * @package   SQL_Buddy
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://github.com/deliciousbrains/sql-buddy/
 */

namespace SQL_Buddy\REST_API;

/**
 * Class Server
 */
class Server {

	/**
	 * API namespace.
	 *
	 * @var string
	 */
	public $namespace;

	/**
	 * Server constructor.
	 */
	public function __construct() {
		$this->namespace = 'sqlbuddy/v1';
	}

	/**
	 * Register route for an endpoint.
	 *
	 * @param string $endpoint Endpoint to register.
	 * @param array  $args     Arguments.
	 */
	public function register_route( $endpoint, $args ) {
		register_rest_route( $this->namespace, $endpoint, $args );
	}
}
