<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin;

/**
 * Toolbar class
 *
 * @package Clear Caches
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
		add_action('init', [$this, 'init']);
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * WP init hook
	 */
	public function init() {

		// Check current user permissions
		if (!current_user_can('manage_options')) {
			return;
		}

		// Load the wrapper object
		$this->plugin->wrapper = $this->plugin->factory->wrapper;

		// Add the admin bar
		add_action('admin_bar_menu', [$this, 'add']);

		// Add the footer code
		add_action('wp_footer', [$this, 'footer']);
		add_action('admin_footer', [$this, 'footer']);

		// Add Toolbar code
		wp_enqueue_style('clrchs-submit', $this->plugin->wrapper->getURL('assets/submit.css'), [], $this->plugin->version);

		// Add the lightboxed plugin
		wp_enqueue_script('clrchs-lightboxed', $this->plugin->wrapper->getURL('assets/lightboxed/jquery.lightboxed.min.js'), ['jquery'], $this->plugin->version, true);

		// Add the submit handler
		wp_enqueue_script('clrchs-submit', $this->plugin->wrapper->getURL('assets/submit.js'), ['jquery', 'clrchs-lightboxed'], $this->plugin->version, true);
	}



	/**
	 * Adds the admin bar link
	 */
	public function add(&$wp_admin_bar) {

		// Initialize
		$menuItems = [];

		// Top menu
		$menuItems[] = [
			'id'     => 'clrchs-menu',
			'parent' => 'top-secondary',
			'title'  => 'Clear Caches',
			'href'   => '#all',
			'meta'   => [
				'title' => '',
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => 'clrchs-menu-opcache',
			'parent' => 'clrchs-menu',
			'title'  => 'Clear PHP OPcache',
			'href'   => '#opcache',
			'meta'   => [
				'title' => '',
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => 'clrchs-menu-nginx',
			'parent' => 'clrchs-menu',
			'title'  => 'Clear Nginx Cache',
			'href'   => '#nginx',
			'meta'   => [
				'title' => '',
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => 'clrchs-menu-object',
			'parent' => 'clrchs-menu',
			'title'  => 'Clear Object Cache',
			'href'   => '#object',
			'meta'   => [
				'title' => '',
				'tabindex' => -1,
			],
		];

		// Add menus
		foreach ($menuItems as $menuItem) {
			$wp_admin_bar->add_menu($menuItem);
		}
	}



	/**
	 * Footer code
	 */
	public function footer() { ?>

		<div id="clrchs-progress" data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-nonce="<?php echo esc_attr($this->plugin->wrapper->createNonce($this->plugin->nonceSeed)); ?>">

			<div id="clrchs-progress-header">
				<p>Clear Caches</p>
			</div>

			<div id="clrchs-progress-body">

				<div id="clrchs-progress-body-inner">

					<p id="clrchs-loading-all" class="clrchs-progress-item clrchs-progress-loading">Removing all cache's ...</p>
					<p id="clrchs-loading-opcache" class="clrchs-progress-item clrchs-progress-loading">Removing OPcache ...</p>
					<p id="clrchs-loading-nginx" class="clrchs-progress-item clrchs-progress-loading">Removing NGINX cache ...</p>
					<p id="clrchs-loading-nginx-path" class="clrchs-progress-item clrchs-progress-loading">Saving NGINX path ...</p>
					<p id="clrchs-loading-object" class="clrchs-progress-item clrchs-progress-loading">Removing Object cache ...</p>

					<p id="clrchs-done-opcache" class="clrchs-progress-item clrchs-progress-success">PHP OPcache removed.</p>
					<p id="clrchs-error-opcache" class="clrchs-progress-item clrchs-progress-error"></p>

					<p id="clrchs-done-nginx" class="clrchs-progress-item clrchs-progress-success">NGINX cache removed: <span class="clrchs-nginx-path"></span></p>
					<p id="clrchs-error-nginx" class="clrchs-progress-item clrchs-progress-error"></p>

					<p id="clrchs-done-nginx-path" class="clrchs-progress-item clrchs-progress-success">NGINX path saved.</p>
					<p id="clrchs-error-nginx-path" class="clrchs-progress-item clrchs-progress-error"></p>

					<p id="clrchs-done-object" class="clrchs-progress-item clrchs-progress-success">Object Cache removed.</p>
					<p id="clrchs-error-object" class="clrchs-progress-item clrchs-progress-error"></p>

				</div>

			</div>

			<div id="clrchs-progress-close" class="clrchs-progress-item clrchs_lightboxed_close"><a href="#">Close window</a></div>

		</div>

	<?php }



}
