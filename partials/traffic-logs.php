<?php
/**
 * Partials Traffic Logs
 *
 * @package TrafficJammer
 */

$cef6d44b_tab = '';
if ( ! empty( $_GET['tab'] ) ) {
	$cef6d44b_tab = wp_unslash( $_GET['tab'] );
} else {
	$cef6d44b_tab = 'ip';
}

?>
<div class="wrap">
	<h2 class="dashicons-before dashicons-privacy">Traffic Jammer - Reports</h2>
	<nav class="nav-tab-wrapper wp-clearfix" aria-label="Traffic Jammer Tabs">
		<a href="?page=trafficjammer_traffic_logs&tab=ip" class="nav-tab <?php echo $cef6d44b_tab == 'ip' ? 'nav-tab-active' : '';  ?>" >Top IP</a>
		<a href="?page=trafficjammer_traffic_logs&tab=useragent" class="nav-tab <?php echo $cef6d44b_tab == 'useragent' ? 'nav-tab-active' : '';  ?>">Top User Agents</a>
		<a href="?page=trafficjammer_traffic_logs&tab=recent" class="nav-tab <?php echo $cef6d44b_tab == 'recent' ? 'nav-tab-active' : ''; ?>">Recent Activity</a>
	</nav>
	<div class="tabs-content">
<?php
if ( 'ip' === $cef6d44b_tab ) {
	$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT count(*) as num_visits, IP FROM ' . $wpdb->prefix . 'trafficjammer_traffic where IP is not null GROUP BY IP ORDER BY num_visits DESC LIMIT 25' ) );

	?>
<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th scope="col">IP</th>
				<th scope="col">Count</th>
				<th scope="col">Check</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $traffic_logs as $value ) { ?>
			<tr>
				<td><?php echo esc_html( $value->IP ); ?></td>
				<td><?php echo esc_html( number_format( $value->num_visits, 0 ) ); ?></td>
				<td><a href="https://www.abuseipdb.com/check/<?php echo esc_html( $value->IP ); ?>" target="_blank" title="Go to abuseipdb.com"><span class="dashicons dashicons-welcome-view-site"></span></a></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col">IP</th>
				<th scope="col">Count</th>
				<th scope="col">Check</th>
			</tr>
		</tfoot>
	<?php
}
?>
<?php
if ( 'useragent' === $cef6d44b_tab ) {
	$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT count(*) as num_visits, user_agent FROM ' . $wpdb->prefix . 'trafficjammer_traffic where user_agent is not null GROUP BY user_agent ORDER BY num_visits DESC LIMIT 25' ) );

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
	$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'trafficjammer_traffic ORDER BY date DESC LIMIT 25' ) );
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
				<td><?php echo esc_html( $value->IP ); ?>
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