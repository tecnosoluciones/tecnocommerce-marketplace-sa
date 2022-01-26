<?php

include_once "scd_general_settings_form.php";
include_once "scd_currencies_settings_form.php";

$scd_license_duration = 365;

/**
 * Get the default settings values for a setting group
 * 
 * @param int $opgroup  The setting group
 * 
 * @return array The defaulkt settings values
 */
function scd_get_default_settings_values($opgroup) {
    switch ($opgroup) {

        case "scd_general_options":
            // General Settings default values
            return apply_filters('scd_init_general_options', array(
                "multiCurrencyPayment" => "1",
                "autoUpdateExchangeRate" => "1",
                "exchangeRateUpdate" => "24",
                "exchangeRateUpdateInterval" => "1",
                "overrideCurrencyOptions" => "0",
                "customCurrencyCount" => "0",
                "customCurrencyOptions" => "",
                "customClasses" => "",
                "deleteDataOnUninstall" => "1",
                "facebookIdAccount" => '',
                "campaignName" => ''
            ));
            break;

        case "scd_currency_options":
            // Currency Settings default values
            return apply_filters('scd_init_currency_options', array(
                "autodetectLocation" => "1",
                "userCurrencyChoice" => "allcurrencies",
                "fallbackCurrency" => "basecurrency",
                "decimalNumber" => "1",
                "decimalPrecision" => "1",
                "thousandSeperator" => "1",
                "targetSession" => "",
                "displayCurrencyMenu" => "",
                "menuTitle" => "Currency",
                "menuPosition" => "0",
                "menuLocation" => "",
                "mobilewidgetcolor" => "#ffffff",
                "mobilewidgetpopup" => "1",
                "mobilewidget" => "1",
                "fallbackPosition" => "Right",
                "textpopup"=>"Here! you can set the currency in which you are comfortable about shopping. Just click and choose...",
				"priceByCurrency" => "1",
            ));
            break;

        default:
            return array();
    }
}

/**
 * Initialize the settings values for a setting group
 * 
 * @param int $opgroup  The setting group
 */
function scd_initialize_options_group($opgroup) {

    // Get the default values for this settings group
    $default_values = scd_get_default_settings_values($opgroup);

    if (false == get_option($opgroup)) {
        // Settings group does not exist. Create and populate with default
        add_option($opgroup);
        update_option($opgroup, $default_values);
    } else {
        // Settings group already exists. Check that all elements are defined
        $options = get_option($opgroup);
        $update_required = false;

        foreach ($default_values as $key => $value) {
            if (array_key_exists($key, $options) == false) {
                $options[$key] = $value;
                $update_required = true;
            }
        }

        if ($update_required) {
            update_option($opgroup, $options);
        }
    }
}

function scd_settings_group() {

    return apply_filters('scd-settings-groups', array(
        'scd_general_section' => array('id' => 'one', 'title' => 'GENERAL SETTINGS', 'callback' => 'scd_options_callback', 'page' => 'scd_general_options', 'group' => 'scd_general_options', 'sanitize' => 'scd_sanitize_general_settings'),
        'scd_currency_section' => array('id' => 'two', 'title' => 'CURRENCIES SETTINGS', 'callback' => 'scd_options_callback', 'page' => 'scd_currency_options', 'group' => 'scd_currency_options', 'sanitize' => 'scd_sanitize_currency_settings'),
        'scd_help_section' => array('id' => 'three', 'title' => 'HELP & SUPPORT', 'callback' => 'scd_options_callback', 'page' => 'scd_help_options', 'group' => 'scd_help_options', 'sanitize' => 'scd_sanitize_help_settings')
            )
    );
}

function scd_settings_fields() {
    // string $title, callable $callback, string $page, string $section = 'default'
    $va = (scd_isChecked('scd_general_options', 'overrideCurrencyOptions') == false) ? 'style="display: none"' : "";
    $va3 = (scd_isChecked('scd_currency_options', 'decimalNumber') == false) ? 'style="display: none"' : "";
    $va4 = (scd_isChecked('scd_currency_options', 'mobilewidget') == false) ? 'style="display: none"' : "";
    $va5 = (scd_isChecked('scd_currency_options', 'mobilewidgetpopup') == false) && (scd_isChecked('scd_currency_options', 'mobilewidget') == false) ? 'style="display: none"' : "";


    return apply_filters('scd-options-fields', array(
        'multiCurrencyPayment' => array('title' => 'Enable multiple currencies payment', 'callback' => 'scd_form_render_multiCurrencyPayment', 'page' => 'scd_general_options', 'section' => 'scd_general_section'),
        'exchangeRateUpdateInterval' => array('title' => '<div id="all_exchangeRateUpdate">Update intervals</div>', 'callback' => 'scd_form_render_exchangeRateUpdateInterval', 'page' => 'scd_general_options', 'section' => 'scd_general_section'),
        'overrideCurrencyOptions' => array('title' => 'Customize currency options and exchange rates', 'callback' => 'scd_form_render_overrideCurrencyOptions', 'page' => 'scd_general_options', 'section' => 'scd_general_section'),
        'allCustom' => array('title' => '<div id="allCustom" ' . $va . '>Manual currency options</div>', 'callback' => 'scd_form_render_allCustom', 'page' => 'scd_general_options', 'section' => 'scd_general_section'),
        'deleteDataOnUninstall' => array('title' => 'Delete settings when plugin is uninstalled', 'callback' => 'scd_form_render_deleteDataOnUninstall', 'page' => 'scd_general_options', 'section' => 'scd_general_section'),
        'autodetectLocation' => array('title' => 'Select currency based on user location', 'callback' => 'scd_form_render_locationBasedSelect', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'userCurrencyChoice' => array('title' => 'Filter currencies enabled for your store', 'callback' => 'scd_form_render_userCurrencyChoice', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'fallbackCurrency' => array('title' => 'Default/Fallback store currency', 'callback' => 'scd_form_render_default_currency', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'decimalNumber' => array('title' => 'Enable decimal numbers display', 'callback' => 'scd_form_render_decimalNumber', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'decimalPrecision' => array('title' => '<div id="decimal" ' . $va3 . ' >Number of decimals</div>', 'callback' => 'scd_form_render_decimalPrecision', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'displayCurrencyMenu' => array('title' => 'Display the currency menu', 'callback' => 'scd_form_render_displayCurrencyMenu', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'menuTitle' => array('title' => 'Menu title', 'callback' => 'scd_form_render_menuTitle', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'menuPosition' => array('title' => 'Menu position', 'callback' => 'scd_form_render_menuPosition', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'menuLocation' => array('title' => 'Theme menu location', 'callback' => 'render_scd_menuLocation', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'help_and_support' => array('title' => '', 'callback' => 'render_scd_help_tutorials', 'page' => 'scd_help_options', 'section' => 'scd_help_section'),
        'mobilewidget' => array('title' => 'Enable mobile currency conversion widget', 'callback' => 'scd_form_render_mobilewidget', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'fallbackPosition' => array('title' => '<div id="position" ' . $va4 . '>Position of the Widget</div>', 'callback' => 'scd_form_mobilewidgetposition', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'mobilewidgetcolor' => array('title' => '<div id="mobilewidgetcolor" ' . $va4 . '>Update Mobile Widget Color</div>', 'callback' => 'scd_form_mobilewidgetcolor', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'mobilewidgetpopup' => array('title' => '<div id="mobilewidgetpopup" ' . $va4 . '>Show Mobile Widget Pop-Up</div>', 'callback' => 'scd_form_mobilewidgetpopup', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'textpopup' => array('title' => '<div id="textpopup" ' . $va5 . '>Write your Pop-Up</div>', 'callback' => 'scd_form_textpopup', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
        'priceByCurrency' => array('title' => 'Product price by currency', 'callback' => 'scd_form_render_pricebycurrency', 'page' => 'scd_currency_options', 'section' => 'scd_currency_section'),
		
            )
    );
}

function scd_init_options() {
    global $post;

    // scd_initialize_options_group ('scd_general_options');
    // scd_initialize_options_group ('scd_currency_options');


    $scd_grp_settings = scd_settings_group();
    foreach ($scd_grp_settings as $key => $grp) {
        scd_initialize_options_group($grp['group']);
        add_settings_section(
                $key, __('<span id="' . $grp['id'] . '">' . $grp['title'] . ' </span>', 'ch_scd_woo'), $grp['callback'], $grp['page']
        );
    }

    $scd_fields = scd_settings_fields();
    foreach ($scd_fields as $key => $field) {
        add_settings_field($key, $field['title'], $field['callback'], $field['page'], $field['section']);
    }


    foreach ($scd_grp_settings as $key => $grp) {
        register_setting(
                $grp['group'], $grp['group'], $grp['sanitize']
        );
    }
}

function render_scd_help_tutorials() {
    echo '<p> <a href="https://www.youtube.com/channel/UCC9c5V4XEfDKTIVPLOiUGEA/videos">See some tutorials here</a></p>';
}

function scd_sanitize_currency_settings($input) {

    $default_values = scd_get_default_settings_values('scd_currency_options');
    return scd_fill_unset_settings($input, array_keys($default_values));
}

function scd_sanitize_general_settings($input) {

    $default_values = scd_get_default_settings_values('scd_general_options');
    return scd_fill_unset_settings($input, array_keys($default_values));
}

function scd_sanitize_help_settings($input) {

    return;
}

function scd_fill_unset_settings($input, $settings_keys) {
    // Create our array for storing the validated options
    $output = array();

    // Loop through each of the keys 
    foreach ($settings_keys as $key) {

        // Check to see if the current option has a value. If so, process it.
        if (isset($input[$key])) {
            $output[$key] = $input[$key];
        } else {
            // This is useful for checkbox options. If value is not defined, it is because the checkbox
            // is not checked. Save as zero.
            $output[$key] = '0';
        }
    } // end foreach
    // Return the array processing any additional functions filtered by this action
    return $output;
}

/**
 * Locate template.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme/woocommerce-plugin-templates/$template_name
 * 2. /themes/theme/$template_name
 * 3. /plugins/woocommerce-plugin-templates/templates/$template_name.
 *
 * @param 	string 	$template_name			Template to load.
 * @param 	string 	$string $template_path	Path to templates.
 * @param 	string	$default_path			Default path to template files.
 * @return 	string 							Path to the template file.
 */
function scd_locate_template($template_name, $template_path = '', $default_path = '') {
    // Set variable to search in woocommerce-plugin-templates folder of theme.
    if (!$template_path) :
        $template_path = 'woocommerce-plugin-templates/';
    endif;
    // Set default plugin templates path.
    if (!$default_path) :
        $default_path = plugin_dir_path(__FILE__) . 'templates/'; // Path to the template folder
    endif;
    // Search template file in theme folder.
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));
    // Get plugins template file.
    if (!$template) :
        $template = $default_path . $template_name;
    endif;
    return apply_filters('scd_locate_template', $template, $template_name, $template_path, $default_path);
}

/**
 * Get template.
 *
 * @param string 	$template_name			Template to load.
 * @param array 	$args					Args passed for the template file.
 * @param string 	$string $template_path	Path to templates.
 * @param string	$default_path			Default path to template files.
 */
function scd_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '') {
    if (is_array($args) && isset($args)) :
        extract($args);
    endif;
    $template_file = scd_locate_template($template_name, $tempate_path, $default_path);
    if (!file_exists($template_file)) :
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
        return;
    endif;
    include $template_file;
}

/**
 * Widget Shortcode.
 *
 * @since 1.1
 */
function scd_widget_shortcode() {

    return scd_get_template('scd_widget.php');
}

add_shortcode('scd_widget', 'scd_widget_shortcode');

/**
 * Vertical Flag Shortcode.
 *
 * @since 1.1
 */
function scd_vertical_flag_shortcode() {

    return scd_get_template('scd_vertical_flag.php');
}
add_shortcode('scd_vertical_flag', 'scd_vertical_flag_shortcode');

/*********************************************************************
 * Start *** SHORTCODE **** Display Price In the Description product /
 * @param array $attr Shortcode attributes.
 * @return string Shortcode output.
/******************************************************************/
function scd_zone_price( $attr) {
    extract( shortcode_atts( array( 'amount' => '', 'from' => '', 'to' => ''), $attr ) );
    if ( '' === $from) {
        $from = get_option('woocommerce_currency');
    } else {
        $from = strtoupper( $attr['from'] );
    }
    if ( '' === $to ) {
        $to = scd_get_target_currency();
    } else {
        $to = strtoupper( $attr['to'] );
    }
    if($from !== '' && $to == ''){
        $from = strtoupper( $attr['from'] );
        $to = scd_get_target_currency();
    }
    if('' !== $amount){
        $amount = $attr['amount'];
        $symbol = get_woocommerce_currency_symbol( $to );
        $decimals = scd_options_get_decimal_precision();
        $result = scd_function_convert_subtotal($amount, $from, $to, $decimals);
        $html = $symbol . ' ' . $result;
    }
    else{$html = '<strong style="font-size: 0.9em; color: red;" >' . "Price is not specified" . "</strong>";}
    return $html;
}
add_shortcode('scd_price', 'scd_zone_price');
/*******************************************************************
/ End *** SHORTCODE **** Display Price In the Description product /
/****************************************************************/

register_activation_hook(__FILE__, 'scd_activate');
register_deactivation_hook(__FILE__, 'scd_deactivate');

function scd_network_propagate($pfunction, $networkwide) {
    global $wpdb;

    if (function_exists('is_multisite') && is_multisite()) {

        if ($networkwide) {
            $old_blog = $wpdb->blogid;
            // Get all
            $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                scd_init_options();
                call_user_func($pfunction, $networkwide);
            }
            switch_to_blog($old_blog);
            return;
        }
    }
    scd_init_options();
    call_user_func($pfunction, $networkwide);
}

function scd_activate($networkwide) {
    scd_network_propagate('_my_activate', $networkwide);
}

function scd_deactivate($networkwide) {
    scd_network_propagate('_my_deactivate', $networkwide);
}
