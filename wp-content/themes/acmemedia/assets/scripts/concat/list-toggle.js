/**
 * File list-toggle.js
 *
 * Toggle a list with a button.
 */
window.wdsListToggle = {};
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
			body: $( 'body' ),
			toggleButton: $( '.dropdown-toggle' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {

		// Toggle the list to open/close on button cick.
		app.$c.toggleButton.on( 'click', app.toggleList );

		// Allow the user to close the list by hitting the esc key.
		app.$c.body.on( 'keydown', app.escKeyClose );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.toggleButton.length;
	};

	// Toggle the ist.
	app.toggleList = function() {

    // Figure out which list we're opening and store the object.
		var $list = $( $( this ).siblings( '.dropdown-list' ) ),
				location = 'bottom';

		// Figure out where if we need to change the list position..
		if ( $( this ).data( 'location' ) ) {
			location = $( this ).data( 'location' );
		}

    if ( $list.hasClass( 'open' ) ) {
      app.closeList( $list, true );
    } else {
      app.closeList( $( '.dropdown-list.open' ), false );
      app.openList( $list, location );
    }
	};

	// Close the open list.
	app.closeList = function( $list, moveFocus ) {
    $list.removeClass( 'open' );
    $list.siblings( '.dropdown-toggle' ).attr( 'aria-expanded', false );

    if ( moveFocus ) {
      $list.siblings( '.dropdown-toggle' ).focus();
    }
	};

  // Open a list.
  app.openList = function( $list, position ) {

		// Add a class top when we want it to open on top, instead of below the button
		if ( 'top' === position ) {
			$list.addClass( 'top' );
		}

    $list.addClass( 'open' );
    $list.siblings( '.dropdown-toggle' ).attr( 'aria-expanded', true );
  };

	// Close if "esc" key is pressed.
	app.escKeyClose = function( e ) {
		if ( 27 === e.keyCode ) {
			app.closeList( $( '.dropdown-list.open' ), true );
		}
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.wdsListToggle );
