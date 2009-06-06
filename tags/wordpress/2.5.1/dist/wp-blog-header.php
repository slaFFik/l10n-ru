<?php

if (! isset($wp_did_header)):
if ( !file_exists( dirname(__FILE__) . '/wp-config.php') ) {
	if (strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false) $path = '';
	else $path = 'wp-admin/';

	require_once( dirname(__FILE__) . '/wp-includes/classes.php');
	require_once( dirname(__FILE__) . '/wp-includes/functions.php');
	require_once( dirname(__FILE__) . '/wp-includes/plugin.php');
    wp_die("Не похоже, что здесь есть файл <code>wp-config.php</code>. Перед тем, как мы начнем, потребуется его создать. Нужна помощь? <a href='http://codex.wordpress.org/Editing_wp-config.php'>Вы получите ее</a>. Вы можете создать файл <code>wp-config.php</code> при помощи web-интерфейса, но это работает не на всех серверах. Ниболее надежно ручное создание файла.</p><p><a href='wp-admin/setup-config.php' class='button'>Создать файл настроек</a>", "WordPress &rsaquo; Ошибка");	
}

$wp_did_header = true;

require_once( dirname(__FILE__) . '/wp-config.php');

wp();

require_once(ABSPATH . WPINC . '/template-loader.php');

endif;

?>
