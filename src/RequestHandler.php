<?php
/**
 * Class RequestHandler
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

use Exception;

/**
 * Class RequestHandler
 *
 * @package LittleBizzy\ClearCaches
 */
class RequestHandler {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_clear_caches_purge', array( $this, 'purge' ) );
	}

	/**
	 * Process purge request.
	 *
	 * @return void
	 */
	public function purge(): void {
		check_ajax_referer( 'clear-caches' );

		$cache_types = Plugin::get_cache_types();
		$cache_type  = isset( $_REQUEST['cacheType'] ) ? $_REQUEST['cacheType'] : '';

		try {
			foreach ( $cache_types as $id => $data ) {
				if ( 'all' === $cache_type || $id === $cache_type ) {
					$this->run( $data );
				}
			}

			wp_send_json_success();
		} catch ( Exception $exception ) {
			wp_send_json_error( $exception->getMessage() );
		}
	}

	/**
	 * Run cache clean handler.
	 *
	 * @param array $data Data.
	 *
	 * @return void
	 */
	protected function run( array $data ) {
		require_once __DIR__ . '/Handlers/' . $data['class_name'] . '.php';

		$class_name = __NAMESPACE__ . '\\' . $data['class_name'];
		$handler    = new $class_name();
		$handler->purge();
	}
}
