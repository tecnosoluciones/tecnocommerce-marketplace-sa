/*-------------------------------------------------------*/
/* This module defines routines to fetch data            */
/*-------------------------------------------------------*/

if(typeof ajaxurl === 'undefined' && ajaxurl === null){
	var ajaxurl = scd_urls['ajaxurl'];
}
var SCD_DEBUG = true;

/**
 * Fetch location and rates and save to local storage
 * 
 * @param {object} scdOptions 
 */
function scd_fetch_location(scdOptions) {

    // Request user location using first method
    var countryRequest1 = jQuery.get("//ipinfo.io/json", function (localdata) {

        if (localdata.country === undefined) {
            // Location detection failed in first request

            if (SCD_DEBUG)
                console.log("Location detection failed in first request, second request init...");

            // Send a 2nd request at an alternate address
            scd_requestUserLocationSecondAttempt(scdOptions);

        } else {

            if (SCD_DEBUG)
                console.log("Location detection success in first request");

            scd_handleLocationDetectionComplete(true, localdata.country, scdOptions);

        }
    });

    // Fail function associated with the country request 1
    countryRequest1.fail(function () {

        // Send a 2nd requst at an alternate address
        scd_requestUserLocationSecondAttempt(scdOptions);

    });
}

/**
 * Fetch user location using second address
 * 
 * @param {Object} scdOptions 
 */
function scd_requestUserLocationSecondAttempt(scdOptions) {
    // Send a 2nd request
    var countryRequest2 = jQuery.get("//api.hostip.info/country.php");

    // Success function associated with the country request 2
    countryRequest2.done(function (localdata) {

        if (localdata === 'XX') {
            // Location detection failed in second request

            if (SCD_DEBUG)
                console.log("Location detection failed in second request");

            scd_handleLocationDetectionComplete(false, "", scdOptions);

        } else {

            if (SCD_DEBUG)
                console.log("Location detection success in second request");

            scd_handleLocationDetectionComplete(true, localdata, scdOptions);
        }
    });

    countryRequest2.fail(function () {

        if (SCD_DEBUG)
            console.log("Location detection failed in second request");

        scd_handleLocationDetectionComplete(false, "", scdOptions);
    });
}

/**
 * Handle user location detected successfully
 * 
 * @param {bool}   bSuccessful      True if the location detection was succesful, False otherwise
 * @param {string} countryCode      The location country code obtained
 * @param {object} scdOptions   SCD options
 */
function scd_handleLocationDetectionComplete(bSuccessful, countryCode, scdOptions) {

    if (bSuccessful)
    {
        // Save in localStorage and process
        localStorage['scd_countryCode'] = countryCode;

        // Trigger event
        if (countryCode in countryMap) {
            jQuery(document).trigger("scd:scd_country_code_updated", countryCode);
        }

        // Save cookie
        //  var date = new Date();
        //  date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
        //  var expires = "; expires=" + date.toGMTString();
        //  document.cookie = "scd_countryCode" + "=" + countryCode + expires + "; path=/wordpress/";
    }

}

/** Local variable to store rates received from server */
var scd_local_rates;

/*
 * Initialize the local rates variable
 */
function scd_init_local_rates()
{

    if (scd_local_rates === undefined) {
        scd_local_rates = {};
        if (localStorage.getItem('scd_rates') !== null) {
            scd_local_rates.data = JSON.parse(localStorage['scd_rates']);
        }
        else {
            scd_local_rates.data = scd_default_exchange.rates;
            scd_local_rates.data["base"] = scd_default_exchange.base;
            scd_local_rates.data["timestamp"] = scd_default_exchange.timestamp;
        }
    }
}

/** 
 * Function to load the updated rates from server
 */
function scd_refresh_local_rates() {

    var timeNow = Math.floor(Date.now() / 1000);
    // Check if rates are defined in session storage, or if
    // rates are older than 12 hours.
    if ((localStorage.getItem('scd_rates') === null) ||
            (localStorage.getItem('scd_last_rate_update') === null) ||
            ((timeNow - localStorage['scd_last_rate_update']) > 6*3600))
    { console.log('updating rates in progress......'); 
//start request
        jQuery.cachedScript = function (url, options) {

            // Allow user to set any option except for dataType, cache, and url
            options = jQuery.extend(options || {}, {
                dataType: "script",
                cache: true,
                url: url
            });

            // Use $.ajax() since it is more flexible than $.getScript
            // Return the jqXHR object so we can chain callbacks
            return jQuery.ajax(options);
        };
// Usage
        jQuery.cachedScript("https://cdn.shopify.com/s/javascripts/currencies.js").done(function (script, textStatus) {
           console.log('fetch rates '+textStatus);
            // Save the rates in the session object
            var sh_rates={};
            sh_rates['base']='USD';
            
            jQuery.each(Currency.rates, function (index, item) {
                sh_rates[index] = 1 / item;
                //console.log(index+'='+sh_rates[index]);
             });
            
            localStorage['scd_rates'] = JSON.stringify(sh_rates);
            
            localStorage['scd_last_rate_update'] = Math.floor(Date.now() / 1000);
            sh_rates['timestamp']=localStorage['scd_last_rate_update'];
            // Update the local rates variable
            if (scd_local_rates === undefined) {
                scd_local_rates = {};
            }
            //if(sh_rates.length>0){
            scd_local_rates.data =  sh_rates; 
            //console.log('rates='+scd_local_rates.data);
            //}
            
            console.log('SCD: rates updated');

        }).fail(function (jqxhr, settings, exception) {
          jQuery.post(
            ajaxurl,
            {
                'action': 'scd_ajax_load_rates',
            }, 
            function (response, status) {
                if (status == 'success') {
                    // Save the rates in the session object
                    localStorage['scd_rates'] = response; 
                    localStorage['scd_last_rate_update'] = Math.floor(Date.now() / 1000);
                    // Update the local rates variable
                    if(scd_local_rates === undefined){
                        scd_local_rates = {};
                    }
                    scd_local_rates.data = JSON.parse(response);
                    console.log('SCD: rates updated from open echangerate');
                } else {
                    console.log('SCD: fetching rates failed! status=' + status);
                }
            }
        );
        });
        //end request
    }
}

/**
 * Get the conversion rate between two currencies
 * 
 * @param {string} base : base currency 
 * @param {string} target : target currency 
 */
function scd_get_convert_rate(base, target)
{
    if (scd_local_rates === undefined) {
        scd_init_local_rates();
    }

    if (scd_local_rates.data[base] !== undefined && scd_local_rates.data[target] !== undefined)
    {
        rate = parseFloat(scd_local_rates.data[target]) / parseFloat(scd_local_rates.data[base]);
        return rate;
    }
    else
    {
        return undefined
    }
}

function scd_fetchRates_from_shpy() {
    jQuery.cachedScript = function (url, options) {

        // Allow user to set any option except for dataType, cache, and url
        options = jQuery.extend(options || {}, {
            dataType: "script",
            cache: true,
            url: url
        });

        // Use $.ajax() since it is more flexible than $.getScript
        // Return the jqXHR object so we can chain callbacks
        return jQuery.ajax(options);
    };

// Usage
    jQuery.cachedScript("https://cdn.shopify.com/s/javascripts/currencies.js").done(function (script, textStatus) {
        console.log(textStatus);
        console.log('load script succefully from shpy');
    }).fail(function (jqxhr, settings, exception) {
        //load defauldata
        console.log('fail to load script from shpy');
    });
}