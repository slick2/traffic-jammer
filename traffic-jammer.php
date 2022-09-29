<?php
/**
 * Traffic Jammer
 *
 * @package  TrafficJammer
 *
 * @wordpress-plguin
 * Plugin Name:        Traffic Jammer
 * Plugin URI:          https://github.com/slick2/traffic-jammer
 * Description:         WordPress plugin to block IP and bots that causes
 *                      malicious traffic.  The poormans WAF.
 * Version:             0.9
 * Requires at least:   5.2
 * Requires PHP:        7.4
 * Author:              Carey Dayrit
 * Author URI:          http://careydayrit.com
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         traffic-jammer
 */

/** Sanitize server variables */
$wptj_server = array_map( 'sanitize_server_var', $_SERVER );

wp_enqueue_script( 'jquery-ui-tabs' );

/**
 * Activate plugin
 *
 * @return void
 */
function wp_traffic_jammer_activate() {
	// define the bad bots.
	$bad_bots  = 'DotBot, Applebot, applebot, GnowitNewsbot, InfoTigerBot, digitalshadowsbot, SeznamBo, YandexBot, badbot';
	$options   = '';
	$blocklist = '';
	$whitelist = '';
	// Get the options.
	if ( get_option( 'wp_traffic_jammer_options' ) === false ) {
		add_option( 'wp_traffic_jammer_options' );
	}

	if ( get_option( ' wp_traffic_jammer_blocklist' ) === false ) {
		add_option( 'wp_traffic_jammer_blocklist', $blocklist, '', 'no' );
	}

	if ( get_option( ' wp_traffic_jammer_whitelist' ) === false ) {
		add_option( 'wp_traffic_jammer_whitelist', $whitelist, '', 'no' );
	}

	if ( get_option( ' wp_traffic_jammer_user_agents' ) === false ) {
		add_option( 'wp_traffic_jammer_user_agents', $bad_bots, '', 'no' );
	}

}
register_activation_hook( __FILE__, 'wp_traffic_jammer_activate' );

/**
 * Deactivate plugin
 *
 * @return void
 */
function wp_traffic_jammer_deactivate() {
	delete_option( 'wp_traffic_jammer_options' );
	delete_option( 'wp_traffic_jammer_blocklist' );
	delete_option( 'wp_traffic_jammer_whitelist' );
	delete_option( 'wp_traffic_jammer_user_agents' );
}
// register_deactivation_hook( __FILE__, 'wp_traffic_jammer_deactivate' ); uncomment later.
/**
 * Limit IP
 *
 * @return void
 */
function wp_traffic_jammer_limit_ip() {
	global $wptj_server;
	$blocklist = get_option( 'wp_traffic_jammer_blocklist' );

	if ( ! isset( $options['blocklist'] ) ) {
		return;
	}

	$ip        = $wptj_server['REMOTE_ADDR'];
	$blocklist = array_map( 'trim', explode( ',', $blocklist ) );

	/**  Check if this IP is in blocklist. */
	$ip_forbidden = wp_traffic_jammer_check_ip( $ip, $blocklist );

	if ( $ip_forbidden ) {
		header( 'HTTP/1.0 403 Forbidden' );
		exit;
	}
}
add_action( 'init', 'wp_traffic_jammer_limit_ip' );

/**
 * Whitelist
 */
function wp_traffic_jammer_whitelist_ip() {
	global $wptj_server;
	$whitelist = get_option( 'wp_traffic_jammer_whitelist' );

	if ( empty( $whitelist ) ) {
		return;
	}

	$whitelist = array_map( 'trim', explode( ',', $whitelist ) );

	$ip = $wptj_server['REMOTE_ADDR'];
	// Check if this IP is in whitelistlist.
	$ip_allow = wp_traffic_jammer_check_ip( $ip, $whitelist );

	if ( preg_match( '/(wp-login.php)/', $wptj_server['REQUEST_URI'] ) ) {
		if ( ! $ip_allow ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit;
		}
	}
}
add_action( 'init', 'wp_traffic_jammer_whitelist_ip' );
/**
 * Limit User Agents
 *
 * @return void
 */
function wp_traffic_jammer_limit_user_agents() {
	global $wptj_server;
	$user_agents = get_option( 'wp_traffic_jammer_user_agents' );

	if ( ! isset( $user_agents ) ) {
		return;
	}

	$user_agents = explode( ',', $user_agents );

	// TODO : This will hit hard on longer list.
	foreach ( $user_agents as $bot ) {
		if ( stripos( $wptj_server['HTTP_USER_AGENT'], $bot ) !== false ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit();
		}
	}
}
add_action( 'init', 'wp_traffic_jammer_limit_user_agents' );

/**
 *
 * Add menu page
 *
 * @return void
 */
function wp_traffic_jammer_add_page() {
	add_menu_page(
		'Traffic Jammer', // page title.
		'Traffic Jammer', // menu title.
		'manage_options', // capability.
		'wp_traffic_jammer', // menu slug.
		'wp_traffic_jammer_options_page', // callback.
		'dashicons-privacy',
	);
}
add_action( 'admin_menu', 'wp_traffic_jammer_add_page' );

/**
 * Options Page
 *
 * @return void
 */
function wp_traffic_jammer_options_page() {
	require plugin_dir_path( __FILE__ ) . 'partials/options-page.php';
}

/**
 * Admin Initialize
 *
 * @return void
 */
function wp_traffic_jammer_admin_init() {

	add_settings_section(
		'wp_traffic_jammer_blocklist_section', // id.
		'', // title.
		null, // callback.
		'wp_traffic_jammer' // page.
	);

	add_settings_field(
		'wp_traffic_jammer_blocklist', // id.
		__( 'Block IP' ),  // title.
		'wp_traffic_jammer_blocklist', // callback display.
		'wp_traffic_jammer', // page.
		'wp_traffic_jammer_blocklist_section' // section.
	);

	add_settings_section(
		'wp_traffic_jammer_user_agent_section', // id.
		__( 'Block User Agent' ), // title.
		null, // callback.
		'wp_traffic_jammer', // page.
	);

	add_settings_field(
		'wp_traffic_jammer_user_agents', // id.
		__( 'User Agent Blocklist' ),    // title.
		'wp_traffic_jammer_user_agents', // callback display.
		'wp_traffic_jammer',             // page.
		'wp_traffic_jammer_user_agent_section', // section.
	);

	add_settings_section(
		'wp_traffic_jammer_whitelist_section', // id.
		__( 'Allow IP' ), // title.
		null, // callback.
		'wp_traffic_jammer', // page.
	);

	add_settings_field(
		'wp_traffic_jammer_whitelist', // id.
		__( 'Whitelist IP' ),   // title.
		'wp_traffic_jammer_whitelist', // callback display.
		'wp_traffic_jammer', // page.
		'wp_traffic_jammer_whitelist_section', // section.
	);

	register_setting(
		'wp_traffic_jammer', // option group.
		'wp_traffic_jammer_blocklist',  // option name.
	);

	register_setting(
		'wp_traffic_jammer', // option group.
		'wp_traffic_jammer_user_agents', // option name.
	);

	register_setting(
		'wp_traffic_jammer', // option group.
		'wp_traffic_jammer_whitelist', // option name.
	);

}
add_action( 'admin_init', 'wp_traffic_jammer_admin_init' );

/**
 * Blocklist Field
 */
function wp_traffic_jammer_blocklist() {
	$blocklist = get_option( 'wp_traffic_jammer_blocklist' );
	echo "<textarea rows='12' name='wp_traffic_jammer_blocklist' class='regular-text'>" . esc_html( $blocklist ) . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)</small>';
}

/**
 * User Agent Field
 *
 * @return void
 */
function wp_traffic_jammer_user_agents() {
	$user_agents = get_option( 'wp_traffic_jammer_user_agents' );
	echo "<textarea rows='12' name='wp_traffic_jammer_user_agents' class='regular-text'>" . esc_html( $user_agents ) . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)</small>';
}

/**
 * Whitelist Field
 *
 * @return void
 */
function wp_traffic_jammer_whitelist() {
	$whitelist = get_option( 'wp_traffic_jammer_whitelist' );
	echo "<textarea rows='12' name='wp_traffic_jammer_whitelist' class='regular-text'>" . esc_html( $whitelist ) . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)</small>';
}

/**
 * Santize Server Variables
 *
 * @param string $server variables.
 * @return mixed sanitized output
 */
function sanitize_server_var( $server ) {
	if ( is_string( $server ) ) {
		return stripslashes( $server );
	}
}

/**
 * Block IP
 *
 * @param string $ip value ot add.
 *
 * @return void
 */
function wp_traffic_jammer_block_ip( $ip ) {
	$blocklist = get_option( 'wp_traffic_jammer_blocklist' );
	if ( ! empty( $blocklist )) {
		$ips = array_map( 'trim', explode( ',', $blocklist ) );
	} else {
		$ips = array();
	}
	// convert to array to traverse.
	array_push( $ips, $ip );
	if ( count( $ips ) > 1 ) {
		$blocklist = implode( ",\n", $ips );
	} else {
		$blocklist = $ip;
	}
	update_option( 'wp_traffic_jammer_blocklist', $blocklist );
}

/**
 * Unblock IP
 *
 * @param string $ip value to remove.
 *
 * @return void
 */
function wp_traffic_jammer_unblock_ip( $ip ) {
	$blocklist = get_option( 'wp_traffic_jammer_blocklist' );
	// convert to array.
	$ips = array_map( 'trim', explode( ',', $blocklist ) );
	$len = count( $ips );
	$idx = array_search( $ip, $ips );
	array_splice( $ips, $idx, 1 );
	$blocklist = implode( ",\n", $ips );
	update_option( 'wp_traffic_jammer_blocklist', $blocklist );
}

/**
 * Trust IP
 *
 * @param string $ip value ot add.
 *
 * @return void
 */
function wp_traffic_jammer_trust_ip( $ip ) {
	$whitelist = get_option( 'wp_traffic_jammer_whitelist' );
	if ( ! empty( $whitelist )) {
		$ips = array_map( 'trim', explode( ',', $whitelist ) );
	} else {
		$ips = array();
	}
	// convert to array to traverse.
	array_push( $ips, $ip );
	if ( count( $ips ) > 1 ) {
		$whitelist = implode( ",\n", $ips );
	} else {
		$whitelist = $ip;
	}
	update_option( 'wp_traffic_jammer_whitelist', $whitelist );
}

/**
 * Untrust IP
 *
 * @param string $ip value to remove.
 *
 * @return void
 */
function wp_traffic_jammer_untrust_ip( $ip ) {
	$whitelist = get_option( 'wp_traffic_jammer_whitelist' );
	// convert to array.
	$ips = array_map( 'trim', explode( ',', $whitelist ) );
	$len = count( $ips );
	$idx = array_search( $ip, $ips );
	array_splice( $ips, $idx, 1 );
	$whitelist = implode( ",\n", $ips );
	update_option( 'wp_traffic_jammer_whitelist', $whitelist );
}
/**
 * Trust all access to wp-login.php
 *
 * @return void
 */
function wp_traffic_jammer_trust_all() {
	update_option( 'wp_traffic_jammer_whitelist', '' );
}

/**
 * Check IP
 *
 * @param string $ip single IP.
 * @param array  $ip_haystack list of IP.
 * @return bool true/false when an IP is found.
 */
function wp_traffic_jammer_check_ip( $ip, $ip_haystack ) {
	$ip_found = false;
	$ip_found = in_array( $ip, $ip_haystack, true );

	if ( ! $ip_found ) {
		// Check if this IP is in CIDR white list.
		foreach ( $ip_haystack as $_cidr ) {
			if ( strpos( $_cidr, '/' ) !== false ) {
				$_ip = ip2long( $ip );
				// expand the range of ips.
				list ( $_net, $_mask ) = explode( '/', $_cidr, 2 );
				// subnet.
				$_ip_net  = ip2long( $_net );
				$_ip_mask = ~( ( 1 << ( 32 - $_mask ) ) - 1 );
				$ip_found = ( $_ip & $_ip_mask );
				if ( $ip_found == ( $_ip_net & $_ip_mask ) ) {
					break;
				}
			}
		}
	}

	return $ip_found;
}

// include wp-cli file.
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-traffic-jammer-cli.php';
