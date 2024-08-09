<?php

class WPF_Webhooks_Public {

	public $webhook_queue = array();

	public $webhook_data = array();

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @return void
	*/

	public function __construct() {

		add_action( 'wpf_pushed_user_meta', array( $this, 'profile_updated' ), 10, 3 );
		add_action( 'wpf_user_created', array( $this, 'user_register' ), 10, 3 );

		add_action( 'wpf_tags_applied', array( $this, 'tags_applied' ), 10, 2 );
		add_action( 'wpf_tags_removed', array( $this, 'tags_removed' ), 10, 2 );
		add_action( 'wpf_tags_modified', array( $this, 'tags_modified' ), 10, 2 );

		add_action( 'wpf_forms_post_submission', array( $this, 'form_submitted' ), 10, 3 );

		register_shutdown_function( array( $this, 'shutdown' ) );

	}

	/**
	 * Queue webhook when profile updated
	 *
	 * @access public
	 * @return void
	 */

	public function profile_updated( $user_id, $contact_id, $post_data ) {

		$args = array(
			'fields'		=> 'ids',
			'post_type'		=> 'wpf_webhook',
			'nopaging'		=> true,
			'meta_key'		=> 'topic',
			'meta_value'	=> 'profile_updated'
		);

		$webhooks = get_posts( $args );

		if( ! empty( $webhooks ) ) {

			$this->push_to_queue( $user_id, $webhooks, 'profile_updated', $post_data );

		}

	}

	/**
	 * Queue webhook when user registers
	 *
	 * @access public
	 * @return void
	 */

	public function user_register( $user_id, $contact_id, $post_data ) {

		$args = array(
			'fields'		=> 'ids',
			'post_type'		=> 'wpf_webhook',
			'nopaging'		=> true,
			'meta_key'		=> 'topic',
			'meta_value'	=> 'user_registered'
		);

		$webhooks = get_posts( $args );

		if( ! empty( $webhooks ) ) {

			$this->push_to_queue( $user_id, $webhooks, 'user_registered', $post_data );

		}

	}

	/**
	 * Queue webhook when user registers
	 *
	 * @access public
	 * @return void
	 */

	public function tags_applied( $user_id, $tags ) {

		$args = array(
			'fields'		=> 'ids',
			'post_type'		=> 'wpf_webhook',
			'nopaging'		=> true,
			'meta_key'		=> 'topic',
			'meta_value'	=> 'tags_applied'
		);

		$webhooks = get_posts( $args );

		foreach( $webhooks as $i => $webhook_id ) {

			$which_tags = get_post_meta( $webhook_id, 'tags', true );

			if( ! empty( $which_tags ) ) {

				$match = array_intersect($tags, $which_tags);

				if( empty( $match ) ) {

					unset( $webhooks[$i] );

				}

			}

		}

		if( ! empty( $webhooks ) ) {

			$this->push_to_queue( $user_id, $webhooks, 'tags_applied' );

		}

	}

	/**
	 * Queue webhook when user registers
	 *
	 * @access public
	 * @return void
	 */

	public function tags_removed( $user_id, $tags ) {

		$args = array(
			'fields'		=> 'ids',
			'post_type'		=> 'wpf_webhook',
			'nopaging'		=> true,
			'meta_key'		=> 'topic',
			'meta_value'	=> 'tags_removed'
		);

		$webhooks = get_posts( $args );

		foreach( $webhooks as $i => $webhook_id ) {

			$which_tags = get_post_meta( $webhook_id, 'tags', true );

			if( ! empty( $which_tags ) ) {

				$match = array_intersect($tags, $which_tags);

				if( empty( $match ) ) {

					unset( $webhooks[$i] );

				}

			}

		}

		if( ! empty( $webhooks ) ) {

			$this->push_to_queue( $user_id, $webhooks, 'tags_removed' );

		}

	}

	/**
	 * Queue webhook tags modified
	 *
	 * @access public
	 * @return void
	 */

	public function tags_modified( $user_id, $tags ) {

		$args = array(
			'fields'		=> 'ids',
			'post_type'		=> 'wpf_webhook',
			'nopaging'		=> true,
			'meta_key'		=> 'topic',
			'meta_value'	=> 'tags_updated'
		);

		$webhooks = get_posts( $args );

		if( ! empty( $webhooks ) ) {

			$this->push_to_queue( $user_id, $webhooks, 'tags_updated' );

		}

	}

	/**
	 * Send webhook when form submitted
	 *
	 * @access public
	 * @return void
	 */

	public function form_submitted( $update_data, $user_id, $contact_id ) {

		$args = array(
			'fields'		=> 'ids',
			'post_type'		=> 'wpf_webhook',
			'nopaging'		=> true,
			'meta_key'		=> 'topic',
			'meta_value'	=> 'form_submitted'
		);

		$webhooks = get_posts( $args );

		if ( ! empty( $webhooks ) ) {

			foreach ( $webhooks as $webhook_id ) {

				$delivery_url = get_post_meta( $webhook_id, 'delivery_url', true );

				$update_data['user_id']                                = $user_id;
				$update_data[ wp_fusion()->crm->slug . '_contact_id' ] = $contact_id;

				$args = array(
					'headers'     => array(
						'wpf-webhook-event' => 'form_submitted',
						'Content-type'		=> 'application/json',
					),
					'blocking' => false,
					'body'     => json_encode( $update_data ),
				);

				wp_safe_remote_post( $delivery_url, $args );

			}
		}

	}

	/**
	 * Push webhooks to queue
	 *
	 * @access public
	 * @return void
	 */

	public function push_to_queue( $user_id, $webhooks, $topic, $data = false ) {

		if( ! isset( $this->webhook_queue[ $user_id ] ) ) {
			$this->webhook_queue[ $user_id ] = array();
		}

		if( ! isset( $this->webhook_queue[ $user_id ][ $topic ] ) ) {
			$this->webhook_queue[ $user_id ][ $topic ] = array();
		}

		$this->webhook_queue[ $user_id ][ $topic ] = array_unique( array_merge( $this->webhook_queue[ $user_id ][ $topic ], $webhooks ) );

		// Add data

		if ( false !== $data ) {

			if( ! isset( $this->webhook_data[ $user_id ] ) ) {
				$this->webhook_data[ $user_id ] = array();
			}

			if( ! isset( $this->webhook_data[ $user_id ][ $topic ] ) ) {
				$this->webhook_data[ $user_id ][ $topic ] = array();
			}

			$this->webhook_data[ $user_id ][ $topic ] = array_merge( $this->webhook_data[ $user_id ][ $topic ], $data );

		}

	}

	/**
	 * Executes the queued API requests on PHP shutdown
	 *
	 * @access  public
	 * @return  void
	 */

	public function shutdown() {

		if ( empty( $this->webhook_queue ) ) {
			return;
		}

		$used_urls = array();

		foreach ( $this->webhook_queue as $user_id => $topics ) {

			foreach ( $topics as $topic => $webhooks ) {

				foreach( $webhooks as $webhook_id ) {

					$delivery_url = get_post_meta( $webhook_id, 'delivery_url', true );

					// No need to hit the same URL twice
					if( in_array($delivery_url, $used_urls) ) {
						continue;
					}

					$post_fields = get_post_meta( $webhook_id, 'post_fields', true );
					$user_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta( $user_id ) );

					$userdata 						= get_userdata( $user_id );
					$user_meta['user_login'] 		= $userdata->user_login;
					$user_meta['user_email'] 		= $userdata->user_email;
					$user_meta['user_id'] 			= $user_id;
					$user_meta['user_registered'] 	= $userdata->user_registered;

					if( ! empty( $userdata->roles ) ) {
						$user_meta['role'] 			= $userdata->roles[0];
					}

					if ( isset( $this->webhook_data[ $user_id ] ) && ! empty( $this->webhook_data[ $user_id ][ $topic ] ) ) {
						$user_meta = array_merge( $this->webhook_data[ $user_id ][ $topic ], $user_meta  );
					}

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

					$used_urls[] = $delivery_url;

				}

			}
		}

	}

}

new WPF_Webhooks_Public;