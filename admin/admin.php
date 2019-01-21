<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin;

/**
 * Admin area class
 *
 * @package Clear Caches
 * @subpackage Admin
 */
class Admin {



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

		// Set plugin object
		$this->plugin = $plugin;

		// Admin menu hook
		add_action('admin_menu', [$this, 'menu']);
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Add a submenu item to the WP Settings menu
	 */
	public function menu() {

		// Check if this plugin is enabled
		if (!$this->plugin->enabled('CLEAR_CACHES') ||
			(!$this->plugin->enabled('CLEAR_CACHES_OPCACHE') && !$this->plugin->enabled('CLEAR_CACHES_NGINX') && !$this->plugin->enabled('CLEAR_CACHES_OBJECT'))) {

			// No modules enabled
			return;
		}

		// Create submenu page
		$hook = add_submenu_page('options-general.php', 'Clear Caches', 'Clear Caches', 'manage_options', 'clear-caches', [$this, 'page']);

		// Add a load handler
		if (false !== $hook) {
			add_action('load-'.$hook, [$this, 'onLoad']);
		}
	}



	/**
	 * Load custom submenu handler
	 */
	public function onLoad() {
		$this->plugin->wrapper = $this->plugin->factory->wrapper;
		wp_enqueue_style( 'clrchs-admin', $this->plugin->wrapper->getURL('assets/admin.css'), [], $this->plugin->version);
		wp_enqueue_script('clrchs-admin', $this->plugin->wrapper->getURL('assets/admin.js'),  ['jquery'], $this->plugin->version, true);
	}



	/**
	 * Load the plugin page
	 */
	public function page() {
		$this->page = $this->plugin->factory->adminPage;
		$this->page->show();
	}



}
