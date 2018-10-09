<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Libraries;

/**
 * A WP Options wrapper class
 *
 * @package Purge Them All
 * @subpackage Libraries
 */
class WP_Options {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Data prefix
	 */
	public $prefix;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($prefix = null) {
		$this->prefix = (string) $prefix;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Retrieve an option
	 */
	public function get($name, $toString = false) {
		$name = (string) $name;
		$value = get_option($this->prefix.$name);
		return $toString? (string) $value : $value;
	}



	/**
	 * Set an option value
	 */
	public function set($name, $value, $autoload = false, $toString = false) {
		$name = (string) $name;
		$value = $toString? (string) $value : $value;
		update_option($this->prefix.$name, $value, $autoload);
	}



	/**
	 * Remove one or several options
	 */
	public function del($name) {

		// Check array
		if (is_array($name)) {

			// Remove each element
			foreach ($name as $subname)
				$this->del($subname);

		// Single value
		} else {

			// Remove single option
			$name = (string) $name;
			delete_option($this->prefix.$name);
		}
	}



}