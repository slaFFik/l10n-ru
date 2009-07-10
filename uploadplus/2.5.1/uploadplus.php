<?php
/*
Plugin Name: Upload+
Plugin URI: http://pixline.net/wordpress-plugins/upload-plus/en/
Description: Security and sanity in file names while uploading. Once activate, please <a href="options-general.php?page=uploadplus">define your settings</a>. 
Author: Pixline
Version: 2.5.1
Author URI: http://pixline.net/

Copyright (C) 2007/2008 Paolo Tresso / Pixline (http://pixline.net/)

Includes hints and code by:
	Francesco Terenzani (http://terenzani.it/)
	Jennifer Hodgdon (http://www.poplarware.com/)

Uses UTF8 PHP classes by http://phputf8.sourceforge.net/

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

### Create text domain for translations
add_action('init', 'uploadplus_textdomain');
function uploadplus_textdomain() {
	load_plugin_textdomain('uploadplus', 'wp-content/plugins/uploadplus');
}

require_once 'utf8/utf8.php';
require_once 'utf8/str_ireplace.php';
require_once UTF8 . '/utils/validation.php';
require_once UTF8 . '/utils/ascii.php';
require_once 'utf8_to_ascii/utf8_to_ascii.php';

$version = get_option('uploadplus_version');
if ($version == '') $version = 0;

if ($version < 3) {
	add_option('uploadplus_style','a:1:{i:0;s:1:"1";}',__('Choose what style will be used', 'uploadplus'),'yes');
	add_option('uploadplus_lettercase','a:1:{i:0;s:1:"1";}',__('Make all lowercase', 'uploadplus'),'yes');
	update_option('uploadplus_version', 3);
	}

/* add option page */
function upp_add_mangle_options_page(){
	if(function_exists('add_options_page')){
		add_options_page(__('Upload+ Options', 'uploadplus'),__('Upload+', 'uploadplus'),8,'uploadplus','upp_mangle_options_page');
	}
}

/* the real option page */
function upp_mangle_options_page(){
global $wp_db_version;

	if (isset($_POST['info_update'])) {
		$style = serialize($_POST['uploadplus_style']);
		update_option('uploadplus_style',$style);
		$lettercase = serialize($_POST['uploadplus_lettercase']);
		update_option('uploadplus_lettercase',$lettercase);

		if( !empty($_POST['uploadplus_prefix_custom']) && $_POST['uploadplus_prefix']=='none' ){
			update_option('uploadplus_prefix_standard','none');
			update_option('uploadplus_prefix_custom',$_POST['uploadplus_prefix_custom']);
		}elseif($_POST['uploadplus_prefix']!='none'){
			update_option('uploadplus_prefix_custom','');
			update_option('uploadplus_prefix_standard',$_POST['uploadplus_prefix']);
		}
		echo('<div id="message" class="updated fade"><p><strong>'.__('Settings saved.', 'uploadplus').'</strong></p></div>');
	}

	$flag1 = ""; $flag2 = $flag1; $flag3 = $flag1; $flag4 = $flag1; $flag5 = $flag1;
	$y = get_option('uploadplus_style');
	$x = unserialize($y);
	if($x[0] == 1){ $flag1 = 'checked="checked"'; }
	if($x[0] == 2){ $flag2 = 'checked="checked"'; }
	if($x[0] == 3){ $flag3 = 'checked="checked"'; }

	$opt_lettercase = get_option('uploadplus_lettercase');
	$new_lettercase = unserialize($opt_lettercase);
	if($new_lettercase[0]==1){ $flag4='checked="checked"'; }else{ $flag5='checked="checked"'; }

	$custom		= get_option('uploadplus_prefix_custom');
	$standard	= get_option('uploadplus_prefix_standard');
	$pref_custom = "";
	$prefs = array("none"=>"","md"=>"","ymd"=>"","ymdhi"=>"","ymdhis"=>"","random"=>"","unix"=>"");

	if($custom!=""){
		$pref_custom = $custom;
	}elseif($custom==""){
		$prefs[$standard] = "selected='selected'";		
	}
	
	$test_string1 = "WordPress Manual (for dummies, experts and pro's) 2.2nd Edition.pdf";
#	$test_string1 = 'У беларускіх гарадах працягваецца паказ культавага швэдзкага кіно.pdf';
#	$test_string3 = 'اذاعة شباب اف ام.pdf';
	$demo_string1 = upp_mangle_filename($test_string1);
#	$demo_string2 = upp_mangle_filename($test_string2);
#	$demo_string3 = upp_mangle_filename($test_string3);

	echo("<div class='wrap'>");
	echo("<form method='post' accept-charset='utf-8'>");
		
	echo("<h2>".__("Upload+ Options", "uploadplus")."</h2>");
	echo("<p>".__("Welcome to Upload+. This plugin allows you to rename every file you upload, and in this page you can define this behaviour.", "uploadplus")."</p>");
	echo("<p> ".__("According to your actual ruleset, your files will be renamed like this:", "uploadplus")."<br/>
	<ul>
	<li> <em>".$test_string1."</em><br> ".__("saved as", "uploadplus")." &rsaquo; <strong>".$demo_string1."</strong></li>
	<!-- li> <em>".$test_string2."</em><br> ".__("saved as", "uploadplus")." &rsaquo; <strong>".$demo_string2."</strong></li>
	<li> <em>".$test_string3."</em><br> ".__("saved as", "uploadplus")." &rsaquo; <strong>".$demo_string3."</strong></li -->
	</ul>
	</p>");

	echo("<p><small>".__("You can choose to <em>convert spaces and underscores into dashes</em>, <em>strip all dashes/underscores/spaces</em>, or <em>convert every spaces into an underscore</em>. Also, you can choose to <em>lowercase</em> the file name or leave it with mixed case, and finally you can define a custom prefix to prepend, either a fixed one (like the name of your blog) or a date-based one. Feel free to play with the settings and save them, because you can check in this page what kind of transformation will be applied.", "uploadplus")."</small></p>");
	
	if($wp_db_version < 6846) $tableclass = "editform optiontable"; else $tableclass = "form-table";
	echo('<fieldset class="options" name="set1">

	<legend>'.__('Cleaning rules', 'uploadplus').'</legend>
	<table class="'.$tableclass.'">

	<tr>
	<th scope="row" valign="top">'.__('Basic cleaning', 'uploadplus').'</th>

	<td>
	
	<p><input type="radio" name="uploadplus_style[]" id="uploadplus_style-1" value="1" '.$flag1.'/>
	<label for="uploadplus_style-1">'.__('Convert spaces and underscores into dashes.', 'uploadplus').'
	<small>[ &rsaquo; <em>wordpress-manual.pdf</em> ]</small></label></p>

	<p><input type="radio" name="uploadplus_style[]" id="uploadplus_style-2" value="2" '.$flag2.'/>
	<label for="uploadplus_style-2">'.__('Strip all spaces/dashes/underscores.', 'uploadplus').'
	<small>[ &rsaquo; <em>wordpressmanual.pdf</em> ]</small></label></p>
	
	<p><input type="radio" name="uploadplus_style[]" id="uploadplus_style-3" value="3" '.$flag3.'/>
	<label for="uploadplus_style-3">'.__('Convert spaces into underscores (dashes allowed).', 'uploadplus').' 
	<small>[ &rsaquo; <em>wordpress_manual.pdf</em> ]</small></label></p>
	
	</td>
	</tr>

	<tr>
	<th scope="row" valign="top">'.__('Lowercase', 'uploadplus').'</th>
	<td>
	<input type="radio" name="uploadplus_lettercase[]" id="uploadplus_lettercase-1" value="1" '.$flag4.'/>
	'.__('Make ALL lowercase.', 'uploadplus').'
	<input type="radio" name="uploadplus_lettercase[]" id="uploadplus_lettercase-0" value="0" '.$flag5.'/>
	'.__('Leave untouched.', 'uploadplus').' 
	</td>
	</tr>

	</table>
	</fieldset>');
	
echo('<fieldset class="options" name="set1">
	<legend>'.__('Prefix', 'uploadplus').'</legend>
	<table class="'.$tableclass.'">

	<tr>
	<th scope="row" valign="top">'.__('Prefix', 'uploadplus').'</th>
	<td><p>
	<select name="uploadplus_prefix" id="uploadplus_prefix">	
	<option value="none" label="'.__('No prefix, or custom prefix', 'uploadplus').'">'.__('No prefix, or custom prefix', 'uploadplus').'</option>
	<optgroup label="'.__('Human Readable', 'uploadplus').'">
	<option value="d" label="dd ('.__('like', 'uploadplus').': '.date('d').'_)" '.$prefs['d'].'>d ('.__('like', 'uploadplus').': '.date('d').'_)</option>
	<option value="md" label="mmdd ('.__('like', 'uploadplus').': '.date('md').'_)" '.$prefs['md'].'>mmdd ('.__('like', 'uploadplus').': '.date('md').'_)</option>
	<option value="ymd" label="yyyymmdd ('.__('like', 'uploadplus').': '.date('Ymd').'_)" '.$prefs['ymd'].'>yyyymmdd ('.__('like', 'uploadplus').': '.date('Ymd').'_)</option>
	<option value="ymdhi" label="yyyymmddhhmm ('.__('like', 'uploadplus').': '.date('YmdHi').'_)" '.$prefs['ymdhi'].'>yyyymmddhhmm ('.__('like', 'uploadplus').': '.date('YmdHi').'_)</option>
	<option value="ymdhis" label="yyyymmddhhmmss ('.__('like', 'uploadplus').': '.date('YmdHis').'_)" '.$prefs['ymdhis'].'>yyyymmddhhmmss ('.__('like', 'uploadplus').': '.date('YmdHis').'_)</option>
	</optgroup>

	<optgroup label="'.__('Other Styles', 'uploadplus').'">
	<option value="random" label="[random (mt-rand)] '.mt_rand().'_" '.$prefs['random'].'>[random] '.mt_rand().'_</option>
	<option value="unix" label="[unix timestamp] '.date('U').'_" '.$prefs['unix'].'>[unix] '.date('U').'_</option>
	<!-- option value="blog" label="[blog name] '.strtolower(get_bloginfo('name')).'_" '.$prefs['blog'].'>[blog] '.strtolower(get_bloginfo('name')).'_</option -->
	</optgroup>
	
	</select>
	</p><p>
	<input type="text" name="uploadplus_prefix_custom" size="50" id="uploadplus_prefix_custom" value="'.$pref_custom.'"/><br/>
	<small>'.__('Enter your custom textual prefix (like "prefix_"). Please note this is a *string*, so please do not use php <em>date()</em> arguments :-).', 'uploadplus').'</small>
	</p>
	</td>
	</tr>
	
	</table>
	</fieldset>');	
	
	echo('<p class="submit">
	<input type="hidden" name="action" value="update" />
	<input type="submit" name="info_update" value="'.__('Update Settings', 'uploadplus').' &raquo;" />
	</p>
	</form>');

	echo('<hr/> <p><small>'.__('<a href="http://pixline.net/wordpress-plugins/upload-plus/">Upload+</a> is GPL&copy; <a href="http://pixline.net/">Paolo Tresso / Pixline</a>. UTF8 php classes and translitteration by <a href="http://phputf8.sourceforge.net/">phputf8</a>.<br/> If you find this plugin useful you can donate and support its development. Thank you!', 'uploadplus').'</small>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="http://pixline.net/files/img/paypal.gif" border="0" name="submit" alt="Support via PayPal">
<img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>
</p>');

	echo("</div>");
}

/* find extension */
function upp_findexts ($filename) { 
	$exts = split("[/\\.]", $filename) ; 
	$n = count($exts)-1; 
	$exts = $exts[$n]; 
	return $exts; 
} 

/* find full filename */
function upp_find_filename ($filename) { 
	$explode = explode("/",$filename);
	$explode = array_reverse($explode);
	return $explode[0];
} 

/*    sanitize uploaded file name    */
function upp_mangle_filename($file_name){	
	/* remove internal dots (cosmetical, it would be done by WP, but we need to display it :)*/
	$ext = upp_findexts($file_name);
	$file_name = str_replace(".".$ext,"",$file_name);
	$file_name = str_replace(".","",$file_name);
		
	// initial cleaning
	$file_name = str_replace("(","",$file_name);
	$file_name = str_replace(")","",$file_name);
	$file_name = str_replace("'","",$file_name);
	$file_name = str_replace('"',"",$file_name);
	$file_name = str_replace(',',"",$file_name);

	// some language-based prefilter. props denis.
	$de_from 	= array('ä','ö','ü','ß','Ä','Ö','Ü');
	$de_to 		= array('ae','oe','ue','ss','Ae','Oe','Ue');
	$file_name	= str_replace($de_from, $de_to, $file_name);

	if ( utf8_is_valid($file_name) ) {
        $file_name = utf8_to_ascii($file_name); 
    }else{
        $file_name = utf8_to_ascii($file_name); 
	}

	$file_name = $file_name.".".$ext;
	
	$lettercase = get_option('uploadplus_lettercase');
	$un_lettercase = unserialize($lettercase);
	switch($un_lettercase[0]):
		case "1":
			$file_name = utf8_strtolower($file_name);
			break;
	endswitch;

	$y = get_option('uploadplus_style');
	$x = unserialize($y);
	switch($x[0]):
	case "1":
		$file_name = ereg_replace("[^A-Za-z0-9._]", "-", $file_name);
		$file_name = utf8_ireplace("_", "-", $file_name);	
		$file_name = utf8_ireplace(" ", "-", $file_name);
		$file_name = utf8_ireplace("%20", "-", $file_name);
		break;
	case "2":	
		$file_name = ereg_replace("[^A-Za-z0-9._]", "", $file_name);
		$file_name = utf8_ireplace("_", "", $file_name);	
		$file_name = utf8_ireplace("-", "", $file_name);	
		$file_name = utf8_ireplace("%20", "", $file_name);
		break;
	case "3":
		$file_name = ereg_replace("[^A-Za-z0-9._]", "_", $file_name);
		$file_name = utf8_ireplace("-", "_", $file_name);	
		$file_name = utf8_ireplace(" ", "_", $file_name);
		$file_name = utf8_ireplace("%20", "_", $file_name);
		break;
	endswitch;

	$custom = get_option('uploadplus_prefix_custom');
	$standard = get_option('uploadplus_prefix_standard');

	if($custom!="" && $standard=="none"){
		$file_name = $custom.$file_name;
	}else{
		switch($standard):
			case "d":		$file_name = date('d')."_".$file_name;			break;
			case "md":		$file_name = date('md')."_".$file_name;			break;
			case "ymd":		$file_name = date('Ymd')."_".$file_name;		break;
			case "ymdhi":	$file_name = date('YmdHi')."_".$file_name;		break;
			case "ymdhis":	$file_name = date('YmdHis')."_".$file_name;		break;
			case "random":	$file_name = mt_rand()."_".$file_name;			break;
			case "unix":	$file_name = date('U')."_".$file_name;			break;
#			case "blog":	$file_name = strtolower(get_bloginfo('name'))."_".$file_name;	break;
		endswitch;
	}

	return $file_name;
}

/* apply out changes to the real file while it's being moved to its destination */
// $array( 'file' => $new_file, 'url' => $url, 'type' => $type );
function upp_rename($array){ 
global $action;
	$current_name = upp_find_filename($array['file']);
	$current_name = urldecode($current_name);
	$new_name = upp_mangle_filename($current_name);		
	$lpath = str_replace($current_name, "", urldecode($array['file']));
	$wpath = str_replace($current_name, "", urldecode($array['url']));
	$lpath_new = $lpath . $new_name;
	$wpath_new = $wpath . $new_name;
	if( @rename($array['file'], $lpath_new) )
	return array(
		'file' => $lpath_new,
		'url' => $wpath_new,
		'type' => $array['type']
		);
	return $array;
}

add_action( 'admin_menu', 'upp_add_mangle_options_page' );	// add option page
add_action('wp_handle_upload', 'upp_rename');				// apply our modifications
?>