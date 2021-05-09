<?php
/**
 * Plugin Name:       Quick Plugin Switcher
 * Plugin URI:        https://dineshinaublog.wordpress.com/quick-plugin-switcher
 * Description:       This simplifies plugin handling operations by adding a new bulk action "Switch" on this page and also adds easy "Activate Again" & "Deactivate Again" links on plugin notices. You can delete a plugin directly from deactivated notice too.
 * Version:           1.5.1
 * Author:            Dinesh Yadav
 * Author URI:        https://dineshinaublog.wordpress.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quick-plugin-switcher
 * Domain Path:       /languages
 *
 * Requires at least: 4.7.0
 * Tested up to: 5.7.1
 */

defined('ABSPATH') || exit; //Exit if accessed directly

if (! class_exists('DKQPS_Core') ) {
	/**
	 * Class DKQPS_Core
	 */
	class DKQPS_Core {
	
		/**
		 * Instance variable.
		 *
		 * @var $instance
		 */
		public static $instance = null;

		/**
		 * Variable to hold Admin object.
		 *
		 * @var DKQPS_Admin
		 */
		public $admin;

		/**
		 * DKQPS_Core constructor.
		 */
		public function __construct() {
			/**
			 * Load important variables and constants
			 */
			$this->define_plugin_properties();

			/**
			 * Initiates and load hooks
			 */
			$this->load_hooks();
		}

		/**
		 * Defining constants
		 */
		public function define_plugin_properties() {
			define('DKQPS_VERSION', '1.5.1');
			define('DKQPS_PLUGIN_FILE', __FILE__);
			define('DKQPS_PLUGIN_DIR', __DIR__);
			define('DKQPS_PLUGIN_SLUG', 'quick-plugin-switcher');
			add_action('plugins_loaded', array( $this, 'load_wp_dependent_properties' ), 1);
		}

		/**
		 * Define wp dependent properties.
		 */
		public function load_wp_dependent_properties() {
			define('DKQPS_PLUGIN_URL', untrailingslashit(plugin_dir_url(DKQPS_PLUGIN_FILE)));
			define('DKQPS_PLUGIN_BASENAME', plugin_basename(__FILE__));
		}

		/**
		 * Adding actions.
		 */
		public function load_hooks() {
			/**
			 * Initialize Localization
			 */
			add_action('init', array( $this, 'localization' ));
			add_action('plugins_loaded', array( $this, 'load_classes' ), 1);
		}

		/**
		 * Loading plugin text domain.
		 */
		public function localization() {
			load_plugin_textdomain('quick-plugin-switcher', false, __DIR__ . '/languages/');
		}

		/**
		 * Loading classes.
		 */
		public function load_classes() {
			/**
			 * Loads the Admin file
			 */
			include __DIR__ . '/admin/class-dkqps-admin.php';
			$this->admin = DKQPS_Admin::get_instance();
		}

		/**
		 * Function to create a new instance.
		 *
		 * @return DKQPS_Core
		 */
		public static function get_instance() {
			if (null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}
/**
 * Initiating the class object.
 */
if (! function_exists('DKQPS_Core') ) {
	/**
	 * Creating a new instance.
	 *
	 * @return DKQPS_Core|null
	 */
	function DKQPS_Core() {
		return DKQPS_Core::get_instance();
	}
}
DKQPS_Core();
