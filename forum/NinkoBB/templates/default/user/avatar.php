		<h3 class="title"><?php echo lang('editing_avatar'); ?></h3>

<?php if ($error){ ?>
		<h3 class="error"><span class="text"><?php echo $error; ?></span></h3>
<?php } else if($success){ ?>
		<h3 class="success"><span class="text"><?php echo $error; ?></span></h3>
<?php } ?>

    	<form method="post" enctype="multipart/form-data">
		<div class="content">
	    	<dl class="input">
				<dt>
					<?php echo lang('current_avatar'); ?>:<br />
					<span>
							<?php echo lang_parse('avatar_upload_limits', array($config['avatar_max_width'], $config['avatar_max_height'], $config['avatar_max_size'])); ?>
					</span>
				</dt>
				<dd>
<?php if($user_data['avatar']){ ?>
					<img src="<?php echo $current_avatar_link; ?>" alt="avatar" />
<?php } else { ?>
					<?php echo lang('no_avatar'); ?>
<?php } ?>
				</dd>
		    </dl>
		</div>
		
		<div class="content">
    		<dl class="input">
				<dt>
					<?php echo lang("upload_from_computer"); ?>:
				</dt>

				<dd>
					<input name="avatar" type="file" />
				</dd>
		    </dl>
			
    		<dl class="input">
				<dt>&nbsp;</dt>
				<dd><input type="submit" class="button rounded" name="avatar" value="submit"></dd>
			</dl>
		</div>
		</form>
		
		<div class="clear"></div>
	</div>