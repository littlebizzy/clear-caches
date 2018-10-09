<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Libraries;

/**
 * Context class
 *
 * @package Purge Them All
 * @subpackage Helpers
 */
class WP_Context {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Boolean flags
	 */
	private $isAdmin 	= false;
	private $isAJAX 	= false;
	private $isCRON 	= false;
	private $isXMLRPC 	= false;
	private $isFrontEnd = false;



	/**
	 * AJAX values
	 */
	private $POSTAction;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Set basic plugin data
	 */
	public function __construct() {

		// Admin context
		if (is_admin()) {

			// Ajax call
			if (defined('DOING_AJAX') && DOING_AJAX) {
				$this->isAJAX = true;
				$this->POSTAction = (!empty($_POST) && is_array($_POST) && isset($_POST['action']))? $_POST['action'] : null;

			// Admin area
			} else {
				$this->isAdmin = true;
			}

		// CRON request
		} elseif (defined('DOING_CRON') && DOING_CRON) {
			$this->isCRON = true;

		// XMLRPC request
		} elseif (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
			$this->isXMLRPC = true;

		// Front-end
		} else {
			$this->isFrontEnd = true;
		}
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Magic GET method
	 */
	public function __get($name) {
		return $this->$name;
	}



	// AJAX action checks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check particular AJAX action
	 */
	public function isAJAXAction($action) {
		return ($this->isAJAX && isset($this->POSTAction) && $action == $this->POSTAction);
	}



	/**
	 * Check if the AJAX action is included in an array of actions
	 */
	public function inAJAXAction($action, $actions) {
		return ($this->isAJAX && isset($this->POSTAction) && in_array($action, $this->POSTAction));
	}



	/**
	 * Determine if a prefix match the beginning of the AJAX action
	 */
	public function AJAXActionStartsWith($prefix) {
		return ($this->isAJAX && isset($this->POSTAction) && 0 === strpos($this->POSTAction, $prefix));
	}



}