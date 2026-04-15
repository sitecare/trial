<?php
/**
 * Template part for displaying a featured post.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Acme Media
 */

$categories = get_the_category();
?>

<article <?php post_class( 'featured-post image-as-background featured-cat-' . esc_html( $categories[0]->slug ) ); ?>
	<?php if ( has_post_thumbnail() ) : ?>style="background-image: url('<?php the_post_thumbnail_url( 'single-post-featured' ); ?>')"<?php endif; ?>
	>
    <span class="image-caption"><?php the_post_thumbnail_caption(); ?></span>
	<header class="entry-header">
		<div class="entry-meta">
			<p class="category "><?php echo esc_html( $categories[0]->name ); ?>
				<span class="hilight" <?php echo 'style="background-color: ' . esc_attr( get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] ) ) . '"' ?>></span>
			</p>
		</div>
		<?php
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		?>
	</header><!-- .entry-header -->
</article><!-- #post-## -->
