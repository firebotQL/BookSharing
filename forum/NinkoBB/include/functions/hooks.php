<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * hooks.php
 * 
 * Includes functions for hooking, plugin functions
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @subpackage functions
 */
 
/**
 * Initiate hook array
 * @global array $hooks
 */
$hooks = array();
$plugins_loaded = array();

/**
 * Turns a plugin on and creates the row to show loading
 * @global resource
 * @param string $name name of plugin
 * @return boolean|string
 */
function load_plugin($name)
{
	global $database;
	
	if($name == "")
	{
		return 'error_plugin_no_name';
	}
	else if(!alpha($name, 'alpha-underscore'))
	{
		return 'error_plugin_name';
	}
	else if(!is_loaded($name))
	{
		// Insert plugin
		$database->query("INSERT INTO `plugins` SET `name` = '{$name}'");
		
		// That plugin has been loaded.
		plugin_loaded($name);
		
		// Include
		include(BASEPATH . '../plugins/' . $name . '.php');
		
		// Install plugin
		if ( function_exists('install_' . $name) )
		{
			// set it up
			$function = 'install_' . $name;
			
			// initiate it
			$function();
		}
		
		// Return true
		return true;
	}

	return 'error_already_loaded';
}

/**
 * Is that plugin already loaded?
 * @global resource
 * @param string $name name of plugin
 * @return boolean
 */
function already_loaded($name)
{
	global $database;
	
	// Insert plugin
	$result = $database->query("SELECT * FROM `plugins` WHERE `name` = '{$name}'");
	
	// Is it even there?
	if($result)
	{
		// Return true
		if($database->num($result) > 0)
		{
			return true;
		}
	}
	
	return false;
}

/**
 * Turns a plugin off and deletes the row it was loaded on
 * @global resource
 * @param string $name name of plugin
 * @return boolean|string
 */
function unload_plugin($name)
{
	global $database, $plugins_loaded;
	
	if($name == "")
	{
		return 'error_plugin_no_name';
	}
	else if(!alpha($name, 'alpha-underscore'))
	{
		return 'error_plugin_name';
	}
	else
	{
		// Install plugin
		if ( function_exists('uninstall_' . $name) )
		{
			// set it up
			$function = 'uninstall_' . $name;
			
			// initiate it
			$function();
		}
		
		// Uninstall plugin
		$database->query("DELETE FROM `plugins` WHERE `name` = '{$name}'");
		
		// Uninstall plugin
		unset($plugins_loaded[$name]);
		
		// Return true
		return true;
	}
}

/**
 * Grabs data for all plugins in plugin directory.
 * @return array
 */
function plugins()
{
	$files = read_files(BASEPATH . '../plugins/');
	
	foreach ($files as $plugin)
	{
		$plugins_data[] = get_plugin_data($plugin);
	}
	
	return $plugins_data;
}

/**
 * Tells the rest of the site that a plugin is loaded.
 * @param string $name name of plugin that just got loaded.
 * @return boolean
 */
function plugin_loaded($name)
{
	global $plugins_loaded;
	
	$plugins_loaded[$name] = true;
}

/**
 * Is the plugin requested loaded?
 * @param string $name name of plugin that you want to verify the loading of
 * @return boolean
 */
function is_loaded($name)
{
	global $plugins_loaded;
	
	if(is_array($plugins_loaded))
	{
		if($plugins_loaded[$name])
		{
			return true;
		}
	}
	
	return false;
}

/**
 * Gets the plugin data from filename
 * @param string $path really just the filename.
 * @return array
 */
function get_plugin_data($path)
{
	$path_ext = pathinfo($path, PATHINFO_EXTENSION);
	$path_name = pathinfo($path, PATHINFO_FILENAME);
		
	if($path_ext == "php")
	{
		$plugin_data = implode( '', file( BASEPATH . '../plugins/' . $path));
			
		// fetch data
		preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $plugin_name );
		preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
		preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
		preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );
		preg_match( '|Version:(.*)$|mi', $plugin_data, $version );
		
		$plugin_name[1] = trim($plugin_name[1]);
		
		// Check name
		if($plugin_name[1] == '')
		{
			return array('name' => $path_name . $path_ext, 'description' => trim($description[1]), 'author' => trim($author_name[1]), 'url' => trim($author_uri[1]), 'version' => trim($version[1]), 'error' => lang('error_plugin_no_name'));
		}
			
		if(!alpha($plugin_name[1], 'alpha-spacers'))
		{
			return array('name' => trim($plugin_name[1]), 'description' => trim($description[1]), 'author' => trim($author_name[1]), 'url' => trim($author_uri[1]), 'version' => trim($version[1]), 'error' => lang('error_plugin_name'));
		}
		
		return array('name' => trim($plugin_name[1]), 'description' => trim($description[1]), 'author' => trim($author_name[1]), 'url' => trim($author_uri[1]), 'version' => trim($version[1]), 'plugin' => $path_name, 'file' => $path_name . "." . $path_ext);
	}
}
 
/**
 * Create hook instance in specific area
 *
 * Allow outside functionality without editing main files
 * @global array
 * @param string $hook_name area of hook
 * @param string $added_function function to be called upon hook load
 * @param array $args arguments of function
 * @return boolean
 */
function add_hook($hook_name, $added_function, $args = null)
{
	global $hooks;
	
	$hooks[] = array(
		'hook' => $hook_name,
		'function' => $added_function,
		'args' => $args
	);
	
	return true;
}

	
/**
 * Initiate hooks set
 * @global array $hooks
 * @global resource
 * @param string $area hooks in this area will be called
 * @return mixed
 */
function load_hook($area)
{
	global $hooks, $database;
	
	foreach ($hooks as $hook)
	{
		if ($hook['hook'] == $area)
		{
			call_user_func_array($hook['function'], $hook['args']);
			// http://us3.php.net/call_user_func_array
		}
	}
}

?>