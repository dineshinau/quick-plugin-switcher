(function( $ ) {
	'use strict';
	/**
	 * @link              https://dineshinaublog.wordpress.com
	 * @since             1.0.0
 	 * @package           Dk_Quick_Plugin_Switcher
 	 * 
	 * Disabling the plugin checkbox on plugins listing page so that 
	 * this plugin "Quick Plugin Switcher" can be avoided from bulk actions.
	 * So no bulk action can be applied on this plugin until this plugin is activated.
	 */
	
	$(document).ready(function(){
		$("tr[data-slug='quick-plugin-switcher'] th input").prop('disabled',true);
	});
})( jQuery );
