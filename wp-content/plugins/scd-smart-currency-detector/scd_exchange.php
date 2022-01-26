<?php

/**
 * Get the exchange rates.
 *
 * @return array The exchange rates, as associative array containing the fields:
 *          - "timestamp" : the timestamp 
 *          - "base" : the base currency for the rates, e.g. USD
 *          - a separate field for each suported currency, e.g. "AED", "EUR". The value of the
 *            field is the conversion rate between the base currency and the target currency
 */

function scd_get_exchange_rates() 
{
    
    $rates = scd_get_openEx_rates();

    if(empty($rates))
    {
        // Rates could not be obtained. Use the default rates
        $rates = scd_get_default_rates();
    }

    return $rates;
}

/**
 * Get the rates from openEx.
 *
 * @return object The rates from openEx
 */
function scd_get_openEx_rates() 
{

  // Do we have this information in our transients already?
  $transient = get_transient( 'scd_rates_OpenEx' );
  
  // Yep!  Just return it and we're done.
  if( ! empty( $transient ) ) {

    // The function will return here every time after the first time it is run, until the transient expires.
    $rates = json_decode($transient, true);

  // Nope!  We gotta make a call.
  } else {
  
    $rates = scd_fetch_rates_from_openEx();
    
    if(!empty($rates))
    {
        // Save the API response so we don't have to call again until the update interval expires  
        $transient = json_encode($rates);
        $validity_in_seconds = scd_get_rates_update_interval();
        set_transient( 'scd_rates_OpenEx', $transient, $validity_in_seconds);
    }

  }

  return $rates;
  
}

/**
 * Fetch the rates from openEx.
 *
 * @return object The rates obtained, or null if data could not be fetched
 */
function scd_fetch_rates_from_openEx()
{
    $success = false;
    $rates = null;

    $apiArray = array();
 //   $apiArray[] = "c6bc01da16fb403a9a7c09be270c269b";
 //   $apiArray[] = "26bec72a4ed1403c98fe7b2e213d6174";
 //   $apiArray[] = "6e40b0cdb2ab4f1396eba6ed764b0bfc";
    $apiArray[] = "c5859382ef1e487fb159251230d9f2ad";
    $apiArray[] = "23d274d3fd754224af55549416a9c6ac";
    $apiArray[] = "8f4c6268eb2c482b88c6201712f8e91d";
    $apiArray[] = "3c06a455b44e4ce1b731ff931cc1165d";
    $apiArray[] = "26bec72a4ed1403c98fe7b2e213d6174";
	$apiArray[] = "a326110605c44d7c801e65e7dbc8caca";
	$apiArray[] = "42adbe51bdfb4a22a297da4f4394636b";
	$apiArray[] = "9230dc426e86404788827905613120cd";
	$apiArray[] = "3008d67f86a94e149a59090512698156";

    // We got this url from the documentation for the remote API.
    $url = 'https://openexchangerates.org/api/latest.json';
    
    while($success == false && count($apiArray) > 0)
    {

        $appId = array_pop($apiArray);
        $query = $url."?app_id=".$appId;
        
        // Call the API.
        $response = wp_remote_get( $query );

        if ( is_wp_error($response) ) {
            $success = false;
        }
        else
        {
            // Check the response
            $code = wp_remote_retrieve_response_code($response);
            $body = wp_remote_retrieve_body($response);
            
            if($code == 200)
            {
                $rates = scd_decode_openEx_rates_to_array($body);
                $success = true;
            }
            
        }
    } // end while

    return $rates;
}

/**
 * Decode the rates obtained from OpenEx into an associative array
 *
 * @return array An associative array with fields:
 *          - "timestamp" : the timestamp read from the openEx response
 *          - "base" : the base currency for the rates
 *          - a separate field for each suported currency, e.g. "AED", "EUR". The value of the
 *            field is the conversion rate between the currency and the base currency  
 */
function scd_decode_openEx_rates_to_array($rates) {

    try
    {
        $decoded = json_decode($rates, true);

        $data = array();
        $data["timestamp"] = $decoded["timestamp"];
        $data["base"] = $decoded["base"];
        $data += $decoded["rates"];

        return $data;
    }
    catch(\Error $e)
    {
        return null;
    }
    catch(\Exception $e)
    {
        return null;   
    }
}

/**
 * Get the default rates to use in case of no response
 *
 * @return object The default rates
 */
function scd_get_default_rates()
{
    $default = file_get_contents(SCD_PLUGIN_DIR_PATH . "js/defaultdata.json");
    return scd_decode_openEx_rates_to_array ($default);
}

/**
 * Get the rate update interval in seconds
 *
 * @return object The default rates
 */
function scd_get_rates_update_interval()
{
    $general_options = get_option('scd_general_options');
    $interval_in_hours = intval($general_options['exchangeRateUpdate']) * intval($general_options['exchangeRateUpdateInterval']);
    if(empty($interval_in_hours) || ($interval_in_hours < 24)){
        $interval_in_hours = 24; 
    }

    return $interval_in_hours * HOUR_IN_SECONDS;
}

?>