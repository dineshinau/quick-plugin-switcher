<?php
/**
 * @link              https://dineshinaublog.wordpress.com
 * @since             1.0
 * @package           quick-plugin-switcher
 *
 * Plugin Name:       Quick Plugin Switcher
 * Plugin URI:        https://dineshinaublog.wordpress.com/quick-plugin-switcher
 * Description:       This simplifies plugin handling operations by adding a new bulk action "Switch" on this page and also adds easy activate/deactivate again links on plugin notices. You can delete a plugin directly from deactivated notice too.
 * Version:           1.4
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
