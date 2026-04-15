<?php
/**
 * Popular Post Clone Widget Template.
 *
 * @package Acme Media
 */

$title = get_the_title();
$url = get_permalink();
$image = wds_acme_get_post_image_uri( 'sidebar-image' );

// Fallback to DB title if one isn't available.
if ( empty( $title ) && function_exists( 'wds_acme_get_post_title_from_database' ) ) {
	$title = wds_acme_get_post_title_from_database( get_the_ID() );
}

echo '<a href="' . esc_url( $url ) . '" class="image-as-background" style="background-image: url( ' . esc_url( $image ) . ' )">' . '<h4>' . esc_html( $title ) . '</h4>' . '</a>';
