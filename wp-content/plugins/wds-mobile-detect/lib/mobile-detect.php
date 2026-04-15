<?php
/**
 * WDS Mobile Detect
 * @version 0.1.0
 */
class WDS_Mobile_Detect {

	/**
	 * This plugin's version
	 * @var string
	 */
	const VERSION = '0.1.0';

	/**
	 * Helpful vars
	 */
	public static 
		$url,
		$path,
		$name;

	/**
	 * Hold an instance of our class
	 */
	protected static $instance = null;

	/**
	 * Returns an instance of mobile detect extension class
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Extend_Mobile_Detect();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	private function __construct() {
		// Useful variables
		self::$url  = trailingslashit( plugin_dir_url( __FILE__ ) );
		self::$path = trailingslashit( dirname( __FILE__ ) );
		self::$name = __( 'WDS Mobile Detect', 'wds_mobile_detect' );
	}
}