<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Core;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Admin;
use \LittleBizzy\ClearCaches\Drivers;
use \LittleBizzy\ClearCaches\Helpers;
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Object Factory class
 *
 * @package Clear Caches
 * @subpackage Core
 */
class Factory {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Plugin object
	 */
	private $plugin;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($plugin) {
		$this->plugin = $plugin;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Magic GET method
	 */
	public function __get($name) {
		$method = 'create'.ucfirst($name);
		return method_exists($this, $method)? $this->{$method}() : null;
	}



	/**
	 * Magic CALL method
	 */
	public function __call($name, $args = null) {
		$method = 'create'.ucfirst($name);
		$args = (!empty($args) && is_array($args))? $args[0] : null;
		return method_exists($this, $method)? $this->{$method}($args) : null;
	}



	// Helpers and library objects creation
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Context object
	 */
	private function createContext() {
		return new Libraries\WP_Context;
	}



	/**
	 * Registrar object
	 */
	private function createRegistrar() {
		return new Helpers\Registrar($this->plugin);
	}



	/**
	 * WP Wrapper object
	 */
	private function createWrapper() {
		$wrapper = new Libraries\WP_Wrapper();
		$wrapper->setPluginFile($this->plugin->file);
		$wrapper->setNoncePrefix($this->plugin->file.'_');
		return $wrapper;
	}



	// Core objects creation
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Create Data object
	 */
	private function createData() {
		$options = new Libraries\WP_Options($this->plugin->prefix);
		return new Data($options);
	}



	/**
	 * Creates the AJAX Object
	 */
	private function createAjax() {
		return new AJAX($this->plugin);
	}



	// Drivers/API objects creation
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates the Driver Opcache object
	 */
	 private function createOpcache() {
 		return new Drivers\Opcache;
 	}



	/**
	 * Creates the Driver Nginx object
	 */
	private function createNginx() {
		return new Drivers\Nginx($this->createData());
	}



	/**
	 * Creates the Driver Object Cache object
	 */
	private function createObjectCache() {
		return new Drivers\Object_Cache;
	}



	// Admin objects creation
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates Toolbar object
	 */
	private function createToolbar() {
		return new Admin\Toolbar($this->plugin);
	}



	/**
	 * Admin object
	 */
	private function createAdmin() {
		return new Admin\Admin($this->plugin);
	}



	/**
	 * Admin Page
	 */
	private function createAdminPage() {
		return new Admin\Page($this->plugin);
	}



	// Admin Views objects creation
	// ---------------------------------------------------------------------------------------------------


	/**
	 * Admin Page All
	 */
	private function createAdminViewOverview($args = null) {
		return new Admin\Views\Overview($args);
	}



	/**
	 * Admin page PHP OpCache
	 */
	private function createAdminViewOpCache($args = null) {
		return new Admin\Views\OpCache($args);
	}



	/**
	 * Admin page Nginx
	 */
	private function createAdminViewNginx($args = null) {
		return new Admin\Views\Nginx($args);
	}



	/**
	 * Admin page Object Cache
	 */
	private function createAdminViewObjectCache($args = null) {
		return new Admin\Views\Object_Cache($args);
	}



}