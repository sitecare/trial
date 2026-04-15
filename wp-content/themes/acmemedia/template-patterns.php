<?php
/**
 * Template Name: Patterns
 *
 * @package Acme Media
 */

/**
 * Build a pattern section.
 *
 * @param array $args The pattern defaults.
 * @return string The pattern documentation.
 * @author Greg Rickaby Carrie Forde
 */
function wds_acme_get_pattern_section( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'title'        => '',       // The pattern title.
		'description'  => '',       // The pattern description.
		'usage'        => '',       // The template tag or markup needed to display the pattern.
		'parameters'   => array(),  // Does the pattern have params? Like $args?
		'arguments'    => array(),  // If the pattern has params, what are the $args?
		'output'       => '',       // Use the template tag or pattern HTML markup here. It will be sanitized displayed.
	);

	// Parse arguments.
	$args = wp_parse_args( $args, $defaults );

	// Add additional HTML tags to the wp_kses() allowed html filter.
	$allowed_tags = array_merge( wp_kses_allowed_html( 'post' ), array(
		'svg' => array(
			'aria-hidden' => true,
			'class'       => true,
			'id'          => true,
			'role'        => true,
			'title'       => true,
		),
		'use' => array(
			'xlink:href' => true,
		),
	) );

	ob_start();

	?>

	<section class="pattern-section">

		<?php if ( $args['title'] ) : ?>
		<header class="pattern-section-header">
			<h2 class="pattern-section-title"><?php echo esc_html( $args['title'] ); ?></h2>
		</header><!-- .pattern-section-header -->
		<?php endif; ?>

		<div class="pattern-section-content">

			<div class="pattern-section-live">

			<?php if ( $args['output'] ) : ?>
				<?php echo wp_kses( $args['output'], $allowed_tags ); ?>
			<?php endif; ?>

			</div><!-- .pattern-section-live -->

			<div class="pattern-section-details">

			<?php if ( $args['description'] ) : ?>
				<p><strong><?php esc_html_e( 'Description', 'acme' ); ?>:</strong></p>
				<p class="pattern-section-description"><?php echo esc_html( $args['description'] ); ?></p>
			<?php endif; ?>

			<?php if ( $args['parameters'] ) : ?>
				<p><strong><?php esc_html_e( 'Parameters', 'acme' ); ?>:</strong></p>
				<?php foreach ( $args['parameters'] as $key => $value ) : ?>
					<p><code><?php echo esc_html( $key ); ?></code> <?php echo esc_html( $value ); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ( $args['arguments'] ) : ?>
				<p><strong><?php esc_html_e( 'Arguments', 'acme' ); ?>:</strong></p>
				<?php foreach ( $args['arguments'] as $key => $value ) : ?>
					<p><code><?php echo esc_html( $key ); ?></code> <?php echo esc_html( $value ); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>

			</div><!-- .pattern-section-details -->

			<div class="pattern-section-usage">

			<?php if ( $args['usage'] ) : ?>
				<p><strong><?php esc_html_e( 'Usage', 'acme' ); ?>:</strong></p>
				<pre><?php echo esc_html( $args['usage'] ); ?></pre>
			<?php endif; ?>

			<?php if ( $args['output'] ) : ?>
				<p><strong><?php esc_html_e( 'HTML Output', 'acme' ); ?>:</strong></p>
				<pre><?php echo esc_html( $args['output'] ); ?></pre>
			<?php endif; ?>

			</div><!-- .pattern-section-usage -->
		</div><!-- .pattern-section-content -->
	</section><!-- .pattern-section -->
	<?php
	return ob_get_clean();
}

/**
 * Build a global pattern element.
 *
 * @param array $args The array of colors or fonts.
 * @return string The pattern documentation.
 * @author Carrie Forde
 */
function wds_acme_get_global_pattern_section( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'global_type' => '',      // Can be 'colors' or 'fonts'.
		'title'       => '',      // Give the section a title.
		'arguments'   => array(), // Use key => value pairs to pass colors or fonts.
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	ob_start(); ?>

	<section class="pattern-section">
		<header class="pattern-section-header">
			<h2 class="pattern-section-title"><?php echo esc_html( $args['title'] ); ?></h2>
		</header>

		<div class="pattern-section-content">

			<?php // We'll alter the output slightly depending upon the global type.
			switch ( $args['global_type'] ) :

				case 'colors' : ?>

					<div class="swatch-container">

					<?php // Grab the array of colors.
					$colors = $args['arguments'];

					foreach ( $colors as $name => $hex ) :
						$color_var = '$color-' . str_replace( ' ', '-', strtolower( $name ) ); ?>

						<div class="swatch" style="background-color: <?php echo esc_attr( $hex ); ?>;">
							<header><?php echo esc_html( $name ); ?></header>
							<footer><?php echo esc_html( $color_var ); ?></footer>
						</div><!-- .swatch -->

					<?php endforeach; ?>
					</div>
					<?php break;

				case 'fonts' : ?>

					<div class="font-container">

					<?php // Grab the array of fonts.
					$fonts = $args['arguments'];

					foreach ( $fonts as $name => $family ) :
						$font_var = '$font-' . str_replace( ' ', '-', strtolower( $name ) ); ?>

						<p><strong><?php echo esc_html( $font_var ); ?>:</strong> <span style="font-family: <?php echo esc_attr( $family ); ?>"><?php echo esc_html( $family ); ?></span></p>
					<?php endforeach; ?>
					</div>
					<?php break; ?>
			<?php endswitch; ?>
		</div>
	</section>

	<?php return ob_get_clean();
}

// Start Template Partterns.
get_header(); ?>

	<div class="wrap">
		<div class="primary content-area">
			<main id="main" class="site-main" role="main">

				<?php
				/**
				 * Possible patterns baked in with wd_s...
				 *
				 * Colors
				 * Buttons
				 * Input
				 * Dropdown
				 * Fonts
				 * Search Form
				 * Hero
				 * Cards
				 * Modal
				 * Imitate a Gravity Form??
				 * SVG Icon
				 */

				/**
				 * Colors.
				 */
				echo wds_acme_get_global_pattern_section( array( // WPCS: XSS OK.
					'global_type' => 'colors',
					'title'       => 'Colors',
					'arguments'   => array(
						'Buttercup'                    => '#f4b225',
						'Abbey' 				       => '#595c62',
						'Mako'        				   => '#3f4147',
						'Shark'        			 	   => '#2c2d31',
						'Dusk'          			   => '#1e1e22',
						'Slate'                        => '#2c2c31',
						'Sand' 				           => '#f5f6f6',
						'Mako'        				   => '#3f4147',
						'Mid Gray'        		       => '#595b61',
						'Sidebar'          	           => '#f7f7f7',
						'Black'        			 	   => '#000000',
						'White'          			   => '#ffffff',
					),
				) );

				/**
				 * Category Colors.
				 */
				echo wds_acme_get_global_pattern_section( array( // WPCS: XSS OK.
					'global_type' => 'colors',
					'title'       => 'Category Colors',
					'arguments'   => array(
						'NBA'                                => '#e25b00',
						'NFL' 				                 => '#000000',
						'MLB'        				         => '#006732',
						'NCAA'        			 	         => '#d10d00',
						'NHL'          				         => '#8bc0eb',
						'Pop Culture'                        => '#deca00',
					),
				) );

				/**
				 * Fonts.
				 */
				echo wds_acme_get_global_pattern_section( array( // WPCS: XSS OK.
					'global_type'  => 'fonts',
					'title'        => 'Fonts',
					'arguments'    => array(
						'Sans'  => '"freight-sans-pro", sans-serif',
						'Serif' => '"ff-tisa-web-pro", Georgia, serif',
						'Code'  => 'Monaco, Consolas, "Andale Mono", "DejaVu Sans Mono", monospace',
						'Pre'   => '"Courier 10 Pitch", Courier, monospace',
					),
				) );

				/**
				 * H1.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'H1',
					'description' => 'Display an H1',
					'usage'       => '<h1>This is a headline</h1> or <div class="h1">This is a headline</div>',
					'output'      => '<h1>This is a headline one</h1>',
				) );

				/**
				 * H2.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'H2',
					'description' => 'Display an H2',
					'usage'       => '<h2>This is a headline</h2> or <div class="h2">This is a headline</div>',
					'output'      => '<h2>This is a headline two</h2>',
				) );

				/**
				 * H3.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'H3',
					'description' => 'Display an H3',
					'usage'       => '<h3>This is a headline</h3> or <div class="h3">This is a headline</div>',
					'output'      => '<h3>This is a headline three</h3>',
				) );

				/**
				 * H4.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'H4',
					'description' => 'Display an H4',
					'usage'       => '<h4>This is a headline</h4> or <div class="h4">This is a headline</div>',
					'output'      => '<h4>This is a headline four</h4>',
				) );

				/**
				 * H5.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'H5',
					'description' => 'Display an H5',
					'usage'       => '<h5>This is a headline</h5> or <div class="h5">This is a headline</div>',
					'output'      => '<h5>This is a headline five</h5>',
				) );

				/**
				 * H6.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'H6',
					'description' => 'Display an H6',
					'usage'       => '<h6>This is a headline</h6> or <div class="h6">This is a headline</div>',
					'output'      => '<h6>This is a headline six</h6>',
				) );

				/**
				 * SVGs.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'SVG',
					'description' => 'Display inline SVGs.',
					'usage'       => '<?php wds_acme_get_svg( array( \'icon\' => \'facebook-square\' ) ); ?>',
					'parameters'  => array(
						'$args' => '(required) Configuration arguments.',
					),
					'arguments'    => array(
						'icon'  => '(required) The SVG icon file name. Default none',
						'title' => '(optional) The title of the icon. Default: none',
						'desc'  => '(optional) The description of the icon. Default: none',
					),
					'output'       => wds_acme_get_svg( array( 'icon' => 'facebook-square' ) ),
				) );

				/**
				 * Button.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'Button',
					'description' => 'Display a button.',
					'usage'       => '<button class="button" href="#">Click Me</button>',
					'output'      => '<button class="button">Click Me</button>',
				) );

				/**
				 * Button Secondary.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'Button Secondary',
					'description' => 'Display a secondary button.',
					'usage'       => '<button class="button-secondary" href="#">Click Me</button>',
					'output'      => '<button class="button-secondary">Click Me</button>',
				) );

				/**
				 * Button Outline.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'Button Outline',
					'description' => 'Display an outline button.',
					'usage'       => '<button class="button-outline" href="#">Click Me</button>',
					'output'      => '<button class="button-outline">Click Me</button>',
				) );

				/**
				 * Search Form.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'Search Form',
					'description' => 'Display the search form.',
					'usage'       => '<?php get_search_form(); ?>',
					'output'      => get_search_form(),
				) );

				/**
				 * Category Labels.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'Category Labels',
					'description' => 'Display the Category Labels.',
					'usage'       => '<?php get_the_category(); ?>',
					'output'      => '<span class="cat-links"><a href="http://acme.wdslab.com/blog/category/mlb/" rel="category tag">MLB</a></span>',
				) );

				/**
				 * SVG Icons.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'SVG Icons',
					'description' => 'Display the SVG Icons.',
					'usage'       => '<?php wds_acme_do_svg_icons(); ?>',
					'output'      => wds_acme_do_svg_icons(),
				) );

				/**
				 * Featured Posts.
				 */
				echo wds_acme_get_pattern_section( array( // WPCS: XSS OK.
					'title'       => 'Featured Posts',
					'description' => 'Display the Featured Posts.',
					'usage'       => '<?php  wds_acme_get_featured_posts(); ?>',
					'output'      =>  wds_acme_get_featured_posts(),
				) );

				?>

			</main><!-- #main -->
		</div><!-- .primary -->
	</div><!-- .wrap -->

<?php get_footer(); ?>
