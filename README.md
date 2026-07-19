# WooCommerce Store Credit

Adds a My Account order-details action that lets eligible customers convert a paid, non-refunded WooCommerce order into store credit.
The plugin creates a single-use fixed-cart coupon for the order total and restricts it to the order billing email.

## Installation

1. Copy this folder to `wp-content/plugins/woocommerce-store-credit`.
2. Activate **WooCommerce Store Credit** in WordPress.

Each order stores the generated coupon ID in order meta, so the same order cannot be converted twice.
The UI is split into small plugin partials and backend checks are repeated in the POST handler.
