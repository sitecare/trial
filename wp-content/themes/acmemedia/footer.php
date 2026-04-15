<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Acme Media
 */

?>

	</div><!-- #content -->

	<?php if ( is_active_sidebar( 'bottom-featured' ) ) : ?>
		<section id="below-content" class="below-content">
			<div class="wrap">
				<?php dynamic_sidebar( 'bottom-featured' ); ?>
			</div>
		</section>
	<?php endif; ?>

	<footer class="site-footer">
		<h2 class="screen-reader-text"><?php _e( 'Footer', 'acme' ); // WPCS: XSS ok. ?></h2>
		<div class="footer-top">
			<div class="wrap">
				<nav id="footer-navigation" class="footer-navigation">
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer',
							'menu_id'        => 'footer-menu',
							'menu_class'     => 'menu dropdown',
						) );
					?>
				</nav><!-- #footer-navigation -->

				<?php echo wds_acme_get_social_network_links(); // WPCS: XSS ok. ?>

				<div class="sitelist">
					<?php
						$sitelist_title = get_theme_mod( 'wds_acme_sitelist_title' );

						if ( $sitelist_title ) {
							echo '<h3>' . esc_html( $sitelist_title ) . '</h3>';
						}

						// Set arguments for toggleable menu.
						$toggle_args = array(
							'location' => 'top',
							'icon'     => array( 'icon' => 'down-carrot' ),
						);

						wds_acme_do_toggle_menu( $toggle_args );
					?>
				</div><!-- .sitelist -->
			</div><!-- .wrap -->
		</div><!-- . footer-top -->

		<div class="footer-bottom">
			<div class="wrap">
				<?php
				if ( is_active_sidebar( 'footer' ) ) {
					dynamic_sidebar( 'footer' );
				}
				?>

				<div class="footer-logos mobile-only">
					<?php

					wds_acme_do_footer_logo( 'wds_acme_footer_logo_1' );
					wds_acme_do_footer_logo( 'wds_acme_footer_logo_2' );

					?>
				</div>

				<?php
				// Footer copyright text from the customizer.
				$copyright = get_theme_mod( 'wds_acme_copyright_text' );

				printf( '<aside class="site-info">%s</aside>', $copyright );
				?>

				<aside class="sitelist">
					<?php
						if ( $sitelist_title ) {
							echo '<h3>' . esc_html( $sitelist_title ) . '</h3>';
						}

						// Add unique id to arguments, to prevent duplicate ids on the page.
						$toggle_args['menu_id'] = 'sites-menu-desktop';

						wds_acme_do_toggle_menu( $toggle_args );
					?>

					<div class="footer-logos">
						<?php

						wds_acme_do_footer_logo( 'wds_acme_footer_logo_1' );
						wds_acme_do_footer_logo( 'wds_acme_footer_logo_2' );

						?>
					</div><!-- .footer-logos -->
				</aside><!-- .sitelist -->
			</div><!-- .wrap -->
		</div><!-- .footer-bottom -->
	</footer><!-- .site-footer -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
