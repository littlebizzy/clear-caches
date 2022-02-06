=== Clear Caches ===

Contributors: littlebizzy
Donate link: https://www.patreon.com/littlebizzy
Tags: clear, purge, empty, cache, nginx
Requires at least: 4.4
Tested up to: 5.1
Requires PHP: 7.2
Multisite support: No
Stable tag: 1.2.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CLRCHS

The easiest way to clear caches including WordPress cache, PHP Opcache, Nginx cache, Transient cache, Varnish cache, and object cache (e.g. Redis).

== Changelog ==

= 1.2.3 =
* fixed fatal error (missing bracket)

= 1.2.2 =
* fixed undefined variable error (new default $modified = false)

= 1.2.1 =
* tweaked spelling of various buttons
* tested with WP 5.1

= 1.2.0 =
* PBP v1.2.0
* AUTOMATIC_UPDATE_PLUGINS

= 1.1.1 =
* simplied settings page to be single page (no tabs)
* new button `Save Nginx Path`
* popup modal outputs the Nginx path when caches are cleared

= 1.1.0 =
* changed plugin name from Purge Them All to Clear Caches
* PBP v1.1.1
* CLEAR_CACHES
* CLEAR_CACHES_NGINX
* CLEAR_CACHES_NGINX_PATH
* CLEAR_CACHES_OBJECT
* CLEAR_CACHES_OPCACHE
* removed all CloudFlare integration
* (focus going forward will be on-server caches only)

= 1.0.0 =
* initial release
* uses PHP namespaces
* object-oriented codebase
