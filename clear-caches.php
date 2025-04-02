<?php
/*
Plugin Name: Clear Caches
Plugin URI: https://www.littlebizzy.com/plugins/clear-caches
Description: Purge all of the WordPress caches
Version: 2.1.0
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
if ( ! defined( 'CLEAR_CACHES_MIN_CAPABILITY' ) ) define( 'CLEAR_CACHES_MIN_CAPABILITY', 'manage_options' );
if ( ! defined( 'CLEAR_CACHES_OPCACHE' ) ) define( 'CLEAR_CACHES_OPCACHE', true );
if ( ! defined( 'CLEAR_CACHES_NGINX' ) ) define( 'CLEAR_CACHES_NGINX', true );
if ( ! defined( 'CLEAR_CACHES_OBJECT' ) ) define( 'CLEAR_CACHES_OBJECT', true );
if ( ! defined( 'CLEAR_CACHES_TRANSIENTS' ) ) define( 'CLEAR_CACHES_TRANSIENTS', true );
if ( ! defined( 'CLEAR_CACHES_NGINX_PATH' ) ) define( 'CLEAR_CACHES_NGINX_PATH', '/var/www/cache/nginx' );
if ( ! defined( 'CLEAR_CACHES_MEMCACHED_HOST' ) ) define( 'CLEAR_CACHES_MEMCACHED_HOST', '127.0.0.1' );
if ( ! defined( 'CLEAR_CACHES_MEMCACHED_PORT' ) ) define( 'CLEAR_CACHES_MEMCACHED_PORT', 11211 );
if ( ! defined( 'CLEAR_CACHES_REDIS_HOST' ) ) define( 'CLEAR_CACHES_REDIS_HOST', '127.0.0.1' );
if ( ! defined( 'CLEAR_CACHES_REDIS_PORT' ) ) define( 'CLEAR_CACHES_REDIS_PORT', 6379 );

// return required capability
function get_clear_caches_capability() {
    return CLEAR_CACHES_MIN_CAPABILITY;
}

// return a fresh nonce for clear caches ajax
function clear_caches_get_nonce() {
    static $nonce = null;
    if ( $nonce === null ) {
        $nonce = wp_create_nonce( 'clear_caches_nonce' );
    }
    return $nonce;
}

// add clear caches dropdown to the admin bar
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    $min_capability = get_clear_caches_capability();

    // check user is logged in and has minimum capability
    if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) || ( $min_capability !== 'edit_posts' && ! current_user_can( $min_capability ) ) ) {
        return;
    }

    // add main menu item
    $wp_admin_bar->add_node( [
        'id'     => 'clear_caches',
        'parent' => 'top-secondary',
        'title'  => 'Clear Caches',
        'meta'   => [ 'class' => 'clear-caches-admin-bar' ]
    ] );

    // add submenu for php opcache
    if ( CLEAR_CACHES_OPCACHE ) {
        $wp_admin_bar->add_node( [
            'id'     => 'clear_php_opcache',
            'parent' => 'clear_caches',
            'title'  => 'Clear PHP OPcache',
            'href'   => 'javascript:void(0);',
            'meta'   => [ 'class' => 'clear-cache-php-opcache' ]
        ] );
    }

    // add submenu for nginx cache
    if ( CLEAR_CACHES_NGINX ) {
        $wp_admin_bar->add_node( [
            'id'     => 'clear_nginx_cache',
            'parent' => 'clear_caches',
            'title'  => 'Clear Nginx Cache',
            'href'   => 'javascript:void(0);',
            'meta'   => [ 'class' => 'clear-cache-nginx' ]
        ] );
    }

    // add submenu for object cache
    if ( CLEAR_CACHES_OBJECT ) {
        $wp_admin_bar->add_node( [
            'id'     => 'clear_object_cache',
            'parent' => 'clear_caches',
            'title'  => 'Clear Object Cache',
            'href'   => 'javascript:void(0);',
            'meta'   => [ 'class' => 'clear-cache-object' ]
        ] );
    }

    // add submenu for transients
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
        wp_enqueue_script(
            'clear-caches-script',
            plugin_dir_url( __FILE__ ) . 'clear-caches.js',
            [ 'jquery' ],
            filemtime( plugin_dir_path( __FILE__ ) . 'clear-caches.js' ),
            true
        );

        wp_localize_script( 'clear-caches-script', 'clearCachesData', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => clear_caches_get_nonce()
        ] );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_clear_caches_scripts' );
add_action( 'admin_enqueue_scripts', 'enqueue_clear_caches_scripts' );

// handle ajax requests
add_action( 'wp_ajax_clear_caches_action', function() {
    $min_capability = get_clear_caches_capability();

    // check user capability (edit_posts minimum)
    if ( ! current_user_can( 'edit_posts' ) || ( $min_capability !== 'edit_posts' && ! current_user_can( $min_capability ) ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied' ] );
        wp_die();
    }

    // verify nonce
    check_ajax_referer( 'clear_caches_nonce', 'security' );

    $cache_type = sanitize_key( $_POST['cache_type'] ?? '' );
    $valid_cache_types = [ 'php_opcache', 'nginx_cache', 'object_cache', 'clear_transients' ];

    // validate requested cache type
    if ( ! in_array( $cache_type, $valid_cache_types, true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid cache type specified.' ] );
        wp_die();
    }

    // call matching cache clear function
    if ( $cache_type === 'php_opcache' ) {
        clear_php_opcache();
        return;
    }

    if ( $cache_type === 'nginx_cache' ) {
        clear_nginx_cache();
        return;
    }

    if ( $cache_type === 'object_cache' ) {
        clear_object_cache();
        return;
    }

    if ( $cache_type === 'clear_transients' ) {
        clear_all_transients();
        return;
    }

    // fallback response (should not be reached)
    wp_send_json_error( [ 'message' => 'Unhandled cache type.' ] );
    wp_die();
});

// clear php opcache
function clear_php_opcache() {
    if ( function_exists( 'opcache_reset' ) ) {
        opcache_reset();
        wp_send_json_success( [ 'message' => 'PHP OPcache cleared successfully.' ] );
        wp_die();
    } else {
        wp_send_json_error( [ 'message' => 'PHP OPcache is not available on this server.' ] );
        wp_die();
    }
}

// clear nginx cache
function clear_nginx_cache() {
    $nginx_cache_path = CLEAR_CACHES_NGINX_PATH;

    // check if nginx cache path is valid and writable
    if ( file_exists( $nginx_cache_path ) && is_dir( $nginx_cache_path ) && is_writable( $nginx_cache_path ) ) {
        // refresh filesystem metadata cache
        clearstatcache();

        // recursively iterate all files and folders, child-first
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $nginx_cache_path, RecursiveDirectoryIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        $errors = [];

        // attempt to delete each file and folder
        foreach ( $files as $fileinfo ) {
            $path = $fileinfo->getRealPath();
            if ( $fileinfo->isDir() ) {
                if ( ! @rmdir( $path ) ) {
                    $errors[] = $path;
                }
            } else {
                if ( ! @unlink( $path ) ) {
                    $errors[] = $path;
                }
            }
        }

        // send response based on errors
        if ( empty( $errors ) ) {
            wp_send_json_success( [ 'message' => 'Nginx cache cleared successfully.' ] );
            wp_die();
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear some cache files. Check permissions.' ] );
            wp_die();
        }
    } else {
        // path not valid or not writable
        wp_send_json_error( [ 'message' => 'Nginx cache path does not exist, is not a directory, or is not writable.' ] );
        wp_die();
    }
}

// clear object cache
function clear_object_cache() {
    // handle memcached backend
    if ( class_exists( 'Memcached' ) ) {
        $memcached = new Memcached();
        if ( $memcached->addServer( CLEAR_CACHES_MEMCACHED_HOST, CLEAR_CACHES_MEMCACHED_PORT ) ) {
            $memcached->flush();
            $memcached->set( 'test_key', 'test_value', 10 );
            if ( $memcached->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object cache (Memcached) cleared successfully.' ] );
                wp_die();
            } else {
                wp_send_json_error( [ 'message' => 'Memcached flush succeeded, but verification failed.' ] );
                wp_die();
            }
        } else {
            wp_send_json_error( [ 'message' => 'Could not connect to Memcached server.' ] );
            wp_die();
        }
    }

    // handle memcache backend
    elseif ( class_exists( 'Memcache' ) ) {
        $memcache = new Memcache();
        if ( $memcache->addServer( CLEAR_CACHES_MEMCACHED_HOST, CLEAR_CACHES_MEMCACHED_PORT ) ) {
            $memcache->flush();
            $memcache->set( 'test_key', 'test_value', 0, 10 );
            if ( $memcache->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object cache (Memcache) cleared successfully.' ] );
                wp_die();
            } else {
                wp_send_json_error( [ 'message' => 'Memcache flush succeeded, but verification failed.' ] );
                wp_die();
            }
        } else {
            wp_send_json_error( [ 'message' => 'Could not connect to Memcache server.' ] );
            wp_die();
        }
    }

    // handle redis extension
    elseif ( class_exists( 'Redis' ) ) {
        $redis = new Redis();
        try {
            if ( $redis->connect( CLEAR_CACHES_REDIS_HOST, CLEAR_CACHES_REDIS_PORT ) ) {
                $redis->flushAll();
                $redis->set( 'test_key', 'test_value', 10 );
                if ( $redis->get( 'test_key' ) === 'test_value' ) {
                    wp_send_json_success( [ 'message' => 'Object cache (Redis) cleared successfully.' ] );
                    wp_die();
                } else {
                    wp_send_json_error( [ 'message' => 'Redis flush succeeded, but verification failed.' ] );
                    wp_die();
                }
            } else {
                wp_send_json_error( [ 'message' => 'Could not connect to Redis server.' ] );
                wp_die();
            }
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => 'Redis error: ' . $e->getMessage() ] );
            wp_die();
        }
    }

    // handle predis client
    elseif ( class_exists( 'Predis\Client' ) ) {
        try {
            $predis = new Predis\Client();
            $predis->flushall();
            $predis->set( 'test_key', 'test_value' );
            if ( $predis->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object cache (Predis) cleared successfully.' ] );
                wp_die();
            } else {
                wp_send_json_error( [ 'message' => 'Predis flush succeeded, but verification failed.' ] );
                wp_die();
            }
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => 'Predis error: ' . $e->getMessage() ] );
            wp_die();
        }
    }

    // handle relay backend
    elseif ( function_exists( 'relay_flush' ) ) {
        $flushed = relay_flush();
        if ( $flushed ) {
            wp_send_json_success( [ 'message' => 'Object cache (Relay) cleared successfully.' ] );
            wp_die();
        } else {
            wp_send_json_error( [ 'message' => 'Relay flush failed. Check server configuration.' ] );
            wp_die();
        }
    }

    // fallback to wordpress object cache flush
    else {
        wp_cache_flush();
        wp_send_json_success( [ 'message' => 'Object cache (WordPress) cleared. Verification not supported.' ] );
        wp_die();
    }
}

// clear all transients
function clear_all_transients() {
    global $wpdb;

    // delete _transient_ keys for current site only
    $transients_deleted = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $wpdb->esc_like( '_transient_' ) . '%'
        )
    );

    // delete _site_transient_ keys for current site only
    $site_transients_deleted = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $wpdb->esc_like( '_site_transient_' ) . '%'
        )
    );

    // delete network-wide transients from sitemeta (super admin only)
    $sitemeta_deleted = true;
    if ( is_multisite() && is_super_admin() ) {
        $sitemeta_deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s",
                $wpdb->esc_like( '_site_transient_' ) . '%'
            )
        );
    }

    // return error if any query failed
    if ( $transients_deleted === false || $site_transients_deleted === false || ( is_multisite() && is_super_admin() && $sitemeta_deleted === false ) ) {
        wp_send_json_error( [ 'message' => 'Failed to clear transients. Check database permissions.' ] );
        wp_die();
    }

    // return success if nothing found
    if ( $transients_deleted === 0 && $site_transients_deleted === 0 && ( ! is_multisite() || ! is_super_admin() || $sitemeta_deleted === 0 ) ) {
        wp_send_json_success( [ 'message' => 'No transients found to delete.' ] );
        wp_die();
    }

    // calculate total rows deleted
    $total_deleted = (int) $transients_deleted + (int) $site_transients_deleted;
    if ( is_multisite() && is_super_admin() ) {
        $total_deleted += (int) $sitemeta_deleted;
    }

    // return success after deletions
    wp_send_json_success( [ 'message' => "Successfully cleared {$total_deleted} transient row" . ( $total_deleted === 1 ? '' : 's' ) . "." ] );
    wp_die();
}

// Ref: ChatGPT
