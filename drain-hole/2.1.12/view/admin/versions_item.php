<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><td align="center"><?php echo $version->id?></td>
<td align="center"><?php echo $version->version?></td>
<td align="center"><?php echo $version->hits?></td>
<td align="center"><?php echo date (get_option ('date_format'), $version->created_at); ?></td>
<td><?php echo htmlspecialchars ($version->reason); ?></td>
<td align="center">
	<?php if ($file->has_version ($version->id, $hole)) : ?>
		<?php _e ('Yes', 'drain-hole'); ?>
	<?php else : ?>
		<?php _e ('No', 'drain-hole'); ?>
	<?php endif; ?>
</td>
<td align="center"><a href="#" onclick="return edit_version(<?php echo $version->id ?>)">edit</a> <a href="#" onclick="return edit_version(<?php echo $version->id ?>)"><img src="<?php echo $this->url () ?>/images/edit.png" width="16" height="16" alt="Edit"/></a></td>
<td align="center" width="16">
	<?php if ($version->id != $file->version_id) : ?>
	<a href="#delete" onclick="return delete_version(<?php echo $version->id ?>)"><img src="<?php echo $this->url () ?>/images/delete.png" width="16" height="16" alt="Delete"/></a>
	<?php endif; ?>
</td>