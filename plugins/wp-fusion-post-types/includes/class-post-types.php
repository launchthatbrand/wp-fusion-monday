<?php

// if ( ! defined( 'ABSPATH' ) ) {
// 	exit; // Exit if accessed directly
// }

/**
 * Post Type Sync Integration
 */
class WPF_Post_Type_Sync_Integration extends WPF_Integrations_Base {


	public $slug = 'post-type-sync';

	public $name = 'Post Type Sync';

	public function init() {

		// Add tabs for post types
		add_filter( 'wpf_settings_tabs', array( $this, 'add_post_type_tabs' ) );
        add_filter( 'wpf_configure_sections', array( $this, 'configure_sections' ), 20, 2 );

		

		// Global settings
		add_filter( 'wpf_configure_settings', array( $this, 'register_settings' ), 15, 2 );

		

		// Render field mapping
		add_action( 'wpf_settings_page_content', array( $this, 'render_post_type_field_mapping' ) );

		// add_action( 'show_field_contact_fields2', array( $this, 'show_field_post_fields' ), 15, 2 );
		// add_action( 'show_field_contact_fields2_begin', array( $this, 'show_field_post_fields_begin' ), 15, 2 );

		// Save field mappings
		add_action( 'admin_init', array( $this, 'save_field_mappings' ) );

		// Post type actions
		add_action( 'post_updated', array( $this, 'post_updated' ), 10 );

		// Register dynamic actions for all post types
		$this->register_dynamic_actions();
	}

	private function register_dynamic_actions() {
		$post_types = get_post_types(array('public' => true), 'objects');
		$options = get_option('wpf_options');
		
		foreach ($post_types as $post_type) {
			if (isset($options['post_type_sync_' . $post_type->name]) && !empty($options['post_type_sync_' . $post_type->name])) {
				// $board_id = $options['post_type_sync_' . $post_type->name];
	
				add_action("show_field_{$post_type->name}_fields", array($this, 'show_field_postType_fields'), 15, 2);
				add_action("show_field_{$post_type->name}_fields_begin", array($this, 'show_field_postType_fields_begin'), 15, 2);
			}
		}
	}
	
	

	public function post_updated() {
		BugFu::log("post_updated");
		
		$test = wp_fusion()->crm->app->api("item/add");

		BugFu::log($test);
		
		// BugFu::log("CRM class: " . get_class($crm));
		
		// if ($crm instanceof WPF_Custom) {
		// 	$test = $crm->app;
		// 	BugFu::log($test, false);
		// } else {
		// 	BugFu::log("CRM is not an instance of WPF_Custom. It is an instance of: " . get_class($crm), false);
		// }
	}

	// public function post_updated() {
	// 	BugFu::log("post_updated");
	// 	$test = wp_fusion()->crm;
	// 	BugFu::log($test, false);

	// 	// // Don't run on autosave
	// 	// if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	// 	// 	return;
	// 	// }

	// 	// // Don't run for revisions
	// 	// if ( wp_is_post_revision( $post_id ) ) {
	// 	// 	return;
	// 	// }

	// 	// // Push post data to CRM
	// 	// $this->push_post_data( $post_id );

	// }

	public function register_settings( $settings, $options ) {
		$settings['post_type_sync_header'] = array(
			'title'   => __( 'Post Type Sync', 'wp-fusion' ),
			'type'    => 'heading',
			'section' => 'post-types',
		);

		$exclude_post_types = array('revision', 'nav_menu_item', 'page'); // Add post types you want to exclude

		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$boards = wp_fusion()->settings->get( 'available_lists', array() );
		BugFu::log($boards);

		foreach ( $post_types as $post_type ) {
			if (in_array($post_type->name, $exclude_post_types)) {
				continue; // Skip excluded post types
			}

			$settings['post_type_sync_' . $post_type->name] = array(
				'title'   => $post_type->label,
				'type'    => 'select',
				'section' => 'post-types',
				'choices' => $boards,
			);
		}

        $settings['post_fields'] = array(
			'title'   => __( 'Contact Fields2', 'wp-fusion-lite' ),
			'std'     => array(),
			'type'    => 'post-fields',
			'section' => 'post-fields',
			'choices' => array(),
		);

		return $settings;
	}

	public function show_field_postType_fields( $id, $field ) {
		$post_type = 'post'; // Replace with dynamic post type if needed
	
		// Group post fields by meta keys
		$field_groups = array(
			'post_meta_fields' => array(
				'title'  => __( 'Post Meta Fields', 'wp-fusion-lite' ),
				'fields' => format_post_meta_keys( $post_type ),
			),
		);
	
		if ( empty( $this->options[ $id ] ) ) {
			$this->options[ $id ] = array();
		}
	
		// Set some defaults to prevent notices, and then rebuild fields array into group structure.
	
		foreach ( $field_groups['post_meta_fields']['fields'] as $meta_key => $data ) {
			if ( empty( $this->options[ $id ][ $meta_key ] ) || ! isset( $this->options[ $id ][ $meta_key ]['crm_field'] ) || ! isset( $this->options[ $id ][ $meta_key ]['active'] ) ) {
				$this->options[ $id ][ $meta_key ] = array(
					'active'    => false,
					'crm_field' => false,
				);
			}
		}
	
		echo '<p>' . sprintf( esc_html__( 'For more information on these settings, %1$ssee our documentation%2$s.', 'wp-fusion-lite' ), '<a href="https://wpfusion.com/documentation/getting-started/syncing-contact-fields/" target="_blank">', '</a>' ) . '</p>';
		echo '<br />';
	
		// Display contact fields table.
		echo '<table id="contact-fields-table" class="table table-hover">';

		echo '<thead>';
		echo '<tr>';
		echo '<th class="sync">' . esc_html__( 'Sync', 'wp-fusion' ) . '</th>';
		echo '<th>' . esc_html__( 'Names', 'wp-fusion' ) . '</th>';
		echo '<th>' . esc_html__( 'Meta Field', 'wp-fusion' ) . '</th>';
		echo '<th>' . esc_html__( 'Type', 'wp-fusion' ) . '</th>';
		echo '<th>' . sprintf( esc_html__( '%s Field', 'wp-fusion' ), esc_html( wp_fusion()->crm->name ) ) . '</th>';
		echo '</tr>';
		echo '</thead>';
	
		foreach ( $field_groups as $group => $group_data ) {
			if ( empty( $group_data['fields'] ) ) {
				continue;
			}
	
			// Output group section headers.
			if ( empty( $group_data['title'] ) ) {
				$group_data['title'] = 'none';
			}
	
			echo '<tbody class="labels">';
			echo '<tr class="group-header"><td colspan="5">';
			echo '<label for="' . esc_attr( $group ) . '" class="group-header-title">';
			echo wp_kses_post( $group_data['title'] );
			echo '<i class="fa fa-angle-down"></i><i class="fa fa-angle-up"></i></label>';
			echo '</td></tr>';
			echo '</tbody>';
	
			echo '<tbody class="table-collapse">';
	
			foreach ( $group_data['fields'] as $meta_key => $data ) {
				echo '<tr' . ( $this->options[ $id ][ $meta_key ]['active'] == true ? ' class="success"' : '' ) . '>';
				echo '<td><input class="checkbox" type="checkbox" id="wpf_cb_' . esc_attr( $meta_key ) . '" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $meta_key ) . '][active]" value="1" ' . checked( $this->options[ $id ][ $meta_key ]['active'], 1, false ) . '/></td>';
				echo '<td class="wp_field_label">' . esc_html( $data['label'] ) . '</td>';
				echo '<td><span class="label label-default">' . esc_html( $meta_key ) . '</span></td>';
				echo '<td class="wp_field_type">';
	
				echo '<select class="wpf_type" name="wpf_options[' . esc_attr( $id ) . '][' . esc_attr( $meta_key ) . '][type]">';
				$field_types = array( 'text', 'date', 'multiselect', 'checkbox', 'state', 'country', 'int', 'raw', 'tel' );
				foreach ( $field_types as $type ) {
					echo '<option value="' . esc_attr( $type ) . '" ' . selected( $data['type'], $type, false ) . '>' . esc_html( $type ) . '</option>';
				}
				echo '</select>';
	
				echo '<td>';
				wpf_render_post_field_select( $this->options[ $id ][ $meta_key ]['crm_field'], 'wpf_options', $id, $meta_key, $post_type );
				echo '</td>';
	
				echo '</tr>';
			}
	
			echo '</tbody>';
		}
	
		// Add new field row.
		echo '<tr>';
		echo '<td><input class="checkbox contact-fields-checkbox" type="checkbox" disabled id="wpf_cb_new_field" name="wpf_options[' . esc_attr( $id ) . '][new_field][active]" value="1" /></td>';
		echo '<td class="wp_field_label">Add new field</td>';
		echo '<td><input type="text" id="wpf-add-new-field" name="wpf_options[' . esc_attr( $id ) . '][new_field][key]" placeholder="New Field Key" /></td>';
		echo '<td class="wp_field_type">';
	
		echo '<select class="wpf_type" name="wpf_options[' . esc_attr( $id ) . '][new_field][type]">';
		foreach ( $field_types as $type ) {
			echo '<option value="' . esc_attr( $type ) . '">' . esc_html( $type ) . '</option>';
		}
		echo '</select>';
	
		echo '<td>';
		wpf_render_post_field_select( false, 'wpf_options', $id, 'new_field', $post_type );
		echo '</td>';
	
		echo '</tr>';

		echo '</tbody>';

		echo '</table>';

	}
	
	
	
	

	public function show_field_postType_fields_begin( $id, $field ) {

		if ( ! isset( $field['disabled'] ) ) {
			$field['disabled'] = false;
		}

		echo '<tr valign="top"' . ( $field['disabled'] ? ' class="disabled"' : '' ) . '>';
		echo '<td style="padding:0px">';
	}





	// public function add_post_type_tabs( $tabs ) {
		
	// 	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	// 	foreach ( $post_types as $post_type ) {
	// 		$board_id = get_option( 'wpf_post_type_sync_' . $post_type->name );
	// 		if ( $board_id ) {
	// 			$tabs['wpf_' . $post_type->name . '_fields'] = $post_type->label . ' Fields';
	// 		}
	// 	}

		

	// 	return $tabs;
	// }

	// public function render_post_type_field_mapping( $tab ) {
	// 	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	// 	foreach ( $post_types as $post_type ) {
	// 		if ( 'wpf_' . $post_type->name . '_fields' === $tab ) {
	// 			$board_id = get_option( 'wpf_post_type_sync_' . $post_type->name );
	// 			$fields = $this->get_post_type_fields( $post_type->name );
	// 			$board_columns = $this->get_monday_board_columns( $board_id );

	// 			echo '<h2>' . sprintf( __( '%s Fields', 'wp-fusion' ), $post_type->label ) . '</h2>';
	// 			echo '<table class="form-table">';
	// 			foreach ( $fields as $field_key => $field_label ) {
	// 				echo '<tr>';
	// 				echo '<th scope="row">' . esc_html( $field_label ) . '</th>';
	// 				echo '<td>';
	// 				echo '<select name="wpf_field_mapping[' . esc_attr( $post_type->name ) . '][' . esc_attr( $field_key ) . ']">';
	// 				echo '<option value="">' . __( 'Select a column', 'wp-fusion' ) . '</option>';
	// 				foreach ( $board_columns as $column_id => $column_name ) {
	// 					echo '<option value="' . esc_attr( $column_id ) . '">' . esc_html( $column_name ) . '</option>';
	// 				}
	// 				echo '</select>';
	// 				echo '</td>';
	// 				echo '</tr>';
	// 			}
	// 			echo '</table>';
	// 			submit_button();
	// 		}
	// 	}
	// }

	public function get_post_type_fields( $post_type ) {
		// Fetch custom fields for the post type
		// This is a simplified example. You might need to adjust it to fit your actual fields
		return array(
			'field_1' => 'Field 1',
			'field_2' => 'Field 2',
		);
	}

	public function get_monday_board_columns( $board_id ) {
		// Fetch columns from Monday.com API for the specified board
		// Replace with your existing function to get board columns
		return array(
			'col_1' => 'Column 1',
			'col_2' => 'Column 2',
		);
	}

	public function save_field_mappings() {
		if ( isset( $_POST['wpf_field_mapping'] ) ) {
			foreach ( $_POST['wpf_field_mapping'] as $post_type => $fields ) {
				update_option( 'wpf_field_mapping_' . $post_type, $fields );
			}
		}
	}

    public function configure_sections( $page, $options ) {

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		foreach ( $post_types as $post_type ) {
            $board_id = wpf_get_option( 'post_type_sync_' . $post_type->name );
            if ( $board_id ) {
				$page['sections'] = wp_fusion()->settings->insert_setting_after(
					'contact-fields',
					$page['sections'],
					array(
						$post_type->name . '-fields' => sprintf( __( '%s Fields', 'wp-fusion' ), $post_type->label ),
						),
				);
                // $page['sections'][ $post_type->name . '_fields' ] = sprintf( __( '%s Fields', 'wp-fusion' ), $post_type->label ) . ' →';
            }
        }

		$page['sections'] = wp_fusion()->settings->insert_setting_after(
			'advanced',
			$page['sections'],
			array(
				'post-types' => 'Post Type Sync',
				),
		);

		
    
        return $page;
    }
}

new WPF_Post_Type_Sync_Integration();
