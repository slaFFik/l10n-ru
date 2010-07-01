<?php
/*
Plugin Name: WPLANG Lite
Version: 0.3
Plugin URI: http://uplift.ru/projects/
Description: Creates a separate tiny WPLANG_lite.mo file to use on a site front-end. Allows to save some amount of RAM on a shared hosting server.
Author: Sergey Biryukov
Author URI: http://sergeybiryukov.ru/
*/

$wpll_locale = defined('WPLANG') ? WPLANG : '';
$wpll_pofile = WP_LANG_DIR . "/$wpll_locale.po";	
$wpll_mofile = WP_LANG_DIR . "/{$wpll_locale}_lite.mo";

if ( empty($wpll_locale) || !is_writable(dirname($wpll_mofile)) )
	return;

function wpll_load_mofile($mofile, $domain) {
	global $wpll_mofile;

	if ( $domain == 'default' && !defined('WP_ADMIN') && file_exists($wpll_mofile) )
		$mofile = $wpll_mofile;

	return $mofile;
}
add_filter('load_textdomain_mofile', 'wpll_load_mofile', 10, 2);

function wpll_filter_references($reference) {
	$exclusions = array(
		'wp-admin/',
		'wp-content/plugins/',
		'wp-content/themes/',
		'wp-includes/js/tinymce/',
		'wp-includes/functions.php',
		'wp-includes/script-loader.php',
		'xmlrpc.php'
	);

	return $reference == str_replace($exclusions, '', $reference);
}

function wpll_create_mofile() {
	global $wpll_pofile, $wpll_mofile;

	include_once(ABSPATH . WPINC . '/pomo/po.php');

	$po = new PO();
	if ( !@$po->import_from_file($wpll_pofile) )
		return;

	foreach ( $po->entries as $key => $entry ) {
		if ( !empty($entry->references) ) {
			$entry->references = array_filter($entry->references, 'wpll_filter_references');
			if ( empty($entry->references) ) {
				unset($po->entries[$key]);
				continue;
			}
		}
		if ( !empty($entry->translations) ) {
			if ( $entry->singular == $entry->translations[0] ) {
				unset($po->entries[$key]);
			}
		}
	}

	$mo = new MO();
	$mo->headers = $po->headers;
	$mo->entries = $po->entries;
	$mo->export_to_file($wpll_mofile);
	die();
}
if ( !empty($_GET['wpll_action']) && $_GET['wpll_action'] == 'create_mofile' ) {
	add_action('plugins_loaded', 'wpll_create_mofile', 3);
}

function wpll_create_mofile_call() {
	global $wpll_pofile, $wpll_mofile;

	if ( file_exists($wpll_mofile) && file_exists($wpll_pofile) ) {
		if ( filemtime($wpll_mofile) >= filemtime($wpll_pofile) )
			return;
	}

	echo '<script type="text/javascript" src="' . get_option('home') . '/?wpll_action=create_mofile"></script>';
}
add_action('admin_print_scripts', 'wpll_create_mofile_call');
?>