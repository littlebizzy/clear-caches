<?php
/**
 * Class ObjectHandler
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

use Exception;

/**
 * Class ObjectHandler
 *
 * @package LittleBizzy\ClearCaches
 */
class ObjectHandler {
	/**
	 * Constructor.
	 *
	 * @throws Exception Exception.
	 */
	public function __construct() {
		// @todo: do we need this check?
		if ( ! function_exists( 'wp_using_ext_object_cache' ) ) {
			throw new Exception( 'Object Cache get status function is not available.' );
		}

		// @todo: do we need this check?
		if ( ! function_exists( 'wp_cache_flush' ) ) {
			throw new Exception( 'Object Cache flush function is not available.' );
		}
	}

	/**
	 * Check if Opcache is enabled.
	 *
	 * @return bool
	 * @throws Exception Exception.
	 */
	protected function is_enabled(): bool {
		return wp_using_ext_object_cache();
	}

	/**
	 * Purge cache.
	 *
	 * @return void
	 * @throws Exception Exception.
	 */
	public function purge(): void {
		if ( $this->is_enabled() ) {
			$result = wp_cache_flush();
			if ( false === $result ) {
				throw new Exception( 'Object Cache flush failed.' );
			}
		}
	}
}
