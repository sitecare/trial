<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Acme Media
 */

if ( ! function_exists( 'wds_acme_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time, author, and category.
	 */
	function wds_acme_posted_on() {

		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'acme' ) );

			/* Displays the Category name in a label with custom ACF color */
			// echo wds_acme_get_category_label(); // WPCS: XSS OK.
		}

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$date_format = get_option( 'date_format' );

		$time_string = sprintf( $time_string,
			esc_attr( date_i18n( $date_format, strtotime( get_the_date( 'c' ) ) ) ),
			esc_html( date_i18n( $date_format, strtotime( get_the_date() ) ) ),
			esc_attr( date_i18n( $date_format, strtotime( get_the_modified_date( 'c' ) ) ) ),
			esc_html( date_i18n( $date_format, strtotime( get_the_modified_date( 'c' ) ) ) )
		);

		$posted_on = sprintf(
			esc_html_x( 'on %s', 'post date', 'acme' ),
			$time_string
		);

		if ( ! wds_acme_has_author_alias() ) {
			$author = get_the_author();
		} else {
			$author = '<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a>';
		}

		$byline = sprintf(
			esc_html_x( 'By %s', 'post author', 'acme' ),
			'<span class="author vcard">' . $author . '</span>'
		);

		echo '<span class="byline">' . $byline . ' </span><span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.
	}
endif;

if ( ! function_exists( 'wds_acme_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the comments.
	 */
	function wds_acme_entry_footer() {

		if ( is_singular( 'post' ) ) {
			// Display post tags.
			wds_acme_display_tags();
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link( esc_html__( 'Leave a comment', 'acme' ), esc_html__( '1 Comment', 'acme' ), esc_html__( '% Comments', 'acme' ) );
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', 'acme' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

/**
 * Return SVG markup.
 *
 * @param array $args {
 *     Parameters needed to display an SVG.
 *
 *     String $icon Required. Use the icon filename, e.g. "facebook-square".
 *     String $title Optional. SVG title, e.g. "Facebook".
 *     String $desc Optional. SVG description, e.g. "Share this post on Facebook".
 * }.
 * @return string SVG markup.
 */
function wds_acme_get_svg( $args = array() ) {

	// Make sure $args are an array.
	if ( empty( $args ) ) {
		return esc_html__( 'Please define default parameters in the form of an array.', 'acme' );
	}

	// Define an icon.
	if ( false === array_key_exists( 'icon', $args ) ) {
		return esc_html__( 'Please define an SVG icon filename.', 'acme' );
	}

	// Set defaults.
	$defaults = array(
		'icon'  => '',
		'title' => '',
		'desc'  => '',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Figure out which title to use.
	$title = ( $args['title'] ) ? $args['title'] : $args['icon'];

	// Set aria hidden.
	$aria_hidden = ' aria-hidden="true"';

	// Set ARIA.
	$aria_labelledby = '';
	if ( $args['title'] && $args['desc'] ) {
		$aria_labelledby = ' aria-labelledby="title-ID desc-ID"';
		$aria_hidden = '';
	}

	// Begin SVG markup.
	$svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

	// Add title markup.
	$svg .= '<title>' . esc_html( $title ) . '</title>';

	// If there is a description, display it.
	if ( $args['desc'] ) {
		$svg .= '<desc>' . esc_html( $args['desc'] ) . '</desc>';
	}

	// Use absolute path in the Customizer so that icons show up in there.
	if ( is_customize_preview() ) {
		$svg .= '<use xlink:href="' . get_parent_theme_file_uri( '/assets/images/svg-icons.svg#icon-' . esc_html( $args['icon'] ) ) . '"></use>';
	} else {
		$svg .= '<use xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use>';
	}

	$svg .= '</svg>';

	return $svg;
}

/**
 * Trim the title length.
 *
 * @param array $args Parameters include length and more.
 * @return string        The shortened excerpt.
 */
function wds_acme_get_the_title( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'length'  => 12,
		'more'    => '...',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Trim the title.
	return wp_trim_words( get_the_title( get_the_ID() ), $args['length'], $args['more'] );
}

/**
 * Customize "Read More" string on <!-- more --> with the_content();
 */
function wds_acme_content_more_link() {
	return ' <a class="more-link" href="' . get_permalink() . '">' . esc_html__( 'Read More', 'acme' ) . '...</a>';
}
add_filter( 'the_content_more_link', 'wds_acme_content_more_link' );

/**
 * Customize the [...] on the_excerpt();
 *
 * @param string $more The current $more string.
 * @return string Replace with "Read More..."
 */
function wds_acme_excerpt_more( $more ) {
	return sprintf( ' <a class="more-link" href="%1$s">%2$s</a>', get_permalink( get_the_ID() ), esc_html__( 'Read more...', 'acme' ) );
}
add_filter( 'excerpt_more', 'wds_acme_excerpt_more' );

/**
 * Limit the excerpt length.
 *
 * @param array $args Parameters include length and more.
 * @return string The shortened excerpt.
 */
function wds_acme_get_the_excerpt( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'length' => 20,
		'more'   => '...',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	if ( wds_acme_has_custom_excerpt() ) {
		$excerpt = wds_acme_the_custom_excerpt( false, false );
	} else {
		$excerpt = wp_trim_words( get_the_excerpt(), absint( $args['length'] ), esc_html( $args['more'] ) );
	}

	// Trim the excerpt.
	return $excerpt;
}

/**
 * Echo an image, no matter what.
 *
 * @param string $size The image size you want to display.
 */
function wds_acme_get_post_image( $size = 'thumbnail' ) {

	// If featured image is present, use that.
	if ( has_post_thumbnail() ) {
		return the_post_thumbnail( $size );
	}

	// Check for any attached image.
	$media = get_attached_media( 'image', get_the_ID() );
	$media = current( $media );

	// Set up default image path.
	$media_url = get_stylesheet_directory_uri() . '/assets/images/placeholder.png';

	// If an image is present, then use it.
	if ( is_array( $media ) && 0 < count( $media ) ) {
		$media_url = ( 'thumbnail' === $size ) ? wp_get_attachment_thumb_url( $media->ID ) : wp_get_attachment_url( $media->ID );
	}

	// Start the markup.
	ob_start(); ?>

	<img src="<?php echo esc_url( $media_url ); ?>" class="attachment-thumbnail wp-post-image" alt="<?php echo esc_html( get_the_title() ); ?>"/>

	<?php
	return ob_get_clean();
}

/**
 * Return an image URI, no matter what.
 *
 * @param  string $size The image size you want to return.
 * @return string The image URI.
 */
function wds_acme_get_post_image_uri( $size = 'thumbnail' ) {

	// If featured image is present, use that.
	if ( has_post_thumbnail() ) {

		$featured_image_id = get_post_thumbnail_id( get_the_ID() );
		$media = wp_get_attachment_image_src( $featured_image_id, $size );

		if ( is_array( $media ) ) {
			return current( $media );
		}
	}

	// Check for any attached image.
	$media = get_attached_media( 'image', get_the_ID() );
	$media = current( $media );

	// Set up default image path.
	$media_url = get_stylesheet_directory_uri() . '/assets/images/placeholder.png';

	// If an image is present, then use it.
	if ( is_array( $media ) && 0 < count( $media ) ) {
		$media_url = ( 'thumbnail' === $size ) ? wp_get_attachment_thumb_url( $media->ID ) : wp_get_attachment_url( $media->ID );
	}

	return $media_url;
}

/**
 * Echo the copyright text saved in the Customizer.
 */
function wds_acme_get_copyright_text() {

	// Grab our customizer settings.
	$copyright_text = get_theme_mod( 'wds_acme_copyright_text' );

	// Stop if there's nothing to display.
	if ( ! $copyright_text ) {
		return false;
	}

	// Echo the text.
	echo '<span class="copyright-text">' . wp_kses_post( $copyright_text ) . '</span>';
}

/**
 * Build social sharing icons.
 *
 * @return string
 */
function wds_acme_get_social_share() {

	// Build the sharing URLs.
	$twitter_url  = 'https://twitter.com/share?text=' . rawurlencode( html_entity_decode( get_the_title() ) ) . '&amp;url=' . rawurlencode( get_the_permalink() );
	$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_the_permalink() );
	$linkedin_url = 'https://www.linkedin.com/shareArticle?title=' . rawurlencode( html_entity_decode( get_the_title() ) ) . '&amp;url=' . rawurlencode( get_the_permalink() );

	// Start the markup.
	ob_start(); ?>
	<div class="social-share">
		<h5 class="social-share-title"><?php esc_html_e( 'Share This', 'acme' ); ?></h5>
		<ul class="social-icons menu menu-horizontal">
			<li class="social-icon">
				<a href="<?php echo esc_url( $twitter_url ); ?>" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=600, height=300' ); return false;">
					<?php echo wds_acme_get_svg( array( 'icon' => 'twitter-square', 'title' => 'Twitter', 'desc' => __( 'Share on Twitter', 'acme' ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Share on Twitter', 'acme' ); ?></span>
				</a>
			</li>
			<li class="social-icon">
				<a href="<?php echo esc_url( $facebook_url ); ?>" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=600, height=300' ); return false;">
					<?php echo wds_acme_get_svg( array( 'icon' => 'facebook-square', 'title' => 'Facebook', 'desc' => __( 'Share on Facebook', 'acme' ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Share on Facebook', 'acme' ); ?></span>
				</a>
			</li>
			<li class="social-icon">
				<a href="<?php echo esc_url( $linkedin_url ); ?>" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=475, height=505' ); return false;">
					<?php echo wds_acme_get_svg( array( 'icon' => 'linkedin-square', 'title' => 'LinkedIn', 'desc' => __( 'Share on LinkedIn', 'acme' ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Share on LinkedIn', 'acme' ); ?></span>
				</a>
			</li>
		</ul>
	</div><!-- .social-share -->

	<?php
	return ob_get_clean();
}

/**
 * Output the mobile navigation
 */
function wds_acme_get_mobile_navigation_menu() {

	// Figure out which menu we're pulling.
	$mobile_menu = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';

	// Start the markup.
	ob_start();
	?>

	<nav id="mobile-nav-menu" class="mobile-nav-menu">
		<button class="close-mobile-menu"><span class="screen-reader-text"><?php echo esc_html_e( 'Close menu', 'acme' ); ?></span><?php echo wds_acme_get_svg( array( 'icon' => 'close' ) ); // WPCS: XSS ok. ?></button>
		<?php
			wp_nav_menu( array(
				'theme_location' => $mobile_menu,
				'menu_id'        => 'mobile-menu',
				'menu_class'     => 'menu dropdown mobile-nav',
				'link_before'    => '<span>',
				'link_after'     => '</span>',
			) );
		?>
	</nav>
	<?php
	return ob_get_clean();
}

/**
 * Retrieve the social links saved in the customizer
 *
 * @return mixed HTML output of social links
 */
function wds_acme_get_social_network_links() {

	// Create an array of our social links for ease of setup.
	// Change the order of the networks in this array to change the output order.
	$social_networks = array( 'twitter', 'facebook', 'googleplus', 'instagram', 'linkedin' );

	// Kickoff our output buffer.
	ob_start(); ?>

	<ul class="social-icons">
	<?php
	// Loop through our network array.
	foreach ( $social_networks as $network ) :

		// Look for the social network's URL.
		$network_url = get_theme_mod( 'wds_acme_' . $network . '_url' );

		// Only display the list item if a URL is set.
		if ( isset( $network_url ) && ! empty( $network_url ) ) : ?>
			<li class="social-icon <?php echo esc_attr( $network ); ?>">
				<a href="<?php echo esc_url( $network_url ); ?>">
					<?php echo wds_acme_get_svg( array( 'icon' => $network, 'title' => sprintf( __( 'Link to %s', 'acme' ), ucwords( esc_html( $network ) ) ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php echo sprintf( __( 'Link to %s', 'acme' ), ucwords( esc_html( $network ) ) ); // WPCS: XSS ok. ?></span>
				</a>
			</li><!-- .social-icon -->
		<?php endif;
	endforeach; ?>

		<li class="social-icon rss">
			<a href="<?php echo bloginfo( 'rss2_url' ); ?>">
				<?php echo wds_acme_get_svg( array( 'icon' => 'rss', 'title' => __( 'Link to RSS', 'acme' ) ) ); // WPCS: XSS ok. ?>
				<span class="screen-reader-text"><?php echo __( 'Link to RSS', 'acme' ); // WPCS: XSS ok. ?></span>
			</a>
		</li>
	</ul><!-- .social-icons -->

	<?php
	return ob_get_clean();
}

/**
 * Generates HTML for a fancier looking select box. Will bail if no label or options are passed to it.
 *
 * @param  array $args Array of arguments to set the field's label, name, and options.
 * @return Mixed HTML  The HTML that consists of a label and the fancy select box.
 */
function wds_acme_get_fancy_selectbox( $args = array() ) {

	// Set default args.
	// label: Text for the label, should be translatable. Also used as the ID for the field.
	// name: The text for the name attribute on the field.
	// options: A key/value array that will generate the options.
	// icon: An array like you would pass into the get_svg() function.
	$defaults = array(
		'label'   => '',
		'name'    => 'dropdown',
		'options' => array(),
		'icon'    => array(
			'icon' => 'down-carrot',
		),
	);

	$args = wp_parse_args( $args, $defaults );

	// Bail if there is no label or if there are no options.
	if ( '' === $args['label'] || 0 === count( $args['options'] ) ) {
		return '';
	}

	// Start our output.
	ob_start(); ?>

	<label for="<?php echo esc_attr( strtolower( $args['label'] ) ); ?>">
		<?php echo esc_html( $args['label'] ); ?>
	</label>
	<div class="fancy-selectbox">
		<select id="<?php echo esc_attr( strtolower( $args['label'] ) ); ?>" name="<?php echo esc_attr( strtolower( $args['name'] ) ); ?>">
			<?php foreach ( $args['options'] as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $key ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php echo wds_acme_get_svg( $args['icon'] ); // WPCS: XSS ok. ?>
	</div>

	<?php
	return ob_get_clean();
}

/**
 * Immediately prints the fancy selectbox generated by wds_acme_get_fancy_selectbox().
 *
 * @param  array $args Array of arguments to set the field's label, name, and options.
 * @return void
 */
function wds_acme_do_fancy_selectbox( $args = array() ) {
	echo wds_acme_get_fancy_selectbox( $args ); // WPCS: XSS ok.
}

/**
 * Get an content card.
 *
 * @param array $args Content card defaults.
 * @return string     Content card markup.
 */
function wds_acme_get_content_card( $args = array() ) {

	// Setup defaults.
	$defaults = array(
		'image'         => '',
		'title'         => '',
		'title_length'  => 12,
		'content'       => '',
		'link'          => '',
		'date'          => '',
		'comments'		=> '',
		'comments-link' => '',
		'category'      => '',
		'category_link' => '',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );
	$long_title = false;

	if ( $args['title_length'] < str_word_count( $args['title'] ) ) {
		$long_title = true;
	}

	ob_start();
	?>

	<?php if ( ! is_single() ) : ?>
		<article <?php post_class( 'horizontal-blog-card', get_the_ID() ); ?> <?php echo 'style="border-top-color: ' . esc_attr( get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] ) ) . '"' ?>>
	<?php else : ?>
		<div <?php post_class( 'horizontal-blog-card', get_the_ID() ); ?> <?php echo 'style="border-top-color: ' . esc_attr( get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] ) ) . '"' ?>>
	<?php endif ; ?>

<!--	<a aria-hidden="true" tabindex="-1" role="presentation" href="--><?php //echo esc_url( $args['link'] ); ?><!--" class="card-image image-as-background" -->
<!--	   style="background-image: url(--><?php //echo esc_url( wds_acme_get_post_image_uri( 'blog-card' ) ); ?><!--); border-bottom-color: --><?php //echo esc_attr( get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] ) ) ?><!--"></a>-->

		<a aria-hidden="true" tabindex="-1" role="presentation" href="<?php echo esc_url( $args['link'] ); ?>" class="card-image image-as-background" style="border-bottom-color: <?php echo esc_attr( get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] ) ) ?>">
			<?php the_post_thumbnail( 'full' ); ?>
		</a>

		<div class="card-contents" <?php echo 'style="border-top-color: ' . esc_attr( get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] ) ) . '"' ?>>

			<header class="card-title">
				<?php if ( 'post' === get_post_type() && true === get_option( 'card_categories' ) ) : ?>

				<span class="cat-links">
					<a href="<?php echo esc_url( $args['category_link'] ); ?>"><?php echo esc_html( $args['category'] ); ?></a>
				</span>

				<?php endif; ?>

				<?php
					if ( ! is_single() ) {
						$headings = '<h2 class="entry-title">%s</h2>';
					} else {
						$headings = '<h3 class="entry-title">%s</h3>';
					}

					$title = ( is_search() ) ? wds_acme_get_highlighted_content( array( 'content' => get_the_title() ) ) : esc_html( $args['title'] );
					$link = sprintf( '<a href="%s">%s</a>', $args['link'], $title );

					printf( $headings, $link ); // WPCS: XSS ok.
				?>

			</header>

			<section class="card-excerpt<?php if ( $long_title ) : ?> long-title<?php endif; ?>">
				<p><?php echo $args['content']; // WPCS: XSS ok. ?></p>
			</section>

			<?php if ( 'post' === get_post_type() ) : ?>
			<footer>
					<span class="byline"><?php echo esc_html( 'By ' ); ?>

					<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ) ); ?>">
						<?php echo get_the_author(); ?>
					</a>

					<?php echo esc_html( ' On' ); ?><time class="entry-date"><?php echo esc_attr( $args['date'] ); ?></time> <em class="comments-number"><?php echo esc_html( $args['comments'] ); ?></em></span>
			</footer>
			<?php endif; ?>

		</div><!-- .card-contents -->

	<?php if ( ! is_single() ) : ?>
		</article><!-- .horizontal-blog-card -->
	<?php else : ?>
		</div><!-- .horizontal-blog-card -->
	<?php endif ; ?>

	<?php
	return ob_get_clean();
}

/**
 * Prints all the available SVG icons in the theme.
 *
 * String SVG markup
 */
function wds_acme_do_svg_icons() {
	$svg_path = TEMPLATEPATH . '/assets/images/svg-icons/';
	$svgs = glob( $svg_path . '*.svg' );

	foreach ( $svgs as $svg ) {
		// Strip out the path, and svg extension.
		$svg_id = str_replace( array( $svg_path, '.svg' ), '', $svg );

		echo wds_acme_get_svg( array( 'icon' => $svg_id ) ); // WPCS: XSS ok.
	};
}

/**
 * Add additional scripts to the header.
 *
 * @return  void
 */
function wds_acme_add_additional_header_scripts() {
	// Get header scripts from customizer.
	$header_scripts = get_theme_mod( 'wds_acme_additional_header_scripts' );

	// Bail early if no header scripts.
	if ( empty( $header_scripts ) ) {
		return;
	}

	// Replace variables in header scripts.
	$header_scripts = wds_acme_replace_vars_in_string( $header_scripts );

	// Output scripts.
	echo wds_acme_sanitize_js( $header_scripts ); // WPCS: XSS ok.
}
add_action( 'wp_head', 'wds_acme_add_additional_header_scripts', 60 );

/**
 * Add additional scripts to the footer.
 *
 * @return  void
 */
function wds_acme_add_additional_footer_scripts() {
	// Get header scripts from customizer.
	$footer_scripts = get_theme_mod( 'wds_acme_additional_footer_scripts' );

	// Bail early if no header scripts.
	if ( empty( $footer_scripts ) ) {
		return;
	}

	// Replace variables in footer scripts.
	$footer_scripts = wds_acme_replace_vars_in_string( $footer_scripts );

	// Output scripts.
	echo wds_acme_sanitize_js( $footer_scripts ); // WPCS: XSS ok.
}
add_action( 'wp_footer', 'wds_acme_add_additional_footer_scripts', 60 );

/**
 * Add TripleLift scripts if necessary.
 *
 * @return  void
 */
function wds_acme_add_triplelift_scripts() {
	// bail early if this is the customizer preview.
	if ( is_customize_preview() ) {
		return;
	}

	// determine which theme mod we're using.
	if ( is_single() ) {
		$mod_name = 'wds_acme_triplelift_single';
	} elseif ( is_archive() || is_front_page() ) {
		$mod_name = 'wds_acme_triplelift_archive';
	}

	// bail early if no theme mod name.
	if ( empty( $mod_name ) ) {
		return;
	}

	// get mod value.
	$active = get_theme_mod( $mod_name );

	// bail early if not active or empty.
	if ( empty( $active ) ) {
		return;
	}

	// get the script URL.
	$script_url = get_theme_mod( $mod_name . '_url', true );

	// bail early if script URL isn't available.
	if ( empty( $script_url ) ) {
		return;
	}
	?>
	<script src="<?php echo esc_url( $script_url ); ?>"></script>
	<?php
}

/**
 * Generates a toggleable menu list that's operated with a button.
 *
 * @param  array $args Arguments array, see the $defaults for a description of each option.
 * @return Mixed HTML   The markup for the list, ready to be echo'd.
 */
function wds_acme_get_toggle_menu( $args = array() ) {

	// Setup defaults.
	$defaults = array(
		'button_text' => get_bloginfo( 'name' ), // The text displayed on the button.
		'menu'        => 'sites', // The menu that it needs to use,
		'menu_id'     => 'sites-menu', // ID for the menu, should be unique if multiples are called.
		'icon'        => '', // Array like you would pass to the SVG function.
		'location'    => 'bottom', // Where the menu will appear, top or bottom.
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Bail if no menu is assigned to the requested location.
	if ( ! has_nav_menu( $args['menu'] ) ) {
		return '';
	}

	// Start our output.
	ob_start(); ?>

	<nav class="dropdown-wrap">
		<button type="button" class="dropdown-toggle" aria-expanded="false" data-location="<?php echo esc_attr( $args['location'] ); ?>">
			<?php
				echo esc_html( $args['button_text'] );

				if ( ! empty( $args['icon'] ) ) {
					echo wds_acme_get_svg( $args['icon'] ); // WPCS: XSS ok.
				}
			?>
		</button>
		<?php
			wp_nav_menu( array(
				'container'      => '',
				'theme_location' => $args['menu'],
				'menu_id'        => $args['menu_id'],
				'menu_class'     => 'menu dropdown-list',
			) );
		?>
	</nav>

	<?php
	return ob_get_clean();
}

/**
 * Immediately prints the toggleable list generated by wds_acme_get_toggle_menu().
 *
 * @param  array $args Array of arguments to set the field's label, name, and options.
 * @return void
 */
function wds_acme_do_toggle_menu( $args = array() ) {
	echo wds_acme_get_toggle_menu( $args ); // WPCS: XSS ok.
}

/**
 * Get markup for a customizer set footer logo. Basically a wrapper function.
 *
 * @param  string $id ID for the customizer field that holds an image id.
 * @return Mixed HTML Image markup.
 */
function wds_acme_get_footer_logo( $id ) {

	// Bail if there is no id.
	if ( empty( $id ) ) {
		return '';
	}

	$image_id = get_theme_mod( $id );
	$markup = '<div class="logo-wrap %s">%s</div>';
	$wrap_class = $id . '_wrap';

	// Bail if no image is set, but still return the wrap for the customizer.
	if ( ! $image_id ) {
		return sprintf( $markup, $wrap_class, '' );
	}

	return sprintf( $markup, $wrap_class, wp_get_attachment_image( $image_id, 'full', null, array( 'class' => strtolower( $id ) ) ) );
}

/**
 * Immediately prints the image generated by wds_acme_get_footer_logo().
 *
 * @param  string $id ID for the customizer field that holds an image id.
 * @return void
 */
function wds_acme_do_footer_logo( $id ) {
	echo wds_acme_get_footer_logo( $id ); // WPCS XSS: ok.
}

/**
 * Displays a featured thumbnail with the Link boxes according to comps.
 * This function is dependent on the linkbox plugin code.
 * Function called in bloguin-link-boxes/link-boxes.php.
 *
 * @param  int $post_id Featured thumbnail of first post set link box post.
 * @return void
 */
function wds_acme_get_link_boxes_images( $post_id ) {
	// check if the post has a thumbnail.
	if ( has_post_thumbnail( $post_id ) ) {

		$img_desktop  = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'sidebar-image' );

		// bail early if no thumbnail.
		if ( empty( $img_desktop ) ) {
			return;
		}

		ob_start(); ?>

		<div class="link-box-thumb image-as-background" style="background-image: url('<?php echo esc_attr( $img_desktop[0] ); ?>')">
		</div>

		<?php echo ob_get_clean();
	}
}

/**
 * Add additional scripts to the header.
 *
 * @return  string|bool Email for the contact form recipient, false on failure.
 */
function wds_acme_get_contact_form_email_recipient() {
	// get email form recipient from customizer.
	$recipient_email = get_theme_mod( 'wds_acme_contact_form_email' );

	// bail early if no recipient.
	if ( empty( $recipient_email ) ) {
		return false;
	}

	// bail early if not a valid email.
	if ( ! filter_var( $recipient_email, FILTER_VALIDATE_EMAIL ) ) {
		return false;
	}

	return $recipient_email;
}

/**
 * Displays InPost content from the customizer.
 *
 * @return string Markup for the InPost content.
 */
function wds_acme_get_inpost_content() {
	// get inpost content from customizer.
	$inpost_content = get_theme_mod( 'wds_acme_in_post_content' );

	// bail early if no inpost content.
	if ( empty( $inpost_content ) ) {
		return;
	}

	return wp_kses_post( $inpost_content );
}

/**
 * Modified version of the_post_navigation, does the same, but uses different classes and adds arrows.
 *
 * @param  array $args Arguments array. Lets you set the text for the buttons.
 * @return string
 */
function wds_acme_get_the_posts_navigation( $args = array() ) {

	global $wp_query;

	// Bail if there's only one page.
	if ( 1 === $wp_query->max_num_pages ) {
		return '';
	}

	// Set defaults.
	$defaults = array(
		'prev_text'          => wds_acme_get_svg( array( 'icon' => 'arrow' ) ) . __( 'Older posts', 'acme' ),
		'next_text'          => __( 'Newer posts', 'acme' ) . wds_acme_get_svg( array( 'icon' => 'arrow' ) ),
		'screen_reader_text' => __( 'Posts navigation', 'acme' ),
	);

	$args = wp_parse_args( $args, $defaults );

	$navigation = '';
	$next_link = get_previous_posts_link( $args['next_text'] );
	$prev_link = get_next_posts_link( $args['prev_text'] );

	if ( $prev_link ) {
		$navigation .= $prev_link;
	}

	if ( $next_link ) {
		$navigation .= $next_link;
	}

	$navigation = _navigation_markup( $navigation, 'posts-navigation', $args['screen_reader_text'] );

	return $navigation;
}

/**
 * Immediately print the custom prev/next pagination generated by wds_acme_do_the_posts_navigation().
 *
 * @param  array $args Array of arguments to set the button texts.
 * @return void
 */
function wds_acme_do_the_posts_navigation( $args = array() ) {
	echo wds_acme_get_the_posts_navigation( $args ); // WPCS: XSS ok.
}

/**
 * Filter that adds the class attribute to the next post links.
 *
 * @return string Class attribute string.
 */
function wds_acme_next_posts_link_attributes() {
	return 'class="nav-previous button button-outline"';
}
add_filter( 'next_posts_link_attributes', 'wds_acme_next_posts_link_attributes' );

/**
 * Filter that adds the class attribute to the previous post links.
 *
 * @return string Class attribute string.
 */
function wds_acme_prev_posts_link_attributes() {
	return 'class="nav-next button button-outline"';
}
add_filter( 'previous_posts_link_attributes', 'wds_acme_prev_posts_link_attributes' );

/**
 * Get the ad code for an author and display it.
 *
 * @return HTML for user ad
 */
function wds_acme_display_author_ad() {

	$author_id = get_the_author_meta( 'ID' );

	// bail if no author id.
	if ( empty( $author_id ) ) {
		return;
	}

	$author_ad = get_field( 'ad_code', 'user_' . $author_id );

	// bail if no author ad is defined.
	if ( empty( $author_ad ) ) {
		return;
	}

	// if we reached this point, display ad.
	echo '<div class="author-ad-box">' . $author_ad . '</div>'; // WPCS: XSS ok.
}

/**
 * Check if post has an author alias.
 *
 * @return bool
 */
function wds_acme_has_author_alias() {
	$author_alias = get_field( 'author_alias' );
	return ( empty( $author_alias ) );

}

/**
 * Generate the markup for an author box.
 *
 * @param  array $args Array of arguments.
 * @return Mixed HTML   Markup for the author box.
 */
function wds_acme_get_authorbox( $args = array() ) {

	// Bail if no author is passed.
	if ( ! isset( $args['authorID'] ) || empty( $args['authorID'] ) ) {
		return '';
	}

	// Bail if there is an author alias set.
	if ( ! wds_acme_has_author_alias() ) {
		return '';
	}

	$defaults = array(
		'authorID'      => '',
		'title'         => esc_html__( 'About %s', 'acme' ),
		'posts_link'    => true,
		'extra_classes' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$author = $args['authorID'];

	// Get the author's meta.
	$author_name = get_the_author_meta( 'display_name', $author );
	$author_bio = get_the_author_meta( 'description', $author );

	// Get the author image from simple local avatar.
	$author_images = get_user_meta( $author, 'simple_local_avatar', true );
	$author_image = isset( $author_images['96'] ) ? $author_images['96'] : false;

	// If no avatar yet but we have the full image, use it.
	if ( ( false === $author_image ) && isset( $author_images['full'] ) ) {
		$author_image = $author_images['full'];
	}

	// Default to regular avatar.
	if ( false ===  $author_image ) {
		$author_image = get_avatar_url( $author );
	}

	$author_twitter = get_the_author_meta( 'twitter', $author );
	$author_link = get_author_posts_url( $author );

	// Set up the title.
	$title = sprintf( '<h2 class="author-title">%s</h2>', $args['title'] );

	if ( is_author() ) {
		$title = sprintf( '<h1 class="author-title">%s</h1>', $args['title'] );
	}

	ob_start(); ?>

	<section id="author-box" class="author-box<?php echo ' ' . esc_attr( $args['extra_classes'] ); ?>">
		<div class="author-wrap">
			<?php if ( $author_image ) : ?>
				<img class="author-avatar" src="<?php echo esc_url( $author_image ); ?>" alt="" />
			<?php endif ?>

			<?php printf( $title , esc_html( $author_name ) ); // WPCS: XSS ok. ?>

			<?php if ( $author_bio ) : ?>
				<div class="author-bio"><?php echo wp_kses_post( wpautop( $author_bio ) ); ?></div>
			<?php endif; ?>

			<div class="author-box-footer">
				<?php if ( $args['posts_link'] ) : ?>
				<a href="<?php echo esc_url( $author_link ); ?>" class="all-posts">
					<?php printf( esc_html__( 'View all posts by %s', 'acme' ), esc_html( $author_name ) ); ?>
				</a>
				<?php endif; ?>

				<?php if ( $author_twitter ) : ?>
					<a href="<?php echo esc_url( $author_twitter ); ?>" class="twitter">
						<?php esc_html_e( 'Follow on Twitter', 'acme' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php wds_acme_display_author_ad(); ?>
	</section>

	<?php return ob_get_clean();
}

/**
 * Immediately print the markup genrated by wds_acme_get_authorbox()
 *
 * @param  array $args Array of arguments.
 * @return void
 */
function wds_acme_do_authorbox( $args = array() ) {
	echo wds_acme_get_authorbox( $args ); // WPCS: XSS ok.
}

/**
 * Generate HTML for a set of recent posts, filtered by arguments.
 *
 * @param  array $args      Array of arguments.
 * @return mixed HTML       HTML to display the recent posts.
 */
function wds_acme_get_recent_posts( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'ad_after' => 3,
	);

	$args = wp_parse_args( $args, $defaults );

	$recent_posts = wds_acme_retrieve_recent_posts( $args );

	// Bail early query failed.
	if ( empty( $recent_posts ) ) {
		return '';
	}

	// Bail if there are no posts.
	if ( ! $recent_posts->have_posts() ) {
		return '';
	}

	$loop_index = 1;

	ob_start();

	while ( $recent_posts->have_posts() ) : $recent_posts->the_post();

		$template = wds_acme_get_post_template( get_the_ID() );

		get_template_part( $template, 'page' );

		if ( $loop_index === $args['ad_after'] ) {
			// When the counter is at 5, and there are widgets in the ad area, display it.
			if ( is_active_sidebar( 'in-river-ads' ) ) : ?>
				<div id="in-river-ads" class="in-river-ads">
					<?php dynamic_sidebar( 'in-river-ads' ); ?>
				</div>
			<?php endif;
		}

		++$loop_index;

	endwhile;

	// Avoid post leak issues.
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * Immediately print the results for wds_acme_get_recent_posts.
 *
 * @param  array $args Array of arguments.
 * @return void
 */
function wds_acme_do_recent_posts( $args = array() ) {
	echo wds_acme_get_recent_posts( $args ); // WPCS: XSS ok.
}

/**
 * Add a span to search terms in the content that is being passed.
 *
 * @param  array $args Array of arguments, only takes "content" currently.
 * @return string      The content with any occurrences of the search term highlighted.
 */
function wds_acme_get_highlighted_content( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'content' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	// Bail if there is no content.
	if ( '' === $args['content'] ) {
		return '';
	}

	$keys = implode( '|', explode( ' ', get_search_query() ) );
	return preg_replace( '/(' . $keys . ')/iu', '<span class="search-term">\0</span>', $args['content'] );
}

/**
 * Generate Category Label with Custom ACF Color.
 *
 * @param string $args String of additional classes.
 * @return string
 */
function wds_acme_get_category_label( $args = null ) {

	// Get the Categories.
	$categories = get_the_category();

	// Check to see if args exist.
	if ( is_null( $args ) ) {
		$args = '';
	}

	// Clean output.
	$output = '';

	// Start the markup.
	ob_start();
	if ( ! empty( $categories ) ) {
		foreach ( $categories as $category ) {
			$output .= '<span class="cat-links"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'acme' ), $category->name ) ) . '"' . $args . 'style="background-color: ' . esc_attr( get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] ) ) . '">' . esc_html( $category->name ) . '</a></span>';
		}
		echo trim( $output ); // WPCS: XSS OK.
	}

	return ob_get_clean();
}

/**
 * Echo Category Label.
 *
 * @param string $args String of additional classes.
 */
function wds_acme_do_category_label( $args = null ) {
	echo wds_acme_get_category_label( $args ); // WPCS: XSS OK.
}

/**
 * Display a list of tags that were assigned to the post.
 */
function wds_acme_display_tags() {
	$tags_list = get_the_tag_list();

	if ( $tags_list ) : ?>

	<div class="tags-links">
		<?php echo $tags_list; // WPCS: XSS ok. ?>
	</div>

	<?php endif;
}
