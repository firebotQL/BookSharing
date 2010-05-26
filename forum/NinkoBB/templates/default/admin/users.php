		<td valign="top" align="left">
			<table class="ac" width="100%" cellpadding="5" cellspacing="0">
<?php if(!$_GET['edit']) { ?>
				<tr>
					<td colspan="6" class="title"><?php echo lang('admin_cp'); ?> - <?php echo lang('manage_users'); ?></td>
				</tr>
				<tr>
					<td class="item key"><?php echo lang('username'); ?></td>
					<td align="center" class="item key"><?php echo lang('email'); ?></td>
					<td align="center" class="item key"><?php echo lang('status'); ?></td>
					<td align="center" class="item key"><?php echo lang('posts_c'); ?></td>
					<td colspan="2" class="item key"><?php echo lang('actions'); ?></td>
				</tr>
<?php foreach($users as $user){ ?>
				<tr>
					<td class="item"><?php echo $user['styled_name']; ?></td>
					<td align="center" class="item grey"><?php echo $user['email']; ?></td>
					<td align="center" class="item grey"><?php if($user['admin']){ echo lang('admin') . ' '; } else if($user['moderator']){ echo lang('moderator') . ' '; } else if($user['banned']){ echo lang('banned') . ' '; } ?></td>
					<td align="center" class="item grey"><?php echo forum_count(false, $user['id'], 'user'); ?></td>
					<td align="center" class="item key"><a href="<?php echo $config['url_path']; ?>/admin.php?a=users&edit=<?php echo $user['id']; ?>"><?php echo lang('edit'); ?></a></td>
<?php if($user['banned']){ ?>
					<td align="center" class="item key"><a href="<?php echo $config['url_path']; ?>/admin.php?a=users&unban=<?php echo $user['id']; ?>"><?php echo lang('unban'); ?></a></td>
<?php } else { ?>
					<td align="center" class="item key"><a href="<?php echo $config['url_path']; ?>/admin.php?a=users&ban=<?php echo $user['id']; ?>"><?php echo lang('ban'); ?></a></td>
<?php } ?>
				</tr>
<?php } ?>

<?php if($user_pagination){ ?>
				<tr>
					<td colspan="6"><?php echo $user_pagination; ?></td>
				</tr>
<?php } ?>

<?php } else { ?>
			<form method="post">
				<tr>
					<td colspan="6" class="title"><?php echo lang('admin_cp'); ?> - <?php echo lang('editing_user'); ?></td>
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
<?php } else if($major_error) { ?>
				<tr>
                    <td class="error">
                        <?php echo $major_error; ?>
                    </td>
				</tr>
<?php exit; } ?>
    			<tr>
    				<td class="form">
						<dl class="input">
							<dt>
								<?php echo lang('username'); ?>
							</dt>
							<dd><input type="text" name="username" class="border" style="width: 40%" value="<?php echo switchs($_POST['username'], $update_user_data['username']); ?>"></dd>
						</dl>
						<dl class="input">
							<dt>
								<?php echo lang('settings'); ?><br /><br />
								<span></span>
							</dt>
							<dd><input type="checkbox" name="banned" <?php echo equals($update_user_data['banned'], true, ' checked '); ?>/> <?php echo lang('banned'); ?></dd>
							<dd><input type="checkbox" name="moderator" <?php echo equals($update_user_data['moderator'], true, ' checked '); ?>/> <?php echo lang('moderator'); ?></dd>
							<dd><input type="checkbox" name="admin" <?php echo equals($update_user_data['admin'], true, ' checked '); ?>/> <?php echo lang('admin'); ?></dd>
						</dl>
						<dl class="input">
							<dt><?php echo lang('email'); ?></dt>
							<dd><input type="text" name="email" class="border" style="width: 40%" value="<?php echo switchs($_POST['email'], $update_user_data['email']); ?>"></dd>
						</dl>
						<dl class="input">
							<dt><?php echo lang('new_password'); ?></dt>
							<dd><input type="password" name="npassword" class="border" style="width: 40%"></dd>
						</dl>
						<dl class="input">
							<dt><?php echo lang('new_password_again'); ?></dt>
							<dd><input type="password" name="npassworda" class="border" style="width: 40%"></dd>
						</dl>
						<dl class="input">
							<dt>&nbsp;</dt>
							<dd><input type="submit" class="button" name="edit" value="submit"></dd>
						</dl>
     				</td>
    			</tr>
			</form>
<?php } ?>
			</table>
    	</td>
	</tr>
</table>
