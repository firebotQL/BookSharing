<?php 
load_hook('topic_before'); 
?>
		<tr>
<?php 
load_hook('topic_info_before'); 
?>
			<td width="2%">
				<img src="<?php echo get_avatar($topic_author['id']); ?>" style="width: 35px; height: 35px;" alt="pic" />
			</td>
			<td class="subject">
				<div class="link"><?php if($sticky){ echo $sticky . ": "; } ?><a href="<?php echo $topic_url; ?>"><?php echo $subject; ?></a> <?php echo $closed; ?></div>
<?php 
load_hook('topic_subject'); 
?>
				<div class="by"><?php echo lang('by'); ?> <?php echo $topic_author['styled_name']; ?></div>
<?php 
load_hook('topic_subject_name'); 
?>
			</td>
			<td class="posts"><?php echo $posts; ?></td>
			<td class="last">
				<span title="<?php echo $last_post['date']; ?>"><?php echo $last_post['ago']; ?> ago</span><br />
				<span class="by"><?php echo lang('by'); ?> <?php echo $last_post_author['styled_name']; ?></span>
<?php 
load_hook('topic_info_after'); 
?>

		</tr>
<?php 
load_hook('topic_after'); 
?>