/**
 * File comment-toggle.js
 *
 * Handles toggling of the Disqus comment section on single posts.
 */
window.wdsCommentToggler = {};
( function( window, $, app ) {

	// Store comment block id globally for easy re-use.
	var commentBlockID = '';

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
			toggleButton: $( '.comment-toggle' ),
			window: $( window )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.toggleButton.on( 'click', app.toggleComments );
		//app.$c.window.on( 'transitionend', app.commentEndTransition );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.toggleButton.length;
	};

	// Toggle the comment block, and make sure the aria-expanded attribute gets updated.
	app.toggleComments = function() {

		// Get the height of the disqus block (or whatever element data-height is set to)
		// And use that to smoothly expand/collapse the comment block with CSS transitions.
		const commentHeight = app.getBlockHeight( '#' + $( this ).data( 'height' ) );
		const styles = {
			'height': commentHeight,
			'min-height': commentHeight
		}

		commentBlockID = $( this ).data( 'target' );

		app.$c.commentBlock = $( '#' + commentBlockID );
		app.$c.commentBlock[0].addEventListener( 'transitionend', app.commentEndTransition );

		// DOM attributes on jQuery objects are strings.
		if ( 'true' === $( this ).attr( 'aria-expanded' ) ) {

			// Reset the height from auto to an actual value.
			app.$c.commentBlock.css( styles );

			// Then wait a bit before removing the rest for a smoother height transition.
			setTimeout( function() {

				// Remove the "style" attribute (which sets the height)
				// The CSS transition will take over and hide all the things.
				app.$c.commentBlock.removeAttr( 'style' ).removeClass( 'open' );

			}, 50 );

			$( this ).attr( 'aria-expanded', false );

		} else {

			app.$c.commentBlock.css( styles ).addClass( 'open' );
			$( this ).attr( 'aria-expanded', true );

		}
	};

	// Get the height of the element that's passed, or fall back to default comment block if there is no height.
	app.getBlockHeight = function( heightID ) {
		var returnHeight = $( heightID ).height();

		// Fall back to the default comment form if we can't get a height from the requested element.
		if ( ! returnHeight ) {
			returnHeight = $( '#comments' ).height();
			commentBlockID = 'comments';
		}

		// 25 is to account for the padding on the comment wrap.
		return returnHeight + 25;
	};

	// transitionend event handler, add the opened class if the target's id matches that of our comment block.
	app.commentEndTransition = function( e ) {

		if ( e.propertyName.includes( 'height' ) ) {
			// Only add the opened class when it already has the open class (meaning it did, in fact, open and not close).
			if ( app.$c.commentBlock.hasClass( 'open' ) ) {
				app.$c.commentBlock.height( 'auto' );
			}
		}

	};

	// Engage!
	$( app.init );

})( window, jQuery, window.wdsCommentToggler );
