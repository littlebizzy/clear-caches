<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Drivers;

/**
 * Ningx class
 *
 * @package Clear Caches
 * @subpackage Drivers
 */
class Nginx {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Data object
	 */
	private $data;



	/**
	 * Error message
	 */
	private $error;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($data) {
		$this->data = $data;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Update nginx settings
	 */
	public function updateSettings() {

		// Check submit
		if (!isset($_POST['nginx_path']))
			return;

		// Save it
		$this->data->save(['nginx_path' => trim($_POST['nginx_path'])]);
	}



	/**
	 * Purge cache
	 */
	public function purgeCache() {

		// Load data
		$this->data->loadNginx();

		// Check path
		if (!$this->isValidPath())
			return false;

		// Remove and re-create
		global $wp_filesystem;
		$wp_filesystem->rmdir($this->data->nginxPath, true);
		$wp_filesystem->mkdir($this->data->nginxPath);

		// Done
		return true;
	}



	/**
	 * Error value
	 */
	public function getError() {
		return $this->error;
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------


	private function isValidPath() {

		$path = $this->data->nginxPath;

		if ( empty( $path ) ) {
			$this->error = 'Nginx cache path is not set.';
			return false;
		}

		if (!$this->checkCacheDir()) {
			$this->error = 'Nginx cache directory does not exist and cannot be created.';
			return false;
		}

		if (!$this->initializeFilesystem() ) {
			$this->error = 'Nginx cache error: Filesystem API could not be initialized.';
			return false;
		}

		// Globals
		global $wp_filesystem;

		if ( ! $wp_filesystem->exists( $path ) ) {
			$this->error = 'Nginx cache path does not exist.';
			return false;

		}

		if ( ! $wp_filesystem->is_dir( $path ) ) {
			$this->error = 'Nginx cache path is not a directory.';
			return false;
		}

		$list = $wp_filesystem->dirlist( $path, true, true );
		if ( ! $this->validateDirList( $list ) ) {
			$this->error = 'Nginx cache path does not appear to be a Nginx cache zone directory.';
			return false;
		}

		if ( ! $wp_filesystem->is_writable( $path ) ) {
			$this->error = 'Nginx cache path  is not writable.';
			return false;
		}

		// Done
		return true;
	}



	private function validateDirList($list) {

		foreach ( $list as $item ) {

			// abort if file is not a MD5 hash
			if ( $item[ 'type' ] === 'f' && ( strlen( $item[ 'name' ] ) !== 32 || ! ctype_xdigit( $item[ 'name' ] ) ) ) {
				return false;
			}

			// validate subdirectories recursively
			if ( $item[ 'type' ] === 'd' && ! $this->validateDirList( $item[ 'files' ] ) ) {
				return false;
			}

		}

		return true;
	}


	private function checkCacheDir() {
		$path = $this->data->nginxPath;

		// if the cache directory doesn't exist, try to create it
		if ( ! @file_exists( $path ) ) {
			@mkdir( $path );
			if (! @file_exists( $path ))
				return false;
		}

		return true;
	}



	private function initializeFilesystem() {

		$path = $this->data->nginxPath;

		// Buffering
		ob_start();

		// Attempt
		try {

			// Check WordPress file API
			if ( ! function_exists( 'request_filesystem_credentials' ) )
				require_once ABSPATH . 'wp-admin/includes/file.php';

			if ( ! function_exists( 'submit_button' ) )
				require_once ABSPATH . 'wp-admin/includes/template.php';

			// Request credentials
			$credentials = @request_filesystem_credentials( '', '', false, $path, null, true);

		// Error
		} catch (Exception $e) {
			$credentials = false;
		}

		// Remove output
		ob_end_clean();

		if ( $credentials === false )
			return false;

		if ( ! WP_Filesystem( $credentials, $path, true ) )
			return false;

		return true;
	}



}