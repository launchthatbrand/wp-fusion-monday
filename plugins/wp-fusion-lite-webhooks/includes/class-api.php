<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPF_API {

	public function __construct() {

        add_action( 'init', array( $this, 'remove_wpf_lite_handle_webhooks' ), 1000 );

		add_action( 'init', array( $this, 'handle_webhooks' ), 1 );
		// add_action( 'after_setup_theme', array( $this, 'thrivecart' ) ); // after_setup_theme to get around issues with ThriveCart and headers already being sent by init.

		// add_action( 'wpf_update', array( $this, 'update_user' ) );
		// add_action( 'wpf_update_tags', array( $this, 'update_tags' ) );
		// add_action( 'wpf_add', array( $this, 'add_user' ) );

		// // Import / update user actions.
		add_action( 'wp_ajax_nopriv_wpf_update_object', array( $this, 'update_object' ) );
		add_action( 'wp_ajax_nopriv_wpf_add_object', array( $this, 'add_object' ) );

		// // Clean the transient after successful webhook.
		// add_action( 'wpf_api_success', array( $this, 'delete_transient' ), 5, 3 ); // 5 so it runs before any wp_die()s in CRM modules, i.e. Salesforce
		// add_action( 'wpf_api_fail', array( $this, 'delete_transient' ), 5, 3 );

	}


    /**
	 * Removes wp_fusion_lite handle_webhooks function
	 *
	 * @access public
	 * @return void
	 */

    public function remove_wpf_lite_handle_webhooks() {
        // BugFu::log('remove_wpf_lite_handle_webhooks');
        remove_action( 'init', array( wp_fusion(), 'handle_webhooks' ) );
    }


	/**
	 * Gets actions passed as query params
	 *
	 * @access public
	 * @return void
	 */

	public function handle_webhooks() {

		if ( isset( $_REQUEST['wpf_action'] ) && 'thrivecart' != $_REQUEST['wpf_action'] ) {



			if ( empty( $_REQUEST['wpf_action'] ) ) {
				wpf_log( 'error', 0, 'Webhook received but <strong>wpf_action</strong> was not specified.', array( 'source' => 'api' ) );
				wp_die( 'No Action Specified' );
			}

			$action = strtolower( sanitize_text_field( $_REQUEST['wpf_action'] ) );
			$key    = isset( $_REQUEST['access_key'] ) ? sanitize_text_field( $_REQUEST['access_key'] ) : false;

			if ( ! isset( $_REQUEST['access_key'] ) || wpf_get_option( 'access_key' ) != $key ) {

				wpf_log( 'error', 0, 'Webhook received but <strong>access_key</strong> ' . $key . ' was invalid.', array( 'source' => 'api' ) );
				wp_die( 'Invalid Access Key', 'Invalid Access Key', 403 );

			}

			// When wpfusion.com connects to this site to test webhooks

			if ( 'test' == $action ) {
				wp_send_json_success();
				die();
			}

			define( 'DOING_WPF_WEBHOOK', true );

			// Get the contact ID out of the payload
            error_log('Getting contact ID out of payload');
            error_log( print_r( wpf_clean( wp_unslash( $_REQUEST ) ), true ) );

            //BugFu::log('Post data before wpf_crm_post_data filter:', $post_data);
            error_log('Post data before wpf_crm_post_data filter:' . print_r( wpf_clean( wp_unslash( $_REQUEST ) ), true));
            

			$post_data = apply_filters( 'wpf_crm_post_data', wpf_clean( wp_unslash( $_REQUEST ) ) );

            BugFu::log('Post data after wpf_crm_post_data filter:', $post_data);
            error_log('Post data after wpf_crm_post_data filter:' . print_r($post_data, true));

            // $post_data = wp_fusion()->crm->format_post_data(wpf_clean(wp_unslash($_REQUEST)));
            // BugFu::log('Post data after direct call:', $post_data);

			if ( empty( $post_data ) || empty( $post_data['contact_id'] ) ) {

				// Debug stuff.

				$request = wpf_clean( wp_unslash( $_REQUEST ) );
				$payload = json_decode( file_get_contents( 'php://input' ), true );

				if ( ! empty( $payload ) ) {
					$payload = wpf_clean( $payload );
					$request = array_merge( $request, $payload );
				}

				wpf_log( 'error', 0, '<strong>' . $action . '</strong> webhook received but contact data was not found or in an invalid format.', array( 'source' => 'api', 'meta_array_nofilter' => $request ) );

				do_action( 'wpf_api_fail', false, $action, false );

				wp_die( '<h3>Abort</h3>Contact data not found or contact not eligible for update.', 'Abort', 200 );

			}

			// Get the User ID from the contact ID.

			if ( 'update' === $action || 'update_tags' === $action ) {

				$post_data['user_id'] = wp_fusion()->user->get_user_id( $post_data['contact_id'] );

				// If user isn't found.

				if ( false === $post_data['user_id'] ) {

					if ( isset( $post_data['email'] ) ) {

						// Try to look up the user by email, if supplied.

						$post_data['user_id'] = email_exists( $post_data['email'] );

						if ( false !== $post_data['user_id'] ) {
							wp_fusion()->user->get_contact_id( $post_data['user_id'], true );
						}
					}
				}

				if ( false === $post_data['user_id'] ) {

					wpf_log( 'notice', 0, '<strong>' . $action . '</strong> webhook received but no matching user found for contact #' . $post_data['contact_id'], array( 'source' => 'api' ) );

					do_action( 'wpf_api_fail', false, $action, $post_data['contact_id'] );

					wp_die( 'No matching user found', 'Not Found', 200 );

				}
			} else {
				$post_data['user_id'] = false;
			}

			// Log what's about to happen

			$message = 'Received <strong>' . $action . '</strong> webhook for contact #' . $post_data['contact_id'];

			if ( ! empty( $post_data['async'] ) ) {
				$message .= '. Dispatching to async queue.';
			}

			wpf_log( 'info', $post_data['user_id'], $message, array( 'source' => 'api' ) );

			// As of 3.36.1 we're going to try and detect and prevent simultaneous incoming webhooks

			$status = get_transient( 'wpf_api_lock_' . $post_data['contact_id'] );

			if ( $status ) {

				// There was a webhook for this contact within the last minute

				$error = false;

				if ( $action == $status ) {

					$error = '<strong>Doing it wrong:</strong> An <strong>' . $action . '</strong> webhook is already being processed for contact #' . $post_data['contact_id'] . '. Sending simulteanous webhooks can result in unexpected behavior, and so this duplicate request will be ignored.';

				} elseif ( 'add' == $status ) {

					$error = '<strong>Doing it wrong:</strong> An <strong>add</strong> webhook is already being processed for contact #' . $post_data['contact_id'] . '. It\'s not necessary to send an <strong>' . $action . '</strong> webhook in addition, and doing so can result in unexpected behavior. This duplicate request will be ignored.';

				} elseif ( 'update' == $status && 'update_tags' == $action ) {

					$error = '<strong>Doing it wrong:</strong> An <strong>update</strong> webhook is already being processed for contact #' . $post_data['contact_id'] . '. It\'s not necessary to send an <strong>update_tags</strong> webhook in addition, and doing so can result in unexpected behavior. This duplicate request will be ignored.';

				}

				// If it's a duplicate, record a warning and die.

				if ( $error ) {
					wpf_log( 'notice', $post_data['user_id'], $error, array( 'source' => 'api' ) );
					do_action( 'wpf_api_fail', $post_data['user_id'], $action, $post_data['contact_id'] );
					wp_die( $error, 'Doing it wrong', 200 );
				}
			}

			// Set the transient that locks this API call

			set_transient( 'wpf_api_lock_' . $post_data['contact_id'], $action, MINUTE_IN_SECONDS );

			// Finally, send the data off to any functions registered to act on the webhook method

			if ( has_action( 'wp_ajax_nopriv_wpf_' . $action ) ) {
				error_log('Doing action wp_ajax_nopriv_wpf_' . $action);
				do_action( 'wp_ajax_nopriv_wpf_' . $action, $post_data );
			} else {
				do_action( 'wpf_' . $action, $post_data );
			}
		}

	}


	/**
	 * Called by CRM HTTP Posts to update a user
	 *
	 * @access public
	 * @return null
	 */

	public function update_user( $post_data ) {

		// Async queue
		if ( ! empty( $post_data['async'] ) ) {

			wp_fusion()->batch->quick_add( 'wpf_batch_pull_users_meta', array( $post_data['user_id'] ), false );
			wp_fusion()->batch->quick_add( 'wpf_batch_users_tags_sync', array( $post_data['user_id'] ) );

			do_action( 'wpf_api_success', $post_data['user_id'], 'update', $post_data['contact_id'] );
			wp_die( '<h3>Success</h3>Contact #' . $post_data['contact_id'] . ' pushed to async queue.', 'Success', 200 );

		}

		ob_start(); // Catch output from other plugins

		// Load the user's metadata
		$user_meta = wp_fusion()->user->pull_user_meta( $post_data['user_id'] );

		if ( isset( $post_data['tags'] ) ) {

			// ActiveCampaign and Mautic can read the tags out of the payload, we don't need another API call
			wp_fusion()->user->set_tags( $post_data['tags'], $post_data['user_id'] );

			$tags = $post_data['tags'];

		} else {

			// Regular CRMs, make the API call
			$tags = wp_fusion()->user->get_tags( $post_data['user_id'], true, false );

		}

		// Maybe change role (but not for admins)
		if ( isset( $post_data['role'] ) && ! user_can( $post_data['user_id'], 'manage_options' ) && wp_roles()->is_role( $post_data['role'] ) ) {

			$user = new WP_User( $post_data['user_id'] );
			$user->set_role( $post_data['role'] );
		}

		ob_clean();

		do_action( 'wpf_api_success', $post_data['user_id'], 'update', $post_data['contact_id'] );

		wp_die( '<h3>Success</h3>Updated user meta:<pre>' . wpf_print_r( $user_meta, true ) . '</pre><br />Updated tags:<pre>' . wpf_print_r( $tags, true ) . '</pre>', 'Success', 200 );

	}


	/**
	 * Called by CRM HTTP Posts to update a user
	 *
	 * @access public
	 * @return null
	 */

	public function update_object( $post_data ) {

		ob_start(); // Catch output from other plugins.

		if ($post_data['challenge']) {
			BugFu::log('Challenge detected');
			error_log('Challenge detected');

			// Prepare the response
			$response = array(
				'challenge' => $post_data['challenge']
			);

			// Send the JSON response
			wp_send_json($response);

			// Stop further execution
			wp_die();
		}

		if ( isset( $post_data['tags'] ) ) {

			// ActiveCampaign and Mautic can read the tags out of the payload, we don't need another API call.
			wp_fusion()->user->set_tags( $post_data['tags'], $post_data['user_id'] );

			$tags = $post_data['tags']; // for the debug output.

		} else {

			if ( ! empty( $post_data['async'] ) ) {

				wp_fusion()->batch->quick_add( 'wpf_batch_users_tags_sync', array( $post_data['user_id'] ) );
				do_action( 'wpf_api_success', $post_data['user_id'], 'update_tags', $post_data['contact_id'] );
				wp_die( '<h3>Success</h3>Contact #' . $post_data['contact_id'] . ' pushed to async queue.', 'Success', 200 );

			}

			$tags = wp_fusion()->user->get_tags( $post_data['user_id'], true, false );

		}

		ob_clean();

		do_action( 'wpf_api_success', $post_data['user_id'], 'update_tags', $post_data['contact_id'] );

		wp_die( '<h3>Success</h3>Updated tags:<pre>' . wpf_print_r( $tags, true ) . '</pre>', 'Success', 200 );

	}


	/**
	 * Called by CRM HTTP Posts to add a user
	 *
	 * @access public
	 * @return null
	 */

	public function add_object( $post_data ) {
        BugFu::log('WPF_API::add_object()');
        error_log('WPF_API::add_object()');

		if ($post_data['challenge']) {
			BugFu::log('Challenge detected');
			error_log('Challenge detected');

			// Prepare the response
			$response = array(
				'challenge' => $post_data['challenge']
			);

			// Send the JSON response
			wp_send_json($response);

			// Stop further execution
			wp_die();
		}
			

		// $defaults = array(
		// 	'send_notification' => false,
		// 	'role'              => false,
		// 	'async'             => false,
		// );

		// $post_data = wp_parse_args( $post_data, $defaults );

		// // Convert string value to bool
		// if ( 'false' == $post_data['send_notification'] ) {
		// 	$post_data['send_notification'] = false;
		// }

		// // Async queue

		// if ( $post_data['async'] ) {

		// 	wp_fusion()->batch->quick_add( 'wpf_batch_import_users', array( $post_data['contact_id'], $post_data ) );
		// 	do_action( 'wpf_api_success', false, 'add', $post_data['contact_id'] );
		// 	wp_die( '<h3>Success</h3>Contact #' . $post_data['contact_id'] . ' pushed to async queue.', 'Success', 200 );

		// }

		// // Catch output from other plugins
		// ob_start();

		// $user_id = wp_fusion()->user->import_user( $post_data['contact_id'], $post_data['send_notification'], $post_data['role'] );

		// ob_clean();

		// if ( is_wp_error( $user_id ) ) {

		// 	$message = 'Import user failed for contact ID <strong>' . $post_data['contact_id'] . '</strong>. Error: ' . $user_id->get_error_message();
		// 	wpf_log( 'error', 0, $message, array( 'source' => 'api' ) );
		// 	do_action( 'wpf_api_fail', false, 'add', $post_data['contact_id'] );
		// 	wp_die( '<h3>Error</h3>' . $message );

		// }

		// if ( is_multisite() ) {
		// 	$result = add_user_to_blog( get_current_blog_id(), $user_id, $post_data['role'] );
		// }

		// do_action( 'wpf_api_success', $user_id, 'add', $post_data['contact_id'] );

		// $meta = wp_fusion()->user->get_user_meta( $user_id );
		// $tags = wp_fusion()->user->get_tags( $user_id );

		// wp_die( '<h3>Success</h3>User imported with ID ' . $user_id . '. <br /><br />Loaded tags:<pre>' . wpf_print_r( $tags, true ) . '</pre><br />Loaded meta:<pre>' . wpf_print_r( $meta, true ) . '</pre>', 'Success', 200 );

	}


	/**
	 * Deletes the transient when the API operation is finished.
	 *
	 * @since 3.36.1
	 *
	 * @param int    $user_id    The user ID.
	 * @param string $method     The API method.
	 * @param string $contact_id The contact ID.
	 */
	public function delete_transient( $user_id, $method, $contact_id = false ) {

		if ( $contact_id ) {
			delete_transient( "wpf_api_lock_{$contact_id}" );
		}

	}

	/**
	 * Handle ThriveCart auto login.
	 *
	 * @access public
	 * @return null
	 */

	public function thrivecart() {

		if ( ! isset( $_GET['wpf_action'] ) || $_GET['wpf_action'] != 'thrivecart' ) {
			return;
		}

		if ( ! isset( $_REQUEST['access_key'] ) || $_REQUEST['access_key'] != wpf_get_option( 'access_key' ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['thrivecart']['customer']['email'] ) ) {

			$request = map_deep( $_REQUEST, 'sanitize_text_field' );

			wpf_log( 'error', 0, 'ThriveCart success URL detected but customer data was not found or in an invalid format.', array( 'source' => 'api', 'meta_array_nofilter' => $request ) );
			return;

		}

		if ( ! wpf_get_option( 'auto_login_thrivecart' ) ) {
			wpf_log( 'notice', 0, 'A ThriveCart success URL was detected but <strong>ThriveCart Auto Login</strong> is disabled at Settings &raquo; WP Fusion &raquo; Advanced. The request will be ignored and no user will be imported.' );
			return;
		}

		$email_address = sanitize_email( $_REQUEST['thrivecart']['customer']['email'] );

		$user = get_user_by( 'email', $email_address );

		if ( ! empty( $user ) ) {

			// Existing user
			$user_id = $user->ID;

			$contact_id = wp_fusion()->user->get_contact_id( $user_id );

		} else {

			// Create new user
			$password = wp_generate_password( 12, false );

			$first_name = false;
			$last_name  = false;

			if ( ! empty( $_REQUEST['thrivecart']['customer']['firstname'] ) ) {

				$first_name = $_REQUEST['thrivecart']['customer']['firstname'];
				$last_name  = $_REQUEST['thrivecart']['customer']['lastname'];

			} elseif ( ! empty( $_REQUEST['thrivecart']['customer']['name'] ) ) {

				$name = urldecode( $_REQUEST['thrivecart']['customer']['name'] );

				$name = explode( ' ', $name );

				$first_name = $name[0];

				unset( $name[0] );

				if ( ! empty( $name ) ) {
					$last_name = implode( ' ', $name );
				}
			}

			$userdata = array(
				'user_login' => $email_address,
				'user_email' => $email_address,
				'first_name' => sanitize_text_field( $first_name ),
				'last_name'  => sanitize_text_field( $last_name ),
				'user_pass'  => $password,
			);

			$userdata = apply_filters( 'wpf_import_user', $userdata, false );

			wpf_log( 'info', 0, 'ThriveCart user creation triggered for ' . $email_address . ':', array( 'meta_array_nofilter' => $userdata ) );

			if ( ! isset( $_GET['send_notification'] ) && false == wpf_get_option( 'send_welcome_email' ) ) {
				// Block welcome emails
				add_filter( 'wp_mail', array( wp_fusion()->user, 'suppress_wp_mail' ), 100 );
			}

			$user_id = wp_insert_user( $userdata );

			$contact_id = wp_fusion()->user->get_contact_id( $user_id );

			do_action( 'wpf_user_imported', $user_id, $userdata );

			$user = get_user_by( 'id', $user_id );

			// Send notification
			if ( isset( $_GET['send_notification'] ) || wpf_get_option( 'send_welcome_email' ) ) {
				wp_new_user_notification( $user_id, null, 'user' );
			}
		}

		// Load tags
		wp_fusion()->user->get_tags( $user_id, true, false );

		// Apply the tags

		if ( ! empty( $_GET['apply_tags'] ) ) {

			$apply_tags = urldecode( $_GET['apply_tags'] );

			$apply_tags = explode( ',', $apply_tags );

			foreach ( $apply_tags as $i => $tag ) {

				$apply_tags[ $i ] = wp_fusion()->user->get_tag_id( $tag );

			}

			wp_fusion()->user->apply_tags( $apply_tags, $user_id );

		}

		// Maybe change role (but not for admins)
		if ( isset( $_GET['role'] ) && ! user_can( $user_id, 'manage_options' ) && wp_roles()->is_role( $_GET['role'] ) ) {
			$user->set_role( $_GET['role'] );
		}

		if ( is_user_logged_in() && get_current_user_id() == $user_id ) {

			// If the user is already logged in as the customer, don't bother
			return;

		}

		// Don't sync tags or meta on login.
		remove_action( 'wp_login', array( wp_fusion()->user, 'login' ), 10, 2 );

		// Handle login
		wp_set_current_user( $user_id, $user->user_login );
		wp_set_auth_cookie( $user_id, true );
		do_action( 'wp_login', $user->user_login, $user );

	}

}


new WPF_API();
