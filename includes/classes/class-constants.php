<?php
/**
 * Shared plugin constants.
 *
 * @package WCSC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores shared keys used across plugin classes.
 */
class WCSC_Constants {
	public const CONVERTED_COUPON_META_KEY = '_wcsc_store_credit_coupon_id';
	public const SOURCE_ORDER_META_KEY     = '_wcsc_source_order_id';
}
