<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Core;

/**
 * Purge class
 *
 * @package Clear Caches
 * @subpackage Core
 */
class Purge {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Purge scope
	 */
	private $scope;



	/**
	 * Error data
	 */
	private $error;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	private function __construct($scope = null) {
		$this->scope = $scope;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Purge
	 */
	public function run() {
		return true;
	}



	/**
	 * Error object
	 */
	public function getError() {
		return $this->error;
	}



}