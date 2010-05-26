
<?php if($pagination){ ?>
	<div class="container">
		<h3 class='title'>Pages:</h3>
		<div class='content'>
			<?php echo $pagination; ?>
		</div>
	</div>
<?php } ?>

<?php load_hook('qr_before'); ?>
<form action="<?php echo $topic_url; ?>" method="post">
<input type="hidden" name="reply" value="<?php echo $topic['id']; ?>">
<div class="container" id="qr">
	<h3 class="title"><?php echo lang('quick_reply'); ?></h3>
	
<?php if ($error){ ?>
	<h3 class="error"><span class="text"><?php echo $error; ?></span></h3>
<?php } ?>
<?php load_hook('qr_inside_before'); ?>
	<div class="content">
		<?php echo lang('subject_c'); ?>:<br />
<?php load_hook('qr_subject_before'); ?>
		<input type="text" name="qsubject" class="border" style="width: 88%" value="<?php echo switchs(field_clean($_POST['qsubject'])); ?>" /> <input name="post" value="<?php echo lang('reply'); ?>" type="submit" />
<?php load_hook('qr_subject_after'); ?>
	</div>
	
	<div class="content">	
		<?php echo lang('message'); ?>:<br />
<?php load_hook('qr_textarea_before'); ?>
		<textarea name="qcontent" id="qcontent" class="border" style="width: 98.3%; height: 150px;"><?php echo switchs(field_clean($_POST['qcontent'])); ?></textarea>
<?php load_hook('qr_textarea_after'); ?>
	</div>
<?php load_hook('qr_inside_after'); ?>
</div>
</form>
<?php load_hook('qr_after'); ?>