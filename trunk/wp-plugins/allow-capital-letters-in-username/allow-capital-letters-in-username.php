<?php
/*
Plugin Name: Allow Capital Letters In Username
Version: 0.3-trunk
Plugin URI: http://ru.forums.wordpress.org/topic/3738
Description: Allows to use uppercase latin letters when registering a new user.
Author: Sergey Biryukov
Author URI: http://sergeybiryukov.ru/
Site Wide Only: true
Network: true
*/

function acl_sanitize_user($username, $raw_username, $strict) {
	$username = wp_strip_all_tags($raw_username);
	$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
	$username = preg_replace('/&.+?;/', '', $username);

	if ( $strict )
		$username = preg_replace('|[^a-zA-Z0-9 _.\-@]|i', '', $username);

	return preg_replace('|\s+|', ' ', $username);
}
add_filter('sanitize_user', 'acl_sanitize_user', 10, 3);

function acl_bp_core_validate_user_signup($result) {
	$illegal_names = get_site_option('illegal_names');

	if ( validate_username($result['user_name']) && !in_array($result['user_name'], (array)$illegal_names) ) {
		$error_index = array_search(__('Only lowercase letters and numbers allowed', 'buddypress'), $result['errors']->errors['user_name']);
		if ( isset($error_index) ) {
			unset($result['errors']->errors['user_name'][$error_index]);
			sort($result['errors']->errors['user_name']);
		}
	}

	return $result;
}
add_filter('bp_core_validate_user_signup', 'acl_bp_core_validate_user_signup');
?>