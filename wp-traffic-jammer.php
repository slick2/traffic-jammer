<?php
/**
 * WP Traffic Jammer
 *
 * @package  WPTrafficJammer
 *
 * @wordpress-plguin
 * Plugin Name:         WP Traffic Jammer
 * Plugin URI:          https://github.com/slick2/wp-traffic-jammer
 * Description:         WordPress plugin to block IP and bots that causes
 *                      malicious traffic
 * Version:             0.6
 * Requires at least:   5.2
 * Requires PHP:        7.4
 * Author:              Carey Dayrit
 * Author URI:          http://careydayrit.com
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:          https://careydayrit.com/plugins/wp-traffic-jammer
 * Text Domain:         wp-traffic-jammer
 */

/** Sanitize server variables */
$wptj_server = array_map( 'sanitize_server_var', $_SERVER );

/**
 * Activate plugin
 *
 * @return void
 */
function wp_traffic_jammer_activate() {
	// define the bad bots.
	$bad_bots = 'DotBot, Applebot, applebot, GnowitNewsbot, InfoTigerBot, digitalshadowsbot, SeznamBo, YandexBot, badbot';
	// Get the options.
	$options = get_option( 'wp_traffic_jammer_options' );
	if ( is_array( $options ) ) {
		return;
	} else {
		$options = array(
			'user_agents' => $bad_bots,
			'ip_list'     => '',
			'settings'    => '',
		);
		add_option( 'wp_traffic_jammer_options', $options );
		return;
	}
}
register_activation_hook( __FILE__, 'wp_traffic_jammer_activate' );

/**
 * Limit IP
 *
 * @return void
 */
function wp_traffic_jammer_limit_ip() {
	global $wptj_server;
	$options = get_option( 'wp_traffic_jammer_options' );

	if ( ! isset( $options['ip_list'] ) ) {
		return;
	}
	$ips = array_map( 'trim', explode( ',', $options['ip_list'] ) );
	$ip  = $wptj_server['REMOTE_ADDR'];

	/**  Check if this IP is in blocklist. */
	$ip_forbidden = wp_traffic_jammer_check_ip( $ip, $ips );

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

	$ip_whitelist = array(
		'192.0.2.38',
		'192.0.3.125',
		'192.0.67.0/30',
		'192.0.78.0/24',
		'192.168.0.2',
	);

	$ip = $wptj_server['REMOTE_ADDR'];
	// Check if this IP is in blocklist.
	$ip_allow = wp_traffic_jammer_check_ip( $ip, $ip_whitelist );

	if ( preg_match( '/(wp-login.php)/', $wptj_server['REQUEST_URI'] ) ) {
		if ( ! $ip_allow ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit;
		}
	}
}
/**
 * Limit User Agents
 *
 * @return void
 */
function wp_traffic_jammer_limit_user_agent() {
	global $wptj_server;
	$options = get_option( 'wp_traffic_jammer_options' );

	if ( ! isset( $options['user_agents'] ) ) {
		return;
	}

	$user_agents = explode( ',', $options['user_agents']);

	// TODO : This will hit hard on longer list.
	foreach ( $user_agents as $bot ) {
		if ( stripos( $wptj_server['HTTP_USER_AGENT'], $bot ) !== false ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit();
		}
	}
}
add_action( 'init', 'wp_traffic_jammer_limit_user_agent' );

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
		'wp_traffic_jammer_options_page' // callback.
	);
}
add_action( 'admin_menu', 'wp_traffic_jammer_add_page' );

/**
 * Options Page
 *
 * @return void
 */
function wp_traffic_jammer_options_page() {
	?>
	<div class="wrap">
		<h2>Traffic Jammer</h2>
		<form action="options.php" method="post" class="form-table">
			<?php settings_fields( 'wp_traffic_jammer' ); ?>
			<?php do_settings_sections( 'wp_traffic_jammer' ); ?>
			<p class="submit">                    
				<input name="Submit" type="submit" value="Save Changes" class='button-primary' />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Admin Initialize
 *
 * @return void
 */
function wp_traffic_jammer_admin_init() {

	add_settings_section(
		'wp_traffic_jammer_ip_section',   // id.
		__( 'Block IP' ),                 // title.
		/** 'traffic_jammer_settings_ip', //callback */
		null,
		'wp_traffic_jammer'               // page.
	);

	add_settings_field(
		'wp_traffic_jammer_ip',          // id.
		__( 'IP blocklist' ),            // title.
		'wp_traffic_jammer_ip',          // callback display.
		'wp_traffic_jammer',             // page.
		'wp_traffic_jammer_ip_section'   // section.
	);

	add_settings_section(
		'wp_traffic_jammer_user_agent_section',   // id.
		__( 'Block User Agent' ),                 // title.
		/** 'traffic_jammer_settings_user_agent', // callback */
		null,
		'wp_traffic_jammer'                       // page.
	);

	add_settings_field(
		'wp_traffic_jammer_user_agents',          // id.
		__( 'User Agent blocklist' ),                 // title.
		'wp_traffic_jammer_user_agents',          // callback display.
		'wp_traffic_jammer',                     // page.
		'wp_traffic_jammer_user_agent_section'   // section.
	);

	register_setting(
		'wp_traffic_jammer',                    // option group.
		'wp_traffic_jammer_options',            // option name.
		/** 'traffic_jammer_block_ip_text'   // callback */
	);
}
add_action( 'admin_init', 'wp_traffic_jammer_admin_init' );

/**
 * Field
 */
function wp_traffic_jammer_ip() {
	$options = get_option( 'wp_traffic_jammer_options' );
	$ip_list = isset( $options['ip_list'] ) ? $options['ip_list'] : '';
	echo "<textarea rows='12' name='wp_traffic_jammer_options[ip_list]' class='regular-text'>" . esc_html( $ip_list ) . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)';
}

/**
 * User Agent Text Area
 *
 * @return void
 */
function wp_traffic_jammer_user_agents() {
	$options    = get_option( 'wp_traffic_jammer_options' );
	$user_agents = isset( $options['user_agents'] ) ? $options['user_agents'] : '';
	echo "<textarea rows='12' name='wp_traffic_jammer_options[user_agents]' class='regular-text'>" . esc_html( $user_agents ) . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)';
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
 * Add IP
 *
 * @param string $ip value ot add.
 *
 * @return void
 */
function wp_traffic_jammer_add_ip( $ip ) {
	$options     = get_option( 'wp_traffic_jammer_options' );
	$ip_list     = isset( $options['ip_list'] ) ? $options['ip_list'] : '';
	$user_agents = $options['user_agents'];

	$ips = array_map( 'trim', explode( ',', $options['ip_list'] ) );
	array_push( $ips, $ip );
	$ip_list = implode( ",\n", $ips );
	// reconstruct the array option.
	$options = array(
		'ip_list'     => $ip_list,
		'user_agents' => $user_agents,
	);

	update_option( 'wp_traffic_jammer_options', $options );
}

/**
 * Remove IP
 *
 * @param string $ip value to remove.
 *
 * @return void
 */
function wp_traffic_jammer_remove_ip( $ip ) {
	$options = get_option( 'wp_traffic_jammer_options' );
	if ( $options['ip_list'] == '' ) {
		return;
	}

	$ip_list     = isset( $options['ip_list'] ) ? $options['ip_list'] : '';
	$user_agents = $options['user_agents'];

	$ips = array_map( 'trim', explode( ',', $options['ip_list'] ) );
	$len = count( $ips );
	$idx = array_search( $ip, $ips );
	array_splice( $ips, $idx, 1 );

	$ip_list = implode( ",\n", $ips );
	// reconstruct the array option.
	$options = array(
		'ip_list'     => $ip_list,
		'user_agents' => $user_agents,
	);

	update_option( 'wp_traffic_jammer_options', $options );

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
require plugin_dir_path( __FILE__ ) . 'wp-traffic-jammer-cli.php';
