<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Core;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Data class
 *
 * @package Clear Caches
 * @subpackage Core
 */
class Data {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Options object
	 */
	private $options;



	/**
	 * CloudFlare
	 */
	private $domain;
	private $isCloudflare;
	private $cloudflareKey;
	private $cloudflareEmail;
	private $cloudflareZone;
	private $cloudflareDevMode;
	private $cloudflareDevModeAt;



	/**
	 * Nginx
	 */
	private $nginxPath;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($options) {

		// Options object
		$this->options = $options;

		// Current domain
		$this->setDomain();
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Load data
	 */
	public function load()  {
		$this->loadCloudflare();
		$this->loadNginx();
	}



	/**
	 * Load Cloudflare data
	 */
	public function loadCloudflare() {

		// Check IP
		$this->checkCloudflareIP();

		// Load data
		$this->cloudflareKey 		= $this->options->get('cloudflare_key', true);
		$this->cloudflareEmail 		= $this->options->get('cloudflare_email', true);
		$this->cloudflareZone 	 	= $this->sanitizeCloudflareZone(@json_decode($this->options->get('cloudflare_zone', true), true));
		$this->cloudflareDevModeAt 	= (int) $this->options->get('cloudflare_dev_mode_at');

		// Check Dev mode
		$this->checkCloudflareDevMode();
	}



	/**
	 * Load Nginx data
	 */
	public function loadNginx() {
		$this->nginxPath = $this->options->get('nginx_path', true);
	}



	/**
	 * Save data
	 */
	public function save($values, $reload = true) {

		// Check arguments
		if (empty($values) || !is_array($values))
			return;


		/* Cloudflare data */

		// Check key value
		if (isset($values['cloudflare_key']))
			$this->options->set('cloudflare_key', $values['cloudflare_key'], false, true);

		// Check email value
		if (isset($values['cloudflare_email']))
			$this->options->set('cloudflare_email', $values['cloudflare_email'], false, true);

		// Check zone value
		if (isset($values['cloudflare_zone']))
			$this->options->set('cloudflare_zone', @json_encode($values['cloudflare_zone']), false, true);

		// Check Dev mode timestamp
		if (isset($values['cloudflare_dev_mode_at']))
			$this->options->set('cloudflare_dev_mode_at', (int) $values['cloudflare_dev_mode_at']);


		/* Nginx data */

		// Check Nginx path
		if (isset($values['nginx_path']))
			$this->options->set('nginx_path', $values['nginx_path'], false, true);


		/* Post-processing */

		// Check reload
		if ($reload)
			$this->load();
	}



	/**
	 * Remove options from database
	 */
	public function remove() {
		$this->options->del(['cloudflare_key', 'cloudflare_email', 'cloudflare_zone', 'cloudflare_dev_mode_at', 'nginx_path']);
	}



	/**
	 * Sanitize zone data
	 */
	public function sanitizeCloudflareZone($zone) {

		// Check array
		if (empty($zone) || !is_array($zone))
			$zone = array();

		// Sanitize values
		return [
			'id' 				=> isset($zone['id'])? 				 $zone['id'] : '',
			'name' 				=> isset($zone['name'])? 			 $zone['name'] : '',
			'status' 			=> isset($zone['status'])? 			 $zone['status'] : '',
			'paused' 			=> isset($zone['paused'])? 			 $zone['paused'] : '',
			'type' 				=> isset($zone['type'])? 			 $zone['type'] : '',
			'development_mode' 	=> isset($zone['development_mode'])? $zone['development_mode'] : '',
		];
	}



	/**
	 * Magic GET method
	 */
	public function __get($name) {
		return $this->$name;
	}



	/**
	 * Check current Cloudflare IP headers
	 */
	public function checkCloudflareIP() {
		$this->isCloudflare = Libraries\Ip_Rewrite::isCloudflare();
	}



	/**
	 * Copy the Cloudflare options
	 */
	public function copyCloudflarePluginSettings() {

		// Cloudflare data object
		if (false === ($cloudflareData = $this->getCloudflarePluginDataObject()))
			return;

		// Check existing values
		$this->loadCloudflare();
		if (!empty($this->cloudflareKey) || !empty($this->cloudflareEmail))
			return;

		// Save settings
		$this->save([
			'cloudflare_key' 		 => $cloudflareData->key,
			'cloudflare_email' 		 => $cloudflareData->email,
			'cloudflare_zone' 		 => $cloudflareData->zone,
			'cloudflare_dev_mode_at' => $cloudflareData->devModeAt,
		]);
	}



	/**
	 * Update Cloudflare values from this plugin
	 */
	public function updateCloudflarePluginSettings() {

		// Cloudflare data object
		if (false === ($cloudflareData = $this->getCloudflarePluginDataObject()))
			return;

		// Load current settings
		$this->loadCloudflare();

		// Sync data
		$cloudflareData->save([
			'key' => $this->cloudflareKey,
			'email' => $this->cloudflareEmail,
			'zone' => $this->cloudflareZone,
			'dev_mode_at' => $this->cloudflareDevModeAt,
		]);
	}



	/**
	 * Check and retrieve Cloudflare plugin Data object
	 */
	public function getCloudflarePluginDataObject() {
		$className = '\LittleBizzy\CloudFlare\Core\Data';
		return (class_exists($className) && method_exists($className, 'instance'))? $className::instance() : false;
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check a valid Dev Mode due the 3 hours limit
	 */
	private function checkCloudflareDevMode() {

		// Default value
		$this->cloudflareDevMode = false;

		// Check timestamp
		if (empty($this->cloudflareDevModeAt))
			return;

		// Check current value
		$devMode = isset($this->cloudflareZone['development_mode'])? (int) $this->cloudflareZone['development_mode'] : 0;
		if ($devMode <= 0)
			return;

		// Check the 3 hours limit
		if (time() - $this->cloudflareDevModeAt >= 10800) {
			$this->cloudflareZone['development_mode'] = 0;
			$this->save(['cloudflare_zone' => $this->cloudflareZone, 'cloudflare_dev_mode_at' => 0]);
			return;
		}

		// Dev mode is active
		$this->cloudflareDevMode = true;
	}



	/**
	 * Current domain
	 */
	private function setDomain() {
		$this->domain = ''.@parse_url(site_url(), PHP_URL_HOST);
		if (0 === stripos($this->domain, 'www.'))
			$this->domain = substr($this->domain, 4);
//$this->domain = 'asimetrica.com';
	}



}