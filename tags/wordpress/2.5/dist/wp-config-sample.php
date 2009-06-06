<?php
// ** Настройки MySQL ** //
define('DB_NAME', 'putyourdbnamehere'); // Имя базы данных
define('DB_USER', 'usernamehere'); // Ваше имя в MySQL
define('DB_PASSWORD', 'yourpasswordhere'); // ...и пароль
define('DB_HOST', 'localhost'); // С вероятностью 99% ва it придется это менять
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_general_ci');

// Смените уникальную фразу SECRET_KEY.  Вы можете не запоминать ее, так что сделайте
// ее длинной и сложной. Вы можете посетить https://www.grc.com/passwords.htm, чтобы
// сгенерировать себе уникальную и неповторимую фразу, или просто придумать ее сами.
define('SECRET_KEY', 'put your unique phrase here'); // Измените эту уникальную фразу.

// Вы можете установить несколько блогов в одну базу данных, если будете использовать разные префиксы.
$table_prefix  = 'wp_';   // Только цифры, буквы и знак '_'

// Это настройка локализации WordPress. Соответствующий MO-файл для выбранного языка
// должен быть установлен в wp-content/languages.
define ('WPLANG', 'ru_RU');

/* Это все, дальше не редактируем! Счастливого блоггинга. */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'wp-settings.php');
?>
