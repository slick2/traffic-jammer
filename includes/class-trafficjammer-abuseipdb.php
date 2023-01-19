<?php
/**
 * Class Trafficjammer_AbuseIPDB
 *
 * @package TrafficJammer
 */
class Traffic_Jammer_AbuseIPDB {

	/**
	 * AbuseIPDB endpoint
	 *
	 * @var string
	 */
	private $base_url = 'https://api.abuseipdb.com/api/v2/';
	/**
	 * Api Key
	 *
	 * @var string
	 */
	public $api;
	/**
	 * Class initialization
	 *
	 * @return void
	 */
	public function __construct() {
		$setting_options = get_option( 'wp_traffic_jammer_options' );
		$this->api       = $setting_options['abuseipdb_key'];
	}

	/**
	 * Check IP
	 *
	 * @param string $ip IP to be checked.
	 * @return bool would return true or false.
	 */
	public function check( $ip ) {

		$response = wp_remote_request(
			$this->base_url . 'check?ipAddress=' . $ip,
			array(
				'method'  => 'GET',
				'headers' => array(
					'Accept' => 'application/json',
					'Key'    => $this->api,
				),
			)
		);

		if ( $response ) {
			return json_decode( $response['body'], true );
		}

		return false;
	}
	/**
	 * Verify API key
	 *
	 * @param string $key API key.
	 * @return bool would return true or false.
	 */
	public static function verify( $key ) {

		return true;
	}

}

