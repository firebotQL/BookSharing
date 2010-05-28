<div class="profile_view">
    <h4><?php echo $user_name ?> profile </h4>
    <div class="main_profile_area">
        <div class="profile_photo">
           <img src="<?php echo $user_data->avatar; ?>"  height="100px" width="100px"/>
        </div>
        <div class="profile_data">
            <h5>User data</h5>
            <p><?php
                    if (isset($my_profile))
                    {
                        echo anchor(base_url() . "site/edit_profile","(edit profile)");
                    }
                    else
                    {
                        if (!$friend_exist)
                        {
                            $url = base_url() . "site/add_friend/" . $user_data->user_id;
                            echo anchor($url, "Add to friendlist");
                        }
                        else
                        {
                            echo anchor("", "Already your friend", 'onclick="return false"');
                        }
                    }
                ?></p>
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
         <h5>Users bookshelf</h5>
         <div class="pagination">
            <?php echo $set_1 ?>
            </div>
            <div class="my_book_shelve">
                <table>
                <?php if (isset($book_list) && !empty($book_list)): ?>

                    <?php foreach ($book_list->Items->Item as $book): ?>
                            <tr>
                                <td class="profile_book_p">
                                    <p> Title: <strong><?php echo $book->ItemAttributes->Title; //Echo title of book ?></strong></p>
                                    <p> by <?php echo $book->ItemAttributes->Author; // Echo author of book ?></p>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                
                <?php else :?>
                  <tr>
                      <td>
                        <p>No books were added to the bookshelf</p>
                      </td>
                  </tr>
                <?php endif; ?>
                </table>
            </div>
        </div>
    <div class="profile_comments">
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
            echo form_open('comments/send/' . $url_user_id);
            echo form_textarea($textarea_init);
            echo form_submit('submit','Post');
            echo form_close();
        ?>
    </div>
</div>

