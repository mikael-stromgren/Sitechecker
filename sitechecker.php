<?php 

/**
Plugin Name: Sitechecker
Plugin URI: 
Description: Sitechecker lets you check the online availablity of any website(s) of your choice.
Version: 1.0
Author: Mikael Strömgren
Author URI:
License: GPLv2 or later
Text Domain: 
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action('admin_menu', 'sc_menu');
add_action('admin_enqueue_scripts', 'sc_inline_script', 1, 1);
add_action('admin_post_submit-form', 'sc_handle_form_action');

register_activation_hook( __FILE__, 'sc_plugin_activation');
register_deactivation_hook( __FILE__, 'sc_plugin_deactivation');

$interval = get_option('sitechecker_interval');
$sites = get_option('sitechecker_sites');

$sc_main = 'sc_main';
$sc_settings = 'sc_settings';

$okHttpCodes = array(200, 201, 202, 203, 204, 205, 206, 207, 208, 226, 300, 301, 302, 303, 304, 305, 307, 308);




function check_status($url) {
	global $okHttpCodes;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	$online = in_array($httpCode, $okHttpCodes);
	return array($online, $httpCode);
}

function sc_inline_script() {
	global $interval;
    $interval_ms = 1000 * (int)$interval;
    ?>
    <script type="text/javascript">
    	window.setInterval(function(){
		 jQuery('#sitechecker_table').load(document.URL +  ' #sitechecker_table');
		}, <?php echo $interval_ms; ?>);
    </script>
    <?php
}

function sc_handle_form_action() {
	global $sites, $sc_settings;
	if (isset($_POST['interval']) && is_numeric($_POST['interval'])) {
		update_option('sitechecker_interval', $_POST['interval']);
	}
	if (isset($_POST['url']) && !empty($_POST['url'])) {
		$site = $_POST['protocol'] . '://' . $_POST['url'];
		array_push($sites, $site);
		update_option('sitechecker_sites', $sites);
	
	}
	if (isset($_POST['delete-site']) && is_numeric($_POST['delete-site'])) {
		unset($sites[$_POST['delete-site']]);
		update_option('sitechecker_sites', $sites);
	}
	wp_redirect('/wp-admin/admin.php?page=' . $sc_settings);
}



function sc_menu() {
	global $sc_main, $sc_settings;
	add_menu_page('Sitechecker', 'Sitechecker', 'edit_plugins', $sc_main, 'sc_main_render', '', 7); 
	add_submenu_page($sc_main, 'Sitechecker settings', 'Settings', 'edit_plugins', $sc_settings, 'sc_settings_render');
}

function sc_main_render() {
    global $sites;
    ?>
    <h2>Sitechecker</h2>
    
    <div id="sitechecker_table">
    	<p>Last check performed: <strong><?php echo date('Y-m-d H:i:s'); ?></strong></p>
        <table class="wp-list-table widefat fixed striped posts">
        	<thead>
        		<tr>
        			<th>URL</th>
        			<th>Online</th>
        			<th>HTTP Status</th>
        		</tr>
        	</thead>
        	<tbody>
		        <?php
		        foreach ($sites as $site) {
		        	$status = check_status($site);
		        	$online = $status[0] ? '<span style="color:#0C0;">Online</span>' : '<span style="color:#C00;">Offline</span>';
		        	echo '<tr><td><strong><a class="row-title" target="_blank" href="' . $site . '">' . $site . '</a></strong></td><td><strong>' . $online . '</strong></td><td>' . $status[1] . '</td></tr>';
		        }
		        ?>
        	</tbody>
    	</table>
	</div>
    <?php
}

function sc_settings_render() {
    global $interval, $sites;
    ?>
    <h2>Sitechecker settings</h2>
    <h3>Sites currently being checked</h3>
    <table class="wp-list-table widefat striped">
    	<thead>
    		<tr>
    			<th>URL</th>
    			<th></th>
    		</tr>
    	</thead>
    	<tbody>
	        <?php
	        foreach ($sites as $key => $site) {
	        	echo 
	        	'<tr>
	        		<td>
	        			<strong><a class="row-title" target="_blank" href="' . $site . '">' . $site . '</a></strong>
	        		</td>
	        		<td>
						<form action="' . get_admin_url() . 'admin-post.php" method="post">
							<input type="hidden" name="action" value="submit-form" />
							<input type="hidden" name="delete-site" value="' . $key . '" />
							<input type="submit" value="Delete site" />
						</form>
	        		</td>
	        	</tr>';
	        }
	        ?>
    	</tbody>
	</table>

    <hr/>

	<h3>Add new site to be checked</h3>
	<form action="<?php echo get_admin_url(); ?>admin-post.php" method="post">
		<input type="hidden" name="action" value="submit-form" />
		<label for="url">URL</label>
		<select name="protocol">
			<option value="http">http://</option>
			<option value="https">https://</option>
		</select>
		<input id="url" type="text" name="url" />
		<input type="submit" value="Add site" />
	</form>

	<hr/>

	<h3>Update site check interval</h3>
	<form action="<?php echo get_admin_url(); ?>admin-post.php" method="post">
		<input type="hidden" name="action" value="submit-form" />
		<label for="interval">Interval (seconds)</label>
		<input id="interval" type="number" name="interval" value="<?php echo $interval; ?>" />
		<input type="submit" value="Update interval" />
	</form>

	<hr/>

    <?php
}


 
function sc_plugin_activation() {
	$init_sites = array(
		'https://girlit.se', 
		'https://pennyfriends.se', 
		'https://stabenfeldt.se',
		'http://stabenfeldt.en'
	);
	add_option('sitechecker_sites', $init_sites);
	add_option('sitechecker_interval', 60);
} 

function sc_plugin_deactivation() {
	delete_option('sitechecker_sites');
	delete_option('sitechecker_interval');
}
