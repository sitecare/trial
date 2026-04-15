<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Acme Media
 */

get_header(); ?>

	<div class="wrap">
		<div class="content-area">
			<main id="main" class="site-main" role="main">

				<section class="error-404 not-found">
					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Sorry, this page doesn\'t exist.', 'acme' ); ?></h1>
					</header><!-- .page-header -->

					<div class="page-content">
						<p>
							<?php
								printf( esc_html__( 'You asked for %s, but despite our computers looking very hard, we could not find it. What happened?', 'acme' ),
												'<strong>' . esc_html( $_SERVER['REQUEST_URI'] ) . '</strong>' );
							?>
						</p>
						<ul>
							<li><?php esc_html_e( 'the link you clicked to arrive here has a typo in it' , 'acme' ); ?></li>
							<li><?php esc_html_e( 'or somehow we removed that page, or gave it another name' , 'acme' ); ?></li>
							<li><?php esc_html_e( 'or, quite unlikely for sure, maybe you typed it yourself and there was a little mistake?' , 'acme' ); ?></li>
						</ul>

						<p>
							<?php esc_html_e( 'Perhaps searching can help get you on your way.', 'acme' ); ?>
						</p>

						<?php get_search_form(); ?>

					</div><!-- .page-content -->
				</section><!-- .error-404 -->

			</main><!-- #main -->
		</div><!-- .primary -->

	</div><!-- .wrap -->

<?php get_footer(); ?>
