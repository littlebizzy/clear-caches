<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Admin page class
 *
 * @package Clear Caches
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

			<h1 id="clrchs-title">Clear Caches</h1>

			<form id="clrchs-form" data-nonce="<?php echo $this->plugin->wrapper->createNonce($this->plugin->nonceSeed); ?>">

				<h2 id="clrchs-nav-tabs" class="nav-tab-wrapper wp-clearfix">
					<a id="clrchs-nav-tab-all" href="#" class="nav-tab nav-tab-active">Overview</a>
					<?php if ($this->plugin->enabled('CLEAR_CACHES_OPCACHE')) : ?><a id="clrchs-nav-tab-opcache" href="#" class="nav-tab">PHP Opcache</a><?php endif; ?>
					<?php if ($this->plugin->enabled('CLEAR_CACHES_NGINX')) : ?><a id="clrchs-nav-tab-nginx" href="#" class="nav-tab">Nginx Cache</a><?php endif; ?>
					<?php if ($this->plugin->enabled('CLEAR_CACHES_OBJECT')) : ?><a id="clrchs-nav-tab-object" href="#" class="nav-tab">Object Cache</a><?php endif; ?>
				</h2>

				<div class="clrchs-nav-content-wrapper">

					<div id="clrchs-nav-content-all" class="clrchs-nav-content clrchs-nav-content-active">
						<?php $this->viewOverview->show(); ?>
					</div>

					<?php if ($this->plugin->enabled('CLEAR_CACHES_OPCACHE')) : ?>
						<div id="clrchs-nav-content-opcache" class="clrchs-nav-content">
							<?php $this->viewOpCache->show(); ?>
						</div>
					<?php endif; ?>

					<?php if ($this->plugin->enabled('CLEAR_CACHES_NGINX')) : ?>
						<div id="clrchs-nav-content-nginx" class="clrchs-nav-content">
							<?php $this->viewNginx->show(); ?>
						</div>
					<?php endif; ?>

					<?php if ($this->plugin->enabled('CLEAR_CACHES_OBJECT')) : ?>
						<div id="clrchs-nav-content-object" class="clrchs-nav-content">
							<?php $this->viewObjectCache->show(); ?>
						</div>
					<?php endif; ?>

				</div>

			</form>

		</div>

	<?php }



}