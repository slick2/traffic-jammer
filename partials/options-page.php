<?php
/**
 * Partials Option Page
 *
 * @package TrafficJammer
 */

?>
<script>
(function($) {
	$(function(){
		$('#tabs').tabs();
	});
})(jQuery);
</script>

<div class="wrap">
	<h2 class="dashicons-before dashicons-privacy">Traffic Jammer</h2>
	<p><?php esc_html_e( 'Traffic Jammer offers ability to block IP and crawlers that hog system resources.' ); ?></p>
		<div id="tabs" class="nav-tab-wrapper">
			<ul>
				<li><a href="#blocklist" class="nav-tab" >Block IP</a></li>
				<li><a href="#useragents" class="nav-tab" >Block Bots</a></li>
				<li><a href="#whitelist" class="nav-tab"  >Allow IP</a></li>
			</ul>
			<div class="tabs-content">
			<div id="blocklist">
				<?php settings_fields( 'wp_traffic_jammer_blocklist' ); ?>
				<?php do_settings_sections( 'wp_traffic_jammer_blocklist' ); ?>
			</div>
			<div id="useragent">

			</div>
			<div id="whitelist">
			
			</div>
			</div>
		</div>
	<form action="options.php" method="post" class="form-table">
		
		<p class="submit">                    
			<input name="Submit" type="submit" value="Save Changes" class='button-primary' />
		</p>
	</form
</div>
