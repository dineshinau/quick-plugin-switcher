<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0.0
 *
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/includes
 * @author     DINESH KUMAR YADAV <dineshinau@gmail.com>
 */
class Dk_Quick_Plugin_Switcher {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Dk_Quick_Plugin_Switcher_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'quick-plugin-switcher';
		$this->version = '1.2';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Loading the required dependencies for this plugin.
	 *
	 * Including the following files that make up the plugin:
	 *
	 * - Dk_Quick_Plugin_Switcher_Loader. Orchestrates the hooks of the plugin.
	 * - Dk_Quick_Plugin_Switcher_i18n. Defines internationalization functionality.
	 * - Dk_Quick_Plugin_Switcher_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dk-quick-plugin-switcher-loader.php';
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dk-quick-plugin-switcher-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dk-quick-plugin-switcher-admin.php';

		$this->loader = new Dk_Quick_Plugin_Switcher_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dk_Quick_Plugin_Switcher_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Dk_Quick_Plugin_Switcher_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Dk_Quick_Plugin_Switcher_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'bulk_actions-plugins', $plugin_admin, 'dk_quick_bulk_actions', 999,1 );
		$this->loader->add_filter( 'handle_bulk_actions-plugins', $plugin_admin, 'dk_handle_quick_bulk_actions', 10,3 );
			
		/**
		 *  Making sure the function " is_plugin_active_for_network" exist before 
		 *	using plugin in multisite environment
		 */ 
		if ( ! function_exists( 'is_plugin_active_for_network' ) ){
		    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		if( is_plugin_active_for_network( $this->plugin_name."/".$this->plugin_name.".php" ) ){
			$this->loader->add_filter( 'bulk_actions-plugins-network', $plugin_admin, 'dk_quick_bulk_actions', 999,1 );
			$this->loader->add_filter( 'handle_bulk_actions-plugins-network', $plugin_admin, 'dk_handle_quick_bulk_network_actions', 10,3 );	
		}

		/**
		* Displaying success notice after successful switching
		*/
		if(isset($_GET['dk_act'])){
			if(is_network_admin()){
				$this->loader->add_action( 'network_admin_notices', $plugin_admin, 'switch_success_admin_notice', 10 );
			}
			else{
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'switch_success_admin_notice', 10 );
			}
		}

		if (isset($_GET['dkqps_ssp']) && !empty($_GET['dkqps_ssp'])) {
			$this->loader->add_action('admin_init',$plugin_admin,'dkqps_again_switch_the_plugin',99);
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'dkpqs_again_switched_success_admin_notice', 10);
		}

		$this->loader->add_action('activated_plugin',$plugin_admin,'dkqps_update_switched_plugin',10,2);
		$this->loader->add_action('deactivated_plugin',$plugin_admin,'dkqps_update_switched_plugin',10,2);

		if (is_admin()) {
			$this->loader->add_filter('gettext',$plugin_admin,'dkqps_add_switching_link',99,3);
		}
	}
	/**
	 * Runs the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Dk_Quick_Plugin_Switcher_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
