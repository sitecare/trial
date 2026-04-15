<?php
/**
 * Tags used to manipulate custom excerpt
 *
 * @package WDS Acme Custom Excerpt
 * @author Mike Grotton
 */

/**
 * Does this post contain a custom excerpt
 *
 * @return bool
 */
function wds_acme_has_custom_excerpt() {
	global $post;
	// Check for $post->ID, if doesn't exist bail.
	if ( ! isset( $post->ID ) ) {
		return false;
	}

	// is there a custom excerpt?
	if ( ! empty( wp_strip_all_tags( get_post_meta( $post->ID, 'wds-acme-custom-excerpt', true ) ) ) ) {
		return true;
	}

	return false;
}

/**
 * Display the custom excerpt for current post.
 *
 * Usage: wds_acme_the_custom_excerpt( bool $raw = false|true, bool $echo = true|false ).
 * if $raw is true, html will be stripped from the excerpt.
 * if $echo is true excerpt is echoed, if false returned
 *
 * @param boolean $raw flag to return without markup (Default: false).
 * @param boolean $echo flag to echo output or return it (Default: True).
 * @return html
 */
function wds_acme_the_custom_excerpt( $raw = false, $echo = true ) {
	global $post;

	// Check for $post->ID, if not there bail early.
	if ( ! isset( $post->ID ) ) {
		return;
	}

	$excerpt = get_post_meta( $post->ID, 'wds-acme-custom-excerpt', true );

	// The excerpt is not being requested in raw format.
	if ( ! $raw ) {
		if ( $echo ) {
			// echo and bail.
			echo wp_kses_post( htmlspecialchars_decode( $excerpt ) );
			return;
		}
		if ( ! $echo ) {
			return htmlspecialchars_decode( $excerpt );
		}
	}

	// The excerpt is being requested in raw format.
	if ( $raw ) {
		if ( $echo ) {
			// echo and bail.
			echo esc_html( wp_strip_all_tags( $excerpt ) );
			return;
		}
		if ( ! $echo ) {
			return wp_strip_all_tags( $excerpt );
		}
	}

}
