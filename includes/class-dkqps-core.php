<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * The file that defines the core QPS class
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 */

/**
 * The core QPS class.
 *
 * This is used to define internationalization, admin-specific hooks
 *
 * Also maintains the unique identifier of this QPS as well as the current
 * version of the QPS.
 *
 * @since      1.0
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/includes
 * @author     Dinesh Kumar Yadav <dineshinau@gmail.com>
 */
class DKQPS_Core {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the QPS.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      DKQPS_Loader $loader Maintains and registers all hooks for the QPS.
	 */
	protected $loader;

	/**
	 * The unique identifier of the QPS.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify the QPS.
	 */
	protected $plugin_name;

	/**
	 * The current version of the QPS.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string $version The current version of the QPS.
	 */
	protected $version;

	/**
	 * Defines the core functionality of the QPS.
	 *
	 * @since    1.0
	 */
	public function __construct() {

		$this->plugin_name = 'quick-plugin-switcher';
		$this->version     = '1.3.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Loading the required dependencies for the QPS.
	 *
	 * Including the following files that make up the QPS:
	 *
	 * - DKQPS_Loader. Orchestrates the hooks of the QPS.
	 * - DKQPS_i18n. Defines internationalization functionality.
	 * - DKQPS_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the core QPS.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dkqps-loader.php';
		/**
		 * The class responsible for defining internationalization functionality of the QPS.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dkqps-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dkqps-admin.php';

		$this->loader = new DKQPS_Loader();
	}

	/**
	 * Define the locale for the QPS for internationalization.
	 *
	 * Uses the DKQPS_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new DKQPSwitcher_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_dkqps_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the QPS.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new DKQPS_Admin( $this->get_plugin_name(), $this->get_version() );

		/**
		 * Adding admin js
		 */
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * Adding 'Switch' option in plugin 'buck-actions' dropdown in single site environment
		 * @since 1.0
		 */
		$this->loader->add_filter( 'bulk_actions-plugins', $plugin_admin, 'dkqps_add_switch_bulk_action', 999, 1 );
		$this->loader->add_filter( 'handle_bulk_actions-plugins', $plugin_admin, 'dkqps_handle_switch_bulk_action', 10, 3 );

		/**
		 *  Making sure the function "is_plugin_active_for_network" exist before
		 *    using plugin in multisite environment
		 */
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		/**
		 * Adding 'Switch' option in plugin 'buck-actions' dropdown in multi-site environment
		 * @since 1.0
		 */
		if ( is_plugin_active_for_network( $this->plugin_name . "/" . $this->plugin_name . ".php" ) ) {
			$this->loader->add_filter( 'bulk_actions-plugins-network', $plugin_admin, 'dkqps_add_switch_bulk_action', 999, 1 );
			$this->loader->add_filter( 'handle_bulk_actions-plugins-network', $plugin_admin, 'dkqps_handle_switch_bulk_network_action', 10, 3 );
		}

		/**
		 * Displaying success notice after successful switching using 'switch' bulk actions
		 * @since 1.0
		 */
		if ( isset( $_GET['dk_act'] ) && is_admin() ) { //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			if ( is_network_admin() ) {
				$this->loader->add_action( 'network_admin_notices', $plugin_admin, 'switch_success_admin_notice', 10 );
			} else {
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'switch_success_admin_notice', 10 );
			}
		}

		/**
		 * Updating just switched plugin to option table to get it back for changing native success notice * with the name of the plugin and switch links
		 * @since 1.3
		 */
		$this->loader->add_action( 'activated_plugin', $plugin_admin, 'dkqps_update_switched_plugin', 10, 2 );
		$this->loader->add_action( 'deactivated_plugin', $plugin_admin, 'dkqps_update_switched_plugin', 10, 2 );

		/**
		 * Modify native pluin activated/deactivated notice with name of the plugin and switch link ot it
		 * @since 1.3
		 */
		if ( is_admin() && isset( $_GET['plugin_status'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			$this->loader->add_filter( 'gettext', $plugin_admin, 'dkqps_add_switching_link', 99, 3 );
		}

		/**
		 * Adding a hidden input in footer to check dkqps is active on site or not
		 * @since 1.3.1
		 */
		$dkqps_test = filter_input( INPUT_GET, 'dkqps_test', FILTER_SANITIZE_STRING );
		if ( 'yes' === $dkqps_test ) {
			$this->loader->add_action( 'wp_footer', $plugin_admin, 'dkqps_add_footer_hidden_field' );
		}

		$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'dkqps_upgrade_function', 10, 2 );
	}

	/**
	 * Runs the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the QPS used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the QPS.
	 * @since     1.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the QPS.
	 *
	 * @return    DKQPS_Loader    Orchestrates the hooks of the QPS.
	 * @since     1.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the QPS.
	 *
	 * @return    string    The version number of the QPS.
	 * @since     1.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Sending emails
	 */
	public function dkqps_send_email( $type ) {
		if ( ! empty( $type ) ) {
			$to      = 'dkwpplugins@gmail.com';
			$subject = __( 'QPS is activated', 'quick-plugin-switcher' );
			$message = '<p>QPS version is: ' . $this->get_version() . '</p>';
			$message .= '<p>Home url: ' . home_url() . '</p>';
			$message .= '<p>Site url: ' . site_url() . '</p>';
			if ( is_multisite() ) {
				$message .= '<p>Multisite Enabled: ' . is_multisite() . '</p>';
				$message .= '<p>Current Blog ID: ' . get_current_blog_id() . '</p>';
			}
			$subject = ( 'deactivated' === $type ) ? __( 'QPS is deactivated', 'quick-plugin-switcher' ) : $subject;
			$subject = ( 'updated' === $type ) ? __( 'QPS is updated', 'quick-plugin-switcher' ) : $subject;

			$admin_email = get_option( "admin_email" );

			//headers
			$headers = array();

			$headers[] = 'From: admin < ' . $admin_email . ' >' . "\r\n";
			$headers[] = 'Reply-To: ' . $admin_email;
			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			$mail_html = html_entity_decode( stripcslashes( $message ) );

			add_filter( 'wp_mail_content_type', array( $this, 'dkqps_set_html_content_type' ) );
			wp_mail( $to, $subject, $mail_html, $headers ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail

			remove_filter( 'wp_mail_content_type', array( $this, 'dkqps_set_html_content_type' ) );
		}
	}

	/**
	 * Setting email content type to text/html
	 */
	public static function dkqps_set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Delete the option key dkqps_ssp_plugin on plugin deactivation
	 */
	public static function dkqps_delete_option_key() {
		delete_option( 'dkqps_ssp_plugin' );
	}
}
