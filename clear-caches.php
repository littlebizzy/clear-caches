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
if (!defined('ABSPATH')) {
    exit;
}

// Disable WordPress.org updates for this plugin
add_filter('gu_override_dot_org', function ($overrides) {
    $overrides['clear-caches/clear-caches.php'] = true;
    return $overrides;
});

// Define default constants if not already defined
if (!defined('CLEAR_CACHES_NGINX_PATH')) {
    define('CLEAR_CACHES_NGINX_PATH', '/var/www/cache/nginx/');
}

if (!defined('CLEAR_CACHES_OBJECT')) {
    define('CLEAR_CACHES_OBJECT', true); // Default value to show Clear Object Cache link
}

if (!defined('CLEAR_CACHES_OPCACHE')) {
    define('CLEAR_CACHES_OPCACHE', true); // Default value to show Clear PHP Opcache link
}

if (!defined('CLEAR_CACHES_TRANSIENTS')) {
    define('CLEAR_CACHES_TRANSIENTS', true); // Default value to show Clear Transients link
}

if (!defined('CLEAR_CACHES_NGINX')) {
    define('CLEAR_CACHES_NGINX', true); // Default value to show Clear Nginx Cache link
}

// Add Clear Caches dropdown to the WP Admin bar
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        return;
    }

    $wp_admin_bar->add_node([
        'id'     => 'clear_caches',
        'parent' => 'top-secondary',
        'title'  => 'Clear Caches',
        'meta'   => ['class' => 'clear-caches-admin-bar']
    ]);

    // Conditionally add Clear PHP Opcache link based on CLEAR_CACHES_OPCACHE
    if (CLEAR_CACHES_OPCACHE) {
        $wp_admin_bar->add_node([
            'id'     => 'clear_php_opcache',
            'parent' => 'clear_caches',
            'title'  => 'Clear PHP Opcache',
            'href'   => 'javascript:void(0);',
            'meta'   => ['class' => 'clear-cache-php-opcache']
        ]);
    }

    // Conditionally add Clear Nginx Cache link based on CLEAR_CACHES_NGINX
    if (CLEAR_CACHES_NGINX) {
        $wp_admin_bar->add_node([
            'id'     => 'clear_nginx_cache',
            'parent' => 'clear_caches',
            'title'  => 'Clear Nginx Cache',
            'href'   => 'javascript:void(0);',
            'meta'   => ['class' => 'clear-cache-nginx']
        ]);
    }

    // Conditionally add Clear Object Cache link based on CLEAR_CACHES_OBJECT
    if (CLEAR_CACHES_OBJECT) {
        $wp_admin_bar->add_node([
            'id'     => 'clear_object_cache',
            'parent' => 'clear_caches',
            'title'  => 'Clear Object Cache',
            'href'   => 'javascript:void(0);',
            'meta'   => ['class' => 'clear-cache-object']
        ]);
    }

    // Conditionally add Clear Transients link based on CLEAR_CACHES_TRANSIENTS
    if (CLEAR_CACHES_TRANSIENTS) {
        $wp_admin_bar->add_node([
            'id'     => 'clear_transients',
            'parent' => 'clear_caches',
            'title'  => 'Clear Transients',
            'href'   => 'javascript:void(0);',
            'meta'   => ['class' => 'clear-cache-transients']
        ]);
    }
}, 100);

// Enqueue JavaScript for clearing caches site-wide on both frontend and backend
add_action('wp_enqueue_scripts', function () {
    if (is_admin_bar_showing()) {
        $plugin_url = plugin_dir_url(__FILE__);
        
        // Enqueue the script with jQuery as a dependency
        wp_enqueue_script('clear-caches-script', $plugin_url . 'clear-caches.js', ['jquery'], null, true);

        // Localize script to pass AJAX URL and nonce to JavaScript
        wp_localize_script('clear-caches-script', 'clearCachesData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('clear_caches_nonce')
        ]);
    }
});

// Handle AJAX request to clear caches or clear transients
add_action('wp_ajax_clear_caches_action', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    // Verify nonce for security
    check_ajax_referer('clear_caches_nonce', 'security');

    // Sanitize input data
    $cache_type = sanitize_text_field($_POST['cache_type'] ?? '');

    switch ($cache_type) {
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
            wp_send_json_error(['message' => 'Invalid cache type specified.']);
    }
});

// Separate functions for each cache-clearing operation

function clear_php_opcache() {
    if (function_exists('opcache_reset')) {
        opcache_reset();
        wp_send_json_success(['message' => 'PHP Opcache cleared successfully.']);
    } else {
        wp_send_json_error(['message' => 'PHP Opcache is not enabled.']);
    }
}

function clear_nginx_cache() {
    $nginx_cache_path = defined('CLEAR_CACHES_NGINX_PATH') ? CLEAR_CACHES_NGINX_PATH : '/var/www/cache/nginx/';
    if (file_exists($nginx_cache_path) && is_dir($nginx_cache_path) && is_writable($nginx_cache_path)) {
        $files = glob("$nginx_cache_path/*");
        if ($files) {
            array_map('unlink', $files);
            wp_send_json_success(['message' => 'Nginx Cache cleared successfully.']);
        } else {
            wp_send_json_error(['message' => 'Nginx Cache is empty or unable to read files.']);
        }
    } else {
        wp_send_json_error(['message' => 'Nginx Cache path does not exist, is not a directory, or is not writable.']);
    }
}

function clear_object_cache() {
    if (class_exists('Memcached')) {
        $memcached = new Memcached();
        $memcached->addServer('127.0.0.1', 11211);
        if (@$memcached->flush()) {
            $memcached->set('test_key', 'test_value', 10);
            if ($memcached->get('test_key') === 'test_value') {
                wp_send_json_success(['message' => 'Object Cache (Memcached) cleared successfully.']);
            } else {
                wp_send_json_error(['message' => 'Failed to verify Object Cache (Memcached) flush.']);
            }
        } else {
            wp_send_json_error(['message' => 'Failed to clear Object Cache (Memcached). Check server connection.']);
        }
    } elseif (class_exists('Memcache')) {
        $memcache = new Memcache();
        $memcache->addServer('127.0.0.1', 11211);
        if (@$memcache->flush()) {
            $memcache->set('test_key', 'test_value', 0, 10);
            if ($memcache->get('test_key') === 'test_value') {
                wp_send_json_success(['message' => 'Object Cache (Memcache) cleared successfully.']);
            } else {
                wp_send_json_error(['message' => 'Failed to verify Object Cache (Memcache) flush.']);
            }
        } else {
            wp_send_json_error(['message' => 'Failed to clear Object Cache (Memcache). Check server connection.']);
        }
    } elseif (class_exists('Redis')) {
        $redis = new Redis();
        try {
            $redis->connect('127.0.0.1', 6379);
            if ($redis->flushAll()) {
                $redis->set('test_key', 'test_value', 10);
                if ($redis->get('test_key') === 'test_value') {
                    wp_send_json_success(['message' => 'Object Cache (Redis, PhpRedis) cleared successfully.']);
                } else {
                    wp_send_json_error(['message' => 'Failed to verify Object Cache (Redis, PhpRedis) flush.']);
                }
            } else {
                wp_send_json_error(['message' => 'Failed to clear Object Cache (Redis, PhpRedis).']);
            }
        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Redis connection error: ' . $e->getMessage()]);
        }
    } elseif (class_exists('Predis\Client')) {
        try {
            $predis = new Predis\Client();
            $predis->connect();
            $predis->flushall();
            $predis->set('test_key', 'test_value');
            if ($predis->get('test_key') === 'test_value') {
                wp_send_json_success(['message' => 'Object Cache (Predis) cleared successfully.']);
            } else {
                wp_send_json_error(['message' => 'Failed to verify Object Cache (Predis) flush.']);
            }
        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Predis connection error: ' . $e->getMessage()]);
        }
    } elseif (function_exists('relay_flush')) {
        if (relay_flush()) {
            wp_send_json_success(['message' => 'Object Cache (Relay) cleared successfully, but verification not supported.']);
        } else {
            wp_send_json_error(['message' => 'Failed to clear Object Cache (Relay).']);
        }
    } else {
        if (wp_cache_flush()) {
            wp_send_json_success(['message' => 'Object Cache cleared using WordPress default method, verification not supported.']);
        } else {
            wp_send_json_error(['message' => 'Failed to clear Object Cache using WordPress default method.']);
        }
    }
}

function clear_all_transients() {
    global $wpdb;

    // Delete all transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");

    wp_send_json_success(['message' => 'All transients cleared successfully.']);
}

// Ref: ChatGPT
