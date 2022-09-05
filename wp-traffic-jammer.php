<?php
/**
 * WP Traffic Jammer
 *
 * @package  WPTrafficJammer
 *
 * @wordpress-plguin
 * Plugin Name:         WP Traffic Jammer
 * Plugin URI:          https://github.com/slick2/plugins/wp-traffic-jammer
 * Description:         WordPress plugin to block IP and bots that causes
 *                      malicious traffic
 * Version:             0.3
 * Requires at least:   5.2
 * Requires PHP:        7.4
 * Author:              Carey Dayrit
 * Author URI:          http://careydayrit.com
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:          https://example.com/my-plugin/
 * Text Domain:         wp-traffic-jammer
 */

/** Sanitize server variables */
$wptj_server = array_map( 'stripslashes', $_SERVER );

/**
 * Limit IP
 *
 * @return void
 */
function wp_traffic_jammer_limit_ip() {
	global $wptj_server;
	$options = get_option( 'wp_traffic_jammer_options' );
	$ips     = array_map( 'trim', explode( ',', $options['ip_list'] ) );
	$ip      = $wptj_server['REMOTE_ADDR'];

	if ( in_array( $ip, $ips ) ) {
		header( 'HTTP/1.0 403 Forbidden' );
		exit();
	}
}
add_action( 'init', 'wp_traffic_jammer_limit_ip' );

/**
 * Limit User Agents
 *
 * @return void
 */
function wp_traffic_jammer_limit_user_agent() {
	global $wptj_server;
	$options     = get_option( 'wp_traffic_jammer_options' );
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
		<?php // screen_icon(); depreacted ?>
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
		__( 'IP blocklist' ),                 // title.
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
		'wp_traffic_jammer_user_agent',          // id.
		__( 'User Agent blocklist' ),                 // title.
		'wp_traffic_jammer_user_agent',          // callback display.
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
function wp_traffic_jammer_user_agent() {
	$options    = get_option( 'wp_traffic_jammer_options' );
	$user_agent = isset( $options['user_agent'] ) ? $options['user_agent'] : '';
	echo "<textarea rows='12' name='wp_traffic_jammer_options[user_agent]' class='regular-text'>" . $user_agent . '</textarea>';
	echo '<br/>';
	echo '<small>Separated by comma (,)';
}
