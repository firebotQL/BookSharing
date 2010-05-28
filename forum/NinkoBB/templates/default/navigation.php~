<div id="navigation">
<?php 
if($_SESSION['logged_in']) { 
?>
	<h2 class="title">Hi, <?php echo $user_data['username']; ?></h2>
	<ul class="main">
		<li><a href="<?php echo $config['url_path']; ?>/users.php?a=home"><?php echo lang('user_cp'); ?></a></li>
<?php if($user_data['admin']) { ?>
		<li><a href="<?php echo $config['url_path']; ?>/admin.php"><?php echo lang('admin_cp'); ?></a></li>
<?php } ?>
		<li><a href="<?php echo $config['url_path']; ?>/logout.php"><?php echo lang('logout_c'); ?></a></li>
<?php load_hook('navigation_menu'); ?>
		<div class="clear"></div>
	</ul>
<?php } else { ?>
	<h2 class="title"><?php echo lang('login_c'); ?> or <a href="<?php echo $config['url_path']; ?>/register.php"><?php echo lang('register_c'); ?></a></h2>
	
<?php 	if($login_error){ ?>
	<div class="error main">
		<p class="text">
			<?php echo $login_error; ?>
		</p>
	</div>
<?php 	} ?>

	<form method="post">
		<ul class="login">
			<li><label><?php echo lang('username'); ?>:</label><input name="username" size="15" type="text" class="border" value="" /></li>
			<li><label><?php echo lang('password'); ?>:</label><input name="password" size="15" type="password" class="border" value="" /></li>
<?php load_hook('navigation_login'); ?>
			<li><input type="submit" name="login" value="<?php echo lang('login'); ?>" class="button"></li>
			<div class="clear"></div>
		</ul>
	</form>
<?php 
}

if($_SESSION['logged_in']) { 
load_hook('navigation_right'); 
?>
	<ul>
<?php 
	if($in_topic){
		if($closed && (!$_SESSION['admin'] || !$_SESSION['moderator'])){ 
?>
		<li><a href="" class="nav_link"><?php echo lang('closed'); ?></a></li>
<?php 
		} else { 
?>
		<li><a href="<?php echo $config['url_path']; ?>/message.php?page=<?php echo $page; ?>&amp;reply=<?php echo $topic['id']; ?><?php if($current_category != 0){ echo '&amp;category=' . $current_category; } ?>" class="nav_link"><?php echo lang('reply_c'); ?></a></li>
<?php 
		} 
	} else { 
?>
		<li><a href="<?php echo $config['url_path']; ?>/message.php?reply=0<?php if($current_category != 0){ echo '&amp;category=' . $current_category; } ?>" class="nav_link"><?php echo lang('start_new_topic'); ?></a></li>
<?php 	} ?>
		<div class="clear"></div>
	</ul>
<?php if($pagination){ /* echo "<h3 class='title'>Pagination</h3>" . lang('pages') . ": "; echo $pagination; */ } ?>
<?php } ?>
	<div class="clear"></div>
</div>