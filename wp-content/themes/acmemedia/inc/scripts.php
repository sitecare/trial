<?php
/**
 * Custom scripts and styles.
 *
 * @package Acme Media
 */

// Define the theme version.
define( 'THEME_VER', 1 === preg_match( '/acme\.com$/', $_SERVER['HTTP_HOST'] ) ? wp_get_theme()->Version : time() );

/**
 * Enqueue scripts and styles.
 */
function wds_acme_scripts() {
	/**
	 * If WP is in script debug, or we pass ?script_debug in a URL - set debug to true.
	 */
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) || ( isset( $_GET['script_debug'] ) ) ? true : false;

	/**
	 * If we are debugging the site, use a unique version every page load so as to ensure no cache issues.
	 */
	$version = THEME_VER;

	/**
	 * Should we load minified files?
	 */
	$suffix = ( true === $debug ) ? '' : '.min';

	// Enqueue styles.
	wp_enqueue_style( 'acme-style', get_stylesheet_directory_uri() . '/public/temp.css', array(), $version );
	wp_enqueue_style( 'acme-mobile-menu', get_stylesheet_directory_uri() . '/mobile-menu.css', array(), $version );

	wp_add_inline_style( 'acme-style', '.fs-sticky-footer a:not(.button), .ad-widget a:not(.button) { border: none; }' );

	// Enqueue scripts.
	wp_enqueue_script( 'acme-scripts', get_template_directory_uri() . '/assets/scripts/project' . $suffix . '.js', array( 'jquery' ), $version, true );
	wp_enqueue_script( 'acme-typekit', 'https://use.typekit.net/nou3ovc.js', array(), '1.0' );
	wp_add_inline_script( 'acme-typekit', 'try{Typekit.load({ async: true });}catch(e){}' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Enqueue the mobile nav script
	// Since we're showing/hiding based on CSS and wp_is_mobile is wp_is_imperfect, enqueue this everywhere.
	wp_enqueue_script( 'acme-mobile-nav', get_template_directory_uri() . '/assets/scripts/mobile-nav-menu' . $suffix . '.js', array( 'jquery' ), $version, true );

	// add SumoMe JS.
	wp_enqueue_script(
		'sumome',
		'//load.sumome.com/',
		array(),
		null,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'wds_acme_scripts' );


function taboola_script() {

	if( is_single() ){
		echo '
			<script type="text/javascript">
				window._taboola = window._taboola || [];
				_taboola.push({
					mode: "alternating-thumbnails-a",
					container: "taboola-below-article-thumbnails",
					placement: "Below Article Thumbnails",
					target_type: "mix"
				});
				</script>

				<script type="text/javascript">
				window._taboola = window._taboola || [];
				_taboola.push({flush: true});
			</script>
		';
	}
    
}

// Add hook for front-end <head></head>
add_action( 'wp_head', 'taboola_script' );

/**
 * Filters the SumoMe script enqueue to add the side id to it.
 *
 * @param  string $tag    The script tag for the enqueue.
 * @param  string $handle The handle with which the script has been enqueued.
 * @param  string $src    The source URL for the script.
 *
 * @return string         A modified script tag.
 */
function wds_acme_async_sumome_script( $tag, $handle ) {

	// Don't do anything if this isn't the SumoMe script.
	if ( 'sumome' !== $handle ) {
		return $tag;
	}

	$sumome_siteid = get_theme_mod( 'wds_acme_sumome_id', false );

	// Bail if there is no id set. Returns the tag untouched.
	if ( ! $sumome_siteid ) {
		return $tag;
	}

	return str_replace( '></script>', ' data-sumo-site-id="' . esc_js( $sumome_siteid ) . '" async></script>', $tag );
}
add_filter( 'script_loader_tag', 'wds_acme_async_sumome_script', 10, 2 );

/**
 * Enqueue customizer preview JS.
 */
function wds_acme_customize_preview_js() {
	wp_enqueue_script( 'acme-customizer', get_template_directory_uri() . '/assets/scripts/customizer.js', array( 'jquery', 'customize-preview' ), '', true );
}
add_action( 'customize_preview_init', 'wds_acme_customize_preview_js' );

/**
 * Add SVG definitions to footer.
 */
function wds_acme_include_svg_icons() {

	// Define SVG sprite file.
	$svg_icons = get_template_directory() . '/assets/images/svg-icons.svg';

	// If it exists, include it.
	if ( file_exists( $svg_icons ) ) {
		require_once( $svg_icons );
	}
}
add_action( 'wp_footer', 'wds_acme_include_svg_icons', 9999 );
