<?php
/* Fogpay Payment Gateway Class */
class SPYR_Fogpay extends WC_Payment_Gateway {

	// Setup our Gateway's id, description and other values
	function __construct() {

		// The global ID for this Payment method
		$this->id = "spyr_fogpay";

		// The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
		$this->method_title = __( "Fogpay", 'spyr-fogpay' );

		// The description for this Payment Gateway, shown on the actual Payment options page on the backend
		$this->method_description = __( "Fogpay", 'spyr-fogpay' );

		// The title to be used for the vertical tabs that can be ordered top to bottom
		$this->title = __( "Fogpay", 'spyr-fogpay' );

		// If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
		$this->icon = null;

		// Bool. Can be set to true if you want payment fields to show on the checkout 
		// if doing a direct integration, which we are doing in this case
		$this->has_fields = true;

		// Supports the default credit card form
		//$this->supports = array( 'default_credit_card_form' );

		// This basically defines your settings which are then loaded with init_settings()
		$this->init_form_fields();

		// After init_settings() is called, you can get the settings and load them into variables, e.g:
		// $this->title = $this->get_option( 'title' );
		$this->init_settings();
		
		// Turn these settings into variables we can use
		foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
		}
		
		// Lets check for SSL
		//add_action( 'admin_notices', array( $this,	'do_ssl_check' ) );
		
		// Save settings
		if ( is_admin() ) {
			// Versions over 2.0
			// Save our administration options. Since we are not going to be doing anything special
			// we have not defined 'process_admin_options' in this class so the method in the parent
			// class will be used instead
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}		
	} // End __construct()

	// Build the administration fields for this specific Gateway
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'		=> __( 'Enable / Disable', 'spyr-fogpay' ),
				'label'		=> __( 'Enable this payment gateway', 'spyr-fogpay' ),
				'type'		=> 'checkbox',
				'default'	=> 'no',
			),
			'title' => array(
				'title'		=> __( 'Title', 'spyr-fogpay' ),
				'type'		=> 'text',
				'desc_tip'	=> __( 'Payment title the customer will see during the checkout process.', 'spyr-fogpay' ),
				'default'	=> __( 'Fogpay BTC', 'spyr-fogpay' ),
			),
			'description' => array(
				'title'		=> __( 'Description', 'spyr-fogpay' ),
				'type'		=> 'textarea',
				'desc_tip'	=> __( 'Payment description the customer will see during the checkout process.', 'spyr-fogpay' ),
				'default'	=> __( 'Pay securely using Bitcoins.', 'spyr-fogpay' ),
				'css'		=> 'max-width:350px;'
			),
			'fogpay_account' => array(
				'title'		=> __( 'Fogpay Username', 'spyr-fogpay' ),
				'type'		=> 'text',
				'desc_tip'	=> __( 'This is the user account provided by Fogpay.com when you signed up for an account.', 'spyr-fogpay' ),
			)					
		);		
	}
	
	// Submit payment and handle response
	public function process_payment( $order_id ) {
		global $woocommerce;
		
		// Get this Order's information so that we know
		// who to charge and how much
		$customer_order = new WC_Order( $order_id );
		
		// Are we testing right now or is it a real transaction
		$environment = ( $this->environment == "yes" ) ? 'TRUE' : 'FALSE';

		// Decide which URL to post to
		$environment_url = "http://fogpay.com/payment_api/payment";

		// This is where the fun stuff begins
		$payload = array(
			// Fogpay Credentials and API Info
			"account_username"      => $this->fogpay_account,
			"currency"				=> 'USD',
		
			
			// Order total
			"amount"             	=> $customer_order->order_total,						
						
			"item_id"        		=> str_replace( "#", "", $customer_order->get_order_number() ),
			"item_name"				=> 'Orders',
			'postback_url'			=> $this->get_return_url( $order_id )
					
		
			
		);
	
		// Send this payload to fogpay.com for processing
	
		return array(
				'result'   => 'success',
				'redirect' =>$environment_url.'?account_username='.$this->fogpay_account.'&amount='. $customer_order->order_total.'&currency=USD',
		);

	}
	
	// Validate fields
	public function validate_fields() {
		return true;
	}
	
	// Check if we are forcing SSL on checkout pages
	// Custom function not required by the Gateway
	public function do_ssl_check() {
		if( $this->enabled == "yes" ) {
			if( get_option( 'woocommerce_force_ssl_checkout' ) == "no" ) {
				echo "<div class=\"error\"><p>". sprintf( __( "<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>" ), $this->method_title, admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) ."</p></div>";	
			}
		}		
	}

} // End of SPYR_AuthorizeNet_AIM
