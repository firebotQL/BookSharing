<div class="profile_view">
    <h4><?php echo $user_name ?> profile</h4>
    <div class="main_profile_area">
        <div class="profile_photo">
           <img src="/images/no_avatar.gif"  height="125px" width="115px"/>
        </div>
        <div class="profile_data">
            <h5>User data</h5>
            <span>Name: <?php echo $user_data->first_name; ?> </span><br/>
            <span>Country: <?php echo $user_data->country; ?> </span><br/>
            <span>City: <?php echo $user_data->city; ?> </span><br/>
            <span>Sex: <?php echo $user_data->sex; ?> </span><br/>
            <span>Age: <?php echo $user_data->age; ?> </span><br/>
            <span>Education: <?php echo $user_data->education; ?> </span><br/>
            <span>Work: <?php echo $user_data->work; ?> </span><br/>
            <span>Description: <?php echo $user_data->description; ?> </span><br/>
        </div>
    </div>
    <div class="profile_bookshelve">
         <h5>User bookshelve</h5>
         <div class="pagination">
            <?php echo $this->pagination->create_links(); ?>
            </div>
            <div class="my_book_shelve">
                <?php if (!empty($book_list)): ?>
                <table>
                    <?php foreach ($book_list->Items->Item as $book): ?>
                            <tr>
                                <td class="profile_book_p">
                                    <p> Title: <strong><?php echo $book->ItemAttributes->Title; //Echo title of book ?></strong></p>
                                    <p> by <?php echo $book->ItemAttributes->Author; // Echo author of book ?></p>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>
    <div class="profile_comments">
        <h5>Comments</h5>
        <?php
            $textarea_init = array('name' => 'comment_content',
                 'cols' => '60',
                 'value' => 'Here is your comment');
            echo form_open('comments/profile/' . $profile_id);
            echo form_textarea($textarea_init);
            echo form_submit('submit','Post');
            echo form_close();
        ?>
    </div>
</div>

