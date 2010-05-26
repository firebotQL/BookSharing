<div class="container">
<?php
if($current_category != 0)
{
?>
	<h1 class="title topic"><a href="<?php echo $config['url_path']; ?>"><?php echo lang('home_c'); ?></a> &raquo; <?php echo $category_data['name']; ?></h1>
<?php
}
else
{
?>
	<h1 class="title topic">New Topics</h1>
<?php
}
?>
	<table width="100%" id="topic">
		<tr>
			<th class="subject" colspan="2">Topic</th>
			<th class="posts">Posts</th>
			<th class="last">Last Poster</th>
<?php 
load_hook('topic_headers'); 
?>
		</tr>
