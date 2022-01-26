<?php
/**
 * Plugin Name: WCFM - WooCommerce Multivendor Marketplace - Direct PayPal
 * Plugin URI: https://wclovers.com/product/wc-frontend-manager-direct-paypal
 * Description: WCFM Marketplace Direct PayPal vendor payment gateway 
 * Author: WC Lovers
 * Version: 1.1.1
 * Author URI: https://wclovers.com
 *
 * Text Domain: wc-frontend-manager-direct-paypal
 * Domain Path: /lang/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 5.7.0
 *
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if ( ! class_exists( 'WCFMpgdp_Dependencies' ) )
	require_once 'helpers/class-wc-frontend-manager-direct-paypal-dependencies.php';

if( !WCFMpgdp_Dependencies::woocommerce_plugin_active_check() )
	return;

if( !WCFMpgdp_Dependencies::wcfm_plugin_active_check() )
	return;

if( !WCFMpgdp_Dependencies::wcfmmp_plugin_active_check() )
	return;

require_once 'helpers/wc-frontend-manager-direct-paypal-core-functions.php';
require_once 'wc-frontend-manager-direct-paypal-config.php';

if(!class_exists('WCFM_PG_Direct_PayPal')) {
	include_once( 'core/class-wc-frontend-manager-direct-paypal.php' );
	global $WCFM, $WCFMpgdp, $WCFM_Query;
	$WCFMpgdp = new WCFM_PG_Direct_PayPal( __FILE__ );
	$GLOBALS['WCFMpgdp'] = $WCFMpgdp;
}