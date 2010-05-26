			<h3 class="title"><?php echo lang('admin_cp'); ?> - <?php echo lang('manage_categories'); ?></h3>

<?php if ($error){ ?>
			<h3 class="error"><div class="text"><?php echo $error; ?></div></h3>
<?php } else if($success){ ?>
			<h3 class="success"><div class="text"><?php echo $success; ?></div></h3>
<?php } ?>
			<form method="post">
<?php 
$categories = category();

// Create the delete list
foreach($categories as $category)
{ 
	foreach($categories as $acat)
	{ 
		if($acat['id'] == $category['id']){ continue; }
			
		$list[$category['id']] .= "<a href='{$config['url_path']}/admin.php?a=categories&delete={$category['id']}&method={$acat['id']}'>{$acat['name']}</a>";
		
		if((end($categories) != $acat))
		{
			$list[$category['id']] .= ", ";
		}
	}
}

foreach ($list as $id => $l)
{	
	if(substr($l, -2) == ", ")
	{
		$list[$id] = substr($l, 0, -2);
	}
}

// Create category management list
foreach($categories as $category)
{ 
?>
				<div class="plugin">
					<span class="status">
						<input type="text" name="order[<?php echo $category['id']; ?>]" value="<?php echo $category['order']; ?>" size="2" />
						<input type="checkbox" name="expanded[<?php echo $category['id']; ?>]" title="Expanded View?"<?php if($category['expanded']){ echo ' checked '; } ?>/>
						<input type="checkbox" name="aot[<?php echo $category['id']; ?>]" title="Admin / Moderator only Topics"<?php if($category['aot']){ echo ' checked '; } ?>/>
						<input type="checkbox" name="aop[<?php echo $category['id']; ?>]" title="Admin / Moderator only Posting"<?php if($category['aop']){ echo ' checked '; } ?>/>
					</span>
					
					<h3 class="name">
						<input type="text" name="name[<?php echo $category['id']; ?>]" value="<?php echo $category['name']; ?>" size="20" />
					</h3>
					<small>
						<a href="<?php echo $config['url_path']; ?>/admin.php?a=categories&delete=<?php echo $category['id']; ?>&method=all">Delete Category &amp; Posts</a> | 
						Delete Category &amp; Move posts to: <?php echo $list[$category['id']]; ?>
					</small>
				</div>
<?php
}	
?>
				<div class="plugin">
					<input type="submit" name="edit" value="edit" />
				</div>
			</form>
			<form method="post">
				<div class="plugin">
					<h3 class="name">Add Category:</h3>
					<dl class="input">
						<dt>
							<?php echo lang('name_c'); ?>
						</dt>
						<dd><input type="text" name="name" class="border" size="20" /></dd>
					</dl>
					<dl class="input">
						<dt>
							<?php echo lang('order_c'); ?>
						</dt>
						<dd><input type="text" name="order" class="border" value="<?php echo ++$category['order']; ?>" size="2" /></dd>
					</dl>
					<dl class="input">
						<dt>
							<?php echo lang('settings'); ?>
							<br /><br /><br /><br /><br />&nbsp;
						</dt>
						<dd><input type="checkbox" name="aot" id="aot" /> <label for="aot">Only admins and moderator topics?</label></dd>
						<dd><input type="checkbox" name="aop" id="aop" /> <label for="aop">Only admins and moderator posts?</label></dd>
					</dl>
					<input type="submit" name="add" value="add" />
				</div>
			</form>
			</table>
    	</td>
	</tr>
</table>
