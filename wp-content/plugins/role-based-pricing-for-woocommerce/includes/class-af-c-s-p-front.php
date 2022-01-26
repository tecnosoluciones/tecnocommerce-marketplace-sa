<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AF_C_S_P_Front' ) ) {

	/**
	 * Front class.
	 */
	class AF_C_S_P_Front {

		public $allfetchedrules;

		/**
		 * Main __construct start.
		 */
		public function __construct() {

			$this->allfetchedrules = $this->csp_load();
			
			add_action( 'wp_loaded', array( $this, 'csp_front_scripts' ) );

			// Min and Max Qty validation.
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'csp_validate_min_max_qty' ), 10, 4 );

			// Update Cart validation.
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'csp_update_cart_quantity_validation' ), 10, 4 );
		}

		/**
		 * Main function start.
		 */
		public function csp_front_scripts() {

			wp_enqueue_style( 'addify_csp_front_css', ADDIFY_CSP_URL . '/assets/css/addify_csp_front_css.css', false, '1.0' );
			wp_enqueue_script( 'af_csp_front_js', ADDIFY_CSP_URL . 'assets/js/addify_csp_front_js.js', array( 'jquery' ), '1.0' );
		}

		/**
		 * Main function start.
		 */
		public function csp_load() {
			// get Rules
			$args = array(
				'post_type'   => 'csp_rules',
				'post_status' => 'publish',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'numberposts' => -1,
			);
			return get_posts( $args );
		}

		/**
		 * Main function start.
		 *
		 * @param init $csppdata .
		 *
		 * @param init $product_id .
		 *
		 * @param init $qty .
		 *
		 * @param init $variation_id .
		 */
		public function csp_validate_min_max_qty( $csppdata, $product_id, $qty, $variation_id = 0 ) {

			wc()->initialize_session();

			$user = wp_get_current_user();

			$prod = wc_get_product( $variation_id );

			if ( is_user_logged_in() ) {

				if ( 0 == $variation_id ) {
					// Simple Product.

					$targeted_id = $product_id;

					foreach ( WC()->cart->get_cart() as $cart_item ) {
						if ( $cart_item['product_id'] == $targeted_id ) {
							$oqty = $cart_item['quantity'];
							break; // stop the loop if product is found.
						}
					}
					// Displaying the quantity if targeted product is in cart.
					if ( ! empty( $oqty ) ) {

						$old_qty = $oqty;
					} else {
						$old_qty = 0;
					}

					// get customer specifc price.
					$cus_base_price = get_post_meta( $product_id, '_cus_base_price', true );

					// get role base price.
					foreach ( $user->roles as $value ) {

						$role_base_price = get_post_meta( $product_id, '_role_base_price_' . $value, true );

						$afrbp_prices = (array) unserialize( $role_base_price );

						if ( empty( $afrbp_prices['discount_value'] ) || empty( $afrbp_prices['discount_type'] ) ) {
							continue;
						} else {
							break;
						}
					}

					if ( ! empty( $cus_base_price ) ) {
						foreach ( $cus_base_price as $cus_price ) {

							if ( $user->ID == $cus_price['customer_name'] ) {

								if ( '' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

									if ( '' != $cus_price['min_qty'] || 0 != $cus_price['min_qty'] ) {
										$min_qty = intval( $cus_price['min_qty'] );
									} else {
										$min_qty = '';
									}

									if ( '' != $cus_price['max_qty'] || 0 != $cus_price['max_qty'] ) {
										$max_qty = intval( $cus_price['max_qty'] );
									} else {
										$max_qty = '';
									}

									if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
										$csppdata      = false;
										$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
										$this->csp_wc_add_notice( $error_message );
										return $csppdata;

									} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

										$csppdata      = false;
										$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
										$this->csp_wc_add_notice( $error_message );
										return $csppdata;

									} else {
										return true;
									}
								}
							}
						}
					}

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( true && 1 != count($afrbp_prices) ) {

						// Product Price.
						if ( '' != $afrbp_prices['discount_value'] || 0 != $afrbp_prices['discount_value'] ) {

							if ( '' != $afrbp_prices['min_qty'] || 0 != $afrbp_prices['min_qty'] ) {
								$min_qty = intval( $afrbp_prices['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices['max_qty'] || 0 != $afrbp_prices['max_qty'] ) {
								$max_qty = intval( $afrbp_prices['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} else {
								return true;
							}
						}
					}

					// Rules.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}
						$product = wc_get_product($product_id);
						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product_id, $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									foreach ( $user->roles as $value ) {

										$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price_' . $value, true );

										$rule_afrbp_prices = (array) unserialize( $rule_role_base_price );

										if ( empty( $rule_afrbp_prices['discount_value'] ) || empty( $rule_afrbp_prices['discount_type'] ) ) {
											continue;
										} else {
											break;
										}
									}

									if ( ! empty( $rule_cus_base_price ) ) {
										foreach ( $rule_cus_base_price as $rule_cus_price ) {

											if ( $user->ID == $rule_cus_price['customer_name'] ) {

												if ( '' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

													if ( '' != $rule_cus_price['min_qty'] || 0 != $rule_cus_price['min_qty'] ) {
														$min_qty = intval( $rule_cus_price['min_qty'] );
													} else {
														$min_qty = '';
													}

													if ( '' != $rule_cus_price['max_qty'] || 0 != $rule_cus_price['max_qty'] ) {
														$max_qty = intval( $rule_cus_price['max_qty'] );
													} else {
														$max_qty = '';
													}

													if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
														$csppdata      = false;
														$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
														$this->csp_wc_add_notice( $error_message );
														return $csppdata;

													} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

														$csppdata      = false;
														$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
														$this->csp_wc_add_notice( $error_message );
														return $csppdata;

													} else {
														return true;
													}
												}
											}
										}
									}

									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true && ! empty( $rule_afrbp_prices ) ) {

										// Product Price.
										if ( '' != $rule_afrbp_prices['discount_value'] || 0 != $rule_afrbp_prices['discount_value'] ) {

											if ( '' != $rule_afrbp_prices['min_qty'] || 0 != $rule_afrbp_prices['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices['max_qty'] || 0 != $rule_afrbp_prices['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} else {
												return true;
											}
										}
									}
								}
							}
						}
					}
				} else {

					// Variable Product.

					$targeted_id = $variation_id;

					foreach ( WC()->cart->get_cart() as $cart_item ) {
						if ( $cart_item['variation_id'] == $targeted_id ) {
							$oqty = $cart_item['quantity'];
							break; // stop the loop if product is found.
						}
					}
					// Displaying the quantity if targeted product is in cart.
					if ( ! empty( $oqty ) ) {

						$old_qty = $oqty;
					} else {
						$old_qty = 0;
					}

					// get customer specifc price.
					$cus_base_price = get_post_meta( $variation_id, '_cus_base_price', true );

					// get role base price.
					foreach ( $user->roles as $value ) {

						$role_base_price = get_post_meta( $variation_id, '_role_base_price_' . $value, true );

						$afrbp_prices = (array) unserialize( $role_base_price );

						if ( empty( $afrbp_prices['discount_value'] ) || empty( $afrbp_prices['discount_type'] ) ) {
							continue;
						} else {
							break;
						}
					}

					if ( ! empty( $cus_base_price ) ) {
						foreach ( $cus_base_price as $cus_price ) {

							if ( $user->ID == $cus_price['customer_name'] ) {

								if ( '' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

									if ( '' != $cus_price['min_qty'] || 0 != $cus_price['min_qty'] ) {
										$min_qty = intval( $cus_price['min_qty'] );
									} else {
										$min_qty = '';
									}

									if ( '' != $cus_price['max_qty'] || 0 != $cus_price['max_qty'] ) {
										$max_qty = intval( $cus_price['max_qty'] );
									} else {
										$max_qty = '';
									}

									if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
										$csppdata      = false;
										$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
										$this->csp_wc_add_notice( $error_message );
										return $csppdata;

									} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

										$csppdata      = false;
										$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
										$this->csp_wc_add_notice( $error_message );
										return $csppdata;

									} else {
										return true;
									}
								}
							}
						}
					}

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( true && 1 != count($afrbp_prices) ) {

						// Product Price.
						if ( '' != $afrbp_prices['discount_value'] || 0 != $afrbp_prices['discount_value'] ) {

							if ( '' != $afrbp_prices['min_qty'] || 0 != $afrbp_prices['min_qty'] ) {
								$min_qty = intval( $afrbp_prices['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices['max_qty'] || 0 != $afrbp_prices['max_qty'] ) {
								$max_qty = intval( $afrbp_prices['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} else {
								return true;
							}
						}
					}

					// Rules.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$product                 = wc_get_product($product_id);
								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									foreach ( $user->roles as $value ) {

										$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price_' . $value, true );

										$rule_afrbp_prices = (array) unserialize( $rule_role_base_price );

										if ( empty( $rule_afrbp_prices['discount_value'] ) || empty( $rule_afrbp_prices['discount_type'] ) ) {
											continue;
										} else {
											break;
										}
									}

									if ( ! empty( $rule_cus_base_price ) ) {
										foreach ( $rule_cus_base_price as $rule_cus_price ) {

											if ( $user->ID == $rule_cus_price['customer_name'] ) {

												if ( '' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

													if ( '' != $rule_cus_price['min_qty'] || 0 != $rule_cus_price['min_qty'] ) {
														$min_qty = intval( $rule_cus_price['min_qty'] );
													} else {
														$min_qty = '';
													}

													if ( '' != $rule_cus_price['max_qty'] || 0 != $rule_cus_price['max_qty'] ) {
														$max_qty = intval( $rule_cus_price['max_qty'] );
													} else {
														$max_qty = '';
													}

													if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
														$csppdata      = false;
														$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
														$this->csp_wc_add_notice( $error_message );
														return $csppdata;

													} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

														$csppdata      = false;
														$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
														$this->csp_wc_add_notice( $error_message );
														return $csppdata;

													} else {
														return true;
													}
												}
											}
										}
									}

									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true && ! empty( $rule_afrbp_prices ) ) {

										// Product Price.
										if ( '' != $rule_afrbp_prices['discount_value'] || 0 != $rule_afrbp_prices['discount_value'] ) {

											if ( '' != $rule_afrbp_prices['min_qty'] || 0 != $rule_afrbp_prices['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices['max_qty'] || 0 != $rule_afrbp_prices['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} else {
												return true;
											}
										}
									}
								}
							}
						}
					}
				}
			} else {

				// For Guest Users.

				if ( 0 == $variation_id ) {
					// Simple Product.

					$targeted_id = $product_id;

					foreach ( WC()->cart->get_cart() as $cart_item ) {
						if ( $cart_item['product_id'] == $targeted_id ) {
							$oqty = $cart_item['quantity'];
							break; // stop the loop if product is found.
						}
					}
					// Displaying the quantity if targeted product is in cart.
					if ( ! empty( $oqty ) ) {

						$old_qty = $oqty;
					} else {
						$old_qty = 0;
					}

					// get customer specifc price.
					$cus_base_price = get_post_meta( $product_id, '_cus_base_price', true );

					// get role base price.
					$role_base_price_guest = get_post_meta( $product_id, '_role_base_price_guest', true );
					$afrbp_prices_guest    = (array) unserialize( $role_base_price_guest );

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( 1 != count($afrbp_prices_guest) ) {

						// Product Price.
						if ( '' != $afrbp_prices_guest['discount_value'] || 0 != $afrbp_prices_guest['discount_value'] ) {

							if ( '' != $afrbp_prices_guest['min_qty'] || 0 != $afrbp_prices_guest['min_qty'] ) {
								$min_qty = intval( $afrbp_prices_guest['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices_guest['max_qty'] || 0 != $afrbp_prices_guest['max_qty'] ) {
								$max_qty = intval( $afrbp_prices_guest['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} else {
								return true;
							}
						}
					}

					// Rules - guest users.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$product                 = wc_get_product($product_id);
								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {
									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									$rule_role_base_price_guest = get_post_meta( $rule->ID, 'rrole_base_price_guest', true );
									$rule_afrbp_prices_guest    = (array) unserialize( $rule_role_base_price_guest );

									if (!isset($rule_afrbp_prices_guest['discount_value']) || empty($rule_afrbp_prices_guest['discount_value'])) {
										$rule_afrbp_prices_guest['discount_value'] = '';
									}

									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true ) {

										if ( '' != $rule_afrbp_prices_guest['discount_value'] || 0 != $rule_afrbp_prices_guest['discount_value'] ) {

											if ( '' != $rule_afrbp_prices_guest['min_qty'] || 0 != $rule_afrbp_prices_guest['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices_guest['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices_guest['max_qty'] || 0 != $rule_afrbp_prices_guest['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices_guest['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} else {
												return true;
											}
										}
									}
								}
							}
						}
					}
				} else {

					// Variable Product.

					$targeted_id = $variation_id;

					foreach ( WC()->cart->get_cart() as $cart_item ) {
						if ( $cart_item['variation_id'] == $targeted_id ) {
							$oqty = $cart_item['quantity'];
							break; // stop the loop if product is found.
						}
					}
					// Displaying the quantity if targeted product is in cart.
					if ( ! empty( $oqty ) ) {

						$old_qty = $oqty;
					} else {
						$old_qty = 0;
					}

					// get customer specifc price.
					$cus_base_price = get_post_meta( $variation_id, '_cus_base_price', true );

					// get role base price.
					$role_base_price_guest = get_post_meta( $variation_id, '_role_base_price_guest', true );
					$afrbp_prices_guest    = (array) unserialize( $role_base_price_guest );

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( true && 1 != count($afrbp_prices_guest) ) {

						// Product Price.
						if ( '' != $afrbp_prices_guest['discount_value'] || 0 != $afrbp_prices_guest['discount_value'] ) {

							if ( '' != $afrbp_prices_guest['min_qty'] || 0 != $afrbp_prices_guest['min_qty'] ) {
								$min_qty = intval( $afrbp_prices_guest['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices_guest['max_qty'] || 0 != $afrbp_prices_guest['max_qty'] ) {
								$max_qty = intval( $afrbp_prices_guest['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

								$csppdata      = false;
								$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
								$this->csp_wc_add_notice( $error_message );
								return $csppdata;

							} else {
								return true;
							}
						}
					}

					// Rules - guest users.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$product                 = wc_get_product($product_id);
								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									$rule_role_base_price_guest = get_post_meta( $rule->ID, 'rrole_base_price_guest', true );
									$rule_afrbp_prices_guest    = (array) unserialize( $rule_role_base_price_guest );

									if (!isset($rule_afrbp_prices_guest['discount_value']) || empty($rule_afrbp_prices_guest['discount_value'])) {
										$rule_afrbp_prices_guest['discount_value'] = '';
									}

									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true ) {

										if ( '' != $rule_afrbp_prices_guest['discount_value'] || 0 != $rule_afrbp_prices_guest['discount_value'] ) {

											if ( '' != $rule_afrbp_prices_guest['min_qty'] || 0 != $rule_afrbp_prices_guest['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices_guest['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices_guest['max_qty'] || 0 != $rule_afrbp_prices_guest['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices_guest['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( '' != $min_qty && $old_qty + $qty < $min_qty ) {
												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_min_qty_error_msg' ), $min_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} elseif ( '' != $max_qty && $old_qty + $qty > $max_qty ) {

												$csppdata      = false;
												$error_message = sprintf( get_option( 'csp_max_qty_error_msg' ), $max_qty );
												$this->csp_wc_add_notice( $error_message );
												return $csppdata;

											} else {
												return true;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			return $csppdata;
		}

		/**
		 * Main function start.
		 *
		 * @param init $passed .
		 *
		 * @param init $cart_item_key .
		 *
		 * @param init $values .
		 *
		 * @param init $qty .
		 */
		public function csp_update_cart_quantity_validation( $passed, $cart_item_key, $values, $qty ) {

			$user = wp_get_current_user();

			if ( is_user_logged_in() ) {

				if ( 0 == $values['variation_id'] ) {
					// Simple Product.
					// get customer specifc price.
					$pro            = wc_get_product( $values['product_id'] );
					$cus_base_price = get_post_meta( $values['product_id'], '_cus_base_price', true );
					$product        = wc_get_product( $values['product_id'] );
					// get role base price.
					foreach ( $user->roles as $value ) {

						$role_base_price = get_post_meta( $values['product_id'], '_role_base_price_' . $value, true );

						$afrbp_prices = (array) unserialize( $role_base_price );

						if ( empty( $afrbp_prices['discount_value'] ) || empty( $afrbp_prices['discount_type'] ) ) {
							continue;
						} else {
							break;
						}
					}

					if ( ! empty( $cus_base_price ) ) {
						foreach ( $cus_base_price as $cus_price ) {

							if ( $user->ID == $cus_price['customer_name'] ) {

								if ( '' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

									if ( '' != $cus_price['min_qty'] || 0 != $cus_price['min_qty'] ) {
										$min_qty = intval( $cus_price['min_qty'] );
									} else {
										$min_qty = '';
									}

									if ( '' != $cus_price['max_qty'] || 0 != $cus_price['max_qty'] ) {
										$max_qty = intval( $cus_price['max_qty'] );
									} else {
										$max_qty = '';
									}

									if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
										$passed        = false;
										$arr           = array(
											'%pro' => $pro->get_title(),
											'%min' => $min_qty,
											'%max' => $max_qty,
										);
										$word          = get_option( 'csp_update_cart_error_msg' );
										$error_message = strtr( $word, $arr );

										$this->csp_wc_add_notice( $error_message );
										return $passed;

									} else {
										return $passed;
									}
								}
							}
						}
					}

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( true && 1 != count($afrbp_prices)) {

						// Product Price.
						if ( '' != $afrbp_prices['discount_value'] || 0 != $afrbp_prices['discount_value'] ) {

							if ( '' != $afrbp_prices['min_qty'] || 0 != $afrbp_prices['min_qty'] ) {
								$min_qty = intval( $afrbp_prices['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices['max_qty'] || 0 != $afrbp_prices['max_qty'] ) {
								$max_qty = intval( $afrbp_prices['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
								$passed        = false;
								$arr           = array(
									'%pro' => $pro->get_title(),
									'%min' => $min_qty,
									'%max' => $max_qty,
								);
								$word          = get_option( 'csp_update_cart_error_msg' );
								$error_message = strtr( $word, $arr );

								$this->csp_wc_add_notice( $error_message );
								return $passed;

							} else {
								return $passed;
							}
						}
					}

					// Rules.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									foreach ( $user->roles as $value ) {

										$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price_' . $value, true );

										$rule_afrbp_prices = (array) unserialize( $rule_role_base_price );

										if ( empty( $rule_afrbp_prices['discount_value'] ) || empty( $rule_afrbp_prices['discount_type'] ) ) {
											continue;
										} else {
											break;
										}
									}

									if ( ! empty( $rule_cus_base_price ) ) {
										foreach ( $rule_cus_base_price as $rule_cus_price ) {

											if ( $user->ID == $rule_cus_price['customer_name'] ) {

												if ( '' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

													if ( '' != $rule_cus_price['min_qty'] || 0 != $rule_cus_price['min_qty'] ) {
														$min_qty = intval( $rule_cus_price['min_qty'] );
													} else {
														$min_qty = '';
													}

													if ( '' != $rule_cus_price['max_qty'] || 0 != $rule_cus_price['max_qty'] ) {
														$max_qty = intval( $rule_cus_price['max_qty'] );
													} else {
														$max_qty = '';
													}

													if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
														$passed        = false;
														$arr           = array(
															'%pro' => $pro->get_title(),
															'%min' => $min_qty,
															'%max' => $max_qty,
														);
														$word          = get_option( 'csp_update_cart_error_msg' );
														$error_message = strtr( $word, $arr );

														$this->csp_wc_add_notice( $error_message );
														return $passed;

													} else {
														return $passed;
													}
												}
											}
										}
									}

									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true && 1 != count($rule_afrbp_prices) ) {

										// Product Price.
										if ( '' != $rule_afrbp_prices['discount_value'] || 0 != $rule_afrbp_prices['discount_value'] ) {

											if ( '' != $rule_afrbp_prices['min_qty'] || 0 != $rule_afrbp_prices['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices['max_qty'] || 0 != $rule_afrbp_prices['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
												$passed        = false;
												$arr           = array(
													'%pro' => $pro->get_title(),
													'%min' => $min_qty,
													'%max' => $max_qty,
												);
												$word          = get_option( 'csp_update_cart_error_msg' );
												$error_message = strtr( $word, $arr );

												$this->csp_wc_add_notice( $error_message );
												return $passed;

											} else {
												return $passed;
											}
										}
									}
								}
							}
						}
					}

				} else {

					// Variable Product.
					// get customer specifc price.
					$pro            = wc_get_product( $values['variation_id'] );
					$cus_base_price = get_post_meta( $values['variation_id'], '_cus_base_price', true );
					$product        = wc_get_product( $values['variation_id'] );
					// get role base price.
					foreach ( $user->roles as $value ) {

						$role_base_price = get_post_meta( $values['variation_id'], '_role_base_price_' . $value, true );

						$afrbp_prices = (array) unserialize( $role_base_price );

						if ( empty( $afrbp_prices['discount_value'] ) || empty( $afrbp_prices['discount_type'] ) ) {
							continue;
						} else {
							break;
						}
					}

					if ( ! empty( $cus_base_price ) ) {
						foreach ( $cus_base_price as $cus_price ) {

							if ( $user->ID == $cus_price['customer_name'] ) {

								if ( '' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

									if ( '' != $cus_price['min_qty'] || 0 != $cus_price['min_qty'] ) {
										$min_qty = intval( $cus_price['min_qty'] );
									} else {
										$min_qty = '';
									}

									if ( '' != $cus_price['max_qty'] || 0 != $cus_price['max_qty'] ) {
										$max_qty = intval( $cus_price['max_qty'] );
									} else {
										$max_qty = '';
									}

									if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
										$passed        = false;
										$arr           = array(
											'%pro' => $pro->get_title(),
											'%min' => $min_qty,
											'%max' => $max_qty,
										);
										$word          = get_option( 'csp_update_cart_error_msg' );
										$error_message = strtr( $word, $arr );

										$this->csp_wc_add_notice( $error_message );
										return $passed;

									} else {
										return $passed;
									}
								}
							}
						}
					}

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( true && 1 != count($afrbp_prices) ) {

						// Product Price.
						if ( '' != $afrbp_prices['discount_value'] || 0 != $afrbp_prices['discount_value'] ) {

							if ( '' != $afrbp_prices['min_qty'] || 0 != $afrbp_prices['min_qty'] ) {
								$min_qty = intval( $afrbp_prices['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices['max_qty'] || 0 != $afrbp_prices['max_qty'] ) {
								$max_qty = intval( $afrbp_prices['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
								$passed        = false;
								$arr           = array(
									'%pro' => $pro->get_title(),
									'%min' => $min_qty,
									'%max' => $max_qty,
								);
								$word          = get_option( 'csp_update_cart_error_msg' );
								$error_message = strtr( $word, $arr );

								$this->csp_wc_add_notice( $error_message );
								return $passed;

							} else {
								return $passed;
							}
						}
					}

					// Rules.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									foreach ( $user->roles as $value ) {

										$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price_' . $value, true );

										$rule_afrbp_prices = (array) unserialize( $rule_role_base_price );

										if ( empty( $rule_afrbp_prices['discount_value'] ) || empty( $rule_afrbp_prices['discount_type'] ) ) {
											continue;
										} else {
											break;
										}
									}

									if ( ! empty( $rule_cus_base_price ) ) {
										foreach ( $rule_cus_base_price as $rule_cus_price ) {

											if ( $user->ID == $rule_cus_price['customer_name'] ) {

												if ( '' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

													if ( '' != $rule_cus_price['min_qty'] || 0 != $rule_cus_price['min_qty'] ) {
														$min_qty = intval( $rule_cus_price['min_qty'] );
													} else {
														$min_qty = '';
													}

													if ( '' != $rule_cus_price['max_qty'] || 0 != $rule_cus_price['max_qty'] ) {
														$max_qty = intval( $rule_cus_price['max_qty'] );
													} else {
														$max_qty = '';
													}

													if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
														$passed        = false;
														$arr           = array(
															'%pro' => $pro->get_title(),
															'%min' => $min_qty,
															'%max' => $max_qty,
														);
														$word          = get_option( 'csp_update_cart_error_msg' );
														$error_message = strtr( $word, $arr );

														$this->csp_wc_add_notice( $error_message );

													} else {
														return $passed;
													}
												}
											}
										}
									}

									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true && ! empty( $rule_afrbp_prices ) ) {

										// Product Price.
										if ( '' != $rule_afrbp_prices['discount_value'] || 0 != $rule_afrbp_prices['discount_value'] ) {

											if ( '' != $rule_afrbp_prices['min_qty'] || 0 != $rule_afrbp_prices['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices['max_qty'] || 0 != $rule_afrbp_prices['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
												$passed        = false;
												$arr           = array(
													'%pro' => $pro->get_title(),
													'%min' => $min_qty,
													'%max' => $max_qty,
												);
												$word          = get_option( 'csp_update_cart_error_msg' );
												$error_message = strtr( $word, $arr );

												$this->csp_wc_add_notice( $error_message );
												return $passed;

											} else {
												return $passed;
											}
										}
									}
								}
							}
						}
					}
				}

			} else {

				// For Guest Users.
				if ( 0 == $values['variation_id'] ) {
					// Simeple Products.
					// get customer specifc price.
					$pro            = wc_get_product( $values['product_id'] );
					$cus_base_price = get_post_meta( $values['product_id'], '_cus_base_price', true );
					$product        = wc_get_product( $values['product_id'] );
					// get role base price.
					$role_base_price_guest = get_post_meta( $values['product_id'], '_role_base_price_guest', true );
					$afrbp_prices_guest    = unserialize( $role_base_price_guest );

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if (  ! empty( $afrbp_prices_guest ) && !empty( $afrbp_prices_guest['discount_value'] ) ) {

						// Product Price.
						if ( '' != $afrbp_prices_guest['discount_value'] || 0 != $afrbp_prices_guest['discount_value'] ) {

							if ( '' != $afrbp_prices_guest['min_qty'] || 0 != $afrbp_prices_guest['min_qty'] ) {
								$min_qty = intval( $afrbp_prices_guest['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices_guest['max_qty'] || 0 != $afrbp_prices_guest['max_qty'] ) {
								$max_qty = intval( $afrbp_prices_guest['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
								$passed        = false;
								$arr           = array(
									'%pro' => $pro->get_title(),
									'%min' => $min_qty,
									'%max' => $max_qty,
								);
								$word          = get_option( 'csp_update_cart_error_msg' );
								$error_message = strtr( $word, $arr );

								$this->csp_wc_add_notice( $error_message );
								return $passed;

							} else {
								return $passed;
							}
						}
					}

					// Rule - Guest Users.

					// Rules - guest users.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									$rule_role_base_price_guest = get_post_meta( $rule->ID, 'rrole_base_price_guest', true );
									$rule_afrbp_prices_guest    = (array) unserialize( $rule_role_base_price_guest );

									if (!isset($rule_afrbp_prices_guest['discount_value']) || empty($rule_afrbp_prices_guest['discount_value'])) {
										$rule_afrbp_prices_guest['discount_value'] = '';
									}
									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true && ! empty( $rule_afrbp_prices_guest ) ) {

										// Product Price.
										if ( '' != $rule_afrbp_prices_guest['discount_value'] || 0 != $rule_afrbp_prices_guest['discount_value'] ) {

											if ( '' != $rule_afrbp_prices_guest['min_qty'] || 0 != $rule_afrbp_prices_guest['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices_guest['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices_guest['max_qty'] || 0 != $rule_afrbp_prices_guest['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices_guest['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
												$passed        = false;
												$arr           = array(
													'%pro' => $pro->get_title(),
													'%min' => $min_qty,
													'%max' => $max_qty,
												);
												$word          = get_option( 'csp_update_cart_error_msg' );
												$error_message = strtr( $word, $arr );

												$this->csp_wc_add_notice( $error_message );
												return $passed;

											} else {
												return $passed;
											}
										}
									}
								}
							}
						}
					}
				} else {
					// Variable Products.

					// Simeple Products.
					// get customer specifc price.
					$pro            = wc_get_product( $values['variation_id'] );
					$cus_base_price = get_post_meta( $values['variation_id'], '_cus_base_price', true );
					$product        = wc_get_product( $values['variation_id'] );
					// get role base price.
					$role_base_price_guest = get_post_meta( $values['variation_id'], '_role_base_price_guest', true );
					$afrbp_prices_guest    = (array) unserialize( $role_base_price_guest );

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if (  ! empty( $afrbp_prices_guest ) && !empty( $afrbp_prices_guest['discount_value'] ) ) {

						// Product Price.
						if ( '' != $afrbp_prices_guest['discount_value'] || 0 != $afrbp_prices_guest['discount_value'] ) {

							if ( '' != $afrbp_prices_guest['min_qty'] || 0 != $afrbp_prices_guest['min_qty'] ) {
								$min_qty = intval( $afrbp_prices_guest['min_qty'] );
							} else {
								$min_qty = '';
							}

							if ( '' != $afrbp_prices_guest['max_qty'] || 0 != $afrbp_prices_guest['max_qty'] ) {
								$max_qty = intval( $afrbp_prices_guest['max_qty'] );
							} else {
								$max_qty = '';
							}

							if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
								$passed        = false;
								$arr           = array(
									'%pro' => $pro->get_title(),
									'%min' => $min_qty,
									'%max' => $max_qty,
								);
								$word          = get_option( 'csp_update_cart_error_msg' );
								$error_message = strtr( $word, $arr );

								$this->csp_wc_add_notice( $error_message );
								return $passed;

							} else {
								return $passed;
							}
						}
					}

					// Rule - Guest Users.

					// Rules - guest users.
					if ( true ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() ) ) || ( has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

									// get role base price.
									$rule_role_base_price_guest = get_post_meta( $rule->ID, 'rrole_base_price_guest', true );
									$rule_afrbp_prices_guest    = (array) unserialize( $rule_role_base_price_guest );

									// Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( true ) {
										if (!isset($rule_afrbp_prices_guest['discount_value']) || empty($rule_afrbp_prices_guest['discount_value'])) {
											$rule_afrbp_prices_guest['discount_value'] = '';
										}
										// Product Price.
										if ( '' != $rule_afrbp_prices_guest['discount_value'] || 0 != $rule_afrbp_prices_guest['discount_value'] ) {

											if ( '' != $rule_afrbp_prices_guest['min_qty'] || 0 != $rule_afrbp_prices_guest['min_qty'] ) {
												$min_qty = intval( $rule_afrbp_prices_guest['min_qty'] );
											} else {
												$min_qty = '';
											}

											if ( '' != $rule_afrbp_prices_guest['max_qty'] || 0 != $rule_afrbp_prices_guest['max_qty'] ) {
												$max_qty = intval( $rule_afrbp_prices_guest['max_qty'] );
											} else {
												$max_qty = '';
											}

											if ( ( '' != $min_qty && $qty < $min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
												$passed        = false;
												$arr           = array(
													'%pro' => $pro->get_title(),
													'%min' => $min_qty,
													'%max' => $max_qty,
												);
												$word          = get_option( 'csp_update_cart_error_msg' );
												$error_message = strtr( $word, $arr );

												$this->csp_wc_add_notice( $error_message );
												return $passed;

											} else {
												return $passed;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			return $passed;
		}

		/**
		 * Main function start.
		 *
		 * @param init $string .
		 *
		 * @param init $type .
		 */
		public function csp_wc_add_notice( $string, $type = 'error' ) {

			wc()->initialize_session();
			wc()->session->set_customer_session_cookie(true);
			
			global $woocommerce;
			if ( version_compare( $woocommerce->version, 2.1, '>=' ) ) {
				wc_add_notice( $string, $type );
			} else {
				$woocommerce->add_error( $string );
			}
		}

	}

	new AF_C_S_P_Front();
}
