<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Libraries;

/**
 * AJAX class
 *
 * @package Clear Caches
 * @subpackage Helpers
 */
class WP_AJAX {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Action submitted
	 */
	protected $action;



	/**
	 * Arguments
	 */
	protected $nonceVar;
	protected $nonceSeed;
	protected $actions;
	protected $capabilities;



	/**
	 * WP init hook flags
	 */
	protected $doWPInit = true;
	protected $didWPInit = false;



	/**
	 * HTTP status code
	 */
	protected $statusCode = 200;



	/**
	 * WP Wrapper object
	 */
	protected $wrapper;



	/**
	 * Response array
	 */
	protected $response;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Start
	 */
	public function start() {

		// Configured by parent class
		$this->configure();

		// Check allowed actions
		if (!$this->checkActions())
			return;

		// Wait to WP init hook
		if ($this->doWPInit) {

			// Wait for user initialization
			add_action('init', array(&$this, 'init'));

		// Direct
		} else {

			// Create default response
			$this->response = $this->defaultResponse();

			// Continue
			$this->handleRequest();
		}
	}



	/**
	 * Configure object settings
	 * Intended to be used from the parent class
	 */
	protected function configure() {}



	/**
	 * WP init hook
	 */
	public function init() {

		// Hook performed
		$this->didWPInit = true;

		// Permissions and nonce
		$this->checkCapabilities();
		$this->checkNonce();

		// Create default response
		$this->response = $this->defaultResponse();

		// Continue
		$this->handleRequest();
	}



	/**
	 * Handle the AJAX request
	 * Intended to be used from the parent class
	 */
	protected function handleRequest() {}



	// Checks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Determine action scope
	 */
	protected function checkActions() {

		// Configured and input values
		if (empty($this->actions) || empty($_POST['action']))
			return false;

		// Copy action
		$this->action = $_POST['action'];

		// Cast to array
		$actions = $this->actions;
		if (!is_array($actions))
			$actions = [$actions];

		// Check action value
		return in_array($this->action, $actions);
	}



	/**
	 * Current user permissions
	 */
	protected function checkCapabilities() {

		// Check restrictions and user initialization
		if (empty($this->capabilities) || !function_exists('current_user_can'))
			return;

		// Cast to array if needee
		$capabilities = $this->capabilities;
		if (!is_array($capabilities))
			$capabilities = [$capabilities];

		// Enum capabilities
		foreach ($capabilities as $capability) {
			if (current_user_can($capability))
				return;
		}

		// Error
		$this->outputError('Operation not allowed for the current user.');
	}



	/**
	 * Check nonce param
	 */
	protected function checkNonce() {

		// Needs a var name and seed value
		if (!isset($this->nonceVar) || !isset($this->nonceSeed))
			return;

		// Check the post var
		if (!isset($_POST[$this->nonceVar]))
			$this->outputError('Operation not allowed due security issues.');

		// Nonce verification
		$verified = isset($this->wrapper)? $this->wrapper->verifyNonce($_POST[$this->nonceVar], $this->nonceSeed) : wp_verify_nonce($_POST[$this->nonceVar], $this->nonceSeed);
		if (!$verified)
			$this->outputError('Security parameters error: please reload the page and try again.');
	}



	// Output
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Return array of ajax response
	 */
	protected function defaultResponse($args = null) {
		$args = (empty($args) || !is_array($args))? [] : $args;
		return array_merge(['status' => 'ok', 'reason' => '', 'data' => []], $args);
	}



	/**
	 * Custom error output
	 */
	protected function outputError($reason, $statusCode = null) {

		// Check status code
		if (isset($statusCode))
			$this->statusCode = $statusCode;

		// Prepare error response
		$this->response = $this->defaultResponse([
			'status' => 'error',
			'reason' => $reason,
		]);

		// And finalize
		$this->outputResponse();
	}



	/**
	 * Output AJAX in JSON format and exit
	 */
	protected function outputResponse() {

        # Prevent browsers to cache response
        @header("Cache-Control: no-cache, must-revalidate", true); # HTTP/1.1
        @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT", true);   # Date in the past

		// JSON content
		@header('Content-Type: application/json; charset=utf-8', true, $this->statusCode);

		// Send the output and ends
		die(@json_encode($this->response));
	}



}