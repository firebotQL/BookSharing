<?php
/**
 * index.php
 * 
 * Base of the forum, shows paginated topics.
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 */
 
/**
 * Include common.php
 */
require("include/common.php");

// Start point
@$page = $_GET['page'];

// What page are we on?
if(is_numeric($page)) {
	if (!isset($page) || $page < 0) $page = 0;
}
else
{
	$page = 0;
}
	
// Start point
$start = $page * $config['messages_per_page'];

// Category
if(isset($_GET['category']))
{
	if(alpha($_GET['category'], 'numeric') && category($_GET['category']))
	{
		$current_category = $_GET['category'];
		$category_data = category($_GET['category']);
	}
	else
	{
		$current_category = 0;
	}
}
else
{
	$current_category = 0;
}

// Sticky topics
$sticky_topics = fetch($current_category, true);

// Check the numbers to fetch.
if(isset($start))
{
	if(is_numeric($start))
	{
		$topics = fetch($current_category, false, false, 'updated', 'DESC', intval($start), $config['messages_per_page']);
	}
	else
	{
		$topics = fetch($current_category, false, false, 'updated', 'DESC', 0, $config['messages_per_page']);
	}
}
else
{
	$topics = fetch($current_category, false, false, 'updated', 'DESC', 0, $config['messages_per_page']);
}

// Topic count
$topic_count = forum_count($current_category, '*', 'exclude_stickies');

// Messages per page
$pagination = generate_pagination($config['url_path'], $topic_count, $config['messages_per_page'], $start);

/**
 * Include header template
 */
include($config['template_path'] . "header.php");

/**
 * Include navigation template
 */
include($config['template_path'] . "navigation.php");

/**
 * Start index
 */
include($config['template_path'] . "forum/index-open.php");

// Any stickies?
if($sticky_topics)
{
	// Count
	$count = 0;
			
	// Stickies
	foreach($sticky_topics as $row)
	{
		// reset
		$sticky = ""; $closed = "";
		
		// Last post info for this topic
		$last_post = last_post($row['id']);
		
		// Trim subject
		$subject = character_limiter(trim(stripslashes($row['subject'])), $config['max_length']);
		
		// Build topic url
		$topic_url = "{$config['url_path']}/read.php?id={$row['id']}";
		
		// Topic starter data
		$topic_author = user_data($row['starter_id']);
		
		// Last post data
		$last_post_author = user_data($last_post['starter_id']);

		// Last post avatar
		$last_post_avatar = get_avatar($last_post_udata['id']);
		
		// Dates
		$row['date'] = date($config['date_format'], ($row['time'] + $config['zone']));
		$last_post['date'] = date($config['date_format'], ($last_post['time'] + $config['zone']));
		
		// Ago
		$row['ago'] = ago(($row['time'] + $config['zone']));
		$last_post['ago'] = ago(($last_post['time'] + $config['zone']));
		
		// Posts
		$posts = forum_count(false, $row['id'], false);
		
		// Alt
		if($count%2) { $alt = "dark"; } else { $alt = "light";}
		
		// Topic status
		if($row['closed'])
		{
			$closed = '<span class="closed rounded">' . lang('closed') . '</span>';
		}
		
		if($row['sticky'])
		{
			$sticky = '<span class="sticky rounded">' . lang('sticky') . '</span>';
		}
		
		/**
		 * Include sticky topics template
		 */
		include($config['template_path'] . "forum/topics.php");
		
		// Increase counter
		$count++;
	}
}

// Do we have any topics?
if($topics)
{	
	// Loop through normal posts
	foreach($topics as $row)
	{
		// reset
		$sticky = ""; $closed = "";
		
		// Last post info for this topic
		$last_post = last_post($row['id']);
		
		// Trim subject
		$subject = character_limiter(stripslashes($row['subject']), $config['max_length']);
		
		// Build topic url
		$topic_url = "{$config['url_path']}/read.php?id={$row['id']}";
		
		// Topic starter data
		$topic_author = user_data($row['starter_id']);
		
		// Last post data
		$last_post_author = user_data($last_post['starter_id']);
		
		// Last post avatar
		$last_post_avatar = get_avatar($last_post_udata['id']);
		
		// Dates
		$row['date'] = date($config['date_format'], ($row['time'] + $config['zone']));
		$last_post['date'] = date($config['date_format'], ($last_post['time'] + $config['zone']));
		
		// Ago
		$row['ago'] = ago(($row['time'] + $config['zone']));
		$last_post['ago'] = ago(($last_post['time'] + $config['zone']));
		
		// Posts
		$posts = forum_count(false, $row['id'], false);
		
		// Alt
		if($count%2) { $alt = "dark"; } else { $alt = "light";}
		
		// Topic status
		if($row['closed'])
		{
			$closed = '<span class="closed rounded">' . lang('closed') . '</span>';
		}
		
		/**
		 * Include topics template
		 */
		include($config['template_path'] . "forum/topics.php");

		// increase counter
		$count++;
	}
}

if(!$sticky_topics && !$topics)
{ 
	/**
	 * No topics to show, include no-topics template
	 */
	include($config['template_path'] . "forum/no-topics.php"); 
}

$categories = category();

if($current_category == 0)
{
	foreach($categories as $category)
	{
		if($category['expanded'])
		{
			/**
			 * Start index
			 */
			include($config['template_path'] . "forum/category-close.php");
			
			$sticky_topics = fetch($category['id'], true);
			$topics = fetch($category['id'], false, false, 'updated', 'DESC', 0, $config['messages_per_page']);
			
			/**
			 * Start index
			 */
			include($config['template_path'] . "forum/category-open.php");

			// Any stickies?
			if($sticky_topics)
			{
				// Count
				$count = 0;
						
				// Stickies
				foreach($sticky_topics as $row)
				{
					// reset
					$sticky = ""; $closed = "";
					
					// Last post info for this topic
					$last_post = last_post($row['id']);
					
					// Trim subject
					$subject = character_limiter(trim(stripslashes($row['subject'])), $config['max_length']);
					
					// Build topic url
					$topic_url = "{$config['url_path']}/read.php?id={$row['id']}";
					
					// Topic starter data
					$topic_author = user_data($row['starter_id']);
					
					// Last post data
					$last_post_author = user_data($last_post['starter_id']);

					// Last post avatar
					$last_post_avatar = get_avatar($last_post_udata['id']);
					
					// Dates
					$row['date'] = date($config['date_format'], ($row['time'] + $config['zone']));
					$last_post['date'] = date($config['date_format'], ($last_post['time'] + $config['zone']));
					
					// Ago
					$row['ago'] = ago(($row['time'] + $config['zone']));
					$last_post['ago'] = ago(($last_post['time'] + $config['zone']));
					
					// Posts
					$posts = forum_count(false, $row['id'], false);
					
					// Alt
					if($count%2) { $alt = "dark"; } else { $alt = "light";}
					
					// Topic status
					if($row['closed'])
					{
						$closed = '<span class="closed rounded">' . lang('closed') . '</span>';
					}
					
					if($row['sticky'])
					{
						$sticky = '<span class="sticky rounded">' . lang('sticky') . '</span>';
					}
					
					/**
					 * Include sticky topics template
					 */
					include($config['template_path'] . "forum/topics.php");
					
					// Increase counter
					$count++;
				}
			}

			// Do we have any topics?
			if($topics)
			{	
				// Loop through normal posts
				foreach($topics as $row)
				{
					// reset
					$sticky = ""; $closed = "";
					
					// Last post info for this topic
					$last_post = last_post($row['id']);
					
					// Trim subject
					$subject = character_limiter(stripslashes($row['subject']), $config['max_length']);
					
					// Build topic url
					$topic_url = "{$config['url_path']}/read.php?id={$row['id']}";
					
					// Topic starter data
					$topic_author = user_data($row['starter_id']);
					
					// Last post data
					$last_post_author = user_data($last_post['starter_id']);
					
					// Last post avatar
					$last_post_avatar = get_avatar($last_post_udata['id']);
					
					// Dates
					$row['date'] = date($config['date_format'], ($row['time'] + $config['zone']));
					$last_post['date'] = date($config['date_format'], ($last_post['time'] + $config['zone']));
					
					// Ago
					$row['ago'] = ago(($row['time'] + $config['zone']));
					$last_post['ago'] = ago(($last_post['time'] + $config['zone']));
					
					// Posts
					$posts = forum_count(false, $row['id'], false);
					
					// Alt
					if($count%2) { $alt = "dark"; } else { $alt = "light";}
					
					// Topic status
					if($row['closed'])
					{
						$closed = '<span class="closed rounded">' . lang('closed') . '</span>';
					}
					
					/**
					 * Include topics template
					 */
					include($config['template_path'] . "forum/topics.php");

					// increase counter
					$count++;
				}
			}

			if(!$sticky_topics && !$topics)
			{ 
				/**
				 * No topics to show, include no-topics template
				 */
				include($config['template_path'] . "forum/no-topics.php"); 
			}
		}
		else
		{
			continue;
		}
	}
}

/**
 * End index
 */
include($config['template_path'] . "forum/index-close.php");

// The online data for all users
$online_data = users_online();

// Guest Counter Plugin
if(is_loaded('guest_counter'))
{
	// The online data for guests
	$guests_online_data = guests_online(true);

	// The online data for bots
	$bots_online_data = guests_online(false, true);
}

// The online data for admins
$admin_online_data = users_online(true);

// Total users
$user_count = count_users();

// Forum counts
$topic_count = forum_count(false, '*', false);
$post_count = forum_count(false, false, 'all');

/**
 * Include forum details template
 */
include($config['template_path'] . "forum/details.php");

/**
 * Include footer template
 */
include($config['template_path'] . "footer.php"); 
?>