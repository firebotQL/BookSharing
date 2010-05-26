		<td valign="top" align="left">
			<table class="ac" width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="7" class="title"><?php echo lang('admin_cp'); ?> - Manage Topics / Topics</td>
				</tr>
<?php if ($error){ ?>
                <tr>
                    <td class="error" colspan="7" >
                        <?php echo $error; ?>
                    </td>
                </tr>
<?php } else if($success){ ?>
                <tr>
                    <td class="error" colspan="7" >
                        <?php echo $success; ?>
                    </td>
                </tr>
<?php } ?>
				<tr>
					<td class="item key"><?php echo lang('subject_c'); ?></td>
					<td align="center" class="item key"><?php echo lang('starter_c'); ?></td>
					<td align="center" class="item key"><?php echo lang('status'); ?></td>
					<td align="center" class="item key"><?php echo lang('posts_c'); ?></td>
					<td colspan="2" class="item key"><?php echo lang('actions'); ?></td>
					<td colspan="2" class="item key"><?php echo lang('category'); ?></td>
				</tr>
<?php
if(is_array($topics))
{
	foreach($topics as $row)
	{ 
		// reset
		$status = ""; $list = "";
			
		// Trim subject
		$subject = substru(trim(htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8')), 0, $config['max_length']) . "&#8230;";
			
		// Build topic url
		$topic_url = "{$config['url_path']}/read.php?id={$row['id']}";
			
		// Topic starter data
		$topic_author = user_data($row['starter_id']);
			
		// Topic status
		if($row['closed'])
		{
			$status = 'closed, ';
		}
			
		if($row['sticky'])
		{
			$status .= 'sticky';
		}

		$categories = category();

		// Create the delete list
		foreach($categories as $acat)
		{ 
			if($acat['id'] == $row['category']){ continue; }
			
			if($page){ $apage = "&page={$page}"; }
			
			$list .= "<a href='{$config['url_path']}/admin.php?a=topics&update={$row['id']}&cat={$acat['id']}{$apage}'>{$acat['name']}</a>";
				
			if((end($categories) != $acat))
			{
					$list .= ", ";
			}
		}

		if(substr($list, -2) == ", ")
		{
			$list = substr($list, 0, -2);
		}
?>
                <tr>
                    <td nowrap="nowrap" width="40%" class="item">
                        <span class="smallfont">
                            <a href="<?php echo $topic_url; ?>"> 
                            <?php echo $subject; ?>
                            </a>
                        </span>
                    </td>
                    <td nowrap="nowrap" align="center" class="item">
                        <?php echo $topic_author['styled_name']; ?>
                    </td>
                    <td nowrap="nowrap" align="center" class="item grey">
                        <?php echo $status ?>
                    </td>
                    <td nowrap="nowrap" align="center" class="item grey">
                        <?php echo forum_count(false, $row['id'], ''); ?>
                    </td>
                    <td nowrap="nowrap" align="center" class="item key">
                        <a href="<?php echo $config['url_path']; ?>/message.php?edit=<?php echo $row['id']; ?>">Edit</a>
                    </td>
                    <td nowrap="nowrap" align="center" class="item key">
                        <a href="<?php echo $config['url_path']; ?>/admin.php?a=topics&delete=<?php echo $row['id']; ?>">Delete</a>
                    </td>
                    <td nowrap="nowrap" align="center" class="item key">
                        <?php echo $list; ?>
                    </td>
                </tr>
<?php
	}
}
else
{
?>
				<tr>
					<td colspan="7">
						No topics to display!
					</td>
				</tr>
<?php
}
?>

<?php if($topics_pagination){ ?>
				<tr>
					<td colspan="7"><?php echo $topics_pagination; ?></td>
				</tr>
<?php } ?>
			</table>
    	</td>
	</tr>
</table>
