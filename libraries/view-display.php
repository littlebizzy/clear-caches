<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Libraries;

/**
 * A simple View class
 *
 * @package Clear Caches
 * @subpackage Libraries
 */
class View_Display {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Display args
	 */
	private $args;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($args = null) {
		$this->args = (empty($args) || !is_array($args))? [] : $args;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Shows the view
	 */
	public function show($args = null) {
		$args = (empty($args) || !is_array($args))? [] : $args;
		$this->args = (empty($this->args) || !is_array($this->args))? [] : $this->args;
		$this->display($this->prepare(array_merge($this->args, $args)));
	}



	// Methods for being overwritten
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Prepare view arguments
	 */
	protected function prepare($args) {
		return $args;
	}



	/**
	 * Display the view
	 */
	protected function display($args) {}



}