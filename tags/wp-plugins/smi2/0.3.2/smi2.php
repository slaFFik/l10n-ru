<?php
/*
Plugin Name: SMI2
Plugin URI: http://salpagarov.ru/tag/smi2
Description: Интеграция блога с сервисами SMI2.
Author: Sol
Version: 0.3.2
Author URI: http://salpagarov.ru
*/

class smi2 {
	var $page_title;
	var $short_description;
	var $menu_title;
	var $access_level;
	var $add_page_to;
	var $API;
	var $toodoo_user;
	var $toodoo_avatar;
	
	/**
	 * Добавляем пункт в меню
	 *
	 */
	function add_admin_menu() {
		if     ( $this->add_page_to == 1 ) add_menu_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
		elseif ( $this->add_page_to == 2 ) add_options_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
		elseif ( $this->add_page_to == 3 ) add_management_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
		elseif ( $this->add_page_to == 4 ) add_theme_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
	}

	/**
	 * Инициализация
	 *
	 */
	function init () {
		$this->menu_title        = 'SMI2';
		$this->page_title        = 'Настройки SMI2';
		$this->add_page_to       = 2; // В подменю "настройки"
		$this->access_level      = 5; // Уровень доступа - администратор

		$this->get_options ();
		$plugin = 'smi2/smi2.php';

		add_action ('activate_'   . $plugin, array($this, 'activate'));
		add_action ('deactivate_' . $plugin, array($this, 'deactivate'));
		add_action ('admin_menu', array($this, 'add_admin_menu'));
		add_action ('private_to_published', array($this, 'publish'));
		add_action ('admin_notices', array($this, 'status'));
		add_action ('admin_menu', array($this, 'add_custom_box'));
		add_action ('the_content', array($this, 'see_also'));
		
	}

	/**
	 * Чтение настроек
	 *
	 */
	function get_options() {
		foreach (array('name','email','password','count') as $option) $this->$option = get_option('smi2_'.$option);
	}


	function activate() {
        add_option ('smi2_email', '', 'Login email');
		add_option ('smi2_password', '', 'Password');
		add_option ('smi2_name', '', 'Nickname');
	}

	function add_custom_box() {
		if ( function_exists('add_meta_box') )
			add_meta_box ('smi2', $this->menu_title, array($this, 'ask'), 'post', 'advanced' );
		else
			add_action ('dbx_post_advanced', array($this, 'ask'));
	}

	function ask () {
		if ( !function_exists('add_meta_box') ) echo '
			<div id="smi2" class="postbox if-js-closed">
			<h3>SMI2</h3>
			<div class="inside">';
		echo '
				<select name="group">
					<option value="0">Не публиковать</option>
					<option value="1">Технологии</option>
					<option value="2">Наука</option>
					<option value="3">Бизнес</option>
					<option value="4">Развлечения</option>
					<option value="5">Спорт</option>
					<option value="6">Политика</option>
					<option value="7">Происшествия</option>
					<option value="8">Другое</option>
					<option value="9">Финансы</option>
					<option value="10">Медицина</option>
					<option value="11">Недвижимость</option>
					<option value="12">Культура</option>
					<option value="13">Интернет</option>
					<option value="14">Авто</option>
					<option value="15">Игры</option>
					<option value="16">Реклама</option>
					<option value="17">Юмор</option>
					<option value="18">Общество</option>
				</select>
				<p>Используя кросспостинг, блоггер обязуется показывать блок ссылок по теме. В противном случае администрация сайта СМИ2 может отключить возможность кросспостинга для данного пользователя.</p>';
		if ( !function_exists('add_meta_box') ) echo '
			</div>
			</div>
		';
	}
	
	function deactivate() {
	//	Хорошо бы вернуть структуру базы взад и удалить настройки
		foreach (array('name','email','password','count') as $option) delete_option('smi2_'.$option);
	}

	function admin_page() {
		echo "<div class='wrap'>";
		echo "<h2>SMI2.wordpress</h2>";

		if (isset($_POST['UPDATE'])) {
            echo "<div class='updated'>Настройки сохранены</div><p>";

			if (isset($_POST['name'])) update_option ('smi2_name',$_POST['name']);
			if (isset($_POST['email'])) update_option ('smi2_email',$_POST['email']);
			if (isset($_POST['password'])) update_option ('smi2_password',$_POST['password']);
			if (isset($_POST['count'])) update_option ('smi2_count',$_POST['count']);
		}
		
		$this->get_options ();

		echo "<form action='' method='POST'>";
		echo "<h3>Настройки доступа к SMI2</h3>";
		
		echo "<b>Имя пользователя</b><br />";
		echo "<input type='text' name='name' size='40' value='{$this->name}'><br /><br />";

		echo "<b>E-mail</b><br />";
		echo "<input type='text' name='email' size='40' value='{$this->email}'><br /><br />";

		echo "<b>Пароль</b><br />";
		echo "<input type='text' name='password' size='40' value='{$this->password}'><br /><br />";

		echo "<b>Количество статей</b><br />";
		echo "<input type='text' name='count' size='10' value='{$this->count}'><br /><br />";

		echo "<input type='submit' name='UPDATE' value='Готово'>";
		echo "</form>";

		echo '</div>';
	}
	
	function publish ($ID) {
		global $rss;
		
		if ($_POST['group'] == '0') return;
		
		$group = $_POST['group'];		
		$post = &get_post($ID);
		
		$excerpt = $post->post_excerpt;
		$thumbnail = get_post_meta($ID,'thumbnail',true);

		$tagline = '';
		if (count(get_the_tags($ID))) {
			foreach (get_the_tags($ID) as $tag) $tagline .= ','.$tag->name;
			$tagline = substr($tagline,1);
		}
		$count = $this->count;

		include_once(ABSPATH . WPINC . '/rss.php');

		$rss = @fetch_rss("http://smi2.ru/export/rss/actual.php?tags=$tagline&count=$count");
		if ( !empty($rss->items) ) {
			$smi2_str = "<ul class='smi2'>";
			foreach ( $rss->items as $item )
				$smi2_str .= "<li><a href='".$item['link']."?master=".urlencode($this->name)."'>".$item['title']."</a></li>";
			$smi2_str .= "</ul>";
		}

		$smi2_str .= "</ul>";
		add_post_meta($ID,'smi2',$smi2_str);
		
	    $url  = "http://smi2.ru/post/";
		$vars = array (
			'email' => $this->email,
			'password' => $this->password,
			'precaption' => $post->post_title,
			'pretext' => $excerpt,
			'preurl' => get_permalink($ID),
			'pretags' => $tagline,
			'prepic' => $thumbnail,
			'group' => $group
		);
	    if (function_exists('curl_init')) {
	      	$ch = curl_init();
	      	curl_setopt($ch, CURLOPT_URL, $url);
	      	curl_setopt($ch, CURLOPT_POST, 1);
	      	curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    	curl_setopt($ch, CURLOPT_TIMEOUT, 5 );
	    	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	    	$content = curl_exec( $ch );
	
	    	curl_close($ch);
	    	
		    if (ereg("\<status\>([^\<]+)\<\/status\>",$content, $token)) {
		    	$status = $token[1];
		    }
	
		    if (ereg("\<url\>([^\<]+)\<\/url\>",$content, $token2)) {
		    	$SMI2_url = $token2[1];
		    }
		    
			if ($status='OK') add_post_meta($ID,'smi2_url',$SMI2_url);
			add_post_meta($ID,'smi2_responce',$content);
	    }
	    
	}
	
	function status () {
		if (isset($_GET['post'])) {
			$ID=$_GET['post'];
			$smi2_url = get_post_meta($ID,'smi2_url',true);
			if ($smi2_url != '') 
				$message="Опубликовано <a href='$smi2_url'>здесь</a>"; 
			else 
				$message="Эта запись не была опубликована.";
				
			echo '<div id="smi2" class="updated fade"><p>СМИ2: '.$message.'</p></div>';
		}
	}
	
	function see_also($content) {
		$meta = get_post_meta(get_the_ID(), 'smi2', true);
		if ( !empty($meta) )
			$content .= "<h3 class='smi2'>Смотрите на СМИ2:</h3>".$meta;
		return $content;
	}
}

$smi2 = new smi2 ();
$smi2-> init ();
?>
