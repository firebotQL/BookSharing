	<div class="container">
		<h3 class="title user"><?php echo lang('navigation'); ?></h3>
		
		<ul class="users">
			<li><a href="<?php echo $config['url_path']; ?>/users.php?a=home" class="<?php echo equals($action, "home", "menu-current", "menu"); ?>"><?php echo lang('home_c'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/users.php?a=account" class="<?php echo equals($action, "account", "menu-current", "menu"); ?>"><?php echo lang('edit_account'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/users.php?a=avatar" class="<?php echo equals($action, "avatar", "menu-current", "menu"); ?>"><?php echo lang('edit_avatar'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/users.php?a=profile" class="<?php echo equals($action, "profile", "menu-current", "menu"); ?>"><?php echo lang('edit_profile'); ?></a></li>
<?php 
load_hook('user_navigation'); 
?>
			<div class="clear"></div>
		</ul>
		
		<div class="clear"></div>