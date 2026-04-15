<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Acme Media
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function wds_acme_body_classes( $classes ) {

	// @codingStandardsIgnoreStart
	// Allows for incorrect snake case like is_IE to be used without throwing errors.
	global $is_IE;

	// If it's IE, add a class.
	if ( $is_IE ) {
		$classes[] = 'ie';
	}
	// @codingStandardsIgnoreEnd

	// Give all pages a unique class.
	if ( is_page() ) {
		$classes[] = 'page-' . basename( get_permalink() );
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Are we on mobile?
	// PHP CS wants us to use jetpack_is_mobile instead, but what if we don't have Jetpack installed?
	// Allows for using wp_is_mobile without throwing an error.
	// @codingStandardsIgnoreStart
	if ( wp_is_mobile() ) {
		$classes[] = 'mobile';
	}
	// @codingStandardsIgnoreEnd

	// Adds "no-js" class. If JS is enabled, this will be replaced (by javascript) to "js".
	$classes[] = 'no-js';

	return $classes;
}
add_filter( 'body_class', 'wds_acme_body_classes' );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images
 *
 * @package Acme Media
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function wds_acme_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	840 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';

	if ( 'page' === get_post_type() ) {
		840 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	} else {
		840 > $width && 600 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
		600 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'wds_acme_content_image_sizes_attr', 10 , 2 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails
 *
 * @package Acme Media
 *
 * @param array $attr Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function wds_acme_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'wds_acme_post_thumbnail_sizes_attr', 10 , 3 );

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function wds_acme_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'wds_acme_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'wds_acme_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so wds_acme_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so wds_acme_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in wds_acme_categorized_blog.
 */
function wds_acme_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}
	// Like, beat it. Dig?
	delete_transient( 'wds_acme_categories' );
}
add_action( 'delete_category', 'wds_acme_category_transient_flusher' );
add_action( 'save_post',     'wds_acme_category_transient_flusher' );

/**
 * Get an attachment ID from it's URL.
 *
 * @param string $attachment_url The URL of the attachment.
 * @return int The attachment ID.
 */
function wds_acme_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;

	$attachment_id = false;

	// If there is no url, return.
	if ( '' === $attachment_url ) {
		return false;
	}

	// Get the upload directory paths.
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image.
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL.
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Do something with $result.
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) ); // WPCS: db call ok , cache ok.
	}

	return $attachment_id;
}

/**
 * Filters out the archive title pre-text, ie. "Category: ".
 *
 * @param  string $title The archive's title.
 * @return string        The modified title.
 */
function wds_acme_archive_titles( $title ) {

	if ( is_category() ) {
		$title = single_cat_title( '', false ) . esc_html( ' News', 'acme' );
	} elseif ( is_author() ) {
		$title = get_the_author();
	} elseif ( is_date() ) {
		if ( is_year() ) {
			$title = sprintf( __( '%s Archives', 'acme' ), get_the_date( _x( 'Y', 'yearly archives date format' ) ) );
		} elseif ( is_month() ) {
			$title = sprintf( __( '%s Archives', 'acme' ), get_the_date( _x( 'F Y', 'monthly archives date format' ) ) );
		} elseif ( is_day() ) {
			$title = sprintf( __( '%s Archives', 'acme' ), get_the_date( _x( 'F j, Y', 'daily archives date format' ) ) );
		}
	} else {
		$title = __( 'Archives' );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'wds_acme_archive_titles' );

/**
 * Prepend the full-size featured image to feed content.
 *
 * @param string $content The feed content.
 * @return string The modified feed content.
 */
function wds_acme_featured_post_thumb_in_feeds( $content ) {
	global $post;

	if ( isset( $post->ID ) && has_post_thumbnail( $post->ID ) ) {
		$content = get_the_post_thumbnail( $post->ID, 'full' ) . $content;
	}

	return $content;
}
add_filter( 'the_excerpt_rss', 'wds_acme_featured_post_thumb_in_feeds' );
add_filter( 'the_content_feed', 'wds_acme_featured_post_thumb_in_feeds' );


/**
 * Display Author Alias on articles if present
 *
 */
function wds_acme_author_alias( $author_display_name ) {
	$author_alias = get_field( 'author_alias' );

	if ( empty( $author_alias ) ) {
		return $author_display_name;
	} else {
		return $author_alias;
	}
}
add_filter( 'the_author', 'wds_acme_author_alias', 10, 1 );

/**
 * Add Twitter to author profile.
 *
 * @param  array $contactmethods Array of available contact methods.
 * @return array                 Modified array of contact methods.
 */
function wds_acme_contactmethods( $contactmethods ) {
	$contactmethods['twitter'] = __( 'Twitter', 'acme' );

	return $contactmethods;
}
add_filter( 'user_contactmethods', 'wds_acme_contactmethods', 10, 1 );

/**
 * Retrieve a set of recent WordPress posts.
 *
 * @param  array $args Array of arguments that can be used to modify the query.
 * @return object       WP_Query object of recent posts.
 */
function wds_acme_retrieve_recent_posts( $args = array() ) {
	// Set defaults.
	$defaults = array(
		'posts_per_page' => 6,
		'filter'   		 => array(),
		'meta_query' 	 => array(
			array(
				'key' 		=> 'cloned',
				'compare' 	=> 'NOT EXISTS',
			),
		),
	);

	// Parse arguments for the query.
	$args = wp_parse_args( $args, $defaults );

	// Fetch posts.
	$posts = new WP_Query( array(
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'post_type'		 => 'post',
		'posts_per_page' => $args['posts_per_page'],
		'post__not_in'   => $args['filter'],
		'ignore_sticky'	 => true,
		'meta_query'	 => $args['meta_query'],
	) );

	return $posts;
}

/**
 * Filter any posts that have an author alias set on an author archive page.
 *
 * @param  object $query A WP_Query object.
 * @return object        A WP_Query object that may include a custom meta_query.
 */
function wds_acme_filter_aliased_posts( $query ) {

	// Bail if this is not an author page and return the query unmodified.
	if ( ! is_author() ) {
		return $query;
	}

	// Get any existing metaqueries.
	$meta_query = $query->get( 'meta_query' );

	// If there are none, start off with a new array.
	if ( ! is_array( $meta_query ) ) {
		$meta_query = array();
	}

	// Our metaquery, get only posts that have an empty author_alias string,
	// or no meta named author_alias set at all.
	$meta_query[] = array(
		array(
			'relation' => 'OR',
			array(
				'key'     => 'author_alias',
				'compare' => '=',
				'value'   => '',
			),
			array(
				'key'     => 'author_alias',
				'compare' => 'NOT EXISTS',
			)
		),
	);

	$query->set( 'meta_query', $meta_query );

	return $query;
}
add_action( 'pre_get_posts', 'wds_acme_filter_aliased_posts' );

/**
 * Filter the HTML output of individual page number links to append the #main id.
 *
 * @param  string $link        The page number HTML output.
 * @param  int    $page_number Page number for paginated posts' page links.
 * @return string              The filtered HTML output.
 */
function wds_acme_append_main( $link, $page_number ) {
	return preg_replace( '~href=(["|\'])(.+?)\1~', 'href="$2#main-content"', $link );
}
add_filter( 'wp_link_pages_link', 'wds_acme_append_main', 10, 2 );

/**
 * Allow iframes/scripts in TinyMCE for contributors or higher.
 *
 * @param  array $allowedposttags Allowed tags in posts.
 * @return array                  Modified allowed tags in posts.
 */
function wds_acme_allow_post_tags( $allowedposttags ) {
	// Bail early if the user isn't allowed to publish posts.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return $allowedposttags;
	}

	// Allow script tags.
	$allowedposttags['script'] = array(
		'type'	 => true,
		'src'	 => true,
		'height' => true,
		'width'  => true,
	);

	// Allow iframes.
	$allowedposttags['iframe'] = array(
		'src' 					=> true,
		'width' 				=> true,
		'height' 				=> true,
		'class' 				=> true,
		'frameborder' 			=> true,
		'webkitAllowFullScreen' => true,
		'mozallowfullscreen' 	=> true,
		'allowFullScreen' 	=> true,
	);

	return $allowedposttags;
}
add_filter( 'wp_kses_allowed_html', 'wds_acme_allow_post_tags', 10, 1 );

/**
 * Force Feature 5 Admin Grid to 1 Column.
 *
 * @param  string $columns Number of columns.
 * @return int          Column number set.
 */
function wds_acme_screen_layout_columns( $columns ) {
	$columns['Grid'] = 2;
	return $columns;
}
add_filter( 'screen_layout_columns', 'wds_acme_screen_layout_columns' );

function so_screen_layout_post() {
	return 2;
}
add_filter( 'get_user_option_screen_layout_post', 'so_screen_layout_post' );

/**
 * Sanitize JS in the customizer.
 *
 * @param string $js Javascript markup.
 */
function wds_acme_sanitize_js( $js ) {
	return $js; // WPCS: XSS ok.
}

/**
 * Replaces variables in the string.
 *
 * @param  string $string The string to replace variables in.
 * @return string    	  Updated string with variables replaced.
 */
function wds_acme_replace_vars_in_string( $string ) {
	// Bail early if not a string.
	if ( ! is_string( $string ) ) {
		return $string;
	}

	// Replacements for the string.
	$replacements = array(
		'%%AUTHOR%%' => 'default',
	);

	// If we're on a single post get the author name.
	if ( is_single() ) {
		$replacements['%%AUTHOR%%'] = get_the_author_meta( 'nicename' );
	}

	/**
	 * Variables to replace in the string.
	 *
	 * Placeholders in a string to replace.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $replacements Replace variables in a string.
	 */
	$replacements = apply_filters( 'wds_acme_replacement_vars', $replacements );

	return strtr( $string, $replacements );
}

/**
 * Add noindex, nofollow meta tag to header on paginated archive pages.
 */
function wds_acme_archive_paginated_noindex( $robots ) {
	if ( is_paged() ) {
		$robots = false;
	}
	return $robots;
}

add_filter( 'wpseo_robots', 'wds_acme_archive_paginated_noindex' );
