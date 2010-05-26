<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * language.php
 * 
 * Includes functions directed to language files
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage functions
 */
 
/**
 * Loads a language based on input data
 * @param string $language language to grab.
 * @return string
 */
function language($language)
{
	if(in_array($language, languages()))
	{
		// Include language
		include(BASEPATH . 'languages/' . $language . EXT);
	}
	else
	{
		// Include language
		include(BASEPATH . 'languages/en' . EXT);
	}
	
	// Return language
	return $lang;
}

/**
 * Grabs language string from key
 * @global array
 * @param string $key determines the string output
 * @return string
 */
function lang($key)
{
	global $lang;
	
	// Make sure the key is uppercase
	$key = strtoupper($key);
	
	// Does that key exist?
	if(!$lang[$key])
	{
		// So we know it needs to be translated.
		return '{' . $key . '}';
	}
	else
	{
		return $lang[$key];
	}
}

/**
 * Grabs language string from key and parses it
 * @global array
 * @param string $key determines the string output
 * @param array $args arguments to be passed through string
 * @return string
 */
function lang_parse($key, $args)
{
	global $lang;
	
	// Make sure the key is uppercase
	$key = strtoupper($key);
	
	// Does that key exist?
	if(!$lang[$key])
	{
		// So we know it needs to be translated.
		return '{' . $key . '}';
	}
	else
	{
		return mb_vsprintf($lang[$key], $args, $lang['ENCODING']);
	}
}

/**
 * Parses language name into language code
 * @global array
 * @param string $language language to grab.
 * @return mixed
 */
function languages($html = false)
{
	// Fetch the files
	$handle = @opendir(BASEPATH .'languages/') or die("Unable to open 'include/languages/'");
	$languages = array();
	
	while ($file = readdir($handle))
	{
		$languages[] = $file;
	}
	
	closedir($handle);
	
	// check if we want html or the array
	if($html)
	{
		// Start select
		echo '<select name="language" class="border" style="width: 40%">';
		
		foreach($languages as $language)
		{
			$language_ext = pathinfo($language, PATHINFO_EXTENSION);
			$language_name = pathinfo($language, PATHINFO_FILENAME);
				
			if($language_ext == "php")
			{
				echo '<option value="' . $language_name . '">' . $language_name .'</option>';
			}
		}
		
		// End select
		echo '</select>';
	}
	else
	{
		foreach($languages as $language)
		{
			$language_ext = pathinfo($language, PATHINFO_EXTENSION);
			$language_name = pathinfo($language, PATHINFO_FILENAME);
			
			$list[] = $language_name;
		}
		
		return $list;
	}
}


if (!function_exists('mb_sprintf'))
{
  /**
   * Internationalized sprintf
   * @param string $format data to be parsed for internationalization
   * @return mixed
   */
	function mb_sprintf($format) {
		$argv = func_get_args() ;
		array_shift($argv) ;
		return mb_vsprintf($format, $argv) ;
	}
}

if (!function_exists('mb_vsprintf')) 
{
  /**
   * Works with all encodings in format and arguments. Supported: Sign, padding, alignment, width and precision. Not supported: Argument swapping.
   * @param string $format data to be parsed
   * @param array $argv array of data
   * @param string $encoding encoding for international languages
   * @return mixed
   */
	function mb_vsprintf($format, $argv, $encoding=null) {
		if (is_null($encoding))
		$encoding = mb_internal_encoding();

		// Use UTF-8 in the format so we can use the u flag in preg_split
		$format = mb_convert_encoding($format, 'UTF-8', $encoding);

		$newformat = ""; // build a new format in UTF-8
		$newargv = array(); // unhandled args in unchanged encoding

		while ($format !== "") {
			
			// Split the format in two parts: $pre and $post by the first %-directive
			// We get also the matched groups
			list ($pre, $sign, $filler, $align, $size, $precision, $type, $post) =
			preg_split("!\%(\+?)('.|[0 ]|)(-?)([1-9][0-9]*|)(\.[1-9][0-9]*|)([%a-zA-Z])!u",
			$format, 2, PREG_SPLIT_DELIM_CAPTURE) ;

			$newformat .= mb_convert_encoding($pre, $encoding, 'UTF-8');
			
			if ($type == '') {
				// didn't match. do nothing. this is the last iteration.
			}
			elseif ($type == '%') {
				// an escaped %
				$newformat .= '%%';
			}
			elseif ($type == 's') {
				$arg = array_shift($argv);
				$arg = mb_convert_encoding($arg, 'UTF-8', $encoding);
				$padding_pre = '';
				$padding_post = '';
				
				// truncate $arg
				if ($precision !== '') {
					$precision = intval(substr($precision,1));
					if ($precision > 0 && mb_strlen($arg,$encoding) > $precision)
					$arg = mb_substr($precision,0,$precision,$encoding);
				}
				
				// define padding
				if ($size > 0) {
					$arglen = mb_strlen($arg, $encoding);
					if ($arglen < $size) {
						if($filler==='')
						$filler = ' ';
						if ($align == '-')
						$padding_post = str_repeat($filler, $size - $arglen);
						else
						$padding_pre = str_repeat($filler, $size - $arglen);
					}
				}
				
				// escape % and pass it forward
				$newformat .= $padding_pre . str_replace('%', '%%', $arg) . $padding_post;
			}
			else {
				// another type, pass forward
				$newformat .= "%$sign$filler$align$size$precision$type";
				$newargv[] = array_shift($argv);
			}
			$format = strval($post);
		}
		// Convert new format back from UTF-8 to the original encoding
		$newformat = mb_convert_encoding($newformat, $encoding, 'UTF-8');
		return vsprintf($newformat, $newargv);
	}
}

?>