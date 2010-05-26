<?php
/*
Plugin Name: Guest Counter
Description: Counts Guests / Bots Online
Version: 1.0
Author: Nijiko
Author URI: http://ninkobb.com
*/

/**
 * Add hook to common to update guests
 */
add_hook('common', 'update_guests', array());

/**
 * List of bots
 */
$config['bot_list'] = array(
	'AdsBot [Google]'			=> 'AdsBot\-Google',
	'Alexa [Bot]'				=> 'ia\_archiver',
	'Alta Vista [Bot]'			=> 'Scooter/',
	'Ask Jeeves [Bot]'			=> 'Ask Jeeves',
	'Baidu [Spider]'			=> 'Baiduspider\+\(',
	'Exabot [Bot]'				=> 'Exabot/',
	'FAST Enterprise [Crawler]'	=> 'FAST Enterprise Crawler',
	'FAST WebCrawler [Crawler]'	=> 'FAST\-WebCrawler/',
	'Francis [Bot]'				=> 'http://www.neomo.de/',
	'Gigabot [Bot]'				=> 'Gigabot/',
	'Google Adsense [Bot]'		=> 'Mediapartners-Google',
	'Google Desktop'			=> 'Google Desktop',
	'Google Feedfetcher'		=> 'Feedfetcher-Google',
	'Google [Bot]'				=> 'Googlebot',
	'Heise IT-Markt [Crawler]'	=> 'heise\-IT\-Markt-Crawler',
	'Heritrix [Crawler]'		=> 'heritrix/1\.',
	'IBM Research [Bot]'		=> 'ibm.com/cs/crawler',
	'ICCrawler - ICjobs'		=> 'ICCrawler - ICjobs',
	'ichiro [Crawler]'			=> 'ichiro/2',
	'Majestic-12 [Bot]'			=> 'MJ12bot/',
	'Metager [Bot]'				=> 'MetagerBot/',
	'MSN NewsBlogs'				=> 'msnbot-NewsBlogs/',
	'MSN [Bot]'					=> 'msnbot/',
	'MSNbot Media'				=> 'msnbot\-media/',
	'NG-Search [Bot]'			=> 'NG-Search/',
	'Nutch [Bot]'				=> 'http\://lucene.apache.org/nutch/',
	'Nutch/CVS [Bot]'			=> 'NutchCVS/',
	'OmniExplorer [Bot]'		=> 'OmniExplorer_Bot/',
	'Online link [Validator]'	=> 'online link validator',
	'psbot [Picsearch]'			=> 'psbot/0',
	'Seekport [Bot]'			=> 'Seekbot/',
	'Sensis [Crawler]'			=> 'Sensis Web Crawler',
	'SEO Crawler'				=> 'SEO search Crawler/',
	'Seoma [Crawler]'			=> 'Seoma \[SEO Crawler\]',
	'SEOSearch [Crawler]'		=> 'SEOsearch/',
	'Snappy [Bot]'				=> 'Snappy/1.1 \( http://www.urltrends.com/ \)',
	'Steeler [Crawler]'			=> 'http://www.tkl.iis.u-tokyo.ac.jp/~crawler/',
	'Synoo [Bot]'				=> 'SynooBot/',
	'Telekom [Bot]'				=> 'crawleradmin.t-info@telekom.de',
	'TurnitinBot [Bot]'			=> 'TurnitinBot/',
	'Voyager [Bot]'				=> 'voyager/1.0',
	'W3 [Sitesearch]'			=> 'W3 SiteSearch Crawler',
	'W3C [Linkcheck]'			=> 'W3C-checklink/',
	'W3C [Validator]'			=> 'W3C_*Validator',
	'WiseNut [Bot]'				=> 'http://www.WISEnutbot.com',
	'YaCy [Bot]'				=> 'yacybot',
	'Yahoo MMCrawler [Bot]'		=> 'Yahoo-MMCrawler/',
	'Yahoo Slurp [Bot]'			=> 'Yahoo! DE Slurp',
	'Yahoo [Bot]'				=> 'Yahoo! Slurp',
	'YahooSeeker [Bot]'			=> 'YahooSeeker/',
);

/**
 * Installs Guest counter
 * @global resource
 */
function install_guest_counter()
{
	global $database;
	
	// Create the table `guests`
	$database->query("CREATE TABLE IF NOT EXISTS `guests` (`ip` text NOT NULL, `visit` text NOT NULL, `type` text NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;") or die(mysql_error());
}

/**
 * Installs Guest counter
 * @global resource
 */
function uninstall_guest_counter()
{
	global $database;
	
	// Delete
	$database->query("DROP TABLE IF EXISTS `guests`") or die(mysql_error());
}

/**
 * Cleans up the guest array
 * @global array
 * @param string $referrer
 * @return boolean
 */
function is_bot($referrer)
{
	global $config;
	
	foreach($config['bot_list'] as $key => $bot)
	{
		if(preg_match("#{$bot}#iU", $referrer))
		{
			return $key;
		}
	}
	
	return false;
}

/**
 * Cleans up the guest array
 * @global array
 * @global resource
 */
function update_guests()
{
	global $config, $database;
	
	// The time between them
	$time_between = time() - $config['user_online_timeout'];
	$time = time();
	
	// Clean up the database of old guests
	$result = $database->query("DELETE FROM `guests` WHERE `visit` < '{$time_between}'");
	
	// Insert a new one
	if(!$_SESSION['logged_in'])
	{
		$bot_check = is_bot($_SERVER["HTTP_USER_AGENT"]);
		
		// Are they a bot or a guest?
		if(is_string($bot_check))
		{
			$type = $bot_check;
		}
		else
		{
			$type = "GUEST";
		}
		
		// Grab the hostname
		$host = gethostname();
		
		// Check to see if they already exist.
		$result = $database->query( "SELECT * FROM `guests` WHERE `ip` = '{$host}'" );

		if($database->num($result) < 1)
		{
			// Insert them in there.
			$database->query("INSERT INTO `guests` (`visit`,`ip`,`type`) VALUES ('{$time}', '{$host}', '{$type}')");
		}
		else
		{
			// Insert them in there.
			$database->query("UPDATE `guests` SET `visit` = '{$time}' WHERE `ip` = '{$host}'");
		}
	}
}

/**
 * Checks to see what guests are online, can check for all together or bots alone.
 * @global array
 * @global resource
 * @param boolean $all check for all together?
 * @param boolean $bots just for bots?
 * @return array
 */
function guests_online($all = false, $bots = false)
{
	global $config, $database;
	
	if($all)
	{
		$result = $database->query( "SELECT * FROM `guests` ORDER BY `visit`" );
	}
	else if($bots)
	{
		$result = $database->query( "SELECT * FROM `guests` WHERE `type` != 'GUEST' ORDER BY `visit`" );
	}
	else
	{
		$result = $database->query( "SELECT * FROM `guests` WHERE `type` = 'GUEST' ORDER BY `visit`" );
	}
	
	// is there a result?
	if($database->num($result) < 1)
	{
		return array('count' => 0, 'users' => false);
	}
	else
	{
		// The overall count
		$online['count'] = $database->num($result);
		$count = 1;
		
		// Making the list
		if($bot || $all)
		{
			while($row = $database->fetch($result))
			{
				if($row['type'] != "GUEST")
				{
					// the seperator
					$seperator = ", ";
					
					// the bot
					$username = "<span class='bot'>{$row['type']}</span>";
					
					// add to list
					$online['users'] .= "{$username}{$seperator}";
				}
			}
			
			// simple fix for commas
			$online['users'] = substr($online['users'], 0, -2); 
		}
		
		// Returns array
		return $online;
	}
}

?>