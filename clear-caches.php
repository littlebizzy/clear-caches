<?php
/**
 * Plugin Name: Clear Caches
 * Plugin URI: https://www.littlebizzy.com/plugins/clear-caches
 * Description: Purge all of the WordPress caches
 * Version: 1.2.4
 * Author: LittleBizzy
 * Author URI: https://www.littlebizzy.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * PBP Version: 1.2.0
 * WC requires at least: 3.3
 * WC tested up to: 3.5
 * Prefix: CLRCHS
 * Text Domain:       clear-caches
 * Domain Path:       /languages/
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

defined( 'ABSPATH' ) || exit;

define( 'CLEAR_CACHE_FILE', __FILE__ );

require_once __DIR__ . '/src/Bootstrap.php';

new Bootstrap();
