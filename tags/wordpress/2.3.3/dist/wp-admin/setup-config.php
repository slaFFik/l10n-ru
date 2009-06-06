<?php
define('WP_INSTALLING', true);

require_once('../wp-includes/compat.php');
require_once('../wp-includes/functions.php');

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
<title>WordPress &rsaquo; Setup Configuration File</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style media="screen" type="text/css">
	<!--
	html {
		background: #eee;
	}
	body {
		background: #fff;
		color: #000;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 20%;
		margin-right: 20%;
		padding: .2em 2em;
	}

	h1 {
		color: #006;
		font-size: 18px;
		font-weight: lighter;
	}

	h2 {
		font-size: 16px;
	}

	p, li, dt {
		line-height: 140%;
		padding-bottom: 2px;
	}

	ul, ol {
		padding: 5px 5px 5px 20px;
	}
	#logo {
		margin-bottom: 2em;
	}
	.step a, .step input {
		font-size: 2em;
	}
	td input {
		font-size: 1.5em;
	}
	.step, th {
		text-align: right;
	}
	#footer {
		text-align: center;
		border-top: 1px solid #ccc;
		padding-top: 1em;
		font-style: italic;
	}
	-->
	</style>
</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>Добро пожаловать в WordPress. Перед тем, как мы начнем, потребуется некоторая информация. Вы должны знать следующее: </p>
<ol>
	<li>Имя базы данных</li>
	<li>Имя пользователя базы данных</li>
	<li>Пароль базы данных</li>
	<li>Сервер базы данных</li>
	<li>Префикс таблиц (если Вы хотите использовать несколько блогов в одной базе) </li>
</ol>
<p><strong>Если автоматическое создание файла не удалось, не переживайте. Все эти данные можно заполнить прямо в конфигурационном файле. Вы можете просто открыть <code>wp-config-sample.php</code> в текстовом редакторе, ввести Ваши данные и сохранить его как <code>wp-config.php</code>. </strong></p>
<p>Вероятнее всего, эта информация Вам была предоставлена Вашим провайдером. Если у Вас ее нет, Вам потребуется связаться с ними прежде чем мы сможем продолжить. А если все уже готово, <a href="setup-config.php?step=1">поехали</a>! </p>
<?php
	break;

	case 1:
		display_header();
	?>
</p>
<form method="post" action="setup-config.php?step=2">
	<p>Ниже Вы можете ввести подробности настроек подключения к базе данных. Если Вы не уверены, свяжитесь с Вашим провайдером. </p>
	<table>
		<tr>
			<th scope="row">База данных</th>
			<td><input name="dbname" type="text" size="25" value="wordpress" /></td>
			<td>Имя базы, в которую Вы хотите установить WP. </td>
		</tr>
		<tr>
			<th scope="row">Пользователь</th>
			<td><input name="uname" type="text" size="25" value="username" /></td>
			<td>Ваше имя пользователя в MySQL</td>
		</tr>
		<tr>
			<th scope="row">Пароль</th>
			<td><input name="pwd" type="text" size="25" value="password" /></td>
			<td>...и пароль.</td>
		</tr>
		<tr>
			<th scope="row">Сервер</th>
			<td><input name="dbhost" type="text" size="25" value="localhost" /></td>
			<td>С вероятностью 99% Вам не придется менять это значение.</td>
		</tr>
		<tr>
			<th scope="row">Префикс таблиц</th>
			<td><input name="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>Если Вы хотите запустить несколько блогов в одну базу, измените это.</td>
		</tr>
	</table>
	<h2 class="step">
	<input name="submit" type="submit" value="Послать" />
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
	require_once('../wp-includes/wp-db.php');
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
<p>Заработало! Вы успешно прошли первую часть настройки. WordPress теперь подключен к Вашей базе данных. Если Вы готовы продолжать, пора <a href="install.php">начинать установку!</a></p>
<?php
	break;
}
?>
<p id="footer"><a href="http://wordpress.org/">WordPress</a>, платформа персональных публикаций.</p>
</body>
</html>
