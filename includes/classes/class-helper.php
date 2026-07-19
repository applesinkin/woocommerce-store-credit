<?php
/**
 * Shared plugin helpers.
 *
 * @package WCSC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides shared helper methods.
 */
class WCSC_Helper {
	/**
	 * Loads a PHP template file from within the plugin.
	 *
	 * The $args array is passed to the template as the local variable `$args`
	 * so templates can access data without relying on extract().
	 *
	 * @param string $template_name File name without the .php extension.
	 * @param array  $args          Variables to expose inside the template as $args.
	 * @param string $template_path Sub-directory relative to the plugin root. Default 'partials'.
	 *
	 * @return void
	 */
	public static function get_template_part( string $template_name, array $args = array(), string $template_path = '' ): void {
		if ( empty( $template_path ) ) {
			$template_path = 'partials';
		}

		$template_dir  = WCSC_PATH . trailingslashit( $template_path );
		$template      = $template_dir . $template_name . '.php';
		$resolved      = realpath( $template );
		$resolved_base = realpath( WCSC_PATH );

		if ( $resolved === false || $resolved_base === false ) {
			return;
		}

		$resolved      = wp_normalize_path( $resolved );
		$resolved_base = trailingslashit( wp_normalize_path( $resolved_base ) );

		if ( ! str_starts_with( $resolved, $resolved_base ) ) {
			return;
		}

		include $resolved;
	}

	/**
	 * Adds a WooCommerce notice after loading notice helpers for admin-post requests.
	 *
	 * @param string $message Notice message.
	 * @param string $type    Notice type.
	 * @return void
	 */
	public static function add_notice( string $message, string $type = 'success' ): void {
		if ( ! function_exists( 'wc_add_notice' ) && defined( 'WC_ABSPATH' ) ) {
			$notice_functions = WC_ABSPATH . 'includes/wc-notice-functions.php';

			if ( file_exists( $notice_functions ) ) {
				require_once $notice_functions;
			}
		}

		if ( function_exists( 'wc_add_notice' ) ) {
			wc_add_notice( $message, $type );
		}
	}

	/**
	 * Trims the string value, or returns a fallback when the value is not a string.
	 *
	 * @param mixed  $value                The value to trim.
	 * @param mixed  $return_if_not_string Value returned when $value is not a string. Default ''.
	 *
	 * @return mixed Trimmed string, or $return_if_not_string.
	 */
	public static function trim_string( $value, $return_if_not_string = '' ) {
		if ( ! is_string( $value ) ) {
			return $return_if_not_string;
		}

		return trim( $value );
	}
}
