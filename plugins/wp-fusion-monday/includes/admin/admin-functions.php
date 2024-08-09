<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wpf_render_workspace_multiselect( $args = array() ) {

	$defaults = array(
		'setting'     => array(),
		'meta_name'   => null,
		'field_id'    => null,
		'disabled'    => false,
		'placeholder' => __( 'Select workspaces', 'wp-fusion' ),
		'class'       => '',
		'return'      => false,
	);

	$args = wp_parse_args( $args, $defaults );

	// Get the field ID
	if ( false === $args['field_id'] ) {
		$field_id = sanitize_html_class( $args['meta_name'] );
	} else {
		$field_id = sanitize_html_class( $args['meta_name'] ) . '-' . $args['field_id'];
	}

	$available_workspaces = wp_fusion()->settings->get( 'available_workspaces', array() );

	// Ensure the setting is an array
	if ( ! is_array( $args['setting'] ) ) {
		$args['setting'] = (array) $args['setting'];
	}

	// If we're returning instead of echoing.
	if ( $args['return'] ) {
		ob_start();
	}

	// Start generating the HTML for the multiselect
	echo '<select';
		echo ( true == $args['disabled'] ? ' disabled' : '' );
		echo ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		echo ' multiple="multiple"';
		echo ' id="' . esc_attr( $field_id ) . '"';
		echo ' class="select4-wpf-workspaces ' . esc_attr( $args['class'] ) . '"';
		echo ' name="' . esc_attr( $args['meta_name'] ) . '[]"';
	echo '>';

	// Output the workspaces <option> elements
	foreach ( $available_workspaces as $id => $workspace ) {
		$is_selected = in_array( $id, $args['setting'], $strict = false );
		echo '<option value="' . esc_attr( $id ) . '" ' . selected( true, $is_selected, false ) . '>' . esc_html( $workspace ) . '</option>';
	}

	echo '</select>';

	// Return or echo the output
	if ( $args['return'] ) {
		return ob_get_clean();
	}
}
