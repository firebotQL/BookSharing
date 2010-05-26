		<h3 class="title admin"><?php echo lang('admin_cp'); ?></h3>
		
		<div class="content">
			<?php echo lang('admin_welcome'); ?>
		</div>
		
		<h3 class="title admin"><?php echo lang('forum_statistics'); ?></h3>
		
		<div class="content">
			<dl class="input">
				<dt>
					<?php echo lang('topics_c'); ?><br />
					<span><?php echo lang('today'); ?></span>
				</dt>
				<dd>
					<?php echo forum_count(false, '*', ''); ?><br />
					<?php echo forum_count(false, '*', '', true); ?>
				</dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('posts_c'); ?><br />
					<span><?php echo lang('today'); ?></span><br /><br />
				</dt>
				<dd>
					<?php echo forum_count(false, false, 'posts'); ?><br />
					<?php echo forum_count(false, false, 'posts', true); ?>
				</dd>
			</dl>
	
			<dl class="input">
				<dt><?php echo lang('total_topics_posts'); ?></dt>
				<dd><?php echo forum_count(false, false, 'all'); ?></dd>
			</dl>
		</div>
			
		<h3 class="title admin"><?php echo lang('user_registrations'); ?></h3>
		
		<div class="content">
			<dl class="input">
				<dt>
					<?php echo lang('user_registrations'); ?><br />
					<span><?php echo lang('today'); ?></span>
				</dt>
				<dd><?php echo count_users('1'); ?></dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('user_registrations'); ?><br />
					<span><?php echo lang('week'); ?></span>
				</dt>
				<dd><?php echo count_users('7'); ?></dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('user_registrations'); ?><br />
					<span><?php echo lang('month'); ?></span>
				</dt>
				<dd><?php echo count_users('30'); ?></dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('user_registrations'); ?><br />
					<span><?php echo lang('year_c'); ?></span>
				</dt>
				<dd><?php echo count_users('365'); ?></dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('total_user_registrations'); ?>
				</dt>
				<dd><?php echo count_users(); ?></dd>
			</dl>
		</div>
	</div>
