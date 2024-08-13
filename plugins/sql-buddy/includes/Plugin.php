<?php
/**
 * Plugin initialization file.
 *
 * @package   SQL_Buddy
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://github.com/deliciousbrains/sql-buddy/
 */

namespace SQL_Buddy;

use SQL_Buddy\REST_API\Table_Controller;

/**
 * Class Plugin
 */
class Plugin {

	/**
	 * Dashboard.
	 *
	 * @var Dashboard
	 */
	public $dashboard;

	/**
	 * User Preferences class.
	 *
	 * @var User_Preferences
	 */
	public $user_preference;

	/**
	 * Initialize plugin functionality.
	 *
	 * @return void
	 */
	public function register() {
		$this->user_preference = new User_Preferences();
		add_action( 'init', array( $this->user_preference, 'register' ) );

		$this->dashboard = new Dashboard();
		add_action( 'init', array( $this->dashboard, 'init' ) );

		// REST API endpoints.
		// High priority so it runs after create_initial_rest_routes().
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ], 100 );
	}

	/**
	 * Registers REST API routes.
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		$table_controller = new Table_Controller( $this->user_preference );
		$table_controller->register_routes();
	}
}
