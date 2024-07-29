<?php

class WPF_EC_Infusionsoft_iSDK {

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

		if ( get_option( 'wpf_infusionsoft_products' ) == false ) {
			$this->sync_products();
		}

		add_action( 'wpf_sync', array( $this, 'sync_products' ) );
		add_action( 'init', array( $this, 'get_affiliate' ) );

	}

	/**
	 * Gets affiliate tracking data if available
	 *
	 * @access  public
	 * @since   1.2
	 */

	public function get_affiliate() {

		if ( ! empty( $_GET['affiliate'] ) ) {
			setcookie( 'is_aff', $_GET['affiliate'], time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
		}

		if ( ! empty( $_GET['aff'] ) ) {
			setcookie( 'is_affcode', $_GET['aff'], time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
		}

	}

	/**
	 * Syncs available products and product IDs from Infusionsoft and stores them locally
	 *
	 * @access  public
	 * @since   1.0
	 */

	public function sync_products() {

		$fields   = array( 'Id', 'ProductName' );
		$query    = array( 'Id' => '%' );
		$products = array();

		$result = wp_fusion()->crm->connect();

		if ( is_wp_error( $result ) ) {
			wp_fusion()->logger->handle( $result->get_error_code(), 0, 'Error syncing products: ' . $result->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return false;
		}

		$result = wp_fusion()->crm->app->dsQuery( 'Product', 1000, 0, $query, $fields );

		foreach ( (array) $result as $product ) {
			if ( isset( $product['ProductName'] ) ) {
				$products[ $product['Id'] ] = $product['ProductName'];
			}
		}

		$result = update_option( 'wpf_infusionsoft_products', $products );

	}

	/**
	 * Add an order
	 *
	 * @access  public
	 * @return  int Invoice ID
	 */

	public function add_order( $order_id, $contact_id, $order_label, $payment_method, $products, $line_items, $total, $currency = 'usd', $order_date, $provider = false ) {

		if ( empty( $order_date ) ) {
			$order_date = time();
		}

		// Convert date to GMT
		$offset      = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		$order_date -= $offset;

		$result = wp_fusion()->crm->connect();

		if ( is_wp_error( $result ) ) {
			wp_fusion()->logger->handle( $result->get_error_code(), 0, 'Error adding order: ' . $result->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return false;
		}

		// Add affiliate referral if present
		if ( isset( $_COOKIE['is_aff'] ) || isset( $_COOKIE['is_affcode'] ) ) {

			$is_aff = (int) $_COOKIE['is_aff'];

			if ( empty( $is_aff ) ) {

				if ( ! empty( $_COOKIE['is_affcode'] ) ) {
					$returnFields = array( 'Id' );
					$affiliate    = wp_fusion()->crm->app->dsFind( 'Affiliate', 1, 0, 'AffCode', $_COOKIE['is_affcode'], $returnFields );
					$affiliate    = $affiliate[0];
					$is_aff       = (int) $affiliate['Id'];
				}
			}

			if ( ! empty( $is_aff ) ) {

				wp_fusion()->logger->handle( 'info', 0, 'Setting referral affiliate ID <strong>' . $is_aff . '</strong> for contact ID <strong>' . $contact_id . '</strong>', array( 'source' => 'wpf-ecommerce' ) );

				wp_fusion()->crm->app->dsAdd(
					'Referral', array(
						'ContactId'   => $contact_id,
						'AffiliateId' => $is_aff,
						'IPAddress'   => $_SERVER['REMOTE_ADDR'],
						'Type'        => 0,
						'DateSet'     => date( 'Y-m-d' ),
					)
				);
			}
		} else {
			$is_aff = 0;
		}

		$order_date = date( 'Ymd\TH:i:s', $order_date );

		$infusionsoft_invoice_id = wp_fusion()->crm->app->blankOrder( $contact_id, $order_label, $order_date, 0, $is_aff );
		$calc_totals             = 0;

		// Create each product if it doesn't exist yet
		foreach ( $products as $product ) {

			$infusionsoft_product_id = $product['crm_product_id'];

			if ( empty( $infusionsoft_product_id ) ) {
				$infusionsoft_product_id = $this->add_product( $product );
			}

			// Fix ampersands in product name
			$product['name'] = str_replace( '&', '&amp;', $product['name'] );

			// $product should have $product['name'], $product['id'], $product['sku'], $product['qty'], $product['price']
			wp_fusion()->crm->app->addOrderItem( $infusionsoft_invoice_id, $infusionsoft_product_id, 4, floatval( $product['price'] ), $product['qty'], $product['name'], '' );
			$calc_totals += $product['qty'] * $product['price'];

		}

		// Add each line item (not products) to the order
		foreach ( $line_items as $line_item ) {

			if ( $line_item['type'] == 'discount' ) {
				$type = 7;
			} elseif ( $line_item['type'] == 'tax' ) {
				$type = 2;
			} elseif ( $line_item['type'] == 'shipping' ) {
				$type = 1;
			} else {
				continue;
			}

			wp_fusion()->crm->app->addOrderItem( $infusionsoft_invoice_id, 0, $type, floatval( $line_item['price'] ), 1, $line_item['title'], $line_item['description'] );
			$calc_totals += $line_item['price'];
		}

		// Mark order as paid
		wp_fusion()->crm->app->manualPmt( $infusionsoft_invoice_id, $calc_totals, $order_date, $payment_method, $order_label, true );

		// Add Order Notes
		$job = wp_fusion()->crm->app->dsLoad( 'Invoice', $infusionsoft_invoice_id, array( 'JobId' ) );
		wp_fusion()->crm->app->dsUpdate( 'Job', $job['JobId'], array( 'OrderType' => 'Online' ) );

		return $infusionsoft_invoice_id;

	}


	/**
	 * Register products in Infusionsoft
	 *
	 * @access  public
	 * @return  int Product ID
	 */

	public function add_product( $product ) {

		// Try and find existing product
		$product['name']      = str_replace( '&', '&amp;', $product['name'] );
		$infusionsoft_product = wp_fusion()->crm->app->dsFind( 'Product', 1, 0, 'ProductName', $product['name'], array( 'Id' ) );

		if ( is_wp_error( $infusionsoft_product ) ) {
			wp_fusion()->logger->handle( $infusionsoft_product->get_error_code(), 0, 'Error looking up product ' . $product['name'] . ' in ' . wp_fusion()->crm->name . ': ' . $infusionsoft_product->get_error_message(), array( 'source' => 'wpf-ecommerce' ) );
			die();
		}

		if ( empty( $infusionsoft_product ) || empty( $infusionsoft_product[0] ) ) {

			$new_product = array(
				'ProductName'  => $product['name'],
				'ProductPrice' => $product['price'],
			);

			if ( ! empty( $product['sku'] ) ) {
				$new_product['Sku'] = $product['sku'];
			}

			// Logger
			$product_id = $product['id'];
			wp_fusion()->logger->handle(
				'info', 0, 'Registering new product <a href="' . admin_url( 'post.php?post=' . $product_id . '&action=edit' ) . '" target="_blank">' . get_the_title( $product_id ) . '</a> in Infusionsoft:', array(
					'meta_array_nofilter' => $new_product,
					'source'              => 'wpf-ecommerce',
				)
			);

			// Add product
			$infusionsoft_product_id = wp_fusion()->crm->app->dsAdd( 'Product', $new_product );

		} else {
			$infusionsoft_product_id = $infusionsoft_product[0]['Id'];

		}

		// Save the ID to the product
		update_post_meta( $product['id'], 'infusionsoft_product_id', $infusionsoft_product_id );

		$infusionsoft_products                             = get_option( 'wpf_infusionsoft_products', array() );
		$infusionsoft_products[ $infusionsoft_product_id ] = $product['name'];
		update_option( 'wpf_infusionsoft_products', $infusionsoft_products );

		return $infusionsoft_product_id;

	}


	/**
	 * Mark a previously added order as refunded
	 *
	 * @access  public
	 * @return  mixed Bool or WP_Error
	 */

	public function refund_order( $invoice_id, $refund_amount ) {

		$result = wp_fusion()->crm->connect();

		if ( is_wp_error( $result ) ) {
			wp_fusion()->logger->handle( $result->get_error_code(), 0, 'Error refunding order: ' . $result->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return false;
		}

		// Convert date to GMT
		$offset     = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		$order_date = time() - $offset;
		$order_date = date( 'Ymd\TH:i:s', $order_date );

		$response = wp_fusion()->crm->app->manualPmt( intval( $invoice_id ), floatval( - $refund_amount ), $order_date, 'Refund', 'Refund', true );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;

	}

}
