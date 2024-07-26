<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPF_Post_Type {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Post type actions
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );

		// Custom fields
		add_action( 'add_post_meta', array( $this, 'push_post_meta' ), 10, 4 );
		add_action( 'update_post_meta', array( $this, 'push_post_meta' ), 10, 4 );

	}

	/**
	 * Handles post saving
	 *
	 * @access public
	 * @return void
	 */

	public function save_post( $post_id, $post, $update ) {

		// Don't run on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Don't run for revisions
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Push post data to CRM
		$this->push_post_data( $post_id );

	}

	/**
	 * Handles post deletion
	 *
	 * @access public
	 * @return void
	 */

	public function before_delete_post( $post_id ) {

		// Handle post deletion in CRM

	}

	/**
	 * Handles post trashing
	 *
	 * @access public
	 * @return void
	 */

	public function trash_post( $post_id ) {

		// Handle post trashing in CRM

	}

	/**
	 * Handles post untrashing
	 *
	 * @access public
	 * @return void
	 */

	public function untrash_post( $post_id ) {

		// Handle post untrashing in CRM

	}

	/**
	 * Pushes post meta to CRM when it's added or updated
	 *
	 * @access public
	 * @return void
	 */

	public function push_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {

		// Push post meta to CRM

	}

	/**
	 * Pushes post data to CRM
	 *
	 * @access public
	 * @return void
	 */

	public function push_post_data( $post_id ) {

		// Get post data
		$post = get_post( $post_id );

		// Prepare data for CRM
		$post_data = array(
			'ID'           => $post->ID,
			'post_title'   => $post->post_title,
			'post_content' => $post->post_content,
			'post_status'  => $post->post_status,
			'post_type'    => $post->post_type,
		);

		// Push to CRM
		// wp_fusion()->crm->update_post( $post_data );

	}

}

new WPF_Post_Type();
<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPF_Post_Type {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Post type actions
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );

		// Custom fields
		add_action( 'add_post_meta', array( $this, 'push_post_meta' ), 10, 4 );
		add_action( 'update_post_meta', array( $this, 'push_post_meta' ), 10, 4 );

	}

	/**
	 * Handles post saving
	 *
	 * @access public
	 * @return void
	 */

	public function save_post( $post_id, $post, $update ) {

		// Don't run on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Don't run for revisions
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Push post data to CRM
		$this->push_post_data( $post_id );

	}

	/**
	 * Handles post deletion
	 *
	 * @access public
	 * @return void
	 */

	public function before_delete_post( $post_id ) {

		// Handle post deletion in CRM

	}

	/**
	 * Handles post trashing
	 *
	 * @access public
	 * @return void
	 */

	public function trash_post( $post_id ) {

		// Handle post trashing in CRM

	}

	/**
	 * Handles post untrashing
	 *
	 * @access public
	 * @return void
	 */

	public function untrash_post( $post_id ) {

		// Handle post untrashing in CRM

	}

	/**
	 * Pushes post meta to CRM when it's added or updated
	 *
	 * @access public
	 * @return void
	 */

	public function push_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {

		// Push post meta to CRM

	}

	/**
	 * Pushes post data to CRM
	 *
	 * @access public
	 * @return void
	 */

	public function push_post_data( $post_id ) {

		// Get post data
		$post = get_post( $post_id );

		// Prepare data for CRM
		$post_data = array(
			'ID'           => $post->ID,
			'post_title'   => $post->post_title,
			'post_content' => $post->post_content,
			'post_status'  => $post->post_status,
			'post_type'    => $post->post_type,
		);

		// Push to CRM
		// wp_fusion()->crm->update_post( $post_data );

	}

}

new WPF_Post_Type();
