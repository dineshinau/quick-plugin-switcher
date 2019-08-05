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

Quick Plugin Switcher(QPS) is the ONLY WordPress plugin that provides smooth and time efficent plugin handling operations.

== Description ==

It adds a new plugin bulk action "Switch" and easy links on admin notices on plugins page to simplify plugin operations.

If you have any suggestion regarding the improvement of its feature, please leave a [Review](https://dineshinaublog.wordpress.com/quick-plugin-switcher/).

== QPS Features ==

1. **New "Switch" bulk action**
QPS supplies a new bulk action "Switch" on admin plugin listing page that can be used to activate and deactivate differnt plugins simultaneously in one go rather than native activate and then deactivate in two page reload.(Screenshot-1)

2. **More useful Notices with links**
QPS modifies native plugin activated/deactivated notice to provide name and version of the switched plugin alongwith a useful links like "Activate it again!!" if deactivated mistakenly. (Screenshot 2, 3)

3. **Delete link on deactivated notice**
If you want to delete an active plugin then you can delete it directly from deactivated notice without scrolling down again and searching for the plugin.(Screenshot 4)

4. **Multisite support**
QPS is fully compatible in multisite environment (Screenshot 5)

= Connect with me =

* **Website** - https://dineshinaublog.wordpress.com/
* **Facebook** - https://www.facebook.com/dineshinau
* **Twitter** - https://twitter.com/dineshinau
* **LinkedIn** - https://www.linkedin.com/in/dineshinau


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/quick-plugin-switcher` directory, or install the plugin through the WordPress plugins screen directly.

2. Activate the plugin through the \'Plugins\' screen in WordPress

3. Select one or more plugin(s) and choose option "Switch" from bulk actions dropdown to change state of selected plugin(s).

4. Easily activate or delete the a deactivated plugin from the plugin deactivated admin notice. Remove need for scrolling upto the plugin and searching the plugin on page.


== Frequently Asked Questions ==

= How many plugins can be switched at a time? =
As many plugins(s) are selected before applying the "Switch" action.

= What is meant by switching a plugin? =

Switching a plugin means that changing it's state e.g. if a plugin is deactivated then after applying switch action it will be activated. Similarly, as many plugins are switched, will be activated/deactivated depending on their current state(deactivated/activated).

= Why this plugin itself can not be switched through "Switch" bulk action? =
The checkbox against this plugin is disabled after activation because this plugin switched itself to deactivate mode then the "Switch" option will be disappeared.

= Why I created this plugin =


== Screenshots ==

1. Multisite Plugin Actions - is the screen that shows the plugin name displayed in plugin listing page and "Switch" option created inside bulk actions drop-down menu in the multisite environment. 
screenshot-1.png

2. Single site Plugin Actions - is the screen that shows "Switch" option created inside bulk actions drop-down menu in the single-site environment.
screenshot-2.png

3. Switch link on natively activated/deactivated success notice.
screenshot-3.png
   
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