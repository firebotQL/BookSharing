<div class="pagination">
    <?php echo $this->pagination->create_links(); ?>
</div>
<div class="news_area">
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
                                    <a href="<?php echo base_url(); ?>site/news_comments/<?php echo $news->id  ?>" style="float: right"
                                   class="comments">Comments (<?php echo $comments[$news->id];?>)</a>
                                     
                                </p>
                                
                        </p>
                    </td> 
                </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <div>
        <p>No news were added to the bookshelve</p>
    </div>
    <?php endif; ?>
</div>
