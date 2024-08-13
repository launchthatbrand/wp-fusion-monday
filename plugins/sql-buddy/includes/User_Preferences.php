<?php

namespace SQL_Buddy;

class User_Preferences {
	/**
	 * Name of the meta key used for storing hidden columns.
	 *
	 * @var string
	 */
	const HIDDEN_COLUMNS_KEY = 'sqlbuddy_hidden_columns';

	/**
	 * User ID.
	 *
	 * @var false|int
	 */
	public $user_id;

	/**
	 * User_Preferences constructor.
	 *
	 * @param false|int $user_id User ID.
	 */
	public function __construct( $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$this->user_id = $user_id;
	}

	/**
	 * Register class.
	 */
	public function register() {
		register_meta(
			'user',
			static::HIDDEN_COLUMNS_KEY,
			[
				'type'          => 'array',
				'default'       => false,
				'show_in_rest'  => false,
				'auth_callback' => [ $this, 'can_edit_current_user' ],
				'single'        => false,
			]
		);
	}

	/**
	 * Auth callback.
	 *
	 * @return bool
	 */
	public function can_edit_current_user(): bool {
		return current_user_can( 'edit_user', get_current_user_id() );
	}

	/**
	 * Get a list of all hidden columns.
	 *
	 * @return array
	 */
	protected function get_all_hidden_columns() {
		$hidden_columns = get_user_meta( get_current_user_id(), static::HIDDEN_COLUMNS_KEY, true );

		if ( ! is_array( $hidden_columns ) ) {
			return array();
		}

		return $hidden_columns;
	}

	public function get_hidden_columns( $table ) {
		$columns = $this->get_all_hidden_columns();

		return $columns[ $table ] ?? [];
	}

	public function set_hidden_columns( $table, $columns ) {
		$hidden_columns = $this->get_all_hidden_columns();

		if ( is_array( $columns ) ) {
			$hidden_columns[ $table ] = $columns;
		} elseif ( isset( $hidden_columns[ $table ] ) ) {
			unset( $hidden_columns[ $table ] );
		}

		return update_user_meta( get_current_user_id(), static::HIDDEN_COLUMNS_KEY, $hidden_columns );
	}

	public function prepare_hidden_columns_for_js( $table ) {
		$columns = $this->get_hidden_columns( $table );

		if ( ! count( $columns ) ) {
			return [];
		}

		$formatted = [];
		foreach ( $columns as $column ) {
			$formatted[ $column ] = false;
		}

		return $formatted;
	}
}
