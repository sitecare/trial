/**
 * File input-focus-fix.js
 *
 * Ensures inputs with text in them don't shrink when they lose focus.
 * For example, when focus is shifted to the submit button.
 */
window.wdsFixedHeaderInputsFix = {};
( function( window, $, app ) {

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
			inputs: $( '.site-header .search-form input[type="text"]' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {

		// We only need to check for a value when the focus is moved away from the input.
		app.$c.inputs.on( 'focusout', app.fixInput );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.inputs.length;
	};

	// Set a class on the input when it has a value.
	app.fixInput = function() {
		var value = $( this ).val();

		if ( '' !== value ) {
			$( this ).addClass( 'not-empty' );
		} else {
			$( this ).removeClass( 'not-empty' );
		}
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.wdsFixedHeaderInputsFix );
