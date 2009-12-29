<?php
/*
Description: Converts binary message catalog to a PHP file
Version: 0.1
Author: Sergey Biryukov
Author URI: http://sergeybiryukov.ru/
*/

require('wp-config.php');

global $l10n;

unset($l10n['default']);
load_textdomain('default', WP_LANG_DIR . '/ru_RU.mo');

$l10_strings = array();
foreach ( $l10n['default']->entries as $original => $translation_entry ) {
	if ( isset($translation_entry->plural) )
		$original .= chr(0) . $translation_entry->plural;

	$l10_strings[$original] = implode(chr(0), $translation_entry->translations);
}

$content = '<?php
$l10n_strings["default"] = ' . var_export($l10_strings, true) . ';

unset($l10n["default"]);

function ru_direct_gettext($translation, $text, $domain) {
	global $l10n_strings;

	if ( isset($l10n_strings[$domain][$text]) )
		$translation = $l10n_strings[$domain][$text];

	return $translation;
}
add_filter("gettext", "ru_direct_gettext", 10, 3);

function ru_direct_gettext_with_context($translation, $text, $context, $domain) {
	global $l10n_strings;

	$index = $context . chr(4) . $text;
	if ( isset($l10n_strings[$domain][$index]) )
		$translation = $l10n_strings[$domain][$index];

	return $translation;
}
add_filter("gettext_with_context", "ru_direct_gettext_with_context", 10, 4);

function ru_direct_ngettext($translation, $single, $plural, $number, $domain) {
	global $l10n_strings;

	$index = $single . chr(0) . $plural;
	if ( isset($l10n_strings[$domain][$index]) ) {
		$translations = explode(chr(0), $l10n_strings[$domain][$index]);
		$mo = new MO();
		$translation = $translations[$mo->select_plural_form($number)];
	}

	return $translation;
}
add_filter("ngettext", "ru_direct_ngettext", 10, 5);

function ru_direct_ngettext_with_context($translation, $single, $plural, $number, $context, $domain) {
	global $l10n_strings;

	$index = $context . chr(4) . $single . chr(0) . $plural;
	if ( isset($l10n_strings[$domain][$index]) ) {
		$translations = explode(chr(0), $l10n_strings[$domain][$index]);
		$mo = new MO();
		$translation = $translations[$mo->select_plural_form($number)];
	}

	return $translation;
}
add_filter("ngettext_with_context", "ru_direct_ngettext_with_context", 10, 6);
?>';

$content = str_replace("\n  ", "\n\t", $content);
$content = str_replace('\000', chr(0), $content);

file_put_contents(WP_LANG_DIR . '/ru_RU-strings.php', $content);
?>