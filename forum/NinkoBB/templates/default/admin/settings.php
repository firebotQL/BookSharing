		<h3 class="title admin"><?php echo lang('admin_cp'); ?> - <?php echo lang('site_settings'); ?></h3>
		
		<ul class="admin">
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=settings&amp;area=main" class="<?php echo equals($area, "main", "menu-current", "menu"); ?>"><?php echo lang('main'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=settings&amp;area=register" class="<?php echo equals($area, "register", "menu-current", "menu"); ?>"><?php echo lang('registration'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=settings&amp;area=user" class="<?php echo equals($area, "user", "menu-current", "menu"); ?>"><?php echo lang('user_c'); ?></a></li>
			<li><a href="<?php echo $config['url_path']; ?>/admin.php?a=settings&amp;area=topics" class="<?php echo equals($area, "topics", "menu-current", "menu"); ?>"><?php echo lang('topics_c'); ?></a></li>
<?php 
load_hook('settings_navigation'); 
?>
			<div class="clear"></div>
		</ul>
		
		<div class="clear"></div>
		
<?php if($area == "main"){ ?>
		<form method="post">
		<h3 class="title admin">
			<?php echo lang('main_settings'); ?>
		</h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<?php echo lang('site_name'); ?>
				</dt>
				<dd><input type="text" name="site_name" value="<?php echo switchs($_POST['site_name'], $config['site_name']); ?>" class="border" style="width: 40%" /></dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('site_language'); ?>
				</dt>
				<dd><?php languages(true); ?></dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('site_theme'); ?>
				</dt>
				<dd><?php themes(true); ?></dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('site_url'); ?><br />
					<span><?php echo lang('site_url_msg'); ?></span>
				</dt>
				<dd><input type="text" name="url_path" value="<?php echo switchs($_POST['url_path'], $config['url_path']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
		</div>
		
		<br clear="both" />
		
		<h3 class="title admin"><?php echo lang('time_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label for="timechange"><?php echo lang('timezone'); ?></label><br />
					<span></span>
				</dt>
				<dd>
					<select id="timechange" name="timechange" class="border" style="width: 40%">
						<option value="-12"<?php echo equals($config['timechange'], "-12", ' selected'); ?>>(UTC-12:00) International Date Line West</option>
						<option value="-11"<?php echo equals($config['timechange'], "-11", ' selected'); ?>>(UTC-11:00) Niue, Samoa</option>
						<option value="-10"<?php echo equals($config['timechange'], "-10", ' selected'); ?>>(UTC-10:00) Hawaii-Aleutian, Cook Island</option>
						<option value="-9.5"<?php echo equals($config['timechange'], "-9.5", ' selected'); ?>>(UTC-09:30) Marquesas Islands</option>
						<option value="-9"<?php echo equals($config['timechange'], "-9", ' selected'); ?>>(UTC-09:00) Alaska, Gambier Island</option>
						<option value="-8"<?php echo equals($config['timechange'], "-8", ' selected'); ?>>(UTC-08:00) Pacific</option>
						<option value="-7"<?php echo equals($config['timechange'], "-7", ' selected'); ?>>(UTC-07:00) Mountain</option>
						<option value="-6"<?php echo equals($config['timechange'], "-6", ' selected'); ?>>(UTC-06:00) Central</option>
						<option value="-5"<?php echo equals($config['timechange'], "-5", ' selected'); ?>>(UTC-05:00) Eastern</option>
						<option value="-4"<?php echo equals($config['timechange'], "-4", ' selected'); ?>>(UTC-04:00) Atlantic</option>
						<option value="-3.5"<?php echo equals($config['timechange'], "-3.5", ' selected'); ?>>(UTC-03:30) Newfoundland</option>
						<option value="-3"<?php echo equals($config['timechange'], "-3", ' selected'); ?>>(UTC-03:00) Amazon, Central Greenland</option>
						<option value="-2"<?php echo equals($config['timechange'], "-2", ' selected'); ?>>(UTC-02:00) Mid-Atlantic</option>
						<option value="-1"<?php echo equals($config['timechange'], "-1", ' selected'); ?>>(UTC-01:00) Azores, Cape Verde, Eastern Greenland</option>
						<option value="0"<?php echo equals($config['timechange'], "0", ' selected'); ?>>(UTC) Western European, Greenwich</option>
						<option value="1"<?php echo equals($config['timechange'], "1", ' selected'); ?>>(UTC+01:00) Central European, West African</option>
						<option value="2"<?php echo equals($config['timechange'], "2", ' selected'); ?>>(UTC+02:00) Eastern European, Central African</option>
						<option value="3"<?php echo equals($config['timechange'], "3", ' selected'); ?>>(UTC+03:00) Moscow, Eastern African</option>
						<option value="3.5"<?php echo equals($config['timechange'], "3.5", ' selected'); ?>>(UTC+03:30) Iran</option>
						<option value="4"<?php echo equals($config['timechange'], "4", ' selected'); ?>>(UTC+04:00) Gulf, Samara</option>
						<option value="4.5"<?php echo equals($config['timechange'], "4.5", ' selected'); ?>>(UTC+04:30) Afghanistan</option>
						<option value="5"<?php echo equals($config['timechange'], "5", ' selected'); ?>>(UTC+05:00) Pakistan, Yekaterinburg</option>
						<option value="5.5"<?php echo equals($config['timechange'], "5.5", ' selected'); ?>>(UTC+05:30) India, Sri Lanka</option>
						<option value="5.75"<?php echo equals($config['timechange'], "5.75", ' selected'); ?>>(UTC+05:45) Nepal</option>
						<option value="6"<?php echo equals($config['timechange'], "6", ' selected'); ?>>(UTC+06:00) Bangladesh, Bhutan, Novosibirsk</option>
						<option value="6.5"<?php echo equals($config['timechange'], "6.5", ' selected'); ?>>(UTC+06:30) Cocos Islands, Myanmar</option>
						<option value="7"<?php echo equals($config['timechange'], "7", ' selected'); ?>>(UTC+07:00) Indochina, Krasnoyarsk</option>
						<option value="8"<?php echo equals($config['timechange'], "8", ' selected'); ?>>(UTC+08:00) Greater China, Australian Western, Irkutsk</option>
						<option value="8.75"<?php echo equals($config['timechange'], "8.75", ' selected'); ?>>(UTC+08:45) Southeastern Western Australia</option>
						<option value="9"<?php echo equals($config['timechange'], "9", ' selected'); ?>>(UTC+09:00) Japan, Korea, Chita</option>
						<option value="9.5"<?php echo equals($config['timechange'], "9.5", ' selected'); ?>>(UTC+09:30) Australian Central</option>
						<option value="10"<?php echo equals($config['timechange'], "10", ' selected'); ?>>(UTC+10:00) Australian Eastern, Vladivostok</option>
						<option value="10.5"<?php echo equals($config['timechange'], "10.5", ' selected'); ?>>(UTC+10:30) Lord Howe</option>
						<option value="11"<?php echo equals($config['timechange'], "11", ' selected'); ?>>(UTC+11:00) Solomon Island, Magadan</option>
						<option value="11.5"<?php echo equals($config['timechange'], "11.5", ' selected'); ?>>(UTC+11:30) Norfolk Island</option>
						<option value="12"<?php echo equals($config['timechange'], "12", ' selected'); ?>>(UTC+12:00) New Zealand, Fiji, Kamchatka</option>
						<option value="12.75"<?php echo equals($config['timechange'], "12.75", ' selected'); ?>>(UTC+12:45) Chatham Islands</option>
						<option value="13"<?php echo equals($config['timechange'], "13", ' selected'); ?>>(UTC+13:00) Tonga, Phoenix Islands</option>
						<option value="14"<?php echo equals($config['timechange'], "14", ' selected'); ?>>(UTC+14:00) Line Islands</option>
					</select>
				</dd>
			</dl>

			<dl class="input">
				<dt>
					<?php echo lang('date_format'); ?><br />
					<span><?php echo lang('currently'); ?>: <?php echo $config['submitdate']; ?><br /><?php echo lang('help_formatting'); ?></span>
				</dt>
				<dd><input type="text" name="date_format" value="<?php echo switchs($_POST['date_format'], $config['date_format']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
		</div>
		
		<br clear="both" />
		
		<h3 class="title admin"><?php echo lang('cookie_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label for="allow">
						<?php echo lang('allow'); ?><br />
						<span><?php echo lang('allow_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="checkbox" id="allow" name="allow_cookies" <?php echo equals($config['allow_cookies'], true, ' checked '); ?>/></dd>
			</dl>

			<dl class="input">
				<dt>
					<label for="allow">
						<?php echo lang('cookie_domain'); ?><br />
						<span><?php echo lang('cookie_domain_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="text" name="cookie_domain" value="<?php echo switchs($_POST['cookie_domain'], $config['cookie_domain']); ?>" class="border" style="width: 40%" /></dd>
			</dl>

			<dl class="input">
				<dt><label for=""><span></span></label></dt>
				<dd><input type="submit" name="settings" class="button rounded" value="save" /></dd>
			</dl>
		</div>
		</form>
<?php } else if($area == "register"){ ?>
		<form method="post">
		<h3 class="title admin"><?php echo lang('registration_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label for="age_validation">
						<?php echo lang('age_validation'); ?><br />
						<span><?php echo lang('age_validation_msg'); ?></span>
					</label>
				</dt>
				<dd>
					<input type="text" id="age_validation" name="age_validation" value="<?php echo switchs($_POST['age_validation'], $config['age_validation']); ?>" class="border" style="width: 10%" />
				</dd>
			</dl>

			<dl class="input">
				<dt>
					<label for="ul">
						<?php echo lang('username_length'); ?><br />
						<span><?php echo lang('min_max'); ?></span>
					</label>
				</dt>
				<dd>
					<input type="text" id="ul" name="min_name_length" value="<?php echo switchs($_POST['min_name_length'], $config['min_name_length']); ?>" class="border" style="width: 10%" /> 
					<input type="text" id="ul" name="max_name_length" value="<?php echo switchs($_POST['max_name_length'], $config['max_name_length']); ?>" class="border" style="width: 10%" />
				</dd>
			</dl>
		</div>
		
		<br clear="both" />

		<h3 class="title admin"><?php echo lang('email_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label for="validation">
						<?php echo lang('email_validation'); ?><br />
						<span><?php echo lang('email_validation_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="checkbox" id="validation" name="email_validation" <?php echo equals($config['email_validation'], true, ' checked '); ?>/></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label for="sender">
						<?php echo lang('email_sender'); ?><br />
						<span><?php echo lang('email_sender_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="sender" name="email_sender" value="<?php echo switchs($_POST['email_sender'], $config['email_sender']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
		</div>
		
		<br clear="both" />

		<h3 class="title admin"><?php echo lang('email_template'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label for="subject">
						<?php echo lang('email_subject'); ?><br />
						<span><?php echo lang('tags'); ?>: {site_name}, {username}, {email}</span>
					</label>
				</dt>
				<dd><input type="text" id="subject" name="email_subject" value="<?php echo switchs($_POST['email_subject'], $config['email_subject']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label for="em">
						<?php echo lang('email_message'); ?><br />
						<span><?php echo lang('tags'); ?>: {site_name}, {username}, {email}, {link}</span>
					</label>
				</dt>
				<dd><textarea class="border" id="em" name="email_message" rows="15" style="width: 50%; padding: 5px;"><?php echo switchs(br2nl(str_replace('\r\n','<br />', $config['email_message'])), stripslashes($_POST['email_message'])); ?></textarea></dd>
			</dl>
			
			<dl class="input">
				<dt><label for=""><span></span></label></dt>
				<dd><input type="submit" name="settings" class="button rounded" value="save" /></dd>
			</dl>
		</div>
		</form>
<?php } else if($area == "user"){ ?>
		<form method="post">
		<h3 class="title admin"><?php echo lang('user_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label for="admin_symbol">
						<?php echo lang('admin_symbol'); ?><br />
						<span><?php echo lang('admin_symbol_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="admin_symbol" name="admin_symbol" value="<?php echo switchs(htmlspecialchars($_POST['admin_symbol'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($config['admin_symbol'], ENT_QUOTES, 'UTF-8')); ?>" class="border" style="width: 40%" /></dd>
			</dl>

			<dl class="input">
				<dt>
					<label for="user_online_timeout">
						<?php echo lang('user_timeout'); ?><br />
						<span><?php echo lang('in_seconds'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="user_online_timeout" name="user_online_timeout" value="<?php echo switchs($_POST['user_online_timeout'], $config['user_online_timeout']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
		</div>
		
		<br clear="both" />

		<h3 class="title admin"><?php echo lang('avatar_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label for="avatar_upload_path">
						<?php echo lang('avatar_directory'); ?><br />
						<span><?php echo lang('avatar_directory_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="avatar_upload_path" name="avatar_upload_path" value="<?php echo switchs($_POST['avatar_upload_path'], $config['avatar_upload_path']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label for="avatar_folder_name">
						<?php echo lang('avatar_directory_name'); ?><br />
						<span><?php echo lang('avatar_directory_name_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="avatar_folder_name" name="avatar_folder_name" value="<?php echo switchs($_POST['avatar_folder_name'], $config['avatar_folder_name']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label for="avatar_max_size">
						<?php echo lang('avatar_max_size'); ?><br />
						<span><?php echo lang('avatar_max_size_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="avatar_max_size" name="avavatar_max_size" value="<?php echo switchs($_POST['avatar_max_size'], $config['avatar_max_size']); ?>" class="border" style="width: 10%" /></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label for="mx">
						<?php echo lang('avatar_max_widthxheight'); ?><br />
						<span><?php echo lang('widthxheight'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="mx" name="avatar_max_width" value="<?php echo switchs($_POST['avatar_max_width'], $config['avatar_max_width']); ?>" class="border" style="width: 10%" /> x <input type="text" id="mx" name="avatar_max_height" value="<?php echo switchs($_POST['avatar_max_height'], $config['avatar_max_height']); ?>" class="border" style="width: 10%" /></dd>
			</dl>

			<dl class="input">
				<dt>
					<label for="avatar_use">
						<?php echo lang('avatar_filename_use'); ?><br />
						<span><?php echo lang('avatar_filename_use_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="text" id="avatar_use" name="avatar_use" value="<?php echo switchs($_POST['avatar_use'], $config['avatar_use']); ?>" class="border" style="width: 40%" /></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label for="avatar_md5_use">
						<?php echo lang('avatar_md5_use'); ?><br />
						<span><?php echo lang('avatar_md5_use_msg'); ?></span>
					</label>
				</dt>
				<dd><input type="checkbox" id="avatar_md5_use" name="avatar_md5_use" <?php echo equals($config['avatar_md5_use'], true, ' checked '); ?>/></dd>
			</dl>
			
			<dl class="input">
				<dt><label for=""><span></span></label></dt>
				<dd><input type="submit" name="settings" class="button rounded" value="save" /></dd>
			</dl>
		</div>
		</form>
<?php } else if($area == "topics"){ ?>
		<form method="post">
		<h3 class="title admin"><?php echo lang('topic_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label>
						<?php echo lang('page_options'); ?>
						<br />&nbsp;
					</label>
				</dt>
				<dd>
					<input type="checkbox" id="show_first_post" name="show_first_post" <?php echo equals($config['show_first_post'], true, ' checked '); ?>/> 
					<label for="show_first_post"><?php echo lang('show_first_post'); ?></label>
				</dd>
				<dd>
					<input type="checkbox" id="allow_quick_reply" name="allow_quick_reply" <?php echo equals($config['allow_quick_reply'], true, ' checked '); ?>/> 
					<label for="allow_quick_reply"><?php echo lang('show_quick_reply'); ?></label>
				</dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label>
						<?php echo lang('bbcode_options'); ?>
						<br /><br /><br />&nbsp;
					</label>
				</dt>
				<dd><input type="checkbox" id="bbcode" name="bbcode" <?php echo equals($config['bbcode'], true, ' checked '); ?>/> <label for="bbcode"><?php echo lang('allow_bbcode'); ?></label></dd>
				<dd><input type="checkbox" id="bbcode_url" name="bbcode_url" <?php echo equals($config['bbcode_url'], true, ' checked '); ?>/> <label for="bbcode_url"><?php echo lang('allow_bbcode_url'); ?></label></dd>
				<dd><input type="checkbox" id="bbcode_image" name="bbcode_image" <?php echo equals($config['bbcode_image'], true, ' checked '); ?>/> <label for="bbcode_image"><?php echo lang('allow_bbcode_img'); ?></label></dd>
			</dl>
		</div>
		
		<br clear="both" />

		<h3 class="title admin"><?php echo lang('message_settings'); ?></h3>
		
		<div class="content admin">
			<dl class="input">
				<dt>
					<label>
						<?php echo lang('messages_per'); ?><br /><br /><br />&nbsp;
					</label>
				</dt>
				<dd><input type="text" name="messages_per_page" value="<?php echo switchs($_POST['messages_per_page'], $config['messages_per_page']); ?>" class="border" style="width: 10%" /> <label><?php echo lang('topics_per'); ?></label></dd><br />
				<dd><input type="text" name="messages_per_topic" value="<?php echo switchs($_POST['messages_per_topic'], $config['messages_per_topic']); ?>" class="border" style="width: 10%" /> <label><?php echo lang('posts_per'); ?></label></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label>
						<?php echo lang('subject_length'); ?><br />
						<span><?php echo lang('min_max'); ?></span>
					</label>
				</dt>
				<dd><input type="text" name="subject_minimum_length" value="<?php echo switchs($_POST['subject_minimum_length'], $config['subject_minimum_length']); ?>" class="border" style="width: 10%" /> <input type="text" name="subject_max_length" value="<?php echo switchs($_POST['subject_max_length'], $config['subject_max_length']); ?>" class="border" style="width: 10%" /></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label>
						<?php echo lang('message_length'); ?><br />
						<span><?php echo lang('min_max'); ?></span>
					</label>
				</dt>
				<dd><input type="text" name="message_minimum_length" value="<?php echo switchs($_POST['message_minimum_length'], $config['message_minimum_length']); ?>" class="border" style="width: 10%" /> <input type="text" name="message_max_length" value="<?php echo switchs($_POST['message_max_length'], $config['message_max_length']); ?>" class="border" style="width: 10%" /></dd>
			</dl>
			
			<dl class="input">
				<dt>
					<label>
						<?php echo lang('time_settings'); ?><br />
						<span><?php echo lang('in_seconds'); ?></span>
						<br /><br />&nbsp;
					</label>
				</dt>
				<dd><input type="text" name="post_topic_time_limit" value="<?php echo switchs($_POST['post_topic_time_limit'], $config['post_topic_time_limit']); ?>" class="border" style="width: 10%" /> <label><?php echo lang('seconds_between_topics'); ?></label></dd><br />
				<dd><input type="text" name="post_reply_time_limit" value="<?php echo switchs($_POST['post_reply_time_limit'], $config['post_reply_time_limit']); ?>" class="border" style="width: 10%" /> <label><?php echo lang('seconds_between_posting'); ?></label></dd>
			</dl>
			
			<dl class="input">
				<dt><label for=""><span></span></label></dt>
				<dd><input type="submit" name="settings" class="button rounded" value="save" /></dd>
			</dl>
		</div>
		</form>
<?php } else { load_hook('admin_settings_page'); } ?>
	</div>
