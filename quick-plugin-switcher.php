<?php
/**
 * @link              https://dineshinaublog.wordpress.com
 * @since             1.0.0
 * @package           Quick_Plugin_Switcher
 *
 * Plugin Name:       Quick Plugin Switcher
 * Plugin URI:        https://dineshinaublog.wordpress.com/quick-plugin-switcher
 * Description:       This helps the admin(s)/super-admin(s) to activate and deactivate plugins faster by reducing the steps, which speeds up the process and minimize the efforts. This adds a new option “Switch” in the drop-down menu of plugin bulk actions on plugin listing page.
 * Version:           1.3
 * Author:            Dinesh Kumar Yadav
 * Author URI:        https://dineshinaublog.wordpress.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dk-quick-plugin-switcher
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('Direct access is not allowed');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dk-quick-plugin-switcher-activator.php
 */
function activate_dk_quick_plugin_switcher() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dk-quick-plugin-switcher-activator.php';
	Dk_Quick_Plugin_Switcher_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dk-quick-plugin-switcher-deactivator.php
 */
function deactivate_dk_quick_plugin_switcher() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dk-quick-plugin-switcher-deactivator.php';
	Dk_Quick_Plugin_Switcher_Deactivator::deactivate();
}

//Registering activation and deactivation hooks
register_activation_hook( __FILE__, 'activate_dk_quick_plugin_switcher' );
register_deactivation_hook( __FILE__, 'deactivate_dk_quick_plugin_switcher' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dk-quick-plugin-switcher.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function run_dk_quick_plugin_switcher() {

	$plugin = new Dk_Quick_Plugin_Switcher();
	$plugin->run();

}
/*is_admin() && add_filter( 'gettext', 
    function( $translated_text, $untranslated_text, $domain )
    {
        $old = array(
            "Plugin <strong>activated</strong>.",
            "Selected plugins <strong>activated</strong>." 
        );

        $new = "Captain: The Core is stable and the Plugin is <strong>activated</strong> at full Warp speed";

        if ( in_array( $untranslated_text, $old, true ) )
            $translated_text = $new;

        return $translated_text;
     }
, 99, 3 );
*/
run_dk_quick_plugin_switcher();
