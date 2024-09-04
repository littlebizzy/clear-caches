# Clear Caches

Purge all of the WordPress caches

## Changelog

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
* CLEAR_CACHES
* CLEAR_CACHES_NGINX
* CLEAR_CACHES_NGINX_PATH
* CLEAR_CACHES_OBJECT
* CLEAR_CACHES_OPCACHE
* removed all CloudFlare integration
* (focus going forward will be on-server caches only)

### 1.0.0
* initial release
* uses PHP namespaces
* object-oriented codebase

## Defined Constants

define('CLEAR_CACHES', true); // default = true
define('CLEAR_CACHES_NGINX', true); // default = true
define('CLEAR_CACHES_NGINX_PATH', '/var/www/cache'); // *must be unique* (SlickStack servers = /var/www/cache)
define('CLEAR_CACHES_OBJECT', true); // default = true
define('CLEAR_CACHES_OPCACHE', true); // default = true
    
