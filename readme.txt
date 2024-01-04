=== Quick Plugin Switcher ===
Contributors: dineshinau
Donate link: https://dineshinaublog.wordpress.com/donate-me
Tags: qps, switcher, switch, change, quick, quick switcher, plugin switcher, quick plugin, change plugin status, plugin status switcher
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.6.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Quick Plugin Switcher(QPS) is the ONLY plugin that provides smooth and time-efficient plugin handling operations.

== Description ==

It adds a new plugin bulk action "Switch" and easy links on admin notices on plugins page to simplify plugin operations.

Do not miss to add this awesome featured plugin to your site. Once, you will use, you will never deactivate this and will like its working very much. It has really useful and time-efficient tools built in to provide you a loving experience on admin plugins listing page.

== QPS Features ==

1. **New "Switch" bulk action**
QPS supplies a new bulk action "Switch" on admin plugin listing page that can be used to activate and deactivate different plugins simultaneously in one go rather than native activate and then deactivate in two pages reload. (Screenshot-1,3)

2. **More useful Notices with links**
QPS modifies native plugin activated/deactivated notice to provide name and version of the switched plugin along with useful links like "Activate it again!!" if deactivated mistakenly. (Screenshot 1, 3)

3. **Delete link on deactivated notice**
If you want to delete an active plugin then you can delete it directly from deactivated notice without scrolling down again and searching for the plugin. (Screenshot 1,3)

4. **Multisite support**
QPS is fully compatible in the multisite environment (Screenshot 3)

= Connect with me =

* **Website** - https://dineshinaublog.wordpress.com/
* **Facebook** - https://www.facebook.com/dineshinau/
* **Twitter** - https://twitter.com/dineshinau/
* **LinkedIn** - https://www.linkedin.com/in/dineshinau/

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/quick-plugin-switcher` directory, or install the plugin through the WordPress plugins screen directly.

2. Activate the plugin through the \'Plugins\' screen in WordPress

3. Select one or more plugin(s) and choose the option "Switch" from bulk actions dropdown to change the state of the selected plugin(s).

4. Easily activate or delete the deactivated plugin from the plugin deactivated admin notice. Remove the need for scrolling up to the plugin and searching the plugin on-page.


== Frequently Asked Questions ==

= How many plugins can be switched at a time? =
As many plugins(s) are selected before applying the "Switch" action.

= What is meant by switching a plugin? =

Switching a plugin means that changing it's state e.g. if a plugin is deactivated then after applying switch action it will be activated. Similarly, as many plugins are switched, will be activated/deactivated depending on their current state(deactivated/activated).

= Why this plugin itself can not be switched through "Switch" bulk action? =
The checkbox against this plugin is disabled after activation because this plugin switched itself to deactivate mode then the "Switch" option will be disappeared.

= Why I created this plugin? =
Its need arose when I was troubleshooting a site for finding the culprit plugin in case of plugin conflict.

= When Links appear on notices? =
Activate, Deactivate and Delete links are shown on plugin page notices when a single plugin is activated/deactivated.

= Where can give feedback =
If you have any suggestion regarding the improvement of its feature, please leave a [Review](https://dineshinaublog.wordpress.com/quick-plugin-switcher/).


== Use Cases of QPS ==

Here are just a few use cases of QPS

* **Troubleshooting** It helps in troubleshooting to activate and deactivate multiple plugins simultaneously. (Screenshot-2)

* **Avoid Scrolling** Its allows activate/deactivate just deactivated/activated plugin without scrolling down and going up to that plugin. (Screenshot-1,3)

* **No Searching** Now you don't need of search a plugin which you have just deactivated to delete it. You can directly delete the plugin from deactivated notice. (Screenshot-1,3)


== Screenshots ==

1. **Single Site Plugin Actions** - It shows the "Switch" option created inside bulk actions drop-down menu, 'Activate it Again' link and 'Delete' link in the single-site environment. Also shows plugin name and version.
screenshot-1.png

2. **More Useful Notices** - It shows the number of plugins activated and deactivated on applying the 'Switch' action.
screenshot-2.png

3. **Multi-Site Plugin Actions** - It shows the "Switch" option created inside bulk actions drop-down menu, 'Activate it Again' link and 'Delete' link in the Multi-Site environment. Also shows plugin name and version.
screenshot-3.png
   

== Changelog ==

= 1.6.0 (2024-01-04) =
* Fixed: Notices on instance having php 8.
* Fixed: Delete link on notice showing redirection before deleting.

= 1.5.4 (2023-08-11) =
* Updated: Tested upto WordPress 6.3

= 1.5.3 (2022-05-07) =
* Updated: Tested upto WordPress 6.0
* Added: WC Log link on admin bar if woocommerce is active.

= 1.5.2 (2021-07-24) =
* Updated: Tested upto WordPress 5.8
* Updated: Code standards as per WP Coding standards.

= 1.5.1 (2021-05-09) =
* Improved: Plugin data delete on uninstall.
* Added: Tested upto WP version 5.7.1
* Changed: Minimum PHP requires 7.0.
* Fixed: Phpcs issues.

= 1.5.0 (2020-12-13) =
* Improved: Plugin code structure
* Added: Tested upto WP version 5.6

= 1.4.2 (2019-11-20) =
* Fixed: Compatibility with WP 5.3
* Fixed: Removed the 'Delete' link on deactivate notice when on 'active' plugin status page as the plugin can not be deleted from this page.
* Fixed: js code optimization, now available only on plugins page.

= 1.4.1 (2019-08-14) = 
* Fixed: Removed tracking code without opt-in message.

= 1.4 (2019-08-13) = 
* Added: Delete link on successfully deactivated notice

= 1.3 (2019-03-17) =
* Added: Compatible with WordPress 5.1
* Added: Switch again button on successful notices if a single plugin is switched.

* Added: Added switch links on native activate/deactivate success notices
* Improved: Text domain strings and translations

= 1.2 (2017-11-28) =
* Tested with WordPress 4.9
* Updated "Tested up to" value to 4.9
* Added Requires PHP version

= 1.0.1 (2017-06-02) =
* Fixed the issue with multisite
* Added successful notice on multisite
* Fixed conflict with other plugins

= 1.0 (2017-04-10) =
This is Initial Release of the plugin
