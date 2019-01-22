=== Clear Caches ===

Contributors: littlebizzy
Donate link: https://www.patreon.com/littlebizzy
Tags: clear, purge, empty, cache, nginx
Requires at least: 4.4
Tested up to: 5.0
Requires PHP: 7.2
Multisite support: No
Stable tag: 1.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CLRCHS

The easiest way to clear caches including WordPress cache, PHP Opcache, Nginx cache, Transient cache, Varnish cache, and object cache (e.g. Redis).

== Description ==

The easiest way to clear caches including WordPress cache, PHP Opcache, Nginx cache, Transient cache, Varnish cache, and object cache (e.g. Redis).

* [Plugin Homepage](https://www.littlebizzy.com/plugins/clear-caches)
* [Plugin GitHub](https://github.com/littlebizzy/clear-caches)

#### The Long Version ####

Designed for SlickStack or other LEMP stack servers to provide a centralized cache clearing menu. Currently supports PHP Opcache, Nginx FastCGI cache, and most any WordPress object cache. Soon will support general WordPress cache (in the case of using a page cache plugin etc) and Varnish cache, and perhaps more.

#### Compatibility ####

This plugin has been designed for use on LEMP (Nginx) web servers with PHP 7.0 and MySQL 5.7 to achieve best performance. All of our plugins are meant for single site WordPress installations only; for both performance and security reasons, we highly recommend against using WordPress Multisite for the vast majority of projects.

Note: Any WordPress plugin may also be loaded as "Must-Use" by using the [Autoloader](https://github.com/littlebizzy/autoloader) script within the `mu-plugins` directory.

#### Defined Constants ####

    /* Plugin Meta */
    define('DISABLE_NAG_NOTICES', true);
    
    /* Clear Caches Functions */
    define('CLEAR_CACHES', true);
    define('CLEAR_CACHES_NGINX', true);
    define('CLEAR_CACHES_NGINX_PATH', '/var/www/cache');
    define('CLEAR_CACHES_OBJECT', true);
    define('CLEAR_CACHES_OPCACHE', true);

#### Plugin Features ####

* Parent Plugin: n/a
* Disable Nag Notices: [[Yes](https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices#Disable_Nag_Notices)]
* Settings Page: Yes
* PHP Namespaces: Yes
* Object-Oriented Code: Yes
* Includes Media (images, icons, etc): No
* Includes CSS: No
* Database Storage: Yes
  * Transients: No
  * WP Options Table: Yes
  * Other Tables: No
  * Creates New Tables: No
* Database Queries: Backend Only
  * Query Types: Options API
* Multisite Support: No
* Uninstalls Data: Yes

#### Disclaimer ####

We released this plugin in response to our managed hosting clients asking for better access to their server, and our primary goal will remain supporting that purpose. Although we are 100% open to fielding requests from the WordPress community, we kindly ask that you keep these conditions in mind, and refrain from slandering, threatening, or harassing our team members in order to get a feature added, or to otherwise get "free" support. The only place you should be contacting us is in our free [**Facebook group**](https://www.facebook.com/groups/littlebizzy/) which has been setup for this purpose, or via GitHub if you are an experienced developer. Thank you!

#### Our Philosophy ####

> "Decisions, not options." -- WordPress.org

> "Everything should be made as simple as possible, but not simpler." -- Albert Einstein, et al

> "Write programs that do one thing and do it well... write programs to work together." -- Doug McIlroy

> "The innovation that this industry talks about so much is bullshit. Anybody can innovate... 99% of it is 'Get the work done.' The real work is in the details." -- Linus Torvalds

== Installation ==

1. Upload to `/wp-content/plugins/clear-caches-littlebizzy`
2. Activate via WP Admin > Plugins
3. Test plugin is working

== Frequently Asked Questions ==

= How can I change this plugin's settings? =

Use the defined constants or use the provided settings page, but the constants will take priority in case you try both.

= I have a suggestion, how can I let you know? =

Please avoid leaving negative reviews in order to get a feature implemented. Instead, we kindly ask that you post your feedback on the wordpress.org support forums by tagging this plugin in your post. If needed, you may also contact our homepage.

== Changelog ==

= 1.1.0 =
* changed plugin name from Purge Them All to Clear Caches
* PBP v1.1.1
* tested with PHP 7.0, 7.1, 7.2
* tested with PHP 5.6 (no fatal errors only)
* CLEAR_CACHES
* CLEAR_CACHES_NGINX
* CLEAR_CACHES_NGINX_PATH
* CLEAR_CACHES_OBJECT
* CLEAR_CACHES_OPCACHE
* removed all CloudFlare integration
* (focus going forward will be on-server caches only)

= 1.0.0 =
* initial release
* tested with PHP 7.0
* uses PHP namespaces
* object-oriented codebase
