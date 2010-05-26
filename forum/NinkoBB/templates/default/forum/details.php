	<div class="container">
		<h3 class="title"><?php echo lang('users_online'); ?></h3>
	
		<div class="content">
<?php if(is_loaded('guest_counter')){ ?>
			<?php echo lang('guests_online'); ?>: <?php echo $guests_online_data['count']; ?>, 
<?php } ?>
			<?php echo lang('users_online'); ?>: <?php echo $online_data['count']; ?>,  
			<?php echo lang('admins_online'); ?>: <?php echo $admin_online_data['count']; ?><br /><br />
			
			<?php if($online_data['users']){ ?><?php echo $online_data['users']; ?><?php } else { echo lang('no_online'); } ?>
			<?php if(is_loaded('guest_counter')){ if($bots_online_data['users']){ ?>, <?php echo $bots_online_data['users']; ?><?php } } ?>
		</div>
	</div>
	
	<div class="container">
		<h3 class="title"><?php echo lang('forum_statistics'); ?></h3>
	
		<div class="content">
			<?php echo lang('registered_users'); ?>: <?php echo $user_count; ?>, 
			<?php echo lang('topics_c'); ?>: <?php echo $topic_count; ?>, 
			<?php echo lang('posts_c'); ?>: <?php echo $post_count; ?>
		</div>
	</div>