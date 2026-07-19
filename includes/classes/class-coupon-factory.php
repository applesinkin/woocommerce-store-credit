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
	private const SOURCE_ORDER_META_KEY = '_wcsc_source_order_id';

	/**
	 * Creates a single-use fixed cart coupon for the given order total.
	 *
	 * @param WC_Order $order Source order.
	 * @return WC_Coupon
	 */
	public function create_for_order( WC_Order $order ): WC_Coupon {
		$email = $order->get_billing_email();

		if ( $email === '' ) {
			throw new InvalidArgumentException( 'Order billing email is required to create store credit.' );
		}

		$coupon = new WC_Coupon();

		$coupon->set_code( $this->generate_code( $order ) );
		$coupon->set_discount_type( 'fixed_cart' );
		$coupon->set_amount( $order->get_total() );
		$coupon->set_usage_limit( 1 );
		$coupon->set_usage_limit_per_user( 1 );
		$coupon->set_email_restrictions( array( $email ) );
		$coupon->add_meta_data( self::SOURCE_ORDER_META_KEY, $order->get_id(), true );
		$coupon->save();

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
