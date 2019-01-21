# Clear Caches

The easiest way to clear caches including WordPress cache, PHP Opcache, Nginx cache, Transient cache, Varnish cache, and object cache (e.g. Redis).

* [Plugin Homepage](https://www.littlebizzy.com/plugins/clear-caches)
* [**Become A LittleBizzy.com Member Today!**](https://www.littlebizzy.com/members)

### Defined Constants

    /* Plugin Meta */
    define('DISABLE_NAG_NOTICES', true);
    
    /* Clear Caches Functions */
    define('CLEAR_CACHES', true);
    define('CLEAR_CACHES_NGINX', true);
    define('CLEAR_CACHES_NGINX_PATH', '/var/www/cache');
    define('CLEAR_CACHES_OBJECT', true);
    define('CLEAR_CACHES_OPCACHE', true);
    define('CLEAR_CACHES_TRANSIENTS', true);
    define('CLEAR_CACHES_VARNISH', true);
    define('CLEAR_CACHES_VARNISH_PATH', '/var/www/cache');
    
### Compatibility

This plugin has been designed for use on [SlickStack](https://slickstack.io) web servers with PHP 7.2 and MySQL 5.7 to achieve best performance. All of our plugins are meant for single site WordPress installations only; for both performance and usability reasons, we strongly recommend avoiding WordPress Multisite for the vast majority of your projects.

Any of our WordPress plugins may also be loaded as "Must-Use" plugins (meaning that they load first, and cannot be deactivated) by using our free [Autoloader](https://github.com/littlebizzy/autoloader) script in the `mu-plugins` directory.

### Support Issues

*Please do not submit Pull Requests. Instead, kindly create a new Issue with relevant information if you are an experienced developer, otherwise post your comments in our free Facebook group.*

***No emails, please! Thank you.***
