<?php
/**
 * DB class.
 *
 * @package   SQL_Buddy
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://github.com/deliciousbrains/sql-buddy/
 */

namespace SQL_Buddy;

/**
 * Class DB
 *
 * @package SQL_Buddy
 */
final class DB {

	/**
	 * WordPress Database class.
	 *
	 * @var \WPDB
	 */
	private $wpdb;

	/**
	 * DB constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Get list of tables.
	 *
	 * @return array
	 */
	public static function get_tables(): array {
		global $wpdb;

		return $wpdb->get_col( 'SHOW TABLES' );
	}

	/**
	 * Get table records.
	 *
	 * @param string $table_name Table name to get records from.
	 * @param int    $limit Records limit.
	 * @param int    $offset Records offset.
	 * @param string $where MySQL where clause. Optional.
	 * @param string $orderby MySQL `order by` clause. Optional.
	 *
	 * @return array|object|null
	 */
	public function get_table_records( $table_name, $limit = 50, $offset = 0, $where = null, $orderby = null ) {
		return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM `$table_name` {$where} {$orderby} LIMIT %d OFFSET %d", $limit, $offset ) );
	}

	/**
	 * Delete table row.
	 *
	 * @param string $table_name Table name to delete from.
	 * @param string $where_key WHERE key.
	 * @param string $where_value WHERE value.
	 *
	 * @return int|bool The number of rows updated, or false on error.
	 */
	public function delete_record( $table_name, $where_key, $where_value ) {
		return $this->wpdb->delete( $table_name, [ $where_key => $where_value ] );
	}

	/**
	 * Updte table row.
	 *
	 * @param string $table Table name to update.
	 * @param array  $data Data to update in key/value format.
	 * @param array  $where WHERE condition in key value format.
	 *
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function update_record( $table, $data, $where ) {
		return $this->wpdb->update( $table, $data, $where );
	}

	/**
	 * Get columns for a specific table.
	 *
	 * @param string $table Table name.
	 *
	 * @return array
	 */
	public function get_columns( $table ) {
		$primary_key = null;
		$columns     = array();
		$fields      = $this->wpdb->get_results( 'DESCRIBE ' . $table );

		if ( is_array( $fields ) ) {
			foreach ( $fields as $column ) {
				$columns[] = $column->Field;
				if ( $column->Key === 'PRI' ) {
					$primary_key = $column->Field;
				}
			}
		}

		return array( $primary_key, $columns );
	}

	/**
	 * Returns the rows count and pages in table.
	 *
	 * @param string $table Table name.
	 * @param string $where WHERE SQL cause.
	 * @return array
	 */
	public function get_pages_in_table( $table, $where = null ) {
		$table = esc_sql( $table );
		$rows  = (int) $this->wpdb->get_var( "SELECT COUNT(*) FROM `$table` {$where}" );
		$pages = ceil( $rows / self::get_page_size() );

		return [ $rows, $pages ];
	}

	/**
	 * Returns the current page size.
	 *
	 * @access public
	 * @return int
	 */
	public static function get_page_size() {
		return apply_filters( 'sql_buddy_page_size', 50 );
	}

	/**
	 * Return an array containing name and size of each table.
	 *
	 * @return array|object|null
	 */
	public static function get_sizes() {
		global $wpdb;

		static $return;
		$return = [];

		$sql = $wpdb->prepare(
			"SELECT TABLE_NAME AS 'table',
			ROUND( ( data_length + index_length ) / 1024 / 1024, 2 ) AS 'size'
			FROM INFORMATION_SCHEMA.TABLES
			WHERE table_schema = %s
			AND table_type = %s
			ORDER BY TABLE_NAME",
			DB_NAME,
			'BASE TABLE'
		);

		$results = $wpdb->get_results( $sql, ARRAY_A );

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$return[ $result['table'] ] = $result['size'];
			}
		}

		return $return;
	}

	public function get_schema( $table ) {
		static $return;
		$return = [];

		$sql = $this->wpdb->prepare(
			'SELECT COLUMN_NAME AS name,
			DATA_TYPE AS type
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_schema = %s
			AND table_name = %s',
			DB_NAME,
			$table
		);

		$results = $this->wpdb->get_results( $sql, ARRAY_A );

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$return[ $result['name'] ] = $result['type'];
			}
		}

		return $return;
	}

	public static function get_overview() {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT
				TABLE_NAME AS `name`,
				TABLE_ROWS AS `rows`,
				ROUND(data_length / 1024 / 1024, 2) AS `data_size`,
				ROUND(index_length / 1024 / 1024, 2) AS `index_size`
			FROM
				INFORMATION_SCHEMA.TABLES
			WHERE
				table_schema = %s
				AND table_type = %s
			ORDER BY TABLE_NAME',
			DB_NAME,
			'BASE TABLE'
		);

		$results = $wpdb->get_results( $sql, ARRAY_A );

		if ( ! empty( $results ) ) {
			foreach ( $results as $key => $result ) {
				$results[ $key ]['rows'] = number_format_i18n( $result['rows'] );
			}
		}

		return $results;
	}
}
