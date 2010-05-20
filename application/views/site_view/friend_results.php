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
                                if (!$friend_exist)
                                {
                                    $url = base_url() . "site/add_friend/" . $user_data->user_id;
                                    echo anchor($url, "Add to friendlist", array('class' => 'jid_unique'));
                                }
                                else
                                {
                                    echo anchor("", "Already your friend", 'onclick="return false"', array('class' => 'jid_unique'));
                                } ?>
                                <?php echo anchor('friends/add/' . $row->username, 'Add as a friend'); ?>
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