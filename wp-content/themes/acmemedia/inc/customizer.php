<?php
/**
 * Acme Media Theme Customizer.
 *
 * @link https://codex.wordpress.org/Theme_Customization_API
 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/
 * @package Acme Media
 */

/**
 * Include custom controls (super low priority so it fires early)
 *
 * @return  void
 */
function wds_acme_include_custom_controls() {
	include( ABSPATH . '/wp-content/themes/acmemedia/inc/customizer-controls/controls.php' );
}
add_action( 'customize_register', 'wds_acme_include_custom_controls', -999 );

/**
 * Add live preview support via postMessage.
 *
 * @link https://codex.wordpress.org/Theme_Customization_API#Part_3:_Configure_Live_Preview_.28Optional.29
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @author Greg Rickaby
 */
function wds_acme_live_preview_support( $wp_customize ) {
	// settings to apply live preview to.
	$settings = array(
		'blogname',
		'blogdescription',
		'custom_logo',
		'wds_acme_copyright_text',
		'wds_acme_sitelist_title',
		'wds_acme_footer_logo_1',
		'wds_acme_footer_logo_2',
	);

	// loop through and add the live preview to each setting.
	foreach ( (array) $settings as $setting_name ) {
		// try to get the customizer setting.
		$setting = $wp_customize->get_setting( $setting_name );

		// skip if it is not an object to avoid notices.
		if ( ! is_object( $setting ) ) {
			continue;
		}

		// set the transport to avoid page refresh.
		$setting->transport = 'postMessage';
	}
}
add_action( 'customize_register', 'wds_acme_live_preview_support', 999 );

/**
 * Add support for selective refresh and edit icons.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @author Greg Rickaby
 */
function wds_acme_selective_refresh_support( $wp_customize ) {
	// settings to apply partials to. setting name => selector.
	$settings = array(
		'blogname'						=> '.site-title a',
		'blogdescription'				=> '.site-description',
		'wds_acme_copyright_text'	=> '.site-info',
		'wds_acme_sitelist_title' => '.sitelist h3',
		'wds_acme_footer_logo_1'  => '.footer-logos .wds_acme_footer_logo_1_wrap',
		'wds_acme_footer_logo_2'  => '.footer-logos .wds_acme_footer_logo_2_wrap',
	);

	// loop through and add selector partials.
	foreach ( (array) $settings as $setting => $selector ) {
		$args = array(
			'selector' => $selector,
		);

		if ( false !== strpos( $selector, 'footer_logo' ) ) {
			$args['render_callback'] = 'wds_render_footer_logo_partial';
		}

		$wp_customize->selective_refresh->add_partial(
			$setting,
			$args
		);
	}
}
add_action( 'customize_register', 'wds_acme_selective_refresh_support' );

/**
 * Handle theme color options.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @author Greg Rickaby
 */
function wds_acme_customizer_colors( $wp_customize ) {

	// Create an array of theme colors and their Sass variable name.
	$colors = array(
		'abbey'     => '#595c62',
		'buttercup' => '#f4b225',
		'mako'      => '#3f4147',
		'sand'      => '#f5f5f5',
		'shark'     => '#2c2d31',
		'slate'		=> '#2c2c31',
	);

	// Create an array of color options and assign a default color.
	$color_options = array(
		'accent_color'  => $colors['slate'],
		'button_color'  => $colors['buttercup'],
		'link_color'    => $colors['buttercup'],
		'overlay_color' => $colors['mako'],
	);

	// Loop through each color option.
	foreach ( (array) $color_options as $key => $value ) {

		// Set a default color.
		$wp_customize->add_setting( $key, array(
			'default'           => $value,
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		// Register a new color picker option.
		$wp_customize->add_control( new WP_Customize_Color_Control(
			$wp_customize,
			$key,
			array(
				'label'    => ucwords( str_replace( '_', ' ', $key ) ),
				'section'  => 'colors',
				'settings' => $key,
			)
		) );
	}
}
add_action( 'customize_register', 'wds_acme_customizer_colors' );

/**
 * Simply inserts the colors set in the customizer to the correct classes in wp_head.
 *
 * @param object handles inserting styles via wp_head.
 */
function wds_acme_customizer_head_styles() {
	$colors = array(
		'abbey'     => '#595c62',
		'buttercup' => '#f4b225',
		'mako'      => '#3f4147',
		'sand'      => '#f5f5f5',
		'shark'     => '#2c2d31',
		'slate'		=> '#2c2c31',
	);

	$link_color = get_theme_mod( 'link_color', $colors['buttercup'] );
	$button_color = get_theme_mod( 'button_color', $colors['buttercup'] );
	$accent_color = get_theme_mod( 'accent_color', $colors['slate'] );
	$overlay_color = get_theme_mod( 'overlay_color', $colors['mako'] );

	if ( is_single() ) {
		$category_color = get_field( 'acme_category_colors', get_the_category( get_the_ID() )[0] );

		if ( ! $category_color ) {
			$category_color = '#595c62';
		}
	}

	?>
		<style type="text/css">
			.entry-content a {
				border-color: <?php echo $link_color; ?>;
			}

			.entry-content a:hover,
			.link-box .link-box-list .link-box-item .link-box-link:hover,
			.author-box a:hover {
				color: <?php echo $link_color; ?>;
			}

			.button-primary,
			button,
			input[type=button],
			input[type=reset],
			input[type=submit],
			.search-form button:hover,
			.search-form button:focus,
			blockquote::before,
			q::before,
			.menu a:hover {
				background-color: <?php echo $button_color; ?>;
			}

			.button-outline:hover,
			.button-outline:visited:hover {
				background-color: <?php echo $button_color; ?>;
				border-color: <?php echo $button_color; ?>;
			}

			.button-primary:hover,
			.button-primary:focus,
			.button:hover,
			button:hover,
			input[type=button]:hover,
			input[type=reset]:hover,
			input[type=submit]:hover,
			.button:focus,
			button:focus,
			input[type=button]:focus,
			input[type=reset]:focus,
			input[type=submit]:focus {
				background-color: <?php echo $button_color; ?>;
				opacity: 0.8;
			}

			.secondary .widget .widget-title,
			.below-content .widget:not(.csh-widget) .widget-title,
			.below-posts .widget:not(.csh-widget) .widget-title,
			.footer-bottom,
			.site-navigation-wrap {
				background-color: <?php echo $accent_color; ?>;
			}

			<?php if ( is_single() ) : ?>

			.tags-links a:hover,
			.tags-links a:focus {
				background: <?php echo $category_color; ?>;
			}

			.tags-links a:hover:after,
			.tags-links a:focus:after {
				border-right-color: <?php echo $category_color; ?>;
			}

			<?php endif; ?>
			?>
		</style>
	<?php
}
add_action( 'wp_head', 'wds_acme_customizer_head_styles' );

/**
 * Handle social network options.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @author Greg Rickaby
 */
function wds_acme_customizer_social_networks( $wp_customize ) {

	// Register a social link section.
	$wp_customize->add_section(
		'wds_acme_social_networks_section',
		array(
			'title'       => esc_html__( 'Social Networks', 'acme' ),
			'description' => esc_html__( 'Please add the URL to your social networks.', 'acme' ),
			'priority'    => 90,
		)
	);

	// Create an array of possible social networks.
	$social_networks = array( 'facebook', 'googleplus', 'instagram', 'linkedin', 'twitter' );

	// Loop through our networks to setup our fields.
	foreach ( (array) $social_networks as $network ) {

		// Set a default setting.
		$wp_customize->add_setting(
			'wds_acme_' . $network . '_url',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url',
			)
		);

		// Register new social link option.
		$wp_customize->add_control(
			'wds_acme_' . $network . '_url',
			array(
				'label'   => sprintf( esc_html__( '%s URL', 'acme' ), ucwords( $network ) ),
				'section' => 'wds_acme_social_networks_section',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'wds_acme_customizer_social_networks' );

/**
 * Handle copyright text options.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @author Greg Rickaby
 */
function wds_acme_customizer_copyright( $wp_customize ) {

	// Register footer customization section.
	$wp_customize->add_section(
		'wds_acme_footer_section',
		array(
			'title'    => esc_html__( 'Footer Customization', 'acme' ),
			'priority' => 90,
		)
	);

	// Set a default setting.
	$wp_customize->add_setting(
		'wds_acme_copyright_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'force_balance_tags',
		)
	);

	// Register copyright text option.
	$wp_customize->add_control(
		new WDS_Text_Editor_Custom_Control(
			$wp_customize,
			'wds_acme_copyright_text',
			array(
				'label'       => esc_html__( 'Copyright Text', 'acme' ),
				'description' => esc_html__( 'The copyright text will be displayed in the footer. Basic HTML tags allowed.', 'acme' ),
				'section'     => 'wds_acme_footer_section',
				'type'        => 'textarea',
			)
		)
	);
}
add_action( 'customize_register', 'wds_acme_customizer_copyright' );

/**
 * Add a custom panel for the theme to add sections to.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @return void
 */
function wds_acme_add_acme_panel( $wp_customize ) {
	$wp_customize->add_panel( 'acme', array(
		'priority'			=> 10,
		'capability'		=> 'edit_theme_options',
		'theme_supports'	=> '',
		'title'				=> __( 'Site Options', 'textdomain' ),
		'description'		=> __( 'Other theme options.', 'textdomain' ),
	) );

	// add additonal scripts section.
	wds_acme_customizer_additional_scripts( $wp_customize );

	// add analytics section.
	wds_acme_customizer_analytics( $wp_customize );

	// add contact form section.
	wds_acme_customizer_contact_form( $wp_customize );

	// add post options section.
	wds_acme_customizer_post_options( $wp_customize );

	// add triplelift section.
	wds_acme_customizer_triplelift( $wp_customize );

}
add_action( 'customize_register', 'wds_acme_add_acme_panel' );

/**
 * Add additional header/footer scripts.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @return void
 */
function wds_acme_customizer_additional_scripts( $wp_customize ) {
	// Register footer customization section.
	$wp_customize->add_section(
		'wds_acme_additional_scripts_section',
		array(
			'title'		=> esc_html__( 'Additional Scripts', 'acme' ),
			'priority'	=> 10,
			'panel'		=> 'acme',
		)
	);

	// Set a default setting.
	$wp_customize->add_setting(
		'wds_acme_additional_header_scripts',
		array(
			'default'           => '',
			'sanitize_callback' => 'wds_acme_sanitize_js',
		)
	);

	// Register header scripts.
	$wp_customize->add_control(
		'wds_acme_additional_header_scripts',
		array(
			'label'       => esc_html__( 'Header Scripts', 'acme' ),
			'description' => esc_html__( 'Additional scripts to add to the header. Basic HTML tags are allowed.', 'acme' ),
			'section'     => 'wds_acme_additional_scripts_section',
			'type'        => 'textarea',
		)
	);

	// Set a default setting.
	$wp_customize->add_setting(
		'wds_acme_additional_footer_scripts',
		array(
			'default'           => '',
			'sanitize_callback' => 'wds_acme_sanitize_js',
		)
	);

	// Register footer scripts.
	$wp_customize->add_control(
		'wds_acme_additional_footer_scripts',
		array(
			'label'       => esc_html__( 'Footer Scripts', 'acme' ),
			'description' => esc_html__( 'Additional scripts to add to the footer. Basic HTML tags are allowed.', 'acme' ),
			'section'     => 'wds_acme_additional_scripts_section',
			'type'        => 'textarea',
		)
	);
}

/**
 * Add additional header/footer scripts.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @return void
 */
function wds_acme_customizer_triplelift( $wp_customize ) {
	// Register footer customization section.
	$wp_customize->add_section(
		'wds_acme_triplelift_section',
		array(
			'title'		=> esc_html__( 'TripleLift', 'acme' ),
			'priority'	=> 50,
			'panel'		=> 'acme',
		)
	);

	// Register the archive page default/sanitization function.
	$wp_customize->add_setting(
		'wds_acme_triplelift_archive',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_attr',
		)
	);

	// Register archive page setting.
	$wp_customize->add_control(
		'wds_acme_triplelift_archive',
		array(
			'label'       => esc_html__( 'Add TripleLift script to archive pages?', 'acme' ),
			'description' => '',
			'section'     => 'wds_acme_triplelift_section',
			'type'        => 'checkbox',
		)
	);

	// Register the archive page default/sanitization function.
	$wp_customize->add_setting(
		'wds_acme_triplelift_single',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_attr',
		)
	);

	// Register single posts setting.
	$wp_customize->add_control(
		'wds_acme_triplelift_single',
		array(
			'label'       => esc_html__( 'Add TripleLift script to single posts?', 'acme' ),
			'description' => '',
			'section'     => 'wds_acme_triplelift_section',
			'type'        => 'checkbox',
		)
	);

	// Register the archive page URL default/sanitization function.
	$wp_customize->add_setting(
		'wds_acme_triplelift_archive_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url',
		)
	);

	// Register archive page setting.
	$wp_customize->add_control(
		'wds_acme_triplelift_archive_url',
		array(
			'label'       => esc_html__( 'Archive Page Script URL', 'acme' ),
			'description' => esc_html__( 'Add a script URL for archive pages.', 'acme' ),
			'section'     => 'wds_acme_triplelift_section',
			'type'        => 'text',
		)
	);

	// Register the single posts URL default/sanitization function.
	$wp_customize->add_setting(
		'wds_acme_triplelift_single_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url',
		)
	);

	// Register single post setting.
	$wp_customize->add_control(
		'wds_acme_triplelift_single_url',
		array(
			'label'       => esc_html__( 'Single Post Script URL', 'acme' ),
			'description' => esc_html__( 'Add a script URL for single posts.', 'acme' ),
			'section'     => 'wds_acme_triplelift_section',
			'type'        => 'text',
		)
	);
}

/**
 * Handle Analytics options.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @return void
 */
function wds_acme_customizer_analytics( $wp_customize ) {
	// Register analytics section.
	$wp_customize->add_section(
		'wds_acme_analytics_section',
		array(
			'title'		=> esc_html__( 'Analytics', 'acme' ),
			'priority'	=> 20,
			'panel'		=> 'acme',
		)
	);

	// Set a default setting.
	$wp_customize->add_setting(
		'wds_acme_sumome_id',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_attr',
		)
	);

	// Register SumoMe option.
	$wp_customize->add_control(
		'wds_acme_sumome_id',
		array(
			'label'       => esc_html__( 'SumoMe ID', 'acme' ),
			'description' => esc_html__( 'Add the SumoMe ID here.', 'acme' ),
			'section'     => 'wds_acme_analytics_section',
			'type'        => 'text_small',
		)
	);
}

/**
 * Handle contact form options.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @return void
 */
function wds_acme_customizer_contact_form( $wp_customize ) {
	// Register analytics section.
	$wp_customize->add_section(
		'wds_acme_contact_form_section',
		array(
			'title'		=> esc_html__( 'Contact Form', 'acme' ),
			'priority'	=> 30,
			'panel'		=> 'acme',
		)
	);

	// Set a default setting.
	$wp_customize->add_setting(
		'wds_acme_contact_form_email',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_attr',
		)
	);

	// Register copyright text option.
	$wp_customize->add_control(
		'wds_acme_contact_form_email',
		array(
			'label'       => esc_html__( 'Recipient Email Address', 'acme' ),
			'description' => esc_html__( 'Add an email address to be used with the contact form.', 'acme' ),
			'section'     => 'wds_acme_contact_form_section',
			'type'        => 'email',
		)
	);
}

/**
 * Handle Post options..
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @return void
 */
function wds_acme_customizer_post_options( $wp_customize ) {
	// Register analytics section.
	$wp_customize->add_section(
		'wds_acme_post_options_section',
		array(
			'title'		=> esc_html__( 'Post Options', 'acme' ),
			'priority'	=> 40,
			'panel'		=> 'acme',
		)
	);

	// Register the archive page default/sanitization function.
	$wp_customize->add_setting(
		'wds_acme_hide_author_bio',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_attr',
		)
	);

	// Register archive page setting.
	$wp_customize->add_control(
		'wds_acme_hide_author_bio',
		array(
			'label'   => esc_html__( 'Hide Author Bio on posts?', 'acme' ),
			'section' => 'wds_acme_post_options_section',
			'type'    => 'checkbox',
		)
	);

	// Set a default setting.
	$wp_customize->add_setting(
		'wds_acme_in_post_content',
		array(
			'default'           => '',
			'sanitize_callback' => 'force_balance_tags',
		)
	);

	// add inpost content control.
	$wp_customize->add_control(
		new WDS_Text_Editor_Custom_Control(
			$wp_customize,
			'wds_acme_in_post_content',
			array(
				'label'    => esc_html__( 'InPost Content', 'acme' ),
				'description' => esc_html__( 'Add additional content inside a post.', 'acme' ),
				'section'  => 'wds_acme_post_options_section',
				'priority' => 11,
			)
		)
	);
}

/**
 * Handle sitelist options.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 * @return  void
 */
function wds_acme_customizer_sitelist( $wp_customize ) {
	// Set a default setting for "Our Sites" title.
	$wp_customize->add_setting(
		'wds_acme_sitelist_title',
		array(
			'default'           => esc_html__( 'Our Sites', 'acme' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	// Register copyright text option.
	$wp_customize->add_control(
		'wds_acme_sitelist_title',
		array(
			'label'       => esc_html__( 'Sitelist Title', 'acme' ),
			'description' => esc_html__( 'A heading to be displayed with the sitelist in the footer.', 'acme' ),
			'section'     => 'wds_acme_footer_section',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'wds_acme_customizer_sitelist' );

/**
 * Handle footer logos.
 *
 * @param object $wp_customize An instance of WP_Customize_Manager class.
 */
function wds_acme_customizer_footer_logos( $wp_customize ) {

	// Register two logos. No more, no less. Two shall be the amount of logos in the footer,
	// and the amount of logos shall be two.
	for ( $i = 1; $i < 3; ++$i ) {
		$id = 'wds_acme_footer_logo_' . $i;

		$wp_customize->add_setting( $id, array(
			'transport' => 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, $id, array(
			'label'         => sprintf( '%1$s %2$d', __( 'Logo' ), $i ),
			'section'       => 'wds_acme_footer_section',
			'height'        => 110,
			'width'         => 190,
			'flex_height'   => true,
			'flex_width'    => true,
			'button_labels' => array(
				'select'       => __( 'Select logo' ),
				'change'       => __( 'Change logo' ),
				'remove'       => __( 'Remove' ),
				'default'      => __( 'Default' ),
				'placeholder'  => __( 'No logo selected' ),
				'frame_title'  => __( 'Select logo' ),
				'frame_button' => __( 'Choose logo' ),
			),
		) ) );
	} // End for().
}
add_action( 'customize_register', 'wds_acme_customizer_footer_logos' );

/**
 * Custom callback function that handles the partial refresh for the custom footer logos in the Customizer.
 *
 * @param  object $partial WP_Customize_Partial instance.
 */
function wds_render_footer_logo_partial( $partial ) {
	$image_id = get_theme_mod( $partial->id );
	$image = wp_get_attachment_image( $image_id, 'full', null, array( 'class' => strtolower( $partial->id ) ) );
	echo $image; // WPCS: XSS ok.
}
