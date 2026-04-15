<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Acme Media
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'acme' ); ?></a>

	<?php if ( is_active_sidebar( 'top-ad-area' ) ) : ?>
		<section id="top-ad-section" class="top-ad-section">
			<div class="wrap">
				<?php dynamic_sidebar( 'top-ad-area' ); ?>
			</div>
		</section>
	<?php endif; ?>

	<header class="site-header">

			<div class="site-branding">
				<?php the_custom_logo(); ?>

				<?php if ( is_front_page() && is_home() ) : ?>
					<h1 class="screen-reader-text"><?php bloginfo( 'name' ); ?></h1>
				<?php else : ?>
					<p class="screen-reader-text"><?php bloginfo( 'name' ); ?></p>
				<?php endif; ?>
			</div><!-- .site-branding -->

			<?php get_search_form(); ?>

			<div class="site-navigation-wrap">
				<div class="wrap">
					<nav id="site-navigation" class="main-navigation">
						<?php
							wp_nav_menu( array(
								'theme_location' => 'primary',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'menu dropdown',
							) );
						?>
					</nav><!-- #site-navigation -->

					<?php echo wds_acme_get_mobile_navigation_menu(); // WPCS: XSS ok. ?>

					<?php echo wds_acme_get_social_network_links(); // WPCS: XSS ok. ?>
				</div>
			</div><!-- .site-navigation-wrap -->

	</header><!-- .site-header -->

	<div id="content" class="site-content">
