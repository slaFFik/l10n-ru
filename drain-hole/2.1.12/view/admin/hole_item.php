<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><td align="center" valign="top"><?php echo $hole->id ?></td>
<td id="hole_item_<?php echo $hole->id ?>">
	<a title="<?php _e ('View files', 'drain-hole'); ?>" href="edit.php?page=drain-hole.php&amp;files=<?php echo $hole->id ?>"><?php echo $hole->url ?></a>
	<?php if ($hole->files > 0) : ?>
	<span class="sub">(<?php printf (__ngettext ('%d file', '%d files', $hole->files, 'drain-hole'), number_format ($hole->files)); ?>)</span>
	<?php endif; ?>
</td>
<td align="center"><a href="edit.php?page=drain-hole.php&amp;sub=downloads&amp;hole=<?php echo $hole->id ?>"><?php echo number_format ($hole->hits); ?></a></td>

<td align="center">
	<a title="<?php _e ('Edit Drain Hole', 'drain-hole'); ?>" href="#" onclick="edit_hole(<?php echo $hole->id ?>);return false;"><img src="<?php echo $this->url () ?>/images/edit.png" alt="edit" width="16" height="16"/></a>
	<a href="#" onclick="edit_hole(<?php echo $hole->id ?>);return false;">edit</a>
</td>

<td align="center">
	<a title="<?php _e ('View files', 'drain-hole'); ?>" href="edit.php?page=drain-hole.php&amp;files=<?php echo $hole->id ?>"><img src="<?php echo $this->url () ?>/images/files.png" alt="files" width="16" height="16"/></a>
	<a title="<?php _e ('View files', 'drain-hole'); ?>" href="edit.php?page=drain-hole.php&amp;files=<?php echo $hole->id ?>"><?php _e ('files', 'drain-hole'); ?></a>
</td>

<td align="center">
	<a href="edit.php?page=drain-hole.php&amp;source=hole&amp;chart=<?php echo $hole->id ?>"><img src="<?php echo $this->url () ?>/images/chart.png" width="16" height="16" alt="Chart"/></a>
	<a href="edit.php?page=drain-hole.php&amp;source=hole&amp;chart=<?php echo $hole->id ?>"><?php _e ('view', 'drain-hole'); ?></a>
</td>

<td align="center">
	<a title="<?php _e ('Delete Drain Hole', 'drain-hole'); ?>" href="#" onclick="delete_hole(<?php echo $hole->id ?>);return false"><img src="<?php echo $this->url () ?>/images/delete.png" alt="delete" width="16" height="16"/></a>
	<a href="#" onclick="delete_hole(<?php echo $hole->id ?>);return false"><?php _e ('delete', 'drain-hole'); ?></a>
</td>
