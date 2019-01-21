<?php
/*
Plugin Name: Clear Caches
Plugin URI: https://www.littlebizzy.com/plugins/clear-caches
Description: The easiest way to clear caches including WordPress cache, PHP Opcache, Nginx cache, Transient cache, Varnish cache, and object cache (e.g. Redis).
Version: 1.1.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
PBP Version: 1.1.1
WC requires at least: 3.3
WC tested up to: 3.5
Prefix: CLRCHS
*/

// Plugin namespace
namespace LittleBizzy\ClearCaches;

// Avoid script calls via plugin URL
if (!function_exists('add_action')) {
	die;
}

// Plugin constants
const FILE = __FILE__;
const PREFIX = 'clrchs';
const VERSION = '1.1.0';

// Loader
require_once dirname(FILE).'/helpers/loader.php';

// Run the main class
Helpers\Runner::start('Core\Core', 'instance');