		<h3 class="title admin"><?php echo lang('admin_cp'); ?> - <?php echo lang('manage_plugins'); ?></h3>

<?php if ($error){ ?>
		<h3 class="error"><div class="text"><?php echo $error; ?></div></h3>
<?php } else if($success){ ?>
		<h3 class="success"><div class="text"><?php echo $success; ?></div></h3>
<?php } ?>
<?php 
foreach($plugins as $plugin)
{ 
	if(!is_array($plugin) || $plugin['name'] == "") { continue; }
		
	// Trim subject
	$content = htmlspecialchars(character_limiter(trim($plugin['description']), 50), ENT_QUOTES, 'UTF-8', false);
		
	// Build topic url
	$post_url = "{$config['url_path']}/read.php?id={$row['reply']}&page={$n}";
		
	// Is the plugin active?
	if(is_loaded($plugin['plugin']))
	{
		$status = true;
	}
	else
	{
		$status = false;
	}
	
	if($plugin['url'] != "")
	{
		$plugin['author'] = "<a href='{$plugin['url']}'>{$plugin['author']}</a>";
	}
		
	if(!isset($plugin['error']))
	{
?>
		<div class="plugin">
			<span class="status">
<?php if($status){ ?>
				<a href="<?php echo $config['url_path']; ?>/admin.php?a=plugins&deactivate=<?php echo $plugin['plugin']; ?>" class="active"><?php echo lang('deactivate'); ?></a>
<?php } else { ?>
				<a href="<?php echo $config['url_path']; ?>/admin.php?a=plugins&activate=<?php echo $plugin['plugin']; ?>" class="unactive"><?php echo lang('activate'); ?></a>
<?php } ?>
			</span>
			
			<h3 class="name"><?php echo $plugin['name']; ?></h3>
			<small><?php if($content){ echo $content; } ?> | Version: <?php echo $plugin['version']; ?> | Plugin by <?php echo $plugin['author']; ?></small>
		</div>
<?php
	}
	else
	{
?>
		<div class="plugin">
			<span class="status">
				<?php echo $plugin['error']; ?>
			</span>
			
			<h3 class="name"><?php echo $plugin['name']; ?></h3>
			<small>Version: <?php echo $plugin['version']; ?> | Plugin by <?php echo $plugin['author']; ?></small>
		</div>
<?php
	}
}	
?>
			</table>
    	</td>
	</tr>
</table>
