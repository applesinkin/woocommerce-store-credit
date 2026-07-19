<?php
/**
 * Plugin Name: WooCommerce Store Credit
 * Description: Minimal WooCommerce store credit plugin scaffold for a technical exercise.
 * Version: 0.1.0
 * Requires Plugins: woocommerce
 * Requires at least: 6.6
 * Requires PHP: 8.0
 * Author: Oleh Synkin
 * Text Domain: wcsc
 *
 * @package WCSC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WCSC_VERSION', '0.1.0' );
define( 'WCSC_FILE', __FILE__ );
define( 'WCSC_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCSC_URL', plugin_dir_url( __FILE__ ) );

require_once WCSC_PATH . 'includes/autoload.php';

add_action(
	'plugins_loaded',
	static function (): void {
		$core = new WCSC_Core();
		$core->init();
	}
);
