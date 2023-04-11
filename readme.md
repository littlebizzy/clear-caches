# Clear Caches

Purge all of the WordPress caches

### Defined Constants

    /* Plugin Meta */
    define('AUTOMATIC_UPDATE_PLUGINS', false); // default = false
    define('DISABLE_NAG_NOTICES', true); // default = true
    
    /* Clear Caches Functions */
    define('CLEAR_CACHES', true); // default = true
    define('CLEAR_CACHES_NGINX', true); // default = true
    define('CLEAR_CACHES_NGINX_PATH', '/var/www/cache'); // *must be unique* (SlickStack servers = /var/www/cache)
    define('CLEAR_CACHES_OBJECT', true); // default = true
    define('CLEAR_CACHES_OPCACHE', true); // default = true
    
