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
		add_action( 'admin_post_wcsc_convert_order', array( $this, 'handle_store_credit_conversion' ) );
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

		$coupon_id = $order->get_meta( WCSC_Constants::CONVERTED_COUPON_META_KEY, true );

		if ( $coupon_id ) {
			$coupon      = new WC_Coupon( $coupon_id );
			$coupon_code = $coupon->get_code();

			if ( $coupon_code === '' ) {
				return;
			}

			WCSC_Helper::get_template_part(
				'store-credit-coupon',
				array(
					'coupon_code' => $coupon_code,
				),
				'partials/wc/order'
			);
			return;
		}

		if ( (float) $order->get_total_refunded() > 0 ) {
			return;
		}

		$order_id   = $order->get_id();
		$action_url = admin_url( 'admin-post.php' );
		$nonce      = wp_create_nonce( 'wcsc_convert_order_' . $order_id );

		WCSC_Helper::get_template_part(
			'store-credit',
			array(
				'action_url' => $action_url,
				'nonce'      => $nonce,
				'order_id'   => $order_id,
			),
			'partials/wc/order'
		);
	}

	/**
	 * Handles store credit conversion form submissions.
	 *
	 * @return void
	 */
	public function handle_store_credit_conversion(): void {
		$order_id = isset( $_POST['order_id'] ) ? absint( wp_unslash( $_POST['order_id'] ) ) : 0;
		$nonce    = isset( $_POST['wcsc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wcsc_nonce'] ) ) : '';
		$order    = $order_id ? wc_get_order( $order_id ) : false;

		if ( ! $order instanceof WC_Order ) {
			WCSC_Helper::add_notice( __( 'Order was not found.', 'wcsc' ), 'error' );
			wp_safe_redirect( wc_get_account_endpoint_url( 'orders' ) );
			exit;
		}

		$redirect_url = $order->get_view_order_url();

		if ( ! is_user_logged_in() || (int) $order->get_user_id() !== get_current_user_id() ) {
			WCSC_Helper::add_notice( __( 'You cannot convert this order to store credit.', 'wcsc' ), 'error' );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		if ( ! wp_verify_nonce( $nonce, 'wcsc_convert_order_' . $order_id ) ) {
			WCSC_Helper::add_notice( __( 'Store credit request could not be verified.', 'wcsc' ), 'error' );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		if ( ! $order->is_paid() ) {
			WCSC_Helper::add_notice( __( 'Only paid orders can be converted to store credit.', 'wcsc' ), 'error' );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		if ( (float) $order->get_total_refunded() > 0 ) {
			WCSC_Helper::add_notice( __( 'Refunded orders cannot be converted to store credit.', 'wcsc' ), 'error' );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$factory = new WCSC_Coupon_Factory();
		$result  = $factory->create_for_order( $order );

		if ( is_wp_error( $result ) ) {
			WCSC_Helper::add_notice( $result->get_error_message(), 'error' );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		WCSC_Helper::add_notice(
			sprintf(
				/* translators: %s: store credit coupon code. */
				__( 'Store credit coupon created: %s', 'wcsc' ),
				$result->get_code()
			),
			'success'
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
