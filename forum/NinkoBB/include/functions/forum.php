<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * forum.php
 * 
 * Includes functions commonly used througout the entire script
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage functions
 */
 
function category($id = false)
{
	global $database;
	
	if($id)
	{
		if(!alpha($id, 'numeric'))
		{
			return false;
		}
		
		$sql = "SELECT * FROM `categories` WHERE `id`='{$id}'";
		
		// Return Data
		$return = $database->query( $sql );
			
		// Exists?
		if($database->num( $return ) > 0)
		{
			$category = $database->fetch( $return );
			
			// fix name just incase
			$category['name'] = htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8', FALSE);
			
			return $category;
		}
		else
		{
			// Guess not~
			return false;
		}
	}
	
	$sql = "SELECT * FROM `categories` ORDER BY `order`";
	
	// Return Data
	$return = $database->query( $sql );
		
	// Exists?
	if($database->num( $return ) > 0)
	{
		// Finally return Results
		while($category = $database->fetch( $return ))
		{
			$categories[] = $category;
		}
			
		return $categories;
	}
	else
	{
		// Guess not~
		return false;
	}
}

function show_categories()
{
	$categories = category();
	
	$show = 0;
	
	foreach($categories as $category)
	{
		if(!$category['expanded'])
		{ 
			$show++; 
		} 
	}
	
	return $show;
}

/**
 * Fetches forum data based on input
 * @global resource
 * @param boolean|integer $forum data to be scanned for links and replaced
 * @param boolean $sticky true: searches stickies, false: excludes stickies
 * @param boolean|integer $topic data to be scanned for links and replaced
 * @param string $order_by what do we order results by?
 * @param string $order how do we order them?
 * @param integer $current data to be scanned for links and replaced
 * @param integer $limit data to be scanned for links and replaced
 * @return array|boolean
 */
function fetch($category = false, $sticky = false, $topic = false, $order_by = 'updated', $order = 'DESC', $current = 0, $limit = 15)
{
	global $database;
	
	// Fetch topics
	if(is_numeric($category))
	{
		if($category)
		{
			$forum = "`category` = '{$category}' AND ";
		}
		
		if($sticky)
		{
			$query = "SELECT * FROM `forum` WHERE {$forum}(`reply` = 0 AND `sticky` = 1) ORDER BY `time` {$order} LIMIT {$current},{$limit}";
		}
		else
		{
			// Query
			$query = "SELECT * FROM `forum` WHERE {$forum}(`reply` = 0 AND `sticky` = 0) ORDER BY `{$order_by}` {$order} LIMIT {$current},{$limit}";
		}

		// Return Data
		$return = $database->query( $query );
		
		// Exists?
		if($database->num( $return ) > 0)
		{
			// Finally return Results
			while($topic = $database->fetch( $return ))
			{
				$topics[] = $topic;
			}
			
			return $topics;
		}
		else
		{
			// Guess not~
			return false;
		}
	}
	else if($topic)
	{
		// Query
		$query = "SELECT  * FROM `forum` WHERE (`id` = {$topic}) OR (`reply` = {$topic}) ORDER BY `{$order_by}` {$order} LIMIT {$current},{$limit}";

		// Return Data
		$return = $database->query( $query );
		
		// Exists?
		if($database->num( $return ) > 0)
		{
			// Finally return Results
			while($topic = $database->fetch( $return ))
			{
				$topics[] = $topic;
			}
			
			return $topics;
		}
		else
		{
			// Guess not~
			return false;
		}
	}
}

/**
 * Allows creation of topics, stuck or closed, and posts
 * @global array
 * @global array
 * @global resource
 * @param string $topic post subject
 * @param string $content post content
 * @param integer $reply id of topic we are replying to
 * @param boolean $sticky are we sticking it to the top?
 * @param boolean $closed are we closing it?
 * @return string|int
 */
function post($category, $topic, $content, $reply = false, $sticky = false, $closed = false)
{
	global $config, $user_data, $database;
	
	// The time. milliseconds / seconds may change.
	$time = time();
	
	// Its new right now.
	$new = true;
	
	// Pre-Parse
	$topic = strip_repeat($topic);
	
	if($_SESSION['logged_in'])
	{
		// If we aren't replying don't set up a category.
		if(!$reply)
		{
			// Check validity of category as numeric
			if(!alpha( $category, 'numeric'))
			{
				return lang('error_invalid_category');
			}
			
			// Check to see if category exists
			$category = category($category);
			
			if(!$category)
			{
				return lang('error_invalid_category');
			}
			
			// Check category settings
			if($category['aop'] && $reply)
			{
				if(!$user_data['admin'] || !$user_data['moderator'])
				{
					return lang('error_invalid_category');
				}
			}
			
			if($category['aot'] && !$reply)
			{
				if($user_data['id'] != $category['aot'])
				{
					return lang('error_invalid_category');
				}
			}
		}
		
		// String length
		if(is_string(length($topic, $config['subject_minimum_length'], $config['subject_max_length'])))
		{
			return lang_parse('error_subject_length', array($config['subject_max_length'], $config['subject_minimum_length']));
		}
		
		// Do we have content to go on?
		if($content != "")
		{
			if(!is_string(length($content, $config['message_minimum_length'], $config['message_max_length'])))
			{
				// Are we replying or is it new?
				if($reply)
				{
					if(alpha($reply, 'numeric'))
					{
						if(topic($reply, 'id'))
						{
							$new = false;
							
							// topic data
							$topic_data = topic($reply, '*');
							
							// is it closed?
							if($topic_data['closed'] && (!$user_data['admin'] || !$user_data['moderator']))
							{
								return lang('error_topic_closed');
							}
							
							// Setup reply category
							$category = category($topic_data['category']);
						}
						else
						{
							return lang('error_topic_missing');
						}
					}
					else
					{
						return lang_parse('error_invalid_given', array(lang('topic') . " " . lang('id')));
					}
				}
				
				// Sticky
				$sticky = ($sticky) ? '1' : '0';
				
				// Closed
				$closed = ($closed) ? '1' : '0';
				
				// Time Lapse
				if(!$user_data['admin'])
				{
					// Get the time we need to check against
					if(!$new)
					{
						$time_between = time() - $config['post_reply_time_limit'];
					}
					else
					{
						$time_between = time() - $config['post_topic_time_limit'];
					}
					
					
					// Last post by this user?
					$query = "SELECT `time` FROM `forum` WHERE `starter_id` = '{$user_data['id']}' AND `time` > {$time_between}";
					
					// Fetch users last post
					$result = $database->query( $query );
					
					// is there a result?
					if($database->num($result) > 0)
					{
						return lang('error_flood_detection');
					}
				}
				
				// So we don't have leftovers.
				unset($query, $result);
				
				
				// Guess we can go ahead and add you~
				$query = "INSERT INTO `forum` (`category`,`subject`,`message`,`reply`,`starter_id`,`host`,`time`,`updated`,`sticky`,`closed`) VALUES (%d,'%s','%s',%d,%d,'%s','%s','%s','%s','%s')";
				$query = sprintf(
					$query,
					$category['id'],
					$database->escape($topic),
					$database->escape($content),
					(($new) ? 0 : $reply),
					$user_data['id'],
					$database->escape(gethostname()),
					$time,
					$time,
					$sticky,
					$closed
				);
				
				// Insert into mysql and retrieve id.
				$result = $database->query($query);
				
				if($result)
				{
					// the id from the previous query
					$id = $database->insert_id();
					
					// users new post count
					$new_post_count = $user_data['posts']+1;
					
					// update user post count
					update_user($user_data['id'], false, 'posts', $new_post_count);
					
					// Start sending back information
					if($new)
					{
						return $id;
					}
					else
					{
						// How many replies?
						$replies = intval(forum_count(false, $reply, false));
						
						// Lets update it
						$replies = $replies+1;
						
						// Woooo~ Last id for redirecting~
						if($config['show_first_post'])
						{
							$page_numbers = ((($replies-1) / $config['messages_per_topic']) - 1);
						}
						else
						{
							$page_numbers = (($replies / $config['messages_per_topic']) - 1);
						}
						
						$n = ceil($page_numbers);
						
						// A little fixing
						if ($n == -1)
						{
							$n = 0;
						}
						else
						{
							$n = abs($n);
						}
						
						// Update
						$query = "UPDATE `forum` SET `updated`='{$time}', `replies`='{$replies}' WHERE id = '{$reply}'";
						
						// Update
						$result = $database->query($query);
						
						// Return last page number and id for redirect.
						return array('page' => $n, 'id' => $id);
					}
				}
				else
				{
					return lang('error_unknown');
				}
				
			}
			else
			{
				return lang_parse('error_message_length', array($config['message_max_length'], $config['message_minimum_length']));
			}
		}
		else
		{
			return lang_parse('error_no_given', array(lang('message')));
		}
	}
	else
	{
		return lang('error_not_logged');
	}
}
	
/**
 * Allows updating of topics, stuck or closed, and posts
 * @global array
 * @global array
 * @global resource
 * @param integer $id post we are editing
 * @param string $topic post subject
 * @param string $content post content
 * @param integer $reply id of topic we are replying to
 * @param boolean $sticky are we sticking it to the top?
 * @param boolean $closed are we closing it?
 * @return string|int
 */
function update($id, $category, $topic, $content, $sticky = false, $closed = false)
{
	global $config, $user_data, $database;
	
	// The time. milliseconds / seconds may change.
	$time = time();
	
	// Is the id numeric?
	if(!alpha($id, 'numeric'))
	{
		return lang_parse('error_given_not_numeric', array(lang('post') . " " . lang('id')));
	}
	
	// Grab the data for the update.
	$post_data = topic($id);
	
	// Check to see if the post or topic was found.
	if(!$post_data)
	{
		return lang('error_post_missing');
	}
	
	// Pre-Parse
	$topic = strip_repeat($topic);
	
	// Can't update a replies category!
	if($post_data['reply'])
	{
		$category = $post_data['category'];
	}
	
	// Check validity of category as numeric
	if(!alpha( $category, 'numeric'))
	{
		return lang('error_invalid_category');
	}
		
	// Check to see if category exists
	$category = category($category);
		
	if(!$category)
	{
		return lang('error_invalid_category');
	}
	
	// Check category settings against user
	if(!$user_data['admin'])
	{
		if($category['aop'] && $post_data['reply'])
		{
			if(!$user_data['admin'] || !$user_data['moderator'])
			{
				return lang('error_invalid_category');
			}
		}
		
		if($category['aot'] && !$post_data['reply'])
		{
			if($user_data['id'] != $category['aot'])
			{
				return lang('error_invalid_category');
			}
		}
	}
	
	// Is the user currently logged in? If not we can't update return error.
	if($_SESSION['logged_in'])
	{
		// Editing a topic not post
		if($post_data['reply'] == 0)
		{
			// Is there a topic?
			if($topic == "")
			{
				return lang_parse('error_no_given', array(lang('username')));
			}
		}
		else
		{
			// If there was no topic put re: on it.
			if($topic == "")
			{
				$topic = "re:";
			}
		}
		
		// Did they give us any content to work with?
		if($content != "")
		{
			if(!is_string(length($content, $config['message_minimum_length'], $config['message_max_length'])))
			{
				// Check to see if the user is an admin and able to sticky / close the topic
				if($_SESSION['admin'] || $_SESSION['moderator'])
				{
					// Sticky
					$sticky = ($sticky) ? '1' : '0';
					
					// Closed
					$closed = ($closed) ? '1' : '0';
					
					// Admin functions
					update_field($id, 'sticky', $sticky);
					update_field($id, 'closed', $closed);
				}
				
				// Parsing
				$topic = $database->escape( $topic );
				$content = $database->escape( $content );
				
				// Update the post already inside of the database with the new data
				$result = $database->query( "UPDATE `forum` SET `category`='{$category['id']}', `subject`='{$topic}', `message`='{$content}', `updated`='{$time}', `replies`='{$replies}' WHERE id = '{$id}'" ) or die(mysql_error());
					
				// Did it work?
				if($result)
				{
					// Update replies with category
					if($category != $post_data['category'] && !$post_data['reply'])
					{
						$database->query( "UPDATE `forum` SET `category`='{$category['id']}' WHERE `reply` = {$id}");
					}
					
					if($post_data['reply'] != 0)
					{
						// How many replies?
						$replies = intval(forum_count(false, $post_data['reply'], false));
							
						// Woooo~ Last id for redirecting~
						if($config['show_first_post'])
						{
							$page_numbers = ((($replies-1) / $config['messages_per_topic']) - 1);
						}
						else
						{
							$page_numbers = (($replies / $config['messages_per_topic']) - 1);
						}
							
						$n = ceil($page_numbers);
							
						// A little fixing
						if ($n == -1)
						{
							$n = 0;
						}
						else
						{
							$n = abs($n);
						}
						
						// Return last page number and id for redirect.
						return array('page' => $n, 'id' => $post_data['id']);
					}
					
					return true;
				}
				else
				{
					return false;
				}
				
			}
			else
			{
				return lang_parse('error_message_length', array($config['message_max_length'], $config['message_minimum_length']));
			}
		}
		else
		{
			return lang_parse('error_no_given', array(lang('message')));
		}
	}
	else
	{
		return lang('error_not_logged');
	}
}

/**
 * Fetches post data by id and custom select
 * @global resource
 * @param integer $topic id used to retrieve topic / post data
 * @param string $data fields to be retrieved from database
 * @return array|boolean
 */
function topic($topic, $data = '*')
{
	global $database;
	
	// Query
	$query = "SELECT {$data} FROM `forum` WHERE `id` = '{$topic}' LIMIT 1";
	
	// Return Data
	$return = $database->query( $query );

	// Return the data
	if($database->num( $return ) > 0)
	{
		return $database->fetch( $return );
	}
	else
	{
		return false;
	}
}

/**
 * Grabs all categories and puts them into an array
 * @global resource
 * @param integer $topic id used to retrieve topic / post data
 * @param string $data fields to be retrieved from database
 * @return array|boolean
 */
function categories($order = 'ASC')
{
	global $database;
	
	// Array
	$category = array();
	
	// Query
	$query = "SELECT * FROM `categories` WHERE `order` = '{$order}' LIMIT 1";
	
	// Return Data
	$return = $database->query( $query );

	// Return the data
	if($database->num( $return ) > 0)
	{
		while($category = $database->fetch( $return ))
		{
			$categories[$category['id']] = $category['name'];
		}
	}
	else
	{
		return false;
	}
}

/**
 * Fetches the last post data in a topic
 * @global resource
 * @param integer $topic id used to retrieve reply data
 * @param integer $id user id to retrieve last post from that user
 * @param string $data fields to be retrieved from database
 * @return array|boolean
 */
function last_post($topic, $id = false, $data = '*')
{
	global $database;
	
	if($id)
	{
		// Query
		$query = "SELECT {$data} FROM `forum` WHERE `starter_id` = '{$id}' ORDER BY `time` DESC LIMIT 1";
		
		// Return Data
		$return = $database->query( $query );
		
		// Return the data
		if($database->num( $return ) > 0)
		{
			return $database->fetch( $return );
		}
		else
		{
			return false;
		}
	}
	else
	{
		// Query
		$query = "SELECT {$data} FROM `forum` WHERE `reply` = '{$topic}' ORDER BY `time` DESC LIMIT 1";
		
		// Return Data
		$return = $database->query( $query );
		
		// Return the data
		if($database->num( $return ) > 0)
		{
			return $database->fetch( $return );
		}
		else
		{
			return topic($topic);
		}
	}
}

/**
 * Return replies in topic. Currently a good way to see if topic exists.
 * @global resource
 * @param integer $topic id used to retrieve reply data
 * @return array|boolean
 */
function get_replies($topic, $category = false)
{
	global $database;
	
	if($category)
	{
	}
	else
	{
		// Query
		$query = "SELECT replies FROM `forum` WHERE `id` = {$topic}";
	}
	
	// Return Data
	$return = $database->query( $query );
	
	if($return)
	{
		$topic_data = $database->fetch($return);
		
		// Return that reply data!
		return $topic_data['replies'];
	}
	else
	{
		return false;
	}
}

/**
 * Count replies for the forum or user
 * @global resource
 * @param integer $id id used to retrieve data for topics users etc
 * @param boolean $type refers to what are we retrieving
 * @param boolean $exclude_stickies include stickies in our count?
 * @param boolean $posts count posts?
 * @param boolean $today show count from past day only?
 * @return int
 */
function forum_count($category, $id, $type, $today = false, $topics = false)
{
	global $database;
	
	if($type == "all")
	{
		if($category)
		{
			if(alpha($category, 'numeric'))
			{
				$category = " WHERE `category` = '{$category}'";
			}
		}
		
		if($topics)
		{
			$category .= " AND `reply` = '0'";
		}
		
		// Query
		$query = "SELECT id FROM `forum`{$category}";
		
		// Return Data
		$return = $database->query( $query );
		
		// Return the count
		return $database->num( $return );
	}
	
	if($type == "posts")
	{
		if($category)
		{
			if(alpha($category, 'numeric'))
			{
				$category = "`category` = '{$category}' AND ";
			}
		}
		
		if($today)
		{
			$query = "SELECT `id` FROM `forum` WHERE {$category}(`reply` != '0' AND `time` >= " . strtotime('-1 day') . ")";
		}
		else
		{
			// Query
			$query = "SELECT id FROM `forum` WHERE {$category}(`reply` != '0')";
		}
		
		// Return Data
		$return = $database->query( $query );
		
		// Return the count
		return $database->num( $return );
	}
	
	if($type == "user")
	{
		if(is_numeric($id))
		{
			// Query
			$query = "SELECT id FROM `forum` WHERE `starter_id` = '{$id}'";
				
			// Return Data
			$return = $database->query( $query );
				
			// Return the count
			return $database->num( $return );
		}
		else
		{
			return intval(0);
		}
	}
	
	if($id === "*")
	{
		if($category)
		{
			if(alpha($category, 'numeric'))
			{
				$category = "`category` = '{$category}' AND ";
			}
		}
		
		if($type == "exclude_stickies")
		{
			if($today)
			{
				$query = "SELECT `id` FROM `forum` WHERE {$category}(`reply` = '0' AND `sticky` = '0') AND (`time` >= " . strtotime('-1 day') . ")";
			}
			else
			{
				// Query
				$query = "SELECT id FROM `forum` WHERE {$category}(`reply` = '0' AND `sticky` = '0')";
			}
		}
		else
		{
			if($today)
			{
				$query = "SELECT `id` FROM `forum` WHERE {$category}(`reply` != '0' AND `time` >= " . strtotime('-1 day'). ")";
			}
			else
			{
				// Query
				$query = "SELECT id FROM `forum` WHERE {$category}(`reply` = '0')";
			}
		}
		
		// Return Data
		$return = $database->query( $query );
		
		// Return the count
		return $database->num( $return );
	}
	else
	{
		// Query
		$query = "SELECT id FROM `forum` WHERE `reply` = {$id}";
			
		// Return Data
		$return = $database->query( $query );
			
		// Return the count
		return $database->num( $return );
	}
}

/**
 * Update specific field rather than whole post
 * @param integer $id post we are editing
 * @param string $field post field
 * @param string $value new data to enter into post
 * @return string|int
 */
	function update_field($id, $field, $value)
	{
		global $database;
		
		// Error codes
		//	905		- Invalid id
		
		if(!is_numeric($id))
		{
			return 905;
		}
		else
		{
			// Clean value, fields are clean as WE set them
			$value = $database->escape($value);
				
			// Update the forum with the new value
			$result = $database->query( "UPDATE `forum` SET `{$field}` = '{$value}' WHERE `id` = '{$id}' LIMIT 1" );
				
			// Did it work?
			if($result)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
/**
 * Parse post content into readable data, or return default text
 * @global array
 * @param string $text data to be parsed
 * @param boolean $bbcode show bbcode or not?
 * @return mixed
 */
function parse($text, $bbcode_show = true)
{
	global $config, $parser;
	
	// Return base text!
	if(!$bbcode_show)
	{
		// Load the hook on a no bbcode parsing message
		load_hook('no_bbcode_message');
		
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', FALSE);
	}
	
	// Do they allow bbcode or does this post allow bbcode?
	if($config['bbcode'] && $bbcode_show)
	{
		// Convert special characters before bbcode :3
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8', FALSE);
		
		// Load the hook after special characters just incase.
		load_hook('bbcode_before');
		
		// Convert newlines
		$text = preg_replace ("/\015\012|\015|\012/", "\n", $text);
		
		// Strip everything but newlines
		if(!function_exists('bbcode_stripcontents'))
		{
			function bbcode_stripcontents ($text)
			{
				return preg_replace ("/[^\n]/", '', $text);
			}
		}
		
		// Convert codes related specifically to code bbcode
		if(!function_exists('bbcode_code_convert'))
		{
			function bbcode_code_convert ($text)
			{
				return preg_replace ("/(\n|\r\n)/", '%nl', $text);
			}
		}
		
		// Convert codes related specifically to code bbcode
		if(!function_exists('bbcode_code_revert'))
		{
			function bbcode_code_revert ($text)
			{
				return preg_replace ("/\%nl/", chr(13) . chr(10), $text);
			}
		}
		
		// Quoting :D
		if(!function_exists('bbcode_quote'))
		{
			function bbcode_quote($action, $attributes, $content, $params, $node_object)
			{
				if($action == 'validate')
				{
					return true;
				}
				
				if (!isset ($attributes['default']))
				{
					$name = $content;
					
					if($action == 'output')
					{
						$text = $content;
					}
						
					return '<blockquote><div class="userquotebox"><h4>Quote:</h4><div class="text">'.$text.'</div></div></blockquote>';
				} 
				else 
				{
					$name = $attributes['default'];
					
					if($action == 'output')
					{
						$text = $content;
					}
						
					return '<blockquote><div class="userquotebox"><h4>'.$name.' wrote:</h4><div class="text">'.$text.'</div></div></blockquote>';
				}
			}
		}
		
		if(!function_exists('bbcode_color'))
		{
			function bbcode_color($action, $attributes, $content, $params, $node_object)
			{
				if ($action == 'validate')
				{
					if($attributes['default'] == "")
					{
						return false;
					}
					
					if(!preg_match('/([a-zA-Z]*|\#?[0-9a-fA-F]{6})/i', $attributes['default']))
					{
						return false;
					}
					
					return true;
				}

				$color = $attributes['default'];
				$text = $content;
					
				return '<span style="color: '.$color.'">'.$text.'</span>';
			}
		}
		
		// Url parsing
		if(!function_exists('bbcode_url'))
		{
			function bbcode_url($action, $attributes, $content, $params, $node_object)
			{
				if (!isset ($attributes['default']))
				{
					$url = $content;
					$text = $content;
				} 
				else 
				{
					$url = $attributes['default'];
					$text = $content;
				}
				
				if ($action == 'validate')
				{
					if(substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:' || substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') 
					{
						return false;
					}
					
					if(!is_url($url))
					{
						return false;
					}
					
					return true;
				}
				
				return '<a href="'.$url.'" rel="no-follow">'.$text.'</a>';
			}
		}
		
		// Url parsing
		if(!function_exists('bbcode_img'))
		{
			function bbcode_img ($action, $attributes, $content, $params, $node_object) 
			{
				if ($action == 'validate')
				{
					if(substr ($content, 0, 5) == 'data:' || substr ($content, 0, 5) == 'file:' || substr ($content, 0, 11) == 'javascript:' || substr ($content, 0, 4) == 'jar:') 
					{
						return false;
					}
					
					if(!preg_match('/((ht|f)tps?:\/\/)([^\s<\"]*?)\.(jpg|jpeg|png|gif)/i', $content))
					{
						return false;
					}
					
					return true;
				}
				
				return '<img src="'.$content.'" rel="no-follow">';
			}
		}
		
		// Parsers
		$parser->addParser ('list', 'bbcode_stripcontents');
		$parser->addParser ('code', 'bbcode_code_convert');
		
		// Codes
		$parser->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		$parser->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		$parser->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		$parser->addCode ('s', 'simple_replace', null, array ('start_tag' => '<s>', 'end_tag' => '</s>'), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		
		// Text related
		$parser->addCode ('color', 'usecontent?', 'bbcode_color', array ('usecontent_param' => 'default'), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ('link'));
		
		// Links
		$parser->addCode ('url', 'usecontent?', 'bbcode_url', array ('usecontent_param' => 'default'), 'link', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		$parser->addCode ('link', 'usecontent', 'bbcode_url', array (), 'link', array ('listitem', 'block', 'inline', 'quote'), array ());
		
		// List
		$parser->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'), 'list', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		$parser->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'), 'listitem', array ('list'), array ());
		
		// Images
		$parser->addCode ('img', 'usecontent', 'bbcode_img', array (), 'image', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		$parser->addCode ('image', 'usecontent', 'bbcode_img', array (), 'image', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		
		// Quote & Code
		$parser->addCode ('quote', 'callback_replace', 'bbcode_quote', array ('usecontent_param' => 'default'), 'quote', array ('block', 'inline', 'quote'), array ());
		$parser->addCode ('code', 'simple_replace', null, array ('start_tag' => '<div class="codebox"><h4>Code:</h4><div class="scrollbox"><pre>', 'end_tag' => '</pre></div></div>'), 'code', array ('block', 'inline', 'quote'), array ('listitem','link'));

		
		// Occurrences
		$parser->setOccurrenceType ('img', 'image');
		$parser->setOccurrenceType ('image', 'image');
		$parser->setOccurrenceType ('url', 'link');
		$parser->setMaxOccurrences ('image', 4);
		$parser->setMaxOccurrences ('link', 20);
		
		// Flags
		$parser->setCodeFlag('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
		$parser->setCodeFlag('*', 'paragraphs', false);
		$parser->setCodeFlag('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
		$parser->setCodeFlag('list', 'opentag.after.newline', BBCODE_NEWLINE_DROP);
		$parser->setCodeFlag('list', 'closetag.after.newline', BBCODE_NEWLINE_DROP);
		$parser->setCodeFlag('quote', 'opentag.after.newline', BBCODE_NEWLINE_DROP);
		$parser->setCodeFlag('quote', 'closetag.after.newline', BBCODE_NEWLINE_DROP);
		
		// Just before its parsed
		load_hook('bbcode_before_parse');
		
		// Parse the text
		$text = $parser->parse($text);
		
		// Just once
		$text = nl2br($text);
		
		// Clickable
		$text = clickable($text);
		
		// Revert the code changes
		$text = bbcode_code_revert($text);
		
		// After everything is done.
		load_hook('bbcode_after');
	}
	
	// Return a fully parsed post / other
	return $text;
}
		
/**
 * Creates the pagination for the topics
 * @global array
 * @param string $replies the number of replies
 * @param integer $url the root url
 * @return string
 */
function topic_pagination($replies, $url)
{
	global $config;
	
	// Make sure $per_page is a valid value
	$per_page = ($config['messages_per_topic'] <= 0) ? 1 : $config['messages_per_topic'];
	
	if (($replies + 1) > $per_page)
	{
		$total_pages = ceil(($replies + 1) / $per_page);
		$pagination = '';
		
		$times = 1;
		for ($j = 1; $j < $replies + 1; $j += $per_page)
		{
			$pagination .= '<a href="' . $url . '&amp;start=' . $j . '">' . $times . '</a>';
			if ($times == 1 && $total_pages > 5)
			{
				$pagination .= ' ... ';
				
				// Display the last three pages
				$times = $total_pages - 3;
				$j += ($total_pages - 4)  * $per_page;
			}
			else if ($times < $total_pages)
			{
				$pagination .= '<span class="page-sep">,</span>';
			}
			$times++;
		}
	}
	else
	{
		$pagination = '';
	}
	
	return $pagination;
}
?>