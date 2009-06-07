<?php
/*
Plugin Name: Yet another threaded Comments plugin
Plugin URI: http://organisiert.net/yatcp/
Description: This comment allows to have newly created comments relate to existing comments, so you can comment on a comment. This information allows to display the Comments in a hierarchical fashion (e.g. a Tree).
Version: 0.6beta
Author: Joachim Praetorius (yatcp@organisiert.net)
Author URI: http://organisiert.net/yatcp/

 		Copyright 2007  Joachim Praetorius (yatcp@organisiert.net)

    YATCP is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/*Init Localization*/
if (function_exists('load_plugin_textdomain')) {
	load_plugin_textdomain('yatcp', 'wp-content/plugins/yatcp');
}

/*Define functions needed to extend the Functionality and add WP Hooks for them */
function yatcp_add_default_options() {
	$yatcp_trackback = get_option("YATCP_EXCLUDE_TRACKBACKS");
	if(empty($yatcp_trackbacks)){
		add_option('YATCP_EXCLUDE_TRACKBACKS', 'true', 'Decides whether trackbacks can be replied to or not. If set to false, Trackbacks get no Reply Link and are not shown in the Comments dropdownbox.');
	}
	
	$yatcp_comment_depth = get_option("YATCP_MAX_COMMENT_DEPTH");
	if(empty($yatcp_comment_depth)){
		add_option('YATCP_MAX_COMMENT_DEPTH', '4', 'Sets the Depth up to which Comments are shown nested. Above that Level Comments will be displayed linearly as usual.');
	}
	
	$yatcp_hide_rackbacks = get_option("YATCP_HIDE_TRACKBACKS");
	if(empty($yatcp_hide_rackbacks)){
		add_option('YATCP_HIDE_TRACKBACKS', 'false', 'Decides whether Trackbacks/Pingbacks are shown in the comment List at all. If set to false Trackbacks/Pingbacks will also not be counted in the Number of comments.');
	}

	$yatcp_verbose_warning = get_option("YATCP_VERBOSE_WARNING");
	if(empty($yatcp_verbose_warning)){
		add_option('YATCP_VERBOSE_WARNING', 'false', 'Decides whether the warning about not deeper nested comments is shown for every comment deeper than the max. Depth or only for the ones on max. depth.');
	}
}
add_action('activate_yatcp/template_functions.php','yatcp_add_default_options');

function yatcp_remove_default_options() {
	//be nice and cleanup behind ourselves
	delete_option("YATCP_EXCLUDE_TRACKBACKS");
	delete_option("YATCP_HIDE_TRACKBACKS");
	delete_option("YATCP_MAX_COMMENT_DEPTH");
	delete_option("YATCP_VERBOSE_WARNING");
}
add_action('deactivate_yatcp/template_functions.php','yatcp_remove_default_options');

function yatcp_add_comment_parent($comment_id) {
	global $wpdb;			
	$parent = $_POST['comment_parent'];
	
	if( (!is_numeric($parent)) || ('0' == $parent) ) {
		return; //do nothing if the parameter is faked or pointing to the post
	} else {
		$result = $wpdb->query("UPDATE $wpdb->comments SET comment_parent = '$parent' WHERE comment_ID = '$comment_id'");
	}
}
add_action('comment_post','yatcp_add_comment_parent');

function yatcp_show_comment_parents($post_id) {
	global $wpdb;
	$comments = $wpdb->get_results("SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1' ORDER BY comment_ID, comment_date");

	echo "<a name='comment_selection'>\n";
	echo "<label for='comment_parent'>" . __('Reply to:', 'yatcp') . "</label>\n";
	echo "<select name='comment_parent' id='comment_parent'>\n";
	echo "	<option value='0'>" . __('Post', 'yatcp') . "</option>\n";	
	yatcp_print_comment_option();
	echo "</select>\n";
	echo "</a>\n";
}
add_action('comment_form','yatcp_show_comment_parents');

/*Replaces the Number Wordpress delivers by it's own, when needed*/
function yatcp_filter_comments_number($num) {
	global $id, $wpdb;
	$YATCP_HIDE_TRACKBACKS = strcmp(get_option("YATCP_HIDE_TRACKBACKS"),"true") == 0;
	//only do the DB Query when needed
	if(!$YATCP_HIDE_TRACKBACKS) {
		return $num;
	}
	
	$sql = "SELECT count(*) as count from $wpdb->comments WHERE comment_post_ID = '$id'";
	if($YATCP_HIDE_TRACKBACKS) {
		$sql .=" AND comment_type != 'trackback' AND comment_type != 'pingback'";
	}
	$count = $wpdb->get_results($sql);
	$c = $count[0];
	$number = $c->count;
	return $number;
}
add_filter('get_comments_number', 'yatcp_filter_comments_number');

// action function for above hook
function yatcp_add_option_page() {
 /// Add a new submenu under Options:
 add_options_page(__('YATCP Options', 'yatcp'), __('YATCP Options', 'yatcp'), 8, __FILE__, 'yatcp_show_options_page');
}

// Hook for adding admin menus
add_action('admin_menu', 'yatcp_add_option_page');

function yatcp_show_options_page() {
		require( ABSPATH . 'wp-content/plugins/yatcp/yatcp_options_page.php');
}

/* Private Functions*/
function yatcp_print_comment_option($prefix='', $number_prefix='', $parent=null){
	$my_comments = yatcp_find_comments($parent, true);
	$counter = 1;
	foreach($my_comments as $comment){
		echo "<option value='$comment->comment_ID'>$prefix"; 
		yatcp_print_comment_type($comment);
		echo sprintf(__(' %s (%s at %s)', 'yatcp'), $number_prefix.$counter, $comment->comment_author, mysql2date(__('d.m.Y H:i', 'yatcp'), $comment->comment_date))."</option>\n";
		if(!count(yatcp_find_comments($comment, true))==0){
			yatcp_print_comment_option($prefix.'&nbsp;&nbsp;',$number_prefix.$counter.'.',$comment);
		}
		$counter+=1;
	}
}

function yatcp_comments_template($file='/yatcp_comments.php'){
	global $withcomments, $post, $id, $user_login, $user_ID, $user_identity;
	if ( ! (is_single() || is_page() || $withcomments) )
		return;

	$req = get_settings('require_name_email');
	$commenter = wp_get_current_commenter();
	extract($commenter);

	define('COMMENTS_TEMPLATE', true);
	$include = apply_filters('comments_template', TEMPLATEPATH .$file );
	if ( file_exists( $include ) ){
		require( $include );
	}
	else {
		require( ABSPATH . 'wp-content/plugins/yatcp/yatcp_comments.php');
	}

}

function yatcp_show_comments( $parent=null, $comment_depth=null) {
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity,$cmts,$cmt_ID, $comment_anchor;
	
	//used for restricting comment-depth
	global $comment_depth, $YATCP_MAX_COMMENT_DEPTH;
	$YATCP_MAX_COMMENT_DEPTH = get_option("YATCP_MAX_COMMENT_DEPTH");

	if(is_null($parent)){
		$cmt_ID = '0';
	} else {
		$cmt_ID = $parent->comment_ID;
	}

	//increase depth
	if (is_null($comment_depth)) {
		$comment_depth = 0;	
	} else {
		$comment_depth = $comment_depth + 1;
	}

	$cmts[$cmt_ID] = yatcp_find_comments($parent);
	$comment_anchor = yatcp_get_url();
	define('COMMENTS_TEMPLATE', true);
	
	$include = apply_filters('comments_template', TEMPLATEPATH .'/single-comment.php'  );
	if ( file_exists( $include ) ){
		require( $include );
	} else {
		require( ABSPATH . 'wp-content/plugins/yatcp/yatcp_single-comment.php');
	}

}

function yatcp_find_comments($parent=null, $considerTBExclusion=false){
	global $YATCP_EXCLUDE_TRACKBACKS;
	global $post, $wpdb, $id, $user_login, $user_ID, $user_identity;

	$YATCP_EXCLUDE_TRACKBACKS = strcmp(get_option("YATCP_EXCLUDE_TRACKBACKS"),"true") == 0;
	$YATCP_HIDE_TRACKBACKS = strcmp(get_option("YATCP_HIDE_TRACKBACKS"),"true") == 0;
	$commenter = wp_get_current_commenter();
	extract($commenter);

	if(is_null($parent)){
		$comment_parent = '0';
	} else {
		$comment_parent = $parent->comment_ID;
	}

// TODO: Use API instead of SELECTs.

	$sql = "SELECT * from $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_parent = '$comment_parent'";
	if( ($YATCP_EXCLUDE_TRACKBACKS && $considerTBExclusion) || ($YATCP_HIDE_TRACKBACKS) ) {
		$sql .=" AND comment_type != 'trackback' AND comment_type != 'pingback'";
	}
	if ( empty($comment_author) ) {
		$sql .= " AND comment_approved='1'";
	} else {
		$author_db = $wpdb->escape($comment_author);
		$email_db  = $wpdb->escape($comment_author_email);

		$sql .= " AND ( comment_approved = '1' OR ( comment_author = '$author_db' AND comment_author_email = '$email_db' AND comment_approved = '0' ) )";
	}

	$sql .= " ORDER BY comment_date";

	$comments = $wpdb->get_results($sql);

	return $comments;
}

function yatcp_get_url(){
	$my_url = '';
	if($_SERVER['HTTPS']){
		$my_url = 'https://';
	} else {
		$my_url = 'http://';
	}
	
	$my_url .= $_SERVER['HTTP_HOST'];
	
	if($_SERVER['SERVER_PORT'] != '80'){
		$my_url .= ':' . $_SERVER['SERVER_PORT'];
	}

	$my_url .= $_SERVER['REQUEST_URI'];
	$my_url .= '#comment_selection';	
	return $my_url;
}

function yatcp_print_comment_type($comment) {
	$type = $comment->comment_type;
	if(strcmp($type,'')==0) {
		_e('Comment', 'yatcp');
	}
	else if(strcmp($type,'trackback')==0) {
		_e('Trackback', 'yatcp');
	}
	else if(strcmp($type,'pingback')==0) {
		_e('Pingback', 'yatcp');
	}
	else {
		echo '';
	}
}

function yatcp_print_comment_warning($depth) {
	$YATCP_VERBOSE_WARNING = strcmp(get_option("YATCP_VERBOSE_WARNING"),"true") == 0;
	$YATCP_MAX_COMMENT_DEPTH = get_option("YATCP_MAX_COMMENT_DEPTH");

	if( ($depth > $YATCP_MAX_COMMENT_DEPTH) && $YATCP_VERBOSE_WARNING) {
		_e('(Comments won\'t nest below this level)', 'yatcp'); 
	}
	elseif ($depth == $YATCP_MAX_COMMENT_DEPTH) {
		_e('(Comments won\'t nest below this level)', 'yatcp'); 
	}
}

?>
