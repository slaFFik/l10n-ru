<?php
// Make the menu wider and correct some overlapping issues
function ru_accomodate_markup() {
	global $locale, $wp_styles, $WPAdminBar;

	wp_enqueue_style($locale, WP_CONTENT_URL . "/languages/$locale.css", array(), '20090128', 'all');
	wp_enqueue_style("$locale-ie", WP_CONTENT_URL . "/languages/$locale-ie.css", array(), '20090128', 'all');
	$wp_styles->add_data("$locale-ie", 'conditional', 'IE');

	if ( !empty($WPAdminBar->settings) && $WPAdminBar->settings['show_admin'] )
		wp_enqueue_script($locale, WP_CONTENT_URL . "/languages/$locale.js", array(), '20090128');

	wp_print_styles();
	wp_print_scripts();
}
add_action('admin_head', 'ru_accomodate_markup', 11);
?>