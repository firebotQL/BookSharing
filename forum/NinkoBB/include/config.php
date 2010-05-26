<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * config.php
 * 
 * Contains the default configurations settings, now inside of mysql.
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.3RC5
 * @lyric Why can't our bodies reset themselves? Won't you please reset me.
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 */

/**
 * Initiate array
 * @global array $config
 */
$config = array();

/**
 * Forum main configuration
 */

	// Site name
	$config['site_name'] = "ninko";
	
	// Site language
	$config['language'] = "en";
	
	// Theme
	$config['theme'] = "default";
	
	// Admin symbol
	$config['admin_symbol'] = "!"; // EG: !Nijikokun

	// URL path of forum WITHOUT trailing slash e.g, http://riot.anigaiku.com/board
	$config['url_path'] = "http://localhost/ninkobb/ninkobb/";

	// Path of directory WITHOUT trailing slash e.g, /home/anigaiku/riot/board
	$config['path'] = "";
	
	// Allow cookies? if you feel more secure without them change to false.
	$config['allow_cookies'] = true;
	$config['cookie_domain'] = '/'; // change for added security. advanced users only.
	$config['cookie_save'] = time() + 86400*365*2; // Time to save a cookie, 2 years.
	
/**
 * Registration configuration
 */

	// Username Length Minimum * Password uses this as well *
	$config['min_name_length'] = 3;

	// Username Length Maximum * Password uses this as well *
	$config['max_name_length'] = 32;
	
	// Email validation
	$config['email_validation'] = false; // set to true if you want them to validate their email
	$config['email_sender'] = "noreply@email.com"; // set to a noreply email for forum validation
	
	// Email validation message
	$config['email_subject'] =	"[Step 2 of 2] Your registration at {site_name}!";
	$config['email_message'] = 	"Hello {username}!" . "\r\n" .
								"You recently signed up at {$config['site_name']}, this email is to validate " .
								"that the email you used is a real email address." . "\r\n\r\n" . 
								"Click on the following link to validate your account:" . "\r\n" .
								"{link}" . "\r\n\r\n" .
								"----------------------------------------------------------------------------" . "\r\n" . 
								"This email was sent automatically. Please do not respond to this for support or help" . "\r\n" .
								"Thank you and have a nice day! From {site_name}";
	
	// Age validation
	$config['age_validation'] = false; // if you want to validate age, change to how old say.. 12 or 13, numerical!
	
/**
 * Avatar configuration
 */
		
	// Avatar configuration
	$config['avatar_max_size'] = 100; // max avatar size in kb, if you only know bytes then 21500/1024 would be acceptable.
	$config['avatar_max_width'] = 100; // max avatar width
	$config['avatar_max_height'] = 100; // max avatar height
	
	// Avatar saving
	//	- Make sure you chmod the avatar folder to 777!
	$config['avatar_upload_path'] = "avatars/"; // usually /home/public_html/location/to/avatar/folder/
	$config['avatar_folder_name'] = "avatars"; // the name of the folder
	$config['avatar_use'] = "username"; // username | email | id
	$config['avatar_md5_use'] = true; // md5 the username / email to obtain the avatar url?
	
	// Default avatar
	//	- avatars/['default_avatar']['default_avatar_type']
	//		- avatars/default.jpg
	//	- try to keep it a jpg, user avatars are png so if someone registers as default
	//	- their avatar will be -> default.png
	$config['default_avatar'] = "default";
	$config['default_avatar_type'] = ".jpg";
	
/**
 * User configuration
 */

	// Online settings
	$config['user_online_timeout'] = 30; // last seen for x seconds before not showing as online
	
	// Minimum message length
	$config['interests_min_length'] = 3;
	
	// Max length of interests - 9999999 for none srsly.
	$config['interests_max_length'] = 1000;

/**
 * Topic configuration
 */

	// Number of messages per page
	// Effects both number of messages on index.php
	$config['messages_per_page'] = 20;
	
	// Number of messages per page
	// Effects both number of messages on index.php
	$config['messages_per_topic'] = 13;
	
	// Minimum message length
	$config['message_minimum_length'] = 3;
	
	// Max length of post - 9999999 for none srsly.
	$config['message_max_length'] = 500;
	
	// Minimum subject length
	$config['subject_minimum_length'] = 3;
	
	// Max length of subjects - 9999999 for none srsly.
	$config['subject_max_length'] = 32;
	
	// Bbcode settings
	$config['bbcode'] = true;
	$config['bbcode_url'] = true;
	$config['bbcode_image'] = true;
	
	// Posting time limits
	$config['post_topic_time_limit'] = 30; // 30 seconds between posting
	$config['post_reply_time_limit'] = 10; // 10 seconds - you reply more than post topics
	
	// Show first post?
	$config['show_first_post'] = true; // always show the topics starting post on every page?
	
	// Allow quick reply?
	$config['allow_quick_reply'] = true; // allow users to post a quick reply from inside topic.

	// Number of characters after the subject needs to be cut off in the index
	$config['max_length'] = 32;

	// This strips slashes to the messages and subject when inserting into the database.
	// If you are getting slashes after quotes then change this to true.
	$config['slashes'] = false;
	
	// Here you can add some 'bad' words (use lowercase) [Depricated for now]
	// $config['bad_words'] = array("fuck", "cunt", "motherfucker", "twat");
	
/**
 * Time / Date configuration
 */

	// Date format
	$config['date_format'] = "F jS, Y, g:i a"; // the way we will display the date all over the site
		
	// To change time displayed change $timechange number (can be negative number as well)
	// This is in GMT time 
	$config['timechange'] = 7;

	// No changes need to be made to $zone or $submitdate
	$config['zone'] = 3600*-$timechange;
	$config['submitdate'] = date($config['date_format'], (time() + $config['zone']));


/**
 * Versioning configuration
 */

	// Version Number [1.3] do not change
	$config['version'] = "1.3";
?>