<?php
/**
 * Plugin Name:       Quick Plugin Switcher
 * Plugin URI:        https://dineshinaublog.wordpress.com/quick-plugin-switcher
 * Description:       This simplifies plugin handling operations by adding a new bulk action "Switch" on this page and also adds easy "Activate Again" & "Deactivate Again" links on plugin notices. You can delete a plugin directly from deactivated notice too.
 * Version:           1.5.0
 * Author:            Dinesh Yadav
 * Author URI:        https://dineshinaublog.wordpress.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quick-plugin-switcher
 * Domain Path:       /languages
 *
 * Requires at least: 4.9.0
 * Tested up to: 5.6.0
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

if ( ! class_exists( 'DKQPS_Core' ) ) {

	class DKQPS_Core {
		/**
		 * @var DKQPS_Core
		 */
		public static $_instance = null;

		/**
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

			register_deactivation_hook( __FILE__, [ $this, 'deactivate_dk_quick_plugin_switcher' ] );
		}

		/**
		 * Defining constants
		 */
		public function define_plugin_properties() {
			define( 'DKQPS_VERSION', '1.5.0' );
			define( 'DKQPS_PLUGIN_FILE', __FILE__ );
			define( 'DKQPS_PLUGIN_DIR', __DIR__ );
			define( 'DKQPS_PLUGIN_SLUG', 'quick-plugin-switcher' );
			add_action( 'plugins_loaded', array( $this, 'load_wp_dependent_properties' ), 1 );
		}

		public function load_wp_dependent_properties() {
			define( 'DKQPS_PLUGIN_URL', untrailingslashit( plugin_dir_url( DKQPS_PLUGIN_FILE ) ) );
			define( 'DKQPS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}

		public function load_hooks() {
			/**
			 * Initialize Localization
			 */
			add_action( 'init', array( $this, 'localization' ) );
			add_action( 'plugins_loaded', array( $this, 'load_classes' ), 1 );
		}

		public function localization() {
			load_plugin_textdomain( 'quick-plugin-switcher', false, __DIR__ . '/languages/' );
		}

		public function load_classes() {
			/**
			 * Loads the Admin file
			 */
			require __DIR__ . '/admin/class-dkqps-admin.php';
			$this->admin = DKQPS_Admin::get_instance();
		}

		/**
		 * @return DKQPS_Core
		 */
		public static function get_instance() {
			if ( null === self::$_instance ) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		/**
		 * The code that runs during QPS deactivation.
		 * This action is documented in includes/class-dkqps-deactivator.php
		 */
		public function deactivate_dk_quick_plugin_switcher() {
			$dkqps_core = new DKQPS_Core();
			/**
			 * @since 1.4
			 * Deleting the option key dkqps_ssp_plugin on plugin deactivation
			 */
			$this->admin->dkqps_delete_option_key();
		}
	}
}
if ( ! function_exists( 'DKQPS_Core' ) ) {
	/**
	 * @return DKQPS_Core
	 */
	function DKQPS_Core() {
		return DKQPS_Core::get_instance();
	}
}

DKQPS_Core();