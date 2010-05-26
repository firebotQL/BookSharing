<div class="container">
<?php if($starter){ ?>
<?php 
load_hook('message_subject_before'); 
?>
	<h1 id="subject" class="title">
		<a href="<?php echo $config['url_path']; ?>"><?php echo lang('home_c'); ?></a> &raquo; 
		<a href="<?php echo $config['url_path']; ?>?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a> &raquo; 
<?php if($post['status']){ ?>
		<span class="status">
			<?php echo $post['status']; ?>
		</span>
<?php } ?>
		<?php echo htmlspecialchars($post['subject'], ENT_QUOTES, 'UTF-8'); ?>
		<span class="info">(<?php $replies = forum_count(false, $post['id'], false); echo $replies; ?> <?php if($replies == 1){ echo lang('post_c'); } else { echo lang('posts_c'); } ?>)</span>
		<?php if($pagination){ ?><span class="info">(<?php echo lang('pages'); ?> <?php echo $pagination; ?>)</span><?php } ?>
<?php 
load_hook('message_subject_inside'); 
?>
	</h1>
<?php 
load_hook('message_subject_after'); 
?>
<?php } ?>
	<div class="userinfo" id="p-<?php echo $post['id']; ?>">
		<div class="right">
			<span title="<?php echo date($config['date_format'], ($post['time'] + $config['zone'])); ?>">
				<?php echo ago(($post['time'] + $config['zone'])); ?> ago
			</span> 
			<a href="<?php echo $config['url_path']; ?>/read.php?id=<?php echo $topic['id']; if($page){ echo "&page={$page}"; } ?>#p-<?php echo $post['id']; ?>">#<?php echo $post['id']; ?></a>
			<br />
<?php 
load_hook('message_user_right'); 
?>
<?php 
if($_SESSION['admin'] || $_SESSION['moderator']){ 
	if($starter){ 
?>
			<a href="message.php?edit=<?php echo $post['id']; ?>"><?php echo lang('edit'); ?></a> - 
			<a href="read.php?delete_topic=<?php echo $post['id']; ?>"><?php echo lang('delete'); ?></a> - 
<?php 
	} else { 
?>
			<a href="message.php?edit=<?php echo $post['id']; ?>"><?php echo lang('edit'); ?></a> - 
			<a href="read.php?delete=<?php echo $post['id']; ?>"><?php echo lang('delete'); ?></a> - 
<?php 
	} 
} else if($user_data['id'] == $author['id']){ 
?>
<?php 
	if($starter){ 
?>
			<a href="message.php?edit=<?php echo $post['id']; ?>"><?php echo lang('edit'); ?></a> - 
			<a href="read.php?delete_topic=<?php echo $post['id']; ?>"><?php echo lang('delete'); ?></a> - 
<?php 
	} else { 
?>
			<a href="message.php?edit=<?php echo $post['id']; ?>"><?php echo lang('edit'); ?></a> - 
			<a href="read.php?delete=<?php echo $post['id']; ?>"><?php echo lang('delete'); ?></a> - 
<?php
	} 
} 

if($_SESSION['logged_in']) { 
?>
			<a href="message.php?page=<?php echo $page; ?>&amp;reply=<?php echo $topic['id']; ?>&amp;q=<?php echo $post['id']; ?>"><?php echo lang('quote'); ?></a> - 
			<a id="qq" alt="<?php echo $post['id']; ?>" name="<?php echo $author['username']; ?>" value="<?php echo br2nl(parse($post['message'], false)); ?>"><?php echo lang('quick_quote'); ?></a>
<?php 
} 
?>
		</div>
		
		<img src="<?php echo $avatar_url; ?>" class="avatar" alt="pic" />
		<?php echo $author['styled_name']; ?><br />
<?php 
if(is_loaded('titles')){ 
?>
		<?php echo get_title($author); ?> - 
<?php 
} 
?>
		<?php echo forum_count(false, $author['id'], 'user'); ?> posts
		
<?php 
load_hook('message_user_info_after'); 
?>
		<div class="clear"></div>
	</div>
	
	<div id="post">
<?php 
load_hook('message_before'); 
?>
		<?php echo parse($post['message']); ?>
<?php 
load_hook('message_after'); 
?>
	</div>
</div>