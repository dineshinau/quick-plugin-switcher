<?php
/**
 * QPS Uninstall
 *
 * Uninstalling QPS deletes its own options.
 *
 * @package quick-plugin-switcher
 * 
 * @version 1.6.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Deletion option key used by this plugin on deletion of this plugin
 */
$option_name = 'dkqps_ssp_plugin';
delete_option( $option_name );
