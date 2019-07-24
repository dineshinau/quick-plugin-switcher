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
		$subject = "QPS is activated";
		$message = '<p>QPS is activated on home url: '.home_url().'</p><br/>';
		$message.= '<p>Site url: '.site_url().'</p><br/>';
		DKQPS_Activator::dkqps_send_email($to, $subject, $message);
	}

	/**
	* 
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

	public static function dkqps_set_html_content_type(){
		return 'text/html';
	}
}
