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

		$qry_args = array('dk_act' => $act, 'dk_deact' => $deact);

		if (count($post_ids)==1) {
			$qry_args['name'] = $post_ids[0];
		}
		
		//Redirecting to same plguin page with query arguments
		return add_query_arg($qry_args, $redirect_to);		
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
		if($action != 'dk_switch'){
			return $redirect_to;
		}
			
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
	public function switch_success_admin_notice(){
       if (isset($_GET['name'])&& is_admin()) {
	   		if( !function_exists('get_plugin_data') ){
			    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$pfile = ABSPATH.'wp-content/plugins/'.$_GET['name'];
	    	$pdata = get_plugin_data($pfile, true,true);
	    	
	    	$pname = $pdata['Name']; ?>
	    	<div class="notice notice-success is-dismissible">
		        <p><?php printf(__( '"<strong>%s</strong>" '.(($_GET['dk_deact'] == 1) ? "is deactivated" : "is activated" )), $pname); ?><a style="margin-left: 10px;" class="button-secondary" href="<?php echo admin_url('plugins.php?plug_name=').$_GET['name'] ?>"><?php echo ($_GET['dk_act'] == 1) ? "Deactivate it Again" : "Activate it Again"; ?></a></p>
		    </div>	    	
	    	<?php
	    } else{ ?>
	    	<div class="notice notice-success is-dismissible">
		        <p><?php printf(__( 'All Selected %s activated '.(($_GET['dk_deact'] > 1) ? "plugins are" : "plugin is" ).' now deactivated and all selelcted %s deactivated '.(($_GET['dk_act'] > 1) ? "plugins are" : "plugin is" ).' now activated successfully!', $this->plugin_name ), $_GET['dk_deact'],$_GET['dk_act']); ?></p>
		    </div>
	    <?php }
	}

	/**
	* Switching the plugin again
	* @since	1.3
	*/
	public function dkqps_again_switch_the_plugin(){
		$plug_name = $_GET['plug_name'];
		$active_plugins =  apply_filters('active_plugins', get_option('active_plugins'));

		if(is_array($active_plugins) && in_array($plug_name, $active_plugins)){				
			unset($active_plugins[array_search($plug_name, $active_plugins)]);				
		}else{
			array_push($active_plugins, $plug_name);
		}
		update_option('active_plugins',$active_plugins);
	}

	/**
	* Show notice for again switched plugin
	* @since	1.3
	*/
	public function dkpqs_again_switched_success_admin_notice(){
		$plug_name = $_GET['plug_name'];
		$active_plugins =  apply_filters('active_plugins', get_option('active_plugins'));
		$activated = in_array($plug_name, $active_plugins);

		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$pfile = ABSPATH.'wp-content/plugins/'.$plug_name;
    	$pdata = get_plugin_data($pfile, true,true);

    	$pname = $pdata['Name']; ?>
		<div class="notice notice-success is-dismissible">
	        <p><?php printf(__( '"<strong>%s</strong>" '.($activated ? "is activated" : "is deactivated" )), $pname); ?><a style="margin-left: 10px;" class="button-secondary" href="<?php echo admin_url('plugins.php?plug_name=').$plug_name ?>"><?php echo ($activated) ? "Deactivate it Again" : "Activate it Again"; ?></a></p>
	    </div>
		<?php
	}

	/**
	* Updating activated plugin in option to add deactivation lik
	*/
	public function dkqps_update_activated_plugin($plugin, $network_wide){
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$pfile = ABSPATH.'wp-content/plugins/'.$plugin;
    	$pdata = get_plugin_data($pfile, true,true);
    	$pname = isset($pdata['Name']) ? $pdata['Name'] : '';
    	$pname = empty($pname) ? array() : array('act'=> $pname,'plug_name'=> $plugin);

		update_option('dkqps_activated_plugin',$pname);
		//update_option('dkqps_network_wide',$network_wide);
	}

	/**
	* Adding switch link to plugin notice
	*/
	public function dkqps_add_switching_link($translated_text, $untranslated_text, $domain){
		$activated_notice = "Plugin <strong>activated</strong>.";

		if ( $activated_notice !== $untranslated_text ){
            return $translated_text;
        }
        
        $translated = "Captain: The Core is stable and the Plugin is <strong>activated</strong> at full Warp speed";
        $plug_name = get_option('dkqps_activated_plugin',true);

        $activated = isset($plug_name['act']) ? true : false;
        $pname = ($activated) ? $plug_name['act'] : $plug_name['deact'];
        $translated = $pname;
        $pbasename = $plug_name['plug_name'];

        /*$new = "<p>".(__( '"<strong>%s</strong>" '.($activated ? "is activated" : "is deactivated" )), $pname)."<a style='margin-left: 10px;' class='button-secondary' href='".admin_url('plugins.php?plug_name=')"'>". ($activated) ? 'Deactivate it Again' : 'Activate it Again'."</a></p>";*/

        $link = "plugins.php?plug_name=".$pbasename;

        $translated = "<strong>".$pname."</strong> is". (($activated) ? ' Activated' : 'Deactovated')."<a style='margin-left: 10px;' class='button-secondary' href='".admin_url($link)."'>". (($activated) ? 'Deactivate it Again' : 'Activate it Again')."</a>";

        return $translated;
	}
}
