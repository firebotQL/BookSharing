<table border="0" cellpadding="1" cellspacing="0" class="topic">
	<tr>
		<td valign="top" rowspan="2" width="6%">
			<?php echo $status ?>
		</td>
		<td valign="top" width="50%">
			<span class="url"><a href="<?php echo $topic_url; ?>"><?php echo $subject; ?></a></span>
		</td>
		<td align="right" class="creator">
			<?php echo lang('created_on'); ?> <?php echo date($config['date_format'], ($row['time'] + $config['zone'])); ?>
		</td>
	</tr>
	<tr>
		<td class="details" colspan="2"> 
			<span class="item"><?php echo forum_count($row['id']); ?> Posts</span>
			<span class="item"><?php echo lang('topic_by'); ?> <strong><?php echo $topic_author['styled_name']; ?></strong></span>
			<span class="item">
<?php if($last_post){ ?>
				Last post was <?php echo nice_date($last_post['time']); ?> <?php echo lang('by'); ?> <strong><?php echo $last_post_udata['styled_name'] ?></strong>
<?php } else { ?>
				<?php echo lang('none'); ?>
<?php } ?>
			</span>
		</td>
	</tr>
</table>