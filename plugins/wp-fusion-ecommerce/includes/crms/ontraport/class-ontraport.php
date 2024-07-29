<?php

class WPF_EC_Ontraport {

	/**
	 * Lets pluggable functions know which features are supported by the CRM
	 */

	public $supports;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function init() {

		$this->supports = array( 'products', 'refunds' );

		add_filter( 'wpf_configure_settings', array( $this, 'register_settings' ), 20, 2 );

		if ( get_option( 'wpf_ontraport_products' ) == false ) {
			$this->sync_products();
		}

		add_action( 'wpf_sync', array( $this, 'sync_products' ) );

	}

	/**
	 * Add fields to settings page
	 *
	 * @access public
	 * @return array Settings
	 */

	public function register_settings( $settings, $options ) {

		$new_settings = array();

		$new_settings['ec_op_prices'] = array(
			'title'   => __( 'Ontraport Pricing', 'wp-fusion' ),
			'type'    => 'radio',
			'section' => 'addons',
			'choices' => array(
				'op'       => __( 'Use product prices as set in Ontraport', 'wp-fusion' ),
				'checkout' => __( 'Use product prices as paid at checkout' ),
			),
			'std'     => 'op',
		);

		$settings = wp_fusion()->settings->insert_setting_after( 'ec_woo_taxes', $settings, $new_settings );

		return $settings;

	}

	/**
	 * Syncs available products
	 *
	 * @since 1.3
	 * @return void
	 */

	public function sync_products() {

		if ( ! wp_fusion()->crm->params ) {
			wp_fusion()->crm->get_params();
		}

		$products = array();
		$offset   = 0;
		$proceed  = true;

		while ( $proceed == true ) {

			$request  = 'https://api.ontraport.com/1/Products&range=50&start=' . $offset . '';
			$response = wp_remote_get( $request, wp_fusion()->crm->params );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$body_json = json_decode( $response['body'], true );

			foreach ( (array) $body_json['data'] as $product ) {
				$products[ $product['id'] ] = $product['name'];
			}

			$offset = $offset + 50;

			if ( count( $body_json['data'] ) < 50 ) {
				$proceed = false;
			}
		}

		update_option( 'wpf_ontraport_products', $products );
	}

	/**
	 * Register a product in Ontraport
	 *
	 * @access  public
	 * @return  int Product ID
	 */

	public function add_product( $product ) {

		$query    = '[{ "field":{"field":"name"}, "op":"=", "value":{"value":"' . $product['name'] . '"} }]';
		$request  = 'https://api.ontraport.com/1/Products?condition=' . urlencode( $query );
		$response = wp_remote_get( $request, wp_fusion()->crm->params );
		$body     = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $body['data'] ) && $body['data'][0]['price'] == $product['price'] ) {

			// If matching product name found
			$product_id = $body['data'][0]['id'];

		} else {

			// Logger
			wp_fusion()->logger->handle(
				'info', 0, 'Registering new product <a href="' . admin_url( 'post.php?post=' . $product['id'] . '&action=edit' ) . '" target="_blank">' . $product['name'] . '</a> in Ontraport:', array(
					'meta_array_nofilter' => array(
						'Name'  => $product['name'],
						'Price' => $product['price'],
					),
					'source'              => 'wpf-ecommerce',
				)
			);

			// Add new product
			$nparams         = wp_fusion()->crm->params;
			$nparams['body'] = json_encode(
				array(
					'name'  => $product['name'],
					'price' => $product['price'],
				)
			);

			$response = wp_remote_post( 'https://api.ontraport.com/1/Products', $nparams );

			if ( is_wp_error( $response ) ) {

				return $response;

			} else {

				$body       = json_decode( wp_remote_retrieve_body( $response ), true );
				$product_id = $body['data']['id'];

			}
		}

		// Save the ID to the product
		update_post_meta( $product['id'], 'ontraport_product_id', $product_id );

		// Update the global products list
		$ontraport_products                = get_option( 'wpf_ontraport_products', array() );
		$ontraport_products[ $product_id ] = $product['name'];
		update_option( 'wpf_ontraport_products', $ontraport_products );

		return $product_id;

	}


	/**
	 * Add an order
	 *
	 * @access  public
	 * @return  mixed Invoice ID or
	 */

	public function add_order( $order_id, $contact_id, $order_label, $payment_method, $products, $line_items, $total, $currency = 'usd', $order_date, $currency_symbol = '$', $provider = false ) {

		if ( empty( $order_date ) ) {
			$order_date = current_time( 'timestamp' );
		}

		// Convert date to GMT
		$offset      = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		$order_date -= $offset;

		if ( ! wp_fusion()->crm->params ) {
			wp_fusion()->crm->get_params();
		}

		$params = wp_fusion()->crm->params;

		$order_data = array(
			'objectID'          => 0,
			'contact_id'        => $contact_id,
			'chargeNow'         => 'chargeLog',
			'trans_date'        => (int) $order_date * 1000,
			'invoice_template'  => 0,
			'offer'             => array( 'products' => array() ),
			'delay'             => 0,
			'external_order_id' => $order_id,
		);

		// Referral handling
		if ( isset( $_COOKIE['oprid'] ) ) {
			$order_data['oprid'] = $_COOKIE['oprid'];
		}

		$calc_totals = 0;

		// Get product IDs for each product
		foreach ( $products as $product ) {

			if ( empty( $product['crm_product_id'] ) ) {

				$product['crm_product_id'] = $this->add_product( $product );

				// Error handling for adding products
				if ( is_wp_error( $product['crm_product_id'] ) ) {
					return $product['crm_product_id'];
				}
			}

			$product_data = array(
				'name'     => $product['name'],
				'id'       => $product['crm_product_id'],
				'quantity' => $product['qty'],
				'sku'      => $product['sku'],
				// 'price'       => array(array('price' => $product['price'], 'payment_count' => 1, 'unit' => 'day')) (removed for customer Michael Bernstein using Barzilian currency payments)
			);

			if ( wp_fusion()->settings->get( 'ec_op_prices' ) == 'checkout' ) {
				$product_data['price'] = array(
					array(
						'price'         => $product['price'],
						'payment_count' => 1,
						'unit'          => 'day',
					),
				);
			}

			$order_data['offer']['products'][] = $product_data;

		}

		foreach ( $line_items as $line_item ) {

			if ( $line_item['type'] == 'shipping' ) {

				// Shipping doesn't work yet because a shipping method must first be registered in OP
			} elseif ( $line_item['type'] == 'tax' ) {

				// Taxes don't work yet because a tax object must first be registered in OP
			} elseif ( $line_item['type'] == 'discount' ) {

				// Discounts don't work at all with the current OP API
			}
		}

		$params['body'] = json_encode( $order_data );

		$response = wp_remote_post( 'https://api.ontraport.com/1/transaction/processManual', $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body       = json_decode( wp_remote_retrieve_body( $response ), true );
		$invoice_id = $body['data']['invoice_id'];

		return $invoice_id;

	}


	/**
	 * Mark a previously added order as refunded
	 *
	 * @access  public
	 * @return  mixed Bool or WP_Error
	 */

	public function refund_order( $transaction_id, $refund_amount ) {

		if ( ! wp_fusion()->crm->params ) {
			wp_fusion()->crm->get_params();
		}

		$params = wp_fusion()->crm->params;

		$refund_data = array(
			'ids' => array( $transaction_id ),
		);

		$params['body']   = json_encode( $refund_data );
		$params['method'] = 'PUT';

		$response = wp_remote_request( 'https://api.ontraport.com/1/transaction/refund', $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;

	}

}
