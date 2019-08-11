<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
/**
 *
 * This class defines all code necessary to run during the QPS's activation.
 *
 * @since      1.0
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 * @author     Dinesh Kumar Yadav <dineshinau@gmail.com>
 */
class DKQPS_Activator {

	/**
	 * Sending an email to plugin developer for QPS's activation
	 *
	 * @since    1.0
	 */
	public static function activate() {
		$dkqps_core = new DKQPS_Core();
		/**
		* @since 1.4
		*/
		$dkqps_core->dkqps_send_email( 'activated' );
	}
}
