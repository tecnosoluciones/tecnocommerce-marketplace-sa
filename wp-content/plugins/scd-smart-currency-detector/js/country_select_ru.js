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
		n: "афгани",
		i: "af",
		c: "AFN"
	}, {
		n: "�?лбан�?кий лек",
		i: "al",
		c: "ALL"
	}, {
		n: "�?лжир динар",
		i: "dz",
		c: "DZD"
	}, {
		n: "анголь�?кой кванза",
		i: "ao",
		c: "AOA"
	}, {
		n: "Argentine peso",
		i: "ar",
		c: "ARS"
	}, {
		n: "�?рм�?н�?кий драм",
		i: "am",
		c: "AMD"
	}, {
		n: "�?руба флорин",
		i: "aw",
		c: "AWG"
	}, {
		n: "�?в�?тралий�?кий доллар",
		i: "au",
		c: "AUD"
	}, {
		n: "�?зербайджан�?кий манат",
		i: "az",
		c: "AZN"
	}, {
		n: "Багам�?кий доллар",
		i: "bs",
		c: "BSD"
	}, {
		n: "Бахрейн�?кий динар",
		i: "bh",
		c: "BHD"
	}, {
		n: "бангладеш�?ких така",
		i: "bd",
		c: "BDT"
	}, {
		n: "Барбадо�?�?кий доллар",
		i: "bb",
		c: "BBD"
	}, {
		n: "Белору�?�?кий рубль",
		i: "by",
		c: "BYR"
	}, {
		n: "Белиз�?кий доллар",
		i: "bz",
		c: "BZD"
	}, {
		n: "бермуд�?кий доллар",
		i: "bm",
		c: "BMD"
	}, {
		n: "�?ГУЛТРУМ",
		i: "bt",
		c: "BTN"
	}, {
		n: "Боливиано",
		i: "bo",
		c: "BOB"
	}, {
		n: "Бо�?ни�? и Герцеговина конвертируема�? марка",
		i: "ba",
		c: "BAM"
	}, {
		n: "Бот�?вана Пулы",
		i: "bw",
		c: "BWP"
	}, {
		n: "Бразиль�?кий реал",
		i: "br",
		c: "BRL"
	}, {
		n: "Британ�?кий фунт",
		i: "gb",
		c: "GBP"
	}, {
		n: "Бруней�?кий доллар",
		i: "bn",
		c: "BND"
	}, {
		n: "Болгар�?кий лев",
		i: "bg",
		c: "BGN"
	}, {
		n: "Бурундий�?кий франк",
		i: "bi",
		c: "BIF"
	}, {
		n: "Камбоджий�?кий Риель",
		i: "kh",
		c: "KHR"
	}, {
		n: "Канада",
		i: "ca",
		c: "CAD"
	}, {
		n: "Э�?кудо Кабо-Верде",
		i: "cv",
		c: "CVE"
	}, {
		n: "Доллар Каймановых о�?тровов",
		i: "ky",
		c: "KYD"
	}, {
		n: "Центральный КФ�? франк",
		i: "cf",
		c: "XAF"
	}, {
		n: "КФП",
		i: "pf",
		c: "XPF"
	}, {
		n: "Чилий�?кий пе�?о",
		i: "cl",
		c: "CLP"
	}, {
		n: "Китай�?кий юань",
		i: "cn",
		c: "CNY"
	}, {
		n: "Колумбий�?кий пе�?о",
		i: "co",
		c: "COP"
	}, {
		n: "Конголез�?кий франк",
		i: "cd",
		c: "CDF"
	}, {
		n: "Конголез�?кий франк",
		i: "cr",
		c: "CRC"
	}, {
		n: "хорват�?кие куны",
		i: "hr",
		c: "HRK"
	}, {
		n: "конвертируемый кубин�?кий пе�?о",
		i: "cu",
		c: "CUP"
	}, {
		n: "Чеш�?ка�? крона",
		i: "cz",
		c: "CZK"
	}, {
		n: "Франк Джибути",
		i: "dj",
		c: "DJF"
	}, {
		n: "Доминикан�?кий пе�?о",
		i: "do",
		c: "DOP"
	}, {
		n: "Египет�?кий фунт",
		i: "eg",
		c: "EGP"
	}, {
		n: "�?ритрей�?ка�? накфа",
		i: "er",
		c: "ERN"
	}, {
		n: "Европей�?кий �?оюз",
		i: "eu",
		c: "EUR"
	}, {
		n: "Во�?точно-кариб�?кий доллар",
		i: "lc",
		c: "XCD"
	}, {
		n: "Эфиоп�?кий быр",
		i: "et",
		c: "ETB"
	}, {
		n: "Фолкленд�?кие о�?трова фунт",
		i: "fk",
		c: "FKP"
	}, {
		n: "фиджи доллар",
		i: "fj",
		c: "FJD"
	}, {
		n: "гамбий�?кие дала�?и",
		i: "gm",
		c: "GMD"
	}, {
		n: "Грузин�?кий лари",
		i: "ge",
		c: "GEL"
	}, {
		n: "Гана �?еди",
		i: "gh",
		c: "GHS"
	}, {
		n: "Гватемаль�?ка�? кет�?аль",
		i: "gt",
		c: "GTQ"
	}, {
		n: "Гвиней�?кий франк",
		i: "gn",
		c: "GNF"
	}, {
		n: "Гайан�?кий доллар",
		i: "gy",
		c: "GYD"
	}, {
		n: "гаит�?н�?кий гурд",
		i: "ht",
		c: "HTG"
	}, {
		n: "Honduran Лемпира",
		i: "hn",
		c: "HNL"
	}, {
		n: "Гонконг�?кий доллар",
		i: "hk",
		c: "HKD"
	}, {
		n: "Венгер�?кий форинт",
		i: "hu",
		c: "HUF"
	}, {
		n: "И�?ланд�?ка�? крона",
		i: "is",
		c: "ISK"
	}, {
		n: "Инди�? рупи�?",
		i: "in",
		c: "INR"
	}, {
		n: "индонезий�?ка�? рупи�?",
		i: "id",
		c: "IDR"
	}, {
		n: "Иран�?кий риал",
		i: "ir",
		c: "IRR"
	}, {
		n: "Ирак�?кий динар",
		i: "iq",
		c: "IQD"
	}, {
		n: "Израиль�?кий новый шекель",
		i: "il",
		c: "ILS"
	}, {
		n: "Ямай�?кий доллар",
		i: "jm",
		c: "JMD"
	}, {
		n: "Япон�?ка�? иена",
		i: "jp",
		c: "JPY"
	}, {
		n: "Джер�?и фунт",
		i: "je",
		c: "JEP"
	}, {
		n: "иордан�?кий динар",
		i: "jo",
		c: "JOD"
	}, {
		n: "казах�?тан�?кий тенге",
		i: "kz",
		c: "KZT"
	}, {
		n: "Кений�?кий шиллинг",
		i: "ke",
		c: "KES"
	}, {
		n: "Кувейт�?кий динар",
		i: "kw",
		c: "KWD"
	}, {
		n: "киргиз�?кий �?ом",
		i: "kg",
		c: "KGS"
	}, {
		n: "Ливан�?кий фунт",
		i: "lb",
		c: "LBP"
	}, {
		n: "Лата",
		i: "lv",
		c: "LVL"
	}, {
		n: "Ле�?ото Лоти",
		i: "ls",
		c: "LSL"
	}, {
		n: "Либерий�?кий доллар",
		i: "lr",
		c: "LRD"
	}, {
		n: "Ливий�?кий динар",
		i: "ly",
		c: "LYD"
	}, {
		n: "Литов�?кий лит",
		i: "lt",
		c: "LTL"
	}, {
		n: "македон�?кий динар",
		i: "mk",
		c: "MKD"
	}, {
		n: "малавийцем квача",
		i: "mw",
		c: "MWK"
	}, {
		n: "Малайзий�?кий ринггит",
		i: "my",
		c: "MYR"
	}, {
		n: "мальдив�?кий Руфи�?",
		i: "mv",
		c: "MVR"
	}, {
		n: "мавритан�?ка�? уги�?",
		i: "mr",
		c: "MRO"
	}, {
		n: "Маврикий�?ка�? рупи�?",
		i: "mu",
		c: "MUR"
	}, {
		n: "Mexicon пе�?о",
		i: "mx",
		c: "MXN"
	}, {
		n: "Молдав�?кий Лей",
		i: "md",
		c: "MDL"
	}, {
		n: "монголь�?кий тугрик",
		i: "mn",
		c: "MNT"
	}, {
		n: "Мароккан�?кий дирхам",
		i: "ma",
		c: "MAD"
	}, {
		n: "Мозамбик�?ка�? Метикал",
		i: "mz",
		c: "MZN"
	}, {
		n: "�?амибий�?кий доллар",
		i: "na",
		c: "NAD"
	}, {
		n: "�?епаль�?ка�? рупи�?",
		i: "np",
		c: "NPR"
	}, {
		n: "�?овый тайвань�?кий доллар",
		i: "tw",
		c: "TWD"
	}, {
		n: "�?овозеланд�?кий доллар",
		i: "nz",
		c: "NZD"
	}, {
		n: "�?икарагуан�?кий Кордоба",
		i: "ni",
		c: "NIO"
	}, {
		n: "�?игерий�?кий �?аира",
		i: "ng",
		c: "NGN"
	}, {
		n: "Северна�? Коре�? выиграла",
		i: "kp",
		c: "KPW"
	}, {
		n: "�?орвеж�?ка�? крона",
		i: "no",
		c: "NOK"
	}, {
		n: "Оман�?кий риал",
		i: "om",
		c: "OMR"
	}, {
		n: "Паки�?тан�?ка�? рупи�?",
		i: "pk",
		c: "PKR"
	}, {
		n: "Панам�?кий Бальбоа",
		i: "pa",
		c: "PAB"
	}, {
		n: "Папуа-�?ова�? Гвине�? кина",
		i: "pg",
		c: "PGK"
	}, {
		n: "Парагвай�?кий Гуарани",
		i: "py",
		c: "PYG"
	}, {
		n: "Перуан�?кий новый �?оль",
		i: "pe",
		c: "PEN"
	}, {
		n: "Филиппин�?кий пе�?о",
		i: "ph",
		c: "PHP"
	}, {
		n: "Поль�?кий злотый",
		i: "pl",
		c: "PLN"
	}, {
		n: "Катар�?кий риал",
		i: "qa",
		c: "QAR"
	}, {
		n: "Румын�?кий Лей",
		i: "ro",
		c: "RON"
	}, {
		n: "Ру�?�?кий рубль",
		i: "ru",
		c: "RUB"
	}, {
		n: "Франк Руанды",
		i: "rw",
		c: "RWF"
	}, {
		n: "Св�?та�? Елена фунт",
		i: "sh",
		c: "SHP"
	}, {
		n: "Сальвадор�?кий колон",
		i: "sv",
		c: "SVC"
	}, {
		n: "Самоан�?кие тала",
		i: "ws",
		c: "WST"
	}, {
		n: "Сан-Томе и Прин�?ипи Добра",
		i: "st",
		c: "STD"
	}, {
		n: "Саудов�?кий риал",
		i: "sa",
		c: "SAR"
	}, {
		n: "�?ерб�?кий динар",
		i: "rs",
		c: "RSD"
	}, {
		n: "Сейшель�?ка�? рупи�?",
		i: "sc",
		c: "SCR"
	}, {
		n: "ьерра-Леоне леоне",
		i: "sl",
		c: "SLL"
	}, {
		n: "Сингапур�?кий доллар",
		i: "sg",
		c: "SGD"
	}, {
		n: "доллар Соломоновых о�?тровов",
		i: "sb",
		c: "SBD"
	}, {
		n: "Сомалий�?кий шиллинг",
		i: "so",
		c: "SOS"
	}, {
		n: "Южноафрикан�?кий ранд",
		i: "za",
		c: "ZAR"
	}, {
		n: "Южна�? Коре�? выиграла",
		i: "kr",
		c: "KRW"
	}, {
		n: "Южный Судан�?кий фунт",
		i: "ss",
		c: "SSP"
	}, {
		n: "рупи�? Шри-Ланки",
		i: "lk",
		c: "LKR"
	}, {
		n: "Судан",
		i: "sd",
		c: "SDG"
	}, {
		n: "Суринам�?кий доллар",
		i: "sr",
		c: "SRD"
	}, {
		n: "�?вази Лилангени",
		i: "sz",
		c: "SZL"
	}, {
		n: "Швед�?ка�? крона",
		i: "se",
		c: "SEK"
	}, {
		n: "Швейцари�?",
		i: "ch",
		c: "CHF"
	}, {
		n: "�?ирий�?кий фунт",
		i: "sy",
		c: "SYP"
	}, {
		n: "таджик�?кий �?омони",
		i: "tj",
		c: "TJS"
	}, {
		n: "Танзаний�?кий шиллинг",
		i: "tz",
		c: "TZS"
	}, {
		n: "Тай�?кий бат",
		i: "th",
		c: "THB"
	}, {
		n: "Тонган�?ка�? паанга",
		i: "to",
		c: "TOP"
	}, {
		n: "Тринидад и Тобаго доллар",
		i: "tt",
		c: "TTD"
	}, {
		n: "туни�?�?кий динар",
		i: "tn",
		c: "TND"
	}, {
		n: "турецка�? лира",
		i: "tr",
		c: "TRY"
	}, {
		n: "Туркмени�?тан манатов",
		i: "tm",
		c: "TMT"
	}, {
		n: "угандий�?ко шиллинг",
		i: "ug",
		c: "UGX"
	}, {
		n: "Украин�?ка�? гривна",
		i: "ua",
		c: "UAH"
	}, {
		n: "Объединенные �?раб�?кие Эмираты дирхам",
		i: "ae",
		c: "AED"
	}, {
		n: "доллар СШ�?",
		i: "us",
		c: "USD"
	}, {
		n: "уругвай�?кий пе�?о",
		i: "uy",
		c: "UYI"
	}, {
		n: "узбек�?ка�? �?ома",
		i: "uz",
		c: "UZS"
	}, {
		n: "Вата",
		i: "vu",
		c: "VUV"
	}, {
		n: "Вене�?у�?ль�?кий боливар",
		i: "ve",
		c: "VEF"
	}, {
		n: "вьетнам�?кий đồng",
		i: "vn",
		c: "VND"
	}, {
		n: "Запад КФ�? франк",
		i: "bf",
		c: "XOF"
	}, {
		n: "Йемен�?кий риал",
		i: "ye",
		c: "YER"
	}, {
		n: "замбий�?ка�? квача",
		i: "zm",
		c: "ZMW"
	}, {
		n: "зимбабвий�?ких долларов",
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
	
