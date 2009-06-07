<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<div class="wrap">
<?php
	if (!isset($rurumo)) die();
	if (isset($_GET['update'])) $name = $_GET['update']; else die();
	
	$update = $rurumo[$name];
	$errors = array ();
		
//	Скачиваем файл
	if ( ! $wp_filesystem || !is_object($wp_filesystem) ) WP_Filesystem();
	if ( $wp_filesystem->is_dir($working_dir) ) $wp_filesystem->delete($working_dir, true);
	$base = $wp_filesystem->get_base_dir();
	$working_dir = $base . 'wp-content/upgrade/' . basename($_GET['update']);
	
	$file = download_url($update->package);
	
	if ( is_wp_error($file) ) {
		$errors[] = 'Не удалось скачать файл';
	}
	else {
		$result = unzip_file($file, $working_dir);	
		if ( is_wp_error($result) ) {
			$errors[] = 'Не удалось разархивировать архив';
		}
		else {
		//	Обновление
			$deleted = $wp_filesystem->delete($base . PLUGINDIR . '/' .$name);	
			if (!rurumo_copy_dir($working_dir, $base.PLUGINDIR)) $errors[] = 'Не удалось скопировать файлы обновления';
		}
	//	Удаление временных файлов 
		$wp_filesystem->delete($working_dir, true);
		unlink($file);
	}
//	Отчет о проджеланной работе
	if (count($errors))	{
		echo "<h2>По время обновления произошли ошибки</h2>";
		foreach ($errors as $error) echo "<p>{$error}</p>";
		echo '.';
	}
	else {
		$rurumo[$name]->installed = true;
		update_option('rurumo',serialize($rurumo));

		echo "<h2>Обновление плагина успешно завершено</h2>";
		if (file_exists($update->report)) readfile($update->report);
	}
?>
</div>