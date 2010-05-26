<div class="pagination">
    <?php echo $this->pagination->create_links(); ?>
</div>
<div class="friend_search_list">
    <?php if (isset($search_result) && $search_result->num_rows() > 0): ?>
                <table>
                    <?php foreach ($search_result->result() as $row): ?>
                    <tr>
                        <td class="search_friend_list_img">
                            <img src="<?php echo $row->avatar; ?>"  height="55px" width="45px"/>
                        </td>
                        <td class="search_friend_list_p" valign="top">
                            <p> <?php echo "Nickname: " . $row->username ?> </p>
                            <p> <?php echo "Name: " . $row->first_name ?> </p>
                        </td>
                        <td class="search_friend_list_control" valign="top">
                            <p>
                                <?php echo anchor('site/profile/' . $row->friend_id, 'View profile', array('class' => 'jid_unique')) ?>
                                <?php
                                if (!($friend_exist[$row->friend_id]))
                                {
                                    $url = base_url() . "site/add_friend/";
                                    echo form_open($url);
                                    echo form_hidden('friend_id', $row->friend_id);
                                    echo form_submit('submit', 'Add to friendlist ');
                                    echo form_close();
                                }
                                else
                                {
                                    echo "<p>Already your friend</p>";
                                }
                                ?>
                            </p>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
    <?php else: ?>
    <div>
        <p>No results were found</o>
    </div>
    <?php endif; ?>
</div>