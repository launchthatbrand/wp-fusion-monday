<?php

class WPF_EC_Drip {

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

		$this->supports = array();

		add_filter( 'wpf_configure_sections', array( $this, 'configure_sections' ), 10, 2 );
		add_filter( 'wpf_configure_settings', array( $this, 'register_settings' ), 15, 2 );

	}

	/**
	 * Adds Addons tab if not already present
	 *
	 * @access public
	 * @return void
	 */

	public function configure_sections( $page, $options ) {

		if ( ! isset( $page['sections']['addons'] ) ) {
			$page['sections'] = wp_fusion()->settings->insert_setting_before( 'import', $page['sections'], array( 'addons' => __( 'Addons', 'wp-fusion' ) ) );
		}

		return $page;

	}


	/**
	 * Add fields to settings page
	 *
	 * @access public
	 * @return array Settings
	 */

	public function register_settings( $settings, $options ) {

		if ( ! isset( $options['conversions_enabled'] ) ) {
			$options['conversions_enabled'] = true;
		}

		$settings['ecommerce_header'] = array(
			'title'   => __( 'Drip Ecommerce Tracking', 'wp-fusion' ),
			'std'     => 0,
			'type'    => 'heading',
			'section' => 'addons',
		);

		$settings['orders_enabled'] = array(
			'title'   => __( 'Orders', 'wp-fusion' ),
			'desc'    => __( 'Create an <a href="https://help.drip.com/hc/en-us/articles/360002603911-Orders" target="_blank">order</a> in Drip for each sale.', 'wp-fusion' ),
			'std'     => 1,
			'type'    => 'checkbox',
			'section' => 'addons',
			'unlock'  => array( 'orders_api_version' ),
		);

		$settings['orders_api_version'] = array(
			'title'   => __( 'API Version', 'wp-fusion' ),
			'desc'    => __( 'The Shopper Activity API is Drip\'s newer ecommerce API. <a href="https://wpfusion.com/documentation/ecommerce-tracking/drip-ecommerce/" target="_blank">See the documentation</a> for more info.', 'wp-fusion' ),
			'std'     => 'v3',
			'type'    => 'radio',
			'section' => 'addons',
			'choices' => array(
				'v3' => __( 'Shopper Activity API' ),
				'v2' => __( 'Orders API' ),
			),
		);

		$settings['conversions_enabled'] = array(
			'title'   => __( 'Events (Legacy Feature)', 'wp-fusion' ),
			'desc'    => __( 'Record an <a href="https://help.drip.com/hc/en-us/articles/115003757391-Events" target="_blank">event</a> in Drip for each sale.', 'wp-fusion' ),
			'std'     => 0,
			'type'    => 'checkbox',
			'section' => 'addons',
		);

		return $settings;

	}


	/**
	 * Add an order
	 *
	 * @access  public
	 * @return  bool
	 */

	public function add_order( $order_id, $contact_id, $order_label, $payment_method, $products, $line_items, $total, $currency = 'usd', $order_date, $currency_symbol, $provider = false ) {

		if ( empty( $order_date ) ) {
			$order_date = current_time( 'timestamp' );
		}

		$result = true;

		// Convert to UTC
		$time_offset = get_option( 'gmt_offset' );
		$time_offset = $time_offset * 60 * 60;
		$order_date -= $time_offset;

		$items    = array();
		$discount = 0;
		$shipping = 0;
		$tax      = 0;

		if ( wp_fusion()->settings->get( 'orders_enabled', true ) == true && wp_fusion()->settings->get( 'orders_api_version' ) == 'v3' ) {

			// v3 API
			// Build up items array
			foreach ( $products as $product ) {

				$item_data = array(
					'product_id'  => (string) $product['id'],
					'sku'         => $product['sku'],
					'price'       => floatval( $product['price'] ),
					'name'        => $product['name'],
					'quantity'    => intval( $product['qty'] ),
					'total'       => floatval( $product['price'] * $product['qty'] ),
					'product_url' => get_permalink( $product['id'] ),
					'image_url'   => $product['image'],
					'categories'  => $product['categories'],
				);

				// Clean up possible empty values
				foreach ( $item_data as $key => $value ) {

					if ( empty( $value ) ) {
						unset( $item_data[ $key ] );
					}

				}

				if ( ! isset( $item_data['price'] ) ) {
					$item_data['price'] = 0;
				}

				$items[] = (object) $item_data;

			}

			foreach ( $line_items as $line_item ) {

				if ( $line_item['type'] == 'shipping' ) {

					// Shipping
					$items[] = (object) array(
						'product_id' => 'SHIPPING',
						'price'      => floatval( $line_item['price'] ),
						'total'      => floatval( $line_item['price'] ),
						'name'       => $line_item['title'] . ' - ' . $line_item['description'],
						'quantity'   => 1,
					);

					$shipping += abs( $line_item['price'] );

				} elseif ( $line_item['type'] == 'tax' ) {

					// Tax
					$tax += abs( $line_item['price'] );

				} elseif ( $line_item['type'] == 'discount' ) {

					// Discounts
					$discount += abs( $line_item['price'] );

				} else {

					// Addons & variations
					$items[] = (object) array(
						'product_id' => (string) $line_item['id'],
						'sku'        => $line_item['sku'],
						'price'      => floatval( $line_item['price'] ),
						'name'       => $line_item['name'],
						'quantity'   => intval( $line_item['qty'] ),
						'total'      => floatval( $line_item['price'] * $line_item['qty'] ),
					);

				}
			}

			$order = (object) array(
				'provider'        => $provider,
				'person_id'       => $contact_id,
				'action'          => 'placed',
				'occurred_at'     => date( 'c', $order_date ),
				'order_id'        => (string) $order_id,
				'order_public_id' => $order_label,
				'grand_total'     => floatval( $total ),
				'total_discounts' => floatval( $discount ),
				'total_taxes'     => floatval( $tax ),
				'total_shipping'  => floatval( $shipping ),
				'currency'        => $currency,
				'order_url'       => admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
				'items'           => array_reverse( $items ),
			);

			$api_token  = wp_fusion()->settings->get( 'drip_token' );
			$account_id = wp_fusion()->settings->get( 'drip_account' );

			$params = array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $api_token ),
					'Content-Type'  => 'application/json',
				),
				'body'    => json_encode( $order ),
			);

			$request = 'https://api.getdrip.com/v3/' . $account_id . '/shopper_activity/order';

			$response = wp_remote_post( $request, $params );

			if ( is_wp_error( $response ) ) {

				wp_fusion()->logger->handle( $response->get_error_code(), 0, 'Error adding order: ' . $response->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
				return $response;

			} else {

				$body   = json_decode( wp_remote_retrieve_body( $response ) );
				$result = $body->request_id;

			}
		} elseif ( wp_fusion()->settings->get( 'orders_enabled', true ) == true ) {

			// v2 API
			foreach ( $products as $product ) {

				if ( ! isset( $product['price'] ) ) {
					$product['price'] = 0;
				}

				$items[] = (object) array(
					'product_id' => $product['id'],
					'sku'        => $product['sku'],
					'amount'     => floor( floatval( $product['price'] ) * 100 * $product['qty'] ),
					'price'      => floor( floatval( $product['price'] ) * 100 ),
					'name'       => $product['name'],
					'quantity'   => intval( $product['qty'] ),
				);

			}

			foreach ( $line_items as $line_item ) {

				if ( $line_item['type'] == 'shipping' ) {

					// Shipping
					$items[] = (object) array(
						'amount'   => floor( floatval( $line_item['price'] ) * 100 ),
						'price'    => floor( floatval( $line_item['price'] ) * 100 ),
						'name'     => $line_item['title'],
						'quantity' => 1,
					);

				} elseif ( $line_item['type'] == 'tax' ) {

					// Tax
					$tax += abs( $line_item['price'] );

				} elseif ( $line_item['type'] == 'discount' ) {

					// Discounts
					$discount += abs( $line_item['price'] );

				} else {

					// Addons & variations
					$items[] = (object) array(
						'product_id' => $line_item['id'],
						'sku'        => $line_item['sku'],
						'amount'     => floor( floatval( $line_item['price'] ) * 100 * $line_item['qty'] ),
						'price'      => floor( floatval( $line_item['price'] ) * 100 ),
						'name'       => $line_item['name'],
						'quantity'   => intval( $line_item['qty'] ),
					);

				}
			}

			$order = array(
				'id'          => $contact_id,
				'provider'    => $order_label,
				'amount'      => floor( floatval( $total ) * 100 ),
				'permalink'   => admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
				'occurred_at' => date( 'c', $order_date ),
				'discount'    => floatval( $discount ) * 100,
				'tax'         => floatval( $tax ) * 100,
				'items'       => array_reverse( $items ),
			);

			error_log( print_r( $order, true ) );

			$orders = (object) array( 'orders' => array( (object) $order ) );

			$api_token  = wp_fusion()->settings->get( 'drip_token' );
			$account_id = wp_fusion()->settings->get( 'drip_account' );

			$request = 'https://api.getdrip.com/v2/' . $account_id . '/orders';

			$params = array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $api_token ),
					'Content-Type'  => 'application/json',
				),
				'body'    => json_encode( $orders ),
			);

			$response = wp_remote_post( $request, $params );

			if ( is_wp_error( $response ) ) {

				wp_fusion()->logger->handle( $response->get_error_code(), 0, 'Error adding order: ' . $response->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
				return $response;

			}
		}

		if ( wp_fusion()->settings->get( 'conversions_enabled', true ) == true ) {

			// API request for events (when purchase is made)
			$events = array(
				'events' => array(
					(object) array(
						'email'       => wp_fusion()->crm->get_email_from_cid( $contact_id ),
						'occurred_at' => date( 'c', $order_date ),
						'action'      => 'Conversion',
						'properties'  => array(
							'value' => intval( round( floatval( $total ), 2 ) * 100 ),
						),
					),
				),
			);

			$request        = 'https://api.getdrip.com/v2/' . $account_id . '/events/';
			$params['body'] = json_encode( $events );

			$response = wp_remote_post( $request, $params );

			if ( is_wp_error( $response ) ) {

				wp_fusion()->logger->handle( $response->get_error_code(), 0, 'Error adding Event: ' . $response->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
				return $response;

			}
		}

		return $result;

	}

}
