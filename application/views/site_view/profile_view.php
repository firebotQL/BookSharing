<div class="profile_view">
    <h4>NAME OF THE USER</h4>
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
        <p>BOOKSSSSSSSSSSSSSSSSS</p>
        <p>BOOKSSSSSSSSSSSSSSSSS</p>
        <p>BOOKSSSSSSSSSSSSSSSSS</p>
        <p>BOOKSSSSSSSSSSSSSSSSS</p>
        <p>BOOKSSSSSSSSSSSSSSSSS</p>
        <p>BOOKSSSSSSSSSSSSSSSSS</p>
        <p>BOOKSSSSSSSSSSSSSSSSS</p>
    </div>
    <div class="profile_comments">
        <p>Comment1</p>
        <p>Comment2</p>
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

