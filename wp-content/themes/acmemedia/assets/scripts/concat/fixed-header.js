/**
 * File fixed-header.js
 *
 * Fix the header to top and shrink on scroll.
 */
window.wdsScrollToFixed = {};
( function( window, $, app ) {

	// Constructor.
	app.init = function() {
		app.cache();

		if ( app.meetsRequirements() ) {
			app.bindEvents();
		}
	};

	// Cache all the things, but mostly the header.
	app.cache = function() {
		app.$c = {
			window: $( window ),
			body: $( 'body' ),
			siteHeader: $( '.site-header' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'scroll', app.toggleFixedHeader );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.siteHeader.length;
	};

	// Toggle the fixed version of the header.
	app.toggleFixedHeader = function() {
		var headerHeight = app.$c.siteHeader.height() / 2;

		if ( app.$c.window.scrollTop() > headerHeight ) {
			app.$c.body.addClass( 'fixed-header' );
		} else {
			app.$c.body.removeClass( 'fixed-header' );
		}
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.wdsScrollToFixed );
