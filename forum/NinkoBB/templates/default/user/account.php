		<h3 class="title"><?php echo lang('editing_account'); ?></h3>

<?php if ($error){ ?>
		<h3 class="error"><span class="text"><?php echo $error; ?></span></h3>
<?php } else if($success){ ?>
		<h3 class="success"><span class="text"><?php echo $error; ?></span></h3>
<?php } ?>
		<div class="content">
			<?php echo lang('editing_account_msg'); ?>
		</div>
		
		<form method="post">
		<div class="content">
			<dl class="input">
				<dt>
					<?php echo lang('username'); ?><br />
					<span><?php echo lang('change_username_disallowed'); ?></span>
				</dt>
				<dd><strong><?php echo $user_data['username']; ?></strong></dd>

			</dl>
			
			<dl class="input">
				<dt><?php echo lang('email'); ?></dt>
				<dd><input type="text" name="email" class="border" style="width: 40%" value="<?php echo switchs(addslashes($_POST['email']), $user_data['email']); ?>"></dd>
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
				<dt>
					<?php echo lang('current_password'); ?><br />
					<span><?php echo lang('confirm_current_password'); ?></span>
				</dt>
				<dd><input type="password" name="current" class="border" style="width: 40%"></dd>
			</dl>
			
			<dl class="input">
				<dt>&nbsp;</dt>
				<dd><input type="submit" class="button" name="account" value="submit"></dd>
			</dl>
		</div>
		</form>
		
		<div class="clear"></div>
	</div>