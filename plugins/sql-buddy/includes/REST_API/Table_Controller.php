<?php
/**
 * Table class.
 *
 * @package   SQL_Buddy
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://github.com/deliciousbrains/sql-buddy/
 */

namespace SQL_Buddy\REST_API;

use SQL_Buddy\DB;
use SQL_Buddy\User_Preferences;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Request;

/**
 * Class Table
 *
 * @package SQL_Buddy\REST_API
 */
class Table_Controller extends WP_REST_Controller {

	/**
	 * SQL Buddy DB class.
	 *
	 * @var DB
	 */
	private $db;

	/**
	 * User Preferences instance class.
	 *
	 * @var User_Preferences
	 */
	private $user_preferences;

	/**
	 * Table_Controller constructor.
	 *
	 * @param User_Preferences $user_preferences User Preferences instance.
	 */
	public function __construct( User_Preferences $user_preferences ) {
		$this->namespace        = 'sqlbuddy/v1';
		$this->rest_base        = 'table';
		$this->db               = new DB();
		$this->user_preferences = $user_preferences;
	}

	/**
	 * Get REST API base endpoint.
	 *
	 * @param string $slug Endpoint slug.
	 *
	 * @return string
	 */
	private function get_rest_base( $slug ): string {
		return '/' . $this->rest_base . '/' . $slug;
	}

	/**
	 * Registers routes for Table.
	 *
	 * @see register_rest_route()
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->get_rest_base( 'get-records' ),
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_table_records' ),
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(
						'table_name' => array(
							'description' => __( 'Name of the table.', 'sql-buddy' ),
							'type'        => 'string',
							'required'    => true,
						),
						'limit'      => array(
							'description'       => __( 'The number of table records to show.', 'sql-buddy' ),
							'type'              => 'integer',
							'default'           => DB::get_page_size(),
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
						'offset'     => array(
							'description'       => __( 'The number of table records to offset to skip.', 'sql-buddy' ),
							'type'              => 'integer',
							'default'           => 0,
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->get_rest_base( 'delete-record' ),
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_record' ),
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(
						'table' => array(
							'description' => __( 'Name of table you want to delete.', 'sql-buddy' ),
							'type'        => 'string',
							'required'    => true,
						),
						'key'   => array(
							'description' => __( 'Table\'s primary key for WHERE clause.', 'sql-buddy' ),
							'type'        => 'string',
							'required'    => true,
						),
						'value' => array(
							'description' => __( 'Table\'s value for WHERE clause to match against key argument.', 'sql-buddy' ),
							'type'        => 'string',
							'required'    => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->get_rest_base( 'update-record' ),
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_record' ),
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(
						'table' => array(
							'description' => __( 'Name of table you want to delete.', 'sql-buddy' ),
							'type'        => 'string',
							'required'    => true,
						),
						'data'  => array(
							'description' => __( 'Table\'s row data in key/value pair.', 'sql-buddy' ),
							'type'        => 'object',
							'required'    => true,
						),
						'where' => array(
							'description' => __( 'Table\'s row data in key/value pair for WHERE clause.', 'sql-buddy' ),
							'type'        => 'object',
							'required'    => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->get_rest_base( 'hidden-columns' ),
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'set_hidden_columns' ),
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(
						'table'   => array(
							'description' => __( 'Name of table you want of which hidden columns should be set.', 'sql-buddy' ),
							'type'        => 'string',
							'required'    => true,
						),
						'columns' => array(
							'description' => __( 'Columns list which should be hidden.', 'sql-buddy' ),
							'type'        => 'array',
							'required'    => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->get_rest_base( 'overview' ),
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => function() {
						return DB::get_overview();
					},
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
				),
			)
		);
	}

	/**
	 * Fetch records for specified table.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_table_records( WP_REST_Request $request ) {
		$table_name = filter_var( $request['table_name'], FILTER_SANITIZE_STRING );
		$where      = $this->prepare_where_sql_query( $request );
		$limit      = filter_var( $request['limit'], FILTER_VALIDATE_INT ) ?? $this->db::get_page_size();
		$offset     = filter_var( $request['offset'], FILTER_VALIDATE_INT ) ?? 0;
		$orderby    = $this->prepare_orderby_sql_query( $request );

		list( $count, $pages ) = $this->db->get_pages_in_table( $table_name, $where );

		$response = new WP_REST_Response(
			array(
				'columns'       => $this->db->get_columns( $table_name ),
				'data'          => $this->db->get_table_records( $table_name, $limit, $offset, $where, $orderby ),
				'count'         => $count,
				'pages'         => $pages,
				'schema'        => $this->db->get_schema( $table_name ),
				'hiddenColumns' => $this->user_preferences->prepare_hidden_columns_for_js( $table_name ),
			)
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Prepare WHERE clause from user input.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return string|null
	 */
	protected function prepare_where_sql_query( WP_REST_Request $request ) {
		if ( ! isset( $request['filters'] ) ) {
			return null;
		}

		$filters = json_decode( $request['filters'], true );

		if ( ! is_array( $filters ) || empty( $filters ) ) {
			return null;
		}

		$where_sql = [];
		foreach ( $filters as $filter ) {
			$input   = esc_sql( $filter['input'] );
			$compare = esc_sql( $filter['filter'] );
			$column  = esc_sql( $filter['column'] );

			switch ( $filter['filter'] ) {
				case 'IS NULL':
				case 'IS NOT NULL':
					$input = null;
					break;

				case 'IN':
				case 'NOT IN':
					$parts  = explode( ',', $input );
					$return = [];
					foreach ( $parts as $part ) {
						$part = trim( $part );
						if ( ! is_int( $part ) ) {
							$part = "'{$part}'";
						}
						$return[] = $part;
					}
					$input = '(' . implode( ',', $return ) . ')';
					break;

				case 'BETWEEN':
				case 'NOT BETWEEN':
					$input = $input;
					break;

				case 'LIKE':
				case 'NOT LIKE':
					$input = "'%{$input}%'";
					break;

				default:
					if ( ! is_int( $input ) ) {
						$input = "'{$input}'";
					}
					break;
			}

			$where_sql[] = "`{$column}` {$compare} {$input}";
		}

		$where_sql = implode( ' AND ', $where_sql );

		return " WHERE ({$where_sql})";
	}

	/**
	 * Prepare ORDER BY clause from user request.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return string|null
	 */
	protected function prepare_orderby_sql_query( WP_REST_Request $request ) {
		if ( ! isset( $request['orderBy'] ) ) {
			return null;
		}

		$orderby = json_decode( $request['orderBy'], true );

		if ( ! is_array( $orderby ) || empty( $orderby ) ) {
			return null;
		}

		$orderby_sql = [];

		foreach ( $orderby as $clause ) {
			$order         = $clause['desc'] ? 'DESC' : 'ASC';
			$orderby_sql[] = "`{$clause['id']}` {$order} ";
		}

		$orderby_sql = ' ORDER BY ' . implode( ', ', $orderby_sql );

		return esc_sql( $orderby_sql );
	}

	/**
	 * Delete a single row.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|\WP_HTTP_Response|WP_REST_Response
	 */
	public function delete_record( WP_REST_Request $request ) {
		$deleted = $this->db->delete_record( $request['table'], $request['key'], $request['value'] );

		$response = new WP_REST_Response( [ 'deleted' => $deleted ] );

		return rest_ensure_response( $response );
	}

	/**
	 * Update a single row.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|\WP_HTTP_Response|WP_REST_Response
	 */
	public function update_record( WP_REST_Request $request ) {
		$updated = $this->db->update_record( $request['table'], $request['data'], $request['where'] );

		$response = new WP_REST_Response( [ 'updated' => $updated ] );

		return rest_ensure_response( $response );
	}

	public function set_hidden_columns( WP_REST_Request $request ) {
		$table   = $request['table'];
		$columns = $request['columns'];

		$updated = $this->user_preferences->set_hidden_columns( $table, $columns );

		$response = new WP_REST_Response( [ 'updated' => $updated ] );

		return rest_ensure_response( $response );
	}
}
