<?php
/**
 * The template for displaying the search form.
 *
 * @package Acme Media
 */

?>

<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-field" class="screen-reader-text"><?php esc_html_e( 'To search this site, enter a search term', 'acme' ) ?></label>
	<input class="search-field" id="search-field" type="text" name="s" value="<?php echo get_search_query() ?>" aria-required="false" autocomplete="off" placeholder="<?php echo esc_attr_x( 'Search...', 'acme' ) ?>" />
	<button>
		<span class="screen-reader-text"><?php esc_html_e( 'Submit search form', 'acme' ); ?></span>
		<?php echo wds_acme_get_svg( array( 'icon' => 'search' ) ); // WPCS: XSS ok. ?>
	</button>
</form>
