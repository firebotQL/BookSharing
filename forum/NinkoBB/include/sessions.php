<?php
/**
 * sessions.php
 * 
 * User session and cookie management file
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 */
 
// gets cookie if there is one
@$login_cookie = $_COOKIE['login'];

// Check cookie first
if(!empty($login_cookie))
{
	// Check if they have no session which could happen.
	if(!isset($_SESSION['logged_in']))
	{
		// If they didn't lets give them a session.
		$data = explode(":", $login_cookie);
		
		$username = mysql_clean($data[0]);
		$password = mysql_clean($data[1]);
		
		$sql = "SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}'";
		$result = @$database->query($sql) or die("No.");
		
		// Cookies don't match and no session, so tell them to logout!
		if($database->num($result) < 1)
		{
			unset($username);
			unset($password);
				
			include($config['template_path'] . "header.php");
			
			print_out(lang('error_with_cookies'), lang_parse('error_cookie_body', array($config['url_path'] . '/logout.php')), false);
		}
		else if($database->num($result) > 0)
		{
			// Get the users data
			$user_data = $database->fetch($result);
			
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
			
			// Update their session
			$_SESSION['logged_in']  = true;
			$_SESSION['user_id']	= $user_data['id'];
			$_SESSION['user_name']	= $user_data['username'];
		}
	}
}
else
{
	// User tries logging in.
	if($_POST['login'])
	{
		$username = $_POST['username'];
		
		// due to current security concerns, we force the username to lowercase.
		$password = $_POST['password'];
		
		$results = login($username, false, $password);
		
		// Do we have results?
		if($results)
		{
			// Is the result numeric?
			if(is_numeric($results))
			{
				// What error do we show?
				switch($results)
				{
					case 904: $login_error = lang_parse('error_no_given', array(lang('email'))); break;
					case 905: $login_error = lang_parse('error_invalid_chars', array(lang('email'))); break;
					case 906: $login_error = lang_parse('error_no_given', array(lang('username'))); break;
					case 907: $login_error = lang_parse('error_invalid_chars', array(lang('username'))); break;
					case 908: $login_error = lang('error_banned'); break;
					default: $login_success = lang('welcome_back') . ", {$_SESSION['user_name']}"; break;
				}
			}
			else
			{
				// Incase your server doesn't classify booleans as numbers. Just incase.
				$login_success = lang('welcome_back') . ", {$_SESSION['user_name']}";
			}
		}
		else
		{
			$login_error = lang('error_invalid_user_pass');
		}
	}
}
?>