<?php
/*
Plugin Name: Purge Them All
Plugin URI: https://www.littlebizzy.com
Description: The easiest way to clear caches including WordPress cache, PHP Opcache, Nginx cache, CloudFlare cache, Varnish cache, and object cache (e.g. Redis).
Version: 1.0.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: PRGTHA
*/

// Plugin namespace
namespace LittleBizzy\PurgeThemAll;

// Avoid script calls via plugin URL
if (!function_exists('add_action'))
	die;

// Plugin constants
const FILE = __FILE__;
const PREFIX = 'prgtha';
const VERSION = '1.0.0';

// Loader
require_once dirname(FILE).'/helpers/loader.php';

// Run the main class
Helpers\Runner::start('Core\Core', 'instance');