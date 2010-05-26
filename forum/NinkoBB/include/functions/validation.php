<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * validation.php
 * 
 * Includes functions for validation of specific items
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage functions
 */

/**
 * Checks a string against a set of regular expressions
 * @param string $string data that will be checked against regular expression
 * @param string $check which expression to check against
 * @return boolean
 */
function alpha($string, $check = 'alpha')
{
	switch($check)
	{
		case "alpha":
			$regexp = "([a-z0-9])";
		break;
		
		case "alpha-space":
			$regexp = "([a-z0-9+ ])";
		break;
		
		case "alpha-underscore":
			$regexp = "([a-z0-9\_])";
		break;
		
		case "alpha-spacers":
			$regexp = "([a-z0-9-[\]_+ ])";
		break;
		
		case "alpha-extra":
			$regexp = "([a-z0-9+ \\\,\?\`\'\!\.\;\:\[\]\&\%\^\*\$\@\(\)\<\-\_\+\=])"; $modifier = "sm";
		break;
		
		case "num-dash": case "numeric-dash":
			$regexp = "([0-9-])";
		break;
		
		case "num": case "numeric":
			$regexp = "([0-9])";
		break;
		
		case "strict":
			$regexp = "([a-z])";
		break;
		
		case "md5":
			$regexp = "^[A-Fa-f0-9]{32}$"; $custom = true;
		break;
		
		case "email":
			$regexp = "^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$"; $modifier = "ix"; $custom = true;
		break;
		
		case "url":
			$regexp = "^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}((:[0-9]{1,5})?\/.*)?$"; $modifier = "i"; $custom = true;
		break;
	}
	
	if(!$custom)
	{
		return (preg_match('/^'.$regexp.'+$/'.$modifier.'i',$string)) ? true : false;
	}
	else
	{
		return (preg_match('/'.$regexp.'/' . $modifier ,$string)) ? true : false;
	}
}

/**
 * Checks for a valid email
 * @param string $string data that will be checked as valid email
 * @return boolean
 */
function is_email($string)
{
	return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $string)) ? false : true;
}
	
/**
 * Checks for a valid link
 * @param string $string data that will be checked as valid url
 * @return boolean
 */
function is_url($url)
{
	return (!preg_match('/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i',$url)) ? false : true;
}
	
/**
 * Re-creates url structure and creates an html link out of it
 * @param string $url url to be re-structured
 * @param string $link title of url
 * @return string
 * @see parse()
 */
function url_tag($url, $link = '')
{
	$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
	
	if (strpos($url, 'www.') === 0)
	{
		$full_url = 'http://'.$full_url;
	}
	else if (strpos($url, 'ftp.') === 0)
	{
		$full_url = 'ftp://'.$full_url;
	}
	else if (!preg_match('#^([a-z0-9]{3,6})://#', $url, $matches))
	{
		$full_url = 'http://'.$full_url;
	}
	
	$link = ($link == '' || $link == $url) ? ((strlen($url) > 55) ? substr($url, 0 , 39).' ... '.substr($url, -10) : $url) : stripslashes($link);
	
	return '<a href="'.$full_url.'">'.$link.'</a>';
}
	
/**
 * Checks for a valid md5 hash
 * @param string $string data to be checked as valid md5
 * @return boolean
 */
function is_md5($string)
{
	return preg_match('/^[A-Fa-f0-9]{32}$/', $string);
}
	
/**
 * Strip repeat characters and spaces
 * @param string $string data that will be stripped of repeats
 * @return string
 */
function strip_repeat($string)
{
	# Do not allow repeated spaces
	$string = preg_replace("/(\s){2,}/",'$1',$string);

	# No multiple repeats after any space.
	$string = preg_replace('{( ?.)\1{4,}}','$1$1$1',$string);

	return $string;
}
	
/**
 * Checks the lenght of a string
 * @param string $string data that will be stripped of repeats
 * @param integer $min minimum length that $string can be
 * @param integer $max maximum length that $string can be
 * @return string
 * @see post(), update(), add_user()
 */
function length($string, $min = 0, $max = 0)
{
	if(empty($string)){
		return 'EMPTY';
	} 
	else if(strlen($string) < $min)
	{
		return 'TOO_SHORT';
	} 
	else if(strlen($string) > $max)
	{
		return 'TOO_LONG';
	}
	
	return false;
}
	
/**
 * Very simple age check
 *
 * Didn't want to delve really deep into age verification due to leap years and a thousand other variables.
 * Subtracts required from the year to obtain how old they are if it they aren't old enough then false.
 * @param integer $year birth year
 * @param integer $required required age
 * @return boolean
 * @see add_user()
 */
function age_limit($year, $required)
{
	if($year - $required)
	{
		return false;
	}
	
	return true; // date malformed
}


/**
 * Cleans mysql values
 * @param mixed $value data to be cleaned
 * @return mixed
 */
function mysql_clean($value)
{
	global $database;
	
	return $database->escape($value);
}

/**
 * Cleans form values
 * @param mixed $value data to be cleaned
 * @param boolean $strip_tags do we strip tags or not?
 * @return mixed
 */
function field_clean($value, $strip_tags = false)
{
	$value = trim($value);
	
	if($strip_tags){ $value = strip_tags($value); }
	
	$value = stripslashes($value);
	
	$value = htmlentities($value, ENT_QUOTES, "UTF-8");
	
	# Return it
	return $value;
}
	
/**
 * Clean user input
 * @param mixed $user_input data to be cleaned
 * @return mixed
 */
function clean_input($user_input)
{
	global $database;
	
	$user_input = trim($user_input);

	// check to see if magic quotes are turned on
	if(get_magic_quotes_gpc())
	{
		// clean the user's inpt using stringspashes to
		// unquote a quoted string
		$user_input = stripslashes($user_input);
	}

	// check for numeric values, if not
	// clean it
	if(!is_numeric($user_input))
	{
		$user_input = $database->escape($user_input);
	}
	
	return($user_input);
}
?>