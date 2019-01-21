<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Drivers;

/**
 * Cloudflare class
 *
 * @package Clear Caches
 * @subpackage Drivers
 */
class Cloudflare {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Data object
	 */
	private $data;



	/**
	 * Error message
	 */
	private $error;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($plugin) {
		$this->plugin = $plugin;
	}



	/**
	 * Cloudflare data
	 */
	private function loadData() {
		$this->data = $this->plugin->factory->data;
		$this->data->loadCloudflare();
	}



	/**
	 * Update settings
	 */
	public function updateSettings() {

		// Cloudflare data
		$this->loadData();


		/* Key */

		// Check key
		$key = isset($_POST['cloudflare_key'])? trim($_POST['cloudflare_key']) : '';

		// Save new key value
		if ($key != $this->data->cloudflareKey)
			$this->data->save(['cloudflare_key' => $key]);


		/* Email */

		// Check email
		$email = isset($_POST['cloudflare_email'])? trim($_POST['cloudflare_email']) : '';

		// Validate
		if (!empty($email) && !is_email($email)) {
			$this->error = ('The Cloudflare API email'.esc_html($email).' is not valid.');
			return false;
		}

		// Check if save a new email
		if ($email != $this->data->cloudflareEmail)
			$this->data->save(['cloudflare_email' => $email]);


		/* API request */

		// Validate
		if (empty($key) || empty($email)) {
			$this->data->save(['cloudflare_zone' => '', 'cloudflare_dev_mode_at' => '']);
			$this->error = 'Missing Cloudflare API Key or Email value.';
			return false;
		}

		// Initialize
		$zone = false;

		// Perform the API calls
		$result = $this->checkDomain($key, $email);
		if (is_wp_error($result)) {
			$this->error = 'CloudFlare API request error.';
			return false;
		}

		// Missing domain
		if (false === $result) {
			$this->error = 'Current domain does not match the CloudFlare API zones.';
			return false;
		}


		/* Domain found */

		// Process zone
		$zone = $this->data->sanitizeCloudflareZone($result);
		$this->data->save(['cloudflare_zone' => $zone]);

		// Check development mode
		$this->checkDevMode($zone['development_mode']);

		// Cast developer mode
		$zone['development_mode'] = $this->data->cloudflareDevMode;

		// Update Cloudflare plugin data
		$this->data->updateCloudflarePluginSettings();

		// Done
		return $zone;
	}



	/**
	 * Change Development Mode status
	 */
	public function updateDevMode() {

		// Cloudflare data
		$this->loadData();

		// Check data
		if (!$this->checkAPIData())
			return false;

		// Check zone
		if (!$this->checkAPIZone())
			return false;

		// Determine action
		$enable = empty($_POST['dev_mode'])? false : ('on' == $_POST['dev_mode']);

		// Enable or disable Dev mode
		$response = $this->plugin->factory->cloudflareAPI(['key' => $this->data->cloudflareKey, 'email' => $this->data->cloudflareEmail])->setDevMode($this->data->cloudflareZone['id'], $enable);
		if (is_wp_error($response)) {
			$this->error = 'CloudFlare API request error.';
			return false;
		}

		// Success
		$zone = $this->data->cloudflareZone;
		$zone['development_mode'] = $response['result']['time_remaining'];
		$this->data->save(['cloudflare_zone' => $zone]);

		// Check development mode
		$this->checkDevMode($zone['development_mode']);

		// Update Cloudflare plugin data
		$this->data->updateCloudflarePluginSettings();

		// Done
		return $zone['development_mode'];
	}



	/**
	 * Purge all files
	 */
	public function purgeCache() {

		// Cloudflare data
		$this->loadData();

		// Check data
		if (!$this->checkAPIData())
			return false;

		// Check zone
		if (!$this->checkAPIZone())
			return false;

		// Purge cache request
		$response = $this->plugin->factory->cloudflareAPI(['key' => $this->data->cloudflareKey, 'email' => $this->data->cloudflareEmail])->purgeZone($this->data->cloudflareZone['id']);
		if (is_wp_error($response)) {
			$this->error = 'CloudFlare API request error.';
			return false;
		}

		// Done
		return true;
	}



	/**
	 * Error value
	 */
	public function getError() {
		return $this->error;
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check basic API data
	 */
	private function checkAPIData() {

		// Copy data
		$key = $this->data->cloudflareKey;
		$email = $this->data->cloudflareEmail;

		// Check API data
		if (empty($key) || empty($email)) {
			$this->error = 'Missing Cloudflare API Key or Email value.';
			return false;
		}

		// Found
		return true;
	}



	/**
	 * Check cloudflare zone
	 */
	private function checkAPIZone() {

		// Copy data
		$zoneId = $this->data->cloudflareZone['id'];

		// Check zone data
		if (empty($zoneId)) {
			$this->error = 'Missing Cloudflare API zone detected.';
			return false;
		}

		// Found
		return true;
	}



	/**
	 * Check current domain calling the API
	 */
	private function checkDomain($key, $email) {

		// Initialize
		$page = $maxPages = 1;

		// Enum page
		while ($page <= $maxPages) {

			// Perform the API call
			$response = $this->plugin->factory->cloudflareAPI(['key' => $key, 'email' => $email])->getZones($page);
			if (is_wp_error($response))
				return $response;

			// Check domains
			if (false !== ($zone = $this->matchZone($response['result'])))
				return $zone;

			// Max pages check
			if (1 == $page)
				$maxPages = empty($response['result_info']['total_pages'])? 0 : (int) $response['result_info']['total_pages'];

			// Next page
			$page++;
		}

		// Done
		return false;
	}



	/**
	 * Compare zones with current domain
	 */
	private function matchZone($result) {

		//Check array
		if (empty($result) || !is_array($result))
			return false;

		// Current domain
		$domain = strtolower(trim($this->data->domain));

		// Enum zones
		foreach ($result as $zone) {

			// Check zone name
			$name = strtolower(trim($zone['name']));
			if ('' === $name || false === strpos($domain, $name))
				continue;

			// Check same alue
			if ($domain == $name)
				return $zone;

			// Check length
			$length = strlen($name);
			if ($length > strlen($domain))
				continue;

			// Ends with the zone name
			if (substr($domain, -$length) === $name)
				return $zone;
		}

		// Not found
		return false;
	}



	/**
	 * Check current zone
	 */
	private function checkDevMode($remaining) {

		// Initialize
		$devModeAt = 0;

		// Check zone development info
		if ((int) $remaining > 0) {
			$duration = 10800 - (int) $remaining;
			if ($duration >= 0)
				$devModeAt = time() - $duration;
		}

		// Save mode
		$this->data->save(['cloudflare_dev_mode_at' => $devModeAt]);
	}



}