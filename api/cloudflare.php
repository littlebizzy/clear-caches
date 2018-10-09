<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\API;

/**
 * Cloudflare API class
 *
 * @package Purge Them All
 * @subpackage API
 */
class Cloudflare {



	// Constants
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Endpoint base URL
	 */
	const BASE_URL = 'https://api.cloudflare.com/client/v4/';



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Current values
	 */
	private $key;
	private $email;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($key, $email) {
		$this->key = $key;
		$this->email = $email;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Retrieve current zones
	 */
	public function getZones($page = 1, $per_page = 50) {
		$endpoint = add_query_arg(['page' => $page, 'per_page' => $per_page], 'zones');
		return $this->request($endpoint, [
			'method' => 'GET',
		]);
	}



	/**
	 * Retrieve zone details
	 */
	public function getZone($zoneId) {
		return $this->request('zones/'.$zoneId, [
			'method' => 'GET',
		]);
	}



	/**
	 * Set dev mode
	 */
	public function setDevMode($zoneId, $enable) {
		return $this->request('zones/'.$zoneId.'/settings/development_mode', [
			'method' => 'PATCH',
			'body' => @json_encode(['value' => $enable? 'on' : 'off']),
		]);
	}



	/**
	 * Purge zone cache
	 */
	public function purgeZone($zoneId) {
		return $this->request('zones/'.$zoneId.'/purge_cache', [
			'method' => 'DELETE',
			'body'   => @json_encode(['purge_everything' => true]),
		]);
	}



	/**
	 * Makes an API request
	 */
	public function request($endpoint, $args) {

		// Perform request
	    $result = wp_remote_request(self::BASE_URL.$endpoint, array_merge([
	        'timeout' 	=> 10,
	        'sslverify' => true,
			'headers' 	=> [
				'Content-Type'	=> 'application/json',
				'X-Auth-Key'	=> $this->key,
				'X-Auth-Email'	=> $this->email,
			],
	    ], $args));

		// Check error
		if (is_wp_error($result))
			return $result;

		// Check response code
		if (empty($result['response']) || empty($result['response']['code']) || 200 != $result['response']['code'])
			return $this->newError('cloudflare_api_error_code', $result);

		// Check body
		if (empty($result['body']))
			return $this->newError('cloudflare_api_error_body', $result);

		// Cast body
		$response = @json_decode($result['body'], true);
		if (empty($response) || !is_array($response))
			return $this->newError('cloudflare_api_error_response', $result);

		// Check result
		if (empty($response['result']) || 'true' != $response['success'])
			return $this->newError('cloudflare_api_error_result', $response);

		// Done
		return $response;
	}



	// Utils
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates new WP_Error object
	 */
	private function newError($code = '', $message = '', $data = '') {
		return new \WP_Error($code, $message, $data);
	}



}