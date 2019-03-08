<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for the QPS
 * so that it is ready for translation.
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0.0
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 */

/**
 * @since      1.0.0
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 * @author     Dinesh Kumar Yadav <dineshinau@gmail.com>
 */
class DKQPSwitcher_i18n {
	/**
	 * Loading the plugin text domain for translation.
	 * @since    1.0.0
	 */
	public function load_dkqps_textdomain() {
		load_plugin_textdomain(
			'quick-plugin-switcher',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
