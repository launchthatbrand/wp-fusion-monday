<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



class WPF_EC_RCP extends WPF_EC_Integrations_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @return void
	 */

	public function init() {

		$this->slug = 'restrict-content-pro';

		add_action( 'rcp_create_payment', array( $this, 'send_order_data' ), 10, 2 );

		// meta box
		add_action( 'rcp_edit_subscription_form', array( $this, 'rcp_product_select' ) );
		add_action( 'rcp_add_subscription_form', array( $this, 'rcp_product_select' ) );
		add_action( 'rcp_edit_subscription_level', array( $this, 'save_subscription_settings' ), 10, 2 );
		add_action( 'rcp_add_subscription', array( $this, 'save_subscription_settings_new' ), 10, 2 );

	}


	/**
	 * Adds CRM product association field to edit view for single Subscription
	 *
	 * @access  public
	 * @return  mixed
	 */

	public function rcp_product_select( $level = false ) {

		if ( ! in_array( 'products', wp_fusion_ecommerce()->crm->supports ) ) {
			return;
		}
		  $settings = get_option( 'wpf_rcp_tags', array() );

		if ( is_object( $level ) && isset( $settings[ $level->id ] ) ) {

			if ( empty( $settings[ $level->id ]['ontraport_product_id'] ) ) {
				$settings[ $level->id ]['ontraport_product_id'] = false;
			}
		}

			$available_products = get_option( 'wpf_' . wp_fusion()->crm->slug . '_products', array() );

			echo '<tr>';
				echo '<th scope="row" style="width: 162px;"><label for="wpf-ec-product">' . wp_fusion()->crm->name . ' product</label>';
				echo '<td>';

				echo '<select id="wpf-ec-product" class="select4-search" data-placeholder="None" name="wpf-settings[ontraport_product_id]"' . wp_fusion()->crm->slug . '_product_id">';

					echo '<option></option>';

		foreach ( $available_products as $id => $name ) {

			echo '<option value="' . $id . '"' . selected( $id, $settings[ $level->id ]['ontraport_product_id'], false ) . '>' . $name . '</option>';
		}

				echo '</select>';

				echo '</td>';

			echo '</tr>';
	}

	/**
	 * Saves changes to WPF fields on the subscription edit screen
	 *
	 * @access  public
	 * @return  void
	 */

	public function save_subscription_settings( $id, $args ) {

		if ( ! isset( $args['wpf-settings'] ) ) {
			return;
		}

		$settings        = get_option( 'wpf_rcp_tags', array() );
		$settings[ $id ] = $args['wpf-settings'];

		update_option( 'wpf_rcp_tags', $settings );

	}


	/**
	 * Saves WPF settings for new subscription levels
	 *
	 * @access  public
	 * @return  void
	 */

	public function save_subscription_settings_new( $id, $args ) {

		if ( ! isset( $_POST['wpf-settings'] ) ) {
			return;
		}

		$settings        = get_option( 'wpf_rcp_tags', array() );
		$settings[ $id ] = $_POST['wpf-settings'];
		update_option( 'wpf_rcp_tags', $settings );

	}


	/**
	 * Sends order data to CRM's ecommerce system
	 *
	 * @access  public
	 * @return  bool
	 */

	public function send_order_data( $payment_id, $payment ) {

		$wpf_complete = get_post_meta( $payment_id, 'wpf_ec_complete', true );

		if ( ! empty( $wpf_complete ) ) {
			return true;
		}

		$products   = array();
		$discount   = $payment['discount_amount'];
		$contact_id = wp_fusion()->user->get_contact_id( $payment['user_id'] );

		if ( empty( $payment['gateway'] ) ) {
			$payment['gateway'] = 'Manual';
		}

		$settings     = get_option( 'wpf_rcp_tags', array() );
		$subscription = rcp_get_subscription_details( $payment['object_id'] );

		if ( isset( $settings[ $subscription->id ][ wp_fusion()->crm->slug . '_product_id' ] ) ) {
			$crm_product_id = $settings[ $subscription->id ][ wp_fusion()->crm->slug . '_product_id' ];
		} else {
			$crm_product_id = false;
		}

		$products[] = array(
			'id'             => $subscription->id,
			'crm_product_id' => $crm_product_id,
			'name'           => $subscription->name,
			'price'          => $subscription->price,
			'discount'       => $payment['discount_amount'],
			'qty'            => 1,
			'gateway'        => $payment['gateway'],
			'sku'            => '',
		);

		$line_items = array();

		if ( ! empty( $discount ) ) {
			$line_items[] = array(
				'type'        => 'discount',
				'price'       => -$discount,
				'title'       => 'Discounts Applied',
				'description' => 'Used discount codes: ' . $payment['discount_amount'],
			);
		}

		// Logging
		$log_data_array = array(
			'Products'       => count( $products ),
			'Line Items'     => count( $line_items ),
			'Payment Method' => $payment['gateway'],
		);

		wp_fusion()->logger->handle(
			'info', $contact_id, 'Adding RCP Order <a href="' . admin_url( 'post.php?post=' . $payment_id . '&action=edit' ) . '" target="_blank">#' . $payment_id . '</a>:', array(
				'meta_array_nofilter' => $log_data_array,
				'source'              => 'wpf-ecommerce',
			)
		);

		// Currency
		$currency = rcp_get_currency();

		// Date
		$order_date = $payment['date'];

		// Add order
		$result = wp_fusion_ecommerce()->crm->add_order( $payment_id, $contact_id, 'RCP Order #' . $payment_id, $payment['gateway'], $products, $line_items, $currency, $order_date, false );

		if ( is_wp_error( $result ) ) {

			wp_fusion()->logger->handle( 'error', $contact_id, 'Error adding RCP Order <a href="' . admin_url( 'post.php?post=' . $payment_id . '&action=edit' ) . '" target="_blank">#' . $payment_id . '</a>: ' . $result->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return false;

		}

		// Denotes that the WPF actions have already run for this order
		// update_post_meta( $payment_id, 'wpf_ec_complete', true );
		// update_post_meta( $payment_id, 'wpf_ec_' . wp_fusion()->crm->slug . '_invoice_id', $result );
	}



}

new WPF_EC_RCP();
