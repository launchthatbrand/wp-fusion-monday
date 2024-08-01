<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function get_post_meta_keys( $post_type ) {
    global $wpdb;

    // Query to get all meta keys for the specified post type
    $query = $wpdb->prepare("
        SELECT DISTINCT pm.meta_key
        FROM {$wpdb->postmeta} pm
        JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_type = %s
    ", $post_type);

    // Get the results
    $meta_keys = $wpdb->get_col($query);

    return $meta_keys;
}

function format_post_meta_keys( $post_type ) {
	$meta_keys = get_post_meta_keys( $post_type );
	$standard_fields = get_standard_post_fields();

	// Combine standard fields and custom meta keys
	$all_fields = array_merge( array_keys( $standard_fields ), $meta_keys );
	$meta_fields = array();

	foreach ( $all_fields as $key ) {
		$label = isset( $standard_fields[ $key ] ) ? $standard_fields[ $key ] : $key;
		$meta_fields[ $key ] = array(
			'label' => $label,
			'type'  => 'text', // Assuming all fields are text for simplicity
			'group' => 'post_meta_fields',
		);
	}

	return $meta_fields;
}

// function get_board_fields( $board_id ) {
//     $api_key = wpf_get_option('custom_key');

//     if ( empty( $api_key ) || empty( $board_id ) ) {
//         return array();
//     }

//     $query = '{"query": "{ boards (ids: [' . $board_id . ']) { columns { id title } } }"}';
    
//     $response = wp_safe_remote_post(
//         'https://api.monday.com/v2',
//         array(
//             'method'  => 'POST',
//             'headers' => array(
//                 'Authorization' => $api_key,
//                 'Content-Type'  => 'application/json',
//             ),
//             'body'    => $query,
//         )
//     );

//     if ( is_wp_error( $response ) ) {
//         return array();
//     }

//     $body = json_decode( wp_remote_retrieve_body( $response ), true );

//     if ( isset( $body['errors'] ) && ! empty( $body['errors'] ) ) {
//         return array();
//     }

//     $fields = array();

//     if ( ! empty( $body['data']['boards'][0]['columns'] ) ) {
//         foreach ( $body['data']['boards'][0]['columns'] as $column ) {
//             $fields[ $column['id'] ] = $column['title'];
//         }
//     }

//     return $fields;
// }

function wpf_render_post_field_select( $setting, $meta_name, $field_id = false, $field_sub_id = false, $post_type = 'post' ) {

    BugFu::log("wpf_render_post_field_select init");
	BugFu::log($setting);
    
    if ( doing_action( 'show_field_post_field' ) ) {
        $name = $meta_name . '[' . $field_id . ']';
    } elseif ( false === $field_id ) {
        $name = $meta_name . '[post_field]';
    } elseif ( false === $field_sub_id ) {
        $name = $meta_name . '[' . $field_id . '][post_field]';
    } else {
        $name = $meta_name . '[' . $field_id . '][' . $field_sub_id . '][post_field]';
    }

    if ( false === $field_id ) {
        $id = sanitize_html_class( $meta_name );
    } else {
        $id = sanitize_html_class( $meta_name ) . '-' . $field_id;
    }

    echo '<select id="' . esc_attr( $id . ( ! empty( $field_sub_id ) ? '-' . $field_sub_id : '' ) ) . '" class="select4-crm-field" name="' . esc_attr( $name ) . '" data-placeholder="Select a field">';
    echo '<option></option>';

    $board_id = wp_fusion()->settings->get( 'post_type_sync_' . $post_type );
    $post_fields = wpf_get_option( 'post_type_fields_' . $post_type );

    // BugFu::log("post fields:");
    // BugFu::log($post_fields, false);

    if ( ! empty( $post_fields ) ) {
        foreach ( $post_fields as $field => $label ) {
            echo '<option ' . selected( esc_attr( $setting ), $field, false ) . ' value="' . esc_attr( $field ) . '">' . esc_html( $label ) . '</option>';
        }
    }

    echo '</select>';
}
