<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Drivers;

/**
 * Object cache class
 *
 * @package Purge Them All
 * @subpackage Drivers
 */
class Object_Cache {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Custom error
	 */
	private $error;



	/**
	 * Current Object Cache status
	 */
	private $enabled;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct() {
		$this->checkEnabled();
	}



	/**
	 * Check if the Object Cache is enabled
	 */
	private function checkEnabled() {
		$this->enabled = @function_exists('wp_using_ext_object_cache')? (bool) @wp_using_ext_object_cache() : false;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Purge cache
	 */
	public function purgeCache() {

		// Check extension
		if (!$this->enabled) {
			$this->error = 'Object Cache is not enabled.';
			return false;
		}

		// Check flush function
		if (!function_exists('wp_cache_flush')) {
			$this->error = 'Object Cache flush function is not available.';
			return false;
		}

		// Flush cache attempt
		$result = (bool) @wp_cache_flush();
		if (!$result) {
			$this->error = 'Object Cache flush failed.';
			return false;
		}

		// Done
		return true;
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