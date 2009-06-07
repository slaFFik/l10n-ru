<?php
	function exclude_cb_checked(){
		if(strcmp(get_option("YATCP_EXCLUDE_TRACKBACKS"), "true") == 0){
			echo "checked='checked'";
		}
	}
	
	function hide_cb_checked(){
		if(strcmp(get_option("YATCP_HIDE_TRACKBACKS"), "true") == 0){
			echo "checked='checked'";
		}
	}

	function verbose_rb_selected($value){
		if(strcmp(get_option("YATCP_VERBOSE_WARNING"), $value) == 0){
			echo "checked='checked'";
		}
	}

?>


<?php if ( !empty($_POST ) ) : 
	$tb_enabled = $_POST["exclude_tb"];
	$hide_tb = $_POST["hide_tb"];
	$max_depth = $_POST["max_depth"];
	$verbose_warning = $_POST["verbose_warning"];

	//Checkboxes are not submitted, when unchecked
	if(empty($tb_enabled)){
		update_option("YATCP_EXCLUDE_TRACKBACKS", 'false');
	}
	else {
		update_option("YATCP_EXCLUDE_TRACKBACKS", 'true');
	}

	if(empty($hide_tb)){
		update_option("YATCP_HIDE_TRACKBACKS", 'false');
	}
	else {
		update_option("YATCP_HIDE_TRACKBACKS", 'true');
	}

	//take the value put for max. depth only if it is at least a number
	if(is_numeric($max_depth)){
		update_option("YATCP_MAX_COMMENT_DEPTH", $max_depth);
	}
	else {
		//otherwise fall back to default
		update_option("YATCP_MAX_COMMENT_DEPTH", '4');
	}

	if(strcmp($verbose_warning, "true")==0) {
		update_option("YATCP_VERBOSE_WARNING", 'true');
	}
	else {
		update_option("YATCP_VERBOSE_WARNING", 'false');
	}
		
	
?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
	<h2><?php _e('YATCP Options', 'yatcp'); ?></h2>
		<form action="" method="post" id="yatcp_conf">
			<h3><?php _e('Comments nesting Level', 'yatcp'); ?></h3>
			<p><?php _e('Decide how deep comments should be nested. This equals to the number of lines shown on the left in the Default style. When the maximum depth is reached Comments are not shown indented anymore but just below each other (as it is Wordpress Standard). A warning <em>"Comments won\'t nest below this level"</em> is shown for Comments on the maximum level.', 'yatcp'); ?></p>
			<label for="max_depth"><?php _e('Max. Comment nesting depth:', 'yatcp'); ?></label>
			<input type="text" name="max_depth" id="max_depth" size="3" value="<?php echo get_option("YATCP_MAX_COMMENT_DEPTH");?>" /> <br/>
			
			<h3><?php _e('Exclude Trackbacks/Pingbacks?', 'yatcp'); ?></h3>
			<p><?php _e('Decide whether Trackbacks/Pingbacks should be shown in the comments dropdown to write a reply to them. Additionally when set to \'off\' there willl also be no \'Reply\' Link behind Trackbacks or Pingbacks.', 'yatcp'); ?></p>
			<label for="exclude_tb"><?php _e('Prohibit Answers to Trackbacks/Pingbacks:', 'yatcp'); ?></label>
			<input type="checkbox" name="exclude_tb" id="exclude_tb" value="true" <?php exclude_cb_checked();?> /> <br/>

			<h3><?php _e('Hide Trackbacks/Pingbacks?', 'yatcp'); ?></h3>
			<p><?php _e('Decide whether Trackbacks/Pingbacks should be completly hidden, i.e. not even show up in the Comments List at all. This implies the behaviour described by \'Exclude Trackbacks\', so Trackbacks/Pingbacks won\'t be shown in the Reply Drowdown. <em>Please note that this also influences the number of comments shown everywhere!</em>', 'yatcp'); ?></p>
			<label for="hide_tb"><?php _e('Completely hide Trackbacks/Pingbacks:', 'yatcp'); ?></label>
			<input type="checkbox" name="hide_tb" id="hide_tb" value="true" <?php hide_cb_checked();?> /> <br/>

			<h3><?php _e('Verbose nesting warning?', 'yatcp'); ?></h3>
			<p><?php _e('Decide whether the warning <em>"Comments won\'t nest below this level"</em> is shown for <em>every</em> comment nested too deep, or only for the ones reaching maximum nesting depth.', 'yatcp'); ?></p>
			<input type="radio" name="verbose_warning" id="verbose_warning_yes" value="true" <?php verbose_rb_selected('true');?> /> 
			<label for="verbose_warning_yes"><?php _e('Show warning for every Comment above maximum nesting depth', 'yatcp'); ?></label><br/>
			<input type="radio" name="verbose_warning" id="verbose_warning_no" value="false" <?php verbose_rb_selected('false');?> /> 
			<label for="verbose_warning_no"><?php _e('Show warning only for Comments on maximum nesting depth', 'yatcp'); ?></label><br/>

			<p class="submit">
				<input type="submit" value="<?php _e('Update Options &raquo;'); ?>	" />
			</p>
		</form>
</div>
