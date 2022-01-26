<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'addify_csp_get_role_based_price') ) {

	/**
	 * Return a role based price of product.
	 *
	 * @param  Object $product   product id or object.
	 * @param  Object $user      User id or object.
	 * @param  Object $user_role User Role.
	 * @return float
	 */
	function addify_csp_get_role_based_price( $product, $user = false, $user_role = 'guest' ) {

		if ( !class_exists( 'AF_C_S_P_Price' ) ) {
			require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-price.php';
		}

		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		$price_object = new AF_C_S_P_Price();

		return $price_object->get_price_of_product( $product, $user, $user_role );
	}
}

if ( !function_exists( 'addify_csp_get_role_based_price_html') ) {

	/**
	 * Return a role based price of product.
	 *
	 * @param  string $price_html HTML of product price.
	 * @param  Object $product    product id or object.
	 * @param  Object $user       User id or object.
	 * @param  Object $user_role  User Role.
	 * @return bool
	 */
	function addify_csp_get_role_based_price_html( $price_html, $product, $user = false, $user_role = 'guest' ) {

		if ( !class_exists( 'AF_C_S_P_Price' ) ) {
			require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-price.php';
		}

		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( !is_a( $product, 'WC_Product') ) {
			return $price_html;
		}

		$price_object = new AF_C_S_P_Price();

		return $price_object->get_price_html_of_product( $price_html, $product, $user, $user_role );
	}
}

if ( !function_exists( 'addify_csp_have_price_of_product') ) {

	/**
	 * Return true if product have role based pricing.
	 *
	 * @param  Object $product   product id or object.
	 * @param  Object $user      User id or object.
	 * @param  Object $user_role User Role.
	 * @return bool
	 */
	function addify_csp_have_price_of_product( $product, $user = false, $user_role = 'guest' ) {

		if ( !class_exists( 'AF_C_S_P_Price' ) ) {
			require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-price.php';
		}

		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		$price_object = new AF_C_S_P_Price();

		return $price_object->have_price_of_product( $product, $user, $user_role );
	}
}

if ( !function_exists( 'addify_csp_is_product_price_hidden') ) {

	/**
	 * Return true if product price is hidden.
	 *
	 * @param  Object $product   product id or object.
	 * @param  Object $user      User id or object.
	 * @param  Object $user_role User Role.
	 * @return bool
	 */
	function addify_csp_is_product_price_hidden( $product, $user = false, $user_role = 'guest' ) {

		if ( !class_exists( 'AF_C_S_P_Price' ) ) {
			require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-price.php';
		}

		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		$price_object = new AF_C_S_P_Price();

		return $price_object->is_product_price_hidden( $product, $user, $user_role );
	}
}

if ( !function_exists( 'addify_csp_is_product_add_to_cart_button_hidden') ) {

	/**
	 * Return true if add to cart button is hidden for product.
	 *
	 * @param  Object $product   product id or object.
	 * @param  Object $user      User id or object.
	 * @param  Object $user_role User Role.
	 * @return bool
	 */
	function addify_csp_is_product_add_to_cart_button_hidden( $product, $user = false, $user_role = 'guest' ) {

		if ( !class_exists( 'AF_C_S_P_Price' ) ) {
			require ADDIFY_CSP_PLUGINDIR . 'includes/class-af-c-s-p-price.php';
		}

		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		$price_object = new AF_C_S_P_Price();

		return $price_object->is_product_add_to_cart_button_hidden( $product, $user, $user_role );
	}
}
