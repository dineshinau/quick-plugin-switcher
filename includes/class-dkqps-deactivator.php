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
		$to = 'dkwpplugins@gmail.com';
		$subject = "QPS is deactivated";
		$message = '<p>QPS is Deactivated on home url: '.home_url().'</p>';
		$message.= '<p>Site url: '.site_url().'</p>';
		$message.= '<p>Multisite Enabled: '.is_multisite().'</p>';
		DKQPS_Deactivator::dkqps_send_email($to, $subject, $message);

		/**
		* Delete the option key dkqps_ssp_plugin on plugin deactivation
		*/
		DKQPS_Deactivator::dkqps_delete_option_key();
	}

	/**
	* Send notificaiton email on deactivation
	*/
	public static function dkqps_send_email($to, $subject, $message){
		$admin_email = get_option("admin_email");

		//headers
		$headers = array();

		$headers[] = 'From: admin < '.$admin_email.' >'."\r\n";
		$headers[] = 'Reply-To: '.$admin_email;
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		$mail_html = html_entity_decode(stripcslashes($message));

		add_filter( 'wp_mail_content_type', array(__CLASS__, 'dkqps_set_html_content_type') );
		wp_mail($to, $subject, $mail_html,$headers);
		remove_filter( 'wp_mail_content_type', array(__CLASS__, 'dkqps_set_html_content_type') );
	}

	/**
	* Set email content type as html
	*/
	public static function dkqps_set_html_content_type(){
		return 'text/html;';
	}

	/**
	* Delete the option key dkqps_ssp_plugin on plugin deactivation
	*/
	public static function dkqps_delete_option_key(){
		delete_option('dkqps_ssp_plugin');
	}
}
