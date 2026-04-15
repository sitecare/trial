/**
 * File smooth-scroll.js
 *
 * Smooth scroll functionality, specifically for #main-content type links (post pagination).
 */
window.smoothScroll = {};
( function( window, $, app ) {

	// Constructor.
	app.init = function() {
		app.cache();
		app.bindEvents();
	};

	// Cache all the things.
	app.cache = function() {
		app.$c = {
			window: $( window ),
			document: $( document ),
			page: $( 'html, body' ),
			content: $( '.site-main' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.document.on( 'ready', app.handleScroll );
	};

	// Scroll to content
	app.handleScroll = function() {

		// Bail if the hash is not what we're looking for.
		if ( '#main-content' !== window.location.hash ) {
			return;
		}

		// Use animate to scroll down to the content portion of the page more smoothly.
		app.$c.page.animate({
			scrollTop: app.$c.content.offset().top + 'px'
		}, 1000, 'swing' );
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.smoothScroll );
