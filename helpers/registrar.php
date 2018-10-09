<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Helpers;

/**
 * Registrar class
 *
 * @package Purge Them All
 * @subpackage Helpers
 */
class Registrar {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Plugin object
	 */
	private $plugin;



	/**
	 * Handler object
	 */
	private $handler;



	/**
	 * Temp instance
	 */
	private static $instance;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($plugin) {
		$this->plugin = $plugin;
	}



	/**
	 * Set the plugin hooks handler
	 */
	public function setHandler($handler) {

		// Set object
		$this->handler = $handler;

		// Check activation support
		if (method_exists($this->handler, 'activation'))
			register_activation_hook($this->plugin->file, array($this->handler, 'activation'));

		// Check deactivation support
		if (method_exists($this->handler, 'deactivation'))
			register_deactivation_hook($this->plugin->file, array($this->handler, 'deactivation'));

		// Check uninstall support, points to a local static method
		if (method_exists($this->handler, 'uninstall')) {
			self::$instance = $this;
			register_uninstall_hook($this->plugin->file, array('\\'.__CLASS__, 'uninstall'));
		}
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Plugin uninstall wrapper
	 */
	public static function uninstall() {
		self::$instance->handler->uninstall();
	}



}