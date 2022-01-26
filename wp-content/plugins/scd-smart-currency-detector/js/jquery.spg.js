/*
 * 
 * SCD - Smart Currency Detector | Version 3.3 | July 29, 2017
 * A jQuery plugin for compatibilities beetwen SCD and Smart payment Gateways
 * Developed by GaJeLabs.
 * 
 * 
 * 
 */
DEBUG = true;

jQuery( document ).ready(function() {
    if (DEBUG) console.log( "ready!" );
//get param from client side
    var lsIndex;
    var scd_target_currency = localStorage.getItem('scd_target_currency');
    var scd_base_currency = localStorage.getItem('scd_base_currency');
lsIndex = "scd_" + scd_base_currency + scd_target_currency;
    if (DEBUG) console.log( "got "+  lsIndex);
var rate = parseFloat(localStorage.getItem(lsIndex));
    if (DEBUG) console.log( "got rate "+  rate);
jQuery.post(
  ajaxurl,
    {
        'action': 'scd_get_action',
        'target_currency':scd_target_currency,
        'therate':rate
    },
    function(response){
            console.log(response);
        }
);
});
