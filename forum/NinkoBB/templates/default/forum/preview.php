<?php if(!$reply){ ?>
<?php 
load_hook('message_subject_before'); 
?>
	<h1 id="subject" class="title">
		<?php echo stripslashes(htmlentities($_POST['subject'])); ?> 
		<span class="info">(0 <?php echo lang('posts_c'); ?>)</span>
<?php 
load_hook('message_subject_inside'); 
?>
	</h1>
<?php 
load_hook('message_subject_after'); 
?>
<?php } ?>
	<div class="userinfo" id="p-000">
		<div class="right">
			<span title="<?php echo date($config['date_format'], (time() + $config['zone'])); ?>">
				<?php echo ago((time() + $config['zone'])); ?> ago
			</span> 
			#000
			<br />
<?php 
load_hook('message_user_right'); 
?>
		</div>
		
		<img src="<?php echo get_avatar($user_data['id']); ?>" class="avatar" alt="pic" />
		<?php echo $user_data['styled_name']; ?><br />
<?php 
if(is_loaded('titles')){ 
?>
		<?php echo get_title($user_data); ?> - 
<?php 
} 
?>
		<?php echo forum_count(false, $user_data['id'], 'user'); ?> posts
		
<?php 
load_hook('message_user_info_after'); 
?>
		<div class="clear"></div>
	</div>
	
	<div id="post">
<?php 
load_hook('message_before'); 
?>
		<?php echo parse($_POST['content']); ?>
<?php 
load_hook('message_after'); 
?>
	</div>
