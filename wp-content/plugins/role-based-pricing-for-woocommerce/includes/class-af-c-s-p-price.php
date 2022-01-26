<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AF_C_S_P_Price {

	public $all_pricing_rules;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->all_pricing_rules = $this->load_pricing_rules();
	}

	/**
	 * Load all pricing rules.
	 */
	public function load_pricing_rules() {

		$args = array(
			'post_type'   => 'csp_rules',
			'post_status' => 'publish',
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
			'numberposts' => -1,
			'fields'      => 'ids'
		);

		return get_posts( $args );
	}

	/**
	 * Return a price HTML of product.
	 *
	 * @param  Object $price_html HTML of default price.
	 * @param  Object $product    product id or object.
	 * @param  Object $user       User id or object.
	 * @return float
	 */
	public function get_price_html_of_product( $price_html, $product, $user = false, $user_role = 'guest' ) {

		if ( $this->is_product_price_hidden( $product, $user, $user_role ) ) {
			$cps_price_text = get_option( 'csp_price_text' );
			return $cps_price_text;
		}

		$has_price = $this->have_price_of_product( $product, $user, $user_role );

		// var_dump( $has_price );
		// die();
		
		if ( ! $has_price ) {
			return $price_html;
		}

		$replace_original = $this->is_replace_price( $product, $user, $user_role );

		switch ( $product->get_type() ) {

			case 'simple':
			case 'variation':
				return $this->get_product_price_html( $product, $replace_original );

			case 'variable':
				return $this->get_variable_product_price_html( $product, $replace_original );

			case 'grouped':
				return $this->get_grouped_product_price_html( $product, $replace_original );
			default:
				return $price_html;
		}
	}

	/**
	 * Return HTML of role based price of grouped product.
	 *
	 * @param  Object $product product object.
	 * @param  bool   $replace_original Replace original price.
	 * @return string
	 */
	public function get_grouped_product_price_html( $product, $replace_original ) {

		$price            = '';
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$child_prices     = array();
		$children         = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

		foreach ( $children as $child ) {
			if ( '' !== $child->get_price() ) {
				$child_prices[]    = 'incl' === $tax_display_mode ? wc_get_price_including_tax( $child ) : wc_get_price_excluding_tax( $child );
				$max_price_regular = empty( $max_price_regular ) || $child->get_regular_price() > $max_price_regular ? $child->get_regular_price() : $max_price_regular;
			}
		}

		if ( ! empty( $child_prices ) ) {
			$min_price = min( $child_prices );
			$max_price = max( $child_prices );
		} else {
			$min_price = '';
			$max_price = '';
		}

		if ( '' !== $min_price ) {

			if ( $min_price !== $max_price ) {
				$price = wc_format_price_range( $min_price, $max_price );

			} else {

				if ( $replace_original ) {
					$price = wc_price( $min_price );
				} else {
					$price = wc_format_sale_price( wc_price( $max_price_regular ), wc_price( $max_price ) );
				}
			}
		}

		return $price . $product->get_price_suffix();
	}

	/**
	 * Return HTML of role based price of simple/variation product.
	 *
	 * @param  Object $product product object.
	 * @param  bool   $replace_original Replace original price.
	 * @return string
	 */
	public function get_product_price_html( $product, $replace_original ) {

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$active_price     = 'incl' === $tax_display_mode ?  wc_get_price_including_tax( $product ) : wc_get_price_excluding_tax( $product );
		$args             = array('price' => $product->get_regular_price() );
		$regular_price    = 'incl' === $tax_display_mode ?  wc_get_price_including_tax( $product, $args ) : wc_get_price_excluding_tax( $product, $args );

		if ( $active_price >= $regular_price ) {
			return wc_price( $active_price ) . $product->get_price_suffix();
		}

		if ( $replace_original ) {
			return wc_price( $active_price ) . $product->get_price_suffix();
		}

		return wc_format_sale_price( wc_price( $regular_price ), wc_price( $active_price ) ) . $product->get_price_suffix();
	}

	/**
	 * Return HTML of role based price of variable product.
	 *
	 * @param  Object $product product object.
	 * @param  bool   $replace_original Replace original price.
	 * @return string
	 */
	public function get_variable_product_price_html( $product, $replace_original ) {

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$child_prices     = array();
		$children         = array_map( 'wc_get_product', $product->get_visible_children() );

		foreach ( $children as $child ) {
			if ( '' !== $child->get_price() ) {
				$child_prices[] = 'incl' === $tax_display_mode ? wc_get_price_including_tax( $child ) : wc_get_price_excluding_tax( $child );

				$args = array('price' => $child->get_regular_price() );

				$child_prices_regular[] = 'incl' === $tax_display_mode ? wc_get_price_including_tax( $child, $args ) : wc_get_price_excluding_tax( $child, $args );
			}
		}

		if ( ! empty( $child_prices ) ) {
			$min_price         = min( $child_prices );
			$max_price         = max( $child_prices );
			$max_price_regular = max( $child_prices_regular );
		} else {
			$min_price         = '';
			$max_price         = '';
			$max_price_regular = '';
		}

		if ( $min_price == $max_price ) {

			if ( $replace_original ) {
				return wc_price( $max_price ) . $product->get_price_suffix();
			}

			return wc_format_sale_price( wc_price( $max_price_regular ), wc_price( $max_price ) ) . $product->get_price_suffix();
		}

		return wc_format_price_range( $min_price, $max_price );
	}

	/**
	 * Return true if product has role based price/Customer Specific Price.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user User id or object.
	 * @return bool
	 */
	public function have_price_of_product( $product, $user = false, $user_role = 'guest' ) {

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		if ( empty( $user ) && 'guest' == $user_role ) {

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				if ( $user ) {
					$user_role = current( $user->roles );
				}

			} else {

				$user_role = 'guest';
			}
		}

		if ( $user ) {

			if ( $product->is_type('variable') || $product->is_type('grouped') ) {

				$variations = $product->get_children();

				foreach ( $variations as $variation_id ) {

					$have_role_price = $this->have_customer_price_of_product( wc_get_product( $variation_id ), $user->ID );

					if ( $have_role_price ) {
						return $have_role_price;
					}
				}

			} else {

				$have_role_price = $this->have_customer_price_of_product( $product, $user->ID );

				if ( $have_role_price ) {
					return $have_role_price;
				}
			}

			
		}

		if ( $user_role ) {

			if ( $user && count( $user->roles ) > 1 ) {

				$user_roles = $user->roles;

				foreach ( $user_roles as $user_role ) {

					if ( $product->is_type('variable') || $product->is_type('grouped') ) {

						$variations = $product->get_children();

						foreach ( $variations as $variation_id ) {

							$have_role_price = $this->have_role_price_of_product( wc_get_product( $variation_id ), $user_role );

							if ( $have_role_price ) {
								return $have_role_price;
							}
						}

					} else {

						$have_role_price = $this->have_role_price_of_product( $product, $user_role );

						if ( $have_role_price ) {
							return $have_role_price;
						}
					}
				}

			} else {

				if ( $product->is_type('variable') || $product->is_type('grouped') ) {

						$variations = $product->get_children();

					foreach ( $variations as $variation_id ) {

						$have_role_price = $this->have_role_price_of_product( wc_get_product( $variation_id ), $user_role );

						if ( $have_role_price ) {
							return $have_role_price;
						}
					}

				} else {

					$have_role_price = $this->have_role_price_of_product( $product, $user_role );

					if ( $have_role_price ) {
						return $have_role_price;
					}
				}
			}
			
		}

		return false;
	}

	/**
	 * Return true if product has customer based priced.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user User id or object.
	 * @return bool
	 */
	public function have_customer_price_of_product( $product, $user_id ) {

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		// get customer specific price.
		$cus_base_price = get_post_meta( $product->get_id(), '_cus_base_price', true );

		if ( ! empty( $cus_base_price ) ) {

			foreach ( $cus_base_price as $cus_price ) {

				if ( empty( $cus_price['customer_name'] ) || empty( $cus_price['discount_value'] ) || empty( $cus_price['discount_type'] ) ) {
					continue;
				}

				if ( $user_id == $cus_price['customer_name'] ) {

					return true;
				}
			}
		}

		if ( empty( $this->all_pricing_rules ) ) {
			return;
		}

		foreach ( $this->all_pricing_rules as $rule_id ) {

			if ( $this->is_rule_valid_for_product( $product, $rule_id ) ) {

				$rule_cus_base_price = get_post_meta( $rule_id, 'rcus_base_price', true );

				if ( empty( $rule_cus_base_price ) ) {
					continue;
				}

				foreach ( $rule_cus_base_price as $cus_price ) {

					if ( empty( $cus_price['customer_name'] ) || empty( $cus_price['discount_value'] ) || empty( $cus_price['discount_type'] ) ) {
						continue;
					}

					if ( $user_id == $cus_price['customer_name'] ) {

						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Return true if product has role based price.
	 *
	 * @param  Object $product   product id or object.
	 * @param  Object $user_role User Role.
	 * @return bool
	 */
	public function have_role_price_of_product( $product, $user_role ) {

		$role_base_price = get_post_meta( $product->get_id(), '_role_base_price_' . $user_role, true );
		$afrbp_prices    = (array) unserialize( $role_base_price );

		if ( ! empty( array_filter( $afrbp_prices ) ) ) {
			
			if ( !empty( $afrbp_prices['discount_value'] ) && !empty( $afrbp_prices['discount_type'] ) ) {

				return true;
			}
		}

		if ( empty( $this->all_pricing_rules ) ) {
			return false;
		}

		foreach ( $this->all_pricing_rules as $rule_id ) {

			if ( $this->is_rule_valid_for_product( $product, $rule_id ) ) {

				$role_base_price = get_post_meta( $rule_id, 'rrole_base_price_' . $user_role, true );
				$afrbp_prices    = (array) unserialize( $role_base_price );

				if ( ! empty( array_filter( $afrbp_prices ) ) ) {
					
					if ( !empty( $afrbp_prices['discount_value'] ) && !empty( $afrbp_prices['discount_type'] ) ) {

						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Return a role based price of product.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user User id or object.
	 * @return float
	 */
	public function get_price_of_product( $product, $user = false, $user_role = 'guest' ) {

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		if ( empty( $user ) && 'guest' == $user_role ) {

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				if ( $user ) {
					$user_role = current( $user->roles );
				}

			} else {

				$user_role = 'guest';
			}
		}

		if ( $user ) {

			$customer_price = $this->get_customer_price_of_product( $product, $user->ID );

			if ( $customer_price ) {
				return $customer_price;
			}
		}

		if ( $user_role ) {

			if ( $user && count( $user->roles ) > 1 ) {

				foreach ( $user->roles as $user_role ) {

					$role_price = $this->get_role_price_of_product( $product, $user_role );

					if ( $role_price ) {
						return $role_price;
					}
				}

			} else {

				$role_price = $this->get_role_price_of_product( $product, $user_role );

				if ( $role_price ) {
					return $role_price;
				}
			}
		}

		return false;
	}

	/**
	 * Return whether to replace the base price or not.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user User id or object.
	 * @param  string $user_role User role.
	 * @return bool
	 */
	public function is_replace_price( $product, $user = false, $user_role = 'guest' ) {

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		if ( empty( $user ) && 'guest' == $user_role ) {

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				if ( $user ) {
					$user_role = current( $user->roles );
				}

			} else {

				$user_role = 'guest';
			}
		}

		if ( $user ) {

			$replace = $this->is_replace_for_customer( $product, $user->ID );

			if ( is_bool( $replace ) ) {
				return $replace;
			}
		}

		if ( $user_role ) {

			$replace = $this->is_replace_for_role( $product, $user_role );

			if ( is_bool( $replace ) ) {
				return $replace;
			}
		}

		return false;
	}

	/**
	 * Return a customer based price of product either by rule or product level.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user_id User id.
	 * @return float
	 */
	public function is_replace_for_customer( $product, $user_id ) {

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		// get customer specific price.
		$cus_base_price = get_post_meta( $product->get_id(), '_cus_base_price', true );

		if ( ! empty( $cus_base_price ) ) {

			foreach ( $cus_base_price as $cus_price ) {

				if ( empty( $cus_price['customer_name'] ) || empty( $cus_price['discount_value'] ) || empty( $cus_price['discount_type'] ) ) {
					continue;
				}

				if ( $user_id == $cus_price['customer_name'] ) {

					return isset( $cus_price['replace_orignal_price'] ) && 'yes' == $cus_price['replace_orignal_price'] ? true : false;
				}
			}
		}

		if ( empty( $this->all_pricing_rules ) ) {
			return;
		}

		foreach ( $this->all_pricing_rules as $rule_id ) {

			if ( $this->is_rule_valid_for_product( $product, $rule_id ) ) {

				$rule_cus_base_price = get_post_meta( $rule_id, 'rcus_base_price', true );

				if ( empty( $rule_cus_base_price ) ) {
					continue;
				}

				foreach ( $rule_cus_base_price as $cus_price ) {

					if ( empty( $cus_price['customer_name'] ) || empty( $cus_price['discount_value'] ) || empty( $cus_price['discount_type'] ) ) {
						continue;
					}

					if ( $user_id == $cus_price['customer_name'] ) {

						return isset( $cus_price['replace_orignal_price'] ) && 'yes' == $cus_price['replace_orignal_price'] ? true : false;
					}
				}
			}
		}
	}

	/**
	 * Return a customer based price of product either by rule or product level.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user_id User id.
	 * @return float
	 */
	public function is_replace_for_role( $product, $user_role ) {

		$role_base_price = get_post_meta( $product->get_id(), '_role_base_price_' . $user_role, true );
		$afrbp_prices    = (array) unserialize( $role_base_price );

		if ( ! empty( array_filter( $afrbp_prices ) ) ) {
			
			if ( !empty( $afrbp_prices['discount_value'] ) && !empty( $afrbp_prices['discount_type'] ) ) {

				return isset( $afrbp_prices['replace_orignal_price'] ) && 'yes' == $afrbp_prices['replace_orignal_price'] ? true : false;
			}
		}

		if ( empty( $this->all_pricing_rules ) ) {
			return false;
		}

		foreach ( $this->all_pricing_rules as $rule_id ) {

			if ( $this->is_rule_valid_for_product( $product, $rule_id ) ) {

				$role_base_price = get_post_meta( $rule_id, 'rrole_base_price_' . $user_role, true );
				$afrbp_prices    = (array) unserialize( $role_base_price );

				if ( ! empty( array_filter( $afrbp_prices ) ) ) {
					
					if ( !empty( $afrbp_prices['discount_value'] ) && !empty( $afrbp_prices['discount_type'] ) ) {

						return isset( $afrbp_prices['replace_orignal_price'] ) && 'yes' == $afrbp_prices['replace_orignal_price'] ? true : false;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Return a customer based price of product either by rule or product level.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user_id User id.
	 * @return float
	 */
	public function get_customer_price_of_product( $product, $user_id ) {

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		// get customer specific price.
		$cus_base_price = get_post_meta( $product->get_id(), '_cus_base_price', true );

		if ( ! empty( $cus_base_price ) ) {

			foreach ( $cus_base_price as $cus_price ) {

				if ( empty( $cus_price['customer_name'] ) || empty( $cus_price['discount_value'] ) || empty( $cus_price['discount_type'] ) ) {
					continue;
				}

				if ( $user_id == $cus_price['customer_name'] ) {

					return $this->get_discounted_price( $product->get_price('edit'), $cus_price['discount_value'], $cus_price['discount_type'] );
				}
			}
		}

		if ( empty( $this->all_pricing_rules ) ) {
			return false;
		}

		foreach ( $this->all_pricing_rules as $rule_id ) {

			if ( $this->is_rule_valid_for_product( $product, $rule_id ) ) {

				$rule_cus_base_price = get_post_meta( $rule_id, 'rcus_base_price', true );

				if ( empty( $rule_cus_base_price ) ) {
					continue;
				}

				foreach ( $rule_cus_base_price as $cus_price ) {

					if ( empty( $cus_price['customer_name'] ) || empty( $cus_price['discount_value'] ) || empty( $cus_price['discount_type'] ) ) {
						continue;
					}

					if ( $user_id == $cus_price['customer_name'] ) {

						return $this->get_discounted_price( $product->get_price('edit'), $cus_price['discount_value'], $cus_price['discount_type'] );
					}
				}
			}
		}

		return false;
	}

	/**
	 * Return a role based price of product either by rule or product level.
	 *
	 * @param  Object $product product id or object.
	 * @param  Object $user_id User id.
	 * @return float|false
	 */
	public function get_role_price_of_product( $product, $user_role ) {

		if ( !is_a( $product, 'WC_Product') ) {
			return false;
		}

		$role_base_price = get_post_meta( $product->get_id(), '_role_base_price_' . $user_role, true );
		$afrbp_prices    = (array) unserialize( $role_base_price );

		if ( ! empty( array_filter( $afrbp_prices ) ) ) {
			
			if ( !empty( $afrbp_prices['discount_value'] ) && !empty( $afrbp_prices['discount_type'] ) ) {

				return $this->get_discounted_price( $product->get_price('edit'), $afrbp_prices['discount_value'], $afrbp_prices['discount_type']);
			}
		}

		if ( empty( $this->all_pricing_rules ) ) {
			return false;
		}

		foreach ( $this->all_pricing_rules as $rule_id ) {

			if ( $this->is_rule_valid_for_product( $product, $rule_id ) ) {

				$role_base_price = get_post_meta( $rule_id, 'rrole_base_price_' . $user_role, true );
				$afrbp_prices    = (array) unserialize( $role_base_price );

				if ( ! empty( array_filter( $afrbp_prices ) ) ) {
					
					if ( !empty( $afrbp_prices['discount_value'] ) && !empty( $afrbp_prices['discount_type'] ) ) {

						return $this->get_discounted_price( $product->get_price('edit'), $afrbp_prices['discount_value'], $afrbp_prices['discount_type']);
					}
				}
			}
		}

		return false;
	}

	/**
	 * Apply discount on price.
	 *
	 * @param  float  $price Price original.
	 * @param  float  $value Discount value.
	 * @param  string $type Type of discount.
	 * @return float
	 */
	public function get_discounted_price( $price, $value, $type ) {

		$price = is_numeric( $price ) ? $price : floatval( $price );
		$value = is_numeric( $value ) ? $value : floatval( $value );

		switch ( $type ) {

			case 'fixed_price':
				return $value;

			case 'fixed_increase':
				return ( $price + $value ) < 0 ? 0 : $price + $value;

			case 'fixed_decrease':
				return ( $price - $value ) < 0 ? 0 : $price - $value;

			case 'percentage_increase':
				return $price + ( $price * $value / 100 );

			case 'percentage_decrease':
				return $price - ( $price * $value / 100 );
		}
	}

	/**
	 * Check if the rule is valid for product.
	 *
	 * @param  Object $product Product object.
	 * @param  int    $rule_id Rule ID.
	 * @return bool
	 */
	public function is_rule_valid_for_product( $product, $rule_id ) {

		$applied_on_all_products = get_post_meta( $rule_id, 'csp_apply_on_all_products', true );
		$products                = get_post_meta( $rule_id, 'csp_applied_on_products', true);
		$categories              = get_post_meta( $rule_id, 'csp_applied_on_categories', true);

		if ('yes' == $applied_on_all_products ) {
			return true;
		} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
			return true;
		}

		if (!empty( $categories) ) {

			foreach ( $categories as $cat ) {

				if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

					return true;
				} 
			}
		}

		return false;
	}

	/**
	 * Check if the price is hidden for product.
	 *
	 * @param  Object $product   Product object.
	 * @param  int    $user      User ID/User object.
	 * @param  int    $user_role User role.
	 * @return bool
	 */
	public function is_product_price_hidden( $product, $user = false, $user_role = 'guest' ) {

		$enable_hide_price_feature = get_option( 'csp_enable_hide_pirce' );
		$enable_for_guest          = get_option( 'csp_enable_hide_pirce_guest' );
		$enable_for_registered     = get_option( 'csp_enable_hide_pirce_registered' );
		$csp_hide_user_role        = (array) unserialize( get_option( 'csp_hide_user_role' ) );
		$enable_hide_price         = get_option( 'csp_hide_price' );

		if ( 'yes' != $enable_hide_price_feature || 'yes' != $enable_hide_price ) {
			return false;
		}

		if ( !empty( $user ) ) {

			if ( is_int( $user ) ) {
				$user = get_user_by( 'id', $user );
			}

			if ( is_a( $user, 'WP_User') ) {
				$user_role = current( $user->roles );
			}

		} elseif ( 'guest' == $user_role && is_user_logged_in() ) {

			$user_role = current( wp_get_current_user()->roles );
		}

		$valid_for_user = false;

		if ( 'yes' == $enable_for_registered && in_array( $user_role, $csp_hide_user_role ) ) {
			$valid_for_user = true;
		}

		if ( 'guest' == $user_role && 'yes' == $enable_for_guest ) {
			$valid_for_user = true;
		}

		if ( !$valid_for_user ) {
			return false;
		}

		$csp_hide_products   =  get_option( 'csp_hide_products' );
		$csp_hide_categories =  get_option( 'cps_hide_categories' );

		if ( empty( $csp_hide_products ) && empty( $csp_hide_categories ) ) {
			return true;
		}

		if ( !empty( $csp_hide_products ) ) {

			$csp_hide_products = unserialize( $csp_hide_products );

			if ( in_array( $product->get_id(), $csp_hide_products ) || in_array( $product->get_parent_id(), $csp_hide_products ) ) {
				return true;
			}
		}

		if ( !empty( $csp_hide_categories ) ) {

			$csp_hide_categories = unserialize( $csp_hide_categories );

			foreach ( $csp_hide_categories as $cat ) {

				if ( !empty( $cat ) && has_term( $cat, 'product_cat', $product->get_id() ) ) {
					return true;
				}

				if ( !empty( $cat ) && has_term( $cat, 'product_cat', $product->get_parent_id() ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check if the add to cart is hidden for product.
	 *
	 * @param  Object $product   Product object.
	 * @param  int    $user      User ID/User object.
	 * @param  int    $user_role User role.
	 * @return bool
	 */
	public function is_product_add_to_cart_button_hidden( $product, $user = false, $user_role = 'guest' ) {

		$enable_hide_price_feature = get_option( 'csp_enable_hide_pirce' );
		$enable_for_guest          = get_option( 'csp_enable_hide_pirce_guest' );
		$enable_for_registered     = get_option( 'csp_enable_hide_pirce_registered' );
		$csp_hide_user_role        = (array) unserialize( get_option( 'csp_hide_user_role' ) );

		$csp_hide_cart_button = get_option( 'csp_hide_cart_button' );
		$csp_cart_button_text = get_option( 'csp_cart_button_text' );
		$csp_cart_button_link = get_option( 'csp_cart_button_link' );

		if ( 'yes' != $enable_hide_price_feature || 'yes' != $csp_hide_cart_button ) {
			return false;
		}

		if ( !empty( $user ) ) {

			if ( is_int( $user ) ) {
				$user = get_user_by( 'id', $user );
			}

			if ( is_a( $user, 'WP_User') ) {
				$user_role = current( $user->roles );
			}
			
		} elseif ( 'guest' == $user_role && is_user_logged_in() ) {

			$user_role = current( wp_get_current_user()->roles );
		}

		$valid_for_user = false;

		if ( 'yes' == $enable_for_registered && in_array( $user_role, $csp_hide_user_role ) ) {
			$valid_for_user = true;
		}

		if ( 'guest' == $user_role && 'yes' == $enable_for_guest ) {
			$valid_for_user = true;
		}

		if ( !$valid_for_user ) {
			return false;
		}

		$csp_hide_products   =  get_option( 'csp_hide_products' );
		$csp_hide_categories =  get_option( 'cps_hide_categories' );

		if ( empty( $csp_hide_products ) && empty( $csp_hide_categories ) ) {
			return true;
		}

		if ( !empty( $csp_hide_products ) ) {

			$csp_hide_products = unserialize( $csp_hide_products );

			if ( in_array( $product->get_id(), $csp_hide_products ) ) {
				return true;
			}
		}

		if ( !empty( $csp_hide_categories ) ) {

			$csp_hide_categories = unserialize( $csp_hide_categories );

			foreach ( $csp_hide_categories as $cat ) {

				if ( !empty( $cat ) && has_term( $cat, 'product_cat', $product->get_id() ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
