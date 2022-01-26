<?php
/**
 * Main class start.
 *
 * @package : rlevel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="csp_admin_main">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__( 'Rule Priority', 'addify_role_price' ); ?></strong></label></div>
	<div class="csp_admin_main_right">
		<input type="number" name="csp_rule_priority" class="rule_input" min="0" max="100" placeholder="0" value="<?php echo esc_attr( $post->menu_order ); ?>" />
		<input type="hidden" name="csp_rules" class="rule_input" value="yes" />
		<p class="csp_msg"><?php echo esc_html__( 'Provide number between 0 and 100, If more than one rules are applied on same item then rule with high priority will be applied. 1 is high and 100 is low.', 'addify_role_price' ); ?></p>
	</div>
</div>

<div class="csp_admin_main">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__( 'Apply on All Products', 'addify_role_price' ); ?></strong></label></div>
	<div class="csp_admin_main_right">
		<?php
			$applied_on_all_products = get_post_meta( $post->ID, 'csp_apply_on_all_products', true );
		?>
		<input type="checkbox" name="csp_apply_on_all_products" id="csp_apply_on_all_products" value="yes" <?php echo checked( 'yes', $applied_on_all_products ); ?>>
		<p class="csp_msg"><?php echo esc_html__( 'Check this if you want to apply this rule on all products.', 'addify_role_price' ); ?></p>
	</div>
</div>

<div class="csp_admin_main hide_all_pro">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__( 'Select Products', 'addify_role_price' ); ?></strong></label></div>
	<div class="csp_admin_main_right">
		<?php
			$applied_on = get_post_meta( $post->ID, 'csp_applied_on_products', true );
		?>
		<select name="csp_applied_on_products[]" id="csp_applied_on_products" class="applied_on_products sel_pros" multiple="multiple" style="width:100%">

			<?php

			if ( ! empty( $applied_on ) ) {

				foreach ( $applied_on as $pro ) {

					$prod_post = get_post( $pro );

					?>

						<option value="<?php echo intval( $pro ); ?>" selected="selected"><?php echo esc_attr( $prod_post->post_title ); ?></option>

					<?php
				}
			}
			?>
			
		</select>

		

	</div>
</div>

<div class="csp_admin_main hide_all_pro">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__( 'Select Categories', 'addify_role_price' ); ?></strong></label></div>
	<div class="csp_admin_main_right">
			
		<div class="all_cats">
			<ul>

				<?php


				if ( ! empty( $csp_applied_on_categories ) ) {

					$pre_vals = $csp_applied_on_categories;


				}


					$args = array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => false,
						'parent'     => 0,
					);

					$product_cat = get_terms( $args );
					foreach ( $product_cat as $parent_product_cat ) {
						?>
						  <li class="par_cat">
							  <input type="checkbox" class="parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval( $parent_product_cat->term_id ); ?>" 
																																						<?php
																																						if ( ! empty( $pre_vals ) && in_array( $parent_product_cat->term_id, $pre_vals ) ) {
																																							echo 'checked'; }
																																						?>
								 />
							<?php echo esc_attr( $parent_product_cat->name ); ?>

							<?php
								$child_args         = array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => false,
									'parent'     => $parent_product_cat->term_id,
								);
								$child_product_cats = get_terms( $child_args );
								if ( ! empty( $child_product_cats ) ) {
									?>
									  <ul>
										<?php foreach ( $child_product_cats as $child_product_cat ) { ?>
											  <li class="child_cat">
												  <input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval( $child_product_cat->term_id ); ?>" 
																																												  <?php
																																													if ( ! empty( $pre_vals ) && in_array( $child_product_cat->term_id, $pre_vals ) ) {
																																														echo 'checked'; }
																																													?>
													 />
												<?php echo esc_attr( $child_product_cat->name ); ?>


												<?php
													// 2nd level
													$child_args2 = array(
														'taxonomy' => 'product_cat',
														'hide_empty' => false,
														'parent'   => $child_product_cat->term_id,
													);

													$child_product_cats2 = get_terms( $child_args2 );
													if ( ! empty( $child_product_cats2 ) ) {
														?>

													  <ul>
														<?php foreach ( $child_product_cats2 as $child_product_cat2 ) { ?>

															  <li class="child_cat">
																  <input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval( $child_product_cat2->term_id ); ?>" 
																																																  <?php
																																																	if ( ! empty( $pre_vals ) && in_array( $child_product_cat2->term_id, $pre_vals ) ) {
																																																		echo 'checked'; }
																																																	?>
																	 />
																<?php echo esc_attr( $child_product_cat2->name ); ?>


																<?php
																	// 3rd level
																	$child_args3 = array(
																		'taxonomy' => 'product_cat',
																		'hide_empty' => false,
																		'parent'   => $child_product_cat2->term_id,
																	);

																	$child_product_cats3 = get_terms( $child_args3 );
																	if ( ! empty( $child_product_cats3 ) ) {
																		?>

																	  <ul>
																		<?php foreach ( $child_product_cats3 as $child_product_cat3 ) { ?>

																			  <li class="child_cat">
																				  <input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval( $child_product_cat3->term_id ); ?>" 
																																																				  <?php
																																																					if ( ! empty( $pre_vals ) && in_array( $child_product_cat3->term_id, $pre_vals ) ) {
																																																						echo 'checked'; }
																																																					?>
																					 />
																				<?php echo esc_attr( $child_product_cat3->name ); ?>


																				<?php
																					// 4th level
																					$child_args4 = array(
																						'taxonomy' => 'product_cat',
																						'hide_empty' => false,
																						'parent'   => $child_product_cat3->term_id,
																					);

																					$child_product_cats4 = get_terms( $child_args4 );
																					if ( ! empty( $child_product_cats4 ) ) {
																						?>

																					  <ul>
																						<?php foreach ( $child_product_cats4 as $child_product_cat4 ) { ?>

																							  <li class="child_cat">
																								  <input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval( $child_product_cat4->term_id ); ?>" 
																																																								  <?php
																																																									if ( ! empty( $pre_vals ) && in_array( $child_product_cat4->term_id, $pre_vals ) ) {
																																																										echo 'checked'; }
																																																									?>
																									 />
																								<?php echo esc_attr( $child_product_cat4->name ); ?>


																								<?php
																									// 5th level
																									$child_args5 = array(
																										'taxonomy' => 'product_cat',
																										'hide_empty' => false,
																										'parent'   => $child_product_cat4->term_id,
																									);

																									$child_product_cats5 = get_terms( $child_args5 );
																									if ( ! empty( $child_product_cats5 ) ) {
																										?>

																									  <ul>
																										<?php foreach ( $child_product_cats5 as $child_product_cat5 ) { ?>

																											  <li class="child_cat">
																												  <input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval( $child_product_cat5->term_id ); ?>" 
																																																												  <?php
																																																													if ( ! empty( $pre_vals ) && in_array( $child_product_cat5->term_id, $pre_vals ) ) {
																																																														echo 'checked'; }
																																																													?>
																													 />
																												<?php echo esc_attr( $child_product_cat5->name ); ?>


																												<?php
																													// 6th level
																													$child_args6 = array(
																														'taxonomy' => 'product_cat',
																														'hide_empty' => false,
																														'parent'   => $child_product_cat5->term_id,
																													);

																													$child_product_cats6 = get_terms( $child_args6 );
																													if ( ! empty( $child_product_cats6 ) ) {
																														?>

																													  <ul>
																														<?php foreach ( $child_product_cats6 as $child_product_cat6 ) { ?>

																															  <li class="child_cat">
																																  <input type="checkbox" class="child" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval( $child_product_cat6->term_id ); ?>" 
																																																														   <?php
																																																															if ( ! empty( $pre_vals ) && in_array( $child_product_cat6->term_id, $pre_vals ) ) {
																																																																echo 'checked'; }
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
	</div>

</div>


<div class="csp_admin_main">

	
	<h3><?php echo esc_html__( 'Role Based Pricing(By Customers)', 'addify_role_price' ); ?></h3>
	<p><?php echo esc_html__( 'If more than one rule is applied on same customer then rule that is added last will be applied.', 'addify_role_price' ); ?></p>
	  <div class="cdiv">
		<table cellspacing="0" cellpadding="0" border="1" width="100%">
			<thead>
				<tr>
					<th align="center" class="rcname"><?php echo esc_html__( 'Customer', 'addify_role_price' ); ?></th>
					<th align="center" class="rcname1"><?php echo esc_html__( 'Adjustment Type', 'addify_role_price' ); ?></th>
					<th align="center" class="rcname2"><?php echo esc_html__( 'Value', 'addify_role_price' ); ?></th>
					<th align="center" class="rcname3"><?php echo esc_html__( 'Min Qty', 'addify_role_price' ); ?></th>
					<th align="center" class="rcname4"><?php echo esc_html__( 'Max Qty', 'addify_role_price' ); ?></th>
					<th align="center" class="rcname5"><?php echo esc_html__( 'Replace?', 'addify_role_price' ); ?>
						<div class="tooltip">?
						  <span class="tooltiptext"><?php echo esc_html__( 'Replace Orignal Price? This will only work for Fixed Price, Fixed Decrease and Percentage Decrease.', 'addify_role_price' ); ?></span>
						</div>
					</th>
					<th align="center" class="rcname6"><?php echo esc_html__( 'X', 'addify_role_price' ); ?></th>
				</tr>
			</thead>

			<tbody>
			<?php

			$a = 1;
			if ( ! empty( $rcus_base_price ) ) {
				foreach ( $rcus_base_price as $cus_price ) {

					if ( ! empty( $cus_price['replace_orignal_price'] ) ) {

							$replace_orignal_price = 'yes';
					} else {
						$replace_orignal_price = 'no';
					}

					?>

				<tr id="filter-row-rule<?php echo intval( $post->ID ); ?><?php echo intval( $a ); ?>">
					<td align="center" class="rcname rheight">
						<select class="sel22" name="rcus_base_price[<?php echo intval( $a ); ?>][customer_name]">

							<?php
								$author_obj = get_user_by( 'id', $cus_price['customer_name'] );
							?>
							<option value="<?php echo intval( $cus_price['customer_name'] ); ?>" selected="selected"><?php echo esc_attr( $author_obj->display_name ); ?>(<?php echo esc_attr( $author_obj->user_email ); ?>)</option>
							
						</select>
					</td>

					<?php
					if ( isset( $cus_price['discount_type'] ) ) {
						$cus_price_dt = $cus_price['discount_type'];
					} else {
						$cus_price_dt = '';
					}

					if ( isset( $cus_price['discount_type'] ) ) {
						$cus_price_dt = $cus_price['discount_type'];
					} else {
						$cus_price_dt = '';
					}

					if ( isset( $cus_price['discount_value'] ) ) {
						$cus_price_dv = $cus_price['discount_value'];
					} else {
						$cus_price_dv = '';
					}

					if ( isset( $cus_price['min_qty'] ) ) {
						$cus_price_miq = $cus_price['min_qty'];
					} else {
						$cus_price_miq = '';
					}

					if ( isset( $cus_price['max_qty'] ) ) {
						$cus_price_maq = $cus_price['max_qty'];
					} else {
						$cus_price_maq = '';
					}




					?>
					<td align="center" class="rcname1 rheight">
						<select name="rcus_base_price[<?php echo intval( $a ); ?>][discount_type]">

							 <option value="fixed_price" <?php echo selected( 'fixed_price', $cus_price_dt ); ?>><?php echo esc_html__( 'Fixed Price', 'addify_role_price' ); ?></option>
							<option value="fixed_increase" <?php echo selected( 'fixed_increase', $cus_price_dt ); ?>><?php echo esc_html__( 'Fixed Increase', 'addify_role_price' ); ?></option>
							<option value="fixed_decrease" <?php echo selected( 'fixed_decrease', $cus_price_dt ); ?>><?php echo esc_html__( 'Fixed Decrease', 'addify_role_price' ); ?></option>
							<option value="percentage_decrease" <?php echo selected( 'percentage_decrease', $cus_price_dt ); ?>><?php echo esc_html__( 'Percentage Decrease', 'addify_role_price' ); ?></option>
							<option value="percentage_increase" <?php echo selected( 'percentage_increase', $cus_price_dt ); ?>><?php echo esc_html__( 'Percentage Increase', 'addify_role_price' ); ?></option>

						 </select>
					</td>
					<td align="center" class="rcname2 rheight">
						<input type="text" name="rcus_base_price[<?php echo intval( $a ); ?>][discount_value]" value = "<?php echo esc_attr( $cus_price_dv ); ?>" />
					</td>
					<td align="center" class="rcname3 rheight">
						<input type="number" min="0" name="rcus_base_price[<?php echo intval( $a ); ?>][min_qty]" value="<?php echo esc_attr( $cus_price_miq ); ?>" />
					</td>
					<td align="center" class="rcname4 rheight">
						<input type="number" min="0" name="rcus_base_price[<?php echo intval( $a ); ?>][max_qty]" value="<?php echo esc_attr( $cus_price_maq ); ?>" />
					</td>

					<td align="center" class="rcname5 rheight">
						<input type="checkbox" name="rcus_base_price[<?php echo intval( $a ); ?>][replace_orignal_price]" value="yes" <?php echo checked( 'yes', $replace_orignal_price ); ?> />
					</td>
					
					<td align="center" class="rcname6 rheight">
						<a onclick="jQuery('#filter-row-rule<?php echo intval( $post->ID ); ?><?php echo intval( $a ); ?>').remove();" class="button button-danger button-large">X</a>
					</td>
				</tr>

					<?php
					$a++;}
			}
			?>
				
			</tbody>

			<tfoot>
				<tr class="topfilters" id="beforerulectf"></tr>
			</tfoot>
		</table>

		<div class="add_rule_bt_div">
			<input type="button" class="btt2 button button-primary button-large" value="<?php echo esc_html__( 'Add Rule', 'addify_role_price' ); ?>" onClick="addGlobalRule();">
		</div>

	  </div>

</div>

<div class="csp_admin_main">
	

	<div class="options_group">
		
		<h3><?php echo esc_html__( 'Role Based Pricing(By User Roles)', 'addify_role_price' ); ?></h3>
		<div class="afrbp_div">
			
			<table class="addify-table-optoin" width="100%">
				<thead>
					<tr>
						<td class="afrpb_head_first"><?php echo esc_html__( 'User Role', 'addify_role_price' ); ?></td>
						<td class="afrpb_head"><?php echo esc_html__( 'Adjustment Type', 'addify_role_price' ); ?></td>
						<td class="afrpb_head"><?php echo esc_html__( 'Value', 'addify_role_price' ); ?></td>
						<td class="afrpb_head"><?php echo esc_html__( 'Min Qty', 'addify_role_price' ); ?></td>
						<td class="afrpb_head"><?php echo esc_html__( 'Max Qty', 'addify_role_price' ); ?></td>
						<td class="afrpb_head"><?php echo esc_html__( 'Replace Orignal Price?', 'addify_role_price' ); ?>
							<div class="tooltip">?
							  <span class="tooltiptext"><?php echo esc_html__( 'This will only work for Fixed Price, Fixed Decrease and Percentage Decrease.', 'addify_role_price' ); ?></span>
							</div>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
				</thead>
				<tbody>
					<?php
						global $wp_roles;
						$roles = $wp_roles->get_names();
					foreach ( $roles as $key => $value ) {

						if ( isset( $_GET['post'] ) && '' != $_GET['post'] ) {
							$p_id = intval( $_GET['post'] );
						} else {
							$p_id = '';
						}

						$rrole_base_price = get_post_meta( $p_id, 'rrole_base_price_' . $key, true );
						$afrbp_prices     = unserialize( $rrole_base_price );

						if ( is_bool( $afrbp_prices ) ) {
							$afrbp_prices                   = array();
							$afrbp_prices['discount_type']  = '';
							$afrbp_prices['discount_value'] = '';
							$afrbp_prices['min_qty']        = '';
							$afrbp_prices['max_qty']        = '';
						}
						if ( ! empty( $afrbp_prices['replace_orignal_price'] ) ) {

							$replace_orignal_price = 'yes';
						} else {
							$replace_orignal_price = 'no';
						}


						?>
							<tr>
								<td><b><?php echo esc_attr( $value ); ?></b></td>
								<td>
									<select name="rrole_base_price[<?php echo esc_attr( $key ); ?>][discount_type]">
										<option value=""><?php echo esc_html__( '---Select Adjustment Type---', 'addify_role_price' ); ?></option>
										<option value="fixed_price" <?php echo selected( 'fixed_price', $afrbp_prices['discount_type'] ); ?>><?php echo esc_html__( 'Fixed Price', 'addify_role_price' ); ?></option>
										<option value="fixed_increase" <?php echo selected( 'fixed_increase', $afrbp_prices['discount_type'] ); ?>><?php echo esc_html__( 'Fixed Increase', 'addify_role_price' ); ?></option>
										<option value="fixed_decrease" <?php echo selected( 'fixed_decrease', $afrbp_prices['discount_type'] ); ?>><?php echo esc_html__( 'Fixed Decrease', 'addify_role_price' ); ?></option>
										<option value="percentage_decrease" <?php echo selected( 'percentage_decrease', $afrbp_prices['discount_type'] ); ?>><?php echo esc_html__( 'Percentage Decrease', 'addify_role_price' ); ?></option>
										<option value="percentage_increase" <?php echo selected( 'percentage_increase', $afrbp_prices['discount_type'] ); ?>><?php echo esc_html__( 'Percentage Increase', 'addify_role_price' ); ?></option>
									</select>
								</td>
								<td><input class="afrbp_inp_field" type="text" name="rrole_base_price[<?php echo esc_attr( $key ); ?>][discount_value]" value="<?php echo esc_attr( $afrbp_prices['discount_value'] ); ?>" /></td>
								<td><input class="afrbp_num_field" type="number" name="rrole_base_price[<?php echo esc_attr( $key ); ?>][min_qty]" value="<?php echo esc_attr( $afrbp_prices['min_qty'] ); ?>" /></td>
								<td><input class="afrbp_num_field" type="number" name="rrole_base_price[<?php echo esc_attr( $key ); ?>][max_qty]" value="<?php echo esc_attr( $afrbp_prices['max_qty'] ); ?>" /></td>
								<td align="center"><input class="" type="checkbox" name="rrole_base_price[<?php echo esc_attr( $key ); ?>][replace_orignal_price]" value="yes" <?php echo checked( 'yes', $replace_orignal_price ); ?> /></td>
								
							</tr>
							<tr><td>&nbsp;</td></tr>
					<?php } ?>

					<tr>
						<?php

							$rrole_base_price_guest = get_post_meta( $p_id, 'rrole_base_price_guest', true );
							$afrbp_prices_guest     = (array) unserialize( $rrole_base_price_guest );

						if ( ! empty( $afrbp_prices_guest['replace_orignal_price'] ) ) {

							$replace_orignal_price_guest = 'yes';
						} else {
							$replace_orignal_price_guest = 'no';
						}

						if ( empty( $afrbp_prices_guest['discount_type'] ) ) {
							$afrbp_prices_guest['discount_type'] = '';
						}

						if ( empty( $afrbp_prices_guest['discount_value'] ) ) {
							$afrbp_prices_guest['discount_value'] = '';
						}

						if ( empty( $afrbp_prices_guest['min_qty'] ) ) {
							$afrbp_prices_guest['min_qty'] = '';
						}

						if ( empty( $afrbp_prices_guest['max_qty'] ) ) {
							$afrbp_prices_guest['max_qty'] = '';
						}

						?>
						<td><b><?php echo esc_html__( 'Non LoggedIn/Guest', 'addify_role_price' ); ?></b></td>
						<td>
							<select name="rrole_base_price[guest][discount_type]">
								<option value=""><?php echo esc_html__( '---Select Adjustment Type---', 'addify_role_price' ); ?></option>
								<option value="fixed_price" <?php echo selected( 'fixed_price', $afrbp_prices_guest['discount_type'] ); ?>><?php echo esc_html__( 'Fixed Price', 'addify_role_price' ); ?></option>
								<option value="fixed_increase" <?php echo selected( 'fixed_increase', $afrbp_prices_guest['discount_type'] ); ?>><?php echo esc_html__( 'Fixed Increase', 'addify_role_price' ); ?></option>
								<option value="fixed_decrease" <?php echo selected( 'fixed_decrease', $afrbp_prices_guest['discount_type'] ); ?>><?php echo esc_html__( 'Fixed Decrease', 'addify_role_price' ); ?></option>
								<option value="percentage_decrease" <?php echo selected( 'percentage_decrease', $afrbp_prices_guest['discount_type'] ); ?>><?php echo esc_html__( 'Percentage Decrease', 'addify_role_price' ); ?></option>
								<option value="percentage_increase" <?php echo selected( 'percentage_increase', $afrbp_prices_guest['discount_type'] ); ?>><?php echo esc_html__( 'Percentage Increase', 'addify_role_price' ); ?></option>
							</select>
						</td>
						<td><input class="afrbp_inp_field" type="text" name="rrole_base_price[guest][discount_value]" value="<?php echo esc_attr( $afrbp_prices_guest['discount_value'] ); ?>" /></td>
						<td><input class="afrbp_num_field" type="number" name="rrole_base_price[guest][min_qty]" value="<?php echo esc_attr( $afrbp_prices_guest['min_qty'] ); ?>" /></td>
						<td><input class="afrbp_num_field" type="number" name="rrole_base_price[guest][max_qty]" value="<?php echo esc_attr( $afrbp_prices_guest['max_qty'] ); ?>" /></td>
						<td align="center"><input class="" type="checkbox" name="rrole_base_price[guest][replace_orignal_price]" value="yes" <?php echo checked( 'yes', $replace_orignal_price_guest ); ?> /></td>
					</tr>

				</tbody>
			</table>
		</div>

	</div>

</div>


<script type="text/javascript" defer>
	var filter_row_rule = 10000;

	function addGlobalRule() {

		var aa = jQuery('.sel2').val();


		html  = '<tr id="filter-row-rule' + filter_row_rule + '">';

			html += '<td align="center" class="rcname rheight">';

				 html += '<select class="sel2" name="rcus_base_price[' + filter_row_rule + '][customer_name]">';

					

				 html += '</select>';

			 html += '</td>';

			 html += '<td align="center" class="rcname1 rheight">';

				 html += '<select name="rcus_base_price[' + filter_row_rule + '][discount_type]">';

					html += '<option value="fixed_price"><?php echo esc_html__( 'Fixed Price', 'addify_role_price' ); ?></option>';
					html += '<option value="fixed_increase"><?php echo esc_html__( 'Fixed Increase', 'addify_role_price' ); ?></option>';
					html += '<option value="fixed_decrease"><?php echo esc_html__( 'Fixed Decrease', 'addify_role_price' ); ?></option>';
					html += '<option value="percentage_decrease"><?php echo esc_html__( 'Percentage Decrease', 'addify_role_price' ); ?></option>';
					html += '<option value="percentage_increase"><?php echo esc_html__( 'Percentage Increase', 'addify_role_price' ); ?></option>';

				 html += '</select>';

			 html += '</td>';

			 html += '<td align="center" class="rcname2 rheight">';

				 html += '<input class="csp_input" type="text" name="rcus_base_price[' + filter_row_rule + '][discount_value]">';

			 html += '</td>';

			 html += '<td align="center" class="rcname3 rheight">';

				 html += '<input class="csp_input" type="number" min="0" value="0" name="rcus_base_price[' + filter_row_rule + '][min_qty]">';

			 html += '</td>';

			 html += '<td class="rcname4 rheight">';

				 html += '<input class="csp_input" align="center" type="number" min="0" value="0" name="rcus_base_price[' + filter_row_rule + '][max_qty]">';

			 html += '</td>';

			 html += '<td class="rcname5 rheight" align="center">';

				 html += '<input class="" align="center" type="checkbox" value="yes" name="rcus_base_price[' + filter_row_rule + '][replace_orignal_price]">';

			 html += '</td>';


			 html += '<td align="center" class="rcname6 rheight">';

				 html += '<a onclick="jQuery(\'#filter-row-rule' + filter_row_rule + '\').remove();" class="button button-danger"><?php esc_html_e( 'X', 'addify_role_price' ); ?></a>';

			 html += '</td>';

		html  += '</tr>';

		jQuery('#beforerulectf').before(html);

		var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		var nonce   = '<?php echo esc_attr( wp_create_nonce( 'afrolebase-ajax-nonce' ) ); ?>';

		jQuery('.sel2').select2({

			ajax: {
				url: ajaxurl, // AJAX URL is predefined in WordPress admin
				dataType: 'json',
				type: 'POST',
				delay: 250, // delay in ms while typing when to perform a AJAX search
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'cspsearchUsers', // AJAX action for admin-ajax.php
						nonce: nonce // AJAX nonce for admin-ajax.php
					};
				},
				processResults: function( data ) {
					var options = [];
					if ( data ) {
	   
						// data is the array of arrays, and each of them contains ID and the Label of the option
						jQuery.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
							options.push( { id: text[0], text: text[1]  } );
						});
	   
					}
					return {
						results: options
					};
				},
				cache: true
			},
			multiple: false,
			placeholder: 'Choose Users',
			minimumInputLength: 3 // the minimum of symbols to input before perform a search
			
		});

		filter_row_rule++;

	}
</script>
