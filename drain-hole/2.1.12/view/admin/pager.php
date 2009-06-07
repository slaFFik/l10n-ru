<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><div id="pager" class="pager">
	<form method="get" action="<?php echo $this->url ($pager->url) ?>">
		<input type="hidden" name="page" value="drain-hole.php"/>
		<?php if (isset ($_GET['files'])) : ?>
		<input type="hidden" name="files" value="<?php echo $_GET['files'] ?>"/>
		<?php elseif (isset ($_GET['stats'])) : ?>
		<input type="hidden" name="stats" value="<?php echo $_GET['stats'] ?>"/>
		<?php elseif (isset ($_GET['version'])) : ?>
		<input type="hidden" name="version" value="<?php echo $_GET['version'] ?>"/>
		<?php endif; ?>
		<input type="hidden" name="sub" value="<?php echo $_GET['sub'] ?>"/>
		<input type="hidden" name="curpage" value="<?php echo $pager->current_page () ?>"/>

		<?php _e ('Search', 'drain-hole'); ?>: 
		<input type="text" name="search" value="<?php echo htmlspecialchars ($_GET['search']) ?>"/>

		<?php _e ('Per page', 'drain-hole') ?>: 
		<select name="perpage">
			<?php foreach ($pager->steps AS $step) : ?>
		  	<option value="<?php echo $step ?>"<?php if ($pager->per_page == $step) echo ' selected="selected"' ?>><?php echo $step ?></option>
			<?php endforeach; ?>
		</select>
		
		<input type="submit" name="go" value="<?php _e ('go', 'drain-hole') ?>"/>
	</form>
</div>
