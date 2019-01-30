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
	private static $purgeScopes = ['all', 'opcache', 'nginx', 'nginx-path', 'object'];



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

		// Verify the correct scope
		if (empty($_POST['scope']) || !in_array($_POST['scope'], self::$purgeScopes)) {
			$this->outputError('Scope argument missing or incorrect.');
		}

		// Enum scopes
		$scopeRequested = $_POST['scope'];
		foreach (self::$purgeScopes as $scope) {

			// Skip `all` scope type
			if ('all' == $scope) {
				continue;
			}

			// Check requested scope
			if ('all' == $scopeRequested || $scope == $scopeRequested) {

				// Early functionality check
				if ('all' == $scopeRequested) {

					// Verify any type of clear cache
					if ('nginx-path' == $scope ||
						('opcache' == $scope && !$this->plugin->enabled('CLEAR_CACHES_OPCACHE')) ||
						('nginx' == $scope && !$this->plugin->enabled('CLEAR_CACHES_NGINX')) ||
						('object' == $scope && !$this->plugin->enabled('CLEAR_CACHES_OBJECT'))) {

						// Skipped
						continue;
					}
				}

				// Do it
				$method = ('nginx-path' == $scope)? 'saveNginxPath' : 'purge'.ucfirst($scope);
				$this->{$method}();
			}
		}
	}



	// Purge methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Purge nginx web server
	 */
	private function purgeNginx() {

		// Check plugin
		if (!$this->plugin->enabled('CLEAR_CACHES')) {
			$this->response['data']['nginx'] = 'Clear Caches plugin is not enabled.';

		// Check functionality
		} elseif (!$this->plugin->enabled('CLEAR_CACHES_NGINX')) {
			$this->response['data']['nginx'] = 'Nginx clear cache functionality is disabled.';

		// Purge
		} else {

			// Attempt to remove nginx directory files
			$nginx = $this->plugin->factory->nginx;
			$this->response['data']['nginx'] = $nginx->purgeCache()? 1 : $nginx->getError();
			$this->response['data']['nginx_path'] = esc_html($nginx->lastPath());
		}
	}



	/**
	 * Saves nginx path
	 */
	private function saveNginxPath() {
		$nginx = $this->plugin->factory->nginx;
		$nginx->updateSettings();
		$this->response['data']['nginx-path'] = 1;
		$this->response['data']['nginx_path'] = esc_html($nginx->data()->nginxPath);
	}



	/**
	 * Purge PHP Opcache
	 */
	private function purgeOpcache() {

		// Check plugin
		if (!$this->plugin->enabled('CLEAR_CACHES')) {
			$this->response['data']['opcache'] = 'Clear Caches plugin is disabled.';

		// Check functionality
		} elseif (!$this->plugin->enabled('CLEAR_CACHES_OPCACHE')) {
			$this->response['data']['opcache'] = 'Opcache clear cache functionality is not enabled.';

		// Purge
		} else {

			// Attempt to purge Opcache
			$opcache = $this->plugin->factory->opcache;
			$this->response['data']['opcache'] = $opcache->purgeCache()? 1 : $opcache->getError();
		}
	}



	/**
	 * Purge Object cache
	 */
	private function purgeObject() {

		// Check plugin
		if (!$this->plugin->enabled('CLEAR_CACHES')) {
			$this->response['data']['object'] = 'Clear Caches plugin is disabled.';

		// Check functionality
		} elseif (!$this->plugin->enabled('CLEAR_CACHES_OBJECT')) {
			$this->response['data']['object'] = 'Object-Cache clear cache functionality is not enabled.';

		// Purge
		} else {

			// Attempt to purge object-cache
			$objectCache = $this->plugin->factory->objectCache;
			$this->response['data']['object'] = $objectCache->purgeCache()? 1 : $objectCache->getError();
		}
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