<?php
/*
 * Widget Mobile/Tablet Class
 *
 * Adds global widget options: Hide on mobile/tablet. Adds class to widget container which can be used to hide the widget using CSS and media queries (not handled in this plugin).
 *
 */

/*
 * Add an option to all widgets.
 *
 * A checkbox for "Hide on mobile"
 * if checked, this value should be used to add a class to the $before_widget parameter when the widget is displayed
 */
add_action( 'in_widget_form', 'wc_in_widget_form', 10, 3 );
function wc_in_widget_form( $widget, $return, $instance ) {
	$instance = wp_parse_args( $instance, array(
		'hide-in-mobile' => 0,
		'hide-in-tablet' => 0,
		'hide-in-desktop' => 0,
	) );

	if ( 'ad-manager-widget' === $widget->id_base ) {
		return;
	}

	?><p style="clear:both;">
	Hide on:
		<label style="padding: 0 5px;"><input type="checkbox" name="<?php echo $widget->get_field_name('hide-in-mobile'); ?>" value="1" <?php checked( $instance['hide-in-mobile'], 1 ); ?> /> Mobile</label>
		<label style="padding: 0 5px;"><input type="checkbox" name="<?php echo $widget->get_field_name('hide-in-tablet'); ?>" value="1" <?php checked( $instance['hide-in-tablet'], 1 ); ?> /> Tablet</label>
		<label style="padding: 0 5px;"><input type="checkbox" name="<?php echo $widget->get_field_name('hide-in-desktop'); ?>" value="1" <?php checked( $instance['hide-in-desktop'], 1 ); ?> /> Desktop</label>
	<br /><em><?php esc_html_e( 'Widgets that are hidden will not output markup on those devices.', 'acme' ); ?> </em></p><?php
	$return = null;
}

/*
 * Saves the option
 */
add_filter('widget_update_callback', 'wc_widget_update_callback', 10, 4 );
function wc_widget_update_callback( $instance, $new_instance, $old_instance, $the_widget ) {
	$instance['hide-in-mobile'] = isset( $new_instance['hide-in-mobile'] ) ? 1 : 0;
	$instance['hide-in-tablet'] = isset( $new_instance['hide-in-tablet'] ) ? 1 : 0;
	$instance['hide-in-desktop'] = isset( $new_instance['hide-in-desktop'] ) ? 1 : 0;
	return $instance;
}

/*
 * Filter the sidebar params to add the class (if needed)
 */
function wc_dynamic_sidebar_params( $params ) {
	// Grab your spears and hunt down the instance!
	$instance = wds_cbm_get_widget_instance( $params[0]['widget_id'], $params[1]['number'] );
	if ( isset( $instance['hide-in-mobile'] ) && $instance['hide-in-mobile'] == 1 ) {
		$params[0]['before_widget'] = str_replace( '">', ' hide-in-mobile">', $params[0]['before_widget'] );
	}
	if ( isset( $instance['hide-in-tablet'] ) && $instance['hide-in-tablet'] == 1 ) {
		$params[0]['before_widget'] = str_replace( '">', ' hide-in-tablet">', $params[0]['before_widget'] );
	}
	if ( isset( $instance['hide-in-desktop'] ) && $instance['hide-in-desktop'] == 1 ) {
		$params[0]['before_widget'] = str_replace( '">', ' hide-in-desktop">', $params[0]['before_widget'] );
	}

	return $params;
}
add_filter( 'dynamic_sidebar_params', 'wc_dynamic_sidebar_params' );

/*
 * Helper function to track down a widget's options
 */
if ( ! function_exists('wds_cbm_get_widget_instance') ) :
	/**
	 * Get a widgets options.
	 *
	 * @param  string $widget_id The widgets ID.
	 * @param  int    $number    The number for the widget.
	 * @return array             Widget instance array.
	 */
	function wds_cbm_get_widget_instance( $widget_id, $number ) {
		global $wp_registered_widgets;

		$widget_instance = null;
		if ( isset( $wp_registered_widgets[ $widget_id ] ) ) {
			$widget = $wp_registered_widgets[ $widget_id ];

			$widget_instances = get_option( $widget['callback'][0]->option_name );
			$widget_instance = $widget_instances[ $number ];
		}

		return $widget_instance;
	}
endif;

/**
 * Filter widget params to override the callback function.
 *
 * @param  array $sidebar_params Parameters for the widget.
 * @return array                 Modified params for the widget.
 */
function wds_acme_mobile_dynamic_sidebar_params( $sidebar_params ) {
	// Bail early if in admin.
	if ( is_admin() ) {
		return $sidebar_params;
	}

	global $wp_registered_widgets;
	$widget_id = $sidebar_params[0]['widget_id'];

	$wp_registered_widgets[ $widget_id ]['original_callback'] = $wp_registered_widgets[ $widget_id ]['callback'];
	$wp_registered_widgets[ $widget_id ]['callback'] = 'wds_acme_mobile_widget_callback_function';

	return $sidebar_params;
}
add_filter( 'dynamic_sidebar_params', 'wds_acme_mobile_dynamic_sidebar_params' );

/**
 * The new widget output function that includes a filter.
 *
 * @return string Markup for the widget,.
 */
function wds_acme_mobile_widget_callback_function() {
	// Get the called widget from global.
	global $wp_registered_widgets;
	$original_callback_params = func_get_args();
	$widget_id = isset( $original_callback_params[0]['widget_id'] ) ? $original_callback_params[0]['widget_id'] : false;

	// Bail early if no widget id.
	if ( false === $widget_id ) {
		return '';
	}

	// Bail early if widget doesn't exist.
	if ( ! isset( $wp_registered_widgets[ $widget_id ] ) ) {
		return '';
	}

	// Save the original callback.
	$original_callback = $wp_registered_widgets[ $widget_id ]['original_callback'];
	$wp_registered_widgets[ $widget_id ]['callback'] = $original_callback;

	$widget_id_base = $wp_registered_widgets[ $widget_id ]['callback'][0]->id_base;

	// Bail early if callback isn't available.
	if ( ! is_callable( $original_callback ) ) {
		return '';
	}

	// Capture widget output in filterable var.
	ob_start();
	call_user_func_array( $original_callback, $original_callback_params );
	$widget_output = ob_get_clean();

	// Allow widget output to be filtered.
	echo apply_filters( 'widget_output', $widget_output, $widget_id_base, $widget_id );
}

/**
 * Modify the widget output to remove widgets hidden on certain devices.
 *
 * @param  string $widget_output  Markup for the widget.
 * @param  string $widget_id_base Base for the widget.
 * @param  string $widget_id      ID for the widget.
 * @return string                 Modified markup for the widget.
 */
function wds_acme_mobile_output_filter( $widget_output, $widget_id_base, $widget_id ) {
	// Bail early and remove widget markup if we're on a desktop and the widget is supposed to be hidden.
	if ( ! wds_is_mobile() && stristr( $widget_output, 'hide-in-desktop' ) ) {
		return '';
	}

	// Bail early and remove widget markup if we're on a tablet and the widget is supposed to be hidden.
	if ( wds_is_tablet() && stristr( $widget_output, 'hide-in-tablet' ) ) {
		return '';
	}

	// Bail early and remove widget markup if we're on a tablet and the widget is supposed to be hidden.
	if ( wds_is_mobile_not_tablet() && stristr( $widget_output, 'hide-in-mobile' ) ) {
		return '';
	}

	return $widget_output;
}
add_filter( 'widget_output', 'wds_acme_mobile_output_filter', 10, 3 );
