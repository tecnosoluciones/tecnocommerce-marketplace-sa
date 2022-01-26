<?php

/*
* Plugin Name:       Role Based Pricing for WooCommerce
* Plugin URI:        https://woocommerce.com/products/role-based-pricing-for-woocommerce/
* Description:        WooCommerce Role Based Pricing plugin empowers merchants to set product prices based on user roles and individual customers. (PLEASE TAKE BACKUP BEFORE UPDATING THE PLUGIN).
* Version:           1.5.8
* Author:            Addify
* Developed By:      Addify
* Author URI:        http://www.addifypro.com
* Support:           http://www.addifypro.com
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Domain Path:       /languages
* Text Domain:       addify_role_price
*
* Woo: 4971447:64b09622d1a85d9a1480337b46b432a1
*
* WC requires at least: 3.0.9
* WC tested up to: 6.*.*
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Check for multisite
if ( ! is_multisite() && ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function afcsp_admin_notice() {

		$afcsp_allowed_tags = array(
			'a'      => array(
				'class' => array(),
				'href'  => array(),
				'rel'   => array(),
				'title' => array(),
			),
			'b'      => array(),

			'div'    => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'p'      => array(
				'class' => array(),
			),
			'strong' => array(),

		);

		// Deactivate the plugin
		deactivate_plugins( __FILE__ );

		$afcsp_woo_check = '<div id="message" class="error">
			<p><strong>Role Based Prices for WooCommerce plugin is inactive.</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> must be active for this plugin to work. Please install &amp; activate WooCommerce »</p></div>';
		echo wp_kses( __( $afcsp_woo_check, 'addify_role_price' ), $afcsp_allowed_tags );

	}
	add_action( 'admin_notices', 'afcsp_admin_notice' );
}

if ( ! class_exists( 'Addify_Customer_And_Role_Pricing' ) ) {

	class Addify_Customer_And_Role_Pricing {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'main_init' ) );
			add_action( 'init', array( $this, 'csp_custom_post' ) );

			$this->constant_vars();

			require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-price.php';
			require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-main.php';
			
			require ADDIFY_CSP_PLUGINDIR . 'includes/af-product-pricing-functions.php';
			
			if ( is_admin() ) {
				require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-admin.php';
			} else {
				require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-front.php';
			}
		}

		public function main_init() {
			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'addify_role_price', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}
		}

		public function constant_vars() {

			if ( ! defined( 'ADDIFY_CSP_URL' ) ) {
				define( 'ADDIFY_CSP_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'ADDIFY_CSP_BASENAME' ) ) {
				define( 'ADDIFY_CSP_BASENAME', plugin_basename( __FILE__ ) );
			}

			if ( ! defined( 'ADDIFY_CSP_PLUGINDIR' ) ) {
				define( 'ADDIFY_CSP_PLUGINDIR', plugin_dir_path( __FILE__ ) );
			}
		}

		public function csp_custom_post() {

			$labels = array(
				'name'                => __( 'Role Based Pricing Rules', 'addify_role_price' ),
				'singular_name'       => __( 'Role Based Pricing Rules', 'addify_role_price' ),
				'add_new'             => __( 'Add New Rule', 'addify_role_price' ),
				'add_new_item'        => __( 'Add Rule', 'addify_role_price' ),
				'edit_item'           => __( 'Edit Rule', 'addify_role_price' ),
				'new_item'            => __( 'New Rule', 'addify_role_price' ),
				'view_item'           => __( 'View Rule', 'addify_role_price' ),
				'search_items'        => __( 'Search Rule', 'addify_role_price' ),
				'exclude_from_search' => true,
				'not_found'           => __( 'No rule found', 'addify_role_price' ),
				'not_found_in_trash'  => __( 'No rule found in trash', 'addify_role_price' ),
				'parent_item_colon'   => '',
				'all_items'           => __( 'All Rules', 'addify_role_price' ),
				'menu_name'           => __( 'Role Based Pricing', 'addify_role_price' ),
			);

			$args = array(
				'labels'             => $labels,
				'menu_icon'          => plugin_dir_url( __FILE__ ) . 'assets/img/small_logo_grey.png',
				'public'             => false,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => true,
				'capability_type'    => 'page',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 30,
				'rewrite'            => array(
					'slug'       => 'csp-rule',
					'with_front' => false,
				),
				'supports'           => array( 'title' ),
			);

			register_post_type( 'csp_rules', $args );

		}
	}

	new Addify_Customer_And_Role_Pricing();
}
