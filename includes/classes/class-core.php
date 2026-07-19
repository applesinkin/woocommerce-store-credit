<?php
/**
 * Core plugin wiring.
 *
 * @package WCSC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers plugin hooks.
 */
class WCSC_Core {
	/**
	 * Registers WordPress and WooCommerce hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'render_store_credit_button' ) );
	}

	/**
	 * Renders the store credit button on eligible My Account order details pages.
	 *
	 * @param WC_Order $order Current WooCommerce order.
	 * @return void
	 */
	public function render_store_credit_button( WC_Order $order ): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( (int) $order->get_user_id() !== get_current_user_id() ) {
			return;
		}

		if ( ! $order->is_paid() ) {
			return;
		}

		if ( (float) $order->get_total_refunded() > 0 ) {
			return;
		}

		if ( $order->get_meta( WCSC_Constants::CONVERTED_COUPON_META_KEY, true ) ) {
			return;
		}

		$order_id   = $order->get_id();
		$action_url = admin_url( 'admin-post.php' );
		$nonce      = wp_create_nonce( 'wcsc_convert_order_' . $order_id );

		include WCSC_PATH . 'partials/wc/order/store-credit.php';
	}
}
