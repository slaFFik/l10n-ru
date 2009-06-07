<?php

class DH_Widget extends Widget_DH
{
	var $title  = '';
	var $count  = 5;
	
	function has_config () { return true; }
	
	function load ($config)
	{
		if (isset ($config['title']))
			$this->title = $config['title'];
			
		if (isset ($config['count']))
			$this->count = $config['count'];
			
		if ($this->count == 0)
			$this->count = 5;
	}
	
	function display ($args)
	{
		extract ($args);

		echo $before_widget;
		if ($this->title)
			echo $before_title.$this->title.$after_title;

		the_drainhole_stats ($this->count);
		echo $after_widget;
	}
	
	function config ($config, $pos)
	{
		?>
		<table>
			<tr>
				<th><?php _e ('Title', 'drain-hole') ?>:</th>
				<td><input type="text" name="<?php echo $this->config_name ('title', $pos) ?>" value="<?php echo htmlspecialchars ($config['title']) ?>"/></td>
			</tr>
			<tr>
				<th><?php _e ('Count', 'drain-hole') ?>:</th>
				<td><input type="text" name="<?php echo $this->config_name ('count', $pos) ?>" value="<?php echo $config['count'] ?>"/></td>
			</tr>
		</table>
		<?php
	}
	
	function save ($data)
	{
		return array ('title' => $data['title'], 'count' => intval ($data['count']));
	}
}

?>