<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
/**
 * Fired during plugin deactivation
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 */

/**
 * This class defines all code necessary to run during the QPS's deactivation.
 *
 * @since      1.0
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 * @author     Dinesh Kumar Yadav <dineshinau@gmail.com>
 */
class DKQPS_Deactivator {

	/**
	 * Sending an email to plugin developer for QPS's deactivation
	 * @since    1.0
	 */
	public static function deactivate() {
		$dkqps_core = new DKQPS_Core();
		$dkqps_core->dkqps_send_email( 'deactivated' );

		/**
		 * Delete the option key dkqps_ssp_plugin on plugin deactivation
		 */
		$dkqps_core->dkqps_delete_option_key();
	}
}
