<div class="profile_edit_area">
<h1> Profile editing </h1>
    <?php echo isset($error) ? $error : "" ?>
    <?php echo validation_errors('<p class="error">');?>
    <fieldset>
        <legend>Personal profile information</legend>

        <?php
            echo form_open_multipart('site/save_profile');
        ?>
            <img src="<?php
                        if (!empty($user_data->avatar))
                                echo $user_data->avatar;
                             else
                                echo "/images/no_avatar.gifs"; ?>" height="96px" width="96px" alt=""/>
        <?php
            echo form_label('Upload your avatar', 'l_avatar');
            echo form_upload('avatar', set_value('avatar', 'Upload avatar'));
            echo form_label('First name', 'l_first_name');
            echo form_input('first_name', set_value('first_name', $user_data->first_name));
            echo form_label('Second name', 'l_second_name');
            echo form_input('second_name', set_value('second_name', $user_data->second_name));
            echo form_label('E-mail address', 'l_email_address');
            echo form_input('email_address', set_value('email_address', $user_data->email_address));
            echo form_label('City', 'l_city');
            echo form_input('city', set_value('city', $user_data->city));
            echo form_label('Country', 'l_country');
            echo form_input('country', set_value('country', $user_data->country));
            $options = array('male' => 'Male',
                             'female' => 'Female'
            );
            echo form_label('Sex', 'l_sex');
            echo "<br />";
            echo form_dropdown('sex', $options, $user_data->sex);
            echo "<br />";
            echo form_label('Age', 'l_age');
            echo form_input('age', set_value('age', $user_data->age));
            echo form_label('Education', 'l_education');
            echo form_input('education', set_value('education', $user_data->education));
            echo form_label('Work', 'l_work');
            echo form_input('work', set_value('work', $user_data->work));
            $textarea_init = array('name' => 'description',
                     'cols' => '60',
                     'value' => $user_data->description);
            echo form_textarea($textarea_init);
            ?>


     </fieldset>
     <fieldset>
        <legend>Change password</legend>
        <?php
            echo form_label('Enter password', 'l_enter_password');
            echo form_password('password', set_value('password', ''));
            echo form_label('Reenter password', 'l_reenter_password');
            echo form_password('password2', set_value('password2', ''));        
        ?>
    </fieldset>
        <?php
            echo form_submit('submit', 'Save changes');
            echo form_close();
        ?>
</div>
