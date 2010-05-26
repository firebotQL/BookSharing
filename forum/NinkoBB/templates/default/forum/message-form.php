<form name="post" method="post">
<input type="hidden" value="<?php echo $reply; ?>" name="reply">
<div class="container">
	<h3 class="title"><?php echo $title; ?></h3>
	
<?php 
if ($error){ 
?>
	<h3 class="error"><span class="text"><?php echo $error; ?></span></h3>
<?php 
} 
?>

	<div class="content">
		<?php echo lang('subject_c'); ?>:<br />
<?php load_hook('msg_subject_before'); ?>
		<input type="text" name="subject" class="border" style="width: 88%" value="<?php echo switchs(field_clean($_POST['subject']), $subject); ?>" /> 
<?php if($reply){ ?>
		<input name="post" value="reply" type="submit" class="button rounded" />
<?php } else if($edit){ ?>
		<input name="edit" value="edit" type="submit" class="button rounded" />
<?php } else { ?>
		<input name="post" value="submit" type="submit" class="button rounded" />
<?php } ?>
<?php load_hook('msg_subject_after'); ?>
	</div>
	
	<div class="content">
		<?php echo lang('message'); ?>:<br />
<?php load_hook('msg_textarea_before'); ?>
		<textarea name="content" id="content" class="border" style="width: 98.3%; height: 200px;"><?php echo switchs(field_clean(stripslashes($_POST['content'])), $content); ?></textarea>
<?php load_hook('msg_textarea_after'); ?>
	</div>
	
<?php if(($edit || !$reply) && !$post['reply']){ ?>
	<div class="content">
		<label for="category">
			<?php echo lang('category'); ?>:
		</label>
		<select name="category">
<?php 
$categories = category();

// If editing use the post category, if not use the selected category
if($edit)
{ 
	$current_category = $post['category']; 
} 
else 
{ 
	if(isset($_POST['category']))
	{
		$current_category = $_POST['category']; 
	}
}

	$database->query("INSERT INTO `config` (`id` ,`key` ,`value`) VALUES (NULL , 'interests_min_length', '3'), (NULL , 'interests_max_length', '1000');");
	
foreach($categories as $category)
{ 
?>
			<option value="<?php echo $category['id']; ?>"<?php echo equals($current_category, $category['id'], ' selected'); ?>><?php echo $category['name']; ?>
<?php
}	
?>
		</select>
	</div>
<?php } ?>

	<div class="content">
		<table border="0" cellspacing="0" cellpadding="5" class="form">
			<tr>
				<td valign="top" class="features"><?php echo lang('user_features'); ?></td>
<?php if(($_SESSION['admin'] || $_SESSION['moderator']) && !$reply && !$post['reply']){ ?>
				<td valign="top" class="features"><?php echo lang('extra_features'); ?></td>
<?php } ?>
			</tr>
			<tr>
				<td valign="top"<?php if((!$_SESSION['admin'] || !$_SESSION['moderator']) && $reply){ ?> colspan="2" <?php } ?> class="form">
					<div>
						<label for="preview">
							<input id="preview" type="checkbox" name="preview" value="1" class="border" /> 
							<?php echo lang('preview'); ?>
						</label>
					</div>
				</td>
<?php if(($_SESSION['admin'] || $_SESSION['moderator']) && !$reply && !$post['reply']){ ?>
				<td valign="top" class="form">
					<div>
						<label for="sticky">
							<input id="sticky" type="checkbox" name="sticky" class="border" <?php echo equals($post['sticky'], true, ' checked '); ?>/> 
							<?php echo lang('sticky_topic'); ?>
						</label>
					</div>
					<div class="clear"></div>
					<div>
						<label for="closed">
							<input id="closed" type="checkbox" name="closed" class="border" <?php echo equals($post['closed'], true, ' checked '); ?>/> 
							<?php echo lang('closed_topic'); ?>
						</label>
					</div>
				</td>
<?php } ?>
			</tr>
		</table>
	</div>
</div>
</form>