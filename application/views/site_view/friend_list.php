<div class="pagination">
    <?php echo $this->pagination->create_links(); ?>
</div>
<div class="my_friend_list">
    <?php if (isset($friends) && $friends->num_rows() > 0): ?>
                <table>
                    <?php foreach ($friends->result() as $row): ?>
                    <tr>
                        <td class="my_friend_flist_a">
                            <img src="<?php echo $row->avatar; ?>"  height="55px" width="45px"/>
                        </td>
                        <td class="my_friend_list_p" valign="top">
                            <p> <?php echo "Nickname: " . $row->username ?> </p>
                            <p> <?php echo "Name: " . $row->first_name ?> </p>
                        </td>
                        <td class="my_friend_list_control" valign="top">
                            <p>
                                <?php echo anchor('site/profile/' . $row->friend_id, 'View profile') ?>
                                <?php echo anchor('site/compose/' . $row->username, 'Send message'); ?> 
                            </p>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
    <?php else: ?>
    <div>
        <p>No friends were added to the friend list</o>
    </div>
    <?php endif; ?>
</div>