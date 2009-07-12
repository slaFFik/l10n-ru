<?php
/*
Plugin Name: Drafts Dropdown 
Plugin URI: http://alexking.org/projects/wordpress 
Description: Easy access to your WordPress drafts from within the web admin interface. Drafts are listed in a drop-down menu. 
Version: 1.0
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
*/

// ini_set('display_errors', '1'); ini_set('error_reporting', E_ALL);

### Create Text Domain For Translations
add_action('init', 'dropdown_textdomain');
function dropdown_textdomain() {
	load_plugin_textdomain('drafts-dropdown', false, 'drafts-dropdown');
}

function cfdd_get_drafts() {
	$drafts = new WP_Query('post_type=post&post_status=draft&posts_per_page=100&order=DESC&orderby=modified');
	return $drafts->posts;
}

function screen_meta_drafts_content() {
	$output = '';
	$drafts = cfdd_get_drafts();
	if (count($drafts)) {
		$output .= '<ul id="cfdd_drafts">';
		foreach ($drafts as $draft) {
			$output .= '<li><a href="'.get_bloginfo('wpurl').'/wp-admin/post.php?action=edit&post='.$draft->ID.'">'.wp_specialchars($draft->post_title).'</a></li>';
		}
		$output .= '</ul>';
	}
	else {
		$output .= '<p>'.__('(none)', 'drafts-dropdown').'</p>';
	}
	return $output;
}

function cfdd_admin_footer() {
?>
<style type="text/css">
.cfdd_col {
	float: left;
	margin-right: 20px;
}
.cfdd_clear {
	clear: both;
	float: none;
}
</style>
<script type="text/javascript">
jQuery(function($) {
	var copy = $('#contextual-help-wrap');
	$('.screen-meta-wrap').css({
		'background-color': copy.css('background-color'),
		'border-color': copy.css('border-bottom-color')
	});
	var drafts = $('#cfdd_drafts li');
	var drafts_count = drafts.size();
	var i = 0;
	if (drafts_count <= 10) {
// set to 2 columns
		$('#screen-meta-drafts-wrap .screen-meta-content').append('<div class="cfdd_col" id="cfdd_col_1"><ul></ul></div><div class="cfdd_col" id="cfdd_col_2"><ul></ul></div><div class="cfdd_clear"></div>');
		var col_count = Math.ceil(drafts_count / 2);
		drafts.each(function() {
			i < col_count ? target = '#cfdd_col_1 ul' : target = '#cfdd_col_2 ul';
			$(this).appendTo(target);
			i++;
		});
	}
	else {
// 3 columns
		$('#screen-meta-drafts-wrap .screen-meta-content').append('<div class="cfdd_col" id="cfdd_col_1"><ul></ul></div><div class="cfdd_col" id="cfdd_col_2"><ul></ul></div><div class="cfdd_col" id="cfdd_col_3"><ul></ul></div><div class="cfdd_clear"></div>');
		var col_count = Math.ceil(drafts_count / 3);
		drafts.each(function() {
			if (i < col_count) {
				target = '#cfdd_col_1 ul';
			}
			else if (i >= col_count * 2) {
				target = '#cfdd_col_3 ul';
			}
			else {
				target = '#cfdd_col_2 ul';
			}
			$(this).appendTo(target);
			i++;
		});
	}
	$('#cfdd_drafts').remove();
// set size of cfdd_col
	$('.cfdd_col').width(Math.floor($('#wpbody-content').width() - 160) / 3);
});
</script>
<?php
}
add_action('admin_footer', 'cfdd_admin_footer');

function cfdd_screen_meta($screen_meta) {
	$screen_meta[] = array(
		'key' => 'drafts',
		'label' => __('Drafts', 'drafts-dropdown'),
		'content' => 'screen_meta_drafts_content'
	);

	return $screen_meta;
}
add_filter('screen_meta', 'cfdd_screen_meta');

function cfdd_init() {
	global $screen_meta;
	if (!count($screen_meta)) {
		$screen_meta = array();
	}
}
if (is_admin()) {
	add_action('init', 'cfdd_init');
}

if (!function_exists('screen_meta_html')) {

function screen_meta_html($meta) {
	extract($meta);
	if (function_exists($content)) {
		$content = $content();
	}
	echo '
<div id="screen-meta-'.$key.'-wrap" class="screen-meta-wrap hidden">
	<div class="screen-meta-content">'.$content.'</div>
</div>
<div id="screen-meta-'.$key.'-link-wrap" class="hide-if-no-js screen-meta-toggle cf">
<a href="#screen-meta-'.$key.'-wrap" id="screen-meta-'.$key.'-link" class="show-settings">'.$label.'</a>
</div>
	';
}

}

if (!function_exists('screen_meta_output')) {

function screen_meta_output() {
	global $screen_meta;
/*
expected format:
$screen_meta = array(
	array(
		'key' => 'drafts',
		'label' => 'Drafts',
		'content' => 'screen_meta_drafts_content' // can be content or function name
	)
);
*/
	$screen_meta = apply_filters('screen_meta', $screen_meta);
	echo '<div id="screen-meta-extra-content">';
	foreach ($screen_meta as $meta) {
		screen_meta_html($meta);
	}
	echo '</div>';
?>
<style type="text/css">
.screen-meta-toggle {
	float: right;
	background: transparent url( <?php bloginfo('wpurl'); ?>/wp-admin/images/screen-options-left.gif ) no-repeat 0 0;
	font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
	height: 22px;
	padding: 0;
	margin: 0 6px 0 0;
}
.screen-meta-wrap h5 {
	margin: 8px 0;
	font-size: 13px;
}
.screen-meta-wrap {
	border-style: none solid solid;
	border-top: 0 none;
	border-width: 0 1px 1px;
	margin: 0 15px;
	padding: 8px 12px 12px;
	-moz-border-radius: 0 0 0 4px;
	-webkit-border-bottom-left-radius: 4px;
	-khtml-border-bottom-left-radius: 4px;
	border-bottom-left-radius: 4px;
}
</style>
<script type="text/javascript">
jQuery(function($) {

// These hacks not needed if adopted into core
// move tabs into place
	$('#screen-meta-extra-content .screen-meta-toggle.cf').each(function() {
		$('#screen-meta-links').append($(this));
	});
// Move content into place
	$('#screen-meta-extra-content .screen-meta-wrap').each(function() {
		$('#screen-meta-links').before($(this));
	});
// end hacks

// simplified generic code to handle all screen meta tabs
	$('#screen-meta-links a.show-settings').unbind().click(function() {
		var link = $(this);
		$(link.attr('href')).slideToggle('fast', function() {
			if (link.hasClass('screen-meta-shown')) {
				link.css({'backgroundImage':'url("images/screen-options-right.gif")'}).removeClass('screen-meta-shown');
				$('.screen-meta-toggle').css('visibility', 'visible');
			}
			else {
				$('.screen-meta-toggle').css('visibility', 'hidden');
				link.css({'backgroundImage':'url("images/screen-options-right-up.gif")'}).addClass('screen-meta-shown').parent().css('visibility', 'visible');
			}
		});
		return false;
	});
});
</script>

<?php
}
add_action('admin_footer', 'screen_meta_output');

}

//a:22:{s:11:"plugin_name";s:15:"Drafts Dropdown";s:10:"plugin_uri";s:38:"http://alexking.org/projects/wordpress";s:18:"plugin_description";s:112:"Easy access to your WordPress drafts from within the web admin interface. Drafts are listed in a drop-down menu.";s:14:"plugin_version";s:3:"1.0";s:6:"prefix";s:4:"cfdd";s:12:"localization";s:14:"draft-dropdown";s:14:"settings_title";N;s:13:"settings_link";N;s:4:"init";b:0;s:7:"install";b:0;s:9:"post_edit";b:0;s:12:"comment_edit";b:0;s:6:"jquery";b:0;s:6:"wp_css";b:0;s:5:"wp_js";b:0;s:9:"admin_css";b:0;s:8:"admin_js";s:1:"1";s:15:"request_handler";b:0;s:6:"snoopy";b:0;s:11:"setting_cat";b:0;s:14:"setting_author";b:0;s:11:"custom_urls";b:0;}

?>