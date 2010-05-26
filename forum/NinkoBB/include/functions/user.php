<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * user.php
 * 
 * Functions that relate directly to the user
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage functions
 */
	
/**
 * Signs user in based on username or email
 * @global array 
 * @global resource
 * @param boolean|string $username username or false
 * @param boolean|string $email user email or false
 * @param string $password user password
 * @return int|boolean
 */
function login($username, $email = false, $password)
{
	global $config, $database;
	
	// Error codes
	//	904		- empty email
	//	905		- invalid email
	//	906		- invalid username
	//	907		- invalid chars in username
	//  908		- banned [.]
	
	// email is true?
	if($email)
	{
		// is just true or null?
		if(empty($email))
		{
			return 904;
		}

		// are we using email?
		if(is_email($email))
		{
			// Encrypt the password
			$password = md5($password);
			
			// login before
			load_hook('login_before');
			
			// Query
			$query = "SELECT * FROM `users` WHERE `email` = '{$email}' AND `password` = '{$password}' LIMIT 1";
			
			// Return Data
			$return = $database->query( $query );
			
			// Exists?
			if($database->num( $return ) > 0)
			{
				// Finally return Results
				$user_data = $database->fetch( $return );
				
				// Are they banned?
				if($user_data['banned'])
				{
					return 908;
				}
				
				// What is this user classified as?
				$type = type($user_data['username']);
				
				// Tell us what they are
				switch($type)
				{
					case 1: $_SESSION['admin'] = true; break;
					case 2: $_SESSION['moderator'] = true; break;
					case 3: $_SESSION['banned'] = true; break;
					default: break;
				}
				
				// logged in
				load_hook('login_after');
				
				// Update their session
				$_SESSION['logged_in']  = true;
				$_SESSION['user_id']	= $user_data['id'];
				$_SESSION['user_name']	= $user_data['username'];
				
				// Last seen
				update_user($user_data['id'], false, 'last_seen', time());
				
				// Set Cookie
				if($config['allow_cookies'])
				{
					setcookie('login', "{$username}|{$password}", (time()+86400*365*2), $config['cookie_domain']);
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			// guess it wasn't a real one.
			return 905;
		}
	}
	else
	{
		// Was it empty?
		if(empty($username))
		{
			return 906;
		}
		
		// Checking for invalid characters
		if(!alpha($username, 'alpha-underscore'))
		{
			return 907;
		}
		
		// Encrypting password
		$password = md5($password);
		
		// login before
		load_hook('login_before');
		
		// Query
		$query = "SELECT * FROM `users` WHERE `username` = '{$username}' AND `password` = '{$password}' LIMIT 1";
		
		// Return Data
		$return = $database->query( $query );
		
		// Exists?
		if($database->num( $return ) > 0)
		{
			// Finally return Results
			$user_data = $database->fetch( $return );
			
			// Are they banned?
			if($user_data['banned'])
			{
				return 908;
			}
			
			// What is this user classified as?
			$type = type($user_data['username']);
			
			// Tell us what they are
			switch($type)
			{
				case 1: $_SESSION['admin'] = true; break;
				case 2: $_SESSION['moderator'] = true; break;
				case 3: $_SESSION['banned'] = true; break;
				default: break;
			}
			
			// login before
			load_hook('login_after');
			
			// Update their session
			$_SESSION['logged_in']  = true;
			$_SESSION['user_id']	= $user_data['id'];
			$_SESSION['user_name']	= $user_data['username'];
			
			// Last seen update
			update_user($user_data['id'], false, 'last_seen', time());
			
			// Set Cookie
			if($config['allow_cookies'])
			{
				setcookie('login', "{$username}|{$password}", (time()+86400*365*2), $config['cookie_domain']);
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}
}
	
/**
 * Adds user to the database
 *
 * Registration function, this controls the sign up functionality.
 * @global array
 * @global resource
 * @param string $username username of user being added
 * @param string $password password of user being added
 * @param string $password_again password again to be checked against first $password
 * @param string $email email incase email registration is turned on
 * @param string $age mm/dd/yyyy
 * @return string|boolean
 */
function add_user($username, $password, $password_again, $email, $age = false)
{
	global $config, $database;
	
	// 904	- Registration complete, needs to validate email!
	
	// Check Username
	if(!alpha($username, 'alpha-underscore'))
	{
		return lang_parse('error_invalid_chars', array(lang('username')));
	}
	
	// Username Taken
	if(username_check($username))
	{
		return lang('error_username_taken');
	}
	
	// Check Username Length
	$length = length($username, $config['min_name_length'], $config['max_name_length']);
	
	if($length)
	{
		if($length == "TOO_LONG")
		{
			return lang('error_username_too_long');
		}
		else
		{
			return lang('error_username_too_short');
		}
	}
	
	// Check Password Length
	$length = length($password, $config['min_name_length'], $config['max_name_length']);
	
	if($length)
	{
		if($length == "TOO_LONG")
		{
			return lang('error_password_too_long');
		}
		else
		{
			return lang('error_password_too_short');
		}
	}
	
	// Setup Passwords
	if($password == $password_again)
	{
		$raw_pass = $password;
		$password = md5( $password );
	}
	else
	{
		return lang('error_password_match');
	}
	
	// Check email
	if(!is_email($email))
	{
		return lang_parse('error_invalid_given', array(lang('email')));
	}
	
	// Banned?
	$query = "SELECT * FROM `users` WHERE `email` = '{$email}' AND `banned` = '1' LIMIT 1";
	$result = $database->query( $query );
	
	if($database->num($result) > 0)
	{
		return lang('error_banned_email');
	}
	
	// Exist?
	$query = "SELECT * FROM `users` WHERE `email` = '{$email}' LIMIT 1";
	$result = $database->query( $query );
	
	// Email exists
	if($database->num($result) > 0)
	{
		return lang('error_email_used');
	}
	
	// Do we have to validate age?
	if($config['age_validation'])
	{
		if($age)
		{
			// Start grabbing age data~
			$age_data = explode('/', $age);
			
			if(alpha($age_data[2], 'numeric'))
			{
				if(strlen($age_data[2]) < 4)
				{
					return lang('error_year_invalid');
				}
				
				$old_enough = age_limit($age_data[2], $config['age_validation']);
				
				if(!$old_enough)
				{
					return lang_parse('error_year_young', array($config['age_validation']));
				}
			}
			else
			{
				return lang_parse('error_given_not_numeric', array(lang('year_c')));
			}
		}
		else
		{
			return lang('error_year_invalid');
		}
	}
	
	load_hook('add_user_check');
	
	// Finally Add user
	if($config['email_validation'])
	{
		// The Key for Validation
		$key   = md5( $username . $email . substr( microtime(), 1, 3) );
		
		// The query
		$query = "INSERT INTO `users` (`username`,`password`,`email`,`join_date`,`age`,`active`,`key`) VALUES ('{$username}', '{$password}', '{$email}', '".time()."','{$age}','0','{$key}')";
	}
	else
	{
		// The query
		$query = "INSERT INTO `users` (`username`,`password`,`email`,`join_date`,`age`,`active`) VALUES ('{$username}', '{$password}', '{$email}', '".time()."','{$age}','1')";
	}
	
	// Return Data
	if( $result = $database->query( $query ) )
	{
		// Auto login
		if(!$config['email_validation'])
		{
			// log them in
			login($username, false, $raw_pass);
			
			// Return True
			return true;
		}
		else
		{
			// Subject / Message replacing
			$subject = str_replace('{site_name}', $config['site_name'], $config['email_subject']);
			$subject = str_replace('{username}', $username, $subject);
			$subject = str_replace('{email}', $email, $subject);
			
			// The message
			$message = str_replace('{site_name}', $config['site_name'], $config['email_message']);
			$message = str_replace('{username}', $username, $message);
			$message = str_replace('{email}', $email, $message);
			$message = str_replace('{link}', $config['url_path'] . "/register.php?e={$email}&amp;k={$key}", $message);
			
			// Mail the results
			riot_mail($email, $subject, nl2nl($message));
			
			// Return the results
			return 904;
		}
	}
	else
	{
		return false;
	}
}

/**
 * Validates user
 *
 * Validates user using key sent to the user via email upon registration. Can be turned on/off
 * @global resource
 * @param string $email email of user being validated
 * @param string $key key given at registration
 * @return integer|boolean
 */
function validate_user($email, $key)
{
	global $database;
	
	// Error codes
	//	904		- Email not given
	//	905		- Invalid email
	//	906		- No key given
	//	907		- Invaid key given
	//	908		- Invalid key / email combo
	
	// Empty email?
	if(empty($email))
	{
		return 904;
	}
	
	// Empty Key?
	if(empty($key))
	{
		return 906;
	}
	
	// Valid email?
	if(is_email($email))
	{
		// Valid md5?
		if(is_md5($key))
		{
			//Query
			$result = $database->query( "SELECT * FROM `users` WHERE `email` = '{$email}' AND `key` = '{$key}' LIMIT 1" );
			
			if($database->num($result) < 1)
			{
				return 908;
			}
			else
			{
				// The user data
				$user_data = $database->fetch($result);
				
				// update user fields
				$active = update_user($user_data['id'], false, 'active', 1);
				$key = update_user($user_data['id'], false, 'key', '');
				
				// What happened?
				if($active && $key)
				{
					// is it true?
					if(!alpha($active, 'numeric'))
					{
						// Example of codes
						switch($active)
						{
							case 905: return false; break;
							default: return true; break;
						}
					}
					
					// is it true?
					if(!alpha($key, 'numeric'))
					{
						// Example of codes
						switch($key)
						{
							case 905: return false; break;
							default: return true; break;
						}
					}
				}
			}
		}
		else
		{
			return 907;
		}
	}
	else
	{
		return 905;
	}
}

/**
 * Checks to see what type of user we are dealing with
 * @global resource
 * @param string $username username of user to be checked against
 * @return boolean|integer
 */
function type($username)
{
	global $database;
	
	// Don't trust anyone
	$username = $database->escape($username);
	
	// Select only admin from the user table with the username given.
	$data = $database->query("SELECT * FROM `users` WHERE `username` = '{$username}' LIMIT 1");
	
	// Check to see if any rows were returned
	if($database->num($data) < 0)
	{
		return false;
	}
	else
	{
		// There were, So return that they are infact an admin.
		$data = $database->fetch($data);
		
		// before
		load_hook('type_before');
		
		if($data['banned'])
		{
			return 3;
		}
		
		if($data['moderator'])
		{
			return 2;
		}
		
		if($data['admin'])
		{
			return 1;
		}
		
		// after
		load_hook('type_after');
		
		return false;
	}
}

/**
 * Checks to see whether user exists by username
 * @global resource
 * @param string $username username of user to be checked against
 * @return boolean|integer
 */
function username_check($username)
{
	global $database;
	
	// Select only id from the users table with the given username.
	$return = $database->query( "SELECT `id` FROM `users` WHERE `username` = '{$username}' LIMIT 1" );
	
	// Exists?
	if($database->num( $return ) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Styles username based on information given.
 * @param array
 * @param array $data user data
 * @return boolean|integer
 */
function username_style($data)
{
	global $config;
	
	// If the user is an admin style the name, if they are banned style the name, if no style yet, nothing but normal.
	if($data['admin'])
	{
		$styled_name = "<a href='{$config['url_path']}/users.php?a=view&id={$data['id']}' class='admin'>{$config['admin_symbol']}{$data['username']}</a>";
	}
	else if($data['moderator'])
	{
		$styled_name = "<a href='{$config['url_path']}/users.php?a=view&id={$data['id']}' class='moderator'>{$config['moderator_symbol']}{$data['username']}</a>";
	}
	else if($data['banned'])
	{
		$styled_name = $data['username'];
	}
	else
	{
		$styled_name = "<a href='{$config['url_path']}/users.php?a=view&id={$data['id']}' class='username'>{$data['username']}</a>";
	}
	
	return $styled_name;
}

/**
 * Grab user information using an id or username
 * @global array
 * @global resource
 * @param boolean|integer $id id used to obtain data, or false for username
 * @param boolean|string $username username used to obtain data, or false for id
 * @param boolean|integer $limit limit how many users?
 * @param boolean|string $current username used to obtain data, or false for id
 * @return boolean|integer
 */
function user_data($id, $username = false, $current = false, $limit = 1)
{
	global $config, $database;
	
	/**
	 * Error codes
	 * 904		- Invalid username
	 * 905		- Invalid id
	 */
	 
	// Create the limit
	if($current)
	{
		$limit = "{$current},{$limit}";
	}
	 
	// Check to see if they are using username, If not go to the id check.
	if($username)
	{
		// Make sure that the username is valid if not return error 904
		if(alpha($username, 'alpha-underscore'))
		{
			// Select everything from the users table with the username given, limiting only one row.
			$result = $database->query( "SELECT * FROM `users` WHERE `username` = '{$username}' LIMIT {$limit}" );
			
			// Was there a row returned?
			if($database->num($result) > 0)
			{
				$result = $database->fetch($result);
				$result['styled_name'] = username_style($result);
				
				return $result;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return 904;
		}
	}
	else if($id)
	{
		// Check to see if id is really numeric, ie no dashes. If not return error 905
		if(alpha($id, 'numeric'))
		{
			// Select everything from the users table with the id given, limiting only one row.
			$result = $database->query( "SELECT * FROM `users` WHERE `id` = '{$id}' LIMIT {$limit}" );
			
			// Was there a row returned?
			if($database->num($result) > 0)
			{
				$result = $database->fetch($result);
				$result['styled_name'] = username_style($result);
				
				return $result;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return 905;
		}
	}
	else
	{
		// Select everything from the users table with the id given, limiting only one row.
		$result = $database->query( "SELECT * FROM `users` ORDER BY `username` ASC LIMIT {$limit}" );
			
		// Was there a row returned?
		if($database->num($result) > 0)
		{
			while($row = $database->fetch($result))
			{
				$row ['styled_name'] = username_style($row);
					
				// insert into rows
				$rows[] = $row;
					
			}
				
			return $rows;
		}
		else
		{
			return false;
		}
	}
}

/**
 * Count all the users or users in a certain amount of days.
 * @global resource
 * @param integer|boolean $days the amount of days you want the count to go back. e.g, a year would be 365 days.
 * @return integer
 */
function count_users($days = false)
{
	global $database;
	
	if($days)
	{
		// make sure its a number
		$days = intval($days);
		
		// How many?
		$query = "SELECT `id` FROM `users` WHERE `join_date` >= " . strtotime('-'.$days.' day');
	}
	else
	{
		$query = "SELECT `id` FROM `users`";
	}
	
	// fetch the count
	$result = $database->query($query);
	
	// return the amount of rows from the database
	return $database->num($result);
}
	
/**
 * Checks to see if the user set by id is online.
 * @global array
 * @global resource
 * @param integer $days the amount of days you want the count to go back. e.g, a year would be 365 days.
 * @return boolean
 */
function is_online($id)
{
	global $config, $database;
	
	// The time offset by the timeout set in configuration
	$time_between = time() - $config['user_online_timeout'];
	
	// Make sure that the id is an integer
	$id = intval($id);
	
	// Check to see if the users last seen is greater than the timeout
	$result = $database->query( "SELECT `id` FROM `users` WHERE `last_seen` > {$time_between} AND `id` = '{$id}' LIMIT 1" );
	
	// Is there a row?
	if($database->num($result) < 1)
	{
		return false;
	}
	else
	{
		return true;
	}
}

/**
 * Checks to see what users are online
 * @global array
 * @global resource
 * @param boolean $admins check for admins only?
 * @return array
 */
function users_online($admins = false)
{
	global $config, $database;
	
	// The time between them
	$time_between = time() - $config['user_online_timeout'];
	
	if($admins)
	{
		// Admin last seen
		$query = "SELECT `id`,`username` FROM `users` WHERE `admin` = '1' AND `last_seen` > {$time_between}";
	}
	else
	{
		// User last seen
		$query = "SELECT `id`,`admin`,`moderator`,`username` FROM `users` WHERE `last_seen` > {$time_between}";
	}
	
	// Fetch users last post
	$result = $database->query( $query );
	
	// is there a result?
	if($database->num($result) < 1)
	{
		return array('count' => 0, 'users' => false);
	}
	else
	{
		// The overall count
		$online['count'] = $database->num($result);
		$count = 1;
		
		// Making the list
		while($row = $database->fetch($result))
		{
			if($count == $online['count'])
			{
				$seperator = "";
			}
			else
			{
				$seperator = ", ";
			}
			
			$username = username_style($row);
			
			$online['users'] .= "{$username}{$seperator}";
			
			$count++;
		}
		
		// Returns array
		return $online;
	}
}

/**
 * Updates user
 *
 * Updates the user using either the id, or username. Only updates one field at a time.
 * @global resource
 * @param boolean|integer $id id used to update data, or false for username
 * @param boolean|string $username username used to update data, or false for id
 * @param string $field field on database to be updated
 * @param mixed $value data to update $field
 * @return boolean|integer
 */
function update_user($id, $username = false, $field, $value)
{
	global $database;
	
	/**
	 * Error codes
	 * 904		- Invalid username
	 * 905		- Invalid id
	 */
	
	// Did they give us a username? If not go to the id check.
	if($username)
	{
		// Is the username valid? If not return 904
		if(!alpha($username, 'alpha-underscore'))
		{
			return 904;
		}
		else
		{
			// Clean username
			$username = $database->escape($username);
			
			// Clean value, fields are clean as WE set them
			$value = $database->escape($value);
			
			// Insert Query / Result
			$result = $database->query( "UPDATE `users` SET `{$field}` = '{$value}' WHERE `username` = '{$username}' LIMIT 1" );
			
			// Did it work?
			if(mysql_num_rows( $return ) > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		// Check to see if the id is numeric, if not return 905 error
		if(!alpha($id, 'numeric'))
		{
			return 905;
		}
		else
		{
			// Clean value, fields are clean as WE set them
			$value = $database->escape(stripslashes($value));
			
			// Insert Query / Result
			$query = "UPDATE `users` SET `{$field}` = '{$value}' WHERE `id` = '{$id}' LIMIT 1";
			$result = $database->query($query);
			
			// Did it work?
			if($result)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}

/**
 * Grabs the users avatar by their id
 * @global array
 * @param integer $id used to retrieve the user data
 * @return string|boolean
 */
function get_avatar($id, $size = "100", $rating = "R")
{
	global $config;
	
	if(alpha($id, 'numeric'))
	{
		$data = user_data($id);
		
		if($data['avatar'])
		{
			switch($config['avatar_use'])
			{
				case "username": 
					// Avatar folder path
					$folder_path = $config['url_path'] . "/" . $config['avatar_folder_name'] . "/";
					
					// Do we md5?
					if($config['avatar_md5_use'])
					{
						$name = md5($data['username']);
					}
					else
					{
						$name = $data['username'];
					}
					
					// The outcome
					return $folder_path . $name . ".png";
				break;
				
				case "email": 
					// Avatar folder path
					$folder_path = $config['url_path'] . "/" . $config['avatar_folder_name'] . "/";
					
					// Do we md5?
					if($config['avatar_md5_use'])
					{
						$name = md5($data['email']);
					}
					else
					{
						$name = $data['email'];
					}
					
					// The outcome
					return $folder_path . $name . ".png";
				break;
				
				case "id": 
					// Avatar folder path
					$folder_path = $config['url_path'] . "/" . $config['avatar_folder_name'] . "/";
					
					// Do we md5?
					if($config['avatar_md5_use'])
					{
						$name = md5($data['id']);
					}
					else
					{
						$name = $data['id'];
					}
					
					// The outcome
					return $folder_path . $name . ".png";
				break;
			}
		}
		else
		{
			// Default to the gravatar
			$gravatar = get_gravatar($data['email'], $size, $rating);
			
			// Default
			return $gravatar;
		}
	}
	else
	{
		return false;
	}
}

/**
 * Fallback plan for when the user hasn't uploaded an avatar.
 *
 * @param string $email users email address, we will md5 this
 * @param integer $size this is the size of the avatar, width only such as 50, not 50x50
 * @param string $rating can be G, PG, R, OR X, default R
 * @return string|boolean
 */
function get_gravatar($email, $size, $rating = "R")
{
	// The size of the image
	$avatar_size = (is_numeric($size)) ? $size : '50';

	// Minimum rating for your site
	// Possible values (G, PG, R, X)
	$avatar_rating = $rating;

	// URL for Gravatar
	$url = "http://www.gravatar.com/avatar/%s?s=%s&r=%s&d=identicon";

	$avatar_url = sprintf(
		$url, 
		md5($email), 
		$avatar_size,
		$avatar_rating
	);
		
	return $avatar_url;
}

/**
 * Uploads an avatar to the site for the user to use.
 * @global array
 * @param string $use data from the user we are to use for the filename
 * @param resource $_FILES file to be uploaded
 * @return boolean|string
 */
function avatar_upload($use, $_FILES)
{
	global $config;
	
	if ( ! isset($_FILES['avatar']))
	{
		return 'NO_FILE';
	}
	else
	{
		// File Data
		$file_temp = $_FILES['avatar']['tmp_name'];		
		$file_name = $_FILES['avatar']['name'];
		$file_size = $_FILES['avatar']['size'];		
		$file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES['avatar']['type']);
		$file_type = strtolower($file_type);
		$file_ext  = explode('.', $file_name);
		$file_ext  = strtolower($file_ext[count($file_ext)-1]);
		list($file_width, $file_height) = getimagesize($file_temp);
		
		// Filesize to KiB
		if ($file_size > 0)
		{
			$file_size = round($file_size/1024, 2);
		}
		
		// Allowed to use this image?
		if(!in_array($file_ext, array('jpg','jpeg','png','gif')))
		{
			return "NOT_IMAGE";
		}

		// Is image?
		if(strpos($file_type, 'image/') === false)
		{
			return "NOT_IMAGE";
		}
		
		// Is correct filesize?
		if($file_size > $config['avatar_max_size'])
		{
			return "TOO_LARGE";
		}
		
		if($file_width > $config['avatar_max_width'] || $file_height > $config['avatar_max_height']){
			return "WRONG_DIMENSIONS";
		}
		
		switch($file_ext)
		{
			case 'gif': $old_img = ImageCreateFromGif($file_temp); 	break;
			case 'jpg': $old_img = ImageCreateFromJpeg($file_temp); break;
			case 'png': $old_img = ImageCreateFromPng($file_temp); 	break;
		}
		
		// create new image
		if($config['avatar_md5_use'])
		{
			$name = md5($use);
		}
		else
		{
			$name = $use;
		}
		
		@imagepng($old_img, $config['avatar_upload_path'] . $name . '.png');
		@chmod($config['avatar_upload_path'] . $name . '.png', 0777);
		
		return 'done';
	}
}
?>