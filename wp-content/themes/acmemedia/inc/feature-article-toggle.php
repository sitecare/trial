<?php
/**
 * Add toggle checkbox to Post Editor to define current post as 'Featured' 
 *
 * @package Acme Media
 */

add_action('post_submitbox_misc_actions', 'wds_acme_featured_article_toggle');
add_action('save_post', 'wds_acme_save_featured_article_toggle');

/**
 * Display checkbox on Post 'Publish' box to allow author to assign post as 'Featured in Newsfeed'
 * 
 */
function wds_acme_featured_article_toggle(){
	$post_id = get_the_ID();

	// should only work on 'post' type
	if ( 'post' != get_post_type( $post_id) ) {
		return;
	}

	$value = get_post_meta( $post_id, 'featured_in_newsfeed', true );
	wp_nonce_field( 'featured_nonce_' . $post_id, 'featured_nonce' );
	?>
	<div class="misc-pub-section misc-pub-section-last">
		<label><input type="checkbox" value="1" <?php checked( $value, true, true ); ?> name="featured_in_newsfeed" /><?php _e( 'Featured in Newsfeed', 'acme' ); ?></label>
	</div>
	<?php
}

/**
 * If post is being edited or saved, update the featured_in_newsfeed appropriately
 * @param $post_id Post being saved or edited
 * 
 */
function wds_acme_save_featured_article_toggle( $post_id ) {

	if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['featured_nonce'] ) || ! wp_verify_nonce( $_POST['featured_nonce'], 'featured_nonce_'.$post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['featured_in_newsfeed'] ) ) {
		update_post_meta( $post_id, 'featured_in_newsfeed', (int) $_POST['featured_in_newsfeed'] );
	} else {
		delete_post_meta( $post_id, 'featured_in_newsfeed' );
	}
}

/**
 * Function to return wether or not the current post is featured.
 * usage:  if ( is_post_featured() ) { // do stuff  }
 * @return boolean Defaults to false if used outside of loop
 */
function wds_acme_is_post_featured( ) {
	global $post;
	if ( isset( $post->ID ) ) {
		return get_post_meta( $post->ID, 'featured_in_newsfeed', true );
	} else {
		return false;
	}		
}

/**
 * Get the template a post should use, based on whether the checkbox for featured post is set or not.
 * @param  int $id The ID for the post we're trying to display.
 *
 * @return string     The template part that should be used.
 */
function wds_acme_get_post_template( $id ) {
	$is_featured = get_post_meta( $id, 'featured_in_newsfeed', true );
	$template = 'template-parts/content-card';

	// Use the featured template when the checkbox is set.
	if ( '1' === $is_featured ) {
		$template = 'template-parts/content-featured';
	}

	return $template;
}
