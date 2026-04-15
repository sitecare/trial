/**
 * File hide-tapbar.js
 *
 * Hide the tapbar when scrolling down, unhide it when scrolling up.
 */
window.wdsHideTapbar = {};
( function( window, $, app ) {

	// Variable to keep track of whether we're scrolling up or down.
	let lastScrollTop = 0;

	// Constructor.
	app.init = function() {
		app.cache();

		if ( app.meetsRequirements() ) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function() {
		app.$c = {
			window: $( window ),
			mobileNavWrap: $( '#mobile-nav-menu' ), // the nav wrapper, using this to  check if the nav is open of not.
			siteHeader: $( '.site-header' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		//app.$c.window.on( 'scroll', app.handleScroll );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.siteHeader.length;
	};

	// (Un)hide the tapbar / header
	app.handleScroll = function() {

		// Bail early if the window isn't showing the tapbar.
		if ( 1024 <= app.$c.window.width() ) {
			return;
		}

		// Also bail early if the menu is open and (somehow) the scroll event is triggered
		if ( app.$c.mobileNavWrap.hasClass( 'more' ) ) {
			return;
		}

		const scrollTop = $( this ).scrollTop();

		if ( scrollTop > lastScrollTop ) {
			app.$c.siteHeader.addClass( 'tapbar-hidden' );
		} else {
			app.$c.siteHeader.removeClass( 'tapbar-hidden' );
		}

		lastScrollTop = scrollTop;

	};

	// Engage!
	$( app.init );

})( window, jQuery, window.wdsHideTapbar );
