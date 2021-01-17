<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * The admin functionality of the QPS
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/admin
 * @author     Dinesh Yadav <dineshinau@gmail.com>
 */
class DKQPS_Admin {
	private static $ins = null;
	/**
	 * The new version of wp having new plugin activation/deactivation notice text
	 * To provide backward compatibility
	 *
	 * @since    1.4
	 * @access   private
	 * @var      string $dkqps_new_wp the new WP version 5.3
	 */
	private $dkqps_new_wp;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this QPS.
	 * @param string $version The version of this QPS.
	 *
	 * @since    1.0
	 */
	public function __construct() {
		$this->dkqps_new_wp = '5.3';

		global $pagenow;
		$is_plugins_page = false;

		if ( empty( $pagenow ) ) {
			$current_url     = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
			$is_plugins_page = ( strpos( $current_url, '/wp-admin/plugins.php' ) > 0 ) ? true : $is_plugins_page;
			$is_plugins_page = ( $is_plugins_page ) ? $is_plugins_page : ( strpos( $current_url, '/wp-admin/network/plugins.php' ) > 0 );
		}

		/**
		 * Adding admin js if on plugins.php page
		 */
		if ( 'plugins.php' === $pagenow || $is_plugins_page ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		/**
		 * Adding 'Switch' option in plugin 'bulk-actions' dropdown in single site environment
		 * @since 1.0
		 */
		add_filter( 'bulk_actions-plugins', [ $this, 'dkqps_add_switch_bulk_action' ], 999, 1 );
		add_filter( 'handle_bulk_actions-plugins', [ $this, 'dkqps_handle_switch_bulk_action' ], 10, 3 );

		/**
		 *  Making sure the function "is_plugin_active_for_network" exist before using plugin in multi-site environment
		 */
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		/**
		 * Adding 'Switch' option in plugin 'bulk-actions' dropdown in multi-site environment
		 * @since 1.0
		 */
		if ( is_plugin_active_for_network( DKQPS_PLUGIN_BASENAME ) ) {
			add_filter( 'bulk_actions-plugins-network', [ $this, 'dkqps_add_switch_bulk_action' ], 999, 1 );
			add_filter( 'handle_bulk_actions-plugins-network', [ $this, 'dkqps_handle_switch_bulk_network_action' ], 10, 3 );
		}

		/**
		 * Displaying success notice after successful switching using 'switch' bulk actions
		 * @since 1.0
		 */
		if ( isset( $_GET['dk_act'] ) && is_admin() ) { //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			if ( is_network_admin() ) {
				add_action( 'network_admin_notices', [ $this, 'switch_success_admin_notice' ], 10 );
			} else {
				add_action( 'admin_notices', [ $this, 'switch_success_admin_notice' ], 10 );
			}
		}

		/**
		 * Updating just switched plugin to option table to get it back for changing native success notice with the name of the plugin and switch links
		 * @since 1.3
		 */
		add_action( 'activated_plugin', [ $this, 'dkqps_update_switched_plugin' ], 10, 2 );
		add_action( 'deactivated_plugin', [ $this, 'dkqps_update_switched_plugin' ], 10, 2 );

		/**
		 * Modify native plugin activated/deactivated notice with name of the plugin and switch link ot it
		 * @since 1.3
		 */
		if ( is_admin() && isset( $_GET['plugin_status'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			add_filter( 'gettext', [ $this, 'dkqps_add_switching_link' ], 99, 3 );
		}

	}

	/**
	 * @return DKQPS_Admin|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	/**
	 * Registering the JavaScript for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( DKQPS_PLUGIN_SLUG, DKQPS_PLUGIN_URL . '/admin/js/dkqps-admin.js', array( 'jquery' ), DKQPS_VERSION . rand( 1, 1000 ), false );
	}

	/**
	 * Adding a new dropdown option "Switch" in plugins bulk action in single site environment
	 *
	 * @param $actions
	 *
	 * @return array
	 * @since    1.0
	 */
	public function dkqps_add_switch_bulk_action( $actions ) {
		return array_merge( array( 'dk_switch' => __( 'Switch', 'quick-plugin-switcher' ) ), $actions );
	}

	/**
	 * Handling the switch action when triggered in single site environment
	 *
	 * @param $redirect_to
	 * @param $action
	 * @param $post_ids
	 *
	 * @return string
	 */
	public function dkqps_handle_switch_bulk_action( $redirect_to, $action, $post_ids ) {

		if ( 'dk_switch' !== $action ) {
			// Return if switch action is not triggered
			return $redirect_to;
		}
		$act = $deact = 0;

		// Fetching array of the all active plugins from database
		$active_plugins = get_option( 'active_plugins' );

		// Loop through all post ids of plugins		
		foreach ( ( is_array( $post_ids ) || is_object( $post_ids ) ) ? $post_ids : array() as $post_id ) {
			if ( is_array( $active_plugins ) && in_array( $post_id, $active_plugins, true ) ) {
				unset( $active_plugins[ array_search( $post_id, $active_plugins, true ) ] );
				$deact ++;
			} else {
				array_push( $active_plugins, $post_id );
				$act ++;
			}
		}
		//Updating option back to the database after switching
		update_option( 'active_plugins', $active_plugins );

		$qry_args = array( 'dk_act' => $act, 'dk_deact' => $deact );

		if ( 1 === count( $post_ids ) ) {
			$plugin       = $post_ids[0];
			$network_wide = false;
			$this->dkqps_update_switched_plugin( $plugin, $network_wide );
		}

		return add_query_arg( $qry_args, $redirect_to );
	}

	/**
	 * Handling the switch action when triggered on network plugins page
	 *
	 * @param string $redirect_to URL where to redirect after performing action
	 * @param string $action containing the switch action
	 * @param array $post_ids array of all selected plugins
	 *
	 * @return    string    redirect_to        redirect link with query strings
	 * @since    1.0
	 */
	public function dkqps_handle_switch_bulk_network_action( $redirect_to, $action, $post_ids ) {

		//Returning to the plugin page when the "Switch" action is not triggered
		if ( 'dk_switch' !== $action ) {
			return $redirect_to;
		}
		//Fetching all site wide active plugins		
		$active_plugins = get_site_option( 'active_sitewide_plugins' );
		$act            = $deact = 0;

		foreach ( ( is_array( $post_ids ) || is_object( $post_ids ) ) ? $post_ids : array() as $post_id ) {
			if ( is_array( $active_plugins ) && count( $active_plugins ) && array_key_exists( $post_id, $active_plugins ) ) {
				unset( $active_plugins[ $post_id ] );
				$deact ++;
			} else {
				$active_plugins[ $post_id ] = time();
				$act ++;
			}
		}

		if ( 1 === count( $post_ids ) ) {
			$plugin       = $post_ids[0];
			$network_wide = true;
			$this->dkqps_update_switched_plugin( $plugin, $network_wide );
		}

		//Updating option back after switching		
		update_site_option( 'active_sitewide_plugins', $active_plugins );

		return add_query_arg( array(
			'dk_act'   => $act,
			'dk_deact' => $deact
		), $redirect_to );    //Redirecting to same plugin page with query arguments
	}

	/**
	 * Updating natively activated/deactivated plugin in option table to add switch link to native success notice
	 * @since 1.3
	 * @hooked on action hook 'activated_plugin' and 'deactivated_plugin'
	 */
	public function dkqps_update_switched_plugin( $plugin, $network_wide ) {
		if ( $plugin === DKQPS_PLUGIN_BASENAME && did_action( 'deactivated_plugin' ) ) {
			return;
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_file     = ABSPATH . 'wp-content/plugins/' . $plugin;
		$plugin_data     = get_plugin_data( $plugin_file, true, true );
		$plugin_name     = isset( $plugin_data['Name'] ) ? $plugin_data['Name'] : 'Plugin';
		$plugin_version  = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '0.0.0';
		$switched_plugin = empty( $plugin_name ) ? array() : array( 'name' => $plugin_name, 'version' => $plugin_version, 'plugin' => $plugin );

		//ssp_plugin -> Single Switched Plugin
		if ( is_network_admin() || $network_wide ) {
			update_site_option( 'dkqps_ssp_plugin', $switched_plugin );
		} else {
			update_option( 'dkqps_ssp_plugin', $switched_plugin );
		}
	}

	/**
	 * Displaying switched plugin success notice when switched using 'switch' bulk action
	 *
	 * @hooked    'network_admin_notices' and 'admin_notices'
	 * @since    1.0
	 */
	public function switch_success_admin_notice() {
		//Adding switch link to success notice when there is only only plugin is switch using bulk switch action
		$dk_act   = intval( filter_input( INPUT_GET, 'dk_act', FILTER_SANITIZE_NUMBER_INT ) );
		$dk_deact = intval( filter_input( INPUT_GET, 'dk_deact', FILTER_SANITIZE_NUMBER_INT ) );

		if ( ( 1 === $dk_act && 0 === $dk_deact ) || ( 0 === $dk_act && 1 === $dk_deact ) ) {

			$switched_plugin = get_option( 'dkqps_ssp_plugin', true );
			if ( is_network_admin() ) {
				$switched_plugin = get_site_option( 'dkqps_ssp_plugin', true );
			}
			$plugin_name    = $switched_plugin['name'];
			$plugin_version = $switched_plugin['version'];
			$plugin         = $switched_plugin['plugin'];

			$plugin_section = filter_input( INPUT_GET, 'plugin_status', FILTER_SANITIZE_STRING );

			$activated  = ( 1 === $dk_act ) ? true : false;
			$action_url = $this->dkqps_get_action_url( $plugin, $activated ); ?>

            <div class="notice notice-success is-dismissible">
                <p><span data-dkqps-blog_id="<?php echo get_current_blog_id() ?>" data-plugin="<?php echo $plugin ?>">
		        	<?php
			        if ( $activated ) {
				        printf( __( '"<strong>%s (v%s)</strong>" is activated.', 'quick-plugin-switcher' ), $plugin_name, $plugin_version );
			        } else {
				        printf( __( '"<strong>%s (v%s)</strong>" is deactivated.', 'quick-plugin-switcher' ), $plugin_name, $plugin_version );
			        } ?>
		        	<a style="position: relative; left: 5px;" class="button-primary" href="<?php echo esc_url( $action_url ) ?>">
		        		<?php if ( $activated ) {
					        esc_html_e( 'Deactivate it again!', 'quick-plugin-switcher' );
				        } else {
					        esc_html_e( 'Activate it again!', 'quick-plugin-switcher' );
				        } ?>
		        	</a>
		        	<?php if ( ( 'active' !== $plugin_section ) && ! $activated && ( ! is_multisite() || ( is_multisite() && is_network_admin() ) ) ) { ?>
                        <a style="position: relative; left: 1%; color: #a00; text-decoration: none;" href="javascript:void(0);" class="dkqps-delete"><?php esc_html_e( 'Delete', 'quick-plugin-switcher' ) ?></a>
			        <?php } ?>
		        	</span>
                </p>
            </div>
			<?php
		} else { ?>
            <div class="notice notice-success is-dismissible">
                <p><?php
					if ( 1 === $dk_act && 1 === $dk_deact ) {
						printf( __( 'The <strong>only</strong> selected <strong>active</strong> plugin is now <strong>deactivated</strong> and the <strong>only</strong> selected <strong>inactive</strong> plugin is now <strong>activated</strong> successfully.', 'quick-plugin-switcher' ) );
					} elseif ( $dk_act > 1 && 0 === $dk_deact ) {
						printf( __( 'All selected <strong>%d inactive</strong> plugins are <strong>activated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_act );
					} elseif ( 0 === $dk_act && 1 < $dk_deact ) {
						printf( __( 'All selected <strong>%d active</strong> plugins are <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_deact );
					} elseif ( 1 === $dk_act && 1 < $dk_deact ) {
						printf( __( 'The <strong>only</strong> selected <strong>inactive</strong> plugin is <strong>activated</strong> and all selected <strong>%d active</strong> plugins are <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_deact );
					} elseif ( $dk_act > 1 && 1 === $dk_deact ) {
						printf( __( 'All selected <strong>%d inactive</strong> plugins are <strong>activated</strong> and the <strong>only</strong> selected <strong>active</strong> plugin is <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_act );
					} else {
						printf( __( 'All selected <strong>%d inactive</strong> plugins are <strong>activated</strong> and all selected <strong>%d active</strong> plugins are <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_act, $dk_deact );
					} ?>
                </p>
            </div>
		<?php }
	}

	/**
	 * Adding switch links to native success notice when activated/deactivate using native 'activate/deactivate' link
	 *
	 * @param $translated_text
	 * @param $untranslated_text
	 * @param $domain
	 *
	 * @return string
	 *
	 * @since 1.3
	 * @hooked on filter hook 'gettext'
	 *
	 */

	public function dkqps_add_switching_link( $translated_text, $untranslated_text, $domain ) {
		global $wp_version;
		$wp_pre_53 = false;
		if ( version_compare( $wp_version, $this->dkqps_new_wp, '<' ) ) {
			$wp_pre_53 = true;
		}

		$activated_notice   = $wp_pre_53 ? "Plugin <strong>activated</strong>." : "Plugin activated.";
		$deactivated_notice = $wp_pre_53 ? "Plugin <strong>deactivated</strong>." : "Plugin deactivated.";

		$switched_plugin = get_option( 'dkqps_ssp_plugin', array() );
		if ( is_network_admin() ) {
			$switched_plugin = get_site_option( 'dkqps_ssp_plugin', true );
		}

		if ( ! is_array( $switched_plugin ) || ( is_array( $switched_plugin ) && 3 !== count( $switched_plugin ) ) ) {
			return $translated_text;
		}

		$plugin_name    = $switched_plugin['name'];
		$plugin         = $switched_plugin['plugin'];
		$plugin_version = $switched_plugin['version'];

		$plugin_section = filter_input( INPUT_GET, 'plugin_status', FILTER_SANITIZE_STRING );

		if ( $activated_notice === $untranslated_text ) {
			$action_url = $this->dkqps_get_action_url( $plugin, true );

			$translated_text = sprintf( __( '"<strong>%s (v%s)</strong>" is activated.', 'quick-plugin-switcher' ), $plugin_name, $plugin_version );

			if ( DKQPS_PLUGIN_BASENAME !== $plugin ) {
				$translated_text .= "<a style='position: relative; left: 5px;' class='button-primary' href='" . $action_url . "'>" . __( 'Deactivate it again!', 'quick-plugin-switcher' ) . "</a>";
			}

		} elseif ( $deactivated_notice === $untranslated_text ) {
			$action_url = $this->dkqps_get_action_url( $plugin, false );

			$translated_text = '<span class="dkqps-plugin-data" data-dkpqs-blog-id="' . get_current_blog_id() . '" data-plugin="' . $plugin . '">';
			$translated_text .= sprintf( __( '"<strong>%s (v%s)</strong>" is deactivated.', 'quick-plugin-switcher' ), $plugin_name, $plugin_version );
			$translated_text .= '<a style="position: relative; left: 5px;" class="button-primary" href="' . $action_url . '"> ' . __( 'Activate it again!', 'quick-plugin-switcher' ) . '</a>';

			if ( 'active' !== $plugin_section && ! is_multisite() || ( is_multisite() && is_network_admin() ) ) {
				$translated_text .= '<a style="position: relative; left: 1%; color: #a00; text-decoration: none;" href="javascript:void(0);" class="dkqps-delete">' . __( 'Delete', 'quick-plugin-switcher' ) . '</a>';
			}

			$translated_text .= '</span>';
		}

		return $translated_text;
	}

	/**
	 * Creating activate/deactivate action links
	 *
	 * @param $plugin
	 * @param $activated
	 *
	 * @return string
	 * @since    1.3
	 */
	public function dkqps_get_action_url( $plugin, $activated ) {
		global $status, $page, $s;
		$context    = $status;
		$action_url = '';
		if ( empty( $plugin ) ) {
			return $action_url;
		}
		if ( $activated ) {
			$action_url = wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
		} else {
			$action_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . urlencode( $plugin ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
		}

		return $action_url;
	}

	/**
	 * @since 1.4
	 * Delete the option key dkqps_ssp_plugin on plugin deactivation
	 */
	public function dkqps_delete_option_key() {
		delete_option( 'dkqps_ssp_plugin' );
	}
}
