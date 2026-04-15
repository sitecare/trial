/**
 * File submenu-toggle.js
 *
 * Allow submenus to be toggled by keyboard, without breaking mouse hover.
 */
window.subMenuToggler = {};
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
			parentMenuItems: $( 'nav:not(.mobile-nav-menu) .menu-item-has-children' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.parentMenuItems.on( 'click keydown', 'a', app.handleToggle );
		app.$c.parentMenuItems.on( 'mouseleave', app.classToggleLeave );
		app.$c.parentMenuItems.on( 'mouseenter', app.classToggleEnter );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.parentMenuItems.length;
	};

	// Toggling logic.
	// - Toggle immediately if the href is simply #
	// - Allow the second interaction to load the link
	app.handleToggle = function( e ) {

		// Bail if there is no submenu.
		if ( ! $( this ).parent().hasClass( 'menu-item-has-children' ) ) {
			return;
		}

		// Bail if it is neither space or enter.
		if ( 32 !== e.keyCode && 13 !== e.keyCode && 'keydown' === e.type ) {
			return;
		}

		const link = $( this ).attr( 'href' ),
					hasClass = $( this ).hasClass( 'open-link' );

		// If the link is just a hash, we can safely toggle the menu.
		// Do the same thing if it does not have the class open-link.
		if ( '#' === link || ! hasClass ) {
			$( this ).parent().toggleClass( 'focus' );

			// Make sure to mark legit links with a class to allow them to work on a second activation.
			if ( '#' !== link ) {
				$( this ).addClass( 'open-link' );
			}

			return false;
		}
	};

	// Removes the focus class. Triggered on mouseleave, in case people mouse-click on links and then move on.
	app.classToggleLeave = function() {

		if ( $( this ).hasClass( 'focus' ) ) {
			$( this ).removeClass( 'focus' ) ;
		}

		if ( $( this ).children( 'a' ).hasClass( 'open-link' ) ) {
			$( this ).children( 'a' ).removeClass( 'open-link' );
		}

	};

	// Add the open-link class when the mouse enters the link - this way, legit links will just work
	// while the hover takes care of showing the submenu.
	app.classToggleEnter = function() {

		const $link = $( this ).children( 'a' );

		if ( '#' !== $( $link[0] ).attr( 'href' ) ) {
			$( $link[0] ).addClass( 'open-link' );
		}
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.subMenuToggler );
