<?php
/**
 * @link              https://dineshinaublog.wordpress.com
 * @since             1.0
 * @package           quick-plugin-switcher
 *
 * Plugin Name:       Quick Plugin Switcher
 * Plugin URI:        https://dineshinaublog.wordpress.com/quick-plugin-switcher
 * Description:       This helps the admin(s)/super-admin(s) to activate and deactivate plugins faster by reducing the steps, which speeds up the process and minimize the efforts. This adds a new option “Switch” in the drop-down menu of plugin bulk actions on plugin listing page.
 * Version:           1.3.1
 * Author:            Dinesh Kumar Yadav
 * Author URI:        https://dineshinaublog.wordpress.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quick-plugin-switcher
 * Domain Path:       /languages
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * The code that runs during QPS activation.
 * This action is documented in includes/class-dkqps-activator.php
 */
function activate_dk_quick_plugin_switcher() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dkqps-activator.php';
	DKQPS_Activator::activate();
}

/**
 * The code that runs during QPS deactivation.
 * This action is documented in includes/class-dkqps-deactivator.php
 */
function deactivate_dk_quick_plugin_switcher() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dkqps-deactivator.php';
	DKQPS_Deactivator::deactivate();
}

//Registering activation and deactivation hooks
register_activation_hook( __FILE__, 'activate_dk_quick_plugin_switcher' );
register_deactivation_hook( __FILE__, 'deactivate_dk_quick_plugin_switcher' );

/**
 * The core QPS class that is used to define internationalization and admin-specific hooks
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dkqps-core.php';

/**
 * Begins execution of the QPS.
 * @since    1.0
 */
function run_dkqps_core() {

	$plugin = new DKQPS_Core();
	$plugin->run();

}
run_dkqps_core();
