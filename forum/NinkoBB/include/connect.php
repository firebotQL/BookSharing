<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * connect.php
 * 
 * Attempts to connect to the database using variables defined in: database.php
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @package ninko
 */
	
switch ($database['type'])
{
	case 'mysql':
		require DATABASE . 'mysql.class' . EXT;
	break;

	case 'mysqli':
		require DATABASE . 'mysqli.class' . EXT;
	break;

	case 'sqlite':
		require DATABASE . 'sqlite.class' . EXT;
	break;

	default:
		die('"' . $database['type'] . '" is not a valid database type. Please check settings in database.php.');
	break;
}

/**
 * Initiate database
 * @global resource $database
 */
$database = new Database($database);
?>