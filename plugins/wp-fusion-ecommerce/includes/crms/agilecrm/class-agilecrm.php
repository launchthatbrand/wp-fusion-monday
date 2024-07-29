<?php

class WPF_EC_AgileCRM {

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

		$this->supports = array( 'products' );

		if ( get_option( 'wpf_agilecrm_products' ) == false ) {
			$this->sync_products();
		}

		add_action( 'wpf_sync', array( $this, 'sync_products' ) );

	}

	/**
	 * Add an order
	 *
	 * @access  public
	 * @return  bool
	 */

	public function add_order( $order_id, $contact_id, $order_label, $payment_method, $products, $line_items, $total, $currency = 'usd', $order_date, $provider = false ) {

		if ( empty( $order_date ) ) {
			$order_date = current_time( 'timestamp' );
		}

		$calc_totals = 0;
		$did_sync    = false;

		foreach ( $products as $product ) {

			if ( empty( $product['crm_product_id'] ) ) {

				if ( $did_sync == false ) {

					$this->sync_products();
					$did_sync = true;

				}

				$product['crm_product_id'] = $this->add_product( $product );

				// Error handling for adding products
				if ( is_wp_error( $product['crm_product_id'] ) ) {
					return $product['crm_product_id'];
				}
			}

			$items[] = (object) array(
				'id'    => $product['crm_product_id'],
				'name'  => $product['name'],
				'price' => $product['price'],
				'qty'   => $product['qty'],
				'total' => $product['qty'] * $product['price'],
			);

			$calc_totals += $product['qty'] * $product['price'];

		}

		$order_data = array(
			'name'           => $order_label,
			'expected_value' => $calc_totals,
			'close_date'     => $order_date,
			'milestone'      => 'Won',
			'products'       => $items,
		);

		$params         = wp_fusion()->crm->get_params();
		$params['body'] = json_encode( $order_data );

		// Get email for contact
		$email = wp_fusion()->crm->get_email_from_cid( $contact_id );

		$response = wp_remote_post( 'https://' . wp_fusion()->crm->domain . '.agilecrm.com/dev/api/opportunity/email/' . $email, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $response->id;

	}


	/**
	 * Sync available products
	 *
	 * @access  public
	 * @return  array Products
	 */

	public function sync_products() {

		if ( ! wp_fusion()->crm->params ) {
			wp_fusion()->crm->get_params();
		}

		$products = array();

		$request  = 'https://' . wp_fusion()->crm->domain . '.agilecrm.com/dev/api/products';
		$response = wp_remote_get( $request, wp_fusion()->crm->params );

		if ( is_wp_error( $response ) ) {
			wp_fusion()->logger->handle( $response->get_error_code(), 0, 'Error syncing products: ' . $response->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return;
		}

		$body_json = json_decode( $response['body'], true );

		foreach ( (array) $body_json as $product ) {
			$products[ $product['id'] ] = $product['name'];
		}

		update_option( 'wpf_agilecrm_products', $products );

		return $products;

	}

	/**
	 * Register a product in AgileCRM
	 *
	 * @access  public
	 * @return  int Product ID
	 */

	public function add_product( $product ) {

		$available_products = get_option( 'wpf_agilecrm_products', array() );

		$product_id = array_search( $product['name'], $available_products );

		// If no product found, then add
		if ( $product_id == false ) {

			wp_fusion()->logger->handle(
				'info', 0, 'Registering new product <a href="' . admin_url( 'post.php?post=' . $product['id'] . '&action=edit' ) . '" target="_blank">' . $product['name'] . '</a> in AgileCRM:', array(
					'meta_array_nofilter' => array(
						'Name'  => $product['name'],
						'Price' => $product['price'],
					),
					'source'              => 'wpf-ecommerce',
				)
			);

			$product_data = array(
				'name'  => $product['name'],
				'sku'   => $product['sku'],
				'price' => $product['price'],
			);

			$request        = 'https://' . wp_fusion()->crm->domain . '.agilecrm.com/dev/api/products';
			$params         = wp_fusion()->crm->get_params();
			$params['body'] = json_encode( $product_data );

			$response = wp_remote_retrieve_body( wp_remote_post( $request, $params ) );
			$response = json_decode( $response );

			$product_id = $response->id;

		}

		update_post_meta( $product['id'], 'agilecrm_product_id', $product_id );

		// Update the global products list
		$products                = get_option( 'wpf_agilecrm_products', array() );
		$products[ $product_id ] = $product['name'];
		update_option( 'wpf_agilecrm_products', $products );

		return $product_id;

	}

}
