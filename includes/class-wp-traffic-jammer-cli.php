<?php
/**
 * Command for wp-cli
 *
 * @package TrafficJammer
 */

/**
 * Traffic Jammer command line
 */
class Traffic_Jammer_CLI {
	/**
	 * Display status
	 *
	 * @return void
	 */
	public function status() {
		WP_CLI::line( 'Traffic Jammer activated' );
		WP_CLI::line( '' );
	}
	/**
	 * Add an IP to the blocklist
	 *
	 * @param string $args arguments.
	 * @return void
	 */
	public function block( $args ) {
		trafficjammer_block_ip( $args[0] );
		WP_CLI::line( $args[0] . ' addded to the block list' );
	}
	/**
	 * Remove an IP to the blocklist
	 *
	 * @param string $args arguments.
	 * @return void
	 */
	public function unblock( $args ) {
		trafficjammer_unblock_ip( $args[0] );
		WP_CLI::line( $args[0] . ' removed from the block list' );
	}
	/**
	 * Trust all on wp-login.php
	 *
	 * @return void
	 */
	public function trustall() {
		trafficjammer_trust_all();
		WP_CLI::line( 'Allow all access to wp-login.php' );
	}
	/**
	 * Add an IP to whitelist
	 *
	 * @param string $args arguments.
	 * @return void
	 */
	public function trust( $args ) {
		trafficjammer_trust_ip( $args[0] );
		WP_CLI::line( $args[0] . ' addded to the allow list' );
	}
	/**
	 * Remove an IP on whitelist
	 *
	 * @param string $args arguments.
	 * @return void
	 */
	public function untrust( $args ) {
		trafficjammer_untrust_ip( $args[0] );
		WP_CLI::line( $args[0] . ' removed from the allow list' );
	}
	/**
	 * Display Top 10 IP visists
	 *
	 * @return void
	 */
	public function topip() {
		global $wpdb;
		$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT count(*) as num_visits, IP as ip FROM ' . $wpdb->prefix . 'trafficjammer_traffic where IP is not null GROUP BY IP ORDER BY num_visits DESC LIMIT 10' ) );
		WP_CLI::line( ' IP' . ".\t\t\t" . str_pad( 'Visits', 26, ' ', STR_PAD_LEFT ) );
		WP_CLI::line( str_repeat( '=', 52) );
		foreach ( $traffic_logs as $value ) {
			$visits = number_format( $value->num_visits, 0, ',' );
			WP_CLI::line( ' ' . $value->ip . "\t\t" . str_pad( $visits, 26, ' ', STR_PAD_LEFT ) );
		}
	}


}

/**
 * Register Command
 */
function trafficjammer_cli_register_commands() {
	WP_CLI::add_command( 'jam', 'Traffic_Jammer_CLI' );
}
add_action( 'cli_init', 'trafficjammer_cli_register_commands' );

