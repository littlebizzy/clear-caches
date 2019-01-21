<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Core;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * AJAX class
 *
 * @package Clear Caches
 * @subpackage Core
 */
class AJAX extends Libraries\WP_AJAX {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Plugin object
	 */
	private $plugin;



	/**
	 * Actions mapping
	 */
	private static $actionsMap = [
		'purge' => 'purge',
	];



	/**
	 * Scopes allowed in purge action
	 */
	private static $purgeScopes = ['all', 'opcache', 'nginx', 'object'];



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($plugin) {

		// Plugin object
		$this->plugin = $plugin;

		// Start
		$this->start();
	}



	/**
	 * AJAX configuration
	 */
	protected function configure() {
		$this->nonceVar 	= 'nonce';
		$this->nonceSeed 	= $this->plugin->nonceSeed;
		$this->capabilities = 'manage_options';
		$this->actions 		= $this->prefixActions(array_keys(self::$actionsMap));
		$this->wrapper 		= $this->plugin->factory->wrapper;
	}



	/**
	 * Handle the AJAX request
	 */
	protected function handleRequest() {

		// Perform the mapped action
		$action = substr($this->action, strlen($this->plugin->prefix.'_'));
		$method = self::$actionsMap[$action];
		$this->{$method}();

		// End
		$this->outputResponse();
	}



	// Purge actions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Purge by scope
	 */
	private function purge() {
		$this->checkScope();
		$scopeRequested = $_POST['scope'];
		foreach (self::$purgeScopes as $scope) {
			if ('all' == $scope) {
				continue;
			}
			if ('all' == $scopeRequested || $scope == $scopeRequested) {
				$method = 'purge'.ucfirst($scope);
				$this->{$method}();
			}
		}
	}



	/**
	 * Check the correct scope
	 */
	private function checkScope() {
		if (empty($_POST['scope']) || !in_array($_POST['scope'], self::$purgeScopes)) {
			$this->outputError('Scope argument missing or incorrect.');
		}
	}



	// Purge methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Purge nginx web server
	 */
	private function purgeNginx() {
		$nginx = $this->plugin->factory->nginx;
		$nginx->updateSettings();
		$this->response['data']['nginx'] = $nginx->purgeCache()? 1 : $nginx->getError();
	}



	/**
	 * Purge PHP Opcache
	 */
	private function purgeOpcache() {
		$opcache = $this->plugin->factory->opcache;
		$this->response['data']['opcache'] = $opcache->purgeCache()? 1 : $opcache->getError();
	}



	/**
	 * Purge Object cache
	 */
	private function purgeObject() {
		$objectCache = $this->plugin->factory->objectCache;
		$this->response['data']['object'] = $objectCache->purgeCache()? 1 : $objectCache->getError();
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Add a prefix to the actions
	 */
	private function prefixActions($actions) {
		$actions2 = [];
		foreach ($actions as $action) {
			$actions2[] = $this->plugin->prefix.'_'.$action;
		}
		return $actions2;
	}



}