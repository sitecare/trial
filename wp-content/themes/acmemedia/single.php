<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Acme Media
 */

get_header(); ?>

	<div class="wrap">

		<article <?php post_class( '' ); ?> id="main">
			<?php while ( have_posts() ) : the_post(); ?>

			<header class="entry-header">
				<?php
				if ( is_single() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				endif;
				if ( 'post' === get_post_type() ) :

					// Print the custom excerpt, if there is one.
					if ( wds_acme_has_custom_excerpt() ) : ?>

						<blockquote class="entry-custom-excerpt">
							<?php wds_acme_the_custom_excerpt(); ?>
						</blockquote>

					<?php endif; ?>

				<?php
				endif; ?>
			</header><!-- .entry-header -->

			<div class="primary content-area">
				<main class="site-main" role="main">

				<?php
				// Add the post content.
				get_template_part( 'template-parts/content', get_post_format() );

				?>

				<div id="taboola-below-article-thumbnails"></div>

				<?php

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
				?>
					<div class="comment-wrapper picosignal">

					<?php 
						$show_comments = get_field('display_show_comments_button', 'option');

						if( $show_comments != 'no' ){ ?>
							<button id="toggle-comments" class="button button-primary comment-toggle" aria-expanded="false" data-target="comment_wrap" data-height="disqus_thread">
								<?php printf( '%1$s (%2$d)', __( 'Show Comments', 'acme' ), get_comments_number() ); // WPCS: XSS ok. ?>
							</button>
					<?php } ?>

					

					<div id="comment_wrap" class="comment-wrap <?php echo $show_comments == 'no' ? 'open' : '' ?>">
						<?php comments_template(); ?>
					</div>
				<?php endif; ?>

				<?php 
					// Get the author bio.
					wds_acme_do_authorbox( array( 'authorID' => get_the_author_meta( 'ID' ) ) );
				?>

				<?php if ( is_active_sidebar( 'below-posts' ) ) : ?>
					<aside id="below-posts" class="below-posts">
						<h2 class="screen-reader-text"><?php _e( 'Related Content', 'acme' ); // WPCS: XSS ok. ?></h2>
						<?php dynamic_sidebar( 'below-posts' ); ?>
					</aside>
				<?php endif; ?>

				<aside id="recent-posts" class="recent-posts mobile-only">
					<h2 class="screen-reader-text"><?php _e( 'Recent Posts', 'acme' ); // WPCS: XSS ok. ?></h2>
					<?php wds_acme_do_recent_posts( array( 'filter' => array( get_the_ID() ) ) ); ?>
				</aside>

				<?php
				// Add triplelift scripts.
				if ( function_exists( 'wds_acme_add_triplelift_scripts' ) ) :
					wds_acme_add_triplelift_scripts();
				endif; ?>

				</main><!-- #main -->
			</div><!-- .primary -->

			<?php endwhile; // End of the loop. ?>
			<?php get_sidebar(); ?>

			<aside id="recent-posts" class="recent-posts desktop-only">
				<h2 class="screen-reader-text"><?php _e( 'Recent Posts', 'acme' ); // WPCS: XSS ok. ?></h2>
				<?php wds_acme_do_recent_posts( array( 'filter' => array( get_the_ID() ) ) ); ?>
			</aside>

		</article>
	</div><!-- .wrap -->

<?php get_footer(); ?>
