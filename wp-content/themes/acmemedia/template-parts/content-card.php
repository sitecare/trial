<?php
/**
 * Template part for displaying blog cards
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Acme Media
 */

	// Set up category stuff.
	$category_name = '';
	$category_link = '';
	$category = get_the_category();

	// Check for variable.
	if ( isset( $category, $category[0] ) ) {
		// If there is an array of categories.
		if ( is_array( $category ) ) {
			$category_name = $category[0]->name;
			$category_link = get_category_link( $category[0]->cat_ID );
		}
	}

	// Setup defaults.
	$args = array(
		'image'         => wds_acme_get_post_image_uri( 'blog-card' ),
		'title'         => wds_acme_get_the_title( array( 'length' => 23 ) ),
		'content'       => wds_acme_get_the_excerpt( array( 'length' => 18 ) ),
		'link'          => get_the_permalink(),
		'author'        => get_the_author(),
		'date'          => get_the_date( 'M j, Y' ),
		'comments'      => get_comments_number(),
		'category'      => $category_name,
		'category_link' => $category_link,
	);

	// Echo a card.
	echo apply_filters( 'dynamic_cdn_content', wds_acme_get_content_card( $args ) ); // WPCS: XSS ok.
