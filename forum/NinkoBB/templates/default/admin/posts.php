		<td valign="top" align="left">
			<table class="ac" width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="4" class="title"><?php echo lang('admin_cp'); ?> - <?php echo lang('manage_posts'); ?></td>
				</tr>
<?php if ($error){ ?>
                <tr>
                    <td class="error">
                        <?php echo $error; ?>
                    </td>
                </tr>
<?php } else if($success){ ?>
                <tr>
                    <td class="error">
                        <?php echo $success; ?>
                    </td>
                </tr>
<?php } ?>
				<tr>
					<td class="item key"><?php echo lang('message'); ?></td>
					<td align="center" class="item key"><?php echo lang('starter_c'); ?></td>
					<td colspan="2" class="item key"><?php echo lang('actions'); ?></td>
				</tr>
<?php
if(is_array($posts))
{
	foreach($posts as $row){ 
		// reset
		$status = "";
		
		// Trim subject
		$content = substru(trim(stripslashes($row['message'])), 0, 25) . "&#8230;";
		
		// How many replies?
		$replies = intval(get_replies($row['reply']));
						
		// Lets update it
		$replies = $replies+1;
						
		// Woooo~ Last id for redirecting~
		$page_numbers = (($replies / 20) - 1);
		$n = ceil($page_numbers);
						
		if ($n == -1)
		{
			$n = 0;
		}
		else
		{
			$n = abs($n);
		}
		
		// Build topic url
		$post_url = "{$config['url_path']}/read.php?id={$row['reply']}&page={$n}";
		
		// Topic starter data
		$post_author = user_data($row['starter_id']);
?>
                <tr>
                    <td nowrap="nowrap" width="40%" class="item">
						<a href="<?php echo $post_url; ?>"> 
							<?php echo $content; ?>
						</a>
                    </td>
                    <td nowrap="nowrap" align="center" class="item"> 
                        <?php echo $post_author['styled_name']; ?>
                    </td>
                    <td nowrap="nowrap" align="center" class="item key">
                        <a href="<?php echo $config['url_path']; ?>/message.php?edit=<?php echo $row['id']; ?>"><?php echo lang('edit'); ?></a>
                    </td>
                    <td nowrap="nowrap" align="center" class="item key">
                        <a href="<?php echo $config['url_path']; ?>/admin.php?a=posts&delete=<?php echo $row['id']; ?>"><?php echo lang('delete'); ?></a>
                    </td>
                </tr>
<?php } ?>				

<?php if($post_pagination){ ?>
				<tr>
					<td colspan="6"><?php echo $post_pagination; ?></td>
				</tr>
<?php } ?>
<?php } else { ?>
                <tr>
                    <td colspan="4">
						<center>No posts!</center>
                    </td>
                </tr>
<?php } ?>
			</table>
    	</td>
	</tr>
</table>
