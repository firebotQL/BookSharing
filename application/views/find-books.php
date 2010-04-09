<?php

	//If page is displaying a search result, include that information
	//at the top of the page.  If not, then just display instructions
	//for searching Amazon Database.
	if (isset($page_info['search-term']))
	{
		echo "<h2>Search Results For \"". $page_info['search-term'] ."\"</h2>";
		echo "<p>Go back to the \"". anchor('books', 'Find Books')."\" page. Search again below.</p>";

	}
	elseif (isset($page_info['error-message']))
	{
		echo "<h2>".$page_info['title']."</h2>";
		echo "<p>".$page_info['error-message'].".</p>";
	}
	else
	{
		echo "<h2>".$page_info['title']."</h2>";
		echo "<p>In the search field below type the name of a <em>teacher</em>, <em>author</em>, or <em>book title</em>.</p>";
	}


	// Set attributes for the form
	$form_attributes = array('class' => 'search-form', 'id' => 'search-form', 'name' => 'search_terms');
	$hidden_fields = array('item_page' => '1', 'action' => 'search');
	echo form_open('books/search_amazon', $form_attributes, $hidden_fields);

	// Set attributes for the input field
	$keyword_data = array('name'=>'keywords','class'=>'keyword',);
	echo form_input($keyword_data) ;

	// Set attributes for the submit field
	$submit_data = array ('name'  => 'submit','class' => 'button submit');
	echo form_submit($submit_data, 'Search Amazon');

	echo '</form>';


?>


<?php if (isset($book_info)): // Check to see if $book_info has been passed ?>
<p>
        <?php print_r($book_info); ?>
	Total Number of Results:
	<?php echo $book_info->Items->TotalResults; //Output total number of results in Amazon format ?>
        <br/>
        Debugging:
        <?php if ($book_info === "") : ?>
        <?php log_message('debug','Some var not set'); ?>
        <?php endif; ?>
</p>
<p>
	Page
	<?php echo $page_info['item-page']; //Output page number ?>
	out of
	<?php echo $book_info->Items->TotalPages; //Output total number of pages ?>
	pages
</p>
<table class="books">
	<tr>
		<th class="name">Title (Author)</th>
	</tr>
	<?php foreach($book_info->Items->Item as $book): //Loop through results ?>
	<tr>
		<td>
			<img src="<?php echo $book->MediumImage->URL; //Link to Thumbnail ?>" alt="Cover of <?=$book->ItemAttributes->Title //Add title of book to alt attribute ?>" class="image-thumbnail" />
			<h3><?php echo $book->ItemAttributes->Title; //Echo title of book ?></h3>
			<p></p><?php echo $book->ItemAttributes->Author; // Echo author of book ?></p>
		</td>
	</tr>
        <?php endforeach; ?>

</table>
<?php
	//This code below could probably be handled in the controller?

	//check to see if there are previous pages
	if($page_info['item-page'] > 1 && $book_info->Items->TotalPages > 1)
	{
		$keywords = $page_info['search-term'];
		$item_page = $page_info['item-page'];
		$item_page = $item_page - 1;

		//Form for the previous page button
		$form_attributes = array('name' => 'search_terms');
		$hidden_fields = array('item_page' => $item_page, 'action' => 'search', 'keywords' => $keywords);
		echo form_open('books/search_amazon', $form_attributes, $hidden_fields);
		$submit_data = array ('name'  => 'submit','class' => 'button submit');
		echo form_submit($submit_data, 'Previous Page');
		echo '</form>';
	}
	//check to see if there are more pages
	if($page_info['item-page'] < $book_info->Items->TotalPages)
	{
		$keywords = $page_info['search-term'];
		$item_page = $page_info['item-page'];
		$item_page = $item_page + 1;

		//Form for the next page button
		$form_attributes = array('name' => 'search_terms');
		$hidden_fields = array('item_page' => $item_page, 'action' => 'search', 'keywords' => $keywords);
		echo form_open('books/search_amazon', $form_attributes, $hidden_fields);
		$submit_data = array ('name'  => 'submit','class' => 'button submit');
		echo form_submit($submit_data, 'Next Page');
		echo '</form>';
	}
?>
<?php endif; ?>


</body>
</html>