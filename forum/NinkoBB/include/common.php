<?php session_start();
/**
 * common.php
 * 
 * Controls inclusions, configuration, and common data. The base file.
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @copyright (c) 2010 ANIGAIKU
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @package ninko
 */
 
/**
 * Setup a timezone if it isn't supported
 *
 * You can find more timezones here: http://nl3.php.net/manual/en/timezones.php
 */
//date_default_timezone_set('UTC');
 
/**
 * Are we inside of ninko?
 */
define("IN_NK", true);

// System Folder
if (function_exists ( 'realpath' ) and @realpath ( dirname ( __FILE__ ) ) !== FALSE)
{ 
	$system_folder = str_replace ( '', '/', realpath ( dirname ( __FILE__ ) ) ); 
}

// Inclusion variables
define ( 'EXT', 		'.' . pathinfo ( __FILE__, PATHINFO_EXTENSION ) );
define ( 'BASEPATH', 	$system_folder . '/' );
define ( 'FUNCTIONS', 	$system_folder . '/functions/' );
define ( 'DATABASE',	$system_folder . '/database/' );

/**
 * Include utf8 Dependencies
 */
require BASEPATH . "utf8/utf8" . EXT;
require BASEPATH . "utf8/ucwords" . EXT;
require BASEPATH . "utf8/trim" . EXT;

// Turn off magic quotes
if (get_magic_quotes_runtime())
	set_magic_quotes_runtime(0);

// Strip slashes from user defined variables
if (get_magic_quotes_gpc())
{
	function stripslashes_array($array)
	{
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
}

/**
 * Include configuration
 */
include(BASEPATH . "config" . EXT);


if(file_exists(BASEPATH . "database" . EXT))
{
	/**
	 * Include database configuration
	 */
	include(BASEPATH . "database" . EXT);
}
else if(!$installing)
{
	// send to setup.
	header('location: setup/');
}

/**
 * Include connection to database: MySQL
 */
if(!$connect)
{
	include(BASEPATH . "connect" . EXT);

	// Parse Config
	$result = $database->query("SELECT * FROM `config`");

	if(!$result && !$installing)
	{
		// send to setup.
		header('location: setup/');
	}

	// Loop through the results and set the values.
	while($row = $database->fetch($result))
	{
		if($row['value'] == "" || !$row['value'])
		{
			$config[$row['key']] = false;
		}
		else
		{
			$config[$row['key']] = $row['value'];
		}
	}

	// Check version
	if($config['version'] != '1.3')
	{
		// send to upgrade
		header('location: setup/upgrade.php?v=' . $config['version']);
	}
}

if($installing && isset($_GET['lang']))
{
	$config['language'] = $_GET['lang'];
}

/**
 * Include theme functions
 */
include(FUNCTIONS . "theme" . EXT);

// Load the theme
load_theme();

/**
 * Include language functions
 */
include(FUNCTIONS . "language" . EXT);

// Include language file
if(isset($config['language']) && $config['language'] != "")
{
	$lang = language($config['language']);
	
	if(!is_array($lang))
	{
		// Default language
		$lang = language('en');
	}
}
else
{
	$lang = language('en');
}

// Functions to include
$functions = array(
	'common',
	'validation',
	'hooks',
	'user',
	'parser',
	'parser_bbcode',
	'forum',
	'admin'
);

// Include the functions
foreach($functions as $file)
{
	include(FUNCTIONS . $file . EXT);
}

// Bad Characters
remove_bad_utf8();

// Initiate BBCode
$parser = new Parser_BBCode();

if(!$installing)
{
	// Fetch plugins
	$plugins = plugins();
			
	// Fetch loaded plugins
	$result = $database->query( "SELECT * FROM `plugins`" );

	// Load plugins
	if($database->num($result) >= 1)
	{
		while($loading = $database->fetch($result))
		{
			foreach($plugins as $plugin)
			{
				// don't even think of loading error'd plugins
				if($plugin['error']) { continue; }
				if(!isset($plugin['name'])) { continue; }
				if(is_loaded($loading['name'])){ continue; }
					
				if($loading['name'] == $plugin['plugin'])
				{
					if(!$load_plugins)
					{
						// That plugin has been loaded.
						plugin_loaded($plugin['plugin']);
						
						// Load the plugin
						include(BASEPATH . '../plugins/' . $plugin['file']);
					}
					
					// Still load the language files though.
					if(file_exists(BASEPATH . 'languages/plugins/' . $plugin['file']))
					{
						include(BASEPATH . 'languages/plugins/' . $plugin['file']);
					}
				}
			}
		}
	}
}


if(!$user_login)
{
	/**
	 * Include Sessions
	 */
	include(BASEPATH . "sessions" . EXT);

	// Common hook
	load_hook('common');

	// Just incase
	unset($user_data);

	// Logged in?
	if(isset($_SESSION['logged_in']))
	{
		/**
		 * Set user data
		 * @global array $user_data
		 */
		$user_data = user_data($_SESSION['user_id']);
		
		// Last seen update
		update_user($user_data['id'], false, 'last_seen', time());
	}
}
?>