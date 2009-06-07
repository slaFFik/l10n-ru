<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<td align="center"><?php echo $file->id ?></td>
<td>
	<a href="<?php echo $file->url_ref ($hole); ?>" title="<?php echo htmlspecialchars ($hole->directory.'/'.$file->file) ?>" onclick="return edit_file(<?php echo $file->id ?>)"><?php echo $file->name () ?></a>
</td>
<td>
	<a href="edit.php?page=drain-hole.php&amp;version=<?php echo $file->id ?>"><?php echo $file->version; ?></a>
</td>
<td align="center"><a href="edit.php?page=drain-hole.php&amp;stats=<?php echo $file->id ?>"><?php echo $file->hits ?></a></td>
<td>
	<?php if ($file->exists ($hole)) echo date (get_option('date_format'), $file->updated_at); else echo '<span class="missing">'.__ ('File is missing','drain-hole').'</span>'; ?>
</td>
<td align="center">
	<a href="#newversion" onclick="return new_version (<?php echo $file->id ?>)"><img src="<?php echo $this->url () ?>/images/add.png" width="16" height="16" alt="Add"/></a>
	<a href="#newversion" onclick="return new_version (<?php echo $file->id ?>)">new</a>
</td>
<td align="center">
	<a href="edit.php?page=drain-hole.php&amp;source=hole&amp;chart=<?php echo $file->id ?>"><img src="<?php echo $this->url () ?>/images/chart.png" width="16" height="16" alt="Chart"/></a>
	<a href="edit.php?page=drain-hole.php&amp;source=file&amp;chart=<?php echo $file->id ?>">view</a>
</td>
<td width="16"><a href="#" onclick="delete_file(<?php echo $file->id ?>); return false"><img src="<?php echo $this->url () ?>/images/delete.png" width="16" height="16" alt="Delete"/></a></td>
