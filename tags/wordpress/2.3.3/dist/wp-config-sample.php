<?php
// ** Настройки MySQL ** //
define('DB_NAME', 'putyourdbnamehere'); // Имя базы данных
define('DB_USER', 'usernamehere'); // Ваш пользователь в MySQL
define('DB_PASSWORD', 'yourpasswordhere'); // ...и пароль
define('DB_HOST', 'localhost'); // С вероятностью 99% вам не придется менять это значение
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Вы можете устанавливать несколько блогов одну базу если зададите каждому уникальный префикс
$table_prefix  = 'wp_';  // Пожалуйста, только буквы, цифры и знак подмеркивания!

// Это параменты локализации WordPress.
// Соотвествующий MO-файл должен быть в директории wp-content/languages.
define ('WPLANG', 'ru_RU');

/* Это все, дальше не редактируйте! Успешного блоггинга */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'wp-settings.php');
?>
