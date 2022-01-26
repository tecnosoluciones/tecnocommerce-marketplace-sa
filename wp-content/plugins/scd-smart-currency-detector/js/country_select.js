// wrap in UMD - see https://github.com/umdjs/umd/blob/master/jqueryPlugin.js
(function(factory) {
	if (typeof define === "function" && define.amd) {
		define([ "jquery" ], function($) {
			factory($, window, document);
		});
	} else {
		factory(jQuery, window, document);
	}
})(function($, window, document, undefined) {
	"use strict";
	var pluginName = "countrySelect", id = 1, // give each instance its own ID for namespaced event handling
	defaults = {
		// Default country
		defaultCountry: "",
		// Position the selected flag inside or outside of the input
		defaultStyling: "inside",
		// don't display these countries
		excludeCountries: [],
		// Display only these countries
		onlyCountries: [],
		// The countries at the top of the list. Defaults to United States and United Kingdom
		preferredCountries: [ "us", "gb" ],
		// Set the dropdown's width to be the same as the input. This is automatically enabled for small screens.
		responsiveDropdown: true,
	}, keys = {
		UP: 38,
		DOWN: 40,
		ENTER: 13,
		ESC: 27,
		PLUS: 43,
		A: 65,
		Z: 90
	}, windowLoaded = false;
	// keep track of if the window.load event has fired as impossible to check after the fact
	jQuery(window).on('load', function() {
		windowLoaded = true;
	});
	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
		this._defaults = defaults;
		// event namespace
		this.ns = "." + pluginName + id++;
		this._name = pluginName;
		this.init();
	}
	Plugin.prototype = {
		init: function() {
			// Process all the data: onlyCountries, excludeCountries, preferredCountries, defaultCountry etc
			this._processCountryData();
			// Generate the markup
			this._generateMarkup();
			// Set the initial state of the input value and the selected flag
			this._setInitialState();
			// Start all of the event listeners: input keyup, selectedFlag click
			this._initListeners();
			// Return this when the auto country is resolved.
			this.autoCountryDeferred = new $.Deferred();
			// Get auto country.
			this._initAutoCountry();

			return this.autoCountryDeferred;
		},
		/********************
		 *  PRIVATE METHODS
		 ********************/
		// prepare all of the country data, including onlyCountries, excludeCountries, preferredCountries and
		// defaultCountry options
		_processCountryData: function() {
			// Sort the countries
			allCountries.sort(sort_by('name', false, function(a){return a.toUpperCase()}));
			// set the instances country data objects
			this._setInstanceCountryData();
			// set the preferredCountries property
			this._setPreferredCountries();
		},
		// process onlyCountries array if present
		_setInstanceCountryData: function() {
			var that = this;
			if (this.options.onlyCountries.length) {
				var newCountries = [];
				$.each(this.options.onlyCountries, function(i, countryCode) {
					var countryData = that._getCountryData(countryCode, true);
					if (countryData) {
						newCountries.push(countryData);
					}
				});
				this.countries = newCountries;
			} else if (this.options.excludeCountries.length) {
                var lowerCaseExcludeCountries = this.options.excludeCountries.map(function(country) {
                    return country.toLowerCase();
                });
                this.countries = allCountries.filter(function(country) {
                    return lowerCaseExcludeCountries.indexOf(country.iso2) === -1;
                });
            } else {
				this.countries = allCountries;
			}
		},
		// Process preferred countries - iterate through the preferences,
		// fetching the country data for each one
		_setPreferredCountries: function() {
			var that = this;
			this.preferredCountries = [];
			$.each(this.options.preferredCountries, function(i, countryCode) {
				var countryData = that._getCountryData(countryCode, false);
				if (countryData) {
					that.preferredCountries.push(countryData);
				}
			});
		},
		// generate all of the markup for the plugin: the selected flag overlay, and the dropdown
		_generateMarkup: function() {
			// Country input
			this.countryInput = jQuery(this.element);
			// containers (mostly for positioning)
			var mainClass = "country-select";
			if (this.options.defaultStyling) {
				mainClass += " " + this.options.defaultStyling;
			}
			this.countryInput.wrap(jQuery("<div>", {
				"class": mainClass
			}));
			var flagsContainer = jQuery("<div>", {
				"class": "flag-dropdown"
			}).insertAfter(this.countryInput);
			// currently selected flag (displayed to left of input)
			var selectedFlag = jQuery("<div>", {
				"class": "selected-flag"
			}).appendTo(flagsContainer);
			this.selectedFlagInner = jQuery("<div>", {
				"class": "flag"
			}).appendTo(selectedFlag);
			// CSS triangle
			jQuery("<div>", {
				"class": "arrow"
			}).appendTo(selectedFlag);
			// country list contains: preferred countries, then divider, then all countries
			this.countryList = jQuery("<ul>", {
				"class": "country-list v-hide"
			}).appendTo(flagsContainer);
			if (this.preferredCountries.length) {
				this._appendListItems(this.preferredCountries, "preferred");
				jQuery("<li>", {
					"class": "divider"
				}).appendTo(this.countryList);
			}
			this._appendListItems(this.countries, "");
			// Add the hidden input for the country code
			this.countryCodeInput = jQuery("#"+this.countryInput.attr("id")+"_code");
			if (!this.countryCodeInput) {
				this.countryCodeInput = jQuery('<input type="hidden" id="'+this.countryInput.attr("id")+'_code" name="'+this.countryInput.attr("name")+'_code" value="" />');
				this.countryCodeInput.insertAfter(this.countryInput);
			}
			// now we can grab the dropdown height, and hide it properly
			this.dropdownHeight = this.countryList.outerHeight();
			// set the dropdown width according to the input if responsiveDropdown option is present or if it's a small screen
			if (this.options.responsiveDropdown) {
				jQuery(window).resize(function() {
					jQuery('.country-select').each(function(i, obj) {
						var dropdownWidth = jQuery(obj).width();
						jQuery(obj).find('.country-list').css("width", dropdownWidth + "px");
					});
				}).resize();
			}
			this.countryList.removeClass("v-hide").addClass("hide");
			// this is useful in lots of places
			this.countryListItems = this.countryList.children(".country");
		},
		// add a country <li> to the countryList <ul> container
		_appendListItems: function(countries, className) {
			// Generate DOM elements as a large temp string, so that there is only
			// one DOM insert event
			var tmp = "";
			// for each country
			$.each(countries, function(i, c) {
				
				// open the list item
				tmp += '<li class="country ' + className + '" data-country-code="' + c.iso2 + '" data-currency-code="' + c.name + '">';
				// add the flag
				tmp += '<div class="flag ' + c.iso2 + '"></div>';
				// and the country name
				tmp += '<span class="country-name">' + c.name + ' (' + c.cur + ')' + '</span>';
				// close the list item
				tmp += '</li>';
			});
			this.countryList.append(tmp);
		},
		// set the initial state of the input value and the selected flag
		_setInitialState: function() {
			var flagIsSet = false;
			// If the input is pre-populated, then just update the selected flag
			if (this.countryInput.val()) {
				flagIsSet = this._updateFlagFromInputVal();
			}
			// If the country code input is pre-populated, update the name and the selected flag
			var selectedCode = this.countryCodeInput.val();
			if (selectedCode) {
				this.selectCountry(selectedCode);
			}
			if (!flagIsSet) {
				// flag is not set, so set to the default country
				var defaultCountry;
				// check the defaultCountry option, else fall back to the first in the list
				if (this.options.defaultCountry) {
					defaultCountry = this._getCountryData(this.options.defaultCountry, false);
					// Did we not find the requested default country?
					if (!defaultCountry) {
						defaultCountry = this.preferredCountries.length ? this.preferredCountries[0] : this.countries[0];
					}
				} else {
					defaultCountry = this.preferredCountries.length ? this.preferredCountries[0] : this.countries[0];
				}
				this.defaultCountry = defaultCountry.iso2;
			}
		},
		// initialise the main event listeners: input keyup, and click selected flag
		_initListeners: function() {
			var that = this;
			// Update flag on keyup.
			// Use keyup instead of keypress because we want to update on backspace
			// and instead of keydown because the value hasn't updated when that
			// event is fired.
			// NOTE: better to have this one listener all the time instead of
			// starting it on focus and stopping it on blur, because then you've
			// got two listeners (focus and blur)
			this.countryInput.on("keyup" + this.ns, function() {
				that._updateFlagFromInputVal();
			});
			// toggle country dropdown on click
			var selectedFlag = this.selectedFlagInner.parent();
			selectedFlag.on("click" + this.ns, function(e) {
				// only intercept this event if we're opening the dropdown
				// else let it bubble up to the top ("click-off-to-close" listener)
				// we cannot just stopPropagation as it may be needed to close another instance
				if (that.countryList.hasClass("hide") && !that.countryInput.prop("disabled")) {
					that._showDropdown();
				}
			});
			// Despite above note, added blur to ensure partially spelled country
			// with correctly chosen flag is spelled out on blur. Also, correctly
			// selects flag when field is autofilled
			this.countryInput.on("blur" + this.ns, function() {
				if (that.countryInput.val() != that.getSelectedCountryData().name) {
					that.setCountry(that.countryInput.val());
				}
				that.countryInput.val(that.getSelectedCountryData().name);
			});
		},
		_initAutoCountry: function() {
			if (this.options.initialCountry === "auto") {
				this._loadAutoCountry();
			} else {
				if (this.defaultCountry) {
					this.selectCountry(this.defaultCountry);
				}
				this.autoCountryDeferred.resolve();
			}
		},
		// perform the geo ip lookup
		_loadAutoCountry: function() {
			var that = this;

			// 3 options:
			// 1) already loaded (we're done)
			// 2) not already started loading (start)
			// 3) already started loading (do nothing - just wait for loading callback to fire)
			if ($.fn[pluginName].autoCountry) {
				this.handleAutoCountry();
			} else if (!$.fn[pluginName].startedLoadingAutoCountry) {
				// don't do this twice!
				$.fn[pluginName].startedLoadingAutoCountry = true;

				if (typeof this.options.geoIpLookup === 'function') {
					this.options.geoIpLookup(function(countryCode) {
						$.fn[pluginName].autoCountry = countryCode.toLowerCase();
						// tell all instances the auto country is ready
						// TODO: this should just be the current instances
						// UPDATE: use setTimeout in case their geoIpLookup function calls this callback straight away (e.g. if they have already done the geo ip lookup somewhere else). Using setTimeout means that the current thread of execution will finish before executing this, which allows the plugin to finish initialising.
						setTimeout(function() {
							jQuery(".country-select input").countrySelect("handleAutoCountry");
						});
					});
				}
			}
		},
		// Focus input and put the cursor at the end
		_focus: function() {
			this.countryInput.focus();
			var input = this.countryInput[0];
			// works for Chrome, FF, Safari, IE9+
			if (input.setSelectionRange) {
				var len = this.countryInput.val().length;
				input.setSelectionRange(len, len);
			}
		},
		// Show the dropdown
		_showDropdown: function() {
			this._setDropdownPosition();
			// update highlighting and scroll to active list item
			var activeListItem = this.countryList.children(".active");
			this._highlightListItem(activeListItem);
			// show it
			this.countryList.removeClass("hide");
			this._scrollTo(activeListItem);
			// bind all the dropdown-related listeners: mouseover, click, click-off, keydown
			this._bindDropdownListeners();
			// update the arrow
			this.selectedFlagInner.parent().children(".arrow").addClass("up");
		},
		// decide where to position dropdown (depends on position within viewport, and scroll)
		_setDropdownPosition: function() {
			var inputTop = this.countryInput.offset().top, windowTop = jQuery(window).scrollTop(),
			dropdownFitsBelow = inputTop + this.countryInput.outerHeight() + this.dropdownHeight < windowTop + jQuery(window).height(), dropdownFitsAbove = inputTop - this.dropdownHeight > windowTop;
			// dropdownHeight - 1 for border
			var cssTop = !dropdownFitsBelow && dropdownFitsAbove ? "-" + (this.dropdownHeight - 1) + "px" : "";
			this.countryList.css("top", cssTop);
		},
		// we only bind dropdown listeners when the dropdown is open
		_bindDropdownListeners: function() {
			var that = this;
			// when mouse over a list item, just highlight that one
			// we add the class "highlight", so if they hit "enter" we know which one to select
			this.countryList.on("mouseover" + this.ns, ".country", function(e) {
				that._highlightListItem(jQuery(this));
			});
			// listen for country selection
			this.countryList.on("click" + this.ns, ".country", function(e) {
				that._selectListItem(jQuery(this));
			});
			// click off to close
			// (except when this initial opening click is bubbling up)
			// we cannot just stopPropagation as it may be needed to close another instance
			var isOpening = true;
			jQuery("html").on("click" + this.ns, function(e) {
				if (!isOpening) {
					that._closeDropdown();
				}
				isOpening = false;
			});
			// Listen for up/down scrolling, enter to select, or letters to jump to country name.
			// Use keydown as keypress doesn't fire for non-char keys and we want to catch if they
			// just hit down and hold it to scroll down (no keyup event).
			// Listen on the document because that's where key events are triggered if no input has focus
			jQuery(document).on("keydown" + this.ns, function(e) {
				// prevent down key from scrolling the whole page,
				// and enter key from submitting a form etc
				e.preventDefault();
				if (e.which == keys.UP || e.which == keys.DOWN) {
					// up and down to navigate
					that._handleUpDownKey(e.which);
				} else if (e.which == keys.ENTER) {
					// enter to select
					that._handleEnterKey();
				} else if (e.which == keys.ESC) {
					// esc to close
					that._closeDropdown();
				} else if (e.which >= keys.A && e.which <= keys.Z) {
					// upper case letters (note: keyup/keydown only return upper case letters)
					// cycle through countries beginning with that letter
					that._handleLetterKey(e.which);
				}
			});
		},
		// Highlight the next/prev item in the list (and ensure it is visible)
		_handleUpDownKey: function(key) {
			var current = this.countryList.children(".highlight").first();
			var next = key == keys.UP ? current.prev() : current.next();
			if (next.length) {
				// skip the divider
				if (next.hasClass("divider")) {
					next = key == keys.UP ? next.prev() : next.next();
				}
				this._highlightListItem(next);
				this._scrollTo(next);
			}
		},
		// select the currently highlighted item
		_handleEnterKey: function() {
			var currentCountry = this.countryList.children(".highlight").first();
			if (currentCountry.length) {
				this._selectListItem(currentCountry);
			}
		},
		// Iterate through the countries starting with the given letter
		_handleLetterKey: function(key) {
			var letter = String.fromCharCode(key);
			// filter out the countries beginning with that letter
			var countries = this.countryListItems.filter(function() {
				return jQuery(this).text().charAt(0) == letter && !jQuery(this).hasClass("preferred");
			});
			if (countries.length) {
				// if one is already highlighted, then we want the next one
				var highlightedCountry = countries.filter(".highlight").first(), listItem;
				// if the next country in the list also starts with that letter
				if (highlightedCountry && highlightedCountry.next() && highlightedCountry.next().text().charAt(0) == letter) {
					listItem = highlightedCountry.next();
				} else {
					listItem = countries.first();
				}
				// update highlighting and scroll
				this._highlightListItem(listItem);
				this._scrollTo(listItem);
			}
		},
		// Update the selected flag using the input's current value
		_updateFlagFromInputVal: function() {
			var that = this;
			// try and extract valid country from input
			var value = this.countryInput.val().replace(/(?=[() ])/g, '\\');
			if (value) {
				var countryCodes = [];
				var matcher = new RegExp("^"+value, "i");
				for (var i = 0; i < this.countries.length; i++) {
					if (this.countries[i].name.match(matcher)) {
						countryCodes.push(this.countries[i].iso2);
					}
				}
				// Check if one of the matching countries is already selected
				var alreadySelected = false;
				$.each(countryCodes, function(i, c) {
					if (that.selectedFlagInner.hasClass(c)) {
						alreadySelected = true;
					}
				});
				if (!alreadySelected) {
					this._selectFlag(countryCodes[0]);
					this.countryCodeInput.val(countryCodes[0]).trigger("change");
				}
				// Matching country found
				return true;
			}
			// No match found
			return false;
		},
		// remove highlighting from other list items and highlight the given item
		_highlightListItem: function(listItem) {
			this.countryListItems.removeClass("highlight");
			listItem.addClass("highlight");
		},
		// find the country data for the given country code
		// the ignoreOnlyCountriesOption is only used during init() while parsing the onlyCountries array
		_getCountryData: function(countryCode, ignoreOnlyCountriesOption) {
			var countryList = ignoreOnlyCountriesOption ? allCountries : this.countries;
			for (var i = 0; i < countryList.length; i++) {
				if (countryList[i].iso2 == countryCode) {
					return countryList[i];
				}
			}
			return null;
		},
		// update the selected flag and the active list item
		_selectFlag: function(countryCode) {
			if (! countryCode) {
				return false;
			}
			this.selectedFlagInner.attr("class", "flag " + countryCode);
			// update the title attribute
			var countryData = this._getCountryData(countryCode);
			this.selectedFlagInner.parent().attr("title", countryData.name);
			// update the active list item
			var listItem = this.countryListItems.children(".flag." + countryCode).first().parent();
			this.countryListItems.removeClass("active");
			listItem.addClass("active");
		},
		// called when the user selects a list item from the dropdown
		_selectListItem: function(listItem) {
			// update selected flag and active list item
			var countryCode = listItem.attr("data-country-code");
			this._selectFlag(countryCode);
			this._closeDropdown();
			// update input value
			this._updateName(countryCode);
			this.countryInput.trigger("change");
			this.countryCodeInput.trigger("change");
			// focus the input
			this._focus();
		},
		// close the dropdown and unbind any listeners
		_closeDropdown: function() {
			this.countryList.addClass("hide");
			// update the arrow
			this.selectedFlagInner.parent().children(".arrow").removeClass("up");
			// unbind event listeners
			jQuery(document).off("keydown" + this.ns);
			jQuery("html").off("click" + this.ns);
			// unbind both hover and click listeners
			this.countryList.off(this.ns);
		},
		// check if an element is visible within its container, else scroll until it is
		_scrollTo: function(element) {
			if (!element || !element.offset()) {
				return;
			}
			var container = this.countryList, containerHeight = container.height(), containerTop = container.offset().top, containerBottom = containerTop + containerHeight, elementHeight = element.outerHeight(), elementTop = element.offset().top, elementBottom = elementTop + elementHeight, newScrollTop = elementTop - containerTop + container.scrollTop();
			if (elementTop < containerTop) {
				// scroll up
				container.scrollTop(newScrollTop);
			} else if (elementBottom > containerBottom) {
				// scroll down
				var heightDifference = containerHeight - elementHeight;
				container.scrollTop(newScrollTop - heightDifference);
			}
		},
		// Replace any existing country name with the new one
		_updateName: function(countryCode) {
			this.countryCodeInput.val(countryCode).trigger("change");
			this.countryInput.val(this._getCountryData(countryCode).name);
		},
		/********************
		 *  PUBLIC METHODS
		 ********************/
		// this is called when the geoip call returns
		handleAutoCountry: function() {
			if (this.options.initialCountry === "auto") {
				// we must set this even if there is an initial val in the input: in case the initial val is invalid and they delete it - they should see their auto country
				this.defaultCountry = $.fn[pluginName].autoCountry;
				// if there's no initial value in the input, then update the flag
				if (!this.countryInput.val()) {
					this.selectCountry(this.defaultCountry);
				}
				this.autoCountryDeferred.resolve();
			}
		},
		// get the country data for the currently selected flag
		getSelectedCountryData: function() {
			// rely on the fact that we only set 2 classes on the selected flag element:
			// the first is "flag" and the second is the 2-char country code
			var countryCode = this.selectedFlagInner.attr("class").split(" ")[1];
			return this._getCountryData(countryCode);
		},
		// update the selected flag
		selectCountry: function(countryCode) {
			countryCode = countryCode.toLowerCase();
			// check if already selected
			if (!this.selectedFlagInner.hasClass(countryCode)) {
				this._selectFlag(countryCode);
				this._updateName(countryCode);
			}
		},
		// set the input value and update the flag
		setCountry: function(country) {
			this.countryInput.val(country);
			this._updateFlagFromInputVal();
		},
		// remove plugin
		destroy: function() {
			// stop listeners
			this.countryInput.off(this.ns);
			this.selectedFlagInner.parent().off(this.ns);
			// remove markup
			var container = this.countryInput.parent();
			container.before(this.countryInput).remove();
		}
	};
	// adapted to allow public functions
	// using https://github.com/jquery-boilerplate/jquery-boilerplate/wiki/Extending-jQuery-Boilerplate
	$.fn[pluginName] = function(options) {
		var args = arguments;
		// Is the first parameter an object (options), or was omitted,
		// instantiate a new instance of the plugin.
		if (options === undefined || typeof options === "object") {
			return this.each(function() {
				if (!$.data(this, "plugin_" + pluginName)) {
					$.data(this, "plugin_" + pluginName, new Plugin(this, options));
				}
			});
		} else if (typeof options === "string" && options[0] !== "_" && options !== "init") {
			// If the first parameter is a string and it doesn't start
			// with an underscore or "contains" the `init`-function,
			// treat this as a call to a public method.
			// Cache the method call to make it possible to return a value
			var returns;
			this.each(function() {
				var instance = $.data(this, "plugin_" + pluginName);
				// Tests that there's already a plugin-instance
				// and checks that the requested public method exists
				if (instance instanceof Plugin && typeof instance[options] === "function") {
					// Call the method of our plugin instance,
					// and pass it the supplied arguments.
					returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
				}
				// Allow instances to be destroyed via the 'destroy' method
				if (options === "destroy") {
					$.data(this, "plugin_" + pluginName, null);
				}
			});
			// If the earlier cached method gives a value back return the value,
			// otherwise return this to preserve chainability.
			return returns !== undefined ? returns : this;
		}
	};
	/********************
   *  STATIC METHODS
   ********************/
	// get the country data object
	$.fn[pluginName].getCountryData = function() {
		return allCountries;
	};
	// set the country data object
	$.fn[pluginName].setCountryData = function(obj) {
		allCountries = obj;
	};
	// Tell JSHint to ignore this warning: "character may get silently deleted by one or more browsers"
	// jshint -W100
	// Array of country objects for the flag dropdown.
	// Each contains a name and country code (ISO 3166-1 alpha-2).
	//
	// Note: using single char property names to keep filesize down
	// n = name
	// i = iso2 (2-char country code)
	var allCountries = $.each([ {
		n: "Afghan afghani",
		i: "af",
		c: "AFN"
	}, {
		n: "Albanian lek",
		i: "al",
		c: "ALL"
	}, {
		n: "Algeria dinar",
		i: "dz",
		c: "DZD"
	}, {
		n: "Angolan kwanza",
		i: "ao",
		c: "AOA"
	}, {
		n: "Argentine peso",
		i: "ar",
		c: "ARS"
	}, {
		n: "Armenian dram",
		i: "am",
		c: "AMD"
	}, {
		n: "Aruban florin",
		i: "aw",
		c: "AWG"
	}, {
		n: "Australian dollar",
		i: "au",
		c: "AUD"
	}, {
		n: "Azerbaijani manat",
		i: "az",
		c: "AZN"
	}, {
		n: "Bahamian dollar",
		i: "bs",
		c: "BSD"
	}, {
		n: "Bahraini dinar",
		i: "bh",
		c: "BHD"
	}, {
		n: "Bangladeshi taka",
		i: "bd",
		c: "BDT"
	}, {
		n: "Barbadian dollar",
		i: "bb",
		c: "BBD"
	}, {
		n: "Belarusian ruble",
		i: "by",
		c: "BYR"
	}, {
		n: "Belize dollar",
		i: "bz",
		c: "BZD"
	}, {
		n: "Bermudian dollar",
		i: "bm",
		c: "BMD"
	}, {
		n: "Bhutanese ngultrum",
		i: "bt",
		c: "BTN"
	}, {
		n: "Bolivian boliviano",
		i: "bo",
		c: "BOB"
	}, {
		n: "Bosnia and Herzegovina convertible mark",
		i: "ba",
		c: "BAM"
	}, {
		n: "Botswana pula",
		i: "bw",
		c: "BWP"
	}, {
		n: "Brazilian real",
		i: "br",
		c: "BRL"
	}, {
		n: "British pound",
		i: "gb",
		c: "GBP"
	}, {
		n: "Brunei dollar",
		i: "bn",
		c: "BND"
	}, {
		n: "Bulgarian lev",
		i: "bg",
		c: "BGN"
	}, {
		n: "Burundian franc",
		i: "bi",
		c: "BIF"
	}, {
		n: "Cambodian riel",
		i: "kh",
		c: "KHR"
	}, {
		n: "Canada",
		i: "ca",
		c: "CAD"
	}, {
		n: "Cape Verdean escudo",
		i: "cv",
		c: "CVE"
	}, {
		n: "Cayman islands dollar",
		i: "ky",
		c: "KYD"
	}, {
		n: "Central African CFA franc",
		i: "cf",
		c: "XAF"
	}, {
		n: "CFP franc",
		i: "pf",
		c: "XPF"
	}, {
		n: "Chilean peso",
		i: "cl",
		c: "CLP"
	}, {
		n: "Chinese yuan",
		i: "cn",
		c: "CNY"
	}, {
		n: "Colombian peso",
		i: "co",
		c: "COP"
	}, {
		n: "Congolese franc",
		i: "cd",
		c: "CDF"
	}, {
		n: "Costa Rican colón",
		i: "cr",
		c: "CRC"
	}, {
		n: "Croatian kuna",
		i: "hr",
		c: "HRK"
	}, {
		n: "Cuban convertible peso",
		i: "cu",
		c: "CUP"
	}, {
		n: "Czech koruna",
		i: "cz",
		c: "CZK"
	}, {
		n: "Djiboutian franc",
		i: "dj",
		c: "DJF"
	}, {
		n: "Dominican peso",
		i: "do",
		c: "DOP"
	},{
		n: "Danish Krone",
		i: "dk",
		c: "DKK"
	}, {
		n: "Egyptian pound",
		i: "eg",
		c: "EGP"
	}, {
		n: "Eritrean nakfa",
		i: "er",
		c: "ERN"
	}, {
		n: "European union",
		i: "eu",
		c: "EUR"
	}, {
		n: "East Caribbean dollar",
		i: "lc",
		c: "XCD"
	}, {
		n: "Ethiopian birr",
		i: "et",
		c: "ETB"
	}, {
		n: "Falkland Islands pound",
		i: "fk",
		c: "FKP"
	}, {
		n: "Fijian dollar",
		i: "fj",
		c: "FJD"
	}, {
		n: "Gambian dalasi",
		i: "gm",
		c: "GMD"
	}, {
		n: "Georgian lari",
		i: "ge",
		c: "GEL"
	}, {
		n: "Ghana cedi",
		i: "gh",
		c: "GHS"
	}, {
		n: "Guatemalan quetzal",
		i: "gt",
		c: "GTQ"
	}, {
		n: "Guinean franc",
		i: "gn",
		c: "GNF"
	}, {
		n: "Guyanese dollar",
		i: "gy",
		c: "GYD"
	}, {
		n: "Haitian gourde",
		i: "ht",
		c: "HTG"
	}, {
		n: "Honduran lempira",
		i: "hn",
		c: "HNL"
	}, {
		n: "Hong Kong dollar",
		i: "hk",
		c: "HKD"
	}, {
		n: "Hungarian forint",
		i: "hu",
		c: "HUF"
	}, {
		n: "Icelandic króna",
		i: "is",
		c: "ISK"
	}, {
		n: "India rupee",
		i: "in",
		c: "INR"
	}, {
		n: "Indonesian rupiah",
		i: "id",
		c: "IDR"
	}, {
		n: "Iranian rial",
		i: "ir",
		c: "IRR"
	}, {
		n: "Iraqi dinar",
		i: "iq",
		c: "IQD"
	}, {
		n: "Israeli new shekel",
		i: "il",
		c: "ILS"
	}, {
		n: "Jamaican dollar",
		i: "jm",
		c: "JMD"
	}, {
		n: "Japanese yen",
		i: "jp",
		c: "JPY"
	}, {
		n: "Jersey pound",
		i: "je",
		c: "JEP"
	}, {
		n: "Jordanian dinar",
		i: "jo",
		c: "JOD"
	}, {
		n: "Kazakhstani tenge",
		i: "kz",
		c: "KZT"
	}, {
		n: "Kenyan shilling",
		i: "ke",
		c: "KES"
	}, {
		n: "Kuwaiti dinar",
		i: "kw",
		c: "KWD"
	}, {
		n: "Kyrgyzstani som",
		i: "kg",
		c: "KGS"
	}, {
		n: "Lebanese pound",
		i: "lb",
		c: "LBP"
	}, {
		n: "Latvian lats",
		i: "lv",
		c: "LVL"
	}, {
		n: "Lesotho loti",
		i: "ls",
		c: "LSL"
	}, {
		n: "Liberian dollar",
		i: "lr",
		c: "LRD"
	}, {
		n: "Libyan dinar",
		i: "ly",
		c: "LYD"
	}, {
		n: "Lithuanian litas",
		i: "lt",
		c: "LTL"
	}, {
		n: "Macedonian denar",
		i: "mk",
		c: "MKD"
	}, {
		n: "Malawian kwacha",
		i: "mw",
		c: "MWK"
	}, {
		n: "Malaysian ringgit",
		i: "my",
		c: "MYR"
	}, {
		n: "Maldivian rufiyaa",
		i: "mv",
		c: "MVR"
	}, {
		n: "Mauritanian ouguiya",
		i: "mr",
		c: "MRO"
	}, {
		n: "Mauritian rupee",
		i: "mu",
		c: "MUR"
	}, {
		n: "Mexicon peso",
		i: "mx",
		c: "MXN"
	}, {
		n: "Moldovan leu",
		i: "md",
		c: "MDL"
	}, {
		n: "Mongolian tögrög",
		i: "mn",
		c: "MNT"
	}, {
		n: "Moroccan dirham",
		i: "ma",
		c: "MAD"
	}, {
		n: "Mozambican metical",
		i: "mz",
		c: "MZN"
	}, {
		n: "Namibian dollar",
		i: "na",
		c: "NAD"
	}, {
		n: "Nepalese rupee",
		i: "np",
		c: "NPR"
	}, {
		n: "New Taiwan dollar",
		i: "tw",
		c: "TWD"
	}, {
		n: "New Zealand dollar",
		i: "nz",
		c: "NZD"
	}, {
		n: "Nicaraguan córdoba",
		i: "ni",
		c: "NIO"
	}, {
		n: "Nigeria naira",
		i: "ng",
		c: "NGN"
	}, {
		n: "North Korean won",
		i: "kp",
		c: "KPW"
	}, {
		n: "Norwegian krone",
		i: "no",
		c: "NOK"
	}, {
		n: "Omani rial",
		i: "om",
		c: "OMR"
	}, {
		n: "Pakistani rupee",
		i: "pk",
		c: "PKR"
	}, {
		n: "Panamanian balboa",
		i: "pa",
		c: "PAB"
	}, {
		n: "Papua New Guinean kina",
		i: "pg",
		c: "PGK"
	}, {
		n: "Paraguayan guaraní",
		i: "py",
		c: "PYG"
	}, {
		n: "Peruvian nuevo sol",
		i: "pe",
		c: "PEN"
	}, {
		n: "Philippine peso",
		i: "ph",
		c: "PHP"
	}, {
		n: "Polish złoty",
		i: "pl",
		c: "PLN"
	}, {
		n: "Qatari riyal",
		i: "qa",
		c: "QAR"
	}, {
		n: "Romanian leu",
		i: "ro",
		c: "RON"
	}, {
		n: "Russian ruble",
		i: "ru",
		c: "RUB"
	}, {
		n: "Rwandan franc",
		i: "rw",
		c: "RWF"
	}, {
		n: "Saint Helena pound",
		i: "sh",
		c: "SHP"
	}, {
		n: "Salvadoran colón",
		i: "sv",
		c: "SVC"
	}, {
		n: "Samoan t�?l�?",
		i: "ws",
		c: "WST"
	}, {
		n: "São Tomé and Príncipe dobra",
		i: "st",
		c: "STD"
	}, {
		n: "Saudi riyal",
		i: "sa",
		c: "SAR"
	}, {
		n: "Serbian dinar",
		i: "rs",
		c: "RSD"
	}, {
		n: "Seychellois rupee",
		i: "sc",
		c: "SCR"
	}, {
		n: "Sierra Leonean leone",
		i: "sl",
		c: "SLL"
	}, {
		n: "Singapore dollar",
		i: "sg",
		c: "SGD"
	}, {
		n: "Solomon Islands dollar",
		i: "sb",
		c: "SBD"
	}, {
		n: "Somali shilling",
		i: "so",
		c: "SOS"
	}, {
		n: "South African rand",
		i: "za",
		c: "ZAR"
	}, {
		n: "South Korean won",
		i: "kr",
		c: "KRW"
	}, {
		n: "South Sudanese pound",
		i: "ss",
		c: "SSP"
	}, {
		n: "Sri Lankan rupee",
		i: "lk",
		c: "LKR"
	}, {
		n: "Sudan",
		i: "sd",
		c: "SDG"
	}, {
		n: "Surinamese dollar",
		i: "sr",
		c: "SRD"
	}, {
		n: "Swazi lilangeni",
		i: "sz",
		c: "SZL"
	}, {
		n: "Swedish krona",
		i: "se",
		c: "SEK"
	}, {
		n: "Switzerland",
		i: "ch",
		c: "CHF"
	}, {
		n: "Syrian pound",
		i: "sy",
		c: "SYP"
	}, {
		n: "Tajikistani somoni",
		i: "tj",
		c: "TJS"
	}, {
		n: "Tanzanian shilling",
		i: "tz",
		c: "TZS"
	}, {
		n: "Thai baht",
		i: "th",
		c: "THB"
	}, {
		n: "Tongan paʻanga",
		i: "to",
		c: "TOP"
	}, {
		n: "Trinidad and Tobago dollar",
		i: "tt",
		c: "TTD"
	}, {
		n: "Tunisian dinar",
		i: "tn",
		c: "TND"
	}, {
		n: "Turkish lira",
		i: "tr",
		c: "TRY"
	}, {
		n: "Turkmenistan manat",
		i: "tm",
		c: "TMT"
	}, {
		n: "Ugandan shilling",
		i: "ug",
		c: "UGX"
	}, {
		n: "Ukrainian hryvnia",
		i: "ua",
		c: "UAH"
	}, {
		n: "United Arab Emirates dirham",
		i: "ae",
		c: "AED"
	}, {
		n: "United states dollar",
		i: "us",
		c: "USD"
	}, {
		n: "Uruguayan peso",
		i: "uy",
		c: "UYI"
	}, {
		n: "Uzbekistani som",
		i: "uz",
		c: "UZS"
	}, {
		n: "Vanuatu vatu",
		i: "vu",
		c: "VUV"
	}, {
		n: "Venezuelan bolívar",
		i: "ve",
		c: "VEF"
	}, {
		n: "Vietnamese đồng",
		i: "vn",
		c: "VND"
	}, {
		n: "West African CFA franc",
		i: "bf",
		c: "XOF"
	}, {
		n: "Yemeni rial",
		i: "ye",
		c: "YER"
	}, {
		n: "Zambian kwacha",
		i: "zm",
		c: "ZMW"
	}, {
		n: "Zimbabwean dollar",
		i: "zw",
		c: "ZWL"
	} ], function(i, c) {
		c.name = c.c;
		c.iso2 = c.i;
		c.cur = c.n;
		delete c.n;
		delete c.i;
		delete c.c;
	});

	// Sort function - Added for SCD widget
	// Obtained from: https://stackoverflow.com/questions/979256/sorting-an-array-of-javascript-objects-by-property
	var sort_by = function(field, reverse, primer){

		var key = primer ? 
			function(x) {return primer(x[field])} : 
			function(x) {return x[field]};
	 
		reverse = !reverse ? 1 : -1;
	 
		return function (a, b) {
			return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
		  } 
	 }

});
