<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * common.php
 * 
 * Includes functions commonly used througout the entire script
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage functions
 */
 
/**
 * Reads all files and prints data into an array
 * @param string $path directory where files are located
 * @return array
 */
function read_files($path)
{
	$handle = @opendir($path) or die("Unable to open $path");
	$file_arr = array();
	while ($file = readdir($handle)) {
		$file_arr[] = $file;
	}
	return $file_arr;
	closedir($handle);
}

 
/**
 * Parses urls that start with http(s) into clickable links
 * @param string $text data to be scanned for links and replaced
 * @param boolean $shorten do we want to shorten?
 * @return string
 */
function clickable($text, $shorten = false)
{
	if($shorten)
	{
		if (strlen($text) > 32)
		{
			$offset1 = ceil(0.65 * 32) - 2;
			$offset2 = ceil(0.30 * 32) - 1;

			$text = substr($text, 0, $offset1) . '...' . substr($text, -$offset2);
		}
	}
	else
	{
		$text = preg_replace( "/(?<!href=[\"\'])(?<!src=[\"\'])((ht|f)tps?:\/\/[^\s\r\n\t<>\"\'\!\(\)]+)/ie", "'<a href=\"$1\" title=\"$1\">'. clickable(\"$1\", true) .'</a>'", $text);
	}
	
	return $text;
}

/**
 * Prints out a redirection / error template.
 * @global array
 * @param mixed $title subject of printing out
 * @param mixed $body the main details
 * @param boolean|string $redirect do we redirect or not? if so where to?
 * @return string
 */
function print_out($title, $body, $redirect = true)
{
	global $config;
	
	echo "<div class='container'>";
	echo "<div class='title'>{$title}</div><div class='errormessage'>{$body}</div>";
	echo "</div>";
	
	if($redirect)
	{
		if(is_string($redirect))
		{
			$url = $redirect;
		}
		else
		{
			$url = $config['url_path'];
		}
	
		echo "<meta http-equiv='refresh' content='3;url={$url}'>";
	}
	
	exit;
}

/**
 * Check to see if gethostname() function already exists.
 */
if(!function_exists('gethostname'))
{
	/**
	 * Retrieves the host name of a vistor / user
	 * @return string
	 */
	function gethostname() 
	{
		$hostaddress = getenv('REMOTE_ADDR');
		if (!$hostaddress) { $hostaddress = getenv('REMOTE_HOST'); }
		$hostaddress = @GetHostByAddr($hostaddress);
		return $hostaddress;
	}
}

/**
 * Our constantly rioting mailing function
 * @global array
 * @param string $email address email is to be sent to
 * @param string $subject subject of email being sent
 * @param string $message contents of email being sent
 * @return mixed
 */
function riot_mail($email, $subject, $message)
{
	global $config;
	
	$email = trim(preg_replace('#[\r\n]+#s', '', $email));
	$subject = trim(preg_replace('#[\r\n]+#s', '', $subject));
	
	$headers = 'From: '.$config['email_sender']."\r\n".'Date: '.date('r')."\r\n".'MIME-Version: 1.0'."\r\n".'Content-transfer-encoding: 8bit'."\r\n".'Content-type: text/plain; charset=iso-8859-1'."\r\n".'X-Mailer: RiotPix Mailer';
	
	mail($email, $subject, $message, $headers);
}

/**
 * Switches return value based on input data
 * @param mixed $variable variable we are checking
 * @param mixed $default variable to be set incase $variable was false
 * @return mixed
 */
function switchs($variable, $default = "")
{
	if($variable != "" || !empty($variable))
	{
		return $variable;
	}
	else
	{
		return $default;
	}
}

/**
 * Checks $variable against $equals, Outputs $true / $false
 * @param mixed $variable the data we are checking $equal against
 * @param mixed $equals the data we check against $variable
 * @param mixed $true data output on true
 * @param mixed $false data output on false
 * @return mixed
 */
function equals($variable, $equals = "", $true = "", $false = "")
{
	if($variable == $equals)
	{
		if($true != "")
		{
			return $true;
		}
		else
		{
			return true;
		}
	}
	else
	{
		if($false != "")
		{
			return $false;
		}
		else
		{
			return false;
		}
	}
}

/**
 * Limits a string by character amount, and adds an ending such as &hellip;
 * @param string $str string to be limited
 * @param integer $n characters $str is limited to
 * @param mixed $end_char data to be appended to $str upon limiting
 * @return string
 */
function character_limiter($str, $n = 500, $end_char = '&#8230;')
{
	if (strlen($str) < $n)
	{
		return $str;
	}
	
	$str = preg_replace("/\s+/", ' ', preg_replace("/(\r\n|\r|\n)/", " ", $str));

	if (strlen($str) <= $n)
	{
		return $str;
	}
	
	$out = "";
	foreach (explode(' ', trim($str)) as $val)
	{
		$out .= $val.' ';			
		if (strlen($out) >= $n)
		{
			return trim($out).$end_char;
		}		
	}
}

function substru($str,$from,$len)
{
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $from .'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $len .'}).*#s','$1', $str);
}

/**
 * Returns a string representation of the date, of differing format depending on how recent the date is.
 * @param integer $_date unix timestamp created with time(), mktime(), strtotime()
 * @return string
 */
function nice_date( $_date ) {
	$date_ymd = date( "Ymd", $_date );
	$now = time();
	
	if ( $date_ymd == date( "Ymd", $now + 86400 ) ) { // tomorrow
		return lang('tomorrow');
	} elseif ( $date_ymd == date( "Ymd", $now ) ) { // today
		return lang('today');
	} elseif ( $date_ymd == date( "Ymd", $now - 86400 ) ) { // yesterday
		return lang('yesterday');
	} elseif ( ( $now < $_date ) && ( abs( $now - $_date ) < 518400 ) ) { // next week
		return lang('next') . " ".date( "l", $_date );
	} elseif ( ( $now > $_date ) && ( abs( $now - $_date ) < 518400 ) ) { // previous week
		return lang('last') . " ".date( "l", $_date );
	} elseif ( date( "Y", $now ) == date( "Y", $_date ) ) { // this year
		return date( "j M", $_date );
	} else {
		return date( "j M Y", $_date );
	}
}


/**
 * Returns a string interpreting how long ago a timestamp was created
 * @param integer $timestamp unix timestamp created with time(), mktime(), strtotime()
 * @return string
 */
function ago($timestamp) {
		
	// Store the current time
	$current_time = time();

	// Determine the difference, between the time now and the timestamp
	$difference = $current_time - $timestamp;
		
	// Set the periods of time
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		
	// Set the number of seconds per period
	$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

	// Determine which period we should use, based on the number of seconds lapsed.
	// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
	// Go from decades backwards to seconds       
	for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

	// Ensure the script has found a match
	if ($val < 0) $val = 0;
		
	// Determine the minor value, to recurse through
	$new_time = $current_time - ($difference % $lengths[$val]);
		
	// Set the current value to be floored
	$number = floor($number);
		
	// If required create a plural
	if($number != 1) $periods[$val].= "s";

	// Return text
	$text = sprintf("%d %s ", $number, $periods[$val]);   
		
	return $text;
}

/**
 * Splits text based on $start and $end giving you an array of text outside and text inside
 * @param string $text text to be split
 * @param mixed $start opening data
 * @param mixed $end ending data
 * @return array
 */
function split_text($text, $start, $end)
{
	$tokens = explode($start, $text);
	
	$outside[] = $tokens[0];
	
	$num_tokens = count($tokens);
	
	for ($i = 1; $i < $num_tokens; ++$i)
	{
		$temp = explode($end, $tokens[$i]);
		$inside[] = $temp[0];
		$outside[] = $temp[1];
	}
	
	return array($inside, $outside);
}

/**
 * Creates newlines that will be parsed by text areas.
 * @param string $string data to be replaced
 * @return string
 */
function br2nl($string)
{
	$return = eregi_replace('<br[[:space:]]*/?[[:space:]]*>', chr(13) . chr(10), $string);
	return $return;
}

/**
 * Creates newlines that will be parsed by text areas.
 * @param string $string data to be replaced
 * @return string
 */
function nl2nl($string)
{
	$return = str_replace(array('\n\r','\r','\n'), chr(13) . chr(10), $string);
	return $return;
}

/**
 * Closes bbcode tags
 * @param string $text data to be replaced
 * @return string
 */
function closetags($text)
{
	preg_match_all("/\[([a-zA-Z0-9]+)((\=)?([^\[]*?))?\]/is", $text, $result);
	$openedtags = $result[1];
	
	preg_match_all("/\[\/([a-z]+)\]/iU", $text, $result);
	$closedtags = $result[1];
	
	$len_opened = count($openedtags);
	
	if (count($closedtags) == $len_opened)
	{
		return $text;
	}
	
	$openedtags = array_reverse($openedtags);
	
	for ($i=0; $i < $len_opened; $i++)
	{
		if (!in_array($openedtags[$i], $closedtags))
		{
			$text .= '[/'.$openedtags[$i].']';
		}
	}
	
	return $text;
}

/**
 * Strips slashes recursively
 * @param mixed $value data to be stripped of slashes
 * @return mixed
 */
function stripslashes_deep($value) 
{
	if(is_array($value)) 
	{
		foreach($value as $k => $v) 
		{
			$return[$k] = $this->stripslashes_deep($v);
		}
	} 
	elseif(isset($value)) 
	{
		$return = stripslashes($value);
	}
	
	return $return;
}

/**
 * Converts tabs to the appropriate amount of spaces while preserving formatting
 *
 * @param string $text The text to convert
 * @param int $spaces Number of spaces per tab column
 * @return string The text with tabs replaced
 */
function tab2space($text, $spaces = 4)
{
    // Explode the text into an array of single lines
    $lines = explode("\n", $text);
    
    // Loop through each line
    foreach ($lines as $line)
    {
        // Break out of the loop when there are no more tabs to replace
        while (false !== $tab_pos = strpos($line, "\t"))
        {
            // Break the string apart, insert spaces then concatenate
            $start = substr($line, 0, $tab_pos);
            $tab = str_repeat(' ', $spaces - $tab_pos % $spaces);
            $end = substr($line, $tab_pos + 1);
            $line = $start . $tab . $end;
        }
        
        $result[] = $line;
    }
    
    return implode("\n", $result);
}

/**
 * Creates the pagination for the topics / posts
 * @param string $base_url the root path
 * @param integer $num_items the total number of items we have
 * @param integer $per_page how many items per page to show
 * @param integer $start_item the current item we are on
 * @param boolean $add_prev_next_text do we add the text prev / next?
 * @return string
 */
function generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = false)
{
	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 1) ? 1 : $per_page;

	$seperator = '<span class="page-sep">, </span>';
	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}

	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : '&amp;';
	
	// Make sure we aren't trying to do a little hacky jive
	if($total_pages < $on_page)
	{
		$on_page = $total_pages;
		$start_item = $num_items;
	}

	$page_string = ($on_page == 1) ? '<span class="page-selected">1</span>' : '<a href="' . $base_url . '" class="page-item">1</a>';

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 4), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + 4), 6);

		$page_string .= ($start_cnt > 0) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<span class="page-selected">' . $i . '</span>' : '<a href="' . $base_url . $url_delim . 'page=' . (($i - 1)) . '" class="page-item">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<span class="page-selected">' . $i . '</span>' : '<a href="' . $base_url . $url_delim . 'page=' . (($i - 1)) . '" class="page-item">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<span class="page-selected">' . $total_pages . '</span>' : '<a href="' . $base_url . $url_delim . 'page=' . (($total_pages - 1)) . '" class="page-item">' . $total_pages . '</a>';

	if ($add_prevnext_text)
	{
		if ($on_page != 1)
		{
			$page_string = '<a href="' . $base_url . $url_delim . 'page=' . (($on_page)) . '" class="page-item page-prev">< prev</a>&nbsp;&nbsp;' . $page_string;
		}

		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . $url_delim . 'page=' . ($on_page) . '" class="page-item page-next">next ></a>';
		}
	}

	return $page_string;
}

/**
 * Cleans everything of utf8 illegal characters.
 * @return array
 */
function remove_bad_utf8()
{
	global $chars;

	$chars = array("\0", "\xc2\xad", "\xcc\xb7", "\xcc\xb8", "\xe1\x85\x9F", "\xe1\x85\xA0", "\xe2\x80\x80", "\xe2\x80\x81", "\xe2\x80\x82", "\xe2\x80\x83", "\xe2\x80\x84", "\xe2\x80\x85", "\xe2\x80\x86", "\xe2\x80\x87", "\xe2\x80\x88", "\xe2\x80\x89", "\xe2\x80\x8a", "\xe2\x80\x8b", "\xe2\x80\x8e", "\xe2\x80\x8f", "\xe2\x80\xaa", "\xe2\x80\xab", "\xe2\x80\xac", "\xe2\x80\xad", "\xe2\x80\xae", "\xe2\x80\xaf", "\xe2\x81\x9f", "\xe3\x80\x80", "\xe3\x85\xa4", "\xef\xbb\xbf", "\xef\xbe\xa0", "\xef\xbf\xb9", "\xef\xbf\xba", "\xef\xbf\xbb", "\xE2\x80\x8D");

	function removing_bad_utf8($array)
	{
		global $chars;
		
		return is_array($array) ? array_map('removing_bad_utf8', $array) : str_replace($chars, '', $array);
	}

	$_GET = removing_bad_utf8($_GET);
	$_POST = removing_bad_utf8($_POST);
	$_COOKIE = removing_bad_utf8($_COOKIE);
	$_REQUEST = removing_bad_utf8($_REQUEST);
}
?>