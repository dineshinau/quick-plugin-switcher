<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * The core functionality of the QPS
 *
 * @link       https://dineshinaublog.wordpress.com
 * @since      1.0
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/admin
 */

/**
 * Defines the QPS name, version, and hooks to enqueue the admin-specific JavaScript.
 *
 * @package    quick-plugin-switcher
 * @subpackage quick-plugin-switcher/admin
 * @author     Dinesh Kumar Yadav <dineshinau@gmail.com>
 */
class DKQPS_Admin {
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
	 * The basename of this QPS.
	 *
	 * @since    1.3.1
	 * @access   private
	 * @var      string    $dkqps    The basename of this QPS.
	 */
	private $dkqps;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param    string    $plugin_name       The name of this QPS.
	 * @param    string    $version    The version of this QPS.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$this->dkqps 		= $this->plugin_name.'/'.$this->plugin_name.'.php';
	}
	
	/**
	 * Registering the JavaScript for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		/**
		 * The DKQPS_Loader is creating the relationship
		 * between the defined hooks and the functions defined in this class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dkqps-admin.js', array( 'jquery' ), $this->version, false );
	}
	
	/**
	* Adding a new dropdown option "Switch" in plugins bulk action in single site environment
	* 
	* @since	1.0
	* @param	array $actions The array of all bulk actions
	* @hooked 'bulk_actions-plugins'
	*/
	public function dkqps_add_switch_bulk_action($actions){
		return array_merge(array('dk_switch' => __('Switch','quick-plugin-switcher')),$actions);		
	}
	/**
	* Hanndling the switch action when triggered in single site environment
	* 
	* @since	1.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action		containing the switch action
	* @param 	array	$post_ids	array of all selected plugins 
	*/
	public function dkqps_handle_switch_bulk_action($redirect_to, $action, $post_ids){
		
		if( 'dk_switch' !== $action ){
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

		if ( 1 === count($post_ids)) {
			$plugin 		= $post_ids[0];
			$network_wide	= false;
			$this->dkqps_update_switched_plugin($plugin, network_wide);
		}
		return add_query_arg($qry_args, $redirect_to);
	}
	
	/**
	* Hanndling the switch action when triggered on network plugins page
	* 
	* @since	1.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action			containing the switch action
	* @param 	array	$post_ids		array of all selected plugins 
	* @return 	string 	redirect_to		redirect link with query strings
	*/
	public function dkqps_handle_switch_bulk_network_action($redirect_to, $action, $post_ids){
		
		//Returning to the plugin page when the "Switch" action is not triggered
		if( 'dk_switch'!== $action ){
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

		if (1 === count($post_ids) ) {
			$plugin 		=	$post_ids[0];
			$network_wide 	= 	true;
			$this->dkqps_update_switched_plugin($plugin, $network_wide);
		}

		//Updating option back after switching		
		update_site_option('active_sitewide_plugins', $active_plugins);
	 	return add_query_arg(
					array(
						'dk_act'=> $act,
						'dk_deact' => $deact),
					$redirect_to); 	//Redirecting to same plugin page with query arguments	 
	}

	/**
	* Updating natively activated/deactivated plugin in option table to add switch link to native success notice
	* @since 1.3 
	* @hooked on action hook 'activated_plugin' and 'deactivated_plugin' 
	*/
	public function dkqps_update_switched_plugin($plugin, $network_wide){
		if ($plugin === $this->dkqps && did_action('deactivated_plugin') ) {
			return;
		}

		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}		
		$plugin_file 		= ABSPATH.'wp-content/plugins/'.$plugin;
		$plugin_data 		= get_plugin_data($plugin_file, true,true);
		$plugin_name 		= isset($plugin_data['Name']) ? $plugin_data['Name'] : 'Plugin';
		$plugin_version		= isset($plugin_data['Version']) ? $plugin_data['Version'] : '0.0.0';
		$switched_plugin 	= empty($plugin_name) ? array() : array('name'=> $plugin_name, 'version' => $plugin_version, 'plugin'=> $plugin);

		 //ssp_plugin -> Single Switched Plugin
		if (is_network_admin() || $network_wide) {
			update_site_option('dkqps_ssp_plugin',$switched_plugin);
		}else{
			update_option('dkqps_ssp_plugin',$switched_plugin);	
		}				
	}

	/**
	* Displaying switched plugin success notice when switched using 'switch' bulk action
	* 
	* @since	1.0
	* @param	string	$redirect_to	URL where to redirect after performing action
	* @param	string	$action			containing the switch action
	* @param 	array	$post_ids		array of all selected plugins 
	* @hooked 	'network_admin_notices' and 'admin_notices'
	*/
	public function switch_success_admin_notice(){
		//Adding switch link to success notice when there is only only plugin is switch using bulk switch action
		$dk_act = intval( filter_input(INPUT_GET, 'dk_act',FILTER_SANITIZE_NUMBER_INT));
		$dk_deact = intval( filter_input(INPUT_GET, 'dk_deact',FILTER_SANITIZE_NUMBER_INT));

        if (( 1 === $dk_act && 0 === $dk_deact ) || ( 0 === $dk_act && 1 === $dk_deact )) {

	   		$switched_plugin 	= get_option('dkqps_ssp_plugin',true);
	   		if (is_network_admin()) {
	   			$switched_plugin 	= get_site_option('dkqps_ssp_plugin',true);
	   		}
	        $plugin_name 		= $switched_plugin['name'];
	        $plugin_version		= $switched_plugin['version'];
	        $plugin 			= $switched_plugin['plugin'];

	    	$activated 	= (1 === $dk_act) ? true : false;
	    	$action_url = $this->dkqps_get_action_url($plugin, $activated); ?>

	    	<div class="notice notice-success is-dismissible">
		        <p><span data-dkqps-blog_id="<?php echo get_current_blog_id() ?>" data-plugin="<?php echo $plugin ?>">
		        	<?php 
		        	if($activated){
		        		printf(__( '"<strong>%s (v%s)</strong>" is activated.', 'quick-plugin-switcher' ), $plugin_name, $plugin_version);
		        	}else{
		        		printf(__( '"<strong>%s (v%s)</strong>" is deactivated.', 'quick-plugin-switcher' ), $plugin_name, $plugin_version); 
		        	} ?>
		        	<a style="position: relative; left: 5px;" class="button-primary" href="<?php echo $action_url ?>">
		        		<?php if($activated){
		        			esc_html_e( 'Deactivate it again!', 'quick-plugin-switcher' );
		        		}else{
		        			esc_html_e( 'Activate it again!', 'quick-plugin-switcher' );
		        		} ?>
		        	</a>
		        	<?php if (!$activated && 1 === get_current_blog_id()) { ?>
		        		<a style="position: relative; left: 1%; color: #a00; text-decoration: none;" href="javascript:void(0);" class="dkqps-delete"><?php esc_html_e( 'Delete','quick-plugin-switcher' ) ?></a>
		        	<?php } ?>
		        	</span>
		        </p>
		    </div>
	    	<?php
	    } else{ ?>
	    	<div class="notice notice-success is-dismissible">
		       <p><?php 
		       	if ( 1 === $dk_act && 1 === $dk_deact ) {
		       		printf(__( 'The <strong>only</strong> selected <strong>active</strong> plugin is now <strong>deactivated</strong> and the <strong>only</strong> selected <strong>inactive</strong> plugin is now <strong>activated</strong> successfully.', 'quick-plugin-switcher' ));
		       	}elseif ($dk_act > 1 && 0 === $dk_deact ) {
		       		printf(__( 'All selected <strong>%d inactive</strong> plugins are <strong>activated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_act);
		       	}elseif (0 === $dk_act && 1 < $dk_deact) {
		       		printf(__( 'All selected <strong>%d active</strong> plugins are <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_deact);
		       	}elseif ( 1 === $dk_act && 1 < $dk_deact ) {
		       		printf(__( 'The <strong>only</strong> selected <strong>inactive</strong> plugin is <strong>activated</strong> and all selected <strong>%d active</strong> plugins are <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_deact);
		       	}elseif ($dk_act > 1 && 1 === $dk_deact ) {
		       		printf(__( 'All selected <strong>%d inactive</strong> plugins are <strong>activated</strong> and the <strong>only</strong> selected <strong>active</strong> plugin is <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_act);
		       	}else{
		       		printf(__( 'All selected <strong>%d inactive</strong> plugins are <strong>activated</strong> and all selected <strong>%d active</strong> plugins are <strong>deactivated</strong> now successfully.', 'quick-plugin-switcher' ), $dk_act, $dk_deact);
		       	} ?>
		       </p>		        
		    </div>
	    <?php }
	}

	/**
	* Adding switch links to native success notice when activated/deactivate
	* using native 'activate/deactivate' link
	* @since 1.3
	* @hooked on filter hook 'gettext'
	* @return $translated_text modified plugin success notice with switch links
	*/
	public function dkqps_add_switching_link($translated_text, $untranslated_text, $domain){
		$activated_notice 	= "Plugin <strong>activated</strong>.";
		$deactivated_notice = "Plugin <strong>deactivated</strong>.";

		$switched_plugin 	= get_option('dkqps_ssp_plugin',array());
		if (is_network_admin()) {
   			$switched_plugin 	= get_site_option('dkqps_ssp_plugin',true);
   		}

   		if (!is_array($switched_plugin) || (is_array($switched_plugin) && 3 !== count($switched_plugin))) {
   			return $translated_text;				
   		}

   		$plugin_name 		= $switched_plugin['name'];	        
    	$plugin 			= $switched_plugin['plugin'];
    	$plugin_version		= $switched_plugin['version'];
        
		if ( $activated_notice === $untranslated_text ){
	        $action_url = $this->dkqps_get_action_url($plugin, true);
	    	
	        $translated_text = sprintf(__('"<strong>%s (v%s)</strong>" is activated.','quick-plugin-switcher'),$plugin_name, $plugin_version);
	        
	        if ($this->dkqps !== $plugin) {
	        	$translated_text.="<a style='position: relative; left: 5px;' class='button-primary' href='".$action_url."'>".__('Deactivate it again!','quick-plugin-switcher')."</a>";
	        }
	        
        }elseif ($deactivated_notice === $untranslated_text) {
        	$action_url = $this->dkqps_get_action_url($plugin, false);

        	$translated_text = '<span data-dkpqs-blog-id="'.get_current_blog_id().'" data-plugin="'.$plugin.'">';
        	$translated_text.= sprintf(__('"<strong>%s (v%s)</strong>" is deactivated.','quick-plugin-switcher'),$plugin_name, $plugin_version);
        	$translated_text.= '<a style="position: relative; left: 5px;" class="button-primary" href="'.$action_url.'"> '.__('Activate it again!','quick-plugin-switcher').'</a>';

        	if (1 === get_current_blog_id()) {
        		$translated_text.='<a style="position: relative; left: 1%; color: #a00; text-decoration: none;" href="javascript:void(0);" class="dkqps-delete">' . __( 'Delete','quick-plugin-switcher' ) . '</a>';
        	}
        	
        	$translated_text.='</span>';
        }
        return $translated_text;   
	}
	
	/**
	* Creating activate/deactivate action links
	* 
	* @since	1.3
	* @param	string	$plugins	plugin basename
	* @param	string	$activated	current plugin action (activated/deactivated)
	* 
	* @return 	plugin action url for adding to modified notice
	*/
	public function dkqps_get_action_url($plugin, $activated){
		global $status, $page, $s, $totals;
        $context = $status;
        $action_url = '';
        if (empty($plugin)) {
        	return $action_url;
        }
        if ($activated) {
    		$action_url = wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin );	    		
    	}else{
    		$action_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . urlencode( $plugin ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin );	    		
    	}
    	return $action_url;
	}

	/**
	* @since	1.3.1
	* Function to check wheather dkqps is active on site.
	* @hooked wp_footer
	*/
	public function dkqps_add_footer_hidden_field(){
		echo '<input data-dkqps-blog_id="'.get_current_blog_id().'" type="hidden" value="dkqps-active">';
	}
}
