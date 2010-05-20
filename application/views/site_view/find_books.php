<h1>Book search results</h1>
<div class="pagination">
    <?php echo $this->pagination->create_links(); ?>
</div>
<!--<pre><?php //print_r($book_info) ?></pre> -->
<?php if (!empty($book_info)): // Check to see if $book_info has been passed ?>
<p>     Total Number of Results:
	<?php echo $book_info->Items->TotalResults; //Output total number of results in Amazon format ?>
</p>

<table class="books">
	<?php foreach($book_info->Items->Item as $book): //Loop through results ?>
	<tr>
            <td>
                <img src="<?php echo ($book->MediumImage->URL != "") ? $book->MediumImage->URL : "/images/no-cover.png"; // Link to Thumbnail?>"
                     class="image-thumbnail"
                     height="100px"
                     width="75px"/>
            </td>
            <td>
                <p> Title: <strong><?php echo $book->ItemAttributes->Title; //Echo title of book ?></strong></p>
                <p> by <?php echo $book->ItemAttributes->Author; // Echo author of book ?></p>
                <?php
                   // foreach( as $row)
                   // {
                        echo $book->ItemAttributes->Feature;
                   // }
                ?>
            </td>
            <td>
                <?php
                  $isbn = (string)$book->ItemAttributes->ISBN;

                       echo form_open('books/details');
                       echo form_hidden('isbn', $isbn);
                       echo form_submit('submit', 'View details');
                       echo form_close();
                  if (!$exist[$isbn])
                  {
                       echo form_open('books/add_book');
                       echo form_hidden('action', set_value('action', $action));
                       echo form_hidden('keywords', set_value('keywrods',$keywords));
                       echo form_hidden('item_page', set_value('item_page',$item_page));
                       echo form_hidden('isbn', set_value('isbn', $isbn));
                       echo form_hidden('type', set_value('type', isset($book->ItemAttributes->TYPE) ?
                                                                  $book->ItemAttributes->TYPE :
                                                                  "0"));
                       echo form_hidden('page_type', set_value('page_type', '0'));
                       echo form_submit('submit','Add to Bookshelve');
                       echo form_close();
                  }
                  else
                  {
                      echo "<p>Already in bookshevlve</p>";
                  }
                ?>

                
            </td>
	</tr>
        <?php endforeach; ?>

</table>
<?php echo form_close(); ?>
<?php endif; ?>


</body>
</html>