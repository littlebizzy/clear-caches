<?php
/**
 * Class Plugin.
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

require_once __DIR__ . '/RequestHandler.php';
require_once __DIR__ . '/AdminBar.php';

/**
 * Class Plugin.
 *
 * @package LittleBizzy\ClearCaches
 */
class Plugin {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		new RequestHandler();
		new AdminBar();
	}

	/**
	 * Get known cache pypes.
	 *
	 * @return array[]
	 */
	public static function get_cache_types(): array {
		return array(
			'nginx'   => array(
				'title'      => __( 'Purge Nginx Cache', 'clear-caches' ),
				'class_name' => 'NginxHandler',
			),
			'opcache' => array(
				'title'      => __( 'Purge OPcache cache', 'clear-caches' ),
				'class_name' => 'OPcacheHandler',
			),
			'object'  => array(
				'title'      => __( 'Purge Object Cache', 'clear-caches' ),
				'class_name' => 'ObjectHandler',
			),
		);
	}
}
