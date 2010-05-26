<?php
/**
 * register.php
 * 
 * Sign up form!
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 */
 
include("include/common.php");

// Are they already logged in?
if($_SESSION['logged_in'])
{
	header('Location: index.php');
}

// Defaults
$error = false;
$success = false;

// Have they submitted the form yet?
if(isset($_POST['submit']))
{
	// did they fall for the honeypot?
	if($_POST['username'])
	{
		$error = "bot.";
	}
	else
	{
		load_hook('registration_check');
		
		if(!$error)
		{
			// If they want to validate age then ok!
			if($config['age_validation'])
			{
				// Age conversion
				$age = "{$_POST['month']}/{$_POST['day']}/{$_POST['year']}";
			}
			else
			{
				$age = false;
			}
			
			// The results
			$result = add_user($_POST['blatent'], $_POST['password'], $_POST['pagain'], $_POST['email'], $age);
			
			// Check the results?
			if(is_string($result))
			{
				// String is instant error.
				$error = $result;
			}
			else
			{
				if($result === false)
				{
					$error = lang('error_unknown');
				}
				else
				{
					if(is_numeric($result))
					{
						switch($result)
						{
							case 1: header('location: index.php'); break;
							case 904: $success = lang_parse('success_reg_email_msg', array($_POST['email'])); break;
							default: header('location: index.php'); break;
						}
					}
					else
					{
						header('location: index.php');
					}
				}
			}
		}
	}
}

// Header
include($config['template_path'] . "header.php");

if(isset($_GET['e']))
{
	$result = validate_user($_GET['e'], $_GET['k']);
	
	if($result === false)
	{
		$error = lang('error_unknown');
	}
	else if($result === true)
	{
		print_out(lang('account_verified'), lang('redirect'));
	}
	else
	{
		if(is_numeric($result))
		{
			switch($result)
			{
				case 908: $error = lang('error_user_doesnt_exist'); break;
				case 905: $error = lang_parse('error_invalid_given', array(lang('email'))); break;
				case 906: $error = lang_parse('error_no_given', array(lang('key'))); break;
				case 907: $error = lang_parse('error_invalid_given', array(lang('key'))); break;
				case 904: $error = lang_parse('error_no_given', array(lang('email'))); break;
				default: print_out(lang('account_verified'), lang('redirect')); break;
			}
		}
		else
		{
			print_out(lang('account_verified'), lang('redirect'));
		}
	}
}

// Header
include($config['template_path'] . "navigation.php");

// Registration Form
include($config['template_path'] . "user/register.php");

// Footer
include($config['template_path'] . "footer.php");
?>