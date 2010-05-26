	</table>
</div>

<?php
if($current_category == 0)
{
	// If we do..
	if(show_categories())
	{
?>
<div class="container">
	<h1 class="title topic">Categories</h1>
	<table width="100%" id="topic">
		<tr>
			<th class="subject">Category</th>
			<th class="posts">Posts</th>
			<th class="posts">Topics</th>
<?php 
		load_hook('category_headers'); 
?>
		</tr>
<?php 
		foreach($categories as $category)
		{ 
			if($category['expanded']){ continue; }
?>
		<tr>
			<td class="subject"><a href="?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a></td>
			<td class="posts"><?php $posts = forum_count($category['id'], false, 'posts'); echo (int) $posts; ?></td>
			<td class="posts"><?php $posts = forum_count($category['id'], false, 'all', false, true); echo (int) $posts; ?></td>
		</tr>
<?php
		}	
?>
	</table>
</div>
<?php
	}	
?>
<?php
}	
?>