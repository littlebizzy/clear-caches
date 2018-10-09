<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin;

/**
 * Toolbar class
 *
 * @package Purge Them All
 * @subpackage Admin
 */
class Toolbar {



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
		add_action('init', array(&$this, 'init'));
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * WP init hook
	 */
	public function init() {

		// Check current user permissions
		if (!current_user_can('manage_options'))
			return;

		// Load the wrapper object
		$this->plugin->wrapper = $this->plugin->factory->wrapper;

		// Add the admin bar
		add_action('admin_bar_menu', array(&$this, 'add'));

		// Add the footer code
		add_action('wp_footer', array(&$this, 'footer'));
		add_action('admin_footer', array(&$this, 'footer'));

		// Add Toolbar code
		wp_enqueue_style('prgtha-submit', $this->plugin->wrapper->getURL('assets/submit.css'), array(), $this->plugin->version);

		// Add the lightboxed plugin
		wp_enqueue_script('prgtha-lightboxed', $this->plugin->wrapper->getURL('assets/lightboxed/jquery.lightboxed.min.js'), array('jquery'), $this->plugin->version, true);

		// Add the submit handler
		wp_enqueue_script('prgtha-submit', $this->plugin->wrapper->getURL('assets/submit.js'), array('jquery', 'prgtha-lightboxed'), $this->plugin->version, true);
	}



	/**
	 * Adds the admin bar link
	 */
	public function add(&$wp_admin_bar) {

		// Initialize
		$menuItems = [];

		// Top menu
		$menuItems[] = [
			'id'     => 'prgtha-menu',
			'parent' => 'top-secondary',
			'title'  => 'Purge Them All',
			'href'   => '#all',
			'meta'   => [
				'title' => 'Clear all cache`s',
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => 'prgtha-menu-cloudflare',
			'parent' => 'prgtha-menu',
			'title'  => 'Purge Cloudflare cache',
			'href'   => '#cloudflare',
			'meta'   => [
				'title' => 'Clear Cloudflare cache`s',
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => 'prgtha-menu-opcache',
			'parent' => 'prgtha-menu',
			'title'  => 'Purge PHP Opcache',
			'href'   => '#opcache',
			'meta'   => [
				'title' => 'Clear PHP Opcache',
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => 'prgtha-menu-nginx',
			'parent' => 'prgtha-menu',
			'title'  => 'Purge Nginx cache',
			'href'   => '#nginx',
			'meta'   => [
				'title' => 'Clear Nginx cache',
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => 'prgtha-menu-object',
			'parent' => 'prgtha-menu',
			'title'  => 'Purge Object Cache',
			'href'   => '#object',
			'meta'   => [
				'title' => 'Clear Object Cache',
				'tabindex' => -1,
			],
		];

		// Add menus
		foreach ($menuItems as $menuItem)
			$wp_admin_bar->add_menu($menuItem);
	}



	/**
	 * Footer code
	 */
	public function footer() { ?>

		<div id="prgtha-progress" data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-nonce="<?php echo esc_attr($this->plugin->wrapper->createNonce($this->plugin->nonceSeed)); ?>">

			<div id="prgtha-progress-header">
				<p>Purge Them All</p>
			</div>

			<div id="prgtha-progress-body">

				<div id="prgtha-progress-body-inner">

					<p id="prgtha-loading-cloudflare-settings" class="prgtha-progress-item prgtha-progress-loading">Updating Cloudflare settings ...</p>
					<p id="prgtha-loading-cloudflare-dev-mode" class="prgtha-progress-item prgtha-progress-loading">Updating Cloudflare Dev mode ...</p>

					<p id="prgtha-loading-all" class="prgtha-progress-item prgtha-progress-loading">Removing all cache's ...</p>
					<p id="prgtha-loading-cloudflare" class="prgtha-progress-item prgtha-progress-loading">Removing Cloudflare cache ...</p>
					<p id="prgtha-loading-opcache" class="prgtha-progress-item prgtha-progress-loading">Removing OPcache ...</p>
					<p id="prgtha-loading-nginx" class="prgtha-progress-item prgtha-progress-loading">Removing Nginx cache ...</p>
					<p id="prgtha-loading-object" class="prgtha-progress-item prgtha-progress-loading">Removing Object cache ...</p>

					<p id="prgtha-done-cloudflare" class="prgtha-progress-item prgtha-progress-success">Cloudflare cache removed.</p>
					<p id="prgtha-error-cloudflare" class="prgtha-progress-item prgtha-progress-error"></p>

					<p id="prgtha-done-opcache" class="prgtha-progress-item prgtha-progress-success">PHP OPcache removed.</p>
					<p id="prgtha-error-opcache" class="prgtha-progress-item prgtha-progress-error"></p>

					<p id="prgtha-done-nginx" class="prgtha-progress-item prgtha-progress-success">Nginx cache removed.</p>
					<p id="prgtha-error-nginx" class="prgtha-progress-item prgtha-progress-error"></p>

					<p id="prgtha-done-object" class="prgtha-progress-item prgtha-progress-success">Object Cache removed.</p>
					<p id="prgtha-error-object" class="prgtha-progress-item prgtha-progress-error"></p>

					<p id="prgtha-done-cloudflare-settings" class="prgtha-progress-item prgtha-progress-success">Updated domain info via CloudFlare API.</p>
					<p id="prgtha-error-cloudflare-settings" class="prgtha-progress-item prgtha-progress-error"></p>

					<p id="prgtha-done-cloudflare-dev-mode" class="prgtha-progress-item prgtha-progress-success">Updated <strong>development mode</strong> status via CloudFlare API.</p>
					<p id="prgtha-error-cloudflare-dev-mode" class="prgtha-progress-item prgtha-progress-error"></p>

				</div>

			</div>

			<div id="prgtha-progress-close" class="prgtha-progress-item prgtha_lightboxed_close"><a href="#">Close window</a></div>

		</div>

	<?php }



}