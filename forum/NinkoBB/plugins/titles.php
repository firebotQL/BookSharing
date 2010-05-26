<?php
/*
Plugin Name: Titles
Description: Allows you to have titles, with revisions!
Version: 1.3
Author: Nijiko
Author URI: http://ninkobb.com
*/

/**
 * For the hook
 */
if(isset($_GET['a']))
{
	if($_GET['a'] == "revisions")
	{ 
		$load_plugins = true; 
		include('../include/common.php'); 
		$action = "revisions"; 
	}
	else if($_GET['a'] == "manage")
	{
		$load_plugins = true;
		include('../include/common.php');
		$action = "revisions";
	}
	else 
	{ 
		if(!defined('IN_NK')) die('Invalid inclusion.'); 
	}
}

/**
 * Incase someone tries to load it when its not loaded.
 */
if(!already_loaded('titles'))
{
	die(print_r($plugins_loaded));
}

/**
 * List of banned revisions
 */
$config['banned_titles'] = array(
	'admin', 'banned', 'moderator', 
	lang('administrator_title'), lang('banned_title'), lang('moderator_title'),
);

/**
 * Add hooks
 */
add_hook('user_profile_edit', 'profile_input', array());
add_hook('user_profile_post', 'profile_edit', array());
add_hook('user_navigation', 'profile_nav_link', array($action));
add_hook('admin_navigation', 'admin_nav_link', array($action));

/**
 * The page for revisions
 */
if(isset($_GET['a']))
{
	if($_GET['a'] == "revisions")
	{
		$action = "revisions";
		
		if(is_array($user_data))
		{
			// Page setup
			include($config['template_path'] . "header.php");
			include($config['template_path'] . "navigation.php");
			include($config['template_path'] . "user/navigation.php");
?>
	<h3 class="title"><?php echo lang('revisions_to_title'); ?></h3>
	
	<div class="content">
<?php
			// Grab revisions
			$results = $database->query( "SELECT * FROM `revisions` WHERE `user_id` = '{$user_data['id']}'" );
						
			if($database->num( $results ) < 1)
			{
?>
		No revisions yet.
<?php
			}
			else
			{
				while($row = $database->fetch($results))
				{
?>
		<div class="plugin">
			<span class="status">
				r<?php echo $row['id']; ?>
			</span>
			
			<h3 class="name"><?php echo $row['text']; ?></h3>
			<span title="<?php echo date($config['date_format'], (($row['date'] + $config['zone']))); ?>"><?php echo ago((($row['date'] + $config['zone']))); ?> ago</span>
		</div>
<?php
				}
			}
?>
	</div>
<?php
			add_hook('footer_right', 'copyright', array());
					
			include($config['template_path'] . "footer.php");
		}
		else
		{
			print_out(lang('error_not_logged'), lang('redirecting'));
		}
	}
	
	if($_GET['a'] == "manage")
	{
		// Are they logged in?
		if(!$_SESSION['logged_in'])
		{
			header('location: ' . $config['url_path']);
		}
		
		// Not admin? Go home!
		if(!$user_data['admin'])
		{
			header('location: ' . $config['url_path']);
		}
		
		/**
		 * Include header
		 */
		include($config['template_path'] . "header.php");

		/**
		 * Include navigation
		 */
		include($config['template_path'] . "navigation.php");

		/**
		 * Include admin navigation
		 */
		include($config['template_path'] . "admin/navigation.php");
		
		if(isset($_GET['settings']))
		{
			exit;
		}
		
		if(isset($_GET['edit']))
		{
			// Check to see if the id is real
			if(!alpha($_GET['edit'], 'numeric'))
			{
				$major_error = lang_parse('error_invalid_given', lang('id'));
			}
		
			// Check to see if the user is real
			if(!$major_error)
			{
				$edit_user = user_data($_GET['edit']);
				
				if(!$edit_user)
				{
					$major_error = lang('error_user_doesnt_exist');
				}
			}
			
			// No title?
			if($edit_user['title'] == "")
			{
				// Cannot edit someone with no title
				$major_error = lang('error_no_title');
			}
			
			// Check the title :D
			if(!$major_error)
			{
				if(isset($_POST['submit']))
				{
					// Setup data
					$data = $_POST['title'];
					
					// Any in there?
					if($data != "")
					{
						// Is it banned?
						if(!in_array($data, $config['banned_titles']))
						{
							// Check the length
							$length = length($data, 2, 32);
							
							if($length)
							{
								if($length == "TOO_LONG")
								{
									$error = lang('error_title_too_long');
								}
								else
								{
									$error = lang('error_title_too_short');
								}
							}
							
							// Now we update it if we can.
							if(!$error)
							{
								// Escape and search for the revision.
								$current = $database->escape($edit_user['title']);								
								$current_revision = $database->query("SELECT * FROM `revisions` WHERE `text`='{$current}' and `user_id` = '{$edit_user['id']}' LIMIT 1");
								
								// Check it
								if($database->num($current_revision) < 1)
								{
									$error = lang('error_revision_lost');
								}
								else
								{
									// Fetch the data
									$current_revision = $database->fetch($current_revision);
									
									// Update it
									update_revision($current_revision['id'], $data);
									
									// Clean the data
									$data = $database->escape($data);
									
									// Update user
									update_user($edit_user['id'], false, 'title', $data);
									
									// Update our variable
									$edit_user = user_data($_GET['edit']);
								}
							}
						}
						else
						{
							$error = lang('error_banned_title');
						}
					}
				}
			}
				
?>
		<td valign="top" align="left">
			<table class="ac" width="100%" cellpadding="5" cellspacing="0">
			<form method="post">
				<tr>
					<td class="title"><?php echo lang('admin_cp'); ?> - <?php echo lang('editing_title'); ?>: <?php echo $edit_user['styled_name']; ?></td>
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
							<dt><?php echo lang('title_c'); ?></dt>
							<dd><input type="text" name="title" class="border" value="<?php echo $edit_user['title']; ?>" style="width: 50%"></dd>
						</dl>
						<dl class="input">
							<dt>&nbsp;</dt>
							<dd><input type="submit" class="button" name="submit" value="submit"></dd>
						</dl>
     				</td>
    			</tr>
			</form>
			</table>
    	</td>
	</tr>
</table>
<?php
			exit;
		}
		
		// Start point
		@$page = $_GET['page'];

		// What page are we on?
		if(is_numeric($page))
		{
			if (!isset($page) || $page < 0) $page = 0;
		}
		else
		{
			$page = 0;
		}
			
		// Start point
		$start = $page * 20;

		// Check the numbers to fetch.
		if(isset($start))
		{
			if(is_numeric($start))
			{
				$users = user_data(false, false, intval($start), 20);
			}
			else
			{
				$users = user_data(false, false, 0, 20);
			}
		}
		else
		{
			$users = user_data(false, false, 0, 20);
		}
		
		// User count
		$user_count = count_users();

		// Users per page
		$user_pagination = generate_pagination($config['url_path'] . '/plugins/titles.php?a=manage', $user_count, 20, $start);
		
?>
		<td valign="top" align="left">
			<table class="ac" width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="3" class="title"><?php echo lang('admin_cp'); ?> - <?php echo lang('manage_titles'); ?></td>
				</tr>
				<tr>
					<td class="item key"><?php echo lang('username'); ?></td>
					<td align="center" class="item key"><?php echo lang('title_c'); ?></td>
					<td class="item key"><?php echo lang('actions'); ?></td>
				</tr>
<?php foreach($users as $user){ ?>
				<tr>
					<td class="item"><?php echo $user['styled_name']; ?></td>
					<td align="center" class="item grey"><?php if($user['title'] != "") { echo $user['title']; } else { echo 'None!'; } ?></td>
					<td class="item key"><a href="<?php echo $config['url_path']; ?>/plugins/titles.php?a=manage&edit=<?php echo $user['id']; ?>"><?php echo lang('edit'); ?></a></td>
				</tr>
<?php } ?>
<?php if($user_pagination){ ?>
				<tr>
					<td colspan="3"><?php echo $user_pagination; ?></td>
				</tr>
<?php } ?>
				<tr>
					<td colspan="3" align="right">Titles by <a href="http://ninkobb.com">Nijiko</a> - Version: 1.3</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
	}
}

/**
 * Setup user title if no title is present.
 */
function get_title($data)
{
	if(!is_array($data))
	{
		return 'err';
	}

	if(!$data['title'])
	{
		if($data['admin'])
		{
			$data['title'] = lang('administrator_title');
		}
		else if($user_data['moderator'])
		{
			$data['title'] = lang('moderator_title');
		}
		else
		{
			$data['title'] = lang('member_title');
		}
	}

	// Show banned regardless of anything if they are banned.
	if($data['banned'])
	{
		$data['title'] = lang('banned_title');
	}
	
	return $data['title'];
}

/**
 * Installs plugin
 */
function install_titles()
{
	global $database;
	
	$database->query("CREATE TABLE IF NOT EXISTS `revisions` (`id` int(255) NOT NULL auto_increment, `user_id` mediumint(255) NOT NULL, `date` text NOT NULL, `text` text NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;") or die(mysql_error());
}

/**
 * Uninstall plugin
 */
function uninstall_titles()
{
	global $database;
	
	$database->query("DROP TABLE IF EXISTS `revisions`") or die(mysql_error());
}

/**
 * Inserts the title input into profile edit page
 * @global array
 * @global array
 */
function profile_input()
{
	global $config, $user_data;
	
	echo '<dl class="input">' . "\n" .
			'<dt>' . lang('title_c') . '</dt>'. "\n" .
			'<dd><input type="text" class="border cp" name="title" value="' . switchs($user_data['title'], $_POST['location']). '"></dd>' ."\n" .
		 '</dl>' . "\n";
}

/**
 * Inserts the title input into profile edit page
 * @global array
 * @global array
 */
function profile_nav_link($action)
{
	global $config, $user_data;
			
	echo '<li><a href="'. $config['url_path']. '/plugins/titles.php?a=revisions" class="'. equals($action, "revisions", "menu-current", "menu") .'">'. lang('revisions_c') .'</a></li>';
}

/**
 * Inserts the title input into profile edit page
 * @global array
 * @global array
 */
function admin_nav_link($action)
{
	global $config, $user_data;
			
	echo '<li><a href="'. $config['url_path']. '/plugins/titles.php?a=manage" class="'. equals($action, "revisions", "menu-current", "menu") .'">Manage '. lang('revisions_c') .'</a></li>';
}

/**
 * Cleans up the guest array
 * @global array
 * @global array
 */
function profile_edit()
{
	global $config, $database, $user_data, $errors, $key, $data;
	
	// Check the data, output error into errors array if there was an error.
	if($key == "title")
	{
		// Check the data, output error into errors array if there was an error.
		if($data != "")
		{
			if(!in_array($data, $config['banned_titles']))
			{
				$length = length($data, 2, 32);
				
				if($length)
				{
					if($length == "TOO_LONG")
					{
						$errors[$key] = lang('error_title_too_long');
					}
					else
					{
						$errors[$key] = lang('error_title_too_short');
					}
				}
				else
				{
					// Clean the data
					$data = $database->escape($data);
					
					// update user
					update_user($user_data['id'], false, $key, $data);
					
					// update revisions
					if(insert_revision($user_data['id'], $data))
					{
						$errors[$key] = insert_revision($user_data['id'], $data);
					}
				}
			}
			else
			{
				$errors[$key] = lang('error_banned_title');
			}
		}
		else
		{
			$errors[$key] = lang_parse('error_invalid_chars', array(lang('title_c')));
		}
	}
}

/**
 * Insert revisions
 * @global array
 * @global resource
 * @param integer $user_id user id
 * @param string $text contains the revision to title
 * @return boolean
 */
function insert_revision($user_id, $text)
{
	global $config, $database;
	
	$result = $database->query( "INSERT INTO `revisions` (`user_id`,`date`,`text`) VALUES ('{$user_id}','".time()."','{$text}'); " );
	
	// is there a result?
	if($result)
	{
		return false;
	}
	else
	{
		return mysql_error;
	}
}

/**
 * Updates revisions
 * @global array
 * @global resource
 * @param integer $revision the revision to update
 * @param string $text contains the revision to title
 * @return boolean
 */
function update_revision($revision, $text)
{
	global $database;
	
	if(!alpha($revision, 'numeric'))
	{
		return false;
	}
	
	// Clean the data
	$text = $database->escape($text);
	
	// Update the revision
	$result = $database->query( "UPDATE `revisions` SET `text`='{$text}' WHERE `id` = '{$revision}'" );
}

function copyright(){ echo "Titles w/ Revisions Plugin v1.3 |"; }

?>