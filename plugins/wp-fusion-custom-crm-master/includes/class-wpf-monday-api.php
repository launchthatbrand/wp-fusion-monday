<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class for interacting with Monday.com API
 */
class WPF_Monday_API {

	public static function get_monday_boards() {
		$api_key = wpf_get_option( 'custom_key' );

		if ( empty( $api_key ) ) {
			return array();
		}

		$query = '{"query": "{ boards { id name } }"}';
		$response = wp_safe_remote_post(
			'https://api.monday.com/v2',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => $query,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['data']['boards'] ) ) {
			return array();
		}

		$boards = array();

		foreach ( $body['data']['boards'] as $board ) {
			$boards[ $board['id'] ] = $board['name'];
		}

		return $boards;
	}

	// public static function get_monday_board_columns( $board_id ) {
	// 	$api_key = wpf_get_option( 'custom_key' );

	// 	if ( empty( $api_key ) || empty( $board_id ) ) {
	// 		return array();
	// 	}

	// 	$query = '{"query": "{ boards (ids: [' . $board_id . ']) { columns { id title } } }"}';
	// 	$response = wp_safe_remote_post(
	// 		'https://api.monday.com/v2',
	// 		array(
	// 			'method'  => 'POST',
	// 			'headers' => array(
	// 				'Authorization' => $api_key,
	// 				'Content-Type'  => 'application/json',
	// 			),
	// 			'body'    => $query,
	// 		)
	// 	);

	// 	if ( is_wp_error( $response ) ) {
	// 		return array();
	// 	}

	// 	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	// 	if ( empty( $body['data']['boards'][0]['columns'] ) ) {
	// 		return array();
	// 	}

	// 	$columns = array();

	// 	foreach ( $body['data']['boards'][0]['columns'] as $column ) {
	// 		$columns[ $column['id'] ] = $column['title'];
	// 	}

	// 	return $columns;
	// }
}
