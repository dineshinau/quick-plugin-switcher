(function( $ ) {
	'use strict';
	/**
	 * @link              https://dineshinaublog.wordpress.com
	 * @since             1.0
 	 * @package           quick-plugin-switcher
 	 * 
	 * Disabling the plugin checkbox on plugins listing page so that 
	 * this plugin "Quick Plugin Switcher" can be avoided from bulk actions.
	 * So no bulk action can be applied on this plugin until this plugin is activated.
	 *
	 *Triggering click on respective 'delete' button from notice delete link
	 */
	
	$(document).ready(function(){
		$("tr[data-slug='quick-plugin-switcher'] th input").prop('disabled',true);
		$('.dkqps-delete').on('click',function(){
			var data_plugin = $(this).parents('span').attr('data-plugin');
			$('tr[data-plugin="'+data_plugin+'"]').find('a.delete').trigger('click');
		});
	});
})( jQuery );
