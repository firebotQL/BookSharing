<?php
/**
 * message.php
 * 
 * Creation of topics, and posts begins right here in this file.
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 */
 
// Include common.php
require("include/common.php");

// Include header
include($config['template_path'] . "header.php");
	
// Are we posting?
if(isset($_POST['post']))
{
	// Are we an admin?
	if($_SESSION['admin'] || $_SESSION['moderator'])
	{
		// Check to see if we are trying to sticky or close a topic
		if($_POST['sticky'] == "on") { $sticky = 1; } else { $sticky = 0; }
		if($_POST['closed'] == "on") { $closed = 1; } else { $closed = 0; }
		
		// Longest hook yet, before the post, inside of session / moderator check
		load_hook('before_post_function_admin_mod');
	}
	else
	{
		// Can't sticky or close if we aren't an admin
		$sticky = 0;
		$closed = 0;
	}
		
	// Trying to preview post?
	if($_POST['preview'])
	{
		$preview = true;
	}
	else
	{
		// Before post function
		load_hook('before_post_function');
		
		/**
		 * Now for the fun part!
		 */
		$data = post($_POST['category'], $_POST['subject'], $_POST['content'], $_POST['reply'], $sticky, $closed);
		
		// After the post is done dunno what for but w/e
		load_hook('after_post_function');
					
		// Errors
		if(is_string($data) && !is_numeric($data))
		{
			$error = $data;
		}
		else
		{
			if(is_array($data))
			{
				$redirect = $config['url_path'] . '/read.php?id=' . $_POST['reply'] . '&page=' . $data['page'] . '#p-' . $data['id'];
				$success = lang('success_post');
			}
			else
			{
				$redirect = $config['url_path'] . '/read.php?id=' . $data;
				$success = lang('success_topic');
			}
			
			// Redirect back to the topic!
			print_out($success, lang('redirecting_topic'), $redirect);
		}
	}
}
else if(isset($_POST['edit']))
{
	// Are we an admin?
	if($_SESSION['admin'] || $_SESSION['moderator'])
	{
		// Check to see if we are trying to sticky or close a topic
		if($_POST['sticky'] == "on") { $sticky = 1; } else { $sticky = 0; }
		if($_POST['closed'] == "on") { $closed = 1; } else { $closed = 0; }
	}
	else
	{
		// Can't sticky or close if we aren't an admin
		$sticky = 0;
		$closed = 0;
	}
		
	// Trying to preview post?
	if($_POST['preview'])
	{
		$preview = true;
	}
	else
	{
		/**
		 * Now for the fun part!
		 */
		$data = update($_GET['edit'], $_POST['category'], $_POST['subject'], $_POST['content'], $sticky, $closed);
					
		// Errors
		if(is_string($data) && !is_numeric($data))
		{
			$error = $data;
		}
		else
		{
			$topic_data = topic($_GET['edit']);
			
			// Are we replying or is it a topic?
			if($topic_data['reply'])
			{ 
				$id = $topic_data['reply'];
				$success = lang('success_edited_post');
			} 
			else 
			{ 
				$id = $topic_data['id'];
				$success = lang('success_edited_topic');
			}
			
			// Create the url
			if(is_array($data))
			{
				$redirect = $config['url_path'] . '/read.php?id=' . $id . '&page=' . $data['page'] . '#p-' . $data['id'];
				$success = lang('success_post');
			}
			else
			{
				$redirect = $config['url_path'] . '/read.php?id=' . $id;
				$success = lang('success_topic');
			}
			
			// Redirect back to the topic!
			print_out($success, lang('redirecting_topic'), $redirect);
		}
	}
}

// sanitize $reply
if(isset($_GET['reply']))
{
	if($_GET['reply'] != 0)
	{
		/**
		 * Validate reply with alpha
		 */
		if(alpha($_GET['reply'], 'numeric'))
		{
			$reply = $_GET['reply'];
				
			// Get topic data
			$topic = topic($reply, 'subject');
			
			if($topic)
			{
				// Title
				$title = lang('replying_to') . ": {$topic['subject']}";
					
				// Our Subject
				$_POST['subject'] = "Re: {$topic['subject']}";
				
				if($_GET['q'] && alpha($_GET['q'], 'numeric'))
				{
					$quote_data = topic($_GET['q'], 'message,starter_id');
					
					$quote_user_data = user_data($quote_data['starter_id']);
					
					$quote_data['message'] = br2nl(stripslashes(parse($quote_data['message'], false)));
					
					$content = '[quote=' . $quote_user_data['username'] . ']' . $quote_data['message'] . '[/quote]';
				}
			}
			else
			{
				$reply = 0;
				
				// New topic 
				$title = lang('posting_new_topic');
			}
		}
		else
		{
			$reply = 0;
			
			// New topic 
			$title = lang('posting_new_topic');
		}
	}
	else
	{
		$reply = 0;
		
		// New topic 
		$title = lang('posting_new_topic');
	}
}
else if(isset($_GET['edit']))
{
	if(alpha($_GET['edit'], 'numeric'))
	{
		$reply = false;
		$edit = true;
		
		// Get topic data
		$post = topic($_GET['edit']);
		
		if($post)
		{
			// Title
			$title = lang('editing_post');
			
			// Convienent stuff.
			$subject = $post['subject'];
			$content = html_entity_decode(htmlspecialchars_decode(stripslashes($post['message'])));
			$sticky = $post['sticky'];
			$closed = $post['closed'];
			$category = $post['category'];
		}
		else
		{
			$reply = 0;
			
			// New topic
			$title = lang('posting_new_topic');
		}
	}
	else
	{
		$reply = 0;
		
		// New topic 
		$title = lang('posting_new_topic');
	}
}
else
{
	$reply = 0;
	
	// New topic 
	$title = lang('posting_new_topic');
}

// Clean the subject
if(isset($_POST['subject']))
{
	$subject = field_clean($_POST['subject']);
}

// Get the category from the url
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

// Forum navigation
include($config['template_path'] . "navigation.php");
?>

<?php if($preview){ include($config['template_path'] . "forum/preview.php"); } ?>

<?php include($config['template_path'] . "forum/message-form.php"); ?>

<?php include($config['template_path'] . "footer.php"); ?>