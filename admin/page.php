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

		// Prepares nginx path by constant
		$pathByConstant = defined('CLEAR_CACHES_NGINX_PATH')? constant('CLEAR_CACHES_NGINX_PATH') : false;

		// Creates the Nginx view
		$this->viewNginx = $this->plugin->factory->adminViewNginx([
			'path' => $this->data->nginxPath,
			'pathByConstant' => $pathByConstant,
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

				<table class="form-table">

					<?php $this->viewOverview->show(); ?>

					<?php if ($this->plugin->enabled('CLEAR_CACHES_NGINX')) $this->viewNginx->show(); ?>

					<?php if ($this->plugin->enabled('CLEAR_CACHES_OPCACHE')) $this->viewOpCache->show(); ?>

					<?php if ($this->plugin->enabled('CLEAR_CACHES_OBJECT')) $this->viewObjectCache->show(); ?>

				</table>

			</form>

		</div>

	<?php }



}