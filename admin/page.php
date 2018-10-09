<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin;

// Aliased namespaces
use \LittleBizzy\PurgeThemAll\Libraries;

/**
 * Admin page class
 *
 * @package Purge Them All
 * @subpackage Admin
 */
class Page extends Libraries\View_Display {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Plugin object
	 */
	private $plugin;



	/**
	 * Data object
	 */
	private $data;



	/**
	 * Opcache object
	 */
	private $opcache;



	/**
	 * Object Cache object
	 */
	private $objectCache;



	/**
	 * Subpages
	 */
	private $viewOverview;
	private $viewCloudflare;
	private $viewOpCache;
	private $viewNginx;
	private $viewObjectCache;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($plugin) {
		$this->plugin = $plugin;
	}



	/**
	 * Prepare data just before to display
	 */
	protected function prepare($args) {

		// Data object
		$this->data = $this->plugin->factory->data;
		$this->data->load();

		// Creates the first view
 		$this->viewOverview = $this->plugin->factory->adminViewOverview;

		// Creates the Cloudflare view
		$this->viewCloudFlare = $this->plugin->factory->adminViewCloudflare([
			'key' 			=> $this->data->cloudflareKey,
			'email' 		=> $this->data->cloudflareEmail,
			'zone' 			=> $this->data->cloudflareZone,
			'devMode'		=> $this->data->cloudflareDevMode,
			'domain' 		=> $this->data->domain,
			'isCloudFlare' 	=> $this->data->isCloudflare,
		]);

		// Creates the OpCache view
		$this->opcache = $this->plugin->factory->opcache;
		$this->viewOpCache = $this->plugin->factory->adminViewOpCache([
			'loaded'		=> $this->opcache->isExtensionLoaded(),
			'enabled'		=> $this->opcache->isEnabled(),
		]);

		// Creates the Nginx view
		$this->viewNginx = $this->plugin->factory->adminViewNginx([
			'path'			=> $this->data->nginxPath,
		]);

		// Creates the Object Cache view
		$this->objectCache = $this->plugin->factory->objectCache;
		$this->viewObjectCache = $this->plugin->factory->adminViewObjectCache([
			'enabled'		=> $this->objectCache->isEnabled(),
		]);
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Admin page output
	 */
	protected function display($args) { ?>

		<div class="wrap">

			<h1 id="prgtha-title">Purge Them All</h1>

			<form id="prgtha-form" data-nonce="<?php echo $this->plugin->wrapper->createNonce($this->plugin->nonceSeed); ?>">

				<h2 id="prgtha-nav-tabs" class="nav-tab-wrapper wp-clearfix">
					<a id="prgtha-nav-tab-all" href="#" class="nav-tab nav-tab-active">Overview</a>
					<a id="prgtha-nav-tab-cloudflare" href="#" class="nav-tab">CloudFlare Cache</a>
					<a id="prgtha-nav-tab-opcache" href="#" class="nav-tab">PHP Opcache</a>
					<a id="prgtha-nav-tab-nginx" href="#" class="nav-tab">Nginx Cache</a>
					<a id="prgtha-nav-tab-object" href="#" class="nav-tab">Object Cache</a>
				</h2>

				<div class="prgtha-nav-content-wrapper">

					<div id="prgtha-nav-content-all" class="prgtha-nav-content prgtha-nav-content-active">
						<?php $this->viewOverview->show(); ?>
					</div>

					<div id="prgtha-nav-content-cloudflare" class="prgtha-nav-content">
						<?php $this->viewCloudFlare->show(); ?>
					</div>

					<div id="prgtha-nav-content-opcache" class="prgtha-nav-content">
						<?php $this->viewOpCache->show(); ?>
					</div>

					<div id="prgtha-nav-content-nginx" class="prgtha-nav-content">
						<?php $this->viewNginx->show(); ?>
					</div>

					<div id="prgtha-nav-content-object" class="prgtha-nav-content">
						<?php $this->viewObjectCache->show(); ?>
					</div>

				</div>

			</form>

		</div>

	<?php }



}