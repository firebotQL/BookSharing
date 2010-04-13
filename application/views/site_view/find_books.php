<div class="test">
    <?php echo $this->pagination->create_links(); ?>
</div>
<?php if (!empty($book_info)): // Check to see if $book_info has been passed ?>
<p>     Total Number of Results:
	<?php echo $book_info->Items->TotalResults; //Output total number of results in Amazon format ?>
</p>
<table class="books">
	<?php foreach($book_info->Items->Item as $book): //Loop through results ?>
	<tr>
            <td>
                <img src="<?php echo $book->MediumImage->URL; //Link to Thumbnail ?>"
                     alt="Cover of <?php echo $book->ItemAttributes->Title //Add title of book to alt attribute ?>"
                     class="image-thumbnail"
                     height="10%"
                     width="10%"/>
                <h3><?php echo $book->ItemAttributes->Title; //Echo title of book ?></h3>
                <p><?php echo $book->ItemAttributes->Author; // Echo author of book ?></p>
            </td>
	</tr>
        <?php endforeach; ?>

</table>
<?php endif; ?>


</body>
</html>