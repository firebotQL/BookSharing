		<h3 class="title"><?php echo lang('editing_profile'); ?></h3>

<?php if ($error){ ?>
		<h3 class="error"><span class="text"><?php echo $error; ?></span></h3>
<?php } else if($errors){ ?>
<?php foreach($errors as $error){ ?>
		<h3 class="error"><span class="text"><?php echo $error; ?></span></h3>
<?php } ?>
<?php } if($success){ ?>
		<h3 class="success"><span class="text"><?php echo $success; ?></span></h3>
<?php } ?>
		<div class="content">
						<?php echo lang('editing_profile_msg'); ?>
		</div>
		
		<form method="post">
		
		<div class="content">
			<dl class="input">
				<dt><?php echo lang('first_name'); ?></dt>
				<dd><input type="text" class="border cp" name="first_name" value="<?php echo switchs($user_data['first_name'], $_POST['first_name']); ?>"></dd>
			</dl>

			<dl class="input">
				<dt><?php echo lang('last_name'); ?></dt>
				<dd><input type="text" class="border cp" name="last_name" value="<?php echo switchs($user_data['last_name'], $_POST['last_name']); ?>"></dd>
			</dl>

			<dl class="input">
				<dt><?php echo lang('location'); ?></dt>
				<dd><input type="text" class="border cp" name="location" value="<?php echo switchs($user_data['location'], $_POST['location']); ?>"></dd>
			</dl>

			<dl class="input">
				<dt><?php echo lang('sex'); ?></dt>
				<dd>
<?php
// Determine thes sex by what we can deduce
if(!isset($_POST['gender']) && $user_data['sex'] == "")
{
	$default = "anonymous";
}
else if($_POST['gender'] != "" && $_POST['gender'] != $user_data['sex']) 
{
	$default = $_POST['gender'];
}
else
{
	$default = $user_data['sex'];
}
?>
					<select name="gender" class="border">
						<option value="male"<?php echo equals($default, "male", " selected", ""); ?>>Male</option>
						<option value="female"<?php echo equals($default, "female", " selected", ""); ?>>Female</option>
						<option value="anonymous"<?php echo equals($default, "anonymous", " selected", ""); ?>>Anonymous</option>
					</select>
				</dd>
			</dl>

			<dl class="input">
				<dt><?php echo lang('msn'); ?></dt>
				<dd><input type="text" class="border cp" name="msn" value="<?php echo switchs($user_data['msn'], $_POST['msn']); ?>"></dd>
			</dl>

			<dl class="input">
				<dt><?php echo lang('aim'); ?></dt>
				<dd><input type="text" class="border cp" name="aim" value="<?php echo switchs($user_data['aim'], $_POST['aim']); ?>"></dd>
			</dl>
			
			<dl class="input">
				<dt><?php echo lang('interests'); ?></dt>
				<dd><textarea class="border cp" name="interests" rows="10"><?php echo htmlspecialchars(stripslashes(switchs($user_data['interests'], $_POST['interests']))); ?></textarea></dd>
			</dl>
			
			<?php load_hook('user_profile_edit'); ?>
			
			<dl class="input">
				<dt>&nbsp;</dt>
				<dd><input type="submit" class="button rounded" name="profile" value="submit"></dd>
			</dl>
		</div>
		</form>
		
		<div class="clear"></div>
	</div>