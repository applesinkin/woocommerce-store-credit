<?php
/**
 * Store credit coupon code partial.
 *
 * @package WCSC
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$coupon_code = WCSC_Helper::trim_string( $args['coupon_code'] ?? '' );

if ( empty( $coupon_code ) ) {
    return;
}
?>

<p class="wcsc-store-credit-code">
	<?php esc_html_e( 'Store credit coupon:', 'wcsc' ); ?>
	<strong><?php echo esc_html( $coupon_code ); ?></strong>
</p>
