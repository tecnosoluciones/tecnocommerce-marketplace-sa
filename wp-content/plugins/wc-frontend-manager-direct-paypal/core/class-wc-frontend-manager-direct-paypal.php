<?php

/**
 * WCFM PG Direct PayPal Pay plugin core
 *
 * Plugin intiate
 *
 * @author 		WC Lovers
 * @package 	wc-frontend-manager-direct-paypal
 * @version   1.0.0
 */
 
class WCFM_PG_Direct_PayPal {
	
	public $plugin_base_name;
	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	
	public function __construct($file) {

		$this->file = $file;
		$this->plugin_base_name = plugin_basename( $file );
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCFMpgdp_TOKEN;
		$this->text_domain = WCFMpgdp_TEXT_DOMAIN;
		$this->version = WCFMpgdp_VERSION;
		
		add_action( 'wcfm_init', array( &$this, 'init' ), 10 );
		
		// Load Gateway Class
		require_once $this->plugin_path . 'gateway/class-wcfm-gateway-direct-paypal-ipn.php';
		new WCFM_PG_Direct_Paypal_IPN_Handler();
	}
	
	function init() {
		global $WCFM, $WCFMmp;
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// By Force Disable Multivendor Checkout
		add_filter( 'wcfmmp_is_disable_multivendor_checkout', array( $this, 'wcfm_direct_paypal_disable_multivendor_checkout' ), 500 );
		
		// Single Vendor Product Order Restricted - Deprecated since WCFM Marketplace 3.4.0
		add_action( 'woocommerce_add_to_cart_validation', array( &$this, 'wcfm_direct_paypal_single_vendor_order_restriction' ), 50, 3 ); 
		
		// Set Vendor PayPal Email for PayPal Checkout
		add_filter( 'woocommerce_paypal_args', array( $this, 'wcfm_direct_paypal_vendor_email_set' ), 50, 2 );
		
		// Available Gateway Validation depends upon Vendor's PayPal Email set or not
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'wcfm_direct_paypal_available_payment_gateways_vendor_email_validate' ), 50 );
		
		// Set Vendor PayPal API Credential for PayPal Refund
		add_filter( 'woocommerce_paypal_refund_request', array( $this, 'wcfm_direct_paypal_refund_vendor_api_credentials_set' ), 50, 4 );
		
		// Set Vendor PayPal API Credential for PayPal Capture
		add_filter( 'woocommerce_paypal_capture_request', array( $this, 'wcfm_direct_paypal_captyre_vendor_api_credentials_set' ), 50, 3 );
	
		// Enable Auto Withdrawal for Direct PayPal Pay
		add_action( 'wcfmmp_is_auto_withdrawal', array( &$this, 'wcfm_direct_paypal_enable_auto_withdrawal' ), 50, 5 ); 
		
		// Add PayPal API Fields to Vendor Payment Settting 
		add_filter( 'wcfm_marketplace_settings_fields_billing', array( &$this, 'wcfm_direct_paypal_payment_fields' ), 50, 2 );
		
	}
	
	function wcfm_direct_paypal_disable_multivendor_checkout( $is_disable ) {
		$is_disable = 'yes';
		return $is_disable;
	}
	
	function wcfm_direct_paypal_single_vendor_order_restriction( $is_allow, $product_id, $quantity ) {
		
		if( defined( 'WCFMmp_VERSION' ) && version_compare( WCFMmp_VERSION, '3.4.0', '>=' ) ) { 
			return $is_allow;
		}
		
		$product = get_post( $product_id );
		$product_author = $product->post_author;
	
		//Iterating through each cart item
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$cart_product_id = $cart_item['product_id'];
			$cart_product = get_post( $cart_product_id );
			$cart_product_author = $cart_product->post_author;
			if( $cart_product_author != $product_author ) {
				$is_allow = false;
				break;
			}
		}
	
		if( !$is_allow ){
			// We display an error message
			wc_clear_notices();
			wc_add_notice( __( "Well, you already have some item in your cart. First checkout with those and then purchase other items!", "wc-frontend-manager-direct-paypal" ), 'error' );
		}
		
		return $is_allow;
	}
	
	function wcfm_direct_paypal_vendor_email_set( $paypal_args, $order ) {
		$line_items          = $order->get_items( 'line_item' );
		foreach ( $line_items as $item_id => $item ) {
		  $product_id  = $item->get_product_id();
		  $product     = get_post( $product_id );
			$vendor_id   = $product->post_author;
			
			if( wcfm_is_vendor( $vendor_id ) ) {
				$vendor_data  = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
				$payment_mode = isset( $vendor_data['payment']['method'] ) ? esc_attr( $vendor_data['payment']['method'] ) : '' ;
				if( $payment_mode && ( $payment_mode == 'paypal' ) ) {
					$paypal_email = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
					
					if( $paypal_email ) {
						$paypal_args['business'] = $paypal_email;
					}
				}
			}
		}
		return $paypal_args;
	}
	
	function wcfm_direct_paypal_available_payment_gateways_vendor_email_validate( $_available_gateways ) {
		$vendor_id = 0;
		if( function_exists( 'is_checkout' ) && is_checkout() ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$cart_product_id = $cart_item['product_id'];
				$cart_product = get_post( $cart_product_id );
				$cart_product_author = $cart_product->post_author;
				if( function_exists( 'wcfm_is_vendor' ) && wcfm_is_vendor( $cart_product_author ) ) $vendor_id = $cart_product_author;
				break;
			}	
			
			foreach( $_available_gateways as $gateway => $gateway_details ) {
				if( $gateway == 'paypal' ) {
					$paypal_email = '';
					if( $vendor_id ) {
						$vendor_data  = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
						$payment_mode = isset( $vendor_data['payment']['method'] ) ? esc_attr( $vendor_data['payment']['method'] ) : '' ;
						if( $payment_mode && ( $payment_mode == 'paypal' ) ) {
							$paypal_email = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
						}
					} else {
						$paypal_email = $gateway_details->get_option( 'email' );
					}
					
					if( !$paypal_email ) {
						unset( $_available_gateways[$gateway] );
					}
					
					break;
				}
			}
		}
		return $_available_gateways;
	}
	
	function wcfm_direct_paypal_refund_vendor_api_credentials_set( $request, $order, $amount, $reason ) {
		
		$vendor_id = $order->get_meta( 'wcfm_direct_paypal_pay_vendor' );
		
		if ( $vendor_id ) {
			if( wcfm_is_vendor( $vendor_id ) ) {
				$vendor_data  = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
				$paypal_email = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
				$api_username  = isset( $vendor_data['payment']['paypal']['api_username'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_username'] ) : '' ;
				$api_pwd       = isset( $vendor_data['payment']['paypal']['api_pwd'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_pwd'] ) : '' ;
				$api_signature = isset( $vendor_data['payment']['paypal']['api_signature'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_signature'] ) : '' ;
				
				if( $paypal_email ) {
					$request['SIGNATURE'] = $api_signature;
					$request['USER']      = $api_username;
					$request['PWD']       = $api_pwd;
				}
			}
		}
		
		return $request;
	}
	
	function wcfm_direct_paypal_captyre_vendor_api_credentials_set( $request, $order, $amount ) {
		
		$vendor_id = $order->get_meta( 'wcfm_direct_paypal_pay_vendor' );
		
		if ( $vendor_id ) {
			if( wcfm_is_vendor( $vendor_id ) ) {
				$vendor_data  = get_user_meta( $product_author, 'wcfmmp_profile_settings', true );
				$paypal_email = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
				$api_username  = isset( $vendor_data['payment']['paypal']['api_username'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_username'] ) : '' ;
				$api_pwd       = isset( $vendor_data['payment']['paypal']['api_pwd'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_pwd'] ) : '' ;
				$api_signature = isset( $vendor_data['payment']['paypal']['api_signature'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_signature'] ) : '' ;
				
				if( $paypal_email ) {
					$request['SIGNATURE'] = $api_signature;
					$request['USER']      = $api_username;
					$request['PWD']       = $api_pwd;
				}
			}
		}
		
		return $request;
	}
	
	function wcfm_direct_paypal_enable_auto_withdrawal( $is_auto_withdrawal, $vendor_id, $order_id, $order, $payment_method ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		if( $payment_method == 'paypal' ) {
			if( wcfm_is_vendor( $vendor_id ) ) {
				$vendor_data  = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
				$paypal_email = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
				
				if( $paypal_email ) {
					$order->update_meta_data( 'wcfm_direct_paypal_pay_vendor', $vendor_id );
					$WCFMmp->wcfmmp_withdrawal_options['withdrawal_reverse'] = 'yes';
					$is_auto_withdrawal = true;
				}
			}
		}
		return $is_auto_withdrawal;
	}
	
	function wcfm_direct_paypal_payment_fields( $payment_fields, $vendor_id ) {
		global $WCFM, $WCFMmp, $wpdb;
		
		$vendor_data   = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		$api_username  = isset( $vendor_data['payment']['paypal']['api_username'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_username'] ) : '' ;
		$api_pwd       = isset( $vendor_data['payment']['paypal']['api_pwd'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_pwd'] ) : '' ;
		$api_signature = isset( $vendor_data['payment']['paypal']['api_signature'] ) ? esc_attr( $vendor_data['payment']['paypal']['api_signature'] ) : '' ;
		
		$paypal_api_fields = array(
			                        "paypal_direct" => array( 'type' => 'html', 'class' => 'paymode_field paymode_paypal', 'value' => '<h2>' . __( 'PayPal API credentials require for PayPal Direct Pay', 'wc-frontend-manager-direct-paypal' ) . '</h2><div class="wcfm_clearfix"></div>' ),
															"api_username" => array('label' => __('API Username', 'wc-frontend-manager-direct-paypal'), 'name' => 'payment[paypal][api_username]', 'type' => 'text', 'custom_attributes' => array( 'required' => true ), 'class' => 'wcfm-text wcfm_ele paymode_field paymode_paypal', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_paypal', 'value' => $api_username ),
															"api_pwd" => array('label' => __('API Password', 'wc-frontend-manager-direct-paypal'), 'name' => 'payment[paypal][api_pwd]', 'type' => 'password', 'custom_attributes' => array( 'required' => true ), 'class' => 'wcfm-text wcfm_ele paymode_field paymode_paypal', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_paypal', 'value' => $api_pwd ),
															"api_signature" => array('label' => __('API Signature', 'wc-frontend-manager-direct-paypal'), 'name' => 'payment[paypal][api_signature]', 'type' => 'password', 'custom_attributes' => array( 'required' => true ), 'class' => 'wcfm-text wcfm_ele paymode_field paymode_paypal', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_paypal', 'value' => $api_signature ),
															);
		
		if( isset( $_REQUEST['store-setup'] ) ) {
			$paypal_api_fields['paypal_direct']['in_table'] = true;
			$paypal_api_fields['api_username']['in_table'] = true;
			$paypal_api_fields['api_pwd']['in_table'] = true;
			$paypal_api_fields['api_signature']['in_table'] = true;
			
			$paypal_api_fields['paypal_direct']['wrapper_class'] = 'paymode_field paymode_paypal';
			$paypal_api_fields['api_username']['wrapper_class'] = 'paymode_field paymode_paypal';
			$paypal_api_fields['api_pwd']['wrapper_class'] = 'paymode_field paymode_paypal';
			$paypal_api_fields['api_signature']['wrapper_class'] = 'paymode_field paymode_paypal';
		}
		
		$payment_fields = array_merge( $payment_fields, $paypal_api_fields );
		
		return $payment_fields;
	}

	
	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'wc-frontend-manager-direct-paypal' );
		
		//load_plugin_textdomain( 'wcfm-tuneer-orders' );
		//load_textdomain( 'wc-frontend-manager-direct-paypal', WP_LANG_DIR . "/wc-frontend-manager-direct-paypal/wc-frontend-manager-direct-paypal-$locale.mo");
		load_textdomain( 'wc-frontend-manager-direct-paypal', $this->plugin_path . "lang/wc-frontend-manager-direct-paypal-$locale.mo");
		load_textdomain( 'wc-frontend-manager-direct-paypal', ABSPATH . "wp-content/languages/plugins/wc-frontend-manager-direct-paypal-$locale.mo");
	}
	
	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}
}