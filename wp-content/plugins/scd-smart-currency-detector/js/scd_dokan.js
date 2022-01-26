open: function () {
    jQuery(this.content).closest('.mfp-wrap').removeAttr('tabindex');
    Dokan_Editor.loadSelect2();

    jQuery("#scd-wcv-select-currencies").data("placeholder", "Set currency per product...").chosen();
    jQuery(".scd_wcv_select").change(function () {
        var key = '';
        var newKeys, oldKeys;

        oldKeys = jQuery('#scd-bind-select-curr').val().toString().split(',');

        if (!jQuery('#scd-wcv-select-currencies').val() == '')
            newKeys = jQuery('#scd-wcv-select-currencies').val().toString().split(',');
        else {
            newKeys = '';
        }
        if (jQuery('#scd-bind-select-curr').val() !== '') {

            if (newKeys.length >= oldKeys.length) {

                if (newKeys.length > 0) {
                    key = newKeys[newKeys.length - 1];
                }

                var myregselect = '<option id="reg_' + key + '" value=' + key + ' >Regular price (' + key + ')</option>';
                //var myfieldregselect = '<input type="hidden" id="regularField_' + key + '" name="regularField_' + key + '" value=""/>';
                var mysalselect = '<option id="reg_' + key + '" value=' + key + ' >Sale price (' + key + ')</option>';
                //var myfieldsalselect ='<input type="hidden" id="saleField_' + key + '" name="saleField_' + key + '" value=""/>';

                jQuery('#scd_regularCurrency').append(myregselect);
                jQuery('#scd_saleCurrency').append(mysalselect);

//        jQuery('#bind-scd-price-curr').append(myfieldregselect);
//        jQuery('#bind-scd-price-curr').append(myfieldsalselect);
                jQuery('#scd-bind-select-curr').val(jQuery('#scd-wcv-select-currencies').val());
            } else {
                for (var k = 0; k < oldKeys.length; k++) {
//                    var tes=newKeys.indexOf(oldKeys[k]);
//                    alert(tes);
                    if (newKeys.indexOf(oldKeys[k]) == -1) {

                        jQuery('#scd_regularCurrency option[value="' + oldKeys[k] + '"]').remove();
                        jQuery('#scd_saleCurrency option[value="' + oldKeys[k] + '"]').remove();
//                      jQuery('input[id="regularField_'+oldKeys[k]+'"]').remove();
//                       jQuery('input[id="saleField_'+oldKeys[k]+'"]').remove();
                    }
                }
            }
        } else {
            if (newKeys.length > 0) {
                key = newKeys[newKeys.length - 1];
            }
            var myregselect = '<option id="reg_' + key + '" value=' + key + ' >Regular price (' + key + ')</option>';
            //var myfieldregselect = '<input type="hidden" id="regularField_' + key + '" name="regularField_' + key + '" value=""/>';
            var mysalselect = '<option id="sale_' + key + '" value=' + key + ' >Sale price (' + key + ')</option>';
            //var myfieldsalselect ='<input type="hidden" id="saleField_' + key + '" name="saleField_' + key + '" value=""/>';

            jQuery('#scd_regularCurrency').append(myregselect);
            jQuery('#scd_saleCurrency').append(mysalselect);
//        jQuery('#bind-scd-price-curr').append(myfieldregselect);
//        jQuery('#bind-scd-price-curr').append(myfieldsalselect);

            jQuery('#scd-bind-select-curr').val(jQuery('#scd-wcv-select-currencies').val());

        }

        if (jQuery(this).val() !== null) {
            var tabCurr = jQuery(this).val().toString().split(',');
            if (tabCurr.length > 0) {
                var regularBloc = '';
                var saleBloc = '';
                var newpriceField = '';
                var priceField = jQuery('#priceField').val();
                var tabC;
                for (var i = 0; i < tabCurr.length; i++) {
                    regularBloc = 'regular_' + tabCurr[i] + '_';
                    saleBloc = '-sale_' + tabCurr[i] + '_';
                    var regularPrice = '', salePrice = '';
                    if (priceField.indexOf(regularBloc) > -1) {
                        regularPrice = priceField.substr(priceField.indexOf(regularBloc) + regularBloc.length,
                                priceField.indexOf(saleBloc) - priceField.indexOf(regularBloc) - regularBloc.length);

                        tabC = priceField.toString().split(',');
                        var pos = -1;
                        for (var j = 0; j < tabC.length; j++) {
                            if (tabC[j].indexOf('sale_' + tabCurr[i]) > -1) {
                                pos = j;
                            }
                        }

                        if (pos > -1) {
                            var tc = tabC[pos].toString().split('_');
                            if (tc.length > 0) {
                                salePrice = tc[tc.length - 1];
                            }
                        }
                    }
                    if (i == 0) {
                        newpriceField = 'regular_' + tabCurr[i] + '_' + regularPrice + '-sale_' + tabCurr[i] + '_' + salePrice;
                    } else {
                        newpriceField = newpriceField + ',regular_' + tabCurr[i] + '_' + regularPrice + '-sale_' + tabCurr[i] + '_' + salePrice;
                    }
                }
                jQuery('#priceField').val(newpriceField);
            }
        }

    });

    // binding '#scd_regularCurrency' and #scd_saleCurrency'
    jQuery('#scd_regularCurrency').change(function () {
        jQuery('#scd_saleCurrency').val(jQuery('#scd_regularCurrency').val()).change();
        //jQuery('#scd_regularPriceCurrency').val( jQuery('#regularField_'+jQuery('#scd_regularCurrency').val()).val());
        //jQuery('#scd_salePriceCurrency').val( jQuery('#saleField_'+jQuery('#scd_saleCurrency').val()).val());
        var priceField = jQuery('#priceField').val();

        var regularBloc = 'regular_' + jQuery('#scd_regularCurrency').val() + '_';
        var saleBloc = '-sale_' + jQuery('#scd_regularCurrency').val() + '_';
        var price = priceField.substr(priceField.indexOf(regularBloc) + regularBloc.length,
                priceField.indexOf(saleBloc) - priceField.indexOf(regularBloc) - regularBloc.length);
        jQuery('#scd_regularPriceCurrency').val(price);

        var tabCurr = priceField.toString().split(',');
        var pos = -1;
        for (var j = 0; j < tabCurr.length; j++) {
            if (tabCurr[j].indexOf('sale_' + jQuery('#scd_saleCurrency').val()) > -1) {
                pos = j;
            }
        }

        if (pos > -1) {
            var tc = tabCurr[pos].toString().split('_');
            if (tc.length > 0) {
                jQuery('#scd_salePriceCurrency').val(tc[tc.length - 1]);

            }
        }

    });
    // end binding

    // start save regular price entered for each currency when hoverout field  
    jQuery('#scd_regularPriceCurrency').focusout(function () {
        // jQuery('#regularField_'+jQuery('#scd_regularCurrency').val()).val(jQuery(this).val());

        var priceField = jQuery('#priceField').val();
        var regularBloc = 'regular_' + jQuery('#scd_regularCurrency').val() + '_';
        var saleBloc = '-sale_' + jQuery('#scd_regularCurrency').val() + '_';

        priceField = priceField.substr(0, priceField.indexOf(regularBloc)) + regularBloc + jQuery(this).val() +
                priceField.substr(priceField.indexOf(saleBloc));
        jQuery('#priceField').val(priceField);

    });
    // end save regular price

    // start save sale price entered for each currency when hoverout field  
    jQuery('#scd_salePriceCurrency').focusout(function () {
        //jQuery('#saleField_'+jQuery('#scd_saleCurrency').val()).val(jQuery(this).val());

        var priceField = jQuery('#priceField').val();
        var tabCurr = priceField.toString().split(',');
        var pos = -1;
        for (var j = 0; j < tabCurr.length; j++) {
            if (tabCurr[j].indexOf('sale_' + jQuery('#scd_saleCurrency').val()) > -1) {
                pos = j;
            }
        }
        if (pos > -1) {
            tabCurr[pos] = tabCurr[pos].substr(0, tabCurr[pos].indexOf('sale')) + 'sale_' + jQuery('#scd_saleCurrency').val() + '_' + jQuery(this).val();
            priceField = tabCurr[0];
            for (var j = 1; j < tabCurr.length; j++) {
                priceField = priceField + ',' + tabCurr[j];
            }

            jQuery('#priceField').val(priceField);
        }
    });
    // end save sale price
}