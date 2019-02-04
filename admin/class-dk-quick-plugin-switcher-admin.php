<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0.0
 *
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/admin
 */

/**
 * Defines the plugin name, version, and hooks to enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/admin
 * @author     DINESH KUMAR YADAV <dineshinau@gmail.com>
 */
class Dk_Quick_Plugin_Switcher_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	/**
	 * Registering the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * An instance of this class is passed to the run() function
		 * defined in Dk_Quick_Plugin_Switcher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dk_Quick_Plugin_Switcher_Loader is creating the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dk-quick-plugin-switcher-admin.js', array( 'jquery' ), $this->version, false );
	}
	
	/**
	* Adding a new dropdown option "Switch" in plugins bulk action
	* 
	* @since	1.0.0
	* @param	array	$actions	The array of all bulk actions
	*/
	public function dk_quick_bulk_actions($actions){
		return array_merge(array('dk_switch' => __('Switch',$this->plugin_name)),$actions);		
	}
	/**
	* Hanndling the switch action when triggered
	* 
	* @since	1.0.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action		containing the switch action
	* @param 	array	$post_ids	array of all selected plugins 
	*/
	public function dk_handle_quick_bulk_actions($redirect_to, $action, $post_ids){
		
		if($action != 'dk_switch') // Return if switch action is not triggered
			return $redirect_to;
		$act = $deact = 0;	
		
		// Fetching array of the all active plugins from database
		$active_plugins =  apply_filters('active_plugins', get_option('active_plugins'));
							
		// Loop through all post ids of plugins
		
		foreach((is_array($post_ids) || is_object($post_ids)) ? $post_ids : array() as $post_id){			
			if(is_array($active_plugins) && in_array($post_id, $active_plugins)){				
				unset($active_plugins[array_search($post_id, $active_plugins)]);
				$deact++;
			}				
			else{ 
				array_push($active_plugins, $post_id);
				$act++;	
			}
		}
		//Updating option back to database after switching
		update_option('active_plugins', $active_plugins); 
		
		//Redirecting to same plguin page with query arguments
		return add_query_arg(
					array(
						'dk_act'=> $act,
						'dk_deact' => $deact),
					$redirect_to);
	}
	
	/**
	* Hanndling the switch action when triggered on network plugins page
	* 
	* @since	1.0.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action		containing the switch action
	* @param 	array	$post_ids	array of all selected plugins 
	*/
	public function dk_handle_quick_bulk_network_actions($redirect_to, $action, $post_ids){
		
		//Returning to the plugin page when the "Switch" action is not triggered
		if($action != 'dk_switch')	
			return $redirect_to;
			
		//Fetching all site wide active plugins
		
		$active_plugins = get_site_option('active_sitewide_plugins');
		$act = $deact = 0;
		
		foreach ((is_array($post_ids) || is_object($post_ids)) ? $post_ids : array() as $post_id){
			if(is_array($active_plugins) && count($active_plugins) && array_key_exists($post_id, $active_plugins)){
				unset($active_plugins[$post_id]);	
				$deact++;
			}
			else {
				$active_plugins[$post_id]= time();	
				$act++;
			}
		}
		//Updating option back after switching		
		update_site_option('active_sitewide_plugins', $active_plugins);
	 	return add_query_arg(
					array(
						'dk_act'=> $act,
						'dk_deact' => $deact),
					$redirect_to); 	//Redirecting to same plguin page with query arguments	 
	}
	/**
	* Displaying admin success notice
	* 
	* @since	1.0.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action		containing the switch action
	* @param 	array	$post_ids	array of all selected plugins 
	*/
	public function switch_success_admin_notice(){?>
    <div class="notice notice-success is-dismissible">
        <p><?php printf(__( 'All Selected %s activated '.(($_GET['dk_deact'] > 1) ? "plugins are" : "plugin is" ).' now deactivated and all selelcted %s deactivated '.(($_GET['dk_act'] > 1) ? "plugins are" : "plugin is" ).' now activated successfully!', $this->plugin_name ), $_GET['dk_deact'],$_GET['dk_act']); ?></p>
    </div>
    <?php
	}
}
