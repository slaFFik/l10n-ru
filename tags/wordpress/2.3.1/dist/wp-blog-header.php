<?php

if (! isset($wp_did_header)):
if ( !file_exists( dirname(__FILE__) . '/wp-config.php') ) {
	if (strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false) $path = '';
	else $path = 'wp-admin/';

	require_once( dirname(__FILE__) . '/wp-includes/classes.php');
	require_once( dirname(__FILE__) . '/wp-includes/functions.php');
	require_once( dirname(__FILE__) . '/wp-includes/plugin.php');
	wp_die("Не найден файл <code>wp-config.php</code>. Чтобы мы смогли начать работу, он должен существовать. <a href='http://codex.wordpress.org/Editing_wp-config.php'>Нужна помощь?</a>. Вы можете <a href='{$path}setup-config.php'>создать <code>wp-config.php</code> при помощи web-интерфейса</a>, но это может сработать не для всех настроек сервера. Безопаснее всего создать файл вручную.", "WordPress &rsaquo; Ошибка");
}

$wp_did_header = true;

require_once( dirname(__FILE__) . '/wp-config.php');

wp();
gzip_compression();

require_once(ABSPATH . WPINC . '/template-loader.php');

endif;

?>
