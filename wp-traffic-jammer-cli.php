<?php
/**
 * Command for wp-cli
 *
 * @package WPTrafficJammer
 */

/**
 * WP Traffic Jammer command line
 */
class WP_Traffic_Jammer_CLI {
	/**
	 * Display status
	 *
	 * @return void
	 */
	public function status() {
		WP_CLI::line( 'WP Traffic Jammer activated' );
		WP_CLI::line( '' );
	}
	/**
	 * Add an IP to the block list
	 *
	 * @param string $args arguments.
	 * @return void
	 */
	public function block( $args ) {
		wp_traffic_jammer_add_ip( $args[0] );
		WP_CLI::line( $args[0] . ' addded to the block list' );
	}
	/**
	 * Remove an IP to the block list
	 *
	 * @param string $args arguments.
	 * @return void
	 */
	public function unblock( $args ) {
		wp_traffic_jammer_remove_ip( $args[0] );
		WP_CLI::line( $args[0] . ' removed from the block list' );
	}

}

/**
 * Reister Command
 */
function wp_traffic_jammer_cli_register_commands() {
	WP_CLI::add_command( 'jam', 'WP_Traffic_Jammer_CLI' );
}
add_action( 'cli_init', 'wp_traffic_jammer_cli_register_commands' );

