var code_currencies = {
  'AFN': 'AF',
  'ALL': 'AL',
  'DZD': 'DZ',
  'USD': 'US',
  'EUR': 'EU',
  'AOA': 'AO',
  'XCD': 'LC',
  'ARS': 'AR',
  'AMD': 'AM',
  'AWG': 'AW',
  'AUD': 'AU',
  'AZN': 'AZ',
  'BSD': 'BS',
  'BHD': 'BH',
  'BDT': 'BD',
  'BBD': 'BB',
  'BTC': 'BC',
  'BYR': 'BY',
  'BZD': 'BZ',
  'XOF': 'BF',
  'BMD': 'BM',
  'BTN': 'BT',
  'BOB': 'BO',
  'BAM': 'BA',
  'BWP': 'BW',
  'NOK': 'BV',
  'BRL': 'BR',
  'BND': 'BN',
  'BGN': 'BG',
  'BIF': 'BI',
  'KHR': 'KH',
  'XAF': 'CF',
  'CAD': 'CA',
  'CVE': 'CV',
  'KYD': 'KY',
  'CLP': 'CL',
  'CNY': 'CN',
  'HKD': 'HK',
  'COP': 'CO',
  'KMF': 'KM',
  'CDF': 'CD',
  'CRC': 'CR',
  'HRK': 'HR',
  'CUP': 'CU',
  'CZK': 'CZ',
  'DKK': 'DK',
  'DJF': 'DJ',
  'DOP': 'DO',
  'ECS': 'EC',
  'EGP': 'EG',
  'SVC': 'SV',
  'ERN': 'ER',
  'ETB': 'ET',
  'FKP': 'FK',
  'DKK': 'FO',
  'FJD': 'FJ',
  'GMD': 'GM',
  'GEL': 'GE',
  'GHS': 'GH',
  'GIP': 'GI',
  'DKK': 'GL',
  'QTQ': 'GT',
  'GGP': 'GG',
  'GNF': 'GN',
  'GWP': 'GW',
  'GYD': 'GY',
  'HTG': 'HT',
  'HNL': 'HN',
  'HUF': 'HU',
  'ISK': 'IS',
  'INR': 'IN',
  'IDR': 'ID',
  'IRR': 'IR',
  'IQD': 'IQ',
  'ILS': 'IL',
  'JMD': 'JM',
  'JPY': 'JP',
  'JOD': 'JO',
  'KZT': 'KZ',
  'KES': 'KE',
  'KPW': 'KP',
  'KRW': 'KR',
  'KWD': 'KW',
  'KGS': 'KG',
  'LAK': 'LA',
  'LBP': 'LB',
  'LSL': 'LS',
  'LRD': 'LR',
  'LYD': 'LY',
  'CHF': 'LI',
  'MKD': 'MK',
  'MGF': 'MG',
  'MWK': 'MW',
  'MYR': 'MY',
  'MVR': 'MV',
  'MRO': 'MR',
  'MUR': 'MU',
  'MXN': 'MX',
  'MDL': 'MD',
  'MNT': 'MN',
  'MAD': 'MA',
  'MZN': 'MZ',
  'MMK': 'MM',
  'NAD': 'NA',
  'NPR': 'NP',
  'ANG': 'AN',
  'XPF': 'PF',
  'NZD': 'NZ',
  'NIO': 'NI',
  'NGN': 'NG',
  'NOK': 'NO',
  'OMR': 'OM',
  'PKR': 'PK',
  'PAB': 'PA',
  'PGK': 'PG',
  'PYG': 'PY',
  'PEN': 'PE',
  'PHP': 'PH',
  'PLN': 'PL',
  'QAR': 'QA',
  'RON': 'RO',
  'RUB': 'RU',
  'RWF': 'RW',
  'SHP': 'SH',
  'WST': 'WS',
  'STD': 'ST',
  'SAR': 'SA',
  'RSD': 'RS',
  'SCR': 'SC',
  'SLL': 'SL',
  'SGD': 'SG',
  'SBD': 'SB',
  'SOS': 'SO',
  'ZAR': 'ZA',
  'SSP': 'SS',
  'LKR': 'LK',
  'SDG': 'SD',
  'SRD': 'SR',
  'NOK': 'SJ',
  'SZL': 'SZ',
  'SEK': 'SE',
  'CHF': 'CH',
  'SYP': 'SY',
  'TWD': 'TW',
  'TJS': 'TJ',
  'TZS': 'TZ',
  'THB': 'TH',
  'TOP': 'TO',
  'TTD': 'TT',
  'TND': 'TN',
  'TRY': 'TR',
  'TMT': 'TM',
  'UGX': 'UG',
  'UAH': 'UA',
  'AED': 'AE',
  'GBP': 'GB',
  'UYU': 'UY',
  'UZS': 'UZ',
  'VUV': 'VU',
  'VEF': 'VE',
  'VND': 'VN',
  'MAD': 'EH',
  'YER': 'YE',
  'ZMW': 'ZM',
  'ZWD': 'ZW',
};

function scd_get_currencies() {
  return currencyMap;
}

var target_currency = scd_getTargetCurrency();

jQuery(document).ready(function () {
  var link = document.createElement('link');
  // set the attributes for link element 
  link.rel = 'stylesheet';

  link.type = 'text/css';

  link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css';

  // Get HTML head element to append  
  // link element to it  
  document.getElementsByTagName('head')[0].appendChild(link);

  //var scd_currencies_size=Object.keys(scd_get_currencies());
  var sele = settings.userCurrencyChoice.split(',');
  resu = false;
  jQuery.each(sele, function (index, myCustom) {
    if (myCustom == "allcurrencies")
      resu = true;
  });
  if (resu) {
    var scd_currencies_size = Object.keys(scd_get_currencies());
  } else {
    var scd_currencies_size = settings.userCurrencyChoice.split(','); 
  }
//currency_search.SetFocus;
  var scd_currencies = "<li><input type='text' width='100%' id='currency_search' class='form-control' placeholder='Choose your currency' /></li>";
  for (var i = 0; i < scd_currencies_size.length; i++) {
    var essaie = scd_currencies_size[i].substr(0, 2).toLowerCase();
    var textecur = currencyMap[scd_currencies_size[i]].toLowerCase();
    scd_currencies += "<li><button class='boutcouleurscd country-select scd_device btn btn-primary'>" + "<div class='flag " + essaie + "' style='float:left; left-align:left;  margin-top:4px; margin-right:5px'></div>" + scd_currencies_size[i] + " (" + currencySymbolMap[scd_currencies_size[i]] + ") </button></li>";

  }
  jQuery('.label-container').hide();
var coch = settings.mobilewidgetpopup;
var textpopup= settings.textpopup;

//    alert(coch);
  //Pop up HTML code
  if (settings.fallbackPosition == "Right"){
    if (settings.mobilewidgetpopup != 1) {
      var popup = '<div class="form-popup" style="visibility: hidden;" id="myForm" onclick="clickCounter()"><i class="fa fa-times" ></i>' +
  '<span style="font-size:14px;margin:2px;"><a  href="#" onclick="clickCounter()"></a></span>' +
  '<p style="font-size:14px;padding-left:20px;margin-top:-10px;" ><strong>Here! you can set the currency in which you are comfortable about shopping. Just click and choose... <span> <i id="fa" style="position:fixed;" class="fa fa-hand-o-down" aria-hidden="true"></i></span></strong></p>' +
  '</div><div class="dir" style="visibility: hidden;" id="myDir">  </div>';
  }else {
  var popup = '<div class="form-popup" id="myForm" style="visibility:visible;" onclick="clickCounter()"><i class="fa fa-times" ></i>' +
  '<span style="font-size:14px;margin:2px;"><a  href="#" onclick="clickCounter()"></a></span>' +
  '<p style="font-size:14px;padding-left:20px;margin-top:-10px;padding-bottom:5px;" ><strong>'+textpopup+'<span> <i id="fa" style="position:fixed;" class="fa fa-hand-o-down" aria-hidden="true"></i></span></strong></br></p>' +
  '</div><div class="dir" id="myDir">  </div>';
  }
    var btn = "<button class='scd_float' onclick='clickCounter()' id='boutton'><strong><a class='picto-item' aria-label='favoris'><i class='scd_widget_loading fa fa-refresh fa-spin'></i></a></strong></button>";
    var drop = "<div class='scd_label-container'><ul class='scd_devices_list'>" + scd_currencies + "</ul></div>";
  
    function hand() {
      setTimeout(function () {
        jQuery('#fa').css('font-size', '18px');
      }, 1000);
      setTimeout(function () {
        jQuery('#fa').css('font-size', '13px');
      }, 1500);
    }
    hand();
    setInterval(hand, 2000);
  
  
    jQuery("body").append(btn, popup, drop);
   
    jQuery("#currency_search").on("keyup", function () {
      var value = jQuery(this).val().toLowerCase();
      jQuery(".scd_devices_list .scd_device").filter(function () {
        jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    jQuery('.scd_float').on('click', function () {
      //jQuery('input#currency_search').focus(); // try to focus the input
      clickCounter();
      jQuery('.scd_label-container').slideToggle();
    });
    jQuery('.scd_device').on('click', function () {
      
      var dev = jQuery(this).text().split(" ")[0];
  
      var country_code = code_currencies[dev];
  
      if (dev.indexOf('BTC') !== -1) {
        scd_convert_all_amounts(dev);
        jQuery('.scd_float').text(dev);
      } else {
              jQuery(document).trigger("scd:scd_widget_currency_updated", country_code);
      }
    });
  
    //Style Pop up span direction 
    jQuery('.dir').css('display', 'none');
    jQuery('.dir').css('position', 'fixed');
    jQuery('.dir').css('width', '20px');
    jQuery('.dir').css('height', '30px');
    jQuery('.dir').css('bottom', '110px');
    jQuery('.dir').css('right', '55px');
  //  jQuery('.dir').css('background-color', 'blue');
  //  jQuery('.dir').css('color', '#FFFFFF');
    jQuery('.dir').css('z-index', '9998');
    jQuery('.dir').css('cursor', 'pointer');
    jQuery('.dir').css('font-size', '12px');
    jQuery('.dir').css('box-shadow', '2px 2px 3px #999');
    jQuery('.dir').css('transform', 'rotate(45deg)');
  
    //Style pop up container
    jQuery('.form-popup').css('display', 'none');
    jQuery('.form-popup').css('position', 'fixed');
    jQuery('.form-popup').css('width', '200px');
  //  jQuery('.form-popup').css('height', '60px');
    jQuery('.form-popup').css('bottom', '120px');
    jQuery('.form-popup').css('right', '40px');
    jQuery('.form-popup').css('overflow', 'auto');
  //  jQuery('.form-popup').css('background-color', '#0066FF');
  //  jQuery('.form-popup').css('color', '#FFFFFF');
    jQuery('.form-popup').css('z-index', '9999');
    jQuery('.form-popup').css('cursor', 'pointer');
    jQuery('.form-popup').css('font-size', '12px');
    jQuery('.form-popup').css('box-shadow', '2px 2px 3px #999');
    
    
    
    jQuery('.scd_widget_loading').css('font-size', '30px');
    jQuery('.scd_widget_loading').css('margin-left', '-7px');
    jQuery('.scd_widget_loading').css('margin-top', '-5px');
    jQuery('.scd_label-container').css('position', 'fixed');
    jQuery('.scd_label-container').css('bottom', '38px');
    jQuery('.scd_label-container').css('right', '110px');
    jQuery('.scd_label-container').css('height', '30%');
    jQuery('.scd_label-container').css('width', '225px');
    jQuery('.scd_label-container').css('overflow', 'auto');
    jQuery('.scd_label-container').css('z-index', '9999');
    jQuery('.scd_label-container').css('display', 'none');
  
  //  jQuery('.scd_label-text').css('color', '#FFF');
    jQuery('.scd_label-text').css('background', 'rgba(51,51,51,0.5)');
    jQuery('.scd_label-text').css('display', 'table-cell');
    jQuery('.scd_label-text').css('vertical-align', 'middle');
    jQuery('.scd_label-text').css('padding', '10px');
    jQuery('.scd_label-text').css('border-radius', '3px');
  
    jQuery('.scd_float').css('display', 'block');
    jQuery('.scd_float').css('position', 'fixed');
    jQuery('.scd_float').css('width', '65px');
    jQuery('.scd_float').css('height', '65px');
    jQuery('.scd_float').css('bottom', '40px');
    jQuery('.scd_float').css('right', '40px');
    
    
  //  jQuery('.scd_float').css('background-color', '#0066FF');
  
  
  //  jQuery('.scd_float').css('color', '#FFFFFF');
    jQuery('.scd_float').css('border-radius', '50px');
    jQuery('.scd_float').css('text-align', 'center');
    jQuery('.scd_float').css('z-index', '9999');
    jQuery('.scd_float').css('cursor', 'pointer');
    jQuery('.scd_float').css('font-size', '12px');
    jQuery('.scd_float').css('box-shadow', '2px 2px 3px #999');
  
    jQuery('.my-float').css('font-size', '24px');
  
    jQuery('.scd_devices_list').css('list-style-type', 'none');
  
    jQuery('.scd_devices_list li').css('margin-top', '0px');
  
    jQuery('.scd_device').css('width', '100%');
  }else if(settings.fallbackPosition == "Left"){
          if (settings.mobilewidgetpopup != 1) {
          var popup = '<div class="form-popup" style="visibility: hidden;" id="myForm" onclick="clickCounter()">' +
      '<span style="font-size:14px;margin:2px;"><a  href="#" onclick="clickCounter()"></a></span>' +
      '<p style="font-size:14px;padding-left:20px;margin-top:-10px;" ><strong>Here! you can set the currency in which you are comfortable about shopping. Just click and choose... <span> <i id="fa" style="position:fixed;" class="fa fa-hand-o-down" aria-hidden="true"></i></span></strong></p>' +
      '</div><div class="dir" style="visibility: hidden;" id="myDir"> <i class="fa fa-times" ></i> </div>';
  }else {
  var popup = '<div class="form-popup" id="myForm" style="visibility:visible;" onclick="clickCounter()">' + '<i class="fa fa-times" ></i>' +
      '<span style="font-size:14px;margin:2px;"><a  href="#" onclick="clickCounter()"></a></span>' +
      '<p style="font-size:14px;padding-left:20px;margin-top:-10px;padding-bottom:5px;" ><strong>'+textpopup+'<span> <i id="fa" style="position:fixed;" class="fa fa-hand-o-down" aria-hidden="true"></i></span></strong></br></p>' +
      '</div><div class="dir" id="myDir">  </div>';
  }
    var btn = "<button class='scd_float' onclick='clickCounter()' id='boutton'><strong><a class='picto-item' aria-label='favoris'><i class='scd_widget_loading fa fa-refresh fa-spin'></i></a></strong></button>";
    var drop = "<div class='scd_label-container'><ul class='scd_devices_list'>" + scd_currencies + "</ul></div>";
  
    function hand() {
      setTimeout(function () {
        jQuery('#fa').css('font-size', '18px');
      }, 1000);
      setTimeout(function () {
        jQuery('#fa').css('font-size', '13px');
      }, 1500);
    }
    hand();
    setInterval(hand, 2000);
  
  
    jQuery("body").append(btn, popup, drop);
   
    jQuery("#currency_search").on("keyup", function () {
      var value = jQuery(this).val().toLowerCase();
      jQuery(".scd_devices_list .scd_device").filter(function () {
        jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    jQuery('.scd_float').on('click', function () {
      //jQuery('input#currency_search').focus(); // try to focus the input
      clickCounter();
      jQuery('.scd_label-container').slideToggle();
    });
    jQuery('.scd_device').on('click', function () {
      
      var dev = jQuery(this).text().split(" ")[0];
  
      var country_code = code_currencies[dev];
  
      if (dev.indexOf('BTC') !== -1) {
        scd_convert_all_amounts(dev);
        jQuery('.scd_float').text(dev);
      } else {
              jQuery(document).trigger("scd:scd_widget_currency_updated", country_code);
      }
    });
  
    //Style Pop up span direction 
    jQuery('.dir').css('display', 'none');
    jQuery('.dir').css('position', 'fixed');
    jQuery('.dir').css('width', '20px');
    jQuery('.dir').css('height', '30px');
    jQuery('.dir').css('bottom', '110px');
    jQuery('.dir').css('left', '55px');
  //  jQuery('.dir').css('background-color', 'blue');
  //  jQuery('.dir').css('color', '#FFFFFF');
    jQuery('.dir').css('z-index', '9998');
    jQuery('.dir').css('cursor', 'pointer');
    jQuery('.dir').css('font-size', '12px');
    jQuery('.dir').css('box-shadow', '2px 2px 3px #999');
    jQuery('.dir').css('transform', 'rotate(45deg)');
  
    //Style pop up container
    jQuery('.form-popup').css('display', 'none');
    jQuery('.form-popup').css('position', 'fixed');
    jQuery('.form-popup').css('width', '200px');
  //  jQuery('.form-popup').css('height', '60px');
    jQuery('.form-popup').css('bottom', '120px');
    jQuery('.form-popup').css('left', '40px');
    jQuery('.form-popup').css('overflow', 'auto');
  //  jQuery('.form-popup').css('background-color', '#0066FF');
  //  jQuery('.form-popup').css('color', '#FFFFFF');
    jQuery('.form-popup').css('z-index', '9999');
    jQuery('.form-popup').css('cursor', 'pointer');
    jQuery('.form-popup').css('font-size', '12px');
    jQuery('.form-popup').css('box-shadow', '2px 2px 3px #999');
    
    
    
    jQuery('.scd_widget_loading').css('font-size', '30px');
    jQuery('.scd_widget_loading').css('margin-right', '-7px');
    jQuery('.scd_widget_loading').css('margin-top', '-5px');
    jQuery('.scd_label-container').css('position', 'fixed');
    jQuery('.scd_label-container').css('bottom', '38px');
    jQuery('.scd_label-container').css('left', '110px');
    jQuery('.scd_label-container').css('height', '30%');
    jQuery('.scd_label-container').css('width', '225px');
    jQuery('.scd_label-container').css('overflow', 'auto');
    jQuery('.scd_label-container').css('z-index', '9999');
    jQuery('.scd_label-container').css('display', 'none');
  
  //  jQuery('.scd_label-text').css('color', '#FFF');
    jQuery('.scd_label-text').css('background', 'rgba(51,51,51,0.5)');
    jQuery('.scd_label-text').css('display', 'table-cell');
    jQuery('.scd_label-text').css('vertical-align', 'middle');
    jQuery('.scd_label-text').css('padding', '10px');
    jQuery('.scd_label-text').css('border-radius', '3px');
  
    jQuery('.scd_float').css('display', 'block');
    jQuery('.scd_float').css('position', 'fixed');
    jQuery('.scd_float').css('width', '65px');
    jQuery('.scd_float').css('height', '65px');
    jQuery('.scd_float').css('bottom', '40px');
    jQuery('.scd_float').css('left', '40px');
    
    
  //  jQuery('.scd_float').css('background-color', '#0066FF');
  
  
  //  jQuery('.scd_float').css('color', '#FFFFFF');
    jQuery('.scd_float').css('border-radius', '50px');
    jQuery('.scd_float').css('text-align', 'center');
    jQuery('.scd_float').css('z-index', '9999');
    jQuery('.scd_float').css('cursor', 'pointer');
    jQuery('.scd_float').css('font-size', '12px');
    jQuery('.scd_float').css('box-shadow', '2px 2px 3px #999');
  
    jQuery('.my-float').css('font-size', '24px');
  
    jQuery('.scd_devices_list').css('list-style-type', 'none');
  
    jQuery('.scd_devices_list li').css('margin-top', '0px');
  
    jQuery('.scd_device').css('width', '100%');
  }else{
    if (settings.mobilewidgetpopup != 1) {
      var popup = '<div class="form-popup" style="visibility: hidden;" id="myForm" onclick="clickCounter()"><i class="fa fa-times" ></i>' +
  '<span style="font-size:14px;margin:2px;"><a  href="#" onclick="clickCounter()"></a></span>' +
  '<p style="font-size:14px;padding-left:20px;margin-top:-10px;" ><strong>Here! you can set the currency in which you are comfortable about shopping. Just click and choose... <span> <i id="fa" style="position:fixed;" class="fa fa-hand-o-down" aria-hidden="true"></i></span></strong></p>' +
  '</div><div class="dir" style="visibility: hidden;" id="myDir">  </div>';
  }else {
  var popup = '<div class="form-popup" id="myForm" style="visibility:visible;" onclick="clickCounter()"><i class="fa fa-times" ></i>' +
  '<span style="font-size:14px;margin:2px;"><a  href="#" onclick="clickCounter()"></a></span>' +
  '<p style="font-size:14px;padding-left:20px;margin-top:-10px;padding-bottom:5px;" ><strong>'+textpopup+'<span> <i id="fa" style="position:fixed;" class="fa fa-hand-o-down" aria-hidden="true"></i></span></strong></br></p>' +
  '</div><div class="dir" id="myDir">  </div>';
  }
    var btn = "<button class='scd_float' onclick='clickCounter()' id='boutton'><strong><a class='picto-item' aria-label='favoris'><i class='scd_widget_loading fa fa-refresh fa-spin'></i></a></strong></button>";
    var drop = "<div class='scd_label-container'><ul class='scd_devices_list'>" + scd_currencies + "</ul></div>";
  
    function hand() {
      setTimeout(function () {
        jQuery('#fa').css('font-size', '18px');
      }, 1000);
      setTimeout(function () {
        jQuery('#fa').css('font-size', '13px');
      }, 1500);
    }
    hand();
    setInterval(hand, 2000);
  
  
    jQuery("body").append(btn, popup, drop);
   
    jQuery("#currency_search").on("keyup", function () {
      var value = jQuery(this).val().toLowerCase();
      jQuery(".scd_devices_list .scd_device").filter(function () {
        jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    jQuery('.scd_float').on('click', function () {
      //jQuery('input#currency_search').focus(); // try to focus the input
      clickCounter();
      jQuery('.scd_label-container').slideToggle();
    });
    jQuery('.scd_device').on('click', function () {
      
      var dev = jQuery(this).text().split(" ")[0];
  
      var country_code = code_currencies[dev];
  
      if (dev.indexOf('BTC') !== -1) {
        scd_convert_all_amounts(dev);
        jQuery('.scd_float').text(dev);
      } else {
              jQuery(document).trigger("scd:scd_widget_currency_updated", country_code);
      }
    });
  
    //Style Pop up span direction 
    jQuery('.dir').css('display', 'none');
    jQuery('.dir').css('position', 'fixed');
    jQuery('.dir').css('width', '20px');
    jQuery('.dir').css('height', '30px');
    jQuery('.dir').css('bottom', '110px');
    jQuery('.dir').css('right', '51%');
  //  jQuery('.dir').css('background-color', 'blue');
  //  jQuery('.dir').css('color', '#FFFFFF');
    jQuery('.dir').css('z-index', '9998');
    jQuery('.dir').css('cursor', 'pointer');
    jQuery('.dir').css('font-size', '12px');
    jQuery('.dir').css('box-shadow', '2px 2px 3px #999');
    jQuery('.dir').css('transform', 'rotate(45deg)');
  
    //Style pop up container
    jQuery('.form-popup').css('display', 'none');
    jQuery('.form-popup').css('position', 'fixed');
    jQuery('.form-popup').css('width', '200px');
  //  jQuery('.form-popup').css('height', '60px');
    jQuery('.form-popup').css('bottom', '120px');
    jQuery('.form-popup').css('right', '50%');
    jQuery('.form-popup').css('overflow', 'auto');
  //  jQuery('.form-popup').css('background-color', '#0066FF');
  //  jQuery('.form-popup').css('color', '#FFFFFF');
    jQuery('.form-popup').css('z-index', '9999');
    jQuery('.form-popup').css('cursor', 'pointer');
    jQuery('.form-popup').css('font-size', '12px');
    jQuery('.form-popup').css('box-shadow', '2px 2px 3px #999');
    
    
    
    jQuery('.scd_widget_loading').css('font-size', '30px');
    jQuery('.scd_widget_loading').css('margin-left', '-7px');
    jQuery('.scd_widget_loading').css('margin-top', '-5px');
    jQuery('.scd_label-container').css('position', 'fixed');
    jQuery('.scd_label-container').css('bottom', '38px');
    jQuery('.scd_label-container').css('right', '50%');
    jQuery('.scd_label-container').css('height', '30%');
    jQuery('.scd_label-container').css('width', '225px');
    jQuery('.scd_label-container').css('overflow', 'auto');
    jQuery('.scd_label-container').css('z-index', '9999');
    jQuery('.scd_label-container').css('display', 'none');
  
  //  jQuery('.scd_label-text').css('color', '#FFF');
    jQuery('.scd_label-text').css('background', 'rgba(51,51,51,0.5)');
    jQuery('.scd_label-text').css('display', 'table-cell');
    jQuery('.scd_label-text').css('vertical-align', 'middle');
    jQuery('.scd_label-text').css('padding', '10px');
    jQuery('.scd_label-text').css('border-radius', '3px');
  
    jQuery('.scd_float').css('display', 'block');
    jQuery('.scd_float').css('position', 'fixed');
    jQuery('.scd_float').css('width', '65px');
    jQuery('.scd_float').css('height', '65px');
    jQuery('.scd_float').css('bottom', '40px');
    jQuery('.scd_float').css('right', '50%');
    
    
  //  jQuery('.scd_float').css('background-color', '#0066FF');
  
  
  //  jQuery('.scd_float').css('color', '#FFFFFF');
    jQuery('.scd_float').css('border-radius', '50px');
    jQuery('.scd_float').css('text-align', 'center');
    jQuery('.scd_float').css('z-index', '9999');
    jQuery('.scd_float').css('cursor', 'pointer');
    jQuery('.scd_float').css('font-size', '12px');
    jQuery('.scd_float').css('box-shadow', '2px 2px 3px #999');
  
    jQuery('.my-float').css('font-size', '24px');
  
    jQuery('.scd_devices_list').css('list-style-type', 'none');
  
    jQuery('.scd_devices_list li').css('margin-top', '0px');
  
    jQuery('.scd_device').css('width', '100%');
  }
  var dragging = false;
  var iX, iY;
  jQuery(".scd_float").mousedown(function (e) {
    dragging = true;
    iX = e.clientX - this.offsetLeft;
    iY = e.clientY - this.offsetTop;
    this.setCapture && this.setCapture();
    return false;
  });
  /*document.onmousemove = function (e) {
    if (dragging) {
      var e = e || window.event;
      var oX = e.clientX - iX;
      var oY = e.clientY - iY;


      if (e.clientY < 400) {
        if (e.clientX < 600) {
          jQuery(".scd_float").css({ "left": oX + "px", "top": oY + "px" });
          jQuery('.scd_label-container').css({ "left": oX + 80 + "px", "top": oY + "px" });
        } else {
          jQuery(".scd_float").css({ "left": oX + "px", "top": oY + "px" });
          jQuery('.scd_label-container').css({ "left": oX - 230 + "px", "top": oY + "px" });
        }
      } else {
        if (e.clientX < 600) {
          jQuery(".scd_float").css({ "left": oX + "px", "top": oY + "px" });
          jQuery('.scd_label-container').css({ "left": oX + 80 + "px", "top": oY - 280 + "px" });
        } else {
          jQuery(".scd_float").css({ "left": oX + "px", "top": oY + "px" });
          jQuery('.scd_label-container').css({ "left": oX - 230 + "px", "top": oY - 280 + "px" });
        }
      }

      return false;
    }
  };*/

  var mobilewidgetcolor = settings.mobilewidgetcolor;

 // convert color in decimal values
            
            function scd_hexToDec(hex) {
var result = 0, digitValue;
hex = hex.toLowerCase();
for (var i = 0; i < hex.length; i++) {
digitValue = '0123456789abcdefgh'.indexOf(hex[i]);
result = result * 16 + digitValue;
}
return result;
}



//	Widget and popup color
 noir='#000000';
            blanc='#ffffff';
            //couleur devise scd
	    var widgetcolor       = document.getElementById("boutton");
	    widgetcolor.style.background = mobilewidgetcolor;
           
            //couleur texte popup
            popucolor1 =document.getElementById("myForm");
            popucolor1.style.background = mobilewidgetcolor;
            popucolor2 =document.getElementById("myDir");
            popucolor2.style.background = mobilewidgetcolor;
           jQuery('.boutcouleurscd').css('background',mobilewidgetcolor);
            code1 = mobilewidgetcolor.substr(1, 2);
            
            code2 =mobilewidgetcolor.substr(3, 2);
            code3 =mobilewidgetcolor.substr(5);
            
//            alert(code3);

            r = scd_hexToDec(code1); 
            v = scd_hexToDec(code2);
            b= scd_hexToDec(code3);
            if( (r>v) && (v===b) ){
            popucolor1.style.color = noir;
            popucolor2.style.color = noir;
            widgetcolor.style.color = noir;
            jQuery('.boutcouleurscd').css('color',noir);
            jQuery('.fa').css('color',noir);
            };
            
            
            if( (v>r) && (r===b) ){
            popucolor1.style.color = noir;
            popucolor2.style.color = noir;
            widgetcolor.style.color = noir;
            jQuery('.boutcouleurscd').css('color',noir);
            jQuery('.fa').css('color',noir);
            
            };
            
            if( (b>r) && (r===v) ){
            popucolor1.style.color = noir;
            popucolor2.style.color = noir;
            widgetcolor.style.color = noir;
            jQuery('.boutcouleurscd').css('color',noir);
            jQuery('.fa').css('color',noir);
            };
            
            if( (b>v) && (v>r) ){
            popucolor1.style.color = noir;
           popucolor2.style.color = noir;
            widgetcolor.style.color = noir;
            jQuery('.boutcouleurscd').css('color',noir);
            jQuery('.fa').css('color',noir);
            };
            
            if( (v>b) && (b>r) ){
            popucolor1.style.color = blanc;
            popucolor2.style.color = blanc;
            widgetcolor.style.color = blanc;
            jQuery('.boutcouleurscd').css('color',blanc);
            jQuery('.fa').css('color',blanc);
            };
            
            if( (v===b) && (b===r) ){
                if(v<=150){
            popucolor1.style.color = blanc;
            popucolor2.style.color = blanc;
            widgetcolor.style.color = blanc;
            jQuery('.boutcouleurscd').css('color',blanc);
            jQuery('.fa').css('color',blanc);
                }
        
        else{
            popucolor1.style.color = noir;
            popucolor2.style.color = noir;
            widgetcolor.style.color = noir;
            jQuery('.boutcouleurscd').css('color',noir);
            jQuery('.fa').css('color',noir);
            
        }
            };
            
            if( (v>b) && (b<r) ){
            popucolor1.style.color = noir;
            popucolor2.style.color = noir;
            widgetcolor.style.color = noir;
            jQuery('.boutcouleurscd').css('color',noir);
            jQuery('.fa').css('color',noir);
            };

            if( (b>v) && (v>r) ){
            popucolor1.style.color = blanc;
            popucolor2.style.color = blanc;
            widgetcolor.style.color = blanc;
            jQuery('.boutcouleurscd').css('color',blanc);
            jQuery('.fa').css('color',blanc);
            };

            if( (b>r) && (r>v) ){
            popucolor1.style.color = noir;
            popucolor2.style.color = noir;
            widgetcolor.style.color = noir;
            jQuery('.boutcouleurscd').css('color',noir);
            jQuery('.fa').css('color',noir);
            };
            
            if( (b>v) && (v>r) ){
            popucolor1.style.color = blanc;
            popucolor2.style.color = blanc;
            widgetcolor.style.color = blanc;
            jQuery('.boutcouleurscd').css('color',blanc);
            jQuery('.fa').css('color',blanc);
            };
            
            if( (r>b) && (b>v) ){
            popucolor1.style.color = blanc;
            popucolor2.style.color = blanc;
            widgetcolor.style.color = blanc;
            jQuery('.boutcouleurscd').css('color',blanc);
            jQuery('.fa').css('color',blanc);
            };
  //mobile widget position restore.
  if (window.localStorage.getItem("left") !== null) {
    jQuery(".scd_float").css({ "left": window.localStorage.getItem('left') + "px", "top": window.localStorage.getItem('top') + "px", "right": window.localStorage.getItem('right') + "px", "bottom": window.localStorage.getItem('right') + "px" });
  }

  jQuery(document).mouseup(function (e) {
    dragging = false;
    var position = jQuery(".scd_float").position();
    window.localStorage.setItem('top', position.top);
    window.localStorage.setItem('bottom', position.bottom);
    window.localStorage.setItem('left', position.left);
    window.localStorage.setItem('right', position.right);
    e.cancelBubble = true;
  });


  //end of widget
  if (target_currency === null) {
    jQuery('.scd_float').text();
  } else { jQuery('.scd_float').text(target_currency); }

});

//Show pop up for the first time
window.onload = function () {
  if ((!sessionStorage.clickcount)) {

    document.getElementById("myForm").style.display = "block";
    document.getElementById("myDir").style.display = "block";
    jQuery('.scd_float').hide();
    jQuery('.scd_float').fadeIn("slow");
    jQuery('.form-popup').hide();
    jQuery('.dir').hide();
    jQuery('.form-popup').slideDown("slow", function () {
      jQuery('.dir').css('display', 'block');
    });
  }
};

//Hidding popup on close
function clickCounter() {

  sessionStorage.clickcount = 1;
  if (typeof (Storage) !== "undefined") {
    if (sessionStorage.clickcount) {
      sessionStorage.clickcount = Number(sessionStorage.clickcount) + 1;
    } else {
      sessionStorage.clickcount = 1;
    }
    document.getElementById("myForm").style.display = "none";
    document.getElementById("myDir").style.display = "none";

  }
}