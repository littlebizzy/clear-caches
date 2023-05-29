<?php
/**
 * Class Bootstrap.
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

require_once __DIR__ . '/AdminBar.php';

/**
 * Class Bootstrap.
 *
 * @package LittleBizzy\ClearCaches
 */
class Bootstrap {
	/**
	 * Class constructor.
	 */
	public function __construct() {
			new AdminBar();
	}
}
