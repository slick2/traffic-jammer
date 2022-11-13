<?php
/**
 * Partials Option Page
 *
 * @package TrafficJammer
 */

$cef6d44b_tab = '';
if ( ! empty( $_GET['tab'] ) ) {
	$cef6d44b_tab = wp_unslash( $_GET['tab'] );
} else {
	$cef6d44b_tab = 'blockip';
}
?>
<div class="wrap">
	<h2 class="dashicons-before dashicons-privacy">Traffic Jammer</h2>
	<p><?php esc_html_e( 'Traffic Jammer offers ability to block IP and crawlers that hog system resources.' ); ?></p>
	<nav class="nav-tab-wrapper wp-clearfix" aria-label="Traffic Jammer Tabs">
		<a href="?page=wp_traffic_jammer&tab=blockip" class="nav-tab <?php echo $cef6d44b_tab == 'blockip' ? 'nav-tab-active' : '';  ?>" >Block IP</a>
		<a href="?page=wp_traffic_jammer&tab=blockbot" class="nav-tab <?php echo $cef6d44b_tab == 'blockbot' ? 'nav-tab-active' : '';  ?>">Block Bots</a>
		<a href="?page=wp_traffic_jammer&tab=allowip" class="nav-tab <?php echo $cef6d44b_tab == 'allowip' ? 'nav-tab-active' : ''; ?>">Whitelist IP</a>
		<a href="?page=wp_traffic_jammer&tab=settings" class="nav-tab <?php echo $cef6d44b_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
	</nav>
	<div class="tabs-content">

	<?php
	if ( 'blockbot' === $cef6d44b_tab ) {
		?>
		<form action="options.php" method="post" class="form-table">	
			<?php settings_fields( 'wp_traffic_jammer_user_agents' ); ?>
		<table>
			<?php do_settings_fields( 'wp_traffic_jammer', 'wp_traffic_jammer_user_agent_section' ); ?>	
		</table>
		<p class="submit">                    
			<input name="Submit" type="submit" value="Save Changes" class='button-primary' />
		</p>
		</form>
		<?php
	}
	?>
	<?php
	if ( 'allowip' === $cef6d44b_tab ) {
		?>
			<div class="card">
			This is a list to limit login on the listed IPs.  Leave it blank to allow all.
			</div>
			<form action="options.php" method="post" class="form-table">	
			<?php settings_fields( 'wp_traffic_jammer_whitelist' ); ?>
			<table>
				<?php do_settings_fields( 'wp_traffic_jammer', 'wp_traffic_jammer_whitelist_section' ); ?>	
			</table>
			<p class="submit">                    
			<input name="Submit" type="submit" value="Save Changes" class='button-primary' />
			</p>
			</form>
		<?php
	}
	?>
	<?php
	if ( 'blockip' === $cef6d44b_tab ) {
		?>
			<form action="options.php" method="post" class="form-table">	
			<?php settings_fields( 'wp_traffic_jammer_blocklist' ); ?>
			<table>
				<?php do_settings_fields( 'wp_traffic_jammer', 'wp_traffic_jammer_blocklist_section' ); ?>			
			</table>
			<p class="submit">                    
			<input name="Submit" type="submit" value="Save Changes" class='button-primary' />
			</p>
			</form>
		<?php
	}
	?>
	<?php
	if ( 'settings' === $cef6d44b_tab ) {
		?>
		<form action="options.php" method="post" class="form-table">	
		<?php settings_fields( 'wp_traffic_jammer_options' ); ?>
			<table>
				<?php do_settings_fields( 'wp_traffic_jammer', 'trafficjammer_settings_section' ); ?>			
			</table>
		<p class="submit">                    
		<input name="Submit" type="submit" value="Save Changes" class='button-primary' />
		</p>
		</form>
		<?php
	}
	?>
	</div>
	<?php
	if ( $cef6d44b_tab === 'blockip' || $cef6d44b_tab === 'allowip' ) {
		?>
	<p>
		<b><span class="dashicons-before dashicons-star-filled"></span> Your IP: <?php echo esc_html( $cef6d44b_server['REMOTE_ADDR']); ?></b>
	</p>
	<table class="striped fixed" cellspacing="0">
				<thead>
				<tr>
					<td colspan="3">When adding items, use the formats listed below.</td>
				</tr>
				<thead>
				<tbody>
				<tr>
					<td>IPv4</td>
					<td>IPv4 Address</td>
					<td>192.168.1.1</td>
				</tr>
				<tr>
					<td>IPv4</td>
					<td>CIDR range</td>
					<td>192.168.1.0/24</td>
				</tr>
				<tr>
					<td>IPv6</td>
					<td>IPv6 Address</td>
					<td>2001:4450:49b6:9900:6498:6f80:4b15:240a</td>
				</tr>
				</tbody>
			</table>
		<?php
	}
	?>
</div>
