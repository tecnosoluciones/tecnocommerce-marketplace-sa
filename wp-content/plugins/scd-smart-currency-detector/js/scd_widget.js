var scd_ccc = {
"AF": "AF",
"AL": "AL",
"DZ": "DZ",
"AD": "EU",
"AO": "AO",
"AI": "CC",
"AR": "AR",
"AM": "AM",
"AW": "AW",
"AU": "AU",
"AT": "EU",
"AZ": "AZ",
"BS": "BS",
"BH": "BH",
"BD": "BD",
"BB": "BB",
'BC': 'BC',
"BY": "BY",
"BE": "EU",
"BZ": "BZ",
"BJ": "BF",
"BM": "BM",
"BT": "BT",
"BO": "BO",
"BA": "BA",
"BW": "BW",
"BR": "BR",
"BN": "BN",
"BG": "BG",
"BF": "BF",
"BI": "BI",
"KH": "KH",
"CM": "CF",
"CA": "CA",
"CV": "CV",
"KY": "KY",
"CF": "CF",
"TD": "CF",
"CL": "CL",
"CN": "CN",
"CX": "AU",
"CC": "AU",
"CO": "CO",
"KM": "KM",
"CK": "NZ",
"CR": "CR",
"HR": "HR",
"CU": "CU",
"CY": "EU",
"CZ": "CZ",
"CD": "CD",
"DJ": "DJ",
"DM": "Dominica",
"DO": "DO",
"EG": "EG",
"GQ": "CF",
"ER": "ER",
"EE": "EU",
"ET": "ET",
"FK": "FK",
"FJ": "FJ",
"FI": "EU",
"FR": "EU",
"PF": "AT",
"GA": "CF",
"GM": "GM",
"GE": "GE",
"DE": "EU",
"GH": "GH",
"GR": "EU",
"GU": "US",
"GT": "GT",
"GN": "GN",
"GW": "BF",
"GY": "GY",
"HT": "HT",
"HN": "HN",
"HK": "HK",
"HU": "HU",
"IS": "IS",
"IN": "IN",
"ID": "ID",
"IR": "IR",
"IQ": "IQ",
"IE": "EU",
"IM": "GB",
"IL": "IL",
"IT": "EU",
"CI": "BF",
"JM": "JM",
"JP": "JP",
"JE": "JE",
"JO": "JO",
"KZ": "KZ",
"KE": "KE",
"KI": "AU",
"XK": "EU",
"KW": "KW",
"KG": "KG",
"LA": "Laos",
"LV": "LV",
"LB": "LB",
"LS": "LS",
"LR": "LR",
"LY": "LY",
"LT": "LT",
"LU": "EU",
"MK": "MK",
"MW": "MW",
"MY": "MY",
"MV": "MV",
"ML": "BF",
"MT": "EU",
"MH": "US",
"MR": "MR",
"MU": "MU",
"YT": "EU",
"MX": "MX",
"MD": "MD",
"MC": "EU",
"MN": "MN",
"ME": "EU",
"MA": "MA",
"MZ": "MZ",
"NA": "NA",
"NP": "NP",
"NL": "EU",
"NC": "AT",
"NZ": "NZ",
"NI": "NI",
"NE": "BF",
"NG": "NG",
"NU": "Niue",
"KP": "KP",
"NO": "NO",
"OM": "OM",
"PK": "PK",
"PW": "US",
"PG": "PG",
"PY": "PY",
"PE": "PE",
"PH": "PH",
"PL": "PL",
"PT": "EU",
"PR": "US",
"QA": "QA",
"CG": "CF",
"RE": "EU",
"RO": "RO",
"RU": "RU",
"RW": "RW",
"BL": "EU",
"SH": "SH",
"PM": "EU",
"WS": "WS",
"ST": "ST",
"SA": "SA",
"SN": "BF",
"RS": "RS",
"SC": "SC",
"SL": "SL",
"SG": "SG",
"SK": "EU",
"SI": "EU",
"SB": "SB",
"SO": "SO",
"ZA": "ZA",
"KR": "KR",
"SS": "SS",
"ES": "EU",
"LK": "LK",
"SD": "SD",
"SR": "SR",
"SZ": "SZ",
"SE": "SE",
"CH": "CH",
"SY": "SY",
"TW": "TW",
"TJ": "TJ",
"TZ": "TZ",
"TH": "TH",
"TG": "BF",
"TO": "TO",
"TT": "TT",
"TN": "TN",
"TR": "TR",
"TM": "TM",
"UG": "UG",
"UA": "UA",
"AE": "AE",
"GB": "GB",
"US": "US",
"UY": "UY",
"UZ": "UZ",
"VU": "VU",
"VE": "VE",
"VN": "VN",
"YE": "YE",
"ZM": "ZM",
"ZW": "ZW"
};

jQuery(window).load(function () {
scd_open_widget();
});

/**
* Open the SCD widget
*/
function scd_open_widget() {

if (jQuery("#scd_widget_selector").length <= 0) {
// widget not included in page
return;
}

var country = localStorage['scd_countryCode'];
var countries = countryMap;

var co = new Array();
var defa = "us";
if (scd_getTargetCurrency() !== null) {
defa = (scd_getTargetCurrency().slice(0, -1)).toLowerCase();
}
//var optionsHTML = '';
var sele = settings.userCurrencyChoice.split(',');
var resu = false;

jQuery.each(sele, function (index, myCustom) {
if (myCustom == "allcurrencies")
resu = true;
});
//check if filter currencies
var filter = settings.filterOnlyOnHome;
if (filter) {
resu = true;
}
if (country !== undefined) {
//defa = country.toLowerCase();
if (scd_ccc[country.toString()] !== undefined)
defa = scd_ccc[country.toString()].toLocaleLowerCase();
else
defa = country.toString().toLocaleLowerCase();
}


if (resu) {
for (var key in countries) {
co.push(key.toLowerCase());
}

jQuery("#scd_widget_selector").countrySelect({
defaultCountry: defa,
preferredCountries: []
});
} else {

for (var key in countries) {

jQuery.each(sele, function (index, myCustom) {

if (myCustom == countries[key].currencyCode) {
co.push(key.toLowerCase());
}

});

}
if (scd_getTargetCurrency() !== null) {
defa = (scd_getTargetCurrency().slice(0, -1)).toLowerCase();
}else{
defa = ((settings.baseCurrency).slice(0, -1)).toLowerCase();
}
jQuery("#scd_widget_selector").countrySelect({
defaultCountry: defa,
onlyCountries: co,
preferredCountries: []
});
}


jQuery('.country-list li').click(function (e) {

e.preventDefault();
var currency = jQuery(this).closest('li').data('currency-code');
var country = jQuery(this).closest('li').data('country-code');

jQuery('#targetSessionName').val(currency);

// Send event
jQuery(document).trigger("scd:scd_widget_currency_updated", country);

});

}