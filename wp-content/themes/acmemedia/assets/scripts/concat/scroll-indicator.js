/**
 * File scroll-indicator.js
 *
 * Checks the hidden mobile nav for height and displays the scroll indicator if it's larger than the screen.
 */
window.mobileMenuScrollIndicator = {};
( function( window, $, app ) {

	// Constructor.
	app.init = function() {
		app.cache();
		app.bindEvents();
	};

	// Cache all the things.
	app.cache = function() {
		app.$c = {
			window: $( window )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'load', app.scaffolding );
	};

	// We're relying on elements that are added after document.ready,
	// which means we'll need to initiate cache / events later.
	app.scaffolding = function() {

		// Cache hidden menu element.
		app.$c.menuWrap = $( '.mobile-nav-menu-hidden' );

		// Bind our events.
		app.$c.menuWrap.on( 'scroll', app.checkScrollPosition );
		app.$c.window.on( 'resize', app.checkForScroll );

		// Initiate, part 2!
		app.checkForScroll();
	};

	// Check if the element can be scrolled.
	app.checkForScroll = function() {

		// Remove the scroll class, in case it's still there from before resizing.
		app.removeScrollClass();

		// If the height is larger than the window height, unhide the icon.
		if ( app.$c.menuWrap[0].scrollHeight - 70 > app.$c.window.height() - 70 ) {
			app.addScrollClass();
		}

	};

	app.checkScrollPosition = function() {

		const scrollPosition = $( this ).scrollTop() + app.$c.menuWrap.height();

		// If the scrollPosition and srollHeight are equal, we've reached the bottom.
		if ( scrollPosition === app.$c.menuWrap[0].scrollHeight ) {
			app.removeScrollClass();
		} else {

			// If not, and it doesn't have the class - we'll need to re-add it.
			// For example, when we've reached the bottom and then scroll back up.
			if ( ! $( this ).hasClass( 'scroll' ) ) {
				app.addScrollClass();
			}

		}

	};

	// Add scroll class.
	app.addScrollClass = function() {
		app.$c.menuWrap.addClass( 'scroll' );
	};

	// Remove scroll class.
	app.removeScrollClass = function() {
		app.$c.menuWrap.removeClass( 'scroll' );
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.mobileMenuScrollIndicator );
