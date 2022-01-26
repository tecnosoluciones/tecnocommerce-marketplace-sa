<?php
include_once "scd_currencies.php";    
//include_once 'scd_pro_currencies.php';

/////////////////////////

function scd_echoChecked($opgroup, $option) {

    $opts = get_option($opgroup);
    checked($opts[$option], "1"); 
}

function scd_isChecked($opgroup, $option) {

    $opts = get_option($opgroup);
    if($opts[$option]=="1")
        return true;
    else
        return false;
}

function scd_get_user_role($user = null) {
    $user = $user ? new WP_User($user) : wp_get_current_user();
    return $user->roles ? $user->roles[0] : false;
}

//////////////////////////


function scd_custom_product_basic_load() {

    $reg = get_option('scd_currency_options');
    if ($reg['priceByCurrency']) {
        add_meta_box('scd_render_custom_product_basic_metabox', __('SCD - Set Currency per Product'), 'scd_render_custom_product_basic_metabox', 'product', 'normal', 'high');
        add_action('save_post', 'scd_custom_product_basic_save_post', 10, 1);
        add_action('woocommerce_product_options_general_product_data', 'scd_custom_product_general_fields');
        //add_action( 'woocommerce_process_product_meta', 'general_fields_save' );
    }
}
     
function scd_render_custom_product_basic_metabox($post) {

    global $wp_roles;
    global $post;
    //$roles = $wp_roles->get_names();
    $reg = get_option('scd_currency_options');
    $scd_getUserRole = scd_get_user_role();


    /* $rol = '';
      $currencyNumber = ''; */
    $curs = "";
    $curspri = '';

    if (get_post_meta($post->ID, 'scd_other_options')) {

        $datas = get_post_meta($post->ID, 'scd_other_options');

        foreach ($datas as $metakey) {

            /* $rol = $metakey['role'];
              $currencyNumber = $metakey['currencyNumber']; */
            $curs = (!empty($metakey['currencyVal']) ) ? $metakey['currencyVal'] : "";
            $curspri = $metakey['currencyPrice'];
        }
    }

    wp_localize_script('ch_scd_adminready', 'mysettings', array(
        'currenciesRole' => $curs
    ));

    
        $setR = apply_filters('scd-role',1);
    
    ?>

<div class="scd_cpp">
    <input type='hidden' name='scd_currencyVal' id="currencyValField" value="<?php echo $curs; ?>" />
    <h4 style="margin-right: -15px;"><?php _e('Set currency : '); ?></h4>

    <?php
    if(apply_filters('is_scd_multivendor',false)) 
    {
        if ('administrator' != $scd_getUserRole) {
            $choi = ($setR == 4) ? "Unlimited" : $setR;
            ?>
    <div class="help-tip">
        <p>You have (<?php echo $choi; ?>) choice(s).</p>
    </div>
    <?php 
        } 
    }
    ?>

    <select data-placeholder="Set currency by product" class="scd_role_select" multiple>

        <?php if ($setR == 4) { ?>
        <optgroup label="<?php _e('All', 'ch_scd_woo'); ?>">
            <option value="allcurrencies" <?php echo (substr_count($curs, "allcurrencies") != 0) ? "selected" : ""; ?>>
                <?php _e('All', 'ch_scd_woo'); ?></option>
        </optgroup>
        <?php } ?>

        <optgroup label="<?php _e('Fixed currencies', 'ch_scd_woo'); ?>">

            <?php
				$ops=get_option('scd_currency_options',true);
                $currChoice=$ops['userCurrencyChoice'];
//				if ($currChoice!='allcurrencies'){
//				$currChoice=explode(',',$currChoice);
//				foreach ($currChoice as $key) {
//				if ($key != get_option('woocommerce_currency')) {
//				$symb= get_woocommerce_currency_symbol($key);
//				if (substr_count($curs, $key) != 0)
//				$sel = "selected";
//				else
//				$sel = "";
//				echo "<option value='$key' $sel>$key - $symb</option>";
//				}
//				}
//				}else {
                                                            $currencies_list= scd_get_list_currencies(); //apply_filters('scd_list_currencies',$GLOBALS['currencies_scd_get_list_currencies()']);
				foreach ($currencies_list as $key => $val) {
				if ($key != get_option('woocommerce_currency')) {

				if (substr_count($curs, $key) != 0)
				$sel = "selected";
				else
				$sel = "";
				echo "<option value='$key' $sel>$key - $val</option>";
				}
				}
				//}
                ?>

        </optgroup>
    </select>
</div>
<?php
}


function scd_custom_product_general_fields($post_id = 0) {
    global $post;
    $reg = get_option('scd_currency_options');

    if ($reg['priceByCurrency']) {

        /* $rol = '';
          $currencyNumber = '';
          $curs = ''; */
        $curspri = '';
        if ($post_id == 0)
            $post_id = $post->ID;
        if (get_post_meta($post_id, 'scd_other_options')) {

            $datas = get_post_meta($post_id, 'scd_other_options');

            foreach ($datas as $metakey) {

                /* $rol = $metakey['role'];
                  $currencyNumber = $metakey['currencyNumber'];
                  $curs = $metakey['currencyVal']; */
                $curspri = $metakey['currencyPrice'];
            }
        }

        /* if( get_post_meta($post->ID, 'scd_other_options') ) {

          $datas = get_post_meta($post->ID, 'scd_other_options');

          foreach ( $datas as $metakey ){

          $curs = $metakey['currencyVal'];
          }
          } */
        ?>
<div id="mySetPrice">
    <!-- Regular Price -->
    <p>
        <input type="hidden" name='priceField' id="priceField" value="<?php echo $curspri; ?>" />
        <select id="scd_regularCurrency" name='scd_regularCurrency' style="margin-right: 7px;width: 148px;"
            data-placeholder="Regular price" class="scd_price_select">

            <?php
                    /* foreach (scd_get_list_currencies() as $key => $val) {

                      if($key != get_option('woocommerce_currency')) {
                      if(substr_count($curs, $key) != 0) echo "<option value='$key' >Regular price ($key)</option>";
                      }
                      } */

                    if (get_post_meta($post_id, 'scd_other_options')) {

                        $datas = get_post_meta($post_id, 'scd_other_options');

                        foreach ($datas as $metakey) {
                            $curs = $metakey['currencyVal'];
                        }

                        if ($curs != "") {
                            if ($curs == "allcurrencies") {
                                foreach (scd_get_list_currencies() as $key => $val) {

                                    echo "<option value='$key' >Regular price ($key)</option>";
                                }
                            } else {
                                $curs = explode(",", $curs);

                                foreach ($curs as $cur) {
                                    echo "<option value='" . $cur . "' >Regular price (" . $cur . ")</option>";
                                }
                            }
                        }
                    }
                    ?>

        </select>

        <?php
                $result = "";
                $ii = 1;
                $curspri2 = explode(",", $curspri);
                foreach ($curspri2 as $curspris) {

                    $curspris = explode("-", $curspris);
                    foreach ($curspris as $curspriss) {

                        $cursprisss = explode("_", $curspriss);

                        if ($cursprisss[0] == "regular" && $ii == 1)
                            $result = $cursprisss[2];

                        $ii++;
                    }
                }
                ?>

        <input id="scd_regularPriceCurrency" name='scd_regularPriceCurrency'
            placeholder="Set Regular Price. (e.g. 59.34)" style="width: 55%;border-radius: 3px;border: 1px solid #ccc;"
            type='text' value='<?php echo $result; ?>' />

    </p>
    <fieldset id="regPrice">
        <?php
                $curspri2 = explode(",", $curspri);
                foreach ($curspri2 as $curspris) {

                    $curspris = explode("-", $curspris);
                    foreach ($curspris as $curspriss) {

                        $cursprisss = explode("_", $curspriss);

                        if ($cursprisss[0] == "regular")
                            echo '<input type="hidden" id="regularField_' . $cursprisss[1] . '" value="' . $curspriss . '"/>';
                    }
                }
                ?>
    </fieldset>

    <!-- Sale Price -->
    <p>

        <!--<input type="hidden" name="scd_saleField" id="saleField" value="<?php //echo $curs; ?>"/>-->
        <select id="scd_saleCurrency" name='scd_saleCurrency' style="margin-right: 7px;width: 148px;"
            data-placeholder="Sale price" class="scd_price_select" disabled="disabled">

            <?php
                    /* foreach (scd_get_list_currencies() as $key => $val) {

                      if($key != get_option('woocommerce_currency')) {

                      echo "<option value='$key' $sel>Sale price ($key)</option>";
                      }
                      } */

                    if (get_post_meta($post_id, 'scd_other_options')) {

                        $datas = get_post_meta($post_id, 'scd_other_options');

                        foreach ($datas as $metakey) {
                            $curs = $metakey['currencyVal'];
                        }

                        if ($curs != "") {
                            if ($curs == "allcurrencies") {
                                foreach (scd_get_list_currencies() as $key => $val) {

                                    echo "<option value='$key' >Sale price ($key)</option>";
                                }
                            } else {
                                $curs = explode(",", $curs);

                                foreach ($curs as $cur) {
                                    echo "<option value='" . $cur . "' >Sale price (" . $cur . ")</option>";
                                }
                            }
                        }
                    }
                    ?>

        </select>

        <?php
                $result = "";
                $ii = 1;
                $curspri2 = explode(",", $curspri);
                foreach ($curspri2 as $curspris) {

                    $curspris = explode("-", $curspris);
                    foreach ($curspris as $curspriss) {

                        $cursprisss = explode("_", $curspriss);

                        if ($cursprisss[0] == "sale" && $ii == 2)
                            $result = $cursprisss[2];

                        $ii++;
                    }
                }
                ?>
        <input id="scd_salePriceCurrency" name='scd_salePriceCurrency' placeholder="Set Sale Price. (e.g. 32.05)"
            style="width: 55%;border-radius: 3px;border: 1px solid #ccc;" type='text' value='<?php echo $result; ?>' />
    </p>
    <fieldset id="salPrice">
        <?php
                $curspri2 = explode(",", $curspri);
                foreach ($curspri2 as $curspris) {

                    $curspris = explode("-", $curspris);
                    foreach ($curspris as $curspriss) {

                        $cursprisss = explode("_", $curspriss);

                        if ($cursprisss[0] == "sale")
                            echo '<input type="hidden" id="saleField_' . $cursprisss[1] . '" value="' . $curspriss . '"/>';
                    }
                }
                ?>
    </fieldset>
</div>

<?php
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////

function scd_sanitize_currency ($curr){
    return strtoupper(sanitize_key($curr));
}

function scd_custom_product_basic_save_post($post_id) {

    // return if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Check the user's permissions.
    /* if ( ! current_user_can( 'edit_post', $post_id ) ){
      return;
      } */

    $scd_currencyVal = '';
    $priceField = '';

    if (isset($_POST['scd_currencyVal'])) {
        $scd_currencyVal = sanitize_text_field($_POST['scd_currencyVal']);
    }

    if (isset($_POST['priceField'])) {
        $priceField = sanitize_text_field($_POST['priceField']);
    }

    $scd_userRole = scd_get_user_role();
    $scd_userID = get_current_user_id();

    update_post_meta($post_id, 'scd_other_options', array(
        /* "role" => $scd_role,
          "currencyNumber" => $scd_currencyNumber, */
        "currencyUserID" => $scd_userID,
        "currencyUserRole" => $scd_userRole,
        "currencyVal" => $scd_currencyVal,
        "currencyPrice" => $priceField
    ));
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////

function scd_getPost() {

    $out = array();

    $productsID = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'fields' => 'ids'
    ));

    $products_IDs = $productsID->get_posts();

    foreach ($products_IDs as $myId) {

        if (get_post_meta($myId, 'scd_other_options')) {
            $datas = get_post_meta($myId, 'scd_other_options');

            foreach ($datas as $metakey) {

                $out[$myId] = $metakey['currencyPrice'];
            }
        }
    }

    return $out;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Simple function to print to console
 */
function scd_php_console_log( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( '<PHP Console>: " . $output . "' );</script>";
}

/**
 * Change the HTML price markup string.
 *
 * @param string $price_html        Price HTML markup.
 * @param string $price_formatted   Formatted price.
 * @param array  $args              The arguments passed to wc_price.
 * @param float  $unformatted_price Price as float to allow plugins custom formatting. Since 3.2.0.
 * 
 * @return string updated HTML markup
     * 
 * Note: This function will be called at the end of the function wc_price() which is invoked by woocommerece 
 *       to create the HTML markup for all prices.
 *       The function wc_price() is defined in woocommerce/includes/wc-formatting-functions.php
 */
function scd_convert_price_in_html_markup($price_html, $price_formatted, $args, $unformatted_price) {
    global $post;
    // Patch for woocommerce versions earlier than 3.4.0: 
    // return the unconverted price, it will be converted by the javascript code
    global $woocommerce;
    if( version_compare( $woocommerce->version, "3.4", "<" ) ){
        return $price_html;
    }

    // Note : If the multi currency payment option is enabled,
    // and the current page is the Order Received page (shown after payment), 
    // do not convert amounts on the page because the order details are already 
    // converted to target currency
    if( (scd_get_bool_option('scd_general_options', 'multiCurrencyPayment')==true) && 
        (is_wc_endpoint_url( 'order-received')==true) ) 
    {
        return $price_html;
    }

    // If the multi currency payment option is NOT enabled, and we are on the
    // checkout page, do not convert the amounts on the page 
    if( (scd_get_bool_option('scd_general_options', 'multiCurrencyPayment')==false) && 
        (is_checkout()==true) ) 
    {
        return $price_html;
    }











    //*******************************************************************
    if(in_array('wcfm-dashboard-page', get_body_class())){   // check if this is the wcfm dashboard page

        //decomment and define the suit function scd_wcfm_get_user_currency
        if (wcfm_is_vendor()) {
            # code...
            $user_curr= get_user_meta(get_current_user_id(), 'scd-user-currency',true);
            $currency = get_option('woocommerce_currency');
            if( $user_curr){
                $decimals = scd_options_get_decimal_precision();
                $converted_price = scd_function_convert_subtotal($unformatted_price, $currency, $user_curr, $decimals);
                $args['currency'] = $user_curr;//function to define
                $args['decimals'] = $decimals;
                $args['price_format'] = scd_change_currency_display_format ($args['price_format'], $user_curr);
                $price_html = scd_format_converted_price_to_html($converted_price, $args);
                
            }
        }

        return $price_html;
    }
	
    if(in_array('dokan-dashboard', get_body_class())){   // check if this is the dokan dashboard page
        return $price_html;
    }

    if(function_exists('wcmp_vendor_dashboard_page_id') && is_page(wcmp_vendor_dashboard_page_id())){ // check if this is the wcmp dashboard page
       /* $decimals = scd_options_get_decimal_precision();
        $converted_price = scd_function_convert_subtotal($unformatted_price, $currency, $target_currency, $decimals);
        $args['currency'] = scd_wcmp_get_user_currency();
        $args['decimals'] = $decimals;
        $args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
        $price_html = scd_format_converted_price_to_html($converted_price, $args);*/
        return $price_html;
    }
	
	if(function_exists('wcv_is_vendor_dashboard') && wcv_is_vendor_dashboard()){ // check if this is the wc-vendor dashboard page

        return $price_html;
    }















    
    if(empty($args['currency']) || $args['currency'] = '' )
    {
        $currency = get_option( 'woocommerce_currency');
    }
    else
    {
        $currency = $args['currency'];
    }
    
    $target_currency = scd_get_target_currency();
   
    if($target_currency != $currency)
    {           
        // We only enter here if the target currency differs from the currency in the args array
        $convert_rate = scd_get_conversion_rate($currency, $target_currency);
        if(!empty($convert_rate))
        {
            // The conversion rate is defined.  We will convert the price and call a function
            // to apply the proper formatting.
            $decimals = scd_options_get_decimal_precision();
            $converted_price = scd_function_convert_subtotal($unformatted_price, $currency, $target_currency, $decimals);
            $args['currency'] = $target_currency;
            $args['decimals'] = $decimals;
            $args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
            $price_html = scd_format_converted_price_to_html($converted_price, $args);
        }
    }
    
    return $price_html;
}
/*
 * this format price in scd format price to prevent js conversion 
 */

/**
 * Format the price in the same way that the woocommerce function wc_price() does
 * 
 * @param float $price The price
 * @param array $args The arguments (same as those received by wc_price() function )
 * 
 * @return string the price HTML markup
 * 
 * Reference: woocommerce/includes/wc-formatting-functions.php
 */
function scd_format_converted_price_to_html ($price, $args)
{
    // Note: This function adds the class 'scd-converted' to the HTML markup element. This class is 
    //       an indication to the javascript that the price has already been converted.

    $unformatted_price = $price;
	$negative          = $price < 0;
	$price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
	$price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
		$price = wc_trim_zeros( $price );
	}

	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $args['currency'] ) . '</span>', $price );
	$return          = '<span class="woocommerce-Price-amount amount scd-converted" basecurrency="'.$args['currency'].'">' . $formatted_price . '</span>';

	if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
		$return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
    }
    
    return $return;
}

/**
 * Function to change the displayed currency symbol if a custom symbol has been set by the user
 * 
 * @param string $currency_symbol The default woocommerce currency symbol
 * @param string $currency        The currency
 */
function scd_change_wc_currency_symbol( $currency_symbol, $currency ) {

    // Check if there are custom options defined for this currency
    $custom_options = scd_get_currency_override_options($currency);
    if(!empty($custom_options)){
        // Read the symbol attribute. If it is an empty string leave as is,
        // otherwise return the custom symbol
        $custom_symbol = $custom_options["sym"];
        if(!empty($custom_symbol)){
            return $custom_symbol;  // return the custom symbol
        }
    }

    return $currency_symbol;
}

/**
 * Function to change the displayed currency format if a custom position has been set by the user
 * 
 * @param string $format     The default woocommerce price format
 * @param string $currency   The currency
 */
function scd_change_currency_display_format ($format, $currency)
{
    // Check if there are custom options defined for this currency
    $custom_options = scd_get_currency_override_options($currency);

    if(!empty($custom_options)){
        // Read the currency position attribute. If it is an empty string leave as is,
        // otherwise change the format
        $custom_position = $custom_options["pos"];
        if(!empty($custom_position)){
            // Set the format. 
            // Note: The switch/case below was taken from the woocommerce function get_woocommerce_price_format()
            switch ( $custom_position ) {
                case 'left':
                    $format = '%1$s%2$s';
                    break;
                case 'right':
                    $format = '%2$s%1$s';
                    break;
                case 'left_space':
                    $format = '%1$s&nbsp;%2$s';
                    break;
                case 'right_space':
                    $format = '%2$s&nbsp;%1$s';
                    break;
				case 'right_country':
					$devise = scd_get_target_currency();
					$format = '%1$s%2$s&nbsp;' . $devise;
					break;
				case 'left_country':
					$devise = scd_get_target_currency();
					$format = $devise.'&nbsp%2$s%1$s';
                default:
                $format = '%1$s%2$s';
					break;  
            }
        }
    }   

    return $format;
}

/**
 * Overwrite the Woocommerce regular price of a product
 */
function scd_overwrite_wc_product_get_regular_price($price, $product)
{
    $product_id = $product->get_id();
    $target_currency = scd_get_target_currency();
    
    if( scd_is_custom_price_defined_for_currency($product_id, $target_currency) )
    {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);

        // convert the custom price in target currency to an equivalent price in base currency
        $base_currency = get_option( 'woocommerce_currency');
        $rate = scd_get_conversion_rate($base_currency, $target_currency);
        if(!empty($rate) && !empty($regprice)) {
            $price = floatval($regprice) / floatval($rate);
        }
    }

    return $price;
}


/**
 * Overwrite the Woocommerce sale price of a product
 */
function scd_overwrite_wc_product_get_sale_price($price, $product)
{
    $product_id = $product->get_id();
    $target_currency = scd_get_target_currency();
   
    if( scd_is_custom_price_defined_for_currency($product_id, $target_currency) )
    {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);

        // convert the custom price in target currency to an equivalent price in base currency
        $base_currency = get_option( 'woocommerce_currency');
        $rate = scd_get_conversion_rate($base_currency, $target_currency);
        if(!empty($rate) && !empty($saleprice)) {
            $price = floatval($saleprice) / floatval($rate);
        }
    }

    return $price;
}

/**
 * Overwrite the Woocommerce price of a product
 */
function scd_overwrite_wc_product_get_price($price, $product)
{
    $product_id = $product->get_id();
    $target_currency = scd_get_target_currency();

    if( scd_is_custom_price_defined_for_currency($product_id, $target_currency) )
    {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);

        // convert the custom price in target currency to an equivalent price in base currency
        $base_currency = get_option( 'woocommerce_currency');
        $rate = scd_get_conversion_rate($base_currency, $target_currency);
        if(!empty($rate) && !empty($saleprice)) {
            $price = floatval($saleprice) / floatval($rate);
        }
    }

    return $price;
}


function scd_overwrite_wc_product_variation_get_regular_price($price, $variation)
{
    $variation_id = $variation->get_id();
     
    $target_currency = scd_get_target_currency();

    if( scd_is_custom_price_defined_for_currency($variation_id, $target_currency) )
    {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($variation_id, $target_currency);
        
        // convert the custom price in target currency to an equivalent price in base currency
        $base_currency = get_option( 'woocommerce_currency');
        $rate = scd_get_conversion_rate($base_currency, $target_currency);
        if(!empty($rate) && !empty($regprice)) {
            $price = floatval($regprice) / floatval($rate);
        }
    }

    return $price;
}


/**
 * Overwrite the Woocommerce sale price of a variation product
 */
function scd_overwrite_wc_product_variation_get_sale_price($price, $variation)
{
    $variation_id = $variation->get_id();
    $target_currency = scd_get_target_currency();

    if( scd_is_custom_price_defined_for_currency($variation_id, $target_currency) )
    {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($variation_id, $target_currency);

        // convert the custom price in target currency to an equivalent price in base currency
        $base_currency = get_option( 'woocommerce_currency');
        $rate = scd_get_conversion_rate($base_currency, $target_currency);
        if(!empty($rate) && !empty($saleprice)) {
            $price = floatval($saleprice) / floatval($rate);
        }
    }

    return $price;
}
/**
 * Overwrite the Woocommerce price of a variation product
 */
function scd_overwrite_wc_product_variation_get_price($price, $variation)
{
    $variation_id = $variation->get_id();
    $target_currency = scd_get_target_currency();

    if( scd_is_custom_price_defined_for_currency($variation_id, $target_currency) )
    {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($variation_id, $target_currency);

        // convert the custom price in target currency to an equivalent price in base currency
        $base_currency = get_option( 'woocommerce_currency');
        $rate = scd_get_conversion_rate($base_currency, $target_currency);
        if(!empty($rate) && !empty($saleprice)) {
            if(!empty($price))
            $price = floatval($saleprice) / floatval($rate);
        }
    }

    return $price;
}
//add_filter('woocommerce_get_price_html', 'scd_change_product_html', 10, 2);
      //  add_filter('woocommerce_cart_item_price', 'scd_change_product_cart_html', 10, 3);
//        add_filter('woocommerce_cart_item_subtotal', 'scd_change_cart_item_subtotal_html', 10, 3);
//        add_filter('woocommerce_cart_subtotal', 'scd_woocommerce_cart_subtotal', 10, 3);
//        //add_filter('woocommerce_cart_totals_order_total_html', 'scd_woocommerce_order_amount_total');   
//        //add_filter('woocommerce_cart_totals_order_total_html','scd_woocommerce_cart_totals_order_total_html',10,1);
//        add_filter('woocommerce_cart_total','scd_woocommerce_cart_total',10,1);
//       add_filter('woocommerce_cart_shipping_method_full_label','scd_woocommerce_cart_shipping_method_full_label',10,2);



function scd_get_variation_regular_price_html($price, $variation ) {
    return woocommerce_price($variation->get_regular_price());
}

function scd_get_variation_sale_price_html($price, $variation ) {
    return woocommerce_price($variation->get_sale_price());
}
function scd_get_variation_price_html($price, $variation ) {
    return woocommerce_price($variation->get_price());
}

/**
 * Determine if a custom price is defined for a product in the give currency
 * 
 * @param int $product_id  Product ID
 * @param string $target_currency Target currency code
 * 
 * @return boolean true or false
 */
function scd_is_custom_price_defined_for_currency($product_id, $target_currency='')
{
    $result = false;

    // Check if a custom price has been specified by currency
    if (get_post_meta($product_id, 'scd_other_options')) {

        $datas = get_post_meta($product_id, 'scd_other_options',true);
        $currencyPrice=$datas['currencyPrice'];
        $tab= explode('_', $currencyPrice);
           
        $index= array_search($target_currency, $tab);
        if($index !== FALSE)
        {
            $result = true;
        }
       if($target_currency==''){
            $result = true; //there is custom price but not for the current target currency
       }
    }

    return $result;
}

/**
 * Return the custom price set for this product for the give currency
 * 
 * @param int $product_id  Product ID
 * @param string $target_currency Target currency code
 * 
 * @return array null if no custom price is defined, or array of two items: regular price and sale price 
 */
function scd_get_product_custom_price_for_currency ($product_id, $target_currency)
{
    $regprice = '';
    $saleprice = '';

    // Check if a custom price has been specified by currency
    if (get_post_meta($product_id, 'scd_other_options')) {

        $datas = get_post_meta($product_id, 'scd_other_options');
        $currencyPrice=$datas[0]['currencyPrice'];
        $tab= explode('_', $currencyPrice);
           
        $index= array_search($target_currency, $tab);
        if($index !== FALSE)
        {
            $regprice  = explode('-',$tab[$index+1] )[0];
            $saleprice = explode(',',$tab[$index+3] )[0];
        }  
    } 

    if($saleprice == '')
    {
        $saleprice = $regprice;
    }

    if ( ($saleprice == '') && ($regprice == '') )
        return null;
    else
        return array( floatval($regprice), floatval($saleprice) );

}

/**
 * Get the price of a product 
 * 
 * @param object $product  The product
 * 
 * @return array Array of 3 elements: regular price, sale price and currency. 
 *               Note that the sale price will be equal to the regular price if there is no sale 
 */
function scd_get_product_price($product,$variation_id=null){
    $product_id = $variation_id ?? $product->id;
    // By default we get the prices from the product object.
    // We will override these values if applicable.
    $regprice  = $product->regular_price;
    $saleprice = $product->sale_price;
    if($saleprice == ''){
        $saleprice = $product->price;
    }
    $currency  = get_option('woocommerce_currency'); 
     $datas = get_post_meta($product_id, 'scd_other_options');
         //var_dump($datas);

    // Now check if a custom price has been specified by currency
       if ($datas) {
           //$datas = $datas[0];
       //if(isset($datas['currencyPrice'])){
       $currencyPrice=$datas[0]['currencyPrice'];
        $tab= explode('_', $currencyPrice);

      //  $crr = get_option('scd_currency_options');
      //  $ts = $crr['targetSession'];
        $ts = scd_get_target_currency();
           
        $index= array_search($ts, $tab);
        if($index !== FALSE) {
            $regprice  = explode('-',$tab[$index+1] )[0];
            $saleprice = explode(',',$tab[$index+3] )[0];
            $currency  = $ts;
        }  else {
            if(apply_filters('is_scd_multivendor',false)) {
                $vendor_id = get_post_field( 'post_author', $product_id );
                $user_curr = get_user_meta($vendor_id, 'scd-user-currency');
                if(count($user_curr)>0) {
                    $user_curr = $user_curr[0];
                    $index = array_search($user_curr, $tab);
                    if($index !== FALSE){
                        $regprice = explode('-',$tab[$index+1] )[0];
                        $saleprice = explode(',',$tab[$index+3] )[0];
                        $currency = $user_curr;       
                        //var_dump($user_curr,$saleprice,$regprice);
                        
                    }else{
                        $regprice = apply_filters('scd_convert_subtotal', explode('-',$tab[1+1])[0], $tab[1], $user_curr, 1);
                        $saleprice = apply_filters('scd_convert_subtotal', explode(',',$tab[1+3])[0], $tab[1], $user_curr, 1);
                        $currency = $user_curr;    
                    }
                }
            }
         }
        //}
    }

    // if there is no sale price, set the sale price to the regular price
    if($saleprice == '')
    {
        $saleprice = $regprice;
    }
 
    // Make sure the values returned are float, not string
    $regprice = floatval($regprice);
    $saleprice = floatval($saleprice);
    $prices=array($regprice, $saleprice, $currency);
    return $prices;
}


// Load target currency into the Session object
add_action( 'wp_ajax_nopriv_scd_load_target_currency', 'scd_load_target_currency');
add_action( 'wp_ajax_scd_load_target_currency', 'scd_load_target_currency');
function scd_load_target_currency()
{
    if(!isset($_SESSION))  {  session_start(); }

    $target_currency = scd_sanitize_currency($_POST['target_currency']);
    $_SESSION['scd_target_currency'] = $target_currency;

    die();
}

// Load target currency into the Session object
add_action( 'wp_ajax_nopriv_scd_load_echange_rates', 'scd_load_echange_rates');
add_action( 'wp_ajax_scd_load_echange_rates', 'scd_load_echange_rates');
function scd_load_echange_rates(){
    
    if(isset($_POST['scd_rates'])){
        
    if(!isset($_SESSION))  {  session_start(); }
      $_SESSION['scd_rates_last_update'] = time();
       $rates= str_replace('\\', '', $_POST['scd_rates']);
        $_SESSION['scd_rates'] = json_decode($rates,true);
    die();
 }
}

// Ajax function to send rates to client
add_action( 'wp_ajax_nopriv_scd_ajax_load_rates', 'scd_ajax_load_rates');
add_action( 'wp_ajax_nopriv_scd_ajax_load_rates', 'scd_ajax_load_rates');
function scd_ajax_load_rates()
{
    //$rates = scd_read_rates_from_session();
    $rates= scd_get_exchange_rates();
    echo json_encode($rates);

    die();
}


/**
 * Get the decimal precision indicated in the options
 */
function scd_options_get_decimal_precision ()
{
    $currency_options = get_option('scd_currency_options');
    if (isset($currency_options['decimalNumber'])) {
        $dec = $currency_options['decimalPrecision'];
    }
    else
        $dec = 0;
    
    return $dec;
}

    
add_filter('scd_convert_line_total', 'scd_function_convert_line_total', 10, 3);

// define scd_convert callback 
function scd_function_convert_line_total($total_amount, $base_currency='' , $target_currency='') {
    // session_start();
    // $target_currency = $_SESSION['scd_target_currency'];
    // $scd_rate = floatval($_SESSION['rate']);

    if(empty($target_currency)){
        $target_currency = scd_get_target_currency();
    }

    if(empty($base_currency)){
        $base_currency = get_option( 'woocommerce_currency');
    }

    if ($target_currency == $base_currency)
        return $total_amount;
    
    $scd_rate = floatval(scd_get_conversion_rate($base_currency, $target_currency));
    $myprice = floatval($total_amount) * $scd_rate;
    $myprice = floatval($myprice);

    return $myprice;
}

add_filter('scd_convert_subtotal', 'scd_function_convert_subtotal', 10, 3);

// define scd_convert callback 
function scd_function_convert_subtotal($total_amount, $base_currency='' , $target_currency='', $decimals=2,$inverse=FALSE ) {
    // session_start();
    // $target_currency = $_SESSION['scd_target_currency'];
    // $scd_rate = floatval($_SESSION['rate']);

    if(empty($target_currency)){
        $target_currency = scd_get_target_currency();
    }

    if(empty($base_currency)){
        $base_currency = get_option( 'woocommerce_currency');
    }

    if(empty($decimals)){
        $decimals = scd_options_get_decimal_precision();
    }

    if ($target_currency == $base_currency) {
        $myprice =  floatval($total_amount);
    }
    else
    {
        $scd_rate = floatval(scd_get_conversion_rate($base_currency, $target_currency));
         if($inverse) $scd_rate=1/$scd_rate;
        $myprice = floatval($total_amount) * $scd_rate;
    }
        
    if ($decimals == 0)
        $myprice = ceil($myprice);
    else {
        $myprice = round($myprice, $decimals);
    }

    if ($target_currency == 'JPY') {
        $myprice = round($myprice, 0);
    }

    return $myprice;
}

add_filter('scd_format_subtotal', 'scd_function_format_subtotal', 10, 2);

// define scd_convert callback 
function scd_function_format_subtotal($total_amount, $decimals=1) {
    // session_start();
    // $target_currency = $_SESSION['scd_target_currency'];

    if(empty($decimals)){
        $decimals = scd_options_get_decimal_precision();
    }

    $myprice = floatval($total_amount);

    if ($decimals == 0)
        $myprice = ceil($myprice);
    else {
        $myprice = round($myprice, $decimals);
    }
    
    // $myprice = floatval($myprice);
    if ($target_currency == 'JPY') {
        $myprice = round($myprice, 0);
    }

    return $myprice;
}

add_filter('scd_get_compare_currency', 'scd_function_compare_currency', $priority = 10, $accepted_args = 1);

function scd_function_compare_currency($woocurrency) {

    // session_start();
    // $scd_target_currency = $_SESSION['scd_target_currency'];

    $scd_target_currency = scd_get_target_currency();
    if ($scd_target_currency == '') {
        return $woocurrency;
    } else {
        return $scd_target_currency;
    }
}


function scd_init_filters() {
    $pbc = get_option('scd_currency_options');

    if (isset($pbc['priceByCurrency'])) {
       
   // Change in SCD version 4.4.3 : 
   //       Hooks are not used to modify the html tags anymore. The approach
   //       of using special classes to define the price html elements,
   //       then compute the cart totals in javascript produces wrong totals
   //       in scenarios where some items in cart have custom prices set using SCD
   //       and coupons have to be applied or taxes have to be calculated as a 
   //       percentage of the total.
   //
   //       Instead, we connect hooks to overwrite the regular and sale price used
   //       by WooCommerce and specify the custom price set using SCD. Woocommerce will then
   //       use this price in all its calculations. SCD only has to convert the result and does
   //       not have to try to replicate the cart totals computations.

        // Add filters to hook into woocommerce WC_Product::get_price() functions
        add_filter('woocommerce_product_get_regular_price' , 'scd_overwrite_wc_product_get_regular_price', 10, 2);
        add_filter('woocommerce_product_get_sale_price' , 'scd_overwrite_wc_product_get_sale_price', 10, 2);
        //add_filter('woocommerce_product_get_price' , 'scd_overwrite_wc_product_get_price', 10, 2);
   
        //variations
       add_filter( 'woocommerce_product_variation_get_regular_price', 'scd_overwrite_wc_product_variation_get_regular_price', 12, 2);
       add_filter( 'woocommerce_product_variation_get_sale_price', 'scd_overwrite_wc_product_variation_get_sale_price', 12, 2 );
        //add_filter( 'woocommerce_product_variation_get_price', 'scd_overwrite_wc_product_variation_get_price', 10, 2 );
		
		
		    //Check if scd_for_woo_product_addon is not actived
            if (!(in_array('scd_for_woo_product_addon/index.php', apply_filters('active_plugins', get_option('active_plugins'))))){
                add_filter('woocommerce_product_get_price' , 'scd_overwrite_wc_product_get_price', 10, 2);
                add_filter( 'woocommerce_product_variation_get_price', 'scd_overwrite_wc_product_variation_get_price', 10, 2 );
            }
			//end Check if scd_for_woo_product_addon is not actived
        
         add_filter( 'woocommerce_variable_price_html', 'scd_woocommerce_variable_price_html', 10, 2 );  
    } 

 function scd_woocommerce_variable_price_html( $price, $product ) {
 

  $target_currency = scd_get_target_currency();
  if($target_currency!==get_option('woocommerce_currency',true)){
//$min_reg = $product->get_variation_regular_price( 'min', true );
$min_sale = $product->get_variation_sale_price( 'max', true );
//$max_reg = $product->get_variation_regular_price( 'max', true );
$max_sale = $product->get_variation_sale_price( 'min', true );
$first=true;
 $childrens=$product->get_children();
    $custom_prices=false;
 foreach ($childrens as $variation_id) {
      
    if( scd_is_custom_price_defined_for_currency($variation_id, $target_currency) ){
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($variation_id, $target_currency);
        if($first){
        $min_sale=$saleprice;
        $max_sale=$saleprice;
        $first=false;
        }  else {
        if($min_sale>$saleprice) $min_sale=$saleprice;
        if($max_sale<$saleprice) $max_sale=$saleprice;
        }
        $custom_prices=true;
    }
  }
  if($custom_prices){
 //format the prices
             $args= array(
                'ex_tax_label'       => false,
                'currency'           => '',
                'decimal_separator'  => wc_get_price_decimal_separator(),
                'thousand_separator' => wc_get_price_thousand_separator(),
                'decimals'           => wc_get_price_decimals(),
                'price_format'       => get_woocommerce_price_format(),
            );
           
   $decimals = scd_options_get_decimal_precision();
    $args['currency'] = $target_currency;
    $args['decimals'] = $decimals;
    $args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
    $min_sale = scd_format_converted_price_to_html($min_sale, $args);
     $max_sale = scd_format_converted_price_to_html($max_sale, $args);
     
    $price = sprintf( __( '%1$s - %2$s', 'woocommerce' ),  $min_sale,  $max_sale );
 }
 }
 
return $price;
}

    // Add filter to convert price displayed in HTML markup
    if(!is_admin()){
        if(apply_filters('scd_enable_price_in_vendor_currency',true)){
          add_filter('wc_price', 'scd_convert_price_in_html_markup', 10, 4);
         }
        

        // Add filter to change currency symbol if a custom symbol is set by the user
        if(scd_get_bool_option('scd_general_options', 'overrideCurrencyOptions')){
            add_filter('woocommerce_currency_symbol', 'scd_change_wc_currency_symbol', 10, 2);
        }
    }
    
}

/**
 * Get the target currency set by SCD
 */
function scd_get_target_currency()
{
    if(!headers_sent() && session_status() === PHP_SESSION_NONE){  session_start(); }

    if(!empty($_SESSION['scd_target_currency']))
    {
        $scd_target_currency = $_SESSION['scd_target_currency'];
    }
//    if(!empty(get_option('scd_currency_options')))
//    {
//        $crr = get_option('scd_currency_options');
//        $scd_target_currency = $crr['targetSession'];
//    }
    else
    {
      $scd_target_currency = get_option( 'woocommerce_currency');
   }

    return apply_filters('scd_target_currency',$scd_target_currency);
}


/**  Auction product compatibility
*   This section is use to make correction during rendering and auction product
*   Fore the auction product we dont use the increase the rate
*/

add_action('wp','scd_init_use_without_increase_rate');
function scd_init_use_without_increase_rate(){
    //initialise scd to use rate with increase admin configuration
    if (!isset($_SESSION)) session_start();
    $_SESSION['scd_use_without_increase_rate'] = false;
}

add_action('woocommerce_before_shop_loop_item','scd_woocommerce_before_shop_loop_item',1);
function scd_woocommerce_before_shop_loop_item(){
    //loop to show production in shop
    global $product;
    if (!isset($_SESSION)) session_start();

    if (method_exists( $product, 'get_type') && $product->get_type() == 'auction') {
        $_SESSION['scd_use_without_increase_rate'] = true;
    }else{
        $_SESSION['scd_use_without_increase_rate'] = false;
    }
}

add_filter( 'woocommerce_product_get_price', 'scd_uwa_cs_get_price_html',1, 2);

function scd_uwa_cs_get_price_html( $price, $product ){
    if (!isset($_SESSION)) session_start();
    if ($product->get_type() === "auction"){
        
        $_SESSION['scd_use_without_increase_rate'] = true;
    }else{
        $_SESSION['scd_use_without_increase_rate'] = false;
    }
    return $price;
}

//end Auction product compatibility







/**
 * Get the SCD conversion rate between two currencies
 * 
 * @param string $base_currency     From currency
 * @param string $target_currency   To currency
 * 
 * @return string The conversion rate, or NULL object if the session variable is not set 
 */
function scd_get_conversion_rate ($base_currency, $target_currency)
{
    
    if($base_currency == $target_currency)
    {
        $rate = 1.0;
    }
    else 
    {
        $convert_rates = scd_read_rates_from_session();

        if( $target_currency != false && (array_key_exists($base_currency, $convert_rates) && array_key_exists($target_currency, $convert_rates)))
        {
            if($convert_rates["base"] == $base_currency){
                $rate = floatval($convert_rates[$target_currency]);
            }
            else {
                $rate = floatval($convert_rates[$target_currency]) / floatval($convert_rates[$base_currency]);
            }
        }
        else
        {
            $rate = null;
        }

    }

    // Check if there is a manual override defined for this currency
    $customOptions = scd_get_currency_override_options($target_currency);
    if( (!empty($customOptions)) && ($base_currency == get_option('woocommerce_currency')) ) {
        // If a custom exchange rate has been specified, use it
        if(!empty($customOptions["rate"])){
            $rate = $customOptions["rate"];
        }

        // Auction product compatibility
        if (isset($_SESSION['scd_use_without_increase_rate']) && $_SESSION['scd_use_without_increase_rate']) {

            return $rate;
        }//end Auction product compatibility

        // If an increase on top percentage has been specified, apply it
        if(!empty($customOptions["inc"]) && !empty($rate) ){
            $rate = floatval($rate) * (1 + floatval($customOptions["inc"])/100);
        }
    }
    

    return $rate;
}


function scd_get_conversion_rate_origine ($base_currency, $target_currency)
{
    
    if($base_currency == $target_currency)
    {
        $rate = 1.0;
    }
    else 
    {
        $convert_rates = scd_read_rates_from_session();

        if( $target_currency != false && (array_key_exists($base_currency, $convert_rates) && array_key_exists($target_currency, $convert_rates)))
        {
            if($convert_rates["base"] == $base_currency){
                $rate = floatval($convert_rates[$target_currency]);
            }
            else {
                $rate = floatval($convert_rates[$target_currency]) / floatval($convert_rates[$base_currency]);
            }
        }
        else
        {
            $rate = null;
        }

    }
 
    return $rate;
}

/**
 * Read conversion rates from the Session object
 */

function scd_read_rates_from_session()
{

    if(!headers_sent() && !isset($_SESSION))  {  session_start(); }

    $convert_rates = null;

    if(!empty($_SESSION['scd_rates']) && !empty($_SESSION['scd_rates_last_update']) ) {
        // Check the last time we updated the rates in the Session object.
        // Only use the stored rates if they are less than 12h old.
        $last_session_update = $_SESSION['scd_rates_last_update'];
        $now = time();
        if($now - $last_session_update < (6 * HOUR_IN_SECONDS) ){
            $convert_rates = $_SESSION['scd_rates'];
        }
    }

    // Update the session object if the rates were not stored or were too old
    if(empty($convert_rates))
    {
        $convert_rates = scd_get_exchange_rates();
        if(!empty($convert_rates)){
            $_SESSION['scd_rates_last_update'] = time();
            $_SESSION['scd_rates'] = $convert_rates;
        }
    }
    
    return $convert_rates;
}

define('duree_max_in_min', 5*24*60);
/**
 * Get the manual override options, if any, for a given curency
 * 
 * @param string $currency     Currency
 * 
 * @return string The associated array containing the options, or NULL object if no manual option is defined for this currency 
 *                The array contains the following elements:
 *                   "rate" : The custom exchnage rate to use
 *                   "inc": The increase to apply on top
 *                   "sym": The custom symbol to use
 *                   "pos": the symbol position for display   
 *                 If an element is empty, no override should be performed for this element
 */
function scd_get_currency_override_options($currency){

    $options = null;

    if(scd_get_bool_option('scd_general_options', 'overrideCurrencyOptions')){
        $customCurrOptions = scd_get_option('scd_general_options', 'customCurrencyOptions');

        if(!empty($customCurrOptions)){
            // Options are encoded in JSON format
            $currenciesOptions = json_decode($customCurrOptions, true);
            if(array_key_exists($currency, $currenciesOptions)){
                $options = $currenciesOptions[$currency];
            }
        }
    }

    return $options;
}

/**
 * if the multi currencies payment is available
 * */
if (scd_get_bool_option('scd_general_options', 'multiCurrencyPayment')) {

    /* *
    * Add a hook on order creation to convert the order prices to the target currency
    * before the order is saved in the database.
    */
    if(apply_filters('scd_enable_price_in_vendor_currency',true)){
    add_action( 'woocommerce_checkout_create_order', 'scd_convert_order_prices', 20, 1 );
    }
    /**
     * Convert the prices in the order before it is passed to the payment gateway
     * 
     * @param int $order_id Order id
     */

    function scd_convert_order_prices ($order)
    {
        // Get the woocommerce base currency
        $base_currency = get_option( 'woocommerce_currency');

        // Get the target currency
        $target_currency = scd_get_target_currency();
		
		$rate = scd_get_conversion_rate_origine ($target_currency,$base_currency);
		
		$rate_c = scd_get_conversion_rate ($base_currency, $target_currency);

        if( !scd_is_supported_gateway_for_order_conversion($order->get_payment_method(), $target_currency) )
			//return;
        {
			
            foreach( $order->get_items( array( 'line_item', 'tax', 'shipping', 'fee', 'coupon'  ) ) as $item_id => $item ) 
            {

                // Line items types are products. Convert their price.
                if( $item['type'] === 'line_item' )
                {
                    $product = $item->get_product();
                    $product_id = $product->id;
					
					

                    // Check if SCD uses a price per currency setting for this product
        /*            if(scd_is_custom_price_defined_for_currency($product_id, $target_currency))
                    {
                        // Get the product price from the id
                        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);

                        // SCD uses a custom price per currency setting for this product. 
                        // Recalculate the item totals to save in the order                   
                        $product_price   = apply_filters('scd_convert_subtotal', $saleprice, $target_currency, $target_currency, 1);
                        $product_quantity = (int) $item->get_quantity(); // product Quantity

                        // The new line item price
                        $new_price = $product_price * $product_quantity;

                        // Set the new price
                        $item->set_subtotal( $new_price );   
                        $item->set_total( $new_price );  // If coupons and discounts are applied it is reflected 
                                                         // in the item total price. We will have to reapply coupons.

                        $price_per_currency_used = true;
                    }
                    else
                    {
         */               // No custom price per currency is specified for this product.
                        // Simply convert the item subtotal and total values
                        //$new_price   = apply_filters('scd_convert_subtotal', $item->get_subtotal(), $base_currency, $target_currency, 1);
						
						$new_price = $item->get_subtotal() *  $rate * $rate_c;
						
                        $item->set_subtotal( $new_price ); 

                        //$new_price   = apply_filters('scd_convert_subtotal', $item->get_total(), $base_currency, $target_currency, 1);
						
						$new_price = $item->get_total() * $rate * $rate_c ;
						
                        $item->set_total( $new_price ); 
         //           }
                }
                elseif( $item['type'] === 'shipping' )
                {
                    // For an item of type Shipping, we convert the shipping price
                    //$new_price = apply_filters('scd_convert_subtotal', $item->get_total(), $base_currency, $target_currency, 1);
                    $new_price = $item->get_total() * $rate * $rate_c ;
                    // Set the new price
                    $item->set_total( $new_price );
                }
                elseif( $item['type'] === 'fee' )
                {
                    // For an item of type Fee, we convert the amount
                    //$new_price = apply_filters('scd_convert_subtotal', $item->get_amount(), $base_currency, $target_currency, 1);
                    $new_price = $item->get_amount() * $rate * $rate_c ;
                    // Set the new price
                    $item->set_amount( $new_price );
                }
                elseif( $item['type'] === 'coupon' )
                {
                    // For an item of type Coupon, we convert the amount
                   // $new_price = apply_filters('scd_convert_subtotal', $item->get_discount(), $base_currency, $target_currency, 1);
                    $new_price = $item->get_discount() * $rate * $rate_c;
                    // Set the new price
                    $item->set_discount( $new_price );

                    $coupons_used = true;
                }
                elseif( $item['type'] === 'tax' )
                {
                    // We don't need to convert anything for an item of type Tax. The tax rate is a percentage
                    // and it will be recalculated when we call the function to recalculate order totals.
                }
                
            }


            // Recaclculate order totals
            $order->calculate_totals();
			
			return;
		}

        // Save the payment method in the Session object
        $_SESSION['scd_order_payment_method'] = $order->get_payment_method();

        $price_per_currency_used = false;
        $coupons_used = false;

        if($target_currency != $base_currency) {

            // Loop through Order items and convert the prices as applicable.
            // There are 5 items types : 'line_item', 'tax', 'shipping', 'fee', 'coupon'
            // Notes: 
            // 1- To see documentation for each item type, look for documentation on the class named
            //    WC_Order_Item_xxxx, e.g. WC_Order_Item_Tax,  WC_Order_Item_Shipping, WC_Order_Item_Fee,
            //    WC_Order_Item_Coupon
            // 2- The source code of the method calculate_totals() of class WC_Abstract_Order gives a good idea
            //    what are the the elements that make up the ordern and what property of each item is used
            //    to compute the total price.
            //    See: https://docs.woocommerce.com/wc-apidocs/source-class-WC_Abstract_Order.html 

            foreach( $order->get_items( array( 'line_item', 'tax', 'shipping', 'fee', 'coupon'  ) ) as $item_id => $item ) 
            {

                // Line items types are products. Convert their price.
                if( $item['type'] === 'line_item' )
                {
                    $product = $item->get_product();
                    $product_id = $product->id;

                    // Check if SCD uses a price per currency setting for this product
        /*            if(scd_is_custom_price_defined_for_currency($product_id, $target_currency))
                    {
                        // Get the product price from the id
                        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);

                        // SCD uses a custom price per currency setting for this product. 
                        // Recalculate the item totals to save in the order                   
                        $product_price   = apply_filters('scd_convert_subtotal', $saleprice, $target_currency, $target_currency, 1);
                        $product_quantity = (int) $item->get_quantity(); // product Quantity

                        // The new line item price
                        $new_price = $product_price * $product_quantity;

                        // Set the new price
                        $item->set_subtotal( $new_price );   
                        $item->set_total( $new_price );  // If coupons and discounts are applied it is reflected 
                                                         // in the item total price. We will have to reapply coupons.

                        $price_per_currency_used = true;
                    }
                    else
                    {
         */               // No custom price per currency is specified for this product.
                        // Simply convert the item subtotal and total values
                        $new_price   = apply_filters('scd_convert_subtotal', $item->get_subtotal(), $base_currency, $target_currency, 1);
                        $item->set_subtotal( $new_price ); 

                        $new_price   = apply_filters('scd_convert_subtotal', $item->get_total(), $base_currency, $target_currency, 1);
                        $item->set_total( $new_price ); 
         //           }
                }
                elseif( $item['type'] === 'shipping' )
                {
                    // For an item of type Shipping, we convert the shipping price
                    $new_price = apply_filters('scd_convert_subtotal', $item->get_total(), $base_currency, $target_currency, 1);
                    
                    // Set the new price
                    $item->set_total( $new_price );
                }
                elseif( $item['type'] === 'fee' )
                {
                    // For an item of type Fee, we convert the amount
                    $new_price = apply_filters('scd_convert_subtotal', $item->get_amount(), $base_currency, $target_currency, 1);
                    
                    // Set the new price
                    $item->set_amount( $new_price );
                }
                elseif( $item['type'] === 'coupon' )
                {
                    // For an item of type Coupon, we convert the amount
                    $new_price = apply_filters('scd_convert_subtotal', $item->get_discount(), $base_currency, $target_currency, 1);
                    
                    // Set the new price
                    $item->set_discount( $new_price );

                    $coupons_used = true;
                }
                elseif( $item['type'] === 'tax' )
                {
                    // We don't need to convert anything for an item of type Tax. The tax rate is a percentage
                    // and it will be recalculated when we call the function to recalculate order totals.
                }
                
            }

            // Update the order curency
            $order->set_currency($target_currency);

            // Recaclculate order totals
            $order->calculate_totals();

        }

    }

    
    /**
     * Check if the order payment method is among the supported gateways
     */
    function scd_is_supported_gateway_for_order_conversion($payment_method, $currency)
    {
        $supported_gateways = array ('paypal',          // Paypal Standard
                                    'ppec_paypal',      // Paypal Express Checkout
                                    'pumcp',            // PayUMoney
                                    'payumbolt',        // PayUMoney Bolt
                                    'payu_in',          // PayUMoney
                                    'stripe',           // Stripe
                                    'cod',               //csh on delivery
									'bacs',              // Direct bank Transfert
                                    'payzen',            // Payzen
									'cardcom',           // Cardcom
									'wcfac',            // WooCommerce First Atlantic Commerce Payment Gateway
									'tbz_rave',          // flutterwave
									'woo-mercado-pago-basic',//mercadopago
                                    'woo-mercado-pago-custom',//mercadopago
                                    'woo-mercado-pago-ticket',//mercadopago
									'mangopay',          // Mangopay
									'ipay-ghana-wc-payment');  //ipayG        
									
        // Check if the payment method is in the list of suppported gateways.
        // To support all stripe payment variations, check for the stripe_ prefix.
        $is_supported = (in_array($payment_method, $supported_gateways) || (substr($payment_method, 0, 7) === 'stripe_') || (substr($payment_method, 0, 6) === 'payzen'));

        // If the payment method is paypal, check that the currency is supported
        if($is_supported && ($payment_method == 'paypal' || $payment_method == 'ppec_paypal') )
        {
            $is_supported = scd_is_paypal_supported_currency($currency);
        }
		
		// If the payment method is cardcom, check that the currency is supported
        if($is_supported && ($payment_method == 'cardcom') )
        {
            $is_supported = scd_is_paypal_supported_currency($currency);
        }
		
		//if the payement metho is mercado, check that currency is supported
        if($is_supported && (in_array($payment_method,array('woo-mercado-pago-basic','woo-mercado-pago-custom','woo-mercado-pago-ticket'))))
        {
            $is_supported = scd_is_mercadopago_supported_currency($currency);
        }
		
		// If the payment method is mangopay, check that the currency is supported
        if($is_supported && ($payment_method == 'mangopay') )
        {
            $is_supported = scd_is_mangopay_supported_currency($currency);
        }
		
		// If the payment method is WooCommerce First Atlantic Commerce Payment Gateway, check that the currency is supported
        if($is_supported && ($payment_method == 'wcfac') )
        {
            $is_supported = scd_is_wcfac_supported_currency($currency);
        }
		
		// If the payment method is flutterwave, check that the currency is supported
        if($is_supported && ($payment_method == 'tbz_rave') )
        {
            $is_supported = scd_is_flutterwave_supported_currency($currency);
        }

        return $is_supported;
    }

    /**
     * Check if the currency is among the supported currencies for paypal
     */
    function scd_is_paypal_supported_currency ($currency)
    {
        // List of paypal supported currencies
        // Obtained from the file: /woocommerce/includes/gateways/paypal/class-wc-gateway-paypal.php ,
        //                        function is_valid_for_use()
        $supported_currencies = array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 
                                       'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 
                                       'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 
                                       'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 
                                       'RMB', 'RUB', 'INR' );

        return in_array($currency, $supported_currencies);
    }
	
	 /**
     * Check if the currency is among the supported currencies for cardcom
     */
	
	function scd_is_cardcom_supported_currency ($currency)
    {
        // List of paypal supported currencies
        // Obtained from the file: /woocommerce/includes/gateways/paypal/class-wc-gateway-paypal.php ,
        //                        function is_valid_for_use()
        $supported_currencies = array( 'EUR', 'USD', 'ILS', 'GBP', 'COD' );

        return in_array($currency, $supported_currencies);
    }
	
	 /**
     * Check if the currency is among the supported currencies for mercadopago
     */
    function scd_is_mercadopago_supported_currency ($currency)
    {
        // List of paypal supported currencies
        // Obtained from the file: /woocommerce/includes/gateways/paypal/class-wc-gateway-paypal.php ,
        //                        function is_valid_for_use()
        $supported_currencies = array('ARS','BRL','COP','CLP','MXN','VEF','PEN','UYU');

        return in_array($currency, $supported_currencies);
    }
	
	function scd_is_mangopay_supported_currency ($currency)
    {
        // List of mangopay supported currencies
        // Obtained from the file: /woocommerce/includes/gateways/paypal/class-wc-gateway-paypal.php ,
        //                        function is_valid_for_use()
        $supported_currencies = array( 'EUR', 'GBP', 'USD', 'CHF', 'NOK', 'PLN', 'SEK', 'DKK', 'CAD', 'ZAR');
      /* $supported_currencies = array( 'AUD','CAD', 
                                        'USD', 'EUR', 'NOK', 
                                       'CZK', 'DKK',
                                       'PLN', 'SEK', 'CHF', 'GBP' );*/

        return in_array($currency, $supported_currencies);
        
    }
	
	function scd_is_flutterwave_supported_currency($currency)
    {
        // List of paypal supported currencies
       
        $supported_currencies = array('NGN', 'USD', 'EUR', 'GBP', 'KES', 'GHS', 'ZAR');

        return in_array($currency, $supported_currencies);
    }
	
	/**
     * Check if the currency is among the supported currencies for WooCommerce First Atlantic Commerce Payment Gateway
    */
    function scd_is_wcfac_supported_currency ($currency)
    {
        // List of wcfac supported currencies
      
        $supported_currencies = array( 'AFN', 'ALL', 'DZD', 'USD', 'EUR', 'AOA', 
                                       'XCD', 'ARS', 'AMD', 'AWG', 'AUD', 'AZN', 
                                       'BSD', 'BHD', 'BDT', 'BBD', 'BYR', 'BZD', 
                                       'XOF', 'INR', 'BMD', 'BOB', 'BAM', 'BWP',
                                       'NOK', 'BRL', 'BND', 'BGN', 'BIF', 'KHR', 
                                       'XAF', 'CAD', 'CVE', 'KYD', 'CLP', 'CNY', 
                                       'COP', 'KMF', 'NZD', 'CRC', 'HRK', 'CUP', 
                                       'ANG', 'CZK', 'DKK', 'DJF', 'DOP', 'EGP',
                                       'ERN', 'ETB', 'FKP', 'FJD', 'XPF', 'GMD', 
                                       'GEL', 'GHS', 'GIP', 'GTQ', 'GBP', 'GNF', 
                                       'GYD', 'HNL', 'HKD', 'HUF', 'ISK', 'IDR', 
                                       'IRR', 'IQD', 'ILS', 'JMD', 'JPY', 'JOD',
                                       'KZT', 'KES', 'KPW', 'KRW', 'KWD', 'KGS', 
                                       'LAK', 'LBP', 'ZAR', 'LRD', 'LYD', 'CHF', 
                                       'MOP', 'MKD', 'MGA', 'MWK', 'MYR', 'MVR', 
                                       'MRO', 'MUR', 'MXN', 'MDL', 'MNT', 'MAD',									   
                                       'MZN', 'MMK', 'NPR', 'NIO', 'NGN', 'OMR', 
                                       'PHP', 'PLN', 'QAR', 'RON', 'RUB', 'RWF', 
                                       'SHP', 'WST', 'STD', 'SAR', 'RSD', 'SCR', 
                                       'SLL', 'SGD', 'SBD', 'SOS', 'SSP', 'LKR', 
                                       'SDG', 'SRD', 'SZL', 'SEK', 'SYP', 'TWD',
                                       'TJS', 'TZS', 'THB', 'TOP', 'TTD', 'TND', 
                                       'TRY', 'TMT', 'UGX', 'UAH', 'AED', 'UYU', 
                                       'UZS', 'VUV', 'VEF', 'VND', 'YER', 'ZMW', 
                                       'ZWL', 'PKR', 'PGK', 'PYG', 'PEN');

        return in_array($currency, $supported_currencies);
    }

    /**
     * Perform action right before the newly created order is saved
     * 
     * NOTE - UNUSED: Since version 4.4.1 this function is not used anymore.
     * 
     * @param WC_order $order  WC_Order item. 
     */
    function scd_on_wc_checkout_create_order( $order ) {

        // If the payment method is PayUMoney, we use this hook
        // to convert the order total and the order currency before the order
        // is passed to the payment portal.

        // Name asscociated with the PayUMoney payment method
        // Unfortunately this can change with each new version of PayUMoney plugin
        // To get the payment method, look inside the plugin code, lcate the function
        // "_construct()", and get the value used for the field "$this->id"
        $payumoney_pumcp_gateway_id = 'pumcp';
        $payumoney_payumbolt_gateway_id = 'payumbolt';
        $payumoney_payuin_gateway_id = 'payu_in';

        if(($order->get_payment_method() == $payumoney_pumcp_gateway_id)  
            || ($order->get_payment_method() == $payumoney_payumbolt_gateway_id)
            || ($order->get_payment_method() == $payumoney_payuin_gateway_id) )
        {
            // Get the woocommerce base currency
            $base_currency = get_option( 'woocommerce_currency');

            // Get the SCD target currency
            $target_currency = scd_get_target_currency();

            // if (( $order->get_currency() == $base_currency) && ($base_currency != $target_currency)) {  
            if ($base_currency != $target_currency) {  
                // Get order total
                $total = $order->get_total();

                // TODO: update price for each item?

                // Update total 
                //   $new_total = $total * $scd_rate;
                $new_total = apply_filters('scd_convert_subtotal', $total);

                // Set the new calculated total
                $order->set_total( $new_total );

                // update currency
                $order->set_currency($target_currency);
            }
        }
    }
    
   

    /* add filter to get paypal arguments before gateway */
 //   add_filter('woocommerce_paypal_args', 'scd_change_paypal_args', 10, 2 );
    add_filter('woocommerce_paypal_args', 'scd_change_paypal_currency_code', 10, 2 );

    /**
     * Change paypal currency code before paypal payment gateway
     * 
     * @param array    $paypal_args : paypal gateway arguments
     * @param WC_Order $order: WC_Order item
     * 
     * @return array   updated paypal arguments
     * 
     */ 
    function scd_change_paypal_currency_code($paypal_args, $order) 
    { 
        $paypal_args['currency_code'] = $order->get_currency();  
        return $paypal_args;
    }

    /**
     * Change paypal arguments before paypal payment gateway
     * 
     * NOTE - UNUSED: Since version 4.4.1 this function is not used anymore. The order items prices 
     * are now cconverted at order creation, so paypal now gets the converted prices by default.
     * 
     * @param array    $paypal_args : paypal gateway arguments
     * @param WC_Order $order: WC_Order item
     * 
     * @return string updated paypal 
     * 
     * Note: for list of paypal arguments, 
     *       see https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/#individual-items-variables
     */       
    function scd_change_paypal_args($paypal_args, $order) {  

        // Get the woocommerce base currency
        $base_currency = get_option( 'woocommerce_currency');

        // Get the target currency
        $target_currency = scd_get_target_currency();

//        if ( $paypal_args['currency_code'] == $base_currency) {  
            if($target_currency != $base_currency) {
                // Change currency to target currency 
                $paypal_args['currency_code'] = $target_currency; 

                // Convert each item from base currency to target currency
                $i = 1;  
                while (isset($paypal_args['amount_' . $i])) { 

                    $update_amount_flag = true;

                    // Get the product id to which the item belongs
                    $product_id = scd_find_product_in_cart_by_name($paypal_args['item_name_' . $i],  $paypal_args['quantity_' . $i]);

                    if($product_id >= 0)
                    {
                        // Check if SCD uses a price per currency setting for this product
                        if(scd_is_custom_price_defined_for_currency($product_id, $target_currency))
                        {
                            // Get the product price from the id
                           list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);
                                
                            // Set the paypal amount argument for this item
                            $paypal_args["amount_".$i]  = apply_filters('scd_convert_subtotal', $saleprice, $target_currency, $target_currency, 1);
                            $update_amount_flag = false;
                        }
                    }

                    if($update_amount_flag)
                    {
                        // Convert the amount of each item from base currency to target currency   
                        $paypal_args['amount_' . $i] = apply_filters('scd_convert_subtotal', $paypal_args['amount_' . $i], $base_currency, $target_currency, 1);

                        // If a discount amount is specified, convert it
                        if(isset($paypal_args['discount_amount_' . $i])){
                            $paypal_args['discount_amount_' . $i] = apply_filters('scd_convert_subtotal', $paypal_args['discount_amount_' . $i], $base_currency, $target_currency, 1);
                        }

                        if(isset($paypal_args['discount_amount2_' . $i])){
                            $paypal_args['discount_amount2_' . $i] = apply_filters('scd_convert_subtotal', $paypal_args['discount_amount2_' . $i], $base_currency, $target_currency, 1);
                        }
                    }

                    // If a tax amount is specified, convert it
                    if(isset($paypal_args['tax_' . $i])){
                        $paypal_args['tax_' . $i] = apply_filters('scd_convert_subtotal', $paypal_args['tax_' . $i], $base_currency, $target_currency, 1);
                    }

                    // If a shipping amount is specified, convert it
                    if(isset($paypal_args['shipping_' . $i])){
                        $paypal_args['shipping_' . $i] = apply_filters('scd_convert_subtotal', $paypal_args['shipping_' . $i], $base_currency, $target_currency, 1);
                    }

                    if(isset($paypal_args['shipping2_' . $i])){
                        $paypal_args['shipping2_' . $i] = apply_filters('scd_convert_subtotal', $paypal_args['shipping2_' . $i], $base_currency, $target_currency, 1);
                    }

                    ++$i;  
                }  

                // If the arguments tax_cart and discount_amount_cart are defined, convert them
                if(isset($paypal_args['discount_amount_cart'])){
                    $paypal_args['discount_amount_cart'] = apply_filters('scd_convert_subtotal', $paypal_args['discount_amount_cart'], $base_currency, $target_currency, 1);
                }

                if(isset($paypal_args['tax_cart'])){
                    $paypal_args['tax_cart'] = apply_filters('scd_convert_subtotal', $paypal_args['tax_cart'], $base_currency, $target_currency, 1);
                }

            }
//        }  
        return $paypal_args;  
    } 

    // Add filter to get paypal express checkout arguments before they are passed to Paypal.
    add_filter('woocommerce_paypal_express_checkout_get_details', 'scd_change_paypal_express_checkout_details', 10, 1 );

    /**
     * Change Paypal express checkout arguments before they are passed to Paypal
     * 
     * @param array   $details : order details to b passed to Paypal Checkout
     * 
     * @return string updated details 
     * 
     * Note: 1- Unfortunately the currency is not part of the details array. We cannot change the paypal currency 
     *          in this callback
     *       2- There is no documentation on this hook. It is invoked from :
     *          \plugins\woocommerce-gateway-paypal-express-checkout\includes\class-wc-gateway-ppec-client.php
     * 
     */
    function scd_change_paypal_express_checkout_details ($details)
    {
        // The $details parameter is an array containing the fields
        // - items[] : The array of items in the cart. Each array has the fields: name, description, amount, quntity
        // - total_item_amount : basically computed from the items quantity and amount
        // - order_tax
        // - shipping
        // - ship_discount_amount : used by paypal checkout to adjust their total
        // - order_total : total_item_amount + order_tax + shipping - ship_discount_amount
        // - shipping_address
        // - email

        if(!scd_is_paypal_supported_currency ( scd_get_target_currency() ))
        {
            // Currency not supported by paypal. We will keep the base currency
            return $details;
        }

        // If we are on the checkout page and the Paypal Express Checkout payment was not started
        // from the checkout page, do nothing, the order has already been 
        // converted on order creation using scd_convert_order_prices(), and normally the payment 
        // gateway should receive the correct amounts.
        if( is_checkout() && (!scd_is_ppec_started_from_checkout_page()) ) 
        {
            // Do nothing
        }
        else
        {
            // However, since Paypal Express Checkout is first called before even creating the order, 
            // we have to convert the amounts if we are not on the checkout page yet or if the
            // payment is started directly from the checkout page. 
            // Note : When Paypal Checkout button is called on a page that is not the checkout page,
            //        the checkout details are gathered and sent to the payment gateway without creating an order first. 
            //        The client is then invited to sign in into his paypal account, and it is after that sign-in
            //        and first confirmaton that the client is directed to the checkout page. 
            //        After the client confirms the payment on the checkout page, the order is created, 
            //        the checkout details are formed and sent to the payment gateway   

            // Get the woocommerce base currency
            $base_currency = get_option('woocommerce_currency');

            // Get the target currency
            $target_currency = scd_get_target_currency();

            if($target_currency != $base_currency) {
                
                $total_item_amount = 0;
                $order_total = 0;

                // Convert the prices of the items and compute the total item amount 
                if ( ! empty( $details['items'] ) ) {
                    foreach($details['items']  as &$item){
                        // Convert item price
                        $item['amount'] = apply_filters('scd_convert_subtotal', $item['amount'], $base_currency, $target_currency, 1);
                        
                        // Get the product id to which the item belongs
                        $product_id = scd_find_product_in_cart_by_name($item['name'],  $item['quantity'], $base_currency, $target_currency, 1);

                        if($product_id >= 0)
                        {
                            // Check if SCD uses a price per currency setting for this product
                            if(scd_is_custom_price_defined_for_currency($product_id, $target_currency))
                            {
                                // Get the product price from the id
                               list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);

                                // Overwrite the amount argument for this item
                                $item['amount'] = apply_filters('scd_convert_subtotal', $saleprice , $target_currency, $target_currency, 1);
                            }
                        }

                        $total_item_amount += $item['quantity'] * $item['amount'];
                    }
                }
                else {
                    // If the array of items is empty and the total item amount is not zero, simply convert the value
                    if($details['total_item_amount'] != 0)
                    {
                        $total_item_amount = apply_filters('scd_convert_subtotal', $details['total_item_amount'] , $base_currency, $target_currency, 1);
                    }
                }

                // Set the total line amount
                $details['total_item_amount'] = round($total_item_amount, 2);

                // Convert the order tax
                $details['order_tax'] = apply_filters('scd_convert_subtotal', $details['order_tax'] , $base_currency, $target_currency, 1);

                // Convert the shipping cost
                $details['shipping'] = apply_filters('scd_convert_subtotal', $details['shipping'] );
                $details['ship_discount_amount'] = apply_filters('scd_convert_subtotal', $details['ship_discount_amount'] , $base_currency, $target_currency, 1);
                
                // Set the order total
                $details['order_total'] = round( $details['total_item_amount'] + $details['order_tax'] + $details['shipping'], 2);
                if($details['order_total'] > $details['ship_discount_amount']){
                    $details['order_total'] -= $details['ship_discount_amount'];
                }
            }

        } // end if (!is_checkout())

        // Save the payment method in the Session object
        scd_set_payment_method_paypal_express_checkout(0);

        return $details;
    }

    /**
     * Determine if the Paypal Express Checkout Payment was started from the checkout page.
     * 
     * Note: This function comes from the PPEC plugin source code, file class-wc-gateway-ppec-checkout-handler.php
     */

    function scd_is_ppec_started_from_checkout_page() {
		if ( ! is_object( WC()->session ) ) {
			return false;
		}

		$session = WC()->session->get( 'paypal' );

		return ( ! $session->checkout_completed );
	}

    // Add filter to set the payment method to paypal express checkout.
    // I could not find a suitable filter , but this one seems to do the work
    add_filter('woocommerce_paypal_express_checkout_allow_guests', 'scd_set_payment_method_paypal_express_checkout', 10, 1 );

    function scd_set_payment_method_paypal_express_checkout ($flag)
    {
        if(scd_is_paypal_supported_currency ( scd_get_target_currency() ))
        {
            // Save the payment method in the Session object
            $_SESSION['scd_order_payment_method'] = 'ppec_paypal';
        }
        return $flag;
    }

    // Add filter to change the curency code returned by a call to get_woocommerce_currency()
    // Note : When the function get_woocommerce_currency() is invoked, the filter is applied by woocommerce,
    //        like this: return apply_filters( 'woocommerce_currency', get_option( 'woocommerce_currency' ) );
    add_filter('woocommerce_currency', 'scd_change_get_woocommerce_currency', 10, 1);

    /**
     * Change the currency returned by the function get_woocommerce_currency()
     * 
     * @param string $woocurrency Base currency
     */
    function scd_change_get_woocommerce_currency( $woocurrency ) {
        // Note: For now this function is only used to modify the arguments passed to the
        //       Paypal Express checkout gateway or the Payzen payment gateway. To prevent the filter from being applied all
        //       the time, we will check if the cart is empty and check if a payment method
        //       is specified in the Session variable

        $currency = $woocurrency;
         
		 // include mercadopago key on payment order session
		 
        if(isset($_SESSION['scd_order_payment_method']) && 
          (($_SESSION['scd_order_payment_method'] == 'ppec_paypal')|| ($_SESSION['scd_order_payment_method'] == 'tbz_rave') || ($_SESSION['scd_order_payment_method'] == 'wcfac') || ($_SESSION['scd_order_payment_method'] == 'mangopay') || substr($_SESSION['scd_order_payment_method'], 0, 6)=="payzen"  || in_array($_SESSION['scd_order_payment_method'], array('woo-mercado-pago-basic','woo-mercado-pago-custom','woo-mercado-pago-ticket'))))
        {
            $current_cart = WC()->cart;
            
            if( (!empty($current_cart)) && 
                (!$current_cart->is_empty()) ) // Check if the cart object exists and the cart is not empty
            {
                $currency = scd_get_target_currency();
            }
            else
            {
                unset($_SESSION['scd_order_payment_method']);
            }
        }
         

        return $currency;
    }

    // Add actions to clear payment method session variable when the payment is processed or cancelled
    add_action('woocommerce_order_status_pending_to_processing', 'scd_clear_payment_method', 10, 0);
    add_action('woocommerce_order_status_pending_to_cancelled', 'scd_clear_payment_method', 10, 0);
    add_action('woocommerce_order_status_pending_to_on-hold', 'scd_clear_payment_method', 10, 0);

    /**
     * Clear the payment method session attribute
     */
    function scd_clear_payment_method()
    {
        if(isset($_SESSION['scd_order_payment_method']))
        {
            unset($_SESSION['scd_order_payment_method']);
        }
    }

    /**
     * Find a product from the item name in cart
     * 
     * @param string $name  Item Name
     * @param int $quantity Item quantity
     */
    function scd_find_product_in_cart_by_name ($name, $quantity)
    {
        $product_id = -1;
        $items = WC()->cart->get_cart();
        foreach($items as $item => $values){
            if(($values['data']->get_title() == $name) && ($values['quantity'] == $quantity))
            {
                $product_id = $values['data']->get_id();       
            }
        }

        return $product_id;
    }

} // end if (scd_get_bool_option('scd_general_options', 'multiCurrencyPayment')) {


$scd_op=  get_option('scd_currency_options',true);
 
if(isset($scd_op['displayCurrencyMenu']) && !empty($scd_op['displayCurrencyMenu'])){
add_filter( 'wp_nav_menu_objects', 'scd_add_currency_menu', 10, 2 );    
}

function scd_add_currency_menu( $items, $args ) {
    
    $options=  get_option('scd_currency_options',true);
    $locs=  isset($options['menuLocation'])?$options['menuLocation']:'';
    $locations=  explode(',', $locs);
    //$current_currency= scd_get_target_currency();
   // $currency_symb=get_woocommerce_currency_symbol($current_currency);
    
    if(in_array($args->theme_location, $locations) || $locs=='alllocations'){
    $new_links = array();
$options=  get_option('scd_currency_options',true);

    $label = isset($options['menuTitle'])?$options['menuTitle']:'Currency';  
    $scd_db_id=9991;
    // Create a nav_menu_item object
    $item = array(
        'title'            => $label, //.': '.$current_currency.'-'.$currency_symb
        'menu_item_parent' => 0,
        'ID'               => 'scd-curr-menu',
        'db_id'            => $scd_db_id,
        'url'              => '#',
        'classes'          => array( 'menu-item','scd-curr-menu','menu-item-has-children','dropdown','no-hover')
    );

    $new_links[] = (object) $item; // Add the new menu item to our array
        
    // insert item
    $position = isset($options['menuPosition'])?$options['menuPosition']:0;
    $position=$position+1;
    array_splice( $items, $position, 0, $new_links );

    
    //submenu of user currencies
    
        $new_links = array();

    
    if($options['userCurrencyChoice']=='allcurrencies'){
    foreach (scd_get_list_currencies() as $key => $val) {
        $item = array(
        'title'            => '<img src="' .scd_flag_currency_display($key). '"> &nbsp;' .$key.'('.get_woocommerce_currency_symbol($key).')',
        'menu_item_parent' => $scd_db_id,
        'ID'               => $key,
        'db_id'            => '',
        'url'              => '#',
        'classes'          => array( 'menu-item','scd-curr-item' )
    );

     $items[]= (object) $item;
        
     }
    }  else {
       $tabcurr=  explode(',', $options['userCurrencyChoice']);
       foreach ($tabcurr as $cur) {
               $item = array(
        'title'            => '<img src="' .scd_flag_currency_display($cur). '"> &nbsp;' .$cur.'('.get_woocommerce_currency_symbol($cur).')',
        'menu_item_parent' => $scd_db_id,
        'ID'               => $cur,
        'db_id'            => '',
        'url'              => '#',
        'classes'          => array( 'menu-item','scd-curr-item' )
    );

     $items[]= (object) $item;
    
     }
    }
}      
    return $items;
}

//---------------------------return flag according to the currency code 

function scd_flag_currency_display($curr) {
	
	$flags_path = plugins_url('images/flags/'.$curr.'.png', __FILE__ );
	
	return $flags_path; 
	
}

//-----------end


function scd_widget_display() {
   //echo '<div class="widget widget_scd_widget">';
    ?>
<form method="POST">
    <input type="hidden" name="targetSessionName1" id="targetSessionName1" value="">
    <div id="endwid1" class="scd_widget1">
        <div class="country-select inside">
            <input id="scd_widget_selector1" type="text" readonly="readonly">
            <div class="flag-dropdown">
                <ul class="country-list1 country-list hide" style="width: 358px;">
                    <?php
                        foreach (scd_get_list_currencies() as $key => $value) {
                            echo '<li class="country"  data-currency-code="'.$key.'">
                            <div class="flag"></div>
                            <span class="country-name">'.$key.' ('.$value.')</span>
                        </li>';
                        }
                        ?>
                </ul>
            </div>
        </div>
        <label for="scd_widget_selector" style="display:none;">Select a country here...</label>
    </div>
</form>


<?php
}



function free_features_date(){
    $reste = 1;
    $text = "";
    if(!apply_filters('is_premium_valid_licence',false)){
        $now = new DateTime('now');
        $fist_install_date = get_option('scd_first_install_date');
        $reste = 0;
        $text = "";
        if($fist_install_date){
            $interval = $fist_install_date->diff($now);
            
            //
            $temps_ecoule = $interval->format('%a')*24*60 + $interval->format('%h')*60 + $interval->format('%i');//difference en minute

            $reste = duree_max_in_min - $temps_ecoule;
            $text = "";
            if($reste <= 0 ){//temps ecouler
                /*$opts = get_option('scd_general_options');
                $opts['overrideCurrencyOptions'] = "";
                update_option('scd_general_options',$opts);*/
                $text = "<span style='color:red;'>You can no longer use this feature. Please get one of our premium variants to use it, </span><a href='https://www.gajelabs.com/our-products-page/' target='__blank'> Here. </a>";
            }else if($reste >0 && $reste <= 60){//moins d'une heure
                $text = "<span style='color:orange;'>You have less than ".$reste." minutes to enjoy this feature.  Please get one of our premium variants to enjoy it indefinitely, </span> <a href='https://www.gajelabs.com/our-products-page/' target='__blank'> Here. </a>";
            }else if($reste > 60 && $reste <= 24*60){//moins d'une journee
                $text = "<span style='color:blue;'>You have less than ".round($reste/60)." hours left to enjoy this feature. Please get one of our premium variants to enjoy it indefinitely,</span> <a href='https://www.gajelabs.com/our-products-page/' target='__blank'> Here. </a>";
            }else{//nombre de jour qui reste
                $text = "<span style='color:black;'>You have less than ".round($reste/(24*60))." days to enjoy this feature. Please get one of our premium variants to enjoy it indefinitely,</span> <a href='https://www.gajelabs.com/our-products-page/' target='__blank'> Here. </a>"  ; 
            }
        }
    }
    return  array('reste' =>  $reste, 'text' => $text,);
}


add_filter( 'woocommerce_currencies', 'add_zimbabwean_currency' );

function add_zimbabwean_currency( $currencies ) {
     $currencies['ZWL'] = __( 'Zimbabwean', 'woocommerce' );
     return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_zimbabwean_currency_symbol', 10, 2);

function add_zimbabwean_currency_symbol( $currency_symbol, $currency ) {
     if( $currency == 'ZWL' ) {
          $currency_symbol = 'Z$'; 
     }
     return $currency_symbol;
}

//Write price in minicart in case of Rigid Child theme activate

$theme = wp_get_theme(); // gets the current theme

if ( 'Rigid Child' == $theme->name || 'Rigid' == $theme->parent_theme ) {
    add_action('woocommerce_before_mini_cart_contents','scd_minicart',10,2);
}

function scd_minicart(){
    $scd_subtotal = 0;
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price',get_woocommerce_currency_symbol(scd_get_target_currency()) .' '. round(apply_filters('scd_convert_subtotal',  $_product->price, $base_currency, $target_currency, scd_options_get_decimal_precision()),scd_options_get_decimal_precision()), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
<li
    class="scd_woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
    <?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_attr__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						),
						$cart_item_key
					);
					?>
    <?php if ( empty( $product_permalink ) ) : ?>
    <?php echo $thumbnail . $product_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <?php else : ?>
    <a href="<?php echo esc_url( $product_permalink ); ?>">
        <?php echo $thumbnail . $product_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </a>
    <?php endif; ?>
    <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</li>
<?php
				$scd_subtotal  = $scd_subtotal + ($cart_item['quantity'] * round(apply_filters('scd_convert_subtotal',  $_product->price, $base_currency, $target_currency, scd_options_get_decimal_precision()),scd_options_get_decimal_precision()));
			}
		}
		    ?>
<script>
jQuery(document).ready(function() {
    jQuery('.woocommerce-mini-cart-item').remove();
    jQuery('.woocommerce-mini-cart__total.total').html(
        "\n\t\t<strong>Subtotal&nbsp;:</strong> <span class=\"woocommerce-Price-amount amount scd-converted\"><span class=\"woocommerce-Price-currencySymbol\">" +
        scd_get_currency_symbol(scd_getTargetCurrency()).symbol +
        "</span> <?php echo $scd_subtotal ?> </span>\t");
});
</script>
<?php
}
//include'scd_customprices.php';



/***********************************************************
compatibility with IpayG Payment Gateway 
*********************************************************************/

/*apply_filters( 'woocommerce_get_formatted_order_total','scd_woocommerce_get_formatted_order_total',999,2);
function scd_woocommerce_get_formatted_order_total($formatted_total, $this_order){
    $formatted_total = wc_price( $this_order->get_total(), array( 'currency' => scd_get_target_currency() ) );
    return $formatted_total;
}
*/

add_action('woocommerce_order_item_meta_start','scd_woocommerce_order_item_meta_start',1,4);
function scd_woocommerce_order_item_meta_start($item_id, $item, $order, $flag){
    if(!isset($_SESSION))  {  session_start(); }
    $_SESSION['scd_order_payment_method'] = $order->get_payment_method();
}
/*
add_action('woocommerce_review_order_before_order_total','scd_woocommerce_review_order_before_order_total',1);
function scd_woocommerce_review_order_before_order_total(){
    if(!isset($_SESSION))  {  session_start(); }
    $_SESSION['scd_order_payment_method'] = 
}
*/

add_action( 'woocommerce_receipt_ipay-ghana-wc-payment', 'scd_pay_for_order',1,1);//priority must be less than 10 to work suitely
function scd_pay_for_order( $order_id ) {
    
    echo '<p>' . __( 'Thank you for placing your order with us.', '' ) . '</p>';
    echo '<p>' . __( 'You will be redirected to iPay Ghana Payment Gateway checkout page so as to complete your payment.', '' ) . '</p>';
    echo scd_generate_ipay_ghana_wc_checkout_form( $order_id );
}

function scd_generate_ipay_ghana_wc_checkout_form( $order_id ) {
    $scd_ipay = new Ipay_Ghana_WC_Payment_Gateway();
    remove_action('woocommerce_receipt_' . $scd_ipay->id,array($scd_ipay,'pay_for_order'));
    global $items_array;
    
    $order = new WC_Order( $order_id );
    $items = $order->get_items();
    
    if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        foreach ( $items as $item ) {
            $items_array[] = $item['name'];
        }
    } else {
        foreach ( $items as $item ) {
            $items_array[] = $item->get_name();
        }
    }
    
    $list_items = implode( ', ', $items_array );
    
    $order->add_order_note( __( 'Order placed successfully; customer has been redirected to the iPay Ghana Payment Gateway checkout page.', '' ) );
    $order->update_status( 'on-hold', __( 'Awaiting Mobile Money payment.<br>', '' ) );
    WC()->cart->empty_cart();
    wc_enqueue_js( 'jQuery( "#submit-payload-to-ipay-ghana-wc-payment-gateway-checkout-url" ).click();' );
    $rate = scd_get_conversion_rate($order->get_currency(),"GHS");

    return  '<form action="' . 'https://manage.ipaygh.com/gateway/checkout' . '" method="post" id="ipay-ghana-wc-payment-gateway-checkout-url-form" target="_top">
    <input type="hidden" name="merchant_key" value="' . esc_attr( $scd_ipay->get_option( 'merchant_key' ) ) . '">
    <input type="text" name="currency" value="' . $order->get_currency() . '">
    <input type="hidden" name="extra_mobile" value="' . $order->get_billing_phone() . '">
    <input type="hidden" name="extra_email" value="' . $order->get_billing_email() . '">
    <input type="hidden" name="description" value="' . '<strong>********************<br> TOTAL ORDER </br>'.$order->get_currency() .' '.$order->get_total(). '<br>********************</br>YOUR TOTAL ORDER IS EQUIVALENT TO </br>' .'">
    <input type="hidden" name="success_url" value="' . esc_url( $scd_ipay->get_option( 'success_url' ) ) . '">
    <input type="hidden" name="cancelled_url" value="' . esc_url( $scd_ipay->get_option( 'cancelled_url' ) ) . '">
    <input type="hidden" name="invoice_id" value="' . str_replace( '#', '', $order->get_order_number() ) . '">
    <input type="hidden" name="total" value="' . $rate*$order->get_total() . '">
    <input type="hidden" name="source" value="WOOCOMMERCE">
    <input type="hidden" name="extra_project_name" value="' . $scd_ipay->get_option( 'extra_project_name' ) . '">
    <div class="btn-submit-payment" style="display: none;">
    <button type="submit" class="button alt" id="submit-payload-to-ipay-ghana-wc-payment-gateway-checkout-url">' . __( 'Checkout with iPay Ghana', '' ) . '</button>
    </div>
    </form>';
}


/********************************************************************
end compatibility with IpayG Payment Gateway 
*********************************************************************/

/******************************************************************
  Disable SCD Price Field to add Booking product
********************************************************************/

    add_action('woocommerce_product_options_general_product_data', 'scd_deletepricefields');
function scd_deletepricefields(){
?>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#product-type').change(function() {
        // alert(22);
        if (jQuery('#product-type').children("option:selected")) {
            //alert(jQuery('#product-type').val());
            var zone = jQuery('#product-type').val();
            jQuery('#mySetPrice').show();
        }
        var zone = jQuery('#product-type').val();
        if (zone == "booking") {
            jQuery('#mySetPrice').hide();
        } else {
            jQuery('#mySetPrice').show();
        }
    });
});
</script>
<?php
}

/******************************************************************
  End Disable SCD Price Field to add Booking product
********************************************************************/

/******************************************************************
  Start Fixing Conversion price in search bar on urna theme
********************************************************************/
add_action( 'wp_ajax_urna_autocomplete_search', 'scd_urna_tbay_autocomplete_suggestions',1);
add_action( 'wp_ajax_nopriv_urna_autocomplete_search', 'scd_urna_tbay_autocomplete_suggestions',1);

function scd_urna_tbay_autocomplete_suggestions(){
    add_filter('wc_price','scd_convert_price_in_html_markup',10,4);
}
/******************************************************************
  End Fixing Conversion price in search bar on urna theme
********************************************************************/




/******************************************************************
  Start Fixing Conversion price in woocommerce apointment
********************************************************************/
add_action( 'wp_ajax_wc_appointments_calculate_costs','scd_calculate_costs', 1);
add_action( 'wp_ajax_nopriv_wc_appointments_calculate_costs','scd_calculate_costs',1);
function scd_calculate_costs(){
    add_filter('wc_price','scd_convert_price_in_html_markup',10,4);
}
/******************************************************************
  End Fixing Conversion price in woocommerce apointment
********************************************************************/





/******************************************************************
  Start Fixing Conversion precission in subtotal and total
********************************************************************/
/*
add_action( 'woocommerce_before_calculate_totals', 'scd_fixing_price', 10, 1);

function scd_fixing_price( $cart_object ) {
    $scd_subtotal = 0;
    foreach ( $cart_object->get_cart() as $cart_item ) {
        ## Set the price with WooCommerce compatibility ##
        $product_id = $cart_item['product_id'];
        $target_currency = scd_get_target_currency();
        
        if( scd_is_custom_price_defined_for_currency($product_id, $target_currency) )
        {
            list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $target_currency);
    
            // convert the custom price in target currency to an equivalent price in base currency
            $base_currency = get_option( 'woocommerce_currency');
            $rate = scd_get_conversion_rate($base_currency, $target_currency);
            if(!empty($rate) && !empty($saleprice)) {
                $price = floatval($saleprice) / floatval($rate);
                $cart_item['data']->set_price( $price ); // WC 3.0+
                $scd_subtotal  = $scd_subtotal + ($cart_item['quantity'] * $price);
            }else {
                $price = floatval($regprice) / floatval($rate);
                $cart_item['data']->set_price( $price ); // WC 3.0+
                $scd_subtotal  = $scd_subtotal + ($cart_item['quantity'] * $price);
            }
        }
            	
    }
    if(scd_get_target_currency() != get_option( 'woocommerce_currency')){
        // add the filter 
        add_filter( 'wc_get_price_decimals', 'filter_wc_get_price_decimals', 10, 1 );
        // define the wc_get_price_decimals callback 
        function filter_wc_get_price_decimals( $get_option ) { 
            $get_option = 18;
            return $get_option; 
        }; 
    }
}
add_action( 'woocommerce_after_calculate_totals', 'scd_fixing_decimal', 10, 0);
function scd_fixing_decimal(){
    remove_filter( 'wc_get_price_decimals', 'filter_wc_get_price_decimals' );
}*/
/******************************************************************
  End Fixing Conversion precission in subtotal and total
********************************************************************/