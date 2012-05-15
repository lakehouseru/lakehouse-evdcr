<?php
if (!defined('ABSPATH') || !defined('WP_UNINSTALL_PLUGIN')) die();

$option_keys = array(
		'semisecurelogin_reimagined_encrypt_admin',
		'semisecurelogin_reimagined_encrypt_login',
		'semisecurelogin_reimagined_rsa_keys',
		'semisecurelogin_reimagined_secretkey_algo',
		'semisecurelogin_reimagined_more_settings'
		);

if (!is_multisite()) {
	// delete data from wp_options
	foreach ($option_keys as $option_key) {
		delete_option($option_key);
	}
}
else {
	// delete data from wp_sitemeta
	foreach ($option_keys as $option_key) {
		delete_site_option($option_key);
	}

	// will we run into memory issues if there are LOTS of site in the network? Let's just limit to 100 for now.
	global $wpdb;
	//$query = $wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = %d", $wpdb->siteid);
	$query = $wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = %d LIMIT %d", $wpdb->siteid, 100);
	$blog_ids = $wpdb->get_col( $query );
	if ($blog_ids) {
		foreach($blog_ids as $blog_id) {
			// also delete the data from each options table (wp_options, wp_2_options, wp_3_options, etc)
			foreach ($option_keys as $option_key) {
				delete_blog_option($blog_id, $option_key);
			}
		}
	}
}
?>