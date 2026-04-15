<?php
/**
 * Plugin Name: WDS Mobile Detect
 * Plugin URI:  http://webdevstudios.com
 * Description: Detect mobile devices
 * Version:     0.1.0
 * Author:      WebDevStudios
 * Author URI:  http://webdevstudios.com
 * Donate link: http://webdevstudios.com
 * License:     GPLv2+
 * Text Domain: wds_mobile_detect
 * Domain Path: languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Lad the plugin classes.
require_once( plugin_dir_path( __FILE__ ) . 'lib/vendor/Mobile_Detect.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/extend-mobile-detect.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/mobile-detect.php' );

/**
 * Determine if we're using a mobile phone.
 *
 * @return bool True if using a phone, false otherwise.
 */
function wds_is_phone() {
	return WDS_Mobile_Detect::get_instance()->isPhone();
}

/**
 * Determine if we're using a obile device the is not a tablet.
 *
 * @return bool True if using a mobile device the is not a tablet, false otherwise.
 */
function wds_is_mobile_not_tablet() {
	return ( WDS_Mobile_Detect::get_instance()->isMobile() && ! WDS_Mobile_Detect::get_instance()->isTablet() );
}

/**
 * Determine if we're using a mobile device.
 *
 * @return bool True if using a mobile device, false otherwise.
 */
function wds_is_mobile() {
	//return true; //for testing locally can be removed
	return WDS_Mobile_Detect::get_instance()->isMobile();
}

/**
 * Determine if we're using a tablet.
 *
 * @return bool True if using a tablet, false otherwise.
 */
function wds_is_tablet() {
	return WDS_Mobile_Detect::get_instance()->isTablet();
}