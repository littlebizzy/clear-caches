<?php
/**
 * Class OPcacheHandler
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

use Exception;

/**
 * Class OPcacheHandler
 *
 * @package LittleBizzy\ClearCaches
 */
class OPcacheHandler {
	/**
	 * Constructor.
	 *
	 * @throws Exception Exception.
	 */
	public function __construct() {
		if ( ! extension_loaded( 'Zend OPcache' ) ) {
			throw new Exception( 'OPcache extension is not installed.' );
		}

		if ( ! function_exists( 'opcache_get_status' ) ) {
			throw new Exception( 'OPcache get status method is not available.' );
		}

		if ( ! function_exists( 'opcache_reset' ) ) {
			throw new Exception( 'OPcache reset method is not available.' );
		}
	}

	/**
	 * Check if Opcache is enabled.
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function is_enabled(): bool {
		$status = @opcache_get_status();

		if ( ! is_array( $status ) || ! isset( $status['opcache_enabled'] ) ) {
			throw new Exception( 'OPcache get status failure.' );
		}

		return (bool) $status['opcache_enabled'];
	}

	/**
	 * Purge cache.
	 *
	 * @return void
	 * @throws Exception Exception.
	 */
	public function purge(): void {
		if ( $this->is_enabled() ) {
			$result = opcache_reset();
			if ( false === $result ) {
				throw new Exception( 'OPcache reset method failed.' );
			}
		}
	}
}
