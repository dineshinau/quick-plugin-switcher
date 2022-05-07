<?php
/**
 * WooCommerce Uninstall
 *
 * Uninstalling WooCommerce deletes user roles, pages, tables, and options.
 *
 * @package WooCommerce\Uninstaller
 * @version 2.3.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Deletion option key used by this plugin on deletion of this plugin
 */
$option_name = 'dkqps_ssp_plugin';
delete_option( $option_name );
