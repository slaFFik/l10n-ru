<?php
define('WP_INSTALLING', true);
//These two defines are required to allow us to use require_wp_db() to load the database class while being wp-content/wp-db.php aware
define('ABSPATH', dirname(dirname(__FILE__)).'/');
define('WPINC', 'wp-includes');

require_once('../wp-includes/compat.php');
require_once('../wp-includes/functions.php');
require_once('../wp-includes/classes.php');

if (!file_exists('../wp-config-sample.php'))
	wp_die('Sorry, I need a wp-config-sample.php file to work from. Please re-upload this file from your WordPress installation.');

$configFile = file('../wp-config-sample.php');

if ( !is_writable('../'))
	wp_die("Sorry, I can't write to the directory. You'll have to either change the permissions on your WordPress directory or create your wp-config.php manually.");

// Check if wp-config.php has been created
if (file_exists('../wp-config.php'))
	wp_die("<p>The file 'wp-config.php' already exists. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href='install.php'>installing now</a>.</p>");

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

function display_header(){
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WordPress &rsaquo; Настройка файла конфигурации</title>
<link rel="stylesheet" href="<?php echo $admin_dir; ?>css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>Добро пожаловать. Прежде чем мы начнем, потребуется информация о базы данных. Вот что вы должны знать до начала процедуры установки.</p>
<ol>
	<li>Имя базы данных</li>
	<li>Имя пользователя базы данных</li>
	<li>Пароль к базе данных</li>
	<li>Адрес сервера базы данных</li>
	<li>Префикс таблиц (если вы хотите запустить более чем один WordPress на одной базе) </li>
</ol>
<p><strong>Если по каким-то причинам это автоматическое создание файла не работает, не расстраивайтесь. Все это предназначено для заполнения настроечного файла. Вы можете просто открыть <code>wp-config-sample.php</code> в текстовом редакторе, внести вашу информацию и сохранить его как <code>wp-config.php</code>. </strong></p>
<p>В любом случае, эти данные должны быть предоставлены вашим хостинг-провайдером. Если у вас ее нет, свяжитесь с администрацией провайдера. А если есть...</p>

<p><a href="setup-config.php?step=1" class="button">Поехали!</a></p>
<?php
	break;

	case 1:
		display_header();
	?>
<form method="post" action="setup-config.php?step=2">
	<p>Введите здесь информацию о подключении к базе данных. Если вы в ней не уверены, свяжитесь с хостинг-провайдером. </p>
	<table class="form-table">
		<tr>
			<th scope="row">Имя базы данных</th>
			<td><input name="dbname" type="text" size="25" value="wordpress" /></td>
			<td>Имя базы данных, в которую вы хотите установить WP.</td>
		</tr>
		<tr>
			<th scope="row">Имя пользователя</th>
			<td><input name="uname" type="text" size="25" value="username" /></td>
			<td>Ваше имя в mySQL</td>
		</tr>
		<tr>
			<th scope="row">Пароль</th>
			<td><input name="pwd" type="text" size="25" value="password" /></td>
			<td>...и пароль MySQL.</td>
		</tr>
		<tr>
			<th scope="row">Сервер базы данных</th>
			<td><input name="dbhost" type="text" size="25" value="localhost" /></td>
			<td>С вероятностью в 99% вам не придется менять это значение.</td>
		</tr>
		<tr>
			<th scope="row">Префикс таблиц</th>
			<td><input name="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>Если вы хотите запустить несколько WordPress установок в одну базу, измените это значение.</td>
		</tr>
	</table>
	<h2 class="step">
	<input name="submit" type="submit" value="Отправить" class="button" />
	</h2>
</form>
<?php
	break;

	case 2:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$prefix  = trim($_POST['prefix']);
	if (empty($prefix)) $prefix = 'wp_';

	// Test the db connection.
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);

	// We'll fail here if the values are no good.
	require_wp_db();
	if ( !empty($wpdb->error) )
		wp_die($wpdb->error->get_error_message());

	$handle = fopen('../wp-config.php', 'w');

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				fwrite($handle, str_replace("putyourdbnamehere", $dbname, $line));
				break;
			case "define('DB_USER'":
				fwrite($handle, str_replace("'usernamehere'", "'$uname'", $line));
				break;
			case "define('DB_PASSW":
				fwrite($handle, str_replace("'yourpasswordhere'", "'$passwrd'", $line));
				break;
			case "define('DB_HOST'":
				fwrite($handle, str_replace("localhost", $dbhost, $line));
				break;
			case '$table_prefix  =':
				fwrite($handle, str_replace('wp_', $prefix, $line));
				break;
			default:
				fwrite($handle, $line);
		}
	}
	fclose($handle);
	chmod('../wp-config.php', 0666);

	display_header();
?>
<p>Все в порядке! Вы успешно прошли через эту часть установки. WordPress теперь может соединиться с вашей базой данных. Если вы готовы, пришло время для &hellip;</p>

<p><a href="install.php" class="button">Запуска установки</a></p>
<?php
	break;
}
?>
</body>
</html>
