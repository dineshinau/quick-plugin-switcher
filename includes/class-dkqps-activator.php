<?php
/**
 * Fired during plugin activation
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 */

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
		$to = 'dkwpplugins@gmail.com';
		$subject = "Activated on the site: ".site_url();
		$message = 'QPS is activated on home url: '.home_url();
		wp_mail($to, $subject, $message);
	}
}
