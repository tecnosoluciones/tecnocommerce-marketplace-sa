/**Get rates**/

if(typeof ajaxurl !== 'undefined' && ajaxurl !== null){
	var ajaxurl = scd_urls['ajaxurl'];
}

var Currency = {
    rates: {"USD":1.0,"EUR":1.18421,"GBP":1.30853,"CAD":0.753668,"ARS":0.0136696,"AUD":0.717205,"BRL":0.184228,"CLP":0.00125391,"CNY":0.143871,"CYP":0.397899,"CZK":0.0453911,"DKK":0.159012,"EEK":0.0706676,"HKD":0.129018,"HUF":0.00341707,"ISK":0.00734145,"INR":0.0133551,"JMD":0.0067488,"JPY":0.00938366,"LVL":1.57329,"LTL":0.320236,"MTL":0.293496,"MXN":0.0454788,"NZD":0.654061,"NOK":0.112477,"PLN":0.269356,"SGD":0.729408,"SKK":21.5517,"SIT":175.439,"ZAR":0.0575609,"KRW":0.000842066,"SEK":0.115084,"CHF":1.1001,"TWD":0.033964,"UYU":0.0234982,"MYR":0.238484,"BSD":1.0,"CRC":0.0016729,"RON":0.24492,"PHP":0.0205215,"AED":0.272294,"VEB":0.000100125,"IDR":6.70218e-05,"TRY":0.135771,"THB":0.0321088,"TTD":0.148051,"ILS":0.293738,"SYP":0.00195306,"XCD":0.37037,"COP":0.000263642,"RUB":0.0137209,"HRK":0.157219,"KZT":0.00238379,"TZS":0.000430394,"XPT":951.599,"SAR":0.266667,"NIO":0.0289044,"LAK":0.000110159,"OMR":2.60078,"AMD":0.00206258,"CDF":0.000510436,"KPW":0.00111111,"SPL":6.0,"KES":0.00922093,"ZWD":0.00276319,"KHR":0.000246792,"MVR":0.0638163,"GTQ":0.129829,"BZD":0.496229,"BYR":4.06504e-05,"LYD":0.730087,"DZD":0.00774188,"BIF":0.000521342,"GIP":1.30853,"BOB":0.145032,"XOF":0.00180532,"STD":4.83069e-05,"NGN":0.00258914,"PGK":0.288001,"ERN":0.0666667,"MWK":0.0013378,"CUP":0.0377358,"GMD":0.0193035,"CVE":0.0107392,"BTN":0.0133551,"XAF":0.00180532,"UGX":0.000272399,"MAD":0.108589,"MNT":0.000350864,"LSL":0.0575609,"XAG":26.4469,"TOP":0.426002,"SHP":1.30853,"RSD":0.01007,"HTG":0.00889963,"MGA":0.000259575,"MZN":0.0140922,"FKP":1.30853,"BWP":0.0862206,"HNL":0.0407561,"PYG":0.000143186,"JEP":1.30853,"EGP":0.0628416,"LBP":0.00066335,"ANG":0.559441,"WST":0.3846,"TVD":0.717205,"GYD":0.00475102,"GGP":1.30853,"NPR":0.00830797,"KMF":0.00240709,"IRR":2.37952e-05,"XPD":2127.4,"SRD":0.13468,"TMM":5.71431e-05,"SZL":0.0575609,"MOP":0.12526,"BMD":1.0,"XPF":0.00992369,"ETB":0.0281537,"JOD":1.41044,"MDL":0.060241,"MRO":0.002661,"YER":0.00402414,"BAM":0.605477,"AWG":0.558659,"PEN":0.2799,"VEF":0.100125,"SLL":0.000102785,"KYD":1.21951,"AOA":0.00175,"TND":0.364899,"TJS":0.0969503,"SCR":0.0541658,"LKR":0.00543549,"DJF":0.00561799,"GNF":0.000103978,"VUV":0.00884891,"SDG":0.0181447,"IMP":1.30853,"GEL":0.325436,"FJD":0.46833,"DOP":0.0171059,"XDR":1.40968,"MUR":0.025,"MMK":0.000734891,"LRD":0.00506313,"BBD":0.5,"ZMK":5.36197e-05,"XAU":1944.96,"VND":4.30661e-05,"UAH":0.0363308,"TMT":0.285715,"IQD":0.000840149,"BGN":0.605477,"KGS":0.0128205,"RWF":0.00105698,"BHD":2.65957,"UZS":9.77354e-05,"PKR":0.00595581,"MKD":0.0191608,"AFN":0.0129736,"NAD":0.0575609,"BDT":0.0117876,"AZN":0.588237,"SOS":0.00173294,"QAR":0.274725,"PAB":1.0,"CUC":1.0,"SVC":0.114286,"SBD":0.12155,"ALL":0.00955511,"BND":0.729408,"KWD":3.2637,"GHS":0.175371,"ZMW":0.0536197,"XBT":11789.0,"NTD":0.0337206,"BYN":0.406504,"CNH":0.144081,"MRU":0.02661,"STN":0.0483069,"VES":3.48776e-06,"MXV":0.294184},
    convert: function(amount, from, to) {
      return (amount * this.rates[from]) / this.rates[to];
    }
  };
 var res = "";
 var reponse =  jQuery.get("https://blockchain.info/tobtc?currency=USD&value=1", function(){
     res = reponse['responseText']; 
 });
var c = 0;
var dev = '';
/** setting save price **/
function save_price(currency){
    var p = jQuery('.amount:not([basecurrency="'+currency+'"])');
    var len = p.length;
    for (var i=0; i<len; i++) {
    window.localStorage.setItem('price'+i, scd_extractPriceValueFromHtml(p));
    }
}

/**BTC converter */
function btc_converter(obj,ii){
    curr = 'BTC';
    price = (localStorage['price'+ii] * Currency.rates[settings.baseCurrency]) / Currency.rates['USD'];
    var rate = res; 
    price = price * rate;
//    price = price.toFixed(10);
    price = scd_humanizeNumber(price);
    // Add currency symbol
    var currency_attributes = scd_get_currency_symbol(curr);
    
    if((jscd_options.useCurrencySymbol) && (currency_attributes.symbol !== undefined)) {
    
    currency_symbol = '<span class="woocommerce-Price-currencySymbol">' + currency_attributes.symbol + '</span>';
    
    currency_code = '<span class="woocommerce-Price-currencySymbol">' + curr + '</span>';
    
    switch (currency_attributes.position) {
    case 'left':
    price = currency_symbol + price;
    break;
    case 'right':
    price = price + currency_symbol;
    break;
    case 'left_space':
    price = currency_symbol + ' ' + price;
    break;
    case 'right_space':
    price = price + ' ' + currency_symbol;
    break;
    case 'left_country':
    price = currency_code + ' ' + price + currency_symbol;
    break;
    case 'right_country':
    price = currency_symbol + price + ' ' + currency_code;
    break;
    default:
    price = price + currency_symbol;
    break;
    }
    }
    else
    {
    price = price + '<span class="scd-currency-symbol">' + ' ' + curr + '</span>';
    }
    
    jQuery(obj).html(price);
    
    jQuery(obj).attr('basecurrency', curr); // this ensures that we will not convert this element again

}

function scd_humanizeNumber(n) {
n = n.toString()
// Split to separate the decimal part
strSplit = n.split('.')
n = strSplit[0]
while (true) {
var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, '$1,$2$3')
if (n == n2)
break
n = n2
}

if (strSplit.length > 1){
// Add the decimal part
n = n + '.' + strSplit[1]
}

return n
}

/**
* Function to post the target currency to server
*/
function scd_send_target_currency_to_server() {

    // If a target currency is set in the localStorage object,
    // post the new currency to the server.
    var target_currency = scd_getTargetCurrency();

    if(target_currency !== null){
    // Post the target currency
        jQuery.post(ajaxurl,
            {
            'action': 'scd_load_target_currency',
            'target_currency': target_currency,
            },
            function (response, status) {
                console.log('SCD ajax status=' + response);
                if (status == 'success') {
                //console.log('scd_send_target_currency_to_server '+currency);
                } else {
                    console.log('SCD: post currency failed! status=' + status);
                }
            }
        );
    }
}
//localStorage['scd_rates']
function scd_send_rates_to_server(){
  if(localStorage['scd_rates']!==undefined){
        
  jQuery.post(
ajaxurl,
{
'action': 'scd_load_echange_rates',
'scd_rates': localStorage['scd_rates'],
},
function (response, status) {
console.log('rate sent with success');
if (status == 'success') {
    
} else {
//console.log('SCD: post currency failed! status=' + status);
 }
}
);
  }  
}



var jscd_options = {

baseCurrency: settings.baseCurrency,
multiCurrencyPayment: (settings.multiCurrencyPayment === '1') ? true : false,
autoUpdateExchangeRate: (settings.autoUpdateExchangeRate === '1') ? true : false,
exchangeRateUpdate: settings.exchangeRateUpdate,
exchangeRateUpdateInterval: settings.exchangeRateUpdateInterval,
customCurrencyCount: settings.customCurrencyCount,
customCurrencyOptions: (settings.overrideCurrencyOptions === '1' && settings.customCurrencyOptions !== null) ? settings.customCurrencyOptions : '',
userCurrencyChoice: settings.userCurrencyChoice.split(','),
autodetectLocation: (settings.autodetectLocation === '1') ? true : false,
fallbackCurrency: settings.fallbackCurrency,
decimalNumber: (settings.decimalNumber === '1') ? true : false,
decimalPrecision: settings.decimalPrecision,
priceByCurrency: (settings.priceByCurrency === '1') ? true : false,
role: settings.role,
currencyNumber: settings.currencyNumber,
getUserRole: settings.getUserRole,
isIt: settings.isIt,
thousandSeperator: (settings.thousandSeperator === '1') ? settings.thousandSeperatorToUse : '',
decimalSeperator: settings.decimalSeperator,
useCurrencySymbol: (settings.useCurrencySymbol === '1') ? true : false,
mobilewidget: (settings.mobilewidget === '1') ? true : false,
fallbackPosition: settings.fallbackPosition,
mobilewidgetpopup: (settings.mobilewidgetpopup === '1') ? true : false,
textpopup: settings.textpopup,
currencyPosition: settings.currencyPosition,
tooltipTheme: 'shadow',
tooltipAnimation: 'fade',
animationDuration: 300,
showTooltipArrow: '1',
tooltipPosition: 'top',
tooltipShowDelay: 100,
touchFriendly: '1',
hideTooltipToNativeVisitor: '1',
tooltipAlwaysOpen: '1',
replacedContentFormat: "[convertedCurrencyCode] [convertedAmount]",
scd_currencies: settings.scd_currencies,
enableJsConvert: (settings.enableJsConvert === '1') ? true : false

};

/**
* Get the target currency
*/
function scd_getTargetCurrency()
{
    var target_currency = null;

    if ( scd_isCountryCodeValid() ){
        var country = localStorage['scd_countryCode'];
        target_currency = countryMap[country].currencyCode;
    }else {
        // If autodetection of currency is not enabled, we use the fallback currency
        if(jscd_options.autodetectLocation !== true){
            var country = localStorage['scd_countryCode'];
            if(countryMap[country]!==undefined){
				target_currency = countryMap[country].currencyCode;
			}            
        }else if (localStorage['scd_countryCode'] !== undefined) {
        // There is a country code detected but it is not a valid currency for display. Use fallback currency
            target_currency = jscd_options.fallbackCurrency;
            
        }
      
    }

    return target_currency;
}

/**
* Check if the country code stored locally is a valid value
*
* @return {bool} True or False
*/
function scd_isCountryCodeValid(){
    var isValid = false;

    if( localStorage['scd_countryCode'] === undefined ||
    (localStorage['scd_countryCode'] in countryMap) === false ){
        isValid = false;
    }else{
        var country = localStorage['scd_countryCode'];
        var currency = countryMap[country].currencyCode;

        // If autodetection of currency is not enabled, check the currency against the fallback currency
        if(jscd_options.autodetectLocation !== true){
            if(currency === jscd_options.fallbackCurrency){
                isValid = true;
            }
        }else{
            // Autodetection of currency is enabled:
            // If the filters user display currency setting indicate "all", then the country code is valid
            // If filters user display currency setting specify a limited set of user currencies
            // check if the currency is in the set of currencies
            if(jscd_options.userCurrencyChoice[0] === "allcurrencies" ||
                jscd_options.userCurrencyChoice.includes(currency)){
                isValid = true;
            }
        }
    }
    return isValid;
}

/**
* Function to determine if the conversion rate to convert prices is available
*
* @param {string} target_currency
*/
function scd_isConvertRateAvailable(target_currency) {
if(scd_get_convert_rate(settings.baseCurrency, target_currency) === undefined ){
return false;
}
else{
return true;
}
}

/**
* Extract the price value from the html tag of an amount element
*/
function scd_extractPriceValueFromHtml (obj)
{
var price = jQuery(obj).html();

// The HTML text may have the form :
// <span class="woocommerce-Price-currencySymbol">$</span>425.00
// If that is the case, remove the currencySymbol span
currencySymbol = jQuery(obj).find('.woocommerce-Price-currencySymbol');
if(currencySymbol.length > 0){
// Remove the currencySymbol span
currency_symbol_text = currencySymbol.prop('outerHTML');
price = price.replace(currency_symbol_text, '');
}

// Similarly, if there is a span of class scd-currency-symbol, remove the span
currencySymbol = jQuery(obj).find('scd-currency-symbol');
if(currencySymbol.length > 0){
// Remove the currencySymbol span
currency_symbol_text = currencySymbol.prop('outerHTML');
price = price.replace(currency_symbol_text, '');
}


var decimal_separator = ".";
if(jscd_options.decimalSeperator !==undefined && jscd_options.decimalSeperator !== "")
{
decimal_separator = jscd_options.decimalSeperator;
}

// Strip out every character that is not a digit or the decimal separator character (. or ,)
regexpr = RegExp('[^\\d\\'+ decimal_separator + ']','g');
price = price.replace(regexpr, '');

// Replace the decimal separator character by a dot
price = price.replace(decimal_separator, '.');

return parseFloat(price);
}

/**
* Convert the price of an element for display
*
* @param {object} obj The element to convert
* @param {string} curr The target currency
*/
function scd_simpleConvert(obj, curr,ii) {
var price = scd_extractPriceValueFromHtml(obj);
if(c==0){
    window.localStorage.setItem('price'+ii, price);
}
var base = settings.baseCurrency;
if (jQuery(obj).attr('basecurrency') !== undefined) {
base = jQuery(obj).attr('basecurrency');
}
if(base == curr){
console.log('base currency== target');
// no conversion to perform
return;
}
var rate = scd_get_convert_rate(base, curr);

// Check if there is a manual override options defined for this currency
if(jscd_options.customCurrencyOptions !== "" && jscd_options.customCurrencyOptions[curr] !==undefined) {
var currency_options = jscd_options.customCurrencyOptions[curr];

// If a custom exchange rate has been specified, use it
if((base===settings.baseCurrency) && (currency_options["rate"] !== "")){
rate = parseFloat(currency_options.rate);
}

if (!jQuery(obj).parent('.uwa_inc_latest_price, .woocommerce-message').length) {
    //this condiction is added to make option work suitable with auction product
    //
    // If an increase on top percentage has been specified, apply it
    if(currency_options["inc"] !== ""){
    rate = rate* (1 + parseFloat(currency_options["inc"])/100);
    }
}
}

console.log('Extract price= '+price);
//price = price * rate;
if(curr.indexOf('BTC') !== -1){
    btc_converter(obj,ii);
}else{
   /* shopify API
    price = (localStorage['price'+ii] * Currency.rates[settings.baseCurrency]) / Currency.rates[curr];
    price = price.toFixed(jscd_options.decimalPrecision);
    price = scd_humanizeNumber(price);*/
	price = price * rate;
    price = price.toFixed(jscd_options.decimalPrecision);
    price = scd_humanizeNumber(price);
// Add currency symbol

var currency_attributes = scd_get_currency_symbol(curr);

if((jscd_options.useCurrencySymbol) && (currency_attributes.symbol !== undefined)) {

currency_symbol = '<span class="woocommerce-Price-currencySymbol">' + currency_attributes.symbol + '</span>';

currency_code = '<span class="woocommerce-Price-currencySymbol">' + curr + '</span>';

switch (currency_attributes.position) {
case 'left':
price = currency_symbol + price;
break;
case 'right':
price = price + currency_symbol;
break;
case 'left_space':
price = currency_symbol + ' ' + price;
break;
case 'right_space':
price = price + ' ' + currency_symbol;
break;
case 'left_country':
price = currency_code + ' ' + price + currency_symbol;
break;
case 'right_country':
price = currency_symbol + price + ' ' + currency_code;
break;
default:
price = price + currency_symbol;
break;
}
}
else
{
price = price + '<span class="scd-currency-symbol">' + ' ' + curr + '</span>';
}

jQuery(obj).html(price);

jQuery(obj).attr('basecurrency', curr); // this ensures that we will not convert this element again
}
}

/**
* Get the currency symbol and symbol position to use for a currency
*
* @param {string} currency
*/
function scd_get_currency_symbol (currency)
{
var symbol = currencySymbolMap[currency];
var position = jscd_options.currencyPosition;

// Check if custom options are defined for this currency
if(jscd_options.customCurrencyOptions !== "" && jscd_options.customCurrencyOptions[currency] !==undefined) {
var currency_options = jscd_options.customCurrencyOptions[currency];

// If a custom exchange rate has been specified, use it
if(currency_options["sym"] !== ""){
symbol = currency_options["sym"];
}

// If an increase on top percentage has been specified, apply it
if(currency_options["pos"] !== "" && currency_options["pos"] !== "default"){
position = currency_options["pos"];
}
}

// Return an object made of the two attributes
return {
symbol: symbol,
position: position
};
}

/**
* Convert all amounts displayed on the page
*/
function scd_convert_all_amounts (dev)
{


try{
// This function will go through all objects of class amount and
// convert the prices if not convertd already.
var target_currency = dev;
//var target_currency = scd_getTargetCurrency();
if ( scd_isConvertRateAvailable(target_currency) ) {

var elements = jQuery('.amount-cart:not([basecurrency="'+target_currency+'"]), .amount:not([basecurrency="'+target_currency+'"])');
var len = elements.length;
for (var i=0; i<len; i++) {
        
// There is a special case to handle here: If the element has the class scd-converted
// ( which is added when the price is converted in server side code) but does not
// have the attribute basecurrency (which is also added by the server side code),
// leave the element as is. I have seen wordpress themes (like StoreFront theme) where
// the basecurrency attribute is sometimes stripped by the theme for elements that appear in the minicart.
if( jQuery(elements[i]).hasClass('scd-converted') && (!elements[i].hasAttribute('basecurrency')) ){
// leave as is
} else {
scd_simpleConvert(elements[i], target_currency,i);
}
}
c++;
}
}
catch(err) {
console.log("SCD conversion error : " + err.message);
}
}
// A function to process messages received by the window.
function receiveMessage(e) {

// Check to make sure that this message came from the correct domain.
if (e.origin === "http://simulate.fcf-gajelabs.com"){
localStorage['scd_countryCode']=e.data;
}

}

function scd_wc_pao_conversion(){

var target_currency = scd_getTargetCurrency();
if ( scd_isConvertRateAvailable(target_currency) ) {

var elements = jQuery('.product-addon-totals').find('.amount');
var len = elements.length;

for (var i=0; i<len; i++) {

if( jQuery(elements[i]).hasClass('scd-converted') && (!elements[i].hasAttribute('basecurrency')) ){
// leave as is

} else {
scd_simpleConvert(elements[i], target_currency,i);
//end conversion
}
}

}
}


/**Slider price converter*/
function slider_price_converter(price, curr, base){

var rate = scd_get_convert_rate(base, curr);

// Check if there is a manual override options defined for this currency
if(jscd_options.customCurrencyOptions !== "" && jscd_options.customCurrencyOptions[curr] !==undefined) {
    var currency_options = jscd_options.customCurrencyOptions[curr];

    // If a custom exchange rate has been specified, use it
    if((base===settings.baseCurrency) && (currency_options["rate"] !== "")){
        rate = parseFloat(currency_options.rate);
    }

    // If an increase on top percentage has been specified, apply it
    if(currency_options["inc"] !== ""){
        rate = rate* (1 + parseFloat(currency_options["inc"])/100);
    }
}

console.log('Extract price= '+price);
//price = price * rate;
if(curr.indexOf('BTC') !== -1){
//    btc_converter(obj,ii);
}else{
    price = price * rate;
    price = price.toFixed(jscd_options.decimalPrecision);
    price = scd_humanizeNumber(price);
// Add currency symbol

var currency_attributes = scd_get_currency_symbol(curr);

if((jscd_options.useCurrencySymbol) && (currency_attributes.symbol !== undefined)) {

currency_symbol = '<span class="woocommerce-Price-currencySymbol">' + currency_attributes.symbol + '</span>';

currency_code = '<span class="woocommerce-Price-currencySymbol">' + scd_getTargetCurrency() + '</span>';


switch (currency_attributes.position) {
case 'left':
price = currency_symbol + price;
break;
case 'right':
price = price + currency_symbol;
break;
case 'left_space':
price = currency_symbol + ' ' + price;
break;
case 'right_space':
price = price + ' ' + currency_symbol;
break;
case 'left_country':
price = currency_code + ' ' + price + currency_symbol;
break;
case 'right_country':
price = currency_symbol + price + ' ' + currency_code;
break;
default:
price = price + currency_symbol;
break;
}
}
else
{
price = price + '<span class="scd-currency-symbol">' + ' ' + curr + '</span>';
}

return price; // this ensures that we will not convert this element again
}
}

jQuery(document).ready(function () {

    //Quick view pop up theme compatibility
    var element = jQuery('.quick-view');
    for (let i = 0; i < element.length; i++) {
        jQuery(element[i]).click(() => {
            const interval = setInterval(function () {
                if (jQuery('.product-quick-view-container').length > 0) {
                    console.log(jQuery('.product-quick-view-container'));
                    var elements = jQuery('.product-quick-view-container').find('.amount');
                    var len = elements.length;
                    for (var i = 0; i < len; i++) {
                        scd_simpleConvert(elements[i], target_currency, i);
                    }
                    clearInterval(interval);
                }
            }, 500);
        });
    }
    //End of Quick view pop up theme compatibility
	  
   if(scd_getTargetCurrency() !== null && jQuery('#min_price').val() !== null ){
        //scd_fetchRates_from_shpy();
        var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
        
        var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
        
        price = '<span class="scd-currency-symbol">'+p1+' - '+p2+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
     }
    
   //scd_fetchRates_from_shpy();
   
    save_price(settings.baseCurrency);
dev = scd_getTargetCurrency(); 
//listening dor event when simulate site
// Setup an event listener that calls receiveMessage() when the window
// receives a new MessageEvent.
window.addEventListener('message', receiveMessage);

// Convert amounts on page /
if(jscd_options.enableJsConvert === true && jscd_options.autodetectLocation === true)
{
// Temporarily hide elements in the page that contains amount
// jQuery(".amount").hide();

// Convert all non-converted amounts elements in the page.
var target_currency = scd_getTargetCurrency();
if(target_currency === null){
scd_fetch_location(jscd_options);
}
else {
scd_convert_all_amounts(dev);
}

// Un-hide elements
// jQuery(".amount").show();

// Attach a listener to the found_variation trigger.
// This trigger is invoked when the user selects a variation
// for a variable product.
// We have to convert the variation price displayed.
jQuery(this).on('found_variation', function (event, variation) {
scd_convert_all_amounts(dev); // Convert the non-converted amounts
});

// Attach a listener to the updated_checkout trigger.
// This trigger is invoked in the checkout page after an action that result in an update
// of the order totals e.g. add/remove coupon, chnag shipping, change country
// We have to convert the new order totals/subtotals displayed.
jQuery(this).on('updated_checkout', function () {
scd_convert_all_amounts(dev); // Convert the non-converted amounts
});

// Attach a listener to the updated_cart_totals trigger.
// This trigger is invoked in the Cart page after an action that result in an update
// of the order or cart totals e.g. add/remove coupon, change shipping, change country
// We have to convert the new order totals/subtotals displayed.
jQuery(this).on('updated_cart_totals', function () {
scd_convert_all_amounts(dev); // Convert the non-converted amounts
});

// Attach a listener to the ajax_complete event.
// There are certain AJAX calls that result in new prics being fectched and sisplayed:
// - Wordpress Themes that use the mini-cart window may use ajax calls to update the
// elements in the mini-cart window and update the total displayed after an item
// is added or remove to/from the cart. If that happens, we have to convert the prices
// displayed in the mini cart again.
// - Certain themes have a quick view button that uses AJAX call, we have to run
// javascript to convert the prices displayed in the quick view window.
jQuery(document).ajaxComplete(function (event, xhr, settings) {
if ((settings.url.indexOf('wc-ajax=add_to_cart') > -1) ||
(settings.url.indexOf('wc-ajax=remove_from_cart') > -1) ||
(settings.url.indexOf('wc-ajax=get_refreshed_fragments') > -1) ||
(settings.url.indexOf('product_quick_view') > -1) )
{
scd_convert_all_amounts(dev); // Convert the non-converted amounts
}
});

// Attach a listener to custom SCD event triggered when the country code is updated
jQuery(document).on('scd:scd_country_code_updated', function (event, new_value) {
scd_send_target_currency_to_server();
scd_convert_all_amounts(dev);
});

}


// Convert amounts on page when scd mobile widget enable /
if(jscd_options.mobilewidget)
{

    // Attach a listener to custom SCD event triggered when the SCD widget currency is updated
    jQuery(document).on('scd:scd_widget_currency_updated', function (event, country) {
       
        country = country.toUpperCase();
        if(countryMap[country].currencyCode !== scd_getTargetCurrency()){
            localStorage['scd_countryCode'] = country;
            // Post updated curency to server
            scd_send_target_currency_to_server();
            // Reload page (Note: As future improvement we could use AJAX to update the prices
            // without having to reload the enitire page )
            setTimeout(function () {
                location.reload(true);
            }, 2000);
        }
    });

}

//Post currency to server
scd_send_target_currency_to_server();

// Refresh local rates if applicable
scd_refresh_local_rates();
//localStorage['scd_rates']
scd_send_rates_to_server();
//listening currency menu and update the target currency depeding user choice

jQuery('.scd-curr-item > a').click(function(){

var currcode=jQuery(this).text().toString();
	
	var country='';
	for(const iterator in countryMap){
		if(currcode.includes(countryMap[iterator].currencyCode)){
			console.log(countryMap[iterator].currencyCode);
			country=iterator;
		}
	}

if(country!==''){

localStorage['scd_countryCode'] = country;

// Post updated curency to server
scd_send_target_currency_to_server();

// Reload page (Note: As future improvement we could use AJAX to update the prices
// without having to reload the enitire page )
setTimeout(function () {
location.reload(true);
}, 2000);
}
});

jQuery('a.scd-sidebar-currency').click(function(e){
e.preventDefault();
var country=jQuery(this).attr('href');
// Send event
if(country.indexOf('bc') !== -1){
    dev="BTC";
    scd_convert_all_amounts(dev);
    jQuery('.scd_float').text(dev);
}else{
    jQuery(document).trigger("scd:scd_widget_currency_updated", country);    
}
});

});




jQuery(window).load(function () {

// Convert all amounts elements in the page.
// Note: We already convert amounts in the document ready function.
// However it is possible with certain themes that some elements, like the items displayed in
// the mini cart window are added to the page after our document ready function has executed.
// If that happens we have to convert the amounts displayed in the new elements.
    if (jscd_options.enableJsConvert) {
        if (scd_getTargetCurrency() !== null) {
            scd_convert_all_amounts(scd_getTargetCurrency());
            jQuery('.scd_float').text(scd_getTargetCurrency());
        } else {
            jQuery('.scd_float').text('SCD');
        }
    } else {
        jQuery('.scd_float').text('SCD');
    }

	if(document.getElementsByClassName('price_slider_wrapper')[0]!=undefined){

	//slider price moving convertion
document.getElementsByClassName('price_slider_wrapper')[0].onmouseout = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };
	
	document.getElementsByClassName('price_slider_wrapper')[0].onmousemouve = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };
	
	
	
	
	document.getElementsByClassName('price_slider')[0].onmouseout = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };
	
	document.getElementsByClassName('price_slider')[0].onmousemouve = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };

	
	
	
	document.getElementsByClassName('ui-slider-range')[0].onmouseleave = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };
	
	document.getElementsByClassName('ui-slider-range')[0].onmousemouve = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };
	
	
	var elements = document.getElementsByClassName('ui-slider-handle');
     var len = elements.length;
	for (var i=0; i<len; i++) {
		 elements[i].onmouseleave = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };
		
		elements[i].onmouseout = function(){
var p1 = slider_price_converter(parseFloat(jQuery('#min_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p1 = ((p1 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p1);		 
var p2 = slider_price_converter(parseFloat(jQuery('#max_price').val()),scd_getTargetCurrency(),settings.baseCurrency);
			p2 = ((p2 * document.getElementsByClassName('ui-slider-range')[0].style.left / 100) + p2);
			 
			 price = '<span class="scd-currency-symbol">'+p1.replace("NaN","")+' - '+p2.replace("NaN","")+'</span>';
        
        jQuery('.price_slider_wrapper .price_slider_amount .price_label').html(price);
		 };
	 }
}
});


/******************************************************************
  scd js Conversion and returns the price and not the object
********************************************************************/
function scd_price_converter(price, curr, base){

    var rate = scd_get_convert_rate(base, curr);
    
    // Check if there is a manual override options defined for this currency
    if(jscd_options.customCurrencyOptions !== "" && jscd_options.customCurrencyOptions[curr] !==undefined) {
        var currency_options = jscd_options.customCurrencyOptions[curr];
    
        // If a custom exchange rate has been specified, use it
        if((base===settings.baseCurrency) && (currency_options["rate"] !== "")){
            rate = parseFloat(currency_options.rate);
        }
    
        // If an increase on top percentage has been specified, apply it
        if(currency_options["inc"] !== ""){
            rate = rate* (1 + parseFloat(currency_options["inc"])/100);
        }
    }
    
    console.log('Extract price= '+price);
    //price = price * rate;
    if(curr.indexOf('BTC') !== -1){
    //    btc_converter(obj,ii);
    }else{
        price = price * rate;  
   		price = price;
    return price; // this ensures that we will not convert this element again
    }
}
/******************************************************************
  End scd js Conversion and returns the price and not the object
********************************************************************/

