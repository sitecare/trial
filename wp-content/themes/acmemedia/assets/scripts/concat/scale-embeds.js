/**
 * File scale-embeds.js
 *
 * Automatically re-scale iframe embeds.
 */
window.wdsScaleEmbeds = {};
( function( window, $, app ) {

	var resizeTimer = 0;

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
			iframes: $( '.site-main .entry-content iframe' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'load', app.checkIframes );
		app.$c.window.on( 'resize', app.rescaleIframes );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.iframes.length;
	};

	// Check iframe size.
	// Only do something when the iFrame's width attribute is set wider than it actually is rendering.
	app.checkIframes = function() {

		app.$c.iframes.each( function() {
			const width = $( this ).attr( 'width' ),
				outerWidth = $( this ).outerWidth();

			if ( width > outerWidth ) {
				app.scaleFrame( $( this ) );
			}
		});

	};

	// Re-run the size check when the window resizes.
	// Using a timer to ensure it only fires after we can reasonably conclude the resizing is done.
	app.rescaleIframes = function() {

		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( app.checkIframes, 500 );

	};

	// Scale the iFrame width.
	app.scaleFrame = function( $iframe ) {

		// Get the height/width of the current iframe, calculate what the new height should be.
		const width = $iframe.attr( 'width' ),
			height = $iframe.attr( 'height' ),
			scale = height / width,
			newHeight = $iframe.outerWidth() * scale;

		$iframe.outerHeight( newHeight );

	};

	// Engage!
	$( app.init );

})( window, jQuery, window.wdsScaleEmbeds );
