<?php
/**
 * Partials Option Page
 *
 * @package TrafficJammer
 */

?>
<script>
jQuery(document).ready(function($) {
	$('#tabs').tabs();
})    
</script>

<div class="wrap">
	<h2 class="dashicons-before dashicons-privacy">Traffic Jammer</h2>
	<p><?php esc_html_e( 'Traffic Jammer offers ability to block IP and crawlers that hog system resources.' ); ?></p>
	<div id="tabs">
		<ul class="wp-clearfix">
			<li><a href="#blocklist" class="nav-tab">Block IP</a></li>
			<li><a href="#useragents" class="nav-tab">Block Bots</a></li>
			<li><a href="#whitelist" class="nav-tab" >Allow IP</a></li>
		</ul>	
		<form action="options.php" method="post" class="form-table">	
		<div id="blocklist">
			<?php settings_fields( 'wp_traffic_jammer_blocklist' ); ?>
			<?php do_settings_sections( 'wp_traffic_jammer_blocklist' ); ?>			
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
		</div>
		<div id="useragents">
			<?php settings_fields( 'wp_traffic_jammer_user_agents' ); ?>
			<?php do_settings_sections( 'wp_traffic_jammer_user_agents' ); ?>
		</div>
		<div id="whitelist">
			<?php settings_fields( 'wp_traffic_jammer_whitelist' ); ?>
			<?php do_settings_sections( 'wp_traffic_jammer_whitelist' ); ?>
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
		</div>
		<p class="submit">                    
			<input name="Submit" type="submit" value="Save Changes" class='button-primary' />
		</p>
		</form>
	</div>
	
		
</div>
