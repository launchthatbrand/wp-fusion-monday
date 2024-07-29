<?php

class WPF_EC_Hubspot {

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

		$this->supports = array( 'deal_stages' );

		add_filter( 'wpf_configure_sections', array( $this, 'configure_sections' ), 10, 2 );
		add_filter( 'wpf_configure_settings', array( $this, 'register_settings' ), 15, 2 );

		add_action( 'wpf_sync', array( $this, 'sync' ) );

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

		if ( ! isset( $options['deals_enabled'] ) ) {
			$options['deals_enabled'] = false;
		}

		$settings['ecommerce_header'] = array(
			'title'   => __( 'HubSpot Ecommerce Tracking', 'wp-fusion' ),
			'std'     => 0,
			'type'    => 'heading',
			'section' => 'addons',
		);

		if ( ! isset( $options['hubspot_pipelines'] ) ) {
			$options['hubspot_pipelines'] = array();
		}

		$settings['hubspot_pipeline_stage'] = array(
			'title'       => __( 'Pipeline / Stage', 'wp-fusion' ),
			'type'        => 'select',
			'section'     => 'addons',
			'placeholder' => 'Select a Pipeline / Stage',
			'choices'     => $options['hubspot_pipelines'],
			'std'         => 'default+closedwon',
			'desc'        => 'Select a default pipeline and stage for new deals.',
		);

		$settings['hubspot_add_note'] = array(
			'title'   => __( 'Add Note', 'wp-fusion' ),
			'desc'    => __( 'Add a note to new deals containing the products purchased and prices.', 'wp-fusion' ),
			'std'     => 0,
			'type'    => 'checkbox',
			'section' => 'addons',
		);

		return $settings;

	}


	/**
	 * Syncs pipelines on plugin install or when Resynchronize is clicked
	 *
	 * @since 1.0
	 * @return void
	 */

	public function sync() {

		$params = wp_fusion()->crm->get_params();

		$pipelines = array();

		$response = wp_remote_get( 'https://api.hubapi.com/crm-pipelines/v1/pipelines/deals/', $params );

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		foreach ( $response->results as $pipeline ) {

			foreach ( $pipeline->stages as $stage ) {

				$pipelines[ $pipeline->pipelineId . '+' . $stage->stageId ] = $pipeline->label . ' &raquo; ' . $stage->label;

			}
		}

		wp_fusion()->settings->set( 'hubspot_pipelines', $pipelines );

	}

	/**
	 * Add an order
	 *
	 * @access  public
	 * @return  bool
	 */

	public function add_order( $order_id, $contact_id, $order_label, $payment_method, $products, $line_items, $total, $currency = 'usd', $order_date, $currency_symbol = '$', $provider = false ) {

		if ( empty( $order_date ) ) {
			$order_date = current_time( 'timestamp' );
		}

		$calc_totals = 0;

		// Build up items array
		foreach ( $products as $product ) {

			if ( ! isset( $product['price'] ) ) {
				$product['price'] = 0;
			}

			$calc_totals += $product['qty'] * $product['price'];

		}

		foreach ( $line_items as $line_item ) {

			// Adjust total for line items
			$calc_totals += $line_item['price'];

		}

		$pipeline_stage = wp_fusion()->settings->get( 'hubspot_pipeline_stage', 'default+closedwon' );
		$pipeline_stage = explode( '+', $pipeline_stage );

		$order = array(
			'associations' => array(
				'associatedVids' => array( $contact_id ),
			),
			'properties'   => array(
				array(
					'name'  => 'dealname',
					'value' => $order_label,
				),
				array(
					'name'  => 'pipeline',
					'value' => $pipeline_stage[0],
				),
				array(
					'name'  => 'dealstage',
					'value' => $pipeline_stage[1],
				),
				array(
					'name'  => 'closedate',
					'value' => $order_date * 1000,
				),
				array(
					'name'  => 'amount',
					'value' => $calc_totals,
				),
			),
		);

		$params = wp_fusion()->crm->get_params();

		$params['body'] = json_encode( $order );

		$response = wp_remote_post( 'https://api.hubapi.com/deals/v1/deal', $params );

		if ( is_wp_error( $response ) ) {

			wp_fusion()->logger->handle( $response->get_error_code(), 0, 'Error adding order: ' . $response->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return $response;

		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		$deal_id = $response->dealId;

		if ( wp_fusion()->settings->get( 'hubspot_add_note' ) == true ) {

			// Attach note to the deal
			$body = '';

			foreach ( $products as $product ) {

				$body .= $product['name'] . ' - ' . $currency_symbol . $product['price'];

				if ( $product['qty'] > 1 ) {
					$body .= ' - x' . $product['qty'];
				}

				$body .= '<br/>';

			}

			foreach ( $line_items as $line_item ) {

				$body .= $line_item['title'] . ' - ' . $currency_symbol . $line_item['price'] . '<br />';

			}

			$engagement_data = array(
				'engagement'   => array(
					'type' => 'NOTE',
				),
				'associations' => array(
					'dealIds' => array( $deal_id ),
				),
				'metadata'     => array(
					'body' => $body,
				),
			);

			$params['body'] = json_encode( $engagement_data );

			$response = wp_remote_post( 'https://api.hubapi.com/engagements/v1/engagements', $params );

			if ( is_wp_error( $response ) ) {
				return $response;
			}
		}

		return $deal_id;

	}

	/**
	 * Update a deal stage when an order status is changed
	 *
	 * @access  public
	 * @return  bool
	 */

	public function change_stage( $deal_id, $stage ) {

		$pipeline_stage = explode( '+', $stage );

		$order = array(
			'properties' => array(
				array(
					'name'  => 'pipeline',
					'value' => $pipeline_stage[0],
				),
				array(
					'name'  => 'dealstage',
					'value' => $pipeline_stage[1],
				),
			),
		);

		$params = wp_fusion()->crm->get_params();

		$params['body']   = json_encode( $order );
		$params['method'] = 'PUT';

		$response = wp_remote_request( 'https://api.hubapi.com/deals/v1/deal/' . $deal_id, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

	}



}
