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
		var data_plugin = $('span.dkqps-plugin-data').attr('data-plugin');
		if ( undefined !== data_plugin) {
			var delete_exist = $('tr[data-plugin="'+data_plugin+'"]').find('a.delete').length > 0;
			if (delete_exist) {
				$('.dkqps-delete').on('click',function(){
					var this_btn = this;			
					var btn_txt = $(this_btn).text();
					btn_txt = btn_txt.replace('...', '');
					$(this_btn).text(btn_txt+'...');
					setTimeout(function(){
						$(this_btn).text(btn_txt);
					}, 10000);
					$('tr[data-plugin="'+data_plugin+'"]').find('a.delete').trigger('click');
				});
			}else{
				$('.dkqps-delete').css('display','none');
			}			
		}		
	});
})( jQuery );
