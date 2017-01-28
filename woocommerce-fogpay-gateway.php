<?php
/*
Plugin Name: Fogpay - WooCommerce Gateway
Plugin URI: http://slick2.me/wp/fogpay
Description: Extends WooCommerce by Adding the Fogpay Gateway.
Version: 1
Author: Carey Dayrit
Author URI: http://slick2.me
*/

// Include our Gateway Class and Register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'spyr_fogpay_init', 0 );
function spyr_fogpay_init() {
	// If the parent WC_Payment_Gateway class doesn't exist
	// it means WooCommerce is not installed on the site
	// so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
	
	// If we made it this far, then include our Gateway Class
	include_once( 'woocommerce-fogpay.php' );

	// Now that we have successfully included our class,
	// Lets add it too WooCommerce
	add_filter( 'woocommerce_payment_gateways', 'spyr_add_fogpay' );
	function spyr_add_fogpay( $methods ) {
		$methods[] = 'SPYR_Fogpay';
		return $methods;
	}	
}


// Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'spyr_fogpay_action_links' );
function spyr_fogpay_action_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'spyr-fogpay' ) . '</a>',
	);

	// Merge our new link with the default ones
	return array_merge( $plugin_links, $links );	
}
