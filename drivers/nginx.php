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



	/**
	 * Path by constant
	 */
	private $pathByConstant;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($data) {

		// Set object data
		$this->data = $data;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Update nginx settings
	 */
	public function updateSettings() {

		// Check path constant
		if ($this->usingConstant()) {
			return;
		}

		// Check submit
		if (!isset($_POST['nginx_path'])) {
			return;
		}

		// Save it
		$this->data->save(['nginx_path' => trim($_POST['nginx_path'])]);
	}



	/**
	 * Purge cache
	 */
	public function purgeCache() {

		// Check constant
		if ($this->usingConstant()) {
			$path = $this->pathByConstant;

		// Load
		} else {

			// Load data
			$this->data->loadNginx();

			// Stored value
			$path = $this->data->nginxPath;
		}

		// Check path
		if (!$this->isValidPath($path)) {
			return false;
		}

		// Remove and re-create
		global $wp_filesystem;
		$wp_filesystem->rmdir($path, true);
		$wp_filesystem->mkdir($path);

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



	/**
	 * Checks if the driver is using or not a constant path
	 */
	private function usingConstant() {

		// Check constant path
		if (isset($this->pathByConstant)) {
			return false !== $this->pathByConstant;
		}

		// Check constant
		if (defined('CLEAR_CACHES_NGINX_PATH')) {

			// Check constant value
			$path = constant('CLEAR_CACHES_NGINX_PATH');
			if (!empty($path)) {
				$this->pathByConstant = $path;
				return true;
			}
		}

		// Set flag value
		$this->pathByConstant = false;

		// No constant
		return false;
	}



	/**
	 * Validates the nginx path
	 */
	private function isValidPath($path) {

		if (empty($path)) {
			$this->error = 'Nginx cache path is not set.';
			return false;
		}

		if (!$this->checkCacheDir($path)) {
			$this->error = 'Nginx cache directory does not exist and cannot be created.';
			return false;
		}

		if (!$this->initializeFilesystem($path)) {
			$this->error = 'Nginx cache error: Filesystem API could not be initialized.';
			return false;
		}

		// Globals
		global $wp_filesystem;

		// Check entire path
		if (!$wp_filesystem->exists($path)) {
			$this->error = 'Nginx cache path does not exist.';
			return false;
		}

		// Ensures it is a directory
		if (!$wp_filesystem->is_dir($path)) {
			$this->error = 'Nginx cache path is not a directory.';
			return false;
		}

		// Find expected file format in directory files
		$list = $wp_filesystem->dirlist($path, true, true);
		if (!$this->validateDirList($list)) {
			$this->error = 'Nginx cache path does not appear to be a Nginx cache zone directory.';
			return false;
		}

		// And finally check if we can write
		if (!$wp_filesystem->is_writable($path)) {
			$this->error = 'Nginx cache path is not writable.';
			return false;
		}

		// Done
		return true;
	}



	/**
	 * Validate expected format of nginx cache files
	 */
	private function validateDirList($list) {

		// Enum directory items
		foreach ($list as $item) {

			// Abort if file is not a MD5 hash
			if ('f' === $item['type'] && (32 !== strlen($item['name']) || !ctype_xdigit($item['name']))) {
				return false;
			}

			// Validate subdirectories recursively
			if ('d' === $item[ 'type' ] && !$this->validateDirList($item['files'])) {
				return false;
			}
		}

		// Valid
		return true;
	}



	/**
	 * If the cache directory does not exist, try to create it
	 */
	private function checkCacheDir($path) {
		if (!@file_exists($path)) {
			@mkdir($path);
			if (!@file_exists($path)) {
				return false;
			}
		}
		return true;
	}



	/**
	 * Attempt to initialize the WP File System object
	 */
	private function initializeFilesystem($path) {

		// Buffering
		ob_start();

		// Attempt
		try {

			// Check WordPress file API
			if (!function_exists('request_filesystem_credentials')) {
				require_once ABSPATH.'wp-admin/includes/file.php';
			}

			// Template WordPress Administration API
			if (!function_exists('submit_button')) {
				require_once ABSPATH .'wp-admin/includes/template.php';
			}

			// Request credentials
			$credentials = @request_filesystem_credentials('', '', false, $path, null, true);

		// Error
		} catch (Exception $e) {
			$credentials = false;
		}

		// Remove output
		ob_end_clean();

		// Check credentials
		if (false === $credentials) {
			return false;
		}

		// Start object
		if (!WP_Filesystem($credentials, $path, true)) {
			return false;
		}

		// Done
		return true;
	}



}