<?php
/*
  Plugin Name: SCD - Smart Currency Detector Variant for WCFM
  Plugin URI: http://gajelabs.com/product/scd
   Description: This wordpress / woocommerce plugin is an ALL-IN-ONE solution for online market places owners, sellers, end customers. Multivendors variant
  Version: 1.0.0
   WC tested up to: 5.6
  Author: GaJeLabs
  Author URI: http://gajelabs.com
 */
 
if (in_array('scd-smart-currency-detector/index.php', apply_filters('active_plugins', get_option('active_plugins')))){
include 'scd_multivendors_renders.php';
require 'scd_wcfm_multivendor.php';
include 'includes/index.php';
}


define( 'SCDS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

function scd_multi_add_scrypt_topost() {
    wp_enqueue_script("scd-pro-ready", trailingslashit(plugins_url("", __FILE__)) . "js/scd_pro_postready.js", array("jquery"));
    wp_enqueue_script("scd-wcfm-multivendor", trailingslashit(plugins_url("", __FILE__)) . "js/scd_wcfm_multivendor.js", array("jquery"));
}
add_action('wp_enqueue_scripts', 'scd_multi_add_scrypt_topost');


add_action('admin_notices','scd_premium_require');
function scd_premium_require() {
if(!is_plugin_active('scd-smart-currency-detector/index.php')){
    echo '<h3 style="color:red;">SCD-Smart Currency Detector for WCFM require scd-smart-currency-detector before use, please <a target="__blank" href="https://wordpress.org/plugins/scd-smart-currency-detector/"> download and install it here</a><h3>';    
}
}  