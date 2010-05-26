<table border="0" cellpadding="1" cellspacing="0">
	<tr>
		<td valign="top" align="left" class="title">
    		<?php echo lang('viewing_profile'); ?> <?php echo $viewing['styled_name']; ?>
    	</td>
	</tr>
	<tr>
		<td valign="top" align="left">
			<table border="0" cellpadding="0" cellspacing="0" class="uc">
				<tr>
					<td width="40%" valign="top" class="form">
					<center>
						<?php echo $viewing['styled_name']; ?><br />
						<img src="<?php echo get_avatar($viewing['id']); ?>" alt="avatar" />
<?php 
if($viewing['banned'])
{ 
	echo "<br /><span class='item'>Banned</span>"; 
}
else if($viewing['moderator'])
{ 
	echo "<br /><span class='item'>Moderator</span>"; 
} 
else if($viewing['admin'])
{ 
	echo "<br /><span class='item'>Administrator</span>"; 
} 
?>
					</center><br />
						<div class="title">
							<div class="inner">
								User Info
							</div>
						</div>
						<div class="post">
							<?php echo lang('joined'); ?>: <?php echo date($config['date_format'], $viewing['join_date']); ?>
						</div>
						<div class="post">
							<?php echo lang('last_visit'); ?>: <?php echo date($config['date_format'], $viewing['last_seen']); ?>
						</div>
						<div class="post">
							<?php echo lang('total_posts'); ?>: <?php echo forum_count(false, $viewing['id'], false); ?>
						</div>
<?php if($viewing['sex'] != ""){ ?>
						<div class="post">
							<?php echo lang('sex'); ?>: <?php echo ucwords($viewing['sex']); ?>
						</div>
<?php } ?>
<?php if($viewing['age'] != ""){ ?>
						<div class="post">
							<?php echo lang('birthday'); ?>: <?php echo nice_date(strtotime($viewing['age'])); ?>
						</div>
<?php } ?>
					</td>
					<td valign="top" align="left">
						<div class="title">
							<div class="inner">
								Instant Messengers
							</div>
						</div>
<?php if($viewing['aim'] != ""){ ?>
						<div class="post">
							<?php echo lang('aim'); ?>: <?php echo parse($viewing['aim']); ?>
						</div>
<?php } ?>
<?php if($viewing['msn'] != ""){ ?>
						<div class="post">
							<?php echo lang('msn'); ?>: <?php echo parse($viewing['msn']); ?>
						</div>
<?php } ?>
						<br />
						<div class="title">
							<div class="inner">
								<?php echo lang('interests'); ?>
							</div>
						</div>
<?php if($viewing['interests'] != ""){ ?>
						<div class="post">
							<?php echo parse($viewing['interests']); ?>
						</div>
<?php } ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
