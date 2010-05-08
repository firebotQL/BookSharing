<div class="book_details">
    <div class="main_details">
        <div class="cover_and_rating">
            <img src="<?php echo ($book->MediumImage->URL != "") ? $book->MediumImage->URL : "/images/no-cover.png"; // Link to Thumbnail?>"
                     class="image-thumbnail"
                     height="100px"
                     width="75px"/>
            <p>Rating hides here</p>
            <?php
                if ($exist == FALSE)
                {
                    echo "<a href=\"#\" class=\"add_details_book\"> Add to Bookshelve </a>";
                    echo form_open();
                    echo form_hidden('jid_hidden_isbn', $book->ItemAttributes->ISBN);
                    echo form_hidden('jid_hidden_type', "1");
                    echo form_close();
                } 
                else  
                {
                    echo '<p> Already in bookshelve </p>';
                }
            ?>
        </div>
        <div class="book_description">
            <p>Title: <strong><?php echo $book->ItemAttributes->Title; //Echo title of book ?></strong></p>
            <p>Author: <?php echo $book->ItemAttributes->Author; // Echo author of book ?></p>
            <p>Publisher: <?php echo $book->ItemAttributes->Publisher; // Echo publisher of book ?></p>
            <p>ISBN: <?php echo $book->ItemAttributes->ISBN; // Echo ISBN of book ?></p>
            <p>Publication Date: <?php echo $book->ItemAttributes->PublicationDate; // Echo pub date of book ?></p>
            <p>Number of pages: <?php echo $book->ItemAttributes->NumberOfPages; // Echo num of book pages ?></p>
        </div>
    </div>
    <div class="book_comments">
        <h5>Comments</h5>
        <?php echo $set_2; ?>
            <div class="comments_area">
                <?php if (isset($comments) && $comments->num_rows() > 0): ?>
                    <table>
                        <?php foreach ($comments->result() as $row): ?>
                        <tr>
                            <td class="profile_comment_a">
                                <img src="<?php echo $row->avatar; ?>"  height="55px" width="45px"/>
                                <p> <?php echo anchor('site/profile/' . $row->sender_id, $row->sender_name); ?> </p>
                            </td>
                            <td class="profile_comment_p" valign="top">
                                <p><strong><?php echo $row->time_stamp ?></strong></p>
                                <p><?php echo $row->content ?></p>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        <?php
            $textarea_init = array('name' => 'comment_content',
                 'cols' => '60',
                 'value' => 'Here is your comment');
            echo form_open('comments/send/' . $isbn);
            echo form_hidden('type_id', '1');
            echo form_textarea($textarea_init);
            echo form_submit('submit','Post');
            echo form_close();
        ?>
    </div>
</div>