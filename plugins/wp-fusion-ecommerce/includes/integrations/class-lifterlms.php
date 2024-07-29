<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPF_EC_LifterLMS extends WPF_EC_Integrations_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @return void
	 */

	public function init() {

		$this->slug = 'lifterlms';

		// Send plan data
		add_action( 'lifterlms_order_status_completed', array( $this, 'access_plan_purchased' ), 10, 2 );

		// Meta Box
		add_action( 'llms_access_plan_mb_after_row_five', array( $this, 'access_plan_product_dropdown' ), 10, 3 );
		add_action( 'llms_access_plan_saved', array( $this, 'save_plan' ), 10, 3 );

	}

	/**
	 * Sends order data to CRM's ecommerce system
	 *
	 * @access  public
	 * @return  void
	 */

	public function access_plan_purchased( $order, $old_status ) {

		$order_id   = $order->get( 'id' );
		$plan_id    = $order->get( 'plan_id' );
		$product_id = $order->get( 'product_id' );
		$user_id    = $order->get( 'user_id' );

		$access_plan = new LLMS_Access_Plan( $plan_id );

		$wpf_complete = get_post_meta( $order_id, 'wpf_ec_complete', true );

		if ( ! empty( $wpf_complete ) ) {
			return true;
		}

		$available_products = get_option( 'wpf_' . wp_fusion()->crm->slug . '_products', array() );

		$crm_product_id = get_post_meta( $plan_id, wp_fusion()->crm->slug . '_product_id', true );

		if ( ! isset( $available_products[ $crm_product_id ] ) ) {
			$crm_product_id = false;
		}

		$products = array(
			array(
				'id'             => $plan_id,
				'name'           => preg_replace( '/[^(\x20-\x7F)]*/', '', $access_plan->title ),
				'price'          => $access_plan->price,
				'qty'            => 1,
				'sku'            => $access_plan->get( 'sku' ),
				'crm_product_id' => $crm_product_id,
			),
		);

		$line_items = array();

		if ( $order->has_coupon() ) {

			$code = $order->get( 'coupon_code' );

			$discount = $order->get( 'coupon_amount' );
			$type     = $order->get( 'coupon_type' );

			if ( $type == 'percent' ) {
				$discount = $access_plan->price * ( $discount / 100 );
			}

			$line_items[] = array(
				'type'        => 'discount',
				'price'       => - $discount,
				'title'       => 'Coupon ' . $code,
				'description' => 'Code: ' . $code,
			);

		}

		// Logging
		$log_data_array = array(
			'Access Plan'    => $access_plan->title,
			'Payment Method' => $order->get( 'payment_gateway' ),
		);

		wp_fusion()->logger->handle(
			'info', $user_id, 'Adding LifterLMS Order <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" target="_blank">#' . $order_id . '</a>:', array(
				'meta_array_nofilter' => $log_data_array,
				'source'              => 'wpf-ecommerce',
			)
		);

		// Date
		$order_date = strtotime( $order->get_start_date() );

		$contact_id = wp_fusion()->user->get_contact_id( $user_id );

		// Add order
		$result = wp_fusion_ecommerce()->crm->add_order( $order_id, $contact_id, 'LLMS Order #' . $order_id, $order->get( 'payment_gateway' ), $products, $line_items, $order->get_transaction_total(), $order->get( 'currency' ), $order_date, false, 'lifterlms' );

		if ( is_wp_error( $result ) ) {

			wp_fusion()->logger->handle( 'error', $contact_id, 'Error adding LLMS Order <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" target="_blank">#' . $order_id . '</a>: ' . $result->get_error_message(), array( 'source' => wp_fusion()->crm->slug ) );
			return false;

		} elseif ( $result === true ) {

			$order->add_note( wp_fusion()->crm->name . ' invoice successfully created.' );

		} elseif ( $result != null ) {

			// CRMs with invoice IDs
			$order->add_note( wp_fusion()->crm->name . ' invoice #' . $result . ' successfully created.' );
			update_post_meta( $order_id, 'wpf_ec_' . wp_fusion()->crm->slug . '_invoice_id', $result );

		}

		// Denotes that the WPF actions have already run for this order
		update_post_meta( $order_id, 'wpf_ec_complete', true );

	}

	/**
	 * Product drop down creation
	 *
	 * @access  public
	 * @return  bool
	 */

	public function access_plan_product_dropdown( $plan, $plan_id, $order ) {

		if ( ! is_array( wp_fusion_ecommerce()->crm->supports ) || ! in_array( 'products', wp_fusion_ecommerce()->crm->supports ) ) {
			return;
		}

		?>

		<div class="llms-metabox-field d-1of3">

			<?php if ( empty( $plan ) ) : ?>

				<label>Save this access plan to configure products.</label>

			<?php else : ?>

				<?php

				$product_id         = get_post_meta( $plan->get( 'id' ), wp_fusion()->crm->slug . '_product_id', true );
				$available_products = get_option( 'wpf_' . wp_fusion()->crm->slug . '_products', array() );

				asort( $available_products );

				?>

				<label><?php echo wp_fusion()->crm->name; ?> Product</label>
				<select class="select4-search" data-placeholder="Select a product" name="_llms_plans[<?php echo $order; ?>][wpf_product_id]">
					<option value="">Select Product</option>

					<?php

					foreach ( $available_products as $id => $name ) {
						echo '<option value="' . $id . '"' . selected( $id, $product_id, false ) . '>' . esc_attr( $name ) . '</option>';
					}

					?>
				</select>

			<?php endif; ?>

		</div> 

		<?php

	}

	/**
	 * Save access plan
	 *
	 * @access  public
	 * @return  void
	 */

	public function save_plan( $plan, $raw_plan_data, $metabox ) {

		if ( isset( $raw_plan_data['wpf_product_id'] ) ) {

			update_post_meta( $raw_plan_data['id'], wp_fusion()->crm->slug . '_product_id', $raw_plan_data['wpf_product_id'] );

		}

	}

}

new WPF_EC_LifterLMS();
