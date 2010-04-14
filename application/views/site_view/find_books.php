<div class="test">
    <?php echo $this->pagination->create_links(); ?>
</div>
<?php if (!empty($book_info)): // Check to see if $book_info has been passed ?>
<p>     Total Number of Results:
	<?php echo $book_info->Items->TotalResults; //Output total number of results in Amazon format ?>
</p>
<?php echo form_open('site/add_book'); ?>
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
            </td>
            <td>
                <?php
                    echo form_hidden('isbn', set_value('isbn', $book->ItemAttributes->ISBN));
                    echo form_submit('submit','Add to Bookshelve');
                ?>
                
            </td>
	</tr>
        <?php endforeach; ?>

</table>
<?php echo form_close(); ?>
<?php endif; ?>


</body>
</html>