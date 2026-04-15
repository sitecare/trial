<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Acme Media
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside class="secondary widget-area" role="complementary">
	<h2 class="screen-reader-text"><?php _e( 'Sidebar', 'acme' ); // WPCS: XSS ok. ?></h2>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- .secondary -->
