<div class="main_books">
    <div class="book_upload">
        <h1> Profile editing </h1>
        <?php echo isset($error) ? $error : "" ?>
        <?php echo validation_errors('<p class="error">');?>
        <?php
            echo form_open_multipart('books/upload');
            echo form_label('Upload cover', 'l_cover');
            echo form_upload('cover', set_value('cover', 'Upload cover'));
            echo form_label('Title: ', 'l_title');
            echo form_input('title', set_value('title',"Enter title"));
            echo form_label('Author: ', 'l_author');
            echo form_input('author', set_value('author', "Enter author"));
            echo form_label('Publisher: ', 'l_publisher');
            echo form_input('publisher', set_value('publisher', "Enter publisher"));
            echo form_label('ISBN: ', 'l_isbn');
            echo form_input('isbn', set_value('isbn', "Enter ISBN"));
            echo form_label('Publication date: ', 'l_pub_date');
            echo form_input('publ_date', set_value('publ_date', "publ_date"));
            echo form_label('Pages: ', 'l_pages');
            echo form_input('pages', set_value('pages', "Enter total pages"));
            echo form_submit('submit', 'Go to step 2');
            echo form_close();
        ?>

        <span>ISBN and Book title is required fields</span>
    </div>
    <div class="book_random">
        <h1>Add books from here or search them</h1>
        <?php
           $form_attributes = array('class' => 'search-form', 'id' => 'search-form', 'name' => 'search_terms');
           $hidden_fields = array('item_page' => '1', 'action' => 'search');
           echo form_open('books/search', $form_attributes, $hidden_fields);
           echo "<div>";
           $keyword_data = array('name'=>'keywords',
                                 'class'=>'keyword',
                                 'value' => 'find books by title or author or isbn',
                                 'size' => "15");
           echo form_input($keyword_data);

           $submit_data = array ('name'  => 'submit','class' => 'button submit');
           echo form_submit($submit_data, 'Search books');
           echo "</div>";
           echo form_close();
        ?>
        <div class="rand_books_list">
            <table>
            <?php if (isset($book_list)): ?>
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
                            ?>
                        </td>
                    </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
        </div>
    </div>
</div>