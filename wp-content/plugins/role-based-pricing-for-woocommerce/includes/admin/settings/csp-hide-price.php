<?php
/**
 * Main class start.
 *
 * @package : hplevel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // restict for direct access.
}
?>

<h1><?php echo esc_html__( 'Hide Price & Add to Cart', 'addify_role_price' ); ?></h1>

<div class="wrap">
	<form action="" method="post">
	<?php wp_nonce_field( 'afroleprice_nonce_action', 'afroleprice_nonce_field' ); ?>
	<div class="hide_price_divs">
		<div class="hide_div">

			<label><?php echo esc_html__( 'Enable Hide Price & Add to Cart', 'addify_role_price' ); ?></label>
			<input type="checkbox" name="csp_enable_hide_pirce" id="csp_enable_hide_pirce" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'csp_enable_hide_pirce' ) ) ); ?> />
		</div>

		<div id="hide_div">
			<div class="hide_div">
				<label><?php echo esc_html__( 'Hide for Guest Users', 'addify_role_price' ); ?></label>
				<input type="checkbox" name="csp_enable_hide_pirce_guest" id="csp_enable_hide_pirce_guest" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'csp_enable_hide_pirce_guest' ) ) ); ?> />
			</div>

			<div class="hide_div">
				<label><?php echo esc_html__( 'Hide for Registered Users', 'addify_role_price' ); ?></label>
				<input type="checkbox" name="csp_enable_hide_pirce_registered" id="csp_enable_hide_pirce_registered" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'csp_enable_hide_pirce_registered' ) ) ); ?> />
			</div>

			<div class="hide_div" id="userroles">
				<label><?php echo esc_html__( 'Select User Roles', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">

					<select class="select_box wc-enhanced-select sel2" name="csp_hide_user_role[]" id="csp_hide_user_role"  multiple='multiple'>

						<?php
						$afrole_hide_user_role = (array) unserialize( get_option( 'csp_hide_user_role' ) );

						global $wp_roles;
						$roles = $wp_roles->get_names();
						foreach ( $roles as $key => $value ) {
							?>
							<option value="<?php echo esc_attr( $key ); ?>"
								<?php
								if ( ! empty( $afrole_hide_user_role ) && in_array( $key, $afrole_hide_user_role ) ) {
									echo 'selected';
								}
								?>
							>
								<?php
								echo esc_attr( $value );
								?>
									
								</option>
						<?php } ?>

					</select>
					<p><?php echo esc_html__( 'Select User Roles for which users you want to hide price and add to cart on frontend. If no user role is selected then price and add to cart will not be hidden for registered users.', 'addify_role_price' ); ?></p>
				</div>
				
			</div>

			<div class="hide_div">
				<label><?php echo esc_html__( 'Hide Price', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">
					<input type="checkbox" name="csp_hide_price" id="csp_hide_price" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'csp_hide_price' ) ) ); ?> />
					<p><?php echo esc_html__( 'If this option is checked then Price is hidden on the archive and product pages. The price will not be hidden in cart. If you enable the "Add to Cart" Button. ', 'addify_role_price' ); ?></p>
				</div>
			</div>

			<div class="hide_div" id="hp_price">
				<label><?php echo esc_html__( 'Price Text', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">
					<input type="text" name="csp_price_text" id="csp_price_text" class="csp_hp_input_field" value="<?php echo esc_attr( get_option( 'csp_price_text' ) ); ?>" />
					<p><?php echo esc_html__( 'This text will be shown in place of price in archive and product pages.', 'addify_role_price' ); ?></p>
				</div>
			</div>

			<div class="hide_div">
				<label><?php echo esc_html__( 'Hide Add to Cart Button', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">
					<input type="checkbox" name="csp_hide_cart_button" id="csp_hide_cart_button" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'csp_hide_cart_button' ) ) ); ?> />
					<p><?php echo esc_html__( 'If this option is checked then Add To Cart button is hidden on the archive and product pages.', 'addify_role_price' ); ?></p>
				</div>
			</div>

			<div class="hide_div hp_cart">
				<label><?php echo esc_html__( 'Add to Cart Button Text', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">
					<input type="text" name="csp_cart_button_text" id="csp_cart_button_text" class="csp_hp_input_field" value="<?php echo esc_attr( get_option( 'csp_cart_button_text' ) ); ?>" />
					<p><?php echo esc_html__( 'This text will be shown in place of text of Add to Cart Button.', 'addify_role_price' ); ?></p>
				</div>
			</div>

			<div class="hide_div hp_cart">
				<label><?php echo esc_html__( 'Add to Cart Button Link', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">
					<input type="text" name="csp_cart_button_link" id="csp_cart_button_link" class="csp_hp_input_field" value="<?php echo esc_attr( get_option( 'csp_cart_button_link' ) ); ?>" />
					<p><?php echo esc_html__( 'This link will replace Add to Cart Button link.', 'addify_role_price' ); ?></p>
				</div>
			</div>


			<div class="hide_div">
				<label><?php echo esc_html__( 'Select Products', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">
					<select class="select_box wc-enhanced-select sel_pros" name="csp_hide_products[]" id="csp_hide_products"  multiple='multiple'>
						<?php
							$csp_hide_products = (array) unserialize( get_option( 'csp_hide_products' ) );

						if ( ! empty( $csp_hide_products ) ) {

							foreach ( $csp_hide_products as $pro ) {

								$prod_post = get_post( $pro );

								?>

									<option value="<?php echo intval( $pro ); ?>" selected="selected"><?php echo esc_attr( $prod_post->post_title ); ?></option>

								<?php
							}
						}
						?>
					</select>
					<p><?php echo esc_html__( 'Select Products for which you want to hide price and add to cart.', 'addify_role_price' ); ?></p>
				</div>
			</div>


			<div class="hide_div">
				<label><?php echo esc_html__( 'Select Categories', 'addify_role_price' ); ?></label>
				<div class="csp_hide_field">
					
					<div class="all_cats">
						<ul>
							<?php

							$pre_vals = (array) unserialize( get_option( 'cps_hide_categories' ) );

							$args = array(
								'taxonomy'   => 'product_cat',
								'hide_empty' => false,
								'parent'     => 0,
							);

							$product_cat = get_terms( $args );
							foreach ( $product_cat as $parent_product_cat ) {
								?>
								<li class="par_cat">
									<input type="checkbox" class="parent" name="cps_hide_categories[]" id="cps_hide_categories" value="<?php echo intval( $parent_product_cat->term_id ); ?>" 
									<?php
									if ( ! empty( $pre_vals ) && in_array( $parent_product_cat->term_id, $pre_vals ) ) {
										echo 'checked';
									}
									?>
									/>
									<?php echo esc_attr( $parent_product_cat->name ); ?>

									<?php
									$child_args         = array(
										'taxonomy'   => 'product_cat',
										'hide_empty' => false,
										'parent'     => intval( $parent_product_cat->term_id ),
									);
									$child_product_cats = get_terms( $child_args );
									if ( ! empty( $child_product_cats ) ) {
										?>
										<ul>
											<?php foreach ( $child_product_cats as $child_product_cat ) { ?>
												<li class="child_cat">
													<input type="checkbox" class="child parent" name="cps_hide_categories[]" id="cps_hide_categories" value="<?php echo intval( $child_product_cat->term_id ); ?>" 
													<?php
													if ( ! empty( $pre_vals ) && in_array( $child_product_cat->term_id, $pre_vals ) ) {
														echo 'checked';
													}
													?>
													/>
													<?php echo esc_attr( $child_product_cat->name ); ?>

													<?php
													// 2nd level
													$child_args2 = array(
														'taxonomy' => 'product_cat',
														'hide_empty' => false,
														'parent'   => intval( $child_product_cat->term_id ),
													);

													$child_product_cats2 = get_terms( $child_args2 );
													if ( ! empty( $child_product_cats2 ) ) {
														?>

														<ul>
															<?php foreach ( $child_product_cats2 as $child_product_cat2 ) { ?>

																<li class="child_cat">
																	<input type="checkbox" class="child parent" name="cps_hide_categories[]" id="cps_hide_categories" value="<?php echo intval( $child_product_cat2->term_id ); ?>" 
																	<?php
																	if ( ! empty( $pre_vals ) && in_array( $child_product_cat2->term_id, $pre_vals ) ) {
																		echo 'checked';
																	}
																	?>
																	/>
																	<?php echo esc_attr( $child_product_cat2->name ); ?>


																	<?php
																	// 3rd level
																	$child_args3 = array(
																		'taxonomy' => 'product_cat',
																		'hide_empty' => false,
																		'parent'   => intval( $child_product_cat2->term_id ),
																	);

																	$child_product_cats3 = get_terms( $child_args3 );
																	if ( ! empty( $child_product_cats3 ) ) {
																		?>

																		<ul>
																			<?php foreach ( $child_product_cats3 as $child_product_cat3 ) { ?>

																				<li class="child_cat">
																					<input type="checkbox" class="child parent" name="cps_hide_categories[]" id="cps_hide_categories" value="<?php echo intval( $child_product_cat3->term_id ); ?>" 
																					<?php
																					if ( ! empty( $pre_vals ) && in_array( $child_product_cat3->term_id, $pre_vals ) ) {
																						echo 'checked';
																					}
																					?>
																					/>
																					<?php echo esc_attr( $child_product_cat3->name ); ?>


																					<?php
																					// 4th level
																					$child_args4 = array(
																						'taxonomy' => 'product_cat',
																						'hide_empty' => false,
																						'parent'   => intval( $child_product_cat3->term_id ),
																					);

																					$child_product_cats4 = get_terms( $child_args4 );
																					if ( ! empty( $child_product_cats4 ) ) {
																						?>

																						<ul>
																							<?php foreach ( $child_product_cats4 as $child_product_cat4 ) { ?>

																								<li class="child_cat">
																									<input type="checkbox" class="child parent" name="cps_hide_categories[]" id="cps_hide_categories" value="<?php echo intval( $child_product_cat4->term_id ); ?>"
																									<?php
																									if ( ! empty( $pre_vals ) && in_array( $child_product_cat4->term_id, $pre_vals ) ) {
																										echo 'checked';
																									}
																									?>
																									/>
																									<?php echo esc_attr( $child_product_cat4->name ); ?>


																									<?php
																									// 5th level
																									$child_args5 = array(
																										'taxonomy' => 'product_cat',
																										'hide_empty' => false,
																										'parent'   => intval( $child_product_cat4->term_id ),
																									);

																									$child_product_cats5 = get_terms( $child_args5 );
																									if ( ! empty( $child_product_cats5 ) ) {
																										?>

																										<ul>
																											<?php foreach ( $child_product_cats5 as $child_product_cat5 ) { ?>

																												<li class="child_cat">
																													<input type="checkbox" class="child parent" name="cps_hide_categories[]" id="cps_hide_categories" value="<?php echo intval( $child_product_cat5->term_id ); ?>" 
																													<?php
																													if ( ! empty( $pre_vals ) && in_array( $child_product_cat5->term_id, $pre_vals ) ) {
																														echo 'checked';
																													}
																													?>
																													/>
																													<?php echo esc_attr( $child_product_cat5->name ); ?>


																													<?php
																													// 6th level
																													$child_args6 = array(
																														'taxonomy' => 'product_cat',
																														'hide_empty' => false,
																														'parent'   => intval( $child_product_cat5->term_id ),
																													);

																													$child_product_cats6 = get_terms( $child_args6 );
																													if ( ! empty( $child_product_cats6 ) ) {
																														?>

																														<ul>
																															<?php foreach ( $child_product_cats6 as $child_product_cat6 ) { ?>

																																<li class="child_cat">
																																	<input type="checkbox" class="child" name="cps_hide_categories[]" id="cps_hide_categories" value="<?php echo intval( $child_product_cat6->term_id ); ?>" 
																																	<?php
																																	if ( ! empty( $pre_vals ) && in_array( $child_product_cat6->term_id, $pre_vals ) ) {
																																		echo 'checked';
																																	}
																																	?>
																																	/>
																																	<?php echo esc_attr( $child_product_cat6->name ); ?>
																																</li>

																															<?php } ?>
																														</ul>

																													<?php } ?>

																												</li>

																											<?php } ?>
																										</ul>

																									<?php } ?>


																								</li>

																							<?php } ?>
																						</ul>

																					<?php } ?>


																				</li>

																			<?php } ?>
																		</ul>

																	<?php } ?>

																</li>

															<?php } ?>
														</ul>

													<?php } ?>

												</li>
											<?php } ?>
										</ul>
									<?php } ?>

								</li>
								<?php
							}
							?>
						</ul>
					</div>
					<p><?php echo esc_html__( 'Select Categories for which Products you want to hide price and add to cart.', 'addify_role_price' ); ?></p>
				</div>
			</div>





		
		</div>

		<p><?php submit_button( esc_html__( 'Save Settings', 'addify_role_price' ), 'primary', 'afrole_save_hide_price' ); ?></p>

		
	</div>


</form>
	
</div>
