<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * MySQL.php
 * 
 * Database abstraction layer for mysql without dependancies on PDO
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage database
 */


/**
 * Controls the database functions for all layers
 */
class database
{
	/**
	 * The table prefix, used incase we use a joint database
	 * @access private
	 * @var string
	 */
	var $prefix;
	
	/**
	 * The database link
	 * @access private
	 * @var resource
	 */
	var $link;
	
	/**
	 * Saved queries
	 * @access private
	 * @var array
	 */
	var $saved_queries = array();
	
	/**
	 * Number of queries
	 * @access private
	 * @var integer
	 */
	var $num_queries = 0;
	
	/**
	 * Constructor connects to the database and sets up the link {@link $link}
	 */
	function database($params)
	{
		$this->prefix = $params['prefix'];
		
		if($params['persistant'])
		{
			$this->link = @mysql_pconnect($params['host'], $params['user'], $params['pass']);
		}
		else
		{
			$this->link = @mysql_connect($params['host'], $params['user'], $params['pass']);
		}
		
		if(!$this->link)
		{
			$this->error('Could not connect to database.', __FILE__, __LINE__);
		}
		else
		{
			if(@mysql_select_db($params['db'], $this->link))
			{
				return $this->link;
			}
			else
			{
				$this->error('Could not open database ' . $params['db'], __FILE__, __LINE__);
			}
		}
	}
	
	/**
	 * Execute query and return the result or insertion id.
	 * @param string $query The query we are executing.
	 * @param boolean $id Return the insertion id if true.
	 * @return boolean|integer
	 */
	function query($query, $id = false)
	{
		// Execute query
		$this->result = @mysql_query($query, $this->link);
		
		// Did we get a result?
		if ($this->result)
		{
			// Update saved queries and amount executed
			$this->saved_queries[] = $query;
			$this->num_queries++;
			
			// Return results or the insertion id
			if($id)
			{
				return $this->insert_id();
			}
			else
			{
				return $this->result;
			}
		}
		else
		{
			// Set the query to saved but don't update the number executed
			$this->saved_queries[] = $query;
	
			// Return false
			return false;
		}
	}
	
	/**
	 * Fetch results in different manners
	 * @return boolean|array
	 */
	function fetch($result, $type = 'array')
	{
		switch($type)
		{
			case "array":
				return ($result) ? @mysql_fetch_array($result) : false;
			break;
			
			case "row":
				return ($result) ? @mysql_fetch_row($result) : false;
			break;
			
			case "assoc":
				return ($result) ? @mysql_fetch_assoc($result) : false;
			break;
		}
	}
	
	/**
	 * Return the numbers for a specific type
	 * @return boolean|integer
	 */
	function num($result, $type = 'rows')
	{
		switch($type)
		{
			case "rows":
				return ($result) ? @mysql_num_rows($result) : false;
			break;
			
			case "fields":
				return ($result) ? @mysql_num_fields($result) : false;
			break;
		}
	}
	
	/**
	 * The amount of rows that have been affected
	 * @return boolean|integer
	 */
	function affected_rows()
	{
		return ($this->link) ? @mysql_affected_rows($this->link) : false;
	}
	
	/**
	 * Find the last insert id and return it
	 * @return boolean|integer
	 */
	function insert_id()
	{
		return ($this->link) ? @mysql_insert_id($this->link) : false;
	}
	
	/**
	 * Escape the data
	 * @return string
	 */
	function escape($data)
	{
		if (is_array($data))
		{
			return '';
		}
		else if (function_exists('mysql_real_escape_string'))
		{
			return mysql_real_escape_string($data, $this->link);
		}
		else
		{
			return mysql_escape_string($data);
		}
	}
	
	/**
	 * Exit with an error
	 */
	function error($error, $file, $line)
	{
		die("{$error} in file: {$file} on line {$line}");
	}
}
?>