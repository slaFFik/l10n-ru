<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><div class="wrap">
	<h2><?php printf (__ ('Version history for %s', 'drain-hole'), '<a href="edit.php?page=drain-hole.php&files='.$file->hole_id.'">'.$file->name ().'</a>'); ?></h2>

	<?php $this->submenu (true); ?>
	<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
		
	<?php if (count ($versions) > 0) : ?>
		<table class="holes">
			<thead>
			<tr>
				<th><?php echo $pager->sortable ('id', __ ('ID', 'drain-hole')) ?></th>
				<th><?php echo $pager->sortable ('version', __ ('Version', 'drain-hole')) ?></th>
				<th><?php echo $pager->sortable ('hits', __ ('Hits', 'drain-hole')) ?></th>
				<th><?php echo $pager->sortable ('created_at', __ ('Created', 'drain-hole')) ?></th>
				<th align="left"><?php echo $pager->sortable ('reason', __ ('Reason for version', 'drain-hole')) ?></th>
				<th><?php _e ('File', 'drain-hole'); ?></th>
				<th><?php _e ('Edit', 'drain-hole'); ?></th>
				<th width="16"></th>
			</tr>
			</thead>

			<tbody>
			<?php foreach ($versions as $pos => $version): ?>
				<tr id="version_<?php echo $version->id ?>"<?php if ($pos % 2 == 1) echo ' class="alt"' ?>>
					<?php $this->render_admin ('versions_item', array ('version' => $version, 'file' => $file, 'hole' => $hole)); ?>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
		
		<?php if ($pager->total_pages () > 1) : ?>
			<div class="pagertools">
			<?php foreach ($pager->area_pages () AS $page) : ?>
				<?php echo $page ?>
			<?php endforeach; ?>
			</div>

		<?php endif; ?>
	<?php else : ?>
		<p><?php _e ('There are no versions to display!', 'drain-hole'); ?></p>
	<?php endif; ?>
</div>

<div class="wrap">
	<h2><?php _e ('Add to history', 'drain-hole'); ?></h2>
	<p><?php _e ('You can add to the history of a file.  This allows you to include some history of a file before Drain Hole was used', 'drain-hole'); ?></p>
	
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">
		<table width="100%">
			<tr>
				<th align="right" width="80"><?php _e ('Version', 'drain-hole'); ?>:</th>
				<td><input style="width: 35%" type="text" name="version" value=""/></td>
			</tr>
			<tr>
				<th valign="top" align="right" width="80"><?php _e ('Reason', 'drain-hole'); ?>:</th>
				<td><textarea name="reason" style="width: 95%"></textarea></td>
			</tr>
			<tr>
				<th align="right"><?php _e ('Date', 'drain-hole'); ?>:</th>
				<td>
					<input size="2" type="text" name="day" value="<?php echo date ('j') ?>"/> /
					<input size="2" type="text" name="month" value="<?php echo date ('n') ?>"/> /
					<input size="4" type="text" name="year" value="<?php echo date ('Y') ?>"/> <?php _e ('(D/M/Y)', 'drain-hole'); ?>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><input type="submit" name="save" value="<?php _e ('Save', 'drain-hole'); ?>"/></td>
			</tr>
		</table>
	</form>
</div>