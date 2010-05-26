		<h3 class="title"><?php echo lang('user_cp'); ?></h3>
		
		<div class="content">
			<?php echo lang('user_welcome'); ?>
		</div>
		
		<h3 class="title"><?php echo lang('your_activity'); ?></h3>
		
		<div class="content">
			<dl class="input">
				<dt><?php echo lang('joined'); ?></dt>
				<dd><?php echo date($config['date_format'], $user_data['join_date']); ?></dd>
			</dl>
		</div>
		
		<div class="content">
			<dl class="input">
				<dt>
					<?php echo lang('last_visit'); ?><br />
					<span><?php echo lang('last_visit_msg'); ?></span>
				</dt>
				<dd><?php echo date($config['date_format'], $user_data['last_seen']); ?></dd>
			</dl>
		</div>
		
		<div class="content">
			<dl class="input">
				<dt>
					<?php echo lang('total_posts'); ?><br />
					<span><?php echo lang('total_posts_msg'); ?></span>
				</dt>
				<dd><?php echo forum_count(false, $user_data['id'], 'user'); ?></dd>
			</dl>
		</div>
		
		<div class="content">
			<dl class="input">
				<dt>
					<?php echo lang('last_post'); ?><br />
					<span><?php echo lang('last_post_msg'); ?></span>
				</dt>
				<dd>
<?php
$last = last_post(false, $user_data['id']);

if($last['reply'] == 0)
{
	echo "Topic: <a href='{$config['url_path']}/read.php?id={$last['id']}'>" . stripslashes(htmlspecialchars($last['subject'])) . "</a>";
}
else
{
	$topic_data = topic($last['reply']);
	
	echo "Posted in Topic: <a href='{$config['url_path']}/read.php?id={$topic_data['id']}'>" . stripslashes(htmlspecialchars($topic_data['subject'])) . "</a>";
	
}
?>
				</dd>
			</dl>
		</div>
		
		<div class="clear"></div>
	</div>