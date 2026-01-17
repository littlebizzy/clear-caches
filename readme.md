# Clear Caches

Purge all of the WordPress caches

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
