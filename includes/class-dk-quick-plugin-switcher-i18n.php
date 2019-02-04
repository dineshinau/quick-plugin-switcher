<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0.0
 *
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/includes
 */

/**
 * @since      1.0.0
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/includes
 * @author     DINESH KUMAR YADAV <dineshinau@gmail.com>
 */
class Dk_Quick_Plugin_Switcher_i18n {
	/**
	 * Loading the plugin text domain for translation.
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'dk-quick-plugin-switcher',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
