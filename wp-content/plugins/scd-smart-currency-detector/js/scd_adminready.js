

function bindFallbackCurrencyToggle(){

	if(jQuery("#scd_showFallbackOnAutodetectFailure").is(':checked')){
		jQuery("#scd_autodetectFallbackCurrency").closest("tr").show();
		//console.log("shown");
	} else {

		jQuery("#scd_autodetectFallbackCurrency").closest("tr").hide();
		//console.log("hidden");
	}

	jQuery("#scd_showFallbackOnAutodetectFailure").change(function() {
		if(this.checked) {

			jQuery("#scd_autodetectFallbackCurrency").closest("tr").show();
		} else {
			jQuery("#scd_autodetectFallbackCurrency").closest("tr").hide();
		}
	});
}

function bindChosenTargets (settings){


	jQuery('.scd_chosen_select').chosen();
	//console.log("I got " + settings.currencies);

	var cs;
	if(settings.currencies == "") cs = [];
	else cs = settings.currencies.split(',');


	jQuery('.scd_chosen_select').on('change', function(evt, params) {

		if('selected' in params){
			cs.push(params.selected);
		}

		if('deselected' in params){
			var index = cs.indexOf(params.deselected);

			if(index>=0) cs.splice(index, 1);
		}

		var csStr = "";
		jQuery.each(cs, function (index, element){

			//console.log("" + index);
			if(index!==0) csStr += ',';
			csStr += element;
		})

		//console.log(csStr);

		jQuery('#targetField').val(csStr);

	});

}

function bindChosenUserTargets (settings){


	jQuery('.scd_widget').chosen();
	//console.log("I got " + settings.currenciesUser);

	var cs;
	if(settings.currenciesUser == "") cs = [];
	else cs = settings.currenciesUser.split(',');


	jQuery('.scd_widget').on('change', function(evt, params) {

		if('selected' in params){
			cs.push(params.selected);
		}

		if('deselected' in params){
			var index = cs.indexOf(params.deselected);

			if(index>=0) cs.splice(index, 1);
		}

		var csStr = "", getAll = false;
		
		jQuery.each(cs, function (index, element){

			if( "allcurrencies" == element ) getAll = true;
			
		})
		
		if( getAll ) csStr = "allcurrencies";
		else {
			jQuery.each(cs, function (index, element){

				//console.log("" + index);
				if(index!==0) csStr += ',';
				csStr += element;
			})
		}

		//console.log(csStr);

		jQuery('#userCurrencyChoiceField').val(csStr);

	});

}

function bindRoleChosen (mysettings){

	jQuery('.scd_role_select').chosen();
	// console.log("I got " + mysettings.currenciesRole);
	
	var cs, count = 1;
	//alert(mysettings.currenciesRole);
	if(mysettings.currenciesRole == "") cs = [];
	else cs = mysettings.currenciesRole.split(',');
	
	//cs = (mysettings.currenciesRole != "") ? mysettings.currenciesRole.split(',') : [];
	
	/*if(jQuery('#priceField').val()) jQuery('#mySetPrice').show();
	else jQuery('#mySetPrice').hide();*/

	jQuery('.scd_role_select').on('change', function(evt, params) {

		/*if(jQuery('#priceField').val()) jQuery('#mySetPrice').show();
		else jQuery('#mySetPrice').hide();*/
	
		if('selected' in params){
			cs.push(params.selected);
		}

		if('deselected' in params){
			var index = cs.indexOf(params.deselected);

			if(index>=0) cs.splice(index, 1);
		}

		var csStr = "", getAll = false;
		var mreg = "", msal = "";
		var mySave = jQuery('#priceField').val();
		
		jQuery('#scd_regularCurrency').html('');
		jQuery('#scd_saleCurrency').html('');
		jQuery('#priceField').val('');
		
		jQuery.each(cs, function (index, element){

			if( "allcurrencies" == element ) getAll = true;	
		});
		
		if( getAll ) { 
			csStr = "allcurrencies";
			var ii = 0, ss = 1;
			var csVal = "";
			var numItems = jQuery('#regPrice input').length;
			//alert(numItems);
		
			for (var key in currencyMap){
				
				/*mreg = "regular_" + key + "_";
				msal = "sale_" + key + "_";
				
				var reghid = '#regularField_' + key;
				var salhid = '#saleField_' + key;
				if(jQuery(reghid).val()) mreg = jQuery(reghid).val();
				if(jQuery(salhid).val()) msal = jQuery(salhid).val();*/
				var reghid = '#regularField_' + key;
				var salhid = '#saleField_' + key;
				//alert(jQuery(reghid).val());
				mreg = (jQuery(reghid).val()) ? jQuery(reghid).val() : "regular_" + key + "_";
				msal = (jQuery(salhid).val()) ? jQuery(salhid).val() : "sale_" + key + "_";
					
				//jQuery(reghid).remove();
				//jQuery(salhid).remove();
				/*if(ss == 1) { 
					jQuery('#regPrice').html('');
					jQuery('#salPrice').html('');
				}*/
					
				
				if(ii > 0 && key != key[key.length-1]) csVal += ',';
				csVal += mreg + "-" + msal;
				
				var myregselect = jQuery('<option value=' + key + ' >Regular price (' + key + ')</option>'); 
				var myfieldregselect = jQuery('<input type="hidden" id="regularField_' + key + '" value="' + mreg + '"/>'); 
				var mysalselect = jQuery('<option value=' + key + ' >Sale price (' + key + ')</option>'); 
				var myfieldsalselect = jQuery('<input type="hidden" id="saleField_' + key + '" value="' + msal + '"/>'); 
					
				jQuery('#scd_regularCurrency').append(myregselect);
				jQuery('#regPrice').append(myfieldregselect);
				jQuery('#scd_saleCurrency').append(mysalselect);
				jQuery('#salPrice').append(myfieldsalselect);
				
				
				if(ss == 1) {
					var reghids = '#regularField_' + key;
					var salhids = '#saleField_' + key;
					mregs = jQuery(reghids).val().split('_');
					msals = jQuery(salhids).val().split('_');
					
					jQuery('#scd_regularPriceCurrency').val(mregs[2]);
					jQuery('#scd_salePriceCurrency').val(msals[2]);
				}
				
				ss++;
				ii++;
			}
			var nmb = 'input:lt(' + numItems + ')';
			jQuery('#regPrice').find(nmb).remove();
			jQuery('#salPrice').find(nmb).remove();
		}
		else {
			var count = 0, csVal = "", ss = 1, aa;
			var numItems = jQuery('#regPrice input').length;
			
			//alert(parseInt(nb));
			jQuery.each(cs, function (index, element){
				//var nb = jQuery('#scd_currencyNumber').val();
				var nb = settings.role;
				//if(nb < 4) {
					if(nb == 4) aa = 157;
					else aa = nb;
					
					if (count < aa && element != "") {
						
						var reghid = '#regularField_' + element;
						var salhid = '#saleField_' + element;
						//alert(jQuery(reghid).val());
						mreg = (jQuery(reghid).val()) ? jQuery(reghid).val() : "regular_" + element + "_";
						msal = (jQuery(salhid).val()) ? jQuery(salhid).val() : "sale_" + element + "_";
						
						
						
						/*jQuery(reghid).remove();
						jQuery(salhid).remove();*/
						//jQuery('#regPrice').find('input:first').remove();
						/*if(ss == 1) { 
							jQuery('#regPrice').html('');
							jQuery('#salPrice').html('');
						}*/
						
						if(index!==0) { 
							csStr += ',';
							csVal += ',';
						}
						csStr += element; 
						
						csVal += mreg + "-" + msal;
						
						var myregselect = jQuery('<option value=' + element + ' >Regular price (' + element + ')</option>'); 
						var myfieldregselect = jQuery('<input type="hidden" id="regularField_' + element + '" value="' + mreg + '"/>'); 
						var mysalselect = jQuery('<option value=' + element + ' >Sale price (' + element + ')</option>');
						var myfieldsalselect = jQuery('<input type="hidden" id="saleField_' + element + '" value="' + msal + '"/>');					
						
						jQuery('#scd_regularCurrency').append(myregselect);
						jQuery('#regPrice').append(myfieldregselect);
						jQuery('#scd_saleCurrency').append(mysalselect);
						jQuery('#salPrice').append(myfieldsalselect);
						
						if(ss == 1) {
							var reghids = '#regularField_' + element;
							var salhids = '#saleField_' + element;
							mregs = jQuery(reghids).val().split('_');
							msals = jQuery(salhids).val().split('_');
							
							jQuery('#scd_regularPriceCurrency').val(mregs[2]);
							jQuery('#scd_salePriceCurrency').val(msals[2]);
						}
						
						ss++;
						count++;
					}
				//}
				/*else {
						var reghid = '#regularField_' + element;
						var salhid = '#saleField_' + element;
						//alert(jQuery(reghid).val());
						mreg = (jQuery(reghid).val()) ? jQuery(reghid).val() : "regular_" + element + "_";
						msal = (jQuery(salhid).val()) ? jQuery(salhid).val() : "sale_" + element + "_";
						
						alert(element);
						
						if(index!==0) { 
							csStr += ',';
							csVal += ',';
						}
						csStr += element; 
						
						csVal += mreg + "-" + msal;
						
						var myregselect = jQuery('<option value=' + element + ' >Regular price (' + element + ')</option>'); 
						var myfieldregselect = jQuery('<input type="hidden" id="regularField_' + element + '" value="' + mreg + '"/>'); 
						var mysalselect = jQuery('<option value=' + element + ' >Sale price (' + element + ')</option>');
						var myfieldsalselect = jQuery('<input type="hidden" id="saleField_' + element + '" value="' + msal + '"/>');					
						
						jQuery('#scd_regularCurrency').append(myregselect);
						jQuery('#regPrice').append(myfieldregselect);
						jQuery('#scd_saleCurrency').append(mysalselect);
						jQuery('#salPrice').append(myfieldsalselect);
						
						if(ss == 1) {
							var reghids = '#regularField_' + element;
							var salhids = '#saleField_' + element;
							mregs = jQuery(reghids).val().split('_');
							msals = jQuery(salhids).val().split('_');
							
							jQuery('#scd_regularPriceCurrency').val(mregs[2]);
							jQuery('#scd_salePriceCurrency').val(msals[2]);
						}
						
						ss++;
				}*/
				
			});
			var nmb = 'input:lt(' + numItems + ')';
			jQuery('#regPrice').find(nmb).remove();
			jQuery('#salPrice').find(nmb).remove();
		}

		
		//console.log(csStr);
	
		jQuery('#currencyValField').val(csStr);
		jQuery('#priceField').val(csVal);

	});
	

}

function bindVarious (){
	var mreg = "", msal = "";
	
	jQuery('#scd_regularCurrency').on('change', function(evt, params) {
		
		var reg = jQuery(this).val();
		jQuery('#scd_saleCurrency').val(reg);
		
		jQuery('#scd_regularPriceCurrency').val("");
		jQuery('#scd_salePriceCurrency').val("");
		
		var reghid = '#regularField_' + reg;
		var salhid = '#saleField_' + reg;
		mreg = jQuery(reghid).val().split('_');
		msal = jQuery(salhid).val().split('_');
		
		jQuery('#scd_regularPriceCurrency').val(mreg[2]);
		jQuery('#scd_salePriceCurrency').val(msal[2]);
	});
	
	
	jQuery('#scd_regularPriceCurrency').on('change paste', function(evt, params) {
		
		var csVal = "";
		var reg = jQuery('#scd_regularCurrency').val();
		var myval = jQuery('#scd_regularPriceCurrency').val();
		var hid = '#regularField_' + reg;
		var res = "regular_" + reg + "_" + myval;
		jQuery(hid).val(res);
		
		jQuery('#priceField').val("");
		jQuery("#regPrice :input").each(function(index, value) {
			
			var xval = jQuery(this).attr('value').split('_');
			
			if(index!==0) csVal += ',';
			
			//mreg = "regular_" + jQuery(this).val() + "_";
			//msal = "sale_" + jQuery(this).val() + "_";
					
			var reghid = '#regularField_' + xval[1];
			var salhid = '#saleField_' + xval[1];
			mreg = jQuery(reghid).val();
			msal = jQuery(salhid).val();
					
			csVal += mreg + "-" + msal;
			//alert(csVal);
		});
		jQuery('#priceField').val(csVal);

	});
	
	jQuery('#scd_salePriceCurrency').on('change paste', function(evt, params) {
		
		var csVal = "";
		var sal = jQuery('#scd_saleCurrency').val();
		var myval = jQuery('#scd_salePriceCurrency').val();
		var hid = '#saleField_' + sal;
		var res = "sale_" + sal + "_" + myval;
		jQuery(hid).val(res);
		
		jQuery('#priceField').val("");
		jQuery("#salPrice :input").each(function(index, value) {
			
			var xval = jQuery(this).attr('value').split('_');
			
			if(index!==0) csVal += ',';
			
			//mreg = "regular_" + jQuery(this).val() + "_";
			//msal = "sale_" + jQuery(this).val() + "_";
					
			var reghid = '#regularField_' + xval[1];
			var salhid = '#saleField_' + xval[1];
			mreg = jQuery(reghid).val();
			msal = jQuery(salhid).val();
					
			csVal += mreg + "-" + msal;
			//alert(csVal);
		});
		jQuery('#priceField').val(csVal);
	});
}

function bindValidate(){
	
	jQuery('#scd_regularPriceCurrency').on("keypress keyup blur",function (event) {
			
		jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g,''));
							
		if ((event.which != 46 || jQuery(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57) && event.which != 8) {
			event.preventDefault();
		}
	});
	
	jQuery('#scd_salePriceCurrency').on("keypress keyup blur",function (event) {
			
		jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g,''));
							
		if ((event.which != 46 || jQuery(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57) && event.which != 8) {
			event.preventDefault();
		}
	});
	
	
}


function bindFallreplaceOriginalToggle(){

	if( jQuery("#scd_replaceOriginalPrice").val()==='1' ){

	
		jQuery("#scd_tooltipTheme").closest("tr").hide();
		jQuery("#scd_tooltipAnimation").closest("tr").hide();
		jQuery("#scd_animationDuration").closest("tr").hide();
		jQuery("#scd_tooltipPosition").closest("tr").hide();
		jQuery("#scd_showTooltipArrow").closest("tr").hide();
		jQuery("#scd_tooltipDelay").closest("tr").hide();
		jQuery("#scd_tooltipAlwaysOpen").closest("tr").hide();
		jQuery("#scd_replacedContentFormat").closest("tr").show();
		
	//	console.log("shown");
	} else {

		jQuery("#scd_tooltipTheme").closest("tr").show();
		jQuery("#scd_tooltipAnimation").closest("tr").show();
		jQuery("#scd_animationDuration").closest("tr").show();
		jQuery("#scd_tooltipPosition").closest("tr").show();
		jQuery("#scd_showTooltipArrow").closest("tr").show();
		jQuery("#scd_tooltipDelay").closest("tr").show();
		jQuery("#scd_tooltipAlwaysOpen").closest("tr").show();
		jQuery("#scd_replacedContentFormat").closest("tr").hide();
	//	console.log("hidden");
	}

	jQuery("#scd_replaceOriginalPrice").change(function() {
		if( jQuery(this).val()==='0' ) {

			jQuery("#scd_tooltipTheme").closest("tr").show();
			jQuery("#scd_tooltipAnimation").closest("tr").show();
			jQuery("#scd_animationDuration").closest("tr").show();
			jQuery("#scd_tooltipPosition").closest("tr").show();
			jQuery("#scd_showTooltipArrow").closest("tr").show();
			jQuery("#scd_tooltipDelay").closest("tr").show();
			jQuery("#scd_tooltipAlwaysOpen").closest("tr").show();
			jQuery("#scd_replacedContentFormat").closest("tr").hide();
		} else {
			jQuery("#scd_tooltipTheme").closest("tr").hide();
			jQuery("#scd_tooltipAnimation").closest("tr").hide();
			jQuery("#scd_animationDuration").closest("tr").hide();
			jQuery("#scd_tooltipPosition").closest("tr").hide();
			jQuery("#scd_showTooltipArrow").closest("tr").hide();
			jQuery("#scd_tooltipDelay").closest("tr").hide();
			jQuery("#scd_tooltipAlwaysOpen").closest("tr").hide();
			jQuery("#scd_replacedContentFormat").closest("tr").show();
		}
	});
}


function initTabs(){

	jQuery(function() {
	  jQuery('a[href*="#"]:not([href="#"])').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
		  var target = jQuery(this.hash);
		  target = target.length ? target : jQuery('[name=' + this.hash.slice(1) +']');
		  if (target.length) {
			jQuery('html, body').animate({
			  scrollTop: target.offset().top
			}, 1000);
			return false;
		  }
		}
	  });
	});
}


jQuery(document).ready(function(){
   
	jQuery('input#scd_animationDuration').bind('change', function(){

		jQuery('#animationDurationLabel').text(jQuery('input#scd_animationDuration').val());
	});

	jQuery('input#scd_tooltipDelay').bind('change', function(){

		jQuery('#tooltipDelayLabel').text(jQuery('input#scd_tooltipDelay').val());
	});
	

	jQuery('.scd_chosen_select_static').chosen();


	bindChosenTargets (settings);
	
	bindChosenUserTargets (settings);

	if(typeof(mysettings)!== 'undefined'){
		bindRoleChosen (mysettings);
	 }
	bindVarious ();
	
	bindValidate();


	bindFallbackCurrencyToggle();

	bindFallreplaceOriginalToggle();

	//initTabs();
	
	jQuery('#scd_save_buton').click(function (e) {
        e.preventDefault();
      
        jQuery('#big-text').empty(); 
        var form = '<h2>SMART CURRENCY DETECTOR.</h2>\n\
<p style="font-size:18px">You are using <span style="color:red">SCD Free version</span>, all SCD currency settings as features and other currencies conversion (186 currencies) are not allowed. Download for free SCD Premium 14 days trial and install it together with SCD free version to benefit all the powerful features of currency conversion. Get SCD Premium 14 days trial <a href="https://gajelabs.com/14-day-free-trial/" target="_blank"> here. </a></p>\n\
<p style="font-size:18px">Also see our SCD products variants <a href="https://gajelabs.com/our-products/" target="_blank"> here</a>, to get a suitable SCD variant according to what you need.</p>';
       jQuery('#big-text').html(form);
    });	
});
