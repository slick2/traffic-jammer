<?php
/**
 * Partials Traffic Logs
 *
 * @package TrafficJammer
 */

$cef6d44b_tab = '';
if ( ! empty( $_GET['tab'] ) ) { //phpcs:ignore
	$cef6d44b_tab = wp_unslash( $_GET['tab'] ); //phpcs:ignore
} else {
	$cef6d44b_tab = 'ip';
}
$setting_options = get_option( 'wp_traffic_jammer_options' );
$blocklist       = get_option( 'wp_traffic_jammer_blocklist' );
$blocklist       = array_map( 'trim', explode( ',', $blocklist ) );
$interval_days   = isset( $setting_options['log_retention'] ) ? $setting_options['log_retention'] : 3;
$abuseipdb       = get_option( 'wp_traffic_jammer_abuseipdb' );
$abuse           = new Traffic_Jammer_AbuseIPDB();
?>
<div class="wrap">
	<h2 class="dashicons-before dashicons-privacy">Traffic Jammer - Reports</h2>
	<p>Logs retention is set to <?php echo esc_html( $interval_days ); ?> days</p>
	<nav class="nav-tab-wrapper wp-clearfix" aria-label="Traffic Jammer Tabs">
		<a href="?page=trafficjammer_traffic_logs&tab=ip" class="nav-tab <?php echo $cef6d44b_tab == 'ip' ? 'nav-tab-active' : ''; //phpcs:ignore  ?>" >Top IP</a>
		<a href="?page=trafficjammer_traffic_logs&tab=useragent" class="nav-tab <?php echo $cef6d44b_tab == 'useragent' ? 'nav-tab-active' : ''; //phpcs:ignore ?>">Top User Agents</a>
		<a href="?page=trafficjammer_traffic_logs&tab=recent" class="nav-tab <?php echo $cef6d44b_tab == 'recent' ? 'nav-tab-active' : ''; //phpcs:ignore?>">Recent Activity</a>
	</nav>
	<div class="tabs-content">
<?php
if ( 'ip' === $cef6d44b_tab ) {
	$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT count(*) as num_visits, IP FROM ' . $wpdb->prefix . 'trafficjammer_traffic where IP is not null GROUP BY IP ORDER BY num_visits DESC LIMIT 25' ) ); //phpcs:ignore

	?>
<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th scope="col">IP</th>
				<th scope="col">Count</th>
	<?php if ( isset( $abuseipdb['abuseipdb_key'] ) ) { ?>
				<th scope="col">Abuse Score</th>
	<?php } ?>
				<th scope="col">Check</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $traffic_logs as $value ) { ?>
			<tr>
				<td><?php echo esc_html( $value->IP ); //phpcs:ignore?></td>
				<td><?php echo esc_html( number_format( $value->num_visits, 0 ) ); ?></td>
				<?php if ( isset( $abuseipdb['abuseipdb_key'] ) ) { ?>
						<td>
						<?php
						if ( trafficjammer_check_ip( $value->IP, $blocklist ) ) { //phpcs:ignore	
							$abuse_result = $abuse->check( $value->IP ); //phpcs:ignore						
							echo esc_html( (int) $abuse_result['data']['abuseConfidenceScore'] );
							echo esc_html( '%' );
							echo esc_html( ' (blocked) ' );
						} else {
							$abuse_result = $abuse->check( $value->IP ); //phpcs:ignore
							echo esc_html( (int) $abuse_result['data']['abuseConfidenceScore'] );
							echo esc_html( '%' );
						}
						?>
						</td>
				<?php } ?>
				<td><a href="https://www.abuseipdb.com/check/<?php echo esc_html( $value->IP ); //phpcs:ignore ?>" target="_blank" title="Go to abuseipdb.com"><span class="dashicons dashicons-welcome-view-site"></span></a></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col">IP</th>
				<th scope="col">Count</th>
				<?php if ( isset( $abuseipdb['abuseipdb_key'] ) ) { ?>
				<th scope="col">Abuse Score</th>
				<?php } ?>
				<th scope="col">Check</th>
			</tr>
		</tfoot>
	<?php
}
?>
<?php
if ( 'useragent' === $cef6d44b_tab ) {
	$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT count(*) as num_visits, user_agent FROM ' . $wpdb->prefix . 'trafficjammer_traffic where user_agent is not null GROUP BY user_agent ORDER BY num_visits DESC LIMIT 25' ) ); //phpcs:ignore

	?>
<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th scope="col">User Agents</th>
				<th scope="col">Count</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $traffic_logs as $value ) { ?>
			<tr>
				<td><?php echo esc_html( $value->user_agent ); ?></td>
				<td><?php echo esc_html( number_format( $value->num_visits, 0 ) ); ?></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col">User Agents</th>
				<th scope="col">Count</th>
			</tr>
		</tfoot>		
	<?php
}
?>
<?php
if ( 'recent' === $cef6d44b_tab ) {
	$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'trafficjammer_traffic ORDER BY date DESC LIMIT 25' ) ); //phpcs:ignore
	?>
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th scope="col">IP</th>
				<th scope="col">User Agent</th>
				<th scope="col">Status</th>
				<th scope="col">Request</th>
				<th scope="col">Ref</th>
				<th scope="col">Date</th>
			</tr>
		</thead>

		<tbody>
		<?php
		foreach ( $traffic_logs as $value ) {
			?>
			<tr>
				<td><?php echo esc_html( $value->IP ); //phpcs:ignore ?>
				</td>
				<td><?php echo esc_html( $value->user_agent ); ?>				
				</td>
				<td><?php echo esc_html( $value->status ); ?>						
				</td>
				<td><?php echo esc_html( $value->request ); ?>			
				</td>
				<td><?php echo esc_html( $value->ref ); ?>				
				</td>
				<td><?php echo esc_html( $value->date ); ?>				
				</td>
			</tr>							
			<?php
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col">IP</th>
				<th scope="col">User Agent</th>
				<th scope="col">Status</th>
				<th scope="col">Request</th>
				<th scope="col">Ref</th>
				<th scope="col">Date</th>
			</tr>
		</tfoot>
	</table>
	<?php
}
?>
	</div>


</div>
