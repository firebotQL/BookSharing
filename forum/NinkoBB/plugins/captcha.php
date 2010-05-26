<?php
/*
Plugin Name: Captcha
Description: Prevents bots.. duh :P
Version: 1.0
Author: Nijiko
Author URI: http://ninkobb.com
*/

/**
 * Add hook to check the session
 */
add_hook('registration_form', 'insert_captcha', array());

/**
 * Add hook to check the session
 */
add_hook('registration_check', 'check_captcha', array());

/**
 * Installs captcha no sql needed!
 */
function install_captcha(){ }

/**
 * Uninstalls captcha.. no sql needed!
 */
function uninstall_captcha(){ }

/**
 * Creates the captcha and saves it in captcha/
 * @global array
 */
function captcha()
{
	global $config;
	
	// captcha width
	$captcha_w = 150;
	
	// captcha height
	$captcha_h = 32;
	
	// minimum font size; each operation element changes size
	$min_font_size = 12;
	
	// maximum font size
	$max_font_size = 16;
	
	// rotation angle
	$angle = 20;
	
	// background grid size
	$bg_size = 8;
	
	// path to font - needed to display the operation elements
	$font_path = 'plugins/captcha/courbd.ttf';
	
	// array of possible operators
	$operators = array("+","-","*");
	
	// first number random value; keep it lower than $second_num
	$first_num = rand(1,5);
	
	// second number random value
	$second_num = rand(3,11);
	
	// Shuffle the operators
	shuffle($operators);
	
	// Save the expression as string
	$expression = $second_num . $operators[0] . $first_num;
	
	// operation result is stored in $session_var
	switch($operators[0])
	{
		case "+": $session_var = intval($second_num + $first_num); break;
		case "-": $session_var = intval($second_num - $first_num); break;
		case "*": $session_var = intval($second_num * $first_num); break;
	}
	
	// save the operation result in session to make verifications
	$_SESSION['security_number'] = $session_var;
	
	//start the captcha image
	$img = imagecreate( $captcha_w, $captcha_h );
	
	// Some colors. Text is $black, background is $white, grid is $grey
	$black = imagecolorallocate($img,0,0,0);
	$white = imagecolorallocate($img,255,255,255);
	$grey = imagecolorallocate($img,215,215,215);
	
	//make the background white
	imagefill( $img, 0, 0, $white );
	
	// the background grid lines - vertical lines
	for ($t = $bg_size; $t<$captcha_w; $t+=$bg_size){
		imageline($img, $t, 0, $t, $captcha_h, $grey);
	}
	
	// background grid - horizontal lines
	for ($t = $bg_size; $t<$captcha_h; $t+=$bg_size){
		imageline($img, 0, $t, $captcha_w, $t, $grey);
	}
	
	// Possible spaces
	$item_space = $captcha_w/3;
	
	// First number
	imagettftext($img, rand($min_font_size, $max_font_size), rand(-$angle , $angle), rand(10, $item_space-20), rand(25, $captcha_h-15), $black, $font_path, $second_num);
	
	// Operator
	imagettftext($img, rand($min_font_size, $max_font_size), rand(-$angle , $angle), rand($item_space, 2*$item_space-20), rand(25, $captcha_h-15), $black, $font_path, $operators[0]);
	
	// Second Number
	imagettftext($img, rand($min_font_size, $max_font_size), rand(-$angle , $angle), rand(2*$item_space, 3*$item_space-20), rand(25, $captcha_h-15), $black, $font_path, $first_num);

	// Output image
	imagejpeg($img, 'plugins/captcha/secure.jpg');
	
	// Make sure we can write to it
	if(!is_writable('plugins/captcha/secure.jpg'))
	{
		@chmod('plugins/captcha/secure.jpg', 0777);
	}
}

/**
 * Insert captcha into registration table
 * @global array
 */
function insert_captcha()
{
	global $config;
	
	captcha();
	
		echo '<tr>'. "\n";
			echo '<td class="post captcha" colspan="2">';
				echo '<label for="captcha">' . lang('captcha_title') . '</label>';
				echo '<table><tr><td width="18%"><image src="plugins/captcha/secure.jpg" alt="security" /></td>';
				echo '<td><input type="text" id="captcha" name="captcha" style="width: 99.3%;" class="border" /></td></tr></table>';
			echo '</td>' . "\n";
		echo '</tr>' . "\n";
}

/**
 * Check to see if user entered the correct data
 * @global string
 */
function check_captcha()
{
	global $error;
	
	if($_SESSION['security_number'] != $_POST['captcha'])
	{
		$error = lang('captcha_false');
	}
}

?>