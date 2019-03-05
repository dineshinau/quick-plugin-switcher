<?php
/**
 * The core functionality of the QPS
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0
 *
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/admin
 */

/**
 * Defines the QPS name, version, and hooks to enqueue the admin-specific JavaScript.
 *
 * @package    Quick_Plugin_Switcher
 * @subpackage Quick_Plugin_Switcher/admin
 * @author     Dinesh Kumar Yadav <dineshinau@gmail.com>
 */
class Dk_Quick_Plugin_Switcher_Admin {
	/**
	 * The ID of this QPS.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this QPS.
	 */
	private $plugin_name;

	/**
	 * The version of this QPS.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this QPS.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param    string    $plugin_name       The name of this QPS.
	 * @param    string    $version    The version of this QPS.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	/**
	 * Registering the JavaScript for the admin area.
	 *
	 * @since    1.0
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
	* Adding a new dropdown option "Switch" in plugins bulk action in single site environment
	* 
	* @since	1.0
	* @param	array	$actions	The array of all bulk actions
	* @hooked 'bulk_actions-plugins'
	*/
	public function dk_quick_bulk_actions($actions){
		return array_merge(array('dk_switch' => __('Switch',$this->plugin_name)),$actions);		
	}
	/**
	* Hanndling the switch action when triggered in single site environment
	* 
	* @since	1.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action		containing the switch action
	* @param 	array	$post_ids	array of all selected plugins 
	*/
	public function dk_handle_quick_bulk_actions($redirect_to, $action, $post_ids){
		
		if($action != 'dk_switch'){
			// Return if switch action is not triggered
			return $redirect_to;
		}
		$act = $deact = 0;	
		
		// Fetching array of the all active plugins from database
		$active_plugins =  get_option('active_plugins');
							
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
		//Updating option back to the database after switching
		update_option('active_plugins', $active_plugins);

		$qry_args = array('dk_act' => $act, 'dk_deact' => $deact);

		if (count($post_ids)==1) {
			$qry_args['dkqps_bulk_ssp'] = $post_ids[0];  //ssp-single switched plugin
			$qry_args['dkqps_ssp'] = ''; //ssp-single switched plugin
		}
		
		//Redirecting to same plguin page with query arguments
		//$redirect_to = wp_nonce_url();
		//$url = wp_nonce_url('plugins.php?action=delete-selected&verify-delete=1&' . implode('&', $checked), 'bulk-plugins');
		///return wp_nonce_url(add_query_arg($qry_args, $redirect_to),'dk_switched');
		return add_query_arg($qry_args, $redirect_to);
	}
	
	/**
	* Hanndling the switch action when triggered on network plugins page
	* 
	* @since	1.0
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
	* Displaying switched plugin success notice when switched using 'switch' bulk action
	* 
	* @since	1.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action		containing the switch action
	* @param 	array	$post_ids	array of all selected plugins 
	* @hooked 	'network_admin_notices' and 'admin_notices'
	*/
	public function switch_success_admin_notice(){
		//Adding switch link to success notice when there is only only plugin is switch using bulk switch action
        if (isset($_GET['dkqps_bulk_ssp'])&& is_admin()) {
	   		if( !function_exists('get_plugin_data') ){
			    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$pfile = ABSPATH.'wp-content/plugins/'.$_GET['dkqps_bulk_ssp'];
	    	$pdata = get_plugin_data($pfile, true,true);
	    	
	    	$pname = $pdata['Name']; ?>
	    	<div class="notice notice-success is-dismissible">
		        <p><?php printf(__( '"<strong>%s</strong>" '.(($_GET['dk_deact'] == 1) ? "is deactivated" : "is activated" )), $pname); ?><a style="margin-left: 10px;" class="button-secondary" href="<?php echo admin_url('plugins.php?dkqps_ssp=').$_GET['dkqps_bulk_ssp'] ?>"><?php echo ($_GET['dk_act'] == 1) ? "Deactivate it Again" : "Activate it Again"; ?></a></p>
		    </div>	    	
	    	<?php
	    } else{ ?>
	    	<div class="notice notice-success is-dismissible">
		        <p><?php printf(__( 'All Selected %s activated '.(($_GET['dk_deact'] > 1) ? "plugins are" : "plugin is" ).' now deactivated and all selelcted %s deactivated '.(($_GET['dk_act'] > 1) ? "plugins are" : "plugin is" ).' now activated successfully!', $this->plugin_name ), $_GET['dk_deact'],$_GET['dk_act']); ?></p>
		    </div>
	    <?php }
	}

	/**
	* Switching the plugin again when clicking on switch link in modified success notices
	* @since	1.3
	* @hooked 'admin_init'
	*/
	public function dkqps_again_switch_the_plugin(){
		if (isset($_GET['dkqps_ssp']) && !empty($_GET['dkqps_ssp'])) {
			//check_admin_referer('dk_switched_nonce');
			$dkqps_ssp = $_GET['dkqps_ssp'];  
			$active_plugins =  get_option('active_plugins');
			
			if(is_array($active_plugins) && in_array($dkqps_ssp, $active_plugins)){				
				unset($active_plugins[array_search($dkqps_ssp, $active_plugins)]);				
			}else{
				array_push($active_plugins, $dkqps_ssp);
			}
			update_option('active_plugins',$active_plugins);
		}		
	}

	/**
	* Shows notice for again switched plugin with switch link on single switched plugin notice
	* @hooked admin_notices
	* @since 1.3
	*/
	public function dkpqs_again_switched_success_admin_notice(){
		$dkqps_ssp = $_GET['dkqps_ssp'];
		if (!empty($dkqps_ssp)) {
			$active_plugins =  get_option('active_plugins');
			$activated = in_array($dkqps_ssp, $active_plugins);

			if( !function_exists('get_plugin_data') ){
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$pfile = ABSPATH.'wp-content/plugins/'.$dkqps_ssp;
	    	$pdata = get_plugin_data($pfile, true,true);

	    	$pname = $pdata['Name']; ?>
			<div class="notice notice-success is-dismissible">
		        <p><?php printf(__( '"<strong>%s</strong>" '.($activated ? "is activated" : "is deactivated" )), $pname); ?><a style="margin-left: 10px;" class="button-secondary" href="<?php echo admin_url('plugins.php?dkqps_ssp=').$dkqps_ssp ?>"><?php echo ($activated) ? "Deactivate it Again" : "Activate it Again"; ?></a></p>
		    </div>
		    <?php 
		}
	}

	/**
	* Updating natively activated/deactivated plugin in option table to add switched 
	* link to native success notice
	* @since 1.3 
	* @hooked on action hook 'activated_plugin' and 'deactivated_plugin' 
	*/
	public function dkqps_update_switched_plugin($plugin, $network_wide){
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$pfile = ABSPATH.'wp-content/plugins/'.$plugin;
    	$pdata = get_plugin_data($pfile, true,true);
    	$pname = isset($pdata['Name']) ? $pdata['Name'] : '';
    	$pname = empty($pname) ? array() : array('switched'=> $pname,'dkqps_ssp'=> $plugin);

		update_option('dkqps_switched_plugin',$pname);
		//update_option('dkqps_network_wide',$network_wide);
	}

	/**
	* Adding switch links to native success notice when activated/deactivate
	* using native 'activate/deactivate' link
	* @since 1.3
	* @hooked on filter hook 'gettext'
	*/
	public function dkqps_add_switching_link($translated_text, $untranslated_text, $domain){
		$activated_notice = "Plugin <strong>activated</strong>.";
		$deactivated_notice = "Plugin <strong>deactivated</strong>.";

		if ( $activated_notice === $untranslated_text ){
			$plug_name = get_option('dkqps_switched_plugin',true);

	        $pname = $plug_name['switched'];
	        $translated = $pname;
	        $pbasename = $plug_name['dkqps_ssp'];

	        $link = "plugins.php?dkqps_ssp=".$pbasename;
	        $qps = $this->plugin_name.'/'.$this->plugin_name.'.php';
	    	
	        $translated_text = "<strong>".$pname."</strong> is Activated.";
	        if ($qps !== $pbasename) {
	        	$translated_text.="<a style='margin-left: 10px;' class='button-secondary' href='".admin_url($link)."'> Deactivate it Again</a>";
	        }
	        //return $translated_text;
        }elseif ($deactivated_notice === $untranslated_text) {
        	$plug_name = get_option('dkqps_switched_plugin',true);

	        $pname = $plug_name['switched'];
	        $translated = $pname;
	        $pbasename = $plug_name['dkqps_ssp'];

	        $link = "plugins.php?dkqps_ssp=".$pbasename;

	        $translated_text = "<strong>".$pname."</strong> is deactivated <a style='margin-left: 10px;' class='button-secondary' href='".admin_url($link)."'> Activate it Again </a>";
	        //return $translated_text;
        }
        return $translated_text;   
	}
}
