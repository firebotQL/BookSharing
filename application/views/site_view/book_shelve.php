<div class="pagination">
    <?php echo $this->pagination->create_links(); ?>
</div>
<div class="my_book_shelve">
    <?php if ($book_list->Items->TotalResults > 0): ?>
    <table>
        <?php foreach ($book_list->Items->Item as $book): ?>
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
                            echo form_open('books/details');
                            echo form_hidden('isbn', set_value('isbn', $book->ItemAttributes->ISBN));
                            echo form_submit('submit', 'View details');
                            echo form_close();
                            echo form_open('books/remove');
                            echo form_hidden('isbn', set_value('isbn', $book->ItemAttributes->ISBN));
                            echo form_submit('submit', 'Remove book');
                            echo form_close();
                        ?>
                    </td>
                </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <div>
        No books were added to the bookshelve
    </div>
    <?php endif; ?>
</div>