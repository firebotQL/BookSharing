<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * theme.php
 * 
 * Includes functions directed to themes
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage functions
 */
 
/**
 * Setup the forum theme
 * @global array
 */
function load_theme()
{
	global $config;
	
	if($config['theme'] != "")
	{
		if(in_array($config['theme'], themes()))
		{
			$config['template_path'] = BASEPATH . "../templates/" . $config['theme'] . "/";
			$config['template_url'] = $config['url_path'] . "/templates/" . $config['theme'] . "/";
		}
		else
		{
			$config['template_path'] = BASEPATH . "../templates/default/";
			$config['template_url'] = $config['url_path'] . "/templates/default/";
		}
	}
	else
	{
		$config['template_path'] = BASEPATH . "../templates/default/";
		$config['template_url'] = $config['url_path'] . "/templates/default/";
	}
}

/**
 * Creates a list of themes
 * @param boolean $html show them html or array
 * @return mixed
 */
function themes($html = false)
{
	// Fetch the dirs
	$handle = @opendir(BASEPATH . '../templates/') or die("Unable to open 'templates/'");
	$themes = array();
	
	while ($file = readdir($handle))
	{
		if (filetype(BASEPATH . '../templates/' . $file) === 'dir' && $file != "." && $file != "..")
		{ 
			$themes[] = $file;
		}
	}
	
	closedir($handle);
	
	// check if we want html or the array
	if($html)
	{
		// Start select
		echo '<select name="theme" class="border" style="width: 40%">';
		
		foreach($themes as $theme)
		{
			echo '<option value="' . $theme . '">' . $theme .'</option>';
		}
		
		// End select
		echo '</select>';
	}
	else
	{
		return $themes;
	}
}
?>