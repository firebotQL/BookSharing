<div class="news_details">
    <div class="news_details_area">
        <?php if (isset($news_list)) : ?>
        <table>
            <?php foreach ($news_list->result() as $news): ?>
                    <tr>
                        <td>

                            <h1><?php echo $news->header; ?></h1>
                            <p>
                                <?php echo $news->content; ?>
                            </p>
                            <p class="post-footer align-right">
                                    <p>Published by
                                        <a href="<?php echo base_url(); ?>site/profile/<?php echo $news->user_id;  ?>">
                                                <?php echo $news->username; ?>
                                        </a>
                                        <span class="date">at <?php echo $news->publish_time ?></span>
                                    </p>

                            </p>
                        </td>
                    </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="news_comments">
        <h5>Comments</h5>
       <div class="pagination">
            <?php echo $this->pagination->create_links(); ?>
       </div>
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
            echo form_open('comments/send/' . $news_id);
            echo form_hidden('type_id', '2');
            echo form_textarea($textarea_init);
            echo form_submit('submit','Post');
            echo form_close();
        ?>
    </div>
</div>