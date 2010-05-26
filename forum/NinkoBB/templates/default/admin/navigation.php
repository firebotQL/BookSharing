	<div class="container">
		<h3 class="title admin"><?php echo lang('navigation'); ?></h3>
		
		<ul class="admin">
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=home" class="<?php echo equals($action, "home", "menu-current", "menu"); ?>"><?php echo lang('home_c'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=settings" class="<?php echo equals($action, "settings", "menu-current", "menu"); ?>"><?php echo lang('forum_settings'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=users" class="<?php echo equals($action, "users", "menu-current", "menu"); ?>"><?php echo lang('manage_users'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=categories" class="<?php echo equals($action, "categories", "menu-current", "menu"); ?>"><?php echo lang('manage_categories'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=topics" class="<?php echo equals($action, "topics", "menu-current", "menu"); ?>"><?php echo lang('manage_topics'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=posts" class="<?php echo equals($action, "posts", "menu-current", "menu"); ?>"><?php echo lang('manage_posts'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=plugins" class="<?php echo equals($action, "plugins", "menu-current", "menu"); ?>"><?php echo lang('manage_plugins'); ?></a></li>
<?php 
load_hook('admin_navigation'); 
?>
			<div class="clear"></div>
		</ul>