<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Core;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Data class
 *
 * @package Clear Caches
 * @subpackage Core
 */
class Data {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Options object
	 */
	private $options;



	/**
	 * Nginx
	 */
	private $nginxPath;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($options) {

		// Options object
		$this->options = $options;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Load data
	 */
	public function load()  {
		$this->loadNginx();
	}



	/**
	 * Load Nginx data
	 */
	public function loadNginx() {
		$this->nginxPath = $this->options->get('nginx_path', true);
	}



	/**
	 * Save data
	 */
	public function save($values, $reload = true) {


		/* Input */

		// Check arguments
		if (empty($values) || !is_array($values)) {
			return;
		}


		/* Nginx data */

		// Check Nginx path
		if (isset($values['nginx_path'])) {
			$this->options->set('nginx_path', $values['nginx_path'], false, true);
		}


		/* Post-processing */

		// Check reload
		if ($reload) {
			$this->load();
		}
	}



	/**
	 * Remove options from database
	 */
	public function remove() {
		$this->options->del(['nginx_path']);
	}



	/**
	 * Magic GET method
	 */
	public function __get($name) {
		return $this->$name;
	}



}