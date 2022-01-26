<?php
/*
*Plugin Name: SCD - Smart Currency Detector - Free variant 
* Plugin URI: http://gajelabs.com/product/scd
* Description: This wordpress / woocommerce plugin is an ALL-IN-ONE solution for online market places owners, sellers, end customers.
* Version: 4.7.10.2
* WC tested up to: 5.7
* Author: GaJeLabs
* Author URI: http://gajelabs.com
 */

$scd_plugin_folder = 'scd-smart-currency-detector';
define( 'SCD_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );


include_once "scd_settings.php";
include_once "scd_renders.php";
include_once "scd_widget.php";
include_once "scd_exchange.php";

add_action('init',function(){

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
        }

        if($reste <= 0){
           $options = get_option('scd_general_options');
            if($options && $options['overrideCurrencyOptions'] != "0"){
                $options['overrideCurrencyOptions'] = "0";
                $options['customCurrencyCount'] = "0";
                $options['customCurrencyOptions'] = "";
                update_option('scd_general_options',$options);
            } 

            if($options && $options['multiCurrencyPayment'] != "0"){
                $options['multiCurrencyPayment'] = "0";
                update_option('scd_general_options',$options);
            }

            $options = get_option('scd_currency_options');
            if($options && $options['userCurrencyChoice'] != "allcurrencies"){
                $options['userCurrencyChoice'] = "allcurrencies";
                update_option('scd_currency_options',$options);
            } 
        }
    }
});


add_filter('is_premium_valid_licence',function ($unactivated) {
    $arrays = array(
        "scd_dokan_marketplace/index.php" => 1,
        "scd_wcv_marketplace/index.php" => 1,
        "scd_wcfm_marketplace/index.php" => 1,
        "scd_wcmp_marketplace/index.php" => 1,
        "scd_standard/index.php" => 1,
        "scd_marketplace_pro/index.php" => 1);

    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $plugs = get_plugins();

    if( count(array_intersect_key($arrays, $plugs)) ){
        return true;
    }  else {
        return false;
    }
},100);

function scd_add_scripts_to_admin_panel() {
    global $wp_roles;

    $plugin_folder = $GLOBALS['scd_plugin_folder'];

    wp_enqueue_style('ch_scd_main_css', trailingslashit(plugins_url("", __FILE__)) . "css/scd_style.css");
    wp_enqueue_style('ch_scd_admin_css', trailingslashit(plugins_url("", __FILE__)) . "css/jquery.scd_admin.css");
    wp_enqueue_style('ch_scd_css', trailingslashit(plugins_url("", __FILE__)) . "css/jquery.scd.css");
    wp_enqueue_style('ch_scd_chosen_css', trailingslashit(plugins_url("", __FILE__)) . "css/chosen.min.css");

    $current_screen = get_current_screen();
    //echo("<script>console.log('PHP: ".get_current_screen()->id."');</script>");
    if ($current_screen->id !== "nav-menus") {
        wp_enqueue_script('ch_scd_maps_js',trailingslashit(plugins_url("", __FILE__)) . "js/scd_maps.js", array(), '5.5.3');
        wp_enqueue_script('ch_scd_chosen_js', trailingslashit(plugins_url("", __FILE__)) . "js/chosen.jquery.min.js", array('jquery'), '5.5.3');
        wp_enqueue_script('ch_scd_adminready', trailingslashit(plugins_url("", __FILE__)) . "js/scd_adminready.js", array('ch_scd_chosen_js', 'ch_scd_maps_js'), '5.5.3', true);
    }

    $options = get_option('scd_currency_options');
    $role_options = get_option('scd_role_options');
    if($role_options && isset($role_options['role']))
    $role_options = $role_options['role'];
    else 
     $role_options='';
    $currNumber = !empty($role_options) ? explode(",", $role_options) : [];
    $scd_getUserRole = scd_get_user_role();

    if(apply_filters('is_scd_multivendor',false))
    {
        $rr = "";
        $myrole2 = "";
        $setR = 4;
        $roles = $wp_roles->get_names();
        foreach ($roles as $myrole) {

            foreach ($currNumber as $res) {

                $cn = explode(",", $res);
                foreach ($cn as $resu) {

                    $resul = explode("_", $resu);

                    $rr = str_replace('-', '_', strtolower($resul[0]));
                    $nbr = $resul[1];

                    $myrole = str_replace(' ', '-', $myrole);

                    if (current_user_can($rr) && $myrole == $resul[0])
                        $setR = $nbr;

                    //if( current_user_can('administrator') ) $setR = 4;
                    if ('administrator' == $scd_getUserRole)
                        $setR = 4;
                }
            }
        }
    }
    else
    {
        $setR = 4;
    }

    wp_localize_script('ch_scd_adminready', 'settings', array(
        'currencies' => $options['fallbackCurrency'],
        'currenciesUser' => $options['userCurrencyChoice'],
        'role' => $setR,
        'langStrings' => array(
            'lblCustExchangeRate' => __('Your Custom Exchange Rate', 'ch_scd_woo'),
            'lblAddThisRate' => __('Add this rate', 'ch_scd_woo'),
            'lblWrongFormat' => __('Please give some numeric value for exchange rate. For example 3 or 4.56 or 0.67', 'ch_scd_woo'),
            'lblRemoveRate' => __('Remove this rate', 'ch_scd_woo')
        )
    ));
}

function scd_get_bool_option($opgroup, $option) {

    if (is_array(get_option($opgroup)) && array_key_exists($option, get_option($opgroup))) {
        $options = get_option($opgroup);
        return $options[$option];
    }

    return 0;
}

function scd_get_option ($opgroup, $option) {
    
    if (is_array(get_option($opgroup)) && array_key_exists($option, get_option($opgroup))) {
        $options = get_option($opgroup);
        return $options[$option];
    }

    return null;
}

function scd_add_scripts_to_post() {

    global $post;

    $plugin_folder = $GLOBALS['scd_plugin_folder'];


    wp_enqueue_style('ch_scd_css', trailingslashit(plugins_url("", __FILE__)) . "css/jquery.scd.css");



    wp_enqueue_style('ch_scd_chosen_css', trailingslashit(plugins_url("", __FILE__)) . "css/chosen.min.css");



    wp_enqueue_style('ch_scd_flag_css', trailingslashit(plugins_url("", __FILE__)) . "css/country_select.css");



    wp_enqueue_script('ch_scd_chosen_js', trailingslashit(plugins_url("", __FILE__)) . "js/chosen.jquery.min.js", array('jquery'), '5.5.3', true);




    wp_enqueue_script('ch_scd_maps_js', trailingslashit(plugins_url("", __FILE__)) . "js/scd_maps.js", array(), '5.5.3', true);




    wp_enqueue_script('ch_scd_defaultdata_js', trailingslashit(plugins_url("", __FILE__)) . "js/defaultdata.js", array(), '5.5.3', true);
   



    wp_enqueue_script('ch_scd_flag_js', trailingslashit(plugins_url("", __FILE__)) . "js/country_select.js", array('ch_scd_maps_js'), '5.5.3', true);    
   
    
   wp_enqueue_script('ch_scd_fetch', trailingslashit(plugins_url("", __FILE__)) . "js/scd_fetchdata.js", array('ch_scd_maps_js','ch_scd_defaultdata_js'), '5.5.3', true);



    wp_enqueue_script('ch_scd_postready', trailingslashit(plugins_url("", __FILE__)) . "js/scd_postready.js", array('ch_scd_fetch', 'ch_scd_chosen_js', 'ch_scd_flag_js'), '5.5.3', true);
    

    if(function_exists('wcfm_is_vendor')){

        if(!in_array('wcfm-dashboard-page', get_body_class())){
            wp_enqueue_script('ch_scd_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }

    }elseif(function_exists('wcmp_vendor_dashboard_page_id')){
        
        if(!is_page(wcmp_vendor_dashboard_page_id())){
            wp_enqueue_script('ch_scd_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }
    }elseif(function_exists('wcv_is_vendor_dashboard')){
        
        if(!wcv_is_vendor_dashboard()){
            wp_enqueue_script('ch_scd_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }
    }elseif(function_exists('dokan_is_seller_dashboard')){
        
        if(!in_array('dokan-dashboard', get_body_class())){
            wp_enqueue_script('ch_scd_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }
    }else{
        wp_enqueue_script('ch_scd_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_widget.js", array('ch_scd_postready'), '5.5.3', true);
    }
    
    $currency_options = get_option('scd_currency_options');
    $general_options = get_option('scd_general_options');
    $scd_getUserRole = scd_get_user_role();
	
	if(function_exists('wcfm_is_vendor')){

        if($currency_options["mobilewidget"] === "1" && !in_array('wcfm-dashboard-page', get_body_class())){
		  wp_enqueue_script('ch_scd_mobile_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_mobile_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }

	}elseif(function_exists('wcmp_vendor_dashboard_page_id')){

        if($currency_options["mobilewidget"] === "1" && !is_page(wcmp_vendor_dashboard_page_id())){
            wp_enqueue_script('ch_scd_mobile_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_mobile_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }
    }elseif(function_exists('wcv_is_vendor_dashboard')){

        if($currency_options["mobilewidget"] === "1" && !wcv_is_vendor_dashboard()){
            wp_enqueue_script('ch_scd_mobile_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_mobile_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }
    }elseif(function_exists('dokan_is_seller_dashboard')){

        if($currency_options["mobilewidget"] === "1" && !in_array('dokan-dashboard', get_body_class())){
            wp_enqueue_script('ch_scd_mobile_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_mobile_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }
    }else{
		if($currency_options["mobilewidget"] === "1"){
        wp_enqueue_script('ch_scd_mobile_widget', trailingslashit(plugins_url("", __FILE__)) . "js/scd_mobile_widget.js", array('ch_scd_postready'), '5.5.3', true);
        }
	}
	
	
	
    //value of this variable scd_isPriceByCur(). but this value is not used somewhere for now
    $isIt = true;
    $thousandsep = get_option('woocommerce_price_thousand_sep'); //the default woo thousand separator
    $decimsep = get_option('woocommerce_price_decimal_sep'); //the default woo Decimal separator
  
    // Enable or disable price conversions in javascript.
    // Do not enable conversion on the checkout page if multi currency payment is not enabled.
    // Do not enable conversion on the order received page
    $enableJsConversions = true;
    if ( !isset($general_options['multiCurrencyPayment']) && (is_checkout()==true) ) {
        $enableJsConversions = false;
    }
	
	if ( isset($general_options['multiCurrencyPayment']) && (is_account_page()==true) ) {
        $enableJsConversions = false;
    }
	
    if( isset($general_options['multiCurrencyPayment']) && (is_wc_endpoint_url( 'order-received')==true) ) {
        $enableJsConversions = false;
    }
	
    if( isset($general_options['multiCurrencyPayment']) && (is_wc_endpoint_url( 'order-pay' )==true) ) {
        $enableJsConversions = false;
	}
	
    if(in_array('wcfm-dashboard-page', get_body_class())){   // check if this is the wcfm dashboard page
        $enableJsConversions = false;
    }
	
	if(in_array('dokan-dashboard', get_body_class())){   // check if this is the Dokan dashboard page
        $enableJsConversions = false;
    } 
	
    if(function_exists('wcmp_vendor_dashboard_page_id') && is_page(wcmp_vendor_dashboard_page_id())){   // check if this is the wcmp dashboard page
        $enableJsConversions = false;
    }	
	
	if(function_exists('wcv_is_vendor_dashboard') && wcv_is_vendor_dashboard()){   // check if this is the wc-vendor dashboard page
        $enableJsConversions = false;
    }	

	
    /* $role = "";
      $currencyNumber = ""; */
    
if(!is_home()){
$filter= isset($currency_options['filterOnlyOnHome'])&& !empty($currency_options['filterOnlyOnHome'])?true:false;
}  else {
$filter=false;    
}
    
    wp_localize_script('ch_scd_postready', 'scd_urls',array('ajaxurl' => admin_url( 'admin-ajax.php' )) );
    
    
    wp_localize_script('ch_scd_postready', 'settings', array(
        'baseCurrency' => get_option('woocommerce_currency'),
        'multiCurrencyPayment' => scd_get_bool_option('scd_general_options', 'multiCurrencyPayment'),
        'autoUpdateExchangeRate' => scd_get_bool_option('scd_general_options', 'autoUpdateExchangeRate'),
        'exchangeRateUpdate' => $general_options['exchangeRateUpdate'],
        'exchangeRateUpdateInterval' => $general_options['exchangeRateUpdateInterval'],
        'overrideCurrencyOptions' => scd_get_bool_option('scd_general_options', 'overrideCurrencyOptions'),
        'customCurrencyCount' => $general_options['customCurrencyCount'],
        'customCurrencyOptions' => !empty($general_options['customCurrencyOptions'])? json_decode( $general_options['customCurrencyOptions'], true) : "", 
        'autodetectLocation' => scd_get_bool_option('scd_currency_options', 'autodetectLocation'),
		'priceByCurrency' => scd_get_bool_option('scd_currency_options', 'priceByCurrency'),
        'userCurrencyChoice' => apply_filters('scd_currencies_selected',$currency_options['userCurrencyChoice']),
        'decimalNumber' => scd_get_bool_option('scd_currency_options', 'decimalNumber'),
        'decimalPrecision' => $currency_options['decimalPrecision'],
        'currencyVal' => '',
         'getUserRole' => $scd_getUserRole,
         'mobilewidget' => scd_get_bool_option('scd_currency_options', 'mobilewidget'),
         'fallbackPosition' => $currency_options['fallbackPosition'],
         'mobilewidgetcolor' => $currency_options['mobilewidgetcolor'],
		'mobilewidgetpopup' => $currency_options['mobilewidgetpopup'],
        'textpopup' => $currency_options['textpopup'],
        'isIt' => $isIt,
        'thousandSeperator' => '1',
        'thousandSeperatorToUse' => $thousandsep,
        'decimalSeperator' => $decimsep,
        'useCurrencySymbol' => '1', 
        'currencyPosition' => get_option( 'woocommerce_currency_pos' ),
        'fallbackCurrency' =>$currency_options['fallbackCurrency']=="basecurrency"? get_option('woocommerce_currency'): $currency_options['fallbackCurrency'],
        'customClasses' => '',
        'scd_currencies' => scd_get_list_currencies(),
        'enableJsConvert' => apply_filters('scd_enable_js_conversion',$enableJsConversions),
    ));




    // Pass rates to front end only on first page load
    if(!isset($_SESSION) || empty($_SESSION['rates_posted']) )
    {
        $rates = scd_get_exchange_rates();
        if(!empty($rates))
        {
                
            wp_localize_script('ch_scd_fetch', 'scd_urls',array('ajaxurl'=> admin_url( 'admin-ajax.php' )) );
                
            wp_localize_script('ch_scd_fetch', 'scd_local_rates', array('data' => $rates));
        }
        $_SESSION['rates_posted'] = true;
    }
}

function scd_options_callback() {
    echo '';
}

function scd_adv_options_callback() {
    _e('Please do not change anything here unless you know what you are doing.', 'ch_scd_woo');
}
function scd_settings_tabs() {
   $tabs=array(
       'settings'=>array('id'=>'settings','label'=>'GENERAL SETTINGS','class'=>'nav-tab','page'=>'scd_options_page','name'=>'scd_general_options','submit'=>true),
       'currencies'=>array('id'=>'currencies','label'=>'CURRENCIES SETTINGS','class'=>'nav-tab','page'=>'scd_options_page','name'=>'scd_currency_options','submit'=>true),
       'help'=>array('id'=>'help','label'=>'HELP & SUPPORT','class'=>'nav-tab','page'=>'scd_options_page','name'=>'scd_help_options','submit'=>false)
   ); 
 return apply_filters('scd-admin-tab-list',$tabs);     
}
function scd_render_scd_display() {
    ?>

    <div class="wrap">

        <div id="icon-themes" class="icon32"></div>
        <h2>SCD Currencies</h2>
    <?php
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'settings';
   
    ?>
    <?php settings_errors(); ?>

        <h2 class="nav-tab-wrapper">
            <?php
            $tab_list=scd_settings_tabs();
            
            foreach ($tab_list as $key => $tab_item) {
                $class=$active_tab == $tab_item['id'] ? "nav-tab-active" : "";
            echo '<a href="?page='.$tab_item['page'].'&tab='.$tab_item['id'].'" '
                    . 'class="nav-tab '.$class.'">'
                    . ''.$tab_item['label'].'</a>';
            }
            ?>
        </h2>

    <?php
        if(apply_filters('scd_license_manager_tab',false,$active_tab)){
         ?>
        <div id="scd-form-license">
             <?php do_action('scd_activate_license_form') ?>
        </div>
        <?php
        
        }
     foreach ($tab_list as $key => $tab_item) {
    if ($active_tab == $key) {
       
        ?>   
        <div id="big-text">
        <form method="post" action="options.php" id="<?php echo $tab_item['name']; ?>-form">
             
                 <?php
                        
            settings_fields($tab_item['name']);
            do_settings_sections($tab_item['name']);
            //submit_button('Save Changes', 'button-primary scd_save');
            if($tab_item['submit']){
            //if(apply_filters('scd-pro-unactivated',true)){
            //   echo '<input type="submit" id="scd_save_buton" class="button-primary scd_save" value="Save Changes" />';
            //}  else {
             submit_button('Save Changes', 'button-primary scd_save');    
            //}
          }
            echo("<hr />");
            ?>
             
            </form>
            </div>
        <?php
          }
        }
         ?>
    </div>

    <?php
}

register_activation_hook(__FILE__, 'scd_plugin_install');
function scd_plugin_install(){
    if(!get_option('scd_first_install_date')){
        update_option('scd_first_install_date',new DateTime('now'));
    }   
}

function scd_plugin_menu() {

    add_submenu_page(
            'woocommerce', 'SCD Settings', 'SCD Currencies', 'manage_options', 'scd_options_page', 'scd_render_scd_display'
    );
}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ||
        array_key_exists('woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins'))) {

    add_action('admin_menu', 'scd_plugin_menu');

    add_action('admin_init', 'scd_init_options');
    add_action('current_screen', 'scd_add_scripts_to_admin_panel');

    //if(scd_check_license_active())
    //{
        add_action('wp_enqueue_scripts', 'scd_add_scripts_to_post',1000);
        add_action('load-post.php', 'scd_custom_product_basic_load');
        add_action('load-post-new.php', 'scd_custom_product_basic_load');
        //add_action( 'widgets_init', 'scd_load_widget' );
        scd_init_stat();
        scd_init_filters();

        //cookies
        add_action('init', 'scd_set_scd_cookie');
    //}

    function scd_set_scd_cookie() {
        // 	setcookie( 'scd_user_target_currency', '', 30 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
        // 	setcookie('rate',0,30 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        $nextMonth = time() + (30 * 24 * 60 * 60); // php 7 cookies seeting adaptation
        setcookie('scd_user_target_currency', '', $nextMonth, COOKIEPATH, COOKIE_DOMAIN);
        setcookie('rate', 0, $nextMonth, COOKIEPATH, COOKIE_DOMAIN);
    }

    //session
    function scd_register_scd_session() {
        if (!session_id())
            session_start();
    }

    add_action('wp', 'scd_register_scd_session');

}

function scd_unactivate_notice() { 
    ?>
    <div class="notice notice-warning is-dismissible">
        <p style="color: brown; font-size: 1.3em;"><strong>SCD Smart-Currency-Detector </strong></p>
        <?php $notice=apply_filters('scd_notice','You are using the free version of SCD-Smart-Currency-Detector, please get the Premium version 
            <a href= "https://gajelabs.com/" >here</a> to access to more features');
        echo $notice;
        ?>
    </div>
    <?php 
}
    add_action('admin_notices','scd_unactivate_notice');

if(is_admin()){
    add_filter( 'plugin_row_meta', 'scd_pro_links', 10, 2 );
}
    
function scd_pro_links( $links, $file ) {

	$plugin = plugin_basename(__FILE__);

// create the links
	if ( $file == $plugin ) {

		$supportlink = 'https://wordpress.org/support/plugin/scd-smart-currency-detector/';
		$standardlink = 'https://gajelabs.com/our-products/';
		$marketplacelink = 'https://gajelabs.com/our-products/';
		$marketplaceprolink = 'https://gajelabs.com/our-products/';
		$tutoriallink = 'https://gajelabs.com/docs-and-tutorials/';
		$scdlinks=apply_filters('scd-links',array(
			'<a href="' . $supportlink . '" target="__blank"> <span class="scd-support scd-dashb-item" title="SCD Support"></span>Support</a>',
			'<a href="' . $standardlink . '" target="__blank"><span class="scd-standard scd-dashb-item" title="Get SCD Standard">Standard</span></a>',
			'<a href="' . $marketplacelink . '" target="__blank"><span class="scd-marketplace scd-dashb-item" title="Get SCD Marketplace variant">Marketplace variant</span></a>',
			'<a href="' . $marketplaceprolink . '" target="__blank"><span class="scd-marketplacepro scd-dashb-item" title="Get SCD Marketplace pro">Marketplace pro</span></a>',
			'<a href="' . $tutoriallink . '" target="__blank"><span class="scd-tutorial scd-dashb-item" title="SCD Tutoriel">Tutorial</span></a>'
		));
                
		return array_merge( $links, $scdlinks );
	}

	return $links;
}

// update notification message for this new version 4.7.6.6

function sample_admin_notice_warning() {
    ?>
    <div class="notice notice-warning" >
        <p><?php echo '<strong style="color: brown; font-size: 1.3em;" > SCD Smart-Currency-Detector </strong> : if you are using other SCD variant, please make sure that it is at least to version 4.7.8.'; ?></p>
    </div>
    <?php
}

// function that runs when shortcode is emergency writted by donald
function scd_wpb_emergency_shortcode() { 
 

return '<form method="POST">
			<!--<div class="scd_error" style="background-color: #E12D2D;color: #fff; font-weight: 600;"></div>-->
		
			<input type="hidden" name=\'targetSessionName\' id="targetSessionName" value="<?php echo $ts;?>"/>
			<!--<div id="endwid" style="border: solid 1px #CCC;width: 100%;margin-bottom: 10px;">
				<div id="dvFlag" class=""></div>
				<select id=\'ch_scd_woo_widget_select\' name=\'ch_scd_woo_widget_select_name\' style="max-width: 100%;width: 92.5%;height: 20px;border: none;" >

				</select>
			</div>-->
			<div id="endwid">
				<input id="scd_widget_selector" type="text" readonly="readonly">
				<label for="scd_widget_selector" style="display:none;">Select a country here...</label>
			</div>
			
		</form>';
} 
// register shortcode
add_shortcode('scd_widget', 'scd_wpb_emergency_shortcode'); 


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links' );
 
function my_plugin_action_links( $actions ) {
   $actions[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=scd_options_page') ) .'">Settings</a>';
  // $actions[] = '<a href="http://wp-buddy.com" target="_blank">More plugins by WP-Buddy</a>';
   return $actions;
}


//if(apply_filters('scd_notice_update_pro_version',true)){
//add_action( 'admin_notices', 'sample_admin_notice_warning' );
//}

include "scd_inofs.php";

/* Register activation hook. */
register_activation_hook( __FILE__, "scd_admin_notice_scd_activation_hook" );

register_activation_hook(__FILE__ , "plugin_activated");

register_deactivation_hook(__FILE__ , "plugin_deactivated");




?>