# Clear Caches

Purge all of the WordPress caches

## Description

Clear Caches provides individual WordPress and server-side cache controls from the WordPress admin bar.

Cached data can sometimes cause old content, settings, templates, or code to remain visible after site changes. Authorized users can clear PHP OPcache, Nginx cache files, the object cache, or database transients without using command-line tools or restarting services. The controls are available from the admin bar on both frontend and admin screens when the admin bar is displayed.

The object-cache action includes handlers for Memcached, Memcache, Redis, Predis, and Relay, with `wp_cache_flush()` used as the WordPress fallback. Memcached, Memcache, Redis, and Predis handlers perform a basic write-and-read check after flushing, while other handlers report the result supported by their backend.

Nginx cache deletion is restricted to a predefined list of known cache directories. The configured path must resolve to one of those directories and must exist, be writable, and be a directory before any files are removed.

On Multisite installations, transients stored in the current site’s options table are cleared. Network transients stored in `sitemeta` are also cleared when the action is run by a super administrator.

Each action uses authenticated WordPress AJAX with capability and nonce checks. Cache modules, the required capability, the Nginx cache path, and Memcached or Redis connection details can be configured with constants instead of a settings screen.

Clear Caches only handles caches managed by WordPress or the local server. It does not clear browser, CDN, load balancer, or external reverse-proxy caches.

## Changelog

### 3.1.0
- added Redis connection timeout to prevent stalled PHP workers on broken sockets
- reset PHP filesystem stat cache with `clearstatcache` after Nginx cache purge to avoid stale metadata
- simplified capability checks for better long-term maintainability
- `Tested up to:` bumped to 6.9

### 3.0.0
- major refactoring and security hardening
- Multisite network `sitemeta` transients now cleared (for super admins only)
- added total deleted row count to cleared transients success message
- added dynamic nonce helper with static caching for AJAX
- replaced `sanitize_text_field()` with `sanitize_key()` for AJAX request
- improved capability fallback logic via `get_clear_caches_capability()` helper
- restricted Nginx cache deletion to known safe paths (supports SlickStack, EasyEngine, WordOps, and more)
- updated Nginx deletion logic with stricter realpath + validation
- improved JS modal UX (ARIA tags, better namespaces, more WordPress-native CSS styling)
- improved JS modal security (gracefully degrades if `clearCachesData` is missing, escapes messages with `.text()` now to prevent HTML injection)
- added `wp_die()` after all `wp_send_json_*()` responses
- added `Requires PHP` plugin header
- added `Tested up to` plugin header
- added `Update URI` plugin header
- added `Text Domain` plugin header

### 2.0.2
- fixed `gu_override_dot_org` snippet

### 2.0.1
- fixed enqueue of jQuery both frontend and backend

### 2.0.0
- completely refactored to WordPress coding standards
- now supports multiple object cache softwares (Memcached, Memcache, Redis, Predis, Relay Cache, and default WordPress object cache)
- now supports clearing (deleting) database transients
- better messaging in case of success or failure in each cache module
- better verification of actual flushing in certain modules
- new defined constant `CLEAR_CACHES_MIN_CAPABILITY`
- new defined constant `CLEAR_CACHES_TRANSIENTS`
- supports PHP 7.0 to PHP 8.3
- supports Multisite

### 1.2.3
* fixed fatal error (missing bracket)

### 1.2.2
* fixed undefined variable error (new default $modified = false)

### 1.2.1
* tweaked spelling of various buttons
* tested with WP 5.1

### 1.2.0
* PBP v1.2.0
* AUTOMATIC_UPDATE_PLUGINS

### 1.1.1
* simplied settings page to be single page (no tabs)
* new button `Save Nginx Path`
* popup modal outputs the Nginx path when caches are cleared

### 1.1.0
* changed plugin name from Purge Them All to Clear Caches
* PBP v1.1.1
* defined constant `CLEAR_CACHES`
* defined constant `CLEAR_CACHES_NGINX`
* defined constant `CLEAR_CACHES_NGINX_PATH`
* defined constant `CLEAR_CACHES_OBJECT`
* defined constant `CLEAR_CACHES_OPCACHE`
* removed all CloudFlare integration
* (focus going forward will be on-server caches only)

### 1.0.0
* initial release
* uses PHP namespaces
* object-oriented codebase