<?php
/**
 * Clone Widget template for bottom featured "More Reading" posts.
 *
 * @package Acme Media
 */

$title = get_the_title();
$url = get_permalink();
$image = wds_acme_get_post_image_uri( 'single-post-featured' );

// Fallback to DB title if one isn't available.
if ( empty( $title ) && function_exists( 'wds_acme_get_post_title_from_database' ) ) {
	$title = wds_acme_get_post_title_from_database( get_the_ID() );
}
?>

<a href="<?php echo esc_url( $url ); ?>" class="image-as-background more-reading-article featured-post"
	<?php if ( has_post_thumbnail() ) : ?>style="background-image: url('<?php echo esc_url( $image ); ?>')"<?php endif; ?>>
		<h4><?php echo esc_html( $title ); ?></h4>
</a>
