<?php
/*
Plugin Name: BBCBar
Description: Allows users to easily insert bbcode into their posts
Version: 1.1
Author: Nijiko
Author URI: http://ninkobb.com
*/

/**
 * Add hook to the header
 */
add_hook('page_head', 'bbcbar', array());

/**
 * Add Hooks to forms
 */
add_hook('qr_textarea_before', 'quick_reply_bbcbar', array());
add_hook('msg_textarea_before', 'message_form_bbcbar', array());

/**
 * Installs captcha no sql needed!
 */
function install_bbcbar(){ }

/**
 * Uninstalls captcha.. no sql needed!
 */
function uninstall_bbcbar(){ }

/**
 * Includes the js/css in the header
 */
function bbcbar()
{
	echo '	<link href="plugins/bbcbar/bbcbar.css" rel="stylesheet" type="text/css" />' . "\n";
	echo '	<script src="plugins/bbcbar/bbcbar.js"></script>' . "\n";
}

/**
 * Insert bbcbar into the quick reply form
 */
function quick_reply_bbcbar()
{
?>
		<script type="text/javascript">
			$(function(){
				$('#qcontent').bbcodeeditor(
				{
					bold:$('.bold'),italic:$('.italic'),underline:$('.underline'),link:$('.link'),quote:$('.quote'),code:$('.code'),image:$('.image'),
					usize:$('.usize'),dsize:$('.dsize'),nlist:$('.nlist'),blist:$('.blist'),litem:$('.litem'),
					back:$('.back'),forward:$('.forward'),back_disable:'btn back_disable',forward_disable:'btn forward_disable',
					exit_warning:false
				});
			});
		</script>
		<div class="btn bold" title="bold"></div><div class="btn italic"></div><div class="btn underline"></div><div class="btn link"></div><div class="btn quote"></div>
		<div class="btn code"></div><div class="btn image"></div>
		<div class="btn blist"></div><div class="btn litem"></div>
		<div class="btn back"></div><div class="btn forward"></div>
<?php
}

/**
 * Insert bbcbar into the message form
 */
function message_form_bbcbar()
{
?>
		<script type="text/javascript">
			$(function(){
				$('#content').bbcodeeditor(
				{
					bold:$('.bold'),italic:$('.italic'),underline:$('.underline'),link:$('.link'),quote:$('.quote'),code:$('.code'),image:$('.image'),
					usize:$('.usize'),dsize:$('.dsize'),nlist:$('.nlist'),blist:$('.blist'),litem:$('.litem'),
					back:$('.back'),forward:$('.forward'),back_disable:'btn back_disable',forward_disable:'btn forward_disable',
					exit_warning:false
				});
			});
		</script>
		<div class="btn bold" title="bold"></div><div class="btn italic"></div><div class="btn underline"></div><div class="btn link"></div><div class="btn quote"></div>
		<div class="btn code"></div><div class="btn image"></div>
		<div class="btn blist"></div><div class="btn litem"></div>
		<div class="btn back"></div><div class="btn forward"></div>
<?php
}

?>