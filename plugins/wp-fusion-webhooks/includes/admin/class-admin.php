<?php

class WPF_Webhooks_Admin {

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @return void
	*/

	public function __construct() {
		BugFu::log('WPF_Webhooks_Admin::__construct()');

		add_action( 'init', array( $this, 'register_post_type') );
		add_filter( 'wpf_configure_sections', array( $this, 'configure_sections' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box'), 10, 2 );
		add_action( 'save_post', array( $this, 'save_meta_box_data' ), 20 );

		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes'), 99, 2 );

		add_action( 'wp_ajax_wpf_webhook_test', array( $this, 'test_webhook' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_filter( 'manage_posts_columns', array( $this, 'manage_columns' ), 10, 2 );
		add_filter( 'manage_posts_custom_column', array( $this, 'manage_columns_content' ), 10, 2 );

	}

	/**
	 * Register JS file
	 *
	 * @since 1.0
	 * @return void
	*/

	public function admin_scripts(){

    	wp_enqueue_script( 'test_error', WPF_WEBHOOKS_DIR_URL . "assets/admin.js", array('jquery'), WPF_WEBHOOKS_VERSION);

	}

	/**
	 * Register webhooks post type 
	 *
	 * @access public
	 * @return void
	 */

	public function register_post_type() {

		$labels = array(
			'name'                  => _x( 'Webhooks', 'Post Type General Name', 'wp-fusion' ),
			'singular_name'         => _x( 'Webhook', 'Post Type Singular Name', 'wp-fusion' ),
			'menu_name'             => __( 'Webhooks', 'wp-fusion' ),
			'name_admin_bar'        => __( 'Webhook', 'wp-fusion' ),
			'archives'              => __( 'Webhook Archives', 'wp-fusion' ),
			'attributes'            => __( 'Webhook Attributes', 'wp-fusion' ),
			'parent_item_colon'     => __( 'Parent Webhook:', 'wp-fusion' ),
			'all_items'             => __( 'All Webhooks', 'wp-fusion' ),
			'add_new_item'          => __( 'Add New Webhook', 'wp-fusion' ),
			'add_new'               => __( 'Add New', 'wp-fusion' ),
			'new_item'              => __( 'New Webhook', 'wp-fusion' ),
			'edit_item'             => __( 'Edit Webhook', 'wp-fusion' ),
			'update_item'           => __( 'Update Webhook', 'wp-fusion' ),
			'view_item'             => __( 'View Webhook', 'wp-fusion' ),
			'view_items'            => __( 'View Webhooks', 'wp-fusion' ),
			'search_items'          => __( 'Search Webhooks', 'wp-fusion' ),
			'not_found'             => __( 'Not found', 'wp-fusion' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wp-fusion' ),
			'featured_image'        => __( 'Featured Image', 'wp-fusion' ),
			'set_featured_image'    => __( 'Set featured image', 'wp-fusion' ),
			'remove_featured_image' => __( 'Remove featured image', 'wp-fusion' ),
			'use_featured_image'    => __( 'Use as featured image', 'wp-fusion' ),
			'insert_into_item'      => __( 'Insert into item', 'wp-fusion' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wp-fusion' ),
			'items_list'            => __( 'Webhooks list', 'wp-fusion' ),
			'items_list_navigation' => __( 'Webhooks list navigation', 'wp-fusion' ),
			'filter_items_list'     => __( 'Filter items list', 'wp-fusion' ),
		);
		$args = array(
			'label'                 => __( 'Webhook', 'wp-fusion' ),
			'description'           => __( 'Webhook Description', 'wp-fusion' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => false,
			'menu_icon'				=> 'dashicons-networking',
			'menu_position'         => 30,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'rewrite'               => false,
			'capability_type'       => 'page',
			'show_in_rest'          => false,
		);

		register_post_type( 'wpf_webhook', $args );

	}


	/**
	 * Columns config
	 *
	 * @access public
	 * @return array Columns
	 */

	public function manage_columns( $columns, $post_type = null) {

		if( $post_type == 'wpf_webhook' ) {

			unset( $columns['wpf_settings'] );
			unset( $columns['date'] );

			$columns['webhook_status'] = 'Status';
			$columns['webhook_topic'] = 'Topic';
			$columns['webhook_url'] = 'Delivery URL';

		}

		return $columns;

	}

	/**
	 * Columns content
	 *
	 * @access public
	 * @return array Columns
	 */

	public function manage_columns_content( $column, $post_id ) {

		if ( $column == 'webhook_status' ) {

			$status = get_post_status( $post_id );

			if( $status == 'publish' ) {
				echo 'Active';
			} else {
				echo 'Paused';
			}

		} elseif ( $column == 'webhook_topic' ) {

			echo get_post_meta( $post_id, 'topic', true );

		} elseif ( $column == 'webhook_url' ) {

			echo get_post_meta( $post_id, 'delivery_url', true );

		}

	}


	/**
	 * Adds webhooks tab to main settings for access
	 *
	 * @access public
	 * @return array Page
	 */

	public function configure_sections( $page, $options ) {

		$page['sections'] = wp_fusion()->settings->insert_setting_after( 'advanced', $page['sections'], array( 'webhooks' => array('title' => __( 'Webhooks', 'wp-fusion' ), 'url' => admin_url( 'edit.php?post_type=wpf_webhook' ) ) ) );

		return $page;

	}


	/**
	 * Add webhook settings meta box
	 *
	 * @access public
	 * @return void
	 */

	public function add_meta_box( $post_type, $post ) {

		add_meta_box( 'wpf-webhook-settings', 'Webhook Settings', array( $this, 'meta_box_callback' ), 'wpf_webhook', 'normal', 'high' );

	}


	/**
	 * Displays meta box content
	 *
	 * @access public
	 * @return mixed
	 */

	public function meta_box_callback( $post ) {

		wp_nonce_field( 'wpf_meta_box_webhooks', 'wpf_meta_box_webhooks_nonce' );

		echo '<input type="hidden" name="webhooks_post_id" value="' . $post->ID . '" />';

		echo '<table class="form-table"><tbody>';

		echo '<tr>';

		echo '<th scope="row"><label for="tag_link">Topic:</label></th>';
		echo '<td>';

		$topics = array(
			'user_registered'	=> 'User Registered',
			'profile_updated'	=> 'Profile Updated',
			'tags_applied'		=> 'Tags Applied',
			'tags_removed'		=> 'Tags Removed',
			'tags_updated'		=> 'Tags Updated',
			'form_submitted'	=> 'Form Submitted',
		);

		$topic = get_post_meta( $post->ID, 'topic', true );

		echo '<select id="wpf-webhook-topic" class="select4" style="width: 400px; max-width: 100%;" data-placeholder="None" name="wpf_webhook_settings[topic]">';

		echo '<option></option>';

		foreach ( $topics as $id => $label ) {

			echo '<option value="' . $id . '"' . selected( $id, $topic, false ) . '>' . $label . '</option>';

		}

		echo '</select>';

		echo '<span class="description">Select when the webhook should be triggered.</span>';
		echo '</td>';

		echo '</tr>';

		if( $topic == 'tags_applied' || $topic == 'tags_removed' ) {
			$show = true;
		} else {
			$show = false;
		}

		echo '<tr id="which-tags-row">';

		echo '<th scope="row" ' . ( ! $show ? 'style="padding-top:0px; padding-bottom:0px;"' : '' ) . '>';

		echo '<div class="slidewrapper" ' . ( ! $show ? 'style="display: none"' : '' ) . '><label for="tag_link">Which Tags:</label></div></th>';

		echo '<td ' . ( ! $show ? 'style="padding-top:0px; padding-bottom:0px;"' : '' ) . '>';

		echo '<div class="slidewrapper" ' . ( ! $show ? 'style="display: none"' : '' ) . '>';

		$args = array(
			'setting' 		=> get_post_meta( $post->ID, 'tags', true ),
			'meta_name'		=> 'wpf_webhook_settings',
			'field_id'		=> 'tags'
		);

		wpf_render_tag_multiselect( $args );

		echo '<span class="description">Leave blank for any.</span>';

		echo '</div></td>';

		echo '</tr>';



		echo '<tr>';

		echo '<th scope="row"><label for="tag_link">Delivery URL:</label></th>';

		echo '<td>';

		echo '<input type="text" style="width: 400px; padding: 8px;" class="input-text regular-input" id="wpf-webhook-url" name="wpf_webhook_settings[delivery_url]" value="' . get_post_meta( $post->ID, 'delivery_url', true ) . '" />';

		echo '<a id="wpf-webhook-test" href="#" class="button" style="margin-left: 5px; margin-top: 4px;">Send Test</a>';

		echo '</td>';

		echo '</tr>';

		echo '<tr>';

		echo '<th scope="row"><label for="tag_link">Post Fields:</label><br /><br />';

		echo '<small><a href="#" id="wpf-webhooks-check-all">Check All</a> | <a href="#" id="wpf-webhooks-uncheck-all">Uncheck All</a></small>';

		echo '</th>';

		echo '<td>';

		echo '<ul id="wpf-webhook-post-fields">';

		$post_fields = get_post_meta( $post->ID, 'post_fields', true );

		if( empty( $post_fields ) ) {
			$post_fields = array();
		}

		$fields = wp_fusion()->settings->get( 'contact_fields', array() );

		// Put user ID and CID at the top

		$cid_temp = $fields[ wp_fusion()->crm->slug . '_contact_id' ];
		$tags_temp = $fields[ wp_fusion()->crm->slug . '_tags' ];
		$user_temp = $fields[ 'user_id' ];

		$cid_temp['active'] = true;
		$user_temp['active'] = true;
		$tags_temp['active'] = true;

		unset( $fields[wp_fusion()->crm->slug . '_contact_id'] );
		unset( $fields[wp_fusion()->crm->slug . '_tags'] );
		unset( $fields['user_id'] );

		$fields = array_merge( array( 'user_id' => $user_temp), array( wp_fusion()->crm->slug . '_contact_id' => $cid_temp ), array( wp_fusion()->crm->slug . '_tags' => $tags_temp ), $fields );

		if( empty( $post_fields ) ) {
			$set_defaults = true;
		} else {
			$set_defaults = false;
		}

		// Add in form fields option

		if( ! isset( $post_fields['form_fields'] ) ) {
			$post_fields['form_fields'] = false;
		}

		echo '<li class="show_if_form_submitted" style="' . ( $topic == 'form_submitted' ? '' : 'display: none;' ) . '">';

		echo '<input type="checkbox" id="wpf-webhook-field-form-fields" name="wpf_webhook_settings[post_fields][form_fields]" ' . checked( $post_fields['form_fields'], 1, false ) . ' value="1" />';
		echo '<label for="wpf-webhook-field-form-fields">Submitted Form Fields</label>';

		echo '</li>';

		foreach( $fields as $key => $field ) {

			if( $field['active'] == true && $set_defaults == true ) {
				$post_fields[$key] = true;
			}

			if( ! isset( $post_fields[$key] ) ) {
				$post_fields[$key] = false;
			}

			echo '<li>';

			echo '<input type="checkbox" id="wpf-webhook-field-' . $key . '" name="wpf_webhook_settings[post_fields][' . $key . ']" ' . checked( $post_fields[$key], 1, false ) . ' value="1" />';
			echo '<label for="wpf-webhook-field-' . $key . '">' . $key . '</label>';

			echo '</li>';

		}

		echo '</ul>';

		echo '</td>';

		echo '</tr>';

		echo '</tbody></table>';

	}


	/**
	 * Runs when WPF meta box is saved
	 *
	 * @access public
	 * @return void
	 */

	public function save_meta_box_data( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['wpf_meta_box_webhooks_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['wpf_meta_box_webhooks_nonce'], 'wpf_meta_box_webhooks' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Don't update on revisions
		if ( $_POST['post_type'] == 'revision' ) {
			return;
		}

		if ( isset( $_POST['wpf_webhook_settings'] ) ) {
			$data = $_POST['wpf_webhook_settings'];
		} else {
			$data = array();
		}

		if ( ! isset( $data['tags'] ) ) {
			$data['tags'] = array();
		}

		foreach( $data as $key => $value ) {

			// Update the meta field in the database.
			update_post_meta( $post_id, $key, $value );

		}

	}

	/**
	 * Send test
	 *
	 * @access public
	 * @return void
	*/

	public function test_webhook() {

		parse_str($_POST['data'], $data);

		$post_fields = $data['wpf_webhook_settings']['post_fields'];
		$delivery_url = $data['wpf_webhook_settings']['delivery_url'];
		$topic = $data['wpf_webhook_settings']['topic'];

		$user_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta( get_current_user_id() ) );

		$payload = array();

		foreach( $user_meta as $key => $value ) {

			if( isset( $post_fields[ $key ] ) && $post_fields[ $key ] == true ) {
				$payload[ $key ] = maybe_unserialize( $value );
			}

		}

		if( isset( $payload[ wp_fusion()->crm->slug . '_tags' ] ) ) {

			$payload[ wp_fusion()->crm->slug . '_tag_labels' ] = array();

			foreach( (array) $payload[ wp_fusion()->crm->slug . '_tags' ] as $tag_id ) {

				$payload[ wp_fusion()->crm->slug . '_tag_labels' ][] = wp_fusion()->user->get_tag_label( $tag_id );

			}

		}

		$args = array(
			'headers'     => array(
				'wpf-webhook-event' => $topic,
				'Content-type'		=> 'application/json'
			),
			'blocking' => false,
			'body'	=> json_encode( $payload )
		);

		wp_safe_remote_post( $delivery_url, $args );

		die();

	}


	/**
	 * Clean out any other meta boxes from the post type
	 *
	 * @access public
	 * @return void
	 */

	public function remove_meta_boxes( $post_type, $post ) {

		if( $post_type == 'wpf_webhook' ) {

			global $wp_meta_boxes;

			$exceptions = array(
				'submitdiv',
				'wpf-webhook-settings'
			);

			/** Loop through each page key of the '$wp_meta_boxes' global... */
			if( ! empty($wp_meta_boxes) ) {

				foreach($wp_meta_boxes as $page => $page_boxes) {

					/** Loop through each contect... */
					if(!empty($page_boxes)) {

						foreach($page_boxes as $context => $box_context) {

							/** Loop through each type of meta box... */
							if(!empty($box_context)) {

								foreach($box_context as $box_type) {

									/** Loop through each individual box... */
									if(!empty($box_type)) {

										foreach($box_type as $id => $box) {

											/** Check to see if the meta box should be removed... */
											if(!in_array($id, $exceptions)) {

												/** Remove the meta box */
												remove_meta_box($id, $page, $context);
											
											}
										}
									}
								}
							}
						}
					}
				}
			}

		}

	}

}

new WPF_Webhooks_Admin;