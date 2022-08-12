<?php
/*
 * Plugin Name:	Traffic Jammer
 * Plugin URI: https://github.com/careydayrit/wp-traffic-jammer
 * Description:	Blocking of malicious traffic that hog system resources
 * Version:		0.0.2
 * Author:			Carey Dayrit
 * Author URI:		http://careydayrit.com
 * */


// limit IP

add_action ( 'init', 'traffic_jammer_limit_ip');

function traffic_jammer_limit_ip(){
	$options = get_option('traffic_jammer_options');
	$ips = explode(",", $options['ip_list']);
	
	$ip = $_SERVER['REMOTE_ADDR'];
	if(in_array($ip, $ips)){
		header('HTTP/1.0 403 Forbidden');
        exit();
	}
}

add_action ( 'init', 'traffic_jammer_limit_user_agent');

function traffic_jammer_limit_user_agent(){
    $options = get_option('traffic_jammer_options');
	$user_agents = explode(",", $options['user_agents']);

    // TODO : This will hit hard on longer list
    foreach ($user_agents as $bot) {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== FALSE) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
    }
}


/** Admin Page */

add_action('admin_menu', 'traffic_jammer_add_page');

function traffic_jammer_add_page(){
    add_options_page(
        'Traffic Jammer', // page title
        'Traffic Jammer', // menu title
        'manage_options', // capability
        'traffic_jammer', // menu slug
        'traffic_jammer_options_page' // callback
    );
}

function traffic_jammer_options_page(){
    ?>

    <div class="wrap">
        <?php screen_icon();?>
		<h2>Traffic Jammer</h2>
        <form action="options.php" method="post" class="form-table">
			<?php settings_fields('traffic_jammer');?>
			<?php do_settings_sections('traffic_jammer'); ?>
            <p class="submit">                    
                <input name="Submit" type="submit" value="Save Changes" class='button-primary' />
            </p>
        </form>
    </div>
    <?php
}



add_action('admin_init', 'traffic_jammer_admin_init');

function traffic_jammer_admin_init(){    

    add_settings_section(
        'traffic_jammer_settings_ip',           // id
        __('Block IP'),                         // title
        //'traffic_jammer_settings_ip',           //callback
        null,
        'traffic_jammer'                        // page
    );

    add_settings_field(
        'traffic_jammer_block_ip_text',         // id
        __('User Agent list'),                          // title
        'traffic_jammer_block_ip_text',     // callback display
        'traffic_jammer',                               // page
        'traffic_jammer_settings_ip'            // section
    );


    add_settings_section(
        'traffic_jammer_settings_user_agent',   // id
        __('Block User Agent'),                 // title
        //'traffic_jammer_settings_user_agent',   // callback
        function(){},
        'traffic_jammer'                        // page
    );

    add_settings_field(
        'traffic_jammer_block_user_agent_text',         // id
        __('User Agent list'),                          // title
        'traffic_jammer_block_user_agent_text',     // callback display
        'traffic_jammer',                               // page
        'traffic_jammer_settings_user_agent'            // section
    );

    register_setting(
        'traffic_jammer',                    // option group
        'traffic_jammer_options',            // option name
        //'traffic_jammer_block_ip_text'    // callback
    );
}

function traffic_jammer_block_ip_text(){
    $options = get_option('traffic_jammer_options');
    $ip_list = $options['ip_list'];
    echo "<textarea rows='12' name='traffic_jammer_options[ip_list]' class='regular-text'>".$ip_list."</textarea>";
    echo "<br/>";
    echo "<small>Seperate by comma (,)";
}

function traffic_jammer_block_user_agent_text(){
    $options = get_option('traffic_jammer_options');
    $user_agent = $options['user_agent'];    
    echo "<textarea rows='12' name='traffic_jammer_options[user_agent]' class='regular-text'>".$user_agent."</textarea>";
    echo "<br/>";
    echo "<small>Seperate by comma (,)";
  
}

function traffic_jammer_settings_ip(){
    
}

function traffic_jammer_settings_user_agent(){

}
