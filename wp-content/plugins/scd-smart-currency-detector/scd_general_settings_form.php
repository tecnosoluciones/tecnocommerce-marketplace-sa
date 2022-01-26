<?php

/* ------------------------------------------------------------------------
   This module handles the operations of the SCD General Settings tab
   ------------------------------------------------------------------------ */

/**
 * Render setting: Approximate the price
 */   
function scd_form_render_approxPrice() {
    ?>
    <div class="help-tip">
        <p>This setting rounds up the resulting foreign currency price to your convenience. It avoids several digits after comma while showing your products prices and let you make additional money out of approximations. Approximations rules: bigger than 0,5 is rounded up to 1. smaller than 0,5 is rounded to 0,5. 0,5 itself remains unchanged</p>
    </div>

    <input id="scd_approxPrice" name='scd_general_options[approxPrice]' style="margin-left: 20px; margin-top: 3px;" type='checkbox' value='1' <?php scd_echoChecked('scd_general_options', 'approxPrice'); ?>/>

    <?php
}

/**
 * Render setting: Enable multiple currencies payments
 */  
function scd_form_render_multiCurrencyPayment() {

    $free_features = free_features_date();

    $reste = $free_features['reste'];
    $text = $free_features['text'];

    ?>
    <div class="help-tip">
        <p>This setting enables or disables the use of converted prices and currencies for payments that are made using supported payment gateways like Paypal. When disabled, all payments are made in the default currency. When enabled, payments will be made in the user currency using the converted prices.</p>
    </div>

    <input id="scd_multiCurrencyPayment" name='scd_general_options[multiCurrencyPayment]' style="margin-left: 20px; margin-top: 3px;" type='checkbox' value='1' <?php scd_echoChecked('scd_general_options', 'multiCurrencyPayment'); ?>/>

    <?php
    echo $text;
}

/**
 * Render setting: Automatically update the exchange rate
 */ 
function scd_form_render_autoUpdateExchangeRate() {
    ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            jQuery('#scd_autoUpdateExchangeRate').change(function () {
                if (this.checked) {
                    //
                } else {
                    //
                }
            });
        });
    </script>

    <div class="help-tip">
        <p>This setting lets you automatically update your exchange rate or not. If checked you can use the next setting "Update intervals" as well".</p>
    </div>

    <input id="scd_autoUpdateExchangeRate" name='scd_general_options[autoUpdateExchangeRate]' style="margin-left: 20px; margin-top: 3px;" type='checkbox' value='1' <?php scd_echoChecked('scd_general_options', 'autoUpdateExchangeRate'); ?> />

    <?php
}

/**
 * Render setting: Update Inervals
 */ 
function scd_form_render_exchangeRateUpdateInterval() {

    $exch = get_option('scd_general_options');
    $exch = $exch['exchangeRateUpdate'];
    ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            jQuery('#scd_exchangeRateUpdate').on("keypress keyup blur", function (event) {

                jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g, ''));

                if ((event.which != 46 || jQuery(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57) && event.which != 8) {
                    event.preventDefault();
                }
            });
        });
    </script>

    <div id="all_exchangeRateUpdate2">
        <div class="help-tip">
            <p>Set intervals for automatic currencies updates in hours or days.</p>
        </div>

        <div style="margin-left: 20px;margin-top: 0px;display: inline-block;">Update every : </div>

        <div style="width: 8%;display: inline-block;">
            <input id="scd_exchangeRateUpdate" name='scd_general_options[exchangeRateUpdate]' style="width: 100%;border-radius: 3px;border: 1px solid #ccc;" type='text' value='<?php echo $exch; ?>'/>
        </div>

        <div class="select-style" style="margin-left: 0px;display: inline-flex;overflow: initial;width: 11%;height: 26px;">

            <select name='scd_general_options[exchangeRateUpdateInterval]'>

                <?php
                $upInterval = get_option('scd_general_options');
                $upInterval = $upInterval['exchangeRateUpdateInterval'];
                ?>

                <option value="1" <?php echo ($upInterval == '1') ? 'selected' : ''; ?>  > <?php _e('Hour(s)', 'ch_scd_woo'); ?> </option>
                <option value="24" <?php echo ($upInterval == '24') ? 'selected' : ''; ?>  ><?php _e('Day(s)', 'ch_scd_woo'); ?></option>

            </select>
        </div>
    </div>

    <?php
}

/**
 * Render setting: Customize currency options and exchange rates
 */ 
function scd_form_render_overrideCurrencyOptions() {
    $free_features = free_features_date();

    $reste = $free_features['reste'];
    $text = $free_features['text'];

    if( $reste > 0 || apply_filters('is_premium_valid_licence',false)){ ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#scd_overrideCurrencyOptions').change(function () {
                    if (this.checked) {
                        jQuery("#allCustom").fadeIn('slow');
                        jQuery("#allCustom2").fadeIn('slow');
                    } else {
                        jQuery("#allCustom").fadeOut('slow');
                        jQuery("#allCustom2").fadeOut('slow');
                    }
                });
            });
        </script>
    <?php }

    ?>

    <div class="help-tip">
        <p>This setting allows you to change the default options for a currency. You can manually specify the exchange rate to use for a currency, or the additional percentage to apply on top of the exchange rate.</p>
    </div>

    <input id="scd_overrideCurrencyOptions" name='scd_general_options[overrideCurrencyOptions]' style="margin-left: 20px; margin-top: 3px;" type='checkbox' value='1' <?php scd_echoChecked('scd_general_options', 'overrideCurrencyOptions'); ?> />

    <?php
    if(!apply_filters('is_premium_valid_licence',false)){
        echo $text;
    }
}

/**
 * Render setting: Manual currency options
 */ 
function scd_form_render_allCustom() {

    $customNbr = scd_get_option('scd_general_options', 'customCurrencyCount');
    $customAll = scd_get_option('scd_general_options', 'customCurrencyOptions');

    if(empty($customNbr) || empty($customAll))
    {
        $customNbr = 0;
        $customAll = "";
    }
    
    ?>

    <script type="text/javascript">

        jQuery(document).ready(function () {

            <?php
            if ($customNbr > 0) {

                $call = json_decode($customAll, true);
                $keys = array_keys($call);
                for ($intId = 1; $intId <= $customNbr; $intId++) {

                    $i = $intId - 1;
                    $cc = $keys[$i];            // currency code
                    $attributes =  $call[$cc];
                    $cp = $attributes["pos"];   // position
                    $cr = $attributes["rate"];  // custom rate
                    $car = $attributes["inc"];  // increase on top
                    $cs = $attributes["sym"];   // symbol

            ?>

                    var wrapper = jQuery('<div class="myform" id="field' + <?php echo $intId; ?> + '">'); //Fields wrapper
                    var customCurrency = jQuery('<select id="scd_customCurrency' + <?php echo $intId; ?> + '" data-placeholder="Set currency" class="scd_chosen_select_static" ><?php
                        foreach (scd_get_list_currencies() as $key => $val) {
                            if ($key != get_option('woocommerce_currency') || $key != $cc) {
                                $sel = $key == $cc ? "selected" : "";
                                echo "<option value=\'$key\' $sel>$key - $val</option>";
                            }
                        }
                        ?></select>');
                                         var customPosition = jQuery('<select id="scd_customPosition' + <?php echo $intId; ?> + '" data-placeholder="Position"  class="scd_chosen_select_static" ><option value="default" <?php if ("" == $cp) echo "selected"; ?> >Default <option value="left" <?php if ("left" == $cp) echo "selected"; ?> >Left ($79)</option><option value="right" <?php if ("right" == $cp) echo "selected"; ?> >Right (79$)</option><option value="left_space" <?php if ("left_space" == $cp) echo "selected"; ?> >Left Space ($ 79)</option><option value="right_space" <?php if ("right_space" == $cp) echo "selected"; ?> >Right Space (79 $)</option><option value="left_country" <?php if ("left_country" == $cp) echo "selected"; ?> >Left Country (AUD 79$)</option><option value="right_country" <?php if ("right_country" == $cp) echo "selected"; ?> >Right Country ($79 AUD)</option></select>');

                    var customRate = jQuery('<input id="scd_customRate' + <?php echo $intId; ?> + '" placeholder="Rate" style="width: 75px;height: 24px;margin-left: 10px;border-radius: 3px;border: 1px solid #ccc;" type="text" value="<?php echo $cr; ?>" />');
                    var customIncrease = jQuery('<select id="scd_customIncrease' + <?php echo $intId; ?> + '" data-placeholder="Set applied rate"  class="scd_chosen_select_static" ><?php
                        for ($i = 0; $i < 11; $i++) {
                            $apply = $i . "%";
                            $rate = $i;
                            $sel = $rate == $car ? "selected" : "";
                            echo "<option value=\'$rate\' $sel>$apply</option>";
                        }
                        ?></select>');
                    var customSymbol = jQuery('<input id="scd_customSymbol' + <?php echo $intId; ?> + '" placeholder="Symbol" style="width: 14%;height: 24px;margin-left: 10px;border-radius: 3px;border: 1px solid #ccc;" type="text" value="<?php echo $cs; ?>" />');
                    var removeButton = jQuery('<span class="scd_delSingleExchange" id="scd_deleteCustomCurrency' + <?php echo $intId; ?> + '"><?php echo '<img src="' . plugins_url( 'images/scd_remove.png',  __FILE__  ) . '" style="width: 30px; cursor: pointer;" >'  ?></span>'); 
                    removeButton.click(function() {
                        jQuery(this).parent().remove();
                        var count = jQuery('#buildform div').length - 1;
                        if (count == 0)
                            jQuery('#scd_buildformTitle').hide();
                        else
                            jQuery('#scd_buildformTitle').show();
                    });

                    wrapper.append(removeButton);
                    wrapper.append(customCurrency);
                    wrapper.append(customRate);
                    wrapper.append(customIncrease);
                    wrapper.append(customSymbol);
                    wrapper.append(customPosition);
                    
                    jQuery('#buildform').append(wrapper);

                    jQuery(window).load(function () {
                        var choo = '#scd_customCurrency' + <?php echo $intId; ?> + '_chosen';
                        jQuery(choo).width(250);
                        choo = '#scd_customPosition' + <?php echo $intId; ?> + '_chosen';
                        jQuery(choo).width(130);
                        choo = '#scd_customIncrease' + <?php echo $intId; ?> + '_chosen';
                        jQuery(choo).width(65);
                        //alert(choo);
                        //console.log('choo ' + choo);
                    });

                    customRate.on("keypress keyup blur", function (event) {

                        //this.value = this.value.replace(/[^0-9\.]/g,'');
                        jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g, ''));

                        if ((event.which != 46 || jQuery(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57) && event.which != 8) {
                            event.preventDefault();
                        }
                    });

            <?php
                } // end for loop

            } // end if ($customNbr > 0) 
            ?>

            jQuery('#scd_buildformNextIdx').val(<?php echo ($customNbr + 1); ?>);

            jQuery('#scd_addCurrency').click(function () {

                var intId = jQuery('#scd_buildformNextIdx').val();   

                var wrapper = jQuery('<div class="myform" id="field' + intId + '">'); //Fields wrapper
                var customCurrency = jQuery('<select id="scd_customCurrency' + intId + '" data-placeholder="Set currency"  style="width:65%;margin-left: 18px;" class="scd_chosen_select_static" ><?php
                    foreach (scd_get_list_currencies() as $key => $val) {
                        if ($key != get_option('woocommerce_currency')) {
                            echo "<option value=\'$key\' >$key - $val</option>";
                        }
                    }
                    ?></select>');
                //var customCurrency = jQuery('<select id="scd_customCurrency' + intId + '" data-placeholder="Set currency"  style="width:90%;" class="scd_chosen_select_static" ><?php //foreach ($curren as $key) { echo $key; }   ?></select>');
                                var customPosition = jQuery('<select id="scd_customPosition' + intId + '" data-placeholder="Position"  style="width:33%;margin-left: 15px;margin-right: 4px;" class="scd_chosen_select_static" ><option value="default"> Default<option value="left">Left ($79)</option><option value="right">Right (79$)</option><option value="left_space">Left Space ($ 79)</option><option value="right_space">Right Space (79 $)</option><option value="left_country">Left Country (AUD 79$)</option><option value="right_country">Right Country ($79 AUD)</option></select>');

                var customRate = jQuery('<input id="scd_customRate' + intId + '" placeholder="Rate" style="width: 75px;margin-right: 13px;margin-left: 6px;border-radius: 3px;border: 1px solid #ccc;" type="text" value="" />');
                var customIncrease = jQuery('<select id="scd_customIncrease' + intId + '" data-placeholder="Set applied rate"  style="width:16%;margin-right: 8px;margin-left: 7px;" class="scd_chosen_select_static" ><?php
                    for ($i = 0; $i < 11; $i++) {
                        $apply = $i . "%";
                        $rate = $i;
                        echo "<option value=\'$rate\'>$apply</option>";
                    }
                    ?></select>');
                var customSymbol = jQuery('<input id="scd_customSymbol' + intId + '" placeholder="Symbol" style="width: 14%;border-radius: 3px;border: 1px solid #ccc;" type="text" value="" />');
                var removeButton = jQuery('<span class="scd_delSingleExchange" id="scd_deleteCustomCurrency' + intId + '"><?php echo '<img src="' . plugins_url( 'images/scd_remove.png',  __FILE__  ) . '" style="width: 30px; cursor: pointer;" >'  ?></span>'); 
                removeButton.click(function() {
                    jQuery(this).parent().remove();
                    var count = jQuery('#buildform div').length - 1;
                    if (count == 0)
                        jQuery('#scd_buildformTitle').hide();
                    else
                        jQuery('#scd_buildformTitle').show();
                });

                wrapper.append(removeButton);
                wrapper.append(customCurrency);
                wrapper.append(customRate);
                wrapper.append(customIncrease);
                wrapper.append(customSymbol);
                wrapper.append(customPosition);
                
                jQuery('#buildform').append(wrapper);

                jQuery('#scd_buildformNextIdx').val(parseInt(intId) + 1);

                customRate.on("keypress keyup blur", function (event) {

                    //this.value = this.value.replace(/[^0-9\.]/g,'');
                    jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g, ''));

                    if ((event.which != 46 || jQuery(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57) && event.which != 8) {
                        event.preventDefault();
                    }
                });

                var count = jQuery('#buildform div').length - 1;
                if (count == 0)
                    jQuery('#scd_buildformTitle').hide();
                else
                    jQuery('#scd_buildformTitle').show();

            });

            // Form submit function for the General settings form, called when "Save changes" button is pressed
            jQuery("#scd_general_options-form").on('submit', function(){
                if(jQuery('#scd_overrideCurrencyOptions').prop('checked') === true){
                    // save the table of manual options by currency
                    save_manual_currency_options();   
                }
                else{
                    // Set the options to zero
                    jQuery('#scd_customCurrencyCount').val(0);    
                    jQuery('#scd_customCurrencyOptions').val(""); 
                }
                
            });

            // Function to save the table of manual settings options by currency
            function save_manual_currency_options() {
                var tot = jQuery('#buildform div').length - 1; 
                var count = 0;
                var curr, defa;
                var cusCur = "";

                if (tot > 0) {

                    var maxIdx = parseInt(jQuery('#scd_buildformNextIdx').val()) - 1;

                    for(index = 0; index <= maxIdx; index++) {
                        curr = '#scd_customCurrency' + index;

                        if (index !== 0 && jQuery(curr).val()) {
                            defa = '#scd_default';

                            var attributes = {
                                              rate: jQuery('#scd_customRate' + index).val(), 
                                              pos:  jQuery('#scd_customPosition' + index).val(), 
                                              inc:  jQuery('#scd_customIncrease' + index).val(), 
                                              sym: jQuery('#scd_customSymbol' + index).val()
                                            };

                            if (cusCur != "") {
                                cusCur += ',';
                            }

                           cusCur+="\"" + jQuery(curr).val() + "\":" + JSON.stringify(attributes) 
                           count++;
                        } 
                    }; 

                    cusCur = "{" + cusCur + "}";
                    
                }

                jQuery('#scd_customCurrencyCount').val(count);
                jQuery('#scd_customCurrencyOptions').val(cusCur);
            }

            var count = jQuery('#buildform div').length - 1;
            if (count == 0)
                jQuery('#scd_buildformTitle').hide();
            else
                jQuery('#scd_buildformTitle').show();

        });

    </script>

    <div id="allCustom2" <?php echo ((scd_isChecked('scd_general_options', 'overrideCurrencyOptions')==false) ? 'style="display: none"' : "") ?> style="width: 100%;">

        <div class="help-tip">
            <p>Add each currency for which you want to manually specify the exchange rate, a percentage based price increase, or the display format. For currencies not included in the table, the normal exchange rate and default display format will be used</p>
        </div>

        <input id="scd_addCurrency" name='scd_general_options[addCurrency]' style="margin-left: 20px" class="buttons" type='button' value='Add Currency' />
        <div style="margin-left: 20px; display: inline-block;"> <?php _e('Default currency: <b>' . get_option('woocommerce_currency') . '</b>', 'ch_scc_woo') ?>  </div>

        <fieldset id="buildform" style="width: 100%;">
            <div class="scd_title" id="scd_buildformTitle" style="width: 100%;">
                <span style="font-weight: 800;padding-right: 160px;padding-left: 50px;vertical-align: unset;">Currency</span>
                <span class="help-tip" style="margin-left: 5px;margin-top: 2px;"><p>Set the exchange rate to use for this currency (e.g. 0.74) or leave empty to use the normal exchange rate</p></span>
                <span style="font-weight: 800;padding: 0 25px;vertical-align: unset;" >Rate</span> 
                <span class="help-tip" style="margin-left: 5px;margin-top: 2px;"><p>Set an additionnal percentage to apply on top of the exchange rate</p></span>
                <span style="font-weight: 800;padding: 0 25px;vertical-align: unset;">Increase</span>
                <span class="help-tip" style="margin-left: 5px;margin-top: 2px;"><p>Set a custom currency symbol for this currency or leave empty to use the default symbol</p></span>
                <span style="font-weight: 800;padding-left:25px;padding-right: 5%;vertical-align: unset;">Symbol</span>
                <span class="help-tip" style="margin-left: 5px;margin-top: 2px;"><p>Set the currency symbol position for display. Select default to use the defaut woocommerce setting</p></span>
                <span style="font-weight: 800;padding: 0 25px;vertical-align: unset;">Position</span>
            </div>
            <input id="scd_default" type="hidden" value="<?php echo get_option('woocommerce_currency'); ?>" />
            <input id="scd_customCurrencyCount" name="scd_general_options[customCurrencyCount]" type="hidden" value="<?php $customNbr = ($customNbr > 0) ? $customNbr : "0";
            echo $customNbr;
            ?>" />
            <input id="scd_customCurrencyOptions" name="scd_general_options[customCurrencyOptions]" type="hidden" value="<?php if ($customNbr > 0) echo (htmlspecialchars($customAll)); ?>" />
            <input id="scd_buildformNextIdx" type="hidden" value="1" />
        </fieldset>

    </div>
    <?php
}

/**
 * Render setting: Delete plugin data on uninstall
 */  
function scd_form_render_deleteDataOnUninstall() {
    ?>
    <div class="help-tip">
        <p>If this setting is checked, uninstalling the plugin will delete all plugin data including all your custom settings. If you want to be able to reinstall the plugin and maintain all your settings, uncheck this box </p>
    </div>

    <input id="scd_deleteDataOnUninstall" name='scd_general_options[deleteDataOnUninstall]' style="margin-left: 20px; margin-top: 3px;" type='checkbox' value='1' <?php scd_echoChecked('scd_general_options', 'deleteDataOnUninstall'); ?>/>

    <?php
}

// This function is not used
function scd_form_render_customClasses() {

    $custClasses = get_option('scd_general_options');
    $custClasses = $custClasses['customClasses'];
    ?>


    <textarea id="scd_customClasses" name='scd_general_options[customClasses]' type='text'><?php echo $custClasses; ?></textarea><br>
    <p><small><?php _e('By default, it is assumed that all pricetags will have a CSS class named "amount". But in some rare themes, pricetags can have different classes. If the tooltip is not showing on any particular price, enter the CSS selector of that pricetag here. You can enter multiple CSS selectors (comma-seperated).', 'ch_scd_woo') ?> </small></p>
    <p><small><?php _e('See documentation for details.', 'ch_scd_woo') ?> </small></p>


    <?php
}

   ?>
