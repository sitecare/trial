<?php
/**
 * Updates for RSS feeds
 */

/**
 * Modify the main feed to remove posts hidden from the homepage.
 *
 * @param  WP_Query $query The current query object.
 * @return  void
 */
function wds_acme_update_main_feed_query( $query ) {
	// Bail early if this isn't a feed.
	if ( ! $query->is_feed() ) {
		return;
	}

	// Bail early if we're on a category feed.
	if ( $query->is_category() ) {
		return;
	}

	// Get the current meta_query, default to an empty array.
	$meta_query = $query->get( 'meta_query' );
	$meta_query = is_array( $meta_query ) ? $meta_query : array();

	// Set the meta query to not find posts hidden from the front page.
	$meta_query[] = array(
		'key' 		=> '_wplp_post_front',
		'value' 	=> '',
		'compare' 	=> 'NOT EXISTS',
	);

	// Set the new meta query.
	$query->set( 'meta_query', $meta_query );
}
//add_action( 'pre_get_posts', 'wds_acme_update_main_feed_query', 10, 1 );
