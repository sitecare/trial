<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Acme Media
 */

?>

	<?php the_post_thumbnail( 'single-post-featured', array( 'class' => 'single-post-featured-image' ) ); ?>
    <span class="image-caption"><?php the_post_thumbnail_caption(); ?></span>

	<div class="entry-meta">
		<?php wds_acme_posted_on(); ?>
	</div><!-- .entry-meta -->

	<div class="entry-content">
		<h2 aria-hidden="true" class="hidden"></h2><!-- Arbitrary hidden heading for SumoMe to hook into -->
		<?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'acme' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );

			$args = array(
				'before'            => '<nav class="page-links" aria-label="' . __( 'Post pagination', 'acme' ) . '"><span class="page-link-text">' . __( 'Pages: ', 'acme' ) . '</span>',
				'after'             => '</nav>',
				'link_before'       => '<span class="page-link">',
				'link_after'        => '</span>',
			);
			wp_link_pages( $args );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php wds_acme_entry_footer(); ?>
	</footer><!-- .entry-footer -->
