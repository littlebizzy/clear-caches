<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Drivers;

/**
 * Opcache class
 *
 * @package Purge Them All
 * @subpackage Drivers
 */
class Opcache {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Custom error
	 */
	private $error;



	/**
	 * Check the PHP extension
	 */
	private $extensionLoaded;



	/**
	 * Current Opcache status
	 */
	private $enabled;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct() {
		$this->checkExtension();
		$this->checkEnabled();
	}



	/**
	 * Check current extension
	 */
	private function checkExtension() {
		$this->extensionLoaded = @extension_loaded('Zend OPcache');
	}



	/**
	 * Check if Opcache is enabled
	 */
	private function checkEnabled() {

		// Reset values
		$this->enabled = false;

		// Check extension
		if (!$this->extensionLoaded)
			return;

		// Retrieve status
		$status = @function_exists('opcache_get_status')? @opcache_get_status() : false;
		if (empty($status) || !is_array($status))
			return;

		// Check enabled
		$this->enabled = isset($status['opcache_enabled'])? (bool) $status['opcache_enabled'] : false;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Purge cache
	 */
	public function purgeCache() {

		// Check extension
		if (!$this->extensionLoaded) {
			$this->error = 'Opcache extension is not installed.';
			return false;
		}

		// Check if enabled
		if (empty($this->enabled)) {
			$this->error = 'Opcache function is installed but not enabled.';
			return false;
		}

		// Purge method
		if (!@function_exists('opcache_reset')) {
			$this->error = 'Opcache reset method is not available.';
			return false;
		}

		// Purge attempt
		$result = @opcache_reset();
		if (!$result) {
			$this->error = 'Opcache reset method failed.';
			return false;
		}

		// Done
		return true;
	}



	/**
	 * Retrieve PHP extension load status
	 */
	public function isExtensionLoaded() {
		return $this->extensionLoaded;
	}



	/**
	 * Current status
	 */
	public function isEnabled() {
		return $this->enabled;
	}



	/**
	 * Error value
	 */
	public function getError() {
		return $this->error;
	}



}