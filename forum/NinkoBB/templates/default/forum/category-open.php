<div class="container">
	<h1 class="title topic"><a href="<?php echo $config['url_path']; ?>?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?> &raquo;</a></h1>
	<table width="100%" id="topic">
		<tr>
			<th class="subject" colspan="2">Topic</th>
			<th class="posts">Posts</th>
			<th class="last">Last Poster</th>
<?php 
load_hook('topic_headers'); 
?>
		</tr>
