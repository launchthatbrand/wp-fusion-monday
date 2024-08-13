<?php
/**
 * Plugin initialization file.
 *
 * @package   SQL_Buddy
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://github.com/deliciousbrains/sql-buddy/
 */

namespace SQL_Buddy;

use SQL_Buddy\Traits\Assets;

/**
 * Class Dashboard
 *
 * @package SQL_Buddy
 */
class Dashboard {

	use Assets;

	/**
	 * Script/page handle.
	 *
	 * @var string
	 */
	const HANDLE = 'sql-buddy-dashboard';

	/**
	 * Admin page hook suffix.
	 *
	 * @var string|false The dashboard page's hook_suffix, or false if the user does not have the capability required.
	 */
	private $hook_suffix;

	/**
	 * Initialize the dashboard logic.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'toplevel_page_' . self::HANDLE, array( $this, 'load_dashboard' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add Dashboard page to menu.
	 *
	 * @return void
	 */
	public function add_menu_page() {
		$this->hook_suffix = add_management_page(
			__( 'SQL Buddy', 'sql-buddy' ),
			__( 'SQL Buddy', 'sql-buddy' ),
			'manage_options',
			self::HANDLE,
			array( $this, 'render' ),
			3
		);
	}

	/**
	 * Render dashboard page content.
	 *
	 * @return void
	 */
	public function render() {
		require_once SQL_BUDDY_PLUGIN_DIR_PATH . 'includes/templates/admin/dashboard.php';
	}

	/**
	 * Preload api requests for dashboard.
	 *
	 * @return void
	 */
	public function load_dashboard() {
		// Preload common data.
		$preload_paths = array(
			'/sqlbuddy/v1/table/overview',
		);

		/**
		 * Preload common data by specifying an array of REST API paths that will be preloaded.
		 *
		 * Filters the array of paths that will be preloaded.
		 *
		 * @param string[] $preload_paths Array of paths to preload.
		 */
		$preload_paths = apply_filters( 'sql_buddy_dashboard_preload_paths', $preload_paths );

		$preload_data = array_reduce(
			$preload_paths,
			'rest_preload_api_request',
			array()
		);

		wp_add_inline_script(
			'wp-api-fetch',
			sprintf( 'wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( %s ) );', wp_json_encode( $preload_data ) ),
			'after'
		);
	}

	/**
	 * Enqueue dashboard scripts.
	 *
	 * @param string $hook_suffix Page hook.
	 *
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( $this->hook_suffix !== $hook_suffix ) {
			return;
		}

		$this->enqueue_script( self::HANDLE, array( 'wp-components' ) );
		$this->enqueue_style( self::HANDLE, array( 'wp-components' ) );

		wp_localize_script(
			self::HANDLE,
			'sqlBuddyDashboardSettings',
			$this->get_dashboard_settings()
		);
	}

	/**
	 * Get dashboard settings as an array.
	 *
	 * @return array
	 */
	public function get_dashboard_settings(): array {
		$base = '/sqlbuddy/v1/table/';

		$settings = array(
			'id'     => self::HANDLE,
			'imgDir' => SQL_BUDDY_PLUGIN_DIR_URL . 'assets/images',
			'config' => array(
				'api'        => array(
					'records'       => $base . 'get-records',
					'deleteRecord'  => $base . 'delete-record',
					'updateRecord'  => $base . 'update-record',
					'hiddenColumns' => $base . 'hidden-columns',
					'overview'      => $base . 'overview',
				),
				'version'    => SQL_BUDDY_VERSION,
				'dateFormat' => get_option( 'date_format' ),
				'timeFormat' => get_option( 'time_format' ),
				'gmtOffset'  => get_option( 'gmt_offset' ),
				'tables'     => DB::get_tables(),
				'pageSize'   => DB::get_page_size(),
			),
		);

		/**
		 * Filters settings passed to the dashboard.
		 *
		 * @param array $settings Array of settings passed to dashboard.
		 */
		return apply_filters( 'sql_buddy_dashboard_settings', $settings );
	}
}
