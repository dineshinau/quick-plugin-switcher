=== Quick Plugin Switcher ===
Contributors: dineshinau
Donate link: https://dineshinaublog.wordpress.com/donate-me
Tags: plugin, switcher, switch, change, quick, quick switcher, plugin switcher, quick plugin, change plugin status, plugin status switcher, qps
Requires at least: 4.7
Tested up to: 5.2
Stable tag: 1.4
Requires PHP: 5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This helps the admin(s)/super-admin(s) to activate and deactivate plugins faster by reducing the steps, which speeds up the process and minimize the efforts.

== Description ==

This adds a new option “Switch” in the drop-down menu of plugin bulk actions on plugin listing page using which admin(s)/super-admin(s) can easily change the state of plugin between activating and deactivate simultaneously with all selected plugins.

If you have any suggestion regarding the improvement of its feature, please leave a [Review](https://dineshinaublog.wordpress.com/quick-plugin-switcher/).

== Key Benifit of "Quick Plugin Switcher" ==

This plugin is useful in this way: – suppose at a time you(admin/super-admin) need to activate 5 plugins and deactivate other 8 plugins then normally it can be done in two steps as follows:-

1. In the first step, you need to select the 5 plugins you wish to activate and then you will need to select “Activate” bulk action from the dropdown and apply. 

2. In the second step, you again need to select the other 8 plugins you wish to deactivate and then you will need to select “Deactivate” bulk action from the dropdown and apply.

Now using “Quick Plugin Switcher” you can select all those 13 plugins (5 plugins, which you wish to activate + 8 plugins, which you wish to deactivate) and from bulk actions dropdown you can choose “Switch” bulk action and then "Apply". Now all the 5 active selected plugins will be deactivated and all 8 inactive selected plugins will be activated. So overall in this process, the page will be reloaded only once to activate and deactivate multiple plugins, while in the earlier process it will be reloaded two times that results in more time consumption.

Also in the case of more than 50 plugins installed on the site, it is not easy to remember which plugins were deactivated/activated in the first step and which plugins you were going to deactivate/activate.

This plugin can be found most useful in case of finding culprit plugin during plugin conflict issue. To find a conflicting plugin, it is required to deactivate some plugins and check whether the issue is resolved if not then we activate the deactivated plugins in the earlier step and deactivate some other plugins. Now it is possible to do these two steps in one-page load.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/quick-plugin-switcher` directory, or install the plugin through the WordPress plugins screen directly.

2. Activate the plugin through the \'Plugins\' screen in WordPress

3. Select one or more plugin(s) and choose option "Switch" from bulk actions dropdown to change state of selected plugin(s).


== Frequently Asked Questions ==

= How many plugins can be switched at a time? =
As many plugins(s) are selected before applying the "Switch" action.

= What is meant by switching a plugin? =

Switching a plugin means that changing it's state e.g. if a plugin is deactivated then after applying switch action it will be activated. Similarly, as many plugins are switched, will be activated/deactivated depending on their current state(deactivated/activated).

= Why this plugin itself can not be switched through "Switch" bulk action? =
The checkbox against this plugin is disabled after activation because this plugin switched itself to deactivate mode then the "Switch" option will be disappeared.

== Screenshots ==

1. Multisite Plugin Actions - is the screen that shows the plugin name displayed in plugin listing page and "Switch" option created inside bulk actions drop-down menu in the multisite environment. 
screenshot-1.png

2. Single site Plugin Actions - is the screen that shows "Switch" option created inside bulk actions drop-down menu in the single-site environment.
screenshot-2.png

3. Switch link on natively activated/deactivated success notice.
screenshot-3.png

= connect with me =

* **My Website** - https://dineshinaublog.wordpress.com/
* **My Facebook Page** - https://www.facebook.com/dineshinau
* **My Twitter Account** - https://twitter.com/dineshinau
* **My LinkedIn Account** - https://www.linkedin.com/in/dineshinau
   
== Upgrade Notice ==

= 1.0.1 =
* Now compatible with multisite
* Updated "Tested up to" value to 5.2

== Changelog ==

= 1.4 (31-07-2019) = 
* Added: Delete link on successfully deactivated notice

= 1.3 (17/03/2019) =
* Added: Compatible with WordPress 5.1
* Added: Switch again button on successful notices if a single plugin is switched
* Added: Added switch links on native activate/deactivate success notices
* Improved: Text domain strings and translations

= 1.2 (28/11/2017) =
* Tested with WordPress 4.9
* Updated "Tested up to" value to 4.9
* Added Requires PHP version

= 1.0.1 (02/06/2017) =
* Fixed the issue with multisite
* Added sucessful notice on multisite
* Fixed conflict with other plugins

= 1.0 (10/04/2017) =
This is Initial Release of the plugin