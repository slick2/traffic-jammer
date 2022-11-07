<?php
/**
 * Traffic Jammer
 *
 * @package  TrafficJammer
 *
 * @wordpress-plguin
 * Plugin Name:        Traffic Jammer
 * Plugin URI:          https://wordpress.org/plugins/traffic-jammer/
 * Description:         WordPress plugin to block IP and bots that causes malicious traffic.
 * Version:             1.0.2
 * Requires at least:   5.2
 * Requires PHP:        7.4
 * Author:              Carey Dayrit
 * Author URI:          http://careydayrit.com
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         traffic-jammer
 */

/** Sanitize server variables */
$cef6d44b_server = array_map( 'trafficjammer_server_var', $_SERVER );

/**
 * Activate plugin
 *
 * @return void
 */
function trafficjammer_activate() {
	global $wpdb;
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

	$table_name = $wpdb->prefix . 'trafficjammer_traffic';
	$collate_charset = $wpdb->get_charset_collate();
	// Define the table for traffic logs.
	$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`IP` varchar(45) DEFAULT NULL,
		`user_agent` varchar(255) DEFAULT NULL,
		`status` varchar(45) DEFAULT NULL,
		`request` varchar(255) DEFAULT NULL,
		`ref` varchar(255) DEFAULT NULL,
		`date` datetime DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		UNIQUE KEY `id_UNIQUE` (`id`)
	  ) ENGINE=InnoDB $collate_charset;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql );

}
register_activation_hook( __FILE__, 'trafficjammer_activate' );

/**
 * Deactivate plugin
 *
 * @return void
 */
function trafficjammer_deactivate() {
	global $wpdb;
	/**
	delete_option( 'wp_traffic_jammer_options' );
	delete_option( 'wp_traffic_jammer_blocklist' );
	delete_option( 'wp_traffic_jammer_whitelist' );
	delete_option( 'wp_traffic_jammer_user_agents' );
	*/
	// table name.
	$table_name = $wpdb->prefix . 'trafficjammer_traffic';
	// cleanup.
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $table_name );

}
register_deactivation_hook( __FILE__, 'trafficjammer_deactivate' );


/**
 * Log live traffic
 *
 * @return void
 */
function trafficjammer_traffic_live() {
	global $wpdb, $cef6d44b_server;

	$wpdb->insert(
		$wpdb->prefix . 'trafficjammer_traffic',
		array(
			'IP'         => $cef6d44b_server['REMOTE_ADDR'],
			'user_agent' => $cef6d44b_server['HTTP_USER_AGENT'],
			'status'     => http_response_code(),
			'request'    => $cef6d44b_server['REQUEST_URI'],
			'ref'        => $cef6d44b_server['HTTP_REFERER'],
		)
	);
}
add_action( 'init', 'trafficjammer_traffic_live' );


/**
 * Limit IP
 *
 * @return void
 */
function trafficjammer_limit_ip() {
	global $cef6d44b_server;
	$blocklist = get_option( 'wp_traffic_jammer_blocklist' );

	if ( ! isset( $blocklist ) ) {
		return;
	}

	$ip        = $cef6d44b_server['REMOTE_ADDR'];
	$blocklist = array_map( 'trim', explode( ',', $blocklist ) );

	/**  Check if this IP is in blocklist. */
	$ip_forbidden = trafficjammer_check_ip( $ip, $blocklist );

	if ( $ip_forbidden ) {
		header( 'HTTP/1.0 403 Forbidden' );
		exit;
	}
}
add_action( 'init', 'trafficjammer_limit_ip' );

/**
 * Whitelist
 */
function trafficjammer_whitelist_ip() {
	global $cef6d44b_server;
	$whitelist = get_option( 'wp_traffic_jammer_whitelist' );

	if ( empty( $whitelist ) ) {
		return;
	}

	$whitelist = array_map( 'trim', explode( ',', $whitelist ) );

	$ip = $cef6d44b_server['REMOTE_ADDR'];
	// Check if this IP is in whitelistlist.
	$ip_allow = trafficjammer_check_ip( $ip, $whitelist );

	if ( preg_match( '/(wp-login.php)/', $cef6d44b_server['REQUEST_URI'] ) ) {
		if ( ! $ip_allow ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit;
		}
	}
}
add_action( 'init', 'trafficjammer_whitelist_ip' );
/**
 * Limit User Agents
 *
 * @return void
 */
function trafficjammer_limit_user_agents() {
	global $cef6d44b_server;
	$user_agents = get_option( 'wp_traffic_jammer_user_agents' );

	if ( ! isset( $user_agents ) ) {
		return;
	}

	$user_agents = explode( ',', $user_agents );

	// TODO : This will hit hard on longer list.
	foreach ( $user_agents as $bot ) {
		if ( stripos( $cef6d44b_server['HTTP_USER_AGENT'], $bot ) !== false ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit();
		}
	}
}
add_action( 'init', 'trafficjammer_limit_user_agents' );

/**
 *
 * Add menu page
 *
 * @return void
 */
function trafficjammer_add_page() {
	add_menu_page(
		'Traffic Jammer', // page title.
		'Traffic Jammer', // menu title.
		'manage_options', // capability.
		'wp_traffic_jammer', // menu slug.
		'trafficjammer_options_page', // callback.
		'dashicons-privacy'
	);
	add_submenu_page(
		'wp_traffic_jammer',
		'Dashboard',
		'Dashboard',
		'manage_options',
		'wp_traffic_jammer',
		'trafficjammer_options_page', // callback.
	);
	add_submenu_page(
		'wp_traffic_jammer',
		'Traffic Activity',
		'Reports',
		'manage_options',
		'trafficjammer_traffic_logs',
		'trafficjammer_traffic_logs_submenu'
	);
}
add_action( 'admin_menu', 'trafficjammer_add_page' );

/**
 * Options Page
 *
 * @return void
 */
function trafficjammer_options_page() {
	global $cef6d44b_server, $wpdb;
	require plugin_dir_path( __FILE__ ) . 'partials/options-page.php';
}

/**
 * Traffic Logs - Sub Menu Page
 *
 * @return void
 */
function trafficjammer_traffic_logs_submenu() {
	global $cef6d44b_server, $wpdb;
	require plugin_dir_path( __FILE__ ) . 'partials/traffic-logs.php';
}


/**
 * Admin Initialize
 *
 * @return void
 */
function trafficjammer_admin_init() {

	add_settings_section(
		'wp_traffic_jammer_blocklist_section', // id.
		'', // title.
		null, // callback.
		'wp_traffic_jammer' // page.
	);

	add_settings_field(
		'wp_traffic_jammer_blocklist',          // id.
		__( 'IP blocklist' ),            // title.
		'trafficjammer_blocklist',          // callback display.
		'wp_traffic_jammer',             // page.
		'wp_traffic_jammer_blocklist_section'   // section.
	);

	add_settings_section(
		'wp_traffic_jammer_user_agent_section', // id.
		__( 'Block User Agent' ), // title.
		null, // callback.
		'wp_traffic_jammer', // page.
	);

	add_settings_field(
		'wp_traffic_jammer_user_agents',          // id.
		__( 'Block Bots' ),                 // title.
		'trafficjammer_user_agents',          // callback display.
		'wp_traffic_jammer',                     // page.
		'wp_traffic_jammer_user_agent_section'   // section.
	);

	add_settings_section(
		'wp_traffic_jammer_whitelist_section', // id.
		__( 'Allow IP' ), // title.
		null, // callback.
		'wp_traffic_jammer', // page.
	);

	add_settings_field(
		'wp_traffic_jammer_whitelist',          // id.
		__( 'Limit access to wp-login.php' ),            // title.
		'trafficjammer_whitelist',          // callback display.
		'wp_traffic_jammer',             // page.
		'wp_traffic_jammer_whitelist_section'   // section.
	);

	register_setting(
		'wp_traffic_jammer_blocklist', // option group.
		'wp_traffic_jammer_blocklist',  // option name.
	);

	register_setting(
		'wp_traffic_jammer_user_agents', // option group.
		'wp_traffic_jammer_user_agents', // option name.
	);

	register_setting(
		'wp_traffic_jammer_whitelist', // option group.
		'wp_traffic_jammer_whitelist', // option name.
	);

	wp_enqueue_script( 'jquery-ui-tabs' );

}
add_action( 'admin_init', 'trafficjammer_admin_init' );

/**
 * Blocklist Field
 */
function trafficjammer_blocklist() {
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
function trafficjammer_user_agents() {
	$user_agents = get_option( 'wp_traffic_jammer_user_agents' );
	echo "<textarea rows='12' name='wp_traffic_jammer_user_agents' class='regular-text'>" . esc_textarea( $user_agents ) . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)</small>';
}

/**
 * Whitelist Field
 *
 * @return void
 */
function trafficjammer_whitelist() {
	$whitelist = get_option( 'wp_traffic_jammer_whitelist' );
	echo "<textarea rows='12' name='wp_traffic_jammer_whitelist' class='regular-text'>" . esc_textarea( $whitelist ) . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)</small>';
}

/**
 * Santize Server Variables
 *
 * @param string $server variables.
 * @return mixed sanitized output
 */
function trafficjammer_server_var( $server ) {
	if ( is_string( $server ) ) {
		return sanitize_text_field( $server );
	}
}

/**
 * Block IP
 *
 * @param string $ip value ot add.
 *
 * @return void
 */
function trafficjammer_block_ip( $ip ) {
	$blocklist = get_option( 'wp_traffic_jammer_blocklist' );
	if ( ! empty( $blocklist )) {
		$ips = array_map( 'trim', explode( ',', $blocklist ) );
	} else {
		$ips = array();
	}
	// convert to array to traverse.
	array_push( $ips, sanitize_text_field( $ip ) );
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
function trafficjammer_unblock_ip( $ip ) {
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
function trafficjammer_trust_ip( $ip ) {
	$whitelist = get_option( 'wp_traffic_jammer_whitelist' );
	if ( ! empty( $whitelist )) {
		$ips = array_map( 'trim', explode( ',', $whitelist ) );
	} else {
		$ips = array();
	}
	// convert to array to traverse.
	array_push( $ips, sanitize_text_field( $ip ) );
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
function trafficjammer_untrust_ip( $ip ) {
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
function trafficjammer_trust_all() {
	update_option( 'wp_traffic_jammer_whitelist', '' );
}

/**
 * Check IP
 *
 * @param string $ip single IP.
 * @param array  $ip_haystack list of IP.
 * @return bool true/false when an IP is found.
 */
function trafficjammer_check_ip( $ip, $ip_haystack ) {
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
				if ( ( $_ip & $_ip_mask ) === ( $_ip_net & $_ip_mask ) ) {
					$ip_found = true;
					break;
				}
			}
		}
	}

	return $ip_found;
}

// include wp-cli file.
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-traffic-jammer-cli.php';
