<?php
/**
 * Coupon creation for store credit conversions.
 *
 * @package WCSC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates WooCommerce coupons from orders.
 */
class WCSC_Coupon_Factory {
	/**
	 * Creates a single-use fixed cart coupon for the given order total.
	 *
	 * @param WC_Order $order Source order.
	 * @return WC_Coupon|WP_Error
	 */
	public function create_for_order( WC_Order $order ) {
		if ( $order->get_meta( WCSC_Constants::CONVERTED_COUPON_META_KEY, true ) ) {
			return new WP_Error(
				'wcsc_already_converted',
				'Order has already been converted to store credit.'
			);
		}

		$email = sanitize_email( $order->get_billing_email() );

		if ( ! is_email( $email ) ) {
			return new WP_Error(
				'wcsc_missing_email',
				'Order billing email is required to create store credit.'
			);
		}

		$coupon = new WC_Coupon();

		$coupon->set_code( $this->generate_code( $order ) );
		$coupon->set_discount_type( 'fixed_cart' );
		$coupon->set_amount( $order->get_total() );
		$coupon->set_usage_limit( 1 );
		$coupon->set_usage_limit_per_user( 1 );
		$coupon->set_email_restrictions( array( $email ) );
		$coupon->add_meta_data( WCSC_Constants::SOURCE_ORDER_META_KEY, $order->get_id(), true );
		$coupon_id = $coupon->save();

		if ( ! $coupon_id ) {
			return new WP_Error(
				'wcsc_coupon_not_created',
				'Store credit coupon could not be created.'
			);
		}

		// Mark the order as converted so it cannot generate another store credit coupon.
		$order->update_meta_data( WCSC_Constants::CONVERTED_COUPON_META_KEY, $coupon_id );
		$order->save();

		return $coupon;
	}

	/**
	 * Generates a readable coupon code with a random suffix.
	 *
	 * @param WC_Order $order Source order.
	 * @return string
	 */
	private function generate_code( WC_Order $order ): string {
		return sprintf(
			'SC%d%s',
			$order->get_id(),
			strtolower( wp_generate_password( 8, false, false ) )
		);
	}
}
