<?php
/*
Plugin Name: Clear Caches
Plugin URI: https://www.littlebizzy.com/plugins/clear-caches
Description: Purge all of the WordPress caches
Version: 2.0.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
GitHub Plugin URI: littlebizzy/clear-caches
Primary Branch: master
Prefix: CLRCHS
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Disable WordPress.org updates for this plugin
add_filter( 'gu_override_dot_org', function( $overrides ) {
    $overrides['clear-caches/clear-caches.php'] = true;
    return $overrides;
});

// Define constants
if ( ! defined( 'CLEAR_CACHES_MIN_CAPABILITY' ) ) define( 'CLEAR_CACHES_MIN_CAPABILITY', 'manage_options' ); // Default to Admin level
if ( ! defined( 'CLEAR_CACHES_OPCACHE' ) ) define( 'CLEAR_CACHES_OPCACHE', true );
if ( ! defined( 'CLEAR_CACHES_NGINX' ) ) define( 'CLEAR_CACHES_NGINX', true );
if ( ! defined( 'CLEAR_CACHES_OBJECT' ) ) define( 'CLEAR_CACHES_OBJECT', true );
if ( ! defined( 'CLEAR_CACHES_TRANSIENTS' ) ) define( 'CLEAR_CACHES_TRANSIENTS', true );
if ( ! defined( 'CLEAR_CACHES_NGINX_PATH' ) ) define( 'CLEAR_CACHES_NGINX_PATH', '/var/www/cache/nginx' );

// Add Clear Caches dropdown to the admin bar
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    $min_capability = CLEAR_CACHES_MIN_CAPABILITY;
    
    // Ensure user has at least 'edit_posts' capability and the defined capability
    if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) || ( $min_capability !== 'edit_posts' && ! current_user_can( $min_capability ) ) ) {
        return;
    }

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

// Enqueue JavaScript for admin bar
add_action( 'wp_enqueue_scripts', function() {
    if ( is_admin_bar_showing() ) {
        wp_enqueue_script( 'clear-caches-script', plugin_dir_url( __FILE__ ) . 'clear-caches.js', [ 'jquery' ], null, true );

        // Pass AJAX URL and nonce to JavaScript
        wp_localize_script( 'clear-caches-script', 'clearCachesData', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'clear_caches_nonce' )
        ] );
    }
});

// Handle AJAX requests
add_action( 'wp_ajax_clear_caches_action', function() {
    $min_capability = CLEAR_CACHES_MIN_CAPABILITY;

    // Ensure user has at least 'edit_posts' capability and the defined capability
    if ( ! current_user_can( 'edit_posts' ) || ( $min_capability !== 'edit_posts' && ! current_user_can( $min_capability ) ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied' ] );
    }

    check_ajax_referer( 'clear_caches_nonce', 'security' );

    $cache_type = sanitize_text_field( $_POST['cache_type'] ?? '' );

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
        default:
            wp_send_json_error( [ 'message' => 'Invalid cache type specified.' ] );
    }
});

// Clear PHP OPcache
function clear_php_opcache() {
    // Check if OPcache functions are available
    if ( function_exists( 'opcache_reset' ) ) {
        // Attempt to reset OPcache
        if ( opcache_reset() ) {
            wp_send_json_success( [ 'message' => 'PHP OPcache cleared successfully.' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear OPcache.' ] );
        }
    } else {
        wp_send_json_error( [ 'message' => 'OPcache not installed.' ] );
    }
}

// Clear Nginx Cache
function clear_nginx_cache() {
    $nginx_cache_path = CLEAR_CACHES_NGINX_PATH;

    // Check if the path exists, is a directory, and is writable
    if ( ! file_exists( $nginx_cache_path ) ) {
        wp_send_json_error( [ 'message' => 'Nginx Cache path does not exist.' ] );
    } elseif ( ! is_dir( $nginx_cache_path ) ) {
        wp_send_json_error( [ 'message' => 'Nginx Cache path is not a directory.' ] );
    } elseif ( ! is_writable( $nginx_cache_path ) ) {
        wp_send_json_error( [ 'message' => 'Nginx Cache path is not writable.' ] );
    } else {
        // Use RecursiveIterator to delete files and subdirectories
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $nginx_cache_path, RecursiveDirectoryIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        $errors = [];

        foreach ( $files as $fileinfo ) {
            // Attempt to delete the files and directories
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

        // Handle errors during deletion
        if ( empty( $errors ) ) {
            wp_send_json_success( [ 'message' => 'Nginx Cache cleared successfully.' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear some cache files. Please check permissions.' ] );
        }
    }
}

// Clear Object Cache
function clear_object_cache() {
    if ( class_exists( 'Memcached' ) ) {
        $memcached = new Memcached();
        $memcached->addServer( '127.0.0.1', 11211 );
        if ( @$memcached->flush() ) {
            $memcached->set( 'test_key', 'test_value', 10 );
            if ( $memcached->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object Cache (Memcached) cleared successfully.' ] );
            } else {
                wp_send_json_error( [ 'message' => 'Failed to verify Object Cache (Memcached) flush.' ] );
            }
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear Object Cache (Memcached). Check server connection.' ] );
        }
    } elseif ( class_exists( 'Memcache' ) ) {
        $memcache = new Memcache();
        $memcache->addServer( '127.0.0.1', 11211 );
        if ( @$memcache->flush() ) {
            $memcache->set( 'test_key', 'test_value', 0, 10 );
            if ( $memcache->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object Cache (Memcache) cleared successfully.' ] );
            } else {
                wp_send_json_error( [ 'message' => 'Failed to verify Object Cache (Memcache) flush.' ] );
            }
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear Object Cache (Memcache). Check server connection.' ] );
        }
    } elseif ( class_exists( 'Redis' ) ) {
        $redis = new Redis();
        try {
            $redis->connect( '127.0.0.1', 6379 );
            if ( $redis->flushAll() ) {
                $redis->set( 'test_key', 'test_value', 10 );
                if ( $redis->get( 'test_key' ) === 'test_value' ) {
                    wp_send_json_success( [ 'message' => 'Object Cache (Redis, PhpRedis) cleared successfully.' ] );
                } else {
                    wp_send_json_error( [ 'message' => 'Failed to verify Object Cache (Redis, PhpRedis) flush.' ] );
                }
            } else {
                wp_send_json_error( [ 'message' => 'Failed to clear Object Cache (Redis, PhpRedis).' ] );
            }
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => 'Redis connection error: ' . $e->getMessage() ] );
        }
    } elseif ( class_exists( 'Predis\Client' ) ) {
        try {
            $predis = new Predis\Client();
            $predis->connect();
            $predis->flushall();
            $predis->set( 'test_key', 'test_value' );
            if ( $predis->get( 'test_key' ) === 'test_value' ) {
                wp_send_json_success( [ 'message' => 'Object Cache (Predis) cleared successfully.' ] );
            } else {
                wp_send_json_error( [ 'message' => 'Failed to verify Object Cache (Predis) flush.' ] );
            }
        } catch ( Exception $e ) {
            wp_send_json_error( [ 'message' => 'Predis connection error: ' . $e->getMessage() ] );
        }
    } elseif ( function_exists( 'relay_flush' ) ) {
        if ( relay_flush() ) {
            wp_send_json_success( [ 'message' => 'Object Cache (Relay) cleared successfully, but verification not supported.' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear Object Cache (Relay).' ] );
        }
    } else {
        if ( wp_cache_flush() ) {
            wp_send_json_success( [ 'message' => 'Object Cache cleared using WordPress default method, verification not supported.' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Failed to clear Object Cache using WordPress default method.' ] );
        }
    }
}

// Clear all transients
function clear_all_transients() {
    global $wpdb;

    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'" );

    wp_send_json_success( [ 'message' => 'All transients cleared successfully.' ] );
}

// Ref: ChatGPT
