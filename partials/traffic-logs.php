<?php
/**
 * Partials Traffic Logs
 *
 * @package TrafficJammer
 */

$traffic_logs = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'trafficjammer_traffic ORDER BY date DESC LIMIT 25' ) );
?>
<div class="wrap">
	<h2 class="dashicons-before dashicons-privacy">Traffic Jammer - Activity</h2>

	<h3> Recent Activity </h3>
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
</div>
