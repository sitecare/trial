<?php
/**
 * Acme Media functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Acme Media
 */

if ( ! function_exists( 'wds_acme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function wds_acme_setup() {
		/**
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Acme Media, use a find and replace
		 * to change 'acme' to the name of your theme in all the template files.
		 * You will also need to update the Gulpfile with the new text domain
		 * and matching destination POT file.
		 */
		load_theme_textdomain( 'acme', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// Featured 5 Top Left spot.
		add_image_size( 'featured-five-top-left', 445, 280, array( 'center', 'center' ) );

		// Featured 5 Top Right spot.
		add_image_size( 'featured-five-top-right', 343, 280, array( 'center', 'center' ) );

		// Featured 5 Bottom Left spot.
		add_image_size( 'featured-five-bottom-left', 343, 207, array( 'center', 'center' ) );

		// Featured 5 Bottom Middle AND Right spot.
		add_image_size( 'featured-five-bottom-middle-right', 217, 207, array( 'center', 'center' ) );

		// Horizontal Blog Card.
		add_image_size( 'blog-card', 399, 287, array( 'center', 'center' ) );

		// Single Post Featured Image.
		add_image_size( 'single-post-featured', 832, 447, array( 'center', 'center' ) );

		// Any image inside the sidebar.
		add_image_size( 'sidebar-image', 600, 400, array( 'center', 'center' ) );

		// The More Reading section of posts at the end of single posts.
		add_image_size( 'more-reading-image', 380, 207, array( 'center', 'center' ) );

		// After-Post Link Box.
		add_image_size( 'link-box', 380, 278, array( 'center', 'center' ) );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu', 'acme' ),
			'mobile'  => esc_html__( 'Optional Mobile Menu', 'acme' ),
			'footer'  => esc_html__( 'Footer Menu', 'acme' ),
			'sites'   => esc_html__( 'Sites Menu', 'acme' ),
		) );

		/**
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'wds_acme_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Enable WordPress core custom logo support.
		add_theme_support( 'custom-logo', apply_filters( 'wds_acme_custom_logo_args', array(
			'height'      => 132,
			'width'       => 220,
			'flex-height' => true,
			'flex-width'  => true,
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
endif; // wds_acme_setup.
add_action( 'after_setup_theme', 'wds_acme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wds_acme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wds_acme_content_width', 832 );
}
add_action( 'after_setup_theme', 'wds_acme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wds_acme_widgets_init() {

	// Define sidebars.
	$sidebars = array(
		'sidebar-1'          => esc_html__( 'Sidebar 1', 'acme' ),
		'footer'             => esc_html__( 'Footer', 'acme' ),
		'bottom-featured'    => esc_html__( 'Bottom Featured', 'acme' ),
		'top-ad-area'        => esc_html__( 'Page Top Ad Area', 'acme' ),
		'in-river-ads'       => esc_html__( 'Between Posts Ad Area', 'acme' ),
		'above-posts-ads'    => esc_html__( 'Above Posts Ad Area', 'acme' ),
		'below-posts'      => esc_html__( 'Below Post Content', 'acme' ),
	);

	// Loop through each sidebar and register.
	foreach ( $sidebars as $sidebar_id => $sidebar_name ) {
		register_sidebar( array(
			'name'          => $sidebar_name,
			'id'            => $sidebar_id,
			'description'   => sprintf( esc_html__( 'Widget area for %s', 'acme' ), $sidebar_name ),
			'before_widget' => '<aside class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

}
add_action( 'widgets_init', 'wds_acme_widgets_init' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load styles and scripts.
 */
require get_template_directory() . '/inc/scripts.php';

/**
 * Custom function to add 'featured' article toggle
 */
require get_template_directory() . '/inc/feature-article-toggle.php';

/**
 * Widget mobile classes.
 */
require get_template_directory() . '/inc/widget-mobile-class.php';

// Make sure ACF is installed+active.
if ( function_exists( 'acf_add_options_page' ) ) {
	// Add ACF options page.
	acf_add_options_page();	
}
