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

}

/**
 * Register Command
 */
function trafficjammer_cli_register_commands() {
	WP_CLI::add_command( 'jam', 'Traffic_Jammer_CLI' );
}
add_action( 'cli_init', 'trafficjammer_cli_register_commands' );

