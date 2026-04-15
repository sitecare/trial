/**
 * File customizer.js.
 *
 * Enable real-time updates in the customizer.
 */

( function( $ ) {

	// Hook into the API.
	var api = wp.customize;

	// Site title.
	api( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );

	// Site description.
	api( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Background image.
	api( 'background_image', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).toggleClass( 'custom-background-image', '' !== to );
		} );
	} );

	// Copyright text.
	api( 'wds_acme_copyright_text', function( value ) {
		value.bind( function( to ) {
			$( '.site-info' ).text( to );
		} );
	} );
} )( jQuery );
