<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Libraries;

/**
 * A WP functions wrapper class
 *
 * @package CloudFlare
 * @subpackage Libraries
 */
class WP_Wrapper {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Plugin file
	 */
	private $pluginFile;



	/**
	 * Nonce data
	 */
	private $noncePrefix;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Current plugin file
	 */
	public function setPluginFile($pluginFile) {
		$this->pluginFile = (string) $pluginFile;
	}



	/**
	 * Set a nonce prefix
	 */
	public function setNoncePrefix($noncePrefix) {
		$this->noncePrefix = (string) $noncePrefix;
	}



	// Plugin wrappers
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Return a resource URL
	 */
	public function getURL($path) {
		$pluginFile = isset($this->pluginFile)? $this->pluginFile : '';
		return plugins_url($path, $pluginFile);
	}



	// Nonce wrappers
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Create nonce
	 */
	public function createNonce($action = -1) {
		$action = (isset($this->noncePrefix)? $this->noncePrefix : '').$action;
		return wp_create_nonce($action);
	}



	/**
	 * Verify a valid nonce
	 */
	public function verifyNonce($nonce, $action = -1) {
		$action = (isset($this->noncePrefix)? $this->noncePrefix : '').$action;
		return wp_verify_nonce($nonce, $action);
	}



}