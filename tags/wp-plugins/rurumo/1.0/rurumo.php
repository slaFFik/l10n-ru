<?php
/*
Plugin Name: rurumo
Plugin URI: http://wordpress-russia.org/support/topic/rurumo
Description: Обновление переводов. Антону Скоробогатову (<strong>rurumo</strong>) посвящается.
Author: Sol
Version: 0.1
Author URI: http://salpagarov.ru
*/

/**
 * Проверить наличие пакета в репозитории
 *
 * @param   string $file	Имя плагина
 * @param   string $ver     Номер версии (из плагина)
 * @return  string 			Ссылка для прямого скачивания
 */
function rurumo_check ($file, $ver) {
	
	$response = '';
	if ( false !== ( $fs = @fsockopen( 'wordpress-russia.org', 80, $errno, $errstr, 3 ) ) && is_resource($fs) ) {
		fwrite( $fs, "GET /rurumo/$file.zip?version=$ver HTTP/1.0\r\nHost: wordpress-russia.org\r\n\r\n" );
		while (!feof($fs)) $response .= fgets( $fs, 1160 ); // One TCP-IP packet
		fclose( $fs );

		$response = explode("\r\n\r\n", $response, 2);
		if ( preg_match( '|HTTP/.*? 200|', $response[0] ) ) return "http://wordpress-russia.org/rurumo/$file.zip?version=$ver";
	}
	return false;
}

/**
 * Вариация функции copy_dir (с перезаписью существующих файлов)
 *
 * @param  string $from
 * @param  string $to
 * @return bool
 */
function rurumo_copy_dir($from, $to) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist($from);

	$from = trailingslashit($from);
	$to = trailingslashit($to);

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( 'f' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true) ) return false;
			$wp_filesystem->chmod($to . $filename, 0644);
		} elseif ( 'd' == $fileinfo['type'] ) {
			$wp_filesystem->mkdir($to . $filename, 0755);
			if ( !rurumo_copy_dir($from . $filename, $to . $filename) ) return false;
		}
	}
	return true;
}

/**
 * Создаем пустую настройку (чтобы было куда ссыпать данные плагинов)
 *
 */
function rurumo_activate () { 
	add_option ('rurumo', serialize(array(0)),'','no'); 
}
register_activation_hook( __FILE__, 'rurumo_activate' );

/**
 * Удаление настроек (при необходимости переустановить все переводы -- отключите и включите плагин)
 *
 */
function rurumo_deactivate () { 
	delete_option('rurumo'); 
}
register_deactivation_hook( __FILE__, 'rurumo_deactivate' );

/**
 * Настройки обновления (глобальная переменная)
 */
$rurumo = unserialize(get_option('rurumo'));

/**
 * Проверка возможности обновления
 *
 * @param staring $file_name
 */
function rurumo_notification ($file_name) {
	global $plugin_data;
	global $rurumo;
	
	$plugin_name = basename($file_name,'.php');
	$plugin_dir  = dirname ($file_name);
	$plugin_ver  = $plugin_data['Version'];
	$plugin_pack = ($plugin_dir!='.'?$plugin_dir:$plugin_name);
	
	if (!isset($rurumo[$plugin_pack])) {
		$rurumo[$plugin_pack]->checked = 0;
		$rurumo[$plugin_pack]->installed = false;
		$rurumo[$plugin_pack]->report = ABSPATH.PLUGINDIR.'/'.$file_name.".ru.txt";
	}
	if (file_exists($rurumo[$plugin_pack]->report)) {
		$rurumo[$plugin_pack]->installed  = true;
	}
	else if (time() - $rurumo[$plugin_pack]->checked > 43200 ) {
		$rurumo[$plugin_pack]->package = rurumo_check ($plugin_pack, $plugin_ver);
		$rurumo[$plugin_pack]->checked = time();
	}
	if (($rurumo[$plugin_pack]->package != null) && ($rurumo[$plugin_pack]->installed == false)) {
		echo "<tr><td colspan='5' class='plugin-update'>";
		echo "Перевод этого плагина вы можете скачать с сайта <a href='".$rurumo[$plugin_pack]->package."'>wordpress-russia.org</a> или <a href='/wp-admin/admin.php?page=rurumo/update.php&update=$plugin_pack'>установить автоматически</a>.";
		echo "</td></td>";
	}
	update_option('rurumo', serialize($rurumo));
}
add_action ('after_plugin_row', 'rurumo_notification');

?>