<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><ul>
<?php foreach ($files AS $file) : $hole = DH_Hole::get ($file->hole_id); ?>
	<li><?php echo $file->url ($hole); ?> (<?php echo $file->hits ?>)</li>
<?php endforeach; ?>
</ul>