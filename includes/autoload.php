<?php
/**
 * Minimal class autoloader for the WooCommerce Store Credit plugin.
 *
 * @package WCSC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(
	static function ( string $class_name ): void {
		$prefix = 'WCSC_';

		if ( strpos( $class_name, $prefix ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class_name, strlen( $prefix ) );
		$file_name      = 'class-' . strtolower( str_replace( '_', '-', $relative_class ) ) . '.php';
		$file_path      = WCSC_PATH . 'includes/classes/' . $file_name;

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
);
