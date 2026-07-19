<?php
/**
 * Store credit button partial.
 *
 * @package WCSC
 *
 * @var int    $order_id
 * @var string $action_url
 * @var string $nonce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form class="wcsc-store-credit-form" method="post" action="<?php echo esc_url( $action_url ); ?>">
	<input type="hidden" name="action" value="wcsc_convert_order">
	<input type="hidden" name="order_id" value="<?php echo esc_attr( $order_id ); ?>">
	<input type="hidden" name="wcsc_nonce" value="<?php echo esc_attr( $nonce ); ?>">

	<button type="submit" class="button wcsc-store-credit-button">
		<?php esc_html_e( 'Convert this order to store credit', 'wcsc' ); ?>
	</button>
</form>
