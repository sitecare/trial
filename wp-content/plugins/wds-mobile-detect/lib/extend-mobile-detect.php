<?php
/**
 * WDS Mobile Detect
 * @version 0.1.0
 */
class Extend_Mobile_Detect extends Mobile_Detect {
	/**
	 * Check if device is a phone
	 * 
	 * @return boolean 
	 */
	public function isPhone() {
		// set detection tyupe
		$this->setDetectionType( 'mobile' );

		// check for phone in user agent
		foreach ( self::$phoneDevices as $regex ) {
			if ( $this->match( $regex ) ) {
				return true;
			}
		}

		return false;
	}
}