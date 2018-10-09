<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin;

/**
 * Admin area class
 *
 * @package Purge Them All
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

		// WP init hook
		add_action('init', array(&$this, 'disableCloudflarePluginMenu'));

		// Admin menu hook
		add_action('admin_menu', array(&$this, 'menu'));
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Add a submenu item to the WP Settings menu
	 */
	public function menu() {

		// Create submenu page
		$hook = add_submenu_page('options-general.php', 'Purge Them All', 'Purge Them All', 'manage_options', 'purge-them-all', array(&$this, 'page'));

		// Add a load handler
		if (false !== $hook)
			add_action('load-'.$hook, array(&$this, 'onLoad'));
	}



	/**
	 * Load custom submenu handler
	 */
	public function onLoad() {
		$this->plugin->wrapper = $this->plugin->factory->wrapper;
		wp_enqueue_style( 'prgtha-admin', $this->plugin->wrapper->getURL('assets/admin.css'), array(), $this->plugin->version);
		wp_enqueue_script('prgtha-admin', $this->plugin->wrapper->getURL('assets/admin.js'),  array('jquery'), $this->plugin->version, true);
	}



	/**
	 * Load the plugin page
	 */
	public function page() {
		$this->page = $this->plugin->factory->adminPage;
		$this->page->show();
	}



	/**
	 * Hide the cloudflare plugin Options menu
	 */
	public function disableCloudflarePluginMenu() {

		// Check Cloudflare plugin Admin class
		$className = '\LittleBizzy\CloudFlare\Admin\Admin';
		if (!class_exists($className) || !method_exists($className, 'instance'))
			return;

		// Remove menu
		remove_action('admin_menu', array($className::instance(), 'adminMenu'));
	}



}