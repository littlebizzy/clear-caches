<?php
/*
Plugin Name: Clear Caches
Plugin URI: https://www.littlebizzy.com/plugins/clear-caches
Description: The easiest way to clear caches including WordPress cache, PHP Opcache, Nginx cache, Transient cache, Varnish cache, and object cache (e.g. Redis).
Version: 1.2.1
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
PBP Version: 1.2.0
WC requires at least: 3.3
WC tested up to: 3.5
Prefix: CLRCHS
*/

// Plugin namespace
namespace LittleBizzy\ClearCaches;

// Plugin constants
const FILE = __FILE__;
const PREFIX = 'clrchs';
const VERSION = '1.2.1';
const REPO = 'littlebizzy/clear-caches';

// Boot
require_once dirname(FILE).'/helpers/boot.php';
Helpers\Boot::instance(FILE);
