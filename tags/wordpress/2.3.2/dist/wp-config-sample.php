<?php
// ** Настройки MySQL ** //
define('DB_NAME', 'putyourdbnamehere'); // Имя базы данных
define('DB_USER', 'usernamehere'); // Ваше имя пользователя в MySQL
define('DB_PASSWORD', 'yourpasswordhere'); // ...и пароль
define('DB_HOST', 'localhost'); // C вероятностью 99% вам не придется менять это значение
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_general_ci');

// Вы сможете устанавливать несколько блогов в одну базу, если зададите уникальный префикс
$table_prefix  = 'wp_'; // Допускаются только цифры, буквы и знак подчеркивания

// Это значение определяет локализацию WordPress. Соотвествующий MO-файл для
// выбранного языка должен быть установлен в wp-content/languages.
define ('WPLANG', 'ru_RU');

/* Вот и все, хватит редактировать! Счастливого блоггинга. */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'wp-settings.php');
?>
