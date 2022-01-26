<?php
include 'scd_pro_currencies.php';


add_filter('scd_multivendors_activate', 'scd_multivendors_activate_func', 10, 1);

function scd_multivendors_activate_func($scd_multi_activate) {
    return true;
}


function scd_get_user_currency() {
    $user_curr = get_user_meta(get_current_user_id(), 'scd-user-currency', true);
    if ($user_curr) {
        return $user_curr;
    } else {
        $default_curr = get_option( 'woocommerce_currency');
        return $default_curr;
    }
}

function scd_get_user_currency_option() {
    $curr_opt = get_user_meta(get_current_user_id(), 'user-currency-option');
    if (count($curr_opt) > 0) {
        return $curr_opt[0];
    } else {
        return 'only-default-currency';
    }
}

add_action('wp_ajax_scd_show_user_currency', 'scd_show_user_currency');

function scd_show_user_currency() {
    $options = array(
        'base-currency' => __('Base currency only'),
        'only-default-currency' => __('Your default currency only'),
    );
    ?>
    <div class="scd-container" style="margin-left:5%;">
        <p id="scd-action-status" style="margin-left:15%;"></p>
         <div class="scd-form-grp">
             <p class="scd-label"><?php esc_html_e('Select your default currency')?></p>
             <div class="scd-form-input">
                <select id="scd-currency-list" class="scd-user-curr">
                    <?php
                    $user_curr = scd_get_user_currency();

                    foreach (scd_get_list_currencies() as $key => $val) {
                        if ($user_curr == $key) {
                            _e( '<option selected value="' . $key . '" >' . $key . '(' . get_woocommerce_currency_symbol($key) . ')</option>');
                        } else {
                            _e( '<option value="' . $key . '" >' . $key . '(' . get_woocommerce_currency_symbol($key) . ')</option>');
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="scd-form-btn">
                <?php
                echo '<br>';
                
                ?>
            </div>
        </div>
        <div class="scd-form-grp">
            <p class="scd-label"><?php esc_html_e("Set products price in")?></p>
            <div class="scd-form-input">
                <select id="scd-currency-option" class="scd-user-curr">
                    <?php
                    $currency_opt = scd_get_user_currency_option();
                    foreach ($options as $key => $val) {
                        if ($currency_opt == $key) {
                            _e( '<option selected value="' . $key . '" >' . $val . '</option>');
                        } else {
                            _e( '<option value="' . $key . '" >' . $val . '</option>');
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="scd-form-btn">
                <?php
                echo '<br>';
                
                ?>
            </div>
            <div class="scd-form-btn">
                <?php
                _e('<a  style="color:black;" class="scd-btn-control button" href="#" id="scd-save-currency-option">Save change<a>');
                echo '</p>';
                ?>
            </div>
        </div>
    </div>
    <?php
    die();
}

add_action('wp_ajax_scd_update_user_currency', 'scd_update_user_currency');

function scd_update_user_currency() {
    if (isset($_POST['user_currency'])) {
        $user = sanitize_text_field($_POST['user_currency']);
        update_user_meta(get_current_user_id(), 'scd-user-currency', $user);
        _e('Information saved. Your new custom currency is '); echo get_user_meta(get_current_user_id(), 'scd-user-currency')[0];
    } else {
        echo 'Currency not saved please try again';
    }
    die();
}

add_action('wp_ajax_scd_update_user_currency_option', 'scd_update_user_currency_option');

function scd_update_user_currency_option() {
    if (isset($_POST['user_currency_option'])) {
        $user_opt = sanitize_text_field($_POST['user_currency_option']);
        update_user_meta(get_current_user_id(), 'user-currency-option', $user_opt);
        _e('Information saved');
    } else {
        echo 'Option not saved please try again';
    }
    die();
}

//when vendor is connected set the target currency to his default currency
function scd_multivendor_currency($scd_target_currency) {

    $user_currency = scd_get_user_currency();
    if ($user_currency != false) {
        $scd_target_currency = $user_currency;
    }
    return $scd_target_currency;
}

//export import products with woocommerce

add_filter('woocommerce_product_export_column_names', 'scd_add_export_column');
add_filter('woocommerce_product_export_product_default_columns', 'scd_add_export_column');

function scd_add_export_column($columns) {

    // column slug => column name
    $columns['scd_other_options'] = 'Meta: scd_other_options';

    return $columns;
}

function scd_add_export_data($value, $product) {
    $value = get_post_meta($product->get_id(), 'scd_other_options', true);

    return serialize($value);
}

// Filter you want to hook into will be: 'woocommerce_product_export_product_column_{$column_slug}'.
add_filter('woocommerce_product_export_product_column_scd_other_options', 'scd_add_export_data', 10, 2);

// Hook into the filter
add_filter("woocommerce_product_importer_parsed_data", "scd_csv_import_serialized", 10, 2);

function scd_csv_import_serialized($data, $importer) {
    if (isset($data["meta_data"]) && is_array($data["meta_data"])) {
        foreach (array_keys($data["meta_data"]) as $k) {
            $data["meta_data"][$k]["value"] = maybe_unserialize($data["meta_data"][$k]["value"]);
        }
    }
    return $data;
}

//filter in the free version
add_filter('is_scd_multivendor', 'is_scd_multivendor', 10, 1);

function is_scd_multivendor($multi) {
    return true;
}

add_filter('scd_disable_sidebar_currencies', 'fct_scd_disable_sidebar_currencies', 10, 1);

function fct_scd_disable_sidebar_currencies() {
    return false;
}





add_filter('woocommerce_cart_totals_order_total_html', 'scd_woocommerce_cart_totals_order_total_html', 10, 1);
function scd_woocommerce_cart_totals_order_total_html($total){
    
    if(get_option( 'woocommerce_tax_display_cart' )=="excl"){
        return $total;
    }
    $currency_cart = scd_get_target_currency();

    $items = WC()->cart->get_cart();

    $mysubtot = 0;
    $basecurrency = get_option('woocommerce_currency');
    foreach ($items as $cart_item) {
        // Get the product price from the id
        $product = wc_get_product($cart_item['product_id']);
        if((get_the_terms( $product->get_id(),'product_type')[0]->slug == "variable") || (get_the_terms( $product->get_id(),'product_type')[0]->slug == "simple")){
            if (!empty($product)) {
            if($cart_item['variation_id']){
                $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);
                $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);     
                $price_html = $regprice;
                if($regprice > 0){
                    if($saleprice!=""){
                        $price_html = $saleprice;
                    }
                }else{
                    $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                    $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);     
                    $price_html = $regprice;
                    if($regprice > 0){
                        if($saleprice!=""){
                            $price_html = $saleprice;
                        }
                    }
                }
            }else{
                $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);
                $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);      
                $price_html = $regprice;
                if($regprice > 0){
                    if($saleprice!=""){
                        $price_html = $saleprice;
                    }
                }else{
                    $price_html = (check_simple_product_custom_prices($product,true));
                    if($price_html <= 0 ){
                        $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $price_html = $regprice;
                        $regprice = 0;
                        if($regprice > 0){
                            if($saleprice!=""){
                                $price_html = $saleprice;
                            }
                        }
                    }
                }
            }
            $qty = $cart_item['quantity'];

            // Add the item price to our computed subtotal
            $unit_price = $price_html * $qty;
            $mysubtot += $unit_price;
        }
        }else{
            $qty = $cart_item['quantity'];

            // Add the item price to our computed subtotal
            $unit_price = scd_function_convert_subtotal($cart_item['data']->get_price(), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE) * $qty;
            $mysubtot += $unit_price;
        }
    }

    // If prices are tax inclusive, show taxes here.
    if (wc_tax_enabled() && WC()->cart->display_prices_including_tax()) {
        $tax_string_array = array();
        $cart_tax_totals = WC()->cart->get_tax_totals();

        if (get_option('woocommerce_tax_total_display') === 'itemized') {
            foreach ($cart_tax_totals as $code => $tax) {
                $tax_amount = $tax->amount;
                if ($currency_cart != $basecurrency) {
                    $tax_amount = scd_function_convert_subtotal($tax_amount, $basecurrency, $currency_cart);
                }
                $tax_html = scd_format_converted_price_to_html($tax_amount, $args);
                $tax_string_array[] = sprintf('%s %s', $tax_html, $tax->label);
            }
        } elseif (!empty($cart_tax_totals)) {
            $tax_amount = WC()->cart->get_taxes_total(true, true);
            if ($currency_cart != $basecurrency) {
                $tax_amount = scd_function_convert_subtotal($tax_amount, $basecurrency, $currency_cart);
            }
            $tax_html = scd_format_converted_price_to_html($tax_amount, $args);
            $tax_string_array[] = sprintf('%s %s', $tax_html, WC()->countries->tax_or_vat());
        }

        if (!empty($tax_string_array)) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text = WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping() ? sprintf(' ' . __('estimated for %s', 'woocommerce'), WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]) : '';
        }
    }else{
        $total_tax = "";
        foreach(WC()->cart->get_tax_totals() as $tax){
            $total_tax += scd_function_convert_subtotal($tax->amount, scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
        }
    }

    //get shipping fees
    $ship_total = WC()->cart->get_shipping_total();
    if ($currency_cart != $basecurrency) {
        $ship_total = scd_function_convert_subtotal($ship_total, $basecurrency, $currency_cart);
    }

    $total_amount = $mysubtot + $ship_total + $total_tax;

    if($mysubtot!=0){
        return sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($total_amount)));
        
    }
}

add_filter('woocommerce_cart_subtotal', 'scd_woocommerce_cart_subtotal', 10, 3);
function scd_woocommerce_cart_subtotal($cart_subtotal, $compound, $cart){
    if(get_option( 'woocommerce_tax_display_cart' )=="excl"){
        return $cart_subtotal;
    }
    $items = $cart->get_cart();
    $mysubtot = 0;
    foreach ($items as $cart_item) {

        // Get the product price from the id
        $product = wc_get_product($cart_item['product_id']);
        if((get_the_terms( $product->get_id(),'product_type')[0]->slug == "variable") || (get_the_terms( $product->get_id(),'product_type')[0]->slug == "simple")){
            if (!empty($product)) {
                if($cart_item['variation_id']){
                    $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);
                    $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);     
                    $price_html = $regprice;
                    if($regprice > 0){
                        if($saleprice!=""){
                            $price_html = $saleprice;
                        }
                    }else{
                        $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);     
                        $price_html = $regprice;
                        if($regprice > 0){
                            if($saleprice!=""){
                                $price_html = $saleprice;
                            }
                        }
                    }
                }else{
                    $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);
                    $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);      
                    $price_html = $regprice;
                    if($regprice > 0){
                        if($saleprice!=""){
                            $price_html = $saleprice;
                        }
                    }else{
                        $price_html = (check_simple_product_custom_prices($product,true));
                        if($price_html <= 0 ){
                            $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                            $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                            $price_html = $regprice;
                            $regprice = 0;
                            if($regprice > 0){
                                if($saleprice!=""){
                                    $price_html = $saleprice;
                                }
                            }
                        }
                    }
                }
                $qty = $cart_item['quantity'];
    
                // Add the item price to our computed subtotal
                $unit_price = $price_html * $qty;
                $mysubtot += $unit_price;
            }
        }else{
            $qty = $cart_item['quantity'];
            // Add the item price to our computed subtotal
            $unit_price = scd_function_convert_subtotal($cart_item['data']->get_price(), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE) * $qty;
            $mysubtot += $unit_price;
        }
    }
    
    if($mysubtot!=0){
        return sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($mysubtot)));
    }else{
        return $total;
    }
}




add_filter('woocommerce_cart_item_subtotal', 'scd_change_cart_item_subtotal_html', 10, 3);
function scd_change_cart_item_subtotal_html($subtotal, $cart_item, $cart_item_key){

    // Get the product price from the id
    $product = wc_get_product($cart_item['product_id']);
    if((get_the_terms( $product->get_id(),'product_type')[0]->slug == "variable") || (get_the_terms( $product->get_id(),'product_type')[0]->slug == "simple")){
        if (!empty($product)) {
           if($cart_item['variation_id']){
                $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);
                $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);     
                $price_html = $regprice;
                if($regprice > 0){
                        if($saleprice!=""){
                            $price_html = $saleprice;
                        }
                    }else{
                        $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);     
                        $price_html = $regprice;
                        if($regprice > 0){
                            if($saleprice!=""){
                                $price_html = $saleprice;
                            }
                        }
                    }
            }else{
                $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);
                $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);      
                $price_html = $regprice;
                if($regprice > 0){
                    if($saleprice!=""){
                        $price_html = $saleprice;
                    }
                }else{
                    $price_html = (check_simple_product_custom_prices($product,true));
                    if($price_html <= 0 ){
                        $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $price_html = $regprice;
                        $regprice = 0;
                        if($regprice > 0){
                            if($saleprice!=""){
                                $price_html = $saleprice;
                            }
                        }
                    }
                }
            }
        }
        if($price_html!=0){
            return sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($price_html * $cart_item['quantity'])));
        }else{
            return $subtotal;
        }
    }else{
        return $subtotal;
    }
}


add_filter('woocommerce_cart_item_price', 'scd_change_product_cart_html', 10, 3);
function scd_change_product_cart_html($base_price_html, $cart_item, $cart_item_key) {
    $product = wc_get_product($cart_item['product_id']);
    if((get_the_terms( $product->get_id(),'product_type')[0]->slug == "variable") || (get_the_terms( $product->get_id(),'product_type')[0]->slug == "simple")){

        // Get the product price from the id
        if (!empty($product)) {
           if($cart_item['variation_id']){
                $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);
                $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['variation_id'], '_meta_currency', TRUE), 2, TRUE);     
                $price_html = $regprice;
                if($regprice > 0){
                        if($saleprice!=""){
                            $price_html = $saleprice;
                        }
                    }else{
                        $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['variation_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);     
                        $price_html = $regprice;
                        if($regprice > 0){
                            if($saleprice!=""){
                                $price_html = $saleprice;
                            }
                        }
                    }
            }else{
                $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);
                $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($cart_item['product_id'], '_meta_currency', TRUE), 2, TRUE);      
                $price_html = $regprice;
                if($regprice > 0){
                    if($saleprice!=""){
                        $price_html = $saleprice;
                    }
                }else{
                    $price_html = (check_simple_product_custom_prices($product,true));
                    if($price_html <= 0 ){
                        $regprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $saleprice = scd_function_convert_subtotal(get_post_meta($cart_item['product_id'], '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $price_html = $regprice;
                        $regprice = 0;
                        if($regprice > 0){
                            if($saleprice!=""){
                                $price_html = $saleprice;
                            }
                        }
                    }
                    
                }
            }
        }
        if($price_html > 0 && $price_html!="" ){
            return sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($price_html)));
        }else{
            return $base_price_html;
        }
    }else{
        return $price_html;
    }
}


add_filter('woocommerce_get_price_html', 'scd_change_product_html', 10,2);
function scd_change_product_html($price_html, $product) {
    if((get_the_terms( $product->get_id(),'product_type')[0]->slug == "variable") || (get_the_terms( $product->get_id(),'product_type')[0]->slug == "simple")){
        return scd_product_html($price_html, $product);
    }else{
        return $price_html;
    }
}

function check_simple_product_custom_prices($product,$cart){
$price_html = "";
	if(get_post_meta($product->get_id(), 'scd_other_options')){
         if (strpos(get_post_meta($product->get_id(), 'scd_other_options')[0]["currencyVal"],scd_get_target_currency()) == 0 || strpos(get_post_meta($product->get_id(), 'scd_other_options')[0]["currencyVal"],scd_get_target_currency()) != false) {

			 $prices = explode(",",(get_post_meta($product->get_id(), 'scd_other_options')[0]["currencyPrice"]));
			 
             foreach($prices as $price){
                if(strpos($price,scd_get_target_currency())){
                    $each_prices = explode('-',$price);
						if(explode('_',$each_prices[1])[2]!=""){
						    if($cart){
						        $price_html = explode('_',$each_prices[1])[2];
						    }else{
						        $price_html = sprintf(__('<del>%1$s</del> %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval(explode('_',$each_prices[0])[2])), scd_wcfm_vendor_format_converted_price_to_html(floatval(explode('_',$each_prices[1])[2])));
						    }
						}else{ 
						    if($cart){
						        $price_html = explode('_',$each_prices[0])[2];
						    }else{
							    $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval(explode('_',$each_prices[0])[2])));
						    }
						}
                }   
             }
        }else{
			$prices = explode(",",(get_post_meta($product->get_id(), 'scd_other_options')[0]["currencyPrice"]));
            foreach($prices as $price){
                $each_prices = explode('-',$price);				
				if(explode('_',$each_prices[0])[2] != ""){
					$regprice = scd_function_convert_subtotal(explode('_',$each_prices[0])[2], explode('_',$each_prices[0])[1], get_woocommerce_currency(), 2, TRUE);
					$saleprice = scd_function_convert_subtotal(explode('_',$each_prices[1])[2], explode('_',$each_prices[0])[1], get_woocommerce_currency(), 2, TRUE);
					if(explode('_',$each_prices[1])[2]!=""){
						if($cart){
						    $price_html = $saleprice;
					    }else{
                            $price_html = sprintf(__('<del>%1$s</del> %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)), scd_wcfm_vendor_format_converted_price_to_html(floatval($saleprice)));
					    }
					}else{ 
					    if($cart){
						    $price_html = $regprice;
					    }else{
    						$price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)));
					    }
					}
				}
            }
        }
    }
	return $price_html;
}

function check_simple_product_prices($product,$product_id) {
	$price_html = "";
    $regprice = scd_function_convert_subtotal(get_post_meta($product_id, '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($product_id, '_meta_currency', TRUE), 2, TRUE);
    $saleprice = scd_function_convert_subtotal(get_post_meta($product_id, '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($product_id, '_meta_currency', TRUE), 2, TRUE);
    if(($regprice!='') && ($regprice > 0)){
        if($regprice==$saleprice || $saleprice==""){
            $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)));
        }else{
            $price_html = sprintf(__('<del>%1$s</del> %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)), scd_wcfm_vendor_format_converted_price_to_html(floatval($saleprice)));
        }  
    }else{
        $regprice = scd_function_convert_subtotal(get_post_meta($product_id, '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
        $saleprice = scd_function_convert_subtotal(get_post_meta($product_id, '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
        
        if(($regprice!='') && ($regprice > 0)){
            if($regprice==$saleprice || $saleprice==""){
                $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)));
            }else{
                $price_html = sprintf(__('<del>%1$s</del> %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)), scd_wcfm_vendor_format_converted_price_to_html(floatval($saleprice)));
            }   
        }
    }
	
	if($regprice == 0){
        $regprice = scd_function_convert_subtotal($product->get_regular_price(), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
        $saleprice = scd_function_convert_subtotal($product->get_sale_price(), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
        if(($regprice!='') && ($regprice > 0)){
            if($regprice==$saleprice || $saleprice==""){
                $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)));

            }else{
                $price_html = sprintf(__('<del>%1$s</del> - %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)), scd_wcfm_vendor_format_converted_price_to_html(floatval($saleprice)));
            }   
        }
    }
	return $price_html;
}

function check_variable_prices($product){
    if($product->get_children()){
        $array_price = array();
        $price_html = "";
            foreach ($product->get_children() as $variation_id) {
                $variable_product = wc_get_product($variation_id);
                
                $regprice = scd_function_convert_subtotal(get_post_meta($variation_id, '_meta_regular_price', TRUE), scd_get_target_currency(), get_post_meta($variation_id, '_meta_currency', TRUE), 2, TRUE);
                $saleprice = scd_function_convert_subtotal(get_post_meta($variation_id, '_meta_sale_price', TRUE), scd_get_target_currency(), get_post_meta($variation_id, '_meta_currency', TRUE), 2, TRUE);
                if(($regprice!='') && ($regprice > 0)){
                    if($saleprice==""){
                        array_push($array_price,$regprice);
                    }else{
                        array_push($array_price,$saleprice);
                    }
                }else{
                    $regprice = scd_function_convert_subtotal(get_post_meta($variation_id, '_regular_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                    $saleprice = scd_function_convert_subtotal(get_post_meta($variation_id, '_sale_price', TRUE), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                    if(($regprice!='') && ($regprice > 0)){
                        if($saleprice==""){
                            array_push($array_price,$regprice);
                        }else{
                            array_push($array_price,$saleprice);
                        }
                    }else{
                        $regprice = scd_function_convert_subtotal($variable_product->get_regular_price(), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        $saleprice = scd_function_convert_subtotal($variable_product->get_sale_price(), scd_get_target_currency(), get_woocommerce_currency(), 2, TRUE);
                        if(($regprice!='') && ($regprice > 0)){
                            if($saleprice==""){
                                array_push($array_price,$regprice);
                            }else{
                                array_push($array_price,$saleprice);
                            }
                        }
                    }
                }
                
                
            }
            if(sizeof($array_price) > 0){
                asort($array_price);
                $price1 = $array_price[0];
                $price2 = $array_price[sizeof($array_price)-1];
                if($price1==$price2){
                    $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($price1)));

                }else{
                    $price_html = sprintf(__('%1$s - %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($price2)), scd_wcfm_vendor_format_converted_price_to_html(floatval($price1)));

                }	
            }
    }
	return $price_html;
}




function display_price_in_vendor_currency($product){
    if($product->get_children()){
        $array_price = array();
        $price_html = "";
        foreach ($product->get_children() as $variation_id) {
            $regprice = scd_function_convert_subtotal(get_post_meta($variation_id, '_meta_regular_price', TRUE), scd_get_user_currency(), get_post_meta($variation_id, '_meta_currency', TRUE), 2, TRUE);
            $saleprice = scd_function_convert_subtotal(get_post_meta($variation_id, '_meta_sale_price', TRUE), scd_get_user_currency(), get_post_meta($variation_id, '_meta_currency', TRUE), 2, TRUE);
            if(($regprice!='') && ($regprice > 0)){
                if($saleprice==""){
                    array_push($array_price,$regprice);
                }else{
                    array_push($array_price,$saleprice);
                }
            }
        }
        if(sizeof($array_price)>0){
            (sort($array_price,SORT_NUMERIC));
            $price1 = $array_price[0];
            $price2 = $array_price[sizeof($array_price)-1];   
            if($price1==$price2){
                $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($price1)));
            }else{
                $price_html = sprintf(__('%1$s - %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($price2)), scd_wcfm_vendor_format_converted_price_to_html(floatval($price1)));

            }
        }
    }else{
        $product_id = $product_id= $product->get_id();
        $regprice = scd_function_convert_subtotal(get_post_meta($product_id, '_meta_regular_price', TRUE), scd_get_user_currency(), get_post_meta($product_id, '_meta_currency', TRUE), 2, TRUE);
        $saleprice = scd_function_convert_subtotal(get_post_meta($product_id, '_meta_sale_price', TRUE), scd_get_user_currency(), get_post_meta($product_id, '_meta_currency', TRUE), 2, TRUE);
        if(($regprice!='') && ($regprice > 0)){
            if($regprice==$saleprice || $saleprice==""){
                $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)));
            }else{
                $price_html = sprintf(__('<del>%1$s</del> %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)), scd_wcfm_vendor_format_converted_price_to_html(floatval($saleprice)));
            }   
        }else{
            $user_curr = scd_get_user_currency();
            list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $user_curr);
            if(($regprice!='') && ($regprice > 0)){
                if($regprice==$saleprice || $saleprice==""){
                    $price_html = sprintf(__('%1$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)));
                }else{
                    $price_html = sprintf(__('<del>%1$s</del> %2$s', 'woocommerce'), scd_wcfm_vendor_format_converted_price_to_html(floatval($regprice)), scd_wcfm_vendor_format_converted_price_to_html(floatval($saleprice)));
                }   
            }
        }
    }
    return $price_html;
}

function scd_product_html($price_html_base, $product) {
    global $wp;
    if((class_exists("WCFM_Products_Controller"))){
        $price_html = display_price_in_vendor_currency($product);
    }else{
        $price_html = check_variable_prices($product);
    	if($price_html==""){
    	   $price_html = check_simple_product_custom_prices($product,false);
    	}
    	if($price_html==""){
    	   $price_html = check_simple_product_prices($product,$product_id);		
    	}
    }

    if($price_html != ""){
        $price_html_base = $price_html;
    } 
    return $price_html_base;
}



//Quick edit solution
add_action('wcfm_product_quick_edit_end','scd_quick_view',10,1);
function scd_quick_view ($product_id){

    $regprice = scd_function_convert_subtotal(get_post_meta($product_id, '_meta_regular_price', TRUE), scd_get_user_currency(), get_post_meta($product_id, '_meta_currency', TRUE), 2, TRUE);
    $saleprice = scd_function_convert_subtotal(get_post_meta($product_id, '_meta_sale_price', TRUE), scd_get_user_currency(), get_post_meta($product_id, '_meta_currency', TRUE), 2, TRUE);
  
    if($regprice <= 0 ){
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, scd_get_user_currency());
    }
    
    if($regprice <= 0 ){
        $regprice = scd_function_convert_subtotal($product_id, scd_get_user_currency(), get_woocommerce_currency(), 2, TRUE);
        $saleprice = scd_function_convert_subtotal($product_id, scd_get_user_currency(), get_woocommerce_currency(), 2, TRUE);   
    }
    if($regprice > 0 ){
        ?>
        <script>
            (jQuery('input[name="wcfm_quick_edit_regular_price"]').val('<?php echo json_encode($regprice); ?>'))
        </script>
        <?php
    }
    if($saleprice > 0 ){
        ?>
        <script>
            (jQuery('input[name="wcfm_quick_edit_sale_price"]').val('<?php echo json_encode($saleprice); ?>'))
        </script>
        <?php
    }
}

function scd_wcfm_vendor_format_converted_price_to_html($price)
{
    $store_currency = get_option('woocommerce_currency');
    $target_currency = scd_get_target_currency() ?? $store_currency;
    $decimals = scd_options_get_decimal_precision();
    $args['currency'] = $target_currency; //function to define
    $args['decimals'] = $decimals;
    $args['price_format'] = scd_change_currency_display_format(get_woocommerce_price_format(), $target_currency);

    // Note: This function adds the class 'scd-converted' to the HTML markup element. This class is 
    //       an indication to the javascript that the price has already been converted.

    $unformatted_price = $price;
    $negative          = $price < 0;

    if (apply_filters('woocommerce_price_trim_zeros', false) && $args['decimals'] > 0) {
        $price = wc_trim_zeros($price);
    }

    $dec = get_option('scd_currency_options');
    $dec = $dec['decimalPrecision'];
    //var_dump($args);
    $price = number_format($price, $dec, wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
    $formatted_price = ($negative ? '-' : '') . sprintf($args['price_format'], '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol(scd_get_user_currency()) . '</span>', $price);
    $return          = '<span class="woocommerce-Price-amount amount scd-converted" basecurrency="' . scd_get_user_currency() . '">' . $formatted_price . '</span>';

    if ($args['ex_tax_label'] && wc_tax_enabled()) {
        $return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
    }

    return $return;
}


// symbol withdraw in wcfm vendor dashboard
add_action( 'before_wcfm_payments' ,'scd_vendor_curr_wcfm_page_heading',999);   

function scd_vendor_curr_wcfm_page_heading(){
 
    $currency=scd_get_user_currency();
    $currency=get_woocommerce_currency_symbol($currency);
    
    ?>
    <script>
        jQuery(document).ready(function(){
          
        jQuery('.wcfmfa.fa-currency').text('<?php echo json_encode($currency);?>');
      
        });
    </script>
    
    <?php
}