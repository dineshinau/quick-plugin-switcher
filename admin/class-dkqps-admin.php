<?php
/**
 * Admin file.
 *
 * @package quick-plugin-switcher
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The admin functionality of the QPS.
 *
 * @link  https://dineshinaublog.wordpress.com
 * @since 1.0
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/admin
 */
class DKQPS_Admin {
	/**
	 * Instance variable.
	 *
	 * @var $ins ;
	 */
	private static $ins = null;
	/**
	 * The new version of wp having new plugin activation/deactivation notice text.
	 * To provide backward compatibility.
	 *
	 * @since 1.4
	 * @var   string $dkqps_new_wp the new WP version 5.3.
	 */
	private $dkqps_wp_53;

	/**
	 * * Initialize the class and set its properties.
	 *
	 * DKQPS_Admin constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->dkqps_wp_53 = '5.3';

		global $pagenow;
		$is_plugins_page = false;

		if ( empty( $pagenow ) ) {
			$current_url     = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '';
			$is_plugins_page = ( strpos( $current_url, '/wp-admin/plugins.php' ) > 0 ) ? true : $is_plugins_page;
			$is_plugins_page = ( $is_plugins_page ) ? $is_plugins_page : ( strpos( $current_url, '/wp-admin/network/plugins.php' ) > 0 );
		}

		/**
		 * Adding admin js if on plugins.php page.
		 */
		if ( 'plugins.php' === $pagenow || $is_plugins_page ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Adding 'Switch' option in plugin 'bulk-actions' dropdown in single site environment.
		 *
		 * @since 1.0
		 */
		add_filter( 'bulk_actions-plugins', array( $this, 'dkqps_add_switch_bulk_action' ), 999, 1 );
		add_filter( 'handle_bulk_actions-plugins', array( $this, 'dkqps_handle_switch_bulk_action' ), 10, 3 );

		/**
		 *  Making sure the function "is_plugin_active_for_network" exist before using plugin in multi-site environment.
		 */
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		/**
		 * Adding 'Switch' option in plugin 'bulk-actions' dropdown in multi-site environment.
		 *
		 * @since 1.0
		 */
		if ( is_plugin_active_for_network( DKQPS_PLUGIN_BASENAME ) ) {
			add_filter( 'bulk_actions-plugins-network', array( $this, 'dkqps_add_switch_bulk_action' ), 999, 1 );
			add_filter(
				'handle_bulk_actions-plugins-network',
				array(
					$this,
					'dkqps_handle_switch_bulk_network_action',
				),
				10,
				3
			);
		}

		/**
		 * Displaying success notice after successful switching using 'switch' bulk actions
		 *
		 * @since 1.0
		 */
		if ( is_admin() ) {
			$dk_act = filter_input( INPUT_GET, 'dk_act', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( ! is_null( $dk_act ) ) {
				if ( is_network_admin() ) {
					add_action( 'network_admin_notices', array( $this, 'switch_success_admin_notice' ), 10 );
				} else {
					add_action( 'admin_notices', array( $this, 'switch_success_admin_notice' ), 10 );
				}
			}
		}

		/**
		 * Updating just switched plugin to option table to get it back for changing native success notice with the name of the plugin and switch links.
		 *
		 * @since 1.3
		 */
		add_action( 'activated_plugin', array( $this, 'dkqps_update_switched_plugin' ), 10, 2 );
		add_action( 'deactivated_plugin', array( $this, 'dkqps_update_switched_plugin' ), 10, 2 );

		/**
		 * Modify native plugin activated/deactivated notice with name of the plugin and switch link ot it.
		 *
		 * @since 1.3
		 */
		if ( is_admin() ) {
			$plugin_status = filter_input( INPUT_GET, 'plugin_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( ! empty( $plugin_status ) ) {
				add_filter( 'gettext', array( $this, 'dkqps_add_switching_link' ), 99, 3 );
			}
			add_action( 'admin_bar_menu', array( $this, 'dkqps_maybe_add_wc_log_link_to_admin_bar' ), 100 );
		}
	}

	/**
	 * Creating an instance of this class.
	 *
	 * @return DKQPS_Admin|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	/**
	 * Registering the JavaScript for the admin area.
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( DKQPS_PLUGIN_SLUG, DKQPS_PLUGIN_URL . '/admin/js/dkqps-admin.js', array( 'jquery' ), DKQPS_VERSION . wp_rand( 1, 1000 ), false );
		wp_enqueue_style( DKQPS_PLUGIN_SLUG, DKQPS_PLUGIN_URL . '/admin/css/dkqps-admin.css', array(), DKQPS_VERSION . wp_rand( 1, 1000 ), false );
	}

	/**
	 * Adding a new dropdown option "Switch" in plugins bulk action in single site environment.
	 *
	 * @param array $actions Bulk actions.
	 *
	 * @return array
	 * @since  1.0
	 */
	public function dkqps_add_switch_bulk_action( $actions ) {
		return array_merge( array( 'dk_switch' => esc_html__( 'Switch', 'quick-plugin-switcher' ) ), $actions );
	}

	/**
	 * Handling the switch action when triggered in single site environment
	 *
	 * @param string $redirect_to Redirect URL.
	 * @param string $action Bulk action name.
	 * @param array  $post_ids Selected plugin ids.
	 *
	 * @return string
	 */
	public function dkqps_handle_switch_bulk_action( $redirect_to, $action, $post_ids ) {
		if ( 'dk_switch' !== $action ) {
			// Return if switch action is not triggered.
			return $redirect_to;
		}

		$act    = 0;
		$de_act = 0;

		// Fetching array of the all active plugins from database.
		$active_plugins = get_option( 'active_plugins' );

		// Loop through all post ids of plugins.
		foreach ( ( is_array( $post_ids ) || is_object( $post_ids ) ) ? $post_ids : array() as $post_id ) {
			if ( is_array( $active_plugins ) && in_array( $post_id, $active_plugins, true ) ) {
				unset( $active_plugins[ array_search( $post_id, $active_plugins, true ) ] );
				++$de_act;
			} else {
				array_push( $active_plugins, $post_id );
				++$act;
			}
		}

		// Updating option back to the database after switching.
		update_option( 'active_plugins', $active_plugins );

		$qry_args = array(
			'dk_act'   => $act,
			'dk_deact' => $de_act,
		);

		if ( 1 === count( $post_ids ) ) {
			$plugin       = $post_ids[0];
			$network_wide = false;
			$this->dkqps_update_switched_plugin( $plugin, $network_wide );
		}

		return add_query_arg( $qry_args, $redirect_to );
	}

	/**
	 * Handling the switch action when triggered on network plugins page.
	 *
	 * @param string $redirect_to URL where to redirect after performing action.
	 * @param string $action containing the switch action.
	 * @param array  $post_ids array of all selected plugins.
	 *
	 * @return string    redirect_to        redirect link with query strings
	 * @since  1.0
	 */
	public function dkqps_handle_switch_bulk_network_action( $redirect_to, $action, $post_ids ) {
		// Returning to the plugin page when the "Switch" action is not triggered.
		if ( 'dk_switch' !== $action ) {
			return $redirect_to;
		}

		// Fetching all site wide active plugins.
		$active_plugins = get_site_option( 'active_sitewide_plugins' );
		$act            = 0;
		$de_act         = 0;

		foreach ( ( is_array( $post_ids ) || is_object( $post_ids ) ) ? $post_ids : array() as $post_id ) {
			if ( is_array( $active_plugins ) && count( $active_plugins ) && array_key_exists( $post_id, $active_plugins ) ) {
				unset( $active_plugins[ $post_id ] );
				++$de_act;
			} else {
				$active_plugins[ $post_id ] = time();
				++$act;
			}
		}

		if ( 1 === count( $post_ids ) ) {
			$plugin       = $post_ids[0];
			$network_wide = true;
			$this->dkqps_update_switched_plugin( $plugin, $network_wide );
		}

		// Updating option back after switching.
		update_site_option( 'active_sitewide_plugins', $active_plugins );

		return add_query_arg(
			array(
				'dk_act'   => $act,
				'dk_deact' => $de_act,
			),
			$redirect_to
		);    // Redirecting to same plugin page with query arguments.
	}

	/**
	 * Updating natively activated/deactivated plugin in option table to add switch link to native success notice.
	 *
	 * @param string  $plugin Plugin name.
	 * @param boolean $network_wide Network wide plugin.
	 *
	 * @since  1.3
	 * @hooked on action hook 'activated_plugin' and 'deactivated_plugin'
	 */
	public function dkqps_update_switched_plugin( $plugin, $network_wide ) {
		if ( DKQPS_PLUGIN_BASENAME === $plugin && did_action( 'deactivated_plugin' ) ) {
			return;
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_file     = ABSPATH . 'wp-content/plugins/' . $plugin;
		$plugin_data     = get_plugin_data( $plugin_file, true, true );
		$plugin_name     = isset( $plugin_data['Name'] ) ? $plugin_data['Name'] : 'Plugin';
		$plugin_version  = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '0.0.0';
		$switched_plugin = empty( $plugin_name ) ? array() : array(
			'name'    => $plugin_name,
			'version' => $plugin_version,
			'plugin'  => $plugin,
		);

		// ssp_plugin -> Single Switched Plugin.
		if ( is_network_admin() || $network_wide ) {
			update_site_option( 'dkqps_ssp_plugin', $switched_plugin );
		} else {
			update_option( 'dkqps_ssp_plugin', $switched_plugin );
		}
	}

	/**
	 * Displaying switched plugin success notice when switched using 'switch' bulk action.
	 *
	 * @hooked 'network_admin_notices' and 'admin_notices'
	 * @since  1.0
	 */
	public function switch_success_admin_notice() {
		// Adding switch link to success notice when there is only plugin is switch using bulk switch action.
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

			$plugin_section = filter_input( INPUT_GET, 'plugin_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			$activated  = ( 1 === $dk_act ) ? true : false;
			$action_url = $this->dkqps_get_action_url( $plugin, $activated );
			?>
			<div class="notice notice-success is-dismissible">
				<p class="dkqps-notice">
					<span data-dkqps-blog_id="<?php echo esc_attr( get_current_blog_id() ); ?>"
							data-plugin="<?php echo esc_attr( $plugin ); ?>">
						<?php
						$switch_btn_text = esc_html__( 'Deactivate it again!', 'quick-plugin-switcher' );
						if ( $activated ) {
							printf( /* translators: 1: Plugin name, 2: Plugin version. */ esc_html__( '%1$s (v%2$s is activated.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $plugin_name ), esc_html( $plugin_version ) . ')</strong>' );
						} else {
							$switch_btn_text = esc_html__( 'Activate it again!', 'quick-plugin-switcher' );
							printf( /* translators: 1: Plugin name, 2: Plugin version. */ esc_html__( '%1$s (v%2$s  ctivated.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $plugin_name ), esc_html( $plugin_version ) . ')</strong>' );
						}
						?>
						<a class="dkqps-success-notice button-primary"
							href="<?php echo esc_url( $action_url ); ?>"><?php echo esc_html( $switch_btn_text ); ?></a>
						<?php
						if ( ( 'active' !== $plugin_section ) && ! $activated && ( ! is_multisite() || ( is_multisite() && is_network_admin() ) ) ) {
							?>
							<a href="#"
								class="dkqps-delete"><?php esc_html_e( 'Delete', 'quick-plugin-switcher' ); ?></a>
							<?php
						}
						?>
					</span>
				</p>
			</div>
			<?php
		} else {
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					if ( 1 === $dk_act && 1 === $dk_deact ) {
						printf( /* translators: 1: Strong opening tag, 2: Strong closing tag, 3: Strong opening tag, 4: Strong closing tag, 5: Strong opening tag, 6: Strong closing tag, 7: Strong opening tag, 8: Strong closing tag, 9: Strong opening tag, 10: Strong closing tag, 11: Strong opening tag, 12: Strong closing tag, 13: Strong opening tag, 14: Strong closing tag. */ esc_html__( 'The %1$s only %2$s selected %3$s active %4$s plugin is now %5$s deactivated %6$s and the %7$s only %8$s selected %9$s inactive %10$s plugin is now %11$s activated %12$s successfully.', 'quick-plugin-switcher' ), '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>' );
					} elseif ( $dk_act > 1 && 0 === $dk_deact ) {
						printf( /* translators: 1: Strong opening tag and activated plugins count, 2: Strong closing tag, 3: Strong opening tag, 4: Strong closing tag. */ esc_html__( 'All selected %1$s inactive %2$s plugins are %3$s activated %4$s now successfully.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $dk_act ), '</strong>', '<strong>', '</strong>' );
					} elseif ( 0 === $dk_act && 1 < $dk_deact ) {
						printf( /* translators: 1: Strong opening tag and deactivated plugins count, 2: Strong closing tag, 3: Strong opening tag, 4: Strong closing tag. */ esc_html__( 'All selected %1$s active %2$s plugins are %3$s deactivated %4$s now successfully.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $dk_deact ), '</strong>', '<strong>', '</strong>' );
					} elseif ( 1 === $dk_act && 1 < $dk_deact ) {
						printf( /* translators: 1: Strong opening tag, 2: Strong closing tag, 3: Strong opening tag, 4: Strong closing tag, 5: Strong opening tag, 6: Strong closing tag, 7: Strong opening tag and deactivated plugins count, 8: Strong closing tag, 9: Strong opening tag, 10: Strong closing tag. */ esc_html__( 'The %1$s only %2$s selected %3$s inactive %4$s plugin is %5$s activated %6$s and all selected %7$s active %8$s plugins are %9$s deactivated %10$s now successfully.', 'quick-plugin-switcher' ), '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>' . esc_html( $dk_deact ), '</strong>', '<strong>', '</strong>' );
					} elseif ( $dk_act > 1 && 1 === $dk_deact ) {
						printf( /* translators: 1: Strong opening tag and activated plugins count, 2: Strong closing tag, 3: Strong opening tag, 4: Strong closing tag, 5: Strong opening tag, 6: Strong closing tag, 7: Strong opening tag, 8: Strong closing tag, 9: Strong opening tag, 10: Strong closing tag. */ esc_html__( 'All selected %1$s inactive %2$s plugins are %3$s activated %4$s and the %5$s only %6$s selected %7$s active %8$s plugin is %9$s deactivated %10$s now successfully.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $dk_act ), '</strong>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>' );
					} else {
						printf( /* translators: 1: Strong opening tag and activated plugins count, 2: Strong closing tag, 3: Strong opening tag , 4: Strong closing tag, 5: Strong opening tag and deactivated plugins count, 6: Strong closing tag, 7: Strong opening tag, 8: Strong closing tag. */ esc_html__( 'All selected %1$s inactive %2$s plugins are %3$s activated %4$s and all selected %5$s active %6$s plugins are %7$s deactivated %8$s now successfully.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $dk_act ), '</strong>', '<strong>', '</strong>', '<strong>' . esc_html( $dk_deact ), '</strong>', '<strong>', '</strong>' );
					}
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Adding switch links to native success notice when activated/deactivate using native 'activate/deactivate' link.
	 *
	 * @param string $translated_text Translated text.
	 * @param string $untranslated_text Untranslated text.
	 * @param string $domain Domain.
	 *
	 * @return string
	 *
	 * @since  1.3
	 * @hooked on filter hook 'gettext'
	 */
	public function dkqps_add_switching_link( $translated_text, $untranslated_text, $domain ) {
		global $wp_version;
		$wp_pre_53 = false;
		if ( version_compare( $wp_version, $this->dkqps_wp_53, '<' ) ) {
			$wp_pre_53 = true;
		}

		$activated_notice   = $wp_pre_53 ? 'Plugin <strong>activated</strong>.' : 'Plugin activated.';
		$deactivated_notice = $wp_pre_53 ? 'Plugin <strong>deactivated</strong>.' : 'Plugin deactivated.';

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

		$plugin_section = filter_input( INPUT_GET, 'plugin_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $activated_notice === $untranslated_text ) {
			$action_url = $this->dkqps_get_action_url( $plugin, true );

			$translated_text = sprintf( /* translators: 1: Strong opening tag and plugin name, 2: Plugin version and strong closing tag. */ esc_html__( '%1$s (v%2$s is activated.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $plugin_name ), esc_html( $plugin_version ) . ')</strong>' );

			if ( DKQPS_PLUGIN_BASENAME !== $plugin ) {
				$translated_text .= "<a class='button-primary dkqps-success-notice' href='" . esc_url( $action_url ) . "'>" . __( 'Deactivate it again!', 'quick-plugin-switcher' ) . '</a>';
			}
		} elseif ( $deactivated_notice === $untranslated_text ) {
			$action_url = $this->dkqps_get_action_url( $plugin, false );

			$translated_text = '<span class="dkqps-plugin-data" data-dkpqs-blog-id="' . esc_attr( get_current_blog_id() ) . '" data-plugin="' . esc_attr( $plugin ) . '">';

			$translated_text .= sprintf( /* translators: 1: Strong opening tag and plugin name, 2: Plugin version and strong closing tag. */ esc_html__( '%1$s (v%2$s is deactivated.', 'quick-plugin-switcher' ), '<strong>' . esc_html( $plugin_name ), esc_html( $plugin_version ) . ')</strong>' );
			$translated_text .= '<a class="button-primary dkqps-success-notice" href="' . esc_url( $action_url ) . '"> ' . esc_html__( 'Activate it again!', 'quick-plugin-switcher' ) . '</a>';

			if ( 'active' !== $plugin_section && ! is_multisite() || ( is_multisite() && is_network_admin() ) ) {
				$translated_text .= '<a href="#" class="dkqps-delete dkqps-delete">' . esc_html__( 'Delete', 'quick-plugin-switcher' ) . '</a>';
			}

			$translated_text .= '</span>';
		}

		return $translated_text;
	}

	/**
	 * Creating activate/deactivate action links
	 *
	 * @param string  $plugin Plugin URL.
	 * @param boolean $activated Activated.
	 *
	 * @return string
	 * @since  1.3
	 */
	public function dkqps_get_action_url( $plugin, $activated ) {
		global $status, $page, $s;
		$context    = $status;
		$action_url = '';
		if ( empty( $plugin ) ) {
			return $action_url;
		}
		if ( $activated ) {
			$action_url = wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . rawurlencode( $plugin ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin );
		} else {
			$action_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . rawurlencode( $plugin ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin );
		}

		return $action_url;
	}

	/**
	 * Adding WC Log link on admin bar.
	 *
	 * @param object $admin_bar Admin bar.
	 *
	 * @return void
	 */
	public function dkqps_maybe_add_wc_log_link_to_admin_bar( $admin_bar ) {
		if ( defined( 'WC_VERSION' ) && class_exists( 'Woocommerce' ) ) {
			$admin_bar->add_menu(
				array(
					'id'    => 'dkqps_wc_log',
					'title' => 'WC Log',
					'href'  => admin_url( 'admin.php?page=wc-status&tab=logs' ),
					'meta'  => array(
						'title' => esc_attr__( 'WC Log Tab Link', 'quick-plugin-switcher' ),
					),
				)
			);
		}
	}
}
