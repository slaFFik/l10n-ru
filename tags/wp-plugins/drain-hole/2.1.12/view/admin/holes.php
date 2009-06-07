<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<div class="wrap">
	<h2><?php _e ('Drain Hole | Holes', 'drain-hole') ?> <a href="<?php echo $this->url () ?>/csv.php?id=<?php echo $hole->id ?>&amp;type=holes" title="Download as CSV"><img src="<?php echo $this->url () ?>/images/csv.png" width="16" height="16" alt="CSV"/></a></h2>

	<?php $this->submenu (true); ?>
	
	<?php if (!isset ($options['kitten']) || $options['kitten'] == false) $this->render_admin ('kitten'); ?>
	<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
	
	<?php if (count ($holes) > 0) : ?>
	<table class="holes">
		<thead>
			<tr>
				<th><?php echo $pager->sortable ('id', 'ID') ?></th>
				<th align="left"><?php echo $pager->sortable ('name', 'Name') ?></th>
				<th><?php echo $pager->sortable ('hits', 'Hits') ?></th>
				<th><?php _e ('Edit', 'drain-hole'); ?></th>
				<th><?php _e ('Files', 'drain-hole'); ?></th>
				<th><?php _e ('Charts', 'drain-hole'); ?></th>
				<th><?php _e ('Delete', 'drain-hole'); ?></th>
			</tr>
		</thead>
		
		<?php if ($pager->total_pages () > 1) : ?>
		<tfoot>
			<tr>
				<td colspan="7">
			<div class="pagertools">
			<?php foreach ($pager->area_pages () AS $page) : ?>
				<?php echo $page ?>
			<?php endforeach; ?>
			</div>
				</td>
			</tr>
			
		</tfoot>
		<?php endif; ?>
		
		<tbody>
			<?php foreach ($holes AS $pos => $hole) : ?>
				<tr id="hole_<?php echo $hole->id ?>"<?php if ($pos % 2 == 1) echo ' class="alt"'?>>
					<?php $this->render_admin ('hole_item', array ('hole' => $hole)); ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
	
	<div id="loading" style="display: none">
		<img src="<?php echo $this->url () ?>/images/loading.gif" alt="loading" width="32" height="32"/>
	</div>
</div>
<br/>
<div class="wrap">
	<h2><?php _e ('New Drain Hole', 'drain-hole'); ?></h2>
	
	<p><?php _e ('A drain hole maps a URL path to a directory on your server.  Files placed within the directory are available under your chosen URL path.', 'drain-hole'); ?></p>

	<form method="post" action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>">
		<table width="100%">
			<tr>
			  <th valign="top" align="right" width="120"><?php _e ('URL', 'drain-hole') ?>:</th>
			  <td><input style="width: 95%" type="text" name="urlx" id="urlx" value="<?php echo htmlspecialchars ($base_url); ?>"/></td>
			</tr>
			<tr>
			  <th valign="top" align="right" ><?php _e ('Directory', 'drain-hole') ?>:<br/><span class="sub"><?php _e ('Relative to root', 'drain-hole') ?></span></th>
			  <td><input style="width: 95%" type="text" id="directoryx" name="directoryx" value="<?php echo htmlspecialchars ($base_directory); ?>"/>

				</td>
			</tr>
			<tr>
				<th></th>
				<td><input class="button-secondary" type="submit" name="create" value="<?php _e ('Create Drain Hole', 'drain-hole'); ?>" id="create"/></td>
			</tr>
		</table>
		
		<table class="example">
			<tr>
				<th>URL</th>
				<td><strong id="base_url"><?php echo htmlspecialchars ($base_url) ?></strong>/example.zip</td>
			</tr>
			<tr>
				<th><?php _e ('Directory', 'drain-hole'); ?></th>
				<td><strong id="base_dir"><?php echo htmlspecialchars ($base_directory); ?></strong>example.zip</td>
			</tr>
		</table>

		<br/>
		<div class="error" style="display: none" id="error_dir">
			<p><?php _e ('Your chosen <strong>directory</strong> is within a publicly accessible web directory.  Drain Hole <strong>will not be able to control access</strong> to files placed here unless a <code>.htaccess</code> file is placed in the directory.  Drain Hole will attempt to do this for you, but may not have permission to do so.  If this is the case then you will need to create this file yourself (<a href="#" onclick="$(\'htaccess\').toggle (); return false">click to view</a>)', 'drain-hole'); ?></p>
		</div>
		
		<br/>
		<div class="error" style="display: none" id="error_url">
			<p><?php _e ('Your chosen <strong>URL</strong> is outside of your WordPress site and as such Drain Hole <strong>may not be able to control access</strong> to files unless a <code>.htaccess</code> file is placed in the directory.  Drain Hole will attempt to do this for you, but may not have permission to do so.  If this is the case then you will need to create this file yourself (<a href="#" onclick="$(\'htaccess\').toggle (); return false">click to view</a>)','drain-hole'); ?></p>
		</div>
		
		<div class="updated" id="htaccess" style="display: none">
			<p><?php _e ('The following should be created in the directory given above:', 'drain-hole'); ?></p>
			<pre style="margin: 0px">
			<?php $this->render_admin ('htaccess', array ('index' => DH_Plugin::realpath (ABSPATH).'/index.php'))?>
			</pre>
		</div>

		<script type="text/javascript" charset="utf-8">
			var wp_dh_base_url  = '<?php echo htmlspecialchars ($base_url) ?>';
			var wp_dh_home_url  = '<?php echo htmlspecialchars ($home); ?>';
			var wp_dh_base_dir  = '<?php echo htmlspecialchars ($base_directory); ?>';
			var wp_dh_base_home = '<?php echo htmlspecialchars ($_SERVER['DOCUMENT_ROOT']) ?>';
			
			$('urlx').observe ('keyup', urlKey);
			$('directoryx').observe ('keyup', dirKey);
			
			update_url_warning (wp_dh_base_url);
			update_dir_warning (wp_dh_base_dir);
		</script>
	</form>
</div>