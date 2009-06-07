<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><div class="wrap">
	<h2>
	<?php if (isset ($file)) :?>
		<?php _e ('Drain Hole | Statistics for', 'drain-hole'); ?> <a href="<?php echo $file->url_ref ($hole); ?>" title="<?php echo htmlspecialchars ($hole->directory.'/'.$file->file) ?>" onclick="return edit_file(<?php echo $file->id ?>)"><?php echo $file->file ?></a>
	<?php else : ?>
		<?php _e ('Drain Hole | Downloads', 'drain-hole'); ?>
	<?php endif; ?>
	
	<a href="<?php echo $this->url () ?>/csv.php?id=<?php echo $file->id ?>&amp;type=stats" title="Download as CSV"><img src="<?php echo $this->url () ?>/images/csv.png" width="16" height="16" alt="CSV"/></a>
	</h2>
	
	<?php $this->submenu (true); ?>
	
	<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
	
	<?php if (count ($stats) > 0) : ?>
	<table class="files">
		<thead>
			<tr>
				<th><?php echo $pager->sortable ('created_at', __ ('Downloaded At', 'drain-hole')) ?></th>
				<?php if (!isset ($file)) : ?>
				<th><?php echo $pager->sortable ('file', __ ('File', 'drain-hole')) ?></th>
				<?php endif; ?>
				<th><?php echo $pager->sortable ('ip', __ ('IP', 'drain-hole')) ?></th>
				<th><?php echo $pager->sortable ('users', __ ('User', 'drain-hole')) ?></th>
				<th><?php echo $pager->sortable ('referrer', __ ('Referrer', 'drain-hole')); ?></th>
				<th width="16"></th>
			</tr>
		</thead>
		
		<?php if ($pager->total_pages () > 1) : ?>
		<tfoot>
			<tr>
				<td colspan="6">
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
		<?php foreach ($stats AS $pos => $stat) : ?>
		<tr id="stat_<?php echo $stat->id ?>"<?php if ($pos % 2 == 1) echo ' class="alt"' ?>>
			<td><?php echo date ('H:i', $stat->created_at - (get_option ('gmt_offset') * 60 * 60))?> - <?php echo date (str_replace ('F', 'M', get_option ('date_format')), $stat->created_at); ?></td>
			<?php if (!isset ($file)) : ?>
			<td><?php echo htmlspecialchars ($stat->file); ?></td>
			<?php endif; ?>
			<td><a href="http://ws.arin.net/whois/?queryinput=<?php echo $stat->ip ?>"><?php echo $stat->ip; ?></a></td>
			<td><?php echo $stat->user (); ?></td>
			<td><?php echo $stat->referrer_as_link () ?></td>
			<td><a href="#" onclick="return delete_stat(<?php echo $stat->id ?>)"><img src="<?php echo $this->url () ?>/images/delete.png" width="16" height="16" alt="Delete"/></a></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	
	<div id="loading" style="display: none">
		<img src="<?php echo $this->url () ?>/images/loading.gif" alt="loading" width="32" height="32"/>
	</div>
	<?php else : ?>
	<p><?php _e ('No files have been downloaded!', 'drain-hole'); ?></p>
	<?php endif; ?>
</div>

<div class="wrap">
	<h2><?php _e ('Clear all downloads', 'drain-hole'); ?></h2>
	<br/>
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">
		<input class="button-secondary" type="submit" name="clear_downloads" value="<?php _e ('Clear Downloads', 'drain-hole'); ?>"/>
	</form>
</div>