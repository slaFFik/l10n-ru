<?php
/*
Plugin Name: rurumo
Plugin URI: http://code.google.com/p/l10n-ru/
Description: Автоматическое обновление переводов. Антону Скоробогатову (<strong>rurumo</strong>) посвящается.
Author: Sol, Sergey Biryukov
Version: 0.2-trunk
Author URI: http://ru.wordpress.org/
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
	if ( false !== ( $fs = @fsockopen( 'l10n-ru.googlecode.com', 80, $errno, $errstr, 3 ) ) && is_resource($fs) ) {
		fwrite( $fs, "GET /files/{$file}-{$ver}-ru_RU.zip HTTP/1.0\r\nHost: l10n-ru.googlecode.com\r\n\r\n" );
		while (!feof($fs)) $response .= fgets( $fs, 1160 ); // One TCP-IP packet
		fclose( $fs );
		$response = explode("\r\n\r\n", $response, 2);
		if ( preg_match( '|HTTP/.*? 200|', $response[0] ) ) return "http://l10n-ru.googlecode.com/files/{$file}-{$ver}-ru_RU.zip";
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
function rurumo_notification ($file_name, $plugin_data) {
	global $rurumo;

	$plugin_name = basename($file_name,'.php');
	$plugin_dir  = dirname ($file_name);
	$plugin_pack = ($plugin_dir!='.'?$plugin_dir:$plugin_name);

	if (!isset($rurumo[$plugin_pack])) {
		$rurumo[$plugin_pack]->checked = 0;
		$rurumo[$plugin_pack]->installed = false;
		$rurumo[$plugin_pack]->report = ABSPATH.PLUGINDIR.'/'.$plugin_name.".ru.txt";
	}
	if (file_exists($rurumo[$plugin_pack]->report)) {
		$rurumo[$plugin_pack]->installed  = true;
	}
	else if (time() - $rurumo[$plugin_pack]->checked > 43200 ) {
		$rurumo[$plugin_pack]->package = rurumo_check ($plugin_pack, $plugin_data['Version']);
		$rurumo[$plugin_pack]->checked = time();
	}
	if (($rurumo[$plugin_pack]->package != null) && ($rurumo[$plugin_pack]->installed == false)) {
		echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update" ><div class="update-message">';
		$url = wp_nonce_url('update.php?action=rurumo-get-translation&plugin=' . $plugin_pack, 'rurumo');
		echo "Перевод этого плагина вы можете скачать с сайта <a href='".$rurumo[$plugin_pack]->package."'>l10n.googlecode.com</a> или <a href='$url'>установить автоматически</a>.";		
		echo '</div></td></tr>';
	}
	update_option('rurumo', serialize($rurumo));
}
add_action('after_plugin_row', 'rurumo_notification', 10, 2);

function rurumo_get_translation() {
	global $rurumo, $wp_filesystem;

	if ( !current_user_can('update_plugins') )
		wp_die(__('You do not have sufficient permissions to update plugins for this site.'));

	check_admin_referer('rurumo');

	$title = 'Обновление перевода';
	$parent_file = 'plugins.php';
	$submenu_file = 'plugins.php';

	$name = isset($_GET['plugin']) ? $_GET['plugin'] : '';
	if ( isset($rurumo) && !empty($name) ) {
		require_once(ABSPATH . 'wp-admin/admin-header.php');

		include(dirname(__FILE__) . '/update.php');
		echo '<p><strong>' . __('Actions:') . '</strong> <a href="' . admin_url('plugins.php') . '" title="' . esc_attr__('Goto plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a></p>';

		include(ABSPATH . 'wp-admin/admin-footer.php');
	}
}
add_action('update-custom_rurumo-get-translation', 'rurumo_get_translation');
?>