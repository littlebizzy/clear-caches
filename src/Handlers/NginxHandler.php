<?php
/**
 * Class NginxHandler
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

use Exception;
use WP_Filesystem_Base;

/**
 * Class NginxHandler
 *
 * @package LittleBizzy\ClearCaches
 */
class NginxHandler {
	/**
	 * Path to Nginx cache.
	 *
	 * @var string
	 */
	private string $path;

	/**
	 * Internal WordPress Filesystem.
	 *
	 * @var WP_Filesystem_Base
	 */
	private WP_Filesystem_Base $wp_filesystem;

	/**
	 * Constructor.
	 *
	 * @throws Exception Exception.
	 */
	public function __construct() {
		$this->path = '/var/www/cache/nginx/';

		$this->initialize_filesystem();
	}

	/**
	 * Attempt to initialize WordPress Filesystem.
	 *
	 * @throws Exception Exception.
	 */
	protected function initialize_filesystem(): void {
		/* @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		// Temporary save global WordPress Filesystem.
		$global_wp_filesystem = $wp_filesystem;

		require_once ABSPATH . 'wp-admin/includes/file.php';

		ob_start();
		$credentials = request_filesystem_credentials( '', '', false, $this->path, null, true );
		ob_end_clean();

		// Required but have not been provided
		if ( false === $credentials ) {
			throw new Exception( 'Required credentials have not been provided.' );
		}

		if ( ! WP_Filesystem( $credentials, $this->path, true ) ) {
			throw new Exception( 'Failure initialize WordPress Filesystem.' );
		}

		// Set internal WordPress Filesystem.
		$this->wp_filesystem = $wp_filesystem;

		// Restore global WordPress Filesystem.
		$wp_filesystem = $global_wp_filesystem; // phpcs: WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	/**
	 * Check if Opcache is enabled.
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function is_enabled(): bool {
		return ! $this->wp_filesystem->errors->has_errors();
	}

	/**
	 * Purge cache.
	 *
	 * @return void
	 * @throws Exception Exception.
	 */
	public function purge(): void {
		if ( $this->is_enabled() ) {
			$dirlist = $this->wp_filesystem->dirlist( $this->path );

			if ( ! is_array( $dirlist ) ) {
				throw new Exception( 'Unable to list cache directory contents.' );
			}

			foreach ( $dirlist as $item ) {
				$filename = $this->path . $item['name'];
				$result   = $this->wp_filesystem->delete( $filename );

				if ( true !== $result ) {
					throw new Exception( "Unable to delete cache file: $filename." );
				}
			}
		}
	}
}
