<?php
/*
Plugin Name: Clear Caches
Plugin URI: https://www.littlebizzy.com/plugins/clear-caches
Description: Purge all of the WordPress caches
Version: 2.0.3
Requires PHP: 7.0
Tested up to: 6.7
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Update URI: false
GitHub Plugin URI: littlebizzy/clear-caches
Primary Branch: master
Text Domain: clear-caches
*/

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// override wordpress.org with git updater
add_filter( 'gu_override_dot_org', function( $overrides ) {
    $overrides[] = 'clear-caches/clear-caches.php';
    return $overrides;
}, 999 );

// define constants
if ( ! defined( 'CLEAR_CACHES_MIN_CAPABILITY' ) ) define( 'CLEAR_CACHES_MIN_CAPABILITY', 'manage_options' ); // default to admin level
if ( ! defined( 'CLEAR_CACHES_OPCACHE' ) ) define( 'CLEAR_CACHES_OPCACHE', true );
if ( ! defined( 'CLEAR_CACHES_NGINX' ) ) define( 'CLEAR_CACHES_NGINX', true );
if ( ! defined( 'CLEAR_CACHES_OBJECT' ) ) define( 'CLEAR_CACHES_OBJECT', true );
if ( ! defined( 'CLEAR_CACHES_TRANSIENTS' ) ) define( 'CLEAR_CACHES_TRANSIENTS', true );
if ( ! defined( 'CLEAR_CACHES_NGINX_PATH' ) ) define( 'CLEAR_CACHES_NGINX_PATH', '/var/www/cache/nginx' );

// return a fresh nonce for clear caches ajax
function get_clear_caches_nonce() {
    static $nonce = null;
    if ( $nonce === null ) {
        $nonce = wp_create_nonce( 'clear_caches_nonce' );
    }
    return $nonce;
}

// add clear caches dropdown to the admin bar
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    $min_capability = CLEAR_CACHES_MIN_CAPABILITY;

    // ensure user has required capability
    if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) || ( $min_capability !== 'edit_posts' && ! current_user_can( $min_capability ) ) ) {
        return;
    }

    // create top-level menu node
    $wp_admin_bar->add_node( [
        'id'     => 'clear_caches',
        'parent' => 'top-secondary',
        'title'  => 'Clear Caches',
        'meta'   => [ 'class' => 'clear-caches-admin-bar' ]
    ] );

    if ( CLEAR_CACHES_OPCACHE ) {
        $wp_admin_bar->add_node( [
            'id'     => 'clear_php_opcache',
            'parent' => 'clear_caches',
            'title'  => 'Clear PHP OPcache',
            'href'   => 'javascript:void(0);',
            'meta'   => [ 'class' => 'clear-cache-php-opcache' ]
        ] );
    }

    if ( CLEAR_CACHES_NGINX ) {
        $wp_admin_bar->add_node( [
            'id'     => 'clear_nginx_cache',
            'parent' => 'clear_caches',
            'title'  => 'Clear Nginx Cache',
            'href'   => 'javascript:void(0);',
            'meta'   => [ 'class' => 'clear-cache-nginx' ]
        ] );
    }

    if ( CLEAR_CACHES_OBJECT ) {
        $wp_admin_bar->add_node( [
            'id'     => 'clear_object_cache',
            'parent' => 'clear_caches',
            'title'  => 'Clear Object Cache',
            'href'   => 'javascript:void(0);',
            'meta'   => [ 'class' => 'clear-cache-object' ]
        ] );
    }

    if ( CLEAR_CACHES_TRANSIENTS ) {
        $wp_admin_bar->add_node( [
            'id'     => 'clear_transients',
            'parent' => 'clear_caches',
            'title'  => 'Clear Transients Cache',
            'href'   => 'javascript:void(0);',
            'meta'   => [ 'class' => 'clear-cache-transients' ]
        ] );
    }
}, 100 );
// enqueue javascript for both frontend and backend
function enqueue_clear_caches_scripts() {
    if ( is_admin_bar_showing() ) {
        wp_enqueue_script( 'clear-caches-script', plugin_dir_url( __FILE__ ) . 'clear-caches.js', [ 'jquery' ], null, true );

        // pass ajax url and nonce to javascript
        wp_localize_script( 'clear-caches-script', 'clearCachesData', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => get_clear_caches_nonce()
        ] );
    }
}

// enqueue for frontend
add_action( 'wp_enqueue_scripts', 'enqueue_clear_caches_scripts' );

// enqueue for backend
add_action( 'admin_enqueue_scripts', 'enqueue_clear_caches_scripts' );

// handle ajax requests
add_action( 'wp_ajax_clear_caches_action', function() {
    $min_capability = CLEAR_CACHES_MIN_CAPABILITY;

    // deny if user lacks required capability
    if ( ! current_user_can( 'edit_posts' ) || ( $min_capability !== 'edit_posts' && ! current_user_can( $min_capability ) ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied' ] );
    }

    // verify nonce
    check_ajax_referer( 'clear_caches_nonce', 'security' );

    // get and validate cache type
    $cache_type = sanitize_text_field( $_POST['cache_type'] ?? '' );
    $valid_cache_types = [ 'php_opcache', 'nginx_cache', 'object_cache', 'clear_transients' ];

    if ( ! in_array( $cache_type, $valid_cache_types, true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid cache type specified.' ] );
    }

    // run the requested cache clear action
    switch ( $cache_type ) {
        case 'php_opcache':
            clear_php_opcache();
            break;
        case 'nginx_cache':
            clear_nginx_cache();
            break;
        case 'object_cache':
            clear_object_cache();
            break;
        case 'clear_transients':
            clear_all_transients();
            break;
    }
});

// clear php opcache
function clear_php_opcache() {
    // check if opcache functions are available
    if ( function_exists( 'opcache_reset' ) ) {
        // flush opcache
        opcache_reset();

        // send success response
        wp_send_json_success( [ 'message' => 'PHP OPcache cleared successfully.' ] );
    } else {
        // send error if opcache is not available
        wp_send_json_error( [ 'message' => 'PHP OPcache is not available on this server.' ] );
    }
}

// Clear Nginx Cache
function clear_nginx_cache() {
    $nginx_cache_path = CLEAR_CACHES_NGINX_PATH;

    // Check if the path exists, is a directory, and is writable
    if ( file_exists( $nginx_cache_path ) && is_dir( $nginx_cache_path ) && is_writable( $nginx_cache_path ) ) {
        // Use RecursiveIterator to delete files and subdirectories
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $nginx_cache_path, RecursiveDirectoryIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        $errors = [];

        foreach ( $files as $fileinfo ) {
            // Attempt to delete the files and directories unconditionally
            if ( $fileinfo->isDir() ) {
                if ( ! @rmdir( $fileinfo->getRealPath() ) ) {
                    $errors[] = $fileinfo->getRealPath();
                }
            } else {
                if ( ! @unlink( $fileinfo->getRealPath() ) ) {
                    $errors[] = $fileinfo->getRealPath();
                }
            }
        }

        // Handle the result: if no errors occurred, return success
        if ( empty( $errors ) ) {
            wp_send_json_success( [ 'message' => 'Nginx Cache cleared successfully.' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear some cache files. Please check permissions.' ] );
        }
    } else {
        wp_send_json_error( [ 'message' => 'Nginx Cache path does not exist, is not a directory, or is not writable.' ] );
    }
}

// Clear Object Cache
function clear_object_cache() {

    // Memcached: Official PHP extension for Memcached
    if ( class_exists( 'Memcached' ) ) {
        $memcached = new Memcached();
        // Attempt to add server for Memcached
        if ( $memcached->addServer( '127.0.0.1', 11211 ) ) {
            $memcached->flush();  // Force flush unconditionally

            // Test if the flush worked by setting and retrieving a test key
            $memcached->set( 'test_key', 'test_value', 10 );
            if ( $memcached->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object Cache (Memcached) cleared successfully.' ] );
            } else {
                wp_send_json_error( [ 'message' => 'Object Cache (Memcached) flush succeeded, but verification failed.' ] );
            }
        } else {
            wp_send_json_error( [ 'message' => 'Failed to connect to Memcached server.' ] );
        }
    }

    // Memcache: Legacy PHP extension for Memcache
    elseif ( class_exists( 'Memcache' ) ) {
        $memcache = new Memcache();
        // Attempt to add server for Memcache
        if ( $memcache->addServer( '127.0.0.1', 11211 ) ) {
            $memcache->flush();  // Force flush unconditionally

            // Test if the flush worked by setting and retrieving a test key
            $memcache->set( 'test_key', 'test_value', 0, 10 );
            if ( $memcache->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object Cache (Memcache) cleared successfully.' ] );
            } else {
                wp_send_json_error( [ 'message' => 'Object Cache (Memcache) flush succeeded, but verification failed.' ] );
            }
        } else {
            wp_send_json_error( [ 'message' => 'Failed to connect to Memcache server.' ] );
        }
    }

    // Redis: PHPRedis (PECL Redis extension) or a Redis drop-in replacement
    elseif ( class_exists( 'Redis' ) ) {
        $redis = new Redis();
        try {
            // Attempt to connect to Redis
            if ( $redis->connect( '127.0.0.1', 6379 ) ) {
                $redis->flushAll();  // Force flush unconditionally

                // Test if the flush worked by setting and retrieving a test key
                $redis->set( 'test_key', 'test_value', 10 );
                if ( $redis->get( 'test_key' ) === 'test_value' ) {
                    wp_send_json_success( [ 'message' => 'Object Cache (Redis, PhpRedis) cleared successfully.' ] );
                } else {
                    wp_send_json_error( [ 'message' => 'Object Cache (Redis, PhpRedis) flush succeeded, but verification failed.' ] );
                }
            } else {
                wp_send_json_error( [ 'message' => 'Failed to connect to Redis server.' ] );
            }
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => 'Redis connection error: ' . $e->getMessage() ] );
        }
    }

    // Predis: A PHP-based client for Redis
    elseif ( class_exists( 'Predis\Client' ) ) {
        try {
            $predis = new Predis\Client();
            // Attempt to connect to Predis server
            if ( $predis->connect() ) {
                $predis->flushall();  // Force flush unconditionally

                // Test if the flush worked by setting and retrieving a test key
                $predis->set( 'test_key', 'test_value' );
                if ( $predis->get( 'test_key' ) === 'test_value' ) {
                    wp_send_json_success( [ 'message' => 'Object Cache (Predis) cleared successfully.' ] );
                } else {
                    wp_send_json_error( [ 'message' => 'Object Cache (Predis) flush succeeded, but verification failed.' ] );
                }
            } else {
                wp_send_json_error( [ 'message' => 'Failed to connect to Predis server.' ] );
            }
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => 'Predis connection error: ' . $e->getMessage() ] );
        }
    }

    // Relay: A PECL extension for high-performance Redis
    elseif ( function_exists( 'relay_flush' ) ) {
        // Force flush unconditionally
        relay_flush();

        // Check if flush was successful
        if ( relay_flush() ) {
            // Send success message
            wp_send_json_success( [ 'message' => 'Object Cache (Relay) cleared successfully.' ] );
        } else {
            // Send error message if flush fails
            wp_send_json_error( [ 'message' => 'Failed to flush Object Cache (Relay). Check server connection or configuration.' ] );
        }
    }

    // Default WordPress object cache
    else {
        wp_cache_flush();  // Force flush unconditionally

        // No verification supported, so issue a generic success message
        wp_send_json_success( [ 'message' => 'Object Cache (WordPress default) cleared successfully, but verification not supported.' ] );
    }
}

// clear all transients
function clear_all_transients() {
    global $wpdb;

    // delete regular and site transients
    $transients_deleted = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'" );
    $site_transients_deleted = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'" );

    // check for query failure
    if ( $transients_deleted === false || $site_transients_deleted === false ) {
        wp_send_json_error( [ 'message' => 'Failed to clear transients. Check database permissions.' ] );
    }

    // check if no rows were deleted
    if ( $transients_deleted === 0 && $site_transients_deleted === 0 ) {
        wp_send_json_success( [ 'message' => 'No transients found to delete.' ] );
    }

    // send success response
    wp_send_json_success( [ 'message' => 'Transients cleared successfully.' ] );
}

// Ref: ChatGPT
