/*
 * 
 * SCD - Smart Currency Detector | Version 3.6 | July 29, 2017
 * A jQuery plugin that converts prices by autodetecting client's currency
 * 
 * Developed by GaJeLabs.
 * 
 * This plugin contains source code from Caleb Jacob's Tooltipster plugin which is licensed under the MIT License.
 * 
 * 
 */


// Enable debug messages in console
//var SCD_DEBUG = true;

(function ($, window, document, undefined) {

    var pluginName = "tooltipster",
            defaults = {
                animation: 'fade',
                arrow: true,
                arrowColor: '',
                content: '',
                delay: 200,
                fixedWidth: 0,
                maxWidth: 0,
                functionInit: function (origin, content) {},
                functionBefore: function (origin, continueTooltip) {
                    continueTooltip();
                },
                functionReady: function (origin, tooltip) {},
                functionAfter: function (origin) {},
                icon: '(?)',
                iconDesktop: false,
                iconTouch: false,
                iconTheme: '.tooltipster-icon',
                interactive: false,
                interactiveTolerance: 350,
                interactiveAutoClose: true,
                offsetX: 0,
                offsetY: 0,
                onlyOne: true,
                position: 'top',
                speed: 350,
                timer: 0,
                theme: '.tooltipster-default',
                touchDevices: true,
                trigger: 'hover',
                updateAnimation: true
            };

    function Plugin(element, options) {
        this.element = element;

        this.options = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    // we'll use this to detect for mobile devices
    function is_touch_device() {
        return !!('ontouchstart' in window);
    }

    // detecting support for CSS transitions
    function supportsTransitions() {
        var b = document.body || document.documentElement;
        var s = b.style;
        var p = 'transition';
        if (typeof s[p] == 'string') {
            return true;
        }

        v = ['Moz', 'Webkit', 'Khtml', 'O', 'ms'],
                p = p.charAt(0).toUpperCase() + p.substr(1);
        for (var i = 0; i < v.length; i++) {
            if (typeof s[v[i] + p] == 'string') {
                return true;
            }
        }
        return false;
    }
    var transitionSupport = true;
    if (!supportsTransitions()) {
        transitionSupport = false;
    }

    // detect if this device is mouse driven over purely touch
    var touchDevice = is_touch_device();

    // on mousemove, double confirm that this is a desktop - not a touch device
    jQuery(window).on('mousemove.tooltipster', function () {
        touchDevice = false;
        jQuery(window).off('mousemove.tooltipster');
    });

    Plugin.prototype = {

        init: function () {
            var $this = jQuery(this.element);
            var object = this;
            var run = true;

            // if this is a touch device and touch devices are disabled, disable the plugin
            if (!object.options.touchDevices && touchDevice) {
                run = false;
            }

            // if IE7 or lower, disable the plugin
            if (document.all && !document.querySelector) {
                run = false;
            }

            if (run) {
                // first, strip the title off of the element and set it as a data attribute to prevent the default tooltips from popping up
                var tooltipsterContent = $.trim(object.options.content).length > 0 ? object.options.content : $this.attr('title');

                var c = object.options.functionInit($this, tooltipsterContent);
                if (c)
                    tooltipsterContent = c;

                $this.data('tooltipsterContent', tooltipsterContent);
                $this.removeAttr('title');

                // detect if we're changing the tooltip origin to an icon
                if ((object.options.iconDesktop) && (!touchDevice) || ((object.options.iconTouch) && (touchDevice))) {
                    var theme = object.options.iconTheme;
                    var icon = jQuery('<span class="' + theme.replace('.', '') + '"></span>');
                    icon
                            .data('tooltipsterContent', tooltipsterContent)
                            .append(object.options.icon)
                            .insertAfter($this);
                    $this.data('tooltipsterIcon', icon);
                    $this = icon;
                }

                // if this is a touch device, add some touch events to launch the tooltip
                if ((object.options.touchDevices) && (touchDevice) && ((object.options.trigger == 'click') || (object.options.trigger == 'hover'))) {
                    $this.on('touchstart.tooltipster', function (element, options) {
                        object.showTooltip();
                    });
                }

                // if this is a desktop, deal with adding regular mouse events
                else {

                    // if hover events are set to show and hide the tooltip, attach those events respectively
                    if (object.options.trigger == 'hover') {
                        $this.on('mouseenter.tooltipster', function () {
                            object.showTooltip();
                        });

                        // if this is an interactive tooltip, delay getting rid of the tooltip right away so you have a chance to hover on the tooltip
                        if (object.options.interactive) {
                            $this.on('mouseleave.tooltipster', function () {
                                var tooltipster = $this.data('tooltipster');
                                var keepAlive = false;

                                if ((tooltipster !== undefined) && (tooltipster !== '')) {
                                    tooltipster.mouseenter(function () {
                                        keepAlive = true;
                                    });
                                    tooltipster.mouseleave(function () {
                                        keepAlive = false;
                                    });

                                    var tolerance = setTimeout(function () {

                                        if (keepAlive) {
                                            if (object.options.interactiveAutoClose) {
                                                tooltipster.find('select').on('change', function () {
                                                    object.hideTooltip();
                                                });

                                                tooltipster.mouseleave(function (e) {
                                                    var $target = jQuery(e.target);

                                                    if ($target.parents('.tooltipster-base').length === 0 || $target.hasClass('tooltipster-base')) {
                                                        object.hideTooltip();
                                                    } else {
                                                        $target.on('mouseleave', function (e) {
                                                            object.hideTooltip();
                                                        });
                                                    }
                                                });
                                            }
                                        } else {
                                            object.hideTooltip();
                                        }
                                    }, object.options.interactiveTolerance);
                                } else {
                                    object.hideTooltip();
                                }
                            });
                        }

                        // if this is a dumb tooltip, just get rid of it on mouseleave
                        else {
                            $this.on('mouseleave.tooltipster', function () {
                                object.hideTooltip();
                            });
                        }
                    }

                    // if click events are set to show and hide the tooltip, attach those events respectively
                    if (object.options.trigger == 'click') {
                        $this.on('click.tooltipster', function () {
                            if (($this.data('tooltipster') === '') || ($this.data('tooltipster') === undefined)) {
                                object.showTooltip();
                            } else {
                                object.hideTooltip();
                            }
                        });
                    }
                }
            }
        },

        showTooltip: function (options) {
            var $this = jQuery(this.element);
            var object = this;

            // detect if we're actually dealing with an icon or the origin itself
            if ($this.data('tooltipsterIcon') !== undefined) {
                $this = $this.data('tooltipsterIcon');
            }

            // continue if this tooltip is enabled
            if (!$this.hasClass('tooltipster-disable')) {

                // if we only want one tooltip open at a time, close all tooltips currently open
                if ((jQuery('.tooltipster-base').not('.tooltipster-dying').length > 0) && (object.options.onlyOne)) {
                    jQuery('.tooltipster-base').not('.tooltipster-dying').not($this.data('tooltipster')).each(function () {
                        jQuery(this).addClass('tooltipster-kill');
                        var origin = jQuery(this).data('origin');
                        origin.data('plugin_tooltipster').hideTooltip();
                    });
                }

                // delay the showing of the tooltip according to the delay time
                $this.clearQueue().delay(object.options.delay).queue(function () {

                    // call our custom function before continuing
                    object.options.functionBefore($this, function () {

                        // if this origin already has its tooltip open, keep it open and do nothing else
                        if (($this.data('tooltipster') !== undefined) && ($this.data('tooltipster') !== '')) {
                            var tooltipster = $this.data('tooltipster');

                            if (!tooltipster.hasClass('tooltipster-kill')) {

                                var animation = 'tooltipster-' + object.options.animation;

                                tooltipster.removeClass('tooltipster-dying');

                                if (transitionSupport) {
                                    tooltipster.clearQueue().addClass(animation + '-show');
                                }

                                // if we have a timer set, we need to reset it
                                if (object.options.timer > 0) {
                                    var timer = tooltipster.data('tooltipsterTimer');
                                    clearTimeout(timer);

                                    timer = setTimeout(function () {
                                        tooltipster.data('tooltipsterTimer', undefined);
                                        object.hideTooltip();
                                    }, object.options.timer);

                                    tooltipster.data('tooltipsterTimer', timer);
                                }

                                // if this is a touch device, hide the tooltip on body touch
                                if ((object.options.touchDevices) && (touchDevice)) {
                                    jQuery('body').bind('touchstart', function (event) {
                                        if (object.options.interactive) {
                                            var touchTarget = jQuery(event.target);
                                            var closeTooltip = true;

                                            touchTarget.parents().each(function () {
                                                if (jQuery(this).hasClass('tooltipster-base')) {
                                                    closeTooltip = false;
                                                }
                                            });

                                            if (closeTooltip) {
                                                object.hideTooltip();
                                                jQuery('body').unbind('touchstart');
                                            }
                                        } else {
                                            object.hideTooltip();
                                            jQuery('body').unbind('touchstart');
                                        }
                                    });
                                }
                            }
                        }

                        // if the tooltip isn't already open, open that sucker up!
                        else {
                            // disable horizontal scrollbar to keep overflowing tooltips from jacking with it and then restore it to it's previous value
                            object.options._bodyOverflowX = jQuery('body').css('overflow-x');
                            jQuery('body').css('overflow-x', 'hidden');

                            // get the content for the tooltip
                            var content = object.getContent($this);

                            // get some other settings related to building the tooltip
                            var theme = object.options.theme;
                            var themeClass = theme.replace('.', '');
                            var animation = 'tooltipster-' + object.options.animation;
                            var animationSpeed = '-webkit-transition-duration: ' + object.options.speed + 'ms; -webkit-animation-duration: ' + object.options.speed + 'ms; -moz-transition-duration: ' + object.options.speed + 'ms; -moz-animation-duration: ' + object.options.speed + 'ms; -o-transition-duration: ' + object.options.speed + 'ms; -o-animation-duration: ' + object.options.speed + 'ms; -ms-transition-duration: ' + object.options.speed + 'ms; -ms-animation-duration: ' + object.options.speed + 'ms; transition-duration: ' + object.options.speed + 'ms; animation-duration: ' + object.options.speed + 'ms;';
                            var fixedWidth = object.options.fixedWidth > 0 ? 'width:' + Math.round(object.options.fixedWidth) + 'px;' : '';
                            var maxWidth = object.options.maxWidth > 0 ? 'max-width:' + Math.round(object.options.maxWidth) + 'px;' : '';
                            var pointerEvents = object.options.interactive ? 'pointer-events: auto;' : '';

                            // build the base of our tooltip
                            var tooltipster = jQuery('<div class="tooltipster-base ' + themeClass + ' ' + animation + '" style="' + fixedWidth + ' ' + maxWidth + ' ' + pointerEvents + ' ' + animationSpeed + '"></div>');
                            var tooltipsterHTML = jQuery('<div class="tooltipster-content"></div>');
                            tooltipsterHTML.html(content);
                            tooltipster.append(tooltipsterHTML);


                            tooltipster.appendTo('body');

                            // attach the tooltip to its origin
                            $this.data('tooltipster', tooltipster);
                            tooltipster.data('origin', $this);

                            // do all the crazy calculations and positioning
                            object.positionTooltip();

                            // call our custom callback since the content of the tooltip is now part of the DOM
                            object.options.functionReady($this, tooltipster);

                            // animate in the tooltip
                            if (transitionSupport) {
                                tooltipster.addClass(animation + '-show');
                            } else {
                                tooltipster.css('display', 'none').removeClass(animation).fadeIn(object.options.speed);
                            }

                            // check to see if our tooltip content changes or its origin is removed while the tooltip is alive
                            var currentTooltipContent = content;
                            var contentUpdateChecker = setInterval(function () {
                                var newTooltipContent = object.getContent($this);

                                // if this tooltip's origin is removed, remove the tooltip
                                if (jQuery('body').find($this).length === 0) {
                                    tooltipster.addClass('tooltipster-dying');
                                    object.hideTooltip();
                                }

                                // if the content changed for the tooltip, update it                                            
                                else if ((currentTooltipContent !== newTooltipContent) && (newTooltipContent !== '')) {
                                    currentTooltipContent = newTooltipContent;

                                    // set the new content in the tooltip
                                    tooltipster.find('.tooltipster-content').html(newTooltipContent);

                                    // if we want to play a little animation showing the content changed
                                    if (object.options.updateAnimation) {
                                        if (supportsTransitions()) {
                                            tooltipster.css({
                                                'width': '',
                                                '-webkit-transition': 'all ' + object.options.speed + 'ms, width 0ms, height 0ms, left 0ms, top 0ms',
                                                '-moz-transition': 'all ' + object.options.speed + 'ms, width 0ms, height 0ms, left 0ms, top 0ms',
                                                '-o-transition': 'all ' + object.options.speed + 'ms, width 0ms, height 0ms, left 0ms, top 0ms',
                                                '-ms-transition': 'all ' + object.options.speed + 'ms, width 0ms, height 0ms, left 0ms, top 0ms',
                                                'transition': 'all ' + object.options.speed + 'ms, width 0ms, height 0ms, left 0ms, top 0ms'
                                            }).addClass('tooltipster-content-changing');

                                            // reset the CSS transitions and finish the change animation
                                            setTimeout(function () {
                                                tooltipster.removeClass('tooltipster-content-changing');
                                                // after the changing animation has completed, reset the CSS transitions
                                                setTimeout(function () {
                                                    tooltipster.css({
                                                        '-webkit-transition': object.options.speed + 'ms',
                                                        '-moz-transition': object.options.speed + 'ms',
                                                        '-o-transition': object.options.speed + 'ms',
                                                        '-ms-transition': object.options.speed + 'ms',
                                                        'transition': object.options.speed + 'ms'
                                                    });
                                                }, object.options.speed);
                                            }, object.options.speed);
                                        } else {
                                            tooltipster.fadeTo(object.options.speed, 0.5, function () {
                                                tooltipster.fadeTo(object.options.speed, 1);
                                            });
                                        }
                                    }

                                    // reposition and resize the tooltip
                                    object.positionTooltip();
                                }

                                // if the tooltip is closed or origin is removed, clear this interval
                                if ((jQuery('body').find(tooltipster).length === 0) || (jQuery('body').find($this).length === 0)) {
                                    clearInterval(contentUpdateChecker);
                                }
                            }, 200);

                            // if we have a timer set, let the countdown begin!
                            if (object.options.timer > 0) {
                                var timer = setTimeout(function () {
                                    tooltipster.data('tooltipsterTimer', undefined);
                                    object.hideTooltip();
                                }, object.options.timer + object.options.speed);

                                tooltipster.data('tooltipsterTimer', timer);
                            }

                            // if this is a touch device, hide the tooltip on body touch
                            if ((object.options.touchDevices) && (touchDevice)) {
                                jQuery('body').bind('touchstart', function (event) {
                                    if (object.options.interactive) {

                                        var touchTarget = jQuery(event.target);
                                        var closeTooltip = true;

                                        touchTarget.parents().each(function () {
                                            if (jQuery(this).hasClass('tooltipster-base')) {
                                                closeTooltip = false;
                                            }
                                        });

                                        if (closeTooltip) {
                                            object.hideTooltip();
                                            jQuery('body').unbind('touchstart');
                                        }
                                    } else {
                                        object.hideTooltip();
                                        jQuery('body').unbind('touchstart');
                                    }
                                });
                            }
                        }
                    });

                    $this.dequeue();
                });
            }
        },

        hideTooltip: function (options) {

            var $this = jQuery(this.element);
            var object = this;

            // detect if we're actually dealing with an icon or the origin itself
            if ($this.data('tooltipsterIcon') !== undefined) {
                $this = $this.data('tooltipsterIcon');
            }

            var tooltipster = $this.data('tooltipster');

            // if the origin has been removed, find all tooltips assigned to death
            if (tooltipster === undefined) {
                tooltipster = jQuery('.tooltipster-dying');
            }

            // clear any possible queues handling delays and such
            $this.clearQueue();

            if ((tooltipster !== undefined) && (tooltipster !== '')) {

                // detect if we need to clear a timer
                var timer = tooltipster.data('tooltipsterTimer');
                if (timer !== undefined) {
                    clearTimeout(timer);
                }

                var animation = 'tooltipster-' + object.options.animation;

                if (transitionSupport) {
                    tooltipster.clearQueue().removeClass(animation + '-show').addClass('tooltipster-dying').delay(object.options.speed).queue(function () {
                        tooltipster.remove();
                        $this.data('tooltipster', '');
                        jQuery('body').css('overflow-x', object.options._bodyOverflowX);

                        // finally, call our custom callback function
                        object.options.functionAfter($this);
                    });
                } else {
                    tooltipster.clearQueue().addClass('tooltipster-dying').fadeOut(object.options.speed, function () {
                        tooltipster.remove();
                        $this.data('tooltipster', '');
                        jQuery('body').css('overflow-x', object.options._bodyOverflowX);

                        // finally, call our custom callback function
                        object.options.functionAfter($this);
                    });
                }
            }
        },

        positionTooltip: function (options) {

            var $this = jQuery(this.element);
            var object = this;

            // detect if we're actually dealing with an icon or the origin itself
            if ($this.data('tooltipsterIcon') !== undefined) {
                $this = $this.data('tooltipsterIcon');
            }

            if (($this.data('tooltipster') !== undefined) && ($this.data('tooltipster') !== '')) {

                // find tooltipster and reset its width
                var tooltipster = $this.data('tooltipster');
                tooltipster.css('width', '');

                // find variables to determine placement
                var windowWidth = jQuery(window).width();
                var containerWidth = $this.outerWidth(false);
                var containerHeight = $this.outerHeight(false);
                var tooltipWidth = tooltipster.outerWidth(false);
                var tooltipInnerWidth = tooltipster.innerWidth() + 1; // this +1 stops FireFox from sometimes forcing an additional text line
                var tooltipHeight = tooltipster.outerHeight(false);
                var offset = $this.offset();
                var offsetTop = offset.top;
                var offsetLeft = offset.left;
                var resetPosition = undefined;

                // if this is an <area> tag inside a <map>, all hell breaks loose. Recaclulate all the measurements based on coordinates
                if ($this.is('area')) {
                    var areaShape = $this.attr('shape');
                    var mapName = $this.parent().attr('name');
                    var map = jQuery('img[usemap="#' + mapName + '"]');
                    var mapOffsetLeft = map.offset().left;
                    var mapOffsetTop = map.offset().top;
                    var areaMeasurements = $this.attr('coords') !== undefined ? $this.attr('coords').split(',') : undefined;

                    if (areaShape == 'circle') {
                        var areaLeft = parseInt(areaMeasurements[0]);
                        var areaTop = parseInt(areaMeasurements[1]);
                        var areaWidth = parseInt(areaMeasurements[2]);
                        containerHeight = areaWidth * 2;
                        containerWidth = areaWidth * 2;
                        offsetTop = mapOffsetTop + areaTop - areaWidth;
                        offsetLeft = mapOffsetLeft + areaLeft - areaWidth;
                    } else if (areaShape == 'rect') {
                        var areaLeft = parseInt(areaMeasurements[0]);
                        var areaTop = parseInt(areaMeasurements[1]);
                        var areaRight = parseInt(areaMeasurements[2]);
                        var areaBottom = parseInt(areaMeasurements[3]);
                        containerHeight = areaBottom - areaTop;
                        containerWidth = areaRight - areaLeft;
                        offsetTop = mapOffsetTop + areaTop;
                        offsetLeft = mapOffsetLeft + areaLeft;
                    } else if (areaShape == 'poly') {
                        var areaXs = [];
                        var areaYs = [];
                        var areaSmallestX = 0,
                                areaSmallestY = 0,
                                areaGreatestX = 0,
                                areaGreatestY = 0;
                        var arrayAlternate = 'even';

                        for (i = 0; i < areaMeasurements.length; i++) {
                            var areaNumber = parseInt(areaMeasurements[i]);

                            if (arrayAlternate == 'even') {
                                if (areaNumber > areaGreatestX) {
                                    areaGreatestX = areaNumber;
                                    if (i === 0) {
                                        areaSmallestX = areaGreatestX;
                                    }
                                }

                                if (areaNumber < areaSmallestX) {
                                    areaSmallestX = areaNumber;
                                }

                                arrayAlternate = 'odd';
                            } else {
                                if (areaNumber > areaGreatestY) {
                                    areaGreatestY = areaNumber;
                                    if (i == 1) {
                                        areaSmallestY = areaGreatestY;
                                    }
                                }

                                if (areaNumber < areaSmallestY) {
                                    areaSmallestY = areaNumber;
                                }

                                arrayAlternate = 'even';
                            }
                        }

                        containerHeight = areaGreatestY - areaSmallestY;
                        containerWidth = areaGreatestX - areaSmallestX;
                        offsetTop = mapOffsetTop + areaSmallestY;
                        offsetLeft = mapOffsetLeft + areaSmallestX;
                    } else {
                        containerHeight = map.outerHeight(false);
                        containerWidth = map.outerWidth(false);
                        offsetTop = mapOffsetTop;
                        offsetLeft = mapOffsetLeft;
                    }
                }

                // hardcoding the width and removing the padding fixed an issue with the tooltip width collapsing when the window size is small
                if (object.options.fixedWidth === 0) {
                    tooltipster.css({
                        'width': Math.round(tooltipInnerWidth) + 'px',
                        'padding-left': '0px',
                        'padding-right': '0px'
                    });
                }

                // our function and global vars for positioning our tooltip
                var myLeft = 0,
                        myLeftMirror = 0,
                        myTop = 0;
                var offsetY = parseInt(object.options.offsetY);
                var offsetX = parseInt(object.options.offsetX);
                var arrowConstruct = '';

                // A function to detect if the tooltip is going off the screen horizontally. If so, reposition the crap out of it!
                function dontGoOffScreenX() {

                    var windowLeft = jQuery(window).scrollLeft();

                    // If the tooltip goes off the left side of the screen, line it up with the left side of the window
                    if ((myLeft - windowLeft) < 0) {
                        var arrowReposition = myLeft - windowLeft;
                        myLeft = windowLeft;

                        tooltipster.data('arrow-reposition', arrowReposition);
                    }

                    // If the tooltip goes off the right of the screen, line it up with the right side of the window
                    if (((myLeft + tooltipWidth) - windowLeft) > windowWidth) {
                        var arrowReposition = myLeft - ((windowWidth + windowLeft) - tooltipWidth);
                        myLeft = (windowWidth + windowLeft) - tooltipWidth;

                        tooltipster.data('arrow-reposition', arrowReposition);
                    }
                }

                // A function to detect if the tooltip is going off the screen vertically. If so, switch to the opposite!
                function dontGoOffScreenY(switchTo, resetTo) {
                    // if it goes off the top off the page
                    if (((offsetTop - jQuery(window).scrollTop() - tooltipHeight - offsetY - 12) < 0) && (resetTo.indexOf('top') > -1)) {
                        object.options.position = switchTo;
                        resetPosition = resetTo;
                    }

                    // if it goes off the bottom of the page
                    if (((offsetTop + containerHeight + tooltipHeight + 12 + offsetY) > (jQuery(window).scrollTop() + jQuery(window).height())) && (resetTo.indexOf('bottom') > -1)) {
                        object.options.position = switchTo;
                        resetPosition = resetTo;
                        myTop = (offsetTop - tooltipHeight) - offsetY - 12;
                    }
                }

                if (object.options.position == 'top') {
                    var leftDifference = (offsetLeft + tooltipWidth) - (offsetLeft + containerWidth);
                    myLeft = (offsetLeft + offsetX) - (leftDifference / 2);
                    myTop = (offsetTop - tooltipHeight) - offsetY - 12;
                    dontGoOffScreenX();
                    dontGoOffScreenY('bottom', 'top');
                }

                if (object.options.position == 'top-left') {
                    myLeft = offsetLeft + offsetX;
                    myTop = (offsetTop - tooltipHeight) - offsetY - 12;
                    dontGoOffScreenX();
                    dontGoOffScreenY('bottom-left', 'top-left');
                }

                if (object.options.position == 'top-right') {
                    myLeft = (offsetLeft + containerWidth + offsetX) - tooltipWidth;
                    myTop = (offsetTop - tooltipHeight) - offsetY - 12;
                    dontGoOffScreenX();
                    dontGoOffScreenY('bottom-right', 'top-right');
                }

                if (object.options.position == 'bottom') {
                    var leftDifference = (offsetLeft + tooltipWidth) - (offsetLeft + containerWidth);
                    myLeft = offsetLeft - (leftDifference / 2) + offsetX;
                    myTop = (offsetTop + containerHeight) + offsetY + 12;
                    dontGoOffScreenX();
                    dontGoOffScreenY('top', 'bottom');
                }

                if (object.options.position == 'bottom-left') {
                    myLeft = offsetLeft + offsetX;
                    myTop = (offsetTop + containerHeight) + offsetY + 12;
                    dontGoOffScreenX();
                    dontGoOffScreenY('top-left', 'bottom-left');
                }

                if (object.options.position == 'bottom-right') {
                    myLeft = (offsetLeft + containerWidth + offsetX) - tooltipWidth;
                    myTop = (offsetTop + containerHeight) + offsetY + 12;
                    dontGoOffScreenX();
                    dontGoOffScreenY('top-right', 'bottom-right');
                }

                if (object.options.position == 'left') {
                    myLeft = offsetLeft - offsetX - tooltipWidth - 12;
                    myLeftMirror = offsetLeft + offsetX + containerWidth + 12;
                    var topDifference = (offsetTop + tooltipHeight) - (offsetTop + $this.outerHeight(false));
                    myTop = offsetTop - (topDifference / 2) - offsetY;

                    // If the tooltip goes off boths sides of the page
                    if ((myLeft < 0) && ((myLeftMirror + tooltipWidth) > windowWidth)) {
                        var borderWidth = parseFloat(tooltipster.css('border-width')) * 2;
                        var newWidth = (tooltipWidth + myLeft) - borderWidth;
                        tooltipster.css('width', newWidth + 'px');

                        tooltipHeight = tooltipster.outerHeight(false);
                        myLeft = offsetLeft - offsetX - newWidth - 12 - borderWidth;
                        topDifference = (offsetTop + tooltipHeight) - (offsetTop + $this.outerHeight(false));
                        myTop = offsetTop - (topDifference / 2) - offsetY;
                    }

                    // If it only goes off one side, flip it to the other side
                    else if (myLeft < 0) {
                        myLeft = offsetLeft + offsetX + containerWidth + 12;
                        tooltipster.data('arrow-reposition', 'left');
                    }
                }

                if (object.options.position == 'right') {
                    myLeft = offsetLeft + offsetX + containerWidth + 12;
                    myLeftMirror = offsetLeft - offsetX - tooltipWidth - 12;
                    var topDifference = (offsetTop + tooltipHeight) - (offsetTop + $this.outerHeight(false));
                    myTop = offsetTop - (topDifference / 2) - offsetY;

                    // If the tooltip goes off boths sides of the page
                    if (((myLeft + tooltipWidth) > windowWidth) && (myLeftMirror < 0)) {
                        var borderWidth = parseFloat(tooltipster.css('border-width')) * 2;
                        var newWidth = (windowWidth - myLeft) - borderWidth;
                        tooltipster.css('width', newWidth + 'px');

                        tooltipHeight = tooltipster.outerHeight(false);
                        topDifference = (offsetTop + tooltipHeight) - (offsetTop + $this.outerHeight(false));
                        myTop = offsetTop - (topDifference / 2) - offsetY;

                    }

                    // If it only goes off one side, flip it to the other side
                    else if ((myLeft + tooltipWidth) > windowWidth) {
                        myLeft = offsetLeft - offsetX - tooltipWidth - 12;
                        tooltipster.data('arrow-reposition', 'right');
                    }
                }

                // if arrow is set true, style it and append it
                if (object.options.arrow) {

                    var arrowClass = 'tooltipster-arrow-' + object.options.position;

                    // set color of the arrow
                    if (object.options.arrowColor.length < 1) {
                        var arrowColor = tooltipster.css('background-color');
                    } else {
                        var arrowColor = object.options.arrowColor;
                    }

                    // if the tooltip was going off the page and had to re-adjust, we need to update the arrow's position
                    var arrowReposition = tooltipster.data('arrow-reposition');
                    if (!arrowReposition) {
                        arrowReposition = '';
                    } else if (arrowReposition == 'left') {
                        arrowClass = 'tooltipster-arrow-right';
                        arrowReposition = '';
                    } else if (arrowReposition == 'right') {
                        arrowClass = 'tooltipster-arrow-left';
                        arrowReposition = '';
                    } else {
                        arrowReposition = 'left:' + Math.round(arrowReposition) + 'px;';
                    }

                    // building the logic to create the border around the arrow of the tooltip
                    if ((object.options.position == 'top') || (object.options.position == 'top-left') || (object.options.position == 'top-right')) {
                        var tooltipBorderWidth = parseFloat(tooltipster.css('border-bottom-width'));
                        var tooltipBorderColor = tooltipster.css('border-bottom-color');
                    } else if ((object.options.position == 'bottom') || (object.options.position == 'bottom-left') || (object.options.position == 'bottom-right')) {
                        var tooltipBorderWidth = parseFloat(tooltipster.css('border-top-width'));
                        var tooltipBorderColor = tooltipster.css('border-top-color');
                    } else if (object.options.position == 'left') {
                        var tooltipBorderWidth = parseFloat(tooltipster.css('border-right-width'));
                        var tooltipBorderColor = tooltipster.css('border-right-color');
                    } else if (object.options.position == 'right') {
                        var tooltipBorderWidth = parseFloat(tooltipster.css('border-left-width'));
                        var tooltipBorderColor = tooltipster.css('border-left-color');
                    } else {
                        var tooltipBorderWidth = parseFloat(tooltipster.css('border-bottom-width'));
                        var tooltipBorderColor = tooltipster.css('border-bottom-color');
                    }

                    if (tooltipBorderWidth > 1) {
                        tooltipBorderWidth++;
                    }

                    var arrowBorder = '';
                    if (tooltipBorderWidth !== 0) {
                        var arrowBorderSize = '';
                        var arrowBorderColor = 'border-color: ' + tooltipBorderColor + ';';
                        if (arrowClass.indexOf('bottom') !== -1) {
                            arrowBorderSize = 'margin-top: -' + Math.round(tooltipBorderWidth) + 'px;';
                        } else if (arrowClass.indexOf('top') !== -1) {
                            arrowBorderSize = 'margin-bottom: -' + Math.round(tooltipBorderWidth) + 'px;';
                        } else if (arrowClass.indexOf('left') !== -1) {
                            arrowBorderSize = 'margin-right: -' + Math.round(tooltipBorderWidth) + 'px;';
                        } else if (arrowClass.indexOf('right') !== -1) {
                            arrowBorderSize = 'margin-left: -' + Math.round(tooltipBorderWidth) + 'px;';
                        }
                        arrowBorder = '<span class="tooltipster-arrow-border" style="' + arrowBorderSize + ' ' + arrowBorderColor + ';"></span>';
                    }

                    // if the arrow already exists, remove and replace it
                    tooltipster.find('.tooltipster-arrow').remove();

                    // build out the arrow and append it        
                    arrowConstruct = '<div class="' + arrowClass + ' tooltipster-arrow" style="' + arrowReposition + '">' + arrowBorder + '<span style="border-color:' + arrowColor + ';"></span></div>';
                    tooltipster.append(arrowConstruct);
                }

                // position the tooltip
                tooltipster.css({
                    'top': Math.round(myTop) + 'px',
                    'left': Math.round(myLeft) + 'px'
                });

                // if we had to change the position of the tooltip so it wouldn't go off screen, reset it
                if (resetPosition !== undefined) {
                    object.options.position = resetPosition;
                }
            }
        },
        getContent: function (element) {
            var content = element.data('tooltipsterContent');
            // will remove <script> tags to prevent XSS (execution of JS for dynamic tooltips)
            content = jQuery($.parseHTML('<div>' + content + '</div>')).html();

            return content;
        }
    };

    $.fn[pluginName] = function (options) {
        // change default options for all future instances, using $.fn.tooltipster('setDefaults', myOptions)
        if (options && options === 'setDefaults') {
            $.extend(defaults, arguments[1]);
        } else {
            // better API name spacing by glebtv
            if (typeof options === 'string') {
                var $t = this;
                var arg = arguments[1];
                var v = null;

                // if we're calling a container to interact with API's of tooltips inside it - select all those tooltip origins first
                if ($t.data('plugin_tooltipster') === undefined) {
                    var query = $t.find('*');
                    $t = jQuery();
                    query.each(function () {
                        if (jQuery(this).data('plugin_tooltipster') !== undefined) {
                            $t.push(jQuery(this));
                        }
                    });
                }

                $t.each(function () {
                    switch (options.toLowerCase()) {
                        case 'show':
                            jQuery(this).data('plugin_tooltipster').showTooltip();
                            break;

                        case 'hide':
                            jQuery(this).data('plugin_tooltipster').hideTooltip();
                            break;

                        case 'disable':
                            jQuery(this).addClass('tooltipster-disable');
                            break;

                        case 'enable':
                            jQuery(this).removeClass('tooltipster-disable');
                            break;

                        case 'destroy':

                            jQuery(this).data('plugin_tooltipster').hideTooltip();

                            var icon = jQuery(this).data('tooltipsterIcon');
                            if (icon)
                                icon.remove();

                            jQuery(this)
                                    .attr('title', $t.data('tooltipsterContent'))
                                    .removeData('plugin_tooltipster')
                                    .removeData('tooltipsterContent')
                                    .removeData('tooltipsterIcon')
                                    .off('.tooltipster');
                            break;

                        case 'elementicon':
                            v = jQuery(this).data('tooltipsterIcon');
                            // we will return the raw HTML element if there is an icon, undefined otherwise
                            v = v ? v[0] : undefined;
                            //return false to stop .each iteration on the first element matched by the selector. No need for a 'break;' after that.
                            return false;

                        case 'update':
                            var content = arg;

                            if (jQuery(this).data('tooltipsterIcon') === undefined) {
                                jQuery(this).data('tooltipsterContent', content);
                            } else {
                                var $this = jQuery(this).data('tooltipsterIcon');
                                $this.data('tooltipsterContent', content);
                            }

                            break;

                        case 'reposition':
                            jQuery(this).data('plugin_tooltipster').positionTooltip();
                            break;

                        case 'val':
                            v = jQuery(this).data('tooltipsterContent');
                            if (SCD_DEBUG)
                                console.log(v);
                            //return false : same as above
                            return false;
                    }
                });

                return (v !== null) ? v : this;
            } else {
                // attach a tooltipster object to each element if it doesn't already have one
                return this.each(function () {

                    if (!$.data(this, "plugin_" + pluginName)) {
                        $.data(this, "plugin_" + pluginName, new Plugin(this, options));
                    }
                });
            }
        }
    };

    // hide tooltips on orientation change
    if (touchDevice) {
        window.addEventListener("orientationchange", function () {
            if (jQuery('.tooltipster-base').length > 0) {
                jQuery('.tooltipster-base').each(function () {
                    var origin = jQuery(this).data('origin');
                    origin.data('plugin_tooltipster').hideTooltip();
                });
            }
        }, false);
    }

    // on scroll reposition - otherwise position:fixed element's tooltips will 'scroll-away'
    jQuery(window).on('scroll.tooltipster', function () {
        var origin = jQuery('.tooltipster-base').data('origin');

        if (origin) {
            origin.tooltipster('reposition');
        }
    });

    // on window resize, reposition and open tooltips
    jQuery(window).on('resize.tooltipster', function () {
        var origin = jQuery('.tooltipster-base').data('origin');

        if ((origin !== null) && (origin !== undefined)) {
            origin.tooltipster('reposition');
        }
    });
})(jQuery, window, document);


/* ==========================   jQuery Cookie ==================================================================== */

/*!
 * jQuery Cookie Plugin v1.4.0
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals.
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
        } catch (e) {
            return;
        }

        try {
            // If we can't parse the cookie, ignore it, it's unusable.
            return config.json ? JSON.parse(s) : s;
        } catch (e) {
        }
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function (key, value, options) {

        // Write
        if (value !== undefined && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {};

        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
        var cookies = document.cookie ? document.cookie.split('; ') : [];

        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = parts.join('=');

            if (key && key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) !== undefined) {
            // Must not alter options, thus extending a fresh object...
            $.cookie(key, '', $.extend({}, options, {expires: -1}));
            return true;
        }
        return false;
    };

}));




/* ==========================   SCD Currencies codes begin   =========================================== */
(function ($) {

    if (!String.format) {
        String.format = function (format) {
            var args = Array.prototype.slice.call(arguments, 1);
            return format.replace(/{(\d+)}/g, function (match, number) {
                return typeof args[number] != 'undefined' ? args[number] : match;
            });
        };
    }

    Number.prototype.formatMoney = function (decPlaces, thouSeparator, decSeparator) {
        var n = this,
                decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
                decSeparator = decSeparator === undefined ? "." : decSeparator,
                thouSeparator = thouSeparator === undefined ? "," : thouSeparator,
                sign = n < 0 ? "-" : "",
                i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;
        return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
    };

    if (![].indexOf) {
        Array.prototype.indexOf = function (what, i) {
            i = i || 0;
            var L = this.length;
            while (i < L) {
                if (this[i] === what)
                    return i;
                ++i;
            }
            return -1;
        }
        Array.prototype.lastIndexOf = function (what, i) {
            var L = this.length;
            i = i || L - 1;
            if (isNaN(i) || i >= L)
                i = L - 1;
            else if (i < 0)
                i += L;
            while (i > -1) {
                if (this[i] === what)
                    return i;
                --i;
            }
            return -1;
        }
    }




    function handleTrigger(singleTag, trigger, enableByClick) {

        var triggerElement;
        if (trigger === null)
            triggerElement = jQuery(singleTag);
        else
            triggerElement = jQuery(singleTag).closest(trigger);

        if (enableByClick) {

            var triggerEvent = "click";

            if (!('onclick' in document.documentElement))
                triggerEvent = "touchstart";

            triggerElement.on(triggerEvent, function (e) {

                if (SCD_DEBUG)
                    console.log(triggerEvent + " on price");
                jQuery(singleTag).tooltipster('show');
                e.preventDefault();
                e.stopPropagation();

            });



            if (!(document.scd_document_event_attached)) {
                jQuery('html').on(triggerEvent, function (e) {
                    if (SCD_DEBUG)
                        console.log(triggerEvent + " reached document root");

                    jQuery('body').tooltipster('hide');
                    e.stopPropagation();
                });

                document.scd_document_event_attached = true;

            }


        } else {

            triggerElement.hover(
                    function () {
                        jQuery(singleTag).tooltipster('show');
                    },
                    function () {
                        jQuery(singleTag).tooltipster('hide');
                    }


            );
        }

    }


    var onDOMReady = function (origin, tooltip) {

        tooltip.find('.scd_tooltip_settings').click(function () {
            jQuery('.scd_configpop').bPopup({closeClass: "scd_closeBtn"});

        });

    }



    function tooltipyfy(content, singleTag, globalSettings, singleConfig) {

        jQuery(singleTag).tooltipster({
            animation: singleConfig.tooltipAnimation,
            offsetX: singleConfig.offSetX,
            offsetY: singleConfig.offsetY,
            arrow: globalSettings.showTooltipArrow,
            trigger: 'hover',
            delay: globalSettings.tooltipShowDelay,
            content: content,
            interactiveAutoClose: false,
            speed: singleConfig.animationDuration,
            theme: '.tooltipster-' + singleConfig.tooltipTheme,
            position: singleConfig.tooltipPosition,
            interactive: true,
            functionReady: onDOMReady
        });

        if (globalSettings.enableByClick) {

            jQuery(singleTag).on('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
            });

            if (!(document.scd_document_event_attached)) {

                jQuery('html').on('click', function (e) {
                    if (SCD_DEBUG)
                        console.log("click reached document root");

                    jQuery('body').tooltipster('hide');
                    e.stopPropagation();
                });

                document.scd_document_event_attached = true;

            }
        }


        //      handleTrigger(singleTag, singleConfig.triggerElement, globalSettings.enableByClick);


    }

    function round(value, precision) {
        var multiplier = Math.pow(10, precision || 0);
        return Math.round(value * multiplier) / multiplier;
    }

    function prepareOutputArray(basePriceAmount, baseCurrency, targetCurrencies, globalSettings,woobasecurr) {
//baseCurrency='EUR';
        var outArray = [];

        jQuery.each(targetCurrencies, function (index, target) {

            var inverse;

            var lsIndex;
            var index1;
            var rate1;
            var rate;

            if (rate < 1.0) {
                lsIndex = "scd_" + target + baseCurrency;
                console.log('rate lsindex==='+lsIndex);
                rate = parseFloat(localStorage[lsIndex]);
                console.log('rate==='+rate);
                inverse = true;
            } else {
                lsIndex = "scd_" + baseCurrency + target;
                lsIndex = "scd_"+woobasecurr+target;
                index="scd_"+woobasecurr+ baseCurrency;
                //console.log('rate index inf==='+lsIndex);
                rate = parseFloat(localStorage[lsIndex]);
                rate1 = parseFloat(localStorage[index]);
               // console.log(rate1+'==='+rate);
                if(baseCurrency!==woobasecurr) rate=rate/rate1;
                //console.log('rate final==='+rate);
                inverse = false;
            }


            if (!globalSettings.autoUpdateExchangeRate) {
                if (SCD_DEBUG)
                    console.log(globalSettings.customCurrency);
                jQuery.each(globalSettings.customCurrency, function (index, myCustom) {

                    var res = myCustom.split('_');
                    if (SCD_DEBUG)
                        console.log(res);
                    if (res[2] != "" && res[0] == target && res[5] == baseCurrency) {

                        rate = parseFloat(res[2]);
                        var appliedRate = 1 + parseFloat(res[3] / 100);
                        if (SCD_DEBUG)
                            console.log("++++appliedRate: " + appliedRate);
                        if (SCD_DEBUG)
                            console.log("target currency " + target);
                        if (SCD_DEBUG)
                            console.log("rate for the target " + rate);
                        rate *= appliedRate;
                        if (SCD_DEBUG)
                            console.log("++++rate: " + rate);

                        if (parseFloat(res[2]) < 1.0) {
                            lsIndex = "scd_" + target + baseCurrency;
                            inverse = true;
                        } else {
                            lsIndex = "scd_" + baseCurrency + target;
                            inverse = false;
                        }
                    }

                });
            }

            if (SCD_DEBUG)
                console.log("lsIndex: " + "scd_" + baseCurrency + target);

            if (SCD_DEBUG)
                console.log("***** localStorage: " + localStorage[lsIndex]);

            if (SCD_DEBUG)
                console.log("[||||||=======decimal Precision===== " + globalSettings.decimalPrecision);

            if (globalSettings.decimalNumber)
                dec = globalSettings.decimalPrecision;
            else
                dec = 0;



            if (inverse) {
                if (globalSettings.autoUpdateExchangeRate) {


                    var myprice = basePriceAmount / rate;

                //    if (myprice > 0 && myprice <= 0.5)
                //        myprice = 0.5;
                //    if (myprice > 0.5 && myprice < 1)
                //        myprice = 1;

                    if (globalSettings.approxPrice) {
                        if (SCD_DEBUG)
                            console.log("++++BEFORE ROUNDED: " + myprice);

                        if (dec == 0)
                            myprice = Math.ceil(myprice);
                        else
                            myprice = round(myprice, dec);

                        if (SCD_DEBUG)
                            console.log("++++AFTER ROUNDED: " + myprice);
                    }

                    var price = (myprice).formatMoney(dec, globalSettings.thousandSeperator, globalSettings.decimalSeperator);
                    if(SCD_DEBUG)
                        console.log("CONVERT: " + baseCurrency + target + " " + basePriceAmount + " / " + rate + " -> " + price);
                } else {
                    var myprice = basePriceAmount * rate;

                //    if (myprice > 0 && myprice <= 0.5)
                //        myprice = 0.5;
                //    if (myprice > 0.5 && myprice < 1)
                //        myprice = 1;

                    if (globalSettings.approxPrice) {
                        if (SCD_DEBUG)
                            console.log("++++BEFORE ROUNDED: " + myprice);

                        if (dec == 0)
                            myprice = Math.ceil(myprice);
                        else
                            myprice = round(myprice, dec);

                        if (SCD_DEBUG)
                            console.log("++++AFTER ROUNDED: " + myprice);
                    }

                    var price = (myprice).formatMoney(dec, globalSettings.thousandSeperator, globalSettings.decimalSeperator);
                    if(SCD_DEBUG)
                        console.log("CONVERT: " + baseCurrency + target + " " + basePriceAmount + " * " + rate + " -> " + price);
                }

            } else {
                var myprice = basePriceAmount * rate;

            //    if (myprice > 0 && myprice <= 0.5)
            //        myprice = 0.5;
            //    if (myprice > 0.5 && myprice < 1)
            //        myprice = 1;

                if (globalSettings.approxPrice) {
                    if (SCD_DEBUG)
                        console.log("++++BEFORE ROUNDED: " + myprice);

                    if (dec == 0)
                        myprice = Math.ceil(myprice);
                    else
                        myprice = round(myprice, dec);

                    if (SCD_DEBUG)
                        console.log("++++AFTER ROUNDED: " + myprice);
                }

                var price = (myprice).formatMoney(dec, globalSettings.thousandSeperator, globalSettings.decimalSeperator);
                if(SCD_DEBUG)
                    console.log("CONVERT: " + baseCurrency + target + " " + basePriceAmount + " * " + rate + " -> " + price);
                //get global var
                localStorage['scd_target_currency'] = target;
                localStorage['scd_base_currency'] = baseCurrency;

            }



            if (!globalSettings.autoUpdateExchangeRate) {
                jQuery.each(globalSettings.customCurrency, function (index, myCustom) {

                    var res = myCustom.split('_');
                    if (SCD_DEBUG)
                        console.log("res " + res);
                    if (SCD_DEBUG)
                        console.log("target before " + target);
                    if (res[4] != "" && target == res[0])
                        target = res[4];
                    if (SCD_DEBUG)
                        console.log("target after " + target);
                });
            }

            if (SCD_DEBUG)
                console.log("price " + price);
            var output = {
                currencyLabel: target,
                price: price
            };

            outArray.push(output);

        });
        return outArray;

    }

    function stripPrice(price) {

        return price.replace(/,/g, '.').replace(/[^\d\.]/g, '');
    }



    function renderTooltips(priceTagCollection, globalSettings, configArray) {
  var baseCurr='USD';
        priceTagCollection.each(function (index, element) {

            var baseCurrencyvalStr = jQuery(element).html().replace(globalSettings.thousandSeperator, '').replace(' ', '');//.replace(',','');
            if (SCD_DEBUG)
                console.log("before strip: " + baseCurrencyvalStr);

            var baseCurrency2 = baseCurrencyvalStr.substr(0, 3);
            if (SCD_DEBUG)
                console.log("baseCurrency2: " + baseCurrency2);

            baseCurrencyvalStr = stripPrice(baseCurrencyvalStr);
            if (SCD_DEBUG)
                console.log("Filtered amount: " + baseCurrencyvalStr);

            if (!isNaN(baseCurrencyvalStr)) {

                var baseCurrencyval = parseFloat(baseCurrencyvalStr);


                if (!isNaN(baseCurrency2.substr(1, 1)))
                {
                    if (SCD_DEBUG)
                        console.log("Code: " + configArray[index].baseCurrency);
                } else
                {
                    //configArray[index].baseCurrency = baseCurrency2;
                    if (SCD_DEBUG)
                        console.log("Currency: " + configArray[index].baseCurrency);
                }
                    baseCurr=configArray[index].baseCurrency;
                    if(jQuery(element).attr('basecurrency') !== undefined){
                        //alert(jQuery(element).attr('basecurrency')+'=='+baseCurr);
                        //console.log('defned base '+jQuery(element).attr('basecurrency'));
                        baseCurr=jQuery(element).attr('basecurrency');
                       
                      }else{
                        //  console.log('non defned base '+jQuery(element).attr('basecurrency'));
                      }
                   //console.log('base curr='+baseCurrencyval+baseCurr+' target='+configArray[index].validTargets);
                var outArray =  prepareOutputArray(baseCurrencyval, baseCurr, configArray[index].validTargets, globalSettings,configArray[index].baseCurrency);
                           
                if (outArray.length !== 0) {
                     if(outArray[0].price==0){
                         outArray[0].price=baseCurrencyval;
                     }    
                    if (configArray[index].replaceOriginalPrice) {

                        /*var content = globalSettings.replacedContentFormat;
                         
                         content = content.replace('[convertedCurrencyCode]', outArray[0].currencyLabel);
                         content = content.replace('[convertedAmount]', outArray[0].price);
                         
                         content = content.replace('[originalPrice]', jQuery(element).html());*/
                        var content;

                        // Add currency symbol
                        if((jscd_options.useCurrencySymbol) && (currencySymbolMap[outArray[0].currencyLabel] !== undefined)) {

                            currency_symbol = '<span class="woocommerce-Price-currencySymbol">' +  currencySymbolMap[outArray[0].currencyLabel] + '</span>';

                            switch (jscd_options.currencyPosition) {
                                case 'left':
                                    content = currency_symbol + outArray[0].price;
                                    break;
                                case 'right':
                                    content = outArray[0].price + currency_symbol;
                                    break;
                                case 'left_space':
                                    content = currency_symbol + ' ' + outArray[0].price;
                                    break;
                                case 'right_space':
                                    content = outArray[0].price + ' ' + currency_symbol;
                                    break;
                                default:
                                    content = outArray[0].price + currency_symbol;
                                    break;
                            }
                        }
                        else{
                            content = outArray[0].price + " " + outArray[0].currencyLabel;
                        }
/*
                        if (!globalSettings.autoUpdateExchangeRate) {
                            jQuery.each(globalSettings.customCurrency, function (index, myCustom) {

                                var res = myCustom.split('_');

                                if (res[0] == outArray[0].currencyLabel || res[4] == outArray[0].currencyLabel) {

                                    if (res[1] == "left")
                                        content = outArray[0].currencyLabel + outArray[0].price;

                                    else if (res[1] == "right")
                                        content = outArray[0].price + outArray[0].currencyLabel;

                                    else if (res[1] == "leftspace")
                                        content = outArray[0].currencyLabel + " " + outArray[0].price;

                                    else
                                        content = outArray[0].price + " " + outArray[0].currencyLabel;

                                } else
                                    content = outArray[0].price + " " + outArray[0].currencyLabel;

                            });
                        }
*/
                        jQuery(element).html(content);
                        jQuery(element).attr('basecurrency', outArray[0].currencyLabel);


                    } else {

                        var html = "<table class='scd_priceTable' border='0' cellpadding='0' cellspacing='0'><tbody>";
                        jQuery.each(outArray, function (index, output) {

                            html += ("<tr><td class='left'>" + output.currencyLabel + "</td><td class='right'>" + output.price + "</td></tr>");
                        });



                        html += "</tbody></table>";
                        tooltipyfy(html, element, globalSettings, configArray[index]);

                    }

                }

            } else {

                if (SCD_DEBUG)
                    console.log('"' + baseCurrencyvalStr + '" is not a number. Please pass the selector of a component which contains only a number.');

             }
            
        });
    }


    function overrideExchangeRates(convCodes, overrides) {


        if (overrides.length == 0)
            return;

        jQuery.each(convCodes, function (index, element) {


            if (element in overrides) {
                //   if (overrides.indexOf(element) !== -1 ) {

                var lsIndex = "scd_" + element;
                localStorage[lsIndex] = parseFloat(overrides[element]);
                var date = new Date();
                date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
                if (overrides[element] !== 0 && overrides[element] !== null)
                    document.cookie = lsIndex + "=" + parseFloat(overrides[element]) + expires + "; path=/wordpress/";
            }

        });
    }


    function isLocalRatesValid(convCodes, cacheValidMins) {

        if (localStorage["scd_lastExchangeRateUpdate"] === undefined) {
            if (SCD_DEBUG)
                console.log(localStorage["scd_lastExchangeRateUpdate"] + " Totally New");
            return false; // totally new
        }



        var timeNow = new Date();

        var timeDiff = timeNow.valueOf() - parseInt(localStorage['scd_lastExchangeRateUpdate']);



        if (timeDiff > (cacheValidMins * 60 * 1000)) {
            if (SCD_DEBUG)
                console.log("Cache expired");
            return false; // cache expired
        }


        var allConvCodesFound = true;
        jQuery.each(convCodes, function (index, convCode) {

            var lsIndex = "scd_" + convCode;
            if (SCD_DEBUG)
                console.log("===== convCode:" + convCode);
            if (!(lsIndex in localStorage))
                allConvCodesFound = false;


        });


        return allConvCodesFound;

    }

    function prepareRatesFromLocalData(convCodes) {

        var rates = [];

        jQuery.each(convCodes, function (index, convCode) {
            lsIndex = "scd_" + convCode;
            if (SCD_DEBUG)
                console.log("===== convCodeX:" + convCode);
            rates.push(localStorage[lsIndex]);
        });

        return rates;
    }

    function prepareRatesFromLocalCookies(convCodes) {//in the case local cache and finance provider are down

        var rates = [];

        jQuery.each(convCodes, function (index, convCode) {
            lsIndex = "scd_" + convCode;
            if (SCD_DEBUG)
                console.log("lsIndex " + lsIndex);
            //if (SCD_DEBUG) 
            if (SCD_DEBUG)
                console.log("===== convCodeX:" + convCode + " --cookies " + getCookie(lsIndex));
            rates.push(getCookie(lsIndex));
        });

        return rates;
    }

    function getCookie(name) {
        var getCookieValues = function (cookie) {
            var cookieArray = cookie.split('=');
            return cookieArray[1].trim();
        };

        var getCookieNames = function (cookie) {
            var cookieArray = cookie.split('=');
            return cookieArray[0].trim();
        };

        var cookies = document.cookie.split(';');
        if (SCD_DEBUG)
            console.log("all cookies: " + cookies);
        var cookieValue = cookies.map(getCookieValues)[cookies.map(getCookieNames).indexOf(name)];
        if (SCD_DEBUG)
            console.log("cookie value: " + cookieValue);
        return (cookieValue === undefined) ? null : cookieValue;
    }

    /*function readCookie(name) {
     var nameEQ = name + "=";
     var ca = document.cookie.split(';');
     for(var i=0;i < ca.length;i++) {
     var c = ca[i];
     while (c.charAt(0)==' ') c = c.substring(1,c.length);
     if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
     }
     return null;
     }*/
    function cacheRates(convCodes, rates, from_default_data = false) {
        var date = new Date();
        date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();

        jQuery.each(convCodes, function (index, convCode) {
            lsIndex = "scd_" + convCode;
            if (SCD_DEBUG)
                console.log("===== convCodeXX:" + convCode);
            localStorage[lsIndex] = rates[index];
            //console.log('rates=='+lsIndex);
            document.cookie = lsIndex + "=" + rates[index] + expires + "; path=/wordpress/";
        });

        if(!from_default_data){
            localStorage["scd_lastExchangeRateUpdate"] = (new Date()).valueOf();
            document.cookie = "scd_lastExchangeRateUpdate" + "=" + (new Date()).valueOf();
            +expires + "; path=/wordpress/";
        }
        else {
            localStorage["scd_lastExchangeRateUpdate"] = 0; // This will force us to refresh it next time
        }
    }

    function cacheRatesCookies(convCodes, rates) {
        if ((rates[0] !== "undefined") && (!isNaN(rates[0])) && (rates[0] !== 0) && (rates[0] !== null)) {
            var date = new Date();
            date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();

            jQuery.each(convCodes, function (index, convCode) {
                lsIndex = "scd_" + convCode;
                if (SCD_DEBUG)
                    console.log("===== convCodeXX:" + convCode);
                localStorage[lsIndex] = rates[index];

                document.cookie = lsIndex + "=" + rates[index] + expires + "; path=/wordpress/";
            });

            localStorage["scd_lastExchangeRateUpdate"] = (new Date()).valueOf();
            document.cookie = "scd_lastExchangeRateUpdate" + "=" + (new Date()).valueOf();
            +expires + "; path=/wordpress/";
        }


    }


    function prepareRatesFromYahooData(data) {

        var rates = [];

        if (data.query.results.rate instanceof Array) {

            jQuery.each(data.query.results.rate, function (index, rate) {

                rates.push(parseFloat(rate.Rate));

            });

        } else {
            rates.push(parseFloat(data.query.results.rate.Rate));
        }

        return rates;

    }

    function prepareRatesFromOxrates(data, allConvCodes) {
        var FinalRates = [];
       ;
        index = 0;
        while (index < allConvCodes.length - 1) {
            var convCodes = [];
            convCodes[0] = allConvCodes[index]; //.push(allConvCodes[index]);
            convCodes[1] = allConvCodes[index + 1] //;.push(allConvCodes[index + 1]);
            var defCurr = '';
            var targetCurr = '';
            //var rate = JSON.parse(data);
            jQuery.each(convCodes, function (index, target) { // (XAFEUR,EURXAF
//            console.log('all conv codes'+ allConvCodes);
                if (index == 0)
                    defCurr = target.substring(0, 3);
//                    console.log('default ' + defCurr);
                if ((index == 1))
                    targetCurr = target.substring(0, 3);
//                    console.log('target ' + targetCurr);

            });
            var defRate = parseFloat(data.rates[defCurr]);
            var targetRate = parseFloat(data.rates[targetCurr]);
            if (SCD_DEBUG)
                console.log("target " + targetRate);
            if (SCD_DEBUG)
                console.log("default " + defRate);
            FinalRates.push(targetRate / defRate);
            FinalRates.push(defRate / targetRate);
            index = index + 2;
        }

        return FinalRates;
    }



    function proceedWithRates(allConvCodes, globalSettings, priceTagCollection, configArray) {
 

        overrideExchangeRates(allConvCodes, globalSettings.exchangeRateOverrides);
      
        renderTooltips(priceTagCollection, globalSettings, configArray);

        if (globalSettings.tooltipAlwaysOpen) {
            try {
//                jQuery('body').tooltipster('show'); //wrong
                jQuery('body').tooltipster().tooltipster('show');
            } catch (e) {
                if (SCD_DEBUG)
                    console.log(e);
            }

            if (SCD_DEBUG)
                console.log("All tooltips shown");
        }

        //globalSettings.onFinish();

    }
    /***
     * 
     * @param {type} args
     * @param {type} globalSettings
     * @param {type} allConvCodes
     * @param {type} priceTagCollection
     * @param {type} configArray
     * @param {type} apiArray
     * @returns {undefined}
     */
    function fetchRates(args, globalSettings, allConvCodes, priceTagCollection, configArray, apiArray) {
        //console.log(allConvCodes);
//        SCD_DEBUG = true;
//        var inter = globalSettings.exchangeRateUpdate * globalSettings.exchangeRateUpdateInterval;
        var inter = 12 * 60; // 12 h
       
        if (SCD_DEBUG)
            console.log("*******Int 1 = " + globalSettings.exchangeRateUpdate + " =====Int 2 = " + globalSettings.exchangeRateUpdateInterval + " =====Tot = " + inter);

        if (isLocalRatesValid(allConvCodes, inter)) {

            /**/if (SCD_DEBUG) {
                console.log("Local Rates Valid");
                var timeNow = new Date();
                var timeDiff = timeNow.valueOf() - parseInt(localStorage['scd_lastExchangeRateUpdate']);
                console.log("Last Update Time elapsed: " + timeDiff / 60000);
            }/**/
            
            proceedWithRates(allConvCodes, globalSettings, priceTagCollection, configArray);
            
        } else {

            if (SCD_DEBUG)
                console.log("Local Rates Invalid, Fetching...");
            /* Uses Yahoo Finance API */
            //console.log(args);
            var url = "//query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(" + args + ")&format=json&env=store://datatables.org/alltableswithkeys&callback=";
            var jqxhr = jQuery.get(url, function (data) {

                if (data.query.results === null) {
                    if (SCD_DEBUG)
                        console.log("Yahoo Fetch returned null. Trying with openxchangerates");
                    //query to openxchangerates
                    inCaseOfYahooFailure(globalSettings, allConvCodes, priceTagCollection, configArray, apiArray);
                } else {
                    if (SCD_DEBUG)
                        console.log("Yahoo Fetch Complete.");
                    var rates = prepareRatesFromYahooData(data);
                    cacheRates(allConvCodes, rates);
                    proceedWithRates(allConvCodes, globalSettings, priceTagCollection, configArray);
                    jQuery(document).trigger("scd:scd_rates_fetch_complete", true);
                }
            });
            jqxhr.fail(function () {
//                console.log(apiArray.toString());
                inCaseOfYahooFailure(globalSettings, allConvCodes, priceTagCollection, configArray, apiArray);
            });

        }
    }

    /***
     * 
     * @param {type} globalSettings
     * @param {type} allConvCodes
     * @param {type} priceTagCollection
     * @param {type} configArray
     * @param {type} apiArray
     * @returns {undefined}
     */
    var global_rates={};
    function inCaseOfYahooFailure(globalSettings, allConvCodes, priceTagCollection, configArray, apiArray) {
      
        if (apiArray.length !== 0) {
            var ox_api_id = apiArray.pop();
            var jqxhr = jQuery.get('https://openexchangerates.org/api/latest.json', {app_id: '' + ox_api_id}, function (data) {
                var rates = prepareRatesFromOxrates(data, allConvCodes);
               global_rates=rates;
                if ((rates[0] !== "undefined") && (!isNaN(rates[0])) && (rates[0] !== 0) && (rates[0] !== null)) {
                    //                        consol-yahoo failede.log("1 == equal " + parseFloat(rates[0]) + " Y -yahoo failed " + rates);
                    cacheRates(allConvCodes, rates);
                    proceedWithRates(allConvCodes, globalSettings, priceTagCollection, configArray);
                    jQuery(document).trigger("scd:scd_rates_fetch_complete", true);
                } else {
                   
                    inCaseOfYahooFailure(globalSettings, allConvCodes, priceTagCollection, configArray, apiArray);
                }

            });
            jqxhr.fail(function () {
//                console.log(apiArray.toString());
                inCaseOfYahooFailure(globalSettings, allConvCodes, priceTagCollection, configArray, apiArray);
            });
        } else {
//            if (SCD_DEBUG)
            console.log("Yahoo Fetch Fail. returned null. Trying with default data");
            var rates = prepareRatesFromOxrates(defaultdata, allConvCodes);

            if ((rates[0] !== "undefined") && (!isNaN(rates[0])) && (rates[0] !== 0) && (rates[0] !== null)) {
                cacheRates(allConvCodes, rates, true);
                proceedWithRates(allConvCodes, globalSettings, priceTagCollection, configArray);
                jQuery(document).trigger("scd:scd_rates_fetch_complete", false);
            }
        }

    }

    // returns All conversion codes (including inverses)
    function prepareConvCodes(priceTagCollection, globalSettings, countryCode, configArray) {


        var allConvCodes = [];
        if(SCD_DEBUG)
            console.log("PRICE TAG COLLECTION : "+priceTagCollection.length);
        priceTagCollection.each(function (index, element) {
           
            //if(SCD_DEBUG) 
           
            var baseCurrencyvalStr = jQuery(element).html().replace(globalSettings.thousandSeperator, '').replace(' ', '');//.replace(',','');
            if (SCD_DEBUG)
                console.log("===== Base: ==== " + baseCurrencyvalStr);
            if (SCD_DEBUG)
                console.log("baseCurrencyvalStr " + baseCurrencyvalStr.substr(0, 1));
            if (baseCurrencyvalStr.substr(0, 1) == "<") {
                //var baseCurrency2 = baseCurrencyvalStr.substr(46,1) + baseCurrencyvalStr.substr(54,4);
                var baseCurrency2 = stripPrice(baseCurrencyvalStr);
                if (SCD_DEBUG)
                    console.log("baseCurrency2 " + baseCurrency2 + " case of problem");
            } else
            {
                var baseCurrency2 = baseCurrencyvalStr.substr(0, 3);
            }
            if (SCD_DEBUG)
                console.log("baseCurrency2 " + baseCurrency2 + " final");


            jQuery.each(configArray, function (index, singleConfig) {
                
                var validTargets = getValidTargets(globalSettings.targets, countryCode, singleConfig.baseCurrency, globalSettings.hideTooltipToNativeVisitor, globalSettings);

                singleConfig.validTargets = validTargets;

                var cur;
                if (!isNaN(baseCurrency2.substr(1, 1))) //if the currency's symbol is at the begining 
                {
                    cur = singleConfig.baseCurrency;
                    if (SCD_DEBUG)
                        console.log("cur(c1) " + singleConfig.baseCurrency);
                } else
                {
                    cur = baseCurrency2;
                    if (SCD_DEBUG)
                        console.log("cur(c2) " + baseCurrency2);
                }
                if (SCD_DEBUG)
                    console.log("------------ Cur: --------------" + cur);
                     
                jQuery.each(validTargets, function (index, validTarget) {

                    if (jQuery.inArray(cur + validTarget, allConvCodes) === -1) {

                        allConvCodes.push(cur + validTarget);
                        allConvCodes.push(validTarget + cur); // inverse
                        // console.log("all code " + allConvCodes);
                    }
                     
                });
               
            });
           
        });

        if (SCD_DEBUG) {

            console.log("All Conversion Codes: ");
            console.log(allConvCodes);

        }

        return allConvCodes;

    }

    function getValidTargets(targets, countryCode, baseCurrency, hideTooltipToNativeVisitor, globalSettings) {
        globalSettings.onFinish();
        var validTargets = [];
        //if ( !globalSettings.priceByCurrency ) {
        //if ( !globalSettings.priceByCurrency && !globalSettings.isIt ) {

        jQuery.each(targets, function (index, target) {
           
            if (target === "autodetect") {

                //if ( globalSettings.getUserRole == 'administrator' ) {

                if (countryCode in countryMap) {
                   
                    //if (!(hideTooltipToNativeVisitor  && baseCurrency === countryMap[countryCode].currencyCode)) {
                        
                        validTargets.push(countryMap[countryCode].currencyCode);
//                    } else {
//                        if (SCD_DEBUG)
//                            console.log('Visitor is native... Tooltip not shown');
//                    }

                    if (SCD_DEBUG)
                        console.log("Currency detected : " + countryMap[countryCode].currencyCode);


                } else {

                    if (globalSettings.showFallbackOnAutodetectFailure) {
                        validTargets.push(globalSettings.autodetectFallbackCurrency);
                        if (SCD_DEBUG)
                            console.log("Failed to autodetect currency. Fallbacking to " + globalSettings.autodetectFallbackCurrency);

                    } else
                    if (SCD_DEBUG)
                        console.log("Failed to autodetect currency");
                }
                /*}
                 else {
                 
                 if (globalSettings.baseCurrency in currencyMap){
                 if(jQuery.inArray( globalSettings.baseCurrency, validTargets ) == -1) // Not previously pushed in place of "autodetected"
                 validTargets.push(globalSettings.baseCurrency);
                 } else
                 if (SCD_DEBUG) console.log('"' + globalSettings.baseCurrency + '" is not a valid/supported currency code. See docs to get a list of supported currency codes');
                 
                 }*/

            } else {

                if (target in currencyMap) {
                    if (jQuery.inArray(target, validTargets) == -1) // Not previously pushed in place of "autodetected"
                        validTargets.push(target);
                } else
                if (SCD_DEBUG)
                    console.log('"' + target + '" is not a valid/supported currency code. See docs to get a list of supported currency codes');
            }
        });
        //}

        return validTargets;
    }


    function proceedWithCountryCode(countryCode, globalSettings, priceTagCollection, configArray) {
         
        if (countryCode in countryMap)
            jQuery.scd_currencyCode = (countryMap[countryCode]).currencyCode;
                 
        var allConvCodes = prepareConvCodes(priceTagCollection, globalSettings, countryCode, configArray);
            
        if (allConvCodes.length > 0) { 
            var args = "";
            getArgsFromConvCodes(args, allConvCodes);
            var allConvCodes1 = [];
            getAllConvCodes(allConvCodes1, globalSettings);
            var apiArray = [];
            apiArray.push("23d274d3fd754224af55549416a9c6ac");
            apiArray.push("8f4c6268eb2c482b88c6201712f8e91d");
            
            apiArray.push("3c06a455b44e4ce1b731ff931cc1165d");
            apiArray.push("c6bc01da16fb403a9a7c09be270c269b");
            apiArray.push("ce49a6d9df514cfc8e9e5182289a350f");
//            console.log(typeof (apiArray) + ' 1$$$$ ' + apiArray.toString());
            fetchRates(args, globalSettings, allConvCodes1, priceTagCollection, configArray, apiArray);

        } else {
           
            // console.log("No valid target currencies");
        }

    }



    function prepareConfigArray(globalSettings, elements) {

        var configArray = [];

        var data_options = [
            "animationDuration",
            "baseCurrency",
            "replaceOriginalPrice",
            "tooltipAnimation",
            "tooltipPosition",
            "tooltipTheme",
            "triggerElement",
            "offsetX",
            "offsetY"
        ];

        jQuery.each(elements, function (index, element) {

            var elem = jQuery(element);
            var extraConfigSingle = [];
            jQuery.each(data_options, function (index, data_option) {

                if (elem.attr("data-scd-" + data_option)) {

                    extraConfigSingle[data_option] = elem.data("scd-" + data_option.toLowerCase());

                }

            })



            var singleConfig = {};
            jQuery.extend(true, singleConfig, globalSettings, extraConfigSingle);

            configArray.push(singleConfig);

        });

        return configArray;
    }

    function cleanLocalStorage() {

        jQuery.each(localStorage, function (index, entry) {
            if (SCD_DEBUG)
                console.log("===== Entry:" + entry);
            if (entry === "undefined") {

                localStorage.clear();
                if (SCD_DEBUG)
                    console.log("LocalStorage found corrupted and cleaned");
                return false;
            }
        });
    }

    jQuery.scd_getAllCountries = function () {

        return countryMap;
    }

    jQuery.fn.currencyConverter = function (options) {
        
        var priceTagCollection = this;

       var globalSettings = jQuery.extend({
            /* Defaults */
            baseCurrency: "USD",
            replaceOriginalPrice: true,
            approxPrice: true,
            multiCurrencyPayment: true,
            recalculateCouponValue: true,
            autoUpdateExchangeRate: true,
            exchangeRateUpdate: 10,
            exchangeRateUpdateInterval: 1,
            customNbr: 0,
            customCurrency: [],

            targets: ["autodetect"],
            userCurrencyChoice: ["allcurrencies"],
            decimalNumber: true,
            decimalPrecision: 2,
            priceByCurrency: false,
            role: 'administrator',
            currencyNumber: 1,
            currencyVal: ["USD"],
            getUserRole: 'administrator',
            isIt: true,
            thousandSeperator: ',',
            showFallbackOnAutodetectFailure: true,
            autodetectFallbackCurrency: "USD",

            animationDuration: 300,
            decimalSeperator: '.',
            exchangeRateOverrides: [],
            hideTooltipToNativeVisitor: true,
            onFinish: function () {},
            showFractionWhenBelow: 10,
            showTooltipArrow: true,
            tooltipAlwaysOpen: false,
            tooltipAnimation: 'fade',
            tooltipPosition: 'top',
            tooltipShowDelay: 100,
            tooltipTheme: 'shadow',
            touchFriendly: true,
            triggerElement: null,
            offsetX: 0,
            offsetY: 0,
            /*gearUrl: '',*/


            replacedContentFormat: "[convertedCurrencyCode] [convertedAmount]",

            debugMode: false

        }, options);



        SCD_DEBUG = globalSettings.debugMode;

//        SCD_DEBUG = true;
        cleanLocalStorage();

        if (SCD_DEBUG) {
            console.log("Global settings: ");
            console.log(globalSettings);
        }

        if (!(globalSettings.autodetectFallbackCurrency in currencyMap)) {

            globalSettings.autodetectFallbackCurrency = "USD";
            if (SCD_DEBUG)
                console.log('"' + globalSettings.autodetectFallbackCurrency + '" given as autodetectFallbackCurrency is not a valid/supported currency code. Defaulting to "USD"');

        }


        jQuery.scd_autodetectFallbackCurrency = globalSettings.autodetectFallbackCurrency;

        var configArray = prepareConfigArray(globalSettings, priceTagCollection);

        globalSettings.enableByClick = globalSettings.touchFriendly && ('ontouchstart' in document.documentElement);




        if (localStorage['scd_countryCode'] === undefined) {

            var countryRequest1 = jQuery.get("//ipinfo.io/json");

            countryRequest1.success(function (localdata) {



                if (localdata.country === undefined) {

                    if (SCD_DEBUG)
                        console.log("Location detection failed in first request, second request init...");

                    // 2nd request
                    var countryRequest2 = jQuery.get("//api.hostip.info/country.php");

                    countryRequest2.success(function (localdata) {



                        if (localdata === 'XX') {

                            if (SCD_DEBUG)
                                console.log("Location detection failed in second request");
                            proceedWithCountryCode("error", globalSettings, priceTagCollection, configArray);

                        } else {

                            if (SCD_DEBUG)
                                console.log("Location detection success in second request");
                            localStorage['scd_countryCode'] = localdata;
                            proceedWithCountryCode(localdata, globalSettings, priceTagCollection, configArray);
                            var date = new Date();
                            date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
                            var expires = "; expires=" + date.toGMTString();
                            document.cookie = "scd_countryCode" + "=" + localdata + expires + "; path=/wordpress/";
                            // Trigger event
                            var countryCode = localdata;
                            if (countryCode in countryMap) {
                                jQuery(document).trigger("scd:scd_country_code_updated", countryCode);
                            }
                        }
                    });

                    countryRequest2.error(function (jqXHR, textStatus, errorThrown) {

                        if (SCD_DEBUG)
                            console.log("Location detection failed in second request");
                        proceedWithCountryCode("error", globalSettings, priceTagCollection, configArray);


                    });

                    // end of 2nd request



                } else {

                    if (SCD_DEBUG)
                        console.log("Location detection success in first request");
                    localStorage['scd_countryCode'] = localdata.country;
                    proceedWithCountryCode(localdata.country, globalSettings, priceTagCollection, configArray);
                    var date = new Date();
                    date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
                    var expires = "; expires=" + date.toGMTString();
                    document.cookie = "scd_countryCode" + "=" + localdata.country + expires + "; path=/wordpress/";
                    // Trigger event
                    var countryCode = localdata.country;
                    if (countryCode in countryMap) {
                        jQuery(document).trigger("scd:scd_country_code_updated", countryCode);
                    }

                }
            });

            countryRequest1.error(function (jqXHR, textStatus, errorThrown) {

                // 2nd request
                var countryRequest2 = jQuery.get("//api.hostip.info/country.php");

                countryRequest2.success(function (localdata) {



                    if (localdata === undefined) {

                        if (SCD_DEBUG)
                            console.log("Location detection failed in second request");
                        proceedWithCountryCode("error", globalSettings, priceTagCollection, configArray);

                    } else {

                        if (SCD_DEBUG)
                            console.log("Location detection success in second request");
                        localStorage['scd_countryCode'] = localdata;
                        proceedWithCountryCode(localdata, globalSettings, priceTagCollection, configArray);

                        // Trigger event
                        var countryCode = localdata;
                        if (countryCode in countryMap) {
                            jQuery(document).trigger("scd:scd_country_code_updated", countryCode);
                        }

                    }
                });

                countryRequest2.error(function (jqXHR, textStatus, errorThrown) {

                    if (SCD_DEBUG)
                        console.log("Location detection failed in second request");
                    proceedWithCountryCode("error", globalSettings, priceTagCollection, configArray);


                });

                // end of 2nd request


            });

        } else {

            if (SCD_DEBUG)
                console.log("Cached location valid. Proceeding without XHR");
            proceedWithCountryCode(localStorage['scd_countryCode'], globalSettings, priceTagCollection, configArray);
        }

        return this;

    };

    /*****************************************************************************/
    /*update 7/06/2018 02:24
     /*****************************************************************************/
    /****
     * Get all possible conversion codes from baseCusrency to x currency
     * 
     * @param {type} allConvCodes
     * @param {type} globalSettings
     * @param {type} sep
     * @param {type} reducelist 
     * @returns {undefined}
     */
    function getAllConvCodes(allConvCodes, globalSettings, sep = '', reducelist = false) {
        bc = globalSettings.baseCurrency; //bc: base currency, i.e woocurrency
        jQuery.each(globalSettings.scd_currencies, function (key, value) {
            if (bc !== key) {
                allConvCodes.push(bc + sep + key); //ex: push EURXAF or EUR_XAF if sep = _
                if (!reducelist)
                    allConvCodes.push(key + sep + bc);
            }
        });
    }
    /***
     * Prepare the arguments args to be sent to yahooapi
     * @param {type} args
     * @param {type} convCodes
     * @returns {undefined}
     */
    function getArgsFromConvCodes(args, convCodes) {
        jQuery.each(convCodes, function (index, target) {

            if (index !== 0)
                args += ",";

            args += ("%22" + target + "%22")
            //console.log("target " + target);
        });
    }



}(jQuery));