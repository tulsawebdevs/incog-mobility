/*jslint white: true, browser: true, devel: true, onevar: true, undef: true, nomen: true, eqeqeq: true, plusplus: true, bitwise: true, regexp: true, newcap: true, immed: true */
/*global Modernizr, jQuery, $ */

var oneCare = oneCare || {};

/*===========================================================================
	Utilities
===========================================================================*/
(function ($) {
	$.standardize = function () {
		function addPlaceholders() {
			$('input[placeholder]').each(function () {
				var $el = $(this),
					str = "";
				
				if ($el.val()) {
					return;
				}
				
				str = $el.attr('placeholder');
				$el.val(str);
				
				$el.focus(function () {
					if ($el.val() === str) {
						$el.val('');
					}
				}).blur(function () {
					if ($el.val() === '') {
						$el.val(str);
					}
				});
			});
		}
		
		function init() {		
			// Standardize common HTML5 functionality across browsers	
			if (!Modernizr.input.placeholder) {
				addPlaceholders();
			}
			
			// Prevent image flicker in IE
			if ($.browser.msie) {
				try {
					document.execCommand("BackgroundImageCache", false, true);
				} catch (e) {}
			}
		}
		
		init();
	};
}(jQuery));

var UTIL = {
	fire: function (func, funcname, args) {
		var namespace = oneCare;  // object literal namespace

		funcname = (funcname === undefined) ? 'init' : funcname;

		if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
			namespace[func][funcname](args);
		}
	},

	loadEvents: function () {
		var bodyId = document.body.id;

		UTIL.fire('common');

		$.each(document.body.className.split(/\s+/), function (i, classnm) {
			UTIL.fire(classnm);
			UTIL.fire(classnm, bodyId);
		});
	},
	
	log: function (msg) {
		if (window.console && window.console.log) {
			console.log(msg);
		}
	}
};