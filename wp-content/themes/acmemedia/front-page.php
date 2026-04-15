<?php
/**
 * Template Name: Homepage
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Acme Media
 */

get_header(); ?>

	<?php echo function_exists( 'wds_acme_get_featured_grid' ) ? wds_acme_get_featured_grid() : ''; // WPCS: XSS ok. ?>

	<div class="wrap">

		<div class="primary content-area">
			<main id="main" class="site-main" role="main">

				<?php if ( is_active_sidebar( 'above-posts-ads' ) ) : ?>
					<section id="above-posts-ads" class="above-posts-ads">
							<?php dynamic_sidebar( 'above-posts-ads' ); ?>
					</section>
				<?php endif; ?>

				<?php
				// We need an ad area after 4 posts, start a counter for this here.
				$index = 1;

				while ( have_posts() ) : the_post();

					$template = wds_acme_get_post_template( get_the_ID() );

					get_template_part( $template, 'page' );

					// When the counter is at 5, and there are widgets in the ad area, display it.
					if ( 5 === $index ) {
						if ( is_active_sidebar( 'in-river-ads' ) ) : ?>
							<div id="in-river-ads" class="in-river-ads">
								<?php dynamic_sidebar( 'in-river-ads' ); ?>
							</div>
						<?php endif;
					}

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

					// Add triplelift scripts after the 3rd story.
					if ( ( 3 === $index ) && function_exists( 'wds_acme_add_triplelift_scripts' ) ) :
						wds_acme_add_triplelift_scripts();
					endif;

					++$index;

				endwhile; // End of the loop.

				wds_acme_do_the_posts_navigation();
				?>

			</main><!-- #main -->
		</div><!-- .primary -->

		<?php get_sidebar(); ?>

	</div><!-- .wrap -->

<?php get_footer(); ?>
